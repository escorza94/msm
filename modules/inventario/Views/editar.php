<div class="max-w-4xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-edit text-blue-500 mr-2"></i> Editar Producto</h2>
            <p class="text-sm text-gray-500 mt-1">Modifica los detalles del producto <span class="font-mono font-bold"><?= htmlspecialchars($producto['sku']) ?></span></p>
        </div>
        <a href="<?= base_url('inventario') ?>" class="text-sm bg-white border border-gray-200 text-gray-600 px-3 py-1.5 rounded-lg font-medium hover:bg-gray-50 transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
    </div>

    <?php if(isset($error)): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-xl flex items-center shadow-sm text-sm">
            <i class="fas fa-exclamation-triangle text-lg mr-3"></i> 
            <div><?= htmlspecialchars($error) ?></div>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-6">
        <form action="<?= base_url('inventario/editar') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="id" value="<?= $producto['id'] ?>">
            
            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3"><i class="fas fa-info-circle mr-2 text-gray-400"></i> Información General</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SKU (Código único) <span class="text-red-500">*</span></label>
                    <input type="text" name="sku" value="<?= htmlspecialchars($producto['sku']) ?>" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm transition shadow-sm uppercase font-mono">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Producto <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm transition shadow-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea name="descripcion" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm transition shadow-sm resize-none"><?= htmlspecialchars($producto['descripcion'] ?? '') ?></textarea>
                </div>
            </div>

            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3 pt-4"><i class="fas fa-tags mr-2 text-gray-400"></i> Clasificación y Precios</h3>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="md:col-span-2 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                        <select name="categoria_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm transition shadow-sm bg-white">
                            <?php foreach($categorias ?? [] as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= $c['id'] == $producto['categoria_id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label>
                        <select name="proveedor_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm transition shadow-sm bg-white">
                            <option value="">Ninguno / Uso Interno</option>
                            <?php foreach($proveedores ?? [] as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= $p['id'] == $producto['proveedor_id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio ($) <span class="text-red-500">*</span></label>
                    <input type="number" name="precio" value="<?= $producto['precio'] ?>" step="0.01" min="0" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm transition shadow-sm text-right">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">Stock <i class="fas fa-info-circle text-gray-400 tooltip" title="Modificar este valor generará un 'Ajuste' en el Kardex"></i></label>
                    <input type="number" name="stock" value="<?= $producto['stock'] ?>" min="0" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm transition shadow-sm text-right bg-yellow-50 focus:bg-white" title="Si cambias este valor, se generará un movimiento de 'Ajuste' en el Kardex.">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock Mínimo</label>
                    <input type="number" name="stock_minimo" value="<?= $producto['stock_minimo'] ?>" min="0" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm transition shadow-sm text-right">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ubicación (Pasillo/Bodega)</label>
                    <input type="text" name="ubicacion" value="<?= htmlspecialchars($producto['ubicacion'] ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm transition shadow-sm uppercase font-mono">
                </div>
            </div>

            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3 pt-4"><i class="fas fa-images mr-2 text-gray-400"></i> Galería de Imágenes</h3>
            
            <?php if(!empty($imagenes)): ?>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Imágenes Actuales <span class="text-xs text-red-500 font-normal ml-2">(Haz clic en el ícono de basura para eliminar)</span></label>
                    <div class="grid grid-cols-4 md:grid-cols-6 gap-4">
                        <?php foreach($imagenes as $img): ?>
                            <div class="relative group aspect-square rounded-lg border border-gray-200 overflow-hidden bg-gray-50 transition-all">
                                <img src="<?= base_url($img['ruta']) ?>" class="w-full h-full object-cover transition duration-300">
                                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                    <label class="cursor-pointer bg-red-500 text-white p-2.5 rounded-full hover:bg-red-600 transition shadow">
                                        <input type="checkbox" name="eliminar_imagenes[]" value="<?= $img['id'] ?>" class="hidden">
                                        <i class="fas fa-trash-alt transition-transform duration-200 hover:scale-110"></i>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Agregar Nuevas Fotos</label>
                <div class="flex items-center justify-center w-full">
                    <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="fas fa-plus-circle text-2xl text-gray-400 mb-2"></i>
                            <p class="mb-1 text-sm text-gray-500"><span class="font-semibold">Haz clic para subir</span> o arrastra más fotos</p>
                        </div>
                        <input type="file" name="imagenes[]" class="hidden" multiple accept="image/png, image/jpeg, image/webp" />
                    </label>
                </div>
            </div>

            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3 pt-4"><i class="fas fa-cog mr-2 text-gray-400"></i> Opciones</h3>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado del Producto</label>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="estado" value="activo" <?= $producto['estado'] === 'activo' ? 'checked' : '' ?> class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Activo (Visible en ventas)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="estado" value="inactivo" <?= $producto['estado'] === 'inactivo' ? 'checked' : '' ?> class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Inactivo (Oculto/Agotado temporalmente)</span>
                    </label>
                </div>
            </div>

            <div class="pt-4 mt-6 border-t border-gray-100 flex justify-end gap-3">
                <a href="<?= base_url('inventario') ?>" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition text-sm">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold transition shadow-md flex items-center text-sm"><i class="fas fa-save mr-2"></i> Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll('input[name="eliminar_imagenes[]"]').forEach(input => {
        input.addEventListener('change', function() {
            const container = this.closest('.group');
            const img = container.querySelector('img');
            const icon = this.nextElementSibling;
            
            if (this.checked) {
                img.classList.add('opacity-30', 'grayscale');
                container.classList.add('border-red-500', 'bg-red-50');
                icon.classList.remove('fa-trash-alt');
                icon.classList.add('fa-undo');
                this.parentElement.classList.replace('bg-red-500', 'bg-gray-700');
                this.parentElement.classList.replace('hover:bg-red-600', 'hover:bg-gray-800');
            } else {
                img.classList.remove('opacity-30', 'grayscale');
                container.classList.remove('border-red-500', 'bg-red-50');
                icon.classList.remove('fa-undo');
                icon.classList.add('fa-trash-alt');
                this.parentElement.classList.replace('bg-gray-700', 'bg-red-500');
                this.parentElement.classList.replace('hover:bg-gray-800', 'hover:bg-red-600');
            }
        });
    });
</script>