<div class="flex h-[calc(100vh-100px)] bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden relative">
    <!-- Panel Izquierdo: Lista de Chats -->
    <div id="panel-lista" class="w-full md:w-1/3 lg:w-1/4 border-r border-gray-100 bg-gray-50 flex flex-col absolute md:relative inset-0 z-20 transition-transform duration-300 md:translate-x-0">
        <div class="p-4 border-b border-gray-100 bg-white z-10">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-bold text-gray-800 flex items-center">
                    <a href="<?= base_url('whatsapp') ?>" class="mr-3 text-gray-400 hover:text-indigo-600 transition" title="Volver al panel"><i class="fas fa-arrow-left"></i></a>
                    <i class="fab fa-whatsapp text-green-500 mr-2 text-lg"></i> Bandeja
                    <span id="global-status" class="ml-2 flex h-2 w-2 relative" title="Conectando a Node...">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span>
                    </span>
                </h3>
                <div class="flex gap-2">
                    <button onclick="publicarEstado()" class="text-xs bg-purple-50 text-purple-600 px-3 py-1.5 rounded-lg font-medium hover:bg-purple-100 transition"><i class="fas fa-bullhorn mr-1"></i> Estado</button>
                </div>
            </div>
            <div class="flex bg-gray-100 p-1 rounded-lg">
                <button onclick="filtrarLista('individual')" class="flex-1 text-xs py-1.5 rounded-md bg-white shadow-sm font-bold text-gray-700 filter-btn" id="btn-filtro-individual">Chats</button>
                <button onclick="filtrarLista('grupo')" class="flex-1 text-xs py-1.5 rounded-md text-gray-500 font-medium hover:text-gray-700 filter-btn" id="btn-filtro-grupo">Grupos</button>
                <button onclick="filtrarLista('estado')" class="flex-1 text-xs py-1.5 rounded-md text-gray-500 font-medium hover:text-gray-700 filter-btn" id="btn-filtro-estado">Estados</button>
                <button onclick="filtrarLista('todos')" class="flex-1 text-xs py-1.5 rounded-md text-gray-500 font-medium hover:text-gray-700 filter-btn" id="btn-filtro-todos">Todos</button>
               
            </div>
        </div>
        <div class="flex-1 overflow-y-auto" id="chat-list">
            <?php if(empty($contactos)): ?><div class="text-center mt-10 text-gray-400 text-sm"><i class="fas fa-inbox text-2xl mb-2 opacity-50"></i><br>Bandeja vacía</div>
            <?php else: foreach($contactos as $c): 
                $isStatus = $c['whatsapp_id'] === 'status@broadcast';
                $isGroup = strpos($c['whatsapp_id'], '@g.us') !== false; 
                $tipoChat = $isStatus ? 'estado' : ($isGroup ? 'grupo' : 'individual'); 
                $iconoAvatar = $isStatus ? 'fa-circle-notch' : ($isGroup ? 'fa-users' : 'fa-user');
                $nombreMostrar = $isStatus ? '🌟 Feed de Estados' : htmlspecialchars($c['nombre']);
                $colorAvatar = $isStatus ? 'bg-purple-100 text-purple-600' : 'bg-indigo-100 text-indigo-600';
            ?>
                <div class="chat-item p-3 bg-white border-b border-gray-100 cursor-pointer hover:bg-gray-50 transition flex items-center" style="<?= $tipoChat !== 'individual' ? 'display: none;' : '' ?>" data-tipo="<?= $tipoChat ?>" data-whatsapp-id="<?= htmlspecialchars($c['whatsapp_id']) ?>" onclick="abrirChat(<?= $c['id'] ?>, '<?= htmlspecialchars($c['whatsapp_id']) ?>', '<?= addslashes($nombreMostrar) ?>', <?= $isGroup ? 'true' : 'false' ?>, <?= $c['bot_activo'] ?? 1 ?>)">
                    <div class="w-10 h-10 <?= $colorAvatar ?> rounded-full flex items-center justify-center font-bold mr-3 flex-shrink-0"><i class="fas <?= $iconoAvatar ?>"></i></div>
                    <div class="flex-1 overflow-hidden">
                        <div class="flex justify-between items-center"><h4 class="font-bold text-sm text-gray-800 truncate"><?= $nombreMostrar ?></h4><span class="text-[10px] text-gray-400"><?= !empty($c['fecha_ultimo']) ? date('H:i', strtotime($c['fecha_ultimo'])) : '' ?></span></div>
                        <p class="text-xs text-gray-500 truncate">
                            <?php 
                            if (($c['ultimo_tipo'] ?? '') === 'imagen') echo '📷 Imagen';
                            elseif (($c['ultimo_tipo'] ?? '') === 'audio') echo '🎵 Audio';
                            elseif (($c['ultimo_tipo'] ?? '') === 'video') echo '🎥 Video';
                            elseif (($c['ultimo_tipo'] ?? '') === 'archivo') echo '📄 Archivo';
                            else echo htmlspecialchars($c['ultimo_mensaje'] ?? '');
                            ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>

    <!-- Panel Central: Conversación -->
    <div id="panel-chat" class="w-full md:w-2/3 lg:flex-1 flex flex-col absolute md:relative inset-0 z-30 bg-white transition-transform duration-300 transform translate-x-full md:translate-x-0">
        <div class="p-4 border-b border-gray-100 bg-white flex items-center shadow-sm z-10">
            <button onclick="volverListaChats()" class="md:hidden mr-3 text-gray-500 hover:text-indigo-600 focus:outline-none"><i class="fas fa-arrow-left"></i></button>
            <div id="chat-header-avatar" class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 mr-3"><i class="fas fa-user"></i></div>
            <div><h4 class="font-bold text-gray-800 text-sm" id="chat-title">Selecciona una conversación</h4><p class="text-xs text-green-500" id="chat-number"><i class="fas fa-circle text-[8px] mr-1"></i>Esperando...</p></div>
            
            <!-- Botón IA (Oculto por defecto hasta elegir chat) -->
            <div class="ml-auto hidden" id="chat-header-actions">
                <button onclick="toggleInfoPanel()" class="mr-3 lg:hidden text-gray-400 hover:text-indigo-500 transition focus:outline-none"><i class="fas fa-ellipsis-v text-lg px-2"></i></button>
                <button id="btn-toggle-bot" onclick="toggleBotActual()" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors shadow-sm">
                    <i class="fas fa-robot"></i> <span id="lbl-bot-status">IA</span>
                </button>
            </div>
        </div>
        <div class="flex-1 bg-[#efeae2] overflow-y-auto p-4 flex flex-col gap-2 relative" id="chat-messages">
            <div class="relative z-10 text-center text-gray-500 text-sm mt-10" id="chat-placeholder">Haz clic en un contacto a la izquierda para cargar los mensajes.</div>
        </div>
        
        <!-- Previsualizador de archivo adjunto -->
        <div id="chat-file-preview" class="hidden p-3 bg-white border-t border-gray-100 flex items-center gap-3 shadow-inner z-10">
            <div class="relative">
                <img id="preview-img" src="" class="hidden w-12 h-12 object-cover rounded shadow-sm border border-gray-200">
                <div id="preview-file" class="hidden w-12 h-12 bg-indigo-50 text-indigo-500 rounded flex items-center justify-center text-xl shadow-sm border border-gray-200"><i class="fas fa-file-alt"></i></div>
                <button onclick="removerArchivo()" class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-[10px] hover:bg-red-600 shadow"><i class="fas fa-times"></i></button>
            </div>
            <div class="flex-1 min-w-0"><p id="preview-name" class="text-xs font-bold text-gray-700 truncate">archivo.jpg</p><p id="preview-size" class="text-[10px] text-gray-400">120 KB</p></div>
        </div>

        <div class="p-3 bg-gray-50 border-t border-gray-100 flex items-center gap-2 relative z-10">
            <input type="file" id="chat-file-input" class="hidden">
            <button onclick="document.getElementById('chat-file-input').click()" class="w-10 h-10 rounded-full bg-white border border-gray-200 text-gray-500 hover:bg-gray-100 transition flex-shrink-0"><i class="fas fa-paperclip"></i></button>
            <input type="text" id="chat-input" class="flex-1 border border-gray-200 rounded-full px-4 py-2 focus:ring-1 focus:ring-indigo-500 focus:outline-none shadow-sm text-sm" placeholder="Escribe un mensaje..." disabled onkeypress="if(event.key === 'Enter') enviarMensaje()">
            <button id="btn-send" disabled class="w-10 h-10 rounded-full bg-green-500 text-white shadow hover:bg-green-600 transition flex items-center justify-center flex-shrink-0 disabled:opacity-50"><i class="fas fa-paper-plane relative right-0.5"></i></button>
        </div>
    </div>

    <!-- 3ra Columna: Panel de CRM / Módulos -->
    <div id="panel-info" class="hidden lg:flex w-full md:w-1/3 lg:w-1/4 border-l border-gray-100 bg-gray-50 flex-col absolute lg:relative inset-y-0 right-0 z-40 transform translate-x-full lg:translate-x-0 transition-transform duration-300">
        <div class="p-4 border-b border-gray-100 bg-white flex justify-between items-center z-10 shadow-sm">
            <h3 class="font-bold text-gray-800 text-sm flex items-center"><i class="fas fa-layer-group text-indigo-500 mr-2"></i> Acciones CRM</h3>
            <button onclick="toggleInfoPanel()" class="lg:hidden text-gray-400 hover:text-red-500 focus:outline-none"><i class="fas fa-times text-lg"></i></button>
        </div>
        <div class="flex-1 overflow-y-auto p-4 custom-scrollbar" id="info-panel-content">
            <div class="text-center mt-10 text-gray-400">
                <i class="fas fa-mouse-pointer text-3xl mb-3 opacity-50"></i>
                <p class="text-sm">Selecciona un chat para ver sus accesos directos y datos.</p>
            </div>
        </div>
    </div>

</div>

<script>
    // Extraemos tu ID y Nombre real desde la sesión del sistema
    const NOMBRE_ASESOR_ACTUAL = "<?= htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['nombre'] ?? $_SESSION['nombres'] ?? $_SESSION['nombre_usuario'] ?? $_SESSION['username'] ?? $_SESSION['usuario'] ?? 'Asesor') ?>";
    const ID_ASESOR_ACTUAL = <?= $_SESSION['user_id'] ?? $_SESSION['usuario_id'] ?? $_SESSION['id_usuario'] ?? $_SESSION['id'] ?? 0 ?>;
</script>
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script>
    let chatActual = { id: null, whatsapp_id: null, nombre: null, bot_activo: 1 };
    let archivoActual = null; let nombreArchivoActual = null;
    let offsetMensajes = 0; let cargandoMensajes = false; let finMensajes = false;

    // Cerrar menús de opciones al hacer clic en cualquier otra parte
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.msg-options-menu') && !e.target.closest('.msg-options-btn')) {
            document.querySelectorAll('.msg-options-menu').forEach(menu => menu.classList.add('hidden'));
        }
    });

    window.toggleMenuOpciones = function(id) {
        document.querySelectorAll('.msg-options-menu').forEach(menu => { if (menu.id !== id) menu.classList.add('hidden'); });
        document.getElementById(id).classList.toggle('hidden');
    };

    window.actualizarListaChats = function(whatsappId, mensaje, tipo, nombre) {
        const listContainer = document.getElementById('chat-list');
        let item = document.querySelector(`.chat-item[data-whatsapp-id="${whatsappId}"]`);
        
        let previewText = '';
        if (tipo === 'imagen') previewText = '📷 Imagen';
        else if (tipo === 'audio') previewText = '🎵 Audio';
        else if (tipo === 'video') previewText = '🎥 Video';
        else if (tipo === 'archivo') previewText = '📄 Archivo';
        else previewText = mensaje || '';
        
        const timeStr = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

        if (item) {
            const previewEl = item.querySelector('.text-xs.text-gray-500.truncate'); if (previewEl) previewEl.innerText = previewText;
            const timeEl = item.querySelector('.text-\\[10px\\].text-gray-400'); if (timeEl) timeEl.innerText = timeStr;
            listContainer.prepend(item); // Mover el chat al principio de la lista
        } else {
            // Si es un contacto nuevo, recargamos la lista en segundo plano sin interrumpir
            fetch('<?= base_url('whatsapp/chat') ?>')
                .then(res => res.text())
                .then(html => {
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const newList = doc.getElementById('chat-list');
                    if (newList) {
                        listContainer.innerHTML = newList.innerHTML;
                        const filtroActivo = document.querySelector('.filter-btn.bg-white')?.id.replace('btn-filtro-', '') || 'individual';
                        filtrarLista(filtroActivo);
                    }
                }).catch(err => console.error(err));
        }
    };

    document.getElementById('chat-file-input').addEventListener('change', function(e) {
        const file = e.target.files[0]; if (!file) return;
        nombreArchivoActual = file.name;
        const reader = new FileReader();
        reader.onload = function(evt) {
            archivoActual = evt.target.result;
            document.getElementById('preview-name').innerText = file.name;
            document.getElementById('preview-size').innerText = (file.size / 1024).toFixed(1) + ' KB';
            if (file.type.startsWith('image/')) { document.getElementById('preview-img').src = archivoActual; document.getElementById('preview-img').classList.remove('hidden'); document.getElementById('preview-file').classList.add('hidden'); } 
            else { document.getElementById('preview-file').classList.remove('hidden'); document.getElementById('preview-img').classList.add('hidden'); }
            document.getElementById('chat-file-preview').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    });

    window.removerArchivo = function() { archivoActual = null; nombreArchivoActual = null; document.getElementById('chat-file-input').value = ''; document.getElementById('chat-file-preview').classList.add('hidden'); };

    function actualizarUIBot() {
        const btn = document.getElementById('btn-toggle-bot');
        const lbl = document.getElementById('lbl-bot-status');
        if(chatActual.bot_activo === 1) {
            btn.className = "flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors bg-green-100 text-green-700 hover:bg-green-200 shadow-sm border border-green-200";
            lbl.innerText = "IA Atendiendo";
        } else {
            btn.className = "flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors bg-gray-100 text-gray-500 hover:bg-gray-200 shadow-sm border border-gray-200";
            lbl.innerText = "IA Apagada";
        }
    }

    window.toggleBotActual = function() {
        if(!chatActual.id) return;
        const nuevoEstado = chatActual.bot_activo === 1 ? 0 : 1;
        fetch('<?= base_url('whatsapp/toggleBot') ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({contacto_id: chatActual.id, estado: nuevoEstado})
        }).then(res => res.json()).then(data => {
            if(data.status === 'success') {
                chatActual.bot_activo = nuevoEstado;
                actualizarUIBot();
            }
        });
    }

    window.abrirChat = function(id, whatsappId, nombre, isGroup, botActivo = 1) {
        chatActual = { id, whatsapp_id: whatsappId, nombre, bot_activo: parseInt(botActivo) };
        offsetMensajes = 0; finMensajes = false; removerArchivo();
        
        document.getElementById('chat-title').innerText = nombre;
        document.getElementById('chat-number').innerHTML = `<i class="fas fa-circle text-[8px] mr-1 text-green-500"></i>${whatsappId}`;
        const isStatus = whatsappId === 'status@broadcast';
        document.getElementById('chat-header-avatar').innerHTML = isStatus ? '<i class="fas fa-bullhorn text-purple-500"></i>' : (isGroup ? '<i class="fas fa-users"></i>' : '<i class="fas fa-user"></i>');
        document.getElementById('chat-input').placeholder = isStatus ? "Escribe para publicar un nuevo estado..." : "Escribe un mensaje...";
        
        document.getElementById('chat-input').disabled = false;
        document.getElementById('btn-send').disabled = false;
        document.getElementById('chat-input').focus();

        if(isStatus || isGroup) {
            document.getElementById('chat-header-actions').classList.add('hidden');
        } else {
            document.getElementById('chat-header-actions').classList.remove('hidden');
            actualizarUIBot();
        }
        
        // Mostrar panel de chat en móviles deslizando
        document.getElementById('panel-chat').classList.remove('translate-x-full');
        document.getElementById('panel-lista').classList.add('-translate-x-full');

        // --- Llenar 3ra Columna (CRM) dinámicamente ---
        const infoPanel = document.getElementById('info-panel-content');
        if (!isGroup && !isStatus) {
            infoPanel.innerHTML = `
                <div class="text-center mt-10 text-indigo-500">
                    <i class="fas fa-spinner fa-spin text-3xl mb-3"></i>
                    <p class="text-xs text-gray-500">Cargando Inteligencia CRM...</p>
                </div>
            `;
            
            // Consultar Módulos
            fetch(`<?= base_url('whatsapp/panelCrm') ?>?whatsapp_id=${encodeURIComponent(whatsappId)}`)
                .then(res => res.json())
                .then(data => { if(data.html) infoPanel.innerHTML = data.html; })
                .catch(err => { infoPanel.innerHTML = '<div class="text-center mt-10 text-red-500 text-sm"><i class="fas fa-exclamation-triangle mb-2"></i><br>Error al cargar contexto.</div>'; });
                
        } else {
            infoPanel.innerHTML = `
                <div class="text-center mt-10 text-gray-400">
                    <i class="fas fa-ban text-3xl mb-3 opacity-50"></i>
                    <p class="text-sm">Sin acciones para Grupos o Estados.</p>
                </div>
            `;
        }

        cargarMensajes(true);
    };

    window.volverListaChats = function() {
        document.getElementById('panel-chat').classList.add('translate-x-full');
        document.getElementById('panel-lista').classList.remove('-translate-x-full');
    };

    window.toggleInfoPanel = function() {
        const panelInfo = document.getElementById('panel-info');
        if (panelInfo.classList.contains('hidden')) {
            panelInfo.classList.remove('hidden', 'translate-x-full');
            panelInfo.classList.add('flex');
        } else if (window.innerWidth < 1024) {
            panelInfo.classList.add('hidden', 'translate-x-full');
            panelInfo.classList.remove('flex');
        }
    };

    function cargarMensajes(inicial = false) {
        if (cargandoMensajes || finMensajes) return;
        cargandoMensajes = true;
        const chatMessages = document.getElementById('chat-messages');

        if (inicial) chatMessages.innerHTML = `<div class="relative z-10 flex flex-col items-center justify-center mt-10" id="chat-loader"><i class="fas fa-spinner fa-spin text-2xl text-indigo-500 mb-2"></i><span class="text-xs text-gray-500">Cargando historial...</span></div>`;
        else chatMessages.insertAdjacentHTML('afterbegin', '<div id="mini-loader" class="w-full text-center py-2 relative z-10 text-indigo-400"><i class="fas fa-spinner fa-spin"></i></div>');

        fetch(`<?= base_url('whatsapp/mensajes') ?>?id=${chatActual.id}&offset=${offsetMensajes}`)
            .then(res => res.json())
            .then(data => {
                cargandoMensajes = false;
                if (inicial) chatMessages.innerHTML = '';
                else { const ml = document.getElementById('mini-loader'); if (ml) ml.remove(); }
                
                const mensajes = data.mensajes || [];
                if (!mensajes || mensajes.length === 0) {
                    finMensajes = true;
                    if (inicial) chatMessages.innerHTML += `<div class="relative z-10 text-center text-gray-500 text-sm mt-10 bg-white/90 p-2 rounded-lg max-w-xs mx-auto">No hay mensajes previos. ¡Inicia la conversación!</div>`;
                    else if (offsetMensajes > 0 && !document.getElementById('fin-historial')) chatMessages.insertAdjacentHTML('afterbegin', '<div id="fin-historial" class="w-full text-center py-2 relative z-10 text-xs text-gray-400 mt-4 bg-white/80 rounded-lg max-w-xs mx-auto">Has llegado al principio de la conversación</div>');
                    return;
                }

                const oldHeight = chatMessages.scrollHeight;
                const fragment = document.createDocumentFragment();
                mensajes.forEach(msg => {
                    fragment.appendChild(crearElementoMensaje(msg.contenido, msg.direccion === 'saliente', msg.fecha_registro, msg.tipo, msg.nombre_usuario, msg.usuario_id, msg.es_bot)); 
                });

                if (inicial) { chatMessages.appendChild(fragment); chatMessages.scrollTop = chatMessages.scrollHeight; } 
                else { chatMessages.insertBefore(fragment, chatMessages.firstElementChild); chatMessages.scrollTop = chatMessages.scrollHeight - oldHeight; }
                
                offsetMensajes += mensajes.length;
                if (mensajes.length < 30) finMensajes = true;
            })
            .catch(err => {
                cargandoMensajes = false;
                console.error("Error:", err);
                if (inicial) chatMessages.innerHTML = `<div class="relative z-10 text-center text-red-500 text-sm mt-10 bg-white/90 p-2 rounded-lg max-w-xs mx-auto"><i class="fas fa-exclamation-triangle mb-1"></i><br>Error al cargar el historial.</div>`;
                else { const ml = document.getElementById('mini-loader'); if (ml) ml.remove(); }
            });
    }

    document.getElementById('chat-messages').addEventListener('scroll', function() { if (this.scrollTop === 0) cargarMensajes(false); });

    function crearElementoMensaje(texto, esSaliente, fecha = '', tipo = 'texto', remitente = null, usuario_id = null, es_bot = 0) {
        const timeStr = fecha ? new Date(fecha).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        const msgDiv = document.createElement('div');
        msgDiv.className = `relative z-10 max-w-[75%] p-2.5 rounded-lg text-sm shadow-sm ${esSaliente ? 'bg-green-100 self-end rounded-tr-none' : 'bg-white self-start rounded-tl-none'}`;
        
        let urlArchivo = texto;
        if (texto && texto.startsWith('storage/')) urlArchivo = '<?= rtrim(base_url(), '/') ?>/' + texto;

        let contenidoHtml = '';
        let opcionesHtml = '';
        
        // Menú desplegable para archivos multimedia
        if (tipo !== 'texto') {
            const menuId = 'menu-' + Math.random().toString(36).substr(2, 9);
            opcionesHtml = `
                <div class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity z-20">
                    <button onclick="toggleMenuOpciones('${menuId}'); event.stopPropagation();" class="msg-options-btn w-7 h-7 bg-black/40 backdrop-blur text-white rounded-full flex items-center justify-center hover:bg-black/60 focus:outline-none shadow">
                        <i class="fas fa-chevron-down text-[10px]"></i>
                    </button>
                    <div id="${menuId}" class="msg-options-menu hidden absolute right-0 mt-1 w-32 bg-white shadow-xl rounded-lg border border-gray-100 overflow-hidden z-50">
                        <a href="${urlArchivo}" download class="block px-4 py-2.5 text-xs font-medium text-gray-700 hover:bg-gray-50 flex items-center gap-2 transition-colors"><i class="fas fa-download text-gray-400"></i> Descargar</a>
                    </div>
                </div>
            `;
        }

        const esImagen = tipo === 'imagen' || (texto && texto.startsWith('data:image')) || urlArchivo.match(/\.(jpg|jpeg|png|gif|webp)$/i);
        const esAudio = tipo === 'audio' || (texto && (texto.startsWith('data:audio') || texto.startsWith('data:application/ogg'))) || urlArchivo.match(/\.(mp3|wav|ogg|m4a)$/i);
        const esVideo = tipo === 'video' || (texto && texto.startsWith('data:video')) || urlArchivo.match(/\.(mp4|webm|avi)$/i);
        const esArchivo = tipo === 'archivo' || (texto && texto.startsWith('storage/')) || (texto && texto.startsWith('data:'));

        if (esImagen) {
            contenidoHtml = `<div class="relative group inline-block">${opcionesHtml}<img src="${urlArchivo}" onclick="abrirVisor('${urlArchivo}')" class="max-w-xs rounded-lg mb-1 cursor-pointer hover:opacity-90 transition-opacity shadow-sm border border-black/5" style="max-height: 250px; object-fit: contain;" title="Clic para ampliar"></div>`;
        } else if (esAudio) {
            contenidoHtml = `<div class="relative group flex items-center pr-8"><audio controls class="max-w-xs h-10"><source src="${urlArchivo}" type="audio/ogg"></audio>${opcionesHtml.replace('top-1 right-1', 'top-1.5 right-0').replace('bg-black/40 text-white', 'bg-gray-200 text-gray-600 hover:bg-gray-300')}</div>`;
        } else if (esVideo) {
            contenidoHtml = `<div class="relative group inline-block">${opcionesHtml}<video controls class="max-w-xs rounded-lg mb-1 shadow-sm border border-black/5" style="max-height: 250px;"><source src="${urlArchivo}"></video></div>`;
        } else if (esArchivo) {
            let ext = 'doc';
            if (texto && texto.startsWith('data:')) {
                const mimeMatch = texto.match(/data:[a-zA-Z0-9-]+\/([a-zA-Z0-9-\+.]+);/);
                if (mimeMatch) {
                    ext = mimeMatch[1].toLowerCase();
                    if (ext.includes('wordprocessingml')) ext = 'docx';
                    else if (ext.includes('spreadsheetml')) ext = 'xlsx';
                }
            } else {
                ext = urlArchivo.split('.').pop().toLowerCase();
            }
            if (ext.length > 5 || ext.includes('/')) ext = 'doc';
            
            let iconClass = 'fa-file-alt text-indigo-500';
            if (ext === 'pdf') iconClass = 'fa-file-pdf text-red-500';
            else if (['doc', 'docx'].includes(ext)) iconClass = 'fa-file-word text-blue-500';
            else if (['xls', 'xlsx', 'csv'].includes(ext)) iconClass = 'fa-file-excel text-green-500';
            else if (['zip', 'rar', '7z'].includes(ext)) iconClass = 'fa-file-archive text-yellow-500';

            contenidoHtml = `
            <div class="relative group block min-w-[200px] mt-1">
                ${opcionesHtml.replace('top-1 right-1', '-top-2 -right-2').replace('bg-black/40 text-white', 'bg-white border border-gray-200 text-gray-600 shadow-sm hover:bg-gray-50')}
                <a href="${urlArchivo}" target="_blank" class="flex items-center gap-3 bg-white/60 p-2.5 rounded-lg border border-gray-200 hover:bg-white transition shadow-sm cursor-pointer">
                    <div class="w-10 h-10 rounded bg-white flex items-center justify-center shadow-sm flex-shrink-0"><i class="fas ${iconClass} text-2xl"></i></div>
                    <div class="flex-1 min-w-0"><span class="block truncate text-xs font-bold text-gray-700">Documento</span><span class="block text-[10px] text-gray-400 uppercase font-medium">${ext}</span></div>
                </a>
            </div>`;
        } else {
            const textoSeguro = texto ? texto.replace(/</g, "&lt;").replace(/>/g, "&gt;") : '';
            contenidoHtml = `<p class="text-gray-800 break-words" style="white-space: pre-wrap;">${textoSeguro}</p>`;
        }
        
        let nombreMostrar = esSaliente ? (remitente ? remitente : (usuario_id ? (usuario_id == ID_ASESOR_ACTUAL ? NOMBRE_ASESOR_ACTUAL : 'Asesor (CRM)') : '📱 Enviado desde otro dispositivo')) : (chatActual.nombre || 'Contacto');
        
        if (esSaliente && parseInt(es_bot || 0) === 1) {
            nombreMostrar = '🤖 Asistente IA';
        }

        msgDiv.innerHTML = `
            <div class="text-[10px] font-bold mb-1 ${esSaliente ? 'text-green-700' : 'text-indigo-500'} opacity-80">${nombreMostrar}</div>
            ${contenidoHtml}
            <div class="text-[10px] text-gray-500 mt-1 text-right">${timeStr} ${esSaliente ? '<i class="fas fa-check-double text-blue-400"></i>' : ''}</div>
        `;
        return msgDiv;
    }

    window.renderizarMensaje = function(texto, esSaliente, fecha = '', tipo = 'texto', remitente = null, usuario_id = null, es_bot = 0) {
        if (!texto && tipo === 'texto') return;
        const chatMessages = document.getElementById('chat-messages');
        chatMessages.appendChild(crearElementoMensaje(texto, esSaliente, fecha, tipo, remitente, usuario_id, es_bot));
        chatMessages.scrollTop = chatMessages.scrollHeight;
    };

    window.enviarMensaje = function() {
        const input = document.getElementById('chat-input');
        const mensaje = input.value.trim();
        if ((!mensaje && !archivoActual) || !chatActual.whatsapp_id) return;
        
        input.value = ''; 
        if (archivoActual) {
            let tipoVis = 'archivo'; if (archivoActual.startsWith('data:image')) tipoVis = 'imagen'; else if (archivoActual.startsWith('data:audio') || archivoActual.startsWith('data:application/ogg')) tipoVis = 'audio'; else if (archivoActual.startsWith('data:video')) tipoVis = 'video';
            renderizarMensaje(archivoActual, true, '', tipoVis, NOMBRE_ASESOR_ACTUAL, ID_ASESOR_ACTUAL);
            if (mensaje) renderizarMensaje(mensaje, true, '', 'texto', NOMBRE_ASESOR_ACTUAL, ID_ASESOR_ACTUAL);
            actualizarListaChats(chatActual.whatsapp_id, mensaje || 'Adjunto', mensaje ? 'texto' : tipoVis, NOMBRE_ASESOR_ACTUAL);
        } else { 
            renderizarMensaje(mensaje, true, '', 'texto', NOMBRE_ASESOR_ACTUAL, ID_ASESOR_ACTUAL); 
            actualizarListaChats(chatActual.whatsapp_id, mensaje, 'texto', NOMBRE_ASESOR_ACTUAL);
        }
        
        const payload = { numero: chatActual.whatsapp_id, mensaje: mensaje, contacto_id: chatActual.id, archivo: archivoActual, nombreArchivo: nombreArchivoActual };
        removerArchivo(); // Limpiar UI
        
        fetch('<?= base_url('whatsapp/enviar') ?>', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) })
        .catch(err => console.error("Error al enviar:", err));
    };

    window.filtrarLista = function(tipo) {
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('bg-white', 'shadow-sm', 'font-bold', 'text-gray-700');
            btn.classList.add('text-gray-500', 'font-medium');
        });
        const btnActivo = document.getElementById(`btn-filtro-${tipo}`);
        if(btnActivo) {
            btnActivo.classList.remove('text-gray-500', 'font-medium');
            btnActivo.classList.add('bg-white', 'shadow-sm', 'font-bold', 'text-gray-700');
        }
        document.querySelectorAll('.chat-item').forEach(item => {
            item.style.display = (tipo === 'todos' || item.dataset.tipo === tipo) ? 'flex' : 'none';
        });
    };

    window.publicarEstado = function() {
        const texto = prompt("Escribe tu nuevo Estado de WhatsApp (solo texto):");
        if (!texto || texto.trim() === '') return;
        
        fetch('<?= base_url('whatsapp/enviar') ?>', { 
            method: 'POST', 
            headers: { 'Content-Type': 'application/json' }, 
            body: JSON.stringify({ numero: 'status@broadcast', mensaje: texto.trim(), contacto_id: 0 }) 
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') alert("¡Estado publicado correctamente!");
            else alert("Hubo un error al publicar el estado.");
        })
        .catch(err => console.error("Error publicando estado:", err));
    };

    document.addEventListener('DOMContentLoaded', () => {
        filtrarLista('individual'); // Asegurar estado inicial
        
        const socket = io('http://127.0.0.1:3000');
        const globalStatus = document.getElementById('global-status');

        function updateStatus(isConnected) {
            if (isConnected) {
                globalStatus.title = "WhatsApp Conectado y Listo";
                globalStatus.innerHTML = '<span class="relative inline-flex rounded-full h-2 w-2 bg-green-500 shadow-[0_0_5px_#22c55e]"></span>';
            } else {
                globalStatus.title = "Desconectado de Node.js";
                globalStatus.innerHTML = `
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                `;
            }
        }

        fetch(nodeUrl + '/api/status')
            .then(res => res.json())
            .then(data => updateStatus(data.isConnected))
            .catch(err => {
                console.error('Error al conectar con Node.js:', err);
                updateStatus(false);
            });

        socket.on('wa_ready', () => updateStatus(true));
        socket.on('disconnect', () => updateStatus(false));

        socket.on('mensaje_entrante', (data) => {
            console.log('Mensaje entrante recibido:', data);
            
            actualizarListaChats(data.whatsapp_id, data.mensaje, data.tipo || 'texto', data.nombre);

            if (chatActual.whatsapp_id === data.whatsapp_id) {
                if (data.archivo) {
                    let tipoVis = 'archivo'; if (data.archivo.startsWith('data:image')) tipoVis = 'imagen'; else if (data.archivo.startsWith('data:audio') || data.archivo.startsWith('data:application/ogg')) tipoVis = 'audio'; else if (data.archivo.startsWith('data:video')) tipoVis = 'video';
                    renderizarMensaje(data.archivo, false, data.timestamp * 1000, tipoVis, data.nombre);
                }
                if (data.mensaje) {
                    renderizarMensaje(data.mensaje, false, data.timestamp * 1000, 'texto', data.nombre);
                }
            }
        });

        socket.on('mensaje_saliente_fisico', (data) => {
            console.log('Mensaje enviado desde el celular:', data);

            actualizarListaChats(data.whatsapp_id, data.mensaje, data.tipo || 'texto', 'Tú');

            if (chatActual.whatsapp_id === data.whatsapp_id) {
                if (data.archivo) {
                    let tipoVis = 'archivo'; if (data.archivo.startsWith('data:image')) tipoVis = 'imagen'; else if (data.archivo.startsWith('data:audio') || data.archivo.startsWith('data:application/ogg')) tipoVis = 'audio'; else if (data.archivo.startsWith('data:video')) tipoVis = 'video';
                    renderizarMensaje(data.archivo, true, data.timestamp * 1000, tipoVis, null);
                }
                if (data.mensaje) {
                    renderizarMensaje(data.mensaje, true, data.timestamp * 1000, 'texto', null);
                }
            }
        });
        
        document.getElementById('btn-send').addEventListener('click', enviarMensaje);
    });
</script>
