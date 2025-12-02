<?php
/**
 * Vista de Listado de Membresías
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
        <p class="text-gray-600">Gestión de membresías de socios</p>
    </div>
    <div class="flex space-x-3">
        <a href="<?= url('membresias/tipos') ?>" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
            <i class="fas fa-tags mr-2"></i>Tipos
        </a>
        <a href="<?= url('membresias/crear') ?>" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>Nueva Membresía
        </a>
    </div>
</div>

<!-- Estadísticas -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-check-circle text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Activas</p>
                <p class="text-2xl font-bold text-gray-800"><?= number_format($stats['activas']) ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                <i class="fas fa-clock text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Por Vencer</p>
                <p class="text-2xl font-bold text-gray-800"><?= number_format($stats['por_vencer']) ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-red-100 text-red-600">
                <i class="fas fa-times-circle text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Vencidas</p>
                <p class="text-2xl font-bold text-gray-800"><?= number_format($stats['vencidas']) ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-dollar-sign text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Ingresos Mes</p>
                <p class="text-2xl font-bold text-gray-800">$<?= number_format($stats['ingresos_mes'], 2) ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form method="GET" action="<?= url('membresias') ?>" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
            <input type="text" name="buscar" value="<?= htmlspecialchars($buscar) ?>" 
                   placeholder="Nombre o número de socio"
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
        </div>
        <div class="w-40">
            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
            <select name="estado" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                <option value="">Todos</option>
                <option value="activa" <?= $estado === 'activa' ? 'selected' : '' ?>>Activa</option>
                <option value="vencida" <?= $estado === 'vencida' ? 'selected' : '' ?>>Vencida</option>
                <option value="pendiente" <?= $estado === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
            <i class="fas fa-search mr-2"></i>Filtrar
        </button>
    </form>
</div>

<!-- Tabla de membresías -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Socio</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vigencia</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Días Restantes</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if (empty($membresias)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay membresías registradas</td>
                </tr>
            <?php else: ?>
                <?php foreach ($membresias as $m): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($m['nombre_socio']) ?></div>
                            <div class="text-sm text-gray-500"><?= htmlspecialchars($m['numero_socio']) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?= htmlspecialchars($m['tipo_membresia']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= date('d/m/Y', strtotime($m['fecha_inicio'])) ?> - <?= date('d/m/Y', strtotime($m['fecha_fin'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php 
                            $dias = $m['dias_restantes'];
                            if ($dias > 7) {
                                $diasClass = 'bg-green-100 text-green-800';
                            } elseif ($dias > 0) {
                                $diasClass = 'bg-yellow-100 text-yellow-800';
                            } else {
                                $diasClass = 'bg-red-100 text-red-800';
                            }
                            ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $diasClass ?>">
                                <?= $dias > 0 ? $dias . ' días' : 'Vencida' ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                            switch ($m['estatus']) {
                                case 'activa':
                                    $estatusClass = 'bg-green-100 text-green-800';
                                    break;
                                case 'vencida':
                                    $estatusClass = 'bg-red-100 text-red-800';
                                    break;
                                case 'pendiente':
                                    $estatusClass = 'bg-yellow-100 text-yellow-800';
                                    break;
                                default:
                                    $estatusClass = 'bg-gray-100 text-gray-800';
                            }
                            ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $estatusClass ?>">
                                <?= ucfirst($m['estatus']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="<?= url('membresias/ver/' . $m['id']) ?>" class="text-blue-600 hover:text-blue-900 mr-3">Ver</a>
                            <?php if ($m['estatus'] === 'activa' || $m['estatus'] === 'vencida'): ?>
                                <a href="<?= url('membresias/renovar/' . $m['id']) ?>" class="text-green-600 hover:text-green-900">Renovar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <?php if ($totalPages > 1): ?>
        <div class="px-6 py-4 bg-gray-50 border-t flex justify-between items-center">
            <span class="text-sm text-gray-700">
                Mostrando página <?= $page ?> de <?= $totalPages ?> (<?= $total ?> registros)
            </span>
            <div class="flex space-x-2">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>&estado=<?= $estado ?>&buscar=<?= urlencode($buscar) ?>" 
                       class="px-3 py-1 border rounded text-sm hover:bg-gray-100">Anterior</a>
                <?php endif; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>&estado=<?= $estado ?>&buscar=<?= urlencode($buscar) ?>" 
                       class="px-3 py-1 border rounded text-sm hover:bg-gray-100">Siguiente</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
