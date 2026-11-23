@extends('reportes.layout')

@section('titulo', 'Productos más vendidos')
@section('miga', '<a href="'.route('inicio').'">Inicio</a> / <a href="'.route('reportes.index').'">Reportes</a> / <b>Más vendidos</b>')
@section('titulo-reporte', 'Productos más vendidos')
@section('nota', 'Ranking por unidades vendidas y monto total. Solo ventas registradas (no anuladas).')

@section('filtros')
<form method="GET" class="no-print" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-bottom:14px;">
    <label>Desde <input type="date" name="desde" value="{{ request('desde') }}" style="padding:7px 10px; border:1px solid var(--borde); border-radius:7px;"></label>
    <label>Hasta <input type="date" name="hasta" value="{{ request('hasta') }}" style="padding:7px 10px; border:1px solid var(--borde); border-radius:7px;"></label>
    <button class="btn btn-secundario btn-sm" type="submit">Filtrar</button>
</form>
@endsection

@section('tabla')
<table class="tabla">
    <thead><tr><th>#</th><th>Código</th><th>Producto</th><th>Unidades vendidas</th><th>Monto (Bs)</th><th>N° ventas</th></tr></thead>
    <tbody>
        @forelse($filas as $i => $f)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td><b>{{ $f->codigo }}</b></td>
            <td>{{ $f->nombre }}</td>
            <td>{{ $f->unidades_vendidas }}</td>
            <td>Bs {{ number_format($f->monto_total, 2) }}</td>
            <td>{{ $f->numero_ventas }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="vacio">Sin ventas en el periodo</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
