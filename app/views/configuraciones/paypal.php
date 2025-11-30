<?php
/**
 * Configuración de PayPal
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
        
        <?php if (!empty($success)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p><?= htmlspecialchars($success) ?></p>
            </div>
        <?php endif; ?>
        
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fab fa-paypal text-yellow-400 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        Para obtener tus credenciales de PayPal, visita 
                        <a href="https://developer.paypal.com" target="_blank" class="underline font-medium">developer.paypal.com</a> 
                        y crea una aplicación.
                    </p>
                </div>
            </div>
        </div>
        
        <form method="POST" action="<?= url('configuraciones/paypal') ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="space-y-6">
                <div>
                    <label for="paypal_client_id" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-key mr-2"></i>Client ID
                    </label>
                    <input type="text" id="paypal_client_id" name="paypal_client_id" 
                           value="<?= htmlspecialchars($config['paypal_client_id'] ?? '') ?>"
                           placeholder="Tu Client ID de PayPal"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div>
                    <label for="paypal_secret" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-lock mr-2"></i>Secret Key
                    </label>
                    <input type="password" id="paypal_secret" name="paypal_secret" 
                           placeholder="<?= !empty($config['paypal_secret']) ? '••••••••••••' : 'Tu Secret Key de PayPal' ?>"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="text-sm text-gray-500 mt-1">Deja en blanco para mantener la actual</p>
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
