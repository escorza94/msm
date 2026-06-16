<div class="max-w-7xl mx-auto mt-4 mb-10">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-list text-blue-500 mr-3"></i> Historial de Entregas</h2>
            <p class="text-sm text-gray-500 mt-1">Registro completo de todas las entregas gestionadas.</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('logistica/entregas') ?>" class="px-4 py-2 bg-blue-50 border border-blue-100 text-blue-700 rounded-lg hover:bg-blue-100 font-medium transition text-sm flex items-center shadow-sm">
                <i class="fas fa-boxes mr-2"></i> Ver Kanban
            </a>
            <a href="<?= base_url('logistica') ?>" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 font-medium transition text-sm flex items-center shadow-sm">
                <i class="fas fa-arrow-left mr-2 text-gray-400"></i> Volver
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-6">
        <div class="overflow-x-auto">
            <table id="tabla-entregas" class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase border-b border-gray-100">
                        <th class="p-4 font-medium w-20 text-center">Venta</th>
                        <th class="p-4 font-medium">Cliente</th>
                        <th class="p-4 font-medium">Destino</th>
                        <th class="p-4 font-medium text-center">Estado</th>
                        <th class="p-4 font-medium">Fecha Entrega</th>
                        <th class="p-4 font-medium text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                    <?php foreach($envios as $e): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 text-center font-mono font-bold text-gray-500">
                                <a href="<?= base_url('pos/ver?id=' . $e['folio_venta']) ?>" class="text-blue-600 hover:underline">#<?= str_pad($e['folio_venta'], 5, '0', STR_PAD_LEFT) ?></a>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-gray-800"><?= htmlspecialchars($e['cliente_nombre'] ?? 'Cliente General') ?></div>
                                <?php if($e['cliente_telefono']): ?>
                                    <div class="text-xs text-gray-500"><i class="fas fa-phone-alt text-gray-400 mr-1"></i> <?= $e['cliente_telefono'] ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="p-4">
                                <?php if($e['direccion_destino']): ?>
                                    <div class="text-xs text-gray-600 mb-1 line-clamp-2" title="<?= htmlspecialchars($e['direccion_destino']) ?>"><?= htmlspecialchars($e['direccion_destino']) ?></div>
                                <?php endif; ?>
                                <?php if($e['coordenadas_destino']): ?>
                                    <a href="https://www.google.com/maps/search/?api=1&query=<?= $e['coordenadas_destino'] ?>" target="_blank" class="text-xs text-blue-500 hover:text-blue-700"><i class="fas fa-map-marker-alt"></i> Ver Mapa</a>
                                <?php else: ?>
                                    <span class="text-xs text-amber-500"><i class="fas fa-exclamation-triangle"></i> N/A</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-center">
                                <?php if($e['estado'] === 'entregado'): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-[10px] font-bold uppercase"><i class="fas fa-check-double mr-1"></i> Entregado</span>
                                <?php elseif($e['estado'] === 'en_ruta'): ?>
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-[10px] font-bold uppercase"><i class="fas fa-truck-fast mr-1"></i> En Ruta</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded text-[10px] font-bold uppercase"><i class="fas fa-clock mr-1"></i> Pendiente</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-gray-600">
                                <?= $e['fecha_entrega'] ? date('d/m/Y h:i A', strtotime($e['fecha_entrega'])) : '<span class="text-gray-400 italic">Pendiente</span>' ?>
                            </td>
                            <td class="p-4 text-center">
                                <a href="<?= base_url('pos/ver?id=' . $e['folio_venta']) ?>" class="w-8 h-8 inline-flex items-center justify-center rounded bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition tooltip" title="Ver Nota"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Requisitos de DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<style>
    /* Sobrescribir estilos por defecto de DataTables para integrar con Tailwind */
    .dataTables_wrapper .dataTables_length select, .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 0.25rem 0.5rem; outline: none; margin-bottom: 1rem;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #3b82f6 !important; color: white !important; border: none !important; border-radius: 0.5rem;
    }
</style>

<script>
    $(document).ready(function() {
        $('#tabla-entregas').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            "order": [[ 0, "desc" ]],
            "pageLength": 10
        });
    });
</script>