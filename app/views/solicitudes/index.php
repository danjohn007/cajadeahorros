<?php
/**
 * Vista principal de Solicitudes de Crédito
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Solicitudes de Crédito</h1>
        <a href="<?= BASE_URL ?>/solicitudes/recepcion" class="bg-primary-800 hover:bg-primary-700 text-white px-4 py-2 rounded">
            <i class="fas fa-plus mr-2"></i>Nueva Solicitud
        </a>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <select class="border rounded px-3 py-2" onchange="window.location.href='<?= BASE_URL ?>/solicitudes?estado=' + this.value">
                <option value="">Todos los estados</option>
                <option value="solicitado" <?= isset($_GET['estado']) && $_GET['estado'] === 'solicitado' ? 'selected' : '' ?>>Solicitado</option>
                <option value="revision" <?= isset($_GET['estado']) && $_GET['estado'] === 'revision' ? 'selected' : '' ?>>En Revisión</option>
                <option value="aprobado" <?= isset($_GET['estado']) && $_GET['estado'] === 'aprobado' ? 'selected' : '' ?>>Aprobado</option>
                <option value="rechazado" <?= isset($_GET['estado']) && $_GET['estado'] === 'rechazado' ? 'selected' : '' ?>>Rechazado</option>
            </select>
        </div>
    </div>

    <!-- Tabla de solicitudes -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Solicitante</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Promotor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($solicitudes)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No hay solicitudes registradas</td>
                </tr>
                <?php else: ?>
                <?php foreach ($solicitudes as $sol): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($sol['numero_credito'] ?? 'N/A') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?= htmlspecialchars($sol['nombre'] . ' ' . $sol['apellido_paterno']) ?>
                        <div class="text-xs text-gray-500"><?= htmlspecialchars($sol['numero_socio'] ?? '') ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($sol['producto_nombre'] ?? 'N/A') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">$<?= number_format($sol['monto_solicitado'] ?? 0, 2) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full 
                            <?= $sol['estatus'] === 'solicitado' ? 'bg-blue-100 text-blue-800' : '' ?>
                            <?= $sol['estatus'] === 'revision' ? 'bg-yellow-100 text-yellow-800' : '' ?>
                            <?= $sol['estatus'] === 'aprobado' ? 'bg-green-100 text-green-800' : '' ?>
                            <?= $sol['estatus'] === 'rechazado' ? 'bg-red-100 text-red-800' : '' ?>">
                            <?= htmlspecialchars(ucfirst($sol['estatus'])) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($sol['promotor_nombre'] ?? 'Sin asignar') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="<?= BASE_URL ?>/solicitudes/captura/<?= $sol['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="<?= BASE_URL ?>/solicitudes/evaluacion/<?= $sol['id'] ?>" class="text-green-600 hover:text-green-900 mr-3">
                            <i class="fas fa-clipboard-check"></i>
                        </a>
                        <a href="<?= BASE_URL ?>/solicitudes/expediente/<?= $sol['id'] ?>" class="text-purple-600 hover:text-purple-900">
                            <i class="fas fa-folder-open"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
