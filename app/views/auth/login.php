<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-900 via-blue-800 to-blue-600">
    <div class="max-w-md w-full mx-4">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-full shadow-lg mb-4">
                <i class="fas fa-piggy-bank text-4xl text-blue-600"></i>
            </div>
            <h1 class="text-3xl font-bold text-white"><?= APP_NAME ?></h1>
            <p class="text-blue-200 mt-2">Sistema de Gestión Integral</p>
        </div>
        
        <!-- Login Form -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Iniciar Sesión</h2>
            
            <?php if (!empty($error)): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="<?= BASE_URL ?>/auth/login">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-1"></i> Correo Electrónico
                    </label>
                    <input type="email" id="email" name="email" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                           placeholder="correo@ejemplo.com"
                           value="<?= htmlspecialchars($email ?? '') ?>">
                </div>
                
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-1"></i> Contraseña
                    </label>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" id="password" name="password" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                               placeholder="••••••••">
                        <button type="button" @click="show = !show" 
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <i :class="show ? 'fa-eye-slash' : 'fa-eye'" class="fas"></i>
                        </button>
                    </div>
                </div>
                
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Recordarme</span>
                    </label>
                    <a href="<?= BASE_URL ?>/auth/forgot-password" class="text-sm text-blue-600 hover:text-blue-800">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>
                
                <button type="submit" 
                        class="w-full py-3 px-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition">
                    <i class="fas fa-sign-in-alt mr-2"></i> Iniciar Sesión
                </button>
            </form>
        </div>
        
        <!-- Footer -->
        <div class="text-center mt-6 text-blue-200 text-sm">
            <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. Todos los derechos reservados.</p>
        </div>
    </div>
</div>
