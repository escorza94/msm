<div class="max-w-4xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-tag text-red-500 mr-3"></i> Detalle de Promoción</h2>
            <p class="text-sm text-gray-500 mt-1">Revisa las reglas y configuración de este descuento</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('promociones') ?>" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 font-medium transition text-sm flex items-center shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
            <a href="<?= base_url('promociones/editar?id=' . $promocion['id']) ?>" class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 font-medium transition text-sm flex items-center shadow-sm">
                <i class="fas fa-edit mr-2"></i> Editar
            </a>
        </div>
    </div>

    <!-- Datos Generales -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800"><i class="fas fa-info-circle text-blue-400 mr-2"></i> Información General</h3>
            <span class="px-3 py-1 rounded text-[10px] font-bold uppercase <?= $promocion['estado'] === 'activo' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                <?= $promocion['estado'] ?>
            </span>
        </div>
        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
            <div>
                <p class="text-gray-400 text-[10px] uppercase tracking-wider font-bold mb-1">Nombre Comercial</p>
                <p class="font-bold text-gray-800 text-base"><?= htmlspecialchars($promocion['nombre']) ?></p>
            </div>
            <div>
                <p class="text-gray-400 text-[10px] uppercase tracking-wider font-bold mb-1">Código de Cupón</p>
                <?php if($promocion['codigo_cupon']): ?>
                    <span class="inline-block mt-1 px-3 py-1 bg-gray-100 text-gray-700 font-mono text-sm rounded border border-gray-200 font-bold">
                        <i class="fas fa-barcode text-gray-400 mr-2"></i><?= htmlspecialchars($promocion['codigo_cupon']) ?>
                    </span>
                <?php else: ?>
                    <span class="inline-block mt-1 px-3 py-1 bg-green-50 text-green-700 font-bold text-xs rounded border border-green-100 uppercase">
                        <i class="fas fa-magic mr-1"></i> Promo Automática
                    </span>
                <?php endif; ?>
            </div>
            <div>
                <p class="text-gray-400 text-[10px] uppercase tracking-wider font-bold mb-1">Tipo de Descuento</p>
                <p class="font-medium text-gray-800"><?= $promocion['tipo'] === 'porcentaje' ? 'Porcentaje (%)' : 'Monto Fijo ($)' ?></p>
            </div>
            <div>
                <p class="text-gray-400 text-[10px] uppercase tracking-wider font-bold mb-1">Valor del Descuento</p>
                <p class="font-black text-2xl <?= $promocion['tipo'] === 'porcentaje' ? 'text-blue-600' : 'text-green-600' ?>">
                    <?= $promocion['tipo'] === 'porcentaje' ? round($promocion['valor']) . '%' : '-$' . number_format($promocion['valor'], 2) ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Reglas y Condiciones -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800"><i class="fas fa-clipboard-list text-amber-500 mr-2"></i> Reglas de Aplicación</h3>
            </div>
            <div class="p-5 space-y-4 text-sm">
                <div class="flex justify-between items-center pb-3 border-b border-gray-50">
                    <span class="text-gray-500 font-medium">Monto Mínimo de Compra</span>
                    <span class="font-bold text-gray-800"><?= $promocion['monto_minimo'] > 0 ? '$' . number_format($promocion['monto_minimo'], 2) : '<span class="text-gray-400 font-normal italic">Sin mínimo</span>' ?></span>
                </div>
                <div class="flex justify-between items-center pb-3 border-b border-gray-50">
                    <span class="text-gray-500 font-medium">Cantidad Mínima de Productos</span>
                    <span class="font-bold text-gray-800"><?= $promocion['cantidad_minima'] > 0 ? $promocion['cantidad_minima'] . ' piezas' : '<span class="text-gray-400 font-normal italic">Sin mínimo</span>' ?></span>
                </div>
                <div class="flex justify-between items-center pb-3 border-b border-gray-50">
                    <span class="text-gray-500 font-medium">Válido Desde</span>
                    <span class="font-bold text-gray-800"><?= $promocion['fecha_inicio'] ? date('d/m/Y', strtotime($promocion['fecha_inicio'])) : '<span class="text-gray-400 font-normal italic">No definido</span>' ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500 font-medium">Válido Hasta (Caducidad)</span>
                    <span class="font-bold text-gray-800"><?= $promocion['fecha_fin'] ? date('d/m/Y', strtotime($promocion['fecha_fin'])) : '<span class="text-gray-400 font-normal italic">No definido</span>' ?></span>
                </div>
            </div>
        </div>

        <!-- Productos Requeridos (Combos) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800"><i class="fas fa-boxes text-purple-500 mr-2"></i> Productos Requeridos (Combo)</h3>
            </div>
            <div class="p-5 text-sm">
                <?php if(empty($productos_requeridos)): ?>
                    <div class="flex flex-col items-center justify-center text-gray-400 py-6">
                        <i class="fas fa-check-circle text-3xl mb-2 opacity-50"></i>
                        <p>Aplica para cualquier producto.</p>
                    </div>
                <?php else: ?>
                    <p class="text-xs text-gray-500 mb-3">El cliente debe llevar <b>todos</b> los siguientes productos para que aplique el descuento:</p>
                    <ul class="space-y-2">
                        <?php foreach($productos_requeridos as $pr): ?>
                            <li class="flex items-center gap-3 p-2 bg-gray-50 rounded border border-gray-100">
                                <div class="w-8 h-8 rounded bg-white border border-gray-200 flex items-center justify-center text-gray-400"><i class="fas fa-box"></i></div>
                                <div>
                                    <div class="font-bold text-gray-800 text-xs"><?= htmlspecialchars($pr['nombre']) ?></div>
                                    <div class="text-[10px] text-gray-500 font-mono"><?= htmlspecialchars($pr['sku']) ?></div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>