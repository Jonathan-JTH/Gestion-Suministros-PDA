@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h4>Usuarios</h4>
    <a href="{{ route('usuarios.create') }}" class="btn btn-primary">Nuevo usuario</a>
</div>
<div class="card shadow-sm border-0">
    <table class="table mb-0">
        <thead class="table-light"><tr><th>Nombre</th><th>Correo</th><th>Rol</th><th>Sucursal</th><th>2FA</th><th>Estado</th><th></th></tr></thead>
        <tbody>
            @foreach($usuarios as $u)
                <tr>
                    <td>{{ $u->nombre }}</td>
                    <td>{{ $u->correo }}</td>
                    <td>{{ $u->rol->nombre ?? '—' }}</td>
                    <td>{{ $u->sucursal->nombre ?? '—' }}</td>
                    <td>{{ $u->segundo_factor_habilitado ? 'Sí' : 'No' }}</td>
                    <td>{{ $u->activo ? 'Activo' : 'Inactivo' }}</td>
                    <td><a href="{{ route('usuarios.edit', $u) }}" class="btn btn-sm btn-outline-primary">Editar</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
