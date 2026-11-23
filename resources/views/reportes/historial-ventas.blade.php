@extends('reportes.layout')

@section('titulo', 'Historial de ventas')
@section('miga', '<a href="'.route('inicio').'">Inicio</a> / <a href="'.route('reportes.index').'">Reportes</a> / <b>Historial de ventas</b>')
@section('titulo-reporte', 'Historial de ventas')
@section('nota', 'Ventas por periodo con cliente, vendedor, forma de pago y canal. Total del periodo al final.')

@section('filtros')
<form method="GET" class="no-print" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-bottom:14px;">
    <label>Desde <input type="date" name="desde" value="{{ request('desde') }}" style="padding:7px 10px; border:1px solid var(--borde); border-radius:7px;"></label>
    <label>Hasta <input type="date" name="hasta" value="{{ request('hasta') }}" style="padding:7px 10px; border:1px solid var(--borde); border-radius:7px;"></label>
    <select name="forma_pago" style="padding:8px 10px; border:1px solid var(--borde); border-radius:7px;">
        <option value="">Forma de pago</option>
        <option value="efectivo" @selected(request('forma_pago') === 'efectivo')>Efectivo</option>
        <option value="qr" @selected(request('forma_pago') === 'qr')>QR</option>
    </select>
    <select name="canal" style="padding:8px 10px; border:1px solid var(--borde); border-radius:7px;">
        <option value="">Canal</option>
        @foreach(['presencial','whatsapp','facebook','tiktok','instagram','otro'] as $c)
        <option value="{{ $c }}" @selected(request('canal') === $c)>{{ ucfirst($c) }}</option>
        @endforeach
    </select>
    <button class="btn btn-secundario btn-sm" type="submit">Filtrar</button>
</form>
@endsection

@section('tabla')
<table class="tabla">
    <thead><tr><th>Código</th><th>Fecha</th><th>Cliente</th><th>Vendedor</th><th>Pago</th><th>Canal</th><th>Unidades</th><th style="text-align:right">Total</th><th>Estado</th></tr></thead>
    <tbody>
        @forelse($filas as $f)
        <tr>
            <td><b>{{ $f->codigo }}</b></td>
            <td>{{ \Carbon\Carbon::parse($f->fecha)->format('d/m/Y H:i') }}</td>
            <td>{{ $f->cliente }}</td>
            <td>{{ $f->vendedor }}</td>
            <td>{{ $f->forma_pago === 'qr' ? 'QR' : 'Efectivo' }}</td>
            <td>{{ ucfirst($f->canal) }}</td>
            <td>{{ $f->unidades }}</td>
            <td style="text-align:right">Bs {{ number_format($f->total, 2) }}</td>
            <td><span class="badge badge-{{ $f->estado }}">{{ ucfirst($f->estado) }}</span></td>
        </tr>
        @empty
        <tr><td colspan="9" class="vacio">No existen ventas en el periodo</td></tr>
        @endforelse
    </tbody>
    @if($filas->count())
    <tfoot>
        <tr><td colspan="7" style="text-align:right; font-weight:600;">Total del periodo</td><td style="text-align:right; font-weight:700;">Bs {{ number_format($total, 2) }}</td><td></td></tr>
    </tfoot>
    @endif
</table>
@endsection
