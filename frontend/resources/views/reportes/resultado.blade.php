@extends('layouts.app')

@section('title', 'Resultado de reporte')

@section('content')
@php
    $col = $columna ?? 'Detalle';
    $grafico = $grafico ?? 'bar';
    $chartType = $grafico === 'horizontal' ? 'bar' : $grafico;
    $titulos = [
        'consumo' => 'Consumo por suministro',
        'solicitudes' => 'Solicitudes por estado',
        'sucursal' => 'Solicitudes por sucursal',
        'movimientos' => 'Movimientos de inventario',
        'inventario_bajo' => 'Inventario bajo mínimo',
    ];
    $estilosGrafica = [
        'bar' => 'Barras verticales',
        'horizontal' => 'Barras horizontales',
        'line' => 'Líneas',
        'pie' => 'Pastel',
        'doughnut' => 'Anillo (doughnut)',
    ];
    $palette = ['#2563eb', '#059669', '#64748b', '#d97706', '#7c3aed', '#dc2626', '#0891b2'];
    $queryBase = request()->only(['tipo', 'desde', 'hasta', 'grafico']);
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h4 class="page-title mb-0">{{ $titulos[$tipo] ?? $tipo }}</h4>
        <p class="page-meta mb-0" id="reporteMeta">{{ $desde }} — {{ $hasta }} · Gráfica: <span id="reporteGraficoLabel">{{ $nombreGrafico ?? $estilosGrafica[$grafico] ?? 'Barras verticales' }}</span></p>
    </div>
    <a id="pdfExport" href="{{ route('reportes.pdf', $queryBase) }}" class="btn btn-outline-dark btn-sm"><i class="bi bi-file-pdf"></i> Exportar PDF</a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card form-card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom py-2 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <span class="small fw-semibold text-uppercase text-muted">Gráfica</span>
                <div class="d-flex align-items-center gap-2">
                    <label class="small text-muted mb-0 text-nowrap" for="graficoResultado">Cambiar estilo</label>
                    <select id="graficoResultado" class="form-select form-select-sm" style="min-width: 11rem;">
                        @foreach($estilosGrafica as $valor => $etiqueta)
                            <option value="{{ $valor }}" @selected($grafico === $valor)>{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="card-body">
                <canvas id="reporteChart" height="160"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card card-list-table shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom py-2 small fw-semibold text-uppercase text-muted">Tabla de datos</div>
            <div class="table-responsive">
                <table class="table table-pro table-bordered align-middle mb-0">
                    <thead class="table-dark">
                        <tr><th>{{ $col }}</th><th class="text-end">Total</th></tr>
                    </thead>
                    <tbody>
                        @forelse($filas as $f)
                            <tr>
                                <td>
                                    @if($tipo === 'solicitudes')
                                        @include('partials.badge-solicitud-estado', ['estado' => $f->estado])
                                    @elseif($tipo === 'movimientos')
                                        <span class="badge text-bg-secondary">{{ $f->tipo }}</span>
                                    @else
                                        {{ $f->nombre }}
                                    @endif
                                </td>
                                <td class="text-end fw-medium">{{ $f->total }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-muted py-4">Sin datos en el periodo.</td></tr>
                        @endforelse
                    </tbody>
                    @if($filas->isNotEmpty())
                        <tfoot>
                            <tr class="table-light">
                                <td class="fw-semibold">Total</td>
                                <td class="text-end fw-bold">{{ $filas->sum('total') }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

<a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary btn-sm mt-3">Nuevo reporte</a>
@endsection

@push('scripts')
<script>
(function () {
    const labels = @json($labels);
    const values = @json($values);
    const palette = @json($palette);
    const estilosGrafica = @json($estilosGrafica);

    let chartInstance = null;
    let graficoActual = @json($grafico);

    function chartTypeFor(grafico) {
        return grafico === 'horizontal' ? 'bar' : grafico;
    }

    function buildOptions(grafico, chartType) {
        const options = {
            responsive: true,
            plugins: { legend: { display: ['pie', 'doughnut'].includes(chartType) } },
            scales: {},
        };
        if (chartType === 'bar') {
            if (grafico === 'horizontal') {
                options.indexAxis = 'y';
            } else {
                options.scales = { y: { beginAtZero: true } };
            }
        } else if (chartType === 'line') {
            options.scales = { y: { beginAtZero: true } };
        }
        return options;
    }

    function buildDataset(chartType) {
        return {
            label: 'Total',
            data: values,
            backgroundColor: chartType === 'line' ? 'rgba(37, 99, 235, 0.15)' : palette.slice(0, values.length),
            borderColor: chartType === 'line' ? '#2563eb' : palette.slice(0, values.length),
            borderWidth: chartType === 'line' ? 2 : 1,
            fill: chartType === 'line',
            tension: 0.25,
        };
    }

    function renderChart(grafico) {
        const chartType = chartTypeFor(grafico);
        if (chartInstance) {
            chartInstance.destroy();
        }
        chartInstance = new Chart(document.getElementById('reporteChart'), {
            type: chartType,
            data: { labels, datasets: [buildDataset(chartType)] },
            options: buildOptions(grafico, chartType),
        });
    }

    function updatePdfLink(grafico) {
        const link = document.getElementById('pdfExport');
        const url = new URL(link.href, window.location.origin);
        url.searchParams.set('grafico', grafico);
        link.href = url.pathname + url.search;
    }

    function syncUrl(grafico) {
        const url = new URL(window.location.href);
        url.searchParams.set('grafico', grafico);
        window.history.replaceState({}, '', url);
    }

    function updateLabel(grafico) {
        document.getElementById('reporteGraficoLabel').textContent = estilosGrafica[grafico] || grafico;
    }

    document.getElementById('graficoResultado').addEventListener('change', function () {
        graficoActual = this.value;
        renderChart(graficoActual);
        updatePdfLink(graficoActual);
        syncUrl(graficoActual);
        updateLabel(graficoActual);
    });

    renderChart(graficoActual);
})();
</script>
@endpush
