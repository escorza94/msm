<form action="<?= base_url('inventario/ingresos/guardar') ?>" method="POST" class="max-w-5xl mx-auto mt-6" id="form-ingreso">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-truck-loading text-indigo-500 mr-3"></i> Registrar Ingreso de Mercancía</h2>
            <p class="text-sm text-gray-500 mt-1">Añade productos al inventario desde una compra o ajuste.</p>
        </div>
        <a href="<?= base_url('inventario/ingresos') ?>" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 font-medium transition text-sm flex items-center shadow-sm">
            <i class="fas fa-arrow-left mr-2 text-gray-400"></i> Volver al Historial
        </a>
    </div>

    <!-- Datos Generales del Ingreso -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3 mb-4"><i class="fas fa-file-invoice mr-2 text-gray-400"></i> Información General</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="proveedor_id" class="block text-sm font-medium text-gray-700 mb-1">Proveedor (Opcional)</label>
                <select id="proveedor_id" name="proveedor_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none shadow-sm">
                    <option value="">-- Sin proveedor --</option>
                    <?php foreach($proveedores as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="referencia_factura" class="block text-sm font-medium text-gray-700 mb-1">N° de Factura o Referencia</label>
                <input type="text" id="referencia_factura" name="referencia_factura" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none shadow-sm" placeholder="Ej. F-12345">
            </div>
            <div class="md:col-span-2">
                <label for="notas" class="block text-sm font-medium text-gray-700 mb-1">Notas Adicionales</label>
                <textarea id="notas" name="notas" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none resize-y" placeholder="Ej. Mercancía de la orden de compra #552"></textarea>
            </div>
        </div>
    </div>

    <!-- Buscador y Lista de Productos -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3 mb-4"><i class="fas fa-boxes mr-2 text-gray-400"></i> Productos a Ingresar</h3>
        
        <!-- Buscador -->
        <div class="relative mb-4">
            <input type="text" id="product-search" placeholder="Buscar producto por SKU o nombre para agregarlo..." class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none shadow-sm pr-10">
            <i class="fas fa-search text-gray-400 absolute top-1/2 right-4 transform -translate-y-1/2"></i>
            <div id="product-results" class="absolute z-10 w-full bg-white border border-gray-200 rounded-b-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto"></div>
        </div>

        <!-- Tabla de productos a ingresar -->
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <th class="p-3 font-medium">Producto</th>
                        <th class="p-3 font-medium w-32">Cantidad</th>
                        <th class="p-3 font-medium w-40">Costo Unitario</th>
                        <th class="p-3 font-medium w-40 text-right">Subtotal</th>
                        <th class="p-3 font-medium w-16 text-center"></th>
                    </tr>
                </thead>
                <tbody id="lista-productos-ingreso" class="text-sm text-gray-700 divide-y divide-gray-100">
                    <!-- Los productos se añaden aquí con JS -->
                    <tr id="fila-vacia">
                        <td colspan="5" class="p-8 text-center text-gray-400">
                            <i class="fas fa-search text-2xl mb-2"></i><br>
                            Busca y selecciona productos para comenzar.
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-200">
                        <td colspan="3" class="p-3 text-right font-bold text-gray-600">COSTO TOTAL DEL INGRESO:</td>
                        <td id="costo-total-ingreso" class="p-3 text-right font-black text-xl text-green-600">$0.00</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Botón de Guardar -->
    <div class="mt-6 flex justify-end">
        <button type="submit" class="px-6 py-3 bg-green-600 text-white font-bold rounded-lg shadow-md hover:bg-green-700 transition flex items-center">
            <i class="fas fa-check-circle mr-2"></i> Confirmar y Actualizar Stock
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('product-search');
    const resultsContainer = document.getElementById('product-results');
    const productListBody = document.getElementById('lista-productos-ingreso');
    const filaVacia = document.getElementById('fila-vacia');
    const costoTotalEl = document.getElementById('costo-total-ingreso');
    let searchTimeout;

    // Buscar productos con AJAX
    searchInput.addEventListener('keyup', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            resultsContainer.innerHTML = '';
            resultsContainer.classList.add('hidden');
            return;
        }

        resultsContainer.innerHTML = '<div class="p-4 text-center text-xs text-gray-400"><i class="fas fa-spinner fa-spin"></i> Buscando...</div>';
        resultsContainer.classList.remove('hidden');

        searchTimeout = setTimeout(() => {
            fetch(`<?= base_url('inventario/ajax_buscar') ?>?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    resultsContainer.innerHTML = '';
                    if (data.productos && data.productos.length > 0) {
                        data.productos.forEach(p => {
                            const div = document.createElement('div');
                            div.className = 'p-3 hover:bg-indigo-50 cursor-pointer border-b border-gray-100';
                            div.innerHTML = `<p class="font-bold text-sm">${p.nombre}</p><p class="text-xs text-gray-500">${p.sku}</p>`;
                            div.addEventListener('click', () => agregarProducto(p));
                            resultsContainer.appendChild(div);
                        });
                    } else {
                        resultsContainer.innerHTML = '<div class="p-4 text-center text-xs text-gray-400">No se encontraron productos.</div>';
                    }
                });
        }, 300);
    });

    // Agregar producto a la lista
    function agregarProducto(producto) {
        searchInput.value = '';
        resultsContainer.classList.add('hidden');

        // Evitar duplicados
        if (document.getElementById(`fila-producto-${producto.id}`)) {
            alert('Este producto ya está en la lista.');
            return;
        }

        if (filaVacia) filaVacia.style.display = 'none';

        const newRow = document.createElement('tr');
        newRow.id = `fila-producto-${producto.id}`;
        newRow.innerHTML = `
            <td class="p-3">
                <input type="hidden" name="productos[${producto.id}][id]" value="${producto.id}">
                <p class="font-bold">${producto.nombre}</p>
                <p class="text-xs text-gray-500 font-mono">${producto.sku}</p>
            </td>
            <td class="p-3">
                <input type="number" name="productos[${producto.id}][cantidad]" value="1" min="1" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-1 focus:ring-indigo-500 outline-none" oninput="recalcularTotales()">
            </td>
            <td class="p-3">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">$</span>
                    <input type="number" name="productos[${producto.id}][costo]" value="0.00" step="0.01" min="0" class="w-full border border-gray-300 rounded-md px-3 py-2 pl-7 text-sm focus:ring-1 focus:ring-indigo-500 outline-none" oninput="recalcularTotales()">
                </div>
            </td>
            <td class="p-3 text-right font-bold subtotal-producto">$0.00</td>
            <td class="p-3 text-center">
                <button type="button" class="w-8 h-8 rounded bg-red-50 text-red-500 hover:bg-red-100" onclick="this.closest('tr').remove(); recalcularTotales(); if(productListBody.rows.length === 0 && filaVacia) filaVacia.style.display = 'table-row';">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        `;
        productListBody.appendChild(newRow);
        recalcularTotales();
    }

    // Recalcular totales
    window.recalcularTotales = function() {
        let costoTotal = 0;
        const filas = productListBody.querySelectorAll('tr');

        filas.forEach(fila => {
            if (fila.id === 'fila-vacia') return;
            const cantidad = parseFloat(fila.querySelector('input[name*="[cantidad]"]').value) || 0;
            const costo = parseFloat(fila.querySelector('input[name*="[costo]"]').value) || 0;
            const subtotal = cantidad * costo;
            
            fila.querySelector('.subtotal-producto').innerText = `$${subtotal.toFixed(2)}`;
            costoTotal += subtotal;
        });

        costoTotalEl.innerText = `$${costoTotal.toFixed(2)}`;
    }

    // Evitar que el Enter en el buscador envíe el formulario
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') e.preventDefault();
    });
});
</script>