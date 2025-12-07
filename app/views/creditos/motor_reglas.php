<?php
/**
 * Vista de Motor de Reglas de Crédito
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Motor de Reglas de Crédito</h1>
        <a href="<?= BASE_URL ?>/creditos" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Regresar
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-semibold mb-4">Políticas de Crédito Configuradas</h2>
            
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Política</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($politicas)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No hay políticas configuradas</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($politicas as $politica): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($politica['nombre'] ?? 'N/A') ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($politica['producto_nombre'] ?? 'Todos') ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full <?= $politica['activo'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                <?= $politica['activo'] ? 'Activa' : 'Inactiva' ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="<?= BASE_URL ?>/creditos/politica/<?= $politica['id'] ?>" class="text-blue-600 hover:text-blue-900">
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
</div>
