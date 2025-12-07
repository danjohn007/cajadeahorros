<?php
/**
 * Vista de Plazos y Condiciones
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Plazos y Condiciones</h1>
        <a href="<?= BASE_URL ?>/productos_financieros" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Regresar
        </a>
    </div>

    <?php if (isset($producto)): ?>
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Configurar Plazos - <?= htmlspecialchars($producto['nombre']) ?></h2>
        <form method="POST" action="<?= BASE_URL ?>/productos_financieros/plazos/<?= $producto['id'] ?>" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Plazo Mínimo (meses)</label>
                    <input type="number" name="plazo_minimo" value="<?= $producto['plazo_minimo'] ?? '' ?>" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Plazo Máximo (meses)</label>
                    <input type="number" name="plazo_maximo" value="<?= $producto['plazo_maximo'] ?? '' ?>" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Periodicidad de Pago</label>
                    <select name="periodicidad" class="w-full border rounded px-3 py-2" required>
                        <option value="mensual" <?= ($producto['periodicidad'] ?? '') === 'mensual' ? 'selected' : '' ?>>Mensual</option>
                        <option value="quincenal" <?= ($producto['periodicidad'] ?? '') === 'quincenal' ? 'selected' : '' ?>>Quincenal</option>
                        <option value="semanal" <?= ($producto['periodicidad'] ?? '') === 'semanal' ? 'selected' : '' ?>>Semanal</option>
                    </select>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Condiciones Especiales</label>
                <textarea name="condiciones" rows="3" class="w-full border rounded px-3 py-2"><?= $producto['condiciones'] ?? '' ?></textarea>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plazo Mín.</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plazo Máx.</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periodicidad</th>
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
                    <td class="px-6 py-4 whitespace-nowrap">-</td>
                    <td class="px-6 py-4 whitespace-nowrap">-</td>
                    <td class="px-6 py-4 whitespace-nowrap">-</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="<?= BASE_URL ?>/productos_financieros/plazos/<?= $prod['id'] ?>" class="text-blue-600 hover:text-blue-900">
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
