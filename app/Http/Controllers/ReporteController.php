<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\Suministro;

class ReporteController extends Controller
{
    public function resumen()
    {
        return [
            'solicitudes_total' => Solicitud::count(),
            'pendientes' => Solicitud::where('estado','pendiente')->count(),
            'inventario' => Suministro::all()
        ];
    }
}