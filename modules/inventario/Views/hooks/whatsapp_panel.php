<div class="space-y-2 mt-2">
    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 pl-1">Catálogo de Productos</p>
    <div class="relative">
        <input type="text" id="wa-product-search" onkeyup="buscarProductoEnPanel(this.value)" placeholder="Buscar producto por SKU o nombre..." class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none shadow-sm pr-8">
        <i class="fas fa-search text-gray-400 absolute top-1/2 right-3 transform -translate-y-1/2"></i>
    </div>
    <div id="wa-product-results" class="space-y-2 mt-2 max-h-64 overflow-y-auto custom-scrollbar pr-1">
        <!-- Los resultados de la búsqueda aparecerán aquí -->
    </div>
</div>
<script>
    // Envolvemos todo en una función autoejecutable (IIFE) para asegurar que se ejecute
    // cuando se inyecta el HTML dinámicamente.
    (function() {
        let searchTimeout;
        const baseUrl = "<?= $base_url ?>";
        const placeholderUrl = "<?= $placeholder_url ?>";

        /**
         * Función reutilizable para renderizar una lista de productos en el panel.
         * @param {Array} productos - El array de objetos de producto.
         * @param {HTMLElement} container - El elemento contenedor donde se insertará el HTML.
         */
        function renderizarProductos(productos, container) {
            container.innerHTML = "";
            if (!productos || productos.length === 0) {
                container.innerHTML = '<div class="text-center text-xs text-gray-400 py-3">No se encontraron productos.</div>';
                return;
            }

            productos.forEach(p => {
                const imageUrl = p.imagen ? baseUrl + p.imagen : placeholderUrl;
                const infoMessage = `Hola, te comparto la información del producto:\n\n*${p.nombre}*\n*Precio:* $${parseFloat(p.precio).toFixed(2)}\n*Disponibilidad:* ${p.stock > 0 ? 'Disponible' : 'Agotado'}`;
                const imageMessageUrl = p.imagen ? baseUrl + p.imagen : '';

                // Creamos los elementos con JavaScript puro para evitar errores de sintaxis
                const div = document.createElement('div');
                div.className = 'bg-white p-3 rounded-lg border border-gray-100 shadow-sm';
                div.innerHTML = `
                    <div class="flex items-center gap-3">
                        <img src="${imageUrl}" class="w-12 h-12 object-cover rounded border bg-gray-50">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-800 truncate">${p.nombre}</p>
                            <p class="text-xs text-gray-500 font-mono">${p.sku} | <span class="font-sans font-bold text-green-600">$${parseFloat(p.precio).toFixed(2)}</span></p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 mt-3 text-xs">
                        <button data-action="send-info" class="bg-gray-100 hover:bg-indigo-50 text-gray-600 hover:text-indigo-600 font-medium py-1.5 px-2 rounded-md transition"><i class="fas fa-info-circle mr-1"></i> Info</button>
                        <button data-action="send-image" class="bg-gray-100 hover:bg-indigo-50 text-gray-600 hover:text-indigo-600 font-medium py-1.5 px-2 rounded-md transition"><i class="fas fa-image mr-1"></i> Foto</button>
                    </div>`;
                
                div.querySelector('[data-action="send-info"]').addEventListener('click', () => enviarMensajeAsistido(infoMessage));
                div.querySelector('[data-action="send-image"]').addEventListener('click', () => enviarMensajeAsistido('', imageMessageUrl));
                
                container.appendChild(div);
            });
        }

        // Hacemos la función global para que el onkeyup la encuentre
        window.buscarProductoEnPanel = function(query) {
            clearTimeout(searchTimeout);
            const resultsContainer = document.getElementById("wa-product-results");
            if (query.length < 2) {
                // Si se borra la búsqueda, volvemos a mostrar los productos iniciales
                const productosIniciales = <?= json_encode($productos_iniciales) ?>;
                renderizarProductos(productosIniciales, resultsContainer);
                return;
            }
            resultsContainer.innerHTML = `<div class="text-center text-xs text-gray-400 py-3"><i class="fas fa-spinner fa-spin"></i> Buscando...</div>`;
            searchTimeout = setTimeout(() => {
                fetch("<?= base_url('inventario/ajax_buscar') ?>?q=" + encodeURIComponent(query))
                    .then(res => res.json())
                    .then(data => {
                        renderizarProductos(data.productos, resultsContainer);
                    });
            }, 300);
        }

        // Carga inicial de productos
        const productosIniciales = <?= json_encode($productos_iniciales) ?>;
        const resultsContainer = document.getElementById("wa-product-results");
        if (resultsContainer) {
            renderizarProductos(productosIniciales, resultsContainer);
        }
    })();
</script>