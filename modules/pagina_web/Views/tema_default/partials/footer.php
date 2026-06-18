    <!-- 6. Footer -->
    <footer id="contacto" class="bg-secundario text-white py-10 md:py-12">
        <div class="container mx-auto px-4 md:px-6 text-center">
            <?php $logo_url = $business_logo ?? ''; ?>
            <?php if (!empty($logo_url)): ?>
                <img src="<?= (strpos($logo_url, 'http') === 0 ? '' : base_url()) . htmlspecialchars($logo_url) ?>" alt="Logo <?= htmlspecialchars($config['nombre_empresa'] ?? 'Negocio') ?>" class="h-16 mx-auto mb-6 object-contain filter brightness-0 invert opacity-90 hover:opacity-100 transition">
            <?php else: ?>
                <h3 class="text-2xl font-bold mb-4"><?= htmlspecialchars($config['nombre_empresa'] ?? 'Mueblería San Martín') ?></h3>
            <?php endif; ?>
            <p class="mb-8 text-gray-300 max-w-lg mx-auto leading-relaxed"><?= nl2br(htmlspecialchars($config['footer_texto'] ?? 'Calidad y confianza para amueblar tu vida. Contáctanos para cualquier duda o cotización.')) ?></p>
            <div class="flex justify-center space-x-6 mb-8">
                <?php if(!empty($config['facebook_url'])): ?>
                    <a href="<?= htmlspecialchars($config['facebook_url']) ?>" target="_blank" class="text-gray-300 text-2xl hover:text-white transition"><i class="fab fa-facebook"></i></a>
                <?php endif; ?>
                <?php if(!empty($config['instagram_url'])): ?>
                    <a href="<?= htmlspecialchars($config['instagram_url']) ?>" target="_blank" class="text-gray-300 text-2xl hover:text-white transition"><i class="fab fa-instagram"></i></a>
                <?php endif; ?>
                <?php if(!empty($config['tiktok_url'])): ?>
                    <a href="<?= htmlspecialchars($config['tiktok_url']) ?>" target="_blank" class="text-gray-300 text-2xl hover:text-white transition"><i class="fab fa-tiktok"></i></a>
                <?php endif; ?>
            </div>
            <p class="text-sm text-gray-500">&copy; <?= date('Y') ?> <?= htmlspecialchars($config['nombre_empresa'] ?? 'Mueblería San Martín') ?>. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Botón Flotante de WhatsApp -->
    <?php if(!empty($config['whatsapp_numero'])): ?>
    <a href="https://wa.me/<?= htmlspecialchars($config['whatsapp_numero']) ?>?text=Hola,%20necesito%20ayuda" target="_blank" class="fixed bottom-4 right-4 md:bottom-6 md:right-6 w-14 h-14 md:w-16 md:h-16 bg-green-500 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-green-600 transition transform hover:scale-110 z-40">
        <i class="fab fa-whatsapp text-3xl md:text-4xl"></i>
    </a>
    <?php endif; ?>

    <!-- Scripts JS -->
    
    <!-- Script para Menú Móvil -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnOpen = document.getElementById('mobile-menu-btn');
            const btnClose = document.getElementById('close-menu-btn');
            const menu = document.getElementById('mobile-menu');
            const menuContent = document.getElementById('mobile-menu-content');
            const links = document.querySelectorAll('.mobile-link');

            function openMenu() {
                menu.classList.remove('hidden');
                void menu.offsetWidth; // Forzar reflow para que la animación funcione
                menu.classList.remove('opacity-0');
                menuContent.classList.remove('-translate-x-full');
                menuContent.classList.add('translate-x-0');
            }

            function closeMenu() {
                menu.classList.add('opacity-0');
                menuContent.classList.remove('translate-x-0');
                menuContent.classList.add('-translate-x-full');
                setTimeout(() => { menu.classList.add('hidden'); }, 300);
            }

            if(btnOpen) btnOpen.addEventListener('click', openMenu);
            if(btnClose) btnClose.addEventListener('click', closeMenu);
            links.forEach(l => l.addEventListener('click', closeMenu));
            
            // Cerrar menú al tocar el fondo oscuro
            menu.addEventListener('click', function(e) {
                if(e.target === menu) closeMenu();
            });
        });
    </script>

</body>
</html>