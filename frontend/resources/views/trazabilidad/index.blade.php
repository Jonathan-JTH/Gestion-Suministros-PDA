@extends('layouts.app')

@section('title', 'Trazabilidad')

@section('content')
<h4 class="mb-3">Trazabilidad de movimientos</h4>
<form class="row g-2 mb-3" method="GET">
    <div class="col-md-3">
        <select name="suministro_id" class="form-select">
            <option value="">Todos los suministros</option>
            @foreach($suministros as $s)
                <option value="{{ $s->id }}" @selected(request('suministro_id') == $s->id)>{{ $s->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2"><input type="date" name="desde" class="form-control" value="{{ request('desde') }}"></div>
    <div class="col-md-2"><input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}"></div>
    <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filtrar</button></div>
</form>
<div class="card shadow-sm border-0">
    <table class="table mb-0">
        <thead class="table-light"><tr><th>Fecha</th><th>Sucursal</th><th>Suministro</th><th>Tipo</th><th>Cant.</th><th>Usuario</th><th>Solicitud</th></tr></thead>
        <tbody>
            @forelse($movimientos as $m)
                <tr>
                    <td>{{ $m->fecha_movimiento?->format('d/m/Y H:i') }}</td>
                    <td>{{ $m->inventario?->sucursal?->nombre ?? '—' }}</td>
                    <td>{{ $m->inventario?->suministro?->nombre ?? '—' }}</td>
                    <td>{{ $m->tipo }}</td>
                    <td>{{ $m->cantidad }}</td>
                    <td>{{ $m->usuario->nombre ?? '—' }}</td>
                    <td>{{ $m->solicitud_id ? '#'.$m->solicitud_id : '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center py-3">Sin movimientos.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-3">{{ $movimientos->links() }}</div>
</div>
@endsection
