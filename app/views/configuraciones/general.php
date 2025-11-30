<?php
/**
 * Configuración General
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6">
    <a href="<?= url('configuraciones') ?>" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Volver a Configuraciones
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
        
        <?php if (!empty($success)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p><?= htmlspecialchars($success) ?></p>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?= url('configuraciones/general') ?>" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="space-y-6">
                <div>
                    <label for="nombre_sitio" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-building mr-2"></i>Nombre del Sitio
                    </label>
                    <input type="text" id="nombre_sitio" name="nombre_sitio" 
                           value="<?= htmlspecialchars($config['nombre_sitio'] ?? 'Caja de Ahorros') ?>"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-image mr-2"></i>Logotipo
                    </label>
                    <?php if (!empty($config['logo'])): ?>
                        <div class="mb-2">
                            <img src="<?= asset('images/' . $config['logo']) ?>" alt="Logo actual" class="h-16 object-contain">
                            <p class="text-sm text-gray-500 mt-1">Logo actual: <?= htmlspecialchars($config['logo']) ?></p>
                        </div>
                    <?php endif; ?>
                    <input type="file" id="logo" name="logo" accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-sm text-gray-500 mt-1">Formatos permitidos: JPG, JPEG, PNG, GIF</p>
                </div>
                
                <div>
                    <label for="telefono_contacto" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-phone mr-2"></i>Teléfonos de Contacto
                    </label>
                    <input type="text" id="telefono_contacto" name="telefono_contacto" 
                           value="<?= htmlspecialchars($config['telefono_contacto'] ?? '') ?>"
                           placeholder="Ej: (442) 123-4567, (442) 765-4321"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div>
                    <label for="horario_atencion" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-clock mr-2"></i>Horarios de Atención
                    </label>
                    <textarea id="horario_atencion" name="horario_atencion" rows="3"
                              placeholder="Ej: Lunes a Viernes: 9:00 - 17:00&#10;Sábados: 9:00 - 13:00"
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?= htmlspecialchars($config['horario_atencion'] ?? '') ?></textarea>
                </div>
            </div>
            
            <div class="mt-8 flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Guardar Configuración
                </button>
            </div>
        </form>
    </div>
</div>
