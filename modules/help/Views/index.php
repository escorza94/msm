<div class="max-w-7xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-life-ring text-teal-500 mr-3"></i> Base de Conocimiento</h2>
            <p class="text-sm text-gray-500 mt-1">Preguntas Frecuentes, Políticas y Manuales internos</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="abrirModal('modal-categoria')" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 font-medium transition text-sm shadow-sm">
                <i class="fas fa-folder-plus mr-2"></i> Nueva Categoría
            </button>
            <a href="<?= base_url('help/nuevo') ?>" class="px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 font-medium transition text-sm flex items-center shadow-md">
                <i class="fas fa-plus mr-2"></i> Nuevo Artículo
            </a>
        </div>
    </div>

    <?php if(isset($_GET['success'])): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-xl flex items-center shadow-sm text-sm">
            <i class="fas fa-check-circle text-lg mr-3"></i> <div><?= htmlspecialchars($_GET['success']) ?></div>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-6">
        <div class="overflow-x-auto">
            <table id="tabla-help" class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase border-b border-gray-100">
                        <th class="p-4 font-medium">Título del Artículo</th>
                        <th class="p-4 font-medium">Categoría</th>
                        <th class="p-4 font-medium text-center">Tipo (Uso)</th>
                        <th class="p-4 font-medium text-center">Estado</th>
                        <th class="p-4 font-medium text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                    <?php if(!empty($articulos)): foreach($articulos as $a): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4">
                                <div class="font-bold text-gray-800"><?= htmlspecialchars($a['titulo']) ?></div>
                                <div class="text-[10px] text-gray-400 mt-1">Act. <?= date('d/m/Y', strtotime($a['fecha_actualizacion'])) ?></div>
                            </td>
                            <td class="p-4 text-gray-600"><i class="fas fa-folder text-gray-300 mr-1"></i> <?= htmlspecialchars($a['categoria_nombre'] ?? 'Sin categoría') ?></td>
                            <td class="p-4 text-center">
                                <?php if($a['tipo'] === 'publico'): ?>
                                    <span class="px-2 py-1 bg-indigo-50 text-indigo-600 rounded text-[10px] font-bold uppercase tooltip" title="La IA puede leerlo y compartirlo"><i class="fas fa-robot mr-1"></i> Público / IA</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-[10px] font-bold uppercase tooltip" title="Solo para asesores humanos"><i class="fas fa-user-lock mr-1"></i> Interno</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-center">
                                <a href="<?= base_url('help/cambiarEstado?id=' . $a['id']) ?>" class="px-2 py-1 rounded text-[10px] font-bold uppercase transition <?= $a['estado'] === 'activo' ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' ?>" title="Clic para cambiar">
                                    <?= $a['estado'] ?>
                                </a>
                            </td>
                            <td class="p-4 text-center flex items-center justify-center gap-2">
                                <a href="<?= base_url('help/ver?id=' . $a['id']) ?>" class="w-8 h-8 inline-flex items-center justify-center rounded bg-teal-50 text-teal-600 hover:bg-teal-100 transition tooltip" title="Leer"><i class="fas fa-book-open"></i></a>
                                <a href="<?= base_url('help/editar?id=' . $a['id']) ?>" class="w-8 h-8 inline-flex items-center justify-center rounded bg-gray-50 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition tooltip" title="Editar"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Nueva Categoría -->
<div id="modal-categoria" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 transition-opacity">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden transform scale-95 transition-transform duration-300">
        <div class="p-4 border-b border-gray-100 bg-gray-800 flex justify-between items-center">
            <h3 class="font-bold text-lg text-white"><i class="fas fa-folder-plus mr-2"></i> Nueva Categoría</h3>
            <button onclick="cerrarModal('modal-categoria')" class="text-white/80 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form action="<?= base_url('help/categoria') ?>" method="POST" class="p-5">
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Nombre de la Categoría</label>
                    <input type="text" name="nombre" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:border-teal-500" placeholder="Ej. Políticas de Envío">
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="cerrarModal('modal-categoria')" class="px-4 py-2 text-gray-500 hover:bg-gray-50 rounded-lg font-medium text-sm transition">Cancelar</button>
                <button type="submit" class="px-5 py-2 bg-teal-500 text-white rounded-lg font-bold text-sm shadow hover:bg-teal-600 transition">Crear</button>
            </div>
        </form>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<style>
    .dataTables_wrapper .dataTables_length select, .dataTables_wrapper .dataTables_filter input { border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 0.25rem 0.5rem; outline: none; margin-bottom: 1rem; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #14b8a6 !important; color: white !important; border: none !important; border-radius: 0.5rem; }
</style>

<script>
    $(document).ready(function() {
        $('#tabla-help').DataTable({ "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" }, "order": [[ 0, "asc" ]], "pageLength": 10, "columnDefs": [ { "orderable": false, "targets": [4] } ] });
    });
    
    function abrirModal(id) {
        const modal = document.getElementById(id);
        const content = modal.querySelector('div');
        modal.classList.remove('hidden');
        setTimeout(() => { content.classList.remove('scale-95'); content.classList.add('scale-100'); }, 10);
    }
    function cerrarModal(id) {
        const modal = document.getElementById(id);
        const content = modal.querySelector('div');
        content.classList.remove('scale-100'); content.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); }, 200);
    }
</script>