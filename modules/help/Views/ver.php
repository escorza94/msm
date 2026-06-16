<div class="max-w-3xl mx-auto mt-8 mb-10">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-book-open text-teal-500 mr-3"></i> Lector de Artículo</h2>
            <p class="text-sm text-gray-500 mt-1">
                <?php if($articulo['tipo'] === 'publico'): ?>
                    <span class="text-indigo-600 font-bold"><i class="fas fa-robot mr-1"></i> La IA utiliza este artículo</span>
                <?php else: ?>
                    <span class="text-gray-500 font-bold"><i class="fas fa-user-lock mr-1"></i> Documento de uso Interno</span>
                <?php endif; ?>
            </p>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('help') ?>" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 font-medium transition text-sm flex items-center shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
            <a href="<?= base_url('help/editar?id=' . $articulo['id']) ?>" class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 font-medium transition text-sm flex items-center shadow-sm">
                <i class="fas fa-edit mr-2"></i> Editar
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden relative">
        <div class="p-8">
            <div class="mb-4">
                <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider"><i class="fas fa-folder mr-1 text-gray-300"></i> <?= htmlspecialchars($articulo['categoria_nombre'] ?? 'General') ?></span>
            </div>
            <h1 class="text-3xl font-black text-gray-800 mb-6 leading-tight"><?= htmlspecialchars($articulo['titulo']) ?></h1>
            
            <div class="prose prose-sm md:prose-base max-w-none text-gray-700 bg-gray-50 p-6 rounded-lg border border-gray-100 shadow-inner whitespace-pre-wrap font-medium leading-relaxed" id="contenido-texto"><?= htmlspecialchars($articulo['contenido']) ?></div>
            
            <div class="mt-8 pt-6 border-t border-gray-100 flex justify-between items-center">
                <div class="text-xs text-gray-400">
                    <i class="fas fa-clock mr-1"></i> Última actualización: <?= date('d/m/Y h:i A', strtotime($articulo['fecha_actualizacion'])) ?>
                </div>
                <button onclick="copiarAlPortapapeles()" id="btn-copiar" class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-900 transition text-xs font-bold flex items-center shadow-sm">
                    <i class="fas fa-copy mr-2"></i> Copiar para WhatsApp
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function copiarAlPortapapeles() {
    const texto = document.getElementById('contenido-texto').innerText;
    navigator.clipboard.writeText(texto).then(() => {
        const btn = document.getElementById('btn-copiar');
        const original = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check text-green-400 mr-2"></i> ¡Copiado!';
        setTimeout(() => { btn.innerHTML = original; }, 2000);
    });
}
</script>