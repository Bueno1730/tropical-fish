@extends('layouts.app')

@section('titulo', 'Editar usuario')
@section('miga', '<a href="'.route('inicio').'">Inicio</a> / <a href="'.route('usuarios.index').'">Usuarios</a> / <b>Editar '.$usuario->codigo.'</b>')

@section('contenido')
<div class="tarjeta" style="max-width:520px;">
    <h2>Editar usuario {{ $usuario->codigo }}</h2>
    <form method="POST" action="{{ route('usuarios.update', $usuario) }}">
        @csrf @method('PUT')
        <div class="campo">
            <label for="nombre">Nombre completo *</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $usuario->nombre) }}" required>
        </div>
        <div class="campo">
            <label for="ci">CI (único) *</label>
            <input type="text" id="ci" name="ci" value="{{ old('ci', $usuario->ci) }}" required>
        </div>
        <div class="campo">
            <label for="usuario">Usuario *</label>
            <input type="text" id="usuario" name="usuario" value="{{ old('usuario', $usuario->usuario) }}" required>
        </div>
        <div class="campo">
            <label for="password">Nueva clave <span style="color:var(--texto-suave); font-weight:400;">(dejar vacío para conservar)</span></label>
            <input type="password" id="password" name="password">
        </div>
        <div class="campo">
            <label for="password_confirmation">Confirmar clave</label>
            <input type="password" id="password_confirmation" name="password_confirmation">
        </div>
        <div class="campo">
            <label>Rol *</label>
            <div class="opciones-pastilla">
                <label><input type="radio" name="rol" value="vendedor" @checked(old('rol', $usuario->rol) === 'vendedor')> Vendedor</label>
                <label><input type="radio" name="rol" value="administrador" @checked(old('rol', $usuario->rol) === 'administrador')> Administrador</label>
            </div>
        </div>
        <div style="display:flex; gap:10px;">
            <button class="btn btn-primario" type="submit">Guardar cambios</button>
            <a class="btn btn-secundario" href="{{ route('usuarios.index') }}">Cancelar</a>
        </div>
    </form>
</div>
@endsection
