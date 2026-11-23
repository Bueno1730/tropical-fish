// Tropical Fish — utilidades de interfaz

// Confirmación en formularios con data-confirm
document.addEventListener('submit', function (e) {
    const form = e.target;
    if (form.dataset && form.dataset.confirm && !window.confirm(form.dataset.confirm)) {
        e.preventDefault();
    }
});

// Líneas dinámicas para entradas (y formularios similares)
function inicializarLineas(tablaId, tplId, btnId, totalId, itemsPrevios) {
    const tabla = document.getElementById(tablaId).querySelector('tbody');
    const tpl = document.getElementById(tplId);
    const btn = document.getElementById(btnId);
    const total = document.getElementById(totalId);
    let idx = 0;

    function recalcular() {
        let suma = 0;
        tabla.querySelectorAll('tr').forEach(tr => {
            const cant = parseFloat(tr.querySelector('.item-cantidad')?.value) || 0;
            const precio = parseFloat(tr.querySelector('.item-precio')?.value) || 0;
            const sub = cant * precio;
            const celda = tr.querySelector('.item-subtotal');
            if (celda) celda.textContent = 'Bs ' + sub.toFixed(2);
            suma += sub;
        });
        if (total) total.textContent = 'Bs ' + suma.toFixed(2);
    }

    function agregar(datos) {
        const fila = tpl.content.cloneNode(true);
        fila.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace('IDX', idx);
        });
        const tr = fila.querySelector('tr');
        tabla.appendChild(tr);
        if (datos) {
            tr.querySelector('.item-producto').value = datos.producto_id;
            tr.querySelector('.item-cantidad').value = datos.cantidad;
            tr.querySelector('.item-precio').value = datos.precio_compra;
        }
        tr.querySelector('.quitar-item').addEventListener('click', () => { tr.remove(); recalcular(); });
        tr.addEventListener('input', recalcular);
        idx++;
        recalcular();
    }

    btn.addEventListener('click', () => agregar());

    if (itemsPrevios && itemsPrevios.length) {
        itemsPrevios.forEach(d => agregar(d));
    } else {
        agregar();
    }
}
