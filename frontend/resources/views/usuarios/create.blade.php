@extends('layouts.app')

@section('title', 'Nuevo usuario')

@section('content')
<div class="mb-3">
    <h4 class="page-title mb-0">Nuevo usuario</h4>
    <p class="page-meta mb-0">Cuenta de acceso al sistema</p>
</div>

<div class="card form-card shadow-sm border-0">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('usuarios.store') }}">
            @csrf
            @include('partials.form-validation')
            @include('usuarios._form')
            <div class="d-flex flex-wrap gap-2 border-top pt-3 mt-4">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check-lg"></i> Guardar</button>
                <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary btn-sm">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
