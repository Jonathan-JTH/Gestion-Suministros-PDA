@extends('layouts.app')

@section('title', 'Gestión de solicitudes')

@section('content')
<div class="mb-3">
    <h4 class="page-title mb-0">Gestión de solicitudes</h4>
    <p class="page-meta mb-0">Administración · {{ $solicitudes->count() }} registro(s)</p>
</div>

<div class="card filter-card shadow-sm mb-3">
    <div class="card-body py-3">
        <form class="row g-2 align-items-end" method="GET">
            <div class="col-md-3">
                <label class="form-label" for="estado">Estado</label>
                <select name="estado" id="estado" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    @foreach(['pendiente','en_proceso','atendida','rechazada'] as $estado)
                        <option value="{{ $estado }}" @selected(request('estado') === $estado)>{{ ucfirst(str_replace('_', ' ', $estado)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="sucursal_id">Sucursal</label>
                <select name="sucursal_id" id="sucursal_id" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    @foreach($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}" @selected((string) request('sucursal_id') === (string) $sucursal->id)>{{ $sucursal->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary btn-sm w-100" type="submit"><i class="bi bi-funnel"></i> Filtrar</button>
                <a href="{{ route('solicitudes.gestion') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="card card-list-table shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-pro table-striped table-hover table-bordered align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Sucursal</th>
                    <th>Solicitante</th>
                    <th>Suministro</th>
                    <th>Cant.</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($solicitudes as $sol)
                    @php $detalle = $sol->detalles->first(); @endphp
                    <tr>
                        <td class="fw-medium">#{{ $sol->id }}</td>
                        <td>{{ $sol->sucursal->nombre ?? '—' }}</td>
                        <td>{{ $sol->nombreSolicitanteMostrar() }}</td>
                        <td>{{ $detalle?->suministro?->nombre ?? '—' }}</td>
                        <td>{{ $detalle?->cantidad ?? '—' }}</td>
                        <td>@include('partials.badge-solicitud-estado', ['estado' => $sol->estado])</td>
                        <td class="text-nowrap small">{{ $sol->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('solicitudes.show', $sol) }}" class="btn btn-sm btn-outline-dark" title="Ver"><i class="bi bi-eye"></i></a>
                            @if($sol->estado === 'pendiente')
                                <form action="{{ route('solicitudes.aprobar', $sol->id) }}" method="POST" class="d-inline">@csrf
                                    <button class="btn btn-sm btn-outline-success" title="Poner en proceso"><i class="bi bi-play-fill"></i></button>
                                </form>
                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rechazar{{ $sol->id }}" title="Rechazar"><i class="bi bi-x-lg"></i></button>
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
                    <tr><td colspan="8" class="text-center text-muted py-4">No hay solicitudes con esos filtros.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
