<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="nombre">Nombre completo</label>
        <input name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror"
               value="{{ old('nombre', $usuario->nombre ?? '') }}" required>
        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="correo">Correo</label>
        <input type="email" name="correo" id="correo" class="form-control @error('correo') is-invalid @enderror"
               value="{{ old('correo', $usuario->correo ?? '') }}" required autocomplete="off">
        @error('correo')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="password">Contraseña</label>
        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror"
               {{ isset($usuario) ? '' : 'required' }} autocomplete="new-password">
        @if(isset($usuario))
            <div class="form-text">Deje vacío para mantener la contraseña actual.</div>
        @endif
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="rol_id">Rol</label>
        <select name="rol_id" id="rol_id" class="form-select @error('rol_id') is-invalid @enderror" required>
            @foreach($roles as $rol)
                <option value="{{ $rol->id }}" @selected(old('rol_id', $usuario->rol_id ?? '') == $rol->id)>{{ $rol->nombre }}</option>
            @endforeach
        </select>
        @error('rol_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="sucursal_id">Sucursal</label>
        <select name="sucursal_id" id="sucursal_id" class="form-select">
            <option value="">— Ninguna (admin / soporte) —</option>
            @foreach($sucursales as $s)
                <option value="{{ $s->id }}" @selected(old('sucursal_id', $usuario->sucursal_id ?? '') == $s->id)>{{ $s->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 d-flex flex-column justify-content-center">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="segundo_factor_habilitado" value="1" id="2fa"
                   @checked(old('segundo_factor_habilitado', $usuario->segundo_factor_habilitado ?? false))>
            <label for="2fa" class="form-check-label">Autenticación en dos pasos (2FA)</label>
            <div class="form-text">Recomendado para administrador y soporte TI.</div>
        </div>
    </div>
    @if(isset($usuario))
        <div class="col-12">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo"
                       @checked(old('activo', $usuario->activo ?? true))>
                <label for="activo" class="form-check-label">Usuario activo</label>
            </div>
        </div>
    @endif
</div>
