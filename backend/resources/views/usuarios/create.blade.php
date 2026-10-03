@extends('layouts.app')

@section('title', 'Nuevo usuario')

@section('content')
<div class="card shadow-sm border-0"><div class="card-body">
<form method="POST" action="{{ route('usuarios.store') }}">@csrf
    @include('usuarios._form')
    <button class="btn btn-primary">Guardar</button>
</form>
</div></div>
@endsection
