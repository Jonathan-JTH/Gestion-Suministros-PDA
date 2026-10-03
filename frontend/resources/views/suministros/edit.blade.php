@extends('layouts.app')

@section('title', 'Editar suministro')

@section('content')
<div class="card shadow-sm border-0"><div class="card-body">
<form method="POST" action="{{ route('suministros.update', $suministro) }}">@csrf @method('PUT')
    @include('suministros._form', ['suministro' => $suministro])
    <button class="btn btn-primary">Actualizar</button>
</form>
</div></div>
@endsection
