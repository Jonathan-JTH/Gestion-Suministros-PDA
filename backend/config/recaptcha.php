<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Google reCAPTCHA v2 (checkbox)
    |--------------------------------------------------------------------------
    | Registre el sitio en https://www.google.com/recaptcha/admin
    | Tipo: reCAPTCHA v2 → "No soy un robot". Dominio: localhost y su dominio real.
    | Sin claves o con RECAPTCHA_ENABLED=false el login funciona sin widget (desarrollo).
    */

    'enabled' => env('RECAPTCHA_ENABLED', false),

    'site_key' => env('RECAPTCHA_SITE_KEY', ''),

    'secret_key' => env('RECAPTCHA_SECRET_KEY', ''),

];
