@extends('layouts.app')

@section('title', 'Segundo factor')

@section('content')
<div class="auth-wrapper">
    <div class="card auth-card shadow-lg border-0">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <i class="bi bi-shield-lock fs-1 text-primary"></i>
                <h3 class="mt-2 fw-semibold">Verificación en dos pasos</h3>
                <p class="auth-brand-caption mb-2">Grupo Fabrigas</p>
                <p class="text-muted small mb-0">Abra <strong>Google Authenticator</strong> e ingrese el código de 6 dígitos.</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger py-2 small">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.2fa.submit') }}">
                @csrf
                <div class="mb-4">
                    <label for="codigo" class="form-label">Código de 6 dígitos</label>
                    <input type="text" class="form-control text-center fs-4 letter-spacing-wide" id="codigo" name="codigo"
                           maxlength="6" pattern="[0-9]{6}" required autofocus inputmode="numeric" autocomplete="one-time-code">
                </div>
                <button type="submit" class="btn btn-primary w-100">Verificar</button>
            </form>
        </div>
    </div>
</div>
@endsection
