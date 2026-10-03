@extends('layouts.app')

@section('title', 'Consulta de solicitudes')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h4 class="page-title mb-0">Consulta de solicitudes</h4>
        <p class="page-meta mb-0">{{ $solicitudes->count() }} solicitud(es)</p>
    </div>
    <a href="{{ route('solicitudes.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Nueva solicitud</a>
</div>

<div class="card filter-card shadow-sm mb-3">
    <div class="card-body py-3">
        <form class="row g-2 align-items-end" method="GET">
            <div class="col-md-4">
                <label class="form-label" for="buscar">Número de solicitud</label>
                <input type="number" name="buscar" id="buscar" class="form-control form-control-sm"
                       placeholder="Ej. 42" value="{{ request('buscar') }}" min="1">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-primary btn-sm" type="submit"><i class="bi bi-search"></i> Buscar</button>
                <a href="{{ route('solicitudes.index') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="card card-list-table shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-pro table-striped table-hover table-bordered align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>No.</th>
                    <th>Fecha</th>
                    <th>Suministro</th>
                    <th>Cantidad</th>
                    <th>Estado</th>
                    <th class="text-end">Detalle</th>
                </tr>
            </thead>
            <tbody>
                @forelse($solicitudes as $sol)
                    @php $detalle = $sol->detalles->first(); @endphp
                    <tr>
                        <td class="fw-medium">#{{ $sol->id }}</td>
                        <td class="text-nowrap">{{ $sol->created_at->format('d/m/Y') }}</td>
                        <td>{{ $detalle?->suministro?->nombre ?? '—' }}</td>
                        <td>{{ $detalle?->cantidad ?? '—' }}</td>
                        <td>@include('partials.badge-solicitud-estado', ['estado' => $sol->estado])</td>
                        <td class="text-end">
                            <a href="{{ route('solicitudes.show', $sol) }}" class="btn btn-sm btn-outline-dark">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No hay solicitudes registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
