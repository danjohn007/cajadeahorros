<?php
/**
 * Vista de Historial de Importaciones
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <a href="<?= url('importar') ?>" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
            <i class="fas fa-arrow-left mr-1"></i> Volver a Importar
        </a>
        <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
        <p class="text-gray-600">Historial de todas las importaciones realizadas</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <?php if (empty($importaciones)): ?>
        <div class="p-8 text-center text-gray-500">
            <i class="fas fa-inbox text-4xl mb-4"></i>
            <p>No hay importaciones registradas</p>
        </div>
    <?php else: ?>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Archivo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Exitosos</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Errores</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estatus</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($importaciones as $imp): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#<?= $imp['id'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900"><?= htmlspecialchars($imp['nombre_archivo']) ?></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= date('d/m/Y H:i', strtotime($imp['fecha_inicio'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= htmlspecialchars($imp['usuario_nombre'] ?? 'Sistema') ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                            <?= $imp['total_registros'] ?? 0 ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-green-600 font-medium">
                            <?= $imp['registros_exitosos'] ?? 0 ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-red-600 font-medium">
                            <?= $imp['registros_error'] ?? 0 ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <?php
                            $statusColors = [
                                'completado' => 'green',
                                'parcial' => 'yellow',
                                'error' => 'red',
                                'procesando' => 'blue'
                            ];
                            $color = $statusColors[$imp['estatus']] ?? 'gray';
                            ?>
                            <span class="px-2 py-1 text-xs rounded-full bg-<?= $color ?>-100 text-<?= $color ?>-800">
                                <?= ucfirst($imp['estatus']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <a href="<?= url('importar/detalle/' . $imp['id']) ?>" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye mr-1"></i> Ver detalles
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="bg-gray-50 px-6 py-4 border-t">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-600">
                        Mostrando página <?= $page ?> de <?= $totalPages ?> (<?= $total ?> registros)
                    </p>
                    <div class="flex space-x-2">
                        <?php if ($page > 1): ?>
                            <a href="<?= url('importar/historial?page=' . ($page - 1)) ?>" 
                               class="px-3 py-1 bg-white border rounded hover:bg-gray-100">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <a href="<?= url('importar/historial?page=' . $i) ?>" 
                               class="px-3 py-1 <?= $i === $page ? 'bg-blue-600 text-white' : 'bg-white border hover:bg-gray-100' ?> rounded">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="<?= url('importar/historial?page=' . ($page + 1)) ?>" 
                               class="px-3 py-1 bg-white border rounded hover:bg-gray-100">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
