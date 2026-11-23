@extends('layouts.app')

@section('titulo', 'Registrar lote')
@section('miga', '<a href="'.route('inicio').'">Inicio</a> / <a href="'.route('lotes.index').'">Lotes</a> / <b>Nuevo</b>')

@section('contenido')
<div class="tarjeta" style="max-width:760px;">
    <h2>Registrar lote de peces <span style="color:var(--texto-suave); font-size:13px; font-weight:400;">{{ $proximoCodigo }} (auto)</span></h2>
    <form method="POST" action="{{ route('lotes.store') }}">
        @csrf
        <div class="fila">
            <div class="campo">
                <label for="producto_id">Especie (producto pez) *</label>
                <select id="producto_id" name="producto_id" required>
                    <option value="">— Selecciona —</option>
                    @foreach($peces as $p)
                    <option value="{{ $p->id }}" @selected(old('producto_id') == $p->id)>{{ $p->codigo }} {{ $p->nombre }}</option>
                    @endforeach
                </select>
                @if($peces->isEmpty())<p class="ayuda">No hay productos de categoría con manejo por lotes. Registra primero el pez en Productos.</p>@endif
            </div>
            <div class="campo">
                <label for="proveedor_id">Proveedor *</label>
                <select id="proveedor_id" name="proveedor_id" required>
                    <option value="">— Selecciona —</option>
                    @foreach($proveedores as $p)
                    <option value="{{ $p->id }}" @selected(old('proveedor_id') == $p->id)>{{ $p->codigo }} {{ $p->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="fila">
            <div class="campo">
                <label for="fecha_recepcion">Fecha de recepción *</label>
                <input type="date" id="fecha_recepcion" name="fecha_recepcion" value="{{ old('fecha_recepcion', now()->toDateString()) }}" required>
            </div>
            <div class="campo">
                <label for="cantidad_inicial">Cantidad recibida *</label>
                <input type="number" min="1" id="cantidad_inicial" name="cantidad_inicial" value="{{ old('cantidad_inicial') }}" required>
            </div>
            <div class="campo">
                <label for="precio_compra">Precio de compra unitario (Bs)</label>
                <input type="number" step="0.01" min="0" id="precio_compra" name="precio_compra" value="{{ old('precio_compra') }}">
            </div>
        </div>

        <h3 style="font-size:14px; margin:16px 0 10px;">Condiciones del agua de llegada</h3>
        <div class="fila">
            <div class="campo">
                <label for="ph">pH (0–14)</label>
                <input type="number" step="0.1" min="0" max="14" id="ph" name="ph" value="{{ old('ph') }}" placeholder="Ej: 7.0">
            </div>
            <div class="campo">
                <label for="temperatura">Temperatura (°C)</label>
                <input type="number" step="0.1" id="temperatura" name="temperatura" value="{{ old('temperatura') }}" placeholder="Ej: 25.0">
            </div>
            <div class="campo">
                <label for="amoniaco">Amoníaco (ppm)</label>
                <input type="number" step="0.01" min="0" id="amoniaco" name="amoniaco" value="{{ old('amoniaco') }}">
            </div>
            <div class="campo">
                <label for="nitritos">Nitritos (ppm)</label>
                <input type="number" step="0.01" min="0" id="nitritos" name="nitritos" value="{{ old('nitritos') }}">
            </div>
            <div class="campo">
                <label for="dureza">Dureza (°GH)</label>
                <input type="number" step="0.1" min="0" id="dureza" name="dureza" value="{{ old('dureza') }}">
            </div>
        </div>
        <div class="campo">
            <label for="observaciones">Observaciones</label>
            <textarea id="observaciones" name="observaciones" rows="2">{{ old('observaciones') }}</textarea>
        </div>
        <div style="display:flex; gap:10px;">
            <button class="btn btn-primario" type="submit">Registrar lote</button>
            <a class="btn btn-secundario" href="{{ route('lotes.index') }}">Cancelar</a>
        </div>
        <p class="ayuda" style="margin-top:10px;">La cantidad recibida se sumará automáticamente al stock del producto.</p>
    </form>
</div>
@endsection
