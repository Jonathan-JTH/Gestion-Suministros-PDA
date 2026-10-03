@extends('layouts.app')

@section('title', 'Catálogo de suministros')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h4 class="page-title mb-0">Suministros</h4>
        <p class="page-meta mb-0">{{ $suministros->count() }} en catálogo</p>
    </div>
    <a href="{{ route('suministros.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Nuevo suministro</a>
</div>

<div class="card filter-card shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label" for="q">Buscar</label>
                <input type="search" name="q" id="q" class="form-control form-control-sm"
                       placeholder="Nombre, tipo, marca…" value="{{ request('q') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="tipo">Tipo</label>
                <select name="tipo" id="tipo" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    @foreach($tipos as $t)
                        <option value="{{ $t }}" @selected(request('tipo') === $t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label" for="activo">Estado</label>
                <select name="activo" id="activo" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="1" @selected(request('activo') === '1')>Activos</option>
                    <option value="0" @selected(request('activo') === '0')>Inactivos</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i> Filtrar</button>
                <a href="{{ route('suministros.index') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="card card-list-table shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-pro table-striped table-hover table-bordered align-middle">
            <thead class="table-dark">
                <tr><th>Nombre</th><th>Tipo</th><th>Stock total</th><th>Mínimo</th><th>Estado</th><th class="text-end">Acciones</th></tr>
            </thead>
            <tbody>
                @forelse($suministros as $s)
                    <tr>
                        <td class="fw-medium">{{ $s->nombre }}</td>
                        <td><span class="badge text-bg-secondary">{{ $s->tipo }}</span></td>
                        <td>{{ $s->inventarios->sum('cantidad') }}</td>
                        <td>{{ $s->stock_minimo }}</td>
                        <td>
                            @if($s->activo)
                                <span class="badge text-bg-success">Activo</span>
                            @else
                                <span class="badge text-bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('suministros.edit', $s) }}" class="btn btn-sm btn-outline-dark">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No hay suministros con esos filtros.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
