<div class="max-w-4xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-file-signature text-teal-500 mr-2"></i> Nuevo Artículo</h2>
            <p class="text-sm text-gray-500 mt-1">Escribe reglas, guías o conocimiento para la IA y los asesores</p>
        </div>
        <a href="<?= base_url('help') ?>" class="text-sm bg-white border border-gray-200 text-gray-600 px-3 py-1.5 rounded-lg font-medium hover:bg-gray-50 transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
    </div>

    <?php if(isset($_GET['error'])): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-xl flex items-center shadow-sm text-sm">
            <i class="fas fa-exclamation-triangle text-lg mr-3"></i> <div><?= htmlspecialchars($_GET['error']) ?></div>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-6">
        <form action="<?= base_url('help/nuevo') ?>" method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Título o Pregunta (FAQ) <span class="text-red-500">*</span></label>
                    <input type="text" name="titulo" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-teal-500 outline-none text-sm transition" placeholder="Ej. ¿Cuáles son los tiempos de envío?">
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Categoría</label>
                    <select name="categoria_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-teal-500 outline-none text-sm bg-white">
                        <option value="">-- Sin categoría --</option>
                        <?php foreach($categorias as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Tipo de Visibilidad <span class="text-red-500">*</span></label>
                    <select name="tipo" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-teal-500 outline-none text-sm bg-white">
                        <option value="publico">Público (La IA lo usará para contestar a clientes)</option>
                        <option value="interno">Interno (Solo visible para el Staff en el CRM)</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Contenido / Respuesta <span class="text-red-500">*</span></label>
                    <p class="text-[10px] text-gray-500 mb-2">Si el artículo es "Público", escribe esto como si se lo estuvieras explicando al cliente amablemente.</p>
                    <textarea name="contenido" required rows="8" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-teal-500 outline-none text-sm transition resize-none"></textarea>
                </div>
            </div>
            <div class="pt-4 mt-6 border-t border-gray-100 flex justify-end gap-3"><a href="<?= base_url('help') ?>" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition text-sm">Cancelar</a><button type="submit" class="px-5 py-2.5 bg-teal-500 text-white rounded-lg hover:bg-teal-600 font-bold transition shadow-md text-sm">Guardar Artículo</button></div>
        </form>
    </div>
</div>