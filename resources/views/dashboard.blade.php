@extends('layouts.app')

@section('titulo', 'Inicio')
@section('miga', '<b>Inicio</b>')

@section('contenido')
<div class="encabezado-pagina">
    <h1>Bienvenido, {{ auth()->user()->nombre }}</h1>
</div>

<div class="stats">
    <div class="stat">
        <div class="etiqueta">Ventas de hoy</div>
        <div class="valor">Bs {{ number_format($ventasHoyTotal, 2) }}</div>
        <div class="detalle">{{ $ventasHoyCount }} ventas registradas</div>
    </div>
    <div class="stat alerta">
        <div class="etiqueta">Stock crítico</div>
        <div class="valor">{{ $stockCriticoCount }} productos</div>
        <div class="detalle">Requieren reposición</div>
    </div>
    <div class="stat">
        <div class="etiqueta">Lotes activos</div>
        <div class="valor">{{ $lotesActivos }} lotes</div>
        <div class="detalle">{{ $lotesEnTratamiento }} en tratamiento</div>
    </div>
    <div class="stat">
        <div class="etiqueta">Productos</div>
        <div class="valor">{{ $productosCount }}</div>
        <div class="detalle">En {{ $categoriasCount }} categorías</div>
    </div>
</div>

<div class="grid-2">
    <div class="tarjeta">
        <h2>⚠️ Alerta de stock mínimo</h2>
        <table class="tabla">
            <thead>
                <tr><th>Producto</th><th>Stock</th><th>Mínimo</th></tr>
            </thead>
            <tbody>
                @forelse($stockCriticoTop as $p)
                <tr>
                    <td>{{ $p->nombre }}</td>
                    <td><span class="badge badge-stock">{{ $p->stock_actual }}</span></td>
                    <td>{{ $p->stock_minimo }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="vacio">Sin alertas de stock</td></tr>
                @endforelse
            </tbody>
        </table>
        <p style="margin-top:12px;"><a href="{{ route('productos.index', ['stock_critico' => 1]) }}">Ver todo el stock crítico →</a></p>
    </div>

    <div class="tarjeta">
        <h2>Últimas ventas</h2>
        <table class="tabla">
            <thead>
                <tr><th>Código</th><th>Cliente</th><th>Pago</th><th style="text-align:right">Total</th></tr>
            </thead>
            <tbody>
                @forelse($ultimasVentas as $v)
                <tr>
                    <td><a href="{{ route('ventas.show', $v) }}">{{ $v->codigo }}</a></td>
                    <td>{{ $v->cliente->nombre ?? 'Cliente general' }}</td>
                    <td>{{ $v->forma_pago === 'qr' ? 'QR' : 'Efectivo' }}</td>
                    <td style="text-align:right">Bs {{ number_format($v->total, 0) }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="vacio">Sin ventas registradas</td></tr>
                @endforelse
            </tbody>
        </table>
        <p style="margin-top:12px;"><a href="{{ route('ventas.index') }}">Ir a ventas →</a></p>
    </div>
</div>

<div class="tarjeta">
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a class="btn btn-primario" href="{{ route('ventas.create') }}">＋ Nueva venta</a>
        @if(auth()->user()->esAdministrador())
        <a class="btn btn-secundario" href="{{ route('lotes.create') }}">＋ Registrar lote</a>
        <a class="btn btn-secundario" href="{{ route('entradas.create') }}">＋ Nueva entrada</a>
        @endif
    </div>
</div>
@endsection
