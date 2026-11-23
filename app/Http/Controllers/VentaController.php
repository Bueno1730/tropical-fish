<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $query = Venta::with(['cliente', 'usuario']);

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

        return view('ventas.index', [
            'ventas' => $query->latest('fecha')->paginate(12)->withQueryString(),
        ]);
    }

    public function create()
    {
        return view('ventas.create', [
            'productos' => Producto::where('estado', 'activo')->orderBy('nombre')->get(['id', 'codigo', 'nombre', 'precio_venta', 'stock_actual']),
            'clientes' => Cliente::where('estado', 'activo')->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.producto_id' => ['required', 'exists:productos,id'],
            'items.*.cantidad' => ['required', 'integer', 'min:1'],
            'forma_pago' => ['required', 'in:efectivo,qr'],
            'canal' => ['required', 'in:presencial,whatsapp,facebook,tiktok,instagram,otro'],
            'cliente_id' => ['nullable', 'exists:clientes,id'],
            'cliente_nuevo' => ['nullable', 'string', 'max:150'],
        ], [
            'items.required' => 'Agrega al menos un producto a la venta.',
            'forma_pago.required' => 'Selecciona la forma de pago.',
            'canal.required' => 'Selecciona el canal de venta.',
        ]);

        try {
            $venta = DB::transaction(function () use ($datos, $request) {
                $clienteId = $datos['cliente_id'] ?? null;

                if (! $clienteId && ! empty($datos['cliente_nuevo'])) {
                    $clienteId = Cliente::create([
                        'codigo' => Cliente::proximoCodigo(),
                        'nombre' => $datos['cliente_nuevo'],
                        'estado' => 'activo',
                    ])->id;
                }

                $venta = Venta::create([
                    'codigo' => Venta::proximoCodigo(),
                    'fecha' => now(),
                    'usuario_id' => $request->user()->id,
                    'cliente_id' => $clienteId,
                    'forma_pago' => $datos['forma_pago'],
                    'canal' => $datos['canal'],
                ]);

                foreach ($datos['items'] as $item) {
                    $producto = Producto::lockForUpdate()->findOrFail($item['producto_id']);
                    $venta->detalles()->create([
                        'producto_id' => $producto->id,
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $producto->precio_venta,
                        'subtotal' => $producto->precio_venta * $item['cantidad'],
                    ]);
                }

                return $venta;
            });
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withInput()->with('error', $this->mensajeSql($e));
        }

        return redirect()->route('ventas.show', $venta)->with('exito', "Venta {$venta->codigo} registrada por Bs " . number_format($venta->fresh()->total, 2));
    }

    public function show(Venta $venta)
    {
        $venta->load(['cliente', 'usuario', 'detalles.producto']);

        return view('ventas.show', compact('venta'));
    }

    public function anular(Venta $venta)
    {
        if (! $venta->estaRegistrada()) {
            return back()->with('error', 'La venta ya está anulada.');
        }

        DB::transaction(function () use ($venta) {
            foreach ($venta->detalles as $detalle) {
                Producto::where('id', $detalle->producto_id)->increment('stock_actual', $detalle->cantidad);
            }
            $venta->update(['estado' => 'anulada']);
        });

        return back()->with('exito', "Venta {$venta->codigo} anulada. El stock fue repuesto.");
    }

    private function mensajeSql(\Illuminate\Database\QueryException $e): string
    {
        if (str_contains($e->getMessage(), '45000')) {
            return 'No hay stock suficiente para completar la venta.';
        }

        return 'No se pudo registrar la venta. Revisa los datos e inténtalo de nuevo.';
    }
}
