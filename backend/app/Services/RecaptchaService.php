<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RecaptchaService
{
    public function isRequired(): bool
    {
        if (! config('recaptcha.enabled')) {
            return false;
        }

        return filled(config('recaptcha.site_key')) && filled(config('recaptcha.secret_key'));
    }

    public function siteKey(): ?string
    {
        return config('recaptcha.site_key') ?: null;
    }

    /**
     * @return array{ok: bool, message: ?string}
     */
    public function verify(?string $response, ?string $remoteIp = null): array
    {
        if (! $this->isRequired()) {
            return ['ok' => true, 'message' => null];
        }

        if (! filled($response)) {
            return ['ok' => false, 'message' => 'Confirme que no es un robot (reCAPTCHA).'];
        }

        $verify = Http::asForm()
            ->timeout(10)
            ->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('recaptcha.secret_key'),
                'response' => $response,
                'remoteip' => $remoteIp,
            ]);

        if (! $verify->successful()) {
            return ['ok' => false, 'message' => 'No se pudo validar reCAPTCHA. Intente de nuevo.'];
        }

        $body = $verify->json();

        if (! ($body['success'] ?? false)) {
            return ['ok' => false, 'message' => 'reCAPTCHA inválido o expirado. Intente de nuevo.'];
        }

        return ['ok' => true, 'message' => null];
    }
}
