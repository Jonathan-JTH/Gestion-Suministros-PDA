<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleSolicitud extends Model
{
    protected $fillable = [
        'solicitud_id',
        'impresora_id',
        'tipo_movimiento',
        'cantidad'
    ];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function impresora()
    {
        return $this->belongsTo(Impresora::class);
    }
}