<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BitacoraMovimiento extends Model
{
    protected $fillable = [
        'usuario_id',
        'solicitud_id',
        'modulo',
        'accion',
        'detalle',
        'ip_address'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }
}