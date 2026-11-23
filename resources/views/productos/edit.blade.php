@extends('layouts.app')

@section('titulo', 'Editar producto')
@section('miga', '<a href="'.route('inicio').'">Inicio</a> / <a href="'.route('productos.index').'">Productos</a> / <b>Editar '.$producto->codigo.'</b>')

@section('contenido')
<div class="tarjeta" style="max-width:720px;">
    <h2>Editar producto {{ $producto->codigo }}</h2>
    <form method="POST" action="{{ route('productos.update', $producto) }}">
        @csrf @method('PUT')
        @include('productos.formulario')
        <div style="display:flex; gap:10px; margin-top:18px;">
            <button class="btn btn-primario" type="submit">Guardar cambios</button>
            <a class="btn btn-secundario" href="{{ route('productos.index') }}">Cancelar</a>
        </div>
    </form>
</div>
@endsection
