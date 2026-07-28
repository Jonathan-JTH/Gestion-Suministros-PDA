<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suministro extends Model
{
    protected $fillable = [
        'impresora_id',
        'cantidad_actual',
        'minimo',
        'maximo'
    ];

    public function impresora()
    {
        return $this->belongsTo(Impresora::class);
    }
}