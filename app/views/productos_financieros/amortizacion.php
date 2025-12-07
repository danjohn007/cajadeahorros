<?php
/**
 * Vista de Esquemas de Amortización
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Esquemas de Amortización</h1>
        <a href="<?= BASE_URL ?>/productos_financieros" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Regresar
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Tipos de Amortización Disponibles</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="border rounded-lg p-4">
                <h3 class="font-semibold text-gray-700 mb-2">
                    <i class="fas fa-equals text-blue-600 mr-2"></i>Amortización Francesa
                </h3>
                <p class="text-sm text-gray-600 mb-3">Cuotas fijas durante todo el plazo del crédito</p>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>• Cuota mensual constante</li>
                    <li>• Amortización de capital creciente</li>
                    <li>• Intereses decrecientes</li>
                </ul>
            </div>

            <div class="border rounded-lg p-4">
                <h3 class="font-semibold text-gray-700 mb-2">
                    <i class="fas fa-chart-line text-green-600 mr-2"></i>Amortización Alemana
                </h3>
                <p class="text-sm text-gray-600 mb-3">Amortización de capital constante</p>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>• Capital amortizado constante</li>
                    <li>• Cuota total decreciente</li>
                    <li>• Intereses sobre saldo</li>
                </ul>
            </div>

            <div class="border rounded-lg p-4">
                <h3 class="font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar-alt text-orange-600 mr-2"></i>Amortización al Vencimiento
                </h3>
                <p class="text-sm text-gray-600 mb-3">Pago de capital al final del plazo</p>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>• Solo intereses periódicos</li>
                    <li>• Capital pagado al vencimiento</li>
                    <li>• Para créditos de corto plazo</li>
                </ul>
            </div>

            <div class="border rounded-lg p-4">
                <h3 class="font-semibold text-gray-700 mb-2">
                    <i class="fas fa-adjust text-purple-600 mr-2"></i>Amortización Personalizada
                </h3>
                <p class="text-sm text-gray-600 mb-3">Esquema adaptado a necesidades específicas</p>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>• Cuotas variables</li>
                    <li>• Periodos de gracia</li>
                    <li>• Pagos extraordinarios</li>
                </ul>
            </div>
        </div>

        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
            <h3 class="font-semibold text-blue-800 mb-2">Configuración</h3>
            <p class="text-sm text-blue-700">
                Los esquemas de amortización se configuran por producto financiero. 
                Vaya a la sección de productos para asignar el tipo de amortización deseado.
            </p>
        </div>
    </div>
</div>
