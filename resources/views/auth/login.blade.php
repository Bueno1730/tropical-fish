@extends('layouts.auth')

@section('titulo', 'Iniciar sesión')

@section('contenido')
<div class="login-caja">
    <div class="logo">🐠</div>
    <h1>Tropical Fish</h1>
    <p class="sub">Sistema de Gestión de Acuario</p>

    <div class="modulos-login">
        <span>Inventario</span><span>Ventas</span><span>Lotes de peces</span><span>Reportes</span>
    </div>

    <h2 style="font-size:16px; margin-bottom:4px;">Iniciar sesión</h2>
    <p class="sub" style="text-align:left; margin-bottom:18px;">Ingresa tus credenciales para continuar</p>

    @if(session('exito'))
        <div class="alerta alerta-exito">{{ session('exito') }}</div>
    @endif
    @if($errors->any())
        <div class="alerta alerta-error">
            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <div class="campo">
            <label for="usuario">Usuario</label>
            <input type="text" id="usuario" name="usuario" value="{{ old('usuario') }}" placeholder="Ej: admin" required autofocus>
        </div>
        <div class="campo">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required>
        </div>
        <div class="login-extra">
            <label class="campo-check"><input type="checkbox" name="recordarme" value="1"> Recordarme</label>
            <a href="{{ route('recuperar') }}">¿Olvidaste tu contraseña?</a>
        </div>
        <button type="submit" class="btn btn-primario" style="width:100%; justify-content:center;">Ingresar</button>
    </form>

    <p class="nota">Acceso exclusivo para personal autorizado</p>
</div>
@endsection
