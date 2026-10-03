@extends('layouts.app')

@section('title', 'Registro de solicitud')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <h4 class="mb-3">Registro de solicitud (Sucursal)</h4>
        <form method="POST" action="{{ route('solicitudes.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Sucursal</label>
                    <input type="text" class="form-control" value="{{ $sucursal->nombre ?? 'Sin sucursal' }}" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Solicitante</label>
                    <input type="text" class="form-control" value="{{ auth()->user()->nombre }}" disabled>
                </div>
                <div class="col-md-6">
                    <label for="suministro_id" class="form-label">Suministro</label>
                    <select class="form-select" id="suministro_id" name="suministro_id" required>
                        <option value="">Seleccione...</option>
                        @foreach($suministros as $suministro)
                            <option value="{{ $suministro->id }}" @selected(old('suministro_id') == $suministro->id)>
                                {{ $suministro->nombre }} ({{ $suministro->tipo }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="cantidad" class="form-label">Cantidad</label>
                    <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" value="{{ old('cantidad', 1) }}" required>
                </div>
                <div class="col-12">
                    <label for="observacion" class="form-label">Observaciones</label>
                    <textarea class="form-control" id="observacion" name="observacion" rows="3">{{ old('observacion') }}</textarea>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Registrar solicitud</button>
                <a href="{{ route('solicitudes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
