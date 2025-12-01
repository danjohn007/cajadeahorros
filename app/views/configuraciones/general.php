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

<div class="max-w-3xl mx-auto">
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
            
            <!-- Logo del Sitio -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <label class="block text-sm font-medium text-gray-700 mb-2">Logo del Sitio</label>
                <?php if (!empty($config['logo'])): ?>
                    <div class="mb-3">
                        <img src="<?= asset('images/' . $config['logo']) ?>" alt="Logo actual" class="h-20 object-contain">
                    </div>
                <?php endif; ?>
                <input type="file" id="logo" name="logo" accept="image/jpeg,image/png,image/svg+xml,image/gif"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-sm text-gray-500 mt-1">Formatos aceptados: JPG, PNG, SVG. Tamaño máximo: 2MB. Validado en el servidor.</p>
            </div>
            
            <div class="space-y-6">
                <!-- Nombre del Sitio -->
                <div>
                    <label for="nombre_sitio" class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre del Sitio <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nombre_sitio" name="nombre_sitio" required
                           value="<?= htmlspecialchars($config['nombre_sitio'] ?? 'Caja de Ahorros') ?>"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                </div>
                
                <!-- Email de Contacto -->
                <div>
                    <label for="email_contacto" class="block text-sm font-medium text-gray-700 mb-1">
                        Email de Contacto <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email_contacto" name="email_contacto" 
                           value="<?= htmlspecialchars($config['email_contacto'] ?? '') ?>"
                           placeholder="contacto@ejemplo.com"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                </div>
                
                <!-- Teléfono de Contacto -->
                <div>
                    <label for="telefono_contacto" class="block text-sm font-medium text-gray-700 mb-1">
                        Teléfono de Contacto <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="telefono_contacto" name="telefono_contacto" 
                           value="<?= htmlspecialchars($config['telefono_contacto'] ?? '') ?>"
                           placeholder="Ej: (442) 123-4567"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                </div>
                
                <!-- Cuota de Mantenimiento -->
                <div>
                    <label for="cuota_mantenimiento" class="block text-sm font-medium text-gray-700 mb-1">
                        Cuota de Mantenimiento por Defecto
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">$</span>
                        <input type="number" id="cuota_mantenimiento" name="cuota_mantenimiento" 
                               value="<?= htmlspecialchars($config['cuota_mantenimiento'] ?? '1500') ?>"
                               step="0.01" min="0"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 pl-8 pr-4 py-2 border">
                    </div>
                </div>
                
                <!-- Horarios de Atención -->
                <div>
                    <label for="horario_atencion" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-clock mr-2"></i>Horarios de Atención
                    </label>
                    <textarea id="horario_atencion" name="horario_atencion" rows="3"
                              placeholder="Ej: Lunes a Viernes: 9:00 - 17:00&#10;Sábados: 9:00 - 13:00"
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border"><?= htmlspecialchars($config['horario_atencion'] ?? '') ?></textarea>
                </div>
                
                <!-- Texto de Copyright -->
                <div>
                    <label for="texto_copyright" class="block text-sm font-medium text-gray-700 mb-1">
                        Texto de Copyright
                    </label>
                    <input type="text" id="texto_copyright" name="texto_copyright" 
                           value="<?= htmlspecialchars($config['texto_copyright'] ?? '© ' . date('Y') . ' Sistema. Todos los derechos reservados.') ?>"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                    <p class="text-sm text-gray-500 mt-1">Texto que aparece en el pie de página del sistema</p>
                </div>
            </div>
            
            <div class="mt-8 flex justify-end space-x-4">
                <a href="<?= url('configuraciones') ?>" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Volver
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
