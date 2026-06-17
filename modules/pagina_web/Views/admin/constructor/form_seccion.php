<div class="max-w-4xl mx-auto mt-6 mb-10">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-cube text-indigo-500 mr-3"></i> <?= $titulo ?></h2>
            <p class="text-sm text-gray-500 mt-1 uppercase tracking-wide font-bold">Tipo: <span class="text-indigo-600"><?= str_replace('_', ' ', $tipo) ?></span></p>
        </div>
        <a href="<?= base_url('pagina_web/constructor?pagina_id=' . $pagina_id) ?>" class="text-sm bg-white border border-gray-200 text-gray-600 px-3 py-1.5 rounded-lg font-medium hover:bg-gray-50 transition flex items-center"><i class="fas fa-arrow-left mr-2"></i> Cancelar</a>
    </div>

    <form action="<?= base_url('pagina_web/constructor/guardarSeccion') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
        <input type="hidden" name="id" value="<?= $seccion['id'] ?? 0 ?>">
        <input type="hidden" name="pagina_id" value="<?= $pagina_id ?>">
        <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo) ?>">

        <!-- Configuraciones Generales -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2"><i class="fas fa-cog text-gray-400 mr-2"></i> Ajustes Básicos</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nombre Interno (Solo para identificador)</label>
                    <input type="text" name="nombre_interno" value="<?= htmlspecialchars($seccion['nombre_interno'] ?? '') ?>" required class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-indigo-500 outline-none" placeholder="Ej. Carrusel Principal">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Estado de Visibilidad</label>
                    <select name="estado" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-indigo-500 outline-none bg-white">
                        <option value="activo" <?= ($seccion['estado'] ?? '') === 'activo' ? 'selected' : '' ?>>🌟 Visible al público</option>
                        <option value="inactivo" <?= ($seccion['estado'] ?? '') === 'inactivo' ? 'selected' : '' ?>>🙈 Oculto temporalmente</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- DINÁMICO: Texto Libre -->
        <?php if ($tipo === 'texto_libre'): ?>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2"><i class="fas fa-pen text-indigo-500 mr-2"></i> Contenido del Bloque</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Título Grande (Opcional)</label>
                    <input type="text" name="titulo_seccion" value="<?= htmlspecialchars($seccion['config']['titulo_seccion'] ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-indigo-500 outline-none" placeholder="Ej. Nuestra Historia">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Texto o Párrafos</label>
                    <textarea name="contenido" rows="8" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-indigo-500 outline-none"><?= htmlspecialchars($seccion['config']['contenido'] ?? '') ?></textarea>
                    <p class="text-[10px] text-gray-400 mt-1">Los saltos de línea se respetarán en la página web automáticamente.</p>
                </div>
            </div>
        </div>

        <!-- DINÁMICO: Grid Productos -->
        <?php elseif ($tipo === 'grid_productos'): ?>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2"><i class="fas fa-th text-green-500 mr-2"></i> Catálogo de Exhibición</h3>
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Título de la Sección</label>
                        <input type="text" name="titulo_seccion" value="<?= htmlspecialchars($seccion['config']['titulo_seccion'] ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm outline-none" placeholder="Ej. Lo Más Vendido">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Subtítulo (Opcional)</label>
                        <input type="text" name="subtitulo" value="<?= htmlspecialchars($seccion['config']['subtitulo'] ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm outline-none" placeholder="Ej. Encuentra tu favorito">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">¿Qué colección mostrar?</label>
                        <select name="coleccion_slug" required class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm outline-none bg-white">
                            <option value="">-- Selecciona una --</option>
                            <?php foreach($colecciones as $col): ?>
                                <option value="<?= $col['slug'] ?>" <?= ($seccion['config']['coleccion_slug'] ?? '') === $col['slug'] ? 'selected' : '' ?>><?= htmlspecialchars($col['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Límite de Productos a mostrar</label>
                        <input type="number" name="limite_mostrar" value="<?= intval($seccion['config']['limite_mostrar'] ?? 8) ?>" min="1" max="24" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm outline-none">
                    </div>
                </div>
            </div>
        </div>

        <!-- DINÁMICO: Tarjetas Informativas -->
        <?php elseif ($tipo === 'tarjetas_info'): ?>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2"><i class="fas fa-star text-amber-500 mr-2"></i> Tarjetas (Ventajas / Beneficios)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Título de Sección</label><input type="text" name="titulo_seccion" value="<?= htmlspecialchars($seccion['config']['titulo_seccion'] ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm"></div>
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Subtítulo</label><input type="text" name="subtitulo" value="<?= htmlspecialchars($seccion['config']['subtitulo'] ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm"></div>
            </div>
            <div id="tarjetas-container" class="space-y-3">
                <?php $tarjetas = $seccion['config']['tarjetas'] ?? [['titulo'=>'', 'icono'=>'fas fa-star', 'descripcion'=>'']]; ?>
                <?php foreach($tarjetas as $t): ?>
                <div class="flex gap-2 items-start bg-gray-50 p-3 rounded-lg border border-gray-200">
                    <input type="text" name="tarjeta_icono[]" value="<?= htmlspecialchars($t['icono']) ?>" class="w-20 border border-gray-300 rounded-md px-2 py-2 text-sm text-center" placeholder="fa-star">
                    <div class="flex-1 space-y-2">
                        <input type="text" name="tarjeta_titulo[]" value="<?= htmlspecialchars($t['titulo']) ?>" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm font-bold" placeholder="Ej. Envío Gratis">
                        <textarea name="tarjeta_descripcion[]" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" rows="2" placeholder="Descripción breve..."><?= htmlspecialchars($t['descripcion']) ?></textarea>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:bg-red-100 p-2 rounded"><i class="fas fa-trash"></i></button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" onclick="agregarTarjeta()" class="mt-3 text-sm text-indigo-600 font-bold hover:underline"><i class="fas fa-plus mr-1"></i> Añadir otra tarjeta</button>
            <script>function agregarTarjeta(){ document.getElementById('tarjetas-container').insertAdjacentHTML('beforeend', `<div class="flex gap-2 items-start bg-gray-50 p-3 rounded-lg border border-gray-200"><input type="text" name="tarjeta_icono[]" value="fas fa-check" class="w-20 border border-gray-300 rounded-md px-2 py-2 text-sm text-center"><div class="flex-1 space-y-2"><input type="text" name="tarjeta_titulo[]" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm font-bold" placeholder="Nuevo Beneficio"><textarea name="tarjeta_descripcion[]" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" rows="2" placeholder="Descripción..."></textarea></div><button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:bg-red-100 p-2 rounded"><i class="fas fa-trash"></i></button></div>`); }</script>
        </div>

        <!-- DINÁMICO: Carrusel Banners -->
        <?php elseif ($tipo === 'carrusel_banners'): ?>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2"><i class="fas fa-images text-blue-500 mr-2"></i> Imágenes del Carrusel</h3>
            <div id="banners-container" class="space-y-4">
                <?php $banners = $seccion['config']['banners'] ?? [['titulo'=>'', 'enlace'=>'', 'imagen'=>'']]; ?>
                <?php foreach($banners as $b): ?>
                <div class="flex flex-col md:flex-row gap-4 items-center bg-gray-50 p-4 rounded-lg border border-gray-200 relative">
                    <button type="button" onclick="this.parentElement.remove()" class="absolute top-2 right-2 text-red-500 hover:bg-red-100 p-1.5 rounded"><i class="fas fa-times"></i></button>
                    <div class="w-full md:w-32 h-20 bg-gray-200 rounded flex-shrink-0 overflow-hidden border border-gray-300">
                        <?php if(!empty($b['imagen'])): ?><img src="<?= base_url($b['imagen']) ?>" class="w-full h-full object-cover"><?php else: ?><div class="flex items-center justify-center h-full text-xs text-gray-400">Sin foto</div><?php endif; ?>
                    </div>
                    <div class="flex-1 w-full space-y-2">
                        <input type="hidden" name="banner_imagen_existente[]" value="<?= htmlspecialchars($b['imagen']) ?>">
                        <input type="file" name="banner_imagen[]" accept="image/*" class="text-xs text-gray-500 w-full file:mr-4 file:py-1.5 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <div class="flex gap-2">
                            <input type="text" name="banner_titulo[]" value="<?= htmlspecialchars($b['titulo']) ?>" class="w-1/2 border border-gray-300 rounded-md px-3 py-1.5 text-sm" placeholder="Título para accesibilidad">
                            <input type="text" name="banner_enlace[]" value="<?= htmlspecialchars($b['enlace']) ?>" class="w-1/2 border border-gray-300 rounded-md px-3 py-1.5 text-sm" placeholder="Enlace al hacer clic (Ej. #contacto)">
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" onclick="agregarBanner()" class="mt-4 px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg text-sm font-bold border border-indigo-100 hover:bg-indigo-100 transition"><i class="fas fa-plus mr-1"></i> Añadir otra imagen</button>
            <script>function agregarBanner(){ document.getElementById('banners-container').insertAdjacentHTML('beforeend', `<div class="flex flex-col md:flex-row gap-4 items-center bg-gray-50 p-4 rounded-lg border border-gray-200 relative"><button type="button" onclick="this.parentElement.remove()" class="absolute top-2 right-2 text-red-500 hover:bg-red-100 p-1.5 rounded"><i class="fas fa-times"></i></button><div class="w-full md:w-32 h-20 bg-gray-200 rounded flex-shrink-0 flex items-center justify-center text-xs text-gray-400 border border-gray-300">Nueva</div><div class="flex-1 w-full space-y-2"><input type="hidden" name="banner_imagen_existente[]" value=""><input type="file" name="banner_imagen[]" accept="image/*" class="text-xs text-gray-500 w-full file:mr-4 file:py-1.5 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required><div class="flex gap-2"><input type="text" name="banner_titulo[]" class="w-1/2 border border-gray-300 rounded-md px-3 py-1.5 text-sm" placeholder="Título"><input type="text" name="banner_enlace[]" class="w-1/2 border border-gray-300 rounded-md px-3 py-1.5 text-sm" placeholder="Enlace URL"></div></div></div>`); }</script>
        </div>
        
        <!-- DINÁMICO: Imagen con Texto -->
        <?php elseif ($tipo === 'imagen_texto'): ?>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2"><i class="fas fa-id-card text-purple-500 mr-2"></i> Imagen y Texto</h3>
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Título Grande</label>
                        <input type="text" name="titulo_seccion" value="<?= htmlspecialchars($seccion['config']['titulo_seccion'] ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm outline-none" placeholder="Ej. Sobre Nosotros">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Posición de la Imagen</label>
                        <select name="posicion_imagen" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm outline-none bg-white">
                            <option value="izquierda" <?= ($seccion['config']['posicion_imagen'] ?? '') === 'izquierda' ? 'selected' : '' ?>>Imagen a la Izquierda</option>
                            <option value="derecha" <?= ($seccion['config']['posicion_imagen'] ?? '') === 'derecha' ? 'selected' : '' ?>>Imagen a la Derecha</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Imagen</label>
                    <input type="hidden" name="imagen_existente" value="<?= htmlspecialchars($seccion['config']['imagen'] ?? '') ?>">
                    <input type="file" name="imagen" accept="image/*" class="w-full text-sm mb-2">
                    <?php if(!empty($seccion['config']['imagen'])): ?><img src="<?= base_url($seccion['config']['imagen']) ?>" class="h-24 rounded-lg shadow-sm border"><?php endif; ?>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Texto o Párrafos</label>
                    <textarea name="contenido" rows="6" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm outline-none"><?= htmlspecialchars($seccion['config']['contenido'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="flex justify-end pt-4">
            <button type="submit" class="px-6 py-3 bg-amber-500 text-white rounded-lg shadow-sm font-bold hover:bg-amber-600 transition flex items-center text-lg">
                <i class="fas fa-save mr-2"></i> Guardar Módulo
            </button>
        </div>
    </form>
</div>