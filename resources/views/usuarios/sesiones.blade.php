@extends('layouts.app')

@section('titulo', 'Sesiones')
@section('miga', '<a href="'.route('inicio').'">Inicio</a> / <a href="'.route('usuarios.index').'">Usuarios</a> / <b>Sesiones</b>')

@section('contenido')
<div class="encabezado-pagina">
    <h1>Registro de sesiones</h1>
    <a class="btn btn-secundario" href="{{ route('usuarios.index') }}">Volver a usuarios</a>
</div>

<div class="tarjeta tabla-responsive">
    <table class="tabla">
        <thead><tr><th>Usuario</th><th>Nombre</th><th>Inicio</th><th>Última actividad</th><th>Fin</th><th>IP</th><th>Estado</th></tr></thead>
        <tbody>
            @forelse($sesiones as $s)
            <tr>
                <td><b>{{ $s->usuario->usuario }}</b></td>
                <td>{{ $s->usuario->nombre }}</td>
                <td>{{ $s->inicio->format('d/m/Y H:i') }}</td>
                <td>{{ $s->ultima_actividad->format('d/m/Y H:i') }}</td>
                <td>{{ $s->fin?->format('d/m/Y H:i') ?? '—' }}</td>
                <td>{{ $s->ip ?? '—' }}</td>
                <td><span class="badge badge-{{ $s->fin ? 'cerrado' : 'activo' }}">{{ $s->fin ? 'Cerrada' : 'Activa' }}</span></td>
            </tr>
            @empty
            <tr><td colspan="7" class="vacio">Sin sesiones registradas</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="paginacion">{{ $sesiones->links() }}</div>
</div>
@endsection
