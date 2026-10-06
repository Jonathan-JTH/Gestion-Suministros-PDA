<?php

require __DIR__ . '/../backend/vendor/autoload.php';
$app = require __DIR__ . '/../backend/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Support\ReporteMembretePdf;
use Barryvdh\DomPDF\Facade\Pdf;

$membrete = ReporteMembretePdf::render();
if ($membrete['header'] === '') {
    echo "ERROR: membrete header empty (GD=" . (extension_loaded('gd') ? 'yes' : 'no') . ")\n";
    exit(1);
}

$html = view('reportes.pdf', [
    'tipo' => 'movimientos',
    'desde' => '2026-09-06',
    'hasta' => '2026-10-06',
    'tituloReporte' => 'Movimientos de inventario',
    'nombreGrafico' => 'Barras verticales',
    'membrete' => $membrete,
    'graficoSvg' => \App\Support\ReporteGraficoSvg::renderForPdf('bar', ['entrada', 'salida'], [2, 1]),
    'filas' => collect([
        (object) ['tipo' => 'entrada', 'total' => 2],
        (object) ['tipo' => 'salida', 'total' => 1],
    ]),
    'columna' => 'Tipo de movimiento',
])->render();

$pdf = Pdf::loadHTML($html)->setPaper('a4')->setOption('isRemoteEnabled', true);
$out = __DIR__ . '/../backend/storage/app/test-reporte-full.pdf';
file_put_contents($out, $pdf->output());
echo "OK $out size=" . filesize($out) . " gd=" . (extension_loaded('gd') ? 'yes' : 'no') . "\n";
