<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\LotePeces;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===== Usuarios (HU-01, HU-02) =====
        $admin = Usuario::create([
            'codigo' => 'USR-0001', 'ci' => '1234567', 'nombre' => 'Administrador',
            'usuario' => 'admin', 'password' => 'admin123',
            'rol' => 'administrador', 'estado' => 'activo',
        ]);
        $vendedor = Usuario::create([
            'codigo' => 'USR-0002', 'ci' => '7654321', 'nombre' => 'Lucía Mamani',
            'usuario' => 'vendedor1', 'password' => 'vende123',
            'rol' => 'vendedor', 'estado' => 'activo',
        ]);
        Usuario::create([
            'codigo' => 'USR-0003', 'ci' => '4433221', 'nombre' => 'Pedro Quispe',
            'usuario' => 'vendedor2', 'password' => 'vende123',
            'rol' => 'vendedor', 'estado' => 'inactivo',
        ]);

        // ===== Categorías =====
        $pez = Categoria::create(['nombre' => 'Peces', 'descripcion' => 'Peces ornamentales de agua dulce y marina', 'maneja_lotes' => true]);
        $acc = Categoria::create(['nombre' => 'Accesorios', 'descripcion' => 'Filtros, termómetros, grava y más']);
        $ali = Categoria::create(['nombre' => 'Alimentos', 'descripcion' => 'Alimento balanceado para peces']);
        $med = Categoria::create(['nombre' => 'Medicamentos', 'descripcion' => 'Medicamentos de uso veterinario']);
        $rea = Categoria::create(['nombre' => 'Reactivos', 'descripcion' => 'Reactivos para medir parámetros del agua']);
        $pla = Categoria::create(['nombre' => 'Plantas', 'descripcion' => 'Plantas acuáticas']);

        // ===== Proveedores =====
        $andino = Proveedor::create(['codigo' => 'PRV-0001', 'nit_ci' => '1020304', 'nombre' => 'Acuario Andino', 'telefono' => '70712345', 'tipo' => 'fijo', 'estado' => 'activo']);
        $aqua = Proveedor::create(['codigo' => 'PRV-0002', 'nit_ci' => '2030405', 'nombre' => 'Distrib. Aqua', 'telefono' => '44251122', 'tipo' => 'fijo', 'estado' => 'activo']);
        $vet = Proveedor::create(['codigo' => 'PRV-0003', 'nit_ci' => '3040506', 'nombre' => 'Vet Fish SRL', 'telefono' => '76543210', 'tipo' => 'fijo', 'estado' => 'activo']);
        $chapare = Proveedor::create(['codigo' => 'PRV-0004', 'nombre' => 'Peces Chapare', 'telefono' => '71122334', 'tipo' => 'eventual', 'estado' => 'activo']);
        $lima = Proveedor::create(['codigo' => 'PRV-0005', 'nombre' => 'Import Lima', 'telefono' => '987654321', 'tipo' => 'eventual', 'estado' => 'inactivo']);

        // ===== Productos =====
        $betta = Producto::create(['codigo' => 'PEZ-001', 'nombre' => 'Pez Betta macho', 'categoria_id' => $pez->id, 'proveedor_id' => $andino->id, 'precio_compra' => 20, 'precio_venta' => 35, 'stock_actual' => 24, 'stock_minimo' => 10, 'estado' => 'activo']);
        $guppy = Producto::create(['codigo' => 'PEZ-002', 'nombre' => 'Guppy fancy', 'categoria_id' => $pez->id, 'proveedor_id' => $chapare->id, 'precio_compra' => 6, 'precio_venta' => 12, 'stock_actual' => 75, 'stock_minimo' => 20, 'estado' => 'activo']);
        $neon = Producto::create(['codigo' => 'PEZ-004', 'nombre' => 'Neón tetra', 'categoria_id' => $pez->id, 'proveedor_id' => $lima->id, 'precio_compra' => 4, 'precio_venta' => 8, 'stock_actual' => 111, 'stock_minimo' => 30, 'estado' => 'activo']);
        $molly = Producto::create(['codigo' => 'PEZ-005', 'nombre' => 'Molly negro', 'categoria_id' => $pez->id, 'proveedor_id' => $andino->id, 'precio_compra' => 7, 'precio_venta' => 15, 'stock_actual' => 37, 'stock_minimo' => 15, 'estado' => 'activo']);
        $escalar = Producto::create(['codigo' => 'PEZ-006', 'nombre' => 'Escalar', 'categoria_id' => $pez->id, 'proveedor_id' => $andino->id, 'precio_compra' => 15, 'precio_venta' => 30, 'stock_actual' => 19, 'stock_minimo' => 8, 'estado' => 'activo']);
        Producto::create(['codigo' => 'ALI-001', 'nombre' => 'Alimento TetraMin 100g', 'categoria_id' => $ali->id, 'proveedor_id' => $aqua->id, 'precio_compra' => 30, 'precio_venta' => 45, 'stock_actual' => 10, 'stock_minimo' => 5, 'estado' => 'activo']);
        Producto::create(['codigo' => 'ACC-021', 'nombre' => 'Termostato 50W', 'categoria_id' => $acc->id, 'proveedor_id' => $aqua->id, 'precio_compra' => 90, 'precio_venta' => 120, 'stock_actual' => 1, 'stock_minimo' => 3, 'estado' => 'activo']);
        Producto::create(['codigo' => 'ACC-030', 'nombre' => 'Filtro interno 300L/h', 'categoria_id' => $acc->id, 'proveedor_id' => $aqua->id, 'precio_compra' => 110, 'precio_venta' => 150, 'stock_actual' => 2, 'stock_minimo' => 3, 'estado' => 'activo']);
        Producto::create(['codigo' => 'ACC-040', 'nombre' => 'Bomba de aire 2W', 'categoria_id' => $acc->id, 'proveedor_id' => $lima->id, 'precio_compra' => 55, 'precio_venta' => 80, 'stock_actual' => 0, 'stock_minimo' => 2, 'estado' => 'inactivo']);
        Producto::create(['codigo' => 'MED-004', 'nombre' => 'Azul de metileno', 'categoria_id' => $med->id, 'proveedor_id' => $vet->id, 'precio_compra' => 15, 'precio_venta' => 25, 'stock_actual' => 0, 'stock_minimo' => 4, 'control_especial' => true, 'nota_control' => 'Uso veterinario', 'fecha_vencimiento' => '2026-12-15', 'estado' => 'activo']);
        Producto::create(['codigo' => 'MED-007', 'nombre' => 'Oxitetraciclina 250mg', 'categoria_id' => $med->id, 'proveedor_id' => $vet->id, 'precio_compra' => 22, 'precio_venta' => 38, 'stock_actual' => 6, 'stock_minimo' => 4, 'control_especial' => true, 'nota_control' => 'Venta con receta', 'fecha_vencimiento' => '2027-02-20', 'estado' => 'activo']);
        Producto::create(['codigo' => 'MED-011', 'nombre' => 'Metronidazol 500mg', 'categoria_id' => $med->id, 'proveedor_id' => $vet->id, 'precio_compra' => 18, 'precio_venta' => 32, 'stock_actual' => 10, 'stock_minimo' => 4, 'control_especial' => true, 'nota_control' => 'Venta con receta', 'fecha_vencimiento' => '2027-09-15', 'estado' => 'activo']);
        Producto::create(['codigo' => 'REA-002', 'nombre' => 'Test de pH', 'categoria_id' => $rea->id, 'proveedor_id' => $aqua->id, 'precio_compra' => 40, 'precio_venta' => 60, 'stock_actual' => 30, 'stock_minimo' => 5, 'estado' => 'activo']);
        Producto::create(['codigo' => 'REA-009', 'nombre' => 'Formol 37%', 'categoria_id' => $rea->id, 'proveedor_id' => $vet->id, 'precio_compra' => 30, 'precio_venta' => 50, 'stock_actual' => 3, 'stock_minimo' => 3, 'control_especial' => true, 'nota_control' => 'Manipular con guantes', 'fecha_vencimiento' => '2027-06-30', 'estado' => 'activo']);
        $anubias = Producto::create(['codigo' => 'PLA-001', 'nombre' => 'Planta acuática Anubias', 'categoria_id' => $pla->id, 'proveedor_id' => $chapare->id, 'precio_compra' => 3, 'precio_venta' => 5, 'stock_actual' => 15, 'stock_minimo' => 5, 'estado' => 'activo']);

        // ===== Lotes de peces (sin triggers duplicados: ajustamos stock manual) =====
        // Nota: las migraciones crean triggers que suman cantidad_inicial al stock.
        // Para que el seed reproduzca el estado del mockup, insertamos los lotes con
        // DB::table (los triggers siguen activos) y corregimos stock a los valores del mockup.
        $l31 = LotePeces::create(['codigo' => 'L-2026-031', 'producto_id' => $betta->id, 'proveedor_id' => $andino->id, 'fecha_recepcion' => '2026-11-05', 'cantidad_inicial' => 30, 'total_retirado' => 0, 'ph' => 7.0, 'temperatura' => 26, 'precio_compra' => 20, 'estado' => 'activo', 'observaciones' => 'Llegada en buen estado']);
        $l30 = LotePeces::create(['codigo' => 'L-2026-030', 'producto_id' => $guppy->id, 'proveedor_id' => $chapare->id, 'fecha_recepcion' => '2026-11-01', 'cantidad_inicial' => 80, 'total_retirado' => 0, 'ph' => 7.2, 'temperatura' => 25, 'precio_compra' => 6, 'estado' => 'en_tratamiento']);
        $l29 = LotePeces::create(['codigo' => 'L-2026-029', 'producto_id' => $neon->id, 'proveedor_id' => $lima->id, 'fecha_recepcion' => '2026-10-28', 'cantidad_inicial' => 120, 'total_retirado' => 0, 'ph' => 6.8, 'temperatura' => 24, 'precio_compra' => 4, 'estado' => 'activo']);
        $l28 = LotePeces::create(['codigo' => 'L-2026-028', 'producto_id' => $molly->id, 'proveedor_id' => $andino->id, 'fecha_recepcion' => '2026-10-20', 'cantidad_inicial' => 40, 'total_retirado' => 0, 'ph' => 7.4, 'temperatura' => 25, 'precio_compra' => 7, 'estado' => 'activo']);
        $l27 = LotePeces::create(['codigo' => 'L-2026-027', 'producto_id' => $escalar->id, 'proveedor_id' => $andino->id, 'fecha_recepcion' => '2026-10-12', 'cantidad_inicial' => 20, 'total_retirado' => 0, 'ph' => 6.9, 'temperatura' => 25, 'precio_compra' => 15, 'estado' => 'cerrado']);

        // Retiros registrados (actualizan total_retirado y descuentan stock via trigger)
        foreach ([[$l31, 2], [$l30, 5], [$l29, 9], [$l28, 3], [$l27, 1]] as [$lote, $cant]) {
            DB::table('retiros_lote')->insert([
                'lote_id' => $lote->id, 'usuario_id' => $admin->id,
                'fecha' => now()->subDays(3), 'cantidad' => $cant,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // Tratamiento en curso en el lote de guppys
        $l30->tratamientos()->create([
            'fecha_inicio' => '2026-11-06', 'sintomas' => 'Aletas pegadas, poca actividad',
            'producto' => 'Azul de metileno', 'dosis' => '2 gotas/L',
        ]);

        // Ajustar el stock de los peces a los valores del mockup
        // (los triggers sumaron cantidad_inicial y restaron retiros)
        $betta->update(['stock_actual' => 24]);
        $guppy->update(['stock_actual' => 75]);
        $neon->update(['stock_actual' => 111]);
        $molly->update(['stock_actual' => 37]);
        $escalar->update(['stock_actual' => 19]);

        // ===== Clientes =====
        $juan = Cliente::create(['codigo' => 'CLI-00001', 'ci' => '8912345', 'nombre' => 'Juan Pérez', 'telefono' => '70711223', 'estado' => 'activo']);
        $maria = Cliente::create(['codigo' => 'CLI-00002', 'ci' => '7234567', 'nombre' => 'María López', 'telefono' => '70123456', 'estado' => 'activo']);
        $carlos = Cliente::create(['codigo' => 'CLI-00003', 'ci' => '6456789', 'nombre' => 'Carlos Vargas', 'telefono' => '72233445', 'estado' => 'activo']);

        // ===== Ventas de ejemplo (VEN-00139 a VEN-00142) =====
        // Usamos DB::table para las líneas: el trigger descuenta stock y acumula el total.
        $tetra = Producto::where('codigo', 'ALI-001')->first();
        $testPh = Producto::where('codigo', 'REA-002')->first();
        $filtro = Producto::where('codigo', 'ACC-030')->first();
        $termo = Producto::where('codigo', 'ACC-021')->first();

        $ventas = [
            ['codigo' => 'VEN-00139', 'fecha' => '2026-11-07 16:20', 'cliente' => $carlos->id, 'pago' => 'qr', 'canal' => 'whatsapp', 'vendedor' => $vendedor->id, 'items' => [[$filtro, 1], [$testPh, 2], [$tetra, 2], [$anubias, 12]]],
            ['codigo' => 'VEN-00140', 'fecha' => '2026-11-07 18:05', 'cliente' => null, 'pago' => 'efectivo', 'canal' => 'presencial', 'vendedor' => $vendedor->id, 'items' => [[$betta, 1]]],
            ['codigo' => 'VEN-00141', 'fecha' => '2026-11-08 09:40', 'cliente' => $maria->id, 'pago' => 'efectivo', 'canal' => 'presencial', 'vendedor' => $vendedor->id, 'items' => [[$guppy, 3], [$anubias, 2]]],
            ['codigo' => 'VEN-00142', 'fecha' => '2026-11-08 11:20', 'cliente' => $juan->id, 'pago' => 'qr', 'canal' => 'whatsapp', 'vendedor' => $vendedor->id, 'items' => [[$betta, 2], [$tetra, 1], [$testPh, 1], [$anubias, 1]]],
        ];

        foreach ($ventas as $v) {
            $venta = DB::table('ventas')->insertGetId([
                'codigo' => $v['codigo'], 'fecha' => $v['fecha'],
                'usuario_id' => $v['vendedor'], 'cliente_id' => $v['cliente'],
                'forma_pago' => $v['pago'], 'canal' => $v['canal'],
                'unidades' => 0, 'total' => 0, 'estado' => 'registrada',
                'created_at' => now(), 'updated_at' => now(),
            ]);
            foreach ($v['items'] as [$prod, $cant]) {
                $prod->refresh();
                DB::table('detalle_ventas')->insert([
                    'venta_id' => $venta, 'producto_id' => $prod->id,
                    'cantidad' => $cant, 'precio_unitario' => $prod->precio_venta,
                    'subtotal' => $prod->precio_venta * $cant,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }

        // ===== Entradas de mercadería de ejemplo =====
        $metron = Producto::where('codigo', 'MED-011')->first();
        $oxi = Producto::where('codigo', 'MED-007')->first();

        $entradas = [
            ['codigo' => 'ENT-00055', 'fecha' => '2026-11-02 11:30', 'proveedor' => $lima->id, 'doc' => 'REC-332', 'user' => $admin->id, 'estado' => 'registrada', 'items' => [[$tetra, 10, 30], [$filtro, 5, 110], [$termo, 4, 90]]],
            ['codigo' => 'ENT-00056', 'fecha' => '2026-11-04 09:05', 'proveedor' => $aqua->id, 'doc' => 'F-001198', 'user' => $vendedor->id, 'estado' => 'anulada', 'items' => [[$tetra, 15, 30], [$testPh, 10, 40], [$anubias, 20, 3], [$filtro, 2, 110]]],
            ['codigo' => 'ENT-00057', 'fecha' => '2026-11-06 16:40', 'proveedor' => $vet->id, 'doc' => 'F-88712', 'user' => $admin->id, 'estado' => 'registrada', 'items' => [[$metron, 15, 18], [$oxi, 10, 22], [$testPh, 8, 40]]],
            ['codigo' => 'ENT-00058', 'fecha' => '2026-11-08 10:15', 'proveedor' => $aqua->id, 'doc' => 'F-001245', 'user' => $admin->id, 'estado' => 'registrada', 'items' => [[$tetra, 8, 30], [$termo, 2, 90], [$filtro, 1, 110]]],
        ];

        foreach ($entradas as $e) {
            $id = DB::table('entradas_mercaderia')->insertGetId([
                'codigo' => $e['codigo'], 'fecha' => $e['fecha'],
                'proveedor_id' => $e['proveedor'], 'nro_documento' => $e['doc'],
                'usuario_id' => $e['user'], 'items_count' => count($e['items']),
                'total' => 0, 'estado' => $e['estado'],
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $total = 0;
            foreach ($e['items'] as [$prod, $cant, $precio]) {
                $sub = $cant * $precio;
                $total += $sub;
                if ($e['estado'] === 'registrada') {
                    // inserta vía trigger que suma el stock
                    DB::table('detalle_entradas')->insert([
                        'entrada_id' => $id, 'producto_id' => $prod->id,
                        'cantidad' => $cant, 'precio_compra' => $precio, 'subtotal' => $sub,
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                } else {
                    // entrada anulada: registrar la línea sin mover stock
                    DB::table('detalle_entradas')->insert([
                        'entrada_id' => $id, 'producto_id' => $prod->id,
                        'cantidad' => $cant, 'precio_compra' => $precio, 'subtotal' => $sub,
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                }
            }
            DB::table('entradas_mercaderia')->where('id', $id)->update(['total' => $total]);
        }

        // Corregir stock final para que coincida con el mockup
        // (las entradas sumaron; restablecemos los valores del diseño)
        DB::table('productos')->where('codigo', 'ALI-001')->update(['stock_actual' => 2]);
        DB::table('productos')->where('codigo', 'ACC-021')->update(['stock_actual' => 1]);
        DB::table('productos')->where('codigo', 'ACC-030')->update(['stock_actual' => 2]);
        DB::table('productos')->where('codigo', 'REA-002')->update(['stock_actual' => 12]);
        DB::table('productos')->where('codigo', 'MED-011')->update(['stock_actual' => 10]);
        DB::table('productos')->where('codigo', 'MED-007')->update(['stock_actual' => 6]);
        DB::table('productos')->where('codigo', 'PLA-001')->update(['stock_actual' => 15]);
        DB::table('productos')->where('codigo', 'PEZ-001')->update(['stock_actual' => 24]);
        DB::table('productos')->where('codigo', 'PEZ-002')->update(['stock_actual' => 75]);
        DB::table('productos')->where('codigo', 'PEZ-004')->update(['stock_actual' => 111]);
        DB::table('productos')->where('codigo', 'PEZ-005')->update(['stock_actual' => 37]);
        DB::table('productos')->where('codigo', 'PEZ-006')->update(['stock_actual' => 19]);
    }
}
