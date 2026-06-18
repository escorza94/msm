<div class="max-w-4xl mx-auto mt-6 mb-10">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-cube text-indigo-500 mr-3"></i> <?= $titulo ?></h2>
            <p class="text-sm text-gray-500 mt-1 uppercase tracking-wide font-bold">Tipo: <span class="text-indigo-600"><?= str_replace('_', ' ', $tipo) ?></span></p>
        </div>
        <a href="<?= base_url('pagina_web/constructor?pagina_id=' . $pagina_id) ?>" class="text-sm bg-white border border-gray-200 text-gray-600 px-3 py-1.5 rounded-lg font-medium hover:bg-gray-50 transition flex items-center"><i class="fas fa-arrow-left mr-2"></i> Cancelar</a>
    </div>

    <form action="<?= base_url('pagina_web/constructor/guardarSeccion') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
        <input type="hidden" name="id" value="<?= $seccion['id'] ?? 0 ?>">
        <input type="hidden" name="pagina_id" value="<?= $pagina_id ?>">
        <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo) ?>">

        <!-- Configuraciones Generales -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2"><i class="fas fa-cog text-gray-400 mr-2"></i> Ajustes Básicos</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nombre Interno (Solo para identificador)</label>
                    <input type="text" name="nombre_interno" value="<?= htmlspecialchars($seccion['nombre_interno'] ?? '') ?>" required class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-indigo-500 outline-none" placeholder="Ej. Carrusel Principal">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Estado de Visibilidad</label>
                    <select name="estado" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-indigo-500 outline-none bg-white">
                        <option value="activo" <?= ($seccion['estado'] ?? '') === 'activo' ? 'selected' : '' ?>>🌟 Visible al público</option>
                        <option value="inactivo" <?= ($seccion['estado'] ?? '') === 'inactivo' ? 'selected' : '' ?>>🙈 Oculto temporalmente</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- RENDERIZADOR DINÁMICO DE CAMPOS -->
        <?php if (isset($schema) && !empty($schema['campos'])): ?>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2"><i class="fas <?= htmlspecialchars($schema['icono'] ?? 'fa-cogs') ?> text-indigo-500 mr-2"></i> Contenido de la Sección</h3>
            <div class="space-y-4">
                <?php foreach ($schema['campos'] as $campo): ?>
                    <?php
                        $nombre_campo = $campo['nombre'];
                        $valor_actual = $seccion['config'][$nombre_campo] ?? $campo['default'] ?? null;
                    ?>
                    <div class="border-t border-gray-100 pt-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2"><?= htmlspecialchars($campo['label']) ?></label>

                        <?php switch ($campo['tipo']):
                            case 'texto':
                            case 'url':
                            case 'numero': ?>
                                <input type="<?= $campo['tipo'] === 'numero' ? 'number' : 'text' ?>" name="config[<?= $nombre_campo ?>]" value="<?= htmlspecialchars($valor_actual ?? '') ?>" placeholder="<?= htmlspecialchars($campo['placeholder'] ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                                <?php break; ?>

                            <?php case 'textarea': ?>
                                <textarea name="config[<?= $nombre_campo ?>]" rows="4" placeholder="<?= htmlspecialchars($campo['placeholder'] ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($valor_actual ?? '') ?></textarea>
                                <?php break; ?>

                            <?php case 'markdown': ?>
                                <textarea name="config[<?= $nombre_campo ?>]" rows="6" placeholder="<?= htmlspecialchars($campo['placeholder'] ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($valor_actual ?? '') ?></textarea>
                                <p class="text-xs text-gray-400 mt-1">Puedes usar <a href="https://www.markdownguide.org/basic-syntax/" target="_blank" class="text-indigo-500 underline">sintaxis Markdown</a> para negritas, listas, etc.</p>
                                <?php break; ?>

                            <?php case 'select': ?>
                                <select name="config[<?= $nombre_campo ?>]" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                                    <?php foreach ($campo['opciones'] as $val => $label): ?>
                                        <option value="<?= $val ?>" <?= ($valor_actual == $val) ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php break; ?>

                            <?php case 'coleccion_productos': ?>
                                <select name="config[<?= $nombre_campo ?>]" required class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="">-- Selecciona una colección --</option>
                                    <?php foreach($colecciones_productos ?? [] as $col): ?>
                                        <option value="<?= $col['slug'] ?>" <?= ($valor_actual === $col['slug']) ? 'selected' : '' ?>><?= htmlspecialchars($col['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php break; ?>

                            <?php case 'coleccion_promociones': ?>
                                <select name="config[<?= $nombre_campo ?>]" required class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="">-- Selecciona una colección --</option>
                                    <?php foreach($colecciones_promociones ?? [] as $col): ?>
                                        <option value="<?= $col['slug'] ?>" <?= ($valor_actual === $col['slug']) ? 'selected' : '' ?>><?= htmlspecialchars($col['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php break; ?>

                            <?php case 'imagen': ?>
                                <input type="hidden" name="config[<?= $nombre_campo ?>_existente]" value="<?= htmlspecialchars($valor_actual ?? '') ?>">
                                <input type="file" name="config[<?= $nombre_campo ?>]" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 outline-none transition cursor-pointer mb-2">
                                <?php if(!empty($valor_actual)): ?><img src="<?= base_url($valor_actual) ?>" class="h-24 rounded-lg shadow-sm border bg-gray-50 object-contain"><?php endif; ?>
                                <?php break; ?>

                            <?php case 'repeater': ?>
                                <div id="repeater-<?= $nombre_campo ?>" class="space-y-3">
                                    <?php $items = $valor_actual ?? []; ?>
                                    <?php 
                                        // --- MEJORA: Prevenir error en secciones nuevas ---
                                        // Si no hay items, creamos uno vacío pero con las claves definidas para evitar errores de "Undefined index".
                                        if(empty($items)) {
                                            $item_vacio = [];
                                            foreach($campo['campos_item'] as $sub_campo) { $item_vacio[$sub_campo['nombre']] = ''; }
                                            $items = [$item_vacio];
                                        }
                                    ?>
                                    <?php foreach($items as $item): ?>
                                        <div class="repeater-item bg-gray-50 p-4 rounded-lg border border-gray-200 relative">
                                            <button type="button" onclick="this.closest('.repeater-item').remove()" class="absolute top-2 right-2 text-red-400 hover:text-red-600 p-1.5 rounded-full hover:bg-red-50 transition"><i class="fas fa-times"></i></button>
                                            <div class="space-y-3">
                                                <?php foreach($campo['campos_item'] as $sub_campo): ?>
                                                    <?php
                                                        $sub_nombre = $sub_campo['nombre'];
                                                        $sub_valor = $item[$sub_nombre] ?? $sub_campo['default'] ?? '';
                                                    ?>
                                                    <div>
                                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1"><?= htmlspecialchars($sub_campo['label']) ?></label>
                                                        <?php if($sub_campo['tipo'] === 'imagen'): ?>
                                                            <input type="hidden" name="config[<?= $nombre_campo ?>][<?= $sub_nombre ?>_existente][]" value="<?= htmlspecialchars($sub_valor) ?>">
                                                            <div class="flex items-center gap-3">
                                                                <?php if(!empty($sub_valor)): ?><img src="<?= base_url($sub_valor) ?>" class="h-12 w-12 object-cover rounded border bg-white"><?php endif; ?>
                                                                <input type="file" name="config[<?= $nombre_campo ?>][<?= $sub_nombre ?>][]" accept="image/*" class="w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                                            </div>
                                                        <?php elseif($sub_campo['tipo'] === 'textarea'): ?>
                                                            <textarea name="config[<?= $nombre_campo ?>][<?= $sub_nombre ?>][]" rows="2" placeholder="<?= htmlspecialchars($sub_campo['placeholder'] ?? '') ?>" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm outline-none focus:ring-1 focus:ring-indigo-500"><?= htmlspecialchars($sub_valor) ?></textarea>
                                                        <?php else: ?>
                                                            <input type="text" name="config[<?= $nombre_campo ?>][<?= $sub_nombre ?>][]" value="<?= htmlspecialchars($sub_valor) ?>" placeholder="<?= htmlspecialchars($sub_campo['placeholder'] ?? '') ?>" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm outline-none focus:ring-1 focus:ring-indigo-500">
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" onclick="agregarItemRepeater('<?= $nombre_campo ?>')" class="mt-3 px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg text-sm font-bold border border-indigo-100 hover:bg-indigo-100 transition"><i class="fas fa-plus mr-1"></i> Añadir Item</button>
                                
                                <template id="template-<?= $nombre_campo ?>">
                                    <div class="repeater-item bg-gray-50 p-4 rounded-lg border border-gray-200 relative">
                                        <button type="button" onclick="this.closest('.repeater-item').remove()" class="absolute top-2 right-2 text-red-400 hover:text-red-600 p-1.5 rounded-full hover:bg-red-50 transition"><i class="fas fa-times"></i></button>
                                        <div class="space-y-3">
                                            <?php foreach($campo['campos_item'] as $sub_campo): ?>
                                                <div>
                                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1"><?= htmlspecialchars($sub_campo['label']) ?></label>
                                                    <?php if($sub_campo['tipo'] === 'imagen'): ?>
                                                        <input type="hidden" name="config[<?= $nombre_campo ?>][<?= $sub_campo['nombre'] ?>_existente][]" value="">
                                                        <input type="file" name="config[<?= $nombre_campo ?>][<?= $sub_campo['nombre'] ?>][]" accept="image/*" class="w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                                    <?php elseif($sub_campo['tipo'] === 'textarea'): ?>
                                                        <textarea name="config[<?= $nombre_campo ?>][<?= $sub_campo['nombre'] ?>][]" rows="2" placeholder="<?= htmlspecialchars($sub_campo['placeholder'] ?? '') ?>" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm outline-none focus:ring-1 focus:ring-indigo-500"></textarea>
                                                    <?php else: ?>
                                                        <input type="text" name="config[<?= $nombre_campo ?>][<?= $sub_campo['nombre'] ?>][]" value="<?= htmlspecialchars($sub_campo['default'] ?? '') ?>" placeholder="<?= htmlspecialchars($sub_campo['placeholder'] ?? '') ?>" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm outline-none focus:ring-1 focus:ring-indigo-500">
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </template>
                                <?php break; ?>

                        <?php endswitch; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="flex justify-end pt-4">
            <button type="submit" class="px-6 py-3 bg-amber-500 text-white rounded-lg shadow-sm font-bold hover:bg-amber-600 transition flex items-center text-lg">
                <i class="fas fa-save mr-2"></i> Guardar Sección
            </button>
        </div>
    </form>
</div>

<script>
function agregarItemRepeater(nombreCampo) {
    const template = document.getElementById(`template-${nombreCampo}`);
    const container = document.getElementById(`repeater-${nombreCampo}`);
    if (template && container) {
        container.appendChild(template.content.cloneNode(true));
    }
}
</script>