@extends('layouts.app')

@section('title', 'Bitácora')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h4 class="mb-0">Bitácora del sistema</h4>
    <span class="text-muted small">{{ $bitacora->total() }} registro(s)</span>
</div>

<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('bitacora.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label for="q" class="form-label small mb-1">Buscar</label>
                <input type="search" name="q" id="q" class="form-control form-control-sm"
                       placeholder="Detalle, módulo, acción, IP…"
                       value="{{ request('q') }}">
            </div>
            <div class="col-md-2">
                <label for="modulo" class="form-label small mb-1">Módulo</label>
                <select name="modulo" id="modulo" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    @foreach($modulos as $m)
                        <option value="{{ $m }}" @selected(request('modulo') === $m)>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="accion" class="form-label small mb-1">Acción</label>
                <select name="accion" id="accion" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    @foreach($acciones as $a)
                        <option value="{{ $a }}" @selected(request('accion') === $a)>{{ $a }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="desde" class="form-label small mb-1">Desde</label>
                <input type="date" name="desde" id="desde" class="form-control form-control-sm" value="{{ request('desde') }}">
            </div>
            <div class="col-md-2">
                <label for="hasta" class="form-label small mb-1">Hasta</label>
                <input type="date" name="hasta" id="hasta" class="form-control form-control-sm" value="{{ request('hasta') }}">
            </div>
            <div class="col-12 col-md-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i> Filtrar</button>
                <a href="{{ route('bitacora.index') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered align-middle mb-0 bitacora-table">
            <thead class="table-dark">
                <tr>
                    <th style="width: 11rem;">Fecha</th>
                    <th style="width: 9rem;">Usuario</th>
                    <th style="width: 8rem;">Módulo</th>
                    <th style="width: 9rem;">Acción</th>
                    <th>Detalle</th>
                    <th style="width: 7rem;">IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bitacora as $b)
                    @php
                        $accionBadge = match(true) {
                            str_contains($b->accion, 'fallido') => 'danger',
                            str_contains($b->accion, 'email') => 'info',
                            in_array($b->accion, ['crear', 'despachar', 'atender', 'email_enviado'], true) => 'success',
                            default => 'secondary',
                        };
                    @endphp
                    <tr>
                        <td class="text-nowrap small">
                            <strong>{{ $b->created_at->format('d/m/Y') }}</strong><br>
                            <span class="text-muted">{{ $b->created_at->format('H:i:s') }}</span>
                        </td>
                        <td class="small">{{ $b->usuario->nombre ?? 'Sistema' }}</td>
                        <td><span class="badge text-bg-secondary">{{ $b->modulo }}</span></td>
                        <td><span class="badge text-bg-{{ $accionBadge }}">{{ $b->accion }}</span></td>
                        <td class="small text-break">{{ $b->detalle ?? '—' }}</td>
                        <td class="small font-monospace text-muted">{{ $b->ip_address ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No hay registros con los filtros aplicados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($bitacora->hasPages())
        <div class="card-footer bg-white border-top d-flex justify-content-center py-3">
            {{ $bitacora->links() }}
        </div>
    @endif
</div>

<style>
    .bitacora-table thead th { font-size: 0.85rem; }
    .bitacora-table tbody td { vertical-align: middle; }
</style>
@endsection
