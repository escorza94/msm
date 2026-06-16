<div class="max-w-7xl mx-auto mt-4 h-[calc(100vh-100px)] flex flex-col">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-cash-register text-blue-500 mr-2"></i> Punto de Venta (POS)</h2>
            <p class="text-sm text-gray-500 mt-1">Crea cotizaciones o registra ventas directas</p>
        </div>
        <a href="<?= base_url('pos/historial') ?>" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 font-medium transition text-sm flex items-center shadow-sm">
            <i class="fas fa-history mr-2 text-gray-400"></i> Ver Historial
        </a>
    </div>

    <!-- Contenedor Principal Split -->
    <div class="flex-1 grid grid-cols-1 lg:grid-cols-3 gap-4 min-h-0 overflow-hidden">
        
        <!-- Lado Izquierdo: Catálogo de Productos -->
        <div class="lg:col-span-2 flex flex-col bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex gap-2">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    <input type="text" id="buscador" class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm" placeholder="Buscar por Nombre o SKU..." onkeyup="filtrarProductos()">
                </div>
            </div>
            
            <!-- Grid de Productos -->
            <div class="flex-1 overflow-y-auto p-4 bg-gray-50/50">
                <div id="grid-productos" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                    <!-- Los productos se renderizan por JS -->
                </div>
            </div>
        </div>

        <!-- Lado Derecho: Carrito (Ticket) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col overflow-y-auto">
            
            <!-- Selector de Cliente -->
            <div class="p-4 border-b border-gray-100 bg-blue-50/30">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2"><i class="fas fa-user text-blue-400 mr-1"></i> Cliente</label>
                <select id="cliente_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none shadow-sm bg-white">
                    <option value="" data-lat="" data-lon="">-- Cliente de Mostrador (Público en General) --</option>
                    <?php foreach($clientes as $c): ?>
                        <option value="<?= $c['id'] ?>" data-lat="<?= $c['latitud'] ?? '' ?>" data-lon="<?= $c['longitud'] ?? '' ?>" <?= (isset($cliente_seleccionado) && $cliente_seleccionado == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['nombre']) ?> <?= $c['telefono'] ? '('.$c['telefono'].')' : '' ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="mt-3">
                    <label class="flex items-center text-xs font-medium text-blue-700 cursor-pointer select-none w-max">
                        <input type="checkbox" id="chk-custom-address" class="mr-2 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed" onchange="toggleCustomAddress()">
                        <i class="fas fa-map-pin mr-1"></i> Enviar a una ubicación diferente
                    </label>
                </div>
                <div id="custom-address-container" class="hidden mt-2 p-3 bg-white border border-blue-100 rounded-lg shadow-inner">
                    <p class="text-[10px] uppercase font-bold text-gray-500 mb-2">Selecciona la ubicación de entrega en el mapa</p>
                    <div id="mapa-destino" class="w-full h-40 bg-gray-200 rounded-lg mb-2 z-10 border border-gray-300"></div>
                    <div class="flex gap-2">
                        <input type="text" id="custom-lat" placeholder="Latitud" class="w-1/2 text-xs border border-gray-300 bg-gray-50 rounded px-2 py-1 outline-none" readonly>
                        <input type="text" id="custom-lon" placeholder="Longitud" class="w-1/2 text-xs border border-gray-300 bg-gray-50 rounded px-2 py-1 outline-none" readonly>
                    </div>
                    <textarea id="custom-direccion" rows="2" class="w-full mt-2 text-xs border border-gray-300 bg-gray-50 rounded px-2 py-1 outline-none resize-none" placeholder="Dirección extraída..." readonly></textarea>
                </div>
            </div>

            <!-- Lista de Items en Carrito -->
            <div class="flex-1 p-2 min-h-[200px]" id="carrito-items">
                <div class="h-full flex flex-col items-center justify-center text-gray-400 opacity-60">
                    <i class="fas fa-shopping-cart text-4xl mb-3"></i>
                    <p class="text-sm">El carrito está vacío</p>
                </div>
            </div>

            <!-- Resumen y Totales -->
            <div class="border-t border-gray-100 bg-gray-50 p-4">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span class="font-medium" id="lbl-subtotal">$0.00</span>
                    </div>
                    <!-- Selector de Tarifa Logística -->
                    <div class="flex justify-between items-center text-gray-600 group mb-1 mt-2">
                        <span class="text-xs uppercase font-bold text-gray-500"><i class="fas fa-route text-gray-400 mr-1"></i> Tarifa Envío</span>
                        <select id="select-tarifa" class="w-1/2 text-right border border-gray-300 rounded px-2 py-1 text-xs outline-none focus:ring-1 focus:ring-blue-500 bg-white disabled:bg-gray-100 disabled:opacity-60 disabled:cursor-not-allowed" onchange="calcularTarifaEnvio()">
                            <option value="">Manual / Personalizado</option>
                            <?php if(isset($tarifas_envio)): foreach($tarifas_envio as $t): ?>
                                <option value="<?= $t['id'] ?>" data-tipo="<?= $t['tipo'] ?>" data-base="<?= $t['precio_base'] ?>" data-kmbase="<?= $t['km_base'] ?>" data-kmextra="<?= $t['precio_km_extra'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                    <div class="flex justify-between items-start text-gray-600 group">
                        <span class="mt-1"><i class="fas fa-truck text-gray-400 mr-1"></i> Importe Envío</span>
                        <div class="flex flex-col items-end"><input type="number" id="input-envio" class="w-20 text-right border border-gray-300 rounded px-2 py-1 text-sm outline-none focus:ring-1 focus:ring-blue-500 disabled:bg-gray-100 disabled:opacity-60 disabled:cursor-not-allowed" value="0" min="0" step="0.01" onchange="document.getElementById('select-tarifa').value=''; calcularTotales();"><div id="info-distancia" class="text-[9px] text-blue-500 font-medium hidden mt-1"></div></div>
                    </div>
                    
                    <!-- Descuentos y Cupones -->
                    <div class="flex justify-between items-center text-red-500 mt-2">
                        <div class="flex items-center gap-1">
                            <input type="text" id="input-cupon" placeholder="Código..." class="w-20 text-center border border-red-200 rounded px-1.5 py-1 text-xs outline-none focus:ring-1 focus:ring-red-500 uppercase bg-red-50 placeholder-red-300">
                            <button onclick="aplicarCupon()" class="bg-red-500 text-white hover:bg-red-600 p-1 rounded transition text-xs shadow-sm" title="Aplicar Cupón"><i class="fas fa-check"></i></button>
                            <span id="badge-promo-auto" class="hidden ml-1 px-2 py-0.5 bg-green-100 text-green-700 rounded text-[9px] font-bold uppercase shadow-sm"><i class="fas fa-magic mr-1"></i> Promo Aplicada</span>
                        </div>
                        <div class="flex items-center"><span class="text-xs font-medium mr-2">Descuento</span><input type="number" id="input-descuento" class="w-20 text-right border border-gray-300 text-red-500 rounded px-2 py-1 text-sm outline-none focus:ring-1 focus:ring-red-500 bg-white" value="0" min="0" step="0.01" onchange="document.getElementById('input-cupon').value = ''; document.getElementById('input-descuento').removeAttribute('data-auto'); document.getElementById('badge-promo-auto').classList.add('hidden'); calcularTotales()"></div>
                    </div>
                    <div class="pt-2 mt-2 border-t border-gray-200 flex justify-between items-center">
                        <span class="text-lg font-bold text-gray-800">Total</span>
                        <span class="text-2xl font-black text-blue-600" id="lbl-total">$0.00</span>
                    </div>
                    
                    <!-- Cobro Inteligente -->
                    <div class="mt-3 pt-3 border-t border-gray-200">
                        <div class="flex justify-between items-center bg-gray-100 p-2 rounded-lg border border-gray-200">
                            <span class="text-sm font-bold text-gray-700"><i class="fas fa-hand-holding-usd text-green-500 mr-1"></i> Recibido Hoy</span>
                            <input type="number" id="input-recibido" class="w-28 text-right font-black text-green-600 border border-gray-300 rounded px-2 py-1 text-lg outline-none focus:ring-2 focus:ring-green-500 bg-white shadow-inner" placeholder="0.00" min="0" step="0.01" onkeyup="calcularCambio()" onchange="calcularCambio()">
                        </div>
                        <div class="flex justify-between items-center mt-2 px-1">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider" id="lbl-texto-cambio">Estado</span>
                            <span class="text-sm font-black text-gray-800" id="lbl-valor-cambio">-</span>
                        </div>
                    </div>
                </div>

                <!-- Opciones de Cobro -->
                <div class="mt-4 border-t border-gray-200 pt-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1"><i class="fas fa-money-bill-wave text-green-500 mr-1"></i> Método Pago</label>
                            <select id="metodo_pago" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none shadow-sm bg-white" onchange="autoSeleccionarCuenta()">
                                <option value="Efectivo">Efectivo</option>
                                <option value="Tarjeta">Tarjeta (Créd./Déb.)</option>
                                <option value="Transferencia">Transferencia</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1"><i class="fas fa-wallet text-blue-500 mr-1"></i> Cuenta Destino</label>
                            <select id="cuenta_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none shadow-sm bg-white">
                                <?php if(isset($cuentas_finanzas)): foreach($cuentas_finanzas as $cf): ?>
                                    <option value="<?= $cf['id'] ?>"><?= htmlspecialchars($cf['nombre']) ?></option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Notas Internas -->
                <div class="mt-4 border-t border-gray-200 pt-3">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1"><i class="fas fa-sticky-note text-gray-400 mr-1"></i> Notas</label>
                    <textarea id="input-notas" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none shadow-sm resize-none" placeholder="Observaciones de la venta o cotización..."></textarea>
                </div>

                <!-- Botones de Acción -->
                <div class="grid grid-cols-2 gap-3 mt-4">
                    <button onclick="procesar('cotizacion')" class="w-full py-3 bg-white border-2 border-gray-800 text-gray-800 rounded-lg hover:bg-gray-800 hover:text-white transition font-bold text-sm shadow-sm flex flex-col items-center justify-center">
                        <i class="fas fa-file-pdf mb-1"></i> Guardar Cotización
                    </button>
                    <button onclick="procesar('venta')" class="w-full py-3 bg-green-600 border-2 border-green-600 text-white rounded-lg hover:bg-green-700 hover:border-green-700 transition font-bold text-sm shadow-sm flex flex-col items-center justify-center">
                        <i class="fas fa-check-circle mb-1"></i> Confirmar Venta
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    /* Integración de Select2 con Tailwind */
    .select2-container .select2-selection--single {
        height: 38px !important;
        border-color: #d1d5db !important;
        border-radius: 0.5rem !important;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
</style>

<script>
// Cargar base de datos de productos desde PHP a JS
const catalogo = <?= json_encode($productos) ?>;
const configLogistica = <?= json_encode($config_logistica ?? []) ?>;
const googleMapsKey = "<?= $google_maps_key ?? '' ?>";
const cuentasFinanzas = <?= json_encode($cuentas_finanzas ?? []) ?>;
const promosAutomaticas = <?= json_encode($promos_automaticas ?? []) ?>;
const promocionesActivas = <?= json_encode($promociones_activas ?? []) ?>;
let carrito = [];
let mapaDestino = null, marcadorDestino = null;

$(document).ready(function() {
    $('#cliente_id').select2({ width: '100%' });
    $('#cliente_id').on('change', function () { verificarClienteMostrador(); });
});

document.addEventListener('DOMContentLoaded', () => {
    filtrarProductos(); // Render inicial
    verificarClienteMostrador(); // Validar al cargar la página
});

function verificarClienteMostrador() {
    const clienteId = document.getElementById('cliente_id').value;
    const chkCustom = document.getElementById('chk-custom-address');
    const selTarifa = document.getElementById('select-tarifa');
    const inputEnvio = document.getElementById('input-envio');

    if (!clienteId) {
        chkCustom.checked = false; chkCustom.disabled = true;
        document.getElementById('custom-address-container').classList.add('hidden');
        selTarifa.value = ""; selTarifa.disabled = true;
        inputEnvio.value = "0"; inputEnvio.disabled = true;
    } else {
        chkCustom.disabled = false;
        selTarifa.disabled = false;
        inputEnvio.disabled = false;
    }
    calcularTarifaEnvio();
}

function filtrarProductos() {
    const q = document.getElementById('buscador').value.toLowerCase();
    const contenedor = document.getElementById('grid-productos');
    contenedor.innerHTML = '';

    // Filtrar y mostrar Promociones
    const promosFiltradas = promocionesActivas.filter(p => p.nombre.toLowerCase().includes(q) || (p.codigo_cupon && p.codigo_cupon.toLowerCase().includes(q)));
    promosFiltradas.forEach(p => {
        let desc = p.tipo === 'porcentaje' ? `-${parseFloat(p.valor)}%` : `-$${parseFloat(p.valor).toLocaleString('en-US', {minimumFractionDigits: 0})}`;
        
        let listaProductosHTML = '';
        let precioComboHTML = '';
        
        // Calcular precio del Combo basado en los productos requeridos
        if (p.productos_requeridos && p.productos_requeridos.trim() !== '' && p.productos_requeridos !== 'null') {
            try {
                const req = JSON.parse(p.productos_requeridos);
                if (Array.isArray(req) && req.length > 0) {
                    let totalOriginal = 0;
                    let nombresProductos = [];
                    req.forEach(reqId => {
                        const prod = catalogo.find(c => c.id == reqId);
                        if (prod) {
                            totalOriginal += parseFloat(prod.precio);
                            nombresProductos.push(prod.nombre);
                        }
                    });
                    if (totalOriginal > 0) {
                        let montoDescuento = p.tipo === 'porcentaje' ? (totalOriginal * parseFloat(p.valor)) / 100 : parseFloat(p.valor);
                        if(montoDescuento > totalOriginal) montoDescuento = totalOriginal; // Evita precios negativos
                        let totalFinal = totalOriginal - montoDescuento;
                        
                        listaProductosHTML = `<div class="mt-2 pt-2 border-t border-red-200/50">
                            <p class="text-[9px] font-bold text-red-500 uppercase mb-1">Incluye:</p>
                            <ul class="text-[10px] text-gray-700 space-y-0.5 leading-tight list-disc pl-3">
                                ${nombresProductos.map(n => `<li>${n}</li>`).join('')}
                            </ul>
                        </div>`;
                        
                        precioComboHTML = `<div class="mt-2 bg-white p-1.5 rounded shadow-sm text-center border border-red-100">
                            <p class="text-[10px] text-gray-400 line-through mb-0.5">$${totalOriginal.toLocaleString('en-US', {minimumFractionDigits: 2})}</p>
                            <p class="text-xs font-black text-red-600">Llévatelo por $${totalFinal.toLocaleString('en-US', {minimumFractionDigits: 2})}</p>
                        </div>`;
                    }
                }
            } catch(e) {}
        } else if (parseFloat(p.monto_minimo) > 0) {
             precioComboHTML = `<div class="mt-2 bg-white/60 p-1.5 rounded text-center border border-red-100/50">
                 <p class="text-[10px] text-gray-500 mb-0.5">En compras mayores a</p>
                 <p class="text-xs font-black text-red-600">$${parseFloat(p.monto_minimo).toLocaleString('en-US', {minimumFractionDigits: 2})}</p>
             </div>`;
        }

        const html = `
            <div class="bg-gradient-to-br from-red-50 to-red-100 border border-red-200 rounded-xl overflow-hidden cursor-pointer transition transform hover:-translate-y-1 relative flex flex-col shadow-sm" onclick="aplicarPromoDesdeTarjeta(${p.id})">
                <div class="absolute top-2 right-2 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm"><i class="fas fa-tags"></i> Oferta</div>
                <div class="h-20 bg-red-200/50 w-full flex items-center justify-center text-red-500">
                    <span class="text-3xl font-black drop-shadow-sm">${desc}</span>
                </div>
                <div class="p-3 flex-1 flex flex-col">
                    <p class="text-[10px] text-red-500 font-mono mb-1 font-bold">${p.codigo_cupon ? '<i class="fas fa-barcode"></i> ' + p.codigo_cupon : '<i class="fas fa-magic"></i> AUTO PROMO'}</p>
                    <h4 class="text-xs font-bold text-gray-800 leading-tight flex-1">${p.nombre}</h4>
                    ${listaProductosHTML}
                    ${precioComboHTML}
                    <p class="text-[10px] text-gray-500 font-bold bg-white/60 text-center py-1 rounded mt-2"><i class="fas fa-hand-pointer mr-1"></i> Clic para armar</p>
                </div>
            </div>
        `;
        contenedor.insertAdjacentHTML('beforeend', html);
    });

    const filtrados = catalogo.filter(p => p.nombre.toLowerCase().includes(q) || p.sku.toLowerCase().includes(q));

    if(filtrados.length === 0) {
        contenedor.innerHTML = '<div class="col-span-full py-10 text-center text-gray-400"><i class="fas fa-box-open text-3xl mb-2"></i><p>No se encontraron productos</p></div>';
        return;
    }

    filtrados.forEach(p => {
        const imgUrl = p.imagen ? `<?= base_url() ?>${p.imagen}` : 'https://placehold.co/150x150/f3f4f6/a1a1aa?text=Sin+Foto';
        const esPocoStock = p.stock > 0 && p.stock <= 3;
        const agotado = p.stock <= 0;
        
        let stockBadge = `<span class="absolute top-2 right-2 bg-green-100 text-green-700 text-[10px] font-bold px-2 py-1 rounded shadow-sm">${p.stock} pz</span>`;
        if (agotado) stockBadge = `<span class="absolute top-2 right-2 bg-red-100 text-red-700 text-[10px] font-bold px-2 py-1 rounded shadow-sm">Agotado</span>`;
        else if (esPocoStock) stockBadge = `<span class="absolute top-2 right-2 bg-yellow-100 text-yellow-700 text-[10px] font-bold px-2 py-1 rounded shadow-sm">¡Solo ${p.stock}!</span>`;

        const html = `
            <div class="bg-white border ${agotado ? 'border-red-200 opacity-60' : 'border-gray-200 hover:border-blue-300 hover:shadow-md'} rounded-xl overflow-hidden cursor-pointer transition transform hover:-translate-y-1 relative flex flex-col" onclick="agregarAlCarrito(${p.id})">
                ${stockBadge}
                <div class="h-32 bg-gray-100 w-full">
                    <img src="${imgUrl}" class="w-full h-full object-cover">
                </div>
                <div class="p-3 flex-1 flex flex-col">
                    <p class="text-[10px] text-gray-500 font-mono mb-1">${p.sku}</p>
                    <h4 class="text-xs font-bold text-gray-800 leading-tight mb-2 flex-1">${p.nombre}</h4>
                    <p class="text-sm font-black text-blue-600">$${parseFloat(p.precio).toLocaleString('en-US', {minimumFractionDigits: 2})}</p>
                </div>
            </div>
        `;
        contenedor.insertAdjacentHTML('beforeend', html);
    });
}

function agregarAlCarrito(id) {
    const producto = catalogo.find(p => p.id == id);
    if (!producto) return;
    
    // Buscar si ya existe en el carrito
    const index = carrito.findIndex(item => item.id == id);
    if (index > -1) {
        carrito[index].cantidad++;
    } else {
        carrito.push({ ...producto, cantidad: 1 });
    }
    
    renderCarrito();
}

function cambiarCantidad(id, delta) {
    const index = carrito.findIndex(item => item.id == id);
    if (index > -1) {
        carrito[index].cantidad += delta;
        if (carrito[index].cantidad <= 0) carrito.splice(index, 1);
        
        // Limpiar descuento manual si reducimos la cantidad de un producto
        if (delta < 0) {
            const inputDescuento = document.getElementById('input-descuento');
            if (!inputDescuento.hasAttribute('data-auto') && parseFloat(inputDescuento.value) > 0) {
                inputDescuento.value = "0";
            }
        }
        
        renderCarrito();
    }
}

function removerDelCarrito(id) {
    carrito = carrito.filter(item => item.id != id);
    
    // Limpiar descuento manual por seguridad al quitar productos por completo
    const inputDescuento = document.getElementById('input-descuento');
    if (!inputDescuento.hasAttribute('data-auto') && parseFloat(inputDescuento.value) > 0) {
        inputDescuento.value = "0";
    }
    
    renderCarrito();
}

function renderCarrito() {
    const contenedor = document.getElementById('carrito-items');
    
    if (carrito.length === 0) {
        contenedor.innerHTML = `
            <div class="h-full flex flex-col items-center justify-center text-gray-400 opacity-60">
                <i class="fas fa-shopping-cart text-4xl mb-3"></i>
                <p class="text-sm">El carrito está vacío</p>
            </div>`;
        calcularTotales();
        return;
    }

    let html = '<div class="space-y-2">';
    carrito.forEach(item => {
        const imgUrl = item.imagen ? `<?= base_url() ?>${item.imagen}` : 'https://placehold.co/150x150/f3f4f6/a1a1aa?text=Img';
        const subtotal = item.precio * item.cantidad;
        
        html += `
            <div class="flex items-center gap-3 p-2 bg-white border border-gray-100 rounded-lg shadow-sm">
                <img src="${imgUrl}" class="w-12 h-12 rounded object-cover border border-gray-100">
                <div class="flex-1 min-w-0">
                    <h5 class="text-xs font-bold text-gray-800 truncate">${item.nombre}</h5>
                    <div class="text-[11px] text-gray-500 font-mono">${item.sku}</div>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="text-gray-500 font-bold text-xs">$</span>
                        <input type="number" value="${item.precio}" class="w-20 text-xs border border-gray-300 rounded px-1 py-0.5 outline-none focus:ring-1 focus:ring-blue-500 font-bold text-blue-600 bg-blue-50/10" onchange="cambiarPrecio(${item.id}, this.value)" min="0" step="0.01">
                    </div>
                </div>
                <div class="flex flex-col items-end gap-1">
                    <button onclick="removerDelCarrito(${item.id})" class="text-red-400 hover:text-red-600 p-1"><i class="fas fa-trash-alt text-xs"></i></button>
                    <div class="flex items-center bg-gray-100 rounded">
                        <button onclick="cambiarCantidad(${item.id}, -1)" class="w-6 h-6 flex items-center justify-center text-gray-600 hover:bg-gray-200 rounded-l">-</button>
                        <span class="w-8 text-center text-xs font-bold">${item.cantidad}</span>
                        <button onclick="cambiarCantidad(${item.id}, 1)" class="w-6 h-6 flex items-center justify-center text-gray-600 hover:bg-gray-200 rounded-r">+</button>
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';
    contenedor.innerHTML = html;
    
    calcularTotales();
}

function cambiarPrecio(id, nuevoPrecio) {
    const index = carrito.findIndex(item => item.id == id);
    if (index > -1) {
        carrito[index].precio = parseFloat(nuevoPrecio) || 0;
        
        // Limpiar descuento manual si alteran los precios desde el carrito
        const inputDescuento = document.getElementById('input-descuento');
        if (!inputDescuento.hasAttribute('data-auto') && parseFloat(inputDescuento.value) > 0) {
            inputDescuento.value = "0";
        }
        
        renderCarrito();
    }
}

function calcularTotales(updateCambio = true) {
    const subtotal = carrito.reduce((acc, item) => acc + (item.precio * item.cantidad), 0);
    const cantidadItems = carrito.reduce((acc, item) => acc + item.cantidad, 0);
    const envio = parseFloat(document.getElementById('input-envio').value) || 0;
    
    const inputDescuento = document.getElementById('input-descuento');
    const inputCupon = document.getElementById('input-cupon').value.trim();
    const badgePromo = document.getElementById('badge-promo-auto');
    
    let promosAplicadas = [];
    
    // Evitar que si el input está vacío (NaN) se rompa la detección
    const valorDescuentoActual = parseFloat(inputDescuento.value) || 0;

    // Auto-detección de promociones (Solo si NO hay un cupón ingresado y el cajero NO escribió un descuento manual)
    if (inputCupon === '' && (valorDescuentoActual === 0 || inputDescuento.hasAttribute('data-auto'))) {
        let descuentoAutoAcumulado = 0;
        
        // Obtener fecha local exacta para evitar problemas de zona horaria
        const dateObj = new Date();
        const hoy = dateObj.getFullYear() + '-' + String(dateObj.getMonth() + 1).padStart(2, '0') + '-' + String(dateObj.getDate()).padStart(2, '0');

        console.log('--- Evaluando Auto-Promociones ---');
        promosAutomaticas.forEach(promo => {
            let aplica = true;
            let multiplicador = 1;
            let subtotalBasePromo = subtotal;
            let motivoFallas = [];
            
            if (promo.fecha_inicio && promo.fecha_inicio > hoy) { aplica = false; motivoFallas.push('Aún no inicia'); }
            if (promo.fecha_fin && promo.fecha_fin < hoy) { aplica = false; motivoFallas.push('Ya expiró'); }
            if (parseFloat(promo.monto_minimo) > 0 && subtotal < parseFloat(promo.monto_minimo)) { aplica = false; motivoFallas.push('No cumple monto mínimo'); }
            if (parseInt(promo.cantidad_minima) > 0 && cantidadItems < parseInt(promo.cantidad_minima)) { aplica = false; motivoFallas.push('No cumple cantidad mínima'); }
            
            if (aplica && promo.productos_requeridos && promo.productos_requeridos.trim() !== '' && promo.productos_requeridos !== 'null') {
                try {
                    const req = JSON.parse(promo.productos_requeridos);
                    if (Array.isArray(req) && req.length > 0) {
                        let minCombosPosibles = 999999;
                        let comboSubtotal = 0;
                        const reqCounts = {};
                        
                        // Agrupar requeridos (Por si el combo pide "2 Sillas" iguales)
                        req.forEach(id => { reqCounts[id] = (reqCounts[id] || 0) + 1; });

                        for (const idReq in reqCounts) {
                            const cantidadRequerida = reqCounts[idReq];
                            const itemEnCarrito = carrito.find(item => item.id == idReq);
                            
                            if (!itemEnCarrito || itemEnCarrito.cantidad < cantidadRequerida) { 
                                minCombosPosibles = 0; 
                                break; 
                            }
                            
                            const combosConEsteItem = Math.floor(itemEnCarrito.cantidad / cantidadRequerida);
                            if (combosConEsteItem < minCombosPosibles) minCombosPosibles = combosConEsteItem;
                            
                            comboSubtotal += (parseFloat(itemEnCarrito.precio) * cantidadRequerida);
                        }

                        if (minCombosPosibles > 0) { 
                            multiplicador = minCombosPosibles; 
                            subtotalBasePromo = comboSubtotal; 
                        } else { 
                            aplica = false; 
                            motivoFallas.push('Faltan productos para armar el paquete'); 
                        }
                    }
                } catch(e) { console.error('Error procesando promo:', e); }
            }
            
            if (aplica) {
                console.log(`✅ ¡Promo aplicada! "${promo.nombre}" - Combos armados: ${multiplicador}`);
                let descCalc = promo.tipo === 'porcentaje' ? (subtotalBasePromo * parseFloat(promo.valor)) / 100 : parseFloat(promo.valor);
                let totalDescPromo = descCalc * multiplicador;
                descuentoAutoAcumulado += totalDescPromo; // Multiplicamos por los combos armados
                promosAplicadas.push(`- ${promo.nombre}: -$${totalDescPromo.toFixed(2)}`);
            } else {
                console.log(`❌ Promo "${promo.nombre}" ignorada por: ${motivoFallas.join(', ')}`);
            }
        });

        // Evitar que el descuento acumulado sea mayor al subtotal de la compra
        descuentoAutoAcumulado = Math.min(descuentoAutoAcumulado, subtotal);

        if (descuentoAutoAcumulado > 0) {
            inputDescuento.value = descuentoAutoAcumulado.toFixed(2);
            inputDescuento.setAttribute('data-auto', '1');
            badgePromo.classList.remove('hidden');
        } else {
            inputDescuento.value = "0";
            inputDescuento.removeAttribute('data-auto');
            badgePromo.classList.add('hidden');
        }
    } else if (inputCupon !== '') {
        const promoCup = promocionesActivas.find(p => p.codigo_cupon === inputCupon);
        if (promoCup && valorDescuentoActual > 0) {
            promosAplicadas.push(`- Cupón ${inputCupon} (${promoCup.nombre}): -$${valorDescuentoActual.toFixed(2)}`);
        }
    } else if (valorDescuentoActual > 0 && !inputDescuento.hasAttribute('data-auto')) {
        promosAplicadas.push(`- Descuento Manual / Especial: -$${valorDescuentoActual.toFixed(2)}`);
    }

    window.promosAplicadasList = promosAplicadas;

    const descuento = parseFloat(document.getElementById('input-descuento').value) || 0;
    
    const total = subtotal + envio - descuento;
    
    document.getElementById('lbl-subtotal').innerText = `$${subtotal.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
    document.getElementById('lbl-total').innerText = `$${Math.max(0, total).toLocaleString('en-US', {minimumFractionDigits: 2})}`;
    
    if(updateCambio) calcularCambio();

    return { subtotal, envio, descuento, total: Math.max(0, total) };
}

async function aplicarCupon() {
    const codigo = document.getElementById('input-cupon').value.trim();
    if (!codigo) { Swal.fire('Atención', 'Ingresa un código de cupón', 'warning'); return; }
    if (carrito.length === 0) { Swal.fire('Atención', 'Agrega productos al carrito primero', 'warning'); return; }

    const totales = calcularTotales(false);
    const cantidadItems = carrito.reduce((acc, item) => acc + item.cantidad, 0);

    try {
        const res = await fetch('<?= base_url('promociones/validarCupon') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ codigo: codigo, subtotal: totales.subtotal, cantidad_items: cantidadItems, carrito: carrito })
        });
        const data = await res.json();
        
        if (data.status === 'success') {
            document.getElementById('input-descuento').value = data.descuento_calculado.toFixed(2);
            calcularTotales();
            
            // Toast notification de éxito
            Swal.fire({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, icon: 'success', title: data.mensaje });
        } else {
            Swal.fire('Atención', data.error, 'warning');
        }
    } catch (e) { Swal.fire('Error', 'Error al validar el cupón', 'error'); }
}

function aplicarPromoDesdeTarjeta(id) {
    const promo = promocionesActivas.find(p => p.id == id);
    if(!promo) return;
    
    // 1. Agregamos los productos obligatorios del combo automáticamente
    if (promo.productos_requeridos && promo.productos_requeridos.trim() !== '' && promo.productos_requeridos !== 'null') {
        try {
            const req = JSON.parse(promo.productos_requeridos);
            if (Array.isArray(req) && req.length > 0) {
                req.forEach(reqId => {
                    const prod = catalogo.find(c => c.id == reqId);
                    if (prod) {
                        const index = carrito.findIndex(item => item.id == reqId);
                        if (index === -1) {
                            carrito.push({ ...prod, cantidad: 1 });
                        }
                        else {
                            carrito[index].cantidad++; // Si le vuelven a dar clic, sumamos la cantidad (permite llevar 2 combos iguales)
                        }
                    }
                });
            }
        } catch(e) {}
    }
    
    // 2. Delegar la validación y cálculo del descuento a las funciones principales
    if (promo.codigo_cupon && promo.codigo_cupon.trim() !== '') {
        document.getElementById('input-cupon').value = promo.codigo_cupon;
        renderCarrito();
        aplicarCupon();
    } else {
        document.getElementById('input-cupon').value = '';
        const inputDescuento = document.getElementById('input-descuento');
        
        // Limpiamos el descuento manual para permitir que la Auto-Detección haga su trabajo
        if (!inputDescuento.hasAttribute('data-auto')) {
            inputDescuento.value = "0";
        }
        
        renderCarrito(); // Esto recalcula totales y ejecuta el algoritmo de auto-promos
    }
}

function calcularCambio() {
    const totales = calcularTotales(false);
    const recibido = parseFloat(document.getElementById('input-recibido').value) || 0;
    const lblTexto = document.getElementById('lbl-texto-cambio');
    const lblValor = document.getElementById('lbl-valor-cambio');

    if (recibido === 0) {
        lblTexto.innerText = "Contra entrega (Deuda)";
        lblValor.innerText = `$${totales.total.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        lblValor.className = "text-sm font-black text-amber-500";
    } else if (recibido < totales.total) {
        lblTexto.innerText = "Resta por Pagar";
        lblValor.innerText = `$${(totales.total - recibido).toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        lblValor.className = "text-sm font-black text-red-500";
    } else {
        lblTexto.innerText = "Cambio a devolver";
        lblValor.innerText = `$${(recibido - totales.total).toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        lblValor.className = "text-sm font-black text-green-500";
    }
}

// Fórmula de Haversine para calcular distancia en KM entre 2 puntos GPS
function calcularDistancia(lat1, lon1, lat2, lon2) {
    if (!lat1 || !lon1 || !lat2 || !lon2) return 0;
    const R = 6371; // Radio de la Tierra en km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

function toggleCustomAddress() {
    const chk = document.getElementById('chk-custom-address');
    const container = document.getElementById('custom-address-container');
    
    if (chk.checked) {
        container.classList.remove('hidden');
        if (!mapaDestino) {
            let lat = parseFloat(configLogistica.latitud_sucursal) || 20.659698;
            let lng = parseFloat(configLogistica.longitud_sucursal) || -103.349609;
            
            const selectCliente = document.getElementById('cliente_id');
            if (selectCliente.selectedIndex > -1) {
                const opt = selectCliente.options[selectCliente.selectedIndex];
                if (opt.dataset.lat && opt.dataset.lon) { lat = parseFloat(opt.dataset.lat); lng = parseFloat(opt.dataset.lon); }
            }

            mapaDestino = L.map('mapa-destino').setView([lat, lng], 14);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(mapaDestino);
            marcadorDestino = L.marker([lat, lng], {draggable: true}).addTo(mapaDestino);
            
            document.getElementById('custom-lat').value = lat.toFixed(6);
            document.getElementById('custom-lon').value = lng.toFixed(6);

            marcadorDestino.on('dragend', function(e) { 
                const pos = e.target.getLatLng();
                document.getElementById('custom-lat').value = pos.lat.toFixed(6); 
                document.getElementById('custom-lon').value = pos.lng.toFixed(6); 
                calcularTarifaEnvio(); 
                obtenerDireccion(pos.lat, pos.lng);
            });
            mapaDestino.on('click', function(e) { 
                marcadorDestino.setLatLng(e.latlng); 
                document.getElementById('custom-lat').value = e.latlng.lat.toFixed(6); 
                document.getElementById('custom-lon').value = e.latlng.lng.toFixed(6); 
                calcularTarifaEnvio(); 
                obtenerDireccion(e.latlng.lat, e.latlng.lng);
            });
            
            obtenerDireccion(lat, lng);
        }
        setTimeout(() => { mapaDestino.invalidateSize(); }, 200);
    } else {
        container.classList.add('hidden');
    }
    calcularTarifaEnvio();
}

async function obtenerDireccion(lat, lng) {
    const txtDireccion = document.getElementById('custom-direccion');
    txtDireccion.value = 'Buscando dirección...';
    
    if (!googleMapsKey) {
        txtDireccion.value = 'API Key de Google Maps no configurada.';
        return;
    }
    try {
        const res = await fetch(`https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${googleMapsKey}`);
        const data = await res.json();
        if (data.status === 'OK' && data.results.length > 0) {
            txtDireccion.value = data.results[0].formatted_address;
        } else {
            txtDireccion.value = 'No se pudo obtener la dirección exacta.';
        }
    } catch (e) {
        txtDireccion.value = 'Error al conectar con Google Maps.';
    }
}

function calcularTarifaEnvio() {
    const selectTarifa = document.getElementById('select-tarifa');
    const inputEnvio = document.getElementById('input-envio');
    const selectCliente = document.getElementById('cliente_id');
    const infoDistancia = document.getElementById('info-distancia');
    
    if (!selectCliente.value) {
        infoDistancia.innerText = '⚠️ Registra un cliente para habilitar envíos';
        infoDistancia.classList.remove('hidden', 'text-blue-500');
        infoDistancia.classList.add('text-amber-500');
        calcularTotales();
        return;
    }

    infoDistancia.classList.add('hidden');
    infoDistancia.innerText = '';

    if (selectTarifa.value === "") {
        calcularTotales(); return; // Personalizado
    }

    const optTarifa = selectTarifa.options[selectTarifa.selectedIndex];
    const tipo = optTarifa.dataset.tipo;
    const precioBase = parseFloat(optTarifa.dataset.base) || 0;
    
    if (tipo === 'fija') {
        inputEnvio.value = precioBase;
    } else if (tipo === 'distancia') {
        let latDestino, lonDestino;
        if (document.getElementById('chk-custom-address').checked) {
            latDestino = parseFloat(document.getElementById('custom-lat').value);
            lonDestino = parseFloat(document.getElementById('custom-lon').value);
        } else {
            const optCliente = selectCliente.options[selectCliente.selectedIndex];
            latDestino = parseFloat(optCliente.dataset.lat);
            lonDestino = parseFloat(optCliente.dataset.lon);
        }
        const latTienda = parseFloat(configLogistica.latitud_sucursal);
        const lonTienda = parseFloat(configLogistica.longitud_sucursal);

        if (!latDestino || !lonDestino || isNaN(latDestino) || isNaN(lonDestino)) {
            infoDistancia.innerText = '⚠️ Sin coordenadas de destino. Solo aplica tarifa base.';
            infoDistancia.classList.remove('hidden', 'text-blue-500'); infoDistancia.classList.add('text-amber-500');
            inputEnvio.value = precioBase;
        } else {
            const distancia = calcularDistancia(latTienda, lonTienda, latDestino, lonDestino);
            const kmBase = parseFloat(optTarifa.dataset.kmbase) || 0;
            const precioKmExtra = parseFloat(optTarifa.dataset.kmextra) || 0;
            
            let costoTotal = precioBase;
            let info = `Dist: ${distancia.toFixed(1)}km`;
            if (distancia > kmBase) { const kmExtra = distancia - kmBase; costoTotal += (kmExtra * precioKmExtra); info += ` (+${kmExtra.toFixed(1)}km extra)`; }
            
            infoDistancia.innerText = info; infoDistancia.classList.remove('hidden', 'text-amber-500'); infoDistancia.classList.add('text-blue-500');
            inputEnvio.value = Math.round(costoTotal * 100) / 100;
        }
    }
    calcularTotales();
}

function autoSeleccionarCuenta() {
    const metodo = document.getElementById('metodo_pago').value;
    const selectCuenta = document.getElementById('cuenta_id');
    
    let tipoBuscado = 'efectivo';
    if (metodo === 'Tarjeta') tipoBuscado = 'terminal';
    if (metodo === 'Transferencia') tipoBuscado = 'banco';
    
    const cuentaEncontrada = cuentasFinanzas.find(c => c.tipo === tipoBuscado);
    if (cuentaEncontrada) selectCuenta.value = cuentaEncontrada.id;
}

async function procesar(tipo) {
    if (carrito.length === 0) { 
        Swal.fire('Atención', 'Agrega productos al carrito primero.', 'warning'); 
        return; 
    }
    
    const result = await Swal.fire({
        title: tipo === 'venta' ? '¿Confirmar Venta?' : '¿Guardar Cotización?',
        text: tipo === 'venta' ? 'Se descontará el stock del inventario.' : 'Podrás convertirla a venta en otro momento.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: tipo === 'venta' ? '#16a34a' : '#1f2937',
        cancelButtonColor: '#9ca3af',
        confirmButtonText: 'Sí, procesar',
        cancelButtonText: 'Cancelar'
    });

    if (!result.isConfirmed) return;
    
    const totales = calcularTotales();
    let notas = document.getElementById('input-notas').value;
    if (document.getElementById('chk-custom-address').checked) {
        notas += `\n\n[ATENCIÓN: ENTREGA EN DIRECCIÓN DIFERENTE AL CLIENTE]\nCoordenadas: ${document.getElementById('custom-lat').value}, ${document.getElementById('custom-lon').value}`;
        const direccionExt = document.getElementById('custom-direccion').value;
        if (direccionExt && !direccionExt.includes('Error') && !direccionExt.includes('Buscando') && !direccionExt.includes('API Key')) notas += `\nDirección de Entrega: ${direccionExt}`;
    }

    if (window.promosAplicadasList && window.promosAplicadasList.length > 0 && totales.descuento > 0) {
        notas += `\n\n[PROMOCIONES APLICADAS]\n` + window.promosAplicadasList.join('\n');
    }

    const payload = {
        tipo: tipo,
        cliente_id: document.getElementById('cliente_id').value,
        subtotal: totales.subtotal,
        descuento: totales.descuento,
        costo_envio: totales.envio,
        total: totales.total,
        monto_recibido: parseFloat(document.getElementById('input-recibido').value) || 0,
        notas: notas.trim(),
        metodo_pago: document.getElementById('metodo_pago').value,
        cuenta_id: document.getElementById('cuenta_id').value,
        carrito: carrito,
        tarifa_id: document.getElementById('select-tarifa').value,
        entrega_personalizada: document.getElementById('chk-custom-address').checked,
        lat_destino: document.getElementById('custom-lat').value,
        lon_destino: document.getElementById('custom-lon').value,
        direccion_destino: document.getElementById('custom-direccion').value
    };

    try {
        const res = await fetch('<?= base_url('pos/guardar') ?>', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(payload) });
        const data = await res.json();
        
        if (data.status === 'success') {
            await Swal.fire({
                title: '¡Éxito!',
                text: data.mensaje,
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
            window.location.href = '<?= base_url('pos/ver?id=') ?>' + data.id;
        } else {
            Swal.fire('Error al guardar', data.error, 'error');
        }
    } catch (e) {
        Swal.fire('Error', 'Error de conexión con el servidor', 'error');
    }
}
</script>