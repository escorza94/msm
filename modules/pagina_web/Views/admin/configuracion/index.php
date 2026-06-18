<div class="max-w-4xl mx-auto mt-6 mb-10">
    <?php if(isset($_GET['success'])): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-xl flex items-center shadow-sm text-sm">
            <i class="fas fa-check-circle text-lg mr-3"></i> <div><?= htmlspecialchars($_GET['success']) ?></div>
        </div>
    <?php endif; ?>
    <?php if(isset($_GET['error'])): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-xl flex items-center shadow-sm text-sm">
            <i class="fas fa-exclamation-circle text-lg mr-3"></i> <div><?= htmlspecialchars($_GET['error']) ?></div>
        </div>
    <?php endif; ?>

    <form id="config-form" action="<?= base_url('pagina_web/configuracion/guardar') ?>" method="POST" class="space-y-6">
        <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-paint-roller text-amber-500 mr-3"></i> Configuración Global</h2>
                <p class="text-sm text-gray-500 mt-1">Ajusta los textos, redes sociales y SEO de tu Tienda Online.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?= base_url('pagina_web') ?>" class="text-sm bg-white border border-gray-200 text-gray-600 px-3 py-1.5 rounded-lg font-medium hover:bg-gray-50 transition flex items-center"><i class="fas fa-arrow-left mr-2"></i> Volver</a>
                <button type="submit" class="px-4 py-1.5 bg-amber-500 text-white rounded-lg shadow-sm font-bold hover:bg-amber-600 transition flex items-center text-sm"><i class="fas fa-save mr-2"></i> Guardar Cambios</button>
            </div>
        </div>
        <!-- Identidad y Diseño -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2"><i class="fas fa-palette text-indigo-500 mr-2"></i> Identidad y Diseño</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nombre de la Empresa</label>
                    <input type="text" name="nombre_empresa" value="<?= htmlspecialchars($config['nombre_empresa'] ?? 'Mueblería San Martín') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-indigo-500 outline-none" placeholder="Ej. Mueblería San Martín">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1" title="Botones, enlaces y resaltados">Color Primario</label>
                    <input type="color" name="color_primario" value="<?= htmlspecialchars($config['color_primario'] ?? '#4f46e5') ?>" class="h-10 w-full border border-gray-300 rounded-lg px-2 py-1 cursor-pointer">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1" title="Pie de página y acentos">Color Secundario</label>
                    <input type="color" name="color_secundario" value="<?= htmlspecialchars($config['color_secundario'] ?? '#1f2937') ?>" class="h-10 w-full border border-gray-300 rounded-lg px-2 py-1 cursor-pointer">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1" title="Fondo general de la página web">Fondo Principal</label>
                    <input type="color" name="color_fondo" value="<?= htmlspecialchars($config['color_fondo'] ?? '#f3f4f6') ?>" class="h-10 w-full border border-gray-300 rounded-lg px-2 py-1 cursor-pointer">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1" title="Fondo de la barra de navegación y tarjetas">Fondo Secciones</label>
                    <input type="color" name="color_secciones" value="<?= htmlspecialchars($config['color_secciones'] ?? '#ffffff') ?>" class="h-10 w-full border border-gray-300 rounded-lg px-2 py-1 cursor-pointer">
                </div>
            </div>
        </div>

        <!-- Menú de Navegación -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2"><i class="fas fa-bars text-green-500 mr-2"></i> Menú de Navegación (Header)</h3>
            <p class="text-xs text-gray-500 mb-4">Agrega los enlaces que aparecerán en la barra superior de tu tienda.</p>
            
            <div id="menu-container" class="space-y-3">
                <?php 
                $menu_enlaces = json_decode($config['menu_enlaces'] ?? '[]', true) ?: [
                    ['titulo' => 'Productos', 'enlace' => '#productos'],
                    ['titulo' => 'Ubicación', 'enlace' => '#ubicacion'],
                    ['titulo' => 'Contacto', 'enlace' => '#contacto']
                ];
                foreach($menu_enlaces as $link): 
                ?>
                <div class="flex items-center gap-2 menu-item">
                    <div class="cursor-move text-gray-300 hover:text-gray-500 px-2"><i class="fas fa-grip-vertical"></i></div>
                    <input type="text" name="menu_titulo[]" value="<?= htmlspecialchars($link['titulo']) ?>" placeholder="Título del enlace" class="w-1/3 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 outline-none">
                    <input type="text" name="menu_enlace[]" value="<?= htmlspecialchars($link['enlace']) ?>" placeholder="URL o #seccion" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 outline-none font-mono">
                    <button type="button" onclick="this.closest('.menu-item').remove()" class="text-red-500 hover:text-red-700 px-2"><i class="fas fa-times"></i></button>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="mt-4 flex gap-2">
                <button type="button" onclick="agregarEnlaceMenu()" class="px-4 py-2 bg-gray-50 text-gray-600 rounded text-sm font-bold border border-gray-200 hover:bg-gray-100 transition"><i class="fas fa-plus mr-1"></i> Añadir Enlace Libre</button>
                
                <div class="relative">
                    <select id="select-pagina-menu" class="border border-gray-300 rounded-l-lg px-3 py-2 text-sm outline-none appearance-none" onchange="if(this.value) { agregarEnlacePagina(this.options[this.selectedIndex].text, this.value); this.value=''; }">
                        <option value="">+ Añadir Página Creada...</option>
                        <?php foreach($paginas as $p): ?>
                            <option value="<?= $p['slug'] === 'inicio' ? base_url() : base_url('pagina?slug=' . $p['slug']) ?>"><?= htmlspecialchars($p['titulo']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700"><i class="fas fa-chevron-down text-[10px]"></i></div>
                </div>
            </div>
            
            <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    new Sortable(document.getElementById('menu-container'), { animation: 150, handle: '.cursor-move' });
                });
                function agregarEnlaceMenu(titulo = '', enlace = '') {
                    const html = `<div class="flex items-center gap-2 menu-item"><div class="cursor-move text-gray-300 hover:text-gray-500 px-2"><i class="fas fa-grip-vertical"></i></div><input type="text" name="menu_titulo[]" value="${titulo}" placeholder="Título del enlace" class="w-1/3 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 outline-none"><input type="text" name="menu_enlace[]" value="${enlace}" placeholder="URL o #seccion" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 outline-none font-mono"><button type="button" onclick="this.closest('.menu-item').remove()" class="text-red-500 hover:text-red-700 px-2"><i class="fas fa-times"></i></button></div>`;
                    document.getElementById('menu-container').insertAdjacentHTML('beforeend', html);
                }
                function agregarEnlacePagina(titulo, url) {
                    agregarEnlaceMenu(titulo, url);
                }
            </script>
        </div>

        <!-- SEO y Meta -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2"><i class="fab fa-google text-blue-500 mr-2"></i> Posicionamiento en Buscadores (SEO)</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Título de la Página Principal</label>
                    <input type="text" name="seo_titulo" value="<?= htmlspecialchars($config['seo_titulo'] ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-indigo-500 outline-none" placeholder="Ej. Mueblería San Martín | Calidad">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Descripción Corta (Meta Description)</label>
                    <textarea name="seo_descripcion" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-indigo-500 outline-none resize-none" placeholder="Aparecerá debajo del link en Google..."><?= htmlspecialchars($config['seo_descripcion'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Redes y Contacto -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2"><i class="fas fa-share-alt text-pink-500 mr-2"></i> Redes Sociales y Contacto</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1"><i class="fab fa-whatsapp text-green-500 mr-1"></i> WhatsApp de Ventas</label>
                    <input type="text" name="whatsapp_numero" value="<?= htmlspecialchars($config['whatsapp_numero'] ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-indigo-500 outline-none" placeholder="Ej. 5215555555555">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1"><i class="fab fa-facebook text-blue-600 mr-1"></i> Enlace de Facebook</label>
                    <input type="url" name="facebook_url" value="<?= htmlspecialchars($config['facebook_url'] ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-indigo-500 outline-none" placeholder="https://facebook.com/...">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1"><i class="fab fa-instagram text-pink-600 mr-1"></i> Enlace de Instagram</label>
                    <input type="url" name="instagram_url" value="<?= htmlspecialchars($config['instagram_url'] ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-indigo-500 outline-none" placeholder="https://instagram.com/...">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1"><i class="fab fa-tiktok text-black mr-1"></i> Enlace de TikTok</label>
                    <input type="url" name="tiktok_url" value="<?= htmlspecialchars($config['tiktok_url'] ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-indigo-500 outline-none" placeholder="https://tiktok.com/...">
                </div>
            </div>
        </div>

        <!-- Pie de página -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2"><i class="fas fa-shoe-prints text-gray-500 mr-2"></i> Pie de Página (Footer)</h3>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Texto de bienvenida o eslogan</label>
                <textarea name="footer_texto" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-indigo-500 outline-none resize-none"><?= htmlspecialchars($config['footer_texto'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2.5 bg-amber-500 text-white rounded-lg shadow font-bold hover:bg-amber-600 transition flex items-center"><i class="fas fa-save mr-2"></i> Guardar Ajustes</button>
        </div>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('config-form');
            let isDirty = false;

            form.addEventListener('input', () => {
                isDirty = true;
            });

            form.addEventListener('submit', () => {
                isDirty = false;
            });

            // Interceptar clics en enlaces (como el botón Volver o el menú lateral)
            document.body.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (!link) return;
                
                // Ignorar anclas, javascript void y el mismo lugar
                const href = link.getAttribute('href');
                if (!href || href === '#' || href.startsWith('javascript:')) return;

                if (isDirty) {
                    e.preventDefault(); // Detener la navegación
                    const targetUrl = link.href;
                    
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: '¿Cambios sin guardar?',
                            text: "Has modificado la configuración. Si sales ahora, perderás los cambios.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Sí, salir y descartar',
                            cancelButtonText: 'No, seguir editando'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                isDirty = false;
                                window.location.href = targetUrl;
                            }
                        });
                    }
                }
            });

            // Alerta nativa obligatoria del navegador (solo para recarga (F5) o cerrar pestaña)
            window.addEventListener('beforeunload', (event) => {
                if (isDirty) {
                    event.preventDefault();
                    event.returnValue = '';
                }
            });
        });
    </script>
</div>