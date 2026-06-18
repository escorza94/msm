<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'Mueblería San Martín') ?></title>
    
    <?php if(isset($producto)): // SEO específico para página de producto ?>
        <meta name="description" content="<?= htmlspecialchars(substr(strip_tags($producto['descripcion'] ?? ''), 0, 155)) ?>">
    <?php else: // SEO general para otras páginas ?>
        <meta name="description" content="<?= htmlspecialchars($config['seo_descripcion'] ?? 'Encuentra los mejores muebles para tu hogar.') ?>">
    <?php endif; ?>
    
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
        .swiper-button-next, .swiper-button-prev { color: white; background-color: rgba(0,0,0,0.3); border-radius: 50%; width: 44px; height: 44px; transition: background-color 0.3s; }
        .swiper-button-next:hover, .swiper-button-prev:hover { background-color: rgba(0,0,0,0.5); }
        .swiper-button-next:after, .swiper-button-prev:after { font-size: 18px; font-weight: bold; }
        .swiper-pagination-bullet-active { background: var(--color-primario) !important; }
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
        <div class="container mx-auto px-4 md:px-6 py-3 md:py-4 flex justify-between items-center">
            <div class="text-xl md:text-2xl font-bold text-primario flex items-center">
                <a href="<?= base_url() ?>" class="flex items-center gap-2 md:gap-3">
                    <?php $logo_url = $business_logo ?? ''; ?>
                    <?php if (!empty($logo_url)): ?>
                        <img src="<?= (strpos($logo_url, 'http') === 0 ? '' : base_url()) . htmlspecialchars($logo_url) ?>" alt="Logo <?= htmlspecialchars($config['nombre_empresa'] ?? 'Negocio') ?>" class="h-8 md:h-12 max-w-[150px] md:max-w-[200px] object-contain">
                        <span class="hidden lg:block text-lg"><?= htmlspecialchars($config['nombre_empresa'] ?? '') ?></span>
                    <?php else: ?>
                        <span class="truncate max-w-[200px] md:max-w-none"><?= htmlspecialchars($config['nombre_empresa'] ?? 'Mueblería San Martín') ?></span>
                    <?php endif; ?>
                </a>
            </div>
            <nav class="hidden md:flex space-x-8">
                <?php foreach($menu_enlaces as $link): ?>
                    <a href="<?= htmlspecialchars($link['enlace']) ?>" class="text-gray-600 hover:text-primario transition font-medium"><?= htmlspecialchars($link['titulo']) ?></a>
                <?php endforeach; ?>
            </nav>
            <div class="flex items-center gap-3">
                <a href="https://wa.me/<?= htmlspecialchars($config['whatsapp_numero'] ?? '') ?>?text=Hola,%20vengo%20de%20la%20página%20web" target="_blank" class="hidden md:flex bg-green-500 text-white px-5 py-2 rounded-full hover:bg-green-600 transition items-center shadow-sm font-bold text-sm">
                    <i class="fab fa-whatsapp mr-2"></i> Contactar
                </a>
                <button id="mobile-menu-btn" class="md:hidden text-gray-600 hover:text-primario focus:outline-none text-2xl p-2">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>
    
    <!-- Menú Móvil Deslizable -->
    <div id="mobile-menu" class="fixed inset-0 z-[60] bg-black/50 hidden opacity-0 transition-opacity duration-300">
        <div class="absolute left-0 top-0 bottom-0 w-64 bg-secciones shadow-xl transform -translate-x-full transition-transform duration-300 flex flex-col" id="mobile-menu-content">
            <div class="p-4 flex justify-end border-b border-gray-100">
                <button id="close-menu-btn" class="text-gray-500 hover:text-red-500 text-2xl p-2"><i class="fas fa-times"></i></button>
            </div>
            <nav class="flex flex-col p-6 space-y-4 overflow-y-auto flex-1">
                <?php foreach($menu_enlaces as $link): ?>
                    <a href="<?= htmlspecialchars($link['enlace']) ?>" class="text-gray-700 hover:text-primario font-bold text-lg border-b border-gray-50 pb-3 mobile-link"><?= htmlspecialchars($link['titulo']) ?></a>
                <?php endforeach; ?>
                <a href="https://wa.me/<?= htmlspecialchars($config['whatsapp_numero'] ?? '') ?>?text=Hola,%20vengo%20de%20la%20página%20web" target="_blank" class="bg-green-500 text-white px-5 py-3 rounded-xl text-center shadow-sm font-bold mt-6 flex justify-center items-center"><i class="fab fa-whatsapp mr-2 text-xl"></i> Hablar por WhatsApp</a>
            </nav>
        </div>
    </div>