<div class="max-w-4xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-print text-gray-600 mr-3"></i> Configuración de Documentos</h2>
            <p class="text-sm text-gray-500 mt-1">Ajusta los formatos de tickets, PDFs y salidas de impresión.</p>
        </div>
    </div>

    <?php if(isset($success)): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-xl flex items-center shadow-sm text-sm">
            <i class="fas fa-check-circle text-lg mr-3"></i> 
            <div><?= htmlspecialchars($success) ?></div>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-6">
        <form action="<?= base_url('documentos/guardarConfiguracion') ?>" method="POST" class="space-y-6">
            
            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3"><i class="fas fa-receipt mr-2 text-blue-500"></i> Formato de Ticket (Térmico)</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Ancho del Papel</label>
                    <select name="ticket_ancho" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="80mm" <?= ($configDoc['ticket_ancho'] ?? '') == '80mm' ? 'selected' : '' ?>>80 mm (Estándar Impresora Grande)</option>
                        <option value="58mm" <?= ($configDoc['ticket_ancho'] ?? '') == '58mm' ? 'selected' : '' ?>>58 mm (Pequeño / Bluetooth Portátil)</option>
                    </select>
                </div>
                
                <div class="flex items-center mt-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="imprimir_logo" value="1" class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500" <?= !empty($configDoc['imprimir_logo']) ? 'checked' : '' ?>>
                        <span class="ml-2 text-sm font-bold text-gray-700">Imprimir Logotipo del Negocio</span>
                    </label>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Mensaje de Saludo</label>
                    <input type="text" name="ticket_saludo" value="<?= htmlspecialchars($configDoc['ticket_saludo'] ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Ej. ¡Gracias por su compra!">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Mensaje al Pie (Términos y Condiciones)</label>
                    <textarea name="ticket_pie" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none" placeholder="Ej. No hay devoluciones."><?= htmlspecialchars($configDoc['ticket_pie'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="pt-4 mt-6 border-t border-gray-100 flex justify-end gap-3">
                <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold transition shadow-md flex items-center text-sm"><i class="fas fa-save mr-2"></i> Guardar Configuración</button>
            </div>
        </form>
    </div>
</div>