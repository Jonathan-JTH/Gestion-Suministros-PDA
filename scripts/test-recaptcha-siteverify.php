<?php

require dirname(__DIR__).'/backend/vendor/autoload.php';
$app = require dirname(__DIR__).'/backend/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$secret = config('recaptcha.secret_key');
echo 'APP_ENV: '.config('app.env').PHP_EOL;
echo 'verify_ssl: '.(config('recaptcha.verify_ssl') ? 'true' : 'false').PHP_EOL;
echo 'Secret length: '.strlen((string) $secret).PHP_EOL;

try {
    $client = Illuminate\Support\Facades\Http::asForm()->timeout(15);
    if (! config('recaptcha.verify_ssl', true)) {
        $client = $client->withoutVerifying();
    }
    $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secret,
            'response' => 'test-token',
        ]);
    echo 'HTTP status: '.$response->status().PHP_EOL;
    echo 'Body: '.$response->body().PHP_EOL;
} catch (Throwable $e) {
    echo 'Exception: '.$e->getMessage().PHP_EOL;
    echo $e::class.PHP_EOL;
}
