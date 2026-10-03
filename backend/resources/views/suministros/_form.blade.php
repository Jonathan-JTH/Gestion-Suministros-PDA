<div class="row g-3">
    <div class="col-md-6"><label class="form-label">Nombre</label><input name="nombre" class="form-control" value="{{ old('nombre', $suministro->nombre ?? '') }}" required></div>
    <div class="col-md-6"><label class="form-label">Tipo</label><input name="tipo" class="form-control" value="{{ old('tipo', $suministro->tipo ?? '') }}" required></div>
    <div class="col-md-6"><label class="form-label">Marca</label><input name="marca" class="form-control" value="{{ old('marca', $suministro->marca ?? '') }}"></div>
    <div class="col-md-6"><label class="form-label">Modelo</label><input name="modelo" class="form-control" value="{{ old('modelo', $suministro->modelo ?? '') }}"></div>
    <div class="col-md-4"><label class="form-label">Unidad</label><input name="unidad_medida" class="form-control" value="{{ old('unidad_medida', $suministro->unidad_medida ?? 'unidad') }}" required></div>
    <div class="col-md-4"><label class="form-label">Stock mínimo</label><input type="number" name="stock_minimo" class="form-control" value="{{ old('stock_minimo', $suministro->stock_minimo ?? 0) }}" min="0" required></div>
    @if(empty($suministro))
        <div class="col-md-4"><label class="form-label">Cantidad inicial (cada sucursal)</label><input type="number" name="cantidad_inicial" class="form-control" value="{{ old('cantidad_inicial', 0) }}" min="0" required></div>
    @endif
    @if(!empty($suministro))
        <div class="col-md-6 form-check mt-4 ms-2">
            <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" @checked(old('activo', $suministro->activo))>
            <label class="form-check-label" for="activo">Activo</label>
        </div>
    @endif
</div>
