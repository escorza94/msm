<div class="max-w-3xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-user-edit text-indigo-500 mr-2"></i> Editar Usuario</h2>
            <p class="text-sm text-gray-500 mt-1">Modificar los datos o cambiar la contraseña de acceso</p>
        </div>
        <a href="<?= base_url('usuarios') ?>" class="text-sm bg-white border border-gray-200 text-gray-600 px-3 py-1.5 rounded-lg font-medium hover:bg-gray-50 transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
    </div>

    <?php if(isset($error)): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-xl flex items-center shadow-sm text-sm">
            <i class="fas fa-exclamation-triangle text-lg mr-3"></i> 
            <div><?= htmlspecialchars($error) ?></div>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-6">
        <form action="<?= base_url('usuarios/editar') ?>" method="POST" class="space-y-6">
            <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="<?= htmlspecialchars($usuario['name'] ?? '') ?>" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition shadow-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="<?= htmlspecialchars($usuario['email'] ?? '') ?>" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition shadow-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nueva Contraseña</label>
                    <input type="password" name="password" placeholder="Dejar vacío para conservar la actual" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Roles de Usuario <span class="text-red-500">*</span></label>
                    <select name="roles[]" id="roles" multiple class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition shadow-sm" required>
                        <?php foreach($roles_db ?? [] as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= in_array($r['id'], $user_roles ?? []) ? 'selected' : '' ?>><?= htmlspecialchars($r['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp ID (Para Alertas de Ventas)</label>
                    <select name="whatsapp_id" id="whatsapp_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition shadow-sm font-mono">
                        <option value="">-- Selecciona o escribe Ej. 521XXXXXXXXXX@c.us --</option>
                        <?php foreach($contactos_wa ?? [] as $wa): ?>
                            <option value="<?= htmlspecialchars($wa['whatsapp_id']) ?>" <?= ($wa['whatsapp_id'] === ($usuario['whatsapp_id'] ?? '')) ? 'selected' : '' ?>><?= htmlspecialchars($wa['nombre']) ?> (<?= htmlspecialchars($wa['whatsapp_id']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Selecciona el contacto de la lista o escribe el número con lada y terminación @c.us. Recibirá alertas de la IA.</p>
                </div>
            </div>
            <div class="pt-4 mt-6 border-t border-gray-100 flex justify-end gap-3">
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-bold transition shadow-md flex items-center text-sm"><i class="fas fa-save mr-2"></i> Actualizar Usuario</button>
            </div>
        </form>
    </div>
</div>

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    /* Integración de Select2 con Tailwind */
    .select2-container .select2-selection--single {
        height: 42px !important;
        border-color: #d1d5db !important;
        border-radius: 0.5rem !important;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
    }
</style>

<script>
$(document).ready(function() {
    // Verificar si el valor actual no está en la lista para agregarlo dinámicamente
    var currentVal = "<?= htmlspecialchars($usuario['whatsapp_id'] ?? '') ?>";
    if (currentVal && $('#whatsapp_id option[value="' + currentVal + '"]').length === 0) {
        var newOption = new Option(currentVal + " (Manual)", currentVal, true, true);
        $('#whatsapp_id').append(newOption);
    }

    $('#whatsapp_id').select2({ 
        width: '100%',
        tags: true, // Permite escribir valores nuevos que no están en la lista
        placeholder: "-- Selecciona o escribe Ej. 521XXXXXXXXXX@c.us --",
        allowClear: true
    });

    $('#roles').select2({ 
        width: '100%',
        placeholder: "Selecciona uno o más roles..."
    });
});
</script>