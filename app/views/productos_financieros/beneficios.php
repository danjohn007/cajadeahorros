<?php
/**
 * Vista de Beneficios y Promociones
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Beneficios y Promociones</h1>
        <a href="<?= BASE_URL ?>/productos_financieros" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Regresar
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Promociones Activas</h2>
        
        <div class="space-y-4">
            <div class="border-l-4 border-blue-500 bg-blue-50 p-4 rounded">
                <h3 class="font-semibold text-blue-900 mb-2">
                    <i class="fas fa-gift text-blue-600 mr-2"></i>Tasa Preferencial
                </h3>
                <p class="text-sm text-blue-700 mb-2">Tasa de interés reducida para socios con buen historial crediticio</p>
                <div class="text-xs text-blue-600">
                    <span class="font-semibold">Requisitos:</span> 
                    <ul class="mt-1 ml-4">
                        <li>• Antigüedad mínima de 2 años</li>
                        <li>• Sin atrasos en últimos 12 meses</li>
                        <li>• Historial de ahorro constante</li>
                    </ul>
                </div>
            </div>

            <div class="border-l-4 border-green-500 bg-green-50 p-4 rounded">
                <h3 class="font-semibold text-green-900 mb-2">
                    <i class="fas fa-percent text-green-600 mr-2"></i>Sin Comisión por Apertura
                </h3>
                <p class="text-sm text-green-700 mb-2">Promoción especial sin comisión de apertura</p>
                <div class="text-xs text-green-600">
                    <span class="font-semibold">Aplicable a:</span> 
                    <ul class="mt-1 ml-4">
                        <li>• Créditos de nómina</li>
                        <li>• Montos menores a $50,000</li>
                        <li>• Válido hasta fin de mes</li>
                    </ul>
                </div>
            </div>

            <div class="border-l-4 border-orange-500 bg-orange-50 p-4 rounded">
                <h3 class="font-semibold text-orange-900 mb-2">
                    <i class="fas fa-calendar-check text-orange-600 mr-2"></i>Plazo Extendido
                </h3>
                <p class="text-sm text-orange-700 mb-2">Hasta 60 meses de plazo sin interés adicional</p>
                <div class="text-xs text-orange-600">
                    <span class="font-semibold">Condiciones:</span> 
                    <ul class="mt-1 ml-4">
                        <li>• Créditos hipotecarios y automotrices</li>
                        <li>• Monto mínimo de $100,000</li>
                        <li>• Requisitos adicionales aplican</li>
                    </ul>
                </div>
            </div>

            <div class="border-l-4 border-purple-500 bg-purple-50 p-4 rounded">
                <h3 class="font-semibold text-purple-900 mb-2">
                    <i class="fas fa-star text-purple-600 mr-2"></i>Beneficios VIP
                </h3>
                <p class="text-sm text-purple-700 mb-2">Paquete especial para socios destacados</p>
                <div class="text-xs text-purple-600">
                    <span class="font-semibold">Incluye:</span> 
                    <ul class="mt-1 ml-4">
                        <li>• Tasa preferencial garantizada</li>
                        <li>• Sin comisiones</li>
                        <li>• Seguro de vida incluido</li>
                        <li>• Atención prioritaria</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <h3 class="font-semibold text-gray-800 mb-2">Administración de Beneficios</h3>
            <p class="text-sm text-gray-600 mb-3">
                Para crear o modificar beneficios y promociones, contacte al administrador del sistema.
            </p>
            <button class="bg-primary-800 hover:bg-primary-700 text-white px-4 py-2 rounded text-sm">
                <i class="fas fa-plus mr-2"></i>Nueva Promoción
            </button>
        </div>
    </div>
</div>
