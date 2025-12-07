<?php
/**
 * Vista de Configuración de Créditos
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Configuración de Créditos</h1>
        <a href="<?= BASE_URL ?>/productos_financieros" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Regresar
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold">Productos de Crédito Disponibles</h2>
        </div>
        
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empresa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto Mín.</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto Máx.</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($productos)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No hay productos configurados</td>
                </tr>
                <?php else: ?>
                <?php foreach ($productos as $producto): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($producto['nombre'] ?? 'N/A') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($producto['tipo'] ?? 'N/A') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($producto['empresa_nombre'] ?? 'N/A') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">$<?= number_format($producto['monto_minimo'] ?? 0, 2) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">$<?= number_format($producto['monto_maximo'] ?? 0, 2) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full <?= $producto['activo'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                            <?= $producto['activo'] ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="<?= BASE_URL ?>/productos_financieros/editar/<?= $producto['id'] ?>" class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
