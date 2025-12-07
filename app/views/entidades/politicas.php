<?php
/**
 * Vista de Políticas Institucionales
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Políticas Institucionales</h1>
        <a href="<?= BASE_URL ?>/entidades" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Regresar
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold">Políticas de Crédito Activas</h2>
        </div>
        
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($politicas)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay políticas configuradas</td>
                </tr>
                <?php else: ?>
                <?php foreach ($politicas as $politica): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap font-medium"><?= htmlspecialchars($politica['nombre'] ?? 'N/A') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($politica['producto_nombre'] ?? 'Todos') ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars(substr($politica['descripcion'] ?? '', 0, 50)) ?>...</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full <?= $politica['activo'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                            <?= $politica['activo'] ? 'Activa' : 'Inactiva' ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="<?= BASE_URL ?>/entidades/ver-politica/<?= $politica['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                        <a href="<?= BASE_URL ?>/entidades/editar-politica/<?= $politica['id'] ?>" class="text-green-600 hover:text-green-900">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-6 bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Productos Disponibles para Asignación</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <?php if (empty($productos)): ?>
            <p class="text-gray-500 text-sm col-span-3">No hay productos disponibles</p>
            <?php else: ?>
            <?php foreach ($productos as $producto): ?>
            <div class="border rounded-lg p-4">
                <h3 class="font-semibold text-gray-800 mb-2"><?= htmlspecialchars($producto['nombre']) ?></h3>
                <p class="text-xs text-gray-500 mb-3">ID: <?= $producto['id'] ?></p>
                <a href="<?= BASE_URL ?>/entidades/crear-politica?producto_id=<?= $producto['id'] ?>" 
                   class="text-blue-600 hover:text-blue-900 text-sm">
                    <i class="fas fa-plus mr-1"></i>Crear política
                </a>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
