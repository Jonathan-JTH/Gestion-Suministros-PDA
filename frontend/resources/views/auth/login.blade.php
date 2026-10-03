@extends('layouts.app')

@section('title', 'Inicio de sesión')

@section('content')
<div class="auth-wrapper">
    <div class="card auth-card shadow-lg border-0">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <i class="bi bi-printer-fill fs-1 text-primary"></i>
                <h3 class="mt-2 fw-semibold">Gestión de Suministros</h3>
                <p class="text-muted mb-0 small">Ingrese sus credenciales institucionales</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger py-2 small">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}" id="login-form">
                @csrf
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo</label>
                    <input type="email" class="form-control" id="correo" name="correo" value="{{ old('correo') }}" required autofocus autocomplete="username">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
                </div>
                @if(!empty($recaptchaEnabled) && !empty($recaptchaSiteKey))
                    <div class="mb-3 d-flex justify-content-center">
                        <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
                    </div>
                @endif
                <button type="submit" class="btn btn-primary w-100">Ingresar</button>
            </form>
            @if(!empty($recaptchaEnabled))
                <p class="text-muted text-center mt-3 mb-0" style="font-size: 0.7rem;">Protegido por reCAPTCHA · Privacidad y condiciones de Google</p>
            @endif
        </div>
    </div>
</div>
@endsection

@if(!empty($recaptchaEnabled) && !empty($recaptchaSiteKey))
    @push('scripts')
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endpush
@endif
