<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Sucursal;
use App\Models\User;
use App\Support\RegistraBitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizePermiso('usuarios.administrar');

        $query = User::with(['rol', 'sucursal'])->orderBy('nombre');

        if ($request->filled('q')) {
            $term = '%'.$request->string('q')->trim().'%';
            $query->where(function ($w) use ($term) {
                $w->where('nombre', 'like', $term)->orWhere('correo', 'like', $term);
            });
        }

        if ($request->filled('rol_id')) {
            $query->where('rol_id', $request->rol_id);
        }

        if ($request->filled('activo')) {
            $query->where('activo', $request->boolean('activo'));
        }

        return view('usuarios.index', [
            'usuarios' => $query->get(),
            'roles' => Rol::orderBy('nombre')->get(),
        ]);
    }

    public function create()
    {
        $this->authorizePermiso('usuarios.administrar');

        return view('usuarios.create', [
            'roles' => Rol::orderBy('nombre')->get(),
            'sucursales' => Sucursal::orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizePermiso('usuarios.administrar');
        $data = $this->validated($request);

        User::create([
            ...collect($data)->except('password')->all(),
            'password' => Hash::make($data['password']),
            'segundo_factor_habilitado' => $request->boolean('segundo_factor_habilitado'),
            'activo' => $request->boolean('activo', true),
        ]);

        RegistraBitacora::registrar('usuarios', 'crear', 'Usuario ' . $data['nombre'] . ' creado', $request);

        return redirect()->route('usuarios.index')->with('success', 'Usuario registrado.');
    }

    public function edit(User $usuario)
    {
        $this->authorizePermiso('usuarios.administrar');

        return view('usuarios.edit', [
            'usuario' => $usuario,
            'roles' => Rol::orderBy('nombre')->get(),
            'sucursales' => Sucursal::orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, User $usuario)
    {
        $this->authorizePermiso('usuarios.administrar');
        $data = $this->validated($request, $usuario->id);

        $usuario->fill(collect($data)->except('password')->all());

        if (! empty($data['password'])) {
            $usuario->password = Hash::make($data['password']);
            RegistraBitacora::registrar('usuarios', 'cambio_password', 'Contraseña actualizada: ' . $usuario->nombre, $request);
        }

        $usuario->segundo_factor_habilitado = $request->boolean('segundo_factor_habilitado');
        $usuario->activo = $request->boolean('activo');
        $usuario->save();

        RegistraBitacora::registrar('usuarios', 'actualizar', 'Usuario ' . $usuario->nombre . ' actualizado', $request);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'nombre' => 'required|string|max:100',
            'correo' => ['required', 'email', 'max:100', Rule::unique('usuarios', 'correo')->ignore($ignoreId)],
            'password' => [$ignoreId ? 'nullable' : 'required', 'string', 'min:6'],
            'rol_id' => 'required|exists:roles,id',
            'sucursal_id' => 'nullable|exists:sucursales,id',
        ]);
    }

    private function authorizePermiso(string $permiso): void
    {
        if (! Auth::user()->tienePermiso($permiso)) {
            abort(403);
        }
    }
}
