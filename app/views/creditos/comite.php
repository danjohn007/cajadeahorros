<?php
/**
 * Vista de Comité de Crédito
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Comité de Crédito</h1>
        <a href="<?= BASE_URL ?>/creditos" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Regresar
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Solicitante</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Solicitud</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($solicitudes)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay solicitudes en revisión</td>
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
                    <td class="px-6 py-4 whitespace-nowrap"><?= date('d/m/Y', strtotime($sol['fecha_solicitud'])) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="<?= BASE_URL ?>/creditos/ver/<?= $sol['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                        <a href="<?= BASE_URL ?>/creditos/autorizar/<?= $sol['id'] ?>" class="text-green-600 hover:text-green-900 mr-3">
                            <i class="fas fa-check"></i> Evaluar
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
