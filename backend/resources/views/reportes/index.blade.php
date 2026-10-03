@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
<div class="card shadow-sm border-0"><div class="card-body">
    <h4 class="mb-3">Generación de reportes</h4>
    <form method="GET" action="{{ route('reportes.generar') }}" class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Tipo de reporte</label>
            <select name="tipo" class="form-select" required>
                <option value="consumo">Consumo por suministro (atendidas)</option>
                <option value="solicitudes">Solicitudes por estado</option>
                <option value="sucursal">Solicitudes por sucursal</option>
                <option value="movimientos">Movimientos de inventario</option>
                <option value="inventario_bajo">Existencias bajo stock mínimo</option>
            </select>
        </div>
        <div class="col-md-3"><label class="form-label">Desde</label><input type="date" name="desde" class="form-control" required value="{{ now()->subMonth()->format('Y-m-d') }}"></div>
        <div class="col-md-3"><label class="form-label">Hasta</label><input type="date" name="hasta" class="form-control" required value="{{ now()->format('Y-m-d') }}"></div>
        <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100">Generar</button></div>
    </form>
    <p class="text-muted small mt-2 mb-0">El reporte de inventario bajo usa el stock actual; el rango de fechas aplica a los demás.</p>
</div></div>
@endsection
