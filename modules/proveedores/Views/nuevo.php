<div class="max-w-3xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-building text-amber-500 mr-2"></i> Nuevo Proveedor</h2>
            <p class="text-sm text-gray-500 mt-1">Registra los datos de una nueva empresa o contacto de abastecimiento</p>
        </div>
        <a href="<?= base_url('proveedores') ?>" class="text-sm bg-white border border-gray-200 text-gray-600 px-3 py-1.5 rounded-lg font-medium hover:bg-gray-50 transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
    </div>

    <?php if(isset($error)): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-xl flex items-center shadow-sm text-sm">
            <i class="fas fa-exclamation-triangle text-lg mr-3"></i> 
            <div><?= htmlspecialchars($error) ?></div>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-6">
        <form action="<?= base_url('proveedores/nuevo') ?>" method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Empresa / Nombre de Contacto <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none text-sm transition shadow-sm" placeholder="Ej. Muebles Finos S.A. de C.V.">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="text" name="telefono" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none text-sm transition shadow-sm" placeholder="Ej. 555-123-4567">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                    <input type="email" name="email" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none text-sm transition shadow-sm" placeholder="contacto@empresa.com">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección Completa</label>
                    <textarea name="direccion" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none text-sm transition shadow-sm resize-none" placeholder="Calle, Número, Colonia, Ciudad, Estado, CP..."></textarea>
                </div>
            </div>

            <div class="pt-4 mt-6 border-t border-gray-100 flex justify-end gap-3">
                <a href="<?= base_url('proveedores') ?>" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition text-sm">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 bg-amber-500 text-white rounded-lg hover:bg-amber-600 font-bold transition shadow-md flex items-center text-sm"><i class="fas fa-save mr-2"></i> Guardar Proveedor</button>
            </div>
        </form>
    </div>
</div>