@extends('reportes.layout')

@section('titulo', 'Control especial')
@section('miga', '<a href="'.route('inicio').'">Inicio</a> / <a href="'.route('reportes.index').'">Reportes</a> / <b>Control especial</b>')
@section('titulo-reporte', 'Productos con control especial')
@section('nota', 'Productos marcados por normativa SENASAG, ordenados por fecha de vencimiento más próxima.')

@section('tabla')
<table class="tabla">
    <thead><tr><th>Código</th><th>Producto</th><th>Categoría</th><th>Stock</th><th>Nota de control</th><th>Vencimiento</th></tr></thead>
    <tbody>
        @forelse($filas as $f)
        <tr>
            <td><b>{{ $f->codigo }}</b></td>
            <td>{{ $f->nombre }}</td>
            <td>{{ $f->categoria }}</td>
            <td>{{ $f->stock_actual }}</td>
            <td><span class="badge badge-control">{{ $f->nota_control }}</span></td>
            <td>{{ $f->fecha_vencimiento ? \Carbon\Carbon::parse($f->fecha_vencimiento)->format('d/m/Y') : '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="vacio">Sin productos con control especial</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
