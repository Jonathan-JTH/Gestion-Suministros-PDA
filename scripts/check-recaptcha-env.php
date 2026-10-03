<?php

$envPath = dirname(__DIR__).'/backend/.env';
if (! is_readable($envPath)) {
    fwrite(STDERR, "No se encuentra backend/.env\n");
    exit(1);
}

$site = $secret = $enabled = null;
foreach (file($envPath, FILE_IGNORE_NEW_LINES) as $line) {
    if (str_starts_with($line, 'RECAPTCHA_SITE_KEY=')) {
        $site = trim(substr($line, strlen('RECAPTCHA_SITE_KEY=')));
    }
    if (str_starts_with($line, 'RECAPTCHA_SECRET_KEY=')) {
        $secret = trim(substr($line, strlen('RECAPTCHA_SECRET_KEY=')));
    }
    if (str_starts_with($line, 'RECAPTCHA_ENABLED=')) {
        $enabled = trim(substr($line, strlen('RECAPTCHA_ENABLED=')));
    }
}

$testSite = '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';
$isTest = $site === $testSite;

echo 'RECAPTCHA_ENABLED: '.($enabled ?? '(no definido)').PHP_EOL;
echo 'Site key: '.($site === '' || $site === null ? '(vacía)' : substr($site, 0, 8).'… ('.strlen($site).' chars)').PHP_EOL;
echo 'Secret key: '.($secret === '' || $secret === null ? '(vacía)' : '*** ('.strlen($secret).' chars)').PHP_EOL;
echo 'Tipo: '.($isTest ? 'PRUEBA Google (aviso rojo en login)' : 'Propia (OK para demo sin aviso de prueba)').PHP_EOL;

exit($isTest ? 2 : 0);
