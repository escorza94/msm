<div class="max-w-6xl mx-auto mt-6 mb-10">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-layer-group text-green-500 mr-3"></i> Gestor de Colecciones</h2>
            <p class="text-sm text-gray-500 mt-1">Agrupa productos para mostrarlos en el panel o escaparate.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= base_url('pagina_web') ?>" class="text-sm bg-white border border-gray-200 text-gray-600 px-3 py-2 rounded-lg font-medium hover:bg-gray-50 transition flex items-center shadow-sm"><i class="fas fa-arrow-left mr-2"></i> Panel</a>
            <a href="<?= base_url('pagina_web/colecciones/nuevo') ?>" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold transition text-sm flex items-center shadow-md"><i class="fas fa-plus mr-2"></i> Nueva Colección</a>
        </div>
    </div>

    <?php if(isset($_GET['success'])): ?><div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-xl flex items-center shadow-sm text-sm"><i class="fas fa-check-circle text-lg mr-3"></i> <div><?= htmlspecialchars($_GET['success']) ?></div></div><?php endif; ?>
    <?php if(isset($_GET['error'])): ?><div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-xl flex items-center shadow-sm text-sm"><i class="fas fa-exclamation-triangle text-lg mr-3"></i> <div><?= htmlspecialchars($_GET['error']) ?></div></div><?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase border-b border-gray-100">
                    <th class="p-4 font-medium">Nombre</th>
                    <th class="p-4 font-medium">Slug (URL)</th>
                    <th class="p-4 font-medium text-center">Productos</th>
                    <th class="p-4 font-medium text-center">Estado</th>
                    <th class="p-4 font-medium text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 divide-y divide-gray-50">
                <?php if(empty($colecciones)): ?>
                <tr><td colspan="5" class="p-8 text-center text-gray-400">No hay colecciones creadas.</td></tr>
                <?php else: foreach($colecciones as $c): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 font-bold text-gray-800"><?= htmlspecialchars($c['nombre']) ?></td>
                        <td class="p-4 font-mono text-gray-500 text-xs"><?= htmlspecialchars($c['slug']) ?></td>
                        <td class="p-4 text-center"><span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-bold"><?= $c['total_productos_visibles'] ?> items</span></td>
                        <td class="p-4 text-center">
                            <a href="<?= base_url('pagina_web/colecciones/cambiarEstado?id=' . $c['id']) ?>" class="px-2 py-1 rounded text-[10px] uppercase font-bold <?= $c['estado'] === 'activo' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?> hover:opacity-80">
                                <?= $c['estado'] ?>
                            </a>
                        </td>
                        <td class="p-4 text-center flex items-center justify-center gap-2">
                            <a href="<?= base_url('pagina_web/colecciones/editar?id=' . $c['id']) ?>" class="w-8 h-8 flex items-center justify-center rounded bg-gray-50 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition"><i class="fas fa-edit"></i></a>
                            <?php if($c['id'] != 1): ?>
                                <a href="<?= base_url('pagina_web/colecciones/eliminar?id=' . $c['id']) ?>" onclick="return confirm('¿Borrar colección?')" class="w-8 h-8 flex items-center justify-center rounded bg-gray-50 text-gray-600 hover:bg-red-50 hover:text-red-600 transition"><i class="fas fa-trash-alt"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>