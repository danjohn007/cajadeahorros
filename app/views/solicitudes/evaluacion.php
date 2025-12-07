<?php
/**
 * Vista de Evaluación Preliminar de Requisitos
 * Validación automática de requisitos básicos y excluyentes para optimizar el proceso de análisis crediticio
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Evaluación Preliminar de Requisitos</h1>
        <button class="bg-primary-800 hover:bg-primary-700 text-white px-4 py-2 rounded">
            <i class="fas fa-plus mr-2"></i>Nueva Evaluación
        </button>
    </div>

    <p class="text-gray-600 mb-6">Validación automática de requisitos básicos y excluyentes para optimizar el proceso de análisis crediticio</p>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                    <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Evaluaciones Totales</p>
                    <p class="text-2xl font-semibold text-gray-900">247</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Aprobadas</p>
                    <p class="text-2xl font-semibold text-green-600">189</p>
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
                    <p class="text-2xl font-semibold text-red-600">34</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Pendientes</p>
                    <p class="text-2xl font-semibold text-yellow-600">24</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Filtros de Búsqueda</h2>
        <form class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Número de Solicitud</label>
                <input type="text" placeholder="SOL-2024-001" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Cliente</label>
                <input type="text" placeholder="Nombre completo" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <select class="w-full border rounded px-3 py-2">
                    <option value="">Todos los Estados</option>
                    <option value="aprobado">Aprobado</option>
                    <option value="rechazado">Rechazado</option>
                    <option value="pendiente">Pendiente</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Desde</label>
                <input type="date" value="2024-01-01" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Hasta</label>
                <input type="date" value="2024-12-31" class="w-full border rounded px-3 py-2">
            </div>
        </form>
        <div class="mt-4 flex justify-between">
            <div class="flex gap-2">
                <select class="border rounded px-3 py-2">
                    <option>Todos los Productos</option>
                    <option>Crédito Personal</option>
                    <option>Crédito Automotriz</option>
                </select>
                <select class="border rounded px-3 py-2">
                    <option>Todos los Agentes</option>
                    <option>Juan Pérez</option>
                    <option>María García</option>
                </select>
                <select class="border rounded px-3 py-2">
                    <option>Todas las Sucursales</option>
                    <option>Sucursal Centro</option>
                    <option>Sucursal Norte</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="button" class="bg-primary-800 hover:bg-primary-700 text-white px-6 py-2 rounded">
                    <i class="fas fa-search mr-2"></i>Filtrar
                </button>
                <button type="button" class="text-gray-600 hover:text-gray-800 px-4 py-2">
                    <i class="fas fa-times mr-2"></i>Limpiar
                </button>
            </div>
        </div>
    </div>

    <!-- Tabla de Evaluaciones -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold">Solicitudes en Evaluación</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Solicitud
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cliente
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tipo de Producto
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Monto Solicitado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fecha de Evaluación
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Agente de Ventas
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php 
                    $evaluaciones = [
                        ['id' => 1, 'numero' => 'SOL-2024-001', 'cliente' => 'Juan Carlos Pérez Martínez', 'producto' => 'Crédito Personal', 'monto' => 50000, 'fecha' => '2024-01-15', 'agente' => 'María López', 'estado' => 'APROBADO'],
                        ['id' => 2, 'numero' => 'SOL-2024-002', 'cliente' => 'Ana María Rodríguez', 'producto' => 'Crédito Automotriz', 'monto' => 120000, 'fecha' => '2024-01-16', 'agente' => 'Carlos Gómez', 'estado' => 'PENDIENTE'],
                        ['id' => 3, 'numero' => 'SOL-2024-003', 'cliente' => 'Luis Fernando García', 'producto' => 'Crédito Hipotecario', 'monto' => 850000, 'fecha' => '2024-01-17', 'agente' => 'Patricia Díaz', 'estado' => 'RECHAZADO'],
                    ];
                    
                    foreach ($evaluaciones as $eval): 
                        $estadoClass = 'bg-yellow-100 text-yellow-800';
                        if ($eval['estado'] == 'APROBADO') $estadoClass = 'bg-green-100 text-green-800';
                        if ($eval['estado'] == 'RECHAZADO') $estadoClass = 'bg-red-100 text-red-800';
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900"><?= $eval['numero'] ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?= $eval['cliente'] ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-600"><?= $eval['producto'] ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">$<?= number_format($eval['monto'], 2) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-600"><?= date('d/m/Y', strtotime($eval['fecha'])) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-600"><?= $eval['agente'] ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $estadoClass ?>">
                                <?= $eval['estado'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <button class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-green-600 hover:text-green-900 mr-3">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="bg-gray-50 px-6 py-3 flex items-center justify-between border-t border-gray-200">
            <div class="text-sm text-gray-700">
                Mostrando <span class="font-medium">1</span> a <span class="font-medium">3</span> de <span class="font-medium">247</span> resultados
            </div>
            <div class="flex space-x-2">
                <button class="px-3 py-1 border rounded hover:bg-gray-100">Anterior</button>
                <button class="px-3 py-1 border rounded bg-primary-800 text-white">1</button>
                <button class="px-3 py-1 border rounded hover:bg-gray-100">2</button>
                <button class="px-3 py-1 border rounded hover:bg-gray-100">3</button>
                <button class="px-3 py-1 border rounded hover:bg-gray-100">Siguiente</button>
            </div>
        </div>
    </div>

    <!-- Modal de Detalle (ejemplo) -->
    <div id="detalleModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Detalle de Evaluación - SOL-2024-001</h3>
                <button onclick="document.getElementById('detalleModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="mb-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Cliente</p>
                        <p class="font-semibold">Juan Carlos Pérez Martínez</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Producto</p>
                        <p class="font-semibold">Crédito Personal</p>
                    </div>
                </div>
            </div>

            <div class="border-t pt-4">
                <h4 class="font-semibold mb-3">Criterios de Evaluación</h4>
                <div class="space-y-2">
                    <div class="flex items-center justify-between p-2 bg-green-50 rounded">
                        <span class="text-sm">Edad mínima cumplida</span>
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-green-50 rounded">
                        <span class="text-sm">Score crediticio suficiente</span>
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-green-50 rounded">
                        <span class="text-sm">Capacidad de pago validada</span>
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
