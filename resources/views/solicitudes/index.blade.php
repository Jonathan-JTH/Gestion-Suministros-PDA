@extends('layouts.app')

@section('title', 'Listado de Solicitudes')

@section('content')
<h3>Mis Solicitudes</h3>

<a href="{{ route('solicitudes.create') }}" class="btn btn-primary mb-3">Nueva Solicitud</a>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Sucursal</th>
            <th>Impresora(s)</th>
            <th>Tipo</th>
            <th>Cantidad</th>
            <th>Estado</th>
            <th>Fecha</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse($solicitudes as $sol)
        <tr>
            <td>{{ $sol->id }}</td>
            <td>{{ $sol->sucursal->nombre ?? '—' }}</td>
            <td>
                @foreach($sol->detalles as $detalle)
                    {{ $detalle->impresora->modelo }} - {{ $detalle->impresora->serie }}@if(!$loop->last), @endif
                @endforeach
            </td>
            <td>
                @foreach($sol->detalles as $detalle)
                    {{ $detalle->tipo_movimiento }}@if(!$loop->last), @endif
                @endforeach
            </td>
            <td>
                @foreach($sol->detalles as $detalle)
                    {{ $detalle->cantidad }}@if(!$loop->last), @endif
                @endforeach
            </td>
            <td><span class="badge bg-secondary">{{ $sol->estado }}</span></td>
            <td>{{ $sol->created_at->format('d/m/Y H:i') }}</td>
            <td>
                <a href="{{ route('solicitudes.show', $sol) }}" class="btn btn-sm btn-info">Ver</a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center">No tienes solicitudes registradas.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
