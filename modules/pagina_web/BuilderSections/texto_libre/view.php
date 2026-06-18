<?php
$contenido = $config['contenido'] ?? '';
if (empty($contenido)) return;
?>
<section class="py-12 md:py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="prose lg:prose-lg mx-auto text-gray-600">
            <?= $contenido ?>
        </div>
    </div>
</section>