<?php

namespace App\Services;

use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Models\Solicitud;
use App\Models\Suministro;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InventarioService
{
    public function registrarEntrada(Inventario $inventario, int $cantidad, User $usuario, ?string $observacion = null): void
    {
        if ($cantidad <= 0) {
            throw new RuntimeException('La cantidad debe ser mayor a cero.');
        }

        DB::transaction(function () use ($inventario, $cantidad, $usuario, $observacion) {
            $row = Inventario::query()->whereKey($inventario->id)->lockForUpdate()->firstOrFail();
            $row->cantidad += $cantidad;
            $row->save();

            $this->crearMovimiento($row, 'entrada', $cantidad, $usuario, null, $observacion);
        });
    }

    public function registrarAjuste(Inventario $inventario, int $nuevaCantidad, User $usuario, ?string $observacion = null): void
    {
        if ($nuevaCantidad < 0) {
            throw new RuntimeException('La cantidad no puede ser negativa.');
        }

        DB::transaction(function () use ($inventario, $nuevaCantidad, $usuario, $observacion) {
            $row = Inventario::query()->whereKey($inventario->id)->lockForUpdate()->firstOrFail();
            $delta = $nuevaCantidad - $row->cantidad;
            $row->cantidad = $nuevaCantidad;
            $row->save();

            if ($delta !== 0) {
                $this->crearMovimiento($row, 'ajuste', $delta, $usuario, null, $observacion ?? 'Ajuste manual');
            }
        });
    }

    public function atenderSolicitud(Solicitud $solicitud, User $usuario): void
    {
        DB::transaction(function () use ($solicitud, $usuario) {
            $solicitud->load('detalles');

            foreach ($solicitud->detalles as $detalle) {
                $this->registrarSalidaPorSolicitud(
                    $solicitud->sucursal_id,
                    $detalle->suministro_id,
                    $detalle->cantidad,
                    $usuario,
                    $solicitud
                );
            }
        });
    }

    public function validarDisponibilidad(int $sucursalId, int $suministroId, int $cantidad): bool
    {
        $inventario = Inventario::query()
            ->where('sucursal_id', $sucursalId)
            ->where('suministro_id', $suministroId)
            ->first();

        return $inventario && $inventario->cantidad >= $cantidad;
    }

    public function inicializarInventarioSuministro(Suministro $suministro): void
    {
        $sucursales = \App\Models\Sucursal::query()->where('activa', true)->pluck('id');

        foreach ($sucursales as $sucursalId) {
            Inventario::firstOrCreate(
                ['sucursal_id' => $sucursalId, 'suministro_id' => $suministro->id],
                ['cantidad' => 0]
            );
        }
    }

    private function registrarSalidaPorSolicitud(
        int $sucursalId,
        int $suministroId,
        int $cantidad,
        User $usuario,
        Solicitud $solicitud
    ): void {
        if ($cantidad <= 0) {
            throw new RuntimeException('Cantidad inválida en detalle de solicitud.');
        }

        $inventario = Inventario::query()
            ->where('sucursal_id', $sucursalId)
            ->where('suministro_id', $suministroId)
            ->lockForUpdate()
            ->first();

        if (! $inventario) {
            throw new RuntimeException('No existe inventario para la sucursal y suministro.');
        }

        if ($inventario->cantidad < $cantidad) {
            throw new RuntimeException('Stock insuficiente en la sucursal.');
        }

        $inventario->cantidad -= $cantidad;
        $inventario->save();

        $this->crearMovimiento(
            $inventario,
            'salida',
            $cantidad,
            $usuario,
            $solicitud->id,
            'Atención solicitud #' . $solicitud->id
        );
    }

    private function crearMovimiento(
        Inventario $inventario,
        string $tipo,
        int $cantidad,
        User $usuario,
        ?int $solicitudId,
        ?string $observacion
    ): void {
        MovimientoInventario::create([
            'inventario_id' => $inventario->id,
            'tipo' => $tipo,
            'cantidad' => $cantidad,
            'usuario_id' => $usuario->id,
            'solicitud_id' => $solicitudId,
            'observacion' => $observacion,
            'fecha_movimiento' => now(),
        ]);
    }
}
