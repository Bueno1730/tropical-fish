<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->text('descripcion')->nullable();
            $table->boolean('maneja_lotes')->default(false); // peces se reciben por lote
            $table->timestamps();
        });

        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 10)->unique();          // PRV-0001
            $table->string('nit_ci', 20)->nullable()->unique();
            $table->string('nombre', 150);
            $table->string('telefono', 20);
            $table->string('correo', 100)->nullable();
            $table->string('direccion', 200)->nullable();
            $table->enum('tipo', ['fijo', 'eventual'])->default('fijo');
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });

        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 15)->unique();          // PEZ-001
            $table->string('nombre', 150);
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->nullOnDelete();
            $table->decimal('precio_compra', 10, 2)->default(0);
            $table->decimal('precio_venta', 10, 2)->default(0);
            $table->integer('stock_actual')->default(0);
            $table->integer('stock_minimo')->default(5);
            $table->string('unidad', 30)->default('unidad');
            // Control especial por normativa SENASAG (RF-07)
            $table->boolean('control_especial')->default(false);
            $table->string('nota_control', 200)->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
        Schema::dropIfExists('proveedores');
        Schema::dropIfExists('categorias');
    }
};
