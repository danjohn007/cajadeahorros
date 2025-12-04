<?php
/**
 * Configuración de API Buró de Crédito
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">🏦 <?= htmlspecialchars($pageTitle) ?></h1>
        <p class="text-gray-600">Configuración de la API del Buró de Crédito y costos de consulta</p>
    </div>
    <a href="<?= url('configuraciones') ?>" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Volver a Configuración
    </a>
</div>

<div class="max-w-4xl mx-auto">
    <?php if (!empty($success)): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <p><?= htmlspecialchars($success) ?></p>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="<?= url('configuraciones/buro') ?>" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        
        <!-- Habilitar Servicio -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-power-off mr-2 text-green-600"></i>Estado del Servicio
            </h2>
            
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-medium text-gray-800">Habilitar Consulta Pública al Buró de Crédito</p>
                    <p class="text-sm text-gray-500">Permite que usuarios externos consulten su historial crediticio mediante pago con PayPal</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="buro_api_enabled" value="1" 
                           <?= ($config['buro_api_enabled'] ?? '0') === '1' ? 'checked' : '' ?>
                           class="sr-only peer">
                    <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>
            
            <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <p class="text-sm text-blue-700">
                    <i class="fas fa-info-circle mr-1"></i>
                    La URL pública de consulta es: <strong><?= url('buro/consulta') ?></strong>
                </p>
            </div>
        </div>
        
        <!-- Costo de Consulta -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-dollar-sign mr-2 text-yellow-600"></i>Costo de Consulta
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="buro_costo_consulta" class="block text-sm font-medium text-gray-700 mb-1">
                        Precio por Consulta (MXN)
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                        <input type="number" id="buro_costo_consulta" name="buro_costo_consulta" 
                               value="<?= htmlspecialchars($config['buro_costo_consulta'] ?? '50.00') ?>"
                               step="0.01" min="0"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 pl-8 pr-4 py-2 border">
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Este monto se cobrará a través de PayPal antes de realizar la consulta</p>
                </div>
                
                <div class="flex items-center">
                    <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200 w-full">
                        <p class="text-sm text-yellow-700">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            <strong>Importante:</strong> Asegúrese de que PayPal esté configurado correctamente en 
                            <a href="<?= url('configuraciones/paypal') ?>" class="underline">Configuración de Pagos</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Credenciales de API -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-key mr-2 text-purple-600"></i>Credenciales de la API
            </h2>
            
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="buro_api_url" class="block text-sm font-medium text-gray-700 mb-1">
                        URL de la API
                    </label>
                    <input type="url" id="buro_api_url" name="buro_api_url" 
                           value="<?= htmlspecialchars($config['buro_api_url'] ?? 'https://apif.burodecredito.com.mx') ?>"
                           placeholder="https://apif.burodecredito.com.mx"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="buro_api_username" class="block text-sm font-medium text-gray-700 mb-1">
                            Usuario de API
                        </label>
                        <input type="text" id="buro_api_username" name="buro_api_username" 
                               value="<?= htmlspecialchars($config['buro_api_username'] ?? '') ?>"
                               placeholder="usuario@empresa.com"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                    </div>
                    
                    <div>
                        <label for="buro_api_password" class="block text-sm font-medium text-gray-700 mb-1">
                            Contraseña de API
                        </label>
                        <input type="password" id="buro_api_password" name="buro_api_password" 
                               placeholder="<?= !empty($config['buro_api_password']) ? '••••••••' : 'Ingrese la contraseña' ?>"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                        <p class="text-sm text-gray-500 mt-1">Dejar en blanco para mantener la actual</p>
                    </div>
                </div>
                
                <div>
                    <label for="buro_api_key" class="block text-sm font-medium text-gray-700 mb-1">
                        API Key
                    </label>
                    <input type="text" id="buro_api_key" name="buro_api_key" 
                           value="<?= htmlspecialchars($config['buro_api_key'] ?? '') ?>"
                           placeholder="sk_live_xxxxxxxxxxxx"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border font-mono">
                </div>
            </div>
            
            <div class="mt-4 p-4 bg-gray-50 rounded-lg border">
                <p class="text-sm text-gray-600">
                    <i class="fas fa-info-circle mr-1 text-blue-500"></i>
                    <strong>Nota:</strong> Si no se configuran las credenciales, el sistema funcionará en modo demostración 
                    con datos simulados. Para consultas reales, obtenga sus credenciales en 
                    <a href="https://apif.burodecredito.com.mx/pages/bc/nuestras-apis.html" target="_blank" class="text-blue-600 hover:underline">
                        apif.burodecredito.com.mx
                    </a>
                </p>
            </div>
        </div>
        
        <!-- Información de la API -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-book mr-2 text-blue-600"></i>Información sobre la API
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-medium text-gray-800 mb-2">¿Qué es el Buró de Crédito?</h3>
                    <p class="text-sm text-gray-600">
                        El Buró de Crédito es una Sociedad de Información Crediticia que proporciona 
                        información sobre el comportamiento crediticio de personas físicas y morales en México.
                    </p>
                </div>
                
                <div>
                    <h3 class="font-medium text-gray-800 mb-2">APIs Disponibles</h3>
                    <ul class="text-sm text-gray-600 list-disc list-inside space-y-1">
                        <li>Consulta de Persona Física</li>
                        <li>Consulta de Persona Moral</li>
                        <li>Score de Crédito</li>
                        <li>Alertas de Fraude</li>
                    </ul>
                </div>
            </div>
            
            <div class="mt-4 pt-4 border-t">
                <a href="https://apif.burodecredito.com.mx/pages/bc/nuestras-apis.html" target="_blank" 
                   class="inline-flex items-center text-blue-600 hover:text-blue-800">
                    <i class="fas fa-external-link-alt mr-2"></i>
                    Ver documentación completa de la API
                </a>
            </div>
        </div>
        
        <!-- Botones -->
        <div class="flex justify-end space-x-4">
            <a href="<?= url('configuraciones') ?>" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i>Guardar Configuración
            </button>
        </div>
    </form>
</div>
