<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Suministro extends Model
{
    protected $fillable = [
        'nombre',
        'tipo',
        'marca',
        'modelo',
        'unidad_medida',
        'stock_minimo',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function inventarios(): HasMany
    {
        return $this->hasMany(Inventario::class);
    }

    public function detallesSolicitud(): HasMany
    {
        return $this->hasMany(DetalleSolicitud::class);
    }
}
