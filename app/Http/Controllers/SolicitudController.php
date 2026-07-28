<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Solicitud;
use App\Models\DetalleSolicitud;
use App\Models\BitacoraMovimiento;
use App\Models\Suministro;
use App\Models\Impresora;

class SolicitudController extends Controller
{
    public function index()
    {
        $solicitudes = Solicitud::with(['usuario', 'sucursal', 'detalles.impresora'])
            ->where('usuario_id', Auth::id())
            ->latest()
            ->get();

        return view('solicitudes.index', compact('solicitudes'));
    }

    public function gestion()
    {
        $solicitudes = Solicitud::with(['usuario', 'sucursal', 'detalles.impresora'])
            ->latest()
            ->get();

        return view('solicitudes.gestion', compact('solicitudes'));
    }

    public function create()
    {
        $impresoras = Impresora::where('sucursal_id', Auth::user()->sucursal_id)->get();

        return view('solicitudes.create', compact('impresoras'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'impresora_id' => 'required|exists:impresoras,id',
            'tipo_movimiento' => 'required|in:ENT,DEV,OC',
            'cantidad' => 'required|integer|min:1',
            'observacion' => 'nullable|string|max:500',
        ]);

        $solicitud = Solicitud::create([
            'usuario_id' => Auth::id(),
            'sucursal_id' => Auth::user()->sucursal_id,
            'estado' => 'pendiente',
            'observacion' => $data['observacion'] ?? null,
        ]);

        DetalleSolicitud::create([
            'solicitud_id' => $solicitud->id,
            'impresora_id' => $data['impresora_id'],
            'tipo_movimiento' => $data['tipo_movimiento'],
            'cantidad' => $data['cantidad'],
        ]);

        BitacoraMovimiento::create([
            'usuario_id' => Auth::id(),
            'solicitud_id' => $solicitud->id,
            'modulo' => 'solicitudes',
            'accion' => 'crear',
            'detalle' => 'Solicitud #' . $solicitud->id . ' registrada',
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('solicitudes.index')
            ->with('success', 'Solicitud registrada correctamente.');
    }

    public function show(Solicitud $solicitude)
    {
        $esAdmin = Auth::user()->rol === 'admin';

        if (! $esAdmin && $solicitude->usuario_id !== Auth::id()) {
            abort(403, 'No autorizado.');
        }

        $solicitude->load(['usuario', 'sucursal', 'detalles.impresora']);

        return view('solicitudes.show', [
            'solicitud' => $solicitude,
            'esAdmin' => $esAdmin,
        ]);
    }

    public function aprobar(Request $request, $id)
    {
        $solicitud = Solicitud::findOrFail($id);

        if ($solicitud->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden aprobar solicitudes pendientes.');
        }

        $solicitud->estado = 'aprobado';
        $solicitud->save();

        BitacoraMovimiento::create([
            'usuario_id' => Auth::id(),
            'solicitud_id' => $solicitud->id,
            'modulo' => 'solicitudes',
            'accion' => 'aprobar',
            'detalle' => 'Solicitud #' . $solicitud->id . ' aprobada',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Solicitud #' . $solicitud->id . ' aprobada correctamente.');
    }

    public function rechazar(Request $request, $id)
    {
        $data = $request->validate([
            'observacion' => 'nullable|string|max:500',
        ]);

        $solicitud = Solicitud::findOrFail($id);

        if ($solicitud->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden rechazar solicitudes pendientes.');
        }

        $solicitud->estado = 'rechazado';
        $solicitud->observacion = $data['observacion'] ?? $solicitud->observacion;
        $solicitud->save();

        BitacoraMovimiento::create([
            'usuario_id' => Auth::id(),
            'solicitud_id' => $solicitud->id,
            'modulo' => 'solicitudes',
            'accion' => 'rechazar',
            'detalle' => 'Solicitud #' . $solicitud->id . ' rechazada',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Solicitud #' . $solicitud->id . ' rechazada.');
    }

    public function despachar(Request $request, $id)
    {
        $solicitud = Solicitud::with('detalles')->findOrFail($id);

        if ($solicitud->estado !== 'aprobado') {
            return back()->with('error', 'Solo se pueden despachar solicitudes aprobadas.');
        }

        try {
            DB::transaction(function () use ($solicitud) {
                foreach ($solicitud->detalles as $detalle) {
                    if ($detalle->tipo_movimiento === 'OC') {
                        continue;
                    }

                    $stock = Suministro::where('impresora_id', $detalle->impresora_id)
                        ->lockForUpdate()
                        ->first();

                    if (! $stock) {
                        throw new \RuntimeException('No existe inventario para la impresora ID: ' . $detalle->impresora_id);
                    }

                    if ($detalle->tipo_movimiento === 'ENT') {
                        if ($stock->cantidad_actual < $detalle->cantidad) {
                            throw new \RuntimeException('Stock insuficiente para la impresora ID: ' . $detalle->impresora_id);
                        }
                        $stock->cantidad_actual -= $detalle->cantidad;
                    }

                    if ($detalle->tipo_movimiento === 'DEV') {
                        $stock->cantidad_actual += $detalle->cantidad;
                    }

                    $stock->save();
                }

                $solicitud->estado = 'despachado';
                $solicitud->save();
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        BitacoraMovimiento::create([
            'usuario_id' => Auth::id(),
            'solicitud_id' => $solicitud->id,
            'modulo' => 'inventario',
            'accion' => 'despachar',
            'detalle' => 'Solicitud #' . $solicitud->id . ' despachada e inventario actualizado',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Solicitud #' . $solicitud->id . ' despachada e inventario actualizado.');
    }
}
