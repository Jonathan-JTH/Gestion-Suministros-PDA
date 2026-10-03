<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suministros', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120);
            $table->string('tipo', 80);
            $table->string('marca', 80)->nullable();
            $table->string('modelo', 80)->nullable();
            $table->string('unidad_medida', 30)->default('unidad');
            $table->unsignedInteger('stock_minimo')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->constrained('sucursales')->cascadeOnDelete();
            $table->foreignId('suministro_id')->constrained('suministros')->cascadeOnDelete();
            $table->unsignedInteger('cantidad')->default(0);
            $table->timestamps();

            $table->unique(['sucursal_id', 'suministro_id']);
        });

        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignId('sucursal_id')->constrained('sucursales')->cascadeOnDelete();
            $table->enum('estado', ['pendiente', 'en_proceso', 'atendida', 'rechazada'])->default('pendiente');
            $table->text('observacion')->nullable();
            $table->timestamps();
        });

        Schema::create('detalle_solicitudes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_id')->constrained('solicitudes')->cascadeOnDelete();
            $table->foreignId('suministro_id')->constrained('suministros')->cascadeOnDelete();
            $table->unsignedInteger('cantidad');
            $table->timestamps();
        });

        Schema::create('movimiento_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventario_id')->constrained('inventario')->cascadeOnDelete();
            $table->enum('tipo', ['entrada', 'salida', 'ajuste']);
            $table->integer('cantidad');
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignId('solicitud_id')->nullable()->constrained('solicitudes')->nullOnDelete();
            $table->string('observacion', 255)->nullable();
            $table->timestamp('fecha_movimiento')->useCurrent();
            $table->timestamps();
        });

        Schema::create('desafios_2fa', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->timestamp('expires_at');
            $table->timestamp('completado_at')->nullable();
            $table->timestamps();
        });

        Schema::create('codigos_2fa', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('desafio_id')->constrained('desafios_2fa')->cascadeOnDelete();
            $table->string('codigo_hash');
            $table->timestamp('expires_at');
            $table->timestamp('consumido_at')->nullable();
            $table->timestamps();
        });

        Schema::create('bitacora', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('modulo', 80);
            $table->string('accion', 80);
            $table->text('detalle')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacora');
        Schema::dropIfExists('codigos_2fa');
        Schema::dropIfExists('desafios_2fa');
        Schema::dropIfExists('movimiento_inventario');
        Schema::dropIfExists('detalle_solicitudes');
        Schema::dropIfExists('solicitudes');
        Schema::dropIfExists('inventario');
        Schema::dropIfExists('suministros');
    }
};
