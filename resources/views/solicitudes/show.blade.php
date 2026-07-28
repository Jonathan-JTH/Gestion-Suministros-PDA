@extends('layouts.app')

@section('title', 'Detalle Solicitud')

@section('content')
<h3>Detalle de Solicitud #{{ $solicitud->id }}</h3>

<ul class="list-group mb-3">
    <li class="list-group-item"><strong>Usuario:</strong> {{ $solicitud->usuario->nombre }}</li>
    <li class="list-group-item"><strong>Sucursal:</strong> {{ $solicitud->sucursal->nombre }}</li>
    <li class="list-group-item"><strong>Estado:</strong> {{ $solicitud->estado }}</li>
    <li class="list-group-item"><strong>Fecha:</strong> {{ $solicitud->created_at->format('d/m/Y H:i') }}</li>
    @if($solicitud->observacion)
        <li class="list-group-item"><strong>Observación:</strong> {{ $solicitud->observacion }}</li>
    @endif
</ul>

<h5>Detalle de productos:</h5>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Impresora</th>
            <th>Tipo Movimiento</th>
            <th>Cantidad</th>
        </tr>
    </thead>
    <tbody>
        @foreach($solicitud->detalles as $detalle)
        <tr>
            <td>{{ $detalle->impresora->modelo }} - {{ $detalle->impresora->serie }}</td>
            <td>{{ $detalle->tipo_movimiento }}</td>
            <td>{{ $detalle->cantidad }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@if($esAdmin ?? false)
    <div class="d-flex gap-2 mb-3">
        @if($solicitud->estado === 'pendiente')
            <form action="{{ route('solicitudes.aprobar', $solicitud->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success">Aprobar</button>
            </form>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rechazarModal">
                Rechazar
            </button>
        @endif

        @if($solicitud->estado === 'aprobado')
            <form action="{{ route('solicitudes.despachar', $solicitud->id) }}" method="POST"
                  onsubmit="return confirm('¿Confirmar despacho? Se actualizará el inventario.')">
                @csrf
                <button type="submit" class="btn btn-primary">Despachar</button>
            </form>
        @endif
    </div>

    @if($solicitud->estado === 'pendiente')
    <div class="modal fade" id="rechazarModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('solicitudes.rechazar', $solicitud->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Rechazar solicitud</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label for="observacion" class="form-label">Motivo (opcional)</label>
                        <textarea class="form-control" id="observacion" name="observacion" rows="3"></textarea>
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

    <a href="{{ route('solicitudes.gestion') }}" class="btn btn-secondary">Volver</a>
@else
    <a href="{{ route('solicitudes.index') }}" class="btn btn-primary">Volver</a>
@endif
@endsection
