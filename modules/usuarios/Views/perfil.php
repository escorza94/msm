<div class="max-w-3xl mx-auto mt-4 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 bg-gray-50 flex items-center gap-4">
        <div class="w-14 h-14 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-2xl font-bold shadow-sm">
            <i class="fas fa-user-circle"></i>
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-800">Ajustes de Cuenta</h2>
            <p class="text-sm text-gray-500 mt-1">Actualiza tu información personal y credenciales de acceso</p>
        </div>
    </div>

    <div class="p-6">
        <?php if(!empty($error)): ?>
            <div class="bg-red-50 text-red-600 px-4 py-3 rounded-lg mb-6 border border-red-100 text-sm flex items-center shadow-sm">
                <i class="fas fa-exclamation-circle mr-3 text-lg"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if(!empty($success)): ?>
            <div class="bg-green-50 text-green-600 px-4 py-3 rounded-lg mb-6 border border-green-100 text-sm flex items-center shadow-sm">
                <i class="fas fa-check-circle mr-3 text-lg"></i> <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('usuarios/postPerfil') ?>" method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nombre Completo</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($usuario['name'] ?? '') ?>" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none text-sm transition shadow-sm">
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Correo Electrónico</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($usuario['email'] ?? '') ?>" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none text-sm transition shadow-sm">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">WhatsApp ID (Para Alertas IA)</label>
                    <select name="whatsapp_id" id="whatsapp_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none text-sm transition shadow-sm font-mono">
                        <option value="">-- Selecciona o escribe Ej. 521XXXXXXXXXX@c.us --</option>
                        <?php foreach($contactos_wa ?? [] as $wa): ?>
                            <option value="<?= htmlspecialchars($wa['whatsapp_id']) ?>" <?= ($wa['whatsapp_id'] === ($usuario['whatsapp_id'] ?? '')) ? 'selected' : '' ?>><?= htmlspecialchars($wa['nombre']) ?> (<?= htmlspecialchars($wa['whatsapp_id']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-[10px] text-gray-400 mt-1"><i class="fas fa-info-circle"></i> Recibirás notificaciones cuando un cliente pida hablar con un humano.</p>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-6">
                <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center"><i class="fas fa-lock text-gray-400 mr-2"></i> Seguridad de la cuenta</h3>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nueva Contraseña</label>
                <input type="password" name="password" placeholder="Dejar en blanco para mantener la actual..." class="w-full md:w-1/2 border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none text-sm transition shadow-sm">
                <p class="text-xs text-gray-400 mt-2"><i class="fas fa-info-circle"></i> Solo llena este campo si deseas cambiar tu contraseña actual.</p>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-medium text-sm rounded-lg hover:bg-blue-700 shadow-md hover:shadow-lg transition-all flex items-center"><i class="fas fa-save mr-2"></i> Guardar Cambios</button>
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
        tags: true, // Permite escribir valores nuevos
        placeholder: "-- Selecciona o escribe Ej. 521XXXXXXXXXX@c.us --",
        allowClear: true
    });
});
</script>