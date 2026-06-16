<div class="max-w-3xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-cog text-indigo-500 mr-2"></i> Ajustes de Usuarios</h2>
            <p class="text-sm text-gray-500 mt-1">Configura las preferencias globales del módulo de usuarios</p>
        </div>
        <a href="<?= base_url('usuarios') ?>" class="text-sm bg-white border border-gray-200 text-gray-600 px-3 py-1.5 rounded-lg font-medium hover:bg-gray-50 transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-8 text-center">
        <div class="w-16 h-16 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">
            <i class="fas fa-tools"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-700 mb-2">Página en Construcción</h3>
        <p class="text-gray-500 text-sm max-w-md mx-auto mb-6">
            Próximamente podrás configurar aquí los permisos, roles predeterminados, y preferencias de registro del sistema.
        </p>
        
        <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4 max-w-md mx-auto text-left flex gap-3">
            <i class="fas fa-lightbulb text-indigo-500 text-xl mt-0.5"></i>
            <div>
                <h4 class="font-bold text-indigo-800 text-sm">¿Qué sigue?</h4>
                <p class="text-xs text-indigo-600 mt-1">Podemos implementar la lectura y escritura dinámica del archivo <code>config.php</code> desde esta vista.</p>
            </div>
        </div>
    </div>
</div>