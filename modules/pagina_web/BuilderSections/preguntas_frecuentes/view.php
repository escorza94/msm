<?php
$titulo = $config['titulo'] ?? 'Preguntas Frecuentes';
$subtitulo = $config['subtitulo'] ?? '';
$faqs = $config['faqs'] ?? [];
?>

<section class="py-16 px-4 bg-gray-50 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <!-- Encabezado de la Sección -->
        <div class="text-center mb-12">
            <?php if (!empty($titulo)): ?>
                <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl tracking-tight">
                    <?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?>
                </h2>
            <?php endif; ?>
            <?php if (!empty($subtitulo)): ?>
                <p class="mt-4 text-lg text-gray-500 max-w-2xl mx-auto">
                    <?= htmlspecialchars($subtitulo, ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endif; ?>
        </div>

        <!-- Acordeón de FAQs -->
        <?php if (!empty($faqs) && is_array($faqs)): ?>
            <div class="space-y-4">
                <?php foreach ($faqs as $index => $faq): ?>
                    <?php 
                        $pregunta = $faq['pregunta'] ?? '';
                        $respuesta = $faq['respuesta'] ?? '';
                        if (empty($pregunta)) continue;
                    ?>
                    <details 
                        class="group border border-gray-200 rounded-xl bg-white shadow-sm transition-all duration-300 hover:shadow-md [&_summary::-webkit-details-marker]:hidden"
                        <?= $index === 0 ? 'open' : '' ?>
                    >
                        <summary class="flex items-center justify-between gap-4 p-6 text-gray-900 cursor-pointer focus:outline-none select-none">
                            <span class="font-semibold text-lg text-gray-800 tracking-tight">
                                <?= htmlspecialchars($pregunta, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <span class="relative flex-shrink-0 ml-1.5 w-5 h-5">
                                <!-- Icono de Más -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="absolute inset-0 w-5 h-5 opacity-100 group-open:opacity-0 transition-opacity duration-300 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                <!-- Icono de Menos -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="absolute inset-0 w-5 h-5 opacity-0 group-open:opacity-100 transition-opacity duration-300 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                                </svg>
                            </span>
                        </summary>
                        <div class="px-6 pb-6 text-gray-600 leading-relaxed border-t border-gray-100 pt-4">
                            <!-- Se sanitiza y formatea el texto de respuesta conservando saltos de línea -->
                            <?= nl2br(htmlspecialchars($respuesta, ENT_QUOTES, 'UTF-8')) ?>
                        </div>
                    </details>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-8 text-gray-400 border border-dashed border-gray-300 rounded-xl bg-white">
                No se han configurado preguntas frecuentes aún.
            </div>
        <?php endif; ?>
    </div>
</section>