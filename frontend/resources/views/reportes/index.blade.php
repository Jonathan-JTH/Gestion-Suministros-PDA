@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
<div class="mb-3">
    <h4 class="page-title mb-0">Generación de reportes</h4>
    <p class="page-meta mb-0">Consultas por periodo y exportación PDF</p>
</div>

<div class="card form-card shadow-sm border-0">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('reportes.generar') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label" for="tipo">Tipo de reporte</label>
                <select name="tipo" id="tipo" class="form-select" required>
                    <option value="consumo">Consumo por suministro (atendidas)</option>
                    <option value="solicitudes">Solicitudes por estado</option>
                    <option value="sucursal">Solicitudes por sucursal</option>
                    <option value="movimientos">Movimientos de inventario</option>
                    <option value="inventario_bajo">Existencias bajo stock mínimo</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="desde">Desde</label>
                <input type="date" name="desde" id="desde" class="form-control" required value="{{ now()->subMonth()->format('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="hasta">Hasta</label>
                <input type="date" name="hasta" id="hasta" class="form-control" required value="{{ now()->format('Y-m-d') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary btn-sm w-100" type="submit"><i class="bi bi-bar-chart"></i> Generar</button>
            </div>
        </form>
        <p class="text-muted small mt-3 mb-0">El reporte de inventario bajo usa el stock actual; el rango de fechas aplica a los demás tipos.</p>
    </div>
</div>
@endsection
