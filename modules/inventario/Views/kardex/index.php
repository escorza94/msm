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

    <div class="p-4">
        <table id="kardex-table" class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase border-b border-gray-100">
                    <th class="p-3 font-medium text-center">ID</th>
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
                    <tr><td colspan="9" class="p-8 text-center text-gray-400">No se encontraron movimientos.</td></tr>
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
                        <td class="p-3 text-center">
                            <a href="<?= base_url('inventario/movimiento/ver?id=' . $mov['id']) ?>" class="font-mono text-xs text-indigo-600 hover:underline" title="Ver detalle del movimiento"><?= str_pad($mov['id'], 5, '0', STR_PAD_LEFT) ?></a>
                        </td>
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

<!-- jQuery (necesario para DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<!-- Plugin para ordenar fechas en formato dd/mm/YYYY -->
<script src="https://cdn.datatables.net/plug-ins/1.13.6/sorting/date-eu.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#kardex-table').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        "order": [[ 1, "desc" ]], // Ordenar por fecha (columna 1) descendente por defecto
        "pageLength": 25,
        "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"] ],
        "columnDefs": [
            { "type": "date-eu", "targets": 1 } // Aplicar ordenamiento de fecha europea a la columna 1
        ],
        "initComplete": function() {
            // Si se está filtrando por un producto específico, lo mostramos en el buscador
            <?php if($producto_id_filtro > 0 && !empty($movimientos)): ?>
                const nombreProducto = '<?= htmlspecialchars($movimientos[0]['producto_nombre'] ?? '', ENT_QUOTES) ?>';
                this.api().search(nombreProducto).draw();
            <?php endif; ?>
        }
    });
});
</script>