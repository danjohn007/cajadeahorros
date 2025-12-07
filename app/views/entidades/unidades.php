<?php
/**
 * Vista de Unidades de Negocio
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Unidades de Negocio</h1>
        <a href="<?= BASE_URL ?>/entidades" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Regresar
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filtrar por Empresa</label>
                <select name="empresa_id" class="w-full border rounded px-3 py-2" onchange="this.form.submit()">
                    <option value="">Todas las empresas</option>
                    <?php foreach ($empresas as $empresa): ?>
                    <option value="<?= $empresa['id'] ?>" <?= (isset($_GET['empresa_id']) && $_GET['empresa_id'] == $empresa['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($empresa['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empresa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ubicación</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($unidades)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay unidades registradas</td>
                </tr>
                <?php else: ?>
                <?php foreach ($unidades as $unidad): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap font-medium"><?= htmlspecialchars($unidad['nombre'] ?? 'N/A') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($unidad['empresa_nombre'] ?? 'N/A') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($unidad['tipo'] ?? 'N/A') ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($unidad['ubicacion'] ?? 'N/A') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full <?= $unidad['activo'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                            <?= $unidad['activo'] ? 'Activa' : 'Inactiva' ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="<?= BASE_URL ?>/entidades/editar-unidad/<?= $unidad['id'] ?>" class="text-blue-600 hover:text-blue-900">
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
