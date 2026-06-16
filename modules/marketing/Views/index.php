<div class="max-w-7xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-bullhorn text-pink-500 mr-3"></i> Campañas y Pautas</h2>
            <p class="text-sm text-gray-500 mt-1">Intercepta mensajes de anuncios (Facebook/IG) para responder o etiquetar automáticamente.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= base_url('marketing/nuevo') ?>" class="px-4 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 font-medium transition text-sm flex items-center shadow-md">
                <i class="fas fa-plus mr-2"></i> Nueva Regla
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
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase border-b border-gray-100">
                        <th class="p-4 font-medium">Campaña / Pauta</th>
                        <th class="p-4 font-medium">Disparador (Mensaje Exacto)</th>
                        <th class="p-4 font-medium text-center">IA Asignada</th>
                        <th class="p-4 font-medium text-center">Estado</th>
                        <th class="p-4 font-medium text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                    <?php if(empty($campanas)): ?>
                        <tr><td colspan="5" class="p-8 text-center text-gray-400">No hay reglas de pautas registradas.</td></tr>
                    <?php else: foreach($campanas as $c): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4">
                                <div class="font-bold text-gray-800"><?= htmlspecialchars($c['nombre']) ?></div>
                                <?php if($c['etiqueta_contacto']): ?>
                                    <div class="inline-block mt-1 px-2 py-0.5 bg-blue-50 text-blue-600 font-bold text-[10px] rounded uppercase border border-blue-100">Etiqueta: <?= htmlspecialchars($c['etiqueta_contacto']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="p-4">
                                <div class="text-xs bg-gray-100 p-2 rounded text-gray-600 font-mono inline-block">"<?= htmlspecialchars($c['texto_disparador']) ?>"</div>
                            </td>
                            <td class="p-4 text-center">
                                <?php if($c['activar_bot']): ?>
                                    <span class="text-green-600 text-xs font-bold"><i class="fas fa-robot"></i> Activa</span>
                                <?php else: ?>
                                    <span class="text-gray-400 text-xs font-bold"><i class="fas fa-user-slash"></i> Pausada</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-center">
                                <a href="<?= base_url('marketing/cambiarEstado?id=' . $c['id']) ?>" class="px-2 py-1 rounded text-[10px] font-bold uppercase transition <?= $c['estado'] === 'activo' ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' ?>"><?= $c['estado'] ?></a>
                            </td>
                            <td class="p-4 text-center flex items-center justify-center gap-2">
                                <a href="<?= base_url('marketing/editar?id=' . $c['id']) ?>" class="w-8 h-8 inline-flex items-center justify-center rounded bg-gray-50 text-gray-600 hover:bg-pink-50 hover:text-pink-600 transition tooltip" title="Editar"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>