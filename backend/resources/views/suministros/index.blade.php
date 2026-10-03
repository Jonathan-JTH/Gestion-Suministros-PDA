@extends('layouts.app')

@section('title', 'Catálogo de suministros')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h4 class="mb-0">Suministros</h4>
    <a href="{{ route('suministros.create') }}" class="btn btn-primary">Nuevo suministro</a>
</div>
<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Nombre</th><th>Tipo</th><th>Stock total</th><th>Mínimo</th><th>Estado</th><th></th></tr>
            </thead>
            <tbody>
                @foreach($suministros as $s)
                    <tr>
                        <td>{{ $s->nombre }}</td>
                        <td>{{ $s->tipo }}</td>
                        <td>{{ $s->inventarios->sum('cantidad') }}</td>
                        <td>{{ $s->stock_minimo }}</td>
                        <td><span class="badge bg-{{ $s->activo ? 'success' : 'secondary' }}">{{ $s->activo ? 'Activo' : 'Inactivo' }}</span></td>
                        <td><a href="{{ route('suministros.edit', $s) }}" class="btn btn-sm btn-outline-primary">Editar</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
