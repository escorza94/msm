<div class="max-w-3xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-tag text-red-500 mr-2"></i> Nueva Promoción</h2>
            <p class="text-sm text-gray-500 mt-1">Crea un nuevo descuento o cupón aplicable</p>
        </div>
        <a href="<?= base_url('promociones') ?>" class="text-sm bg-white border border-gray-200 text-gray-600 px-3 py-1.5 rounded-lg font-medium hover:bg-gray-50 transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
    </div>

    <?php if(isset($_GET['error'])): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-xl flex items-center shadow-sm text-sm">
            <i class="fas fa-exclamation-triangle text-lg mr-3"></i> 
            <div><?= htmlspecialchars($_GET['error']) ?></div>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-6">
        <form action="<?= base_url('promociones/nuevo') ?>" method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Comercial <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-red-500 outline-none text-sm transition" placeholder="Ej. Descuento Buen Fin 2024">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Descuento</label>
                    <select name="tipo" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-red-500 outline-none text-sm bg-white">
                        <option value="porcentaje">Porcentaje (%)</option>
                        <option value="monto_fijo">Monto Fijo ($)</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Valor del Descuento <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0" name="valor" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-red-500 outline-none text-sm" placeholder="Ej. 10 o 500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Código de Cupón <span class="text-gray-400 font-normal text-xs">(Opcional)</span></label>
                    <input type="text" name="codigo_cupon" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-red-500 outline-none text-sm uppercase font-mono" placeholder="Ej. BUENFIN10">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Monto Mínimo de Compra ($) <span class="text-gray-400 font-normal text-xs">(Opcional)</span></label>
                    <input type="number" step="0.01" min="0" name="monto_minimo" value="0" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-red-500 outline-none text-sm">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Productos Requeridos (Paquetes) <span class="text-gray-400 font-normal text-xs">(Opcional - Ctrl+Clic para elegir varios)</span></label>
                    <select multiple name="productos_requeridos[]" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-red-500 outline-none text-sm bg-white h-32">
                        <?php if(isset($productos)): foreach($productos as $p): ?>
                            <option value="<?= $p['id'] ?>">[<?= htmlspecialchars($p['sku']) ?>] <?= htmlspecialchars($p['nombre']) ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Si seleccionas productos aquí, el cupón SOLO será válido si el cliente lleva <b>todos</b> los productos marcados.</p>
                </div>

                <div><label class="block text-sm font-medium text-gray-700 mb-1">Válido Desde <span class="text-gray-400 text-xs">(Opcional)</span></label><input type="date" name="fecha_inicio" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Válido Hasta <span class="text-gray-400 text-xs">(Opcional)</span></label><input type="date" name="fecha_fin" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none text-sm"></div>
            </div>

            <div class="pt-4 mt-6 border-t border-gray-100 flex justify-end gap-3">
                <a href="<?= base_url('promociones') ?>" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition text-sm">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 bg-red-500 text-white rounded-lg hover:bg-red-600 font-bold transition shadow-md text-sm">Guardar Promoción</button>
            </div>
        </form>
    </div>
</div>