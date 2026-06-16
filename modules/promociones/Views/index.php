<div class="max-w-7xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-tags text-red-500 mr-3"></i> Promociones y Cupones</h2>
            <p class="text-sm text-gray-500 mt-1">Gestiona los descuentos globales y cupones promocionales</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= base_url('promociones/nuevo') ?>" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 font-medium transition text-sm flex items-center shadow-md">
                <i class="fas fa-plus mr-2"></i> Nueva Promoción
            </a>
        </div>
    </div>

    <?php if(isset($_GET['success'])): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-xl flex items-center shadow-sm text-sm">
            <i class="fas fa-check-circle text-lg mr-3"></i> 
            <div><?= htmlspecialchars($_GET['success']) ?></div>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-6">
        <div class="overflow-x-auto">
            <table id="tabla-promociones" class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase border-b border-gray-100">
                        <th class="p-4 font-medium">Nombre / Cupón</th>
                        <th class="p-4 font-medium text-center">Tipo</th>
                        <th class="p-4 font-medium text-right">Valor</th>
                        <th class="p-4 font-medium text-center">Validez</th>
                        <th class="p-4 font-medium text-center">Estado</th>
                        <th class="p-4 font-medium text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                    <?php if(!empty($promociones)): foreach($promociones as $p): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4">
                                <div class="font-bold text-gray-800"><?= htmlspecialchars($p['nombre']) ?></div>
                                <?php if($p['codigo_cupon']): ?>
                                    <div class="inline-block mt-1 px-2 py-0.5 bg-gray-100 text-gray-600 font-mono text-xs rounded border border-gray-200">
                                        <i class="fas fa-barcode text-gray-400 mr-1"></i> <?= htmlspecialchars($p['codigo_cupon']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-center">
                                <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded text-[10px] font-bold uppercase">
                                    <?= $p['tipo'] === 'porcentaje' ? 'Porcentaje (%)' : 'Monto Fijo ($)' ?>
                                </span>
                            </td>
                            <td class="p-4 text-right font-bold <?= $p['tipo'] === 'porcentaje' ? 'text-blue-600' : 'text-green-600' ?>">
                                <?= $p['tipo'] === 'porcentaje' ? round($p['valor']) . '%' : '-$' . number_format($p['valor'], 2) ?>
                            </td>
                            <td class="p-4 text-center text-xs">
                                <?php if($p['fecha_inicio'] && $p['fecha_fin']): ?>
                                    <div><?= date('d/m/Y', strtotime($p['fecha_inicio'])) ?></div>
                                    <div class="text-gray-400">al</div>
                                    <div><?= date('d/m/Y', strtotime($p['fecha_fin'])) ?></div>
                                <?php else: ?>
                                    <span class="text-gray-400 italic">Sin límite</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-center">
                                <a href="<?= base_url('promociones/cambiarEstado?id=' . $p['id']) ?>" class="px-2 py-1 rounded text-[10px] font-bold uppercase transition <?= $p['estado'] === 'activo' ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' ?>" title="Clic para cambiar">
                                    <?= $p['estado'] ?>
                                </a>
                            </td>
                            <td class="p-4 text-center flex items-center justify-center gap-2">
                                <a href="<?= base_url('promociones/ver?id=' . $p['id']) ?>" class="w-8 h-8 inline-flex items-center justify-center rounded bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition tooltip" title="Ver Detalle"><i class="fas fa-eye"></i></a>
                                <a href="<?= base_url('promociones/editar?id=' . $p['id']) ?>" class="w-8 h-8 inline-flex items-center justify-center rounded bg-gray-50 text-gray-600 hover:bg-red-50 hover:text-red-600 transition tooltip" title="Editar"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
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
    /* Integración visual con Tailwind y los colores del módulo (Rojo) */
    .dataTables_wrapper .dataTables_length select, .dataTables_wrapper .dataTables_filter input { border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 0.25rem 0.5rem; outline: none; margin-bottom: 1rem; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #ef4444 !important; color: white !important; border: none !important; border-radius: 0.5rem; }
</style>

<script>
    $(document).ready(function() {
        $('#tabla-promociones').DataTable({ "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" }, "order": [[ 0, "asc" ]], "pageLength": 10, "columnDefs": [ { "orderable": false, "targets": [4, 5] } ] });
    });
</script>
