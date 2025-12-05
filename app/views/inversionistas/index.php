<?php
/**
 * Vista de Lista de Inversionistas
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
        <p class="text-gray-600">Administración de inversionistas del sistema</p>
    </div>
    <a href="<?= url('inversionistas/crear') ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
        <i class="fas fa-plus mr-2"></i>Nuevo Inversionista
    </a>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-md p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Inversionistas</p>
                <p class="text-2xl font-bold text-blue-600"><?= $stats['total_inversionistas'] ?? 0 ?></p>
            </div>
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-users text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Capital Total Invertido</p>
                <p class="text-2xl font-bold text-green-600">$<?= number_format($stats['total_capital'] ?? 0, 2) ?></p>
            </div>
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-dollar-sign text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Inversiones Activas</p>
                <p class="text-2xl font-bold text-purple-600"><?= $stats['inversiones_activas'] ?? 0 ?></p>
            </div>
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <i class="fas fa-chart-line text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filters -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-64">
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
            <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" 
                   placeholder="Nombre, RFC, Email..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Estatus</label>
            <select name="estatus" class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                <option value="">Todos</option>
                <option value="activo" <?= $estatus === 'activo' ? 'selected' : '' ?>>Activo</option>
                <option value="inactivo" <?= $estatus === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            <i class="fas fa-search mr-2"></i>Buscar
        </button>
        <a href="<?= url('inversionistas') ?>" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-100">
            Limpiar
        </a>
    </form>
</div>

<!-- Inversionistas Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <?php if (empty($inversionistas)): ?>
        <div class="p-8 text-center text-gray-500">
            <i class="fas fa-users text-4xl mb-4"></i>
            <p>No se encontraron inversionistas</p>
        </div>
    <?php else: ?>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Inversionista</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contacto</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Invertido</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inversiones</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estatus</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($inversionistas as $inv): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium text-gray-900">
                                    <?= htmlspecialchars($inv['nombre'] . ' ' . $inv['apellido_paterno'] . ' ' . ($inv['apellido_materno'] ?? '')) ?>
                                </p>
                                <p class="text-sm text-gray-500"><?= htmlspecialchars($inv['numero_inversionista']) ?></p>
                                <?php if ($inv['rfc']): ?>
                                    <p class="text-xs text-gray-400">RFC: <?= htmlspecialchars($inv['rfc']) ?></p>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?php if ($inv['email']): ?>
                                <p><i class="fas fa-envelope mr-1"></i><?= htmlspecialchars($inv['email']) ?></p>
                            <?php endif; ?>
                            <?php if ($inv['celular']): ?>
                                <p><i class="fas fa-phone mr-1"></i><?= htmlspecialchars($inv['celular']) ?></p>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium text-green-600">
                            $<?= number_format($inv['total_invertido'] ?? 0, 2) ?>
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-gray-500">
                            <?= $inv['inversiones_activas'] ?? 0 ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 text-xs rounded-full <?= $inv['estatus'] === 'activo' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                <?= ucfirst($inv['estatus']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="<?= url('inversionistas/ver/' . $inv['id']) ?>" class="text-blue-600 hover:text-blue-800 mr-3">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= url('inversionistas/editar/' . $inv['id']) ?>" class="text-green-600 hover:text-green-800 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?= url('inversionistas/inversion/' . $inv['id']) ?>" class="text-purple-600 hover:text-purple-800" title="Nueva Inversión">
                                <i class="fas fa-plus-circle"></i>
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
                            <a href="<?= url('inversionistas?page=' . ($page - 1) . '&q=' . urlencode($search) . '&estatus=' . urlencode($estatus)) ?>" 
                               class="px-3 py-1 bg-white border rounded hover:bg-gray-100">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <a href="<?= url('inversionistas?page=' . $i . '&q=' . urlencode($search) . '&estatus=' . urlencode($estatus)) ?>" 
                               class="px-3 py-1 <?= $i === $page ? 'bg-blue-600 text-white' : 'bg-white border hover:bg-gray-100' ?> rounded">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="<?= url('inversionistas?page=' . ($page + 1) . '&q=' . urlencode($search) . '&estatus=' . urlencode($estatus)) ?>" 
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
