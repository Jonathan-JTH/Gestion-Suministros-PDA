<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Impresora extends Model
{
    protected $fillable = [
        'modelo',
        'serie',
        'tipo_toner',
        'sucursal_id'
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function suministros()
    {
        return $this->hasMany(Suministro::class);
    }
}
