<?php

namespace App\Services;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Auth\AuthenticationException;
use UnexpectedValueException;

class JwtService
{
    public function issue(User $user): string
    {
        $user->loadMissing('rol');
        $now = time();
        $ttl = config('jwt.ttl_minutes') * 60;

        $payload = [
            'iss' => config('jwt.issuer'),
            'iat' => $now,
            'exp' => $now + $ttl,
            'sub' => (string) $user->id,
            'rol' => $user->rol?->nombre,
        ];

        return JWT::encode($payload, config('jwt.secret'), 'HS256');
    }

    public function userIdFromToken(string $token): int
    {
        try {
            $decoded = JWT::decode($token, new Key(config('jwt.secret'), 'HS256'));
        } catch (UnexpectedValueException) {
            throw new AuthenticationException('Token inválido o expirado.');
        }

        return (int) $decoded->sub;
    }
}
