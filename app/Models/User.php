<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Indicar la tabla correcta en la BD
    protected $table = 'usuarios';

    // Indicar que el campo para login es 'correo'
    protected $username = 'correo';

    protected $fillable = [
        'nombre',   // cambia 'name' por 'nombre'
        'correo',   // cambia 'email' por 'correo'
        'password',
        'rol',
        'sucursal_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'usuario_id');
    }
}