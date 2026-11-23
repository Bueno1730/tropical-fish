@extends('layouts.app')

@section('titulo', 'Editar lote')
@section('miga', '<a href="'.route('inicio').'">Inicio</a> / <a href="'.route('lotes.index').'">Lotes</a> / <b>Editar '.$lote->codigo.'</b>')

@section('contenido')
<div class="tarjeta" style="max-width:760px;">
    <h2>Editar lote {{ $lote->codigo }}</h2>
    <div class="alerta alerta-info">Si cambias la cantidad inicial, el stock del producto se ajustará automáticamente.</div>
    <form method="POST" action="{{ route('lotes.update', $lote) }}">
        @csrf @method('PUT')
        <div class="fila">
            <div class="campo">
                <label for="producto_id">Especie *</label>
                <select id="producto_id" name="producto_id" required>
                    @foreach($peces as $p)
                    <option value="{{ $p->id }}" @selected(old('producto_id', $lote->producto_id) == $p->id)>{{ $p->codigo }} {{ $p->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="campo">
                <label for="proveedor_id">Proveedor *</label>
                <select id="proveedor_id" name="proveedor_id" required>
                    @foreach($proveedores as $p)
                    <option value="{{ $p->id }}" @selected(old('proveedor_id', $lote->proveedor_id) == $p->id)>{{ $p->codigo }} {{ $p->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="fila">
            <div class="campo">
                <label for="fecha_recepcion">Fecha de recepción *</label>
                <input type="date" id="fecha_recepcion" name="fecha_recepcion" value="{{ old('fecha_recepcion', $lote->fecha_recepcion->format('Y-m-d')) }}" required>
            </div>
            <div class="campo">
                <label for="cantidad_inicial">Cantidad recibida * (mín. {{ $lote->total_retirado }} ya retiradas)</label>
                <input type="number" min="{{ max(1, $lote->total_retirado) }}" id="cantidad_inicial" name="cantidad_inicial" value="{{ old('cantidad_inicial', $lote->cantidad_inicial) }}" required>
            </div>
            <div class="campo">
                <label for="precio_compra">Precio de compra unitario (Bs)</label>
                <input type="number" step="0.01" min="0" id="precio_compra" name="precio_compra" value="{{ old('precio_compra', $lote->precio_compra) }}">
            </div>
        </div>
        <h3 style="font-size:14px; margin:16px 0 10px;">Condiciones del agua de llegada</h3>
        <div class="fila">
            <div class="campo"><label for="ph">pH (0–14)</label><input type="number" step="0.1" min="0" max="14" id="ph" name="ph" value="{{ old('ph', $lote->ph) }}"></div>
            <div class="campo"><label for="temperatura">Temperatura (°C)</label><input type="number" step="0.1" id="temperatura" name="temperatura" value="{{ old('temperatura', $lote->temperatura) }}"></div>
            <div class="campo"><label for="amoniaco">Amoníaco (ppm)</label><input type="number" step="0.01" min="0" id="amoniaco" name="amoniaco" value="{{ old('amoniaco', $lote->amoniaco) }}"></div>
            <div class="campo"><label for="nitritos">Nitritos (ppm)</label><input type="number" step="0.01" min="0" id="nitritos" name="nitritos" value="{{ old('nitritos', $lote->nitritos) }}"></div>
            <div class="campo"><label for="dureza">Dureza (°GH)</label><input type="number" step="0.1" min="0" id="dureza" name="dureza" value="{{ old('dureza', $lote->dureza) }}"></div>
        </div>
        <div class="campo">
            <label for="observaciones">Observaciones</label>
            <textarea id="observaciones" name="observaciones" rows="2">{{ old('observaciones', $lote->observaciones) }}</textarea>
        </div>
        <div style="display:flex; gap:10px;">
            <button class="btn btn-primario" type="submit">Guardar cambios</button>
            <a class="btn btn-secundario" href="{{ route('lotes.show', $lote) }}">Cancelar</a>
        </div>
    </form>
</div>
@endsection
