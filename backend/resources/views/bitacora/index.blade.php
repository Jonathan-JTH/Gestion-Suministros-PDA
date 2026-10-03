@extends('layouts.app')

@section('title', 'Bitácora')

@section('content')
<h4 class="mb-3">Bitácora del sistema</h4>
<div class="card shadow-sm border-0">
    <table class="table mb-0">
        <thead class="table-light"><tr><th>Fecha</th><th>Usuario</th><th>Módulo</th><th>Acción</th><th>Detalle</th><th>IP</th></tr></thead>
        <tbody>
            @foreach($bitacora as $b)
                <tr>
                    <td>{{ $b->created_at }}</td>
                    <td>{{ $b->usuario->nombre ?? 'Sistema' }}</td>
                    <td>{{ $b->modulo }}</td>
                    <td>{{ $b->accion }}</td>
                    <td>{{ $b->detalle }}</td>
                    <td>{{ $b->ip_address }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-3">{{ $bitacora->links() }}</div>
</div>
@endsection
