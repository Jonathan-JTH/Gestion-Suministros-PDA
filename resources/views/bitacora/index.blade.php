@extends('layouts.app')

@section('title', 'Bitácora')

@section('content')
<h3>Bitácora del Sistema</h3>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Módulo</th>
            <th>Acción</th>
            <th>Detalle</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bitacora as $b)
        <tr>
            <td>{{ $b->id }}</td>
            <td>{{ $b->usuario->nombre }}</td>
            <td>{{ $b->modulo }}</td>
            <td>{{ $b->accion }}</td>
            <td>{{ $b->detalle }}</td>
            <td>{{ $b->created_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection