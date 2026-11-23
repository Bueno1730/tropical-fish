@extends('layouts.app')

@section('titulo', $producto->codigo)
@section('miga', '<a href="'.route('inicio').'">Inicio</a> / <a href="'.route('productos.index').'">Productos</a> / <b>'.$producto->codigo.'</b>')

@section('contenido')
<div class="encabezado-pagina">
    <h1>{{ $producto->nombre }} <span class="badge badge-{{ $producto->estado }}">{{ ucfirst($producto->estado) }}</span></h1>
    @if(auth()->user()->esAdministrador())
    <a class="btn btn-secundario" href="{{ route('productos.edit', $producto) }}">Editar</a>
    @endif
</div>

<div class="grid-2">
    <div class="tarjeta">
        <h2>Datos del producto</h2>
        <table class="tabla">
            <tr><td style="width:40%; color:var(--texto-suave)">Código</td><td><b>{{ $producto->codigo }}</b></td></tr>
            <tr><td style="color:var(--texto-suave)">Categoría</td><td>{{ $producto->categoria->nombre }}{{ $producto->categoria->maneja_lotes ? ' (se maneja por lotes)' : '' }}</td></tr>
            <tr><td style="color:var(--texto-suave)">Proveedor</td><td>{{ $producto->proveedor ? $producto->proveedor->codigo.' '.$producto->proveedor->nombre : '—' }}</td></tr>
            <tr><td style="color:var(--texto-suave)">Precio de venta</td><td>Bs {{ number_format($producto->precio_venta, 2) }}</td></tr>
            <tr><td style="color:var(--texto-suave)">Precio de compra</td><td>Bs {{ number_format($producto->precio_compra, 2) }}</td></tr>
            <tr><td style="color:var(--texto-suave)">Stock actual</td><td><b>{{ $producto->stock_actual }}</b> {{ $producto->unidad }}</td></tr>
            <tr><td style="color:var(--texto-suave)">Stock mínimo</td><td>{{ $producto->stock_minimo }}</td></tr>
            @if($producto->control_especial)
            <tr><td style="color:var(--texto-suave)">Control especial</td><td><span class="badge badge-control">{{ $producto->nota_control }}</span></td></tr>
            <tr><td style="color:var(--texto-suave)">Vencimiento</td><td>{{ $producto->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</td></tr>
            @endif
        </table>
    </div>

    @if($producto->esPez())
    <div class="tarjeta">
        <h2>Lotes registrados</h2>
        <table class="tabla">
            <thead><tr><th>Código</th><th>Recepción</th><th>Inicial</th><th>Retiradas</th><th>Disponibles</th><th>Estado</th></tr></thead>
            <tbody>
                @forelse($producto->lotes as $l)
                <tr>
                    <td><a href="{{ route('lotes.show', $l) }}">{{ $l->codigo }}</a></td>
                    <td>{{ $l->fecha_recepcion->format('d/m/Y') }}</td>
                    <td>{{ $l->cantidad_inicial }}</td>
                    <td>{{ $l->total_retirado }}</td>
                    <td>{{ $l->disponibles() }}</td>
                    <td><span class="badge badge-{{ $l->estado }}">{{ str_replace('_',' ',ucfirst($l->estado)) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="6" class="vacio">Sin lotes registrados</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
