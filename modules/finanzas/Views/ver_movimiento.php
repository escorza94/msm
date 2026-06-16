<div class="max-w-4xl mx-auto mt-8 mb-10">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                <?php if($movimiento['tipo'] === 'ingreso'): ?>
                    <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xl mr-3 shadow-sm"><i class="fas fa-arrow-up"></i></div>
                    Ingreso #<?= str_pad($movimiento['id'], 5, '0', STR_PAD_LEFT) ?>
                <?php else: ?>
                    <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xl mr-3 shadow-sm"><i class="fas fa-arrow-down"></i></div>
                    Egreso #<?= str_pad($movimiento['id'], 5, '0', STR_PAD_LEFT) ?>
                <?php endif; ?>
            </h2>
            <p class="text-sm text-gray-500 mt-1 ml-14">Registrado el <?= date('d/m/Y h:i A', strtotime($movimiento['fecha_movimiento'])) ?></p>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('finanzas') ?>" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 font-medium transition text-sm flex items-center shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i> Volver al Libro Mayor
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800"><i class="fas fa-file-invoice-dollar text-gray-400 mr-2"></i> Detalles de la Transacción</h3>
        </div>
        
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8 text-sm">
            <div class="space-y-4">
                <div>
                    <p class="text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-1">Concepto / Descripción</p>
                    <p class="font-bold text-gray-800 text-base bg-gray-50 p-3 rounded-lg border border-gray-100"><?= htmlspecialchars($movimiento['concepto']) ?></p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-1">Cuenta Afectada</p>
                        <p class="font-bold text-gray-800"><i class="fas fa-wallet text-gray-400 mr-1"></i> <?= htmlspecialchars($movimiento['cuenta_nombre'] ?? 'N/A') ?></p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-1">Categoría</p>
                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-medium"><?= htmlspecialchars($movimiento['categoria_nombre'] ?? 'Sin categoría') ?></span>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-1">Método de Pago</p>
                        <p class="font-medium text-gray-700"><i class="fas fa-money-check-alt text-gray-400 mr-1"></i> <?= htmlspecialchars($movimiento['metodo_pago'] ?: 'N/A') ?></p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-1">Registrado por</p>
                        <p class="font-medium text-gray-700"><i class="fas fa-user-circle text-gray-400 mr-1"></i> <?= htmlspecialchars($movimiento['usuario_nombre'] ?? 'Sistema') ?></p>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col justify-center items-center p-6 bg-gray-50 rounded-xl border border-gray-100">
                <p class="text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-2">Importe Total</p>
                <h1 class="text-5xl font-black <?= $movimiento['tipo'] === 'ingreso' ? 'text-green-600' : 'text-red-500' ?>"><?= $movimiento['tipo'] === 'ingreso' ? '+' : '-' ?>$<?= number_format($movimiento['monto'], 2) ?></h1>
                <?php if($movimiento['origen_tipo'] === 'venta'): ?><a href="<?= base_url('pos/ver?id=' . $movimiento['origen_id']) ?>" class="mt-6 bg-white border border-blue-200 text-blue-600 hover:bg-blue-50 px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition"><i class="fas fa-shopping-cart mr-2"></i> Ver Venta Relacionada</a><?php endif; ?>
            </div>
        </div>
    </div>
</div>