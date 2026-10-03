@extends('layouts.app')

@section('title', 'Inicio de sesión')

@section('content')
<div class="auth-wrapper">
    <div class="card auth-card shadow-lg border-0">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <i class="bi bi-printer-fill fs-1 text-primary"></i>
                <h3 class="mt-2">Gestión de Suministros</h3>
                <p class="text-muted mb-0">Ingrese sus credenciales</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <div class="mb-3">
                    <label for="correo" class="form-label">Usuario (correo)</label>
                    <input type="email" class="form-control" id="correo" name="correo" value="{{ old('correo') }}" required autofocus>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Ingresar</button>
            </form>
        </div>
    </div>
</div>
@endsection
