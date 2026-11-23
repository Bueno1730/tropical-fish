@extends('layouts.app')

@section('titulo', 'Usuarios')
@section('miga', '<a href="'.route('inicio').'">Inicio</a> / <b>Usuarios</b>')

@section('contenido')
<div class="encabezado-pagina">
    <h1>Usuarios del sistema</h1>
    <a class="btn btn-secundario" href="{{ route('usuarios.sesiones') }}">Ver sesiones</a>
</div>

@if($solicitudes->count())
<div class="tarjeta">
    <h2>Solicitudes de restablecimiento de clave</h2>
    <table class="tabla">
        <thead><tr><th>Usuario</th><th>Nombre</th><th>Solicitado</th><th class="acciones">Acción</th></tr></thead>
        <tbody>
            @foreach($solicitudes as $s)
            <tr>
                <td><b>{{ $s->usuario->usuario }}</b></td>
                <td>{{ $s->usuario->nombre }}</td>
                <td>{{ $s->created_at->format('d/m/Y H:i') }}</td>
                <td class="acciones">
                    <form class="acciones-form" method="POST" action="{{ route('usuarios.restablecer', $s->usuario) }}" data-confirm="¿Generar una clave temporal para {{ $s->usuario->usuario }}?">
                        @csrf
                        <button class="btn btn-sm btn-primario">Restablecer clave</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<div class="grid-2" style="grid-template-columns: 3fr 2fr;">
    <div class="tarjeta tabla-responsive">
        <table class="tabla">
            <thead><tr><th>Código</th><th>CI</th><th>Nombre</th><th>Usuario</th><th>Rol</th><th>Estado</th><th class="acciones">Acciones</th></tr></thead>
            <tbody>
                @foreach($usuarios as $u)
                <tr>
                    <td><b>{{ $u->codigo }}</b></td>
                    <td>{{ $u->ci }}</td>
                    <td>{{ $u->nombre }}</td>
                    <td>{{ $u->usuario }}</td>
                    <td><span class="badge badge-{{ $u->rol === 'administrador' ? 'admin' : 'vendedor' }}">{{ $u->rol === 'administrador' ? 'Admin.' : 'Vendedor' }}</span></td>
                    <td><span class="badge badge-{{ $u->estado }}">{{ ucfirst($u->estado) }}</span></td>
                    <td class="acciones">
                        <a class="btn btn-sm btn-secundario" href="{{ route('usuarios.edit', $u) }}">Editar</a>
                        <form class="acciones-form" method="POST" action="{{ route('usuarios.restablecer', $u) }}" data-confirm="¿Generar una clave temporal para {{ $u->usuario }}?">
                            @csrf
                            <button class="btn btn-sm btn-secundario">Restablecer clave</button>
                        </form>
                        <form class="acciones-form" method="POST" action="{{ route($u->estado === 'activo' ? 'usuarios.desactivar' : 'usuarios.reactivar', $u) }}">
                            @csrf
                            <button class="btn btn-sm btn-secundario">{{ $u->estado === 'activo' ? 'Desactivar' : 'Reactivar' }}</button>
                        </form>
                        <form class="acciones-form" method="POST" action="{{ route('usuarios.destroy', $u) }}" data-confirm="¿Eliminar al usuario {{ $u->usuario }}? Solo si no tiene ventas ni sesiones.">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-peligro">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p class="ayuda" style="margin-top:12px;">No se puede desactivar ni eliminar al último administrador activo.</p>
    </div>

    <div class="tarjeta">
        <h2>Nuevo usuario <span style="color:var(--texto-suave); font-size:12px; font-weight:400;">{{ $proximoCodigo }} (auto)</span></h2>
        <form method="POST" action="{{ route('usuarios.store') }}">
            @csrf
            <div class="campo">
                <label for="nombre">Nombre completo *</label>
                <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" placeholder="Nombre y apellido" required>
            </div>
            <div class="campo">
                <label for="ci">CI (único) *</label>
                <input type="text" id="ci" name="ci" value="{{ old('ci') }}" placeholder="Ej: 7654321" required>
            </div>
            <div class="campo">
                <label for="usuario">Usuario *</label>
                <input type="text" id="usuario" name="usuario" value="{{ old('usuario') }}" placeholder="Ej: vendedor3" required>
            </div>
            <div class="campo">
                <label for="password">Clave de acceso *</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="campo">
                <label for="password_confirmation">Confirmar clave *</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>
            <div class="campo">
                <label>Rol *</label>
                <div class="opciones-pastilla">
                    <label><input type="radio" name="rol" value="vendedor" @checked(old('rol', 'vendedor') === 'vendedor')> Vendedor</label>
                    <label><input type="radio" name="rol" value="administrador" @checked(old('rol') === 'administrador')> Administrador</label>
                </div>
            </div>
            <button class="btn btn-primario" type="submit" style="width:100%; justify-content:center;">Guardar usuario</button>
        </form>
    </div>
</div>
@endsection
