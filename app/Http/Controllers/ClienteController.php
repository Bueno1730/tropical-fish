<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::withCount('ventas');

        if ($buscar = $request->input('buscar')) {
            $query->where(fn ($q) => $q->where('nombre', 'like', "%{$buscar}%")->orWhere('ci', 'like', "%{$buscar}%")->orWhere('codigo', 'like', "%{$buscar}%"));
        }

        return view('clientes.index', [
            'clientes' => $query->orderBy('nombre')->paginate(10)->withQueryString(),
            'proximoCodigo' => Cliente::proximoCodigo(),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);
        $datos['codigo'] = Cliente::proximoCodigo();
        $datos['estado'] = 'activo';

        Cliente::create($datos);

        return back()->with('exito', 'Cliente registrado.');
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $cliente->update($this->validar($request, $cliente));

        return redirect()->route('clientes.index')->with('exito', 'Cliente actualizado.');
    }

    public function cambiarEstado(Cliente $cliente)
    {
        $cliente->update(['estado' => $cliente->estado === 'activo' ? 'inactivo' : 'activo']);

        return back()->with('exito', 'Estado del cliente actualizado.');
    }

    private function validar(Request $request, ?Cliente $cliente = null): array
    {
        return $request->validate([
            'ci' => ['nullable', 'string', 'max:20', Rule::unique('clientes', 'ci')->ignore($cliente)],
            'nombre' => ['required', 'string', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:20'],
        ], [
            'ci.unique' => 'Ya existe un cliente con ese CI.',
            'nombre.required' => 'El nombre del cliente es obligatorio.',
        ]);
    }
}
