<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\Suministro;

class ReporteController extends Controller
{
    public function resumen()
    {
        $resumen = [
            'solicitudes_total' => Solicitud::count(),
            'pendientes' => Solicitud::where('estado', 'pendiente')->count(),
            'inventario' => Suministro::with('impresora')->get(),
        ];

        return view('reportes.resumen', compact('resumen'));
    }
}
