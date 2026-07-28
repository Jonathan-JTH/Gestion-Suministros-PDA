<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $table = 'sucursales';

    protected $fillable = [
        'nombre',
        'direccion'
    ];

    // Relación: una sucursal tiene muchos usuarios
    public function usuarios()
    {
        return $this->hasMany(User::class);
    }

    // Relación: una sucursal tiene muchas impresoras
    public function impresoras()
    {
        return $this->hasMany(Impresora::class);
    }
}