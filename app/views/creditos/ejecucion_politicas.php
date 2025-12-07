<?php
/**
 * Vista de Ejecución de Políticas de Crédito
 * Aplicación y seguimiento de reglas y políticas institucionales en el proceso de aprobación
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Ejecución de Políticas</h1>
        <button class="bg-primary-800 hover:bg-primary-700 text-white px-4 py-2 rounded">
            <i class="fas fa-sync-alt mr-2"></i>Ejecutar Validación
        </button>
    </div>

    <p class="text-gray-600 mb-6">Aplicación y seguimiento de reglas y políticas institucionales en el proceso de aprobación</p>

    <!-- Estadísticas Generales -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                    <i class="fas fa-gavel text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Políticas</p>
                    <p class="text-2xl font-semibold text-gray-900">42</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Activas</p>
                    <p class="text-2xl font-semibold text-green-600">38</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">En Revisión</p>
                    <p class="text-2xl font-semibold text-yellow-600">12</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Rechazadas</p>
                    <p class="text-2xl font-semibold text-red-600">8</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Políticas por Categoría -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Políticas de Edad y Plazo -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-calendar-alt text-primary-800 mr-2"></i>Políticas de Edad y Plazo
                </h2>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <div>
                                <p class="text-sm font-medium">Edad mínima: 18 años</p>
                                <p class="text-xs text-gray-500">Solicitante cumple requisito</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded font-semibold">Cumple</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-green-50 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <div>
                                <p class="text-sm font-medium">Plazo máximo según edad</p>
                                <p class="text-xs text-gray-500">12 meses para mayores de 69 años</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded font-semibold">Cumple</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-green-50 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <div>
                                <p class="text-sm font-medium">Edad máxima al final del crédito</p>
                                <p class="text-xs text-gray-500">No debe exceder 75 años</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded font-semibold">Cumple</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Políticas de Montos y Garantías -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-dollar-sign text-primary-800 mr-2"></i>Políticas de Montos y Garantías
                </h2>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <div>
                                <p class="text-sm font-medium">Monto mínimo: $5,000</p>
                                <p class="text-xs text-gray-500">Monto solicitado válido</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded font-semibold">Cumple</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mr-3"></i>
                            <div>
                                <p class="text-sm font-medium">Requiere aval para >$50,000</p>
                                <p class="text-xs text-gray-500">Debe registrar al menos un aval</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded font-semibold">Pendiente</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-green-50 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <div>
                                <p class="text-sm font-medium">Monto vs Capacidad de pago</p>
                                <p class="text-xs text-gray-500">Ratio 30% de ingresos</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded font-semibold">Cumple</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Políticas de Score Crediticio -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-chart-line text-primary-800 mr-2"></i>Políticas de Score Crediticio
                </h2>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <div>
                                <p class="text-sm font-medium">Score mínimo: 650</p>
                                <p class="text-xs text-gray-500">Score actual: 750</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded font-semibold">Cumple</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-green-50 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <div>
                                <p class="text-sm font-medium">Sin morosidad actual</p>
                                <p class="text-xs text-gray-500">Historial limpio</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded font-semibold">Cumple</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-green-50 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <div>
                                <p class="text-sm font-medium">Máximo 3 consultas/6 meses</p>
                                <p class="text-xs text-gray-500">Consultas: 1</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded font-semibold">Cumple</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Políticas de Documentación -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-file-alt text-primary-800 mr-2"></i>Políticas de Documentación
                </h2>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <div>
                                <p class="text-sm font-medium">Identificación oficial</p>
                                <p class="text-xs text-gray-500">INE vigente</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded font-semibold">Cumple</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-green-50 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <div>
                                <p class="text-sm font-medium">Comprobante de ingresos</p>
                                <p class="text-xs text-gray-500">Últimos 3 meses</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded font-semibold">Cumple</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-green-50 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <div>
                                <p class="text-sm font-medium">Comprobante de domicilio</p>
                                <p class="text-xs text-gray-500">No mayor a 3 meses</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded font-semibold">Cumple</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen de Validación -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold flex items-center">
                <i class="fas fa-clipboard-check text-primary-800 mr-2"></i>Resumen de Validación de Políticas
            </h2>
        </div>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <p class="text-sm text-gray-600 mb-2">Progreso de Cumplimiento</p>
                    <div class="w-96 bg-gray-200 rounded-full h-4">
                        <div class="bg-green-600 h-4 rounded-full" style="width: 92%"></div>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-4xl font-bold text-green-600">92%</p>
                    <p class="text-sm text-gray-500">de cumplimiento</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-3xl mb-2"></i>
                    <p class="text-2xl font-bold text-green-600">11</p>
                    <p class="text-sm text-gray-600">Políticas Cumplidas</p>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-3xl mb-2"></i>
                    <p class="text-2xl font-bold text-yellow-600">1</p>
                    <p class="text-sm text-gray-600">Políticas Pendientes</p>
                </div>
                <div class="text-center p-4 bg-red-50 rounded-lg">
                    <i class="fas fa-times-circle text-red-600 text-3xl mb-2"></i>
                    <p class="text-2xl font-bold text-red-600">0</p>
                    <p class="text-sm text-gray-600">Políticas Incumplidas</p>
                </div>
            </div>

            <div class="bg-blue-50 rounded-lg p-4 mb-4">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-blue-800 mb-2">Recomendación del Sistema</h3>
                        <p class="text-sm text-blue-700">
                            El solicitante cumple con la mayoría de las políticas institucionales. 
                            Se recomienda registrar un aval para completar los requisitos y proceder con la aprobación.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-2">
                <button class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded">
                    <i class="fas fa-file-pdf mr-2"></i>Generar Reporte
                </button>
                <button class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-2 rounded">
                    <i class="fas fa-clock mr-2"></i>Solicitar Excepción
                </button>
                <button class="bg-primary-800 hover:bg-primary-700 text-white px-6 py-2 rounded">
                    <i class="fas fa-arrow-right mr-2"></i>Continuar Proceso
                </button>
            </div>
        </div>
    </div>
</div>
