<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lotes_peces', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 15)->unique();          // L-2026-031
            $table->foreignId('producto_id')->constrained('productos');
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->nullOnDelete();
            $table->date('fecha_recepcion');
            $table->unsignedInteger('cantidad_inicial');
            $table->unsignedInteger('total_retirado')->default(0);
            // Condiciones del agua al recibir el lote (RF-14)
            $table->decimal('ph', 4, 2)->nullable();
            $table->decimal('temperatura', 4, 1)->nullable();   // °C
            $table->decimal('amoniaco', 8, 3)->nullable();      // ppm
            $table->decimal('nitritos', 8, 3)->nullable();      // ppm
            $table->decimal('dureza', 8, 2)->nullable();        // °GH
            $table->text('observaciones')->nullable();
            $table->decimal('precio_compra', 10, 2)->nullable();
            $table->enum('estado', ['activo', 'en_tratamiento', 'agotado', 'cerrado'])->default('activo');
            $table->timestamps();
        });

        Schema::create('tratamientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lotes_peces')->cascadeOnDelete();
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->text('sintomas')->nullable();
            $table->string('producto', 150);
            $table->string('dosis', 100)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('retiros_lote', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lotes_peces')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->dateTime('fecha');
            $table->unsignedInteger('cantidad');
            $table->string('observacion', 200)->nullable();   // sin clasificar el motivo (RF-16)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retiros_lote');
        Schema::dropIfExists('tratamientos');
        Schema::dropIfExists('lotes_peces');
    }
};
