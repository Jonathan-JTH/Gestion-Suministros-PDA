<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="nombre">Nombre</label>
        <input name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror"
               value="{{ old('nombre', $suministro->nombre ?? '') }}" required autocomplete="off">
        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="tipo">Tipo</label>
        <input name="tipo" id="tipo" class="form-control @error('tipo') is-invalid @enderror"
               value="{{ old('tipo', $suministro->tipo ?? '') }}" required placeholder="Ej. Tóner, Papel…">
        @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="marca">Marca</label>
        <input name="marca" id="marca" class="form-control" value="{{ old('marca', $suministro->marca ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="modelo">Modelo</label>
        <input name="modelo" id="modelo" class="form-control" value="{{ old('modelo', $suministro->modelo ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="unidad_medida">Unidad</label>
        <input name="unidad_medida" id="unidad_medida" class="form-control"
               value="{{ old('unidad_medida', $suministro->unidad_medida ?? 'unidad') }}" required>
        <div class="form-text">Ej. unidad, caja, rollo</div>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="stock_minimo">Stock mínimo</label>
        <input type="number" name="stock_minimo" id="stock_minimo" class="form-control"
               value="{{ old('stock_minimo', $suministro->stock_minimo ?? 0) }}" min="0" required>
    </div>
    @if(empty($suministro))
        <div class="col-md-4">
            <label class="form-label" for="cantidad_inicial">Cantidad inicial (cada sucursal)</label>
            <input type="number" name="cantidad_inicial" id="cantidad_inicial" class="form-control"
                   value="{{ old('cantidad_inicial', 0) }}" min="0" required>
        </div>
    @endif
    @if(!empty($suministro))
        <div class="col-12">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo"
                       @checked(old('activo', $suministro->activo))>
                <label class="form-check-label" for="activo">Suministro activo en catálogo</label>
            </div>
        </div>
    @endif
</div>
