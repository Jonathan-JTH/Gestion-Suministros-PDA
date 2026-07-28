<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * RoleMiddleware
 *
 * Este middleware valida el rol del usuario autenticado antes de permitir el acceso
 * a ciertas rutas, garantizando la separación de permisos entre Admin y Usuario.
 *
 * @author Jonathan
 */
class RoleMiddleware
{
    /**
     * Maneja una solicitud entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role  El rol requerido para acceder a la ruta ('admin' o 'usuario')
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Verifica si el usuario está autenticado
        if (!Auth::check()) {
            // Redirige al login si no está autenticado
            return redirect()->route('login');
        }

        // Verifica que el rol del usuario coincida con el requerido
        if (Auth::user()->rol !== $role) {
            // Si el rol no coincide, lanza error 403
            abort(403, 'No autorizado: acceso restringido.');
        }

        // Si todo es correcto, continúa con la solicitud
        return $next($request);
    }
}