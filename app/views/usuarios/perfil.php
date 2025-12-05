<?php
/**
 * Vista de Perfil de Usuario
 * Sistema de Gestión Integral de Caja de Ahorros
 */
$isCliente = ($_SESSION['user_role'] ?? '') === 'cliente';
?>

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
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
        
        <!-- Profile Header with Avatar -->
        <div class="flex items-center mb-8 pb-6 border-b border-gray-200">
            <div class="relative">
                <?php if (!empty($usuario['avatar'])): ?>
                    <img src="<?= BASE_URL ?>/uploads/avatars/<?= htmlspecialchars($usuario['avatar']) ?>" 
                         alt="Avatar" class="h-24 w-24 rounded-full object-cover border-4 border-blue-100">
                <?php else: ?>
                    <div class="h-24 w-24 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-4xl font-bold border-4 border-blue-200">
                        <?= strtoupper(substr($usuario['nombre'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="ml-6">
                <h2 class="text-xl font-semibold text-gray-800"><?= htmlspecialchars($usuario['nombre']) ?></h2>
                <p class="text-gray-600"><?= htmlspecialchars($usuario['email']) ?></p>
                <span class="inline-block mt-2 px-3 py-1 text-sm font-medium rounded-full 
                    <?php
                    switch($usuario['rol']) {
                        case 'administrador': echo 'bg-purple-100 text-purple-800'; break;
                        case 'operativo': echo 'bg-blue-100 text-blue-800'; break;
                        case 'cliente': echo 'bg-green-100 text-green-800'; break;
                        case 'inversionista': echo 'bg-yellow-100 text-yellow-800'; break;
                        default: echo 'bg-gray-100 text-gray-800';
                    }
                    ?>">
                    <?= ucfirst(htmlspecialchars($usuario['rol'])) ?>
                </span>
            </div>
        </div>
        
        <!-- Profile Update Form -->
        <form method="POST" action="<?= url('usuarios/perfil') ?>" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="update_profile">
            
            <div class="space-y-6">
                <!-- Profile Image Upload -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Imagen de Perfil</h3>
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <?php if (!empty($usuario['avatar'])): ?>
                                <img src="<?= BASE_URL ?>/uploads/avatars/<?= htmlspecialchars($usuario['avatar']) ?>" 
                                     alt="Preview" id="avatar-preview" class="h-20 w-20 rounded-full object-cover border-2 border-gray-200">
                            <?php else: ?>
                                <div id="avatar-preview" class="h-20 w-20 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                    <i class="fas fa-user text-2xl"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1">
                            <input type="file" id="avatar" name="avatar" accept="image/*" 
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                   onchange="previewAvatar(this)">
                            <p class="text-xs text-gray-500 mt-1">JPG, PNG o GIF. Máximo 2MB</p>
                        </div>
                    </div>
                </div>
                
                <!-- Basic Info -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Datos Personales</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
                            <input type="text" id="nombre" name="nombre" 
                                   value="<?= htmlspecialchars($usuario['nombre']) ?>"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                            <input type="email" id="email" value="<?= htmlspecialchars($usuario['email']) ?>" disabled
                                   class="w-full rounded-md border-gray-300 shadow-sm bg-gray-100 px-4 py-2 border">
                            <p class="text-xs text-gray-500 mt-1">El correo no se puede cambiar directamente</p>
                        </div>
                        <div>
                            <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                            <input type="tel" id="telefono" name="telefono" 
                                   value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                        </div>
                        <div>
                            <label for="celular" class="block text-sm font-medium text-gray-700 mb-1">Celular</label>
                            <input type="tel" id="celular" name="celular" 
                                   value="<?= htmlspecialchars($usuario['celular'] ?? '') ?>"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Guardar Cambios
                </button>
            </div>
        </form>
    </div>
    
    <?php if ($isCliente && isset($socio) && $socio): ?>
    <!-- Contact Info Update Request (for clients) -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <i class="fas fa-edit text-blue-600 mr-2"></i>Solicitar Actualización de Datos
        </h3>
        <p class="text-sm text-gray-600 mb-4">
            Si necesitas actualizar tu información de contacto (email, teléfono, dirección), envía una solicitud que será revisada por nuestro personal.
        </p>
        
        <form method="POST" action="<?= url('usuarios/perfil') ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="request_update">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nuevo Email</label>
                    <input type="email" name="nuevo_email" value="<?= htmlspecialchars($usuario['email']) ?>"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nuevo Teléfono</label>
                    <input type="tel" name="nuevo_telefono" value="<?= htmlspecialchars($socio['telefono'] ?? '') ?>"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nuevo Celular</label>
                    <input type="tel" name="nuevo_celular" value="<?= htmlspecialchars($socio['celular'] ?? '') ?>"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nueva Dirección</label>
                    <input type="text" name="nueva_direccion" value="<?= htmlspecialchars($socio['direccion'] ?? '') ?>"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                </div>
            </div>
            
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                <i class="fas fa-paper-plane mr-2"></i>Enviar Solicitud
            </button>
        </form>
        
        <?php if (!empty($solicitudesPendientes)): ?>
        <div class="mt-6 border-t pt-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Solicitudes Recientes</h4>
            <div class="space-y-2">
                <?php foreach ($solicitudesPendientes as $sol): ?>
                    <div class="flex items-center justify-between text-sm p-2 bg-gray-50 rounded">
                        <span class="text-gray-600"><?= date('d/m/Y H:i', strtotime($sol['created_at'])) ?></span>
                        <span class="px-2 py-1 rounded-full text-xs <?= $sol['estatus'] === 'pendiente' ? 'bg-yellow-100 text-yellow-800' : ($sol['estatus'] === 'aprobado' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') ?>">
                            <?= ucfirst($sol['estatus']) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <!-- Password Change Section -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="POST" action="<?= url('usuarios/perfil') ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="update_password">
            
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Cambiar Contraseña</h3>
                    <p class="text-sm text-gray-500 mb-4">Deja los campos en blanco si no deseas cambiar tu contraseña</p>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="password_actual" class="block text-sm font-medium text-gray-700 mb-1">Contraseña Actual</label>
                            <input type="password" id="password_actual" name="password_actual"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="password_nueva" class="block text-sm font-medium text-gray-700 mb-1">Nueva Contraseña</label>
                                <input type="password" id="password_nueva" name="password_nueva" minlength="6"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                            </div>
                            <div>
                                <label for="password_confirmar" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nueva Contraseña</label>
                                <input type="password" id="password_confirmar" name="password_confirmar"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-key mr-2"></i>Cambiar Contraseña
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewAvatar(input) {
    const preview = document.getElementById('avatar-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                // Replace div with img
                const img = document.createElement('img');
                img.id = 'avatar-preview';
                img.src = e.target.result;
                img.alt = 'Preview';
                img.className = 'h-20 w-20 rounded-full object-cover border-2 border-gray-200';
                preview.replaceWith(img);
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
