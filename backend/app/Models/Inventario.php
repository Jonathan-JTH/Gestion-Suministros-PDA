<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventario extends Model
{
    protected $table = 'inventario';

    protected $fillable = [
        'sucursal_id',
        'suministro_id',
        'cantidad',
    ];

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function suministro(): BelongsTo
    {
        return $this->belongsTo(Suministro::class);
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'inventario_id');
    }

    public function bajoStockMinimo(): bool
    {
        return $this->cantidad <= (int) ($this->suministro?->stock_minimo ?? 0);
    }
}
