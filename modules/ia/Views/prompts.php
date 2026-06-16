<div class="max-w-4xl mx-auto mt-4 mb-10">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-comment-dots text-purple-500 mr-3"></i> Editor de Prompts y Personalidad</h2>
            <p class="text-sm text-gray-500 mt-1">Define cómo debe comportarse el asistente IA y sus reglas estrictas.</p>
        </div>
        <a href="<?= base_url('ia') ?>" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 font-medium transition text-sm flex items-center shadow-sm">
            <i class="fas fa-arrow-left mr-2 text-gray-400"></i> Volver a IA
        </a>
    </div>

    <?php if(isset($_GET['success'])): ?>
        <div class="bg-green-50 text-green-600 px-4 py-3 rounded-lg mb-6 border border-green-100 text-sm flex items-center shadow-sm">
            <i class="fas fa-check-circle mr-3 text-lg"></i> <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php endif; ?>

    <!-- Generador Mágico con IA -->
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-6 border border-indigo-100 mb-6 flex flex-col md:flex-row items-center gap-5 shadow-sm">
        <div class="w-14 h-14 bg-white text-indigo-500 rounded-full flex items-center justify-center text-2xl flex-shrink-0 shadow-sm border border-indigo-100">
            <i class="fas fa-magic"></i>
        </div>
        <div class="flex-1 w-full">
            <h3 class="font-bold text-indigo-900 text-lg">Generar con Inteligencia Artificial ✨</h3>
            <p class="text-sm text-indigo-700 mt-1">Describe a grandes rasgos cómo quieres que sea tu bot (nombre, estilo, empresa) y la IA redactará las instrucciones de sistema (Prompt) óptimas por ti.</p>
            <div class="flex flex-col sm:flex-row gap-3 mt-4">
                <input type="text" id="ai-description" class="flex-1 border border-indigo-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none shadow-sm transition" placeholder="Ej. Un bot llamado Martín para una mueblería, muy carismático y que use emojis...">
                <button type="button" onclick="generarConIA()" id="btn-ai-gen" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-bold shadow-md hover:bg-indigo-700 transition flex items-center justify-center whitespace-nowrap">
                    <i class="fas fa-sparkles mr-2"></i> Auto-Completar
                </button>
            </div>
            <p id="ai-status" class="text-xs font-bold text-indigo-600 mt-3 hidden"><i class="fas fa-spinner fa-spin mr-1"></i> La IA está pensando y redactando tu prompt...</p>
        </div>
    </div>

    <form action="<?= base_url('ia/guardarPrompts') ?>" method="POST" class="space-y-6">
        <!-- Personalidad -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex items-center">
                <i class="fas fa-user-astronaut text-indigo-500 mr-2 text-lg"></i>
                <h3 class="font-bold text-gray-800">Identidad y Personalidad</h3>
            </div>
            <div class="p-6">
                <p class="text-xs text-gray-500 mb-4">Define quién es el bot, cuál es su tono de voz y cómo debe presentarse ante los clientes.</p>
                <textarea name="personalidad" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none resize-y" required><?= htmlspecialchars($config['personalidad'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- Reglas Obligatorias -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-red-50 flex items-center">
                <i class="fas fa-exclamation-triangle text-red-500 mr-2 text-lg"></i>
                <h3 class="font-bold text-red-800">Reglas Obligatorias (Límites)</h3>
            </div>
            <div class="p-6">
                <p class="text-xs text-gray-500 mb-4">Escribe una regla por línea. Estas instrucciones evitan que la IA alucine o invente información. Usa tono imperativo.</p>
                <textarea name="reglas" rows="8" class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none resize-y font-mono" required><?= htmlspecialchars($config['reglas'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-bold rounded-lg shadow-md hover:bg-indigo-700 transition flex items-center">
                <i class="fas fa-save mr-2"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<script>
function generarConIA() {
    const desc = document.getElementById('ai-description').value.trim();
    if (!desc) {
        alert('Por favor, describe cómo quieres que sea el bot primero en la caja de texto.');
        return;
    }
    
    const btn = document.getElementById('btn-ai-gen');
    const status = document.getElementById('ai-status');
    
    btn.disabled = true;
    btn.classList.add('opacity-50', 'cursor-not-allowed');
    status.classList.remove('hidden');
    
    fetch('<?= base_url('ia/generarPromptConIA') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ descripcion: desc })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
        status.classList.add('hidden');
        
        if (data.status === 'success') {
            if (data.data.personalidad) document.querySelector('textarea[name="personalidad"]').value = data.data.personalidad;
            if (data.data.reglas) document.querySelector('textarea[name="reglas"]').value = data.data.reglas;
            
            // Efecto visual de actualización
            document.querySelector('textarea[name="personalidad"]').classList.add('ring-4', 'ring-green-400', 'bg-green-50');
            document.querySelector('textarea[name="reglas"]').classList.add('ring-4', 'ring-green-400', 'bg-green-50');
            setTimeout(() => {
                document.querySelector('textarea[name="personalidad"]').classList.remove('ring-4', 'ring-green-400', 'bg-green-50');
                document.querySelector('textarea[name="reglas"]').classList.remove('ring-4', 'ring-green-400', 'bg-green-50');
            }, 1500);
        } else { alert('Error: ' + (data.error || 'Algo salió mal al generar el prompt.')); }
    })
    .catch(err => { btn.disabled = false; btn.classList.remove('opacity-50', 'cursor-not-allowed'); status.classList.add('hidden'); alert('Error de conexión con el servidor.'); });
}
</script>