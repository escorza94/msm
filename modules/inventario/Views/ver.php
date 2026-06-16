<div class="max-w-6xl mx-auto mt-8">
    <!-- Cabecera -->
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-tag text-blue-500 mr-3"></i> <?= htmlspecialchars($producto['nombre']) ?>
            </h2>
            <p class="text-sm text-gray-500 mt-1">Ficha técnica y detalles del producto</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= base_url('inventario') ?>" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 font-medium transition text-sm shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
            <a href="<?= base_url('inventario/editar?id=' . $producto['id']) ?>" class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 font-medium transition text-sm flex items-center">
                <i class="fas fa-edit mr-2"></i> Editar
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Columna Izquierda: Galería -->
        <div class="lg:col-span-5 space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <!-- Imagen Principal -->
                <div class="w-full aspect-square rounded-lg bg-gray-50 border border-gray-100 flex items-center justify-center overflow-hidden mb-4 relative group">
                    <?php if(!empty($imagenes)): ?>
                        <img id="img-principal" src="<?= base_url($imagenes[0]['ruta']) ?>" class="w-full h-full object-contain cursor-pointer transition-transform duration-300 group-hover:scale-105" onclick="abrirVisor(this.src)" alt="Producto">
                    <?php else: ?>
                        <i class="fas fa-box-open text-6xl text-gray-300"></i>
                    <?php endif; ?>
                </div>
                
                <!-- Miniaturas -->
                <?php if(count($imagenes) > 1): ?>
                    <div class="grid grid-cols-4 gap-2">
                        <?php foreach($imagenes as $idx => $img): ?>
                            <div class="aspect-square rounded border-2 <?= $idx === 0 ? 'border-blue-500' : 'border-transparent' ?> overflow-hidden cursor-pointer hover:opacity-80 transition thumb-container" onclick="cambiarImagen('<?= base_url($img['ruta']) ?>', this)">
                                <img src="<?= base_url($img['ruta']) ?>" class="w-full h-full object-cover">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Columna Derecha: Información -->
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex justify-between items-start mb-4 pb-4 border-b border-gray-100">
                    <div>
                        <span class="inline-block px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-mono font-bold mb-2 tracking-widest"><?= htmlspecialchars($producto['sku']) ?></span>
                        <h1 class="text-3xl font-bold text-gray-800"><?= htmlspecialchars($producto['nombre']) ?></h1>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-blue-600">$<?= number_format($producto['precio'], 2) ?></div>
                        <span class="inline-block mt-1 px-2 py-1 <?= $producto['estado'] === 'activo' ? 'bg-green-50 text-green-600' : 'bg-gray-100 text-gray-500' ?> rounded text-[10px] font-bold uppercase"><?= $producto['estado'] ?></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-y-6 gap-x-4 mb-6">
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Categoría</p>
                        <p class="font-medium text-gray-800 flex items-center"><i class="fas fa-folder-open text-blue-400 mr-2"></i> <?= htmlspecialchars($producto['categoria_nombre'] ?? 'General') ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Proveedor</p>
                        <p class="font-medium text-gray-800 flex items-center"><i class="fas fa-truck text-indigo-400 mr-2"></i> <?= htmlspecialchars($producto['proveedor_nombre'] ?? 'Interno / No especificado') ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Inventario Actual</p>
                        <p class="font-bold text-lg <?= $producto['stock'] <= $producto['stock_minimo'] ? 'text-red-500' : 'text-green-600' ?> flex items-center">
                            <i class="fas fa-cubes mr-2"></i> <?= $producto['stock'] ?> Unidades
                            <?php if($producto['stock'] <= $producto['stock_minimo']): ?> <span class="ml-2 text-[10px] bg-red-100 text-red-700 px-2 py-0.5 rounded-full uppercase">Stock Bajo</span> <?php endif; ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Ubicación Física</p>
                        <p class="font-medium text-gray-800 flex items-center"><i class="fas fa-map-marker-alt text-red-400 mr-2"></i> <?= htmlspecialchars($producto['ubicacion'] ?: 'No asignada') ?></p>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-2">Descripción</p>
                    <p class="text-sm text-gray-600 leading-relaxed bg-gray-50 p-4 rounded-lg"><?= nl2br(htmlspecialchars($producto['descripcion'] ?: 'Sin descripción detallada.')) ?></p>
                </div>
            </div>

            <!-- Etiqueta QR Inteligente -->
            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-5 flex items-center gap-6">
                <div class="w-24 h-24 bg-white p-2 rounded shadow-sm flex-shrink-0">
                    <!-- Usamos una API gratuita para generar el QR al vuelo con el SKU -->
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= urlencode($producto['sku']) ?>" alt="QR Code" class="w-full h-full">
                </div>
                <div>
                    <h4 class="font-bold text-indigo-900 mb-1">Etiqueta Inteligente</h4>
                    <p class="text-sm text-indigo-700 mb-3">Escanea este código con cualquier lector para buscar rápidamente el producto.</p>
                    <button onclick="window.print()" class="text-xs bg-white text-indigo-600 font-bold px-4 py-2 rounded shadow-sm hover:bg-indigo-600 hover:text-white transition"><i class="fas fa-print mr-1"></i> Imprimir Etiqueta</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function cambiarImagen(src, element) {
        document.getElementById('img-principal').src = src;
        document.querySelectorAll('.thumb-container').forEach(el => el.classList.remove('border-blue-500', 'border-transparent'));
        document.querySelectorAll('.thumb-container').forEach(el => el.classList.add('border-transparent'));
        element.classList.remove('border-transparent');
        element.classList.add('border-blue-500');
    }
</script>