@extends('layouts.app')

@section('titulo', 'Clientes')
@section('miga', '<a href="'.route('inicio').'">Inicio</a> / <b>Clientes</b>')

@section('contenido')
<div class="encabezado-pagina">
    <h1>Clientes</h1>
</div>

<div class="tarjeta">
    <form method="GET" action="{{ route('clientes.index') }}" style="display:flex; gap:10px; align-items:center;">
        <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por nombre, CI o código" style="flex:2; min-width:180px; padding:9px 12px; border:1px solid var(--borde); border-radius:7px;">
        <button class="btn btn-secundario" type="submit">Buscar</button>
    </form>
</div>

<div class="grid-2" style="grid-template-columns: 3fr 2fr;">
    <div class="tarjeta tabla-responsive">
        <table class="tabla">
            <thead><tr><th>Código</th><th>CI</th><th>Nombre</th><th>Teléfono</th><th>Ventas</th><th>Estado</th><th class="acciones">Acciones</th></tr></thead>
            <tbody>
                @forelse($clientes as $c)
                <tr>
                    <td><b>{{ $c->codigo }}</b></td>
                    <td>{{ $c->ci ?? '—' }}</td>
                    <td>{{ $c->nombre }}</td>
                    <td>{{ $c->telefono ?? '—' }}</td>
                    <td>{{ $c->ventas_count }}</td>
                    <td><span class="badge badge-{{ $c->estado }}">{{ ucfirst($c->estado) }}</span></td>
                    <td class="acciones">
                        <a class="btn btn-sm btn-secundario" href="{{ route('clientes.edit', $c) }}">Editar</a>
                        <form class="acciones-form" method="POST" action="{{ route('clientes.estado', $c) }}">
                            @csrf
                            <button class="btn btn-sm btn-secundario">{{ $c->estado === 'activo' ? 'Desactivar' : 'Reactivar' }}</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="vacio">Sin clientes registrados</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="paginacion">{{ $clientes->links() }}</div>
    </div>

    <div class="tarjeta">
        <h2>Nuevo cliente <span style="color:var(--texto-suave); font-size:12px; font-weight:400;">{{ $proximoCodigo }} (auto)</span></h2>
        <form method="POST" action="{{ route('clientes.store') }}">
            @csrf
            <div class="campo">
                <label for="nombre">Nombre completo *</label>
                <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" placeholder="Nombre y apellido" required>
            </div>
            <div class="campo">
                <label for="ci">CI (único)</label>
                <input type="text" id="ci" name="ci" value="{{ old('ci') }}" placeholder="Ej: 8912345">
            </div>
            <div class="campo">
                <label for="telefono">Teléfono</label>
                <input type="text" id="telefono" name="telefono" value="{{ old('telefono') }}" placeholder="Ej: 70712345">
            </div>
            <button class="btn btn-primario" type="submit" style="width:100%; justify-content:center;">Guardar cliente</button>
        </form>
    </div>
</div>
@endsection
