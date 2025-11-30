<?php
/**
 * Vista de Bitácora de Acciones
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
    <p class="text-gray-600">Registro de todas las acciones realizadas en el sistema</p>
</div>

<!-- Filtros -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form method="GET" action="<?= url('bitacora') ?>" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
            <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($fechaInicio) ?>" 
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
            <input type="date" name="fecha_fin" value="<?= htmlspecialchars($fechaFin) ?>" 
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Usuario</label>
            <select name="usuario" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Todos</option>
                <?php foreach ($usuarios as $u): ?>
                    <option value="<?= $u['id'] ?>" <?= $usuarioFilter == $u['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($u['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Acción</label>
            <select name="accion" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Todas</option>
                <?php foreach ($acciones as $a): ?>
                    <option value="<?= htmlspecialchars($a['accion']) ?>" <?= $accionFilter == $a['accion'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($a['accion']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                <i class="fas fa-search mr-2"></i>Filtrar
            </button>
        </div>
    </form>
</div>

<!-- Tabla de registros -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <p class="text-sm text-gray-600">Total de registros: <span class="font-semibold"><?= number_format($total) ?></span></p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha/Hora</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entidad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($registros)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay registros que mostrar</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($registros as $reg): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= date('d/m/Y H:i:s', strtotime($reg['fecha'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= htmlspecialchars($reg['usuario_nombre'] ?? 'Sistema') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    <?php
                                    $color = 'bg-gray-100 text-gray-800';
                                    if (strpos($reg['accion'], 'CREAR') !== false) $color = 'bg-green-100 text-green-800';
                                    elseif (strpos($reg['accion'], 'EDITAR') !== false || strpos($reg['accion'], 'ACTUALIZAR') !== false) $color = 'bg-yellow-100 text-yellow-800';
                                    elseif (strpos($reg['accion'], 'ELIMINAR') !== false || strpos($reg['accion'], 'BAJA') !== false) $color = 'bg-red-100 text-red-800';
                                    elseif (strpos($reg['accion'], 'LOGIN') !== false) $color = 'bg-blue-100 text-blue-800';
                                    echo $color;
                                    ?>">
                                    <?= htmlspecialchars($reg['accion']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 max-w-md truncate">
                                <?= htmlspecialchars($reg['descripcion']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= htmlspecialchars($reg['entidad'] ?? '-') ?>
                                <?php if ($reg['entidad_id']): ?>
                                    <span class="text-gray-400">#<?= $reg['entidad_id'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= htmlspecialchars($reg['ip'] ?? '-') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Paginación -->
    <?php if ($totalPages > 1): ?>
        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Página <?= $page ?> de <?= $totalPages ?>
            </div>
            <div class="flex space-x-2">
                <?php if ($page > 1): ?>
                    <a href="<?= url('bitacora') ?>?page=<?= $page - 1 ?>&fecha_inicio=<?= $fechaInicio ?>&fecha_fin=<?= $fechaFin ?>&usuario=<?= $usuarioFilter ?>&accion=<?= urlencode($accionFilter) ?>" 
                       class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-50">Anterior</a>
                <?php endif; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="<?= url('bitacora') ?>?page=<?= $page + 1 ?>&fecha_inicio=<?= $fechaInicio ?>&fecha_fin=<?= $fechaFin ?>&usuario=<?= $usuarioFilter ?>&accion=<?= urlencode($accionFilter) ?>" 
                       class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-50">Siguiente</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
