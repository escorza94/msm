<?php
    // Incluimos el header para mantener la consistencia
    $headerPath = __DIR__ . '/../partials/header.php';
    if (file_exists($headerPath)) require $headerPath;
?>

    <main>
        <!-- SECCIONES DINÁMICAS (Layout Builder CMS) -->
        <?php foreach ($secciones as $seccion) {
            // El StorefrontController ya preparó la ruta de la vista y los datos necesarios (ej. productos).
            // Simplemente incluimos el archivo de la vista correspondiente.
            if (!empty($seccion['view_path']) && file_exists($seccion['view_path'])) {
                // Hacemos que las variables de la sección estén disponibles para la vista incluida.
                // $config, $productos, $promociones, etc.
                extract($seccion);
                require $seccion['view_path'];
            }
        } ?>

        <!-- 5. Ubicación (Mapa) -->
        <section id="ubicacion" class="py-10 md:py-16 bg-fondo">
            <div class="container mx-auto px-4 md:px-6">
                <h2 class="text-2xl md:text-3xl font-bold text-center text-primario mb-2">Visítanos</h2>
                <p class="text-center text-gray-500 mb-8 md:mb-10 text-sm md:text-base">Encuéntranos en el corazón de la ciudad.</p>
                <div class="w-full h-64 md:h-96 bg-gray-300 rounded-lg shadow-md overflow-hidden border border-gray-200" id="mapa-sucursal">
                    <!-- El mapa se inyectará aquí -->
                </div>
            </div>
        </section>
    </main>

    <!-- Script para el Mapa de Google -->
    <script>
        function initMap() {
            const lat = <?= json_encode(floatval($sucursal_coords['lat'] ?? 0)) ?>;
            const lng = <?= json_encode(floatval($sucursal_coords['lon'] ?? 0)) ?>;
            
            const mapElement = document.getElementById('mapa-sucursal');

            if (!mapElement || (lat === 0 && lng === 0)) {
                if(mapElement) mapElement.innerHTML = '<div class="flex items-center justify-center h-full text-gray-500 bg-gray-100">Ubicación no configurada en el módulo de Logística.</div>';
                return;
            }

            const sucursalPos = { lat: lat, lng: lng };
            const map = new google.maps.Map(mapElement, {
                zoom: 16,
                center: sucursalPos,
                mapId: 'MUEBLERIA_SAN_MARTIN_MAP',
                disableDefaultUI: true,
                zoomControl: true,
            });

            new google.maps.Marker({
                position: sucursalPos,
                map: map,
                title: "Mueblería San Martín",
            });
        }
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?= htmlspecialchars($google_maps_api_key ?? '') ?>&callback=initMap&map_ids=MUEBLERIA_SAN_MARTIN_MAP"></script>
<?php
    $footerPath = __DIR__ . '/../partials/footer.php';
    if (file_exists($footerPath)) require $footerPath;
?>
