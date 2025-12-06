<?php
/**
 * Vista de Configuración de Módulos Especiales
 * Sistema de Gestión Integral de Caja de Ahorros
 * Solo accesible por usuarios con rol 'programador'
 */
?>

<div class="mb-6">
    <a href="<?= url('configuraciones') ?>" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-1"></i> Volver a Configuraciones
    </a>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center mb-6">
        <div class="p-3 rounded-full bg-orange-100 text-orange-600 mr-4">
            <i class="fas fa-cogs text-2xl"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
            <p class="text-gray-600">Controla la visibilidad de los módulos especiales del sistema</p>
        </div>
    </div>
    
    <?php if (!empty($success)): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p><?= htmlspecialchars($success) ?></p>
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
    
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    <strong>Nota:</strong> Los módulos deshabilitados no serán visibles para los usuarios del sistema, excepto para los usuarios con rol 'Programador'.
                </p>
            </div>
        </div>
    </div>
    
    <form method="POST" action="<?= url('configuraciones/modulos') ?>">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        
        <div class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Deshabilitar Módulos</h3>
            <p class="text-sm text-gray-600 mb-4">Marca los módulos que deseas ocultar del menú principal del sistema:</p>
            
            <!-- Módulo Financiero -->
            <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                <input type="checkbox" id="deshabilitar_financiero" name="deshabilitar_financiero" 
                       <?= in_array('financiero', $modulosDeshabilitados) ? 'checked' : '' ?>
                       class="h-5 w-5 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                <label for="deshabilitar_financiero" class="ml-3 flex-1">
                    <span class="block font-medium text-gray-800"><i class="fas fa-coins mr-2 text-yellow-600"></i>Módulo Financiero</span>
                    <span class="block text-sm text-gray-500">Gestión de transacciones, categorías y reportes financieros</span>
                </label>
            </div>
            
            <!-- Membresías -->
            <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                <input type="checkbox" id="deshabilitar_membresias" name="deshabilitar_membresias" 
                       <?= in_array('membresias', $modulosDeshabilitados) ? 'checked' : '' ?>
                       class="h-5 w-5 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                <label for="deshabilitar_membresias" class="ml-3 flex-1">
                    <span class="block font-medium text-gray-800"><i class="fas fa-id-card mr-2 text-blue-600"></i>Membresías</span>
                    <span class="block text-sm text-gray-500">Gestión de tipos de membresía y renovaciones</span>
                </label>
            </div>
            
            <!-- Inversionistas -->
            <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                <input type="checkbox" id="deshabilitar_inversionistas" name="deshabilitar_inversionistas" 
                       <?= in_array('inversionistas', $modulosDeshabilitados) ? 'checked' : '' ?>
                       class="h-5 w-5 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                <label for="deshabilitar_inversionistas" class="ml-3 flex-1">
                    <span class="block font-medium text-gray-800"><i class="fas fa-chart-line mr-2 text-green-600"></i>Inversionistas</span>
                    <span class="block text-sm text-gray-500">Gestión de inversionistas e inversiones</span>
                </label>
            </div>
            
            <!-- Informe CRM -->
            <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                <input type="checkbox" id="deshabilitar_crm" name="deshabilitar_crm" 
                       <?= in_array('crm', $modulosDeshabilitados) ? 'checked' : '' ?>
                       class="h-5 w-5 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                <label for="deshabilitar_crm" class="ml-3 flex-1">
                    <span class="block font-medium text-gray-800"><i class="fas fa-address-book mr-2 text-purple-600"></i>Informe CRM</span>
                    <span class="block text-sm text-gray-500">Métricas de clientes, segmentos y customer journey</span>
                </label>
            </div>
            
            <!-- Sistema KYC -->
            <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                <input type="checkbox" id="deshabilitar_kyc" name="deshabilitar_kyc" 
                       <?= in_array('kyc', $modulosDeshabilitados) ? 'checked' : '' ?>
                       class="h-5 w-5 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                <label for="deshabilitar_kyc" class="ml-3 flex-1">
                    <span class="block font-medium text-gray-800"><i class="fas fa-user-shield mr-2 text-indigo-600"></i>Sistema KYC</span>
                    <span class="block text-sm text-gray-500">Know Your Customer - Verificación de identidad</span>
                </label>
            </div>
            
            <!-- Sistema ESCROW -->
            <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                <input type="checkbox" id="deshabilitar_escrow" name="deshabilitar_escrow" 
                       <?= in_array('escrow', $modulosDeshabilitados) ? 'checked' : '' ?>
                       class="h-5 w-5 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                <label for="deshabilitar_escrow" class="ml-3 flex-1">
                    <span class="block font-medium text-gray-800"><i class="fas fa-handshake mr-2 text-teal-600"></i>Sistema ESCROW</span>
                    <span class="block text-sm text-gray-500">Custodia de fondos y gestión de transacciones seguras</span>
                </label>
            </div>
        </div>
        
        <div class="mt-8 flex justify-end space-x-4">
            <a href="<?= url('configuraciones') ?>" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                <i class="fas fa-save mr-2"></i>Guardar Cambios
            </button>
        </div>
    </form>
</div>
