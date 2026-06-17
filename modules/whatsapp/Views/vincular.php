<div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 max-w-md mx-auto mt-10 text-center">
    <h2 class="text-2xl font-bold text-gray-800 mb-2"><i class="fas fa-qrcode text-indigo-500 mr-2"></i> Vincular Dispositivo</h2>
    <p class="text-sm text-gray-500 mb-6">Abre WhatsApp en tu celular y escanea este código para conectar tu número al CRM.</p>
    <div class="bg-gray-50 rounded-xl p-4 inline-flex items-center justify-center min-w-[250px] min-h-[250px] border border-gray-200 mb-4">
        <div id="qr-loader" class="text-gray-400 text-sm"><i class="fas fa-spinner fa-spin text-3xl mb-2 text-indigo-500"></i><br>Consultando a Node.js...</div>
        <img id="qr-img" src="" class="hidden mx-auto rounded">
        <div id="qr-success" class="hidden text-green-600 font-bold"><i class="fas fa-check-circle text-4xl mb-2"></i><br>¡WhatsApp Conectado!</div>
    </div>
    <div class="text-left bg-blue-50 text-blue-800 p-3 rounded-lg text-xs mb-4"><i class="fas fa-info-circle mr-1"></i>Asegúrate de que el servidor Node.js esté ejecutándose en el puerto 3000.</div>
    <a href="<?= base_url('whatsapp') ?>" class="text-sm text-gray-500 hover:text-gray-800 font-medium underline">Regresar al panel</a>
</div>

<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const nodeUrl = window.location.protocol + '//' + window.location.hostname + ':3000';
        const socket = io(nodeUrl);
        
        const qrLoader = document.getElementById('qr-loader');
        const qrImg = document.getElementById('qr-img');
        const qrSuccess = document.getElementById('qr-success');

        // Comprobar estado inicial del Gateway al cargar la página
        fetch(nodeUrl + '/api/status')
            .then(res => res.json())
            .then(data => {
                if (data.isConnected) {
                    mostrarExito();
                } else if (data.qr) {
                    mostrarQR(data.qr);
                }
            })
            .catch(err => {
                qrLoader.innerHTML = '<i class="fas fa-exclamation-triangle text-3xl mb-2 text-red-500"></i><br>Error al conectar con Node.js. ¿Está encendido?';
                console.error('Error consultando el status de Node.js:', err);
            });

        // Escuchar eventos en tiempo real a través de WebSockets
        socket.on('qr_update', (qrDataUrl) => mostrarQR(qrDataUrl));
        socket.on('wa_ready', () => mostrarExito());

        function mostrarQR(url) { qrLoader.classList.add('hidden'); qrSuccess.classList.add('hidden'); qrImg.src = url; qrImg.classList.remove('hidden'); }
        function mostrarExito() { qrLoader.classList.add('hidden'); qrImg.classList.add('hidden'); qrSuccess.classList.remove('hidden'); }
    });
</script>
