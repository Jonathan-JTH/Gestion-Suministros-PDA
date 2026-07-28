@extends('layouts.app')

@section('title', 'Inventario de Suministros')

@section('content')
<h3>Inventario de Suministros</h3>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Impresora</th>
            <th>Cantidad Actual</th>
            <th>Mínimo</th>
            <th>Máximo</th>
            <th>Última Actualización</th>
        </tr>
    </thead>
    <tbody>
        @foreach($suministros as $s)
        <tr>
            <td>{{ $s->id }}</td>
            <td>{{ $s->impresora->modelo }} - {{ $s->impresora->serie }}</td>
            <td>{{ $s->cantidad_actual }}</td>
            <td>{{ $s->minimo }}</td>
            <td>{{ $s->maximo }}</td>
            <td>{{ $s->fecha_actualizacion }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection