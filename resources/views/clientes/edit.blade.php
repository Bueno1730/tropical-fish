@extends('layouts.app')

@section('titulo', 'Editar cliente')
@section('miga', '<a href="'.route('inicio').'">Inicio</a> / <a href="'.route('clientes.index').'">Clientes</a> / <b>Editar '.$cliente->codigo.'</b>')

@section('contenido')
<div class="tarjeta" style="max-width:520px;">
    <h2>Editar cliente {{ $cliente->codigo }}</h2>
    <form method="POST" action="{{ route('clientes.update', $cliente) }}">
        @csrf @method('PUT')
        <div class="campo">
            <label for="nombre">Nombre completo *</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $cliente->nombre) }}" required>
        </div>
        <div class="campo">
            <label for="ci">CI (único)</label>
            <input type="text" id="ci" name="ci" value="{{ old('ci', $cliente->ci) }}">
        </div>
        <div class="campo">
            <label for="telefono">Teléfono</label>
            <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $cliente->telefono) }}">
        </div>
        <div style="display:flex; gap:10px;">
            <button class="btn btn-primario" type="submit">Guardar cambios</button>
            <a class="btn btn-secundario" href="{{ route('clientes.index') }}">Cancelar</a>
        </div>
    </form>
</div>
@endsection
