@extends('layouts.app')

@section('title', 'Consulta de solicitudes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Consulta de solicitudes</h4>
    <a href="{{ route('solicitudes.create') }}" class="btn btn-primary">Nueva solicitud</a>
</div>

<form class="row g-2 mb-3" method="GET">
    <div class="col-md-4">
        <input type="number" name="buscar" class="form-control" placeholder="Buscar por número de solicitud" value="{{ request('buscar') }}">
    </div>
    <div class="col-md-2">
        <button class="btn btn-outline-primary w-100" type="submit">Buscar</button>
    </div>
</form>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>No.</th>
                    <th>Fecha</th>
                    <th>Suministro</th>
                    <th>Cantidad</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($solicitudes as $sol)
                    @php
                        $detalle = $sol->detalles->first();
                        $badge = match($sol->estado) {
                            'pendiente' => 'warning',
                            'en_proceso' => 'info',
                            'atendida' => 'success',
                            'rechazada' => 'danger',
                            default => 'secondary',
                        };
                    @endphp
                    <tr>
                        <td>#{{ $sol->id }}</td>
                        <td>{{ $sol->created_at->format('d/m/Y') }}</td>
                        <td>{{ $detalle?->suministro?->nombre ?? '—' }}</td>
                        <td>{{ $detalle?->cantidad ?? '—' }}</td>
                        <td><span class="badge bg-{{ $badge }}">{{ str_replace('_', ' ', $sol->estado) }}</span></td>
                        <td><a href="{{ route('solicitudes.show', $sol) }}" class="btn btn-sm btn-outline-primary">Ver</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No hay solicitudes registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
