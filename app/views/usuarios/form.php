<?php
/**
 * Formulario de Usuario
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6">
    <a href="<?= url('usuarios') ?>" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Volver a Usuarios
    </a>
</div>

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
        
        <form method="POST" action="<?= url('usuarios/' . $action . (isset($usuario['id']) ? '/' . $usuario['id'] : '')) ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="space-y-6">
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo *</label>
                    <input type="text" id="nombre" name="nombre" required
                           value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico *</label>
                    <input type="email" id="email" name="email" required
                           value="<?= htmlspecialchars($usuario['email'] ?? '') ?>"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div>
                    <label for="rol" class="block text-sm font-medium text-gray-700 mb-1">Rol *</label>
                    <select id="rol" name="rol" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="consulta" <?= ($usuario['rol'] ?? '') == 'consulta' ? 'selected' : '' ?>>Consulta</option>
                        <option value="operativo" <?= ($usuario['rol'] ?? '') == 'operativo' ? 'selected' : '' ?>>Operativo</option>
                        <option value="administrador" <?= ($usuario['rol'] ?? '') == 'administrador' ? 'selected' : '' ?>>Administrador</option>
                    </select>
                    <p class="mt-1 text-sm text-gray-500">
                        <strong>Consulta:</strong> Solo puede ver información<br>
                        <strong>Operativo:</strong> Puede capturar y modificar información<br>
                        <strong>Administrador:</strong> Acceso total al sistema
                    </p>
                </div>
                
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <?= $action == 'crear' ? 'Contraseña' : 'Cambiar Contraseña' ?>
                    </h3>
                    
                    <?php if ($action == 'editar'): ?>
                        <p class="text-sm text-gray-500 mb-4">Deja los campos en blanco si no deseas cambiar la contraseña</p>
                    <?php endif; ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                Contraseña <?= $action == 'crear' ? '*' : '' ?>
                            </label>
                            <input type="password" id="password" name="password" <?= $action == 'crear' ? 'required' : '' ?>
                                   minlength="6"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="password_confirm" class="block text-sm font-medium text-gray-700 mb-1">
                                Confirmar Contraseña <?= $action == 'crear' ? '*' : '' ?>
                            </label>
                            <input type="password" id="password_confirm" name="password_confirm" <?= $action == 'crear' ? 'required' : '' ?>
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
                
                <?php if ($action == 'editar'): ?>
                    <div class="flex items-center">
                        <input type="checkbox" id="activo" name="activo" value="1"
                               <?= ($usuario['activo'] ?? 1) ? 'checked' : '' ?>
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="activo" class="ml-2 block text-sm text-gray-900">
                            Usuario activo
                        </label>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="mt-8 flex justify-end space-x-4">
                <a href="<?= url('usuarios') ?>" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <?= $action == 'crear' ? 'Crear Usuario' : 'Guardar Cambios' ?>
                </button>
            </div>
        </form>
    </div>
</div>
