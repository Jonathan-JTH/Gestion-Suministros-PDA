<?php

namespace App\Support;

use App\Models\ParametroSistema;

class NotificacionConfig
{
    public const CLAVE_EMAILS = 'notificaciones_email_habilitadas';

    public const CLAVE_DESPACHO_DESTINATARIO = 'notificaciones_despacho_destinatario_habilitado';

    public static function emailsHabilitados(): bool
    {
        $default = filter_var(env('NOTIFICACIONES_EMAIL_HABILITADAS', true), FILTER_VALIDATE_BOOL);

        return ParametroSistema::valorBool(self::CLAVE_EMAILS, $default);
    }

    public static function despachoDestinatarioHabilitado(): bool
    {
        $default = filter_var(env('NOTIFICACION_DESPACHO_DESTINATARIO_HABILITADO', true), FILTER_VALIDATE_BOOL);

        return ParametroSistema::valorBool(self::CLAVE_DESPACHO_DESTINATARIO, $default);
    }

    public static function correoIt(): ?string
    {
        $correo = config('notificaciones.correo_soporte_solicitudes');

        return $correo ?: null;
    }
}
