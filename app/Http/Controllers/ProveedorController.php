<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $query = Proveedor::query();

        if ($buscar = $request->input('buscar')) {
            $query->where(fn ($q) => $q->where('nombre', 'like', "%{$buscar}%")->orWhere('codigo', 'like', "%{$buscar}%")->orWhere('nit_ci', 'like', "%{$buscar}%"));
        }
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        return view('proveedores.index', [
            'proveedores' => $query->orderBy('codigo')->paginate(10)->withQueryString(),
            'proximoCodigo' => Proveedor::proximoCodigo(),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);
        $datos['codigo'] = Proveedor::proximoCodigo();
        $datos['estado'] = 'activo';

        $proveedor = Proveedor::create($datos);

        return redirect()->route('proveedores.index')->with('exito', "Proveedor {$proveedor->codigo} registrado.");
    }

    public function show(Proveedor $proveedore)
    {
        $proveedore->load(['productos', 'lotes' => fn ($q) => $q->latest('fecha_recepcion')->take(10)]);

        return view('proveedores.show', ['proveedor' => $proveedore]);
    }

    public function edit(Proveedor $proveedore)
    {
        return view('proveedores.edit', ['proveedor' => $proveedore]);
    }

    public function update(Request $request, Proveedor $proveedore)
    {
        $proveedore->update($this->validar($request, $proveedore));

        return redirect()->route('proveedores.index')->with('exito', "Proveedor {$proveedore->codigo} actualizado.");
    }

    public function destroy(Proveedor $proveedore)
    {
        if ($proveedore->tieneDependencias()) {
            return back()->with('error', "El proveedor {$proveedore->codigo} tiene productos, entradas o lotes asociados. Solo puede desactivarse.");
        }

        $proveedore->delete();

        return back()->with('exito', 'Proveedor eliminado.');
    }

    public function cambiarEstado(Proveedor $proveedore)
    {
        $proveedore->update(['estado' => $proveedore->estado === 'activo' ? 'inactivo' : 'activo']);

        $accion = $proveedore->estado === 'activo' ? 'reactivado' : 'desactivado';

        return back()->with('exito', "Proveedor {$proveedore->codigo} {$accion}.");
    }

    private function validar(Request $request, ?Proveedor $proveedor = null): array
    {
        return $request->validate([
            'nit_ci' => ['nullable', 'string', 'max:20', Rule::unique('proveedores', 'nit_ci')->ignore($proveedor)],
            'nombre' => ['required', 'string', 'max:150'],
            'telefono' => ['required', 'string', 'max:20'],
            'correo' => ['nullable', 'email', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'tipo' => ['required', Rule::in(['fijo', 'eventual'])],
        ], [
            'nit_ci.unique' => 'Ya existe un proveedor con ese NIT/CI.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'tipo.in' => 'El tipo debe ser fijo o eventual.',
        ]);
    }
}
