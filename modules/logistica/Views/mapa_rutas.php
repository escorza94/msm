<div class="max-w-7xl mx-auto mt-4 mb-10 h-[calc(100vh-140px)] flex flex-col">
    <div class="flex justify-between items-center mb-4 flex-shrink-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-map-marked-alt text-blue-500 mr-3"></i> Mapa Interactivo de Entregas</h2>
            <p class="text-sm text-gray-500 mt-1">Visualización de todos los envíos pendientes del día de hoy.</p>
        </div>
        <div class="flex gap-2">
            <!-- Botón para mandar a la app de celular -->
            <a href="#" id="btn-google-maps" target="_blank" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition text-sm flex items-center shadow-sm" title="Abre las instrucciones de manejo paso a paso en otra pestaña">
                <i class="fab fa-google mr-2"></i> Navegar en Google Maps
            </a>
            <a href="<?= base_url('logistica/entregas') ?>" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 font-medium transition text-sm flex items-center shadow-sm">
                <i class="fas fa-arrow-left mr-2 text-gray-400"></i> Volver a Kanban
            </a>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex-1 overflow-hidden relative flex flex-col md:flex-row">
        <!-- Panel Izquierdo / Lista de Envíos -->
        <div class="w-full md:w-80 border-b md:border-b-0 md:border-r border-gray-200 bg-gray-50 flex flex-col h-1/3 md:h-full z-20">
            <div class="p-3 border-b border-gray-200 bg-white">
                    <h3 class="font-bold text-sm text-gray-800">Envíos a Ruta</h3>
                <p class="text-[10px] text-gray-500 mb-2">Selecciona para armar el trayecto</p>
                
                <!-- Recuadro de Estimaciones Google Maps -->
                <div class="bg-indigo-50 border border-indigo-100 rounded p-2">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs text-indigo-800 font-bold"><i class="fas fa-clock text-indigo-400 mr-1"></i> Tiempo Estimado:</span>
                        <span id="eta-tiempo" class="text-xs text-indigo-600 font-black">--</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-indigo-800 font-bold"><i class="fas fa-route text-indigo-400 mr-1"></i> Distancia Total:</span>
                        <span id="eta-distancia" class="text-xs text-indigo-600 font-black">--</span>
                    </div>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto p-2 space-y-2 custom-scrollbar" id="lista-envios">
                <!-- Contenido generado por JS -->
            </div>
        </div>
        
        <!-- Mapa -->
        <div id="mapa-entregas" class="w-full h-2/3 md:h-full z-10 flex-1"></div>
    </div>
</div>

<script>
let map;
let directionsService;
let directionsRenderer;
let markers = {};
let selectedIds = [];
let latOrigen;
let lonOrigen;
let enviosData;

window.initMap = function() {
    latOrigen = <?= json_encode($lat_origen) ?>;
    lonOrigen = <?= json_encode($lon_origen) ?>;
    enviosData = <?= json_encode($envios) ?>;

    if (latOrigen == '0' || lonOrigen == '0' || latOrigen === '' || lonOrigen === '') {
        alert("Advertencia: No has configurado la ubicación exacta de tu sucursal en los ajustes de Logística.");
    }

    const originLatLng = new google.maps.LatLng(parseFloat(latOrigen) || 20.659698, parseFloat(lonOrigen) || -103.349609);

    map = new google.maps.Map(document.getElementById('mapa-entregas'), {
        zoom: 12,
        center: originLatLng,
            mapId: 'MAPA_RUTAS_LOGISTICA',
        mapTypeId: 'roadmap',
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: true
    });

    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer({
        map: map,
        suppressMarkers: true,
        polylineOptions: {
            strokeColor: '#4f46e5',
            strokeOpacity: 0.8,
            strokeWeight: 5
        }
    });

    const sucursalSvg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="24" height="32"><path fill="#ef4444" d="M384 192c0 87.4-117 243-168.3 307.2c-12.3 15.3-35.1 15.3-47.4 0C117 435 0 279.4 0 192C0 86 86 0 192 0S384 86 384 192z"/></svg>`;
    const packageSvg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="24" height="32"><path fill="#3b82f6" d="M384 192c0 87.4-117 243-168.3 307.2c-12.3 15.3-35.1 15.3-47.4 0C117 435 0 279.4 0 192C0 86 86 0 192 0S384 86 384 192z"/></svg>`;

    const iconSucursal = document.createElement('div');
    iconSucursal.innerHTML = sucursalSvg;

    const markerSucursal = new google.maps.marker.AdvancedMarkerElement({
        position: originLatLng,
        map: map,
        content: iconSucursal,
        title: 'Mi Sucursal'
    });

    const infoWindow = new google.maps.InfoWindow();
    markerSucursal.addListener('click', () => {
        infoWindow.setContent("<div class='text-center p-2'><b class='text-red-500 text-sm'><i class='fas fa-home'></i> Mi Sucursal</b><br><span class='text-xs text-gray-500'>Punto de origen</span></div>");
        infoWindow.open(map, markerSucursal);
    });

    const bounds = new google.maps.LatLngBounds();
    bounds.extend(originLatLng);
    
    const listaContainer = document.getElementById('lista-envios');

    enviosData.forEach((envio) => {
        if (!envio.coordenadas_destino) return;
        
        const coords = envio.coordenadas_destino.split(',');
        const lat = parseFloat(coords[0]);
        const lng = parseFloat(coords[1]);
        const pos = new google.maps.LatLng(lat, lng);

        bounds.extend(pos);

        const estadoBadge = envio.estado === 'en_ruta' ? '<span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase"><i class="fas fa-truck-fast"></i> En Ruta</span>' : '<span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase"><i class="fas fa-clock"></i> Pendiente</span>';

        const popupHtml = `
            <div class="min-w-[180px] p-1">
                <h4 class="font-bold text-gray-800 text-sm mb-1">Venta #${envio.folio_venta.toString().padStart(5,'0')}</h4>
                <p class="text-xs text-gray-600 m-0"><i class="fas fa-user text-gray-400 w-3"></i> ${envio.cliente_nombre || 'Cliente General'}</p>
                <p class="text-[10px] text-gray-500 mt-1 leading-tight border-b border-gray-100 pb-2"><i class="fas fa-map-marker-alt text-red-400 w-3"></i> ${envio.direccion_destino || 'Sin dirección'}</p>
                <div class="mt-2 text-center">${estadoBadge}</div>
            </div>`;

        const iconPackage = document.createElement('div');
        iconPackage.innerHTML = packageSvg;
        iconPackage.style.opacity = '0.5'; // Semi-transparente por defecto
        iconPackage.style.transition = 'opacity 0.3s ease';

        const marker = new google.maps.marker.AdvancedMarkerElement({
            position: pos,
            map: map,
            content: iconPackage,
            title: `Venta #${envio.folio_venta}`
        });

        marker.addListener('click', () => {
            infoWindow.setContent(popupHtml);
            infoWindow.open(map, marker);
        });

        markers[envio.id] = { marker: marker, lat: lat, lng: lng, data: envio, pos: pos };
        
        listaContainer.innerHTML += `
            <label class="flex items-start p-2.5 bg-white border border-gray-200 rounded-lg cursor-pointer hover:bg-indigo-50 transition shadow-sm select-none">
                <div class="flex-shrink-0 mt-0.5 mr-3">
                    <input type="checkbox" class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500" value="${envio.id}" onchange="toggleEnvio(${envio.id}, this.checked, this)">
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex justify-between items-center mb-1">
                        <span class="font-bold text-xs text-gray-800">#${envio.folio_venta.toString().padStart(5,'0')}</span>
                        ${estadoBadge}
                    </div>
                    <p class="text-xs text-gray-600 truncate" title="${envio.cliente_nombre || 'Cliente General'}"><i class="fas fa-user text-gray-400 w-3 text-center"></i> ${envio.cliente_nombre || 'Cliente General'}</p>
                    <p class="text-[10px] text-gray-500 mt-1 line-clamp-2 leading-tight" title="${envio.direccion_destino || 'Sin dirección'}"><i class="fas fa-map-marker-alt text-red-400 w-3 text-center"></i> ${envio.direccion_destino || 'Sin dirección'}</p>
                </div>
            </label>
        `;
    });

    if (enviosData.length > 0) {
        map.fitBounds(bounds);
    }
    
    actualizarRuta();
}

function toggleEnvio(id, isChecked, checkbox) {
    if(isChecked) {
        if(selectedIds.length >= 24) {
            alert('Solo puedes seleccionar un máximo de 24 entregas por ruta debido a los límites de Google Maps.');
            if (checkbox) checkbox.checked = false;
            return;
        }
        if(!selectedIds.includes(id)) selectedIds.push(id);
        markers[id].marker.content.style.opacity = '1';
    } else {
        selectedIds = selectedIds.filter(i => i !== id);
        markers[id].marker.content.style.opacity = '0.5';
    }
    actualizarRuta();
}

function actualizarRuta() {
    const originLatLng = new google.maps.LatLng(parseFloat(latOrigen) || 20.659698, parseFloat(lonOrigen) || -103.349609);
    
    let waypoints = [];
    
    selectedIds.forEach(id => {
        waypoints.push({
            location: markers[id].pos,
            stopover: true
        });
    });
    
    const btn = document.getElementById('btn-google-maps');
    const etaTiempo = document.getElementById('eta-tiempo');
    const etaDistancia = document.getElementById('eta-distancia');
    
    if(waypoints.length === 0) {
        if (directionsRenderer) directionsRenderer.setDirections({routes: []});
        btn.classList.add('opacity-50', 'pointer-events-none');
        btn.href = '#';
        if(etaTiempo) etaTiempo.innerText = '--';
        if(etaDistancia) etaDistancia.innerText = '--';
        return;
    }

    const destination = waypoints.pop(); // Sacamos el último como destino final
    
    const request = {
        origin: originLatLng,
        destination: destination.location,
        waypoints: waypoints,
        optimizeWaypoints: true,
        travelMode: google.maps.TravelMode.DRIVING
    };

    directionsService.route(request, (result, status) => {
        if (status == google.maps.DirectionsStatus.OK) {
            directionsRenderer.setDirections(result);
            
            let totalDist = 0;
            let totalTime = 0;
            const route = result.routes[0];
            
            for (let i = 0; i < route.legs.length; i++) {
                totalDist += route.legs[i].distance.value;
                totalTime += route.legs[i].duration.value;
            }
            
            if(etaDistancia) etaDistancia.innerText = (totalDist / 1000).toFixed(1) + ' km';
            
            if(etaTiempo) {
                const hours = Math.floor(totalTime / 3600);
                const minutes = Math.floor((totalTime % 3600) / 60);
                etaTiempo.innerText = hours > 0 ? `${hours}h ${minutes}m` : `${minutes} min`;
            }

            btn.classList.remove('opacity-50', 'pointer-events-none');
            const originStr = `${latOrigen},${lonOrigen}`;
            
            let orderedWaypointsStr = [];
            if(waypoints.length > 0) {
                route.waypoint_order.forEach(index => {
                    orderedWaypointsStr.push(`${waypoints[index].location.lat()},${waypoints[index].location.lng()}`);
                });
            }
            const destinationStr = `${destination.location.lat()},${destination.location.lng()}`;
        
            let url = `https://www.google.com/maps/dir/?api=1&origin=${encodeURIComponent(originStr)}&destination=${encodeURIComponent(destinationStr)}`;
            if(orderedWaypointsStr.length > 0) {
                url += `&waypoints=${encodeURIComponent(orderedWaypointsStr.join('|'))}`;
            }
            btn.href = url;
        } else {
            console.error('Error al trazar la ruta:', status);
            
            if (etaTiempo) etaTiempo.innerHTML = '<span class="text-red-500">Error</span>';
            if (etaDistancia) etaDistancia.innerHTML = '<span class="text-red-500">Error</span>';
            
            if (status === 'REQUEST_DENIED') {
                alert('Google Maps rechazó la solicitud.\n\nMotivos comunes:\n1. Falta vincular una Tarjeta de Crédito (Cuenta de Facturación) en Google Cloud (Obligatorio aunque no te cobren).\n2. La "Directions API" no está habilitada en tu proyecto.\n3. Acabas de habilitarla y Google tarda ~5 minutos en reflejar el cambio.\n\nRevisa la consola (F12) para ver el error exacto.');
            } else if (status === 'ZERO_RESULTS') {
                alert('Google Maps no encontró una ruta por calle válida (Asegúrate de que la ubicación de la sucursal y el cliente sean correctas y alcanzables por tierra).');
            } else if (status === 'MAX_WAYPOINTS_EXCEEDED') {
                alert('Google Maps solo permite optimizar un máximo de 25 entregas por ruta. Desmarca algunas entregas en la lista izquierda.');
            } else {
                alert('Error desconocido de Google Maps al trazar la ruta: ' + status);
            }
        }
    });
}
</script>

<!-- Librería Google Maps -->
<script src="https://maps.googleapis.com/maps/api/js?key=<?= htmlspecialchars($google_maps_key) ?>&callback=initMap&libraries=marker&loading=async" async defer></script>