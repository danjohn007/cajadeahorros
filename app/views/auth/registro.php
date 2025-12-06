<?php
// Get system configuration for colors and branding
$systemColors = getSystemColors();
$siteName = getSiteName();
$logoUrl = getLogo();
$colorPrimario = $systemColors['color_primario'];
$colorSecundario = $systemColors['color_secundario'];
$textoCopyright = getConfig('texto_copyright', '© ' . date('Y') . ' ' . APP_NAME . '. Todos los derechos reservados.');
$whatsappNumero = getConfig('chatbot_whatsapp_numero', '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Cliente - <?= htmlspecialchars($siteName) ?></title>
    
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
<div class="min-h-screen flex items-center justify-center login-gradient py-8">
    <div class="max-w-lg w-full mx-4">
        <!-- Logo -->
        <div class="text-center mb-6">
            <?php if ($logoUrl): ?>
                <img src="<?= htmlspecialchars($logoUrl) ?>" alt="<?= htmlspecialchars($siteName) ?>" class="h-16 w-auto mx-auto mb-4">
            <?php else: ?>
                <i class="fas fa-piggy-bank text-5xl text-white mb-4" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.3);"></i>
            <?php endif; ?>
            <h1 class="text-2xl font-bold text-white"><?= htmlspecialchars($siteName) ?></h1>
            <p class="text-blue-200 mt-1">Registro de Cliente</p>
        </div>
        
        <!-- Registration Form -->
        <div class="bg-white rounded-2xl shadow-2xl p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 text-center">Crear Cuenta</h2>
            
            <?php if (!empty($success)): ?>
            <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
                <div class="mt-3 text-center">
                    <a href="<?= BASE_URL ?>/auth/login" class="text-green-800 font-medium hover:underline">
                        <i class="fas fa-sign-in-alt mr-1"></i>Ir a Iniciar Sesión
                    </a>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg">
                <ul class="list-disc list-inside text-sm">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="<?= BASE_URL ?>/auth/registro">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                
                <div class="space-y-4">
                    <!-- Nombre -->
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-user mr-1"></i> Nombre Completo *
                        </label>
                        <input type="text" id="nombre" name="nombre" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Tu nombre completo"
                               value="<?= htmlspecialchars($data['nombre'] ?? '') ?>">
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-envelope mr-1"></i> Correo Electrónico *
                        </label>
                        <input type="email" id="email" name="email" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="correo@ejemplo.com"
                               value="<?= htmlspecialchars($data['email'] ?? '') ?>">
                        <p class="text-xs text-gray-500 mt-1">Si ya eres socio, usa el mismo correo registrado para vincular tu cuenta.</p>
                    </div>
                    
                    <!-- Teléfonos -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-phone mr-1"></i> Teléfono
                            </label>
                            <input type="tel" id="telefono" name="telefono"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="10 dígitos"
                                   maxlength="10"
                                   pattern="[0-9]{10}"
                                   value="<?= htmlspecialchars($data['telefono'] ?? '') ?>">
                        </div>
                        <div>
                            <label for="celular" class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-mobile-alt mr-1"></i> Celular
                            </label>
                            <input type="tel" id="celular" name="celular"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="10 dígitos"
                                   maxlength="10"
                                   pattern="[0-9]{10}"
                                   value="<?= htmlspecialchars($data['celular'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <!-- Contraseñas -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-lock mr-1"></i> Contraseña *
                            </label>
                            <div class="relative" x-data="{ show: false }">
                                <input :type="show ? 'text' : 'password'" id="password" name="password" required
                                       minlength="6"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Mínimo 6 caracteres">
                                <button type="button" @click="show = !show" 
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                    <i :class="show ? 'fa-eye-slash' : 'fa-eye'" class="fas"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label for="password_confirm" class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-lock mr-1"></i> Confirmar *
                            </label>
                            <input type="password" id="password_confirm" name="password_confirm" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Confirmar contraseña">
                        </div>
                    </div>
                    
                    <!-- Captcha -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label for="captcha" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-robot mr-1"></i> Verificación de Seguridad *
                        </label>
                        <div class="flex items-center space-x-3">
                            <span class="text-lg font-bold text-gray-700 bg-white px-4 py-2 rounded border">
                                <?= $captcha_num1 ?> + <?= $captcha_num2 ?> = ?
                            </span>
                            <input type="number" id="captcha" name="captcha" required
                                   class="w-24 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-center"
                                   placeholder="?">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Resuelve la suma para demostrar que no eres un robot</p>
                    </div>
                    
                    <!-- Términos y Condiciones -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="flex items-start">
                            <input type="checkbox" name="acepta_terminos" value="1" required
                                   class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-600">
                                Acepto los <a href="#" class="text-blue-600 hover:underline" onclick="mostrarTerminos(); return false;">Términos y Condiciones</a> 
                                y la <a href="#" class="text-blue-600 hover:underline" onclick="mostrarPrivacidad(); return false;">Política de Privacidad</a> *
                            </span>
                        </label>
                    </div>
                </div>
                
                <button type="submit" 
                        class="w-full mt-6 py-3 px-4 btn-primary text-white font-semibold rounded-lg focus:ring-4 focus:ring-blue-200 transition">
                    <i class="fas fa-user-plus mr-2"></i> Crear Cuenta
                </button>
            </form>
            
            <div class="mt-4 text-center">
                <span class="text-gray-600">¿Ya tienes cuenta?</span>
                <a href="<?= BASE_URL ?>/auth/login" class="text-blue-600 hover:text-blue-800 font-medium ml-1">
                    Iniciar Sesión
                </a>
            </div>
        </div>
        
        <div class="text-center mt-4 text-blue-200 text-sm">
            <p><?= htmlspecialchars($textoCopyright) ?></p>
        </div>
    </div>
</div>

<!-- Modal de Términos -->
<div id="modalTerminos" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold">Términos y Condiciones</h3>
            <button onclick="cerrarModal('modalTerminos')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[60vh]">
            <h4 class="font-medium mb-2">1. Aceptación de los Términos</h4>
            <p class="text-sm text-gray-600 mb-4">Al registrarse y utilizar este sistema, usted acepta cumplir con estos términos y condiciones.</p>
            
            <h4 class="font-medium mb-2">2. Uso del Sistema</h4>
            <p class="text-sm text-gray-600 mb-4">El sistema está destinado únicamente para consulta de información de cuentas de ahorro y créditos. No está permitido el uso del sistema para fines ilegales o no autorizados.</p>
            
            <h4 class="font-medium mb-2">3. Responsabilidades del Usuario</h4>
            <p class="text-sm text-gray-600 mb-4">El usuario es responsable de mantener la confidencialidad de sus credenciales de acceso y de todas las actividades realizadas bajo su cuenta.</p>
            
            <h4 class="font-medium mb-2">4. Protección de Datos</h4>
            <p class="text-sm text-gray-600 mb-4">Sus datos personales serán tratados de acuerdo con nuestra política de privacidad y las leyes aplicables de protección de datos.</p>
            
            <h4 class="font-medium mb-2">5. Modificaciones</h4>
            <p class="text-sm text-gray-600 mb-4">Nos reservamos el derecho de modificar estos términos en cualquier momento. Los cambios serán notificados a través del sistema.</p>
        </div>
        <div class="px-6 py-4 border-t">
            <button onclick="cerrarModal('modalTerminos')" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Entendido
            </button>
        </div>
    </div>
</div>

<!-- Modal de Privacidad -->
<div id="modalPrivacidad" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold">Política de Privacidad</h3>
            <button onclick="cerrarModal('modalPrivacidad')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[60vh]">
            <h4 class="font-medium mb-2">Información que Recopilamos</h4>
            <p class="text-sm text-gray-600 mb-4">Recopilamos información personal como nombre, correo electrónico y números de contacto para proporcionar nuestros servicios.</p>
            
            <h4 class="font-medium mb-2">Uso de la Información</h4>
            <p class="text-sm text-gray-600 mb-4">Su información se utiliza para gestionar su cuenta, procesar transacciones y comunicarnos con usted sobre su cuenta.</p>
            
            <h4 class="font-medium mb-2">Protección de Datos</h4>
            <p class="text-sm text-gray-600 mb-4">Implementamos medidas de seguridad para proteger su información personal contra acceso no autorizado.</p>
            
            <h4 class="font-medium mb-2">Derechos del Usuario</h4>
            <p class="text-sm text-gray-600 mb-4">Tiene derecho a acceder, rectificar y eliminar sus datos personales. Para ejercer estos derechos, contáctenos.</p>
        </div>
        <div class="px-6 py-4 border-t">
            <button onclick="cerrarModal('modalPrivacidad')" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Entendido
            </button>
        </div>
    </div>
</div>

<script>
function mostrarTerminos() {
    document.getElementById('modalTerminos').classList.remove('hidden');
    document.getElementById('modalTerminos').classList.add('flex');
}

function mostrarPrivacidad() {
    document.getElementById('modalPrivacidad').classList.remove('hidden');
    document.getElementById('modalPrivacidad').classList.add('flex');
}

function cerrarModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.getElementById(id).classList.remove('flex');
}

// Close modals when clicking outside
['modalTerminos', 'modalPrivacidad'].forEach(function(id) {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModal(id);
        }
    });
});

// Limit phone inputs to numbers only
['telefono', 'celular'].forEach(function(id) {
    document.getElementById(id).addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '').substring(0, 10);
    });
});
</script>

<!-- WhatsApp Support Button -->
<?php if (!empty($whatsappNumero)): ?>
<a href="https://wa.me/<?= htmlspecialchars(preg_replace('/[^0-9+]/', '', $whatsappNumero)) ?>?text=<?= urlencode('Hola, necesito ayuda con el registro') ?>" 
   target="_blank"
   class="fixed bottom-6 right-6 bg-green-500 hover:bg-green-600 text-white rounded-full w-14 h-14 flex items-center justify-center shadow-lg transition-all duration-300 hover:scale-110 z-50"
   title="Soporte por WhatsApp"
   aria-label="Contactar por WhatsApp">
    <i class="fab fa-whatsapp text-3xl"></i>
</a>
<?php endif; ?>
</body>
</html>
