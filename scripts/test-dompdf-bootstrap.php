<?php

require dirname(__DIR__).'/backend/vendor/autoload.php';
$app = require dirname(__DIR__).'/backend/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$path = config('dompdf.public_path') ?: base_path('public');
echo 'dompdf public_path config: '.$path.PHP_EOL;
echo 'realpath: '.(realpath($path) ?: 'FAIL').PHP_EOL;

$pdf = Barryvdh\DomPDF\Facade\Pdf::loadHTML('<h1>Test</h1>');
echo 'PDF generated: '.strlen($pdf->output()).' bytes'.PHP_EOL;
