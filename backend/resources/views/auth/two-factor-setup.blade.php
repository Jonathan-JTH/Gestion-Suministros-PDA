@extends('layouts.app')

@section('title', 'Configurar Google Authenticator')

@section('content')
<div class="auth-wrapper">
    <div class="card auth-card auth-card-wide shadow-lg border-0">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-3">
                <i class="bi bi-qr-code fs-1 text-primary"></i>
                <h3 class="mt-2 fw-semibold">Vincular Google Authenticator</h3>
                <p class="auth-brand-caption mb-2">Grupo Fabrigas</p>
                <p class="text-muted small mb-0">Escanee el código QR con la app (Google o Microsoft Authenticator).</p>
            </div>

            <div class="text-center mb-3 p-2 bg-light rounded">{!! $qrSvg !!}</div>

            <p class="small text-muted text-center mb-1">Clave manual:</p>
            <p class="text-center font-monospace fw-bold user-select-all small mb-3">{{ $secreto }}</p>

            @if($errors->any())
                <div class="alert alert-danger py-2 small">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.2fa.setup.submit') }}">
                @csrf
                <div class="mb-3">
                    <label for="codigo" class="form-label">Código de 6 dígitos (desde la app)</label>
                    <input type="text" class="form-control text-center fs-4" id="codigo" name="codigo"
                           maxlength="6" pattern="[0-9]{6}" required autofocus inputmode="numeric">
                </div>
                <button type="submit" class="btn btn-primary w-100">Confirmar y continuar</button>
            </form>
        </div>
    </div>
</div>
@endsection
