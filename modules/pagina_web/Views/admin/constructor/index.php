<div class="max-w-5xl mx-auto mt-6">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-layer-group text-indigo-500 mr-3"></i> Constructor Visual</h2>
            <p class="text-sm text-gray-500 mt-1">Arma la página <b><?= htmlspecialchars($pagina['titulo']) ?></b> arrastrando y ordenando secciones.</p>
        </div>
        <div class="flex items-center gap-3 relative">
            <a href="<?= base_url('pagina_web/paginas') ?>" class="text-sm bg-white border border-gray-200 text-gray-600 px-3 py-2 rounded-lg font-medium hover:bg-gray-50 transition flex items-center shadow-sm"><i class="fas fa-arrow-left mr-2"></i> Páginas</a>
            <button onclick="toggleDropdown()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-bold transition text-sm flex items-center shadow-md">
                <i class="fas fa-plus mr-2"></i> Añadir Sección
            </button>
            
            <!-- Dropdown Menú -->
            <div id="dropdown-secciones" class="hidden absolute right-0 top-full mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-50">
                <div class="p-3 border-b border-gray-50 text-xs font-bold text-gray-400 uppercase tracking-wider">Elige el tipo de bloque</div>
                <a href="<?= base_url('pagina_web/constructor/seccion?pagina_id=' . $pagina['id'] . '&tipo=carrusel_banners') ?>" class="flex items-center p-3 hover:bg-indigo-50 transition border-b border-gray-50">
                    <div class="w-10 h-10 rounded bg-indigo-100 text-indigo-600 flex items-center justify-center mr-3"><i class="fas fa-images"></i></div>
                    <div><h4 class="text-sm font-bold text-gray-700">Carrusel Banners</h4><p class="text-[10px] text-gray-500">Imágenes deslizables</p></div>
                </a>
                <a href="<?= base_url('pagina_web/constructor/seccion?pagina_id=' . $pagina['id'] . '&tipo=grid_productos') ?>" class="flex items-center p-3 hover:bg-green-50 transition border-b border-gray-50">
                    <div class="w-10 h-10 rounded bg-green-100 text-green-600 flex items-center justify-center mr-3"><i class="fas fa-th"></i></div>
                    <div><h4 class="text-sm font-bold text-gray-700">Cuadrícula Productos</h4><p class="text-[10px] text-gray-500">Vincula una colección</p></div>
                </a>
                <a href="<?= base_url('pagina_web/constructor/seccion?pagina_id=' . $pagina['id'] . '&tipo=tarjetas_info') ?>" class="flex items-center p-3 hover:bg-amber-50 transition">
                    <div class="w-10 h-10 rounded bg-amber-100 text-amber-600 flex items-center justify-center mr-3"><i class="fas fa-star"></i></div>
                    <div><h4 class="text-sm font-bold text-gray-700">Tarjetas Info</h4><p class="text-[10px] text-gray-500">Beneficios e iconos</p></div>
                </a>
            </div>
        </div>
    </div>

    <?php if(isset($_GET['success'])): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-xl flex items-center shadow-sm text-sm">
            <i class="fas fa-check-circle text-lg mr-3"></i> <div><?= htmlspecialchars($_GET['success']) ?></div>
        </div>
    <?php endif; ?>

    <!-- Contenedor Drag & Drop -->
    <div id="sortable-list" class="space-y-4">
        <?php if(empty($secciones)): ?>
            <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl p-10 text-center text-gray-400">
                <i class="fas fa-cubes text-4xl mb-3 opacity-50"></i>
                <p>La página está vacía. Añade tu primera sección para empezar a construir.</p>
            </div>
        <?php else: foreach($secciones as $sec): 
            $icono = 'fa-cube'; $color = 'bg-gray-100 text-gray-500';
            if ($sec['tipo'] === 'carrusel_banners') { $icono = 'fa-images'; $color = 'bg-indigo-100 text-indigo-600'; }
            if ($sec['tipo'] === 'grid_productos') { $icono = 'fa-th'; $color = 'bg-green-100 text-green-600'; }
            if ($sec['tipo'] === 'tarjetas_info') { $icono = 'fa-star'; $color = 'bg-amber-100 text-amber-600'; }
        ?>
            <div class="sortable-item bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center group transition hover:shadow-md cursor-move" data-id="<?= $sec['id'] ?>">
                <div class="w-8 flex justify-center text-gray-300 group-hover:text-gray-500 mr-2"><i class="fas fa-grip-vertical"></i></div>
                <div class="w-12 h-12 rounded-lg <?= $color ?> flex items-center justify-center text-xl mr-4 flex-shrink-0"><i class="fas <?= $icono ?>"></i></div>
                
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-gray-800"><?= htmlspecialchars($sec['nombre_interno']) ?></h3>
                        <?php if($sec['estado'] === 'inactivo'): ?><span class="px-2 py-0.5 bg-red-100 text-red-700 text-[10px] rounded uppercase font-bold">Oculto</span><?php endif; ?>
                    </div>
                    <p class="text-xs text-gray-500 font-mono mt-0.5">Tipo: <?= $sec['tipo'] ?></p>
                </div>

                <div class="flex gap-2">
                    <a href="<?= base_url('pagina_web/constructor/seccion?pagina_id=' . $pagina['id'] . '&id=' . $sec['id']) ?>" class="w-9 h-9 flex items-center justify-center bg-gray-50 hover:bg-indigo-50 text-gray-600 hover:text-indigo-600 rounded transition"><i class="fas fa-edit"></i></a>
                    <a href="<?= base_url('pagina_web/constructor/eliminarSeccion?pagina_id=' . $pagina['id'] . '&id=' . $sec['id']) ?>" onclick="return confirm('¿Seguro que deseas borrar este bloque?')" class="w-9 h-9 flex items-center justify-center bg-gray-50 hover:bg-red-50 text-gray-600 hover:text-red-600 rounded transition"><i class="fas fa-trash-alt"></i></a>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
    
    <div class="mt-8 text-center">
        <a href="<?= $pagina['slug'] === 'inicio' ? base_url() : base_url('pagina?slug=' . $pagina['slug']) ?>" target="_blank" class="text-gray-500 hover:text-indigo-600 text-sm font-medium"><i class="fas fa-external-link-alt mr-1"></i> Ver Página Pública</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    function toggleDropdown() { document.getElementById('dropdown-secciones').classList.toggle('hidden'); }
    document.addEventListener('click', function(e) { 
        if(!e.target.closest('.relative')) document.getElementById('dropdown-secciones').classList.add('hidden'); 
    });

    document.addEventListener('DOMContentLoaded', function() {
        var el = document.getElementById('sortable-list');
        if (el) {
            Sortable.create(el, {
                animation: 150,
                handle: '.sortable-item',
                ghostClass: 'opacity-50',
                onEnd: function (evt) {
                    let orden = [];
                    document.querySelectorAll('.sortable-item').forEach(item => {
                        orden.push(item.getAttribute('data-id'));
                    });

                    fetch('<?= base_url('pagina_web/constructor/ordenar') ?>', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify({orden: orden, pagina_id: <?= $pagina['id'] ?>})
                    }).then(r => r.json()).then(res => {
                        if(res.status !== 'success') alert('Error al guardar el nuevo orden');
                    });
                }
            });
        }
    });
</script>