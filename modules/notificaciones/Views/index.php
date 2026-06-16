<div class="max-w-6xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-bell text-yellow-500 mr-3"></i> Bandeja de Notificaciones</h2>
            <p class="text-sm text-gray-500 mt-1">Historial completo de alertas del sistema</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="marcarTodasLeidasIndex()" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 font-medium transition text-sm flex items-center shadow-sm">
                <i class="fas fa-check-double mr-2 text-indigo-500"></i> Marcar Todas Leídas
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 overflow-x-auto">
            <table id="tabla-notificaciones" class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase border-b border-gray-100">
                        <th class="p-4 font-medium w-16 text-center">ID</th>
                        <th class="p-4 font-medium">Alerta</th>
                        <th class="p-4 font-medium text-center">Fecha</th>
                        <th class="p-4 font-medium text-center">Estado</th>
                        <th class="p-4 font-medium text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                    <?php if(empty($notificaciones)): ?>
                        <tr><td colspan="5" class="p-12 text-center text-gray-400"><i class="fas fa-bell-slash text-4xl mb-3 opacity-30"></i><br>No tienes notificaciones registradas.</td></tr>
                    <?php else: foreach($notificaciones as $n): ?>
                        <tr class="hover:bg-gray-50 transition <?= $n['leida'] ? 'opacity-70 bg-white' : 'bg-indigo-50/30' ?>">
                            <td class="p-4 text-center text-gray-400 text-xs font-mono font-bold"><?= $n['id'] ?></td>
                            <td class="p-4">
                                <div class="font-bold <?= $n['leida'] ? 'text-gray-600' : 'text-gray-800' ?>"><?= htmlspecialchars($n['titulo']) ?></div>
                                <div class="text-xs text-gray-500 mt-1"><?= nl2br(htmlspecialchars($n['mensaje'])) ?></div>
                            </td>
                            <td class="p-4 text-xs text-gray-500 text-center font-medium"><?= date('d/m/Y h:i A', strtotime($n['fecha_creacion'])) ?></td>
                            <td class="p-4 text-center">
                                <?php if($n['leida']): ?>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-500 rounded text-[10px] font-bold uppercase"><i class="fas fa-check"></i> Leída</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded text-[10px] font-bold uppercase"><i class="fas fa-envelope"></i> Nueva</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-center flex items-center justify-center gap-2">
                                <?php if($n['enlace']): ?>
                                    <a href="<?= base_url($n['enlace']) ?>" class="w-8 h-8 inline-flex items-center justify-center rounded bg-blue-50 text-blue-600 hover:bg-blue-100 transition tooltip" title="Ir al origen de la alerta"><i class="fas fa-external-link-alt"></i></a>
                                <?php endif; ?>
                                
                                <?php if($n['leida']): ?>
                                    <button onclick="marcarNoLeida(<?= $n['id'] ?>)" class="w-8 h-8 inline-flex items-center justify-center rounded bg-gray-50 text-gray-600 hover:bg-amber-50 hover:text-amber-600 transition tooltip" title="Marcar como no leída"><i class="fas fa-envelope"></i></button>
                                <?php else: ?>
                                    <button onclick="marcarLeidaIndex(<?= $n['id'] ?>)" class="w-8 h-8 inline-flex items-center justify-center rounded bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition tooltip" title="Marcar como leída"><i class="fas fa-check-double"></i></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<style>
    /* Integración visual con Tailwind */
    .dataTables_wrapper .dataTables_length select, 
    .dataTables_wrapper .dataTables_filter input { border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 0.35rem 0.75rem; outline: none; margin-left: 0.5rem; font-size: 0.875rem; margin-bottom: 1rem; }
    .dataTables_wrapper .dataTables_length label, 
    .dataTables_wrapper .dataTables_filter label { font-size: 0.875rem; color: #4b5563; font-weight: 500; }
    .dataTables_wrapper .dataTables_info { font-size: 0.875rem; color: #6b7280; padding-top: 1rem; }
    .dataTables_wrapper .dataTables_paginate { padding-top: 1rem; }
    .dataTables_wrapper .dataTables_paginate .paginate_button { padding: 0.35rem 0.75rem !important; border-radius: 0.5rem !important; margin: 0 0.125rem !important; border: 1px solid transparent !important; color: #4b5563 !important; font-size: 0.875rem !important; cursor: pointer; transition: all 0.2s; background: transparent !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background: #f3f4f6 !important; border-color: #e5e7eb !important; color: #1f2937 !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #6366f1 !important; color: white !important; border-color: #6366f1 !important; font-weight: bold; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled { opacity: 0.5; cursor: not-allowed; background: transparent !important; border-color: transparent !important; }
</style>

<script>
    $(document).ready(function() {
        $('#tabla-notificaciones').DataTable({ 
            "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" }, 
            "order": [[ 0, "desc" ]], 
            "pageLength": 10, 
            "columnDefs": [ { "orderable": false, "targets": [4] } ] 
        });
    });

    function marcarLeidaIndex(id) {
        fetch('<?= base_url('notificaciones/marcarLeida') ?>', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({id: id}) }).then(() => window.location.reload());
    }

    function marcarNoLeida(id) {
        fetch('<?= base_url('notificaciones/marcarNoLeida') ?>', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({id: id}) }).then(() => window.location.reload());
    }

    function marcarTodasLeidasIndex() {
        fetch('<?= base_url('notificaciones/marcarLeida') ?>', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({id: 0}) }).then(() => window.location.reload());
    }
</script>