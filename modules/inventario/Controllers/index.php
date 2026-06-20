<style>
    .select2-container--default .select2-selection--single {
        height: 42px;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 42px;
        padding-left: 1rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px;
    }
</style>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mx-auto max-w-7xl mt-6">
    <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
        <div>
            <h3 class="font-bold text-gray-800 flex items-center">
                <i class="fas fa-history text-indigo-500 mr-2 text-lg"></i> Kardex de Inventario
            </h3>
            <p class="text-xs text-gray-500 mt-1">Historial completo de entradas, salidas y ajustes.</p>
        </div>
        <a href="<?= base_url('inventario') ?>" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 font-medium transition text-sm flex items-center shadow-sm">
            <i class="fas fa-arrow-left mr-2 text-gray-400"></i> Volver al Inventario
        </a>
    </div>

    <!-- Filtro por producto -->
    <div class="p-4 border-b border-gray-100">
        <label class="block text-sm font-medium text-gray-700 mb-1">Filtrar por producto</label>
        <select id="filtro-producto" class="w-full md:w-1/2">
            <option value="">-- Mostrar todos los movimientos --</option>
        </select>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase border-b border-gray-100">
                    <th class="p-3 font-medium">Fecha</th>
                    <th class="p-3 font-medium">Producto</th>
                    <th class="p-3 font-medium text-center">Tipo</th>
                    <th class="p-3 font-medium text-center">Cantidad</th>
                    <th class="p-3 font-medium text-right">Costo Unit.</th>
                    <th class="p-3 font-medium text-right">Costo Total</th>
                    <th class="p-3 font-medium">Motivo / Referencia</th>
                    <th class="p-3 font-medium">Usuario</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                <?php if(empty($movimientos)): ?>
                    <tr><td colspan="8" class="p-8 text-center text-gray-400">No se encontraron movimientos.</td></tr>
                <?php else: foreach($movimientos as $mov): 
                    $color_tipo = 'bg-gray-100 text-gray-600';
                    $signo = '';
                    if ($mov['tipo_movimiento'] === 'entrada') {
                        $color_tipo = 'bg-green-50 text-green-700';
                        $signo = '+';
                    } elseif ($mov['tipo_movimiento'] === 'salida') {
                        $color_tipo = 'bg-red-50 text-red-700';
                        $signo = '-';
                    }
                ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-3 text-gray-500 text-xs whitespace-nowrap"><?= date('d/m/Y H:i', strtotime($mov['fecha_registro'])) ?></td>
                        <td class="p-3 font-medium">
                            <a href="<?= base_url('inventario/kardex?producto_id=' . $mov['producto_id']) ?>" class="text-indigo-600 hover:underline">
                                <?= htmlspecialchars($mov['producto_nombre'] ?? 'N/A') ?>
                            </a>
                            <p class="text-xs text-gray-400 font-mono"><?= htmlspecialchars($mov['sku'] ?? 'N/A') ?></p>
                        </td>
                        <td class="p-3 text-center">
                            <span class="px-2 py-1 <?= $color_tipo ?> rounded text-[10px] font-bold uppercase"><?= $mov['tipo_movimiento'] ?></span>
                        </td>
                        <td class="p-3 text-center font-bold <?= str_contains($color_tipo, 'green') ? 'text-green-600' : (str_contains($color_tipo, 'red') ? 'text-red-600' : '') ?>"><?= $signo . $mov['cantidad'] ?></td>
                        <td class="p-3 text-right text-gray-500 text-xs">$<?= number_format($mov['costo_unitario'] ?? 0, 2) ?></td>
                        <td class="p-3 text-right text-gray-600 font-medium">$<?= number_format($mov['costo_total'] ?? 0, 2) ?></td>
                        <td class="p-3 text-gray-500 text-xs italic"><?= htmlspecialchars($mov['motivo']) ?></td>
                        <td class="p-3 text-gray-500 text-xs"><?= htmlspecialchars($mov['usuario_nombre'] ?? 'Sistema') ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#filtro-producto').select2({
        placeholder: 'Busca por SKU o nombre de producto...',
        ajax: {
            url: '<?= base_url('inventario/ajax_buscar') ?>',
            dataType: 'json',
            delay: 250,
            data: function (params) { return { q: params.term }; },
            processResults: function (data) {
                const items = data.productos.map(p => ({ id: p.id, text: `${p.nombre} (${p.sku})` }));
                items.unshift({ id: '', text: '-- Mostrar todos los movimientos --' });
                return { results: items };
            },
            cache: true
        }
    });

    $('#filtro-producto').on('change', function() {
        const productoId = $(this).val();
        window.location.href = '<?= base_url('inventario/kardex') ?>' + (productoId ? '?producto_id=' + productoId : '');
    });
});
</script>