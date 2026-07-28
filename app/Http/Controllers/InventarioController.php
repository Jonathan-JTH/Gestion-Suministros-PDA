<?php

namespace App\Http\Controllers;

use App\Models\Suministro;

class InventarioController extends Controller
{
    public function index()
    {
        $suministros = Suministro::with('impresora')->get();

        return view('inventario.index', compact('suministros'));
    }

    public function updateStock($id, $cantidad)
    {
        $suministro = Suministro::findOrFail($id);

        $suministro->cantidad_actual -= $cantidad;
        $suministro->save();

        return redirect()
            ->route('inventario.index')
            ->with('success', 'Stock actualizado correctamente.');
    }
}
