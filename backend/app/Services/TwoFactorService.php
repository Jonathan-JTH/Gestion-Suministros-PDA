<?php

namespace App\Services;

use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use PragmaRX\Google2FA\Google2FA;
use RuntimeException;

class TwoFactorService
{
    public function __construct(private readonly Google2FA $google2fa) {}

    public function requiereDosFactor(User $user): bool
    {
        return (bool) $user->segundo_factor_habilitado
            && in_array($user->rol?->nombre, ['admin', 'soporte'], true);
    }

    public function tieneSecretoConfigurado(User $user): bool
    {
        return filled($user->totp_secreto);
    }

    /** @return array{secreto: string, qr_svg: string} */
    public function generarEnrolamiento(User $user): array
    {
        return $this->empaquetarSecreto($user, $this->google2fa->generateSecretKey());
    }

    /** @return array{secreto: string, qr_svg: string} */
    public function empaquetarSecreto(User $user, string $secreto): array
    {
        $otpauthUrl = $this->google2fa->getQRCodeUrl(
            config('app.name', 'Gestión de Suministros'),
            $user->correo,
            $secreto
        );

        $writer = new Writer(new ImageRenderer(new RendererStyle(220), new SvgImageBackEnd()));

        return [
            'secreto' => $secreto,
            'qr_svg' => $writer->writeString($otpauthUrl),
        ];
    }

    public function confirmarEnrolamiento(User $user, string $secretoPlano, string $codigo): void
    {
        if (! $this->google2fa->verifyKey($secretoPlano, $codigo)) {
            throw new RuntimeException('Código incorrecto. Verifique la hora de su teléfono e intente de nuevo.');
        }

        $user->totp_secreto = $secretoPlano;
        $user->save();
    }

    public function verificarCodigo(User $user, string $codigo): void
    {
        if (! $this->tieneSecretoConfigurado($user)) {
            throw new RuntimeException('Google Authenticator no está configurado para este usuario.');
        }

        if (! $this->google2fa->verifyKey($user->totp_secreto, $codigo)) {
            throw new RuntimeException('Código 2FA incorrecto.');
        }
    }
}
