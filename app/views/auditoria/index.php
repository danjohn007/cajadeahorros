<?php
/**
 * Vista de Dashboard de Auditoría
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
    <p class="text-gray-600">Monitoreo y registro de actividades del sistema</p>
</div>

<!-- Estadísticas -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-clipboard-list text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Acciones Hoy</p>
                <p class="text-2xl font-bold text-gray-800"><?= number_format($stats['acciones_hoy']) ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-users text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Usuarios Activos</p>
                <p class="text-2xl font-bold text-gray-800"><?= number_format($stats['usuarios_activos']) ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-red-100 text-red-600">
                <i class="fas fa-exclamation-triangle text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Errores Sistema</p>
                <p class="text-2xl font-bold text-gray-800"><?= number_format($stats['errores_sistema']) ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <i class="fas fa-desktop text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Sesiones Activas</p>
                <p class="text-2xl font-bold text-gray-800"><?= number_format($stats['sesiones_activas']) ?></p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Enlaces rápidos -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Módulos de Auditoría</h2>
        <div class="grid grid-cols-2 gap-4">
            <a href="<?= url('bitacora') ?>" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <i class="fas fa-history text-3xl text-blue-600 mb-2"></i>
                <p class="font-medium">Bitácora</p>
                <p class="text-sm text-gray-500">Acciones de usuarios</p>
            </a>
            <a href="<?= url('auditoria/logs') ?>" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <i class="fas fa-file-alt text-3xl text-green-600 mb-2"></i>
                <p class="font-medium">Logs Sistema</p>
                <p class="text-sm text-gray-500">Registros técnicos</p>
            </a>
            <a href="<?= url('auditoria/sesiones') ?>" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <i class="fas fa-user-clock text-3xl text-purple-600 mb-2"></i>
                <p class="font-medium">Sesiones</p>
                <p class="text-sm text-gray-500">Inicios de sesión</p>
            </a>
            <a href="<?= url('auditoria/cambios') ?>" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <i class="fas fa-exchange-alt text-3xl text-orange-600 mb-2"></i>
                <p class="font-medium">Cambios</p>
                <p class="text-sm text-gray-500">Modificaciones a datos</p>
            </a>
        </div>
    </div>
    
    <!-- Acciones por tipo -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Acciones Frecuentes (7 días)</h2>
        <?php if (empty($accionesPorTipo)): ?>
            <p class="text-gray-500 text-center py-4">No hay datos</p>
        <?php else: ?>
            <div class="space-y-2">
                <?php 
                $maxTotal = max(array_column($accionesPorTipo, 'total'));
                foreach ($accionesPorTipo as $accion): 
                    $width = ($accion['total'] / $maxTotal) * 100;
                ?>
                    <div class="flex items-center">
                        <div class="w-32 text-sm text-gray-600 truncate"><?= htmlspecialchars($accion['accion']) ?></div>
                        <div class="flex-1 mx-4">
                            <div class="bg-gray-200 rounded-full h-4">
                                <div class="bg-blue-600 rounded-full h-4" style="width: <?= $width ?>%"></div>
                            </div>
                        </div>
                        <div class="w-12 text-right text-sm font-medium"><?= $accion['total'] ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Filtros de búsqueda -->
<div class="bg-white rounded-lg shadow-md p-6 mt-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-filter mr-2"></i>Filtros de Búsqueda
    </h2>
    <form method="GET" action="<?= url('auditoria') ?>" class="grid grid-cols-1 md:grid-cols-6 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
            <input type="text" name="buscar" value="<?= htmlspecialchars($buscar ?? '') ?>" 
                   placeholder="Descripción o acción..."
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
            <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($fechaInicio ?? '') ?>" 
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
            <input type="date" name="fecha_fin" value="<?= htmlspecialchars($fechaFin ?? '') ?>" 
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Usuario</label>
            <select name="usuario" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                <option value="">Todos</option>
                <?php foreach ($usuarios ?? [] as $u): ?>
                    <option value="<?= $u['id'] ?>" <?= ($usuarioFilter ?? '') == $u['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($u['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Acción</label>
            <select name="accion" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                <option value="">Todas</option>
                <?php foreach ($acciones ?? [] as $a): ?>
                    <option value="<?= htmlspecialchars($a['accion']) ?>" <?= ($accionFilter ?? '') == $a['accion'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($a['accion']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex items-end space-x-2">
            <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                <i class="fas fa-search mr-1"></i>Filtrar
            </button>
            <a href="<?= url('auditoria') ?>" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                <i class="fas fa-times"></i>
            </a>
        </div>
    </form>
</div>

<!-- Últimas acciones con paginación -->
<div class="bg-white rounded-lg shadow-md mt-6 overflow-hidden">
    <div class="px-6 py-4 border-b flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">Registro de Acciones</h2>
        <span class="text-sm text-gray-600">Total: <?= number_format($total ?? 0) ?> registros</span>
    </div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acción</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if (empty($ultimasAcciones)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        No hay registros que coincidan con los filtros
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($ultimasAcciones as $accion): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= date('d/m/Y H:i', strtotime($accion['fecha'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?= htmlspecialchars($accion['usuario_nombre'] ?? 'Sistema') ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                <?= htmlspecialchars($accion['accion']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                            <?= htmlspecialchars($accion['descripcion']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= htmlspecialchars($accion['ip']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <!-- Paginación -->
    <?php if (($totalPages ?? 0) > 1): ?>
        <div class="px-6 py-4 bg-gray-50 border-t flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Página <?= $page ?> de <?= $totalPages ?> (<?= number_format($total) ?> registros)
            </div>
            <div class="flex space-x-2">
                <?php if ($page > 1): ?>
                    <a href="<?= url('auditoria') ?>?page=<?= $page - 1 ?>&fecha_inicio=<?= urlencode($fechaInicio) ?>&fecha_fin=<?= urlencode($fechaFin) ?>&usuario=<?= urlencode($usuarioFilter ?? '') ?>&accion=<?= urlencode($accionFilter ?? '') ?>&buscar=<?= urlencode($buscar ?? '') ?>" 
                       class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-100 text-sm">
                        <i class="fas fa-chevron-left"></i> Anterior
                    </a>
                <?php endif; ?>
                
                <?php 
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);
                for ($i = $startPage; $i <= $endPage; $i++): 
                ?>
                    <a href="<?= url('auditoria') ?>?page=<?= $i ?>&fecha_inicio=<?= urlencode($fechaInicio) ?>&fecha_fin=<?= urlencode($fechaFin) ?>&usuario=<?= urlencode($usuarioFilter ?? '') ?>&accion=<?= urlencode($accionFilter ?? '') ?>&buscar=<?= urlencode($buscar ?? '') ?>" 
                       class="px-3 py-1 rounded border <?= $i === $page ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 hover:bg-gray-100' ?> text-sm">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="<?= url('auditoria') ?>?page=<?= $page + 1 ?>&fecha_inicio=<?= urlencode($fechaInicio) ?>&fecha_fin=<?= urlencode($fechaFin) ?>&usuario=<?= urlencode($usuarioFilter ?? '') ?>&accion=<?= urlencode($accionFilter ?? '') ?>&buscar=<?= urlencode($buscar ?? '') ?>" 
                       class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-100 text-sm">
                        Siguiente <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
