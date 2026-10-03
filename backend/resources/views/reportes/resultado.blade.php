@extends('layouts.app')

@section('title', 'Resultado de reporte')

@section('content')
@php
    $titulos = [
        'consumo' => 'Consumo por suministro',
        'solicitudes' => 'Solicitudes por estado',
        'sucursal' => 'Solicitudes por sucursal',
        'movimientos' => 'Movimientos de inventario',
        'inventario_bajo' => 'Inventario bajo mínimo',
    ];
    $col = $columna ?? 'Detalle';
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h4 class="page-title mb-0">{{ $titulos[$tipo] ?? $tipo }}</h4>
        <p class="page-meta mb-0">{{ $desde }} — {{ $hasta }}</p>
    </div>
    <a href="{{ route('reportes.pdf', request()->query()) }}" class="btn btn-outline-dark btn-sm"><i class="bi bi-file-pdf"></i> Exportar PDF</a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card form-card shadow-sm border-0">
            <div class="card-body"><canvas id="reporteChart" height="140"></canvas></div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card card-list-table shadow-sm border-0">
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
                </table>
            </div>
        </div>
    </div>
</div>

<a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary btn-sm mt-3">Nuevo reporte</a>
@endsection

@push('scripts')
<script>
new Chart(document.getElementById('reporteChart'), {
    type: 'bar',
    data: { labels: @json($labels), datasets: [{ label: 'Total', data: @json($values), backgroundColor: '#2563eb' }] },
    options: { scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
});
</script>
@endpush
