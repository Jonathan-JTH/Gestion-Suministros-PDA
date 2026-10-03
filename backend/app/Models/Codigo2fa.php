<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Codigo2fa extends Model
{
    protected $table = 'codigos_2fa';

    protected $fillable = [
        'desafio_id',
        'codigo_hash',
        'expires_at',
        'consumido_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'consumido_at' => 'datetime',
        ];
    }

    public function desafio(): BelongsTo
    {
        return $this->belongsTo(Desafio2fa::class, 'desafio_id');
    }
}
