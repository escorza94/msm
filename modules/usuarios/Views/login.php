<div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100 w-full max-w-md">
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-800"><i class="fas fa-cube text-indigo-500 mr-2"></i> AED <span class="font-light">Core</span></h1>
        <p class="text-gray-500 text-sm mt-2">Inicia sesión en tu cuenta</p>
    </div>
    <?php if(isset($error) && $error): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm text-center"><i class="fas fa-exclamation-triangle mr-1"></i> Credenciales incorrectas</div>
    <?php endif; ?>
    <form action="<?= base_url('usuarios/login') ?>" method="POST" class="space-y-5">
        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1">Correo Electrónico</label>
            <input type="email" name="email" class="w-full border border-gray-300 p-3 rounded-lg text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" required>
        </div>
        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1">Contraseña</label>
            <input type="password" name="password" class="w-full border border-gray-300 p-3 rounded-lg text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" required>
        </div>
        <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-bold hover:bg-indigo-700 transition duration-200 shadow-md">
            <i class="fas fa-sign-in-alt mr-2"></i> Iniciar Sesión
        </button>
    </form>
    <p class="text-center text-sm text-gray-500 mt-6">¿No tienes cuenta? <a href="<?= base_url('usuarios/registro') ?>" class="text-indigo-600 font-bold hover:underline">Regístrate aquí</a></p>
</div>