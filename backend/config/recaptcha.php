<?php

$siteKey = trim((string) env('RECAPTCHA_SITE_KEY', ''));
$secretKey = trim((string) env('RECAPTCHA_SECRET_KEY', ''));
$enabledEnv = env('RECAPTCHA_ENABLED');

// Si no define RECAPTCHA_ENABLED, se activa al tener ambas claves.
$enabled = $enabledEnv === null
    ? ($siteKey !== '' && $secretKey !== '')
    : filter_var($enabledEnv, FILTER_VALIDATE_BOOLEAN);

return [

    /*
    |--------------------------------------------------------------------------
    | Google reCAPTCHA v2 (checkbox "No soy un robot")
    |--------------------------------------------------------------------------
    | Admin: https://www.google.com/recaptcha/admin
    | Desarrollo local: claves de prueba de Google (siempre pasan verificación):
    |   RECAPTCHA_SITE_KEY=6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI
    |   RECAPTCHA_SECRET_KEY=6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe
    */

    'enabled' => $enabled,

    'site_key' => $siteKey,

    'secret_key' => $secretKey,

    /*
    | Claves creadas en Google Cloud (reCAPTCHA Enterprise / Fraud Defense) requieren
    | enterprise.js. Las clásicas de admin antiguo usan api.js (RECAPTCHA_ENTERPRISE=false).
    */
    'use_enterprise' => filter_var(env('RECAPTCHA_ENTERPRISE', true), FILTER_VALIDATE_BOOLEAN),

    /*
    | classic_v3 — v3 del admin clásico (SiteVerify) + api.js — el más común al registrar en google.com/recaptcha/admin
    | enterprise_v3 — v3 creado solo en Google Cloud Enterprise
    | enterprise_v2 — casilla Cloud + enterprise.js
    | classic_v2 — casilla clásica + api.js
    */
    'integration' => env('RECAPTCHA_INTEGRATION', 'classic_v2'),

    /*
    | En Windows/XAMPP sin cacert.pem, siteverify falla con cURL error 60.
    | En local se desactiva verificación SSL por defecto; en production debe ser true.
    */
    'verify_ssl' => env('RECAPTCHA_VERIFY_SSL') === null
        ? env('APP_ENV') === 'production'
        : filter_var(env('RECAPTCHA_VERIFY_SSL'), FILTER_VALIDATE_BOOLEAN),

];
