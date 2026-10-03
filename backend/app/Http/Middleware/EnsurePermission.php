<?php

namespace App\Http\Middleware;

use App\Support\RegistraBitacora;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    public function handle(Request $request, Closure $next, string $permiso): Response
    {
        $user = $request->user();

        if (! $user || ! $user->tienePermiso($permiso)) {
            RegistraBitacora::registrar('seguridad', 'acceso_denegado', 'Permiso requerido: ' . $permiso, $request);

            return response()->json(['message' => 'No autorizado.'], 403);
        }

        return $next($request);
    }
}
