<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-server text-blue-500 mr-2"></i> Configuración MySQL</h3>
            <?php if($dbStatus): ?>
                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full"><i class="fas fa-check-circle mr-1"></i> Conectado</span>
            <?php else: ?>
                <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full" title="<?= htmlspecialchars($dbError) ?>"><i class="fas fa-times-circle mr-1"></i> Error</span>
            <?php endif; ?>
        </div>
        <?php if(!$dbStatus && $dbError): ?>
            <div class="bg-red-50 text-red-600 p-2 rounded mb-4 text-xs overflow-hidden text-ellipsis whitespace-nowrap" title="<?= htmlspecialchars($dbError) ?>">
                <?= htmlspecialchars($dbError) ?>
            </div>
        <?php endif; ?>
        <?php if(isset($_GET['success'])): ?>
            <div class="bg-green-100 text-green-700 p-2 rounded mb-4 text-sm">✅ Configuración guardada exitosamente.</div>
        <?php endif; ?>
        <form action="<?= base_url('admin_core/update-config') ?>" method="POST">
            <div class="mb-2"><label class="text-xs text-gray-500 block mb-1">Host</label>
            <input type="text" name="db_host" value="<?= $dbHost ?>" class="w-full border border-gray-200 p-2 rounded text-sm focus:outline-none focus:border-indigo-500"></div>
            <div class="mb-2"><label class="text-xs text-gray-500 block mb-1">Base de Datos</label>
            <input type="text" name="db_name" value="<?= $dbName ?>" class="w-full border border-gray-200 p-2 rounded text-sm text-indigo-600 font-bold focus:outline-none focus:border-indigo-500"></div>
            <div class="mb-2"><label class="text-xs text-gray-500 block mb-1">Usuario</label>
            <input type="text" name="db_user" value="<?= $dbUser ?>" class="w-full border border-gray-200 p-2 rounded text-sm focus:outline-none focus:border-indigo-500"></div>
            <div class="mb-4"><label class="text-xs text-gray-500 block mb-1">Contraseña</label>
            <input type="password" name="db_pass" placeholder="***" class="w-full border border-gray-200 p-2 rounded text-sm focus:outline-none focus:border-indigo-500"></div>
            <div class="mb-4"><label class="text-xs text-gray-500 block mb-1">URL Base del Proyecto</label>
            <input type="text" name="app_url" value="<?= $appUrl ?>" class="w-full border border-gray-200 p-2 rounded text-sm focus:outline-none focus:border-indigo-500"></div>
            <button type="submit" class="w-full bg-gray-50 text-gray-600 py-2 rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-indigo-600 transition font-medium text-sm"><i class="fas fa-save mr-1"></i> Guardar Cambios</button>
        </form>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-database text-purple-500 mr-2"></i> Migraciones SQL</h3>
            <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs font-bold rounded-full"><?= count($pendingMigrations) ?> Pendientes</span>
        </div>
        <?php if(isset($_GET['migrated'])): ?><div class="bg-green-100 text-green-700 p-2 rounded mb-4 text-sm">✅ Migraciones ejecutadas.</div><?php endif; ?>
        <?php if(count($pendingMigrations) > 0): ?>
            <ul class="text-sm text-gray-600 mb-4 h-24 overflow-y-auto bg-gray-50 p-2 rounded border border-gray-100">
                <?php foreach($pendingMigrations as $mig): ?>
                    <li class="border-b border-gray-200 last:border-0 py-1"><i class="fas fa-file-code text-gray-400 mr-1"></i> <?= $mig ?></li>
                <?php endforeach; ?>
            </ul>
            <form action="<?= base_url('admin_core/run-migrations') ?>" method="POST">
                <button type="submit" class="w-full bg-purple-600 text-white py-2 rounded-lg border border-purple-700 hover:bg-purple-700 transition font-medium text-sm"><i class="fas fa-play mr-1"></i> Ejecutar Migraciones</button>
            </form>
        <?php else: ?>
            <p class="text-sm text-gray-500 mb-4 text-center py-4 bg-gray-50 rounded">Todo está actualizado.</p>
        <?php endif; ?>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:col-span-3">
        <h3 class="text-lg font-bold text-gray-800 mb-4"><i class="fas fa-puzzle-piece text-green-500 mr-2"></i> Módulos Instalados</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <?php foreach($modules as $mod): ?>
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 flex items-center">
                
                <div class="h-3 w-3 rounded-full <?= $mod['active'] ? 'bg-green-500' : 'bg-red-500' ?> mr-3"></div>
                <div><b class="capitalize"><?= $mod['name'] ?></b> <span class="text-xs text-gray-400">v<?= $mod['version'] ?></span></div>
                <button onclick="editarModuloConfig('<?= $mod['dir'] ?>', '<?= base64_encode(json_encode($mod)) ?>')" class="ml-auto px-3 py-1 bg-gray-100 text-gray-600 rounded hover:bg-indigo-50 hover:text-indigo-600 text-xs font-bold transition flex items-center">
                    <i class="fas fa-edit mr-1"></i> Editar JSON
                </button>

            </div>
            
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modal Editar Módulo -->
<div id="modal-modulo" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 transition-opacity">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl mx-4 overflow-hidden transform scale-95 transition-transform duration-300 max-h-[90vh] flex flex-col">
        <div class="p-4 border-b border-gray-100 bg-gray-800 flex justify-between items-center">
            <h3 class="font-bold text-lg text-white"><i class="fas fa-file-code mr-2"></i> Configuración del Módulo</h3>
            <button onclick="cerrarModal('modal-modulo')" class="text-white/80 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form action="<?= base_url('admin_core/update_module') ?>" method="POST" class="flex-1 overflow-y-auto p-5 custom-scrollbar">
            <input type="hidden" name="module_dir" id="mod_dir">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Nombre (Clave)</label>
                    <input type="text" name="name" id="mod_name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:border-indigo-500 bg-gray-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Ícono (FontAwesome)</label>
                    <input type="text" name="icon" id="mod_icon" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:border-indigo-500 font-mono" placeholder="Ej. fa-box text-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Descripción</label>
                    <input type="text" name="description" id="mod_description" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:border-indigo-500">
                </div>
                <div class="md:col-span-2 flex items-center bg-indigo-50 p-3 rounded-lg border border-indigo-100">
                    <label class="flex items-center cursor-pointer w-full">
                        <input type="checkbox" name="active" id="mod_active" class="w-5 h-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                        <div class="ml-3"><span class="block text-sm font-bold text-indigo-900">Módulo Activo</span><span class="block text-xs text-indigo-600">Si se desmarca, desaparecerá del menú lateral.</span></div>
                    </label>
                </div>
            </div>

            <!-- Sección de Permisos -->
            <div class="border-t border-gray-200 pt-4">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="text-sm font-bold text-gray-800"><i class="fas fa-shield-alt text-indigo-500 mr-1"></i> Permisos Registrados</h4>
                    <button type="button" onclick="agregarPermisoHTML('', '')" class="px-3 py-1 bg-green-50 text-green-600 rounded hover:bg-green-100 font-bold text-xs"><i class="fas fa-plus mr-1"></i> Añadir Permiso</button>
                </div>
                <div id="permisos-list" class="space-y-2">
                    <!-- Dinámico -->
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" onclick="cerrarModal('modal-modulo')" class="px-4 py-2 text-gray-500 hover:bg-gray-50 rounded-lg font-medium text-sm transition">Cancelar</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg font-bold text-sm shadow hover:bg-indigo-700 transition"><i class="fas fa-save mr-1"></i> Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
function editarModuloConfig(dirName, configBase64) {
    const config = JSON.parse(atob(configBase64));
    
    document.getElementById('mod_dir').value = dirName;
    document.getElementById('mod_name').value = config.name || '';
    document.getElementById('mod_description').value = config.description || '';
    document.getElementById('mod_icon').value = config.icon || '';
    document.getElementById('mod_active').checked = config.active !== false;
    
    const list = document.getElementById('permisos-list');
    list.innerHTML = '';
    
    if (config.permissions && Array.isArray(config.permissions)) {
        config.permissions.forEach(p => agregarPermisoHTML(p.name, p.description));
    } else {
        list.innerHTML = '<div class="text-center text-xs text-gray-400 py-3" id="no-perms">No hay permisos definidos.</div>';
    }
    
    abrirModal('modal-modulo');
}

function agregarPermisoHTML(name, desc) {
    const noPerms = document.getElementById('no-perms');
    if (noPerms) noPerms.remove();

    const div = document.createElement('div');
    div.className = 'flex gap-2 items-center bg-gray-50 p-2 rounded border border-gray-200';
    div.innerHTML = `
        <div class="flex-1">
            <input type="text" name="perm_name[]" value="${name}" required placeholder="Ej. pos.crear" class="w-full border border-gray-300 rounded px-2 py-1 text-xs outline-none focus:border-indigo-500 font-mono mb-1">
            <input type="text" name="perm_desc[]" value="${desc}" placeholder="Descripción del permiso..." class="w-full border border-gray-300 rounded px-2 py-1 text-xs outline-none focus:border-indigo-500">
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="w-8 h-8 rounded bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center flex-shrink-0" title="Eliminar"><i class="fas fa-trash"></i></button>
    `;
    document.getElementById('permisos-list').appendChild(div);
}

function abrirModal(id) {
    const modal = document.getElementById(id);
    const content = modal.querySelector('div');
    modal.classList.remove('hidden');
    setTimeout(() => { content.classList.remove('scale-95'); content.classList.add('scale-100'); }, 10);
}

function cerrarModal(id) {
    const modal = document.getElementById(id);
    const content = modal.querySelector('div');
    content.classList.remove('scale-100'); content.classList.add('scale-95');
    setTimeout(() => { modal.classList.add('hidden'); }, 200);
}
</script>

</div>
