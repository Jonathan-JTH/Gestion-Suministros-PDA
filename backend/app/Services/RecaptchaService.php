<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaService
{
    /** Claves públicas de prueba de Google (muestran aviso rojo en el widget). */
    public const TEST_SITE_KEY = '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';

    public function isUsingTestKeys(): bool
    {
        return config('recaptcha.site_key') === self::TEST_SITE_KEY;
    }

    public function isRequired(): bool
    {
        if (! config('recaptcha.enabled')) {
            return false;
        }

        return filled(config('recaptcha.site_key')) && filled(config('recaptcha.secret_key'));
    }

    public function siteKey(): ?string
    {
        $key = config('recaptcha.site_key');

        return filled($key) ? $key : null;
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
            return ['ok' => false, 'message' => 'Marque la casilla «No soy un robot» antes de ingresar.'];
        }

        try {
            $client = Http::asForm()->timeout(15);
            if (! config('recaptcha.verify_ssl', true)) {
                $client = $client->withoutVerifying();
            }

            $verify = $client->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('recaptcha.secret_key'),
                'response' => $response,
                'remoteip' => $remoteIp,
            ]);
        } catch (\Throwable $e) {
            Log::warning('reCAPTCHA siteverify error: '.$e->getMessage());

            $hint = str_contains($e->getMessage(), 'cURL error 60')
                ? ' (certificados SSL de PHP en Windows: instale cacert.pem o use APP_ENV=local).'
                : '';

            return ['ok' => false, 'message' => 'No se pudo contactar a Google reCAPTCHA. Revise su conexión e intente de nuevo.'.$hint];
        }

        if (! $verify->successful()) {
            return ['ok' => false, 'message' => 'No se pudo validar reCAPTCHA. Intente de nuevo.'];
        }

        $body = $verify->json();

        if ($body['success'] ?? false) {
            return ['ok' => true, 'message' => null];
        }

        $codes = $body['error-codes'] ?? [];
        if (config('app.debug') && $codes !== []) {
            Log::debug('reCAPTCHA error-codes', ['codes' => $codes]);
        }

        $message = match (true) {
            in_array('invalid-input-secret', $codes, true) => 'Clave secreta reCAPTCHA incorrecta en .env (RECAPTCHA_SECRET_KEY).',
            in_array('invalid-input-response', $codes, true) => 'reCAPTCHA expirado. Vuelva a marcar la casilla e intente.',
            in_array('timeout-or-duplicate', $codes, true) => 'reCAPTCHA ya usado o expirado. Recargue la página e intente de nuevo.',
            default => 'reCAPTCHA no válido. Use claves v2 «No soy un robot» y dominio localhost en Google Admin.',
        };

        return ['ok' => false, 'message' => $message];
    }
}
