@extends('layouts.app')

@section('titulo', $entrada->codigo)
@section('miga', '<a href="'.route('inicio').'">Inicio</a> / <a href="'.route('entradas.index').'">Entradas</a> / <b>'.$entrada->codigo.'</b>')

@section('contenido')
<div class="encabezado-pagina">
    <h1>Entrada {{ $entrada->codigo }} <span class="badge badge-{{ $entrada->estado }}">{{ ucfirst($entrada->estado) }}</span></h1>
    <div style="display:flex; gap:8px;">
        <a class="btn btn-secundario" href="{{ route('entradas.index') }}">Volver al historial</a>
        @if($entrada->estaRegistrada())
        <a class="btn btn-secundario" href="{{ route('entradas.edit', $entrada) }}">Editar</a>
        <form class="acciones-form" method="POST" action="{{ route('entradas.anular', $entrada) }}" data-confirm="¿Anular la entrada {{ $entrada->codigo }}? El stock se revertirá.">
            @csrf
            <button class="btn btn-peligro">Anular</button>
        </form>
        @endif
    </div>
</div>

<div class="tarjeta comprobante">
    <div class="datos-comp">
        <div><b>Fecha:</b> {{ $entrada->fecha->format('d/m/Y H:i') }}</div>
        <div><b>Proveedor:</b> {{ $entrada->proveedor->codigo }} — {{ $entrada->proveedor->nombre }}</div>
        <div><b>N° documento:</b> {{ $entrada->nro_documento ?? '—' }}</div>
        <div><b>Registrado por:</b> {{ $entrada->usuario->usuario }}</div>
    </div>
    <table class="tabla">
        <thead><tr><th>Producto</th><th>Cantidad</th><th>P. compra</th><th style="text-align:right">Subtotal</th></tr></thead>
        <tbody>
            @foreach($entrada->detalles as $d)
            <tr>
                <td>{{ $d->producto->codigo }} {{ $d->producto->nombre }}</td>
                <td>{{ $d->cantidad }}</td>
                <td>Bs {{ number_format($d->precio_compra, 2) }}</td>
                <td style="text-align:right">Bs {{ number_format($d->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="total-venta">TOTAL Bs {{ number_format($entrada->total, 2) }}</div>
</div>
@endsection
