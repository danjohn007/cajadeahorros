<?php
/**
 * Vista de Métricas de Clientes
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <a href="<?= BASE_URL ?>/crm" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
            <i class="fas fa-arrow-left mr-1"></i> Volver a CRM
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Métricas de Clientes</h2>
        <p class="text-gray-600">Análisis detallado del comportamiento de clientes</p>
    </div>
    <div class="text-sm text-gray-600">
        Total: <?= number_format($total) ?> clientes
    </div>
</div>

<!-- Filtros -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nivel de Riesgo</label>
            <select name="riesgo" class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                <option value="">Todos</option>
                <option value="bajo" <?= $riesgo === 'bajo' ? 'selected' : '' ?>>Bajo</option>
                <option value="medio" <?= $riesgo === 'medio' ? 'selected' : '' ?>>Medio</option>
                <option value="alto" <?= $riesgo === 'alto' ? 'selected' : '' ?>>Alto</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Cliente VIP</label>
            <select name="vip" class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                <option value="">Todos</option>
                <option value="1" <?= $vip === '1' ? 'selected' : '' ?>>Solo VIP</option>
            </select>
        </div>
        
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            <i class="fas fa-filter mr-2"></i>Filtrar
        </button>
        
        <a href="<?= BASE_URL ?>/crm/metricas" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
            Limpiar
        </a>
    </form>
</div>

<!-- Tabla de clientes con métricas -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <?php if (empty($clientes)): ?>
    <div class="p-6 text-center text-gray-500">
        <i class="fas fa-users text-4xl mb-4"></i>
        <p>No se encontraron clientes con los filtros seleccionados</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Socio</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Contacto</th>
                    <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">LTV</th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Frecuencia</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Última Transacción</th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Días Inactivo</th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Riesgo</th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">VIP</th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($clientes as $cliente): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800"><?= htmlspecialchars($cliente['nombre']) ?></div>
                        <div class="text-sm text-gray-500"><?= htmlspecialchars($cliente['numero_socio']) ?></div>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <?php if ($cliente['email']): ?>
                            <div><i class="fas fa-envelope text-gray-400 mr-1"></i> <?= htmlspecialchars($cliente['email']) ?></div>
                        <?php endif; ?>
                        <?php if ($cliente['celular']): ?>
                            <div><i class="fas fa-phone text-gray-400 mr-1"></i> <?= htmlspecialchars($cliente['celular']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-right font-medium text-blue-600">
                        $<?= number_format($cliente['ltv'], 2) ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <?= number_format($cliente['frecuencia']) ?>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <?= $cliente['ultima_transaccion'] ? date('d/m/Y', strtotime($cliente['ultima_transaccion'])) : 'N/A' ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="<?= $cliente['dias_inactivo'] > 90 ? 'text-red-600' : ($cliente['dias_inactivo'] > 30 ? 'text-orange-600' : 'text-green-600') ?>">
                            <?= number_format($cliente['dias_inactivo']) ?> días
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <?php 
                        $riesgoColor = [
                            'bajo' => 'bg-green-100 text-green-800',
                            'medio' => 'bg-yellow-100 text-yellow-800',
                            'alto' => 'bg-red-100 text-red-800'
                        ];
                        ?>
                        <span class="px-2 py-1 rounded-full text-xs <?= $riesgoColor[$cliente['nivel_riesgo']] ?? 'bg-gray-100 text-gray-800' ?>">
                            <?= ucfirst($cliente['nivel_riesgo']) ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <?php if ($cliente['es_vip']): ?>
                            <span class="text-yellow-500"><i class="fas fa-crown"></i></span>
                        <?php else: ?>
                            <span class="text-gray-300"><i class="far fa-circle"></i></span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="<?= BASE_URL ?>/socios/ver/<?= $cliente['id'] ?>" 
                           class="text-blue-600 hover:text-blue-800" title="Ver socio">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="<?= BASE_URL ?>/crm/interaccion?socio_id=<?= $cliente['id'] ?>" 
                           class="text-green-600 hover:text-green-800 ml-2" title="Nueva interacción">
                            <i class="fas fa-comment"></i>
                        </a>
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
                Página <?= $page ?> de <?= $totalPages ?>
            </div>
            <div class="flex space-x-2">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>&riesgo=<?= urlencode($riesgo) ?>&vip=<?= urlencode($vip) ?>" 
                       class="px-3 py-1 border rounded hover:bg-gray-100">Anterior</a>
                <?php endif; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>&riesgo=<?= urlencode($riesgo) ?>&vip=<?= urlencode($vip) ?>" 
                       class="px-3 py-1 border rounded hover:bg-gray-100">Siguiente</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>
