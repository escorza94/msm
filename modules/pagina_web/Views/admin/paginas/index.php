<div class="max-w-5xl mx-auto mt-6 mb-10">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-file-alt text-blue-500 mr-3"></i> Gestor de Páginas</h2>
            <p class="text-sm text-gray-500 mt-1">Crea nuevas URLs en tu tienda y constrúyeles un diseño.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= base_url('pagina_web') ?>" class="text-sm bg-white border border-gray-200 text-gray-600 px-3 py-2 rounded-lg font-medium hover:bg-gray-50 transition flex items-center shadow-sm"><i class="fas fa-arrow-left mr-2"></i> Panel</a>
            <?php if(has_permission('pagina_web.crear')): ?>
            <button onclick="abrirModalPagina()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold transition text-sm flex items-center shadow-md">
                <i class="fas fa-plus mr-2"></i> Nueva Página
            </button>
            <?php endif; ?>
        </div>
    </div>

    <?php if(isset($_GET['success'])): ?><div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-xl flex items-center shadow-sm text-sm"><i class="fas fa-check-circle text-lg mr-3"></i> <div><?= htmlspecialchars($_GET['success']) ?></div></div><?php endif; ?>
    <?php if(isset($_GET['error'])): ?><div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-xl flex items-center shadow-sm text-sm"><i class="fas fa-exclamation-triangle text-lg mr-3"></i> <div><?= htmlspecialchars($_GET['error']) ?></div></div><?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase border-b border-gray-100">
                    <th class="p-4 font-medium">Título</th>
                    <th class="p-4 font-medium">Ruta (URL)</th>
                    <th class="p-4 font-medium text-center">Estado</th>
                    <th class="p-4 font-medium text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 divide-y divide-gray-50">
                <?php foreach($paginas as $p): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 font-bold text-gray-800"><?= htmlspecialchars($p['titulo']) ?></td>
                        <td class="p-4 font-mono text-blue-500 text-xs"><a href="<?= $p['slug'] === 'inicio' ? base_url() : base_url('pagina?slug=' . $p['slug']) ?>" target="_blank" class="hover:underline">/<?= htmlspecialchars($p['slug']) ?> <i class="fas fa-external-link-alt text-[10px] ml-1"></i></a></td>
                        <td class="p-4 text-center">
                            <?php if(has_permission('pagina_web.crear')): ?>
                            <a href="<?= base_url('pagina_web/paginas/cambiarEstado?id=' . $p['id']) ?>" class="px-2 py-1 rounded text-[10px] uppercase font-bold <?= $p['estado'] === 'publicado' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?> hover:opacity-80">
                                <?= $p['estado'] ?>
                            </a>
                            <?php else: ?>
                            <span class="px-2 py-1 rounded text-[10px] uppercase font-bold <?= $p['estado'] === 'publicado' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>"><?= $p['estado'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 text-center flex items-center justify-center gap-2">
                            <a href="<?= base_url('pagina_web/constructor?pagina_id=' . $p['id']) ?>" class="px-3 py-1.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition rounded font-bold text-xs"><i class="fas fa-paint-brush mr-1"></i> Diseñar</a>
                            <?php if(has_permission('pagina_web.crear')): ?>
                            <button onclick='editarPagina(<?= json_encode($p) ?>)' class="w-8 h-8 rounded bg-gray-50 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition"><i class="fas fa-edit"></i></button>
                            <?php if($p['id'] != 1): ?>
                                <a href="<?= base_url('pagina_web/paginas/eliminar?id=' . $p['id']) ?>" onclick="return confirm('¿Borrar página?')" class="w-8 h-8 flex items-center justify-center rounded bg-gray-50 text-gray-600 hover:bg-red-50 hover:text-red-600 transition"><i class="fas fa-trash-alt"></i></a>
                            <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Crear Página -->
<div id="modal-pagina" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden transform scale-95 transition-transform duration-300" id="modal-content">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center"><h3 class="font-bold text-gray-800" id="modal-title">Nueva Página</h3><button onclick="cerrarModalPagina()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button></div>
        <form action="<?= base_url('pagina_web/paginas/guardar') ?>" method="POST" class="p-5">
            <input type="hidden" name="id" id="pagina_id" value="0">
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Título Público</label>
                <input type="text" name="titulo" id="pagina_titulo" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-blue-500 outline-none" required placeholder="Ej. Quiénes Somos">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Slug (Ruta Web)</label>
                <input type="text" name="slug" id="pagina_slug" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-blue-500 outline-none font-mono" placeholder="Ej. quienes-somos (Dejar vacío para autogenerar)">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="cerrarModalPagina()" class="px-4 py-2 text-gray-500 text-sm font-medium">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm font-bold shadow-sm hover:bg-blue-700">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirModalPagina() { document.getElementById('pagina_id').value='0'; document.getElementById('pagina_titulo').value=''; document.getElementById('pagina_slug').value=''; document.getElementById('modal-title').innerText='Nueva Página'; const m = document.getElementById('modal-pagina'); m.classList.remove('hidden'); setTimeout(() => { m.classList.add('opacity-100'); document.getElementById('modal-content').classList.replace('scale-95','scale-100'); },10); }
    function editarPagina(p) { document.getElementById('pagina_id').value=p.id; document.getElementById('pagina_titulo').value=p.titulo; document.getElementById('pagina_slug').value=p.slug; document.getElementById('modal-title').innerText='Editar Página'; const m = document.getElementById('modal-pagina'); m.classList.remove('hidden'); setTimeout(() => { m.classList.add('opacity-100'); document.getElementById('modal-content').classList.replace('scale-95','scale-100'); },10); }
    function cerrarModalPagina() { const m = document.getElementById('modal-pagina'); m.classList.remove('opacity-100'); document.getElementById('modal-content').classList.replace('scale-100','scale-95'); setTimeout(() => m.classList.add('hidden'), 300); }
</script>