<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- SEO: Título y descripción desde la BD -->
    <title><?= htmlspecialchars($titulo ?? $config['seo_titulo'] ?? 'Mueblería San Martín | Calidad y Confianza') ?></title>
    <meta name="description" content="<?= htmlspecialchars($config['seo_descripcion'] ?? 'Encuentra los mejores muebles para tu hogar. Calidad, buen precio y excelente servicio.') ?>">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Swiper.js para el carrusel de banners -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

    <style>
        :root {
            --color-primario: <?= htmlspecialchars($config['color_primario'] ?? '#4f46e5') ?>;
            --color-secundario: <?= htmlspecialchars($config['color_secundario'] ?? '#1f2937') ?>;
            --color-fondo: <?= htmlspecialchars($config['color_fondo'] ?? '#f3f4f6') ?>;
            --color-secciones: <?= htmlspecialchars($config['color_secciones'] ?? '#ffffff') ?>;
        }
        .bg-primario { background-color: var(--color-primario); }
        .text-primario { color: var(--color-primario); }
        .border-primario { border-color: var(--color-primario); }
        .hover\:bg-primario-dark:hover { filter: brightness(0.9); background-color: var(--color-primario); }
        .hover\:text-primario:hover { color: var(--color-primario); }
        .bg-primario-light { background-color: color-mix(in srgb, var(--color-primario) 15%, transparent); color: var(--color-primario); }

        .bg-secundario { background-color: var(--color-secundario); }
        .bg-fondo { background-color: var(--color-fondo); }
        .bg-secciones { background-color: var(--color-secciones); }

        /* Estilos personalizados para el carrusel */
        .swiper-button-next, .swiper-button-prev {
            color: white;
            background-color: rgba(0,0,0,0.3);
            border-radius: 50%;
            width: 44px;
            height: 44px;
            transition: background-color 0.3s;
        }
        .swiper-button-next:hover, .swiper-button-prev:hover {
            background-color: rgba(0,0,0,0.5);
        }
        .swiper-button-next:after, .swiper-button-prev:after {
            font-size: 18px;
            font-weight: bold;
        }
        .swiper-pagination-bullet-active {
            background: var(--color-primario) !important;
        }
    </style>
</head>
<body class="bg-fondo font-sans antialiased">

    <!-- 1. Cintillo de Promociones (si existe) -->
    <?php if (!empty($promocion_activa)): ?>
    <div class="bg-primario text-white text-center p-2.5 text-sm font-medium shadow-lg">
        <i class="fas fa-tag mr-2 animate-pulse"></i>
        <?= htmlspecialchars($promocion_activa['nombre']) ?> - ¡Usa el código <strong><?= htmlspecialchars($promocion_activa['codigo_cupon']) ?></strong>!
    </div>
    <?php endif; ?>

    <!-- 2. Header / Barra de Navegación -->
    <header class="bg-secciones shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="text-xl md:text-2xl font-bold text-primario">
                <a href="<?= base_url() ?>"><?= htmlspecialchars($config['nombre_empresa'] ?? 'Mueblería San Martín') ?></a>
            </div>
            <nav class="hidden md:flex space-x-8">
                <?php foreach($menu_enlaces as $link): ?>
                    <a href="<?= htmlspecialchars($link['enlace']) ?>" class="text-gray-600 hover:text-primario transition font-medium"><?= htmlspecialchars($link['titulo']) ?></a>
                <?php endforeach; ?>
            </nav>
            <div class="flex items-center">
                <a href="https://wa.me/<?= htmlspecialchars($config['whatsapp_numero'] ?? '') ?>?text=Hola,%20vengo%20de%20la%20página%20web" target="_blank" class="bg-green-500 text-white px-5 py-2 rounded-full hover:bg-green-600 transition flex items-center shadow-sm font-bold text-sm">
                    <i class="fab fa-whatsapp mr-2"></i> Contactar
                </a>
            </div>
        </div>
    </header>

    <main>
        <!-- SECCIONES DINÁMICAS (Layout Builder CMS) -->
        <?php foreach ($secciones as $seccion): ?>
            
            <?php if ($seccion['tipo'] === 'carrusel_banners'): ?>
                <!-- WIDGET: Carrusel de Banners -->
                <?php $banners = $seccion['config']['banners'] ?? []; ?>
                <?php if (!empty($banners)): ?>
                <section class="relative h-64 md:h-[500px] bg-gray-300">
                    <div class="swiper-container h-full" id="banner-carousel-<?= $seccion['id'] ?>">
                        <div class="swiper-wrapper">
                            <?php foreach($banners as $banner): ?>
                            <div class="swiper-slide">
                                <a href="<?= htmlspecialchars($banner['enlace'] ?? '#') ?>" class="block h-full w-full">
                                    <img src="<?= base_url(htmlspecialchars($banner['imagen'] ?? '')) ?>" alt="<?= htmlspecialchars($banner['titulo'] ?? 'Banner') ?>" class="h-full w-full object-cover">
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="swiper-pagination"></div>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    </div>
                </section>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        new Swiper('#banner-carousel-<?= $seccion['id'] ?>', {
                            loop: true, autoplay: { delay: 5000, disableOnInteraction: false },
                            pagination: { el: '.swiper-pagination', clickable: true },
                            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' }
                        });
                    });
                </script>
                <?php endif; ?>
            
            <?php elseif ($seccion['tipo'] === 'grid_productos'): ?>
                <!-- WIDGET: Grid de Productos -->
                <?php $productos = $seccion['productos'] ?? []; ?>
                <section id="productos-<?= $seccion['id'] ?>" class="py-16 bg-secciones">
                    <div class="container mx-auto px-6">
                        <h2 class="text-3xl font-bold text-center text-primario mb-2"><?= htmlspecialchars($seccion['config']['titulo_seccion'] ?? 'Catálogo') ?></h2>
                        <?php if (!empty($seccion['config']['subtitulo'])): ?>
                            <p class="text-center text-gray-500 mb-10"><?= htmlspecialchars($seccion['config']['subtitulo']) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($seccion['error'])): ?>
                            <div class="text-left text-red-700 bg-red-50 p-6 rounded-xl border border-red-200 max-w-4xl mx-auto shadow-sm">
                                <h3 class="font-bold text-lg"><i class="fas fa-exclamation-triangle mr-2"></i> Error de Base de Datos en esta sección</h3>
                                <p class="text-sm mt-1 mb-3">La consulta para obtener los productos de la colección falló. Revisa el mensaje de error para diagnosticar el problema:</p>
                                <pre class="mt-2 text-xs bg-red-100 p-3 rounded font-mono overflow-x-auto text-red-800"><?= htmlspecialchars($seccion['error']) ?></pre>
                            </div>
                        <?php elseif (empty($productos)): ?>
                            <div class="text-center text-gray-500 bg-gray-50 p-8 rounded-xl border border-gray-100 max-w-2xl mx-auto">
                                <i class="fas fa-box-open text-4xl mb-3 opacity-30"></i>
                                <p class="font-medium text-gray-600">No hay productos disponibles en esta colección por el momento.</p>
                                <p class="text-xs mt-2 text-gray-400">Asegúrate de que los productos estén activos y su stock sea mayor a 0.</p>
                            </div>
                        <?php else: ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                            <?php foreach($productos as $producto): ?>
                            <div class="bg-secciones rounded-lg shadow-md overflow-hidden transform hover:-translate-y-2 transition-transform duration-300 group">
                                <a href="https://wa.me/<?= htmlspecialchars($config['whatsapp_numero'] ?? '') ?>?text=Hola,%20me%20interesa%20el%20producto:%20<?= urlencode($producto['nombre']) ?>" target="_blank" class="block">
                                    <div class="h-56 bg-gray-200 overflow-hidden relative">
                                        <img src="<?= base_url(htmlspecialchars($producto['imagen'] ?? 'storage/placeholder.jpg')) ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    </div>
                                    <div class="p-5">
                                        <h3 class="font-bold text-gray-800 truncate mb-2"><?= htmlspecialchars($producto['nombre']) ?></h3>
                                        <p class="text-primario font-black text-xl">$<?= number_format($producto['precio'], 2) ?></p>
                                            <div class="mt-4 w-full bg-primario text-white py-2.5 rounded-lg text-center block hover:bg-primario-dark transition font-bold text-sm">
                                            Ver en WhatsApp
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>

            <?php elseif ($seccion['tipo'] === 'tarjetas_info'): ?>
                <!-- WIDGET: Tarjetas de Información -->
                <?php $tarjetas = $seccion['config']['tarjetas'] ?? []; ?>
                <?php if (!empty($tarjetas)): ?>
                <section id="tarjetas-<?= $seccion['id'] ?>" class="py-16 bg-fondo">
                    <div class="container mx-auto px-6">
                        <?php if (!empty($seccion['config']['titulo_seccion'])): ?><h2 class="text-3xl font-bold text-center text-primario mb-2"><?= htmlspecialchars($seccion['config']['titulo_seccion']) ?></h2><?php endif; ?>
                        <?php if (!empty($seccion['config']['subtitulo'])): ?><p class="text-center text-gray-500 mb-10"><?= htmlspecialchars($seccion['config']['subtitulo']) ?></p><?php endif; ?>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <?php foreach($tarjetas as $tarjeta): ?>
                            <div class="bg-secciones p-8 rounded-xl shadow-sm border border-gray-100 text-center hover:-translate-y-2 transition-transform duration-300">
                                <div class="w-16 h-16 mx-auto bg-primario-light rounded-full flex items-center justify-center text-2xl mb-4">
                                    <i class="<?= htmlspecialchars($tarjeta['icono'] ?? 'fas fa-star') ?>"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 mb-2"><?= htmlspecialchars($tarjeta['titulo'] ?? '') ?></h3>
                                <p class="text-gray-600"><?= htmlspecialchars($tarjeta['descripcion'] ?? '') ?></p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
                <?php endif; ?>
            <?php endif; ?>

        <?php endforeach; ?>

        <!-- 5. Ubicación (Mapa) -->
        <section id="ubicacion" class="py-16 bg-fondo">
            <div class="container mx-auto px-6">
                <h2 class="text-3xl font-bold text-center text-primario mb-2">Visítanos</h2>
                <p class="text-center text-gray-500 mb-10">Encuéntranos en el corazón de la ciudad.</p>
                <div class="w-full h-96 bg-gray-300 rounded-lg shadow-md overflow-hidden border border-gray-200" id="mapa-sucursal">
                    <!-- El mapa se inyectará aquí -->
                </div>
            </div>
        </section>
    </main>

    <!-- 6. Footer -->
        <footer id="contacto" class="bg-secundario text-white py-12">
        <div class="container mx-auto px-6 text-center">
            <h3 class="text-2xl font-bold mb-4"><?= htmlspecialchars($config['nombre_empresa'] ?? 'Mueblería San Martín') ?></h3>
            <p class="mb-8 text-gray-300 max-w-lg mx-auto"><?= htmlspecialchars($config['footer_texto'] ?? 'Calidad y confianza para amueblar tu vida. Contáctanos para cualquier duda o cotización.') ?></p>
            <div class="flex justify-center space-x-6 mb-8">
                <?php if(!empty($config['facebook_url'])): ?>
                    <a href="<?= htmlspecialchars($config['facebook_url']) ?>" target="_blank" class="text-gray-300 text-2xl hover:text-white transition"><i class="fab fa-facebook"></i></a>
                <?php endif; ?>
                <?php if(!empty($config['instagram_url'])): ?>
                    <a href="<?= htmlspecialchars($config['instagram_url']) ?>" target="_blank" class="text-gray-300 text-2xl hover:text-white transition"><i class="fab fa-instagram"></i></a>
                <?php endif; ?>
                <?php if(!empty($config['tiktok_url'])): ?>
                    <a href="<?= htmlspecialchars($config['tiktok_url']) ?>" target="_blank" class="text-gray-300 text-2xl hover:text-white transition"><i class="fab fa-tiktok"></i></a>
                <?php endif; ?>
            </div>
            <p class="text-sm text-gray-500">&copy; <?= date('Y') ?> <?= htmlspecialchars($config['nombre_empresa'] ?? 'Mueblería San Martín') ?>. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Botón Flotante de WhatsApp -->
    <?php if(!empty($config['whatsapp_numero'])): ?>
    <a href="https://wa.me/<?= htmlspecialchars($config['whatsapp_numero']) ?>?text=Hola,%20necesito%20ayuda" target="_blank" class="fixed bottom-6 right-6 w-16 h-16 bg-green-500 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-green-600 transition transform hover:scale-110 z-40">
        <i class="fab fa-whatsapp text-4xl"></i>
    </a>
    <?php endif; ?>

    <!-- Scripts JS -->
    
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

</body>
</html>
