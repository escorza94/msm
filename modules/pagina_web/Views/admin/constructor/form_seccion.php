<div class="max-w-4xl mx-auto mt-6 mb-10">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-puzzle-piece text-indigo-500 mr-3"></i> <?= $titulo ?></h2>
            <p class="text-sm text-gray-500 mt-1">Configurando módulo: <span class="font-mono text-indigo-600"><?= $tipo ?></span></p>
        </div>
        <a href="<?= base_url('pagina_web/constructor?pagina_id=' . $pagina_id) ?>" class="text-sm bg-white border border-gray-200 text-gray-600 px-3 py-1.5 rounded-lg font-medium hover:bg-gray-50 transition flex items-center"><i class="fas fa-arrow-left mr-2"></i> Volver</a>
    </div>

    <form action="<?= base_url('pagina_web/constructor/guardarSeccion') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
        <input type="hidden" name="id" value="<?= $seccion['id'] ?? 0 ?>">
        <input type="hidden" name="pagina_id" value="<?= $pagina_id ?>">
        <input type="hidden" name="tipo" value="<?= $tipo ?>">

        <!-- Ajustes Generales -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Ajustes Base</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nombre Interno</label>
                    <input type="text" name="nombre_interno" value="<?= htmlspecialchars($seccion['nombre_interno'] ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-indigo-500 outline-none" placeholder="Ej. Banners Buen Fin" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Estado</label>
                    <select name="estado" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-indigo-500 outline-none">
                        <option value="activo" <?= ($seccion['estado'] ?? '') === 'activo' ? 'selected' : '' ?>>Visible en la Web</option>
                        <option value="inactivo" <?= ($seccion['estado'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Oculto (Borrador)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Configuración Específica por Tipo -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Contenido de la Sección</h3>

            <?php if($tipo === 'carrusel_banners'): ?>
                <div id="banners-container" class="space-y-4">
                    <?php $banners = $seccion['config']['banners'] ?? [['titulo'=>'', 'enlace'=>'', 'imagen'=>'']]; ?>
                    <?php foreach($banners as $index => $b): ?>
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg flex items-center gap-4 relative banner-item">
                            <div class="w-24 h-24 bg-gray-200 rounded overflow-hidden flex-shrink-0 border border-gray-300">
                                <?php if(!empty($b['imagen'])): ?><img src="<?= base_url($b['imagen']) ?>" class="w-full h-full object-cover"><?php else: ?><div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fas fa-image text-2xl"></i></div><?php endif; ?>
                            </div>
                            <div class="flex-1 space-y-2">
                                <input type="hidden" name="banner_imagen_existente[]" value="<?= htmlspecialchars($b['imagen'] ?? '') ?>">
                                <input type="file" name="banner_imagen[]" class="text-xs w-full mb-1" accept="image/*">
                                <input type="text" name="banner_titulo[]" value="<?= htmlspecialchars($b['titulo'] ?? '') ?>" class="w-full border border-gray-300 rounded px-3 py-1.5 text-xs outline-none" placeholder="Texto Alternativo (SEO)">
                                <input type="text" name="banner_enlace[]" value="<?= htmlspecialchars($b['enlace'] ?? '') ?>" class="w-full border border-gray-300 rounded px-3 py-1.5 text-xs outline-none" placeholder="URL Enlace (Ej. /ofertas)">
                            </div>
                            <button type="button" onclick="this.closest('.banner-item').remove()" class="text-red-500 hover:text-red-700 p-2"><i class="fas fa-times"></i></button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" onclick="agregarBanner()" class="mt-4 px-4 py-2 bg-indigo-50 text-indigo-600 rounded text-sm font-bold w-full hover:bg-indigo-100 transition border border-indigo-100 border-dashed">+ Añadir otra imagen</button>
                <script>
                    function agregarBanner() {
                        const html = `<div class="p-4 bg-gray-50 border border-gray-200 rounded-lg flex items-center gap-4 relative banner-item"><div class="w-24 h-24 bg-gray-200 rounded overflow-hidden flex-shrink-0 border border-gray-300"><div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fas fa-image text-2xl"></i></div></div><div class="flex-1 space-y-2"><input type="hidden" name="banner_imagen_existente[]" value=""><input type="file" name="banner_imagen[]" class="text-xs w-full mb-1" accept="image/*"><input type="text" name="banner_titulo[]" class="w-full border border-gray-300 rounded px-3 py-1.5 text-xs outline-none" placeholder="Texto Alternativo (SEO)"><input type="text" name="banner_enlace[]" class="w-full border border-gray-300 rounded px-3 py-1.5 text-xs outline-none" placeholder="URL Enlace (Ej. /ofertas)"></div><button type="button" onclick="this.closest('.banner-item').remove()" class="text-red-500 hover:text-red-700 p-2"><i class="fas fa-times"></i></button></div>`;
                        document.getElementById('banners-container').insertAdjacentHTML('beforeend', html);
                    }
                </script>

            <?php elseif($tipo === 'grid_productos'): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Título Público de la Sección</label>
                        <input type="text" name="titulo_seccion" value="<?= htmlspecialchars($seccion['config']['titulo_seccion'] ?? 'Productos Destacados') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-indigo-500 outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Subtítulo (Opcional)</label>
                        <input type="text" name="subtitulo" value="<?= htmlspecialchars($seccion['config']['subtitulo'] ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Colección a Mostrar</label>
                        <select name="coleccion_slug" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-indigo-500 outline-none">
                            <?php foreach($colecciones as $c): ?>
                                <option value="<?= $c['slug'] ?>" <?= ($seccion['config']['coleccion_slug'] ?? '') === $c['slug'] ? 'selected' : '' ?>><?= htmlspecialchars($c['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Límite de Productos</label>
                        <input type="number" name="limite_mostrar" value="<?= intval($seccion['config']['limite_mostrar'] ?? 8) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-indigo-500 outline-none">
                    </div>
                </div>
            
            <?php elseif($tipo === 'tarjetas_info'): ?>
                <div class="mb-4 space-y-4">
                    <input type="text" name="titulo_seccion" value="<?= htmlspecialchars($seccion['config']['titulo_seccion'] ?? 'Nuestros Beneficios') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-indigo-500 outline-none" placeholder="Título (Opcional)">
                    <input type="text" name="subtitulo" value="<?= htmlspecialchars($seccion['config']['subtitulo'] ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-indigo-500 outline-none" placeholder="Subtítulo (Opcional)">
                </div>
                <div id="tarjetas-container" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <?php $tarjetas = $seccion['config']['tarjetas'] ?? [['titulo'=>'Calidad', 'icono'=>'fas fa-gem', 'descripcion'=>'']]; ?>
                    <?php foreach($tarjetas as $index => $t): ?>
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg tarjeta-item relative">
                            <button type="button" onclick="this.closest('.tarjeta-item').remove()" class="absolute top-2 right-2 text-red-500"><i class="fas fa-times"></i></button>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Ícono (FA)</label>
                            <input type="text" name="tarjeta_icono[]" value="<?= htmlspecialchars($t['icono'] ?? '') ?>" class="w-full border border-gray-300 rounded px-2 py-1 text-xs mb-2 outline-none" placeholder="fas fa-truck">
                            <label class="block text-xs font-bold text-gray-500 mb-1">Título</label>
                            <input type="text" name="tarjeta_titulo[]" value="<?= htmlspecialchars($t['titulo'] ?? '') ?>" class="w-full border border-gray-300 rounded px-2 py-1 text-xs mb-2 outline-none">
                            <label class="block text-xs font-bold text-gray-500 mb-1">Texto</label>
                            <textarea name="tarjeta_descripcion[]" class="w-full border border-gray-300 rounded px-2 py-1 text-xs outline-none resize-none h-16"><?= htmlspecialchars($t['descripcion'] ?? '') ?></textarea>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" onclick="agregarTarjeta()" class="mt-4 px-4 py-2 bg-amber-50 text-amber-600 rounded text-sm font-bold w-full hover:bg-amber-100 transition border border-amber-100 border-dashed">+ Añadir Tarjeta</button>
                <script>
                    function agregarTarjeta() {
                        const html = `<div class="p-4 bg-gray-50 border border-gray-200 rounded-lg tarjeta-item relative"><button type="button" onclick="this.closest('.tarjeta-item').remove()" class="absolute top-2 right-2 text-red-500"><i class="fas fa-times"></i></button><label class="block text-xs font-bold text-gray-500 mb-1">Ícono (FA)</label><input type="text" name="tarjeta_icono[]" class="w-full border border-gray-300 rounded px-2 py-1 text-xs mb-2 outline-none" placeholder="fas fa-star"><label class="block text-xs font-bold text-gray-500 mb-1">Título</label><input type="text" name="tarjeta_titulo[]" class="w-full border border-gray-300 rounded px-2 py-1 text-xs mb-2 outline-none"><label class="block text-xs font-bold text-gray-500 mb-1">Texto</label><textarea name="tarjeta_descripcion[]" class="w-full border border-gray-300 rounded px-2 py-1 text-xs outline-none resize-none h-16"></textarea></div>`;
                        document.getElementById('tarjetas-container').insertAdjacentHTML('beforeend', html);
                    }
                </script>
            <?php endif; ?>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg shadow font-bold hover:bg-indigo-700 transition flex items-center"><i class="fas fa-save mr-2"></i> Guardar Sección</button>
        </div>
    </form>
</div>