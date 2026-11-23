@extends('layouts.app')

@section('titulo', 'Entradas de mercadería')
@section('miga', '<a href="'.route('inicio').'">Inicio</a> / <b>Entradas</b>')

@section('contenido')
<div class="encabezado-pagina">
    <h1>Entradas de mercadería</h1>
    <a class="btn btn-primario" href="{{ route('entradas.create') }}">＋ Nueva entrada</a>
</div>

<div class="tarjeta">
    <form method="GET" action="{{ route('entradas.index') }}" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <label>Desde <input type="date" name="desde" value="{{ request('desde') }}" style="padding:7px 10px; border:1px solid var(--borde); border-radius:7px;"></label>
        <label>Hasta <input type="date" name="hasta" value="{{ request('hasta') }}" style="padding:7px 10px; border:1px solid var(--borde); border-radius:7px;"></label>
        <select name="proveedor" style="padding:9px 12px; border:1px solid var(--borde); border-radius:7px;">
            <option value="">Todos los proveedores</option>
            @foreach($proveedores as $p)
            <option value="{{ $p->id }}" @selected(request('proveedor') == $p->id)>{{ $p->codigo }} {{ $p->nombre }}</option>
            @endforeach
        </select>
        <button class="btn btn-secundario" type="submit">Filtrar</button>
    </form>
</div>

<div class="tarjeta tabla-responsive">
    <table class="tabla">
        <thead>
            <tr>
                <th>Código</th><th>Fecha</th><th>Proveedor</th><th>N° documento</th>
                <th>Registrado por</th><th>Ítems</th><th>Total</th><th>Estado</th><th class="acciones">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($entradas as $e)
            <tr>
                <td><a href="{{ route('entradas.show', $e) }}"><b>{{ $e->codigo }}</b></a></td>
                <td>{{ $e->fecha->format('d/m/Y H:i') }}</td>
                <td>{{ $e->proveedor->codigo }} {{ $e->proveedor->nombre }}</td>
                <td>{{ $e->nro_documento ?? '—' }}</td>
                <td>{{ $e->usuario->codigo }} {{ $e->usuario->usuario }}</td>
                <td>{{ $e->items_count }}</td>
                <td>Bs {{ number_format($e->total, 0) }}</td>
                <td><span class="badge badge-{{ $e->estado }}">{{ ucfirst($e->estado) }}</span></td>
                <td class="acciones">
                    <a class="btn btn-sm btn-secundario" href="{{ route('entradas.show', $e) }}">Ver detalle</a>
                    @if($e->estaRegistrada())
                    <a class="btn btn-sm btn-secundario" href="{{ route('entradas.edit', $e) }}">Editar</a>
                    <form class="acciones-form" method="POST" action="{{ route('entradas.anular', $e) }}" data-confirm="¿Anular la entrada {{ $e->codigo }}? El stock se revertirá.">
                        @csrf
                        <button class="btn btn-sm btn-peligro">Anular</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="vacio">No hay entradas en el periodo seleccionado</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="paginacion">{{ $entradas->links() }}</div>
    <p style="margin-top:14px; font-size:12px; color:var(--texto-suave);">
        Cada entrada suma automáticamente las cantidades al stock. Los peces se registran como lote.
    </p>
</div>
@endsection
