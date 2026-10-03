<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Solicitud extends Model
{
    protected $table = 'solicitudes';

    protected $fillable = [
        'usuario_id',
        'nombre_solicitante',
        'correo_solicitante',
        'ubicacion',
        'puesto_area',
        'modelo_equipo',
        'tipo_movimiento',
        'sucursal_id',
        'estado',
        'observacion',
        'correo_destinatario_despacho',
        'nota_despacho',
        'despachado_por_usuario_id',
        'despachado_at',
    ];

    protected function casts(): array
    {
        return [
            'despachado_at' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function despachadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'despachado_por_usuario_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleSolicitud::class);
    }

    public function movimientosInventario(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    public function nombreSolicitanteMostrar(): string
    {
        return $this->nombre_solicitante
            ?? $this->usuario?->nombre
            ?? '—';
    }

    public function correoSolicitanteMostrar(): ?string
    {
        return $this->correo_solicitante ?? $this->usuario?->correo;
    }
}
