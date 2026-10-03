@extends('layouts.app')

@section('title', 'Solicitud #' . $solicitud->id)

@section('content')
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
        <h4>Solicitud #{{ $solicitud->id }}</h4>
        <div class="row mt-3">
            <div class="col-md-6"><strong>Solicitante:</strong> {{ $solicitud->nombreSolicitanteMostrar() }}</div>
            <div class="col-md-6"><strong>Sucursal:</strong> {{ $solicitud->sucursal->nombre }}</div>
            <div class="col-md-6 mt-2"><strong>Correo:</strong> {{ $solicitud->correoSolicitanteMostrar() ?? '—' }}</div>
            <div class="col-md-6 mt-2"><strong>Tipo:</strong> {{ $solicitud->tipo_movimiento ?? '—' }}</div>
            @if($solicitud->ubicacion)
                <div class="col-md-6 mt-2"><strong>Ubicación:</strong> {{ $solicitud->ubicacion }}</div>
            @endif
            @if($solicitud->puesto_area)
                <div class="col-md-6 mt-2"><strong>Puesto/área:</strong> {{ $solicitud->puesto_area }}</div>
            @endif
            @if($solicitud->modelo_equipo)
                <div class="col-md-6 mt-2"><strong>Modelo equipo:</strong> {{ $solicitud->modelo_equipo }}</div>
            @endif
            <div class="col-md-6 mt-2"><strong>Estado:</strong> {{ str_replace('_', ' ', $solicitud->estado) }}</div>
            <div class="col-md-6 mt-2"><strong>Fecha:</strong> {{ $solicitud->created_at->format('d/m/Y H:i') }}</div>
            @if($solicitud->observacion)
                <div class="col-12 mt-2"><strong>Observaciones:</strong> {{ $solicitud->observacion }}</div>
            @endif
            @if($solicitud->estado === 'atendida' && $solicitud->despachado_at)
                <div class="col-12 mt-3 pt-3 border-top">
                    <h6 class="text-muted mb-2">Auditoría de despacho (TI)</h6>
                    <div class="row">
                        <div class="col-md-6"><strong>Despachado por:</strong> {{ $solicitud->despachadoPor->nombre ?? '—' }}</div>
                        <div class="col-md-6"><strong>Fecha despacho:</strong> {{ $solicitud->despachado_at->format('d/m/Y H:i') }}</div>
                        <div class="col-md-6 mt-2"><strong>Correo notificado:</strong> {{ $solicitud->correo_destinatario_despacho ?? '—' }}</div>
                        @if($solicitud->nota_despacho)
                            <div class="col-12 mt-2"><strong>Nota enviada en correo:</strong> {{ $solicitud->nota_despacho }}</div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<table class="table table-bordered bg-white">
    <thead class="table-light"><tr><th>Suministro</th><th>Cantidad</th></tr></thead>
    <tbody>
        @foreach($solicitud->detalles as $detalle)
            <tr>
                <td>{{ $detalle->suministro->nombre ?? '—' }}</td>
                <td>{{ $detalle->cantidad }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@if($esAdmin && $solicitud->estado === 'en_proceso')
    @include('solicitudes._modal-atender', ['sol' => $solicitud])
    <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#atender{{ $solicitud->id }}">Atender solicitud</button>
@endif

@if($esAdmin)
    <a href="{{ route('solicitudes.gestion') }}" class="btn btn-secondary">Volver</a>
@else
    <a href="{{ route('solicitudes.index') }}" class="btn btn-secondary">Volver</a>
@endif
@endsection
