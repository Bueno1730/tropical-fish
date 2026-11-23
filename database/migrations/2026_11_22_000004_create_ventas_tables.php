<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 12)->unique();          // CLI-00001
            $table->string('ci', 20)->nullable()->unique();
            $table->string('nombre', 150);
            $table->string('telefono', 20)->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });

        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 12)->unique();          // VEN-00142
            $table->dateTime('fecha');
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->enum('forma_pago', ['efectivo', 'qr']);
            $table->enum('canal', ['presencial', 'whatsapp', 'facebook', 'tiktok', 'instagram', 'otro']);
            $table->unsignedInteger('unidades')->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('estado', ['registrada', 'anulada'])->default('registrada');
            $table->timestamps();
        });

        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos');
            $table->unsignedInteger('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_ventas');
        Schema::dropIfExists('ventas');
        Schema::dropIfExists('clientes');
    }
};
