@extends('layouts.app')

@section('titulo', 'Importar productos')
@section('miga', '<a href="'.route('inicio').'">Inicio</a> / <b>Importar productos</b>')

@section('contenido')
<div class="encabezado-pagina">
    <h1>Importar productos desde CSV</h1>
</div>

@if(session('errores_importacion') && count(session('errores_importacion')))
<div class="tarjeta">
    <h2>Detalle de errores</h2>
    <div class="alerta alerta-error" style="margin-bottom:0;">
        @foreach(session('errores_importacion') as $err)<div>{{ $err }}</div>@endforeach
    </div>
</div>
@endif

<div class="grid-2">
    <div class="tarjeta">
        <form method="POST" action="{{ route('importaciones.importar') }}" enctype="multipart/form-data" id="form-importar">
            @csrf
            <div class="subir-csv" id="zona-csv">
                <p style="font-size:15px; margin-bottom:6px;">Arrastra aquí tu archivo .csv</p>
                <p>o</p>
                <label class="btn btn-secundario" style="margin-top:10px;">
                    Seleccionar archivo
                    <input type="file" name="archivo" id="archivo" accept=".csv" required>
                </label>
                <p id="nombre-archivo" style="margin-top:12px; font-weight:600; color:var(--texto);"></p>
            </div>
            <div class="campo" style="margin-top:14px;">
                <label class="campo-check">
                    <input type="checkbox" name="actualizar" value="1">
                    Actualizar productos existentes (mismo código)
                </label>
            </div>
            <button class="btn btn-primario" type="submit">Importar productos</button>
        </form>
    </div>

    <div class="tarjeta">
        <h2>Formato del archivo</h2>
        <p style="color:var(--texto-suave); font-size:13px; margin-bottom:10px;">Columnas en este orden, separadas por coma:</p>
        <table class="tabla">
            <thead><tr><th>Columna</th><th>Ejemplo</th></tr></thead>
            <tbody>
                <tr><td><code>codigo</code></td><td>ALI-011</td></tr>
                <tr><td><code>nombre</code></td><td>Alimento Sera 50g</td></tr>
                <tr><td><code>categoria</code></td><td>Alimentos</td></tr>
                <tr><td><code>proveedor</code></td><td>PRV-0002</td></tr>
                <tr><td><code>precio_venta</code></td><td>38.00</td></tr>
                <tr><td><code>stock_minimo</code></td><td>5</td></tr>
            </tbody>
        </table>
        <p style="margin-top:14px;"><a class="btn btn-secundario btn-sm" href="{{ route('importaciones.plantilla') }}">Descargar plantilla CSV</a></p>
    </div>
</div>

<div class="tarjeta">
    <h2>Historial de importaciones</h2>
    <table class="tabla">
        <thead><tr><th>Fecha</th><th>Archivo</th><th>Usuario</th><th>Insertadas</th><th>Actualizadas</th><th>Con error</th></tr></thead>
        <tbody>
            @forelse($importaciones as $imp)
            <tr>
                <td>{{ $imp->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $imp->archivo }}</td>
                <td>{{ $imp->usuario->usuario }}</td>
                <td>{{ $imp->insertadas }}</td>
                <td>{{ $imp->actualizadas }}</td>
                <td>{{ $imp->con_error }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="vacio">Sin importaciones registradas</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@push('scripts')
<script>
const zona = document.getElementById('zona-csv');
const input = document.getElementById('archivo');
const nombre = document.getElementById('nombre-archivo');
input.addEventListener('change', () => {
    const f = input.files[0];
    if (f && !f.name.toLowerCase().endsWith('.csv')) {
        nombre.textContent = f.name + ' no es un archivo .csv. Usa la plantilla (6 columnas) y vuelve a intentarlo.';
        nombre.style.color = 'var(--error)';
        input.value = '';
    } else if (f) {
        nombre.textContent = f.name;
        nombre.style.color = 'var(--texto)';
    }
});
['dragover','dragenter'].forEach(e => zona.addEventListener(e, ev => { ev.preventDefault(); zona.classList.add('arrastrando'); }));
['dragleave','drop'].forEach(e => zona.addEventListener(e, ev => { ev.preventDefault(); zona.classList.remove('arrastrando'); }));
zona.addEventListener('drop', ev => {
    const f = ev.dataTransfer.files[0];
    if (f && f.name.toLowerCase().endsWith('.csv')) {
        input.files = ev.dataTransfer.files;
        input.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection
