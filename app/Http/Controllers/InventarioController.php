<?php

namespace App\Http\Controllers;

use App\Models\Suministro;

class InventarioController extends Controller
{
    // VER INVENTARIO
    public function index()
    {
        return Suministro::with('impresora')->get();
    }

    // ACTUALIZAR STOCK
    public function updateStock($id, $cantidad)
    {
        $suministro = Suministro::find($id);

        $suministro->cantidad_actual -= $cantidad;
        $suministro->save();

        return response()->json($suministro);
    }
}