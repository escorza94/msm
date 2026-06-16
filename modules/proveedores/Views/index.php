<div class="max-w-6xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-truck text-amber-500 mr-3"></i> Proveedores</h2>
            <p class="text-sm text-gray-500 mt-1">Directorio de empresas, marcas y contactos de abastecimiento</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= base_url('proveedores/nuevo') ?>" class="px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 font-medium transition text-sm flex items-center shadow-md">
                <i class="fas fa-plus mr-2"></i> Nuevo Proveedor
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
                        <th class="p-4 font-medium w-16 text-center">ID</th>
                        <th class="p-4 font-medium">Empresa / Contacto</th>
                        <th class="p-4 font-medium">Teléfono</th>
                        <th class="p-4 font-medium">Email</th>
                        <th class="p-4 font-medium text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                    <?php if(empty($proveedores)): ?>
                        <tr><td colspan="5" class="p-12 text-center text-gray-400"><i class="fas fa-building text-4xl mb-3 opacity-30"></i><br>No hay proveedores registrados en el directorio.</td></tr>
                    <?php else: foreach($proveedores as $p): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 text-center text-gray-500 text-xs font-mono font-bold"><?= $p['id'] ?></td>
                            <td class="p-4 font-bold text-gray-800 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center font-bold text-xs"><i class="fas fa-industry"></i></div>
                                <?= htmlspecialchars($p['nombre']) ?>
                            </td>
                            <td class="p-4 text-gray-600 text-sm"><?= htmlspecialchars($p['telefono'] ?: 'N/A') ?></td>
                            <td class="p-4 text-gray-600 text-sm"><?= htmlspecialchars($p['email'] ?: 'N/A') ?></td>
                            <td class="p-4 text-center">
                                <a href="<?= base_url('proveedores/editar?id=' . $p['id']) ?>" class="w-8 h-8 inline-flex items-center justify-center rounded bg-gray-50 text-gray-600 hover:bg-amber-50 hover:text-amber-600 transition tooltip" title="Editar proveedor"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
