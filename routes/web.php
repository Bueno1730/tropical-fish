<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EntradaController;
use App\Http\Controllers\ImportacionController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;

// Rutas de acceso (invitados)
Route::middleware('guest')->group(function () {
    Route::get('/', fn () => redirect()->route('login'));
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/recuperar', [AuthController::class, 'showRecuperar'])->name('recuperar');
    Route::post('/recuperar', [AuthController::class, 'recuperar'])->name('recuperar.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/inicio', [DashboardController::class, 'index'])->name('inicio');

    // Consulta de inventario: vendedor y administrador
    Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::get('/productos/{producto}', [ProductoController::class, 'show'])->name('productos.show')->whereNumber('producto');

    // Ventas: vendedor y administrador (CDU-07, CDU-08)
    Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
    Route::get('/ventas/nueva', [VentaController::class, 'create'])->name('ventas.create');
    Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store');
    Route::get('/ventas/{venta}', [VentaController::class, 'show'])->name('ventas.show')->whereNumber('venta');
    Route::post('/ventas/{venta}/anular', [VentaController::class, 'anular'])->name('ventas.anular')->middleware('rol:administrador');

    // Clientes: vendedor y administrador
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{cliente}/editar', [ClienteController::class, 'edit'])->name('clientes.edit');
    Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
    Route::post('/clientes/{cliente}/estado', [ClienteController::class, 'cambiarEstado'])->name('clientes.estado');

    // Lotes: consulta para vendedor; registro de retiros para ambos (CDU-11)
    Route::get('/lotes', [LoteController::class, 'index'])->name('lotes.index');
    Route::get('/lotes/{lote}', [LoteController::class, 'show'])->name('lotes.show')->whereNumber('lote');
    Route::post('/lotes/{lote}/retiros', [LoteController::class, 'registrarRetiro'])->name('lotes.retiros.store');

    // ===== Solo administrador =====
    Route::middleware('rol:administrador')->group(function () {
        // Productos
        Route::get('/productos/nuevo', [ProductoController::class, 'create'])->name('productos.create');
        Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
        Route::get('/productos/{producto}/editar', [ProductoController::class, 'edit'])->name('productos.edit');
        Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
        Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');
        Route::post('/productos/{producto}/estado', [ProductoController::class, 'cambiarEstado'])->name('productos.estado');

        // Proveedores
        Route::resource('proveedores', ProveedorController::class)->except(['create'])->parameters(['proveedores' => 'proveedore']);
        Route::post('/proveedores/{proveedore}/estado', [ProveedorController::class, 'cambiarEstado'])->name('proveedores.estado');

        // Entradas de mercadería
        Route::resource('entradas', EntradaController::class)->except(['destroy']);
        Route::post('/entradas/{entrada}/anular', [EntradaController::class, 'anular'])->name('entradas.anular');

        // Importación CSV
        Route::get('/importar', [ImportacionController::class, 'index'])->name('importaciones.index');
        Route::post('/importar', [ImportacionController::class, 'importar'])->name('importaciones.importar');
        Route::get('/importar/plantilla', [ImportacionController::class, 'plantilla'])->name('importaciones.plantilla');

        // Lotes: administración completa
        Route::get('/lotes/nuevo', [LoteController::class, 'create'])->name('lotes.create');
        Route::post('/lotes', [LoteController::class, 'store'])->name('lotes.store');
        Route::get('/lotes/{lote}/editar', [LoteController::class, 'edit'])->name('lotes.edit');
        Route::put('/lotes/{lote}', [LoteController::class, 'update'])->name('lotes.update');
        Route::delete('/lotes/{lote}', [LoteController::class, 'destroy'])->name('lotes.destroy');
        Route::post('/lotes/{lote}/tratamientos', [LoteController::class, 'registrarTratamiento'])->name('lotes.tratamientos.store');
        Route::post('/lotes/{lote}/estado', [LoteController::class, 'cambiarEstado'])->name('lotes.estado');

        // Reportes (solo administrador según el modelo de casos de uso)
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/stock-critico', [ReporteController::class, 'stockCritico'])->name('reportes.stock-critico');
        Route::get('/reportes/mas-vendidos', [ReporteController::class, 'masVendidos'])->name('reportes.mas-vendidos');
        Route::get('/reportes/historial-ventas', [ReporteController::class, 'historialVentas'])->name('reportes.historial-ventas');
        Route::get('/reportes/retiros-lote', [ReporteController::class, 'retirosPorLote'])->name('reportes.retiros-lote');
        Route::get('/reportes/control-especial', [ReporteController::class, 'controlEspecial'])->name('reportes.control-especial');

        // Usuarios y sesiones
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::get('/usuarios/sesiones', [UsuarioController::class, 'sesiones'])->name('usuarios.sesiones');
        Route::get('/usuarios/{usuario}/editar', [UsuarioController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::post('/usuarios/{usuario}/desactivar', [UsuarioController::class, 'desactivar'])->name('usuarios.desactivar');
        Route::post('/usuarios/{usuario}/reactivar', [UsuarioController::class, 'reactivar'])->name('usuarios.reactivar');
        Route::post('/usuarios/{usuario}/restablecer', [UsuarioController::class, 'restablecer'])->name('usuarios.restablecer');
        Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
    });
});
