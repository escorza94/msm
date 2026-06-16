<div class="max-w-6xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-boxes text-blue-500 mr-2"></i> Inventario</h2>
            <p class="text-sm text-gray-500 mt-1">Gestión de productos, stock y precios</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= base_url('inventario/nuevo') ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition text-sm flex items-center shadow-md">
                <i class="fas fa-plus mr-2"></i> Nuevo Producto
            </a>
        </div>
    </div>

    <?php if(isset($_GET['success'])): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-xl flex items-center shadow-sm text-sm">
            <i class="fas fa-check-circle text-lg mr-3"></i> 
            <div><?= htmlspecialchars($_GET['success']) ?></div>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase border-b border-gray-100">
                        <th class="p-4 font-medium text-center">SKU</th>
                        <th class="p-4 font-medium">Producto</th>
                        <th class="p-4 font-medium">Categoría</th>
                        <th class="p-4 font-medium text-right">Precio</th>
                        <th class="p-4 font-medium text-center">Stock</th>
                        <th class="p-4 font-medium text-center">Estado</th>
                        <th class="p-4 font-medium text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                    <?php if(empty($productos)): ?>
                        <tr><td colspan="7" class="p-12 text-center text-gray-400"><i class="fas fa-box-open text-4xl mb-3 opacity-30"></i><br>No hay productos registrados en el inventario.</td></tr>
                    <?php else: foreach($productos as $p): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 text-center text-gray-500 text-xs font-mono font-bold"><?= htmlspecialchars($p['sku']) ?></td>
                            <td class="p-4 font-bold text-gray-800"><?= htmlspecialchars($p['nombre']) ?></td>
                            <td class="p-4 text-gray-500 text-xs"><?= htmlspecialchars($p['categoria_nombre'] ?? 'Sin categoría') ?></td>
                            <td class="p-4 text-right font-medium text-gray-800">$<?= number_format($p['precio'], 2) ?></td>
                            <td class="p-4 text-center">
                                <span class="px-2 py-1 rounded text-xs font-bold <?= $p['stock'] <= 5 ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600' ?>"><?= $p['stock'] ?></span>
                            </td>
                            <td class="p-4 text-center">
                                <span class="px-2 py-1 <?= $p['estado'] === 'activo' ? 'bg-blue-50 text-blue-600' : 'bg-gray-100 text-gray-500' ?> rounded text-[10px] font-bold uppercase"><?= $p['estado'] ?></span>
                            </td>
                            <td class="p-4 text-center flex items-center justify-center gap-2">
                                <a href="<?= base_url('inventario/ver?id=' . $p['id']) ?>" class="w-8 h-8 inline-flex items-center justify-center rounded bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition tooltip" title="Ver detalle"><i class="fas fa-eye"></i></a>
                                <a href="<?= base_url('inventario/editar?id=' . $p['id']) ?>" class="w-8 h-8 inline-flex items-center justify-center rounded bg-gray-50 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition tooltip" title="Editar producto"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
