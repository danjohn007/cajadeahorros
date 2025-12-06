<?php
/**
 * Vista de Configuraciones
 * Sistema de Gestión Integral de Caja de Ahorros
 */
$isProgramador = ($_SESSION['user_role'] ?? '') === 'programador';
?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
    <p class="text-gray-600">Administración de configuraciones globales del sistema</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Configuración General -->
    <a href="<?= url('configuraciones/general') ?>" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center mb-4">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-cog text-2xl"></i>
            </div>
        </div>
        <h2 class="text-lg font-semibold text-gray-800 mb-2">Configuración General</h2>
        <p class="text-gray-600 text-sm">Nombre del sitio, logotipo, teléfonos y horarios de atención</p>
    </a>
    
    <!-- Configuración de Correo -->
    <a href="<?= url('configuraciones/correo') ?>" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center mb-4">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-envelope text-2xl"></i>
            </div>
        </div>
        <h2 class="text-lg font-semibold text-gray-800 mb-2">Configuración de Correo</h2>
        <p class="text-gray-600 text-sm">Correo principal que envía los mensajes del sistema</p>
    </a>
    
    <!-- Estilos -->
    <a href="<?= url('configuraciones/estilos') ?>" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center mb-4">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <i class="fas fa-palette text-2xl"></i>
            </div>
        </div>
        <h2 class="text-lg font-semibold text-gray-800 mb-2">Estilos del Sistema</h2>
        <p class="text-gray-600 text-sm">Cambiar colores principales del sistema</p>
    </a>
    
    <!-- Chatbot / WhatsApp -->
    <a href="<?= url('configuraciones/chatbot') ?>" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center mb-4">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fab fa-whatsapp text-2xl"></i>
            </div>
        </div>
        <h2 class="text-lg font-semibold text-gray-800 mb-2">Chatbot / WhatsApp</h2>
        <p class="text-gray-600 text-sm">Configurar el chatbot y mensajes predeterminados de WhatsApp</p>
    </a>
    
    <!-- PayPal -->
    <a href="<?= url('configuraciones/paypal') ?>" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center mb-4">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                <i class="fab fa-paypal text-2xl"></i>
            </div>
        </div>
        <h2 class="text-lg font-semibold text-gray-800 mb-2">PayPal</h2>
        <p class="text-gray-600 text-sm">Configurar la cuenta de PayPal para pagos</p>
    </a>
    
    <!-- API Buró de Crédito -->
    <a href="<?= url('configuraciones/buro') ?>" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center mb-4">
            <div class="p-3 rounded-full bg-teal-100 text-teal-600">
                <i class="fas fa-search-dollar text-2xl"></i>
            </div>
        </div>
        <h2 class="text-lg font-semibold text-gray-800 mb-2">API Buró de Crédito</h2>
        <p class="text-gray-600 text-sm">Configurar credenciales y costos de consulta al Buró</p>
    </a>
    
    <!-- Generador QR -->
    <a href="<?= url('configuraciones/qr') ?>" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center mb-4">
            <div class="p-3 rounded-full bg-gray-100 text-gray-600">
                <i class="fas fa-qrcode text-2xl"></i>
            </div>
        </div>
        <h2 class="text-lg font-semibold text-gray-800 mb-2">Generador de QR</h2>
        <p class="text-gray-600 text-sm">API para crear códigos QR masivos</p>
    </a>
    
    <!-- Usuarios -->
    <a href="<?= url('usuarios') ?>" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center mb-4">
            <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                <i class="fas fa-users-cog text-2xl"></i>
            </div>
        </div>
        <h2 class="text-lg font-semibold text-gray-800 mb-2">Usuarios del Sistema</h2>
        <p class="text-gray-600 text-sm">Gestión de usuarios y permisos de acceso</p>
    </a>
    
    <!-- Bitácora -->
    <a href="<?= url('bitacora') ?>" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center mb-4">
            <div class="p-3 rounded-full bg-red-100 text-red-600">
                <i class="fas fa-history text-2xl"></i>
            </div>
        </div>
        <h2 class="text-lg font-semibold text-gray-800 mb-2">Bitácora</h2>
        <p class="text-gray-600 text-sm">Registro de acciones y cambios en el sistema</p>
    </a>
</div>

<?php if ($isProgramador): ?>
<!-- Sección de Módulos Especiales (Solo para Programadores) -->
<div class="mt-8">
    <h2 class="text-xl font-bold text-gray-800 mb-4">
        <i class="fas fa-code text-orange-600 mr-2"></i>Módulos Especiales
        <span class="text-sm font-normal text-orange-600 ml-2">(Solo Programadores)</span>
    </h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Módulos del Sistema -->
        <a href="<?= url('configuraciones/modulos') ?>" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow border-l-4 border-orange-500">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                    <i class="fas fa-cogs text-2xl"></i>
                </div>
            </div>
            <h2 class="text-lg font-semibold text-gray-800 mb-2">Módulos del Sistema</h2>
            <p class="text-gray-600 text-sm">Habilitar o deshabilitar módulos especiales del sistema</p>
        </a>
    </div>
</div>
<?php endif; ?>

<!-- Información del Sistema -->
<div class="mt-8 bg-white rounded-lg shadow-md p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-info-circle mr-2 text-blue-600"></i>Información del Sistema
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <p class="text-sm text-gray-500">Versión del Sistema</p>
            <p class="font-medium">1.0.0</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">PHP Versión</p>
            <p class="font-medium"><?= phpversion() ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Servidor</p>
            <p class="font-medium"><?= php_uname('s') ?> <?= php_uname('r') ?></p>
        </div>
    </div>
</div>
