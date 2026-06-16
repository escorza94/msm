<div class="max-w-3xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-cog text-green-500 mr-2"></i> Configuración WhatsApp</h2>
            <p class="text-sm text-gray-500 mt-1">Ajustes del servidor y opciones de mensajería</p>
        </div>
        <a href="<?= base_url('whatsapp') ?>" class="text-sm bg-white border border-gray-200 text-gray-600 px-3 py-1.5 rounded-lg font-medium hover:bg-gray-50 transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
    </div>

    <?php if(isset($success)): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-xl flex items-center shadow-sm text-sm">
            <i class="fas fa-check-circle text-lg mr-3"></i> 
            <div><b>¡Ajustes guardados!</b> Refresca la página (F5) para ver tu nuevo ícono en el menú lateral.</div>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-6">
        <form action="<?= base_url('whatsapp/configuracion') ?>" method="POST" class="space-y-5">
            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3 mb-4"><i class="fas fa-palette mr-2 text-gray-400"></i> Apariencia del Módulo</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Interno (No editable)</label>
                    <input type="text" value="<?= htmlspecialchars($config['name'] ?? 'whatsapp') ?>" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 bg-gray-50 text-gray-500 cursor-not-allowed font-mono text-sm shadow-inner" disabled>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ícono (FontAwesome 6)</label>
                    <input type="text" name="icon" value="<?= htmlspecialchars($config['icon'] ?? 'fa-whatsapp text-green-500') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none text-sm transition shadow-sm" placeholder="Ej. fa-comments text-blue-500">
                    <p class="text-[11px] text-gray-400 mt-1.5">Clases de FontAwesome y Tailwind. Ej: <code>fa-robot text-purple-500</code></p>
                </div>
            </div>

            <div class="pt-4 mt-6 border-t border-gray-100 flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-green-500 text-white rounded-lg hover:bg-green-600 font-bold transition shadow flex items-center text-sm"><i class="fas fa-save mr-2"></i> Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>