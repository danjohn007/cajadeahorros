<?php
/**
 * Vista de Prepagos y Liquidaciones Anticipadas
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Prepagos y Liquidaciones Anticipadas</h1>
        <a href="<?= BASE_URL ?>/cartera" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Regresar
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número Crédito</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Socio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto Liquidado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Liquidación</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($prepagos)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay liquidaciones registradas</td>
                </tr>
                <?php else: ?>
                <?php foreach ($prepagos as $prepago): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($prepago['numero_credito'] ?? 'N/A') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?= htmlspecialchars($prepago['nombre'] . ' ' . $prepago['apellido_paterno']) ?>
                        <div class="text-xs text-gray-500"><?= htmlspecialchars($prepago['numero_socio'] ?? '') ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">$<?= number_format($prepago['monto_liquidado'] ?? 0, 2) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= date('d/m/Y', strtotime($prepago['fecha_liquidacion'])) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                            <?= htmlspecialchars(ucfirst($prepago['tipo_liquidacion'] ?? 'N/A')) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="<?= BASE_URL ?>/cartera/ver-liquidacion/<?= $prepago['id'] ?>" class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-eye"></i> Ver Detalles
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
