<?php
/**
 * Vista principal del Sistema ESCROW
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
        <p class="text-gray-600">Gestión de transacciones de custodia de fondos</p>
    </div>
    <div class="mt-4 md:mt-0">
        <a href="<?= url('escrow/crear') ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>Nueva Transacción
        </a>
    </div>
</div>

<!-- Estadísticas -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-exchange-alt text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Transacciones Totales</p>
                <p class="text-2xl font-bold text-gray-800"><?= number_format($stats['total'] ?? 0) ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-clock text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Activas</p>
                <p class="text-2xl font-bold text-green-600"><?= number_format($stats['activas'] ?? 0) ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                <i class="fas fa-lock text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Fondos Retenidos</p>
                <p class="text-2xl font-bold text-yellow-600">$<?= number_format($stats['monto_retenido'] ?? 0, 2) ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-red-100 text-red-600">
                <i class="fas fa-exclamation-triangle text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">En Disputa</p>
                <p class="text-2xl font-bold text-red-600"><?= number_format($stats['disputas'] ?? 0) ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form method="GET" action="<?= url('escrow') ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
            <input type="text" name="buscar" value="<?= htmlspecialchars($filtros['buscar'] ?? '') ?>" 
                   placeholder="Número, título, participante..."
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
            <select name="estatus" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                <option value="">Todos</option>
                <option value="borrador" <?= ($filtros['estatus'] ?? '') === 'borrador' ? 'selected' : '' ?>>Borrador</option>
                <option value="pendiente_deposito" <?= ($filtros['estatus'] ?? '') === 'pendiente_deposito' ? 'selected' : '' ?>>Pendiente Depósito</option>
                <option value="fondos_depositados" <?= ($filtros['estatus'] ?? '') === 'fondos_depositados' ? 'selected' : '' ?>>Fondos Depositados</option>
                <option value="en_proceso" <?= ($filtros['estatus'] ?? '') === 'en_proceso' ? 'selected' : '' ?>>En Proceso</option>
                <option value="liberado" <?= ($filtros['estatus'] ?? '') === 'liberado' ? 'selected' : '' ?>>Liberado</option>
                <option value="disputa" <?= ($filtros['estatus'] ?? '') === 'disputa' ? 'selected' : '' ?>>En Disputa</option>
                <option value="cancelado" <?= ($filtros['estatus'] ?? '') === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
            <input type="date" name="fecha_desde" value="<?= htmlspecialchars($filtros['fecha_desde'] ?? '') ?>"
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
            <div class="flex gap-2">
                <input type="date" name="fecha_hasta" value="<?= htmlspecialchars($filtros['fecha_hasta'] ?? '') ?>"
                       class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Lista de Transacciones -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transacción</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comprador</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendedor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progreso</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($transacciones)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-4 block"></i>
                        <p>No hay transacciones ESCROW registradas</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($transacciones as $t): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-blue-600">
                            <a href="<?= url('escrow/ver/' . $t['id']) ?>"><?= htmlspecialchars($t['numero_transaccion']) ?></a>
                        </div>
                        <div class="text-sm text-gray-500"><?= htmlspecialchars(substr($t['titulo'], 0, 30)) ?><?= strlen($t['titulo']) > 30 ? '...' : '' ?></div>
                        <div class="text-xs text-gray-400"><?= date('d/m/Y', strtotime($t['fecha_creacion'])) ?></div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        <?= htmlspecialchars($t['comprador'] ?? 'No especificado') ?>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        <?= htmlspecialchars($t['vendedor'] ?? 'No especificado') ?>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">$<?= number_format($t['monto_total'], 2) ?></div>
                        <?php if ($t['monto_liberado'] > 0): ?>
                        <div class="text-xs text-green-600">Liberado: $<?= number_format($t['monto_liberado'], 2) ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php
                        $estatusColores = [
                            'borrador' => 'bg-gray-100 text-gray-800',
                            'pendiente_deposito' => 'bg-yellow-100 text-yellow-800',
                            'fondos_depositados' => 'bg-blue-100 text-blue-800',
                            'en_proceso' => 'bg-indigo-100 text-indigo-800',
                            'entrega_confirmada' => 'bg-purple-100 text-purple-800',
                            'liberado' => 'bg-green-100 text-green-800',
                            'disputa' => 'bg-red-100 text-red-800',
                            'cancelado' => 'bg-gray-100 text-gray-800',
                            'reembolsado' => 'bg-orange-100 text-orange-800'
                        ];
                        $colorClass = $estatusColores[$t['estatus']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $colorClass ?>">
                            <?= ucfirst(str_replace('_', ' ', $t['estatus'])) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <?php if ($t['total_hitos'] > 0): ?>
                        <div class="flex items-center">
                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: <?= ($t['hitos_completados'] / $t['total_hitos']) * 100 ?>%"></div>
                            </div>
                            <span class="text-xs text-gray-500"><?= $t['hitos_completados'] ?>/<?= $t['total_hitos'] ?></span>
                        </div>
                        <?php else: ?>
                        <span class="text-xs text-gray-400">Sin hitos</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="<?= url('escrow/ver/' . $t['id']) ?>" class="text-blue-600 hover:text-blue-900 mr-3" title="Ver">
                            <i class="fas fa-eye"></i>
                        </a>
                        <?php if ($t['estatus'] === 'borrador'): ?>
                        <a href="<?= url('escrow/editar/' . $t['id']) ?>" class="text-gray-600 hover:text-gray-900" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Información sobre ESCROW -->
<div class="mt-8 bg-blue-50 rounded-lg p-6 border border-blue-200">
    <h3 class="text-lg font-semibold text-blue-800 mb-4">
        <i class="fas fa-info-circle mr-2"></i>¿Qué es el Sistema ESCROW?
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-blue-700">
        <div class="flex items-start">
            <i class="fas fa-shield-alt text-blue-500 mt-1 mr-3"></i>
            <div>
                <strong class="block mb-1">Protección del Comprador</strong>
                <p>Los fondos se retienen hasta que se confirme la entrega del bien o servicio.</p>
            </div>
        </div>
        <div class="flex items-start">
            <i class="fas fa-handshake text-blue-500 mt-1 mr-3"></i>
            <div>
                <strong class="block mb-1">Garantía para el Vendedor</strong>
                <p>El vendedor tiene la certeza de que los fondos están disponibles.</p>
            </div>
        </div>
        <div class="flex items-start">
            <i class="fas fa-balance-scale text-blue-500 mt-1 mr-3"></i>
            <div>
                <strong class="block mb-1">Mediación Imparcial</strong>
                <p>En caso de disputa, actuamos como mediador imparcial.</p>
            </div>
        </div>
    </div>
</div>
