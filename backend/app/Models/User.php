<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'correo',
        'password',
        'rol_id',
        'sucursal_id',
        'segundo_factor_habilitado',
        'totp_secreto',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'totp_secreto',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'segundo_factor_habilitado' => 'boolean',
            'activo' => 'boolean',
            'totp_secreto' => 'encrypted',
        ];
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function isAdmin(): bool
    {
        return $this->rol?->nombre === 'admin';
    }

    public function isSucursal(): bool
    {
        return $this->rol?->nombre === 'sucursal';
    }

    public function isSoporte(): bool
    {
        return $this->rol?->nombre === 'soporte';
    }

    public function tienePermiso(string $permiso): bool
    {
        $this->loadMissing('rol.permisos');

        return $this->rol?->permisos?->contains('nombre', $permiso) ?? false;
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function solicitudes(): HasMany
    {
        return $this->hasMany(Solicitud::class, 'usuario_id');
    }

    public function movimientosInventario(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'usuario_id');
    }

    public function bitacoras(): HasMany
    {
        return $this->hasMany(Bitacora::class, 'usuario_id');
    }
}
