@php $p = $producto ?? null; @endphp
<div class="fila">
    <div class="campo">
        <label for="codigo">Código *</label>
        <input type="text" id="codigo" name="codigo" value="{{ old('codigo', $p->codigo ?? '') }}" placeholder="Ej: ALI-011" required>
    </div>
    <div class="campo">
        <label for="nombre">Nombre *</label>
        <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $p->nombre ?? '') }}" required>
    </div>
</div>
<div class="fila">
    <div class="campo">
        <label for="categoria_id">Categoría *</label>
        <select id="categoria_id" name="categoria_id" required>
            <option value="">— Selecciona —</option>
            @foreach($categorias as $c)
            <option value="{{ $c->id }}" @selected(old('categoria_id', $p->categoria_id ?? '') == $c->id)>{{ $c->nombre }}{{ $c->maneja_lotes ? ' (por lotes)' : '' }}</option>
            @endforeach
        </select>
    </div>
    <div class="campo">
        <label for="proveedor_id">Proveedor</label>
        <select id="proveedor_id" name="proveedor_id">
            <option value="">— Sin proveedor —</option>
            @foreach($proveedores as $pr)
            <option value="{{ $pr->id }}" @selected(old('proveedor_id', $p->proveedor_id ?? '') == $pr->id)>{{ $pr->codigo }} {{ $pr->nombre }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="fila">
    <div class="campo">
        <label for="precio_venta">Precio de venta (Bs) *</label>
        <input type="number" step="0.01" min="0" id="precio_venta" name="precio_venta" value="{{ old('precio_venta', $p->precio_venta ?? '') }}" required>
    </div>
    <div class="campo">
        <label for="precio_compra">Precio de compra (Bs)</label>
        <input type="number" step="0.01" min="0" id="precio_compra" name="precio_compra" value="{{ old('precio_compra', $p->precio_compra ?? '') }}">
    </div>
    <div class="campo">
        <label for="stock_minimo">Stock mínimo *</label>
        <input type="number" min="0" id="stock_minimo" name="stock_minimo" value="{{ old('stock_minimo', $p->stock_minimo ?? 5) }}" required>
    </div>
    <div class="campo">
        <label for="unidad">Unidad *</label>
        <input type="text" id="unidad" name="unidad" value="{{ old('unidad', $p->unidad ?? 'unidad') }}" required>
    </div>
</div>
<div class="campo">
    <label class="campo-check">
        <input type="checkbox" id="control_especial" name="control_especial" value="1" @checked(old('control_especial', $p->control_especial ?? false))>
        <b>Producto con control especial</b> (regulado por normativa SENASAG)
    </label>
</div>
<div class="fila">
    <div class="campo">
        <label for="nota_control">Nota de control</label>
        <input type="text" id="nota_control" name="nota_control" value="{{ old('nota_control', $p->nota_control ?? '') }}" placeholder="Ej: Venta con receta">
    </div>
    <div class="campo">
        <label for="fecha_vencimiento">Fecha de vencimiento</label>
        <input type="date" id="fecha_vencimiento" name="fecha_vencimiento" value="{{ old('fecha_vencimiento', $p?->fecha_vencimiento?->format('Y-m-d')) }}">
    </div>
</div>
<p class="ayuda">El stock inicial es 0; ingresa mercadería desde <b>Entradas</b> (o como <b>lote</b> si es un pez).</p>
