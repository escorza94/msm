<div class="max-w-7xl mx-auto mt-4">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-hand-holding-usd text-amber-500 mr-2"></i> Cuentas por Cobrar</h2>
            <p class="text-sm text-gray-500 mt-1">Ventas a crédito, apartados o contra entrega pendientes de pago.</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('finanzas') ?>" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition text-sm flex items-center shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i> Volver al Tablero
            </a>
        </div>
    </div>

    <!-- Lista de Deudas -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800"><i class="fas fa-file-invoice-dollar text-gray-400 mr-2"></i> Notas con Saldo Pendiente</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white text-gray-400 text-[11px] uppercase tracking-wider border-b border-gray-100">
                        <th class="p-4 font-bold w-24 text-center">Folio</th>
                        <th class="p-4 font-bold">Cliente</th>
                        <th class="p-4 font-bold text-right">Total Venta</th>
                        <th class="p-4 font-bold text-right">Abonado</th>
                        <th class="p-4 font-bold text-right text-red-500">Resta</th>
                        <th class="p-4 font-bold text-center">Estado</th>
                        <th class="p-4 font-bold text-center">Acción</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700 divide-y divide-gray-50">
                    <?php if(empty($deudas)): ?>
                        <tr><td colspan="7" class="p-10 text-center text-gray-400"><i class="fas fa-check-circle text-4xl text-green-200 mb-3 block"></i> ¡Felicidades! No hay clientes con deudas pendientes.</td></tr>
                    <?php else: foreach($deudas as $d): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 text-center font-mono font-bold text-gray-500">#<?= str_pad($d['id'], 5, '0', STR_PAD_LEFT) ?></td>
                            <td class="p-4">
                                <div class="font-bold text-gray-800"><?= htmlspecialchars($d['cliente_nombre'] ?? 'Público en General') ?></div>
                                <?php if($d['telefono']): ?><div class="text-xs text-gray-400"><i class="fas fa-phone mr-1"></i> <?= $d['telefono'] ?></div><?php endif; ?>
                            </td>
                            <td class="p-4 text-right font-medium text-gray-600">$<?= number_format($d['total'], 2) ?></td>
                            <td class="p-4 text-right font-medium text-green-600">$<?= number_format($d['monto_recibido'], 2) ?></td>
                            <td class="p-4 text-right font-black text-red-600">$<?= number_format($d['restante'], 2) ?></td>
                            <td class="p-4 text-center">
                                <span class="px-2 py-1 <?= $d['estado_pago'] === 'parcial' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' ?> rounded text-[10px] font-bold uppercase">
                                    <?= $d['estado_pago'] ?>
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <button onclick="abrirModalAbono(<?= $d['id'] ?>, <?= $d['restante'] ?>, '<?= htmlspecialchars(addslashes($d['cliente_nombre'] ?? 'Público en General')) ?>')" class="px-3 py-1 bg-green-500 text-white rounded shadow-sm hover:bg-green-600 font-bold text-xs">
                                    <i class="fas fa-plus mr-1"></i> Abono
                                </button>
                                <a href="<?= base_url('pos/ver?id=' . $d['id']) ?>" class="ml-1 px-3 py-1 bg-gray-100 text-gray-600 rounded hover:bg-gray-200 text-xs tooltip" title="Ver Nota"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Registrar Abono -->
<div id="modal-abono" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 transition-opacity">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden transform scale-95 transition-transform duration-300">
        <div class="p-4 border-b border-gray-100 bg-amber-500 flex justify-between items-center">
            <h3 class="font-bold text-lg text-white"><i class="fas fa-hand-holding-usd mr-2"></i> Registrar Abono</h3>
            <button onclick="cerrarModal('modal-abono')" class="text-white/80 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form action="<?= base_url('finanzas/abono') ?>" method="POST" class="p-5">
            <input type="hidden" name="venta_id" id="abono-venta-id">
            
            <div class="mb-4 bg-amber-50 p-3 rounded-lg border border-amber-100">
                <p class="text-xs text-amber-700 uppercase font-bold tracking-wider mb-1">Cliente</p>
                <p class="font-bold text-gray-800" id="abono-cliente-nombre">-</p>
                <div class="flex justify-between items-end mt-2 pt-2 border-t border-amber-200/50">
                    <span class="text-xs text-amber-700 font-medium">Restante por pagar:</span>
                    <span class="font-black text-red-600 text-lg" id="abono-restante">$0.00</span>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Monto del Abono ($)</label>
                    <input type="number" name="monto" id="abono-monto" required min="0.01" step="0.01" class="w-full text-2xl font-black text-green-600 border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-amber-500 text-center" placeholder="0.00">
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Método Pago</label>
                        <select name="metodo_pago" id="metodo_pago" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none bg-white focus:border-amber-500" onchange="autoSeleccionarCuenta()">
                            <option value="Efectivo">Efectivo</option>
                            <option value="Tarjeta">Tarjeta (Créd./Déb.)</option>
                            <option value="Transferencia">Transferencia</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Caja Destino</label>
                        <select name="cuenta_id" id="cuenta_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none bg-white focus:border-amber-500">
                            <?php foreach($cuentas as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="cerrarModal('modal-abono')" class="px-4 py-2 text-gray-500 hover:bg-gray-50 rounded-lg font-medium text-sm transition">Cancelar</button>
                <button type="submit" class="px-5 py-2 bg-amber-500 text-white rounded-lg font-bold text-sm shadow hover:bg-amber-600 transition">Guardar Abono</button>
            </div>
        </form>
    </div>
</div>

<script>
const cuentasFinanzas = <?= json_encode($cuentas ?? []) ?>;

function abrirModalAbono(venta_id, restante, cliente_nombre) {
    document.getElementById('abono-venta-id').value = venta_id;
    document.getElementById('abono-restante').innerText = '$' + restante.toLocaleString('en-US', {minimumFractionDigits: 2});
    document.getElementById('abono-monto').value = restante; // Por defecto sugiere liquidar todo
    document.getElementById('abono-monto').max = restante; // Evita cobrar más del saldo deudor
    document.getElementById('abono-cliente-nombre').innerText = cliente_nombre;
    abrirModal('modal-abono');
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

function autoSeleccionarCuenta() {
    const metodo = document.getElementById('metodo_pago').value;
    const selectCuenta = document.getElementById('cuenta_id');
    let tipoBuscado = 'efectivo';
    if (metodo === 'Tarjeta') tipoBuscado = 'terminal';
    if (metodo === 'Transferencia') tipoBuscado = 'banco';
    
    const cuentaEncontrada = cuentasFinanzas.find(c => c.tipo === tipoBuscado);
    if (cuentaEncontrada) selectCuenta.value = cuentaEncontrada.id;
}
</script>