@extends('layouts.app')

@section('titulo', 'Lotes de peces')
@section('miga', '<a href="'.route('inicio').'">Inicio</a> / <b>Lotes de peces</b>')

@section('contenido')
<div class="encabezado-pagina">
    <h1>Lotes de peces</h1>
    @if(auth()->user()->esAdministrador())
    <a class="btn btn-primario" href="{{ route('lotes.create') }}">＋ Registrar lote</a>
    @endif
</div>

<div class="tarjeta">
    <form method="GET" action="{{ route('lotes.index') }}" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por código de lote o especie" style="flex:2; min-width:180px; padding:9px 12px; border:1px solid var(--borde); border-radius:7px;">
        <select name="proveedor" style="padding:9px 12px; border:1px solid var(--borde); border-radius:7px;">
            <option value="">Todos los proveedores</option>
            @foreach($proveedores as $p)
            <option value="{{ $p->id }}" @selected(request('proveedor') == $p->id)>{{ $p->codigo }} {{ $p->nombre }}</option>
            @endforeach
        </select>
        <select name="estado" style="padding:9px 12px; border:1px solid var(--borde); border-radius:7px;">
            <option value="">Todos</option>
            <option value="activo" @selected(request('estado') === 'activo')>Activos</option>
            <option value="en_tratamiento" @selected(request('estado') === 'en_tratamiento')>En tratamiento</option>
            <option value="agotado" @selected(request('estado') === 'agotado')>Agotados</option>
            <option value="cerrado" @selected(request('estado') === 'cerrado')>Cerrados</option>
        </select>
        <button class="btn btn-secundario" type="submit">Filtrar</button>
    </form>
</div>

<div class="tarjeta tabla-responsive">
    <table class="tabla">
        <thead>
            <tr>
                <th>Código</th><th>Especie</th><th>Proveedor</th><th>Recepción</th>
                <th>Inicial</th><th>Retiradas</th><th>Disponibles</th><th>pH</th><th>Estado</th><th class="acciones">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lotes as $l)
            <tr>
                <td><a href="{{ route('lotes.show', $l) }}"><b>{{ $l->codigo }}</b></a></td>
                <td>{{ $l->producto->codigo }} {{ $l->producto->nombre }}</td>
                <td>{{ $l->proveedor ? $l->proveedor->codigo.' '.$l->proveedor->nombre : '—' }}</td>
                <td>{{ $l->fecha_recepcion->format('d/m/Y') }}</td>
                <td>{{ $l->cantidad_inicial }}</td>
                <td>{{ $l->total_retirado }}</td>
                <td><b>{{ $l->disponibles() }}</b></td>
                <td>{{ $l->ph ?? '—' }}</td>
                <td><span class="badge badge-{{ $l->estado }}">{{ str_replace('_',' ',ucfirst($l->estado)) }}</span></td>
                <td class="acciones">
                    <a class="btn btn-sm btn-secundario" href="{{ route('lotes.show', $l) }}">Ver detalle</a>
                    @if(auth()->user()->esAdministrador())
                    <a class="btn btn-sm btn-secundario" href="{{ route('lotes.edit', $l) }}">Editar</a>
                    <form class="acciones-form" method="POST" action="{{ route('lotes.destroy', $l) }}" data-confirm="¿Eliminar el lote {{ $l->codigo }}? Solo es posible si no tiene bajas ni tratamientos.">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-peligro">Eliminar</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="10" class="vacio">No se encontraron lotes</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="paginacion">{{ $lotes->links() }}</div>
    <p style="margin-top:14px; font-size:12px; color:var(--texto-suave);">
        La cantidad inicial se suma al stock; las bajas se descuentan automáticamente. El código L-AAAA-### se genera solo.
    </p>
</div>
@endsection
