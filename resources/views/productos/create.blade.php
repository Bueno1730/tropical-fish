@extends('layouts.app')

@section('titulo', 'Nuevo producto')
@section('miga', '<a href="'.route('inicio').'">Inicio</a> / <a href="'.route('productos.index').'">Productos</a> / <b>Nuevo</b>')

@section('contenido')
<div class="tarjeta" style="max-width:720px;">
    <h2>Nuevo producto</h2>
    <form method="POST" action="{{ route('productos.store') }}">
        @csrf
        @include('productos.formulario')
        <div style="display:flex; gap:10px; margin-top:18px;">
            <button class="btn btn-primario" type="submit">Guardar producto</button>
            <a class="btn btn-secundario" href="{{ route('productos.index') }}">Cancelar</a>
        </div>
    </form>
</div>
@endsection
