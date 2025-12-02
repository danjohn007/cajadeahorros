<?php
/**
 * Vista de Crear Dispositivo
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6">
    <a href="<?= url('dispositivos') ?>" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Volver a Dispositivos
    </a>
    <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
    <p class="text-gray-600">Registrar un nuevo dispositivo IoT</p>
</div>

<?php if (!empty($errors)): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
        <ul class="list-disc list-inside">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="POST" action="<?= url('dispositivos/crear') ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="space-y-6">
                <!-- Nombre -->
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre del Dispositivo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nombre" name="nombre" required
                           placeholder="Ej: Sensor Entrada Principal"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                </div>
                
                <!-- Tipo -->
                <div>
                    <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">
                        Tipo de Dispositivo <span class="text-red-500">*</span>
                    </label>
                    <select id="tipo" name="tipo" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                        <option value="">Seleccionar tipo...</option>
                        <option value="shelly">Shelly Cloud</option>
                        <option value="hikvision">HikVision</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                
                <!-- Modelo -->
                <div>
                    <label for="modelo" class="block text-sm font-medium text-gray-700 mb-1">
                        Modelo
                    </label>
                    <input type="text" id="modelo" name="modelo"
                           placeholder="Ej: Shelly 1PM, DS-2CD2143G0-I"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                </div>
                
                <!-- Ubicación -->
                <div>
                    <label for="ubicacion" class="block text-sm font-medium text-gray-700 mb-1">
                        Ubicación
                    </label>
                    <input type="text" id="ubicacion" name="ubicacion"
                           placeholder="Ej: Entrada principal, Oficina 101"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                </div>
                
                <!-- IP Address -->
                <div>
                    <label for="ip_address" class="block text-sm font-medium text-gray-700 mb-1">
                        Dirección IP
                    </label>
                    <input type="text" id="ip_address" name="ip_address"
                           placeholder="Ej: 192.168.1.100"
                           pattern="^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                </div>
                
                <!-- MAC Address -->
                <div>
                    <label for="mac_address" class="block text-sm font-medium text-gray-700 mb-1">
                        Dirección MAC
                    </label>
                    <input type="text" id="mac_address" name="mac_address"
                           placeholder="Ej: AA:BB:CC:DD:EE:FF"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                </div>
                
                <!-- Descripción -->
                <div>
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">
                        Descripción
                    </label>
                    <textarea id="descripcion" name="descripcion" rows="3"
                              placeholder="Descripción o notas adicionales..."
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border"></textarea>
                </div>
            </div>
            
            <div class="mt-8 flex justify-end space-x-4">
                <a href="<?= url('dispositivos') ?>" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Crear Dispositivo
                </button>
            </div>
        </form>
    </div>
    
    <!-- Información adicional -->
    <div class="mt-6 bg-blue-50 rounded-lg p-4">
        <h3 class="font-medium text-blue-800 mb-2">
            <i class="fas fa-info-circle mr-1"></i>Información
        </h3>
        <ul class="text-sm text-blue-700 space-y-1">
            <li>• Después de crear el dispositivo, podrá configurar las credenciales específicas de cada tipo.</li>
            <li>• Los dispositivos Shelly requieren Cloud Key y Auth Token para control remoto.</li>
            <li>• Los dispositivos HikVision requieren usuario, contraseña y puertos de conexión.</li>
        </ul>
    </div>
</div>
