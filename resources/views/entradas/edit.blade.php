@extends('layouts.app')

@section('titulo', 'Editar entrada')
@section('miga', '<a href="'.route('inicio').'">Inicio</a> / <a href="'.route('entradas.index').'">Entradas</a> / <b>Editar '.$entrada->codigo.'</b>')

@section('contenido')
<div class="tarjeta">
    <h2>Editar entrada {{ $entrada->codigo }}</h2>
    <div class="alerta alerta-info">Al guardar se revierte el stock de las líneas anteriores y se aplica el nuevo detalle.</div>
    <form method="POST" action="{{ route('entradas.update', $entrada) }}" id="form-entrada">
        @csrf @method('PUT')
        <div class="fila">
            <div class="campo">
                <label for="fecha">Fecha *</label>
                <input type="datetime-local" id="fecha" name="fecha" value="{{ old('fecha', $entrada->fecha->format('Y-m-d\TH:i')) }}" required>
            </div>
            <div class="campo">
                <label for="proveedor_id">Proveedor *</label>
                <select id="proveedor_id" name="proveedor_id" required>
                    @foreach($proveedores as $p)
                    <option value="{{ $p->id }}" @selected(old('proveedor_id', $entrada->proveedor_id) == $p->id)>{{ $p->codigo }} {{ $p->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="campo">
                <label for="nro_documento">N° documento</label>
                <input type="text" id="nro_documento" name="nro_documento" value="{{ old('nro_documento', $entrada->nro_documento) }}">
            </div>
        </div>

        <h3 style="font-size:14px; margin:14px 0 10px;">Productos de la entrada</h3>
        <table class="tabla" id="tabla-items">
            <thead>
                <tr><th style="width:45%">Producto</th><th>Cantidad</th><th>P. compra (Bs)</th><th>Subtotal</th><th></th></tr>
            </thead>
            <tbody></tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align:right; font-weight:600;">Total</td>
                    <td id="total-entrada" style="font-weight:700;">Bs 0.00</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        <button type="button" class="btn btn-secundario btn-sm" id="agregar-item" style="margin-top:10px;">＋ Agregar producto</button>

        <div style="display:flex; gap:10px; margin-top:20px;">
            <button class="btn btn-primario" type="submit">Guardar cambios</button>
            <a class="btn btn-secundario" href="{{ route('entradas.show', $entrada) }}">Cancelar</a>
        </div>
    </form>
</div>

<template id="tpl-item">
    <tr>
        <td>
            <select name="items[IDX][producto_id]" class="item-producto" required>
                <option value="">— Selecciona —</option>
                @foreach($productos as $p)
                <option value="{{ $p->id }}">{{ $p->codigo }} {{ $p->nombre }}</option>
                @endforeach
            </select>
        </td>
        <td><input type="number" name="items[IDX][cantidad]" class="item-cantidad" min="1" value="1" required style="width:80px;"></td>
        <td><input type="number" name="items[IDX][precio_compra]" class="item-precio" min="0" step="0.01" value="0" required style="width:110px;"></td>
        <td class="item-subtotal">Bs 0.00</td>
        <td><button type="button" class="btn btn-sm btn-peligro quitar-item">✕</button></td>
    </tr>
</template>

@php
$itemsPrevios = $entrada->detalles->map(fn($d) => ['producto_id' => $d->producto_id, 'cantidad' => $d->cantidad, 'precio_compra' => (float) $d->precio_compra])->values();
@endphp
@push('scripts')
<script>inicializarLineas('tabla-items', 'tpl-item', 'agregar-item', 'total-entrada', @json($itemsPrevios));</script>
@endpush
@endsection
