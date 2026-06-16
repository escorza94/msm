<div class="max-w-3xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-user-edit text-blue-500 mr-2"></i> Editar Cliente</h2>
            <p class="text-sm text-gray-500 mt-1">Actualiza los datos de <span class="font-bold"><?= htmlspecialchars($cliente['nombre']) ?></span></p>
        </div>
        <a href="<?= base_url('clientes') ?>" class="text-sm bg-white border border-gray-200 text-gray-600 px-3 py-1.5 rounded-lg font-medium hover:bg-gray-50 transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
    </div>

    <?php if(isset($error)): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-xl flex items-center shadow-sm text-sm">
            <i class="fas fa-exclamation-triangle text-lg mr-3"></i> 
            <div><?= htmlspecialchars($error) ?></div>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-6">
        <form action="<?= base_url('clientes/editar') ?>" method="POST" class="space-y-6">
            <input type="hidden" name="id" value="<?= $cliente['id'] ?>">
            
            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3"><i class="fas fa-id-card mr-2 text-gray-400"></i> Datos Personales y Fiscales</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo o Razón Social <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($cliente['nombre']) ?>" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm transition shadow-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono Móvil</label>
                    <input type="text" name="telefono" value="<?= htmlspecialchars($cliente['telefono'] ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm transition shadow-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                    <input type="email" name="correo" value="<?= htmlspecialchars($cliente['correo'] ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm transition shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">RFC (Para Facturación)</label>
                    <input type="text" name="rfc" value="<?= htmlspecialchars($cliente['rfc'] ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm transition shadow-sm uppercase">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fab fa-whatsapp text-green-500 mr-1"></i> Vincular con Chat de WhatsApp</label>
                    <select name="whatsapp_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none text-sm transition shadow-sm bg-green-50">
                        <option value="">-- No vincular (Sin WhatsApp) --</option>
                        <?php foreach($contactos_wa ?? [] as $wa): ?>
                            <option value="<?= htmlspecialchars($wa['whatsapp_id']) ?>" <?= ($wa['whatsapp_id'] === $cliente['whatsapp_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($wa['nombre']) ?> (<?= explode('@', $wa['whatsapp_id'])[0] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3 pt-4"><i class="fas fa-map-marked-alt mr-2 text-gray-400"></i> Domicilio y Logística</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2 relative">
                    <div class="flex justify-between items-end mb-1">
                        <label class="block text-sm font-medium text-gray-700">Dirección de Entrega</label>
                        <button type="button" onclick="abrirMapa()" class="text-xs bg-blue-100 text-blue-600 px-3 py-1.5 rounded hover:bg-blue-200 transition font-medium flex items-center shadow-sm">
                            <i class="fas fa-map-marked-alt mr-1"></i> Buscar en el Mapa
                        </button>
                    </div>
                    <textarea id="direccion_input" name="direccion" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm transition shadow-sm resize-none" placeholder="Calle, Número, Colonia, Ciudad, Estado, CP..."><?= htmlspecialchars($cliente['direccion'] ?? '') ?></textarea>
                    
                    <!-- Campos ocultos para las coordenadas y enlace -->
                    <input type="hidden" name="latitud" value="<?= htmlspecialchars($cliente['latitud'] ?? '') ?>">
                    <input type="hidden" name="longitud" value="<?= htmlspecialchars($cliente['longitud'] ?? '') ?>">
                    <input type="hidden" name="enlace_maps" value="<?= htmlspecialchars($cliente['enlace_maps'] ?? '') ?>">
                </div>
            </div>

            <div class="pt-4 mt-6 border-t border-gray-100 flex justify-end gap-3">
                <a href="<?= base_url('clientes') ?>" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition text-sm">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold transition shadow-md flex items-center text-sm"><i class="fas fa-save mr-2"></i> Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal del Mapa Interactivo -->
<div id="mapa-modal" class="fixed inset-0 z-[100] bg-black/60 hidden flex items-center justify-center backdrop-blur-sm transition-opacity opacity-0 duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden transform scale-95 transition-transform duration-300" id="mapa-container">
        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800 flex items-center"><i class="fas fa-map-marker-alt text-red-500 mr-2 text-lg"></i> Selecciona la Ubicación de Entrega</h3>
            <button type="button" onclick="cerrarMapa()" class="text-gray-400 hover:text-red-500 text-xl focus:outline-none"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-4">
            <p class="text-xs text-gray-500 mb-3">Arrastra el marcador rojo, usa el buscador o activa tu GPS para fijar la ubicación exacta.</p>
            <div class="flex gap-2 mb-3">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                    <input type="text" id="map-search-box" class="w-full border border-gray-300 rounded-lg pl-9 pr-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none shadow-sm" placeholder="Buscar lugar o colonia en el mapa...">
                </div>
                <button type="button" onclick="obtenerMiUbicacion()" class="bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition flex items-center shadow-sm border border-gray-300" title="Usar mi ubicación actual">
                    <i id="gps-icon" class="fas fa-crosshairs text-blue-500"></i>
                </button>
            </div>
            <div id="map" class="w-full h-[360px] rounded border border-gray-200 bg-gray-100 relative"></div>
        </div>
        <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
            <button type="button" onclick="cerrarMapa()" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition text-sm font-medium">Cancelar</button>
            <button type="button" onclick="confirmarUbicacion()" class="px-5 py-2.5 bg-blue-600 rounded-lg text-white hover:bg-blue-700 transition text-sm font-bold shadow-md flex items-center"><i class="fas fa-check mr-2"></i> Confirmar Ubicación</button>
        </div>
    </div>
</div>

<?php $globalConfig = require BASE_PATH . '/config.php'; ?>
<?php if(!empty($globalConfig['GOOGLE_MAPS_API_KEY'])): ?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?= htmlspecialchars($globalConfig['GOOGLE_MAPS_API_KEY']) ?>&libraries=places"></script>
<?php endif; ?>

<script>
let map, marker, geocoder;
let mapSelectedLat, mapSelectedLng, mapSelectedAddress;

function initMapModal() {
    if (typeof google === 'undefined') { alert("La API de Google Maps no está configurada."); return; }

    const defaultLat = parseFloat(document.querySelector('input[name="latitud"]').value) || 19.432608;
    const defaultLng = parseFloat(document.querySelector('input[name="longitud"]').value) || -99.133209;
    const myLatlng = { lat: defaultLat, lng: defaultLng };

    map = new google.maps.Map(document.getElementById("map"), { zoom: 16, center: myLatlng, mapTypeControl: false, streetViewControl: false });
    marker = new google.maps.Marker({ position: myLatlng, map: map, draggable: true, animation: google.maps.Animation.DROP });
    geocoder = new google.maps.Geocoder();

    updateMarkerPosition(marker.getPosition());

    map.addListener("click", (mapsMouseEvent) => {
        marker.setPosition(mapsMouseEvent.latLng);
        updateMarkerPosition(marker.getPosition());
    });
    marker.addListener("dragend", () => updateMarkerPosition(marker.getPosition()));
    
    const searchInput = document.getElementById("map-search-box");
    const searchBox = new google.maps.places.SearchBox(searchInput);

    map.addListener("bounds_changed", () => {
        searchBox.setBounds(map.getBounds());
    });

    searchBox.addListener("places_changed", () => {
        const places = searchBox.getPlaces();
        if (places.length == 0) return;
        const place = places[0];
        if (!place.geometry || !place.geometry.location) return;

        if (place.geometry.viewport) map.fitBounds(place.geometry.viewport);
        else { map.setCenter(place.geometry.location); map.setZoom(17); }
        
        marker.setPosition(place.geometry.location);
        updateMarkerPosition(place.geometry.location, place.formatted_address);
    });

    const currentAddress = document.getElementById('direccion_input').value;
    if(!document.querySelector('input[name="latitud"]').value && currentAddress) {
        geocoder.geocode({ address: currentAddress }, (results, status) => {
            if (status === "OK") {
                map.setCenter(results[0].geometry.location);
                marker.setPosition(results[0].geometry.location);
                updateMarkerPosition(results[0].geometry.location, results[0].formatted_address);
            }
        });
    } else if (!document.querySelector('input[name="latitud"]').value) {
        obtenerMiUbicacion(true);
    }
}

function updateMarkerPosition(latLng, address = null) {
    mapSelectedLat = latLng.lat(); 
    mapSelectedLng = latLng.lng();
    mapSelectedAddress = address;
}

function abrirMapa() {
    const modal = document.getElementById('mapa-modal');
    const container = document.getElementById('mapa-container');
    modal.classList.remove('hidden');
    setTimeout(() => { modal.classList.remove('opacity-0'); container.classList.remove('scale-95'); if(!map) initMapModal(); }, 10);
}

function cerrarMapa() {
    document.getElementById('mapa-modal').classList.add('opacity-0'); document.getElementById('mapa-container').classList.add('scale-95');
    setTimeout(() => document.getElementById('mapa-modal').classList.add('hidden'), 300);
}

function confirmarUbicacion() {
    if (mapSelectedLat && mapSelectedLng) {
        document.querySelector('input[name="latitud"]').value = mapSelectedLat;
        document.querySelector('input[name="longitud"]').value = mapSelectedLng;
        document.querySelector('input[name="enlace_maps"]').value = `https://www.google.com/maps/search/?api=1&query=${mapSelectedLat},${mapSelectedLng}`;
        
        if (mapSelectedAddress) {
            document.getElementById('direccion_input').value = mapSelectedAddress;
            cerrarMapa();
        } else {
            const btn = document.querySelector('button[onclick="confirmarUbicacion()"]');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Procesando...';
            btn.disabled = true;

            geocoder.geocode({ location: { lat: mapSelectedLat, lng: mapSelectedLng } }, (results, status) => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
                if (status === "OK" && results[0]) {
                    document.getElementById('direccion_input').value = results[0].formatted_address;
                } else {
                    console.error("Geocoding API Error:", status);
                    alert("No se pudo convertir la ubicación a texto. (Status: " + status + ")\n\nPor favor, asegúrate de habilitar la 'Geocoding API' en tu consola de Google Cloud.");
                    if (!document.getElementById('direccion_input').value) {
                        document.getElementById('direccion_input').value = "Ubicación seleccionada por GPS (Falta descripción en texto)";
                    }
                }
                cerrarMapa();
            });
        }
    } else {
        cerrarMapa();
    }
}

function obtenerMiUbicacion(automatic = false) {
    if (!navigator.geolocation) {
        if (!automatic) alert("Tu navegador no soporta geolocalización.");
        return;
    }
    
    const gpsIcon = document.getElementById('gps-icon');
    gpsIcon.className = "fas fa-spinner fa-spin text-blue-500";
    
    navigator.geolocation.getCurrentPosition(
        (position) => {
            const pos = { lat: position.coords.latitude, lng: position.coords.longitude };
            map.setCenter(pos); map.setZoom(17);
            marker.setPosition(pos); updateMarkerPosition(marker.getPosition());
            gpsIcon.className = "fas fa-crosshairs text-blue-500";
        },
        () => {
            if (!automatic) alert("No se pudo obtener tu ubicación. Por favor permite el acceso al GPS en tu navegador.");
            gpsIcon.className = "fas fa-crosshairs text-blue-500";
        }, { enableHighAccuracy: true }
    );
}
</script>
