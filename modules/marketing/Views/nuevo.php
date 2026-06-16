<div class="max-w-4xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-magic text-pink-500 mr-2"></i> Nueva Regla de Pauta</h2>
            <p class="text-sm text-gray-500 mt-1">Configura la intercepción para un anuncio de Facebook o Instagram</p>
        </div>
        <a href="<?= base_url('marketing') ?>" class="text-sm bg-white border border-gray-200 text-gray-600 px-3 py-1.5 rounded-lg font-medium hover:bg-gray-50 transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
    </div>

    <?php if(isset($_GET['error'])): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-xl flex items-center shadow-sm text-sm">
            <i class="fas fa-exclamation-triangle text-lg mr-3"></i> <div><?= htmlspecialchars($_GET['error']) ?></div>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-6">
        <form action="<?= base_url('marketing/nuevo') ?>" method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nombre Interno de la Campaña <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-pink-500 outline-none text-sm transition" placeholder="Ej. Pauta Comedores Buen Fin">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Texto Disparador (Mensaje Exacto de Meta) <span class="text-red-500">*</span></label>
                    <p class="text-[10px] text-gray-500 mb-2">Copia y pega el mensaje predefinido que el cliente envía desde el anuncio.</p>
                    <input type="text" name="texto_disparador" required class="w-full border border-pink-300 bg-pink-50 text-pink-700 font-mono rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-pink-500 outline-none text-sm transition" placeholder="Ej. Hola, vi este anuncio en Facebook y quiero más información.">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Respuesta Inmediata del Sistema (Opcional)</label>
                    <p class="text-[10px] text-gray-500 mb-2">Este mensaje se enviará al instante antes de que la IA tome el control. Puedes dejarlo en blanco si quieres que la IA conteste directo.</p>
                    <textarea name="respuesta_automatica" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-pink-500 outline-none text-sm transition resize-none" placeholder="Ej. ¡Hola! Claro que sí, enseguida un asesor virtual te atenderá..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Etiqueta Automática (Opcional)</label>
                    <input type="text" name="etiqueta_contacto" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none text-sm" placeholder="Ej. Lead Facebook">
                </div>

                <div class="flex items-center mt-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="activar_bot" value="1" checked class="w-5 h-5 text-pink-600 rounded border-gray-300 focus:ring-pink-500">
                        <span class="ml-2 text-sm font-bold text-gray-700">Permitir que la Inteligencia Artificial tome la conversación tras este mensaje</span>
                    </label>
                </div>
            </div>
            <div class="pt-4 mt-6 border-t border-gray-100 flex justify-end gap-3"><a href="<?= base_url('marketing') ?>" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition text-sm">Cancelar</a><button type="submit" class="px-5 py-2.5 bg-pink-500 text-white rounded-lg hover:bg-pink-600 font-bold transition shadow-md text-sm">Guardar Regla</button></div>
        </form>
    </div>
</div>