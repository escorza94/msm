<style>
    /* Estilos para el switch tipo iOS */
    .switch { position: relative; display: inline-block; width: 50px; height: 28px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px; }
    .slider:before { position: absolute; content: ""; height: 20px; width: 20px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%; }
    input:checked + .slider { background-color: #4ade80; } /* green-400 */
    input:checked + .slider:before { transform: translateX(22px); }
</style>

<div class="max-w-3xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-robot text-indigo-500 mr-2"></i> Configuración del Asistente IA</h2>
            <p class="text-sm text-gray-500 mt-1">Ajusta el comportamiento del bot de WhatsApp.</p>
        </div>
    </div>

    <?php if(isset($_GET['success'])): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-xl flex items-center shadow-sm text-sm">
            <i class="fas fa-check-circle text-lg mr-3"></i> 
            <div><b>¡Ajustes guardados!</b> El comportamiento del bot se ha actualizado.</div>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-6">
        <form action="<?= base_url('ia/configuracion') ?>" method="POST" class="space-y-6">
            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3 mb-4"><i class="fas fa-cogs mr-2 text-gray-400"></i> Reglas de Activación</h3>
            
            <div class="flex items-center justify-between p-4 rounded-lg bg-gray-50 border border-gray-200">
                <div>
                    <label for="activar_bot_por_defecto" class="font-medium text-gray-800">Activar para nuevos contactos</label>
                    <p class="text-xs text-gray-500 mt-1">Si un número desconocido te escribe, ¿el bot debe atenderlo por defecto?</p>
                </div>
                <label class="switch">
                    <input type="checkbox" id="activar_bot_por_defecto" name="activar_bot_por_defecto" value="1" <?= ($config['activar_bot_por_defecto'] ?? false) ? 'checked' : '' ?>>
                    <span class="slider"></span>
                </label>
            </div>

            <div class="flex items-center justify-between p-4 rounded-lg bg-gray-50 border border-gray-200">
                <div>
                    <label for="activar_bot_en_marketing" class="font-medium text-gray-800">Activar con campañas de marketing</label>
                    <p class="text-xs text-gray-500 mt-1">Si un cliente activa una campaña (ej. "PROMO20"), ¿el bot debe continuar la conversación?</p>
                </div>
                <label class="switch">
                    <input type="checkbox" id="activar_bot_en_marketing" name="activar_bot_en_marketing" value="1" <?= ($config['activar_bot_en_marketing'] ?? false) ? 'checked' : '' ?>>
                    <span class="slider"></span>
                </label>
            </div>

            <div class="pt-4 mt-6 border-t border-gray-100 flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 font-bold transition shadow flex items-center text-sm"><i class="fas fa-save mr-2"></i> Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>