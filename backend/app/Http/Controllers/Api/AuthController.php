<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\JwtService;
use App\Services\TwoFactorService;
use App\Support\RegistraBitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AuthController extends Controller
{
    public function __construct(
        private readonly JwtService $jwtService,
        private readonly TwoFactorService $twoFactorService,
    ) {}

    public function login(Request $request)
    {
        $data = $request->validate([
            'correo' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::with('rol')->where('correo', $data['correo'])->first();

        if (! $user || ! $user->activo || ! Hash::check($data['password'], $user->password)) {
            RegistraBitacora::registrar('auth', 'login_fallido', 'Intento fallido: ' . $data['correo'], $request);

            return response()->json(['message' => 'Credenciales inválidas.'], 401);
        }

        if ($this->twoFactorService->requiereDosFactor($user)) {
            if (! $this->twoFactorService->tieneSecretoConfigurado($user)) {
                return response()->json([
                    'message' => 'Debe configurar Google Authenticator en la aplicación web primero.',
                    'requires_2fa_setup' => true,
                ], 428);
            }

            return response()->json([
                'message' => 'Ingrese el código de Google Authenticator.',
                'requires_2fa' => true,
            ], 202);
        }

        $token = $this->jwtService->issue($user);
        RegistraBitacora::registrar('auth', 'login', 'Login API exitoso', $request);

        return response()->json([
            'message' => 'Autenticación exitosa.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in_minutes' => config('jwt.ttl_minutes'),
        ]);
    }

    public function verifyTwoFactor(Request $request)
    {
        $data = $request->validate([
            'correo' => 'required|email',
            'password' => 'required|string',
            'codigo' => 'required|digits:6',
        ]);

        $user = User::with('rol')->where('correo', $data['correo'])->first();

        if (! $user || ! $user->activo || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Credenciales inválidas.'], 401);
        }

        try {
            $this->twoFactorService->verificarCodigo($user, $data['codigo']);
        } catch (RuntimeException $e) {
            RegistraBitacora::registrar('auth', '2fa_fallido', $e->getMessage(), $request);

            return response()->json(['message' => 'Verificación 2FA fallida.'], 401);
        }

        $token = $this->jwtService->issue($user);
        RegistraBitacora::registrar('auth', '2fa', '2FA API validado (Google Authenticator)', $request);

        return response()->json([
            'message' => 'Segundo factor validado.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in_minutes' => config('jwt.ttl_minutes'),
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load('rol.permisos', 'sucursal');

        return response()->json([
            'data' => [
                'id' => $user->id,
                'nombre' => $user->nombre,
                'correo' => $user->correo,
                'rol' => $user->rol?->nombre,
                'permisos' => $user->rol?->permisos?->pluck('nombre'),
                'sucursal' => $user->sucursal?->nombre,
            ],
        ]);
    }
}
