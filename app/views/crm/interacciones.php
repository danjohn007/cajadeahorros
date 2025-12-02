<?php
/**
 * Vista de Lista de Interacciones con Clientes
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <a href="<?= BASE_URL ?>/crm" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
            <i class="fas fa-arrow-left mr-1"></i> Volver a CRM
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Interacciones con Clientes</h2>
        <p class="text-gray-600">Historial de comunicaciones y seguimientos</p>
    </div>
    <a href="<?= BASE_URL ?>/crm/interaccion" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
        <i class="fas fa-plus mr-2"></i>Nueva Interacción
    </a>
</div>

<!-- Filtros -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Interacción</label>
            <select name="tipo" class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                <option value="">Todos</option>
                <option value="llamada" <?= $tipoFilter === 'llamada' ? 'selected' : '' ?>>Llamada</option>
                <option value="email" <?= $tipoFilter === 'email' ? 'selected' : '' ?>>Email</option>
                <option value="visita" <?= $tipoFilter === 'visita' ? 'selected' : '' ?>>Visita</option>
                <option value="whatsapp" <?= $tipoFilter === 'whatsapp' ? 'selected' : '' ?>>WhatsApp</option>
                <option value="reunion" <?= $tipoFilter === 'reunion' ? 'selected' : '' ?>>Reunión</option>
                <option value="otro" <?= $tipoFilter === 'otro' ? 'selected' : '' ?>>Otro</option>
            </select>
        </div>
        
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            <i class="fas fa-filter mr-2"></i>Filtrar
        </button>
        
        <a href="<?= BASE_URL ?>/crm/interacciones" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
            Limpiar
        </a>
    </form>
</div>

<!-- Lista de interacciones -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <?php if (empty($interacciones)): ?>
    <div class="p-6 text-center text-gray-500">
        <i class="fas fa-comments text-4xl mb-4"></i>
        <p>No hay interacciones registradas</p>
        <a href="<?= BASE_URL ?>/crm/interaccion" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
            Registrar primera interacción
        </a>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Fecha</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Cliente</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Tipo</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Asunto</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Resultado</th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Seguimiento</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Registró</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($interacciones as $interaccion): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm">
                        <?= date('d/m/Y H:i', strtotime($interaccion['created_at'])) ?>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800"><?= htmlspecialchars($interaccion['cliente_nombre']) ?></div>
                        <div class="text-sm text-gray-500"><?= htmlspecialchars($interaccion['numero_socio']) ?></div>
                    </td>
                    <td class="px-4 py-3">
                        <?php 
                        $tipoIcons = [
                            'llamada' => 'fa-phone text-green-600',
                            'email' => 'fa-envelope text-blue-600',
                            'visita' => 'fa-walking text-orange-600',
                            'whatsapp' => 'fa-brands fa-whatsapp text-green-500',
                            'reunion' => 'fa-users text-purple-600',
                            'otro' => 'fa-comment text-gray-600'
                        ];
                        $icon = $tipoIcons[$interaccion['tipo']] ?? 'fa-comment text-gray-600';
                        ?>
                        <span class="inline-flex items-center px-2 py-1 bg-gray-100 rounded text-sm">
                            <i class="fas <?= $icon ?> mr-2"></i>
                            <?= ucfirst($interaccion['tipo']) ?>
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800"><?= htmlspecialchars($interaccion['asunto'] ?: 'Sin asunto') ?></div>
                        <div class="text-sm text-gray-500 max-w-xs truncate">
                            <?= htmlspecialchars(substr($interaccion['descripcion'], 0, 100)) ?>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        <?= htmlspecialchars($interaccion['resultado'] ?: '-') ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <?php if ($interaccion['seguimiento_requerido']): ?>
                            <span class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">
                                <i class="fas fa-clock mr-1"></i>
                                <?= $interaccion['fecha_seguimiento'] ? date('d/m/Y', strtotime($interaccion['fecha_seguimiento'])) : 'Pendiente' ?>
                            </span>
                        <?php else: ?>
                            <span class="text-gray-400">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        <?= htmlspecialchars($interaccion['usuario_nombre'] ?? 'Sistema') ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Paginación -->
    <?php if ($totalPages > 1): ?>
    <div class="px-6 py-4 border-t bg-gray-50">
        <div class="flex justify-between items-center">
            <div class="text-sm text-gray-600">
                Mostrando página <?= $page ?> de <?= $totalPages ?> (<?= $total ?> registros)
            </div>
            <div class="flex space-x-2">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>&tipo=<?= urlencode($tipoFilter) ?>" 
                       class="px-3 py-1 border rounded hover:bg-gray-100">Anterior</a>
                <?php endif; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>&tipo=<?= urlencode($tipoFilter) ?>" 
                       class="px-3 py-1 border rounded hover:bg-gray-100">Siguiente</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>
