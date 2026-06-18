<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? 'AED Core' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-800">
    <?php
    $sysAppName = 'AED Core';
    $sysBusinessLogo = '';
    $themeSidebarBg = '#111827';
    $themeSidebarText = '#ffffff';
    $themePrimary = '#4f46e5';
    $themeTextColor = '#374151';
    $themeTopbarBg = '#ffffff';
    $themeBodyBg = '#f3f4f6';
    if (file_exists(BASE_PATH . '/config.php')) {
        $sysConfigGlobal = require BASE_PATH . '/config.php';
        $sysAppName = $sysConfigGlobal['APP_NAME'] ?? 'AED Core';
        $sysBusinessLogo = $sysConfigGlobal['BUSINESS_LOGO'] ?? '';
        $themeSidebarBg = $sysConfigGlobal['THEME_SIDEBAR_BG'] ?? '#111827';
        $themeSidebarText = $sysConfigGlobal['THEME_SIDEBAR_TEXT'] ?? '#ffffff';
        $themePrimary = $sysConfigGlobal['THEME_PRIMARY'] ?? '#4f46e5';
        $themeTextColor = $sysConfigGlobal['THEME_TEXT_COLOR'] ?? '#374151';
        $themeTopbarBg = $sysConfigGlobal['THEME_TOPBAR_BG'] ?? '#ffffff';
        $themeBodyBg = $sysConfigGlobal['THEME_BODY_BG'] ?? '#f3f4f6';
    }
    ?>
    <style>
        :root { 
            --theme-sidebar-bg: <?= htmlspecialchars($themeSidebarBg) ?>; 
            --theme-sidebar-text: <?= htmlspecialchars($themeSidebarText) ?>; 
            --theme-primary: <?= htmlspecialchars($themePrimary) ?>; 
            --theme-text-color: <?= htmlspecialchars($themeTextColor) ?>; 
            --theme-topbar-bg: <?= htmlspecialchars($themeTopbarBg) ?>; 
            --theme-body-bg: <?= htmlspecialchars($themeBodyBg) ?>; 
        }
        .bg-custom-sidebar { background-color: var(--theme-sidebar-bg) !important; }
        .text-custom-sidebar { color: var(--theme-sidebar-text) !important; }
        .text-custom-sidebar-muted { color: var(--theme-sidebar-text) !important; opacity: 0.7; }
        .text-custom-sidebar-title { color: var(--theme-sidebar-text) !important; opacity: 0.4; }
        .text-custom-primary { color: var(--theme-primary) !important; }
        .border-custom-sidebar { border-color: color-mix(in srgb, var(--theme-sidebar-text) 10%, transparent) !important; }
        .hover-custom-sidebar:hover { background-color: color-mix(in srgb, var(--theme-sidebar-text) 10%, transparent) !important; opacity: 1 !important; color: var(--theme-sidebar-text) !important; }
        
        .bg-custom-body { background-color: var(--theme-body-bg) !important; }
        .text-custom-body { color: var(--theme-text-color) !important; }
        .bg-custom-topbar { background-color: var(--theme-topbar-bg) !important; }

        /* Estilos Markdown para el Copiloto IA */
        .markdown-body p { margin-bottom: 0.5rem; }
        .markdown-body ul { list-style-type: disc; padding-left: 1.25rem; margin-bottom: 0.5rem; }
        .markdown-body ol { list-style-type: decimal; padding-left: 1.25rem; margin-bottom: 0.5rem; }
        .markdown-body strong { font-weight: bold; }
        .markdown-body a { color: #2563eb; text-decoration: underline; transition: color 0.2s; word-break: break-word; }
        .markdown-body a:hover { color: #1d4ed8; }
        .markdown-body table { width: 100%; border-collapse: collapse; margin-bottom: 0.75rem; font-size: 0.8rem; overflow-x: auto; display: block; }
        .markdown-body th, .markdown-body td { border: 1px solid #e5e7eb; padding: 0.4rem 0.5rem; }
        .markdown-body th { background-color: #f9fafb; font-weight: bold; text-align: left; }
        
        /* Reglas para la barra lateral contraída (solo en Desktop) */
        @media (min-width: 768px) {
            #sidebar.sidebar-collapsed { width: 4.5rem !important; }
            #sidebar.sidebar-collapsed .sidebar-text { display: none !important; }
            #sidebar.sidebar-collapsed .sidebar-divider { display: block !important; }
            #sidebar.sidebar-collapsed .sidebar-link { padding-left: 0.5rem !important; padding-right: 0.5rem !important; justify-content: center !important; }
            #sidebar.sidebar-collapsed .sidebar-icon-margin { margin-right: 0 !important; }
            #sidebar.sidebar-collapsed .sidebar-icon { display: block !important; margin: 0 auto; }
            #sidebar.sidebar-collapsed .sidebar-footer { padding: 1rem 0.5rem !important; }
        }
    </style>
    <div class="flex h-screen overflow-hidden">
        <!-- Overlay para móviles -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-gray-900/50 z-20 hidden md:hidden backdrop-blur-sm transition-opacity" onclick="toggleSidebar()"></div>
        
        <!-- Menú Lateral -->
        <aside id="sidebar" class="w-64 bg-custom-sidebar text-white flex flex-col fixed md:relative inset-y-0 left-0 transform -translate-x-full md:translate-x-0 transition-all duration-300 ease-in-out shadow-2xl md:shadow-xl z-30">
            <script>
                if (localStorage.getItem('sidebarCollapsed') === 'true' && window.innerWidth >= 768) {
                    const sb = document.getElementById('sidebar');
                    sb.classList.add('sidebar-collapsed');
                    // Quitamos la animación momentáneamente al cargar la página para evitar el "salto"
                    sb.classList.remove('transition-all', 'duration-300', 'ease-in-out');
                    setTimeout(() => sb.classList.add('transition-all', 'duration-300', 'ease-in-out'), 100);
                }
            </script>
            <div class="h-16 flex items-center justify-between px-4 border-b border-custom-sidebar">
                <a href="<?= base_url('dashboard') ?>" class="flex items-center justify-center w-full truncate px-2" title="<?= htmlspecialchars($sysAppName) ?>">
                    <?php if(!empty($sysBusinessLogo)): ?>
                        <img src="<?= (strpos($sysBusinessLogo, 'http') === 0 ? '' : base_url()) . htmlspecialchars($sysBusinessLogo) ?>" alt="<?= htmlspecialchars($sysAppName) ?>" class="max-h-10 max-w-full object-contain drop-shadow-md bg-white/10 rounded px-2 py-1 sidebar-text">
                        <i class="fas fa-cube text-custom-primary text-2xl hidden sidebar-icon"></i>
                    <?php else: ?>
                        <h1 class="text-xl font-bold tracking-wider truncate flex items-center justify-center w-full"><i class="fas fa-cube text-custom-primary md:mr-2 sidebar-icon-margin"></i> <span class="sidebar-text"><?= htmlspecialchars($sysAppName) ?></span></h1>
                    <?php endif; ?>
                </a>
                <button onclick="toggleSidebar()" class="md:hidden text-custom-sidebar-muted hover:text-custom-sidebar transition"><i class="fas fa-times text-xl"></i></button>
            </div>
            <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto custom-scrollbar">
                
                <!-- Principal -->
                <p class="px-4 text-[10px] font-bold tracking-wider text-custom-sidebar-title uppercase mt-2 mb-2 sidebar-text">Principal</p>
                <hr class="border-custom-sidebar mx-4 mt-2 mb-2 hidden sidebar-divider">
                <a href="<?= base_url('dashboard') ?>" class="flex items-center px-4 py-2.5 text-sm text-custom-sidebar-muted hover-custom-sidebar rounded-lg transition-colors group sidebar-link" title="Dashboard">
                    <i class="fas fa-chart-pie w-6 text-center text-custom-primary"></i> <span class="sidebar-text ml-2">Dashboard</span>
                </a>

                <!-- Módulos de Operación -->
                <p class="px-4 text-[10px] font-bold tracking-wider text-custom-sidebar-title uppercase mt-6 mb-2 sidebar-text">Operación</p>
                <hr class="border-custom-sidebar mx-4 mt-6 mb-2 hidden sidebar-divider">
                <?php
                if (is_dir(MODULES_PATH)) {
                    foreach (scandir(MODULES_PATH) as $dir) {
                        if ($dir === '.' || $dir === '..' || $dir === 'admin_core' || $dir === 'usuarios' || $dir === 'dashboard') continue;
                        $configPath = MODULES_PATH . '/' . $dir . '/config.json';
                        if (file_exists($configPath)) {
                            $config = json_decode(file_get_contents($configPath), true);
                            if (isset($config['active']) && $config['active']) {
                                // Validar dinámicamente si el usuario tiene permiso de ver este módulo
                                $permisoRequerido = strtolower($config['name']) . '.ver';
                                if (has_permission($permisoRequerido)) {
                                    $menuName = ucfirst($config['name']);
                                    $menuUrl = base_url($config['name']);
                                    $menuIcon = $config['icon'] ?? 'fa-folder';
                                    $menuIcon = str_replace([' text-white/50', ' text-gray-400'], '', $menuIcon);
                                    echo '<a href="' . $menuUrl . '" class="flex items-center px-4 py-2.5 text-sm text-custom-sidebar-muted hover-custom-sidebar rounded-lg transition-colors group sidebar-link" title="' . $menuName . '"><i class="fas ' . htmlspecialchars($menuIcon) . ' w-6 text-center transition-colors"></i> <span class="sidebar-text ml-2">' . $menuName . '</span></a>';
                                }
                            }
                        }
                    }
                }
                ?>

                <!-- Administración y Ajustes -->
                <?php if(has_permission('admin_core.ver') || has_permission('usuarios.ver')): ?>
                    <p class="px-4 text-[10px] font-bold tracking-wider text-custom-sidebar-title uppercase mt-6 mb-2 sidebar-text">Administración</p>
                    <hr class="border-custom-sidebar mx-4 mt-6 mb-2 hidden sidebar-divider">
                    <?php if(has_permission('admin_core.ver')): ?>
                        <a href="<?= base_url('admin_core') ?>" class="flex items-center px-4 py-2.5 text-sm text-custom-sidebar-muted hover-custom-sidebar rounded-lg transition-colors group sidebar-link" title="Panel Core"><i class="fas fa-server w-6 text-center transition-colors"></i> <span class="sidebar-text ml-2">Panel Core</span></a>
                    <?php endif; ?>
                    <?php if(has_permission('usuarios.ver')): ?>
                        <a href="<?= base_url('usuarios') ?>" class="flex items-center px-4 py-2.5 text-sm text-custom-sidebar-muted hover-custom-sidebar rounded-lg transition-colors group sidebar-link" title="Usuarios"><i class="fas fa-users w-6 text-center transition-colors"></i> <span class="sidebar-text ml-2">Usuarios</span></a>
                    <?php endif; ?>
                    <?php if(has_permission('admin_core.ver')): ?>
                        <a href="<?= base_url('admin_core/ajustes') ?>" class="flex items-center px-4 py-2.5 text-sm text-custom-sidebar-muted hover-custom-sidebar rounded-lg transition-colors group sidebar-link" title="Ajustes de Sistema"><i class="fas fa-cogs w-6 text-center transition-colors"></i> <span class="sidebar-text ml-2">Ajustes de Sistema</span></a>
                    <?php endif; ?>
                <?php endif; ?>
            </nav>
            <div class="p-4 border-t border-custom-sidebar sidebar-footer">
                <a href="<?= base_url('usuarios/perfil') ?>" class="flex items-center text-sm text-custom-sidebar-muted hover:text-custom-sidebar transition-colors mb-4 sidebar-link group" title="Mi Perfil">
                    <i class="fas fa-user-circle w-6 text-center text-lg transition-colors"></i> <span class="sidebar-text ml-2 truncate">Mi Perfil (<?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuario') ?>)</span>
                </a>
                <a href="<?= base_url('usuarios/logout') ?>" class="flex items-center text-sm text-red-400 hover:text-red-300 transition-colors sidebar-link group" title="Cerrar Sesión">
                    <i class="fas fa-sign-out-alt w-6 text-center text-lg group-hover:text-red-300 transition-colors"></i> <span class="sidebar-text ml-2">Cerrar Sesión</span>
                </a>
            </div>
        </aside>
        <main class="flex-1 flex flex-col overflow-hidden bg-custom-body text-custom-body">
            <header class="h-16 bg-custom-topbar shadow-sm flex items-center justify-between px-4 md:px-8 z-10">
                <div class="flex items-center min-w-0">
                    <button onclick="toggleSidebar()" class="text-custom-body opacity-70 hover:opacity-100 hover:text-custom-primary focus:outline-none mr-3 transition" title="Alternar menú">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h2 class="text-lg md:text-xl font-semibold text-custom-body truncate"><?= $titulo ?? 'Sistema' ?></h2>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Notificaciones -->
                    <div class="relative">
                        <button onclick="toggleNotificaciones()" class="text-custom-body opacity-70 hover:opacity-100 hover:text-custom-primary focus:outline-none relative transition-colors mt-1">
                            <i class="fas fa-bell text-xl"></i>
                            <span id="notif-badge" class="hidden absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full shadow border-2 border-white">0</span>
                        </button>
                        
                        <!-- Dropdown Notificaciones -->
                        <div id="notif-dropdown" class="hidden absolute right-0 mt-4 w-80 md:w-96 bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden z-50 transform origin-top-right transition-all">
                            <div class="p-3 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                                <span class="font-bold text-gray-800 text-sm">Notificaciones</span>
                                <button onclick="marcarTodasLeidas()" class="text-xs text-indigo-600 hover:underline font-medium">Marcar todas leídas</button>
                            </div>
                            <div id="notif-list" class="max-h-80 overflow-y-auto custom-scrollbar divide-y divide-gray-50"></div>
                            <a href="<?= base_url('notificaciones') ?>" class="block p-3 text-center text-xs font-bold text-gray-600 hover:bg-gray-50 transition border-t border-gray-100">Ver historial completo</a>
                        </div>
                    </div>
                </div>
            </header>
            <div class="flex-1 overflow-auto p-4 md:p-8">
                <?= $content ?>
            </div>
        </main>
        
        <!-- Contenedor Copiloto IA (Flotante o Acoplado) -->
        <div id="copilot-container" class="hidden fixed bottom-20 right-6 w-80 md:w-96 bg-white rounded-xl shadow-2xl border border-gray-200 flex-col z-50 transition-all duration-300 overflow-hidden h-[500px] max-h-[80vh]">
            <script>
                (function(){
                    const c = document.getElementById('copilot-container');
                    if (localStorage.getItem('copilotDocked') === 'true') {
                        c.classList.remove('fixed', 'bottom-20', 'right-6', 'rounded-xl', 'h-[500px]', 'max-h-[80vh]', 'shadow-2xl');
                        c.classList.add('relative', 'w-80', 'md:w-96', 'border-l', 'border-gray-200');
                    }
                    if (localStorage.getItem('copilotOpen') === 'true') {
                        c.classList.remove('hidden'); c.classList.add('flex');
                    }
                })();
            </script>
            <div class="p-3 bg-indigo-600 text-white flex justify-between items-center cursor-pointer shadow-md z-10" onclick="toggleCopilot()">
                <div class="flex items-center gap-2 font-bold text-sm">
                    <i class="fas fa-robot text-xl"></i> Copiloto IA
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="dockCopilot(event)" class="text-white/70 hover:text-white transition" title="Acoplar a la pantalla"><i id="dock-icon" class="fas fa-columns"></i></button>
                    <button type="button" class="text-white/70 hover:text-white transition" title="Cerrar"><i class="fas fa-times text-lg"></i></button>
                </div>
            </div>
            <div id="copilot-messages" class="flex-1 overflow-y-auto p-4 bg-gray-50 flex flex-col gap-3 custom-scrollbar">
                <!-- Mensajes JS -->
            </div>
            <div class="p-3 bg-white border-t border-gray-100 flex items-center gap-2 z-10">
                <textarea id="copilot-input" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 resize-none h-10 custom-scrollbar" placeholder="Pregunta algo a la IA..." onkeydown="if(event.key === 'Enter' && !event.shiftKey){ event.preventDefault(); enviarCopiloto(); }"></textarea>
                <button onclick="enviarCopiloto()" id="btn-copilot-send" class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center hover:bg-indigo-700 transition shadow flex-shrink-0"><i class="fas fa-paper-plane relative right-0.5"></i></button>
            </div>
        </div>

        <!-- Burbuja Flotante Botón -->
        <?php if(has_permission('ia.ver')): ?>
        <button id="copilot-bubble" onclick="toggleCopilot()" class="fixed bottom-6 right-6 w-14 h-14 bg-indigo-600 text-white rounded-full flex items-center justify-center shadow-xl hover:bg-indigo-700 transition hover:scale-110 z-40 group focus:outline-none">
            <i class="fas fa-robot text-2xl group-hover:animate-pulse"></i>
        </button>
        <script>
            if (localStorage.getItem('copilotOpen') === 'true') {
                document.getElementById('copilot-bubble').classList.add('hidden');
            }
        </script>
        <?php endif; ?>
    </div>

<style>
    /* Estilizar scrollbar para que el menú lateral se vea más limpio en PC */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }
</style>

<script>
    // --- TOGGLE MENÚ MÓVIL ---
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        
        if (window.innerWidth < 768) {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        } else {
            sidebar.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('sidebar-collapsed'));
        }
    }

    // --- VISOR DE IMÁGENES GLOBAL (MODAL) ---
    window.abrirVisor = function(url) {
        let modal = document.getElementById('visor-imagenes-global');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'visor-imagenes-global';
            modal.className = 'fixed inset-0 z-[9999] bg-black/90 flex items-center justify-center hidden opacity-0 transition-opacity duration-300';
            modal.innerHTML = `
                <button onclick="cerrarVisor()" class="absolute top-6 right-8 text-white hover:text-gray-300 text-5xl leading-none focus:outline-none">&times;</button>
                <img id="visor-img" src="" class="max-w-[95%] max-h-[95vh] object-contain rounded shadow-2xl transition-transform duration-300 scale-95">
            `;
            modal.addEventListener('click', (e) => { if(e.target === modal) cerrarVisor(); });
            document.body.appendChild(modal);

            window.cerrarVisor = function() {
                modal.classList.remove('opacity-100');
                modal.querySelector('img').classList.remove('scale-100');
                setTimeout(() => { modal.classList.add('hidden'); }, 300);
            };
        }
        const img = modal.querySelector('#visor-img');
        img.src = url;
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.add('opacity-100'); img.classList.remove('scale-95'); img.classList.add('scale-100'); }, 10);
    };

    // --- SISTEMA DE NOTIFICACIONES PUSH (UI) ---
    let notifDropdownOpen = false;

    function toggleNotificaciones() {
        const dropdown = document.getElementById('notif-dropdown');
        notifDropdownOpen = !notifDropdownOpen;
        if (notifDropdownOpen) {
            dropdown.classList.remove('hidden');
            cargarNotificaciones();
        } else {
            dropdown.classList.add('hidden');
        }
    }

    // Cerrar dropdown si se hace clic fuera de él
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('notif-dropdown');
        const btn = document.querySelector('button[onclick="toggleNotificaciones()"]');
        if (notifDropdownOpen && !dropdown.contains(e.target) && !btn.contains(e.target)) {
            dropdown.classList.add('hidden');
            notifDropdownOpen = false;
        }
    });

    function cargarNotificaciones() {
        fetch('<?= base_url('notificaciones/obtenerNoLeidas') ?>')
            .then(res => res.json())
            .then(data => { if (data.status === 'success') renderNotificaciones(data.notificaciones); })
            .catch(err => console.error("Error cargando notificaciones:", err));
    }

    function renderNotificaciones(notificaciones) {
        const list = document.getElementById('notif-list');
        const badge = document.getElementById('notif-badge');
        
        list.innerHTML = '';
        if (notificaciones.length === 0) {
            badge.classList.add('hidden');
            list.innerHTML = '<div class="p-8 text-center text-sm text-gray-400"><i class="fas fa-bell-slash text-3xl mb-3 opacity-30"></i><br>No tienes notificaciones nuevas.</div>';
            return;
        }
        
        badge.innerText = notificaciones.length > 9 ? '+9' : notificaciones.length;
        badge.classList.remove('hidden');
        
        notificaciones.forEach(n => {
            const enlaceUrl = n.enlace ? `<?= base_url() ?>${n.enlace}` : '#';
            list.innerHTML += `
                <div class="p-4 hover:bg-indigo-50/30 transition cursor-pointer relative group" onclick="marcarYRedirigir(${n.id}, '${enlaceUrl}')">
                    <div class="pr-6">
                        <h4 class="text-sm font-bold text-gray-800 leading-tight mb-1">${n.titulo}</h4>
                        <p class="text-xs text-gray-600 line-clamp-2 leading-relaxed">${n.mensaje.replace(/\\n/g, '<br>')}</p>
                        <span class="text-[10px] text-gray-400 font-medium block mt-2"><i class="far fa-clock mr-1"></i>${n.fecha_creacion}</span>
                    </div>
                    <button class="absolute top-4 right-4 text-gray-300 hover:text-indigo-500 opacity-0 group-hover:opacity-100 transition p-1" onclick="event.stopPropagation(); marcarLeida(${n.id})" title="Marcar como leída"><i class="fas fa-check-circle text-lg"></i></button>
                </div>
            `;
        });
    }

    function marcarYRedirigir(id, enlace) {
        marcarLeida(id, true, enlace);
    }

    function marcarLeida(id, redirigir = false, enlace = '#') {
        fetch('<?= base_url('notificaciones/marcarLeida') ?>', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({id: id}) })
        .then(() => { if (redirigir && enlace !== '#') window.location.href = enlace; else cargarNotificaciones(); });
    }

    function marcarTodasLeidas() {
        fetch('<?= base_url('notificaciones/marcarLeida') ?>', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({id: 0}) })
        .then(() => { cargarNotificaciones(); toggleNotificaciones(); });
    }

    // Carga inicial y Polling cada 30 segundos
    document.addEventListener('DOMContentLoaded', cargarNotificaciones);
    setInterval(cargarNotificaciones, 30000);

    // --- LOGICA DEL COPILOTO IA INTERNO ---
    let isCopilotDocked = localStorage.getItem('copilotDocked') === 'true';
    let isCopilotOpen = localStorage.getItem('copilotOpen') === 'true';
    let copilotHistoryLoaded = false;
    
    document.addEventListener('DOMContentLoaded', () => {
        if (isCopilotDocked) aplicarEstadoDock();
        if (isCopilotOpen) aplicarEstadoCopiloto();
    });

    // Configurar marked.js para renderizar los enlaces (sin abrir nueva pestaña)
    marked.use({
        renderer: {
            link(token) {
                // Compatibilidad con las versiones más recientes de Marked.js (v13+)
                if (typeof token === 'object') {
                    const title = token.title ? `title="${token.title}"` : '';
                    const text = token.tokens ? this.parser.parseInline(token.tokens) : token.text;
                    return `<a href="${token.href}" ${title}>${text}</a>`;
                }
                // Fallback para versiones antiguas
                return `<a href="${arguments[0]}" ${arguments[1] ? `title="${arguments[1]}"` : ''}>${arguments[2]}</a>`;
            }
        }
    });

    function toggleCopilot() {
        isCopilotOpen = !isCopilotOpen;
        localStorage.setItem('copilotOpen', isCopilotOpen);
        aplicarEstadoCopiloto();
    }

    function aplicarEstadoCopiloto() {
        const container = document.getElementById('copilot-container');
        const bubble = document.getElementById('copilot-bubble');
        
        if (isCopilotOpen) {
            container.classList.remove('hidden'); container.classList.add('flex');
            if(bubble) bubble.classList.add('hidden');
            if (!copilotHistoryLoaded) { cargarHistorialCopiloto(); copilotHistoryLoaded = true; }
        } else {
            container.classList.add('hidden'); container.classList.remove('flex');
            if(bubble) bubble.classList.remove('hidden');
        }
    }

    function dockCopilot(e) {
        if(e) e.stopPropagation();
        isCopilotDocked = !isCopilotDocked;
        localStorage.setItem('copilotDocked', isCopilotDocked);
        aplicarEstadoDock();
    }

    function aplicarEstadoDock() {
        const container = document.getElementById('copilot-container');
        const iconBtn = document.getElementById('dock-icon');

        if (isCopilotDocked) {
            // Modo acoplado: Forma parte del grid padre, le quitamos lo fixed
            container.classList.remove('fixed', 'bottom-20', 'right-6', 'rounded-xl', 'h-[500px]', 'max-h-[80vh]', 'shadow-2xl');
            container.classList.add('relative', 'w-80', 'md:w-96', 'border-l', 'border-gray-200');
            if(iconBtn) iconBtn.classList.replace('fa-columns', 'fa-clone');
        } else {
            // Modo flotante
            container.classList.remove('relative', 'border-l', 'border-gray-200');
            container.classList.add('fixed', 'bottom-20', 'right-6', 'rounded-xl', 'h-[500px]', 'max-h-[80vh]', 'shadow-2xl');
            if(iconBtn) iconBtn.classList.replace('fa-clone', 'fa-columns');
        }
    }

    async function cargarHistorialCopiloto() {
        const msgs = document.getElementById('copilot-messages');
        msgs.innerHTML = '<div class="text-center text-gray-400 my-4 text-xs"><i class="fas fa-spinner fa-spin mr-1"></i> Cargando memoria...</div>';
        try {
            const res = await fetch('<?= base_url('ia/historialInterno') ?>');
            const data = await res.json();
            msgs.innerHTML = '';
            if (data.historial && data.historial.length > 0) {
                data.historial.forEach(msg => appendCopilotMsg(msg.mensaje, msg.role === 'user'));
            } else {
                msgs.innerHTML = '<div class="text-center text-xs text-gray-400 my-4">¡Hola! Soy tu asistente IA. ¿En qué te ayudo hoy?</div>';
            }
            msgs.scrollTop = msgs.scrollHeight;
        } catch(e) { msgs.innerHTML = '<div class="text-center text-red-400 text-xs my-4">Error al cargar historial.</div>'; }
    }

    function appendCopilotMsg(text, isUser) {
        const msgs = document.getElementById('copilot-messages');
        
        let suggestions = [];
        if (!isUser) {
            // Extraer sugerencias ocultas del texto
            const regex = /\[SUGERENCIA\]:\s*(.*)/gi;
            let match;
            while ((match = regex.exec(text)) !== null) {
                if (match[1].trim() !== '') {
                    suggestions.push(match[1].trim().replace(/[*`_]/g, '')); // Quitar posibles negritas
                }
            }
            // Eliminar las sugerencias del texto visible
            text = text.replace(/\[SUGERENCIA\]:\s*(.*)/gi, '').trim();
        }

        const parsedText = marked.parse(text);
        
        const container = document.createElement('div');
        container.className = `flex flex-col mb-1 ${isUser ? 'self-end items-end ml-auto max-w-[85%]' : 'self-start items-start mr-auto max-w-[85%]'}`;
        
        const div = document.createElement('div');
        div.className = `p-3 rounded-xl text-sm shadow-sm markdown-body leading-snug w-full ${isUser ? 'bg-indigo-100 text-indigo-900 rounded-tr-sm' : 'bg-white border border-gray-100 text-gray-800 rounded-tl-sm'}`;
        div.innerHTML = parsedText;
        container.appendChild(div);

        // Renderizar los botones de acción (Sugerencias)
        if (suggestions.length > 0) {
            const sugContainer = document.createElement('div');
            sugContainer.className = 'mt-2 flex flex-wrap gap-1.5 w-full';
            suggestions.forEach(s => {
                const btn = document.createElement('button');
                btn.className = 'px-3 py-1.5 bg-white hover:bg-indigo-50 border border-indigo-100 text-indigo-600 text-[11px] rounded-xl transition text-left cursor-pointer shadow-sm hover:shadow flex items-center leading-tight';
                btn.innerHTML = `<i class="fas fa-sparkles mr-1.5 text-indigo-400"></i> <span>${s}</span>`;
                btn.onclick = () => { document.getElementById('copilot-input').value = s; enviarCopiloto(); };
                sugContainer.appendChild(btn);
            });
            container.appendChild(sugContainer);
        }

        msgs.appendChild(container);
        msgs.scrollTop = msgs.scrollHeight;
    }

    async function enviarCopiloto() {
        const input = document.getElementById('copilot-input');
        const text = input.value.trim();
        if(!text) return; input.value = ''; appendCopilotMsg(text, true);
        const typingId = 'typing-' + Date.now();
        document.getElementById('copilot-messages').insertAdjacentHTML('beforeend', `<div id="${typingId}" class="max-w-[85%] p-3 rounded-xl text-sm bg-white border border-gray-100 text-gray-400 self-start rounded-tl-sm shadow-sm"><i class="fas fa-ellipsis-h fa-fade"></i></div>`);
        document.getElementById('copilot-messages').scrollTop = document.getElementById('copilot-messages').scrollHeight;
        try { const res = await fetch('<?= base_url('ia/chatInterno') ?>', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({mensaje: text}) }); const data = await res.json(); document.getElementById(typingId).remove(); if (data.status === 'success') { appendCopilotMsg(data.respuesta, false); } else { let errorHtml = '❌ Error: ' + (data.error || 'Fallo interno.'); if(data.details) { errorHtml += '<br><pre class="text-xs mt-2 bg-red-50 text-red-800 p-2 rounded overflow-x-auto">' + JSON.stringify(data.details, null, 2) + '</pre>'; console.error("Detalles de Gemini:", data.details); } appendCopilotMsg(errorHtml, false); } } catch(e) { document.getElementById(typingId).remove(); appendCopilotMsg('❌ Error de conexión.', false); console.error(e); }
    }
</script>
</body>
</html>
