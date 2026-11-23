@extends('reportes.layout')

@section('titulo', 'Unidades retiradas por lote')
@section('miga', '<a href="'.route('inicio').'">Inicio</a> / <a href="'.route('reportes.index').'">Reportes</a> / <b>Retiros por lote</b>')
@section('titulo-reporte', 'Unidades retiradas por lote')
@section('nota', 'Total de bajas acumuladas de cada lote desde su ingreso, con porcentaje y número de retiros.')

@section('tabla')
<table class="tabla">
    <thead><tr><th>Lote</th><th>Especie</th><th>Recepción</th><th>Inicial</th><th>Retiradas</th><th>Disponibles</th><th>% retirado</th><th>N° retiros</th><th>Estado</th></tr></thead>
    <tbody>
        @forelse($filas as $f)
        <tr>
            <td><b>{{ $f->codigo }}</b></td>
            <td>{{ $f->especie }}</td>
            <td>{{ \Carbon\Carbon::parse($f->fecha_recepcion)->format('d/m/Y') }}</td>
            <td>{{ $f->cantidad_inicial }}</td>
            <td>{{ $f->total_retirado }}</td>
            <td>{{ $f->disponibles }}</td>
            <td>{{ $f->porcentaje_retirado }}%</td>
            <td>{{ $f->numero_retiros }}</td>
            <td><span class="badge badge-{{ $f->estado }}">{{ str_replace('_',' ',ucfirst($f->estado)) }}</span></td>
        </tr>
        @empty
        <tr><td colspan="9" class="vacio">Sin lotes registrados</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
