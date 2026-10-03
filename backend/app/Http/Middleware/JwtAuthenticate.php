<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\JwtService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtAuthenticate
{
    public function __construct(private readonly JwtService $jwtService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization', '');

        if (! str_starts_with($header, 'Bearer ')) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        $token = trim(substr($header, 7));

        try {
            $userId = $this->jwtService->userIdFromToken($token);
        } catch (\Throwable) {
            return response()->json(['message' => 'Token inválido o expirado.'], 401);
        }

        $user = User::with('rol.permisos')->find($userId);

        if (! $user || ! $user->activo) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        auth()->setUser($user);
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
