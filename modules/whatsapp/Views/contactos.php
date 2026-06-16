<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mx-auto max-w-5xl mt-6">
    <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
        <h3 class="font-bold text-gray-800 flex items-center">
            <i class="fas fa-address-book text-indigo-500 mr-2 text-lg"></i> Directorio de Contactos
        </h3>
        <a href="<?= base_url('whatsapp') ?>" class="text-sm bg-white border border-gray-200 text-gray-600 px-3 py-1.5 rounded-lg font-medium hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left mr-1"></i> Volver al Panel
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase border-b border-gray-100">
                    <th class="p-3 font-medium w-12 text-center">ID</th>
                    <th class="p-3 font-medium">Nombre de Contacto</th>
                    <th class="p-3 font-medium">Número / WhatsApp ID</th>
                    <th class="p-3 font-medium">Etiqueta</th>
                    <th class="p-3 font-medium text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                <?php if(empty($contactos)): ?>
                    <tr><td colspan="5" class="p-8 text-center text-gray-400">No hay contactos registrados en la base de datos.</td></tr>
                <?php else: foreach($contactos as $c): 
                    $isGroup = ($c['tipo_chat'] ?? '') === 'grupo' || strpos($c['whatsapp_id'], '@g.us') !== false;
                    $isStatus = ($c['tipo_chat'] ?? '') === 'estado' || $c['whatsapp_id'] === 'status@broadcast';
                    $icono = $isStatus ? 'fa-bullhorn text-purple-500' : ($isGroup ? 'fa-users text-indigo-500' : 'fa-user text-gray-400');
                ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-3 text-center text-gray-400 text-xs font-mono"><?= $c['id'] ?></td>
                        <td class="p-3 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center"><i class="fas <?= $icono ?>"></i></div>
                            <span class="font-medium"><?= htmlspecialchars($c['nombre']) ?></span>
                        </td>
                        <td class="p-3 text-gray-500 font-mono text-xs"><?= htmlspecialchars($c['whatsapp_id']) ?></td>
                        <td class="p-3">
                            <?php if(!empty($c['etiqueta'])): ?>
                                <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded text-[10px] font-bold uppercase"><?= htmlspecialchars($c['etiqueta']) ?></span>
                            <?php else: ?>
                                <span class="text-gray-300 italic text-xs">Sin etiqueta</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-3 text-center">
                            <a href="<?= base_url('whatsapp/chat') ?>" class="inline-flex items-center justify-center w-8 h-8 rounded bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition tooltip" title="Ir a mensajes">
                                <i class="fas fa-comment-dots"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>