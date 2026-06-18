<?php
    // Incluimos el header para mantener la consistencia
    $headerPath = __DIR__ . '/../partials/header.php';
    if (file_exists($headerPath)) require $headerPath;
?>

    <main class="py-12 md:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-12">
                
                <!-- Columna de Galería de Imágenes -->
                <div class="space-y-4">
                    <div class="aspect-w-1 aspect-h-1 w-full bg-secciones rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <?php if(!empty($imagenes)): ?>
                            <img id="img-principal" src="<?= base_url($imagenes[0]['ruta']) ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" class="w-full h-full object-contain p-4">
                        <?php else: ?>
                            <div class="flex items-center justify-center h-full"><i class="fas fa-box-open text-6xl text-gray-300"></i></div>
                        <?php endif; ?>
                    </div>
                    <?php if(count($imagenes) > 1): ?>
                        <div class="grid grid-cols-5 gap-3">
                            <?php foreach($imagenes as $idx => $img): ?>
                                <div class="aspect-w-1 aspect-h-1 bg-secciones rounded-lg border-2 <?= $idx === 0 ? 'border-primario' : 'border-gray-200' ?> overflow-hidden cursor-pointer thumb-container" onclick="cambiarImagen('<?= base_url($img['ruta']) ?>', this)">
                                    <img src="<?= base_url($img['ruta']) ?>" class="w-full h-full object-cover">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Columna de Información y Compra -->
                <div class="bg-secciones p-6 md:p-8 rounded-xl shadow-sm border border-gray-100">
                    <div class="space-y-4">
                        <?php if(!empty($producto['categoria_nombre'])): ?>
                            <a href="#" class="text-sm font-bold uppercase tracking-wider text-primario hover:underline"><?= htmlspecialchars($producto['categoria_nombre']) ?></a>
                        <?php endif; ?>
                        
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900"><?= htmlspecialchars($producto['nombre']) ?></h1>
                        
                        <div class="flex items-center gap-4">
                            <p class="text-4xl font-black text-primario">$<?= number_format($producto['precio'], 2) ?></p>
                            <?php if($producto['stock'] > 0): ?>
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold flex items-center gap-1.5"><div class="w-2 h-2 bg-green-500 rounded-full"></div> En Stock</span>
                            <?php else: ?>
                                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">Agotado</span>
                            <?php endif; ?>
                        </div>

                        <div class="prose prose-sm text-gray-600 max-w-none pt-4 border-t border-gray-100">
                            <?= nl2br(htmlspecialchars($producto['descripcion'])) ?>
                        </div>

                        <div class="pt-6">
                            <a href="https://wa.me/<?= htmlspecialchars($config['whatsapp_numero'] ?? '') ?>?text=Hola,%20me%20interesa%20el%20producto:%20<?= urlencode($producto['nombre']) ?>" target="_blank" class="w-full bg-green-500 text-white text-center py-4 rounded-lg text-lg font-bold hover:bg-green-600 transition flex items-center justify-center gap-3 shadow-lg hover:shadow-xl">
                                <i class="fab fa-whatsapp text-2xl"></i> Pedir por WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productos Relacionados -->
            <?php if(!empty($relacionados)): ?>
            <div class="mt-16 md:mt-24">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">También te podría interesar</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <?php foreach($relacionados as $rel): ?>
                        <a href="<?= base_url('producto/' . $rel['id']) ?>" class="bg-secciones rounded-lg shadow-sm border border-gray-100 overflow-hidden group transition-all duration-300 hover:shadow-md hover:-translate-y-1">
                            <div class="aspect-w-1 aspect-h-1">
                                <img src="<?= base_url($rel['imagen'] ?? 'storage/assets/placeholder.png') ?>" alt="<?= htmlspecialchars($rel['nombre']) ?>" class="h-full w-full object-cover object-center group-hover:scale-105 transition-transform duration-300">
                            </div>
                            <div class="p-4">
                                <h3 class="text-sm text-gray-700 font-medium truncate"><?= htmlspecialchars($rel['nombre']) ?></h3>
                                <p class="mt-1 text-lg font-bold text-gray-900">$<?= number_format($rel['precio'], 2) ?></p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <?php
        $footerPath = __DIR__ . '/../partials/footer.php';
        if (file_exists($footerPath)) require $footerPath;
    ?>

    <script>
        function cambiarImagen(src, element) {
            document.getElementById('img-principal').src = src;
            document.querySelectorAll('.thumb-container').forEach(el => el.classList.remove('border-primario'));
            element.classList.add('border-primario');
        }
    </script>

```
