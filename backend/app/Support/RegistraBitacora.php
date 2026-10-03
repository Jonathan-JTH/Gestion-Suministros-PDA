<?php

namespace App\Support;

use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegistraBitacora
{
    public static function registrar(string $modulo, string $accion, ?string $detalle = null, ?Request $request = null): void
    {
        Bitacora::create([
            'usuario_id' => Auth::id(),
            'modulo' => $modulo,
            'accion' => $accion,
            'detalle' => $detalle,
            'ip_address' => $request?->ip(),
        ]);
    }
}
