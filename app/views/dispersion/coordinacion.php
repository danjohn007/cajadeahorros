<?php
/**
 * Vista de Coordinación de Dispersión
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Coordinación de Dispersión</h1>
        <a href="<?= BASE_URL ?>/dispersion" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Regresar
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" value="<?= $fecha_inicio ?? '' ?>" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                <input type="date" name="fecha_fin" value="<?= $fecha_fin ?? '' ?>" class="w-full border rounded px-3 py-2">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-primary-800 hover:bg-primary-700 text-white px-4 py-2 rounded w-full">
                    <i class="fas fa-filter mr-2"></i>Filtrar
                </button>
            </div>
        </form>
    </div>

    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
        <div class="flex">
            <div class="flex-1">
                <p class="text-sm font-medium text-blue-800">Total de Créditos: <?= $totales['cantidad'] ?? 0 ?></p>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-blue-800">Monto Total: $<?= number_format($totales['monto_total'] ?? 0, 2) ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Socio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estatus</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($creditos)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay créditos para dispersión</td>
                </tr>
                <?php else: ?>
                <?php foreach ($creditos as $credito): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($credito['numero_credito'] ?? 'N/A') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?= htmlspecialchars($credito['nombre'] . ' ' . $credito['apellido_paterno']) ?>
                        <div class="text-xs text-gray-500"><?= htmlspecialchars($credito['numero_socio'] ?? '') ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($credito['producto_nombre'] ?? 'N/A') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">$<?= number_format($credito['monto_autorizado'] ?? 0, 2) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full 
                            <?= $credito['estatus'] === 'aprobado' ? 'bg-green-100 text-green-800' : '' ?>
                            <?= $credito['estatus'] === 'formalizacion' ? 'bg-blue-100 text-blue-800' : '' ?>">
                            <?= htmlspecialchars(ucfirst($credito['estatus'])) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="<?= BASE_URL ?>/dispersion/registrar/<?= $credito['id'] ?>" class="text-green-600 hover:text-green-900">
                            <i class="fas fa-paper-plane"></i> Dispersar
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
