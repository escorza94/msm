<div class="max-w-7xl mx-auto mt-4">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-truck text-blue-500 mr-3"></i> Panel de Logística</h2>
            <p class="text-sm text-gray-500 mt-1">Selecciona la sección a la que deseas acceder</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Tarjeta Kanban -->
        <a href="<?= base_url('logistica/entregas') ?>" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md hover:border-blue-300 transition transform hover:-translate-y-1 block">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-2xl mr-4">
                    <i class="fas fa-boxes"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Tablero de Entregas</h3>
                    <p class="text-sm text-gray-500">Kanban de envíos pendientes, en ruta y entregados</p>
                </div>
            </div>
            <p class="text-gray-600 text-sm">Gestiona y actualiza el estado de las entregas de todas las ventas que requieren servicio a domicilio.</p>
        </a>

        <!-- Tarjeta Configuración -->
        <a href="<?= base_url('logistica/configuracion') ?>" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md hover:border-blue-300 transition transform hover:-translate-y-1 block">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-gray-100 text-gray-600 rounded-full flex items-center justify-center text-2xl mr-4">
                    <i class="fas fa-route"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Configuración de Fletes</h3>
                    <p class="text-sm text-gray-500">Tarifas, zonas y punto de origen</p>
                </div>
            </div>
            <p class="text-gray-600 text-sm">Administra los costos de envío fijos o dinámicos por distancia y configura la ubicación GPS de la sucursal.</p>
        </a>
    </div>
</div>