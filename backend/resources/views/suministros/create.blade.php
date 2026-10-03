@extends('layouts.app')

@section('title', 'Nuevo suministro')

@section('content')
<div class="card shadow-sm border-0"><div class="card-body">
<form method="POST" action="{{ route('suministros.store') }}">@csrf
    @include('suministros._form')
    <button class="btn btn-primary">Guardar</button>
</form>
</div></div>
@endsection
