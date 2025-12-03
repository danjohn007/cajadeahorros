<?php
/**
 * Vista de Logs del Sistema
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
        <p class="text-gray-600">Registros técnicos del sistema</p>
    </div>
    <a href="<?= url('auditoria') ?>" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Volver a Auditoría
    </a>
</div>

<!-- Filtros -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form method="GET" action="<?= url('auditoria/logs') ?>" class="flex flex-wrap gap-4 items-end">
        <div class="w-40">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nivel</label>
            <select name="nivel" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                <option value="">Todos</option>
                <option value="info" <?= ($nivel ?? '') === 'info' ? 'selected' : '' ?>>Info</option>
                <option value="warning" <?= ($nivel ?? '') === 'warning' ? 'selected' : '' ?>>Warning</option>
                <option value="error" <?= ($nivel ?? '') === 'error' ? 'selected' : '' ?>>Error</option>
                <option value="critical" <?= ($nivel ?? '') === 'critical' ? 'selected' : '' ?>>Critical</option>
            </select>
        </div>
        <div class="w-48">
            <label class="block text-sm font-medium text-gray-700 mb-1">Módulo</label>
            <select name="modulo" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                <option value="">Todos</option>
                <?php foreach ($modulos ?? [] as $m): ?>
                    <option value="<?= htmlspecialchars($m['modulo']) ?>" <?= ($moduloFilter ?? '') === $m['modulo'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($m['modulo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="w-40">
            <label class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
            <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($fechaInicio ?? '') ?>"
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
        </div>
        <div class="w-40">
            <label class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
            <input type="date" name="fecha_fin" value="<?= htmlspecialchars($fechaFin ?? '') ?>"
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            <i class="fas fa-filter mr-2"></i>Filtrar
        </button>
        <a href="<?= url('auditoria/logs') ?>" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
            <i class="fas fa-times"></i>
        </a>
    </form>
</div>

<!-- Tabla de Logs -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">Registros del Sistema</h2>
        <span class="text-sm text-gray-600">Total: <?= number_format($total ?? 0) ?> registros</span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nivel</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Módulo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mensaje</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            No hay registros que coincidan con los filtros
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= date('d/m/Y H:i:s', strtotime($log['fecha'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $nivelClases = [
                                    'info' => 'bg-blue-100 text-blue-800',
                                    'warning' => 'bg-yellow-100 text-yellow-800',
                                    'error' => 'bg-red-100 text-red-800',
                                    'critical' => 'bg-red-200 text-red-900'
                                ];
                                $clase = $nivelClases[$log['nivel'] ?? 'info'] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="px-2 py-1 text-xs rounded-full <?= $clase ?>">
                                    <?= htmlspecialchars($log['nivel'] ?? 'info') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= htmlspecialchars($log['modulo'] ?? '-') ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 max-w-md truncate">
                                <?= htmlspecialchars($log['mensaje'] ?? '') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= htmlspecialchars($log['usuario_nombre'] ?? 'Sistema') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Paginación -->
    <?php if (($totalPages ?? 0) > 1): ?>
        <div class="px-6 py-4 bg-gray-50 border-t flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Página <?= $page ?> de <?= $totalPages ?> (<?= number_format($total) ?> registros)
            </div>
            <div class="flex space-x-2">
                <?php if ($page > 1): ?>
                    <a href="<?= url('auditoria/logs') ?>?page=<?= $page - 1 ?>&nivel=<?= urlencode($nivel ?? '') ?>&modulo=<?= urlencode($moduloFilter ?? '') ?>&fecha_inicio=<?= urlencode($fechaInicio ?? '') ?>&fecha_fin=<?= urlencode($fechaFin ?? '') ?>" 
                       class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-100 text-sm">
                        <i class="fas fa-chevron-left"></i> Anterior
                    </a>
                <?php endif; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="<?= url('auditoria/logs') ?>?page=<?= $page + 1 ?>&nivel=<?= urlencode($nivel ?? '') ?>&modulo=<?= urlencode($moduloFilter ?? '') ?>&fecha_inicio=<?= urlencode($fechaInicio ?? '') ?>&fecha_fin=<?= urlencode($fechaFin ?? '') ?>" 
                       class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-100 text-sm">
                        Siguiente <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
