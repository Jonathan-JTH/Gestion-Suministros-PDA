<?php

namespace Database\Seeders;

use App\Models\Inventario;
use App\Models\Permiso;
use App\Models\Rol;
use App\Models\Sucursal;
use App\Models\Suministro;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminRol = Rol::create(['nombre' => 'admin', 'descripcion' => 'Administrador del sistema']);
        $soporteRol = Rol::create(['nombre' => 'soporte', 'descripcion' => 'Soporte TI']);
        $sucursalRol = Rol::create(['nombre' => 'sucursal', 'descripcion' => 'Usuario de sucursal']);

        $permisos = [
            ['nombre' => 'dashboard.ver', 'descripcion' => 'Ver panel', 'modulo' => 'dashboard'],
            ['nombre' => 'solicitudes.gestionar', 'descripcion' => 'Gestionar solicitudes', 'modulo' => 'solicitudes'],
            ['nombre' => 'suministros.administrar', 'descripcion' => 'Administrar suministros', 'modulo' => 'suministros'],
            ['nombre' => 'inventario.ver', 'descripcion' => 'Ver inventario', 'modulo' => 'inventario'],
            ['nombre' => 'inventario.gestionar', 'descripcion' => 'Gestionar inventario', 'modulo' => 'inventario'],
            ['nombre' => 'usuarios.administrar', 'descripcion' => 'Administrar usuarios', 'modulo' => 'usuarios'],
            ['nombre' => 'roles.administrar', 'descripcion' => 'Administrar roles y permisos', 'modulo' => 'roles'],
            ['nombre' => 'reportes.generar', 'descripcion' => 'Generar reportes', 'modulo' => 'reportes'],
            ['nombre' => 'trazabilidad.ver', 'descripcion' => 'Consultar trazabilidad', 'modulo' => 'trazabilidad'],
            ['nombre' => 'bitacora.ver', 'descripcion' => 'Consultar bitácora', 'modulo' => 'bitacora'],
        ];

        foreach ($permisos as $permiso) {
            Permiso::create($permiso);
        }

        $todos = Permiso::pluck('id');
        $adminRol->permisos()->sync($todos);

        $soporteRol->permisos()->sync(
            Permiso::whereNotIn('nombre', ['usuarios.administrar', 'roles.administrar'])->pluck('id')
        );

        $sucursales = collect([
            ['nombre' => 'Sucursal Centro', 'codigo' => 'CTR', 'direccion' => 'Av. Principal 100'],
            ['nombre' => 'Sucursal Norte', 'codigo' => 'NRT', 'direccion' => 'Blvd. Norte 250'],
        ])->map(fn ($s) => Sucursal::create($s));

        User::create([
            'nombre' => 'Administrador',
            'correo' => 'admin@suministros.local',
            'password' => Hash::make('admin123'),
            'rol_id' => $adminRol->id,
            'segundo_factor_habilitado' => true,
            'activo' => true,
        ]);

        User::create([
            'nombre' => 'Soporte TI',
            'correo' => 'soporte@suministros.local',
            'password' => Hash::make('soporte123'),
            'rol_id' => $soporteRol->id,
            'segundo_factor_habilitado' => true,
            'activo' => true,
        ]);

        User::create([
            'nombre' => 'María López',
            'correo' => 'sucursal@suministros.local',
            'password' => Hash::make('sucursal123'),
            'rol_id' => $sucursalRol->id,
            'sucursal_id' => $sucursales[0]->id,
            'activo' => true,
        ]);

        $catalogo = [
            ['nombre' => 'Tóner HP 85A', 'tipo' => 'Tóner', 'marca' => 'HP', 'modelo' => '85A', 'unidad_medida' => 'pieza', 'stock_minimo' => 5, 'cantidad' => 25],
            ['nombre' => 'Papel Bond Carta', 'tipo' => 'Papel', 'marca' => 'OfficePro', 'modelo' => 'Carta', 'unidad_medida' => 'caja', 'stock_minimo' => 10, 'cantidad' => 48],
            ['nombre' => 'Tóner Samsung MLT', 'tipo' => 'Tóner', 'marca' => 'Samsung', 'modelo' => 'MLT-D111S', 'unidad_medida' => 'pieza', 'stock_minimo' => 4, 'cantidad' => 12],
        ];

        foreach ($catalogo as $item) {
            $suministro = Suministro::create(collect($item)->except('cantidad')->all());

            foreach ($sucursales as $sucursal) {
                Inventario::create([
                    'sucursal_id' => $sucursal->id,
                    'suministro_id' => $suministro->id,
                    'cantidad' => (int) ($item['cantidad'] / 2),
                ]);
            }
        }
    }
}
