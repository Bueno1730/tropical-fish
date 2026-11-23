@extends('layouts.app')

@section('titulo', 'Reportes')
@section('miga', '<a href="'.route('inicio').'">Inicio</a> / <b>Reportes</b>')

@section('contenido')
<div class="encabezado-pagina"><h1>Reportes</h1></div>

<div class="grid-reportes">
    <div class="reporte-card">
        <h3>Productos más vendidos</h3>
        <p>Ranking por unidades y monto vendido.</p>
        <a class="btn btn-secundario" href="{{ route('reportes.mas-vendidos') }}">Ver reporte →</a>
    </div>
    <div class="reporte-card">
        <h3>Stock crítico</h3>
        <p>Productos que requieren reposición.</p>
        <a class="btn btn-secundario" href="{{ route('reportes.stock-critico') }}">Ver reporte →</a>
    </div>
    <div class="reporte-card">
        <h3>Historial de ventas</h3>
        <p>Ventas por día, semana o mes.</p>
        <a class="btn btn-secundario" href="{{ route('reportes.historial-ventas') }}">Ver reporte →</a>
    </div>
    <div class="reporte-card">
        <h3>Unidades retiradas por lote</h3>
        <p>Total de bajas de cada lote desde su ingreso.</p>
        <a class="btn btn-secundario" href="{{ route('reportes.retiros-lote') }}">Ver reporte →</a>
    </div>
    <div class="reporte-card">
        <h3>Productos con control especial</h3>
        <p>Medicamentos y reactivos regulados.</p>
        <a class="btn btn-secundario" href="{{ route('reportes.control-especial') }}">Ver reporte →</a>
    </div>
</div>
@endsection
