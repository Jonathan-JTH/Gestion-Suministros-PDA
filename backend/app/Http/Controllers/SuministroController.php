<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Suministro;
use App\Services\InventarioService;
use App\Support\RegistraBitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuministroController extends Controller
{
    public function __construct(private readonly InventarioService $inventarioService) {}

    public function index()
    {
        $this->authorizePermiso('suministros.administrar');

        $suministros = Suministro::with('inventarios.sucursal')->orderBy('nombre')->get();

        return view('suministros.index', compact('suministros'));
    }

    public function create()
    {
        $this->authorizePermiso('suministros.administrar');

        return view('suministros.create');
    }

    public function store(Request $request)
    {
        $this->authorizePermiso('suministros.administrar');

        $data = $request->validate([
            'nombre' => 'required|string|max:120',
            'tipo' => 'required|string|max:80',
            'marca' => 'nullable|string|max:80',
            'modelo' => 'nullable|string|max:80',
            'unidad_medida' => 'required|string|max:30',
            'stock_minimo' => 'required|integer|min:0',
            'cantidad_inicial' => 'required|integer|min:0',
        ]);

        $suministro = Suministro::create(collect($data)->except('cantidad_inicial')->all());
        $this->inventarioService->inicializarInventarioSuministro($suministro);

        if ($data['cantidad_inicial'] > 0) {
            Inventario::where('suministro_id', $suministro->id)
                ->update(['cantidad' => $data['cantidad_inicial']]);
        }

        RegistraBitacora::registrar('suministros', 'crear', 'Suministro ' . $suministro->nombre . ' registrado', $request);

        return redirect()->route('suministros.index')->with('success', 'Suministro creado correctamente.');
    }

    public function edit(Suministro $suministro)
    {
        $this->authorizePermiso('suministros.administrar');

        return view('suministros.edit', compact('suministro'));
    }

    public function update(Request $request, Suministro $suministro)
    {
        $this->authorizePermiso('suministros.administrar');

        $data = $request->validate([
            'nombre' => 'required|string|max:120',
            'tipo' => 'required|string|max:80',
            'marca' => 'nullable|string|max:80',
            'modelo' => 'nullable|string|max:80',
            'unidad_medida' => 'required|string|max:30',
            'stock_minimo' => 'required|integer|min:0',
            'activo' => 'nullable|boolean',
        ]);

        $suministro->update([
            'nombre' => $data['nombre'],
            'tipo' => $data['tipo'],
            'marca' => $data['marca'] ?? null,
            'modelo' => $data['modelo'] ?? null,
            'unidad_medida' => $data['unidad_medida'],
            'stock_minimo' => $data['stock_minimo'],
            'activo' => $request->boolean('activo'),
        ]);

        RegistraBitacora::registrar('suministros', 'actualizar', 'Suministro ' . $suministro->nombre . ' actualizado', $request);

        return redirect()->route('suministros.index')->with('success', 'Suministro actualizado.');
    }

    private function authorizePermiso(string $permiso): void
    {
        if (! Auth::user()->tienePermiso($permiso)) {
            abort(403);
        }
    }
}
