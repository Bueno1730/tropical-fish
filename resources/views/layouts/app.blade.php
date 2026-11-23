<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo', 'Inicio') · Tropical Fish</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🐠</text></svg>">
</head>
<body>
@php $esAdmin = auth()->user()?->esAdministrador(); @endphp
<div class="layout">
    <aside class="sidebar" id="sidebar">
        <div class="marca">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#22d3ee" stroke-width="2"><path d="M6.5 12c4-6.5 11-6.5 15 0-4 6.5-11 6.5-15 0z"/><path d="M6.5 12 2 8v8l4.5-4z"/><circle cx="16" cy="10.5" r="1" fill="#22d3ee"/></svg>
            Tropical Fish
        </div>
        <nav>
            <a href="{{ route('inicio') }}" class="{{ request()->routeIs('inicio') ? 'activo' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 10.5 12 3l9 7.5V21H3z"/><path d="M9 21v-7h6v7"/></svg>
                Inicio
            </a>
            <a href="{{ route('productos.index') }}" class="{{ request()->routeIs('productos.*') ? 'activo' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 8 12 3 3 8v8l9 5 9-5z"/><path d="M3 8l9 5 9-5M12 13v8"/></svg>
                Productos
            </a>
            @if($esAdmin)
            <a href="{{ route('proveedores.index') }}" class="{{ request()->routeIs('proveedores.*') ? 'activo' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9h18M3 9l1-5h16l1 5M5 9v11h14V9"/><path d="M9 13h6"/></svg>
                Proveedores
            </a>
            <a href="{{ route('entradas.index') }}" class="{{ request()->routeIs('entradas.*') ? 'activo' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v12m0 0 4-4m-4 4-4-4"/><path d="M4 17v3h16v-3"/></svg>
                Entradas
            </a>
            <a href="{{ route('importaciones.index') }}" class="{{ request()->routeIs('importaciones.*') ? 'activo' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6v20h12V6z"/><path d="M14 2v4h4M9 13h6M9 17h6"/></svg>
                Importar CSV
            </a>
            @endif
            <a href="{{ route('lotes.index') }}" class="{{ request()->routeIs('lotes.*') ? 'activo' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6.5 12c4-6.5 11-6.5 15 0-4 6.5-11 6.5-15 0z"/><path d="M6.5 12 2 8v8l4.5-4z"/></svg>
                Lotes de peces
            </a>
            <a href="{{ route('ventas.index') }}" class="{{ request()->routeIs('ventas.*') ? 'activo' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="20" r="1.5"/><circle cx="17" cy="20" r="1.5"/><path d="M3 3h2l2.5 12h10L21 7H6"/></svg>
                Ventas
            </a>
            <a href="{{ route('clientes.index') }}" class="{{ request()->routeIs('clientes.*') ? 'activo' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                Clientes
            </a>
            @if($esAdmin)
            <a href="{{ route('reportes.index') }}" class="{{ request()->routeIs('reportes.*') ? 'activo' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/></svg>
                Reportes
            </a>
            <a href="{{ route('usuarios.index') }}" class="{{ request()->routeIs('usuarios.*') ? 'activo' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/><path d="M18 5l4 4-4 4"/></svg>
                Usuarios
            </a>
            @endif
        </nav>
        <nav class="salir">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5V3h4M16 17l5-5-5-5M21 12H9"/></svg>
                    Cerrar sesión
                </a>
            </form>
        </nav>
    </aside>

    <div class="contenido">
        <header class="topbar">
            <div class="miga">
                <button class="menu-movil" onclick="document.getElementById('sidebar').classList.toggle('abierto')" aria-label="Menú">☰</button>
                @yield('miga', 'Inicio')
            </div>
            <div class="usuario">{{ auth()->user()->esAdministrador() ? 'Administrador' : 'Vendedor' }} (<b>{{ auth()->user()->usuario }}</b>)</div>
        </header>

        <main class="pagina">
            @if(session('exito'))
                <div class="alerta alerta-exito">{{ session('exito') }}</div>
            @endif
            @if(session('error'))
                <div class="alerta alerta-error">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alerta alerta-error">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @yield('contenido')
        </main>
    </div>
</div>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
