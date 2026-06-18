<?php
// $config contiene la configuración de la sección.
$banners = $config['banners'] ?? [];
if (empty($banners)) return; // No renderizar nada si no hay banners
?>
<!-- Importar Swiper.js -->
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

<section class="bg-gray-100">
    <div class="swiper-container" style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff">
        <div class="swiper-wrapper">
            <?php foreach ($banners as $banner): ?>
                <div class="swiper-slide">
                    <a href="<?= htmlspecialchars($banner['enlace'] ?? '#') ?>" class="block relative w-full h-64 md:h-96 bg-gray-800">
                        <img src="<?= base_url($banner['imagen']) ?>" alt="<?= htmlspecialchars($banner['titulo'] ?? 'Banner') ?>" class="w-full h-full object-cover opacity-70">
                        <?php if (!empty($banner['titulo'])): ?>
                        <div class="absolute inset-0 flex items-center justify-center text-center p-4">
                            <h2 class="text-3xl md:text-5xl font-bold text-white drop-shadow-lg"><?= htmlspecialchars($banner['titulo']) ?></h2>
                        </div>
                        <?php endif; ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- Add Pagination -->
        <div class="swiper-pagination"></div>
        <!-- Add Navigation -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        new Swiper('.swiper-container', {
            loop: true,
            pagination: { el: '.swiper-pagination', clickable: true },
            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        });
    });
</script>