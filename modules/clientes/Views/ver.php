<div class="max-w-7xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-user text-blue-500 mr-3"></i> Resumen del Cliente</h2>
            <p class="text-sm text-gray-500 mt-1"><?= htmlspecialchars($cliente['nombre']) ?></p>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('clientes') ?>" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 font-medium transition text-sm flex items-center shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
            <a href="<?= base_url('clientes/editar?id=' . $cliente['id']) ?>" class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 font-medium transition text-sm flex items-center shadow-sm">
                <i class="fas fa-edit mr-2"></i> Editar
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Datos del Cliente -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:col-span-1">
            <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-3 mb-4"><i class="fas fa-address-card text-gray-400 mr-2"></i> Información de Contacto</h3>
            <div class="space-y-4 text-sm">
                <div>
                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Teléfono</p>
                    <p class="font-medium text-gray-800"><?= htmlspecialchars($cliente['telefono'] ?: 'No registrado') ?></p>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Correo</p>
                    <p class="font-medium text-gray-800"><?= htmlspecialchars($cliente['correo'] ?: 'No registrado') ?></p>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">RFC</p>
                    <p class="font-medium text-gray-800 uppercase"><?= htmlspecialchars($cliente['rfc'] ?: 'No registrado') ?></p>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Dirección Principal</p>
                    <p class="font-medium text-gray-800"><?= htmlspecialchars($cliente['direccion'] ?: 'No registrada') ?></p>
                    <?php if(!empty($cliente['enlace_maps'])): ?>
                        <a href="<?= htmlspecialchars($cliente['enlace_maps']) ?>" target="_blank" class="text-xs text-blue-500 hover:underline mt-1 inline-block"><i class="fas fa-map-marker-alt"></i> Ver en Google Maps</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Resumen Financiero -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:col-span-2">
            <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-3 mb-4"><i class="fas fa-chart-line text-green-500 mr-2"></i> Balance y Estadísticas</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Comprado</p>
                    <h4 class="text-2xl font-black text-gray-800">$<?= number_format($resumen['total_comprado'] ?? 0, 2) ?></h4>
                    <p class="text-xs text-gray-400 mt-1"><?= count($ventas ?? []) ?> notas registradas</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                    <p class="text-xs font-bold text-green-600 uppercase tracking-wider mb-1">Total Pagado</p>
                    <h4 class="text-2xl font-black text-green-700">$<?= number_format($resumen['total_pagado'] ?? 0, 2) ?></h4>
                    <p class="text-xs text-green-600/70 mt-1">En pagos de contado y abonos</p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg border border-red-100">
                    <p class="text-xs font-bold text-red-500 uppercase tracking-wider mb-1">Deuda Pendiente</p>
                    <h4 class="text-2xl font-black text-red-600">$<?= number_format($resumen['deuda_pendiente'] ?? 0, 2) ?></h4>
                    <p class="text-xs text-red-500/70 mt-1">En compras a crédito/apartado</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Historial de Ventas -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800"><i class="fas fa-shopping-bag text-blue-500 mr-2"></i> Historial de Compras</h3>
            </div>
            <div class="overflow-x-auto max-h-96 custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead class="sticky top-0 bg-white shadow-sm">
                        <tr class="bg-white text-gray-400 text-[10px] uppercase border-b border-gray-100">
                            <th class="p-3 font-medium">Folio / Fecha</th>
                            <th class="p-3 font-medium text-right">Monto</th>
                            <th class="p-3 font-medium text-center">Estado</th>
                            <th class="p-3 font-medium text-center">Ver</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-50">
                        <?php if(empty($ventas)): ?>
                            <tr><td colspan="4" class="p-6 text-center text-gray-400">No hay compras registradas.</td></tr>
                        <?php else: foreach($ventas as $v): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="p-3">
                                    <div class="font-bold text-gray-800">#<?= str_pad($v['id'], 5, '0', STR_PAD_LEFT) ?></div>
                                    <div class="text-[10px] text-gray-400"><?= date('d/m/Y', strtotime($v['fecha_creacion'])) ?></div>
                                </td>
                                <td class="p-3 text-right font-black text-gray-700">$<?= number_format($v['total'], 2) ?></td>
                                <td class="p-3 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?= $v['estado_pago'] === 'pagado' ? 'bg-green-100 text-green-700' : ($v['estado_pago'] === 'parcial' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') ?>">
                                        <?= $v['estado_pago'] ?>
                                    </span>
                                </td>
                                <td class="p-3 text-center">
                                    <a href="<?= base_url('pos/ver?id=' . $v['id']) ?>" class="text-blue-500 hover:text-blue-700" title="Ver Nota"><i class="fas fa-external-link-alt"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Historial de Envíos -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800"><i class="fas fa-truck-fast text-amber-500 mr-2"></i> Historial de Envíos</h3>
            </div>
            <div class="overflow-x-auto max-h-96 custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead class="sticky top-0 bg-white shadow-sm">
                        <tr class="bg-white text-gray-400 text-[10px] uppercase border-b border-gray-100">
                            <th class="p-3 font-medium">Nota / Fecha Alta</th>
                            <th class="p-3 font-medium">Destino</th>
                            <th class="p-3 font-medium text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-50">
                        <?php if(empty($envios)): ?>
                            <tr><td colspan="3" class="p-6 text-center text-gray-400">No hay envíos registrados.</td></tr>
                        <?php else: foreach($envios as $e): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="p-3">
                                    <div class="font-bold text-gray-800">Nota #<?= str_pad($e['venta_id'], 5, '0', STR_PAD_LEFT) ?></div>
                                    <div class="text-[10px] text-gray-400"><?= date('d/m/Y', strtotime($e['fecha_creacion'])) ?></div>
                                </td>
                                <td class="p-3 text-xs text-gray-600 line-clamp-2 max-w-[150px]" title="<?= htmlspecialchars($e['direccion_destino'] ?? 'Dirección del cliente') ?>">
                                    <?= htmlspecialchars($e['direccion_destino'] ?? 'Misma del cliente') ?>
                                </td>
                                <td class="p-3 text-center">
                                    <?php if($e['estado'] === 'entregado'): ?>
                                        <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-[10px] font-bold uppercase"><i class="fas fa-check-double mr-1"></i> Entregado</span>
                                    <?php elseif($e['estado'] === 'en_ruta'): ?>
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-[10px] font-bold uppercase"><i class="fas fa-truck-fast mr-1"></i> En Ruta</span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded text-[10px] font-bold uppercase"><i class="fas fa-clock mr-1"></i> Pendiente</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>