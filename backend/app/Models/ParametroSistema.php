<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParametroSistema extends Model
{
    protected $table = 'parametros_sistema';

    protected $fillable = [
        'clave',
        'valor',
    ];

    public static function valorBool(string $clave, bool $default = true): bool
    {
        $valor = static::where('clave', $clave)->value('valor');

        if ($valor === null) {
            return $default;
        }

        return in_array(strtolower((string) $valor), ['1', 'true', 'yes', 'on'], true);
    }

    public static function guardarBool(string $clave, bool $habilitado): void
    {
        static::updateOrCreate(
            ['clave' => $clave],
            ['valor' => $habilitado ? '1' : '0']
        );
    }
}
