<?php
/**
 * Página Pública de Soporte Técnico
 * Sistema de Gestión Integral de Caja de Ahorros
 */

$siteName = $config['nombre_sitio'] ?: APP_NAME;
$logoUrl = $config['logo'] ? PUBLIC_URL . '/images/' . $config['logo'] : '';
$colorPrimario = $config['color_primario'] ?: '#1e40af';
$colorSecundario = $config['color_secundario'] ?: '#3b82f6';
$colorAcento = $config['color_acento'] ?: '#89ab37';
$telefonoContacto = $config['telefono_contacto'] ?: '';
$emailContacto = $config['email_contacto'] ?: '';
$horarioAtencion = $config['horario_atencion'] ?: 'Lunes a Viernes 9:00 - 18:00';
$textoCopyright = $config['texto_copyright'] ?: '© ' . date('Y') . ' ' . $siteName . '. Todos los derechos reservados.';
$whatsappNumero = $config['chatbot_whatsapp_numero'] ?: '';
$chatbotUrl = $config['chatbot_url_publica'] ?: '';
$mensajeBienvenida = $config['chatbot_mensaje_bienvenida'] ?: 'Hola, bienvenido a nuestro soporte técnico. ¿En qué podemos ayudarte?';
$mensajeHorario = $config['chatbot_mensaje_horario'] ?: '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soporte Técnico - <?= htmlspecialchars($siteName) ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            500: '<?= htmlspecialchars($colorSecundario) ?>',
                            600: '<?= htmlspecialchars($colorPrimario) ?>',
                            700: '<?= htmlspecialchars($colorPrimario) ?>',
                        },
                        accent: '<?= htmlspecialchars($colorAcento) ?>'
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, <?= htmlspecialchars($colorPrimario) ?> 0%, <?= htmlspecialchars($colorSecundario) ?> 100%);
        }
        .whatsapp-btn {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 1000;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7);
            }
            70% {
                box-shadow: 0 0 0 20px rgba(37, 211, 102, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(37, 211, 102, 0);
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Header -->
    <header class="hero-gradient text-white">
        <nav class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <?php if ($logoUrl): ?>
                        <img src="<?= htmlspecialchars($logoUrl) ?>" alt="<?= htmlspecialchars($siteName) ?>" class="h-10 w-auto">
                    <?php else: ?>
                        <i class="fas fa-piggy-bank text-3xl"></i>
                    <?php endif; ?>
                    <span class="font-bold text-xl"><?= htmlspecialchars($siteName) ?></span>
                </div>
                <a href="<?= BASE_URL ?>/auth/login" class="bg-white text-primary-600 px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition">
                    <i class="fas fa-sign-in-alt mr-2"></i>Iniciar Sesión
                </a>
            </div>
        </nav>
        
        <div class="container mx-auto px-6 py-16 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Centro de Soporte Técnico</h1>
            <p class="text-xl text-blue-100 max-w-2xl mx-auto">
                Estamos aquí para ayudarte. Encuentra respuestas a tus preguntas o contáctanos directamente.
            </p>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="container mx-auto px-6 py-12">
        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <!-- WhatsApp Support -->
            <?php if ($whatsappNumero): ?>
            <a href="https://wa.me/<?= htmlspecialchars($whatsappNumero) ?>?text=<?= urlencode($mensajeBienvenida) ?>" 
               target="_blank"
               class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition group">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 group-hover:bg-green-200 transition">
                        <i class="fab fa-whatsapp text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Chat por WhatsApp</h3>
                <p class="text-gray-600 text-sm">Chatea con nuestro equipo de soporte en tiempo real a través de WhatsApp.</p>
            </a>
            <?php endif; ?>
            
            <!-- Email Support -->
            <?php if ($emailContacto): ?>
            <a href="mailto:<?= htmlspecialchars($emailContacto) ?>" 
               class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition group">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 group-hover:bg-blue-200 transition">
                        <i class="fas fa-envelope text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Correo Electrónico</h3>
                <p class="text-gray-600 text-sm">Envíanos un correo y te responderemos a la brevedad posible.</p>
            </a>
            <?php endif; ?>
            
            <!-- Phone Support -->
            <?php if ($telefonoContacto): ?>
            <a href="tel:<?= preg_replace('/[^0-9+]/', '', $telefonoContacto) ?>" 
               class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition group">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 group-hover:bg-purple-200 transition">
                        <i class="fas fa-phone text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Soporte Telefónico</h3>
                <p class="text-gray-600 text-sm">Llámanos directamente para asistencia inmediata.</p>
            </a>
            <?php endif; ?>
        </div>
        
        <!-- FAQ Section -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">
                <i class="fas fa-question-circle text-primary-500 mr-2"></i>Preguntas Frecuentes
            </h2>
            
            <div class="space-y-4" x-data="{ openFaq: null }">
                <!-- FAQ Item 1 -->
                <div class="border border-gray-200 rounded-lg">
                    <button @click="openFaq = openFaq === 1 ? null : 1" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50">
                        <span class="font-medium text-gray-800">¿Cómo puedo recuperar mi contraseña?</span>
                        <i :class="openFaq === 1 ? 'fa-chevron-up' : 'fa-chevron-down'" class="fas text-gray-400"></i>
                    </button>
                    <div x-show="openFaq === 1" x-collapse class="px-6 pb-4 text-gray-600">
                        Para recuperar tu contraseña, haz clic en "¿Olvidaste tu contraseña?" en la página de inicio de sesión e ingresa tu correo electrónico. Recibirás instrucciones para restablecerla.
                    </div>
                </div>
                
                <!-- FAQ Item 2 -->
                <div class="border border-gray-200 rounded-lg">
                    <button @click="openFaq = openFaq === 2 ? null : 2" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50">
                        <span class="font-medium text-gray-800">¿Cómo puedo consultar mi estado de cuenta?</span>
                        <i :class="openFaq === 2 ? 'fa-chevron-up' : 'fa-chevron-down'" class="fas text-gray-400"></i>
                    </button>
                    <div x-show="openFaq === 2" x-collapse class="px-6 pb-4 text-gray-600">
                        Inicia sesión en tu cuenta y ve a la sección "Estado de Cuenta" desde el menú principal. Ahí podrás ver todos tus movimientos, saldos y créditos.
                    </div>
                </div>
                
                <!-- FAQ Item 3 -->
                <div class="border border-gray-200 rounded-lg">
                    <button @click="openFaq = openFaq === 3 ? null : 3" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50">
                        <span class="font-medium text-gray-800">¿Cuál es el horario de atención?</span>
                        <i :class="openFaq === 3 ? 'fa-chevron-up' : 'fa-chevron-down'" class="fas text-gray-400"></i>
                    </button>
                    <div x-show="openFaq === 3" x-collapse class="px-6 pb-4 text-gray-600">
                        Nuestro horario de atención es: <?= htmlspecialchars($horarioAtencion) ?>
                    </div>
                </div>
                
                <!-- FAQ Item 4 -->
                <div class="border border-gray-200 rounded-lg">
                    <button @click="openFaq = openFaq === 4 ? null : 4" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50">
                        <span class="font-medium text-gray-800">¿Cómo puedo realizar un pago?</span>
                        <i :class="openFaq === 4 ? 'fa-chevron-up' : 'fa-chevron-down'" class="fas text-gray-400"></i>
                    </button>
                    <div x-show="openFaq === 4" x-collapse class="px-6 pb-4 text-gray-600">
                        Puedes realizar pagos desde tu portal de cliente, utilizando PayPal o acudiendo directamente a nuestras oficinas. También aceptamos pagos por nómina.
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contact Info -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">
                <i class="fas fa-address-card text-primary-500 mr-2"></i>Información de Contacto
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php if ($telefonoContacto): ?>
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-purple-100 text-purple-600 rounded-full mb-3">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800">Teléfono</h3>
                    <p class="text-gray-600"><?= htmlspecialchars($telefonoContacto) ?></p>
                </div>
                <?php endif; ?>
                
                <?php if ($emailContacto): ?>
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-100 text-blue-600 rounded-full mb-3">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800">Correo Electrónico</h3>
                    <p class="text-gray-600"><?= htmlspecialchars($emailContacto) ?></p>
                </div>
                <?php endif; ?>
                
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-green-100 text-green-600 rounded-full mb-3">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800">Horario de Atención</h3>
                    <p class="text-gray-600"><?= htmlspecialchars($horarioAtencion) ?></p>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="flex items-center space-x-3 mb-4 md:mb-0">
                    <?php if ($logoUrl): ?>
                        <img src="<?= htmlspecialchars($logoUrl) ?>" alt="<?= htmlspecialchars($siteName) ?>" class="h-8 w-auto">
                    <?php else: ?>
                        <i class="fas fa-piggy-bank text-2xl"></i>
                    <?php endif; ?>
                    <span class="font-bold"><?= htmlspecialchars($siteName) ?></span>
                </div>
                <p class="text-gray-400 text-sm"><?= htmlspecialchars($textoCopyright) ?></p>
            </div>
        </div>
    </footer>
    
    <!-- WhatsApp Fixed Button -->
    <?php if ($whatsappNumero): ?>
    <a href="https://wa.me/<?= htmlspecialchars($whatsappNumero) ?>?text=<?= urlencode($mensajeBienvenida) ?>" 
       target="_blank"
       class="whatsapp-btn flex items-center justify-center w-16 h-16 bg-green-500 rounded-full shadow-lg hover:bg-green-600 transition">
        <i class="fab fa-whatsapp text-white text-3xl"></i>
    </a>
    <?php endif; ?>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
