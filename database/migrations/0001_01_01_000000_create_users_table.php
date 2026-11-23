<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 10)->unique();          // USR-0001
            $table->string('ci', 20)->unique();              // cédula de identidad
            $table->string('nombre', 150);
            $table->string('usuario', 50)->unique();
            $table->string('password');
            $table->enum('rol', ['administrador', 'vendedor'])->default('vendedor');
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamp('ultimo_acceso')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // Bitácora de sesiones para el módulo "Ver sesiones" (RNF-08)
        Schema::create('sesiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('inicio')->useCurrent();
            $table->timestamp('ultima_actividad')->useCurrent();
            $table->timestamp('fin')->nullable();
            $table->timestamps();
        });

        // Solicitudes de restablecimiento de clave (pantalla "Recuperar contraseña")
        Schema::create('solicitudes_clave', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->enum('estado', ['pendiente', 'atendida'])->default('pendiente');
            $table->foreignId('atendida_por')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes_clave');
        Schema::dropIfExists('sesiones');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('usuarios');
    }
};
