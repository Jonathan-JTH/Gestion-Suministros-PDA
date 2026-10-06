<?php

require __DIR__ . '/../backend/vendor/autoload.php';
$app = require __DIR__ . '/../backend/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Barryvdh\DomPDF\Facade\Pdf;

$path = public_path('images/membrete-fabrigas-pda.png');
$w = 555;
$h = (int) round($w * 295 / 1712);

$html = '<!DOCTYPE html><html><body style="margin:20px">';
$html .= '<p>GD: ' . (extension_loaded('gd') ? 'yes' : 'no') . '</p>';
$html .= '<h3>File path</h3><img src="' . str_replace('\\', '/', $path) . '" width="' . $w . '" height="' . $h . '">';
$html .= '<h3>Base64</h3><img src="data:image/png;base64,' . base64_encode(file_get_contents($path)) . '" width="' . $w . '" height="' . $h . '">';
$html .= '</body></html>';

$pdf = Pdf::loadHTML($html)->setPaper('a4')->setOption('isRemoteEnabled', true);
$out = __DIR__ . '/../backend/storage/app/test-membrete.pdf';
file_put_contents($out, $pdf->output());
echo "Wrote $out size=" . filesize($out) . "\n";
