<?php
// Get system configuration for colors and branding
$systemColors = getSystemColors();
$siteName = getSiteName();
$logoUrl = getLogo();
$colorPrimario = $systemColors['color_primario'];
$colorSecundario = $systemColors['color_secundario'];
$emailContacto = getConfig('email_contacto', '');
$telefonoContacto = getConfig('telefono_contacto', '');
$textoCopyright = getConfig('texto_copyright', '© ' . date('Y') . ' ' . APP_NAME . '. Todos los derechos reservados.');
$whatsappNumero = getConfig('chatbot_whatsapp_numero', '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - <?= htmlspecialchars($siteName) ?></title>
    
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
                <i class="fas fa-piggy-bank text-6xl text-white mb-4" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.3);"></i>
            <?php endif; ?>
            <h1 class="text-3xl font-bold text-white"><?= htmlspecialchars($siteName) ?></h1>
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
                        class="w-full py-3 px-4 btn-primary text-white font-semibold rounded-lg focus:ring-4 focus:ring-blue-200 transition">
                    <i class="fas fa-sign-in-alt mr-2"></i> Iniciar Sesión
                </button>
            </form>
            
            <div class="mt-4 pt-4 border-t text-center">
                <span class="text-gray-600">¿Eres socio y no tienes cuenta?</span>
                <a href="<?= BASE_URL ?>/auth/registro" class="text-blue-600 hover:text-blue-800 font-medium ml-1">
                    Regístrate aquí
                </a>
            </div>
        </div>
        
        <div class="text-center mt-6 text-blue-200 text-sm">
            <p><?= htmlspecialchars($textoCopyright) ?></p>
            <?php if ($telefonoContacto || $emailContacto): ?>
            <div class="mt-2">
                <?php if ($telefonoContacto): ?>
                    <span><i class="fas fa-phone mr-1"></i> <?= htmlspecialchars($telefonoContacto) ?></span>
                <?php endif; ?>
                <?php if ($telefonoContacto && $emailContacto): ?>
                    <span class="mx-2">|</span>
                <?php endif; ?>
                <?php if ($emailContacto): ?>
                    <span><i class="fas fa-envelope mr-1"></i> <?= htmlspecialchars($emailContacto) ?></span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- WhatsApp Support Button -->
    <?php if (!empty($whatsappNumero)): ?>
    <a href="https://wa.me/<?= htmlspecialchars(preg_replace('/[^0-9+]/', '', $whatsappNumero)) ?>?text=<?= urlencode('Hola, ¿me pueden apoyar con mi estado de cuenta?') ?>" 
       target="_blank"
       class="fixed bottom-6 right-6 bg-green-500 hover:bg-green-600 text-white rounded-full p-4 shadow-lg transition-all duration-300 hover:scale-110 z-50"
       title="Soporte por WhatsApp"
       aria-label="Contactar por WhatsApp">
        <i class="fa-brands fa-whatsapp text-3xl"></i>
    </a>
    <?php endif; ?>
</div>
</body>
</html>
