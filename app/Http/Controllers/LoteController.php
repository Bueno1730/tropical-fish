<?php

namespace App\Http\Controllers;

use App\Models\LotePeces;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LoteController extends Controller
{
    public function index(Request $request)
    {
        $query = LotePeces::with(['producto', 'proveedor']);

        if ($buscar = $request->input('buscar')) {
            $query->where(fn ($q) => $q->where('codigo', 'like', "%{$buscar}%")
                ->orWhereHas('producto', fn ($p) => $p->where('nombre', 'like', "%{$buscar}%")->orWhere('codigo', 'like', "%{$buscar}%")));
        }
        if ($request->filled('proveedor')) {
            $query->where('proveedor_id', $request->proveedor);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        return view('lotes.index', [
            'lotes' => $query->latest('fecha_recepcion')->paginate(10)->withQueryString(),
            'proveedores' => Proveedor::orderBy('nombre')->get(),
        ]);
    }

    public function create()
    {
        return view('lotes.create', [
            'peces' => Producto::where('estado', 'activo')->whereHas('categoria', fn ($q) => $q->where('maneja_lotes', true))->orderBy('nombre')->get(),
            'proveedores' => Proveedor::where('estado', 'activo')->orderBy('nombre')->get(),
            'proximoCodigo' => LotePeces::proximoCodigo(),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $this->validarLote($request);

        try {
            $lote = DB::transaction(function () use ($datos) {
                return LotePeces::create($datos + [
                    'codigo' => LotePeces::proximoCodigo(),
                    'estado' => 'activo',
                ]);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withInput()->with('error', $this->mensajeSql($e));
        }

        return redirect()->route('lotes.show', $lote)->with('exito', "Lote {$lote->codigo} registrado. La cantidad se sumó al stock.");
    }

    public function show(LotePeces $lote)
    {
        $lote->load(['producto', 'proveedor', 'tratamientos' => fn ($q) => $q->latest('fecha_inicio'), 'retiros' => fn ($q) => $q->latest('fecha')->with('usuario')]);

        return view('lotes.show', compact('lote'));
    }

    public function edit(LotePeces $lote)
    {
        return view('lotes.edit', [
            'lote' => $lote,
            'peces' => Producto::whereHas('categoria', fn ($q) => $q->where('maneja_lotes', true))->orderBy('nombre')->get(),
            'proveedores' => Proveedor::where('estado', 'activo')->orderBy('nombre')->get(),
        ]);
    }

    // Editar ajusta el stock si cambia la cantidad inicial
    public function update(Request $request, LotePeces $lote)
    {
        $datos = $this->validarLote($request);

        if ($datos['cantidad_inicial'] < $lote->total_retirado) {
            return back()->withInput()->with('error', "La cantidad inicial no puede ser menor que las unidades ya retiradas ({$lote->total_retirado}).");
        }

        try {
            DB::transaction(function () use ($datos, $lote) {
                $diferencia = $datos['cantidad_inicial'] - $lote->cantidad_inicial;
                if ($diferencia !== 0) {
                    Producto::where('id', $lote->producto_id)->increment('stock_actual', $diferencia);
                }
                $lote->update($datos);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withInput()->with('error', $this->mensajeSql($e));
        }

        return redirect()->route('lotes.show', $lote)->with('exito', "Lote {$lote->codigo} actualizado.");
    }

    // Solo se puede eliminar si no tiene retiros ni tratamientos; revierte el stock
    public function destroy(LotePeces $lote)
    {
        if (! $lote->puedeEliminarse()) {
            return back()->with('error', "El lote {$lote->codigo} tiene retiros o tratamientos registrados y no puede eliminarse.");
        }

        $disponibles = $lote->disponibles();
        $producto = $lote->producto;

        if ($producto->stock_actual < $disponibles) {
            return back()->with('error', 'No se puede eliminar: parte del stock del lote ya fue vendido.');
        }

        DB::transaction(function () use ($lote, $producto, $disponibles) {
            $producto->decrement('stock_actual', $disponibles);
            $lote->delete();
        });

        return redirect()->route('lotes.index')->with('exito', "Lote {$lote->codigo} eliminado y stock revertido.");
    }

    public function registrarTratamiento(Request $request, LotePeces $lote)
    {
        $datos = $request->validate([
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'sintomas' => ['nullable', 'string', 'max:1000'],
            'producto' => ['required', 'string', 'max:150'],
            'dosis' => ['nullable', 'string', 'max:100'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ], [
            'fecha_fin.after_or_equal' => 'La fecha de fin no puede ser anterior a la de inicio.',
        ]);

        $lote->tratamientos()->create($datos);
        $lote->update(['estado' => 'en_tratamiento']);

        return back()->with('exito', 'Tratamiento registrado en el lote.');
    }

    public function registrarRetiro(Request $request, LotePeces $lote)
    {
        $datos = $request->validate([
            'cantidad' => ['required', 'integer', 'min:1'],
            'observacion' => ['nullable', 'string', 'max:200'],
        ]);

        try {
            DB::transaction(function () use ($datos, $lote, $request) {
                $lote->retiros()->create([
                    'usuario_id' => $request->user()->id,
                    'fecha' => now(),
                    'cantidad' => $datos['cantidad'],
                    'observacion' => $datos['observacion'] ?? null,
                ]);

                $lote->refresh();
                if ($lote->disponibles() === 0) {
                    $lote->update(['estado' => 'agotado']);
                }
            });
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', $this->mensajeSql($e));
        }

        return back()->with('exito', 'Retiro registrado. El stock fue descontado.');
    }

    public function cambiarEstado(Request $request, LotePeces $lote)
    {
        $request->validate(['estado' => ['required', Rule::in(['activo', 'en_tratamiento', 'agotado', 'cerrado'])]]);
        $lote->update(['estado' => $request->estado]);

        return back()->with('exito', "Estado del lote actualizado a {$lote->estado}.");
    }

    private function validarLote(Request $request): array
    {
        return $request->validate([
            'producto_id' => ['required', 'exists:productos,id'],
            'proveedor_id' => ['required', 'exists:proveedores,id'],
            'fecha_recepcion' => ['required', 'date'],
            'cantidad_inicial' => ['required', 'integer', 'min:1'],
            'precio_compra' => ['nullable', 'numeric', 'min:0'],
            'ph' => ['nullable', 'numeric', 'min:0', 'max:14'],
            'temperatura' => ['nullable', 'numeric', 'min:-10', 'max:50'],
            'amoniaco' => ['nullable', 'numeric', 'min:0'],
            'nitritos' => ['nullable', 'numeric', 'min:0'],
            'dureza' => ['nullable', 'numeric', 'min:0'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ], [
            'ph.max' => 'El pH debe estar en el rango 0–14.',
        ]);
    }

    private function mensajeSql(\Illuminate\Database\QueryException $e): string
    {
        if (str_contains($e->getMessage(), '45000') && preg_match('/45000: (.+?)($| in | at )/s', $e->getMessage(), $m)) {
            return trim($m[1]);
        }

        return 'No se pudo completar la operación. Revisa los datos e inténtalo de nuevo.';
    }
}
