<?php
// $config contiene la configuración de la sección.
// $productos es inyectado por el StorefrontController.
$titulo = $config['titulo_seccion'] ?? '';
$subtitulo = $config['subtitulo'] ?? '';

// No renderizar nada si no hay productos que mostrar.
if (empty($productos)) return;
?>
<section class="py-12 md:py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (!empty($titulo)): ?>
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl"><?= htmlspecialchars($titulo) ?></h2>
            <?php if (!empty($subtitulo)): ?>
            <p class="mt-3 max-w-2xl mx-auto text-lg text-gray-500"><?= htmlspecialchars($subtitulo) ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-10">
            <?php foreach ($productos ?? [] as $producto): ?>
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden flex flex-col group transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                    <a href="<?= base_url('producto/' . $producto['id']) ?>" class="relative overflow-hidden">
                        <div class="aspect-w-1 aspect-h-1">
                            <img src="<?= base_url($producto['imagen'] ?? 'storage/assets/placeholder.png') ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" class="h-full w-full object-cover object-center group-hover:scale-105 transition-transform duration-300">
                        </div>
                    </a>
                    <div class="p-4 flex flex-col flex-1">
                        <h3 class="text-sm text-gray-800 font-bold truncate" title="<?= htmlspecialchars($producto['nombre']) ?>"><?= htmlspecialchars($producto['nombre']) ?></h3>
                        <p class="mt-1 text-xl font-black text-primario">$<?= number_format($producto['precio'], 2) ?></p>
                        <div class="mt-auto pt-4 space-y-2">
                            <a href="<?= base_url('producto/' . $producto['id']) ?>" class="block w-full bg-primario text-white text-center py-2 rounded-lg text-sm font-bold hover:bg-primario-dark transition">
                                Ver Detalles
                            </a>
                            <a href="https://wa.me/<?= htmlspecialchars($global_config['whatsapp_numero'] ?? '') ?>?text=Hola,%20me%20interesa%20el%20producto:%20<?= urlencode($producto['nombre']) ?>" target="_blank" class="block w-full bg-green-500 text-white text-center py-2 rounded-lg text-sm font-bold hover:bg-green-600 transition">
                                <i class="fab fa-whatsapp mr-1.5"></i> WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>