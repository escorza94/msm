<div class="max-w-7xl mx-auto mt-4">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-globe text-indigo-500 mr-3"></i> Panel de Tienda Online</h2>
            <p class="text-sm text-gray-500 mt-1">Gestiona tu página web, colecciones de productos y diseño visual.</p>
        </div>
        <a href="<?= base_url() ?>" target="_blank" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition text-sm flex items-center shadow-sm">
            <i class="fas fa-external-link-alt mr-2"></i> Ver Página Web
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Gestor de Páginas -->
        <a href="<?= base_url('pagina_web/paginas') ?>" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md hover:border-indigo-300 transition transform hover:-translate-y-1 block">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-2xl mr-4">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Páginas</h3>
                    <p class="text-sm text-gray-500">Gestor de contenido</p>
                </div>
            </div>
            <p class="text-gray-600 text-sm">Crea nuevas páginas (Ej. "Nosotros", "Políticas") para tu tienda online.</p>
        </a>

        <!-- Colecciones -->
        <a href="<?= base_url('pagina_web/colecciones') ?>" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md hover:border-indigo-300 transition transform hover:-translate-y-1 block">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-2xl mr-4">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Colecciones</h3>
                    <p class="text-sm text-gray-500">Agrupación de productos</p>
                </div>
            </div>
            <p class="text-gray-600 text-sm">Crea grupos de productos (ej. "Novedades", "Ofertas") para mostrarlos dinámicamente en tu página de inicio.</p>
        </a>

        <!-- Constructor Visual -->
        <a href="<?= base_url('pagina_web/constructor') ?>" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md hover:border-indigo-300 transition transform hover:-translate-y-1 block">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-2xl mr-4">
                    <i class="fas fa-cubes"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Constructor Visual</h3>
                    <p class="text-sm text-gray-500">Diseño de la página principal</p>
                </div>
            </div>
            <p class="text-gray-600 text-sm">Añade carruseles de banners, cuadrículas de productos y tarjetas de información arrastrando y soltando bloques.</p>
        </a>

        <!-- Configuración Global -->
        <a href="<?= base_url('pagina_web/configuracion') ?>" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md hover:border-amber-300 transition transform hover:-translate-y-1 block">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center text-2xl mr-4">
                    <i class="fas fa-paint-roller"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Ajustes Globales</h3>
                    <p class="text-sm text-gray-500">Textos SEO y Redes</p>
                </div>
            </div>
            <p class="text-gray-600 text-sm">Cambia tu número de contacto para el botón flotante de WhatsApp, modifica los enlaces de redes sociales y ajusta el título en Google.</p>
        </a>
    </div>
</div>