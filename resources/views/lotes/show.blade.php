@extends('layouts.app')

@section('titulo', $lote->codigo)
@section('miga', '<a href="'.route('inicio').'">Inicio</a> / <a href="'.route('lotes.index').'">Lotes</a> / <b>'.$lote->codigo.'</b>')

@section('contenido')
<div class="encabezado-pagina">
    <h1>Lote {{ $lote->codigo }} <span class="badge badge-{{ $lote->estado }}">{{ str_replace('_',' ',ucfirst($lote->estado)) }}</span></h1>
    <div style="display:flex; gap:8px;">
        <a class="btn btn-secundario" href="{{ route('lotes.index') }}">Volver</a>
        @if(auth()->user()->esAdministrador())
        <a class="btn btn-secundario" href="{{ route('lotes.edit', $lote) }}">Editar</a>
        @endif
    </div>
</div>

<div class="grid-2">
    <div class="tarjeta">
        <h2>Datos de recepción</h2>
        <table class="tabla">
            <tr><td style="width:45%; color:var(--texto-suave)">Especie</td><td><b>{{ $lote->producto->codigo }} {{ $lote->producto->nombre }}</b></td></tr>
            <tr><td style="color:var(--texto-suave)">Proveedor</td><td>{{ $lote->proveedor ? $lote->proveedor->codigo.' '.$lote->proveedor->nombre : '—' }}</td></tr>
            <tr><td style="color:var(--texto-suave)">Fecha de recepción</td><td>{{ $lote->fecha_recepcion->format('d/m/Y') }}</td></tr>
            <tr><td style="color:var(--texto-suave)">Cantidad inicial</td><td>{{ $lote->cantidad_inicial }}</td></tr>
            <tr><td style="color:var(--texto-suave)">Unidades retiradas</td><td>{{ $lote->total_retirado }}</td></tr>
            <tr><td style="color:var(--texto-suave)">Disponibles</td><td><b>{{ $lote->disponibles() }}</b></td></tr>
            @if($lote->precio_compra)
            <tr><td style="color:var(--texto-suave)">Precio de compra</td><td>Bs {{ number_format($lote->precio_compra, 2) }}</td></tr>
            @endif
            @if($lote->observaciones)
            <tr><td style="color:var(--texto-suave)">Observaciones</td><td>{{ $lote->observaciones }}</td></tr>
            @endif
        </table>
    </div>

    <div class="tarjeta">
        <h2>Condiciones del agua de llegada</h2>
        <table class="tabla">
            <tr><td style="width:45%; color:var(--texto-suave)">pH</td><td>{{ $lote->ph ?? '—' }}</td></tr>
            <tr><td style="color:var(--texto-suave)">Temperatura</td><td>{{ $lote->temperatura ? $lote->temperatura.' °C' : '—' }}</td></tr>
            <tr><td style="color:var(--texto-suave)">Amoníaco</td><td>{{ $lote->amoniaco !== null ? $lote->amoniaco.' ppm' : '—' }}</td></tr>
            <tr><td style="color:var(--texto-suave)">Nitritos</td><td>{{ $lote->nitritos !== null ? $lote->nitritos.' ppm' : '—' }}</td></tr>
            <tr><td style="color:var(--texto-suave)">Dureza</td><td>{{ $lote->dureza ? $lote->dureza.' °GH' : '—' }}</td></tr>
        </table>
        @if(auth()->user()->esAdministrador())
        <form method="POST" action="{{ route('lotes.estado', $lote) }}" style="margin-top:14px; display:flex; gap:8px; align-items:center;">
            @csrf
            <label style="font-size:13px; font-weight:600;">Estado:</label>
            <select name="estado" style="padding:7px 10px; border:1px solid var(--borde); border-radius:7px;">
                @foreach(['activo' => 'Activo', 'en_tratamiento' => 'En tratamiento', 'agotado' => 'Agotado', 'cerrado' => 'Cerrado'] as $v => $txt)
                <option value="{{ $v }}" @selected($lote->estado === $v)>{{ $txt }}</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-secundario">Cambiar</button>
        </form>
        @endif
    </div>
</div>

<div class="grid-2">
    <div class="tarjeta">
        <h2>Registrar retiro de unidades</h2>
        <p style="color:var(--texto-suave); font-size:13px; margin-bottom:12px;">
            Registra las unidades que se retiran del lote, sin clasificar el motivo. Se descuentan del stock.
        </p>
        <form method="POST" action="{{ route('lotes.retiros.store', $lote) }}">
            @csrf
            <div class="fila">
                <div class="campo">
                    <label for="cantidad">Cantidad (máx. {{ $lote->disponibles() }}) *</label>
                    <input type="number" name="cantidad" min="1" max="{{ $lote->disponibles() }}" required>
                </div>
                <div class="campo">
                    <label for="observacion">Observación</label>
                    <input type="text" name="observacion" maxlength="200" placeholder="Opcional">
                </div>
            </div>
            <button class="btn btn-primario" type="submit" @disabled($lote->disponibles() === 0)>Registrar retiro</button>
        </form>

        <h3 style="font-size:14px; margin:20px 0 8px;">Historial de retiros</h3>
        <table class="tabla">
            <thead><tr><th>Fecha</th><th>Cantidad</th><th>Registrado por</th><th>Observación</th></tr></thead>
            <tbody>
                @forelse($lote->retiros as $r)
                <tr>
                    <td>{{ $r->fecha->format('d/m/Y H:i') }}</td>
                    <td>{{ $r->cantidad }}</td>
                    <td>{{ $r->usuario->usuario }}</td>
                    <td>{{ $r->observacion ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="vacio">Sin retiros registrados</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="tarjeta">
        <h2>Tratamientos aplicados</h2>
        @if(auth()->user()->esAdministrador())
        <form method="POST" action="{{ route('lotes.tratamientos.store', $lote) }}">
            @csrf
            <div class="fila">
                <div class="campo">
                    <label for="fecha_inicio">Fecha inicio *</label>
                    <input type="date" name="fecha_inicio" value="{{ now()->toDateString() }}" required>
                </div>
                <div class="campo">
                    <label for="fecha_fin">Fecha fin</label>
                    <input type="date" name="fecha_fin">
                </div>
            </div>
            <div class="fila">
                <div class="campo">
                    <label for="producto">Producto aplicado *</label>
                    <input type="text" name="producto" placeholder="Ej: Azul de metileno" required>
                </div>
                <div class="campo">
                    <label for="dosis">Dosis</label>
                    <input type="text" name="dosis" placeholder="Ej: 2 gotas/L">
                </div>
            </div>
            <div class="campo">
                <label for="sintomas">Síntomas</label>
                <input type="text" name="sintomas" placeholder="Ej: aletas pegadas, manchas blancas">
            </div>
            <button class="btn btn-primario" type="submit">Registrar tratamiento</button>
        </form>
        @endif

        <table class="tabla" style="margin-top:16px;">
            <thead><tr><th>Inicio</th><th>Fin</th><th>Síntomas</th><th>Producto</th><th>Dosis</th></tr></thead>
            <tbody>
                @forelse($lote->tratamientos as $t)
                <tr>
                    <td>{{ $t->fecha_inicio->format('d/m/Y') }}</td>
                    <td>{{ $t->fecha_fin?->format('d/m/Y') ?? 'En curso' }}</td>
                    <td>{{ $t->sintomas ?? '—' }}</td>
                    <td>{{ $t->producto }}</td>
                    <td>{{ $t->dosis ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="vacio">Sin tratamientos registrados</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
