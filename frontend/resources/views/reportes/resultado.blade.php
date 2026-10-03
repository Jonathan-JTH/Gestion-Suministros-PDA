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
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Reporte: {{ $titulos[$tipo] ?? $tipo }} ({{ $desde }} — {{ $hasta }})</h4>
    <a href="{{ route('reportes.pdf', request()->query()) }}" class="btn btn-outline-danger">Exportar PDF</a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0"><div class="card-body"><canvas id="reporteChart" height="140"></canvas></div></div>
    </div>
    <div class="col-lg-5">
        <div class="card shadow-sm border-0">
            <table class="table mb-0">
                <thead class="table-light"><tr><th>{{ $col }}</th><th>Total</th></tr></thead>
                <tbody>
                    @forelse($filas as $f)
                        <tr>
                            <td>
                                @if($tipo === 'solicitudes')
                                    {{ str_replace('_', ' ', $f->estado) }}
                                @elseif($tipo === 'movimientos')
                                    {{ $f->tipo }}
                                @else
                                    {{ $f->nombre }}
                                @endif
                            </td>
                            <td>{{ $f->total }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-center text-muted">Sin datos en el periodo.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<a href="{{ route('reportes.index') }}" class="btn btn-secondary mt-3">Nuevo reporte</a>
@endsection

@push('scripts')
<script>
new Chart(document.getElementById('reporteChart'), {
    type: 'bar',
    data: { labels: @json($labels), datasets: [{ label: 'Total', data: @json($values), backgroundColor: '#3b82f6' }] },
    options: { scales: { y: { beginAtZero: true } } }
});
</script>
@endpush
