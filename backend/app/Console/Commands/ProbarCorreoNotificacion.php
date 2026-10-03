<?php

namespace App\Console\Commands;

use App\Mail\PruebaNotificacionTiMail;
use App\Mail\SolicitudNuevaMail;
use App\Models\Solicitud;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ProbarCorreoNotificacion extends Command
{
    protected $signature = 'notificacion:probar-correo {--ejemplo-solicitud : Enviar plantilla real de nueva solicitud (última en BD)}';

    protected $description = 'Envía un correo de prueba a NOTIFICACION_SOPORTE_EMAIL';

    public function handle(): int
    {
        $destino = config('notificaciones.correo_soporte_solicitudes');

        if (! $destino) {
            $this->error('NOTIFICACION_SOPORTE_EMAIL no está configurado en .env');

            return self::FAILURE;
        }

        $this->info('Mailer: ' . config('mail.default'));
        $this->info('Destino: ' . $destino);
        $this->info('From: ' . config('mail.from.address'));

        try {
            if ($this->option('ejemplo-solicitud')) {
                $solicitud = Solicitud::with(['sucursal', 'usuario', 'detalles.suministro'])->latest('id')->first();
                if (! $solicitud) {
                    $this->warn('No hay solicitudes en BD. Creando prueba HTML genérica.');

                    Mail::to($destino)->send(new PruebaNotificacionTiMail($destino));
                } else {
                    Mail::to($destino)->send(new SolicitudNuevaMail($solicitud));
                    $this->info('Plantilla real enviada (nueva solicitud #' . $solicitud->id . ').');
                }
            } else {
                Mail::to($destino)->send(new PruebaNotificacionTiMail($destino));
            }

            $this->info('Correo enviado correctamente (revisa bandeja y spam).');

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('No se pudo enviar: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
