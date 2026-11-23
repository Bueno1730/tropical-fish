@extends('layouts.app')

@section('titulo', 'Ventas')
@section('miga', '<a href="'.route('inicio').'">Inicio</a> / <b>Ventas</b>')

@section('contenido')
<div class="encabezado-pagina">
    <h1>Ventas</h1>
    <a class="btn btn-primario" href="{{ route('ventas.create') }}">＋ Nueva venta</a>
</div>

<div class="tarjeta">
    <form method="GET" action="{{ route('ventas.index') }}" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <label>Desde <input type="date" name="desde" value="{{ request('desde') }}" style="padding:7px 10px; border:1px solid var(--borde); border-radius:7px;"></label>
        <label>Hasta <input type="date" name="hasta" value="{{ request('hasta') }}" style="padding:7px 10px; border:1px solid var(--borde); border-radius:7px;"></label>
        <select name="forma_pago" style="padding:9px 12px; border:1px solid var(--borde); border-radius:7px;">
            <option value="">Forma de pago</option>
            <option value="efectivo" @selected(request('forma_pago') === 'efectivo')>Efectivo</option>
            <option value="qr" @selected(request('forma_pago') === 'qr')>QR</option>
        </select>
        <select name="canal" style="padding:9px 12px; border:1px solid var(--borde); border-radius:7px;">
            <option value="">Canal</option>
            @foreach(['presencial','whatsapp','facebook','tiktok','instagram','otro'] as $c)
            <option value="{{ $c }}" @selected(request('canal') === $c)>{{ ucfirst($c) }}</option>
            @endforeach
        </select>
        <button class="btn btn-secundario" type="submit">Filtrar</button>
    </form>
</div>

<div class="tarjeta tabla-responsive">
    <table class="tabla">
        <thead>
            <tr><th>Código</th><th>Fecha</th><th>Cliente</th><th>Vendedor</th><th>Pago</th><th>Canal</th><th style="text-align:right">Total</th><th>Estado</th><th class="acciones">Acciones</th></tr>
        </thead>
        <tbody>
            @forelse($ventas as $v)
            <tr>
                <td><a href="{{ route('ventas.show', $v) }}"><b>{{ $v->codigo }}</b></a></td>
                <td>{{ $v->fecha->format('d/m/Y H:i') }}</td>
                <td>{{ $v->cliente->nombre ?? 'Cliente general' }}</td>
                <td>{{ $v->usuario->usuario }}</td>
                <td>{{ $v->forma_pago === 'qr' ? 'QR' : 'Efectivo' }}</td>
                <td>{{ ucfirst($v->canal) }}</td>
                <td style="text-align:right">Bs {{ number_format($v->total, 2) }}</td>
                <td><span class="badge badge-{{ $v->estado }}">{{ ucfirst($v->estado) }}</span></td>
                <td class="acciones"><a class="btn btn-sm btn-secundario" href="{{ route('ventas.show', $v) }}">Ver</a></td>
            </tr>
            @empty
            <tr><td colspan="9" class="vacio">No hay ventas en el periodo seleccionado</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="paginacion">{{ $ventas->links() }}</div>
</div>
@endsection
