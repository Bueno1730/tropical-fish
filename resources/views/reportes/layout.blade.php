@extends('layouts.app')

@section('contenido')
<div class="encabezado-pagina no-print">
    <h1>@yield('titulo-reporte')</h1>
    <div style="display:flex; gap:8px;">
        <a class="btn btn-secundario" href="{{ route('reportes.index') }}">Volver</a>
        <a class="btn btn-secundario" href="{{ request()->fullUrlWithQuery(['exportar' => 1]) }}">Exportar CSV</a>
        <button class="btn btn-secundario" onclick="window.print()">Imprimir</button>
    </div>
</div>

<div class="tarjeta">
    <div style="display:none;" class="solo-print">
        <h2>🐠 Tropical Fish Acuario — @yield('titulo-reporte')</h2>
        <p>Generado: {{ now()->format('d/m/Y H:i') }} por {{ auth()->user()->usuario }}</p>
    </div>
    @yield('filtros')
    <div class="tabla-responsive">
        @yield('tabla')
    </div>
    <p class="ayuda" style="margin-top:12px;">@yield('nota')</p>
</div>

<style>
@media print { .solo-print { display: block !important; margin-bottom: 16px; } }
</style>
@endsection
