@extends('layouts.auth')

@section('titulo', 'Recuperar contraseña')

@section('contenido')
<div class="login-caja">
    <div class="logo">🐠</div>
    <h1>Tropical Fish</h1>
    <p class="sub">Sistema de Gestión de Acuario</p>

    <h2 style="font-size:16px; margin-bottom:4px;">Recuperar contraseña</h2>
    <p class="sub" style="text-align:left; margin-bottom:18px;">Ingresa tu nombre de usuario. El administrador recibirá la solicitud para restablecer tu clave de acceso.</p>

    @if($errors->any())
        <div class="alerta alerta-error">
            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('recuperar.post') }}">
        @csrf
        <div class="campo">
            <label for="usuario">Usuario</label>
            <input type="text" id="usuario" name="usuario" value="{{ old('usuario') }}" placeholder="Ej: vendedor1" required autofocus>
        </div>
        <button type="submit" class="btn btn-primario" style="width:100%; justify-content:center;">Enviar solicitud</button>
    </form>

    <p class="nota"><a href="{{ route('login') }}">Volver al inicio de sesión</a></p>
</div>
@endsection
