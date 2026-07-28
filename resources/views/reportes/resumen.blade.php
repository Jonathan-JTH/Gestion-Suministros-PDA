@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
<h3>Reportes del Sistema</h3>

<ul>
    <li>Total solicitudes: {{ $resumen['solicitudes_total'] }}</li>
    <li>Solicitudes pendientes: {{ $resumen['pendientes'] }}</li>
</ul>

<h5>Inventario Actual:</h5>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Impresora</th>
            <th>Cantidad Actual</th>
        </tr>
    </thead>
    <tbody>
        @foreach($resumen['inventario'] as $s)
        <tr>
            <td>{{ $s->impresora->modelo }} - {{ $s->impresora->serie }}</td>
            <td>{{ $s->cantidad_actual }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection