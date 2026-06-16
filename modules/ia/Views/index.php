<div class="max-w-7xl mx-auto mt-4">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-brain text-indigo-500 mr-3"></i> Panel de Inteligencia Artificial</h2>
            <p class="text-sm text-gray-500 mt-1">Configuración y capacidades de los modelos de lenguaje (Gemini).</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Tarjeta Herramientas -->
        <a href="<?= base_url('ia/herramientas') ?>" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md hover:border-indigo-300 transition transform hover:-translate-y-1 block">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-2xl mr-4">
                    <i class="fas fa-toolbox"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Directorio de Herramientas</h3>
                    <p class="text-sm text-gray-500">Capacidades del Agente y Copiloto</p>
                </div>
            </div>
            <p class="text-gray-600 text-sm">Visualiza todas las funciones (Function Calling) que los módulos le otorgan a la inteligencia artificial para realizar acciones en el CRM.</p>
        </a>

        <!-- Tarjeta Prompts -->
        <a href="<?= base_url('ia/prompts') ?>" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md hover:border-indigo-300 transition transform hover:-translate-y-1 block">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center text-2xl mr-4">
                    <i class="fas fa-comment-dots"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Ajustes de Prompts</h3>
                    <p class="text-sm text-gray-500">Personalidad y Reglas</p>
                </div>
            </div>
            <p class="text-gray-600 text-sm">Edita las instrucciones de comportamiento, la identidad del bot y las reglas estrictas que debe seguir al hablar con los clientes.</p>
        </a>
    </div>
</div>
