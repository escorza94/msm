<?php
// $promociones es inyectado por el StorefrontController.
$titulo = $config['titulo_seccion'] ?? '';
if (empty($promociones)) return;

$subtitulo = $config['subtitulo'] ?? '';
?>
<section class="py-12 md:py-16 bg-secciones">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (!empty($titulo)): ?>
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl"><?= htmlspecialchars($titulo) ?></h2>
            <?php if (!empty($subtitulo)): ?>
            <p class="mt-3 max-w-2xl mx-auto text-lg text-gray-500"><?= htmlspecialchars($subtitulo) ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($promociones as $promo): ?>
                <div class="bg-gradient-to-br from-red-600 to-amber-500 rounded-2xl shadow-xl text-white relative overflow-hidden flex flex-col transform hover:-translate-y-1 transition-transform duration-300 border border-white/10">
                    <!-- Recortes laterales tipo ticket -->
                    <div class="absolute top-1/2 -left-3 w-6 h-6 bg-secciones rounded-full transform -translate-y-1/2 shadow-inner z-20 hidden sm:block"></div>
                    <div class="absolute top-1/2 -right-3 w-6 h-6 bg-secciones rounded-full transform -translate-y-1/2 shadow-inner z-20 hidden sm:block"></div>
                    
                    <div class="p-6 border-b border-dashed border-white/30 relative">
                        <div class="absolute -right-4 -top-4 opacity-10 transform rotate-12 pointer-events-none">
                            <i class="fas fa-ticket-alt text-8xl"></i>
                        </div>
                        <div class="relative z-10">
                            <span class="bg-yellow-400 text-yellow-900 text-[10px] font-black px-2 py-1 rounded-sm uppercase tracking-wider mb-3 inline-block shadow-sm">Cupón de Descuento</span>
                            <h3 class="text-xl font-bold mb-2 leading-tight"><?= htmlspecialchars($promo['nombre']) ?></h3>
                            <div class="flex items-baseline gap-1 mb-2">
                                <span class="text-4xl font-black text-yellow-300"><?= $promo['tipo'] === 'porcentaje' ? floatval($promo['valor']) . '%' : '$' . number_format($promo['valor'], 2) ?></span>
                                <span class="text-sm font-bold opacity-90 uppercase tracking-wide">DTO</span>
                            </div>
                            <?php if(!empty($promo['codigo_cupon'])): ?>
                            <div class="mt-4">
                                <span class="text-[10px] uppercase font-bold opacity-80 block mb-1">Código:</span>
                                <div class="bg-black/30 border border-dashed border-yellow-300/50 rounded px-4 py-2 font-mono text-xl font-bold inline-block tracking-widest text-yellow-300 select-all shadow-inner">
                                    <?= htmlspecialchars($promo['codigo_cupon']) ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="p-6 flex-1 flex flex-col justify-between bg-black/10 relative z-10">
                        <div class="mb-5">
                            <?php if(!empty($promo['productos_incluidos'])): ?>
                                <p class="text-xs font-bold uppercase opacity-70 mb-3 tracking-wider">Productos en esta oferta:</p>
                                <div class="space-y-2.5 mb-4">
                                    <?php foreach($promo['productos_incluidos'] as $prod): ?>
                                        <a href="<?= base_url('producto/' . $prod['id']) ?>" class="flex items-center gap-3 bg-white/10 rounded-lg p-2 hover:bg-white/20 transition border border-transparent hover:border-white/30" title="Ver detalles de <?= htmlspecialchars($prod['nombre']) ?>">
                                            <img src="<?= base_url($prod['imagen'] ?? 'storage/placeholder.jpg') ?>" class="w-10 h-10 rounded shadow-sm object-cover border border-white/20">
                                            <span class="text-sm font-medium leading-tight truncate flex-1" title="<?= htmlspecialchars($prod['nombre']) ?>"><?= htmlspecialchars($prod['nombre']) ?></span>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <a href="https://wa.me/<?= htmlspecialchars($global_config['whatsapp_numero'] ?? '') ?>?text=Hola,%20me%20interesa%20la%20promoción:%20<?= urlencode($promo['nombre']) ?>" target="_blank" class="block w-full bg-white text-red-700 text-center py-3 rounded-lg font-bold hover:bg-gray-50 transition shadow-md hover:shadow-lg uppercase text-sm tracking-wide">Reclamar Cupón <i class="fas fa-arrow-right ml-1"></i></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>