<div class="max-w-7xl mx-auto mt-4 mb-10">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-toolbox text-indigo-500 mr-3"></i> Directorio de Herramientas IA</h2>
            <p class="text-sm text-gray-500 mt-1">Lista de funciones (Function Calling) disponibles para el modelo Gemini.</p>
        </div>
        <a href="<?= base_url('ia') ?>" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 font-medium transition text-sm flex items-center shadow-sm">
            <i class="fas fa-arrow-left mr-2 text-gray-400"></i> Volver a IA
        </a>
    </div>

    <!-- Herramientas del Bot de WhatsApp -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex items-center">
            <i class="fab fa-whatsapp text-green-500 mr-2 text-lg"></i>
            <h3 class="font-bold text-gray-800">Herramientas del Asistente Público (WhatsApp)</h3>
        </div>
        <div class="p-0 overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white text-gray-500 text-xs uppercase border-b border-gray-100">
                        <th class="p-4 font-medium w-48">Módulo Origen</th>
                        <th class="p-4 font-medium w-64">Nombre de la Herramienta</th>
                        <th class="p-4 font-medium">Descripción (Instrucción para la IA)</th>
                        <th class="p-4 font-medium text-center w-24">Estado</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700 divide-y divide-gray-50">
                    <?php if(empty($herramientasWhatsApp)): ?>
                        <tr><td colspan="4" class="p-6 text-center text-gray-400">No hay herramientas registradas para WhatsApp.</td></tr>
                    <?php else: foreach($herramientasWhatsApp as $hw): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4"><span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs font-bold"><?= htmlspecialchars($hw['modulo']) ?></span></td>
                            <td class="p-4 font-mono text-indigo-600 text-xs"><?= htmlspecialchars($hw['nombre']) ?></td>
                            <td class="p-4 text-gray-600 leading-relaxed"><?= htmlspecialchars($hw['descripcion']) ?></td>
                            <td class="p-4 text-center">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" onchange="toggleHerramienta('<?= htmlspecialchars($hw['nombre']) ?>', this.checked)" <?= $hw['estado'] ? 'checked' : '' ?>>
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                </label>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Herramientas del Copiloto Interno -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex items-center">
            <i class="fas fa-robot text-indigo-500 mr-2 text-lg"></i>
            <h3 class="font-bold text-gray-800">Herramientas del Copiloto Interno (CRM)</h3>
        </div>
        <div class="p-0 overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white text-gray-500 text-xs uppercase border-b border-gray-100">
                        <th class="p-4 font-medium w-48">Módulo Origen</th>
                        <th class="p-4 font-medium w-64">Nombre de la Herramienta</th>
                        <th class="p-4 font-medium">Descripción (Instrucción para la IA)</th>
                        <th class="p-4 font-medium text-center w-24">Estado</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700 divide-y divide-gray-50">
                    <?php if(empty($herramientasCopiloto)): ?>
                        <tr><td colspan="4" class="p-6 text-center text-gray-400">No hay herramientas registradas para el Copiloto.</td></tr>
                    <?php else: foreach($herramientasCopiloto as $hc): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4"><span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs font-bold"><?= htmlspecialchars($hc['modulo']) ?></span></td>
                            <td class="p-4 font-mono text-indigo-600 text-xs"><?= htmlspecialchars($hc['nombre']) ?></td>
                            <td class="p-4 text-gray-600 leading-relaxed"><?= htmlspecialchars($hc['descripcion']) ?></td>
                            <td class="p-4 text-center">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" onchange="toggleHerramienta('<?= htmlspecialchars($hc['nombre']) ?>', this.checked)" <?= $hc['estado'] ? 'checked' : '' ?>>
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                </label>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleHerramienta(nombre, estado) {
    fetch('<?= base_url('ia/toggleHerramienta') ?>', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({nombre: nombre, estado: estado}) })
    .then(res => res.json()).then(data => { if(data.status !== 'success') alert('Error al guardar el estado'); })
    .catch(err => alert('Error de conexión'));
}
</script>