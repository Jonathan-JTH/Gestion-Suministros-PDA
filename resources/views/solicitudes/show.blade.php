<!-- resources/views/solicitudes/show.blade.php -->
@extends('layouts.app')

@section('title', 'Detalle Solicitud')

@section('content')
<h3>Detalle de Solicitud #{{ $solicitud->id }}</h3>

<ul class="list-group mb-3">
    <li class="list-group-item"><strong>Usuario:</strong> {{ $solicitud->usuario->nombre }}</li>
    <li class="list-group-item"><strong>Sucursal:</strong> {{ $solicitud->sucursal->nombre }}</li>
    <li class="list-group-item"><strong>Estado:</strong> {{ $solicitud->estado }}</li>
    <li class="list-group-item"><strong>Fecha:</strong> {{ $solicitud->fecha_solicitud }}</li>
</ul>

<h5>Detalle de productos:</h5>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Impresora</th>
            <th>Tipo Movimiento</th>
            <th>Cantidad</th>
        </tr>
    </thead>
    <tbody>
        @foreach($solicitud->detalles as $detalle)
        <tr>
            <td>{{ $detalle->impresora->modelo }} - {{ $detalle->impresora->serie }}</td>
            <td>{{ $detalle->tipo_movimiento }}</td>
            <td>{{ $detalle->cantidad }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<a href="{{ route('solicitudes.index') }}" class="btn btn-primary">Volver</a>
@endsection