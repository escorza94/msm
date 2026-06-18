<?php
$titulo = $config['titulo_seccion'] ?? '';
$subtitulo = $config['subtitulo'] ?? '';
$tarjetas = $config['tarjetas'] ?? [];
if (empty($tarjetas)) return;
?>
<section class="py-12 md:py-20 bg-secciones">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (!empty($titulo)): ?>
        <div class="text-center mb-10 md:mb-16">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl"><?= htmlspecialchars($titulo) ?></h2>
            <?php if (!empty($subtitulo)): ?>
            <p class="mt-4 max-w-3xl mx-auto text-lg text-gray-600"><?= htmlspecialchars($subtitulo) ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
            <?php foreach ($tarjetas as $tarjeta): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center transform transition-all duration-300 hover:shadow-lg hover:-translate-y-2">
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-primario-light mb-6">
                        <i class="fas <?= htmlspecialchars($tarjeta['icono'] ?? 'fa-check') ?> text-3xl text-primario"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900"><?= htmlspecialchars($tarjeta['titulo'] ?? '') ?></h3>
                    <p class="mt-2 text-base text-gray-600"><?= htmlspecialchars($tarjeta['descripcion'] ?? '') ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>