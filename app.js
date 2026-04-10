// Manejo dinámico del formulario de venta: filas, subtotales y total.
const detalleVenta = document.getElementById('detalleVenta');
const agregarFilaBtn = document.getElementById('agregarFila');
const totalVentaSpan = document.getElementById('totalVenta');
const totalInput = document.getElementById('totalInput');
const formVenta = document.getElementById('formVenta');

function crearOpcionesProductos() {
    return productos.map((p) => `<option value="${p.id}" data-precio="${p.precio_venta}" data-stock="${p.stock}">${p.nombre}</option>`).join('');
}

function agregarFila() {
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <select name="producto_id[]" class="producto-select" required>
                <option value="">Seleccione</option>
                ${crearOpcionesProductos()}
            </select>
        </td>
        <td class="precio">$0.00</td>
        <td class="stock">0</td>
        <td><input type="number" min="1" value="1" name="cantidad[]" class="cantidad-input" required></td>
        <td class="subtotal">$0.00</td>
        <td><button type="button" class="btn danger quitar">X</button></td>
    `;

    detalleVenta.appendChild(tr);
    recalcular();
}

function recalcular() {
    let total = 0;

    document.querySelectorAll('#detalleVenta tr').forEach((tr) => {
        const select = tr.querySelector('.producto-select');
        const cantidadInput = tr.querySelector('.cantidad-input');
        const precioTd = tr.querySelector('.precio');
        const stockTd = tr.querySelector('.stock');
        const subtotalTd = tr.querySelector('.subtotal');

        const selected = select.options[select.selectedIndex];
        const precio = parseFloat(selected?.dataset?.precio || 0);
        const stock = parseInt(selected?.dataset?.stock || 0, 10);
        const cantidad = parseInt(cantidadInput.value || 0, 10);

        cantidadInput.max = stock > 0 ? stock : 1;
        if (cantidad > stock && stock > 0) {
            cantidadInput.value = stock;
        }

        const subtotal = precio * parseInt(cantidadInput.value || 0, 10);

        precioTd.textContent = `$${precio.toFixed(2)}`;
        stockTd.textContent = stock;
        subtotalTd.textContent = `$${subtotal.toFixed(2)}`;

        total += subtotal;
    });

    totalVentaSpan.textContent = total.toFixed(2);
    totalInput.value = total.toFixed(2);
}

agregarFilaBtn?.addEventListener('click', agregarFila);

detalleVenta?.addEventListener('change', (e) => {
    if (e.target.classList.contains('producto-select') || e.target.classList.contains('cantidad-input')) {
        recalcular();
    }
});

detalleVenta?.addEventListener('click', (e) => {
    if (e.target.classList.contains('quitar')) {
        e.target.closest('tr')?.remove();
        recalcular();
    }
});

formVenta?.addEventListener('submit', (e) => {
    if (!detalleVenta.querySelector('tr')) {
        alert('Debe agregar al menos un producto.');
        e.preventDefault();
    }
});

agregarFila();
