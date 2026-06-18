<div class="max-w-4xl mx-auto mt-6 mb-10">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-layer-group text-blue-500 mr-3"></i> <?= $titulo ?></h2>
        </div>
        <a href="<?= base_url('pagina_web/colecciones') ?>" class="text-sm bg-white border border-gray-200 text-gray-600 px-3 py-1.5 rounded-lg font-medium hover:bg-gray-50 transition flex items-center"><i class="fas fa-arrow-left mr-2"></i> Volver</a>
    </div>

    <?php if(isset($_GET['error'])): ?><div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-xl flex items-center shadow-sm text-sm"><i class="fas fa-exclamation-triangle text-lg mr-3"></i> <div><?= htmlspecialchars($_GET['error']) ?></div></div><?php endif; ?>

    <form action="<?= base_url('pagina_web/colecciones/editar') ?>" method="POST" class="space-y-6">
        <input type="hidden" name="id" value="<?= $coleccion['id'] ?>">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nombre de la Colección</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($coleccion['nombre']) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-blue-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Slug (URL)</label>
                    <input type="text" name="slug" value="<?= htmlspecialchars($coleccion['slug']) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-blue-500 outline-none font-mono text-gray-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Descripción</label>
                    <input type="text" name="descripcion" value="<?= htmlspecialchars($coleccion['descripcion']) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-blue-500 outline-none">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tipo de Colección</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer p-3 border border-gray-200 rounded-lg w-1/2 hover:bg-gray-50">
                            <input type="radio" name="tipo" value="productos" <?= ($coleccion['tipo'] ?? 'productos') === 'productos' ? 'checked' : '' ?> class="text-blue-600 focus:ring-blue-500" onchange="toggleListas()">
                            <span class="text-sm font-bold text-gray-700"><i class="fas fa-box text-blue-500 mr-1"></i> De Productos</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer p-3 border border-gray-200 rounded-lg w-1/2 hover:bg-gray-50">
                            <input type="radio" name="tipo" value="promociones" <?= ($coleccion['tipo'] ?? '') === 'promociones' ? 'checked' : '' ?> class="text-blue-600 focus:ring-blue-500" onchange="toggleListas()">
                            <span class="text-sm font-bold text-gray-700"><i class="fas fa-tags text-pink-500 mr-1"></i> De Promociones</span>
                        </label>
                    </div>
                </div>
            </div>

            <div id="lista_productos" class="<?= ($coleccion['tipo'] ?? 'productos') === 'promociones' ? 'hidden' : '' ?>">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Seleccionar Productos</label>
                <p class="text-xs text-gray-400 mb-2">Mantén presionado Ctrl (o Cmd en Mac) para seleccionar múltiples productos de la lista.</p>
                <select name="productos[]" multiple class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-blue-500 outline-none h-64">
                    <?php foreach($productos as $p): ?>
                        <?php $selected = in_array($p['id'], $productos_seleccionados) ? 'selected' : ''; ?>
                        <option value="<?= $p['id'] ?>" <?= $selected ?>><?= htmlspecialchars($p['nombre']) ?> - <?= htmlspecialchars($p['sku']) ?> ($<?= number_format($p['precio'], 2) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div id="lista_promociones" class="<?= ($coleccion['tipo'] ?? 'productos') === 'productos' ? 'hidden' : '' ?>">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Seleccionar Promociones</label>
                <p class="text-xs text-gray-400 mb-2">Mantén presionado Ctrl (o Cmd en Mac) para seleccionar múltiples promociones.</p>
                <select name="promociones[]" multiple class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-blue-500 outline-none h-64">
                    <?php foreach($promociones as $p): ?>
                        <?php $selected = in_array($p['id'], $promociones_seleccionadas) ? 'selected' : ''; ?>
                        <option value="<?= $p['id'] ?>" <?= $selected ?>><?= htmlspecialchars($p['nombre']) ?> (<?= $p['tipo'] === 'porcentaje' ? floatval($p['valor']).'%' : '$'.number_format($p['valor'], 2) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg shadow font-bold hover:bg-blue-700 transition flex items-center"><i class="fas fa-save mr-2"></i> Guardar Cambios</button>
        </div>
    </form>
</div>

<script>
    function toggleListas() {
        const tipo = document.querySelector('input[name="tipo"]:checked').value;
        if (tipo === 'productos') {
            document.getElementById('lista_productos').classList.remove('hidden');
            document.getElementById('lista_promociones').classList.add('hidden');
        } else {
            document.getElementById('lista_productos').classList.add('hidden');
            document.getElementById('lista_promociones').classList.remove('hidden');
        }
    }
</script>