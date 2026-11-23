@extends('layouts.app')

@section('titulo', 'Productos')
@section('miga', '<a href="'.route('inicio').'">Inicio</a> / <b>Productos</b>')

@section('contenido')
<div class="encabezado-pagina">
    <h1>Productos</h1>
    @if(auth()->user()->esAdministrador())
    <div style="display:flex; gap:8px;">
        <a class="btn btn-secundario" href="{{ route('importaciones.index') }}">Importar CSV</a>
        <a class="btn btn-primario" href="{{ route('productos.create') }}">＋ Nuevo producto</a>
    </div>
    @endif
</div>

<div class="tarjeta">
    <form method="GET" action="{{ route('productos.index') }}" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por código o nombre" style="flex:2; min-width:180px; padding:9px 12px; border:1px solid var(--borde); border-radius:7px;">
        <select name="categoria" style="padding:9px 12px; border:1px solid var(--borde); border-radius:7px;">
            <option value="">Todas las categorías</option>
            @foreach($categorias as $c)
            <option value="{{ $c->id }}" @selected(request('categoria') == $c->id)>{{ $c->nombre }}</option>
            @endforeach
        </select>
        <select name="proveedor" style="padding:9px 12px; border:1px solid var(--borde); border-radius:7px;">
            <option value="">Todos los proveedores</option>
            @foreach($proveedores as $p)
            <option value="{{ $p->id }}" @selected(request('proveedor') == $p->id)>{{ $p->codigo }} {{ $p->nombre }}</option>
            @endforeach
        </select>
        <label class="campo-check"><input type="checkbox" name="stock_critico" value="1" @checked(request()->boolean('stock_critico'))> Solo stock crítico</label>
        <button class="btn btn-secundario" type="submit">Filtrar</button>
    </form>
</div>

<div class="tarjeta tabla-responsive">
    <table class="tabla">
        <thead>
            <tr>
                <th>Código</th><th>Nombre</th><th>Categoría</th><th>Proveedor</th>
                <th>P. venta</th><th>Stock</th><th>Mín.</th><th>Estado</th>
                @if(auth()->user()->esAdministrador())<th class="acciones">Acciones</th>@endif
            </tr>
        </thead>
        <tbody>
            @forelse($productos as $p)
            <tr>
                <td><a href="{{ route('productos.show', $p) }}"><b>{{ $p->codigo }}</b></a></td>
                <td>
                    {{ $p->nombre }}
                    @if($p->control_especial)<span class="badge badge-control" title="{{ $p->nota_control }}">Control especial</span>@endif
                </td>
                <td>{{ $p->categoria->nombre }}</td>
                <td>{{ $p->proveedor ? $p->proveedor->codigo.' '.$p->proveedor->nombre : '—' }}</td>
                <td>Bs {{ number_format($p->precio_venta, 2) }}</td>
                <td>
                    @if($p->stockCritico() && $p->estado === 'activo')
                        <span class="badge badge-stock">{{ $p->stock_actual }} ⚠</span>
                    @else
                        {{ $p->stock_actual }}
                    @endif
                </td>
                <td>{{ $p->stock_minimo }}</td>
                <td><span class="badge badge-{{ $p->estado }}">{{ ucfirst($p->estado) }}</span></td>
                @if(auth()->user()->esAdministrador())
                <td class="acciones">
                    <a class="btn btn-sm btn-secundario" href="{{ route('productos.show', $p) }}">Ver</a>
                    <a class="btn btn-sm btn-secundario" href="{{ route('productos.edit', $p) }}">Editar</a>
                    <form class="acciones-form" method="POST" action="{{ route('productos.estado', $p) }}">
                        @csrf
                        <button class="btn btn-sm btn-secundario">{{ $p->estado === 'activo' ? 'Desactivar' : 'Reactivar' }}</button>
                    </form>
                    <form class="acciones-form" method="POST" action="{{ route('productos.destroy', $p) }}" data-confirm="¿Eliminar el producto {{ $p->codigo }}? Solo es posible si no tiene registros asociados.">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-peligro">Eliminar</button>
                    </form>
                </td>
                @endif
            </tr>
            @empty
            <tr><td colspan="9" class="vacio">No se encontraron productos</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="paginacion">{{ $productos->links() }}</div>
    <p style="margin-top:14px; font-size:12px; color:var(--texto-suave);">
        <span class="badge badge-stock">n</span> Stock bajo el mínimo &nbsp;·&nbsp;
        <span class="badge badge-control">Control especial</span> Producto regulado por normativa
    </p>
</div>
@endsection
