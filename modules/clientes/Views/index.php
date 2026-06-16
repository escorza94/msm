<div class="max-w-6xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-user-tag text-blue-500 mr-3"></i> Cartera de Clientes</h2>
            <p class="text-sm text-gray-500 mt-1">Directorio principal de clientes, prospectos y facturación</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= base_url('clientes/nuevo') ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition text-sm flex items-center shadow-md">
                <i class="fas fa-user-plus mr-2"></i> Nuevo Cliente
            </a>
        </div>
    </div>

    <?php if(isset($_GET['success'])): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-xl flex items-center shadow-sm text-sm">
            <i class="fas fa-check-circle text-lg mr-3"></i> 
            <div><?= htmlspecialchars($_GET['success']) ?></div>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="tabla-clientes" class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase border-b border-gray-100">
                        <th class="p-4 font-medium w-16 text-center">ID</th>
                        <th class="p-4 font-medium">Nombre / Razón Social</th>
                        <th class="p-4 font-medium">Contacto</th>
                        <th class="p-4 font-medium text-center">Whatsapp Link</th>
                        <th class="p-4 font-medium text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                    <?php if(empty($clientes)): ?>
                        <tr><td colspan="5" class="p-12 text-center text-gray-400"><i class="fas fa-users-slash text-4xl mb-3 opacity-30"></i><br>No hay clientes registrados en el sistema.</td></tr>
                    <?php else: foreach($clientes as $c): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 text-center text-gray-500 text-xs font-mono font-bold"><?= $c['id'] ?></td>
                            <td class="p-4 font-bold text-gray-800"><div class="flex items-center gap-3"><div class="w-8 h-8 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center font-bold text-xs"><?= strtoupper(substr($c['nombre'], 0, 1)) ?></div><?= htmlspecialchars($c['nombre']) ?></div></td>
                            <td class="p-4"><p class="text-gray-800 font-medium"><?= htmlspecialchars($c['telefono'] ?: 'N/A') ?></p><p class="text-xs text-gray-400"><?= htmlspecialchars($c['correo'] ?: 'Sin correo') ?></p></td>
                            <td class="p-4 text-center"><?= $c['whatsapp_id'] ? '<span class="text-green-500 bg-green-50 px-2 py-1 rounded text-xs font-bold font-mono"><i class="fab fa-whatsapp mr-1"></i> Vinculado</span>' : '<span class="text-gray-300 text-xs">-</span>' ?></td>
                            <td class="p-4 text-center">
                                <a href="<?= base_url('clientes/ver?id=' . $c['id']) ?>" class="w-8 h-8 inline-flex items-center justify-center rounded bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition tooltip" title="Ver Resumen"><i class="fas fa-eye"></i></a>
                                <a href="<?= base_url('clientes/editar?id=' . $c['id']) ?>" class="w-8 h-8 inline-flex items-center justify-center rounded bg-gray-50 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition tooltip" title="Editar cliente"><i class="fas fa-edit"></i></a>
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
    /* Integración visual con Tailwind */
    .dataTables_wrapper .dataTables_length select, .dataTables_wrapper .dataTables_filter input { border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 0.25rem 0.5rem; outline: none; margin-bottom: 1rem; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #2563eb !important; color: white !important; border: none !important; border-radius: 0.5rem; }
</style>

<script>
    $(document).ready(function() {
        $('#tabla-clientes').DataTable({ "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" }, "order": [[ 0, "desc" ]], "pageLength": 10, "columnDefs": [ { "orderable": false, "targets": [3, 4] } ] });
    });
</script>