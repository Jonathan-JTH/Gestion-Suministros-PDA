@extends('layouts.app')

@section('title', 'Gestión de Solicitudes')

@section('content')
<h3>Gestión de Solicitudes</h3>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Sucursal</th>
            <th>Impresora(s)</th>
            <th>Tipo</th>
            <th>Cantidad</th>
            <th>Estado</th>
            <th>Fecha</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse($solicitudes as $sol)
        <tr>
            <td>{{ $sol->id }}</td>
            <td>{{ $sol->usuario->nombre ?? '—' }}</td>
            <td>{{ $sol->sucursal->nombre ?? '—' }}</td>
            <td>
                @foreach($sol->detalles as $detalle)
                    {{ $detalle->impresora->modelo }} - {{ $detalle->impresora->serie }}@if(!$loop->last), @endif
                @endforeach
            </td>
            <td>
                @foreach($sol->detalles as $detalle)
                    {{ $detalle->tipo_movimiento }}@if(!$loop->last), @endif
                @endforeach
            </td>
            <td>
                @foreach($sol->detalles as $detalle)
                    {{ $detalle->cantidad }}@if(!$loop->last), @endif
                @endforeach
            </td>
            <td>
                @php
                    $badge = match($sol->estado) {
                        'pendiente' => 'warning',
                        'aprobado' => 'info',
                        'rechazado' => 'danger',
                        'despachado' => 'success',
                        default => 'secondary',
                    };
                @endphp
                <span class="badge bg-{{ $badge }}">{{ $sol->estado }}</span>
            </td>
            <td>{{ $sol->created_at->format('d/m/Y H:i') }}</td>
            <td class="text-nowrap">
                <a href="{{ route('solicitudes.show', $sol) }}" class="btn btn-sm btn-outline-primary">Ver</a>

                @if($sol->estado === 'pendiente')
                    <form action="{{ route('solicitudes.aprobar', $sol->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success">Aprobar</button>
                    </form>

                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rechazarModal{{ $sol->id }}">
                        Rechazar
                    </button>

                    <div class="modal fade" id="rechazarModal{{ $sol->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('solicitudes.rechazar', $sol->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Rechazar solicitud #{{ $sol->id }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <label for="observacion{{ $sol->id }}" class="form-label">Motivo (opcional)</label>
                                        <textarea class="form-control" id="observacion{{ $sol->id }}" name="observacion" rows="3"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-danger">Confirmar rechazo</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                @if($sol->estado === 'aprobado')
                    <form action="{{ route('solicitudes.despachar', $sol->id) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('¿Confirmar despacho de la solicitud #{{ $sol->id }}? Se actualizará el inventario.')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-primary">Despachar</button>
                    </form>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9" class="text-center">No hay solicitudes registradas.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
