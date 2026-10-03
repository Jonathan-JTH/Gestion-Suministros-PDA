@extends('layouts.app')

@section('title', 'Configurar Google Authenticator')

@section('content')
<div class="auth-wrapper">
    <div class="card auth-card shadow-lg border-0">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-3">
                <i class="bi bi-qr-code fs-1 text-primary"></i>
                <h3 class="mt-2">Vincular Google Authenticator</h3>
                <p class="text-muted">Escanea el código QR con la app <strong>Google Authenticator</strong> (o Microsoft Authenticator).</p>
            </div>

            <div class="text-center mb-3">{!! $qrSvg !!}</div>

            <p class="small text-muted text-center">Si no puedes escanear, ingresa esta clave manualmente en la app:</p>
            <p class="text-center font-monospace fw-bold user-select-all">{{ $secreto }}</p>

            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.2fa.setup.submit') }}">
                @csrf
                <div class="mb-3">
                    <label for="codigo" class="form-label">Código de 6 dígitos (desde la app)</label>
                    <input type="text" class="form-control text-center fs-4" id="codigo" name="codigo" maxlength="6" pattern="[0-9]{6}" required autofocus>
                </div>
                <button type="submit" class="btn btn-primary w-100">Confirmar y continuar</button>
            </form>
        </div>
    </div>
</div>
@endsection
