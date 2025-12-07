<?php
/**
 * Vista de Gestión de Expedientes
 * Administración y consulta de documentos digitalizados organizados por cliente y tipo de operación
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestión de Expedientes</h1>
        <div class="flex space-x-2">
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                <i class="fas fa-upload mr-2"></i>Subir Documento
            </button>
            <button class="bg-primary-800 hover:bg-primary-700 text-white px-4 py-2 rounded">
                <i class="fas fa-plus mr-2"></i>Nuevo Expediente
            </button>
        </div>
    </div>

    <p class="text-gray-600 mb-6">Administración y consulta de documentos digitalizados organizados por cliente y tipo de operación</p>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                    <i class="fas fa-folder text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Expedientes</p>
                    <p class="text-2xl font-semibold text-gray-900">1,247</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Completos</p>
                    <p class="text-2xl font-semibold text-green-600">892</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Incompletos</p>
                    <p class="text-2xl font-semibold text-yellow-600">355</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                    <i class="fas fa-file-alt text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Documentos</p>
                    <p class="text-2xl font-semibold text-gray-900">8,945</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Búsqueda de Expedientes</h2>
        <form class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Número de Cliente</label>
                <input type="text" placeholder="CLI-2024-001" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Cliente</label>
                <input type="text" placeholder="Nombre completo" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Expediente</label>
                <select class="w-full border rounded px-3 py-2">
                    <option>Todos los Tipos</option>
                    <option>Solicitud de Crédito</option>
                    <option>Apertura de Cuenta</option>
                    <option>Documentación General</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <select class="w-full border rounded px-3 py-2">
                    <option>Todos</option>
                    <option>Completo</option>
                    <option>Incompleto</option>
                    <option>En Revisión</option>
                </select>
            </div>
        </form>
        <div class="mt-4 flex justify-end space-x-2">
            <button type="button" class="bg-primary-800 hover:bg-primary-700 text-white px-6 py-2 rounded">
                <i class="fas fa-search mr-2"></i>Buscar
            </button>
            <button type="button" class="text-gray-600 hover:text-gray-800 px-4 py-2">
                <i class="fas fa-times mr-2"></i>Limpiar
            </button>
        </div>
    </div>

    <!-- Lista de Expedientes -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold">Expedientes Recientes</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            No. Expediente
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cliente
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tipo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Documentos
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Última Actualización
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
                    $expedientes = [
                        ['numero' => 'EXP-2024-001', 'cliente' => 'Juan Carlos García López', 'tipo' => 'Solicitud de Crédito', 'docs' => 8, 'fecha' => '2024-01-15', 'estado' => 'Completo'],
                        ['numero' => 'EXP-2024-002', 'cliente' => 'María Elena Rodríguez', 'tipo' => 'Apertura de Cuenta', 'docs' => 5, 'fecha' => '2024-01-16', 'estado' => 'Incompleto'],
                        ['numero' => 'EXP-2024-003', 'cliente' => 'Pedro Antonio Martínez', 'tipo' => 'Solicitud de Crédito', 'docs' => 12, 'fecha' => '2024-01-17', 'estado' => 'En Revisión'],
                    ];

                    foreach ($expedientes as $exp):
                        $estadoClass = 'bg-yellow-100 text-yellow-800';
                        if ($exp['estado'] == 'Completo') $estadoClass = 'bg-green-100 text-green-800';
                        if ($exp['estado'] == 'Incompleto') $estadoClass = 'bg-red-100 text-red-800';
                        if ($exp['estado'] == 'En Revisión') $estadoClass = 'bg-blue-100 text-blue-800';
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900"><?= $exp['numero'] ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-user text-gray-500"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900"><?= $exp['cliente'] ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-600"><?= $exp['tipo'] ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <i class="fas fa-file-alt mr-1 text-blue-600"></i><?= $exp['docs'] ?> archivos
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-600"><?= date('d/m/Y', strtotime($exp['fecha'])) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $estadoClass ?>">
                                <?= $exp['estado'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <button class="text-blue-600 hover:text-blue-900 mr-3" title="Ver Expediente">
                                <i class="fas fa-folder-open"></i>
                            </button>
                            <button class="text-green-600 hover:text-green-900 mr-3" title="Agregar Documento">
                                <i class="fas fa-plus-circle"></i>
                            </button>
                            <button class="text-purple-600 hover:text-purple-900 mr-3" title="Descargar">
                                <i class="fas fa-download"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="bg-gray-50 px-6 py-3 flex items-center justify-between border-t border-gray-200">
            <div class="text-sm text-gray-700">
                Mostrando <span class="font-medium">1</span> a <span class="font-medium">3</span> de <span class="font-medium">1,247</span> resultados
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

    <!-- Panel de Detalle del Expediente (si hay uno seleccionado) -->
    <?php if (isset($solicitud)): ?>
    <div class="mt-6 bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold">Documentos del Expediente</h2>
        </div>
        <div class="p-6">
            <?php if (empty($documentos)): ?>
            <div class="text-center py-8">
                <i class="fas fa-folder-open text-gray-400 text-5xl mb-4"></i>
                <p class="text-gray-500">No hay documentos cargados</p>
                <button class="mt-4 bg-primary-800 hover:bg-primary-700 text-white px-6 py-2 rounded">
                    <i class="fas fa-upload mr-2"></i>Subir Primer Documento
                </button>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <?php foreach ($documentos as $doc): ?>
                <div class="border rounded-lg p-4 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-2">
                        <i class="fas fa-file-pdf text-red-600 text-3xl"></i>
                        <span class="text-xs text-gray-500"><?= number_format(filesize($doc['ruta'] ?? '') / 1024, 2) ?> KB</span>
                    </div>
                    <h3 class="font-medium text-sm mb-1"><?= htmlspecialchars($doc['nombre'] ?? 'Documento') ?></h3>
                    <p class="text-xs text-gray-500 mb-3">Subido: <?= date('d/m/Y', strtotime($doc['created_at'])) ?></p>
                    <div class="flex space-x-2">
                        <button class="flex-1 text-xs bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded">
                            <i class="fas fa-eye mr-1"></i>Ver
                        </button>
                        <button class="flex-1 text-xs bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded">
                            <i class="fas fa-download mr-1"></i>Descargar
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
