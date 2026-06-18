<div class="max-w-5xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-tags text-blue-500 mr-2"></i> Categorías</h2>
            <p class="text-sm text-gray-500 mt-1">Organiza tu inventario por familias de productos</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= base_url('inventario') ?>" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 font-medium transition text-sm flex items-center shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i> Volver al Inventario
            </a>
            <button onclick="abrirModalCategoria()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition text-sm flex items-center shadow-md">
                <i class="fas fa-plus mr-2"></i> Nueva Categoría
            </button>
        </div>
    </div>

    <?php if(isset($_GET['success'])): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-xl flex items-center shadow-sm text-sm">
            <i class="fas fa-check-circle text-lg mr-3"></i> <div><?= htmlspecialchars($_GET['success']) ?></div>
        </div>
    <?php endif; ?>
    <?php if(isset($_GET['error'])): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-xl flex items-center shadow-sm text-sm">
            <i class="fas fa-exclamation-triangle text-lg mr-3"></i> <div><?= htmlspecialchars($_GET['error']) ?></div>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase border-b border-gray-100">
                    <th class="p-4 font-medium">Nombre de Categoría</th>
                    <th class="p-4 font-medium">Descripción</th>
                    <th class="p-4 font-medium text-center w-32">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                <?php if(empty($categorias)): ?>
                    <tr><td colspan="3" class="p-12 text-center text-gray-400"><i class="fas fa-folder-open text-4xl mb-3 opacity-30"></i><br>No hay categorías registradas.</td></tr>
                <?php else: foreach($categorias as $c): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 font-bold text-gray-800"><?= htmlspecialchars($c['nombre']) ?></td>
                        <td class="p-4 text-gray-500"><?= htmlspecialchars($c['descripcion'] ?: 'Sin descripción') ?></td>
                        <td class="p-4 text-center flex justify-center gap-2">
                            <button onclick="editarCategoria(<?= $c['id'] ?>, '<?= htmlspecialchars(addslashes($c['nombre'])) ?>', '<?= htmlspecialchars(addslashes($c['descripcion'])) ?>')" class="w-8 h-8 inline-flex items-center justify-center rounded bg-gray-50 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition tooltip" title="Editar"><i class="fas fa-edit"></i></button>
                            <a href="<?= base_url('inventario/categorias/eliminar?id=' . $c['id']) ?>" onclick="return confirm('¿Seguro que deseas eliminar esta categoría? Solo se podrá si no tiene productos asignados.')" class="w-8 h-8 inline-flex items-center justify-center rounded bg-gray-50 text-gray-600 hover:bg-red-50 hover:text-red-600 transition tooltip" title="Eliminar"><i class="fas fa-trash-alt"></i></a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Categoría -->
<div id="modal-categoria" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden transition-opacity">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden transform scale-95 transition-transform duration-300">
        <form action="<?= base_url('inventario/categorias/guardar') ?>" method="POST">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="font-bold text-lg text-gray-800" id="modal-title">Nueva Categoría</h3>
                <button type="button" onclick="cerrarModalCategoria()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>
            <div class="p-6 space-y-4">
                <input type="hidden" name="id" id="cat-id" value="0">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" id="cat-nombre" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea name="descripcion" id="cat-descripcion" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none resize-none"></textarea>
                </div>
            </div>
            <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
                <button type="button" onclick="cerrarModalCategoria()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold shadow-md">Guardar Categoría</button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirModalCategoria() { document.getElementById('cat-id').value = '0'; document.getElementById('cat-nombre').value = ''; document.getElementById('cat-descripcion').value = ''; document.getElementById('modal-title').innerText = 'Nueva Categoría'; const modal = document.getElementById('modal-categoria'); modal.classList.remove('hidden'); setTimeout(() => { modal.firstElementChild.classList.replace('scale-95', 'scale-100'); }, 10); }
    function editarCategoria(id, nombre, desc) { document.getElementById('cat-id').value = id; document.getElementById('cat-nombre').value = nombre; document.getElementById('cat-descripcion').value = desc; document.getElementById('modal-title').innerText = 'Editar Categoría'; const modal = document.getElementById('modal-categoria'); modal.classList.remove('hidden'); setTimeout(() => { modal.firstElementChild.classList.replace('scale-95', 'scale-100'); }, 10); }
    function cerrarModalCategoria() { const modal = document.getElementById('modal-categoria'); modal.firstElementChild.classList.replace('scale-100', 'scale-95'); setTimeout(() => { modal.classList.add('hidden'); }, 200); }
</script>