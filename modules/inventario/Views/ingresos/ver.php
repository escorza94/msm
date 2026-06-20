<div class="max-w-4xl mx-auto mt-8">
    <!-- Cabecera -->
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-file-invoice-dollar text-indigo-500 mr-3"></i> Detalle de Ingreso
            </h2>
            <p class="text-sm text-gray-500 mt-1">Ficha de registro de la entrada de mercancía.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= base_url('inventario/ingresos') ?>" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 font-medium transition text-sm shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i> Volver al Historial
            </a>
        </div>
    </div>

    <!-- Información General -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex justify-between items-start mb-4 pb-4 border-b border-gray-100">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Ingreso #<?= str_pad($ingreso['id'], 5, '0', STR_PAD_LEFT) ?></h1>
                <span class="inline-block mt-1 text-gray-500 text-xs"><?= date('d/m/Y \a \l\a\s H:i', strtotime($ingreso['fecha_ingreso'])) ?></span>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Costo Total</p>
                <div class="text-3xl font-bold text-green-600">$<?= number_format($ingreso['costo_total'], 2) ?></div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-y-6 gap-x-8 mt-6">
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Proveedor</p>
                <p class="font-medium text-gray-800 flex items-center gap-2"><i class="fas fa-truck text-gray-400"></i> <?= htmlspecialchars($ingreso['proveedor_nombre'] ?? 'N/A') ?></p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Factura / Referencia</p>
                <p class="font-medium text-gray-800 font-mono text-sm"><?= htmlspecialchars($ingreso['referencia_factura'] ?: 'S/R') ?></p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Registrado por</p>
                <p class="font-medium text-gray-800 flex items-center gap-2"><i class="fas fa-user-check text-gray-400"></i> <?= htmlspecialchars($ingreso['usuario_nombre'] ?? 'Sistema') ?></p>
            </div>
            <?php if(!empty($ingreso['notas'])): ?>
            <div class="md:col-span-3">
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Notas Adicionales</p>
                <p class="text-sm text-gray-600 leading-relaxed bg-gray-50 p-4 rounded-lg italic"><?= htmlspecialchars($ingreso['notas']) ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Lista de Productos -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex items-center">
            <i class="fas fa-boxes text-gray-500 mr-2"></i>
            <h3 class="font-bold text-gray-800">Productos Incluidos en este Ingreso</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white text-gray-500 text-xs uppercase border-b border-gray-100">
                        <th class="p-3 font-medium">Producto</th>
                        <th class="p-3 font-medium text-center">Cantidad</th>
                        <th class="p-3 font-medium text-right">Costo Unitario</th>
                        <th class="p-3 font-medium text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                    <?php foreach($detalles as $item): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-3 font-medium"><a href="<?= base_url('inventario/ver?id=' . $item['producto_id']) ?>" class="text-indigo-600 hover:underline"><?= htmlspecialchars($item['producto_nombre']) ?></a> <span class="text-gray-400 font-mono text-xs">(<?= htmlspecialchars($item['sku']) ?>)</span></td>
                            <td class="p-3 text-center font-bold"><?= $item['cantidad'] ?></td>
                            <td class="p-3 text-right text-gray-600">$<?= number_format($item['costo_unitario'], 2) ?></td>
                            <td class="p-3 text-right font-bold text-gray-800">$<?= number_format($item['cantidad'] * $item['costo_unitario'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>