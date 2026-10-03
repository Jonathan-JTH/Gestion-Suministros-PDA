@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h4 class="page-title mb-0">Usuarios</h4>
        <p class="page-meta mb-0">{{ $usuarios->count() }} cuenta(s)</p>
    </div>
    <a href="{{ route('usuarios.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-person-plus"></i> Nuevo usuario</a>
</div>

<div class="card filter-card shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label" for="q">Buscar</label>
                <input type="search" name="q" id="q" class="form-control form-control-sm"
                       placeholder="Nombre o correo…" value="{{ request('q') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="rol_id">Rol</label>
                <select name="rol_id" id="rol_id" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    @foreach($roles as $rol)
                        <option value="{{ $rol->id }}" @selected((string) request('rol_id') === (string) $rol->id)>{{ $rol->nombre }}</option>
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
                <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="card card-list-table shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-pro table-striped table-hover table-bordered align-middle">
            <thead class="table-dark">
                <tr><th>Nombre</th><th>Correo</th><th>Rol</th><th>Sucursal</th><th>2FA</th><th>Estado</th><th class="text-end">Acciones</th></tr>
            </thead>
            <tbody>
                @forelse($usuarios as $u)
                    <tr>
                        <td class="fw-medium">{{ $u->nombre }}</td>
                        <td class="small">{{ $u->correo }}</td>
                        <td><span class="badge text-bg-secondary">{{ $u->rol->nombre ?? '—' }}</span></td>
                        <td>{{ $u->sucursal->nombre ?? '—' }}</td>
                        <td>{{ $u->segundo_factor_habilitado ? 'Sí' : 'No' }}</td>
                        <td>
                            @if($u->activo)
                                <span class="badge text-bg-success">Activo</span>
                            @else
                                <span class="badge text-bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('usuarios.edit', $u) }}" class="btn btn-sm btn-outline-dark">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No hay usuarios con esos filtros.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
