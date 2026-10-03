@extends('layouts.app')

@section('title', 'Inventario')

@section('content')
<h4 class="mb-3">Inventario por sucursal</h4>

<form class="row g-2 mb-3" method="GET">
    <div class="col-md-4">
        <select name="sucursal_id" class="form-select">
            <option value="">Todas las sucursales</option>
            @foreach($sucursales as $s)
                <option value="{{ $s->id }}" @selected(request('sucursal_id') == $s->id)>{{ $s->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filtrar</button></div>
</form>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
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
                @foreach($inventario as $item)
                    @php $bajo = $item->bajoStockMinimo(); @endphp
                    <tr>
                        <td>{{ $item->sucursal->nombre ?? '—' }}</td>
                        <td>{{ $item->suministro->nombre ?? '—' }}</td>
                        <td>{{ $item->cantidad }}</td>
                        <td>{{ $item->suministro->stock_minimo ?? 0 }}</td>
                        <td><span class="badge bg-{{ $bajo ? 'danger' : 'success' }}">{{ $bajo ? 'Bajo' : 'OK' }}</span></td>
                        <td>
                            <form method="POST" action="{{ route('inventario.entrada', $item->id) }}" class="d-flex gap-1">@csrf
                                <input type="number" name="cantidad" class="form-control form-control-sm" min="1" value="1" style="width:80px" required>
                                <button class="btn btn-sm btn-outline-success">+</button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('inventario.ajuste', $item->id) }}" class="d-flex gap-1">@csrf
                                <input type="number" name="cantidad" class="form-control form-control-sm" min="0" value="{{ $item->cantidad }}" style="width:80px" required>
                                <button class="btn btn-sm btn-outline-warning">=</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
