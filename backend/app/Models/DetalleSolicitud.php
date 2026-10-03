<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleSolicitud extends Model
{
    protected $table = 'detalle_solicitudes';

    protected $fillable = [
        'solicitud_id',
        'suministro_id',
        'cantidad',
    ];

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function suministro(): BelongsTo
    {
        return $this->belongsTo(Suministro::class);
    }
}
