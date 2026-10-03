@extends('layouts.app')

@section('title', 'Panel administrativo')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card kpi-card bg-warning"><div class="card-body"><div class="small opacity-75">Pendientes</div><div class="display-6 fw-bold">{{ $pendientes }}</div></div></div></div>
    <div class="col-md-3"><div class="card kpi-card bg-info"><div class="card-body"><div class="small opacity-75">En proceso</div><div class="display-6 fw-bold">{{ $enProceso }}</div></div></div></div>
    <div class="col-md-3"><div class="card kpi-card bg-success"><div class="card-body"><div class="small opacity-75">Atendidas</div><div class="display-6 fw-bold">{{ $atendidas }}</div></div></div></div>
    <div class="col-md-3"><div class="card kpi-card bg-danger"><div class="card-body"><div class="small opacity-75">Rechazadas</div><div class="display-6 fw-bold">{{ $rechazadas }}</div></div></div></div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card kpi-card bg-primary"><div class="card-body"><div class="small opacity-75">Suministros activos</div><div class="display-6 fw-bold">{{ $totalSuministros }}</div></div></div></div>
    <div class="col-md-3"><div class="card kpi-card bg-dark"><div class="card-body"><div class="small opacity-75">Usuarios activos</div><div class="display-6 fw-bold">{{ $totalUsuarios }}</div></div></div></div>
    <div class="col-md-3"><div class="card kpi-card bg-secondary"><div class="card-body"><div class="small opacity-75">Alertas stock bajo</div><div class="display-6 fw-bold">{{ $inventarioBajo }}</div></div></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-semibold">Solicitudes por estado</div>
            <div class="card-body"><canvas id="chartEstados" height="220"></canvas></div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-semibold">Movimientos de inventario (6 meses)</div>
            <div class="card-body"><canvas id="chartMovimientos" height="220"></canvas></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
new Chart(document.getElementById('chartEstados'), {
    type: 'doughnut',
    data: {
        labels: @json($estadosLabels),
        datasets: [{ data: @json($estadosValues), backgroundColor: ['#f59e0b','#3b82f6','#10b981','#ef4444'] }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});
new Chart(document.getElementById('chartMovimientos'), {
    type: 'bar',
    data: {
        labels: @json($chartMeses),
        datasets: [
            { label: 'Entradas', data: @json($chartEntradas), backgroundColor: '#10b981' },
            { label: 'Salidas', data: @json($chartSalidas), backgroundColor: '#ef4444' }
        ]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
});
</script>
@endpush
