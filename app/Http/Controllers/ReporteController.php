<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index()
    {
        return view('reportes.index');
    }

    public function stockCritico(Request $request)
    {
        $filas = DB::table('v_stock_critico')->get();

        if ($request->boolean('exportar')) {
            return $this->csv('stock_critico.csv', ['Código', 'Producto', 'Stock', 'Mínimo', 'Proveedor', 'Teléfono'],
                $filas->map(fn ($f) => [$f->codigo, $f->nombre, $f->stock_actual, $f->stock_minimo, $f->proveedor ?? '—', $f->telefono ?? '—'])->all());
        }

        return view('reportes.stock-critico', ['filas' => $filas, 'imprimir' => $request->boolean('imprimir')]);
    }

    public function masVendidos(Request $request)
    {
        $filas = DB::table('v_productos_mas_vendidos')->get();

        if ($desde = $request->input('desde')) {
            $filas = $this->filtrarPorVentas($filas, $desde, $request->input('hasta'));
        }

        if ($request->boolean('exportar')) {
            return $this->csv('productos_mas_vendidos.csv', ['Código', 'Producto', 'Unidades vendidas', 'Monto (Bs)', 'N° ventas'],
                $filas->map(fn ($f) => [$f->codigo, $f->nombre, $f->unidades_vendidas, $f->monto_total, $f->numero_ventas])->all());
        }

        return view('reportes.mas-vendidos', ['filas' => $filas, 'imprimir' => $request->boolean('imprimir')]);
    }

    public function historialVentas(Request $request)
    {
        $query = DB::table('v_historial_ventas');

        if ($request->filled('desde')) {
            $query->whereDate('fecha', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('fecha', '<=', $request->hasta);
        }
        if ($request->filled('forma_pago')) {
            $query->where('forma_pago', $request->forma_pago);
        }
        if ($request->filled('canal')) {
            $query->where('canal', $request->canal);
        }
        $query->orderByDesc('fecha');

        $filas = $query->get();
        $total = $filas->where('estado', 'registrada')->sum('total');

        if ($request->boolean('exportar')) {
            return $this->csv('historial_ventas.csv', ['Código', 'Fecha', 'Cliente', 'Vendedor', 'Pago', 'Canal', 'Unidades', 'Total (Bs)', 'Estado'],
                $filas->map(fn ($f) => [$f->codigo, $f->fecha, $f->cliente, $f->vendedor, $f->forma_pago, $f->canal, $f->unidades, $f->total, $f->estado])->all());
        }

        return view('reportes.historial-ventas', ['filas' => $filas, 'total' => $total, 'imprimir' => $request->boolean('imprimir')]);
    }

    public function retirosPorLote(Request $request)
    {
        $filas = DB::table('v_retiros_por_lote')->orderByDesc('fecha_recepcion')->get();

        if ($request->boolean('exportar')) {
            return $this->csv('retiros_por_lote.csv', ['Lote', 'Especie', 'Recepción', 'Inicial', 'Retiradas', 'Disponibles', '% retirado', 'N° retiros', 'Estado'],
                $filas->map(fn ($f) => [$f->codigo, $f->especie, $f->fecha_recepcion, $f->cantidad_inicial, $f->total_retirado, $f->disponibles, $f->porcentaje_retirado, $f->numero_retiros, $f->estado])->all());
        }

        return view('reportes.retiros-lote', ['filas' => $filas, 'imprimir' => $request->boolean('imprimir')]);
    }

    public function controlEspecial(Request $request)
    {
        $filas = DB::table('v_productos_control_especial')->get();

        if ($request->boolean('exportar')) {
            return $this->csv('control_especial.csv', ['Código', 'Producto', 'Categoría', 'Stock', 'Nota de control', 'Vencimiento'],
                $filas->map(fn ($f) => [$f->codigo, $f->nombre, $f->categoria, $f->stock_actual, $f->nota_control, $f->fecha_vencimiento])->all());
        }

        return view('reportes.control-especial', ['filas' => $filas, 'imprimir' => $request->boolean('imprimir')]);
    }

    private function filtrarPorVentas($filas, ?string $desde, ?string $hasta)
    {
        $query = DB::table('detalle_ventas')
            ->join('ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
            ->join('productos', 'productos.id', '=', 'detalle_ventas.producto_id')
            ->where('ventas.estado', 'registrada')
            ->select('productos.codigo', 'productos.nombre',
                DB::raw('SUM(detalle_ventas.cantidad) AS unidades_vendidas'),
                DB::raw('SUM(detalle_ventas.subtotal) AS monto_total'),
                DB::raw('COUNT(DISTINCT detalle_ventas.venta_id) AS numero_ventas'))
            ->groupBy('productos.id', 'productos.codigo', 'productos.nombre')
            ->orderByDesc('unidades_vendidas');

        if ($desde) {
            $query->whereDate('ventas.fecha', '>=', $desde);
        }
        if ($hasta) {
            $query->whereDate('ventas.fecha', '<=', $hasta);
        }

        return $query->get();
    }

    private function csv(string $nombre, array $encabezado, array $filas)
    {
        $out = fopen('php://temp', 'r+');
        fputcsv($out, $encabezado);
        foreach ($filas as $fila) {
            fputcsv($out, $fila);
        }
        rewind($out);
        $contenido = stream_get_contents($out);
        fclose($out);

        return response("\xEF\xBB\xBF" . $contenido, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$nombre}\"",
        ]);
    }
}
