<?php
/**
 * Vista para clientes sin vínculo con socio
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="max-w-lg mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8 text-center">
        <div class="text-yellow-500 mb-4">
            <i class="fas fa-user-slash text-6xl"></i>
        </div>
        
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Cuenta No Vinculada</h1>
        
        <p class="text-gray-600 mb-6">
            Tu cuenta de usuario aún no está vinculada a un registro de socio en nuestro sistema.
        </p>
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h3 class="font-medium text-blue-800 mb-2">
                <i class="fas fa-info-circle mr-2"></i>¿Qué puedes hacer?
            </h3>
            <ul class="text-sm text-blue-700 text-left list-disc list-inside space-y-1">
                <li>Contacta a la oficina de la Caja de Ahorros para vincular tu cuenta</li>
                <li>Asegúrate de que tu correo electrónico sea el mismo registrado como socio</li>
                <li>Si eres nuevo, acude a nuestras oficinas para registrarte como socio</li>
            </ul>
        </div>
        
        <div class="flex flex-col space-y-3">
            <a href="<?= url('usuarios/perfil') ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-user-cog mr-2"></i>Ver Mi Perfil
            </a>
            <a href="<?= url('auth/logout') ?>" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
            </a>
        </div>
    </div>
    
    <div class="mt-6 text-center text-gray-500 text-sm">
        <p>
            <i class="fas fa-phone mr-1"></i>
            Teléfono de Contacto: <?= htmlspecialchars(getConfig('telefono_contacto', 'No disponible')) ?>
        </p>
        <p class="mt-1">
            <i class="fas fa-envelope mr-1"></i>
            Email: <?= htmlspecialchars(getConfig('email_contacto', getConfig('correo_sistema', 'No disponible'))) ?>
        </p>
    </div>
</div>
