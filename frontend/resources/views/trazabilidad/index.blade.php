@extends('layouts.app')

@section('title', 'Trazabilidad')

@section('content')
<div class="mb-3">
    <h4 class="page-title mb-0">Trazabilidad de movimientos</h4>
    <p class="page-meta mb-0">{{ $movimientos->total() }} movimiento(s)</p>
</div>

<div class="card filter-card shadow-sm mb-3">
    <div class="card-body py-3">
        <form class="row g-2 align-items-end" method="GET">
            <div class="col-md-4">
                <label class="form-label" for="suministro_id">Suministro</label>
                <select name="suministro_id" id="suministro_id" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    @foreach($suministros as $s)
                        <option value="{{ $s->id }}" @selected(request('suministro_id') == $s->id)>{{ $s->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label" for="desde">Desde</label>
                <input type="date" name="desde" id="desde" class="form-control form-control-sm" value="{{ request('desde') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label" for="hasta">Hasta</label>
                <input type="date" name="hasta" id="hasta" class="form-control form-control-sm" value="{{ request('hasta') }}">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-primary btn-sm" type="submit"><i class="bi bi-funnel"></i> Filtrar</button>
                <a href="{{ route('trazabilidad.index') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="card card-list-table shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-pro table-striped table-hover table-bordered align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Sucursal</th>
                    <th>Suministro</th>
                    <th>Tipo</th>
                    <th>Cant.</th>
                    <th>Usuario</th>
                    <th>Solicitud</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movimientos as $m)
                    <tr>
                        <td class="text-nowrap small">{{ $m->fecha_movimiento?->format('d/m/Y H:i') }}</td>
                        <td>{{ $m->inventario?->sucursal?->nombre ?? '—' }}</td>
                        <td class="fw-medium">{{ $m->inventario?->suministro?->nombre ?? '—' }}</td>
                        <td><span class="badge text-bg-secondary">{{ $m->tipo }}</span></td>
                        <td>{{ $m->cantidad }}</td>
                        <td>{{ $m->usuario->nombre ?? '—' }}</td>
                        <td>{{ $m->solicitud_id ? '#'.$m->solicitud_id : '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Sin movimientos con esos filtros.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($movimientos->hasPages())
        <div class="card-footer bg-white border-top d-flex justify-content-center py-3">
            {{ $movimientos->links() }}
        </div>
    @endif
</div>
@endsection
