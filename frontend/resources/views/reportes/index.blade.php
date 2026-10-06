@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
<div class="mb-3">
    <h4 class="page-title mb-0">Generación de reportes</h4>
</div>

<div class="card form-card shadow-sm border-0">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('reportes.generar') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label" for="tipo">Tipo de reporte</label>
                <select name="tipo" id="tipo" class="form-select" required>
                    <option value="consumo" @selected(old('tipo') === 'consumo')>Consumo por suministro (atendidas)</option>
                    <option value="solicitudes" @selected(old('tipo') === 'solicitudes')>Solicitudes por estado</option>
                    <option value="sucursal" @selected(old('tipo') === 'sucursal')>Solicitudes por sucursal</option>
                    <option value="movimientos" @selected(old('tipo') === 'movimientos')>Movimientos de inventario</option>
                    <option value="inventario_bajo" @selected(old('tipo') === 'inventario_bajo')>Existencias bajo stock mínimo</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="grafico">Estilo de gráfica</label>
                <select name="grafico" id="grafico" class="form-select">
                    @php $g = request('grafico', 'bar'); @endphp
                    <option value="bar" @selected($g === 'bar')>Barras verticales</option>
                    <option value="horizontal" @selected($g === 'horizontal')>Barras horizontales</option>
                    <option value="line" @selected($g === 'line')>Líneas</option>
                    <option value="pie" @selected($g === 'pie')>Pastel</option>
                    <option value="doughnut" @selected($g === 'doughnut')>Anillo (doughnut)</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label" for="desde">Desde</label>
                <input type="date" name="desde" id="desde" class="form-control" required value="{{ old('desde', now()->subMonth()->format('Y-m-d')) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label" for="hasta">Hasta</label>
                <input type="date" name="hasta" id="hasta" class="form-control" required value="{{ old('hasta', now()->format('Y-m-d')) }}">
            </div>
            <div class="col-md-1">
                <button class="btn btn-primary btn-sm w-100" type="submit" title="Generar"><i class="bi bi-bar-chart"></i></button>
            </div>
        </form>
    </div>
</div>
@endsection
