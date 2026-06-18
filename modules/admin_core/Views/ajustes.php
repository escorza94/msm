<div class="max-w-4xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-cogs text-gray-600 mr-3"></i> Ajustes del Sistema</h2>
            <p class="text-sm text-gray-500 mt-1">Configuración de Integraciones y API Keys</p>
        </div>
    </div>

    <?php if(isset($success)): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-xl flex items-center shadow-sm text-sm">
            <i class="fas fa-check-circle text-lg mr-3"></i> 
            <div><?= htmlspecialchars($success) ?></div>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-6">
        <form action="<?= base_url('admin_core/ajustes') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            
            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3"><i class="fas fa-store mr-2 text-amber-500"></i> Identidad del Negocio</h3>
            <div class="bg-amber-50 p-4 rounded-lg border border-amber-100 mb-4">
                <label class="block text-sm font-bold text-amber-800 mb-1">Nombre del Negocio</label>
                <input type="text" name="app_name" value="<?= htmlspecialchars($config['APP_NAME'] ?? 'Mueblería San Martín') ?>" class="w-full border border-amber-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-amber-500 outline-none text-sm mb-3 shadow-sm" placeholder="Ej. Mueblería San Martín">
                
                <label class="block text-sm font-bold text-amber-800 mb-1">Logo del Negocio (Opcional)</label>
                <p class="text-xs text-amber-600 mb-3">Sube tu logotipo (JPG, PNG, SVG). Se utilizará para estampar tu marca en los PDFs de Tickets y Cotizaciones.</p>
                
                <div class="flex items-center gap-4 mb-3">
                    <?php if(!empty($config['BUSINESS_LOGO'])): ?>
                        <div class="w-16 h-16 rounded border border-amber-200 bg-white flex items-center justify-center overflow-hidden shrink-0 shadow-sm">
                            <img src="<?= (strpos($config['BUSINESS_LOGO'], 'http') === 0 ? '' : base_url()) . htmlspecialchars($config['BUSINESS_LOGO']) ?>" class="max-w-full max-h-full object-contain">
                        </div>
                    <?php endif; ?>
                    <div class="flex-1">
                        <input type="file" name="logo_file" accept=".jpg,.jpeg,.png,.webp,.svg" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200 outline-none transition cursor-pointer">
                    </div>
                </div>

                <div class="flex items-center my-3 opacity-60">
                    <div class="flex-1 border-t border-amber-300"></div>
                    <span class="px-3 text-[10px] text-amber-700 font-bold uppercase tracking-wider">O usar URL externa</span>
                    <div class="flex-1 border-t border-amber-300"></div>
                </div>

                <input type="text" name="business_logo" value="<?= htmlspecialchars($config['BUSINESS_LOGO'] ?? '') ?>" class="w-full border border-amber-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-amber-500 outline-none text-sm shadow-sm" placeholder="URL externa o ruta interna (ej. storage/assets/logo.png)">
            </div>

            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3 pt-2"><i class="fas fa-palette mr-2 text-pink-500"></i> Apariencia y Tema</h3>
            <div class="bg-pink-50 p-4 rounded-lg border border-pink-100 mb-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-bold text-pink-800 mb-1">Color del Menú Lateral</label>
                    <div class="flex items-center gap-3 mt-2">
                        <input type="color" name="theme_sidebar_bg" value="<?= htmlspecialchars($config['THEME_SIDEBAR_BG'] ?? '#111827') ?>" class="h-10 w-16 cursor-pointer rounded border border-pink-200 p-0 shadow-sm outline-none bg-white">
                        <span class="text-xs text-pink-600">Fondo oscuro<br>(Predeterminado: #111827)</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-pink-800 mb-1">Texto del Menú Lateral</label>
                    <div class="flex items-center gap-3 mt-2">
                        <input type="color" name="theme_sidebar_text" value="<?= htmlspecialchars($config['THEME_SIDEBAR_TEXT'] ?? '#ffffff') ?>" class="h-10 w-16 cursor-pointer rounded border border-pink-200 p-0 shadow-sm outline-none bg-white">
                        <span class="text-xs text-pink-600">Color de enlaces<br>(Predeterminado: #ffffff)</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-pink-800 mb-1">Color Primario (Acentos)</label>
                    <div class="flex items-center gap-3 mt-2">
                        <input type="color" name="theme_primary" value="<?= htmlspecialchars($config['THEME_PRIMARY'] ?? '#4f46e5') ?>" class="h-10 w-16 cursor-pointer rounded border border-pink-200 p-0 shadow-sm outline-none bg-white">
                        <span class="text-xs text-pink-600">Iconos y botones<br>(Predeterminado: #4f46e5)</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-pink-800 mb-1">Color de Texto Principal</label>
                    <div class="flex items-center gap-3 mt-2">
                        <input type="color" name="theme_text_color" value="<?= htmlspecialchars($config['THEME_TEXT_COLOR'] ?? '#374151') ?>" class="h-10 w-16 cursor-pointer rounded border border-pink-200 p-0 shadow-sm outline-none bg-white">
                        <span class="text-xs text-pink-600">Títulos y párrafos<br>(Predeterminado: #374151)</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-pink-800 mb-1">Fondo de la Barra Superior</label>
                    <div class="flex items-center gap-3 mt-2">
                        <input type="color" name="theme_topbar_bg" value="<?= htmlspecialchars($config['THEME_TOPBAR_BG'] ?? '#ffffff') ?>" class="h-10 w-16 cursor-pointer rounded border border-pink-200 p-0 shadow-sm outline-none bg-white">
                        <span class="text-xs text-pink-600">Header principal<br>(Predeterminado: #ffffff)</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-pink-800 mb-1">Fondo del Sistema (Body)</label>
                    <div class="flex items-center gap-3 mt-2">
                        <input type="color" name="theme_body_bg" value="<?= htmlspecialchars($config['THEME_BODY_BG'] ?? '#f3f4f6') ?>" class="h-10 w-16 cursor-pointer rounded border border-pink-200 p-0 shadow-sm outline-none bg-white">
                        <span class="text-xs text-pink-600">Fondo general<br>(Predeterminado: #f3f4f6)</span>
                    </div>
                </div>
            </div>

            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3"><i class="fas fa-map-marked-alt mr-2 text-blue-500"></i> Integración con Google Maps</h3>
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 mb-4">
                <label class="block text-sm font-bold text-blue-800 mb-1">Google Maps API Key</label>
                <p class="text-xs text-blue-600 mb-3">Se utiliza para autocompletar direcciones y obtener coordenadas GPS en el módulo de clientes y logística.</p>
                <input type="text" name="google_maps_api_key" value="<?= htmlspecialchars($config['GOOGLE_MAPS_API_KEY'] ?? '') ?>" class="w-full border border-blue-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none text-sm font-mono shadow-sm" placeholder="AIzaSyAxxxxxxxxxxxxxxxxxxxxxxx">
            </div>

            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3 pt-4"><i class="fas fa-brain mr-2 text-purple-500"></i> Inteligencia Artificial (Google Gemini)</h3>
            <div class="bg-purple-50 p-4 rounded-lg border border-purple-100">
                <label class="block text-sm font-bold text-purple-800 mb-1">Gemini AI API Key</label>
                <p class="text-xs text-purple-600 mb-3">Preparación para automatización de respuestas en WhatsApp, análisis de inventario o generación de descripciones.</p>
                <input type="text" name="gemini_api_key" value="<?= htmlspecialchars($config['GEMINI_API_KEY'] ?? '') ?>" class="w-full border border-purple-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 outline-none text-sm font-mono shadow-sm mb-4" placeholder="AIzaSyByyyyyyyyyyyyyyyyyyyyyyy">
                
                <label class="block text-sm font-bold text-purple-800 mb-1">Modelo de Gemini a utilizar</label>
                <input type="text" name="gemini_model" value="<?= htmlspecialchars($config['GEMINI_MODEL'] ?? 'gemini-1.5-flash') ?>" class="w-full border border-purple-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 outline-none text-sm font-mono shadow-sm" placeholder="Ej. gemini-1.5-flash o gemini-pro">
            </div>

            <div class="pt-4 mt-6 border-t border-gray-100 flex justify-end gap-3">
                <button type="submit" class="px-5 py-2.5 bg-gray-800 text-white rounded-lg hover:bg-gray-900 font-bold transition shadow-md flex items-center text-sm">
                    <i class="fas fa-save mr-2"></i> Guardar Ajustes
                </button>
            </div>
            
        </form>
    </div>
</div>