<?php

namespace App\Http\Controllers;

use App\Models\EntradaMercaderia;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntradaController extends Controller
{
    public function index(Request $request)
    {
        $query = EntradaMercaderia::with(['proveedor', 'usuario']);

        if ($request->filled('desde')) {
            $query->whereDate('fecha', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('fecha', '<=', $request->hasta);
        }
        if ($request->filled('proveedor')) {
            $query->where('proveedor_id', $request->proveedor);
        }

        return view('entradas.index', [
            'entradas' => $query->latest('fecha')->paginate(10)->withQueryString(),
            'proveedores' => Proveedor::orderBy('nombre')->get(),
        ]);
    }

    public function create()
    {
        return view('entradas.create', [
            'proveedores' => Proveedor::where('estado', 'activo')->orderBy('nombre')->get(),
            // Las entradas no admiten peces: se registran como lote (trg_dent_bi)
            'productos' => Producto::with('categoria')->where('estado', 'activo')
                ->whereDoesntHave('categoria', fn ($q) => $q->where('maneja_lotes', true))
                ->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);

        try {
            $entrada = DB::transaction(function () use ($datos, $request) {
                $entrada = EntradaMercaderia::create([
                    'codigo' => EntradaMercaderia::proximoCodigo(),
                    'fecha' => $datos['fecha'],
                    'proveedor_id' => $datos['proveedor_id'],
                    'nro_documento' => $datos['nro_documento'] ?? null,
                    'usuario_id' => $request->user()->id,
                    'items_count' => count($datos['items']),
                    'total' => 0,
                ]);

                $total = 0;
                foreach ($datos['items'] as $item) {
                    $subtotal = $item['cantidad'] * $item['precio_compra'];
                    $entrada->detalles()->create([
                        'producto_id' => $item['producto_id'],
                        'cantidad' => $item['cantidad'],
                        'precio_compra' => $item['precio_compra'],
                        'subtotal' => $subtotal,
                    ]);
                    $total += $subtotal;
                }

                $entrada->update(['total' => $total]);

                return $entrada;
            });
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withInput()->with('error', $this->mensajeSql($e));
        }

        return redirect()->route('entradas.show', $entrada)->with('exito', "Entrada {$entrada->codigo} registrada y stock actualizado.");
    }

    public function show(EntradaMercaderia $entrada)
    {
        $entrada->load(['proveedor', 'usuario', 'detalles.producto']);

        return view('entradas.show', compact('entrada'));
    }

    public function edit(EntradaMercaderia $entrada)
    {
        if (! $entrada->estaRegistrada()) {
            return redirect()->route('entradas.show', $entrada)->with('error', 'Una entrada anulada no se puede editar.');
        }

        $entrada->load('detalles');

        return view('entradas.edit', [
            'entrada' => $entrada,
            'proveedores' => Proveedor::where('estado', 'activo')->orderBy('nombre')->get(),
            'productos' => Producto::with('categoria')->where('estado', 'activo')
                ->whereDoesntHave('categoria', fn ($q) => $q->where('maneja_lotes', true))
                ->orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, EntradaMercaderia $entrada)
    {
        if (! $entrada->estaRegistrada()) {
            return back()->with('error', 'Una entrada anulada no se puede editar.');
        }

        $datos = $this->validar($request);

        try {
            DB::transaction(function () use ($datos, $entrada) {
                // Revertir el stock de las líneas anteriores
                foreach ($entrada->detalles as $detalle) {
                    Producto::where('id', $detalle->producto_id)->decrement('stock_actual', $detalle->cantidad);
                }
                $entrada->detalles()->delete();

                // Insertar las nuevas líneas (el trigger suma el stock)
                $total = 0;
                foreach ($datos['items'] as $item) {
                    $subtotal = $item['cantidad'] * $item['precio_compra'];
                    $entrada->detalles()->create([
                        'producto_id' => $item['producto_id'],
                        'cantidad' => $item['cantidad'],
                        'precio_compra' => $item['precio_compra'],
                        'subtotal' => $subtotal,
                    ]);
                    $total += $subtotal;
                }

                $entrada->update([
                    'fecha' => $datos['fecha'],
                    'proveedor_id' => $datos['proveedor_id'],
                    'nro_documento' => $datos['nro_documento'] ?? null,
                    'items_count' => count($datos['items']),
                    'total' => $total,
                ]);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withInput()->with('error', $this->mensajeSql($e));
        }

        return redirect()->route('entradas.show', $entrada)->with('exito', "Entrada {$entrada->codigo} actualizada y stock ajustado.");
    }

    // Anula la entrada revirtiendo el stock; no se borra para conservar el historial
    public function anular(EntradaMercaderia $entrada)
    {
        if (! $entrada->estaRegistrada()) {
            return back()->with('error', 'La entrada ya está anulada.');
        }

        $problema = null;
        foreach ($entrada->detalles as $detalle) {
            if ($detalle->producto->stock_actual < $detalle->cantidad) {
                $problema = "No se puede anular: el producto {$detalle->producto->codigo} tiene {$detalle->producto->stock_actual} en stock y la entrada registró {$detalle->cantidad}.";
                break;
            }
        }
        if ($problema) {
            return back()->with('error', $problema);
        }

        DB::transaction(function () use ($entrada) {
            foreach ($entrada->detalles as $detalle) {
                Producto::where('id', $detalle->producto_id)->decrement('stock_actual', $detalle->cantidad);
            }
            $entrada->update(['estado' => 'anulada']);
        });

        return back()->with('exito', "Entrada {$entrada->codigo} anulada. El stock fue revertido.");
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'fecha' => ['required', 'date'],
            'proveedor_id' => ['required', 'exists:proveedores,id'],
            'nro_documento' => ['nullable', 'string', 'max:50'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.producto_id' => ['required', 'exists:productos,id'],
            'items.*.cantidad' => ['required', 'integer', 'min:1'],
            'items.*.precio_compra' => ['required', 'numeric', 'min:0'],
        ], [
            'items.required' => 'Agrega al menos un producto a la entrada.',
            'items.min' => 'Agrega al menos un producto a la entrada.',
        ]);
    }

    private function mensajeSql(\Illuminate\Database\QueryException $e): string
    {
        if (str_contains($e->getMessage(), '45000')) {
            if (preg_match("/MESSAGE_TEXT = '([^']+)'|45000: ([^\\n]+)/", $e->getMessage(), $m)) {
                return trim(end($m));
            }
        }

        return 'No se pudo registrar la entrada. Revisa los datos e inténtalo de nuevo.';
    }
}
