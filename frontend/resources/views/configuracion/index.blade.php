@extends('layouts.app')

@section('title', 'Configuración')

@section('content')
<div class="mb-3">
    <h4 class="page-title mb-0">Configuración</h4>
    <p class="page-meta mb-0">Parámetros del sistema y notificaciones</p>
</div>

<div class="card form-card shadow-sm border-0 mb-3">
    <div class="card-body p-4">
        <h5 class="fw-semibold mb-3">General</h5>
        <ul class="list-group list-group-flush border rounded">
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="text-muted small text-uppercase fw-semibold">Aplicación</span>
                <strong>{{ $appName }}</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="text-muted small text-uppercase fw-semibold">Zona horaria</span>
                <strong>{{ $timezone }}</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="text-muted small text-uppercase fw-semibold">Correo TI (copias)</span>
                <strong class="small">{{ $correoIt ?? '—' }}</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="text-muted small text-uppercase fw-semibold">Stack</span>
                <strong class="small text-end">Laravel 12 · Bootstrap 5 · Chart.js · DomPDF</strong>
            </li>
        </ul>
        <p class="text-muted small mt-3 mb-0">Buzón TI: <code>NOTIFICACION_SOPORTE_EMAIL</code> en <code>.env</code>. Login: reCAPTCHA v2 opcional con <code>RECAPTCHA_*</code>.</p>
    </div>
</div>

<div class="card form-card shadow-sm border-0">
    <div class="card-body p-4">
        <h5 class="fw-semibold mb-1">Notificaciones por correo</h5>
        <p class="text-muted small mb-0">
            La copia a TI es obligatoria cuando los correos están activos (nueva solicitud y al atender).
        </p>

        @if(auth()->user()->isAdmin())
            <form method="POST" action="{{ route('configuracion.notificaciones') }}" class="mt-4">
                @csrf
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="notif_emails"
                           name="notificaciones_email_habilitadas" value="1"
                           @checked($emailsHabilitados)>
                    <label class="form-check-label" for="notif_emails">Habilitar envío de correos de notificación</label>
                    <div class="form-text">Si está apagado, no se envía correo (auditoría en pantalla y bitácora).</div>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="notif_destinatario"
                           name="notificaciones_despacho_destinatario_habilitado" value="1"
                           @checked($despachoDestinatarioHabilitado)>
                    <label class="form-check-label" for="notif_destinatario">Al atender, aviso al solicitante</label>
                    <div class="form-text">Si está apagado, solo se envía copia a TI.</div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Guardar notificaciones</button>
            </form>
        @else
            <ul class="list-group list-group-flush border rounded mt-4">
                <li class="list-group-item d-flex justify-content-between">
                    <span>Correos habilitados</span>
                    <strong>{{ $emailsHabilitados ? 'Sí' : 'No' }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Aviso destinatario al despachar</span>
                    <strong>{{ $despachoDestinatarioHabilitado ? 'Sí' : 'No' }}</strong>
                </li>
            </ul>
            <p class="text-muted small mt-2 mb-0">Solo el administrador puede cambiar estos valores.</p>
        @endif
    </div>
</div>
@endsection
