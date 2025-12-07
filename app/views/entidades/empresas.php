<?php
/**
 * Vista de Administración de Empresas
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Administración de Empresas</h1>
        <a href="<?= BASE_URL ?>/entidades" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Regresar
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold">Empresas del Grupo</h2>
        </div>
        
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">RFC</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Razón Social</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($empresas)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay empresas registradas</td>
                </tr>
                <?php else: ?>
                <?php foreach ($empresas as $empresa): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap font-medium"><?= htmlspecialchars($empresa['nombre'] ?? 'N/A') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($empresa['rfc'] ?? 'N/A') ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($empresa['razon_social'] ?? 'N/A') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full <?= $empresa['activo'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                            <?= $empresa['activo'] ? 'Activa' : 'Inactiva' ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="<?= BASE_URL ?>/entidades/editar-empresa/<?= $empresa['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="<?= BASE_URL ?>/entidades/unidades?empresa_id=<?= $empresa['id'] ?>" class="text-green-600 hover:text-green-900">
                            <i class="fas fa-sitemap"></i> Unidades
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
