<div class="max-w-3xl mx-auto mt-8">
    <!-- Cabecera -->
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-receipt text-indigo-500 mr-3"></i> Detalle de Movimiento
            </h2>
            <p class="text-sm text-gray-500 mt-1">Ficha de registro del movimiento de inventario.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= base_url('inventario/kardex') ?>" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 font-medium transition text-sm shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i> Volver al Kardex
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <?php
            $color_tipo = 'bg-gray-100 text-gray-600';
            $signo = '';
            if ($movimiento['tipo_movimiento'] === 'entrada') {
                $color_tipo = 'bg-green-50 text-green-700';
                $signo = '+';
            } elseif ($movimiento['tipo_movimiento'] === 'salida') {
                $color_tipo = 'bg-red-50 text-red-700';
                $signo = '-';
            }
        ?>
        <div class="flex justify-between items-start mb-4 pb-4 border-b border-gray-100">
            <div>
                <span class="inline-block px-3 py-1 <?= $color_tipo ?> rounded-lg text-xs font-bold mb-2 uppercase tracking-widest"><?= $movimiento['tipo_movimiento'] ?></span>
                <h1 class="text-3xl font-bold text-gray-800">Movimiento #<?= str_pad($movimiento['id'], 5, '0', STR_PAD_LEFT) ?></h1>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold <?= str_contains($color_tipo, 'green') ? 'text-green-600' : (str_contains($color_tipo, 'red') ? 'text-red-600' : '') ?>">
                    <?= $signo . $movimiento['cantidad'] ?> Unidades
                </div>
                <span class="inline-block mt-1 text-gray-500 text-xs"><?= date('d/m/Y \a \l\a\s H:i', strtotime($movimiento['fecha'])) ?></span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8 mt-6">
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Producto Afectado</p>
                <a href="<?= base_url('inventario/ver?id=' . $movimiento['producto_id']) ?>" class="font-medium text-indigo-600 hover:underline flex items-center gap-2"><i class="fas fa-box"></i> <?= htmlspecialchars($movimiento['producto_nombre'] ?? 'N/A') ?> (<?= htmlspecialchars($movimiento['sku'] ?? 'N/A') ?>)</a>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Usuario Responsable</p>
                <p class="font-medium text-gray-800 flex items-center gap-2"><i class="fas fa-user-check text-gray-400"></i> <?= htmlspecialchars($movimiento['usuario_nombre'] ?? 'Sistema') ?></p>
            </div>
            <div class="md:col-span-2">
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Motivo / Referencia</p>
                <p class="text-sm text-gray-600 leading-relaxed bg-gray-50 p-4 rounded-lg italic"><?= htmlspecialchars($movimiento['motivo'] ?: 'Sin motivo específico.') ?></p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Costo Unitario (en el momento)</p>
                <p class="font-medium text-gray-800 text-lg">$<?= number_format($movimiento['costo_unitario'] ?? 0, 2) ?></p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Valor Total del Movimiento</p>
                <p class="font-bold text-gray-800 text-lg">$<?= number_format($movimiento['costo_total'] ?? 0, 2) ?></p>
            </div>
        </div>
    </div>
</div>