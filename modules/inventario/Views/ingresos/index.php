<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mx-auto max-w-7xl mt-6">
    <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
        <div>
            <h3 class="font-bold text-gray-800 flex items-center">
                <i class="fas fa-truck-loading text-indigo-500 mr-2 text-lg"></i> Historial de Ingresos de Mercancía
            </h3>
            <p class="text-xs text-gray-500 mt-1">Registro de compras a proveedores y ajustes de entrada.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?= base_url('inventario') ?>" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 font-medium transition text-sm flex items-center shadow-sm">
                <i class="fas fa-arrow-left mr-2 text-gray-400"></i> Volver al Inventario
            </a>
            <a href="<?= base_url('inventario/ingresos/nuevo') ?>" class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 font-bold transition shadow text-sm flex items-center">
                <i class="fas fa-plus mr-2"></i> Registrar Nuevo Ingreso
            </a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase border-b border-gray-100">
                    <th class="p-3 font-medium text-center">ID</th>
                    <th class="p-3 font-medium">Fecha</th>
                    <th class="p-3 font-medium">Proveedor</th>
                    <th class="p-3 font-medium">Factura / Ref.</th>
                    <th class="p-3 font-medium text-right">Costo Total</th>
                    <th class="p-3 font-medium">Registrado por</th>
                    <th class="p-3 font-medium text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                <?php if(empty($ingresos)): ?>
                    <tr><td colspan="7" class="p-8 text-center text-gray-400">Aún no se han registrado ingresos de mercancía.</td></tr>
                <?php else: foreach($ingresos as $ingreso): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-3 text-center text-gray-400 text-xs font-mono"><?= $ingreso['id'] ?></td>
                        <td class="p-3"><?= date('d/m/Y H:i', strtotime($ingreso['fecha_ingreso'])) ?></td>
                        <td class="p-3 font-medium"><?= htmlspecialchars($ingreso['proveedor_nombre'] ?? 'N/A') ?></td>
                        <td class="p-3 text-gray-500 font-mono text-xs"><?= htmlspecialchars($ingreso['referencia_factura'] ?: 'S/R') ?></td>
                        <td class="p-3 text-right font-bold text-green-600">$<?= number_format($ingreso['costo_total'], 2) ?></td>
                        <td class="p-3 text-gray-500"><?= htmlspecialchars($ingreso['usuario_nombre'] ?? 'Sistema') ?></td>
                        <td class="p-3 text-center">
                            <a href="<?= base_url('inventario/ingresos/ver?id=' . $ingreso['id']) ?>" class="inline-flex items-center justify-center w-8 h-8 rounded bg-gray-100 text-gray-500 hover:bg-indigo-100 hover:text-indigo-600 transition tooltip" title="Ver detalle del ingreso">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>