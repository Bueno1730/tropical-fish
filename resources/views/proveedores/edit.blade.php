@extends('layouts.app')

@section('titulo', 'Editar proveedor')
@section('miga', '<a href="'.route('inicio').'">Inicio</a> / <a href="'.route('proveedores.index').'">Proveedores</a> / <b>Editar '.$proveedor->codigo.'</b>')

@section('contenido')
<div class="tarjeta" style="max-width:520px;">
    <h2>Editar proveedor {{ $proveedor->codigo }}</h2>
    <form method="POST" action="{{ route('proveedores.update', $proveedor) }}">
        @csrf @method('PUT')
        <div class="campo">
            <label for="nombre">Nombre *</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $proveedor->nombre) }}" required>
        </div>
        <div class="campo">
            <label for="nit_ci">NIT / CI (único)</label>
            <input type="text" id="nit_ci" name="nit_ci" value="{{ old('nit_ci', $proveedor->nit_ci) }}">
        </div>
        <div class="campo">
            <label for="telefono">Teléfono *</label>
            <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $proveedor->telefono) }}" required>
        </div>
        <div class="campo">
            <label for="correo">Correo</label>
            <input type="email" id="correo" name="correo" value="{{ old('correo', $proveedor->correo) }}">
        </div>
        <div class="campo">
            <label for="direccion">Dirección</label>
            <input type="text" id="direccion" name="direccion" value="{{ old('direccion', $proveedor->direccion) }}">
        </div>
        <div class="campo">
            <label>Tipo de proveedor *</label>
            <div class="opciones-pastilla">
                <label><input type="radio" name="tipo" value="fijo" @checked(old('tipo', $proveedor->tipo) === 'fijo')> Fijo</label>
                <label><input type="radio" name="tipo" value="eventual" @checked(old('tipo', $proveedor->tipo) === 'eventual')> Eventual</label>
            </div>
        </div>
        <div style="display:flex; gap:10px;">
            <button class="btn btn-primario" type="submit">Guardar cambios</button>
            <a class="btn btn-secundario" href="{{ route('proveedores.index') }}">Cancelar</a>
        </div>
    </form>
</div>
@endsection
