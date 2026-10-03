@extends('layouts.app')

@section('title', 'Registro de solicitud')

@section('content')
<div class="mb-3">
    <h4 class="page-title mb-0">Nueva solicitud</h4>
    <p class="page-meta mb-0">Sucursal · {{ $sucursal->nombre ?? 'Sin sucursal asignada' }}</p>
</div>

<div class="card form-card shadow-sm border-0">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('solicitudes.store') }}">
            @csrf
            @include('partials.form-validation')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Sucursal</label>
                    <input type="text" class="form-control bg-light" value="{{ $sucursal->nombre ?? 'Sin sucursal' }}" disabled readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Solicitante</label>
                    <input type="text" class="form-control bg-light" value="{{ auth()->user()->nombre }}" disabled readonly>
                </div>
                <div class="col-md-6">
                    <label for="suministro_id" class="form-label">Suministro</label>
                    <select class="form-select @error('suministro_id') is-invalid @enderror" id="suministro_id" name="suministro_id" required>
                        <option value="">Seleccione…</option>
                        @foreach($suministros as $suministro)
                            <option value="{{ $suministro->id }}" @selected(old('suministro_id') == $suministro->id)>
                                {{ $suministro->nombre }} ({{ $suministro->tipo }})
                            </option>
                        @endforeach
                    </select>
                    @error('suministro_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="cantidad" class="form-label">Cantidad</label>
                    <input type="number" class="form-control @error('cantidad') is-invalid @enderror" id="cantidad" name="cantidad" min="1" value="{{ old('cantidad', 1) }}" required>
                    @error('cantidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label for="observacion" class="form-label">Observaciones</label>
                    <textarea class="form-control" id="observacion" name="observacion" rows="3" placeholder="Opcional">{{ old('observacion') }}</textarea>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2 border-top pt-3 mt-4">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-send"></i> Registrar solicitud</button>
                <a href="{{ route('solicitudes.index') }}" class="btn btn-outline-secondary btn-sm">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
