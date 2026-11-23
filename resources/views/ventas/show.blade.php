@extends('layouts.app')

@section('titulo', $venta->codigo)
@section('miga', '<a href="'.route('inicio').'">Inicio</a> / <a href="'.route('ventas.index').'">Ventas</a> / <b>'.$venta->codigo.'</b>')

@section('contenido')
<div class="encabezado-pagina">
    <h1>Venta {{ $venta->codigo }} <span class="badge badge-{{ $venta->estado }}">{{ ucfirst($venta->estado) }}</span></h1>
</div>

<div class="tarjeta comprobante" id="comprobante">
    <div class="cabecera-comp">
        <h2>🐠 Tropical Fish Acuario</h2>
        <p style="color:var(--texto-suave); font-size:13px;">Venta {{ $venta->codigo }}</p>
    </div>
    <div class="datos-comp">
        <div><b>Fecha:</b> {{ $venta->fecha->format('d/m/Y H:i') }}</div>
        <div><b>Cliente:</b> {{ $venta->cliente ? $venta->cliente->codigo.' — '.$venta->cliente->nombre.($venta->cliente->ci ? ' (CI '.$venta->cliente->ci.')' : '') : 'Cliente general' }}</div>
        <div><b>Vendedor:</b> {{ $venta->usuario->usuario }}</div>
        <div><b>Pago:</b> {{ $venta->forma_pago === 'qr' ? 'QR' : 'Efectivo' }} &nbsp;·&nbsp; <b>Canal:</b> {{ ucfirst($venta->canal) }}</div>
    </div>
    <table class="tabla">
        <thead><tr><th>Producto</th><th>Cantidad</th><th>Precio unitario</th><th style="text-align:right">Subtotal</th></tr></thead>
        <tbody>
            @foreach($venta->detalles as $d)
            <tr>
                <td>{{ $d->producto->nombre }}</td>
                <td>{{ $d->cantidad }}</td>
                <td>Bs {{ number_format($d->precio_unitario, 2) }}</td>
                <td style="text-align:right">Bs {{ number_format($d->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="total-venta">TOTAL Bs {{ number_format($venta->total, 2) }}</div>
</div>

<div style="display:flex; gap:8px; margin-top:16px;" class="no-print">
    <a class="btn btn-secundario" href="{{ route('ventas.index') }}">Volver al historial</a>
    <button class="btn btn-secundario" onclick="window.print()">Imprimir comprobante</button>
    @if($venta->estaRegistrada() && auth()->user()->esAdministrador())
    <form class="acciones-form" method="POST" action="{{ route('ventas.anular', $venta) }}" data-confirm="¿Anular la venta {{ $venta->codigo }}? El stock será repuesto.">
        @csrf
        <button class="btn btn-peligro">Anular</button>
    </form>
    @endif
</div>
@endsection
