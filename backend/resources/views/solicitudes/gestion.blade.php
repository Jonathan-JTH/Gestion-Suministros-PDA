@extends('layouts.app')

@section('title', 'Gestión de solicitudes')

@section('content')
<h4 class="mb-3">Gestión de solicitudes (Administración)</h4>

<form class="row g-2 mb-3" method="GET">
    <div class="col-md-3">
        <select name="estado" class="form-select">
            <option value="">Todos los estados</option>
            @foreach(['pendiente','en_proceso','atendida','rechazada'] as $estado)
                <option value="{{ $estado }}" @selected(request('estado') === $estado)>{{ str_replace('_', ' ', $estado) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <select name="sucursal_id" class="form-select">
            <option value="">Todas las sucursales</option>
            @foreach($sucursales as $sucursal)
                <option value="{{ $sucursal->id }}" @selected((string) request('sucursal_id') === (string) $sucursal->id)>{{ $sucursal->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2"><button class="btn btn-outline-primary w-100" type="submit">Filtrar</button></div>
</form>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Sucursal</th>
                    <th>Solicitante</th>
                    <th>Suministro</th>
                    <th>Cant.</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
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
                        <td>{{ $sol->sucursal->nombre ?? '—' }}</td>
                        <td>{{ $sol->nombreSolicitanteMostrar() }}</td>
                        <td>{{ $detalle?->suministro?->nombre ?? '—' }}</td>
                        <td>{{ $detalle?->cantidad ?? '—' }}</td>
                        <td><span class="badge bg-{{ $badge }}">{{ str_replace('_', ' ', $sol->estado) }}</span></td>
                        <td>{{ $sol->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-nowrap">
                            <a href="{{ route('solicitudes.show', $sol) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                            @if($sol->estado === 'pendiente')
                                <form action="{{ route('solicitudes.aprobar', $sol->id) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-success" title="Poner en proceso"><i class="bi bi-play"></i></button></form>
                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rechazar{{ $sol->id }}"><i class="bi bi-x-lg"></i></button>
                            @endif
                            @if($sol->estado === 'en_proceso')
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#atender{{ $sol->id }}">Atender</button>
                            @endif
                        </td>
                    </tr>
                    @if($sol->estado === 'en_proceso')
                        @include('solicitudes._modal-atender', ['sol' => $sol])
                    @endif
                    <div class="modal fade" id="rechazar{{ $sol->id }}" tabindex="-1">
                        <div class="modal-dialog"><div class="modal-content">
                            <form action="{{ route('solicitudes.rechazar', $sol->id) }}" method="POST">@csrf
                                <div class="modal-header"><h5 class="modal-title">Rechazar #{{ $sol->id }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body"><textarea name="observacion" class="form-control" rows="3" placeholder="Motivo"></textarea></div>
                                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-danger">Rechazar</button></div>
                            </form>
                        </div></div>
                    </div>
                @empty
                    <tr><td colspan="8" class="text-center py-4">No hay solicitudes.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
