<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\NotificacionConfig;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VerificarSistema extends Command
{
    protected $signature = 'sistema:verificar';

    protected $description = 'Comprueba BD, usuarios demo, correo y notificaciones';

    public function handle(): int
    {
        $ok = true;

        try {
            DB::connection()->getPdo();
            $this->info('MySQL: conexión OK');
        } catch (\Throwable $e) {
            $this->error('MySQL: ' . $e->getMessage());
            $ok = false;
        }

        foreach (['usuarios', 'solicitudes', 'inventario', 'bitacora', 'parametros_sistema'] as $tabla) {
            if (Schema::hasTable($tabla)) {
                $this->line("Tabla {$tabla}: OK");
            } else {
                $this->warn("Tabla {$tabla}: no existe (¿php artisan migrate?)");
                $ok = false;
            }
        }

        $admins = User::whereHas('rol', fn ($q) => $q->whereIn('nombre', ['admin', 'soporte']))->count();
        $sucursales = User::whereHas('rol', fn ($q) => $q->where('nombre', 'sucursal'))->count();
        $this->line("Usuarios admin/soporte: {$admins} | sucursal: {$sucursales}");

        $this->line('Correos habilitados (BD): ' . (NotificacionConfig::emailsHabilitados() ? 'sí' : 'no'));
        $this->line('Destinatario TI: ' . (NotificacionConfig::correoIt() ?? '—'));
        $mailer = config('mail.default');
        $this->line('Mailer activo: ' . $mailer);
        $this->line('From: ' . (config('mail.from.address') ?: '—'));

        $recaptchaOn = config('recaptcha.enabled')
            && filled(config('recaptcha.site_key'))
            && filled(config('recaptcha.secret_key'));
        $this->line('reCAPTCHA login: ' . ($recaptchaOn ? 'activo' : 'desactivado (desarrollo)'));

        if ($mailer === 'log') {
            $this->warn('Los correos NO llegan al buzón real: se guardan en backend/storage/logs/laravel.log (busque "To:").');
            $this->warn('Para Gmail: complete MAIL_USERNAME, MAIL_PASSWORD y MAIL_FROM_ADDRESS en backend/.env y ejecute php artisan config:clear');
        } elseif ($mailer === 'smtp' && empty(config('mail.mailers.smtp.username'))) {
            $this->warn('SMTP sin MAIL_USERNAME: configure Gmail en backend/.env');
        }

        if ($ok) {
            $this->info('Sistema listo para demo. Prueba correo: php artisan notificacion:probar-correo');
        } else {
            $this->error('Revise los puntos anteriores.');
        }

        return $ok ? self::SUCCESS : self::FAILURE;
    }
}
