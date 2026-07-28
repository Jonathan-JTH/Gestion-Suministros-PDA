<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\DetalleSolicitud;
use App\Models\BitacoraMovimiento;
use App\Models\Suministro;

class SolicitudController extends Controller
{
    // CREAR SOLICITUD
    public function store(Request $request)
    {
        $solicitud = Solicitud::create([
            'usuario_id' => $request->usuario_id,
            'sucursal_id' => $request->sucursal_id,
            'estado' => 'pendiente'
        ]);

        return response()->json($solicitud);
    }

    // AGREGAR DETALLE
    public function addDetalle(Request $request)
    {
        $detalle = DetalleSolicitud::create([
            'solicitud_id' => $request->solicitud_id,
            'impresora_id' => $request->impresora_id,
            'tipo_movimiento' => $request->tipo_movimiento,
            'cantidad' => $request->cantidad
        ]);

        return response()->json($detalle);
    }

    // APROBAR SOLICITUD (CLAVE)
      public function aprobar($id)
    {
    $solicitud = Solicitud::with('detalles')->findOrFail($id);

    // recorrer cada detalle de la solicitud
    foreach ($solicitud->detalles as $detalle) {

        $stock = Suministro::where('impresora_id', $detalle->impresora_id)->first();

        // VALIDAR STOCK
        if ($stock->cantidad_actual < $detalle->cantidad) {
            return response()->json([
                'message' => 'Stock insuficiente para la impresora ID: ' . $detalle->impresora_id
            ], 400);
        }

        // DESCONTAR INVENTARIO
        $stock->cantidad_actual -= $detalle->cantidad;
        $stock->save();
    }

    // CAMBIAR ESTADO
    $solicitud->estado = 'aprobado';
    $solicitud->save();

    // REGISTRAR BITÁCORA
    BitacoraMovimiento::create([
        'usuario_id' => $solicitud->usuario_id,
        'solicitud_id' => $solicitud->id,
        'modulo' => 'inventario',
        'accion' => 'aprobación automática',
        'detalle' => 'Inventario descontado automáticamente'
    ]);

    return response()->json([
        'message' => 'Solicitud aprobada y stock actualizado correctamente'
    ]);
    }
}