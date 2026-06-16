<div class="max-w-4xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-box text-blue-500 mr-2"></i> Nuevo Producto</h2>
            <p class="text-sm text-gray-500 mt-1">Completa los datos para registrar un artículo en el inventario</p>
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
        <form action="<?= base_url('inventario/nuevo') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3"><i class="fas fa-info-circle mr-2 text-gray-400"></i> Información General</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SKU (Código único) <span class="text-red-500">*</span></label>
                    <input type="text" name="sku" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm transition shadow-sm uppercase font-mono" placeholder="Ej. MUEB-001">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Producto <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm transition shadow-sm" placeholder="Ej. Silla de Madera">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea name="descripcion" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm transition shadow-sm resize-none" placeholder="Detalles del producto..."></textarea>
                </div>
            </div>

            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3 pt-4"><i class="fas fa-tags mr-2 text-gray-400"></i> Clasificación y Precios</h3>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="md:col-span-2 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                        <select name="categoria_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm transition shadow-sm bg-white">
                            <?php foreach($categorias ?? [] as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label>
                        <select name="proveedor_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm transition shadow-sm bg-white">
                            <option value="">Ninguno / Uso Interno</option>
                            <?php foreach($proveedores ?? [] as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio ($) <span class="text-red-500">*</span></label>
                    <input type="number" name="precio" step="0.01" min="0" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm transition shadow-sm text-right" placeholder="0.00">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock Inicial <span class="text-red-500">*</span></label>
                    <input type="number" name="stock" min="0" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm transition shadow-sm text-right" placeholder="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock Mínimo</label>
                    <input type="number" name="stock_minimo" value="5" min="0" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm transition shadow-sm text-right">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ubicación (Pasillo/Bodega)</label>
                    <input type="text" name="ubicacion" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm transition shadow-sm uppercase font-mono" placeholder="Ej. BODEGA NORTE - PASILLO 3">
                </div>
            </div>

            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3 pt-4"><i class="fas fa-images mr-2 text-gray-400"></i> Galería de Imágenes</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Fotos del Producto (La primera será la principal)</label>
                <div class="flex items-center justify-center w-full">
                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                            <p class="mb-1 text-sm text-gray-500"><span class="font-semibold">Haz clic para subir</span> o arrastra y suelta</p>
                            <p class="text-xs text-gray-400">Archivos PNG, JPG o WEBP</p>
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
                        <input type="radio" name="estado" value="activo" checked class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Activo (Visible en ventas)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="estado" value="inactivo" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Inactivo (Oculto/Agotado temporalmente)</span>
                    </label>
                </div>
            </div>

            <div class="pt-4 mt-6 border-t border-gray-100 flex justify-end gap-3">
                <a href="<?= base_url('inventario') ?>" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition text-sm">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold transition shadow-md flex items-center text-sm"><i class="fas fa-save mr-2"></i> Guardar Producto</button>
            </div>
        </form>
    </div>
</div>