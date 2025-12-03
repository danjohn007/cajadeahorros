<?php
/**
 * Vista de Sesiones de Usuario
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
        <p class="text-gray-600">Control de sesiones de usuarios del sistema</p>
    </div>
    <a href="<?= url('auditoria') ?>" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Volver a Auditoría
    </a>
</div>

<!-- Estadísticas -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-user-check text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Sesiones Activas</p>
                <p class="text-2xl font-bold text-gray-800"><?= number_format($stats['activas'] ?? 0) ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-sign-in-alt text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Sesiones Hoy</p>
                <p class="text-2xl font-bold text-gray-800"><?= number_format($stats['hoy'] ?? 0) ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Sesiones -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">Historial de Sesiones</h2>
        <span class="text-sm text-gray-600">Total: <?= number_format($total ?? 0) ?> sesiones</span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inicio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Último Acceso</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Navegador</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($sesiones)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            No hay sesiones registradas
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($sesiones as $sesion): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i class="fas fa-user text-blue-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($sesion['usuario_nombre'] ?? 'Desconocido') ?>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            <?= htmlspecialchars($sesion['email'] ?? '') ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($sesion['activa'] ?? false): ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-circle text-xs mr-1"></i>Activa
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                        Finalizada
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= isset($sesion['fecha_inicio']) ? date('d/m/Y H:i', strtotime($sesion['fecha_inicio'])) : '-' ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= isset($sesion['fecha_ultimo_acceso']) ? date('d/m/Y H:i', strtotime($sesion['fecha_ultimo_acceso'])) : '-' ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= htmlspecialchars($sesion['ip'] ?? '-') ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="<?= htmlspecialchars($sesion['user_agent'] ?? '') ?>">
                                <?php
                                $userAgent = $sesion['user_agent'] ?? '';
                                if (strpos($userAgent, 'Chrome') !== false) {
                                    echo '<i class="fab fa-chrome mr-1"></i>Chrome';
                                } elseif (strpos($userAgent, 'Firefox') !== false) {
                                    echo '<i class="fab fa-firefox mr-1"></i>Firefox';
                                } elseif (strpos($userAgent, 'Safari') !== false) {
                                    echo '<i class="fab fa-safari mr-1"></i>Safari';
                                } elseif (strpos($userAgent, 'Edge') !== false) {
                                    echo '<i class="fab fa-edge mr-1"></i>Edge';
                                } else {
                                    echo '<i class="fas fa-globe mr-1"></i>Otro';
                                }
                                ?>
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
                    <a href="<?= url('auditoria/sesiones') ?>?page=<?= $page - 1 ?>" 
                       class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-100 text-sm">
                        <i class="fas fa-chevron-left"></i> Anterior
                    </a>
                <?php endif; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="<?= url('auditoria/sesiones') ?>?page=<?= $page + 1 ?>" 
                       class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-100 text-sm">
                        Siguiente <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
