<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Desafio2fa extends Model
{
    protected $table = 'desafios_2fa';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'usuario_id',
        'expires_at',
        'completado_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'completado_at' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function codigos(): HasMany
    {
        return $this->hasMany(Codigo2fa::class, 'desafio_id');
    }
}
