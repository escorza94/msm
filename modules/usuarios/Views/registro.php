<div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100 w-full max-w-md my-8">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800"><i class="fas fa-user-plus text-indigo-500 mr-2"></i> Crear Cuenta</h1>
        <p class="text-gray-500 text-sm mt-2">Únete a la plataforma AED</p>
    </div>
    <?php if(isset($error)): ?><div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm text-center"><i class="fas fa-exclamation-triangle mr-1"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if(isset($success)): ?><div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm text-center"><i class="fas fa-check-circle mr-1"></i> <?= htmlspecialchars($success) ?></div><?php endif; ?>
    <form action="<?= base_url('usuarios/registro') ?>" method="POST" class="space-y-4">
        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1">Nombre Completo</label>
            <input type="text" name="name" class="w-full border border-gray-300 p-3 rounded-lg text-sm focus:outline-none focus:border-indigo-500" required>
        </div>
        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1">Correo Electrónico</label>
            <input type="email" name="email" class="w-full border border-gray-300 p-3 rounded-lg text-sm focus:outline-none focus:border-indigo-500" required>
        </div>
        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1">Contraseña</label>
            <input type="password" name="password" class="w-full border border-gray-300 p-3 rounded-lg text-sm focus:outline-none focus:border-indigo-500" required>
        </div>
        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1">WhatsApp ID (Opcional)</label>
            <input type="text" name="whatsapp_id" class="w-full border border-gray-300 p-3 rounded-lg text-sm focus:outline-none focus:border-indigo-500" placeholder="Ej. 521XXXXXXXXXX@c.us">
        </div>
        <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-bold hover:bg-indigo-700 transition duration-200 mt-2">
            <i class="fas fa-save mr-2"></i> Registrarme
        </button>
    </form>
    <p class="text-center text-sm text-gray-500 mt-6">¿Ya tienes cuenta? <a href="<?= base_url('usuarios/login') ?>" class="text-indigo-600 font-bold hover:underline">Inicia sesión</a></p>
</div>