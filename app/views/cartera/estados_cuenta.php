<?php
/**
 * Vista de Estados de Cuenta
 * Consulta y generación de estados de cuenta de créditos
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Estados de Cuenta</h1>
        <div class="flex space-x-2">
            <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                <i class="fas fa-file-excel mr-2"></i>Exportar Excel
            </button>
            <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                <i class="fas fa-file-pdf mr-2"></i>Generar PDF
            </button>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <h2 class="text-lg font-semibold mb-4">Búsqueda</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="text" placeholder="Número de crédito" class="border rounded px-3 py-2">
                <input type="text" placeholder="Número de socio" class="border rounded px-3 py-2">
                <input type="date" class="border rounded px-3 py-2">
                <button class="bg-primary-800 hover:bg-primary-700 text-white px-6 py-2 rounded">
                    <i class="fas fa-search mr-2"></i>Buscar
                </button>
            </div>
        </div>
    </div>

    <!-- Resumen del Estado de Cuenta -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold">Estado de Cuenta - CRE-2024-001</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-1">Monto Original</p>
                    <p class="text-2xl font-bold text-blue-600">$50,000.00</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-1">Total Pagado</p>
                    <p class="text-2xl font-bold text-green-600">$25,000.00</p>
                </div>
                <div class="bg-yellow-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-1">Saldo Actual</p>
                    <p class="text-2xl font-bold text-yellow-600">$25,000.00</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-1">Próximo Pago</p>
                    <p class="text-2xl font-bold text-purple-600">$2,500.00</p>
                </div>
            </div>

            <!-- Tabla de Movimientos -->
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Concepto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cargo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Abono</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Saldo</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">01/01/2024</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">Disposición Crédito</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">$50,000.00</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">-</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">$50,000.00</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">01/02/2024</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">Pago Mensual</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">-</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">$2,500.00</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">$47,500.00</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
