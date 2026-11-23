<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entradas_mercaderia', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 12)->unique();          // ENT-00058
            $table->dateTime('fecha');
            $table->foreignId('proveedor_id')->constrained('proveedores');
            $table->string('nro_documento', 50)->nullable(); // factura/recibo
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->unsignedInteger('items_count')->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('estado', ['registrada', 'anulada'])->default('registrada');
            $table->timestamps();
        });

        Schema::create('detalle_entradas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrada_id')->constrained('entradas_mercaderia')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos');
            $table->unsignedInteger('cantidad');
            $table->decimal('precio_compra', 10, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });

        Schema::create('importaciones_csv', function (Blueprint $table) {
            $table->id();
            $table->string('archivo', 200);
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->unsignedInteger('insertadas')->default(0);
            $table->unsignedInteger('actualizadas')->default(0);
            $table->unsignedInteger('con_error')->default(0);
            $table->text('detalle_errores')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('importaciones_csv');
        Schema::dropIfExists('detalle_entradas');
        Schema::dropIfExists('entradas_mercaderia');
    }
};
