<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with(['categoria', 'proveedor']);

        if ($buscar = $request->input('buscar')) {
            $query->where(fn ($q) => $q->where('codigo', 'like', "%{$buscar}%")->orWhere('nombre', 'like', "%{$buscar}%"));
        }
        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->categoria);
        }
        if ($request->filled('proveedor')) {
            $query->where('proveedor_id', $request->proveedor);
        }
        if ($request->boolean('stock_critico')) {
            $query->whereColumn('stock_actual', '<=', 'stock_minimo');
        }

        return view('productos.index', [
            'productos' => $query->orderBy('codigo')->paginate(10)->withQueryString(),
            'categorias' => Categoria::orderBy('nombre')->get(),
            'proveedores' => Proveedor::orderBy('nombre')->get(),
        ]);
    }

    public function create()
    {
        return view('productos.create', [
            'categorias' => Categoria::orderBy('nombre')->get(),
            'proveedores' => Proveedor::where('estado', 'activo')->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $this->validarProducto($request);

        $datos['control_especial'] = $request->boolean('control_especial');
        $datos['estado'] = 'activo';
        $datos['stock_actual'] = 0; // el stock solo entra por entradas o lotes

        Producto::create($datos);

        return redirect()->route('productos.index')->with('exito', "Producto {$datos['codigo']} registrado correctamente.");
    }

    public function show(Producto $producto)
    {
        $producto->load(['categoria', 'proveedor', 'lotes' => fn ($q) => $q->latest('fecha_recepcion')]);

        return view('productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        return view('productos.edit', [
            'producto' => $producto,
            'categorias' => Categoria::orderBy('nombre')->get(),
            'proveedores' => Proveedor::where('estado', 'activo')->orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, Producto $producto)
    {
        $datos = $this->validarProducto($request, $producto);
        $datos['control_especial'] = $request->boolean('control_especial');

        $producto->update($datos);

        return redirect()->route('productos.index')->with('exito', "Producto {$producto->codigo} actualizado.");
    }

    // Elimina si no tiene dependencias; si las tiene, solo permite desactivar (RNF-10)
    public function destroy(Producto $producto)
    {
        if ($producto->tieneDependencias()) {
            return back()->with('error', "El producto {$producto->codigo} tiene entradas, lotes o ventas asociadas. Solo puede desactivarse.");
        }

        $producto->delete();

        return redirect()->route('productos.index')->with('exito', 'Producto eliminado.');
    }

    public function cambiarEstado(Producto $producto)
    {
        $producto->update(['estado' => $producto->estado === 'activo' ? 'inactivo' : 'activo']);

        $accion = $producto->estado === 'activo' ? 'reactivado' : 'desactivado';

        return back()->with('exito', "Producto {$producto->codigo} {$accion}.");
    }

    private function validarProducto(Request $request, ?Producto $producto = null): array
    {
        return $request->validate([
            'codigo' => ['required', 'string', 'max:15', Rule::unique('productos', 'codigo')->ignore($producto)],
            'nombre' => ['required', 'string', 'max:150'],
            'categoria_id' => ['required', 'exists:categorias,id'],
            'proveedor_id' => ['nullable', 'exists:proveedores,id'],
            'precio_venta' => ['required', 'numeric', 'min:0'],
            'precio_compra' => ['nullable', 'numeric', 'min:0'],
            'stock_minimo' => ['required', 'integer', 'min:0'],
            'unidad' => ['required', 'string', 'max:30'],
            'control_especial' => ['nullable', 'boolean'],
            'nota_control' => ['nullable', 'string', 'max:200', 'required_if:control_especial,1'],
            'fecha_vencimiento' => ['nullable', 'date'],
        ], [
            'codigo.unique' => 'Ya existe un producto con ese código.',
            'precio_venta.min' => 'El precio no puede ser negativo.',
            'nota_control.required_if' => 'Indica la nota de control para un producto regulado.',
        ]);
    }
}
