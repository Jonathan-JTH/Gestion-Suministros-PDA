@extends('layouts.app')

@section('title', 'Solicitud #' . $solicitud->id)

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
    <div>
        <h4 class="page-title mb-1">Solicitud #{{ $solicitud->id }}</h4>
        <p class="page-meta mb-0">{{ $solicitud->created_at->format('d/m/Y H:i') }}</p>
    </div>
    @include('partials.badge-solicitud-estado', ['estado' => $solicitud->estado])
</div>

<div class="card form-card shadow-sm border-0 mb-3">
    <div class="card-body p-4">
        <dl class="row detail-dl mb-0">
            <div class="col-md-6">
                <dt>Solicitante</dt>
                <dd>{{ $solicitud->nombreSolicitanteMostrar() }}</dd>
            </div>
            <div class="col-md-6">
                <dt>Sucursal</dt>
                <dd>{{ $solicitud->sucursal->nombre }}</dd>
            </div>
            <div class="col-md-6">
                <dt>Correo</dt>
                <dd>{{ $solicitud->correoSolicitanteMostrar() ?? '—' }}</dd>
            </div>
            <div class="col-md-6">
                <dt>Tipo de movimiento</dt>
                <dd>{{ $solicitud->tipo_movimiento ?? '—' }}</dd>
            </div>
            @if($solicitud->ubicacion)
                <div class="col-md-6">
                    <dt>Ubicación</dt>
                    <dd>{{ $solicitud->ubicacion }}</dd>
                </div>
            @endif
            @if($solicitud->puesto_area)
                <div class="col-md-6">
                    <dt>Puesto / área</dt>
                    <dd>{{ $solicitud->puesto_area }}</dd>
                </div>
            @endif
            @if($solicitud->modelo_equipo)
                <div class="col-md-6">
                    <dt>Modelo equipo</dt>
                    <dd>{{ $solicitud->modelo_equipo }}</dd>
                </div>
            @endif
            @if($solicitud->observacion)
                <div class="col-12">
                    <dt>Observaciones</dt>
                    <dd>{{ $solicitud->observacion }}</dd>
                </div>
            @endif
        </dl>

        @if($solicitud->estado === 'atendida' && $solicitud->despachado_at)
            <hr class="my-3">
            <h6 class="text-muted text-uppercase small fw-semibold mb-3">Auditoría de despacho (TI)</h6>
            <dl class="row detail-dl mb-0">
                <div class="col-md-6">
                    <dt>Despachado por</dt>
                    <dd>{{ $solicitud->despachadoPor->nombre ?? '—' }}</dd>
                </div>
                <div class="col-md-6">
                    <dt>Fecha despacho</dt>
                    <dd>{{ $solicitud->despachado_at->format('d/m/Y H:i') }}</dd>
                </div>
                <div class="col-md-6">
                    <dt>Correo notificado</dt>
                    <dd>{{ $solicitud->correo_destinatario_despacho ?? '—' }}</dd>
                </div>
                @if($solicitud->nota_despacho)
                    <div class="col-12">
                        <dt>Nota en correo</dt>
                        <dd>{{ $solicitud->nota_despacho }}</dd>
                    </div>
                @endif
            </dl>
        @endif
    </div>
</div>

<div class="card card-list-table shadow-sm border-0 mb-3">
    <div class="table-responsive">
        <table class="table table-pro table-bordered align-middle mb-0">
            <thead class="table-dark">
                <tr><th>Suministro</th><th style="width: 8rem;">Cantidad</th></tr>
            </thead>
            <tbody>
                @foreach($solicitud->detalles as $detalle)
                    <tr>
                        <td class="fw-medium">{{ $detalle->suministro->nombre ?? '—' }}</td>
                        <td>{{ $detalle->cantidad }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex flex-wrap gap-2">
    @if($esAdmin && $solicitud->estado === 'en_proceso')
        @include('solicitudes._modal-atender', ['sol' => $solicitud])
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#atender{{ $solicitud->id }}">Atender solicitud</button>
    @endif
    @if($esAdmin)
        <a href="{{ route('solicitudes.gestion') }}" class="btn btn-outline-secondary btn-sm">Volver a gestión</a>
    @else
        <a href="{{ route('solicitudes.index') }}" class="btn btn-outline-secondary btn-sm">Volver</a>
    @endif
</div>
@endsection
