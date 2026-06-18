<div class="max-w-5xl mx-auto mt-6 mb-10">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-layer-group text-indigo-500 mr-3"></i> Constructor Visual</h2>
            <p class="text-sm text-gray-500 mt-1">Editando la página: <span class="font-bold text-gray-800"><?= htmlspecialchars($pagina['titulo']) ?></span></p>
        </div>
        <div class="flex gap-3">
            <?php if(has_permission('pagina_web.crear')): ?>
            <div class="relative group">
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg shadow-sm font-bold hover:bg-indigo-700 transition flex items-center text-sm">
                    <i class="fas fa-plus mr-2"></i> Añadir Widget
                </button>
                <!-- Dropdown de Tipos de Sección -->
                <div class="absolute right-0 mt-2 w-56 bg-white border border-gray-100 rounded-xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 overflow-hidden">
                    <a href="<?= base_url("pagina_web/constructor/seccion?pagina_id={$pagina['id']}&tipo=carrusel_banners") ?>" class="block px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50 border-b border-gray-50"><i class="fas fa-images text-indigo-400 w-6"></i> Carrusel de Banners</a>
                    <a href="<?= base_url("pagina_web/constructor/seccion?pagina_id={$pagina['id']}&tipo=grid_productos") ?>" class="block px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50 border-b border-gray-50"><i class="fas fa-th text-green-400 w-6"></i> Grid de Productos</a>
                    <a href="<?= base_url("pagina_web/constructor/seccion?pagina_id={$pagina['id']}&tipo=tarjetas_info") ?>" class="block px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50 border-b border-gray-50"><i class="fas fa-info-circle text-blue-400 w-6"></i> Tarjetas de Información</a>
                    <a href="<?= base_url("pagina_web/constructor/seccion?pagina_id={$pagina['id']}&tipo=texto_libre") ?>" class="block px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50"><i class="fas fa-align-left text-gray-400 w-6"></i> Bloque de Texto Libre</a>
                    <a href="<?= base_url("pagina_web/constructor/seccion?pagina_id={$pagina['id']}&tipo=imagen_texto") ?>" class="block px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50"><i class="fas fa-id-card text-purple-400 w-6"></i> Imagen con Texto</a>
                    <a href="<?= base_url("pagina_web/constructor/seccion?pagina_id={$pagina['id']}&tipo=grid_promociones") ?>" class="block px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50"><i class="fas fa-tags text-pink-400 w-6"></i> Grid de Promociones</a>
                </div>
            </div>
            <?php endif; ?>
            <a href="<?= base_url('pagina_web/paginas') ?>" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition font-medium text-sm flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>
    </div>

    <?php if(isset($_GET['success'])): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-xl flex items-center shadow-sm text-sm">
            <i class="fas fa-check-circle text-lg mr-3"></i> <div><?= htmlspecialchars($_GET['success']) ?></div>
        </div>
    <?php endif; ?>

    <!-- Lista de Secciones Arrastrables -->
    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 border-dashed">
        <?php if(empty($secciones)): ?>
            <div class="text-center py-10">
                <i class="fas fa-puzzle-piece text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 font-medium">Esta página aún no tiene contenido.</p>
                <p class="text-sm text-gray-400 mt-1">Haz clic en "Añadir Widget" para empezar a diseñarla.</p>
            </div>
        <?php else: ?>
            <div id="lista-secciones" class="space-y-3">
                <?php foreach($secciones as $sec): ?>
                    <div class="bg-white border border-gray-200 rounded-lg p-4 flex items-center justify-between shadow-sm group hover:border-indigo-300 transition cursor-move" data-id="<?= $sec['id'] ?>">
                        <div class="flex items-center gap-4">
                            <div class="text-gray-300 group-hover:text-indigo-500 cursor-move"><i class="fas fa-grip-vertical text-xl"></i></div>
                            <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-500">
                                <?php if($sec['tipo'] === 'carrusel_banners'): ?><i class="fas fa-images text-indigo-500"></i>
                                <?php elseif($sec['tipo'] === 'grid_productos'): ?><i class="fas fa-th text-green-500"></i>
                                <?php elseif($sec['tipo'] === 'tarjetas_info'): ?><i class="fas fa-info-circle text-blue-500"></i>
                                <?php elseif($sec['tipo'] === 'imagen_texto'): ?><i class="fas fa-id-card text-purple-500"></i>
                                <?php elseif($sec['tipo'] === 'grid_promociones'): ?><i class="fas fa-tags text-pink-500"></i>
                                <?php else: ?><i class="fas fa-align-left text-gray-500"></i><?php endif; ?>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800"><?= htmlspecialchars($sec['nombre_interno']) ?></h3>
                                <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider"><?= str_replace('_', ' ', $sec['tipo']) ?> &bull; <span class="<?= $sec['estado'] === 'activo' ? 'text-green-500' : 'text-red-500' ?>"><?= $sec['estado'] ?></span></p>
                            </div>
                        </div>
                        <?php if(has_permission('pagina_web.crear')): ?>
                        <div class="flex items-center gap-2 opacity-50 group-hover:opacity-100 transition">
                            <a href="<?= base_url("pagina_web/constructor/seccion?id={$sec['id']}&pagina_id={$pagina['id']}&tipo={$sec['tipo']}") ?>" class="w-8 h-8 flex items-center justify-center rounded-md bg-blue-50 text-blue-600 hover:bg-blue-100" title="Editar"><i class="fas fa-edit"></i></a>
                            <a href="<?= base_url("pagina_web/constructor/eliminarSeccion?id={$sec['id']}&pagina_id={$pagina['id']}") ?>" onclick="return confirm('¿Seguro que deseas eliminar esta sección?')" class="w-8 h-8 flex items-center justify-center rounded-md bg-red-50 text-red-600 hover:bg-red-100" title="Eliminar"><i class="fas fa-trash-alt"></i></a>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <p class="text-center text-xs text-gray-400 mt-4"><i class="fas fa-info-circle mr-1"></i> Arrastra los bloques hacia arriba o abajo para reordenarlos.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    <?php if(has_permission('pagina_web.crear')): ?>
    document.addEventListener('DOMContentLoaded', function() {
        const el = document.getElementById('lista-secciones');
        if (el) {
            new Sortable(el, { animation: 150, handle: '.cursor-move', onEnd: function (evt) {
                let orden = Array.from(el.children).map(item => item.getAttribute('data-id'));
                fetch('<?= base_url('pagina_web/constructor/ordenar') ?>', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ pagina_id: <?= $pagina['id'] ?>, orden: orden }) });
            }});
        }
    });
    <?php endif; ?>
</script>