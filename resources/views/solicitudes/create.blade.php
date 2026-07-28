<!-- resources/views/solicitudes/create.blade.php -->
@extends('layouts.app')

@section('title', 'Nueva Solicitud')

@section('content')
<h3>Registrar Nueva Solicitud</h3>
<form method="POST" action="{{ route('solicitudes.store') }}">
    @csrf
    <div class="mb-3">
        <label for="impresora_id" class="form-label">Impresora</label>
        <select class="form-select" id="impresora_id" name="impresora_id">
            @foreach($impresoras as $impresora)
                <option value="{{ $impresora->id }}">
                    {{ $impresora->modelo }} - {{ $impresora->serie }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="tipo_solicitud" class="form-label">Tipo de Solicitud</label>
        <select class="form-select" id="tipo_solicitud" name="tipo_solicitud">
            <option value="ENT">Entrega</option>
            <option value="DEV">Devolución</option>
            <option value="OC">Orden de Compra</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="cantidad" class="form-label">Cantidad</label>
        <input type="number" class="form-control" id="cantidad" name="cantidad" required>
    </div>

    <button type="submit" class="btn btn-success">Registrar Solicitud</button>
</form>
@endsection