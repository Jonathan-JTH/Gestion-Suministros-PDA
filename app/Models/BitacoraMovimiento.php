<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BitacoraMovimiento extends Model
{
    protected $table = 'bitacora_movimientos';

    public const UPDATED_AT = null;

    protected $fillable = [
        'usuario_id',
        'solicitud_id',
        'modulo',
        'accion',
        'detalle',
        'ip_address',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }
}
