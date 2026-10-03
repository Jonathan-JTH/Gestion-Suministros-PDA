@extends('layouts.app')

@section('title', 'Editar suministro')

@section('content')
<div class="mb-3">
    <h4 class="page-title mb-0">Editar suministro</h4>
    <p class="page-meta mb-0">{{ $suministro->nombre }}</p>
</div>

<div class="card form-card shadow-sm border-0">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('suministros.update', $suministro) }}">
            @csrf
            @method('PUT')
            @include('partials.form-validation')
            @include('suministros._form', ['suministro' => $suministro])
            <div class="d-flex flex-wrap gap-2 border-top pt-3 mt-4">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check-lg"></i> Actualizar</button>
                <a href="{{ route('suministros.index') }}" class="btn btn-outline-secondary btn-sm">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
