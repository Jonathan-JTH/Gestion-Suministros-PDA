<?php

namespace App\Http\Controllers;

use App\Models\ParametroSistema;
use App\Support\NotificacionConfig;
use App\Support\RegistraBitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConfiguracionController extends Controller
{
    public function index()
    {
        return view('configuracion.index', [
            'appName' => config('app.name'),
            'timezone' => config('app.timezone'),
            'correoIt' => NotificacionConfig::correoIt(),
            'emailsHabilitados' => NotificacionConfig::emailsHabilitados(),
            'despachoDestinatarioHabilitado' => NotificacionConfig::despachoDestinatarioHabilitado(),
        ]);
    }

    public function actualizarNotificaciones(Request $request)
    {
        $user = Auth::user()->load('rol');
        if (! $user->isAdmin()) {
            abort(403, 'Solo administración puede cambiar esta configuración.');
        }

        $data = $request->validate([
            'notificaciones_email_habilitadas' => 'nullable|boolean',
            'notificaciones_despacho_destinatario_habilitado' => 'nullable|boolean',
        ]);

        $emailsOn = $request->boolean('notificaciones_email_habilitadas');
        $destinatarioOn = $request->boolean('notificaciones_despacho_destinatario_habilitado');

        ParametroSistema::guardarBool(NotificacionConfig::CLAVE_EMAILS, $emailsOn);
        ParametroSistema::guardarBool(NotificacionConfig::CLAVE_DESPACHO_DESTINATARIO, $destinatarioOn);

        RegistraBitacora::registrar(
            'configuracion',
            'notificaciones',
            'Correos ' . ($emailsOn ? 'habilitados' : 'deshabilitados')
                . '; aviso destinatario al despachar ' . ($destinatarioOn ? 'habilitado' : 'deshabilitado')
                . '. Copia TI siempre obligatoria cuando correos están activos.',
            $request
        );

        return back()->with('success', 'Configuración de notificaciones actualizada.');
    }
}
