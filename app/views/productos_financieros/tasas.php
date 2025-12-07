<?php
/**
 * Vista de Tasas de Interés y Comisiones
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Tasas de Interés y Comisiones</h1>
        <a href="<?= BASE_URL ?>/productos_financieros" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Regresar
        </a>
    </div>

    <?php if (isset($producto)): ?>
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Configurar Tasas - <?= htmlspecialchars($producto['nombre']) ?></h2>
        <form method="POST" action="<?= BASE_URL ?>/productos_financieros/tasas/<?= $producto['id'] ?>" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tasa de Interés Anual (%)</label>
                    <input type="number" name="tasa_interes" step="0.01" value="<?= $producto['tasa_interes'] ?? '' ?>" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Comisión por Apertura (%)</label>
                    <input type="number" name="comision_apertura" step="0.01" value="<?= $producto['comision_apertura'] ?? '' ?>" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tasa de Mora (%)</label>
                    <input type="number" name="tasa_mora" step="0.01" value="<?= $producto['tasa_mora'] ?? '' ?>" class="w-full border rounded px-3 py-2">
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-primary-800 hover:bg-primary-700 text-white px-6 py-2 rounded">
                    <i class="fas fa-save mr-2"></i>Guardar Cambios
                </button>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold">Productos Financieros</h2>
        </div>
        
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empresa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tasa Interés</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comisión</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($productos)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay productos configurados</td>
                </tr>
                <?php else: ?>
                <?php foreach ($productos as $prod): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($prod['nombre'] ?? 'N/A') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($prod['empresa_nombre'] ?? 'N/A') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= isset($prod['tasa_interes']) ? number_format($prod['tasa_interes'], 2) . '%' : '-' ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= isset($prod['comision_apertura']) ? number_format($prod['comision_apertura'], 2) . '%' : '-' ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="<?= BASE_URL ?>/productos_financieros/tasas/<?= $prod['id'] ?>" class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-edit"></i> Configurar
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
