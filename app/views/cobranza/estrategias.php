<?php
/**
 * Vista de Estrategias de Cobranza
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Estrategias de Cobranza</h1>
        <a href="<?= BASE_URL ?>/cobranza" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Regresar
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Configuración de Estrategias</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="border rounded-lg p-4">
                <h3 class="font-semibold text-gray-700 mb-2">
                    <i class="fas fa-phone text-blue-600 mr-2"></i>Estrategia de Llamadas
                </h3>
                <p class="text-sm text-gray-600 mb-3">Gestión telefónica para créditos en mora temprana (1-30 días)</p>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>• Contacto inicial al día 1 de mora</li>
                    <li>• Seguimiento cada 3 días</li>
                    <li>• Recordatorios de pago</li>
                </ul>
            </div>

            <div class="border rounded-lg p-4">
                <h3 class="font-semibold text-gray-700 mb-2">
                    <i class="fas fa-envelope text-green-600 mr-2"></i>Estrategia de Notificaciones
                </h3>
                <p class="text-sm text-gray-600 mb-3">Envío de notificaciones escritas (31-60 días)</p>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>• Email y SMS automáticos</li>
                    <li>• Notificación formal</li>
                    <li>• Propuesta de convenio</li>
                </ul>
            </div>

            <div class="border rounded-lg p-4">
                <h3 class="font-semibold text-gray-700 mb-2">
                    <i class="fas fa-home text-orange-600 mr-2"></i>Estrategia de Visitas
                </h3>
                <p class="text-sm text-gray-600 mb-3">Gestión domiciliaria para mora avanzada (61-90 días)</p>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>• Visita domiciliaria programada</li>
                    <li>• Negociación directa</li>
                    <li>• Convenio de pagos</li>
                </ul>
            </div>

            <div class="border rounded-lg p-4">
                <h3 class="font-semibold text-gray-700 mb-2">
                    <i class="fas fa-gavel text-red-600 mr-2"></i>Estrategia Legal
                </h3>
                <p class="text-sm text-gray-600 mb-3">Proceso jurídico para mora crítica (>90 días)</p>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>• Requerimiento legal</li>
                    <li>• Proceso judicial</li>
                    <li>• Recuperación de garantías</li>
                </ul>
            </div>
        </div>

        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
            <h3 class="font-semibold text-blue-800 mb-2">Notas Importantes</h3>
            <ul class="text-sm text-blue-700 space-y-1">
                <li>• Las estrategias se aplican automáticamente según los días de mora</li>
                <li>• Los agentes pueden escalar o cambiar de estrategia según el caso</li>
                <li>• Se registra toda la gestión realizada en la bitácora del crédito</li>
            </ul>
        </div>
    </div>
</div>
