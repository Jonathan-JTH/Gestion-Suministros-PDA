@extends('layouts.app')

@section('title', 'Panel administrativo')

@section('content')
<div class="mb-3">
    <h4 class="page-title mb-0">Panel administrativo</h4>
    <p class="page-meta mb-0">Resumen operativo</p>
</div>

<div class="row g-3 mb-4">
    @php
        $estadoKpis = [
            ['label' => 'Pendientes', 'value' => $pendientes, 'color' => '#64748b'],
            ['label' => 'En proceso', 'value' => $enProceso, 'color' => '#2563eb'],
            ['label' => 'Atendidas', 'value' => $atendidas, 'color' => '#059669'],
            ['label' => 'Rechazadas', 'value' => $rechazadas, 'color' => '#334155'],
        ];
        $otrosKpis = [
            ['label' => 'Suministros activos', 'value' => $totalSuministros, 'color' => '#1f2937'],
            ['label' => 'Usuarios activos', 'value' => $totalUsuarios, 'color' => '#475569'],
            ['label' => 'Alertas stock bajo', 'value' => $inventarioBajo, 'color' => '#b45309'],
        ];
    @endphp
    @foreach(array_merge($estadoKpis, $otrosKpis) as $kpi)
        <div class="col-md-3 col-sm-6">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body d-flex gap-3 align-items-center py-3">
                    <div class="kpi-accent" style="background: {{ $kpi['color'] }};"></div>
                    <div>
                        <div class="kpi-label">{{ $kpi['label'] }}</div>
                        <div class="kpi-value">{{ $kpi['value'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 h-100 form-card">
            <div class="card-header bg-white border-bottom py-3 fw-semibold small text-uppercase text-muted">Solicitudes por estado</div>
            <div class="card-body"><canvas id="chartEstados" height="220"></canvas></div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 h-100 form-card">
            <div class="card-header bg-white border-bottom py-3 fw-semibold small text-uppercase text-muted">Movimientos de inventario (6 meses)</div>
            <div class="card-body"><canvas id="chartMovimientos" height="220"></canvas></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const chartPalette = ['#64748b', '#2563eb', '#059669', '#334155'];
new Chart(document.getElementById('chartEstados'), {
    type: 'doughnut',
    data: {
        labels: @json($estadosLabels),
        datasets: [{ data: @json($estadosValues), backgroundColor: chartPalette, borderWidth: 0 }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});
new Chart(document.getElementById('chartMovimientos'), {
    type: 'bar',
    data: {
        labels: @json($chartMeses),
        datasets: [
            { label: 'Entradas', data: @json($chartEntradas), backgroundColor: '#059669' },
            { label: 'Salidas', data: @json($chartSalidas), backgroundColor: '#475569' }
        ]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
});
</script>
@endpush
