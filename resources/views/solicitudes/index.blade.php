<!-- resources/views/solicitudes/index.blade.php -->
@extends('layouts.app')

@section('title', 'Listado de Solicitudes')

@section('content')
<h3>Listado de Solicitudes</h3>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Impresora</th>
            <th>Tipo de Solicitud</th>
            <th>Cantidad</th>
            <th>Estado</th>
            <th>Fecha</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($solicitudes as $sol)
        <tr>
            <td>{{ $sol->id }}</td>
            <td>{{ $sol->usuario->nombre }}</td>
            <td>{{ $sol->impresora->modelo }} - {{ $sol->impresora->serie }}</td>
            <td>{{ $sol->tipo_solicitud }}</td>
            <td>{{ $sol->cantidad }}</td>
            <td>{{ $sol->estado }}</td>
            <td>{{ $sol->fecha_solicitud }}</td>
            <td>
                @if($sol->estado == 'pendiente')
                <form action="{{ url('solicitudes/'.$sol->id.'/aprobar') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">Aprobar</button>
                </form>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection