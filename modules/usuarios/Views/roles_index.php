<div class="max-w-5xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-user-shield text-indigo-500 mr-2"></i> Roles y Permisos</h2>
            <p class="text-sm text-gray-500 mt-1">Configura el nivel de acceso (RBAC) para cada grupo de usuarios</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('usuarios/roles/sincronizar-permisos') ?>" class="text-sm bg-amber-50 text-amber-600 px-4 py-2 rounded-lg font-bold hover:bg-amber-100 transition flex items-center shadow-sm" title="Escanear módulos y agregar nuevos permisos">
                <i class="fas fa-sync-alt mr-2"></i> Sincronizar Permisos
            </a>
            <button onclick="abrirModalRol()" class="text-sm bg-indigo-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-indigo-700 transition flex items-center shadow-sm">
                <i class="fas fa-user-tag mr-2"></i> Nuevo Rol
            </button>
            <button onclick="abrirModalPermiso()" class="text-sm bg-indigo-50 text-indigo-600 px-4 py-2 rounded-lg font-bold hover:bg-indigo-100 transition flex items-center shadow-sm">
                <i class="fas fa-plus mr-2"></i> Nuevo Permiso
            </button>
            <a href="<?= base_url('usuarios') ?>" class="text-sm bg-white border border-gray-200 text-gray-600 px-4 py-2 rounded-lg font-medium hover:bg-gray-50 transition flex items-center shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i> Volver a Usuarios
            </a>
        </div>
    </div>

    <?php if(isset($_GET['success'])): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-xl flex items-center shadow-sm text-sm"><i class="fas fa-check-circle text-lg mr-3"></i> <div><?= htmlspecialchars($_GET['success']) ?></div></div>
    <?php endif; ?>
    <?php if(isset($_GET['error'])): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-xl flex items-center shadow-sm text-sm"><i class="fas fa-exclamation-triangle text-lg mr-3"></i> <div><?= htmlspecialchars($_GET['error']) ?></div></div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase border-b border-gray-100">
                    <th class="p-4 font-medium w-16 text-center">ID</th>
                    <th class="p-4 font-medium">Nombre del Rol</th>
                    <th class="p-4 font-medium">Descripción</th>
                    <th class="p-4 font-medium text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                <?php if(empty($roles)): ?>
                    <tr><td colspan="4" class="p-8 text-center text-gray-400">No hay roles registrados en la Base de Datos.</td></tr>
                <?php else: foreach($roles as $r): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 text-center font-mono text-gray-400"><?= $r['id'] ?></td>
                        <td class="p-4 font-bold text-gray-800"><?= htmlspecialchars($r['name'] ?? $r['nombre'] ?? '') ?> <?php if($r['id'] == 1): ?><span class="ml-2 px-2 py-0.5 bg-red-100 text-red-700 rounded text-[10px] font-bold uppercase">SuperAdmin</span><?php endif; ?></td>
                        <td class="p-4 text-gray-500 text-xs"><?= htmlspecialchars($r['description'] ?? $r['descripcion'] ?? 'Sin descripción') ?></td>
                        <td class="p-4 text-center">
                            <?php if($r['id'] != 1): ?>
                                <a href="<?= base_url('usuarios/roles/editar?id=' . $r['id']) ?>" class="inline-flex items-center justify-center px-3 py-1.5 rounded bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition font-bold text-xs" title="Configurar Permisos"><i class="fas fa-sliders-h mr-1"></i> Permisos</a>
                                <button onclick='editarRol(<?= json_encode($r) ?>)' class="w-8 h-8 inline-flex items-center justify-center rounded bg-gray-50 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition tooltip" title="Editar Nombre del Rol"><i class="fas fa-edit"></i></button>
                                <a href="<?= base_url('usuarios/roles/eliminar-rol?id=' . $r['id']) ?>" onclick="return confirm('¿Seguro que deseas eliminar este rol?')" class="w-8 h-8 inline-flex items-center justify-center rounded bg-gray-50 text-gray-600 hover:bg-red-50 hover:text-red-600 transition tooltip" title="Eliminar Rol"><i class="fas fa-trash-alt"></i></a>
                            <?php else: ?>
                                <span class="text-[10px] text-gray-400 italic">Acceso Total</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Nuevo Permiso -->
<div id="modal-permiso" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 transition-opacity">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden transform scale-95 transition-transform duration-300">
        <div class="p-4 border-b border-gray-100 bg-gray-800 flex justify-between items-center">
            <h3 class="font-bold text-lg text-white"><i class="fas fa-key mr-2"></i> Crear Nuevo Permiso</h3>
            <button onclick="cerrarModal('modal-permiso')" class="text-white/80 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form action="<?= base_url('usuarios/permisos/guardar') ?>" method="POST" class="p-5">
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Nombre Interno (Clave)</label>
                    <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:border-indigo-500 font-mono" placeholder="Ej. marketing.crear">
                    <p class="text-[10px] text-gray-400 mt-1">Usa un punto para separar el módulo de la acción. Así se agrupará automáticamente.</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Descripción</label>
                    <input type="text" name="description" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:border-indigo-500" placeholder="Ej. Permite crear pautas publicitarias">
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="cerrarModal('modal-permiso')" class="px-4 py-2 text-gray-500 hover:bg-gray-50 rounded-lg font-medium text-sm transition">Cancelar</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg font-bold text-sm shadow hover:bg-indigo-700 transition"><i class="fas fa-save mr-1"></i> Guardar Permiso</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Nuevo/Editar Rol -->
<div id="modal-rol" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 transition-opacity">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden transform scale-95 transition-transform duration-300">
        <div class="p-4 border-b border-gray-100 bg-gray-800 flex justify-between items-center">
            <h3 class="font-bold text-lg text-white" id="modal-rol-title"><i class="fas fa-user-tag mr-2"></i> Nuevo Rol</h3>
            <button onclick="cerrarModal('modal-rol')" class="text-white/80 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form action="<?= base_url('usuarios/roles/guardar-rol') ?>" method="POST" class="p-5">
            <input type="hidden" name="id" id="rol_id" value="0">
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Nombre del Rol</label>
                    <input type="text" name="name" id="rol_name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:border-indigo-500" placeholder="Ej. Cajero, Almacenista...">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Descripción</label>
                    <input type="text" name="description" id="rol_description" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:border-indigo-500" placeholder="Breve descripción del puesto...">
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="cerrarModal('modal-rol')" class="px-4 py-2 text-gray-500 hover:bg-gray-50 rounded-lg font-medium text-sm transition">Cancelar</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg font-bold text-sm shadow hover:bg-indigo-700 transition"><i class="fas fa-save mr-1"></i> Guardar Rol</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalRol() {
    document.getElementById('rol_id').value = '0';
    document.getElementById('rol_name').value = '';
    document.getElementById('rol_description').value = '';
    document.getElementById('modal-rol-title').innerHTML = '<i class="fas fa-user-tag mr-2"></i> Nuevo Rol';
    abrirModal('modal-rol');
}
function editarRol(r) {
    document.getElementById('rol_id').value = r.id;
    document.getElementById('rol_name').value = r.name || r.nombre;
    document.getElementById('rol_description').value = r.description || r.descripcion;
    document.getElementById('modal-rol-title').innerHTML = '<i class="fas fa-user-edit mr-2"></i> Editar Rol';
    abrirModal('modal-rol');
}
function abrirModalPermiso() {
    const modal = document.getElementById('modal-permiso');
    abrirModal('modal-permiso');
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