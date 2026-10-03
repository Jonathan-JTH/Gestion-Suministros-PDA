<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parametros_sistema', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 80)->unique();
            $table->string('valor', 255);
            $table->timestamps();
        });

        DB::table('parametros_sistema')->insert([
            [
                'clave' => 'notificaciones_email_habilitadas',
                'valor' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'clave' => 'notificaciones_despacho_destinatario_habilitado',
                'valor' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('parametros_sistema');
    }
};
