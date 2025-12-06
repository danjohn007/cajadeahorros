<?php
// Get system configuration for colors and branding
$systemColors = getSystemColors();
$siteName = getSiteName();
$logoUrl = getLogo();
$colorPrimario = $systemColors['color_primario'];
$colorSecundario = $systemColors['color_secundario'];
$textoCopyright = getConfig('texto_copyright', '© ' . date('Y') . ' ' . APP_NAME . '. Todos los derechos reservados.');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - <?= htmlspecialchars($siteName) ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        .login-gradient {
            background: linear-gradient(135deg, <?= htmlspecialchars($colorPrimario) ?> 0%, <?= htmlspecialchars($colorSecundario) ?> 100%);
        }
        .btn-primary {
            background-color: <?= htmlspecialchars($colorSecundario) ?>;
        }
        .btn-primary:hover {
            background-color: <?= htmlspecialchars($colorPrimario) ?>;
        }
    </style>
</head>
<body>
<div class="min-h-screen flex items-center justify-center login-gradient">
    <div class="max-w-md w-full mx-4">
        <!-- Logo -->
        <div class="text-center mb-8">
            <?php if ($logoUrl): ?>
                <img src="<?= htmlspecialchars($logoUrl) ?>" alt="<?= htmlspecialchars($siteName) ?>" class="h-20 w-auto mx-auto mb-4">
            <?php else: ?>
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-full shadow-lg mb-4">
                    <i class="fas fa-piggy-bank text-4xl" style="color: <?= htmlspecialchars($colorSecundario) ?>;"></i>
                </div>
            <?php endif; ?>
            <h1 class="text-3xl font-bold text-white"><?= htmlspecialchars($siteName) ?></h1>
            <p class="text-blue-200 mt-2">Restablecer Contraseña</p>
        </div>
        
        <!-- Form -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-2 text-center">
                <i class="fas fa-key mr-2"></i>Nueva Contraseña
            </h2>
            <p class="text-gray-500 text-center mb-6">Ingresa tu nueva contraseña para acceder al sistema.</p>
            
            <?php if (!empty($success)): ?>
            <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
            </div>
            <div class="text-center mt-6">
                <a href="<?= BASE_URL ?>/auth/login" style="color: <?= htmlspecialchars($colorSecundario) ?>;" class="hover:opacity-80">
                    <i class="fas fa-sign-in-alt mr-1"></i> Ir a iniciar sesión
                </a>
            </div>
            <?php elseif (!empty($error)): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            </div>
            
            <?php if (!$validToken): ?>
            <div class="text-center mt-6">
                <a href="<?= BASE_URL ?>/auth/forgot-password" style="color: <?= htmlspecialchars($colorSecundario) ?>;" class="hover:opacity-80">
                    <i class="fas fa-envelope mr-1"></i> Solicitar nuevo enlace
                </a>
            </div>
            <?php endif; ?>
            <?php endif; ?>
            
            <?php if ($validToken): ?>
            <form method="POST" action="<?= BASE_URL ?>/auth/reset-password?token=<?= htmlspecialchars($token) ?>" x-data="{ showPassword: false, showPasswordConfirm: false }">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-1"></i> Nueva Contraseña
                    </label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                               placeholder="Mínimo 6 caracteres">
                        <button type="button" @click="showPassword = !showPassword" 
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i :class="showPassword ? 'fa-eye-slash' : 'fa-eye'" class="fas"></i>
                        </button>
                    </div>
                </div>
                
                <div class="mb-6">
                    <label for="password_confirm" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-1"></i> Confirmar Contraseña
                    </label>
                    <div class="relative">
                        <input :type="showPasswordConfirm ? 'text' : 'password'" id="password_confirm" name="password_confirm" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                               placeholder="Repite la contraseña">
                        <button type="button" @click="showPasswordConfirm = !showPasswordConfirm" 
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i :class="showPasswordConfirm ? 'fa-eye-slash' : 'fa-eye'" class="fas"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" 
                        class="w-full py-3 px-4 btn-primary text-white font-semibold rounded-lg focus:ring-4 focus:ring-blue-200 transition">
                    <i class="fas fa-save mr-2"></i> Guardar Nueva Contraseña
                </button>
            </form>
            <?php endif; ?>
            
            <div class="text-center mt-6">
                <a href="<?= BASE_URL ?>/auth/login" style="color: <?= htmlspecialchars($colorSecundario) ?>;" class="hover:opacity-80">
                    <i class="fas fa-arrow-left mr-1"></i> Volver al inicio de sesión
                </a>
            </div>
        </div>
        
        <div class="text-center mt-6 text-blue-200 text-sm">
            <p><?= htmlspecialchars($textoCopyright) ?></p>
        </div>
    </div>
</div>
</body>
</html>
