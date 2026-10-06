<?php

namespace App\Http\Controllers;

use App\Support\ReporteMembretePdf;
use App\Models\DetalleSolicitud;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Models\Solicitud;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReporteController extends Controller
{
    private const TIPOS = ['consumo', 'solicitudes', 'sucursal', 'movimientos', 'inventario_bajo'];

    private const GRAFICOS = ['bar', 'horizontal', 'line', 'pie', 'doughnut'];

    public function index()
    {
        if (! auth()->user()->tienePermiso('reportes.generar')) {
            abort(403);
        }

        return view('reportes.index');
    }

    public function generar(Request $request)
    {
        $data = $this->validatedReport($request);

        $payload = $this->buildReport($data['tipo'], $data['desde'], $data['hasta']);

        return view('reportes.resultado', array_merge($payload, [
            'tipo' => $data['tipo'],
            'desde' => $data['desde'],
            'hasta' => $data['hasta'],
            'grafico' => $data['grafico'],
            'nombreGrafico' => self::nombreGrafico($data['grafico']),
        ]));
    }

    public function pdf(Request $request)
    {
        if (! extension_loaded('gd')) {
            return redirect()
                ->back()
                ->with('error', 'El PDF con membrete oficial requiere la extensión GD en PHP. Active extension=gd en php.ini, ejecute scripts/check-php-gd.php y reinicie php artisan serve.');
        }

        $data = $this->validatedReport($request);

        $payload = $this->buildReport($data['tipo'], $data['desde'], $data['hasta']);

        $pdf = Pdf::loadView('reportes.pdf', array_merge($payload, $data, [
            'graficoSvg' => \App\Support\ReporteGraficoSvg::renderForPdf(
                $data['grafico'],
                $payload['labels'],
                $payload['values'],
                530,
                200
            ),
            'tituloReporte' => self::tituloReporte($data['tipo']),
            'nombreGrafico' => self::nombreGrafico($data['grafico']),
            'membrete' => ReporteMembretePdf::render(),
        ]))
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', true);

        return $pdf->download('reporte-' . $data['tipo'] . '.pdf');
    }

    private function validatedReport(Request $request): array
    {
        $data = $request->validate([
            'tipo' => ['required', Rule::in(self::TIPOS)],
            'desde' => 'required|date',
            'hasta' => 'required|date|after_or_equal:desde',
            'grafico' => ['nullable', Rule::in(self::GRAFICOS)],
        ]);

        $data['grafico'] = $data['grafico'] ?? 'bar';

        return $data;
    }

    public static function tituloReporte(string $tipo): string
    {
        return match ($tipo) {
            'consumo' => 'Consumo por suministro',
            'solicitudes' => 'Solicitudes por estado',
            'sucursal' => 'Solicitudes por sucursal',
            'movimientos' => 'Movimientos de inventario',
            'inventario_bajo' => 'Inventario bajo stock mínimo',
            default => $tipo,
        };
    }

    public static function nombreGrafico(string $grafico): string
    {
        return match ($grafico) {
            'horizontal' => 'Barras horizontales',
            'line' => 'Líneas',
            'pie' => 'Pastel',
            'doughnut' => 'Anillo',
            default => 'Barras verticales',
        };
    }

    private function buildReport(string $tipo, string $desde, string $hasta): array
    {
        $desdeDt = $desde . ' 00:00:00';
        $hastaDt = $hasta . ' 23:59:59';

        return match ($tipo) {
            'consumo' => $this->reporteConsumo($desdeDt, $hastaDt),
            'solicitudes' => $this->reporteSolicitudesPorEstado($desdeDt, $hastaDt),
            'sucursal' => $this->reporteSolicitudesPorSucursal($desdeDt, $hastaDt),
            'movimientos' => $this->reporteMovimientos($desdeDt, $hastaDt),
            'inventario_bajo' => $this->reporteInventarioBajo(),
            default => ['filas' => collect(), 'labels' => [], 'values' => [], 'columna' => '—'],
        };
    }

    private function reporteConsumo(string $desde, string $hasta): array
    {
        $filas = DetalleSolicitud::query()
            ->select('suministros.nombre', DB::raw('SUM(detalle_solicitudes.cantidad) as total'))
            ->join('solicitudes', 'solicitudes.id', '=', 'detalle_solicitudes.solicitud_id')
            ->join('suministros', 'suministros.id', '=', 'detalle_solicitudes.suministro_id')
            ->where('solicitudes.estado', 'atendida')
            ->whereBetween('solicitudes.created_at', [$desde, $hasta])
            ->groupBy('suministros.nombre')
            ->orderByDesc('total')
            ->get();

        return $this->chartPayload($filas, 'nombre', 'Suministro');
    }

    private function reporteSolicitudesPorEstado(string $desde, string $hasta): array
    {
        $filas = Solicitud::select('estado', DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$desde, $hasta])
            ->groupBy('estado')
            ->orderBy('estado')
            ->get();

        return $this->chartPayload($filas, 'estado', 'Estado', fn ($v) => str_replace('_', ' ', $v));
    }

    private function reporteSolicitudesPorSucursal(string $desde, string $hasta): array
    {
        $filas = Solicitud::query()
            ->select('sucursales.nombre', DB::raw('count(*) as total'))
            ->join('sucursales', 'sucursales.id', '=', 'solicitudes.sucursal_id')
            ->whereBetween('solicitudes.created_at', [$desde, $hasta])
            ->groupBy('sucursales.nombre')
            ->orderByDesc('total')
            ->get();

        return $this->chartPayload($filas, 'nombre', 'Sucursal');
    }

    private function reporteMovimientos(string $desde, string $hasta): array
    {
        $filas = MovimientoInventario::select('tipo', DB::raw('count(*) as total'))
            ->whereBetween('fecha_movimiento', [$desde, $hasta])
            ->groupBy('tipo')
            ->orderBy('tipo')
            ->get();

        return $this->chartPayload($filas, 'tipo', 'Tipo de movimiento');
    }

    private function reporteInventarioBajo(): array
    {
        $filas = Inventario::with(['sucursal', 'suministro'])
            ->get()
            ->filter(fn ($i) => $i->bajoStockMinimo())
            ->map(fn ($i) => (object) [
                'nombre' => ($i->sucursal?->nombre ?? '—') . ' — ' . ($i->suministro?->nombre ?? '—'),
                'total' => $i->cantidad,
            ])
            ->values();

        return $this->chartPayload($filas, 'nombre', 'Sucursal — Suministro');
    }

    private function chartPayload($filas, string $labelKey, string $columna, ?callable $labelFormat = null): array
    {
        $labels = $filas->pluck($labelKey)->map(fn ($v) => $labelFormat ? $labelFormat($v) : $v)->all();

        return [
            'filas' => $filas,
            'labels' => $labels,
            'values' => $filas->pluck('total')->map(fn ($v) => (int) $v)->all(),
            'columna' => $columna,
        ];
    }
}
