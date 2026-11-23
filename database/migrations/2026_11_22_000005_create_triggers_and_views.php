<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        // ===== DISPARADORES (Tabla 57 del documento) =====

        // Impide registrar peces como entrada de mercadería (se registran como lote)
        DB::unprepared("
            CREATE TRIGGER trg_dent_bi BEFORE INSERT ON detalle_entradas
            FOR EACH ROW
            BEGIN
                DECLARE maneja BOOLEAN;
                SELECT c.maneja_lotes INTO maneja
                    FROM productos p JOIN categorias c ON c.id = p.categoria_id
                    WHERE p.id = NEW.producto_id;
                IF maneja THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Los peces no se registran como entrada de mercadería; deben registrarse como lote.';
                END IF;
            END
        ");

        // Suma la cantidad al stock y actualiza el precio de compra
        DB::unprepared("
            CREATE TRIGGER trg_dent_ai AFTER INSERT ON detalle_entradas
            FOR EACH ROW
            BEGIN
                UPDATE productos
                    SET stock_actual = stock_actual + NEW.cantidad,
                        precio_compra = NEW.precio_compra
                    WHERE id = NEW.producto_id;
            END
        ");

        // Verifica que el producto pertenezca a una categoría manejada por lotes
        DB::unprepared("
            CREATE TRIGGER trg_lote_bi BEFORE INSERT ON lotes_peces
            FOR EACH ROW
            BEGIN
                DECLARE maneja BOOLEAN;
                SELECT c.maneja_lotes INTO maneja
                    FROM productos p JOIN categorias c ON c.id = p.categoria_id
                    WHERE p.id = NEW.producto_id;
                IF NOT maneja THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Este producto no pertenece a una categoría manejada por lotes.';
                END IF;
            END
        ");

        // Suma la cantidad inicial del lote al stock del producto
        DB::unprepared("
            CREATE TRIGGER trg_lote_ai AFTER INSERT ON lotes_peces
            FOR EACH ROW
            BEGIN
                UPDATE productos
                    SET stock_actual = stock_actual + NEW.cantidad_inicial
                    WHERE id = NEW.producto_id;
            END
        ");

        // Impide retirar más unidades de las disponibles en el lote o del stock
        DB::unprepared("
            CREATE TRIGGER trg_retiro_bi BEFORE INSERT ON retiros_lote
            FOR EACH ROW
            BEGIN
                DECLARE disponibles INT;
                DECLARE stock INT;
                SELECT (l.cantidad_inicial - l.total_retirado), p.stock_actual
                    INTO disponibles, stock
                    FROM lotes_peces l JOIN productos p ON p.id = l.producto_id
                    WHERE l.id = NEW.lote_id;
                IF NEW.cantidad > disponibles THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'No se puede retirar más unidades de las disponibles en el lote.';
                END IF;
                IF NEW.cantidad > stock THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'No hay stock suficiente para registrar el retiro.';
                END IF;
            END
        ");

        // Acumula el total retirado del lote y descuenta el stock
        DB::unprepared("
            CREATE TRIGGER trg_retiro_ai AFTER INSERT ON retiros_lote
            FOR EACH ROW
            BEGIN
                UPDATE lotes_peces
                    SET total_retirado = total_retirado + NEW.cantidad
                    WHERE id = NEW.lote_id;
                UPDATE productos p JOIN lotes_peces l ON l.id = NEW.lote_id
                    SET p.stock_actual = p.stock_actual - NEW.cantidad
                    WHERE p.id = l.producto_id;
            END
        ");

        // Verifica que exista stock suficiente antes de vender
        DB::unprepared("
            CREATE TRIGGER trg_detventa_bi BEFORE INSERT ON detalle_ventas
            FOR EACH ROW
            BEGIN
                DECLARE stock INT;
                SELECT stock_actual INTO stock FROM productos WHERE id = NEW.producto_id;
                IF NEW.cantidad > stock THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'No hay stock suficiente para completar la venta.';
                END IF;
            END
        ");

        // Descuenta el stock y acumula el total de la venta
        DB::unprepared("
            CREATE TRIGGER trg_detventa_ai AFTER INSERT ON detalle_ventas
            FOR EACH ROW
            BEGIN
                UPDATE productos
                    SET stock_actual = stock_actual - NEW.cantidad
                    WHERE id = NEW.producto_id;
                UPDATE ventas
                    SET total = total + NEW.subtotal,
                        unidades = unidades + NEW.cantidad
                    WHERE id = NEW.venta_id;
            END
        ");

        // ===== VISTAS PARA REPORTES (Tabla 58 del documento) =====

        DB::unprepared("
            CREATE OR REPLACE VIEW v_stock_critico AS
            SELECT p.codigo, p.nombre, p.stock_actual, p.stock_minimo,
                   pr.codigo AS proveedor_codigo, pr.nombre AS proveedor, pr.telefono
            FROM productos p
            LEFT JOIN proveedores pr ON pr.id = p.proveedor_id
            WHERE p.estado = 'activo' AND p.stock_actual <= p.stock_minimo
            ORDER BY p.stock_actual ASC, p.nombre ASC
        ");

        DB::unprepared("
            CREATE OR REPLACE VIEW v_productos_mas_vendidos AS
            SELECT p.codigo, p.nombre,
                   SUM(d.cantidad) AS unidades_vendidas,
                   SUM(d.subtotal) AS monto_total,
                   COUNT(DISTINCT d.venta_id) AS numero_ventas
            FROM detalle_ventas d
            JOIN productos p ON p.id = d.producto_id
            JOIN ventas v ON v.id = d.venta_id AND v.estado = 'registrada'
            GROUP BY p.id, p.codigo, p.nombre
            ORDER BY unidades_vendidas DESC
        ");

        DB::unprepared("
            CREATE OR REPLACE VIEW v_historial_ventas AS
            SELECT v.codigo, v.fecha,
                   YEARWEEK(v.fecha, 3) AS semana,
                   DATE_FORMAT(v.fecha, '%Y-%m') AS mes,
                   COALESCE(c.nombre, 'Cliente general') AS cliente,
                   u.usuario AS vendedor, v.forma_pago, v.canal,
                   v.unidades, v.total, v.estado
            FROM ventas v
            LEFT JOIN clientes c ON c.id = v.cliente_id
            JOIN usuarios u ON u.id = v.usuario_id
        ");

        DB::unprepared("
            CREATE OR REPLACE VIEW v_retiros_por_lote AS
            SELECT l.codigo, p.nombre AS especie, l.fecha_recepcion,
                   l.cantidad_inicial, l.total_retirado,
                   (l.cantidad_inicial - l.total_retirado) AS disponibles,
                   ROUND(l.total_retirado * 100 / l.cantidad_inicial, 1) AS porcentaje_retirado,
                   (SELECT COUNT(*) FROM retiros_lote r WHERE r.lote_id = l.id) AS numero_retiros,
                   l.estado
            FROM lotes_peces l
            JOIN productos p ON p.id = l.producto_id
        ");

        DB::unprepared("
            CREATE OR REPLACE VIEW v_productos_control_especial AS
            SELECT p.codigo, p.nombre, c.nombre AS categoria, p.stock_actual,
                   p.nota_control, p.fecha_vencimiento
            FROM productos p
            JOIN categorias c ON c.id = p.categoria_id
            WHERE p.control_especial = 1 AND p.estado = 'activo'
            ORDER BY p.fecha_vencimiento ASC
        ");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        foreach (['v_stock_critico', 'v_productos_mas_vendidos', 'v_historial_ventas', 'v_retiros_por_lote', 'v_productos_control_especial'] as $vista) {
            DB::unprepared("DROP VIEW IF EXISTS {$vista}");
        }
        foreach (['trg_detventa_ai', 'trg_detventa_bi', 'trg_retiro_ai', 'trg_retiro_bi', 'trg_lote_ai', 'trg_lote_bi', 'trg_dent_ai', 'trg_dent_bi'] as $trg) {
            DB::unprepared("DROP TRIGGER IF EXISTS {$trg}");
        }
    }
};
