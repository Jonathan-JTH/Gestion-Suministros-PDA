@extends('layouts.app')

@section('title', 'Configuración')

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card shadow-sm border-0 mb-3"><div class="card-body">
    <h4>Configuración general</h4>
    <ul class="list-group list-group-flush mt-3">
        <li class="list-group-item d-flex justify-content-between"><span>Nombre de la aplicación</span><strong>{{ $appName }}</strong></li>
        <li class="list-group-item d-flex justify-content-between"><span>Zona horaria</span><strong>{{ $timezone }}</strong></li>
        <li class="list-group-item d-flex justify-content-between"><span>Correo TI (copias)</span><strong>{{ $correoIt ?? '—' }}</strong></li>
        <li class="list-group-item d-flex justify-content-between"><span>Stack</span><strong>Laravel 12 · PHP 8 · Bootstrap 5 · Chart.js · DomPDF</strong></li>
    </ul>
    <p class="text-muted small mt-3 mb-0">El buzón TI se define en <code>NOTIFICACION_SOPORTE_EMAIL</code> del archivo <code>.env</code>.</p>
</div></div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <h4 class="mb-1">Notificaciones por correo</h4>
        <p class="text-muted small">
            La <strong>copia a TI</strong> es obligatoria siempre que los correos estén habilitados (nueva solicitud y al atender).
            Puede desactivar todo el envío o solo el aviso manual al correo que indique soporte al atender la solicitud.
        </p>

        @if(auth()->user()->isAdmin())
            <form method="POST" action="{{ route('configuracion.notificaciones') }}" class="mt-3">
                @csrf
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="notif_emails"
                           name="notificaciones_email_habilitadas" value="1"
                           @checked($emailsHabilitados)>
                    <label class="form-check-label" for="notif_emails">
                        Habilitar envío de correos de notificación
                    </label>
                    <div class="form-text">Si está apagado, no se envía ningún correo (sigue la auditoría en pantalla y bitácora).</div>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="notif_destinatario"
                           name="notificaciones_despacho_destinatario_habilitado" value="1"
                           @checked($despachoDestinatarioHabilitado)>
                    <label class="form-check-label" for="notif_destinatario">
                        Al atender, permitir aviso manual (“su solicitud ha sido atendida”)
                    </label>
                    <div class="form-text">Si está apagado, al atender solo se envía la copia obligatoria a TI.</div>
                </div>
                <button type="submit" class="btn btn-primary">Guardar notificaciones</button>
            </form>
        @else
            <ul class="list-group list-group-flush mt-3">
                <li class="list-group-item d-flex justify-content-between">
                    <span>Correos habilitados</span>
                    <strong>{{ $emailsHabilitados ? 'Sí' : 'No' }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Aviso destinatario al despachar</span>
                    <strong>{{ $despachoDestinatarioHabilitado ? 'Sí' : 'No' }}</strong>
                </li>
            </ul>
            <p class="text-muted small mt-2 mb-0">Solo el administrador puede cambiar estos interruptores.</p>
        @endif
    </div>
</div>
@endsection
