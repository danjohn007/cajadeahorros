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

<!-- Últimas acciones -->
<div class="bg-white rounded-lg shadow-md mt-6 overflow-hidden">
    <div class="px-6 py-4 border-b flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">Últimas Acciones</h2>
        <a href="<?= url('bitacora') ?>" class="text-blue-600 hover:text-blue-800 text-sm">Ver todas</a>
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
        </tbody>
    </table>
</div>
