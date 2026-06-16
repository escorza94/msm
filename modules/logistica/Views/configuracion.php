<div class="max-w-7xl mx-auto mt-4">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-route text-blue-500 mr-3"></i> Configuración de Fletes</h2>
            <p class="text-sm text-gray-500 mt-1">Configura las zonas de envío, fletes y tu punto de origen</p>
        </div>
        <a href="<?= base_url('logistica') ?>" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 font-medium transition text-sm flex items-center shadow-sm">
            <i class="fas fa-arrow-left mr-2 text-gray-400"></i> Volver a Logística
        </a>
    </div>

    <?php if(isset($_GET['error'])): ?>
        <div class="bg-red-50 text-red-600 px-4 py-3 rounded-lg mb-4 border border-red-100 text-sm flex items-center shadow-sm">
            <i class="fas fa-exclamation-circle mr-3 text-lg"></i> <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>
    <?php if(isset($_GET['success'])): ?>
        <div class="bg-green-50 text-green-600 px-4 py-3 rounded-lg mb-4 border border-green-100 text-sm flex items-center shadow-sm">
            <i class="fas fa-check-circle mr-3 text-lg"></i> <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Columna Izquierda: Punto de Origen -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gray-50 flex items-center">
                    <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>
                    <h3 class="font-bold text-gray-800">Sucursal (Punto de Origen)</h3>
                </div>
                <div class="p-5">
                    <p class="text-xs text-gray-500 mb-4">Estas coordenadas se utilizan para calcular la distancia exacta entre tu tienda y la casa del cliente al utilizar fletes dinámicos.</p>
                    
                    <div id="mapa-origen" class="w-full h-48 bg-gray-200 rounded-lg mb-4 border border-gray-300 z-10"></div>
                    
                    <form action="<?= base_url('logistica/guardarConfiguracion') ?>" method="POST" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Latitud</label>
                                <input type="text" id="latitud_input" name="latitud" value="<?= htmlspecialchars($config['latitud_sucursal'] ?? '') ?>" placeholder="Ej: 20.659698" class="w-full border border-gray-300 bg-gray-50 rounded px-3 py-2 text-sm focus:ring-blue-500 outline-none shadow-sm" required readonly>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Longitud</label>
                                <input type="text" id="longitud_input" name="longitud" value="<?= htmlspecialchars($config['longitud_sucursal'] ?? '') ?>" placeholder="Ej: -103.349609" class="w-full border border-gray-300 bg-gray-50 rounded px-3 py-2 text-sm focus:ring-blue-500 outline-none shadow-sm" required readonly>
                            </div>
                        </div>
                        <button type="submit" class="w-full py-2 bg-gray-800 text-white rounded-lg font-bold text-sm hover:bg-gray-900 transition flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i> Guardar Origen
                        </button>
                        <button type="button" onclick="obtenerGPS()" class="w-full py-2 mt-2 bg-blue-50 text-blue-600 border border-blue-100 rounded-lg font-bold text-sm hover:bg-blue-100 transition flex items-center justify-center">
                            <i class="fas fa-location-arrow mr-2"></i> Usar mi ubicación actual
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Tarifas de Envío -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800"><i class="fas fa-tags text-blue-400 mr-2"></i> Tarifas y Zonas de Envío</h3>
                    <button onclick="abrirModalTarifa()" class="px-3 py-1.5 bg-blue-600 text-white rounded font-bold text-xs hover:bg-blue-700 shadow-sm transition"><i class="fas fa-plus mr-1"></i> Nueva Tarifa</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white text-gray-400 text-xs uppercase border-b border-gray-100">
                                <th class="p-4 font-medium">Nombre de la Tarifa</th>
                                <th class="p-4 font-medium text-center">Tipo</th>
                                <th class="p-4 font-medium text-right">Precio / Reglas</th>
                                <th class="p-4 font-medium text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-gray-700 divide-y divide-gray-50">
                            <?php if(empty($tarifas)): ?>
                                <tr><td colspan="4" class="p-8 text-center text-gray-400">Aún no has configurado zonas de envío.</td></tr>
                            <?php else: foreach($tarifas as $t): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="p-4 font-bold text-gray-800"><?= htmlspecialchars($t['nombre']) ?></td>
                                    <td class="p-4 text-center">
                                        <?php if($t['tipo'] == 'fija'): ?>
                                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-[10px] uppercase font-bold">Fija</span>
                                        <?php else: ?>
                                            <span class="bg-blue-100 text-blue-600 px-2 py-1 rounded text-[10px] uppercase font-bold"><i class="fas fa-magic mr-1"></i> Distancia</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4 text-right">
                                        <div class="font-bold text-gray-800">$<?= number_format($t['precio_base'], 2) ?> Base</div>
                                        <?php if($t['tipo'] == 'distancia'): ?>
                                            <div class="text-[10px] text-gray-500 mt-0.5">Incluye <?= number_format($t['km_base'], 1) ?>km. Extra: $<?= number_format($t['precio_km_extra'], 2) ?>/km</div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4 text-center flex items-center justify-center gap-2">
                                        <button onclick='editarTarifa(<?= json_encode($t) ?>)' class="w-8 h-8 rounded bg-gray-50 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition tooltip" title="Editar"><i class="fas fa-edit"></i></button>
                                        <a href="<?= base_url('logistica/eliminarTarifa?id=' . $t['id']) ?>" onclick="return confirm('¿Seguro que deseas eliminar esta tarifa?')" class="w-8 h-8 inline-flex items-center justify-center rounded bg-gray-50 text-gray-600 hover:bg-red-50 hover:text-red-600 transition tooltip" title="Eliminar"><i class="fas fa-trash-alt"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear/Editar Tarifa -->
<div id="modal-tarifa" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden transform scale-95 transition-transform duration-300" id="modal-tarifa-content">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800" id="modal-title">Nueva Tarifa de Envío</h3>
            <button onclick="cerrarModalTarifa()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-lg"></i></button>
        </div>
        <form action="<?= base_url('logistica/guardarTarifa') ?>" method="POST" class="p-5">
            <input type="hidden" name="id" id="tarifa_id" value="0">
            
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Nombre (Ej: Periferia, Local)</label>
                <input type="text" name="nombre" id="tarifa_nombre" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-blue-500 outline-none" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Tipo de Cobro</label>
                <select name="tipo" id="tarifa_tipo" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-blue-500 outline-none" onchange="toggleDistanciaRules()">
                    <option value="fija">Tarifa Fija</option>
                    <option value="distancia">Cobro Dinámico por Distancia (Km)</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Precio Base ($)</label>
                    <input type="number" step="0.01" name="precio_base" id="tarifa_precio_base" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-blue-500 outline-none" value="0" required>
                </div>
                
                <div class="col-span-2 md:col-span-1 distancia-fields hidden">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Km Base Incluidos</label>
                    <input type="number" step="0.01" name="km_base" id="tarifa_km_base" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-blue-500 outline-none" value="0">
                </div>
                
                <div class="col-span-2 distancia-fields hidden">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Precio por Km Extra ($)</label>
                    <input type="number" step="0.01" name="precio_km_extra" id="tarifa_precio_km_extra" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-blue-500 outline-none" value="0">
                </div>
            </div>
            
            <div class="pt-2 border-t border-gray-100 flex justify-end">
                <button type="button" onclick="cerrarModalTarifa()" class="px-4 py-2 text-gray-500 text-sm font-medium mr-2">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm font-bold shadow-sm hover:bg-blue-700">Guardar Tarifa</button>
            </div>
        </form>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
let mapa, marcador;

document.addEventListener('DOMContentLoaded', () => {
    let lat = parseFloat(document.getElementById('latitud_input').value) || 20.659698;
    let lng = parseFloat(document.getElementById('longitud_input').value) || -103.349609;
    
    mapa = L.map('mapa-origen').setView([lat, lng], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(mapa);
    marcador = L.marker([lat, lng], {draggable: true}).addTo(mapa);
    
    marcador.on('dragend', function(e) {
        document.getElementById('latitud_input').value = e.target.getLatLng().lat.toFixed(6);
        document.getElementById('longitud_input').value = e.target.getLatLng().lng.toFixed(6);
    });

    mapa.on('click', function(e) {
        marcador.setLatLng(e.latlng);
        document.getElementById('latitud_input').value = e.latlng.lat.toFixed(6);
        document.getElementById('longitud_input').value = e.latlng.lng.toFixed(6);
    });
});

function obtenerGPS() { if(navigator.geolocation) { navigator.geolocation.getCurrentPosition((pos) => { let lat = pos.coords.latitude; let lng = pos.coords.longitude; document.getElementById('latitud_input').value = lat.toFixed(6); document.getElementById('longitud_input').value = lng.toFixed(6); if(mapa && marcador) { mapa.setView([lat, lng], 15); marcador.setLatLng([lat, lng]); } }, (err) => { alert('No se pudo obtener la ubicación: ' + err.message); }, { enableHighAccuracy: true }); } else { alert('Tu navegador no soporta geolocalización.'); } }
function toggleDistanciaRules() { const tipo = document.getElementById('tarifa_tipo').value; document.querySelectorAll('.distancia-fields').forEach(el => { if(tipo === 'distancia') { el.classList.remove('hidden'); } else { el.classList.add('hidden'); } }); }
function abrirModalTarifa() { const modal = document.getElementById('modal-tarifa'); document.getElementById('tarifa_id').value = '0'; document.getElementById('tarifa_nombre').value = ''; document.getElementById('tarifa_tipo').value = 'fija'; document.getElementById('tarifa_precio_base').value = '0'; document.getElementById('tarifa_km_base').value = '0'; document.getElementById('tarifa_precio_km_extra').value = '0'; document.getElementById('modal-title').innerText = 'Nueva Tarifa de Envío'; toggleDistanciaRules(); modal.classList.remove('hidden'); setTimeout(() => { modal.classList.add('opacity-100'); document.getElementById('modal-tarifa-content').classList.remove('scale-95'); document.getElementById('modal-tarifa-content').classList.add('scale-100'); }, 10); }
function cerrarModalTarifa() { const modal = document.getElementById('modal-tarifa'); modal.classList.remove('opacity-100'); document.getElementById('modal-tarifa-content').classList.remove('scale-100'); document.getElementById('modal-tarifa-content').classList.add('scale-95'); setTimeout(() => { modal.classList.add('hidden'); }, 300); }
function editarTarifa(t) { document.getElementById('tarifa_id').value = t.id; document.getElementById('tarifa_nombre').value = t.nombre; document.getElementById('tarifa_tipo').value = t.tipo; document.getElementById('tarifa_precio_base').value = t.precio_base; document.getElementById('tarifa_km_base').value = t.km_base; document.getElementById('tarifa_precio_km_extra').value = t.precio_km_extra; document.getElementById('modal-title').innerText = 'Editar Tarifa de Envío'; toggleDistanciaRules(); const modal = document.getElementById('modal-tarifa'); modal.classList.remove('hidden'); setTimeout(() => { modal.classList.add('opacity-100'); document.getElementById('modal-tarifa-content').classList.remove('scale-95'); document.getElementById('modal-tarifa-content').classList.add('scale-100'); }, 10); }
</script>