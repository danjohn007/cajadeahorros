<?php
/**
 * Vista de Perfil de Usuario
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6"><?= htmlspecialchars($pageTitle) ?></h1>
        
        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p><?= htmlspecialchars($success) ?></p>
            </div>
        <?php endif; ?>
        
        <!-- Información del usuario -->
        <div class="flex items-center mb-8 pb-6 border-b border-gray-200">
            <div class="h-20 w-20 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-3xl font-bold">
                <?= strtoupper(substr($usuario['nombre'], 0, 1)) ?>
            </div>
            <div class="ml-6">
                <h2 class="text-xl font-semibold text-gray-800"><?= htmlspecialchars($usuario['nombre']) ?></h2>
                <p class="text-gray-600"><?= htmlspecialchars($usuario['email']) ?></p>
                <span class="inline-block mt-2 px-3 py-1 text-sm font-medium rounded-full 
                    <?php
                    switch($usuario['rol']) {
                        case 'administrador': echo 'bg-purple-100 text-purple-800'; break;
                        case 'operativo': echo 'bg-blue-100 text-blue-800'; break;
                        default: echo 'bg-gray-100 text-gray-800';
                    }
                    ?>">
                    <?= ucfirst(htmlspecialchars($usuario['rol'])) ?>
                </span>
            </div>
        </div>
        
        <form method="POST" action="<?= url('usuarios/perfil') ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="space-y-6">
                <!-- Datos básicos -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Datos Personales</h3>
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
                        <input type="text" id="nombre" name="nombre" 
                               value="<?= htmlspecialchars($usuario['nombre']) ?>"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
                
                <!-- Cambiar contraseña -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Cambiar Contraseña</h3>
                    <p class="text-sm text-gray-500 mb-4">Deja los campos en blanco si no deseas cambiar tu contraseña</p>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="password_actual" class="block text-sm font-medium text-gray-700 mb-1">Contraseña Actual</label>
                            <input type="password" id="password_actual" name="password_actual"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="password_nueva" class="block text-sm font-medium text-gray-700 mb-1">Nueva Contraseña</label>
                                <input type="password" id="password_nueva" name="password_nueva" minlength="6"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="password_confirmar" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nueva Contraseña</label>
                                <input type="password" id="password_confirmar" name="password_confirmar"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
