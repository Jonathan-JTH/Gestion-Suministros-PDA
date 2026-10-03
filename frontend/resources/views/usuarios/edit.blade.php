@extends('layouts.app')

@section('title', 'Editar usuario')

@section('content')
<div class="card shadow-sm border-0"><div class="card-body">
<form method="POST" action="{{ route('usuarios.update', $usuario) }}">@csrf @method('PUT')
    @include('usuarios._form', ['usuario' => $usuario])
    <button class="btn btn-primary">Actualizar</button>
</form>
</div></div>
@endsection
