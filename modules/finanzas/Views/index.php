<div class="max-w-7xl mx-auto mt-4">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-wallet text-green-500 mr-2"></i> Finanzas y Libro Mayor</h2>
            <p class="text-sm text-gray-500 mt-1">Control de cuentas, caja, ingresos y egresos.</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('finanzas/cobrar') ?>" class="px-4 py-2 bg-amber-50 border border-amber-100 text-amber-700 rounded-lg hover:bg-amber-100 font-medium transition text-sm flex items-center shadow-sm">
                <i class="fas fa-hand-holding-usd mr-2"></i> Cuentas por Cobrar
            </a>
            <button onclick="abrirModal('modal-cuenta')" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition text-sm flex items-center shadow-sm">
                <i class="fas fa-plus mr-2 text-gray-400"></i> Nueva Cuenta
            </button>
            <button onclick="abrirModal('modal-traspaso')" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition text-sm flex items-center shadow-sm">
                <i class="fas fa-exchange-alt mr-2 text-blue-500"></i> Traspaso
            </button>
            <button onclick="abrirModalMovimiento('egreso')" class="px-4 py-2 bg-red-50 border border-red-100 text-red-600 rounded-lg hover:bg-red-100 font-medium transition text-sm flex items-center shadow-sm">
                <i class="fas fa-arrow-down mr-2"></i> Nuevo Egreso
            </button>
            <button onclick="abrirModalMovimiento('ingreso')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition text-sm flex items-center shadow-md">
                <i class="fas fa-arrow-up mr-2"></i> Nuevo Ingreso
            </button>
        </div>
    </div>

    <!-- Resumen Total -->
    <div class="bg-gradient-to-r from-gray-800 to-gray-900 rounded-2xl p-6 text-white shadow-lg mb-6 flex justify-between items-center">
        <div>
            <p class="text-gray-400 text-sm font-bold uppercase tracking-wider mb-1">Saldo Total en Cuentas</p>
            <h3 class="text-4xl font-black tracking-tight">$<?= number_format($total_general, 2) ?></h3>
        </div>
        <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center text-3xl">
            <i class="fas fa-coins text-yellow-400"></i>
        </div>
    </div>

    <!-- Tarjetas de Cuentas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <?php foreach($cuentas as $c): ?>
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-sm">
                            <i class="fas <?= $c['tipo'] == 'efectivo' ? 'fa-cash-register' : ($c['tipo'] == 'banco' ? 'fa-university' : 'fa-credit-card') ?>"></i>
                        </div>
                        <h4 class="font-bold text-gray-800"><?= htmlspecialchars($c['nombre']) ?></h4>
                    </div>
                    <span class="text-[10px] font-bold uppercase px-2 py-1 bg-gray-100 text-gray-500 rounded-md"><?= $c['tipo'] ?></span>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Saldo Actual</p>
                    <p class="text-2xl font-black text-gray-800">$<?= number_format($c['saldo_actual'], 2) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Tabla de Últimos Movimientos -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800"><i class="fas fa-list text-gray-400 mr-2"></i> Historial Reciente (Libro Mayor)</h3>
        </div>
        <div class="overflow-x-auto">
            <table id="tabla-movimientos" class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white text-gray-400 text-[11px] uppercase tracking-wider border-b border-gray-100">
                        <th class="p-4 font-bold w-32">Fecha</th>
                        <th class="p-4 font-bold">Concepto / Referencia</th>
                        <th class="p-4 font-bold">Cuenta</th>
                        <th class="p-4 font-bold">Categoría</th>
                        <th class="p-4 font-bold text-right">Monto</th>
                        <th class="p-4 font-bold text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700 divide-y divide-gray-50">
                    <?php if(empty($movimientos)): ?>
                        <tr><td colspan="6" class="p-10 text-center text-gray-400">No hay movimientos registrados.</td></tr>
                    <?php else: foreach($movimientos as $m): 
                        $es_ingreso = $m['tipo'] === 'ingreso';
                    ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 whitespace-nowrap">
                                <div class="font-medium text-gray-800"><?= date('d/m/Y', strtotime($m['fecha_movimiento'])) ?></div>
                                <div class="text-xs text-gray-400"><?= date('h:i A', strtotime($m['fecha_movimiento'])) ?></div>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-gray-800 flex items-center">
                                    <?= htmlspecialchars($m['concepto']) ?>
                                    <?php if($m['origen_tipo'] === 'venta'): ?>
                                        <a href="<?= base_url('pos/ver?id=' . $m['origen_id']) ?>" class="ml-2 text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded font-mono hover:bg-blue-100"><i class="fas fa-external-link-alt"></i> Ver Venta</a>
                                    <?php endif; ?>
                                </div>
                                <?php if($m['metodo_pago']): ?>
                                    <div class="text-xs text-gray-500 mt-1"><i class="fas fa-money-check-alt mr-1"></i> <?= htmlspecialchars($m['metodo_pago']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 font-medium text-gray-600"><?= htmlspecialchars($m['cuenta_nombre']) ?></td>
                            <td class="p-4"><span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-md"><?= htmlspecialchars($m['categoria_nombre'] ?? 'Sin categoría') ?></span></td>
                            <td class="p-4 text-right font-black <?= $es_ingreso ? 'text-green-600' : 'text-red-500' ?>">
                                <?= $es_ingreso ? '+' : '-' ?>$<?= number_format($m['monto'], 2) ?>
                            </td>
                            <td class="p-4 text-center">
                                <a href="<?= base_url('finanzas/ver_movimiento?id=' . $m['id']) ?>" class="w-8 h-8 inline-flex items-center justify-center rounded bg-blue-50 text-blue-600 hover:bg-blue-100 transition tooltip" title="Ver Detalle"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Movimiento (Ingreso / Egreso) -->
<div id="modal-movimiento" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 transition-opacity">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden transform scale-95 transition-transform duration-300">
        <div class="p-4 border-b border-gray-100 flex justify-between items-center" id="modal-mov-header">
            <h3 class="font-bold text-lg text-white" id="modal-mov-title">Nuevo Movimiento</h3>
            <button onclick="cerrarModal('modal-movimiento')" class="text-white/80 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form action="<?= base_url('finanzas/movimiento') ?>" method="POST" class="p-5">
            <input type="hidden" name="tipo" id="input-tipo-mov">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Monto ($)</label>
                    <input type="number" name="monto" required min="0.01" step="0.01" class="w-full text-2xl font-black text-gray-800 border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-blue-500" placeholder="0.00">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Cuenta Afectada</label>
                    <select name="cuenta_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none bg-white focus:border-blue-500">
                        <option value="">Seleccione cuenta...</option>
                        <?php foreach($cuentas as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?> (Saldo: $<?= number_format($c['saldo_actual'], 2) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Categoría</label>
                    <select name="categoria_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none bg-white focus:border-blue-500">
                        <option value="">Ninguna</option>
                        <?php foreach($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Concepto / Descripción</label>
                    <input type="text" name="concepto" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:border-blue-500" placeholder="Ej. Pago de Internet...">
                </div>
            </div>
            
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="cerrarModal('modal-movimiento')" class="px-4 py-2 text-gray-500 hover:bg-gray-50 rounded-lg font-medium text-sm transition">Cancelar</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg font-bold text-sm shadow hover:bg-blue-700 transition" id="btn-save-mov">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Traspaso -->
<div id="modal-traspaso" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 transition-opacity">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden transform scale-95 transition-transform duration-300">
        <div class="p-4 border-b border-gray-100 bg-blue-600 flex justify-between items-center">
            <h3 class="font-bold text-lg text-white"><i class="fas fa-exchange-alt mr-2"></i> Traspaso entre Cuentas</h3>
            <button onclick="cerrarModal('modal-traspaso')" class="text-white/80 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form action="<?= base_url('finanzas/traspaso') ?>" method="POST" class="p-5">
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 text-red-500">De la Cuenta (Origen - Salida)</label>
                    <select name="cuenta_origen" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none bg-white focus:border-blue-500">
                        <option value="">Seleccione origen...</option>
                        <?php foreach($cuentas as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?> (Disponible: $<?= number_format($c['saldo_actual'], 2) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex justify-center text-gray-300"><i class="fas fa-arrow-down text-xl"></i></div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 text-green-600">A la Cuenta (Destino - Entrada)</label>
                    <select name="cuenta_destino" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none bg-white focus:border-blue-500">
                        <option value="">Seleccione destino...</option>
                        <?php foreach($cuentas as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Monto a Transferir ($)</label>
                    <input type="number" name="monto" required min="0.01" step="0.01" class="w-full text-2xl font-black text-gray-800 border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-blue-500 text-center" placeholder="0.00">
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="cerrarModal('modal-traspaso')" class="px-4 py-2 text-gray-500 hover:bg-gray-50 rounded-lg font-medium text-sm transition">Cancelar</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg font-bold text-sm shadow hover:bg-blue-700 transition">Procesar Traspaso</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Nueva Cuenta -->
<div id="modal-cuenta" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 transition-opacity">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden transform scale-95 transition-transform duration-300">
        <div class="p-4 border-b border-gray-100 bg-gray-800 flex justify-between items-center">
            <h3 class="font-bold text-lg text-white"><i class="fas fa-plus-circle mr-2"></i> Nueva Cuenta</h3>
            <button onclick="cerrarModal('modal-cuenta')" class="text-white/80 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form action="<?= base_url('finanzas/cuenta') ?>" method="POST" class="p-5">
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Nombre de la Cuenta</label>
                    <input type="text" name="nombre" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:border-blue-500" placeholder="Ej. Caja Secundaria...">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Tipo</label>
                    <select name="tipo" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none bg-white focus:border-blue-500">
                        <option value="efectivo">Efectivo (Caja Física)</option>
                        <option value="banco">Cuenta Bancaria</option>
                        <option value="terminal">Terminal / Punto de Venta</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Saldo Inicial ($)</label>
                    <input type="number" name="saldo_inicial" required min="0" step="0.01" value="0.00" class="w-full text-xl font-black text-gray-800 border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-blue-500 text-center">
                    <p class="text-[10px] text-gray-400 mt-1 text-center">Con cuánto dinero arranca esta cuenta en el sistema.</p>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="cerrarModal('modal-cuenta')" class="px-4 py-2 text-gray-500 hover:bg-gray-50 rounded-lg font-medium text-sm transition">Cancelar</button>
                <button type="submit" class="px-5 py-2 bg-gray-800 text-white rounded-lg font-bold text-sm shadow hover:bg-gray-900 transition">Crear Cuenta</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalMovimiento(tipo) {
    document.getElementById('input-tipo-mov').value = tipo;
    document.getElementById('modal-mov-title').innerText = tipo === 'ingreso' ? 'Nuevo Ingreso de Dinero' : 'Nuevo Egreso (Gasto)';
    document.getElementById('modal-mov-header').className = tipo === 'ingreso' ? 'p-4 border-b border-gray-100 flex justify-between items-center bg-green-600' : 'p-4 border-b border-gray-100 flex justify-between items-center bg-red-600';
    document.getElementById('btn-save-mov').className = tipo === 'ingreso' ? 'px-5 py-2 bg-green-600 text-white rounded-lg font-bold text-sm shadow hover:bg-green-700 transition' : 'px-5 py-2 bg-red-600 text-white rounded-lg font-bold text-sm shadow hover:bg-red-700 transition';
    abrirModal('modal-movimiento');
}

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

<!-- Requisitos de DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<style>
    /* Integración visual de DataTables con Tailwind */
    .dataTables_wrapper .dataTables_length select, .dataTables_wrapper .dataTables_filter input { border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 0.25rem 0.5rem; outline: none; margin-bottom: 1rem; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #16a34a !important; color: white !important; border: none !important; border-radius: 0.5rem; }
</style>

<script>
    $(document).ready(function() {
        $('#tabla-movimientos').DataTable({ 
            "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" }, 
            "order": [[ 0, "desc" ]], 
            "pageLength": 25, 
            "columnDefs": [ { "orderable": false, "targets": [5] } ] 
        });
    });
</script>
