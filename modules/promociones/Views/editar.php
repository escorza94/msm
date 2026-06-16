<div class="max-w-3xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-edit text-blue-500 mr-2"></i> Editar Promoción</h2>
            <p class="text-sm text-gray-500 mt-1">Actualiza los datos del descuento seleccionado</p>
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
        <form action="<?= base_url('promociones/editar') ?>" method="POST" class="space-y-6">
            <input type="hidden" name="id" value="<?= $promocion['id'] ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Comercial <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($promocion['nombre']) ?>" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none text-sm transition">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Descuento</label>
                    <select name="tipo" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none text-sm bg-white">
                        <option value="porcentaje" <?= $promocion['tipo'] == 'porcentaje' ? 'selected' : '' ?>>Porcentaje (%)</option>
                        <option value="monto_fijo" <?= $promocion['tipo'] == 'monto_fijo' ? 'selected' : '' ?>>Monto Fijo ($)</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Valor del Descuento <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0" name="valor" value="<?= floatval($promocion['valor']) ?>" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Código de Cupón <span class="text-gray-400 font-normal text-xs">(Opcional)</span></label>
                    <input type="text" name="codigo_cupon" value="<?= htmlspecialchars($promocion['codigo_cupon']) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none text-sm uppercase font-mono">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Monto Mínimo de Compra ($)</label>
                    <input type="number" step="0.01" min="0" name="monto_minimo" value="<?= floatval($promocion['monto_minimo']) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad Mínima de Productos</label>
                    <input type="number" min="0" name="cantidad_minima" value="<?= intval($promocion['cantidad_minima']) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Productos Requeridos (Paquetes) <span class="text-gray-400 font-normal text-xs">(Opcional - Ctrl+Clic para elegir varios)</span></label>
                    <select multiple name="productos_requeridos[]" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none text-sm bg-white h-32">
                        <?php $req = !empty($promocion['productos_requeridos']) ? json_decode($promocion['productos_requeridos'], true) : []; ?>
                        <?php if(isset($productos)): foreach($productos as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= in_array($p['id'], $req) ? 'selected' : '' ?>>[<?= htmlspecialchars($p['sku']) ?>] <?= htmlspecialchars($p['nombre']) ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Si seleccionas productos aquí, el cupón SOLO será válido si el cliente lleva <b>todos</b> los productos marcados.</p>
                </div>

                <div><label class="block text-sm font-medium text-gray-700 mb-1">Válido Desde</label><input type="date" name="fecha_inicio" value="<?= $promocion['fecha_inicio'] ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Válido Hasta</label><input type="date" name="fecha_fin" value="<?= $promocion['fecha_fin'] ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 outline-none text-sm"></div>
            </div>

            <div class="pt-4 mt-6 border-t border-gray-100 flex justify-end gap-3">
                <a href="<?= base_url('promociones') ?>" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition text-sm">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold transition shadow-md text-sm">Actualizar Cambios</button>
            </div>
        </form>
    </div>
</div>
