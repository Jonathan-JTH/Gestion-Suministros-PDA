@extends('layouts.app')

@section('title', 'Inicio de sesión')

@section('content')
<div class="auth-wrapper">
    <div class="card auth-card shadow-lg border-0">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <i class="bi bi-printer-fill fs-1 text-primary"></i>
                <h3 class="mt-2 fw-semibold">Gestión de Suministros</h3>
                <p class="auth-brand-caption mb-1">Grupo Fabrigas</p>
                <p class="text-muted mb-0 small">Ingrese sus credenciales institucionales</p>
            </div>

            @if($errors->has('recaptcha'))
                <div class="alert alert-danger py-2 small">{{ $errors->first('recaptcha') }}</div>
            @elseif($errors->any())
                <div class="alert alert-danger py-2 small">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}" id="login-form">
                @csrf
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo</label>
                    <input type="email" class="form-control @error('correo') is-invalid @enderror" id="correo" name="correo" value="{{ old('correo') }}" required autofocus autocomplete="username">
                    @error('correo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="current-password">
                </div>
                @if(!empty($recaptchaEnabled) && !empty($recaptchaSiteKey))
                    @if(!empty($recaptchaTestKeys))
                        <div class="alert alert-info py-2 small mb-2">
                            <strong>Desarrollo:</strong> claves de prueba Google (aviso rojo en casilla v2).
                        </div>
                    @endif
                    <div class="mb-3">
                        @include('partials.recaptcha-login', [
                            'recaptchaEnabled' => $recaptchaEnabled,
                            'recaptchaSiteKey' => $recaptchaSiteKey,
                            'recaptchaIntegration' => $recaptchaIntegration ?? config('recaptcha.integration'),
                        ])
                    </div>
                @endif
                <button type="submit" class="btn btn-primary w-100">Ingresar</button>
            </form>
                @if(!empty($recaptchaEnabled))
                <p class="text-muted text-center mt-3 mb-0" style="font-size: 0.7rem;">Protegido por reCAPTCHA · Google</p>
                <p class="text-muted text-center mt-2 mb-0" style="font-size: 0.65rem;">
                    Si el widget falla, en
                    <a href="https://www.google.com/recaptcha/admin" target="_blank" rel="noopener">reCAPTCHA Admin</a>
                    → Configuración → dominios: <strong>localhost</strong> y <strong>127.0.0.1</strong>
                </p>
            @endif
        </div>
    </div>
</div>
@endsection
