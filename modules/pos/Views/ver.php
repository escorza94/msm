<div class="max-w-4xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                <?php if($venta['tipo'] === 'cotizacion'): ?>
                    <i class="fas fa-file-alt text-amber-500 mr-3"></i> Cotización #<?= str_pad($venta['id'], 5, '0', STR_PAD_LEFT) ?>
                <?php else: ?>
                    <i class="fas fa-check-circle text-green-500 mr-3"></i> Nota de Venta #<?= str_pad($venta['id'], 5, '0', STR_PAD_LEFT) ?>
                    <span class="ml-3 px-2 py-1 text-[10px] uppercase font-bold rounded <?= ($venta['estado_entrega'] ?? 'pendiente') === 'entregado' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' ?> shadow-sm">
                        <i class="fas <?= ($venta['estado_entrega'] ?? 'pendiente') === 'entregado' ? 'fa-box-open' : 'fa-box' ?> mr-1"></i> Entrega: <?= $venta['estado_entrega'] ?? 'pendiente' ?>
                    </span>
                <?php endif; ?>
            </h2>
            <p class="text-sm text-gray-500 mt-1">Fecha: <?= date('d/m/Y h:i A', strtotime($venta['fecha_creacion'])) ?> | Atendió: <span class="font-medium text-gray-700"><?= htmlspecialchars($venta['usuario_nombre'] ?? 'Sistema') ?></span></p>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('pos/historial') ?>" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 font-medium transition text-sm flex items-center shadow-sm">
                <i class="fas fa-arrow-left md:mr-2"></i> <span class="hidden md:inline">Volver</span>
            </a>
            
            <!-- Menú Desplegable Documentos -->
            <div class="relative group">
                <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium transition text-sm flex items-center shadow-sm">
                    <i class="fas fa-print md:mr-2"></i> <span class="hidden md:inline">Documentos</span> <i class="fas fa-chevron-down ml-2 text-[10px]"></i>
                </button>
                <div class="absolute right-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden z-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                    <button onclick="abrirModalImpresion('<?= base_url('documentos/ticket?id=' . $venta['id']) ?>')" class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 flex items-center transition">
                        <i class="fas fa-receipt w-5 text-gray-400"></i> Ticket Térmico
                    </button>
                    <button onclick="Swal.fire('Próximamente', 'La generación de PDF en formato A4 estará disponible pronto.', 'info')" class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 flex items-center transition border-t border-gray-50">
                        <i class="fas fa-file-pdf w-5 text-red-400"></i> Formato PDF (A4)
                    </button>
                </div>
            </div>

            <?php if($venta['tipo'] === 'cotizacion'): ?>
                <button onclick="Swal.fire('Próximamente', 'La conversión de cotización a venta estará disponible pronto.', 'info')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition text-sm flex items-center shadow-md">
                    <i class="fas fa-exchange-alt mr-2"></i> Convertir a Venta
                </button>
            <?php endif; ?>
            <?php if($venta['tipo'] === 'venta' && ($venta['estado_entrega'] ?? 'pendiente') === 'pendiente'): ?>
                <button onclick="confirmarEntrega(<?= $venta['id'] ?>)" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold transition text-sm flex items-center shadow-md">
                    <i class="fas fa-box-open mr-2"></i> Entregar Mercancía
                </button>
            <?php endif; ?>
        </div>
    </div>

    <?php if(isset($_GET['success'])): ?>
        <!-- Toast de Éxito Flotante con SweetAlert -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if(typeof Swal !== 'undefined') {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: '<?= htmlspecialchars($_GET['success']) ?>', showConfirmButton: false, timer: 3500 });
                }
            });
        </script>
    <?php endif; ?>

    <!-- Tarjeta: Datos del Cliente -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800"><i class="fas fa-user text-blue-400 mr-2"></i> Datos del Cliente</h3>
        </div>
        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-400 text-[10px] uppercase tracking-wider font-bold mb-1">Nombre / Razón Social</p>
                <p class="font-bold text-gray-800 text-base"><?= htmlspecialchars($venta['cliente_nombre'] ?? 'Público en General') ?></p>
            </div>
            <?php if($venta['cliente_id']): ?>
                <div>
                    <p class="text-gray-400 text-[10px] uppercase tracking-wider font-bold mb-1">Teléfono Móvil</p>
                    <p class="text-gray-800"><?= htmlspecialchars($venta['cliente_telefono'] ?: 'N/A') ?></p>
                </div>
                <div>
                    <p class="text-gray-400 text-[10px] uppercase tracking-wider font-bold mb-1">Correo Electrónico</p>
                    <p class="text-gray-800"><?= htmlspecialchars($venta['cliente_correo'] ?: 'N/A') ?></p>
                </div>
                <?php if(strpos($venta['notas_internas'] ?? '', '[ATENCIÓN: ENTREGA EN DIRECCIÓN DIFERENTE AL CLIENTE]') === false): ?>
                    <div>
                        <p class="text-gray-400 text-[10px] uppercase tracking-wider font-bold mb-1">Dirección de Entrega</p>
                        <p class="text-gray-800"><?= htmlspecialchars($venta['cliente_direccion'] ?: 'N/A') ?></p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <?php if(strpos($venta['notas_internas'] ?? '', '[ATENCIÓN: ENTREGA EN DIRECCIÓN DIFERENTE AL CLIENTE]') !== false): ?>
            <div class="p-3 bg-amber-50 border-t border-amber-100 flex items-start gap-3">
                <i class="fas fa-map-marker-alt text-amber-500 mt-0.5 text-lg"></i>
                <div>
                    <p class="text-xs font-bold text-amber-800 uppercase">Dirección de Envío Personalizada</p>
                    <p class="text-xs text-amber-700 mt-1">La dirección de entrega es diferente a la del cliente. Revisa las <b>Notas Internas</b> o la sección de <b>Logística</b> más abajo para ver el destino y las coordenadas exactas.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if(isset($envios) && count($envios) > 0): ?>
    <!-- Tarjeta: Información de Envío (Logística) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-100 bg-blue-50/50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800"><i class="fas fa-truck-fast text-blue-500 mr-2"></i> Logística y Envíos</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white text-gray-400 text-[10px] uppercase border-b border-gray-100 tracking-wider">
                        <th class="p-3 font-medium text-center">Estado</th>
                        <th class="p-3 font-medium">Destino</th>
                        <th class="p-3 font-medium">Chofer / Asignado</th>
                        <th class="p-3 font-medium text-center">Registro</th>
                        <th class="p-3 font-medium text-center">Entrega</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700 divide-y divide-gray-50">
                    <?php foreach($envios as $e): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-3 text-center">
                                <?php if($e['estado'] === 'entregado'): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-[10px] font-bold uppercase"><i class="fas fa-check-double mr-1"></i> Entregado</span>
                                <?php elseif($e['estado'] === 'en_ruta'): ?>
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-[10px] font-bold uppercase"><i class="fas fa-truck-fast mr-1"></i> En Ruta</span>
                                <?php elseif($e['estado'] === 'cancelado'): ?>
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-[10px] font-bold uppercase"><i class="fas fa-times mr-1"></i> Cancelado</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded text-[10px] font-bold uppercase"><i class="fas fa-clock mr-1"></i> Pendiente</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3">
                                <?php if($e['direccion_destino']): ?>
                                    <div class="text-xs text-gray-800 mb-1 font-medium"><?= htmlspecialchars($e['direccion_destino']) ?></div>
                                <?php endif; ?>
                                <?php if($e['coordenadas_destino']): ?>
                                    <a href="https://www.google.com/maps/search/?api=1&query=<?= $e['coordenadas_destino'] ?>" target="_blank" class="text-xs text-blue-500 hover:text-blue-700 font-medium"><i class="fas fa-map-marker-alt mr-1"></i> Ver en Mapa</a>
                                    <div class="text-[10px] text-gray-400 font-mono mt-1"><?= $e['coordenadas_destino'] ?></div>
                                <?php else: ?>
                                    <span class="text-xs text-amber-500"><i class="fas fa-exclamation-triangle mr-1"></i> Sin coordenadas GPS</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3 text-xs"><?= htmlspecialchars($e['chofer_nombre'] ?? 'Aún no asignado') ?></td>
                            <td class="p-3 text-center text-xs text-gray-500"><?= date('d/m/Y h:i A', strtotime($e['fecha_creacion'])) ?></td>
                            <td class="p-3 text-center text-xs font-medium <?= $e['fecha_entrega'] ? 'text-green-600' : 'text-gray-400 italic' ?>"><?= $e['fecha_entrega'] ? date('d/m/Y h:i A', strtotime($e['fecha_entrega'])) : 'Pendiente' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <?php
    $notas_limpias = $venta['notas_internas'] ?? '';
    $promos_aplicadas = '';
    
    // Extraer automáticamente las promociones si existen
    if (strpos($notas_limpias, '[PROMOCIONES APLICADAS]') !== false) {
        $partes = explode('[PROMOCIONES APLICADAS]', $notas_limpias);
        $notas_limpias = trim($partes[0]);
        $promos_aplicadas = trim($partes[1]);
    }
    ?>

    <!-- Tarjeta: Detalles de la Orden -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50">
            <h3 class="font-bold text-gray-800"><i class="fas fa-box-open text-blue-400 mr-2"></i> Detalles de la Orden</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white text-gray-400 text-xs uppercase border-b border-gray-100">
                        <th class="p-4 font-medium w-16 text-center">Cant.</th>
                        <th class="p-4 font-medium">Producto</th>
                        <th class="p-4 font-medium text-right">Precio Unitario</th>
                        <th class="p-4 font-medium text-right">Importe</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700 divide-y divide-gray-50">
                    <?php foreach($detalles as $d): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 text-center font-bold text-gray-800 text-base"><?= $d['cantidad'] ?></td>
                            <td class="p-4">
                                <div class="font-bold text-gray-800"><?= htmlspecialchars($d['producto_nombre'] ?? 'Producto Desconocido') ?></div>
                                <div class="text-[10px] text-gray-400 font-mono"><?= htmlspecialchars($d['sku'] ?? 'N/A') ?></div>
                            </td>
                            <td class="p-4 text-right text-gray-500 font-medium">$<?= number_format($d['precio_unitario'], 2) ?></td>
                            <td class="p-4 text-right font-bold text-gray-800">$<?= number_format($d['subtotal'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="p-6 border-t border-gray-100 bg-gray-50 grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Lado Izquierdo (Notas) -->
            <div>
                <?php if(!empty($notas_limpias)): ?>
                    <h4 class="text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-2"><i class="fas fa-sticky-note mr-1"></i> Notas Internas</h4>
                    <p class="text-sm text-gray-600 bg-white p-3 rounded-lg border border-gray-200 shadow-sm leading-relaxed"><?= nl2br(htmlspecialchars($notas_limpias)) ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Lado Derecho (Totales) -->
            <div class="space-y-3 text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal</span>
                    <span class="font-medium">$<?= number_format($venta['subtotal'], 2) ?></span>
                </div>
                <?php if($venta['costo_envio'] > 0): ?>
                    <div class="flex justify-between text-gray-600">
                        <span>Costo de Envío <i class="fas fa-truck text-gray-400 ml-1"></i></span>
                        <span class="font-medium text-blue-500">+$<?= number_format($venta['costo_envio'], 2) ?></span>
                    </div>
                <?php endif; ?>
                <?php if($venta['descuento'] > 0): ?>
                    <div class="flex justify-between text-red-500">
                        <span>Descuento <i class="fas fa-tags ml-1"></i></span>
                        <span class="font-bold">-$<?= number_format($venta['descuento'], 2) ?></span>
                    </div>
                    <?php if(!empty($promos_aplicadas)): ?>
                    <div class="text-[11px] text-red-400 ml-4 mb-2 font-medium">
                        <?= nl2br(htmlspecialchars($promos_aplicadas)) ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
                <div class="pt-3 mt-3 border-t border-gray-200 flex justify-between items-center">
                    <span class="text-lg font-bold text-gray-800">Total a Pagar</span>
                    <span class="text-3xl font-black text-blue-600 tracking-tight">$<?= number_format($venta['total'], 2) ?></span>
                </div>
                
                <?php if($venta['tipo'] === 'venta'): 
                    $monto_recibido = $venta['monto_recibido'] ?? 0;
                    $restante = max(0, $venta['total'] - $monto_recibido);
                    $suma_abonos = isset($abonos) ? array_sum(array_column($abonos, 'monto')) : 0;
                    $enganche = max(0, $monto_recibido - $suma_abonos);
                ?>
                <div class="mt-4 pt-3 border-t border-gray-200">
                    <p class="text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-2">Resumen de Pagos</p>
                    
                    <div class="flex justify-between items-center text-sm mb-1">
                        <span class="text-gray-600">Pago Inicial / Enganche</span>
                        <span class="font-bold text-green-600">$<?= number_format($enganche, 2) ?></span>
                    </div>
                    
                    <?php if(isset($abonos) && count($abonos) > 0): foreach($abonos as $a): ?>
                    <div class="flex justify-between items-center text-sm mb-1">
                        <span class="text-gray-500"><i class="fas fa-hand-holding-usd text-green-500 text-[10px] mr-1"></i> Abono (<?= date('d/m/Y', strtotime($a['fecha_abono'])) ?>) <span class="text-[10px] bg-gray-100 px-1.5 py-0.5 rounded ml-1"><?= htmlspecialchars($a['cuenta_nombre'] ?? 'Sin cuenta') ?></span></span>
                        <span class="font-bold text-green-600">+$<?= number_format($a['monto'], 2) ?></span>
                    </div>
                    <?php endforeach; endif; ?>

                    <?php if(isset($venta['cambio']) && $venta['cambio'] > 0): ?>
                    <div class="flex justify-between items-center text-sm mb-1">
                        <span class="text-gray-600">Cambio Devuelto</span>
                        <span class="font-medium text-gray-500">$<?= number_format($venta['cambio'], 2) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($restante > 0): ?>
                    <div class="flex justify-between items-center text-sm mb-2 p-2 bg-red-50 rounded border border-red-100">
                        <span class="font-bold text-red-600">Resta por Pagar</span>
                        <div class="flex flex-col sm:flex-row items-end sm:items-center gap-2 sm:gap-3">
                            <span class="font-black text-red-600">$<?= number_format($restante, 2) ?></span>
                            <button type="button" onclick="abrirModalAbono(<?= $venta['id'] ?>, <?= $restante ?>, '<?= htmlspecialchars(addslashes($venta['cliente_nombre'] ?? 'Público en General')) ?>')" class="px-3 py-1 bg-green-500 text-white rounded shadow-sm hover:bg-green-600 font-bold text-xs flex items-center">
                                <i class="fas fa-plus mr-1"></i> Abonar
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="mt-3 font-medium text-gray-700 flex items-center bg-gray-50 p-2 rounded border border-gray-100">
                        <i class="fas fa-money-check-alt text-green-500 mr-2"></i> <?= htmlspecialchars($venta['metodo_pago'] ?? 'Efectivo') ?>
                        <span class="ml-2 px-2 py-0.5 text-[10px] font-bold uppercase rounded <?= $venta['estado_pago'] === 'pagado' ? 'bg-green-100 text-green-700' : ($venta['estado_pago'] === 'parcial' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') ?>">
                            <?= $venta['estado_pago'] ?>
                        </span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
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
            <!-- Input dinámico para que el controlador sepa a donde volver -->
            <input type="hidden" name="redirect_to" value="pos/ver?id=<?= $venta['id'] ?>">
            
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
                            <?php if(isset($cuentas_finanzas)): foreach($cuentas_finanzas as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                            <?php endforeach; endif; ?>
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

<!-- Modal Impresión de Ticket -->
<div id="modal-impresion" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 transition-opacity">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden transform scale-95 transition-transform duration-300 flex flex-col" style="height: 80vh;">
        <div class="p-4 border-b border-gray-100 bg-gray-800 flex justify-between items-center">
            <h3 class="font-bold text-lg text-white"><i class="fas fa-print mr-2"></i> Imprimir Ticket</h3>
            <button onclick="cerrarModalImpresion()" class="text-white/80 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <div class="flex-1 bg-gray-100 p-4 overflow-hidden flex flex-col">
            <iframe id="iframe-impresion" class="w-full h-full bg-white rounded border border-gray-300 shadow-sm" src=""></iframe>
        </div>
        <div class="p-4 border-t border-gray-100 flex justify-end gap-3 bg-white">
            <button type="button" onclick="cerrarModalImpresion()" class="px-4 py-2 text-gray-500 hover:bg-gray-50 rounded-lg font-medium text-sm transition">Cerrar</button>
            <button type="button" onclick="imprimirIframe()" class="px-5 py-2 bg-gray-800 text-white rounded-lg font-bold text-sm shadow hover:bg-gray-900 transition"><i class="fas fa-print mr-2"></i> Re-Imprimir</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const cuentasFinanzas = <?= json_encode($cuentas_finanzas ?? []) ?>;

function abrirModalAbono(venta_id, restante, cliente_nombre) {
    document.getElementById('abono-venta-id').value = venta_id;
    document.getElementById('abono-restante').innerText = '$' + restante.toLocaleString('en-US', {minimumFractionDigits: 2});
    document.getElementById('abono-monto').value = restante; 
    document.getElementById('abono-monto').max = restante; 
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

function abrirModalImpresion(url) {
    document.getElementById('iframe-impresion').src = url;
    const modal = document.getElementById('modal-impresion');
    const content = modal.querySelector('div');
    modal.classList.remove('hidden');
    setTimeout(() => { content.classList.remove('scale-95'); content.classList.add('scale-100'); }, 10);
}

function cerrarModalImpresion() {
    const modal = document.getElementById('modal-impresion');
    const content = modal.querySelector('div');
    content.classList.remove('scale-100'); content.classList.add('scale-95');
    setTimeout(() => { modal.classList.add('hidden'); document.getElementById('iframe-impresion').src = ''; }, 200);
}

function confirmarEntrega(id) {
    Swal.fire({
        title: '¿Confirmar Entrega?',
        text: "La mercancía se marcará como entregada al cliente. Si había un envío en ruta pendiente, se dará por concluido.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#9ca3af',
        confirmButtonText: '<i class="fas fa-check mr-2"></i> Sí, entregar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?= base_url('pos/marcarEntregado?id=') ?>' + id;
        }
    });
}

function imprimirIframe() {
    const iframe = document.getElementById('iframe-impresion');
    if (iframe.contentWindow) iframe.contentWindow.print();
}
</script>
