<?php
// $config contiene la configuración de la sección.
// $productos es inyectado por el StorefrontController.
?>
<section class="py-12 md:py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (!empty($config['titulo_seccion'])): ?>
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl"><?= htmlspecialchars($config['titulo_seccion']) ?></h2>
            <?php if (!empty($config['subtitulo'])): ?>
            <p class="mt-3 max-w-2xl mx-auto text-lg text-gray-500"><?= htmlspecialchars($config['subtitulo']) ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-10">
            <?php foreach ($productos ?? [] as $producto): ?>
                <div class="group relative">
                    <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg bg-gray-100 group-hover:opacity-75">
                        <img src="<?= base_url($producto['imagen'] ?? 'storage/assets/placeholder.png') ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" class="h-full w-full object-cover object-center">
                    </div>
                    <h3 class="mt-4 text-sm text-gray-700 font-medium"><?= htmlspecialchars($producto['nombre']) ?></h3>
                    <p class="mt-1 text-lg font-bold text-gray-900">$<?= number_format($producto['precio'], 2) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>