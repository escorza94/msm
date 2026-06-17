<div class="max-w-5xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-8 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fab fa-whatsapp text-green-500 mr-2"></i> WhatsApp Gateway</h2>
            <p class="text-sm text-gray-500 mt-1">Panel de control y resumen del servicio</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= base_url('whatsapp/configuracion') ?>" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 font-medium transition text-sm flex items-center shadow-sm">
                <i class="fas fa-cog mr-2 text-gray-400"></i> Configuración
            </a>
            <div id="connection-status" class="px-4 py-2 rounded-lg bg-gray-50 text-gray-500 font-medium text-sm flex items-center border border-gray-200">
                <i class="fas fa-spinner fa-spin mr-2"></i> Verificando conexión...
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
            <div class="w-14 h-14 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-2xl mr-4"><i class="fas fa-address-book"></i></div>
            <div><p class="text-gray-500 text-xs font-bold uppercase tracking-wide">Contactos Guardados</p><p class="text-2xl font-bold text-gray-800"><?= $total_contactos ?? 0 ?></p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
            <div class="w-14 h-14 rounded-full bg-green-50 text-green-500 flex items-center justify-center text-2xl mr-4"><i class="fas fa-comment-dots"></i></div>
            <div><p class="text-gray-500 text-xs font-bold uppercase tracking-wide">Mensajes Intercambiados</p><p class="text-2xl font-bold text-gray-800"><?= $total_mensajes ?? 0 ?></p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center">
            <a href="<?= base_url('whatsapp/vincular') ?>" class="w-full text-center py-2 px-4 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 font-medium transition text-sm"><i class="fas fa-qrcode mr-2"></i> Vincular / Código QR</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="<?= base_url('whatsapp/chat') ?>" class="group bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all hover:-translate-y-1 flex items-center cursor-pointer">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-green-400 to-green-600 text-white flex items-center justify-center text-3xl shadow-lg shadow-green-200 mr-6 group-hover:scale-105 transition-transform"><i class="fab fa-whatsapp"></i></div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-gray-800 mb-1 group-hover:text-green-600 transition-colors">Bandeja de Mensajes</h3>
                <p class="text-xs text-gray-500">Abre la ventana de chat interactiva para enviar y recibir mensajes, archivos y estados en tiempo real.</p>
            </div>
            <i class="fas fa-chevron-right text-gray-300 group-hover:text-green-500 transition-colors"></i>
        </a>

        <a href="<?= base_url('whatsapp/contactos') ?>" class="group bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all hover:-translate-y-1 flex items-center cursor-pointer">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-400 to-blue-600 text-white flex items-center justify-center text-3xl shadow-lg shadow-blue-200 mr-6 group-hover:scale-105 transition-transform"><i class="fas fa-address-book"></i></div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-gray-800 mb-1 group-hover:text-blue-600 transition-colors">Directorio Telefónico</h3>
                <p class="text-xs text-gray-500">Visualiza la tabla, filtra y gestiona todos tus contactos, etiquetas y grupos guardados.</p>
            </div>
            <i class="fas fa-chevron-right text-gray-300 group-hover:text-blue-500 transition-colors"></i>
        </a>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const statusBadge = document.getElementById('connection-status');
        const updateStatus = (isConnected) => {
            statusBadge.className = isConnected ? "px-4 py-2 rounded-lg bg-green-50 text-green-600 font-medium text-sm flex items-center border border-green-200" : "px-4 py-2 rounded-lg bg-red-50 text-red-600 font-medium text-sm flex items-center border border-red-200";
            statusBadge.innerHTML = isConnected ? '<i class="fas fa-check-circle mr-2"></i> Conectado y Activo' : '<i class="fas fa-exclamation-triangle mr-2"></i> Desconectado de Node.js';
        };
        const nodeUrl = window.location.protocol + '//' + window.location.hostname + ':3000';
        fetch(nodeUrl + '/api/status').then(r => r.json()).then(d => updateStatus(d.isConnected)).catch(e => updateStatus(false));
    });
</script>