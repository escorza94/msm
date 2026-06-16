<div class="max-w-7xl mx-auto mt-4">
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <?php if(!empty($configGlobal['BUSINESS_LOGO'])): ?>
                <img src="<?= (strpos($configGlobal['BUSINESS_LOGO'], 'http') === 0 ? '' : base_url()) . htmlspecialchars($configGlobal['BUSINESS_LOGO']) ?>" alt="Logo" class="h-14 max-w-[150px] object-contain drop-shadow-sm">
            <?php endif; ?>
            <div>
                <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                    <?php if(empty($configGlobal['BUSINESS_LOGO'])): ?><i class="fas fa-chart-pie text-indigo-500 mr-3"></i><?php endif; ?>
                    <?= htmlspecialchars($configGlobal['APP_NAME'] ?? 'Resumen del Negocio') ?>
                </h2>
                <p class="text-sm text-gray-500 mt-1">Indicadores comerciales clave al día de hoy, <?= date('d/m/Y') ?></p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('pos') ?>" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition text-sm flex items-center shadow-md">
                <i class="fas fa-cash-register mr-2"></i> Abrir Caja (POS)
            </a>
        </div>
    </div>

    <?php if(isset($_GET['error'])): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-xl flex items-center shadow-sm text-sm">
            <i class="fas fa-exclamation-triangle text-lg mr-3"></i> 
            <div><?= htmlspecialchars($_GET['error']) ?></div>
        </div>
    <?php endif; ?>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Ventas Hoy -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-green-50 rounded-bl-full -z-10 group-hover:scale-110 transition-transform"></div>
            <div class="flex justify-between items-start mb-2 z-10 relative">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Ventas de Hoy</p>
                    <h3 class="text-3xl font-black text-gray-800 mt-1">$<?= number_format($ventasHoy, 2) ?></h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-lg shadow-inner"><i class="fas fa-dollar-sign"></i></div>
            </div>
            <p class="text-[10px] text-green-500 font-bold mt-2"><i class="fas fa-arrow-up mr-1"></i> Actualizado en tiempo real</p>
        </div>

        <!-- Ventas Mes -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-blue-50 rounded-bl-full -z-10 group-hover:scale-110 transition-transform"></div>
            <div class="flex justify-between items-start mb-2 z-10 relative">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Acumulado Mes</p>
                    <h3 class="text-3xl font-black text-gray-800 mt-1">$<?= number_format($ventasMes, 2) ?></h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-lg shadow-inner"><i class="fas fa-calendar-alt"></i></div>
            </div>
            <p class="text-[10px] text-gray-400 font-bold mt-2"><i class="fas fa-clock mr-1"></i> Mes en curso</p>
        </div>

        <!-- Cuentas por Cobrar -->
        <a href="<?= base_url('finanzas/cobrar') ?>" class="block bg-white p-5 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden group hover:border-amber-300 transition">
            <div class="absolute right-0 top-0 w-24 h-24 bg-amber-50 rounded-bl-full -z-10 group-hover:scale-110 transition-transform"></div>
            <div class="flex justify-between items-start mb-2 z-10 relative">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Por Cobrar</p>
                    <h3 class="text-3xl font-black text-gray-800 mt-1">$<?= number_format($porCobrar, 2) ?></h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center text-lg shadow-inner"><i class="fas fa-hand-holding-usd"></i></div>
            </div>
            <p class="text-[10px] text-amber-600 font-bold mt-2"><i class="fas fa-exclamation-circle mr-1"></i> Dinero pendiente en la calle</p>
        </a>

        <!-- Entregas Activas -->
        <a href="<?= base_url('logistica/entregas') ?>" class="block bg-white p-5 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden group hover:border-purple-300 transition">
            <div class="absolute right-0 top-0 w-24 h-24 bg-purple-50 rounded-bl-full -z-10 group-hover:scale-110 transition-transform"></div>
            <div class="flex justify-between items-start mb-2 z-10 relative">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Envíos Activos</p>
                    <h3 class="text-3xl font-black text-gray-800 mt-1"><?= $entregasActivas ?></h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-lg shadow-inner"><i class="fas fa-truck-fast"></i></div>
            </div>
            <p class="text-[10px] text-purple-600 font-bold mt-2"><i class="fas fa-boxes mr-1"></i> Pendientes o en ruta</p>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Últimas Ventas -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800"><i class="fas fa-bolt text-amber-400 mr-2"></i> Últimas Ventas</h3>
                <a href="<?= base_url('pos/historial') ?>" class="text-xs text-blue-600 font-bold hover:underline">Ver todas</a>
            </div>
            <div class="p-0">
                <table class="w-full text-left border-collapse">
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-50">
                        <?php if(empty($ultimasVentas)): ?>
                            <tr><td class="p-6 text-center text-gray-400">No hay ventas recientes.</td></tr>
                        <?php else: foreach($ultimasVentas as $v): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4 w-12 text-center"><div class="w-8 h-8 rounded-full bg-green-50 text-green-500 flex items-center justify-center text-xs"><i class="fas fa-check"></i></div></td>
                                <td class="p-4"><div class="font-bold text-gray-800">Folio #<?= str_pad($v['id'], 5, '0', STR_PAD_LEFT) ?></div><div class="text-[10px] text-gray-400 mt-0.5"><?= date('d/m/Y h:i A', strtotime($v['fecha_creacion'])) ?></div></td>
                                <td class="p-4 text-gray-600"><?= htmlspecialchars($v['cliente_nombre'] ?? 'Público en General') ?></td>
                                <td class="p-4 text-right font-black text-gray-800">$<?= number_format($v['total'], 2) ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Alertas de Stock -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100 bg-red-50 flex justify-between items-center">
                <h3 class="font-bold text-red-800"><i class="fas fa-exclamation-triangle text-red-500 mr-2"></i> Alertas de Stock</h3>
                <a href="<?= base_url('inventario') ?>" class="text-xs text-red-600 font-bold hover:underline">Ir a catálogo</a>
            </div>
            <div class="p-4 space-y-3">
                <?php if(empty($alertasStock)): ?>
                    <div class="text-center py-6 text-gray-400"><i class="fas fa-box-open text-3xl mb-2 opacity-30"></i><p class="text-sm">Todo el inventario está en niveles óptimos.</p></div>
                <?php else: foreach($alertasStock as $p): ?>
                    <div class="flex items-center justify-between p-3 rounded-xl border <?= $p['stock'] <= 0 ? 'border-red-200 bg-red-50' : 'border-amber-100 bg-amber-50' ?>">
                        <div class="overflow-hidden">
                            <h4 class="text-xs font-bold text-gray-800 truncate"><?= htmlspecialchars($p['nombre']) ?></h4>
                            <p class="text-[10px] text-gray-500 font-mono mt-0.5"><?= htmlspecialchars($p['sku']) ?></p>
                        </div>
                        <span class="flex-shrink-0 ml-3 px-2 py-1 rounded text-[10px] font-black uppercase <?= $p['stock'] <= 0 ? 'bg-red-200 text-red-800' : 'bg-amber-200 text-amber-800' ?>"><?= $p['stock'] ?> piezas</span>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</div>