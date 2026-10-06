<?php

require __DIR__ . '/../backend/vendor/autoload.php';
$app = require __DIR__ . '/../backend/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Support\ReporteGraficoSvg;
use Barryvdh\DomPDF\Facade\Pdf;

$labels = ['Tóner HP 85A', 'Papel A4'];
$values = [5, 3];
$embed = ReporteGraficoSvg::renderForPdf('pie', $labels, $values);

$html = '<!DOCTYPE html><html><body><h1>Test PDF embed</h1>' . $embed . '</body></html>';

$pdf = Pdf::loadHTML($html)->setPaper('a4');
file_put_contents(__DIR__ . '/../backend/storage/app/test-reporte-svg.pdf', $pdf->output());

echo "OK wrote test-reporte-svg.pdf\n";
echo "SVG length: " . strlen($svg) . "\n";
