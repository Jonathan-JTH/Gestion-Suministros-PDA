<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Sucursal;
use App\Services\InventarioService;
use App\Support\RegistraBitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class InventarioController extends Controller
{
    public function __construct(private readonly InventarioService $inventarioService) {}

    public function index(Request $request)
    {
        $this->authorizePermiso('inventario.ver');

        $query = Inventario::with(['suministro', 'sucursal'])->orderBy('sucursal_id');

        if ($request->filled('sucursal_id')) {
            $query->where('sucursal_id', $request->input('sucursal_id'));
        }

        if ($request->filled('q')) {
            $term = '%'.$request->string('q')->trim().'%';
            $query->whereHas('suministro', fn ($q) => $q->where('nombre', 'like', $term));
        }

        $items = $query->get();

        if ($request->boolean('solo_bajo')) {
            $items = $items->filter(fn (Inventario $i) => $i->bajoStockMinimo())->values();
        }

        return view('inventario.index', [
            'inventario' => $items,
            'sucursales' => Sucursal::orderBy('nombre')->get(),
        ]);
    }

    public function entrada(Request $request, $id)
    {
        $this->authorizePermiso('inventario.gestionar');

        $data = $request->validate([
            'cantidad' => 'required|integer|min:1',
            'observacion' => 'nullable|string|max:255',
        ]);

        $inventario = Inventario::with('suministro')->findOrFail($id);

        try {
            $this->inventarioService->registrarEntrada(
                $inventario,
                $data['cantidad'],
                Auth::user(),
                $data['observacion'] ?? 'Entrada manual'
            );
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        RegistraBitacora::registrar('inventario', 'entrada', 'Entrada inventario #' . $inventario->id, $request);

        return back()->with('success', 'Entrada registrada correctamente.');
    }

    public function ajuste(Request $request, $id)
    {
        $this->authorizePermiso('inventario.gestionar');

        $data = $request->validate([
            'cantidad' => 'required|integer|min:0',
            'observacion' => 'nullable|string|max:255',
        ]);

        $inventario = Inventario::findOrFail($id);

        try {
            $this->inventarioService->registrarAjuste(
                $inventario,
                $data['cantidad'],
                Auth::user(),
                $data['observacion'] ?? 'Ajuste manual'
            );
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        RegistraBitacora::registrar('inventario', 'ajuste', 'Ajuste inventario #' . $inventario->id, $request);

        return back()->with('success', 'Ajuste registrado.');
    }

    private function authorizePermiso(string $permiso): void
    {
        if (! Auth::user()->tienePermiso($permiso)) {
            abort(403);
        }
    }
}
