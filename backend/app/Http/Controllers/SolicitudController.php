<?php

namespace App\Http\Controllers;

use App\Models\DetalleSolicitud;
use App\Models\Solicitud;
use App\Models\Suministro;
use App\Models\Sucursal;
use App\Services\InventarioService;
use App\Services\NotificacionSolicitudService;
use App\Services\SolicitudEstadoService;
use App\Support\NotificacionConfig;
use App\Support\RegistraBitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use RuntimeException;

class SolicitudController extends Controller
{
    public function __construct(
        private readonly SolicitudEstadoService $estadoService,
        private readonly InventarioService $inventarioService,
        private readonly NotificacionSolicitudService $notificacionSolicitudService,
    ) {}

    public function index(Request $request)
    {
        $query = Solicitud::with(['usuario', 'sucursal', 'detalles.suministro'])
            ->where('usuario_id', Auth::id());

        if ($request->filled('buscar')) {
            $query->where('id', $request->input('buscar'));
        }

        return view('solicitudes.index', [
            'solicitudes' => $query->latest()->get(),
        ]);
    }

    public function gestion(Request $request)
    {
        $this->authorizeGestion();

        $query = Solicitud::with(['usuario', 'sucursal', 'detalles.suministro'])->latest();

        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        if ($request->filled('sucursal_id')) {
            $query->where('sucursal_id', $request->input('sucursal_id'));
        }

        return view('solicitudes.gestion', array_merge(
            [
                'solicitudes' => $query->get(),
                'sucursales' => Sucursal::orderBy('nombre')->get(),
            ],
            $this->flagsNotificacionesVista()
        ));
    }

    public function create()
    {
        return view('solicitudes.create', [
            'suministros' => Suministro::where('activo', true)->orderBy('nombre')->get(),
            'sucursal' => Auth::user()->sucursal,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (! $user->sucursal_id) {
            return back()->with('error', 'Su usuario no tiene sucursal asignada.');
        }

        $data = $request->validate([
            'suministro_id' => 'required|exists:suministros,id',
            'cantidad' => 'required|integer|min:1',
            'observacion' => 'nullable|string|max:500',
        ]);

        $solicitud = Solicitud::create([
            'usuario_id' => $user->id,
            'nombre_solicitante' => $user->nombre,
            'correo_solicitante' => $user->correo,
            'sucursal_id' => $user->sucursal_id,
            'estado' => 'pendiente',
            'observacion' => $data['observacion'] ?? null,
        ]);

        DetalleSolicitud::create([
            'solicitud_id' => $solicitud->id,
            'suministro_id' => $data['suministro_id'],
            'cantidad' => $data['cantidad'],
        ]);

        RegistraBitacora::registrar('solicitudes', 'crear', 'Solicitud #' . $solicitud->id . ' registrada', $request);

        $tiEnviado = $this->notificacionSolicitudService->avisarNuevaSolicitud($solicitud, $request);
        $confirmacionEnviada = $this->notificacionSolicitudService->avisarConfirmacionSolicitante($solicitud, $request);

        $mensaje = 'Solicitud registrada correctamente.';
        $tipoFlash = 'success';

        if (NotificacionConfig::emailsHabilitados()) {
            $fallos = [];
            if ($tiEnviado === false) {
                $fallos[] = 'no se pudo avisar a TI (' . (NotificacionConfig::correoIt() ?? '—') . ')';
            }
            if ($confirmacionEnviada === false) {
                $fallos[] = 'no se pudo enviar confirmación al solicitante';
            }
            if ($fallos !== []) {
                $mensaje .= ' Correos: ' . implode('; ', $fallos) . '. Revise MAIL_PASSWORD (contraseña de aplicación Google) en backend/.env y Bitácora.';
                $tipoFlash = 'warning';
            } elseif (config('mail.default') === 'log') {
                $mensaje .= ' Modo log: correos en backend/storage/logs/laravel.log.';
                $tipoFlash = 'warning';
            }
        }

        return redirect()->route('solicitudes.index')->with($tipoFlash, $mensaje);
    }

    public function show(Solicitud $solicitude)
    {
        Gate::authorize('view', $solicitude);

        $solicitude->load(['usuario', 'sucursal', 'detalles.suministro', 'despachadoPor']);
        $user = Auth::user()->load('rol');

        return view('solicitudes.show', array_merge(
            [
                'solicitud' => $solicitude,
                'esAdmin' => $user->isAdmin() || $user->isSoporte(),
            ],
            $this->flagsNotificacionesVista()
        ));
    }

    public function aprobar(Request $request, $id)
    {
        $this->authorizeGestion();
        $solicitud = Solicitud::findOrFail($id);

        try {
            $this->estadoService->transicionar($solicitud, 'en_proceso');
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        RegistraBitacora::registrar('solicitudes', 'cambio_estado', 'Solicitud #' . $solicitud->id . ' en proceso', $request);

        return back()->with('success', 'Solicitud #' . $solicitud->id . ' marcada en proceso.');
    }

    public function rechazar(Request $request, $id)
    {
        $this->authorizeGestion();

        $data = $request->validate([
            'observacion' => 'nullable|string|max:500',
        ]);

        $solicitud = Solicitud::findOrFail($id);

        try {
            $this->estadoService->transicionar($solicitud, 'rechazada');
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $solicitud->observacion = $data['observacion'] ?? $solicitud->observacion;
        $solicitud->save();

        RegistraBitacora::registrar('solicitudes', 'cambio_estado', 'Solicitud #' . $solicitud->id . ' rechazada', $request);

        return back()->with('success', 'Solicitud #' . $solicitud->id . ' rechazada.');
    }

    public function atender(Request $request, $id)
    {
        $this->authorizeGestion();

        $solicitud = Solicitud::with('detalles')->findOrFail($id);

        if ($solicitud->estado !== 'en_proceso') {
            return back()->with('error', 'Solo se pueden atender solicitudes en proceso.');
        }

        $rules = [
            'nota_despacho' => 'nullable|string|max:500',
        ];
        $rules['correo_destinatario'] = 'nullable|email|max:255';
        $data = $request->validate($rules);

        foreach ($solicitud->detalles as $detalle) {
            if (! $this->inventarioService->validarDisponibilidad(
                $solicitud->sucursal_id,
                $detalle->suministro_id,
                $detalle->cantidad
            )) {
                return back()->with('error', 'Stock insuficiente para atender la solicitud.');
            }
        }

        try {
            $this->inventarioService->atenderSolicitud($solicitud, Auth::user());
            $solicitud->refresh();
            $this->estadoService->transicionar($solicitud, 'atendida');
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $correoDestinatario = filled($data['correo_destinatario'] ?? null)
            ? $data['correo_destinatario']
            : null;

        $solicitud->update([
            'correo_destinatario_despacho' => $correoDestinatario,
            'nota_despacho' => $data['nota_despacho'] ?? null,
            'despachado_por_usuario_id' => Auth::id(),
            'despachado_at' => now(),
        ]);
        $solicitud->refresh();

        $detalleBitacora = 'Solicitud #' . $solicitud->id . ' despachada.';
        if (NotificacionConfig::emailsHabilitados()) {
            $detalleBitacora .= ' Copia TI obligatoria a ' . (NotificacionConfig::correoIt() ?? '—');
            if (NotificacionConfig::despachoDestinatarioHabilitado() && $correoDestinatario) {
                $detalleBitacora .= '; aviso destinatario: ' . $correoDestinatario;
            }
        } else {
            $detalleBitacora .= ' Correos deshabilitados en configuración.';
        }

        RegistraBitacora::registrar('solicitudes', 'despachar', $detalleBitacora, $request);

        RegistraBitacora::registrar('inventario', 'atender', 'Solicitud #' . $solicitud->id . ' atendida', $request);

        $envioCorreos = $this->notificacionSolicitudService->avisarSolicitudDespachada(
            $solicitud,
            $correoDestinatario,
            $data['nota_despacho'] ?? null,
            $request
        );

        $mensaje = 'Solicitud #' . $solicitud->id . ' atendida.';
        $tipoFlash = 'success';
        $avisosCorreo = [];

        if (NotificacionConfig::emailsHabilitados()) {
            if ($envioCorreos['ti'] === true) {
                $avisosCorreo[] = 'Copia a TI enviada.';
            } elseif ($envioCorreos['ti'] === false) {
                $avisosCorreo[] = 'No se pudo enviar copia a TI (revise Bitácora o backend/storage/logs/laravel.log).';
                $tipoFlash = 'warning';
            }

            if (NotificacionConfig::despachoDestinatarioHabilitado() && $correoDestinatario) {
                if ($envioCorreos['destinatario'] === true) {
                    $avisosCorreo[] = 'Aviso enviado a ' . $correoDestinatario . '.';
                } elseif ($envioCorreos['destinatario'] === false) {
                    $avisosCorreo[] = 'No se pudo enviar aviso a ' . $correoDestinatario . '.';
                    $tipoFlash = 'warning';
                }
            } elseif (NotificacionConfig::despachoDestinatarioHabilitado()) {
                $avisosCorreo[] = 'No se indicó correo para aviso al solicitante.';
            }

            if (config('mail.default') === 'log') {
                $avisosCorreo[] = 'Modo log: el contenido del correo está en backend/storage/logs/laravel.log (no llega a Gmail hasta configurar SMTP).';
                $tipoFlash = 'warning';
            }
        }

        if ($avisosCorreo !== []) {
            $mensaje .= ' ' . implode(' ', $avisosCorreo);
        }

        return back()->with($tipoFlash, $mensaje);
    }

    private function authorizeGestion(): void
    {
        $user = Auth::user()->load('rol');

        if (! $user->tienePermiso('solicitudes.gestionar')) {
            abort(403, 'No autorizado.');
        }
    }

    /** @return array{emailsNotificacionHabilitados: bool, despachoDestinatarioHabilitado: bool} */
    private function flagsNotificacionesVista(): array
    {
        return [
            'emailsNotificacionHabilitados' => NotificacionConfig::emailsHabilitados(),
            'despachoDestinatarioHabilitado' => NotificacionConfig::despachoDestinatarioHabilitado(),
        ];
    }
}
