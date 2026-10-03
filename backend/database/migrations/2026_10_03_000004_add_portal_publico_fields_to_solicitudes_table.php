<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
        });

        Schema::table('solicitudes', function (Blueprint $table) {
            $table->foreignId('usuario_id')->nullable()->change();
            $table->foreign('usuario_id')->references('id')->on('usuarios')->nullOnDelete();

            $table->string('nombre_solicitante', 120)->nullable()->after('usuario_id');
            $table->string('correo_solicitante', 255)->nullable()->after('nombre_solicitante');
            $table->string('ubicacion', 255)->nullable()->after('correo_solicitante');
            $table->string('puesto_area', 120)->nullable()->after('ubicacion');
            $table->string('modelo_equipo', 120)->nullable()->after('puesto_area');
            $table->enum('tipo_movimiento', ['ENT', 'DEV', 'OC'])->default('ENT')->after('modelo_equipo');
        });
    }

    public function down(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->dropColumn([
                'nombre_solicitante',
                'correo_solicitante',
                'ubicacion',
                'puesto_area',
                'modelo_equipo',
                'tipo_movimiento',
            ]);
        });

        Schema::table('solicitudes', function (Blueprint $table) {
            $table->foreignId('usuario_id')->nullable(false)->change();
            $table->foreign('usuario_id')->references('id')->on('usuarios')->cascadeOnDelete();
        });
    }
};
