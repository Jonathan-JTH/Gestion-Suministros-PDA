<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->string('correo_destinatario_despacho', 255)->nullable()->after('observacion');
            $table->text('nota_despacho')->nullable()->after('correo_destinatario_despacho');
            $table->foreignId('despachado_por_usuario_id')->nullable()->after('nota_despacho')
                ->constrained('usuarios')->nullOnDelete();
            $table->timestamp('despachado_at')->nullable()->after('despachado_por_usuario_id');
        });
    }

    public function down(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->dropForeign(['despachado_por_usuario_id']);
            $table->dropColumn([
                'correo_destinatario_despacho',
                'nota_despacho',
                'despachado_por_usuario_id',
                'despachado_at',
            ]);
        });
    }
};
