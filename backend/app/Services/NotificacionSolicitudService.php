<?php

namespace App\Services;

use App\Mail\SolicitudConfirmacionSolicitanteMail;
use App\Mail\SolicitudEstadoMail;
use App\Mail\SolicitudNuevaMail;
use App\Models\Solicitud;
use App\Support\NotificacionConfig;
use App\Support\RegistraBitacora;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificacionSolicitudService
{
    public function avisarNuevaSolicitud(Solicitud $solicitud, ?Request $request = null): ?bool
    {
        if (! NotificacionConfig::emailsHabilitados()) {
            return null;
        }

        $solicitud->load(['sucursal', 'usuario', 'detalles.suministro']);

        $destinoIt = NotificacionConfig::correoIt();
        if (! $destinoIt) {
            return null;
        }

        return $this->enviarA(
            $destinoIt,
            $solicitud,
            new SolicitudNuevaMail($solicitud),
            'Aviso nueva solicitud #' . $solicitud->id . ' (IT, obligatorio)',
            $request
        );
    }

    public function avisarConfirmacionSolicitante(Solicitud $solicitud, ?Request $request = null): ?bool
    {
        if (! NotificacionConfig::emailsHabilitados()) {
            return null;
        }

        $correo = $solicitud->correo_solicitante ?? $solicitud->usuario?->correo;
        if (! $correo) {
            return null;
        }

        $solicitud->load(['sucursal', 'detalles.suministro']);

        return $this->enviarA(
            $correo,
            $solicitud,
            new SolicitudConfirmacionSolicitanteMail($solicitud),
            'Confirmación solicitante solicitud #' . $solicitud->id,
            $request
        );
    }

    /** @return array{ti: bool|null, destinatario: bool|null} */
    public function avisarSolicitudDespachada(
        Solicitud $solicitud,
        ?string $correoDestinatario = null,
        ?string $notaDespacho = null,
        ?Request $request = null,
    ): array {
        $resultado = ['ti' => null, 'destinatario' => null];
        if (! NotificacionConfig::emailsHabilitados()) {
            return $resultado;
        }

        $solicitud->load(['sucursal', 'usuario', 'detalles.suministro', 'despachadoPor']);

        $sucursal = $solicitud->sucursal?->nombre ?? 'su sucursal';
        $auditor = $solicitud->despachadoPor?->nombre ?? Auth::user()?->nombre ?? 'Soporte TI';

        $destinoIt = NotificacionConfig::correoIt();
        if ($destinoIt) {
            $detalleIt = 'Despacho auditado por ' . $auditor . '.';
            if (NotificacionConfig::despachoDestinatarioHabilitado() && $correoDestinatario) {
                $detalleIt .= ' Se notificó a ' . $correoDestinatario . ' sobre la solicitud de ' . $sucursal . '.';
            } else {
                $detalleIt .= ' Solicitud de ' . $sucursal . ' (sin aviso externo al destinatario).';
            }

            $resultado['ti'] = $this->enviarA(
                $destinoIt,
                $solicitud,
                new SolicitudEstadoMail(
                    $solicitud,
                    'Copia TI: solicitud despachada',
                    $detalleIt,
                    $notaDespacho,
                ),
                'Copia IT despacho #' . $solicitud->id . ' (obligatoria)',
                $request
            );
        }

        if (! NotificacionConfig::despachoDestinatarioHabilitado() || ! $correoDestinatario) {
            return $resultado;
        }

        $resultado['destinatario'] = $this->enviarA(
            $correoDestinatario,
            $solicitud,
            new SolicitudEstadoMail(
                $solicitud,
                'Su solicitud ha sido atendida',
                'Le informamos que su solicitud de suministros #' . $solicitud->id . ' (' . $sucursal . ') ya fue atendida por el área de TI. Los materiales quedaron registrados en el sistema.',
                $notaDespacho,
            ),
            'Aviso solicitud atendida #' . $solicitud->id . ' → ' . $correoDestinatario,
            $request
        );

        return $resultado;
    }

    private function enviarA(
        string $destino,
        Solicitud $solicitud,
        Mailable $mailable,
        string $bitacoraDetalle,
        ?Request $request,
    ): bool {
        try {
            Mail::to($destino)->send($mailable);

            RegistraBitacora::registrar(
                'notificaciones',
                'email_enviado',
                $bitacoraDetalle . ' → ' . $destino,
                $request
            );

            return true;
        } catch (\Throwable $e) {
            Log::error('No se pudo enviar correo de solicitud #' . $solicitud->id, [
                'destino' => $destino,
                'error' => $e->getMessage(),
            ]);

            RegistraBitacora::registrar(
                'notificaciones',
                'email_fallido',
                $bitacoraDetalle . ' → ' . $destino . ': ' . $e->getMessage(),
                $request
            );

            return false;
        }
    }
}
