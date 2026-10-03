<?php

namespace App\Services;

use App\Models\Solicitud;
use RuntimeException;

class SolicitudEstadoService
{
    private const TRANSICIONES = [
        'pendiente' => ['en_proceso', 'rechazada'],
        'en_proceso' => ['atendida', 'rechazada'],
        'atendida' => [],
        'rechazada' => [],
    ];

    public function transicionar(Solicitud $solicitud, string $nuevoEstado): void
    {
        $actual = $solicitud->estado;

        if (! in_array($nuevoEstado, self::TRANSICIONES[$actual] ?? [], true)) {
            throw new RuntimeException("Transición inválida: {$actual} → {$nuevoEstado}.");
        }

        $solicitud->estado = $nuevoEstado;
        $solicitud->save();
    }
}
