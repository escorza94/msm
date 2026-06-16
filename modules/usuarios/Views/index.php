<div class="max-w-5xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-users text-indigo-500 mr-2"></i> Gestión de Usuarios</h2>
            <p class="text-sm text-gray-500 mt-1">Lista de todos los usuarios registrados en el sistema</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= base_url('usuarios/roles') ?>" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 font-medium transition text-sm flex items-center shadow-sm">
                <i class="fas fa-user-shield mr-2 text-indigo-500"></i> Roles y Permisos
            </a>
            <a href="<?= base_url('usuarios/configuracion') ?>" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 font-medium transition text-sm flex items-center shadow-sm">
                <i class="fas fa-cog mr-2 text-gray-400"></i> Ajustes
            </a>
            <a href="<?= base_url('usuarios/nuevo') ?>" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition text-sm flex items-center shadow-md">
                <i class="fas fa-user-plus mr-2"></i> Nuevo Usuario
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase border-b border-gray-100">
                        <th class="p-4 font-medium w-12 text-center">ID</th>
                        <th class="p-4 font-medium">Nombre / Usuario</th>
                        <th class="p-4 font-medium">Correo Electrónico</th>
                        <th class="p-4 font-medium">Rol</th>
                        <th class="p-4 font-medium text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                    <?php if(empty($usuarios)): ?>
                        <tr><td colspan="5" class="p-8 text-center text-gray-400">No hay usuarios registrados.</td></tr>
                    <?php else: foreach($usuarios as $u): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 text-center text-gray-400 text-xs font-mono"><?= $u['id'] ?? '' ?></td>
                            <td class="p-4 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center font-bold text-xs">
                                    <?= strtoupper(substr($u['nombre'] ?? $u['name'] ?? $u['usuario'] ?? 'U', 0, 1)) ?>
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-800"><?= htmlspecialchars($u['nombre'] ?? $u['name'] ?? $u['nombres'] ?? 'Usuario') ?></span>
                                    <span class="text-[10px] text-gray-400">@<?= htmlspecialchars($u['usuario'] ?? $u['username'] ?? $u['name'] ?? 'user') ?></span>
                                </div>
                            </td>
                            <td class="p-4 text-gray-500 text-xs"><?= htmlspecialchars($u['email'] ?? $u['correo'] ?? 'Sin correo') ?></td>
                            <td class="p-4 flex flex-wrap gap-1">
                                <?php 
                                $nombresRoles = $u['roles_nombres'] ?? (isset($u['role_id']) && $u['role_id'] == 1 ? 'SuperAdmin' : 'Asesor'); 
                                $arrRoles = explode(',', $nombresRoles);
                                foreach($arrRoles as $nRol): ?>
                                    <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded text-[10px] font-bold uppercase inline-block"><?= htmlspecialchars(trim($nRol)) ?></span>
                                <?php endforeach; ?>
                            </td>
                            <td class="p-4 text-center">
                                <a href="<?= base_url('usuarios/editar?id=' . ($u['id'] ?? 0)) ?>" class="inline-flex items-center justify-center w-8 h-8 rounded bg-gray-50 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition tooltip" title="Editar usuario">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>