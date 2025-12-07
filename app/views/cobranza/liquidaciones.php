<?php
/**
 * Vista de Liquidaciones
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Liquidaciones</h1>
        <a href="<?= BASE_URL ?>/cobranza" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($liquidaciones)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay liquidaciones registradas</td>
                </tr>
                <?php else: ?>
                <?php foreach ($liquidaciones as $liquidacion): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($liquidacion['numero_credito'] ?? 'N/A') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?= htmlspecialchars($liquidacion['nombre'] . ' ' . $liquidacion['apellido_paterno']) ?>
                        <div class="text-xs text-gray-500"><?= htmlspecialchars($liquidacion['numero_socio'] ?? '') ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">$<?= number_format($liquidacion['monto_liquidado'] ?? 0, 2) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= date('d/m/Y', strtotime($liquidacion['fecha_liquidacion'])) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                            <?= htmlspecialchars(ucfirst($liquidacion['tipo_liquidacion'] ?? 'N/A')) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="<?= BASE_URL ?>/cobranza/ver-liquidacion/<?= $liquidacion['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                        <a href="<?= BASE_URL ?>/reportes/generar-liquidacion/<?= $liquidacion['id'] ?>" class="text-green-600 hover:text-green-900">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
