@extends('reportes.layout')

@section('titulo', 'Stock crítico')
@section('miga', '<a href="'.route('inicio').'">Inicio</a> / <a href="'.route('reportes.index').'">Reportes</a> / <b>Stock crítico</b>')
@section('titulo-reporte', 'Stock crítico — productos que requieren reposición')
@section('nota', 'Productos activos cuyo stock es igual o menor al mínimo definido, con el proveedor para gestionar la reposición.')

@section('tabla')
<table class="tabla">
    <thead><tr><th>Código</th><th>Producto</th><th>Stock</th><th>Mínimo</th><th>Proveedor</th><th>Teléfono</th></tr></thead>
    <tbody>
        @forelse($filas as $f)
        <tr>
            <td><b>{{ $f->codigo }}</b></td>
            <td>{{ $f->nombre }}</td>
            <td><span class="badge badge-stock">{{ $f->stock_actual }}</span></td>
            <td>{{ $f->stock_minimo }}</td>
            <td>{{ $f->proveedor ? $f->proveedor_codigo.' '.$f->proveedor : '—' }}</td>
            <td>{{ $f->telefono ?? '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="vacio">No hay productos en stock crítico</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
