<div class="max-w-5xl mx-auto mt-8 mb-10">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-sliders-h text-indigo-500 mr-2"></i> Editar Permisos: <span class="text-indigo-600 ml-2"><?= htmlspecialchars($rol['name'] ?? $rol['nombre'] ?? '') ?></span></h2>
            <p class="text-sm text-gray-500 mt-1"><?= htmlspecialchars($rol['description'] ?? $rol['descripcion'] ?? '') ?></p>
        </div>
        <a href="<?= base_url('usuarios/roles') ?>" class="text-sm bg-white border border-gray-200 text-gray-600 px-3 py-1.5 rounded-lg font-medium hover:bg-gray-50 transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
    </div>

    <form action="<?= base_url('usuarios/roles/guardar') ?>" method="POST">
        <input type="hidden" name="role_id" value="<?= $rol['id'] ?>">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach($permisosAgrupados as $grupo => $permisos): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-3 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                        <h3 class="font-bold text-gray-800 uppercase tracking-wider text-[11px]"><i class="fas fa-cube text-gray-400 mr-2"></i> Módulo <?= htmlspecialchars($grupo) ?></h3>
                        <label class="text-[10px] text-indigo-600 font-bold cursor-pointer hover:underline"><input type="checkbox" class="select-all-group hidden" onchange="toggleGroup(this, 'group-<?= $grupo ?>')"> Marcar todos</label>
                    </div>
                    <div class="p-4 space-y-3 group-<?= $grupo ?>">
                        <?php foreach($permisos as $p): ?>
                            <label class="flex items-start cursor-pointer group">
                                <div class="flex items-center h-5"><input type="checkbox" name="permisos[]" value="<?= $p['id'] ?>" class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500" <?= in_array($p['id'], $role_permissions) ? 'checked' : '' ?>></div>
                                <div class="ml-3 text-sm">
                                    <span class="font-bold text-gray-700 group-hover:text-indigo-600 transition"><?= htmlspecialchars($p['name']) ?></span>
                                    <?php if(!empty($p['description'])): ?><p class="text-[11px] text-gray-400 mt-0.5 leading-tight"><?= htmlspecialchars($p['description']) ?></p><?php endif; ?>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mt-8 flex justify-end">
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-bold text-sm shadow-md hover:bg-indigo-700 hover:shadow-lg transition-all flex items-center"><i class="fas fa-save mr-2"></i> Guardar Permisos del Rol</button>
        </div>
    </form>
</div>

<script> function toggleGroup(checkbox, groupClass) { document.querySelectorAll('.' + groupClass + ' input[type="checkbox"]').forEach(cb => cb.checked = checkbox.checked); } </script>