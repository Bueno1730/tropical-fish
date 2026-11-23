@extends('layouts.app')

@section('titulo', 'Nueva venta')
@section('miga', '<a href="'.route('inicio').'">Inicio</a> / <a href="'.route('ventas.index').'">Ventas</a> / <b>Nueva venta</b>')

@section('contenido')
<div class="encabezado-pagina"><h1>Nueva venta</h1></div>

<form method="POST" action="{{ route('ventas.store') }}" id="form-venta">
    @csrf
    <div class="grid-2" style="grid-template-columns: 3fr 2fr;">
        <div class="tarjeta">
            <h2>Productos</h2>
            <div style="display:flex; gap:8px; margin-bottom:14px;">
                <select id="sel-producto" style="flex:1; padding:9px 12px; border:1px solid var(--borde); border-radius:7px;">
                    <option value="">Buscar producto por código o nombre…</option>
                    @foreach($productos as $p)
                    <option value="{{ $p->id }}" data-codigo="{{ $p->codigo }}" data-nombre="{{ $p->nombre }}" data-precio="{{ $p->precio_venta }}" data-stock="{{ $p->stock_actual }}" @disabled($p->stock_actual < 1)>
                        {{ $p->codigo }} — {{ $p->nombre }} (disp: {{ $p->stock_actual }})
                    </option>
                    @endforeach
                </select>
                <input type="number" id="sel-cantidad" min="1" value="1" style="width:70px; padding:9px; border:1px solid var(--borde); border-radius:7px;">
                <button type="button" class="btn btn-secundario" id="btn-agregar">Agregar</button>
            </div>

            <table class="tabla">
                <thead><tr><th>Producto</th><th>Disp.</th><th>Cant.</th><th>P. unit.</th><th>Subtotal</th><th></th></tr></thead>
                <tbody id="carrito"></tbody>
            </table>
            <p class="ayuda" style="margin-top:10px;">El detalle se genera automáticamente y el stock se descuenta al confirmar. No se permite vender más de lo disponible.</p>
            <div id="inputs-venta"></div>
        </div>

        <div>
            <div class="tarjeta">
                <div class="campo">
                    <label for="cliente_id">Cliente (opcional)</label>
                    <select id="cliente_id" name="cliente_id" style="width:100%; padding:9px 12px; border:1px solid var(--borde); border-radius:7px;">
                        <option value="">Cliente general</option>
                        @foreach($clientes as $c)
                        <option value="{{ $c->id }}" @selected(old('cliente_id') == $c->id)>{{ $c->nombre }}{{ $c->ci ? ' (CI '.$c->ci.')' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="campo">
                    <input type="text" name="cliente_nuevo" value="{{ old('cliente_nuevo') }}" placeholder="…o escribe un nombre para registrar cliente nuevo" style="width:100%; padding:9px 12px; border:1px solid var(--borde); border-radius:7px;">
                </div>
                <div class="campo">
                    <label>Forma de pago *</label>
                    <div class="opciones-pastilla">
                        <label><input type="radio" name="forma_pago" value="efectivo" @checked(old('forma_pago', 'efectivo') === 'efectivo')> Efectivo</label>
                        <label><input type="radio" name="forma_pago" value="qr" @checked(old('forma_pago') === 'qr')> QR</label>
                    </div>
                </div>
                <div class="campo">
                    <label>Canal de venta *</label>
                    <div class="opciones-pastilla">
                        @foreach(['presencial' => 'Presencial', 'whatsapp' => 'WhatsApp', 'facebook' => 'Facebook', 'tiktok' => 'TikTok', 'instagram' => 'Instagram', 'otro' => 'Otro'] as $v => $txt)
                        <label><input type="radio" name="canal" value="{{ $v }}" @checked(old('canal', 'presencial') === $v)> {{ $txt }}</label>
                        @endforeach
                    </div>
                </div>
                <div style="border-top:1px solid var(--borde); padding-top:12px; margin-top:6px;">
                    <div style="display:flex; justify-content:space-between; font-size:13px; color:var(--texto-suave);"><span>Unidades</span><b id="venta-unidades">0</b></div>
                    <div class="total-venta">TOTAL <span id="venta-total">Bs 0,00</span></div>
                </div>
                <button class="btn btn-primario" type="submit" style="width:100%; justify-content:center; margin-top:14px;">Confirmar venta</button>
                <a class="btn btn-secundario" href="{{ route('ventas.index') }}" style="width:100%; justify-content:center; margin-top:8px;">Cancelar</a>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
const selProd = document.getElementById('sel-producto');
const selCant = document.getElementById('sel-cantidad');
const carrito = document.getElementById('carrito');
const inputs = document.getElementById('inputs-venta');
let items = [];

function render() {
    carrito.innerHTML = '';
    inputs.innerHTML = '';
    let total = 0, unidades = 0;
    items.forEach((it, i) => {
        const sub = it.cantidad * it.precio;
        total += sub; unidades += it.cantidad;
        carrito.insertAdjacentHTML('beforeend', `<tr>
            <td>${it.codigo} ${it.nombre}</td><td>${it.stock}</td>
            <td><input type="number" class="cant-edit" data-i="${i}" min="1" max="${it.stock}" value="${it.cantidad}" style="width:64px; padding:5px; border:1px solid var(--borde); border-radius:6px;"></td>
            <td>Bs ${it.precio.toFixed(2)}</td><td>Bs ${sub.toFixed(2)}</td>
            <td><button type="button" class="btn btn-sm btn-peligro quitar" data-i="${i}">✕</button></td>
        </tr>`);
        inputs.insertAdjacentHTML('beforeend',
            `<input type="hidden" name="items[${i}][producto_id]" value="${it.id}">
             <input type="hidden" name="items[${i}][cantidad]" value="${it.cantidad}" class="input-cant" data-i="${i}">`);
    });
    document.getElementById('venta-total').textContent = 'Bs ' + total.toFixed(2).replace('.', ',');
    document.getElementById('venta-unidades').textContent = unidades;
}

document.getElementById('btn-agregar').addEventListener('click', () => {
    const opt = selProd.options[selProd.selectedIndex];
    if (!opt || !opt.value) return;
    const cant = Math.max(1, parseInt(selCant.value) || 1);
    const stock = parseInt(opt.dataset.stock);
    const existente = items.find(i => i.id === opt.value);
    const enCarrito = existente ? existente.cantidad : 0;
    if (enCarrito + cant > stock) { alert(`Solo hay ${stock} unidades disponibles de ${opt.dataset.nombre}.`); return; }
    if (existente) { existente.cantidad += cant; }
    else { items.push({ id: opt.value, codigo: opt.dataset.codigo, nombre: opt.dataset.nombre, precio: parseFloat(opt.dataset.precio), stock, cantidad: cant }); }
    selProd.selectedIndex = 0; selCant.value = 1;
    render();
});

carrito.addEventListener('click', e => {
    if (e.target.classList.contains('quitar')) { items.splice(parseInt(e.target.dataset.i), 1); render(); }
});
carrito.addEventListener('change', e => {
    if (e.target.classList.contains('cant-edit')) {
        const i = parseInt(e.target.dataset.i);
        items[i].cantidad = Math.min(Math.max(1, parseInt(e.target.value) || 1), items[i].stock);
        render();
    }
});
document.getElementById('form-venta').addEventListener('submit', e => {
    if (!items.length) { e.preventDefault(); alert('Agrega al menos un producto a la venta.'); }
});
render();
</script>
@endpush
@endsection
