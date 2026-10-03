<div class="row g-3">
    <div class="col-md-6"><label class="form-label">Nombre</label><input name="nombre" class="form-control" value="{{ old('nombre', $usuario->nombre ?? '') }}" required></div>
    <div class="col-md-6"><label class="form-label">Correo</label><input type="email" name="correo" class="form-control" value="{{ old('correo', $usuario->correo ?? '') }}" required></div>
    <div class="col-md-6"><label class="form-label">Contraseña {{ isset($usuario) ? '(dejar vacío para no cambiar)' : '' }}</label><input type="password" name="password" class="form-control" {{ isset($usuario) ? '' : 'required' }}></div>
    <div class="col-md-6">
        <label class="form-label">Rol</label>
        <select name="rol_id" class="form-select" required>
            @foreach($roles as $rol)
                <option value="{{ $rol->id }}" @selected(old('rol_id', $usuario->rol_id ?? '') == $rol->id)>{{ $rol->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Sucursal</label>
        <select name="sucursal_id" class="form-select">
            <option value="">— Ninguna —</option>
            @foreach($sucursales as $s)
                <option value="{{ $s->id }}" @selected(old('sucursal_id', $usuario->sucursal_id ?? '') == $s->id)>{{ $s->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 form-check mt-4 ms-2"><input class="form-check-input" type="checkbox" name="segundo_factor_habilitado" value="1" id="2fa" @checked(old('segundo_factor_habilitado', $usuario->segundo_factor_habilitado ?? false))><label for="2fa" class="form-check-label">Habilitar 2FA (admin/soporte)</label></div>
    @if(isset($usuario))
        <div class="col-md-6 form-check mt-4 ms-2"><input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" @checked(old('activo', $usuario->activo ?? true))><label for="activo" class="form-check-label">Usuario activo</label></div>
    @endif
</div>
