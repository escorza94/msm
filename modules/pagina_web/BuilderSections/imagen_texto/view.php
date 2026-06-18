<?php
$imagen = $config['imagen'] ?? '';
$contenido = $config['contenido'] ?? '';
$posicion = $config['posicion_imagen'] ?? 'izquierda';
if (empty($imagen) || empty($contenido)) return;

$ordenImagen = ($posicion === 'derecha') ? 'md:order-last' : '';
?>
<section class="py-12 md:py-16 bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-center">
            <div class="aspect-w-3 aspect-h-2 rounded-lg overflow-hidden <?= $ordenImagen ?>">
                <img src="<?= base_url($imagen) ?>" alt="Imagen de sección" class="w-full h-full object-cover">
            </div>
            <div class="prose lg:prose-lg text-gray-600"><?= $contenido ?></div>
        </div>
    </div>
</section>