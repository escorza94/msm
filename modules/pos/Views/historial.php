<div class="max-w-7xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-list-alt text-blue-500 mr-3"></i> Historial de Transacciones</h2>
            <p class="text-sm text-gray-500 mt-1">Registro de todas las cotizaciones y ventas realizadas</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= base_url('pos') ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition text-sm flex items-center shadow-md">
                <i class="fas fa-cash-register mr-2"></i> Nuevo Ticket (POS)
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase border-b border-gray-100">
                        <th class="p-4 font-medium w-20 text-center">Folio</th>
                        <th class="p-4 font-medium">Fecha</th>
                        <th class="p-4 font-medium">Cliente</th>
                        <th class="p-4 font-medium text-center">Tipo</th>
                        <th class="p-4 font-medium text-right">Total</th>
                        <th class="p-4 font-medium text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                    <?php if(empty($ventas)): ?>
                        <tr><td colspan="6" class="p-12 text-center text-gray-400"><i class="fas fa-receipt text-4xl mb-3 opacity-30"></i><br>No hay transacciones registradas.</td></tr>
                    <?php else: foreach($ventas as $v): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 text-center font-mono font-bold text-gray-500">
                                #<?= str_pad($v['id'], 5, '0', STR_PAD_LEFT) ?>
                            </td>
                            <td class="p-4">
                                <div class="font-medium text-gray-800"><?= date('d/m/Y', strtotime($v['fecha_creacion'])) ?></div>
                                <div class="text-xs text-gray-400"><?= date('h:i A', strtotime($v['fecha_creacion'])) ?></div>
                            </td>
                            <td class="p-4 font-bold text-gray-800">
                                <?= htmlspecialchars($v['cliente_nombre'] ?? 'Público en General') ?>
                            </td>
                            <td class="p-4 text-center">
                                <?php if($v['tipo'] === 'cotizacion'): ?>
                                    <span class="px-2 py-1 bg-amber-50 text-amber-600 rounded text-[10px] font-bold uppercase"><i class="fas fa-file-alt mr-1"></i> Cotización</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 <?= ($v['estado_entrega'] ?? 'pendiente') === 'entregado' ? 'bg-green-50 text-green-600' : 'bg-blue-50 text-blue-600' ?> rounded text-[10px] font-bold uppercase tooltip" title="<?= ($v['estado_entrega'] ?? 'pendiente') === 'entregado' ? 'Mercancía Entregada' : 'Pendiente de Entrega' ?>">
                                        <i class="fas <?= ($v['estado_entrega'] ?? 'pendiente') === 'entregado' ? 'fa-box-open' : 'fa-box' ?> mr-1"></i> Venta
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-right font-black text-blue-600">
                                $<?= number_format($v['total'], 2) ?>
                            </td>
                            <td class="p-4 text-center flex items-center justify-center gap-2">
                                <a href="<?= base_url('pos/ver?id=' . $v['id']) ?>" class="w-8 h-8 inline-flex items-center justify-center rounded bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition tooltip" title="Ver Detalle"><i class="fas fa-eye"></i></a>
                                <button class="w-8 h-8 inline-flex items-center justify-center rounded bg-gray-50 text-gray-600 hover:bg-red-50 hover:text-red-600 transition tooltip" title="Generar PDF (Próximamente)"><i class="fas fa-file-pdf"></i></button>
                                <?php if($v['tipo'] === 'cotizacion'): ?>
                                    <button class="w-8 h-8 inline-flex items-center justify-center rounded bg-gray-50 text-gray-600 hover:bg-green-50 hover:text-green-600 transition tooltip" title="Convertir a Venta (Próximamente)"><i class="fas fa-exchange-alt"></i></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>