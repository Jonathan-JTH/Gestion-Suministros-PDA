@extends('layouts.app')

@section('title', 'Inventario')

@section('content')
<div class="mb-3">
    <h4 class="page-title mb-0">Inventario por sucursal</h4>
    <p class="page-meta mb-0">{{ $inventario->count() }} registro(s)</p>
</div>

<div class="card filter-card shadow-sm mb-3">
    <div class="card-body py-3">
        <form class="row g-2 align-items-end" method="GET">
            <div class="col-md-3">
                <label class="form-label" for="sucursal_id">Sucursal</label>
                <select name="sucursal_id" id="sucursal_id" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    @foreach($sucursales as $s)
                        <option value="{{ $s->id }}" @selected(request('sucursal_id') == $s->id)>{{ $s->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="q">Suministro</label>
                <input type="search" name="q" id="q" class="form-control form-control-sm"
                       placeholder="Buscar por nombre…" value="{{ request('q') }}">
            </div>
            <div class="col-md-2">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="solo_bajo" value="1" id="solo_bajo"
                           @checked(request()->boolean('solo_bajo'))>
                    <label class="form-check-label small" for="solo_bajo">Solo stock bajo</label>
                </div>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i> Filtrar</button>
                <a href="{{ route('inventario.index') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="card card-list-table shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-pro table-striped table-hover table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Sucursal</th>
                    <th>Suministro</th>
                    <th>Cantidad</th>
                    <th>Mínimo</th>
                    <th>Estado</th>
                    <th>Entrada</th>
                    <th>Ajuste</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inventario as $item)
                    @php $bajo = $item->bajoStockMinimo(); @endphp
                    <tr>
                        <td>{{ $item->sucursal->nombre ?? '—' }}</td>
                        <td class="fw-medium">{{ $item->suministro->nombre ?? '—' }}</td>
                        <td>{{ $item->cantidad }}</td>
                        <td>{{ $item->suministro->stock_minimo ?? 0 }}</td>
                        <td>
                            @if($bajo)
                                <span class="badge text-bg-dark">Stock bajo</span>
                            @else
                                <span class="badge text-bg-success">OK</span>
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ route('inventario.entrada', $item->id) }}" class="d-flex gap-1">@csrf
                                <input type="number" name="cantidad" class="form-control form-control-sm" min="1" value="1" style="width:4.5rem" required>
                                <button class="btn btn-sm btn-outline-dark" title="Entrada">+</button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('inventario.ajuste', $item->id) }}" class="d-flex gap-1">@csrf
                                <input type="number" name="cantidad" class="form-control form-control-sm" min="0" value="{{ $item->cantidad }}" style="width:4.5rem" required>
                                <button class="btn btn-sm btn-outline-secondary" title="Ajuste">=</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No hay registros con esos filtros.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
