<?php

namespace App\Http\Controllers;

use App\Models\MovimientoInventario;
use App\Models\Suministro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrazabilidadController extends Controller
{
    public function index(Request $request)
    {
        if (! Auth::user()->tienePermiso('trazabilidad.ver')) {
            abort(403);
        }

        $query = MovimientoInventario::with(['inventario.sucursal', 'inventario.suministro', 'usuario', 'solicitud'])
            ->latest('fecha_movimiento');

        if ($request->filled('suministro_id')) {
            $query->whereHas('inventario', fn ($q) => $q->where('suministro_id', $request->input('suministro_id')));
        }

        if ($request->filled('desde')) {
            $query->whereDate('fecha_movimiento', '>=', $request->input('desde'));
        }

        if ($request->filled('hasta')) {
            $query->whereDate('fecha_movimiento', '<=', $request->input('hasta'));
        }

        return view('trazabilidad.index', [
            'movimientos' => $query->paginate(20)->withQueryString(),
            'suministros' => Suministro::orderBy('nombre')->get(),
        ]);
    }
}
