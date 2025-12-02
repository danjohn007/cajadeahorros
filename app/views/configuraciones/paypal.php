<?php
/**
 * Configuración de Pagos (PayPal)
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">💳 Configuración de Pagos</h1>
        <p class="text-gray-600">Configuración de PayPal y otros métodos de pago</p>
    </div>
    <a href="<?= url('configuraciones') ?>" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Volver a Configuración
    </a>
</div>

<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        
        <?php if (!empty($success)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p><?= htmlspecialchars($success) ?></p>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?= url('configuraciones/paypal') ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="space-y-6">
                <!-- Habilitar PayPal -->
                <div class="flex items-center">
                    <input type="checkbox" id="paypal_enabled" name="paypal_enabled" value="1"
                           <?= ($config['paypal_enabled'] ?? '0') === '1' ? 'checked' : '' ?>
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="paypal_enabled" class="ml-2 block text-sm text-gray-700">
                        Habilitar pagos con PayPal
                    </label>
                </div>
                
                <!-- Modo de PayPal -->
                <div>
                    <label for="paypal_mode" class="block text-sm font-medium text-gray-700 mb-1">
                        Modo de PayPal
                    </label>
                    <select id="paypal_mode" name="paypal_mode" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                        <option value="sandbox" <?= ($config['paypal_mode'] ?? 'sandbox') === 'sandbox' ? 'selected' : '' ?>>Sandbox (Pruebas)</option>
                        <option value="live" <?= ($config['paypal_mode'] ?? '') === 'live' ? 'selected' : '' ?>>Live (Producción)</option>
                    </select>
                </div>
                
                <!-- Client ID -->
                <div>
                    <label for="paypal_client_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Client ID de PayPal
                    </label>
                    <input type="text" id="paypal_client_id" name="paypal_client_id" 
                           value="<?= htmlspecialchars($config['paypal_client_id'] ?? '') ?>"
                           placeholder="AXXXxxxxXXXxxx..."
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                </div>
                
                <!-- Secret Key -->
                <div>
                    <label for="paypal_secret" class="block text-sm font-medium text-gray-700 mb-1">
                        Secret de PayPal
                    </label>
                    <input type="password" id="paypal_secret" name="paypal_secret" 
                           placeholder="<?= !empty($config['paypal_secret']) ? 'EXXXxxxxXXXxxx...' : 'Ingrese el Secret Key' ?>"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                    <p class="text-sm text-gray-500 mt-1">Dejar en blanco para mantener el actual</p>
                </div>
            </div>
            
            <div class="mt-8 flex justify-end space-x-4">
                <a href="<?= url('configuraciones') ?>" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Volver
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Guardar Configuración
                </button>
            </div>
        </form>
        
        <!-- Información de ayuda -->
        <div class="mt-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <h3 class="font-medium text-blue-800 mb-2">
                <i class="fas fa-info-circle mr-2"></i>Cómo obtener credenciales de PayPal
            </h3>
            <ol class="list-decimal list-inside text-sm text-blue-700 space-y-1">
                <li>Accede a <a href="https://developer.paypal.com" target="_blank" class="underline font-medium">developer.paypal.com</a></li>
                <li>Crea una cuenta de desarrollador si no tienes una</li>
                <li>Ve a "My Apps & Credentials"</li>
                <li>Crea una nueva app para obtener Client ID y Secret</li>
                <li>Usa las credenciales de Sandbox para pruebas</li>
            </ol>
        </div>
    </div>
</div>
