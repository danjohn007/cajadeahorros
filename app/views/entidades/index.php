<?php
/**
 * Vista principal de Entidades (Empresas del Grupo)
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Empresas del Grupo</h1>
        <a href="<?= BASE_URL ?>/entidades/empresas" class="bg-primary-800 hover:bg-primary-700 text-white px-4 py-2 rounded">
            <i class="fas fa-plus mr-2"></i>Nueva Empresa
        </a>
    </div>

    <!-- Navegación rápida -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <a href="<?= BASE_URL ?>/entidades/empresas" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="bg-blue-100 rounded-full p-3 mr-4">
                    <i class="fas fa-building text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Empresas</p>
                    <p class="text-2xl font-bold text-gray-800"><?= count($empresas ?? []) ?></p>
                </div>
            </div>
        </a>
        
        <a href="<?= BASE_URL ?>/entidades/unidades" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="bg-green-100 rounded-full p-3 mr-4">
                    <i class="fas fa-sitemap text-green-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Unidades</p>
                    <p class="text-2xl font-bold text-gray-800">
                        <?= array_sum(array_column($empresas ?? [], 'total_unidades')) ?>
                    </p>
                </div>
            </div>
        </a>
        
        <a href="<?= BASE_URL ?>/entidades/catalogos" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="bg-purple-100 rounded-full p-3 mr-4">
                    <i class="fas fa-list text-purple-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Catálogos</p>
                    <p class="text-2xl font-bold text-gray-800">-</p>
                </div>
            </div>
        </a>
        
        <a href="<?= BASE_URL ?>/entidades/reportes" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="bg-orange-100 rounded-full p-3 mr-4">
                    <i class="fas fa-chart-bar text-orange-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Reportes</p>
                    <p class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-arrow-right text-sm"></i>
                    </p>
                </div>
            </div>
        </a>
    </div>

    <!-- Lista de empresas -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h2 class="text-lg font-semibold text-gray-700">Empresas Registradas</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empresa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">RFC</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contacto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unidades</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Créditos</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($empresas)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No hay empresas registradas</td>
                </tr>
                <?php else: ?>
                <?php foreach ($empresas as $empresa): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-medium text-gray-900"><?= htmlspecialchars($empresa['nombre']) ?></div>
                        <div class="text-sm text-gray-500"><?= htmlspecialchars($empresa['nombre_corto'] ?? '') ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm"><?= htmlspecialchars($empresa['rfc'] ?? 'N/A') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm"><?= htmlspecialchars($empresa['telefono'] ?? 'N/A') ?></div>
                        <div class="text-xs text-gray-500"><?= htmlspecialchars($empresa['email'] ?? '') ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                            <?= $empresa['total_unidades'] ?? 0 ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                            <?= $empresa['total_creditos'] ?? 0 ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full 
                            <?= $empresa['activo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                            <?= $empresa['activo'] ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="<?= BASE_URL ?>/entidades/unidades?empresa_id=<?= $empresa['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3" title="Ver Unidades">
                            <i class="fas fa-sitemap"></i>
                        </a>
                        <a href="<?= BASE_URL ?>/productos-financieros?empresa_id=<?= $empresa['id'] ?>" class="text-green-600 hover:text-green-900" title="Productos">
                            <i class="fas fa-box-open"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
