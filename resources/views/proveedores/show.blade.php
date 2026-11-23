@extends('layouts.app')

@section('titulo', $proveedor->codigo)
@section('miga', '<a href="'.route('inicio').'">Inicio</a> / <a href="'.route('proveedores.index').'">Proveedores</a> / <b>'.$proveedor->codigo.'</b>')

@section('contenido')
<div class="encabezado-pagina">
    <h1>{{ $proveedor->nombre }}</h1>
    <a class="btn btn-secundario" href="{{ route('proveedores.edit', $proveedor) }}">Editar</a>
</div>

<div class="grid-2">
    <div class="tarjeta">
        <h2>Datos del proveedor</h2>
        <table class="tabla">
            <tr><td style="width:40%; color:var(--texto-suave)">Código</td><td><b>{{ $proveedor->codigo }}</b></td></tr>
            <tr><td style="color:var(--texto-suave)">NIT / CI</td><td>{{ $proveedor->nit_ci ?? '—' }}</td></tr>
            <tr><td style="color:var(--texto-suave)">Teléfono</td><td>{{ $proveedor->telefono }}</td></tr>
            <tr><td style="color:var(--texto-suave)">Correo</td><td>{{ $proveedor->correo ?? '—' }}</td></tr>
            <tr><td style="color:var(--texto-suave)">Dirección</td><td>{{ $proveedor->direccion ?? '—' }}</td></tr>
            <tr><td style="color:var(--texto-suave)">Tipo</td><td><span class="badge badge-{{ $proveedor->tipo }}">{{ ucfirst($proveedor->tipo) }}</span></td></tr>
            <tr><td style="color:var(--texto-suave)">Estado</td><td><span class="badge badge-{{ $proveedor->estado }}">{{ ucfirst($proveedor->estado) }}</span></td></tr>
        </table>
    </div>

    <div class="tarjeta">
        <h2>Productos de este proveedor ({{ $proveedor->productos->count() }})</h2>
        <table class="tabla">
            <thead><tr><th>Código</th><th>Nombre</th><th>Stock</th><th>P. venta</th></tr></thead>
            <tbody>
                @forelse($proveedor->productos as $p)
                <tr>
                    <td><a href="{{ route('productos.show', $p) }}">{{ $p->codigo }}</a></td>
                    <td>{{ $p->nombre }}</td>
                    <td>{{ $p->stock_actual }}</td>
                    <td>Bs {{ number_format($p->precio_venta, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="vacio">Sin productos asociados</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="tarjeta">
    <h2>Últimos lotes recibidos</h2>
    <table class="tabla">
        <thead><tr><th>Código</th><th>Especie</th><th>Recepción</th><th>Inicial</th><th>Disponibles</th><th>Estado</th></tr></thead>
        <tbody>
            @forelse($proveedor->lotes as $l)
            <tr>
                <td><a href="{{ route('lotes.show', $l) }}">{{ $l->codigo }}</a></td>
                <td>{{ $l->producto->nombre }}</td>
                <td>{{ $l->fecha_recepcion->format('d/m/Y') }}</td>
                <td>{{ $l->cantidad_inicial }}</td>
                <td>{{ $l->disponibles() }}</td>
                <td><span class="badge badge-{{ $l->estado }}">{{ str_replace('_',' ',ucfirst($l->estado)) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="6" class="vacio">Sin lotes registrados</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
