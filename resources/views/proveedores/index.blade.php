@extends('layouts.app')

@section('titulo', 'Proveedores')
@section('miga', '<a href="'.route('inicio').'">Inicio</a> / <b>Proveedores</b>')

@section('contenido')
<div class="encabezado-pagina">
    <h1>Proveedores</h1>
</div>

<div class="tarjeta">
    <form method="GET" action="{{ route('proveedores.index') }}" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar proveedor" style="flex:2; min-width:180px; padding:9px 12px; border:1px solid var(--borde); border-radius:7px;">
        <select name="tipo" style="padding:9px 12px; border:1px solid var(--borde); border-radius:7px;">
            <option value="">Todos</option>
            <option value="fijo" @selected(request('tipo') === 'fijo')>Fijos</option>
            <option value="eventual" @selected(request('tipo') === 'eventual')>Eventuales</option>
        </select>
        <button class="btn btn-secundario" type="submit">Filtrar</button>
    </form>
</div>

<div class="grid-2" style="grid-template-columns: 3fr 2fr;">
    <div class="tarjeta tabla-responsive">
        <table class="tabla">
            <thead>
                <tr><th>Código</th><th>NIT / CI</th><th>Nombre</th><th>Teléfono</th><th>Tipo</th><th>Estado</th><th class="acciones">Acciones</th></tr>
            </thead>
            <tbody>
                @forelse($proveedores as $p)
                <tr>
                    <td><a href="{{ route('proveedores.show', $p) }}"><b>{{ $p->codigo }}</b></a></td>
                    <td>{{ $p->nit_ci ?? '—' }}</td>
                    <td>{{ $p->nombre }}</td>
                    <td>{{ $p->telefono }}</td>
                    <td><span class="badge badge-{{ $p->tipo }}">{{ ucfirst($p->tipo) }}</span></td>
                    <td><span class="badge badge-{{ $p->estado }}">{{ ucfirst($p->estado) }}</span></td>
                    <td class="acciones">
                        <a class="btn btn-sm btn-secundario" href="{{ route('proveedores.show', $p) }}">Ver</a>
                        <a class="btn btn-sm btn-secundario" href="{{ route('proveedores.edit', $p) }}">Editar</a>
                        <form class="acciones-form" method="POST" action="{{ route('proveedores.estado', $p) }}">
                            @csrf
                            <button class="btn btn-sm btn-secundario">{{ $p->estado === 'activo' ? 'Desactivar' : 'Reactivar' }}</button>
                        </form>
                        <form class="acciones-form" method="POST" action="{{ route('proveedores.destroy', $p) }}" data-confirm="¿Eliminar el proveedor {{ $p->codigo }}?">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-peligro">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="vacio">No se encontraron proveedores</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="paginacion">{{ $proveedores->links() }}</div>
    </div>

    <div class="tarjeta">
        <h2>Nuevo proveedor <span style="color:var(--texto-suave); font-size:12px; font-weight:400;">{{ $proximoCodigo }} (auto)</span></h2>
        <form method="POST" action="{{ route('proveedores.store') }}">
            @csrf
            <div class="campo">
                <label for="nombre">Nombre *</label>
                <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" placeholder="Razón social o nombre" required>
            </div>
            <div class="campo">
                <label for="nit_ci">NIT / CI (único)</label>
                <input type="text" id="nit_ci" name="nit_ci" value="{{ old('nit_ci') }}" placeholder="Ej: 1020304050">
            </div>
            <div class="campo">
                <label for="telefono">Teléfono *</label>
                <input type="text" id="telefono" name="telefono" value="{{ old('telefono') }}" placeholder="Ej: 70712345" required>
            </div>
            <div class="campo">
                <label for="correo">Correo</label>
                <input type="email" id="correo" name="correo" value="{{ old('correo') }}" placeholder="correo@proveedor.com">
            </div>
            <div class="campo">
                <label for="direccion">Dirección</label>
                <input type="text" id="direccion" name="direccion" value="{{ old('direccion') }}" placeholder="Dirección">
            </div>
            <div class="campo">
                <label>Tipo de proveedor *</label>
                <div class="opciones-pastilla">
                    <label><input type="radio" name="tipo" value="fijo" @checked(old('tipo', 'fijo') === 'fijo')> Fijo</label>
                    <label><input type="radio" name="tipo" value="eventual" @checked(old('tipo') === 'eventual')> Eventual</label>
                </div>
            </div>
            <button class="btn btn-primario" type="submit" style="width:100%; justify-content:center;">Guardar proveedor</button>
        </form>
    </div>
</div>
@endsection
