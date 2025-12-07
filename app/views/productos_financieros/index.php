<?php
/**
 * Vista principal de Productos Financieros
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Productos Financieros</h1>
        <a href="<?= BASE_URL ?>/productos-financieros/creditos" class="bg-primary-800 hover:bg-primary-700 text-white px-4 py-2 rounded">
            <i class="fas fa-plus mr-2"></i>Nuevo Producto
        </a>
    </div>

    <!-- Navegación rápida -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <a href="<?= BASE_URL ?>/productos-financieros/creditos" class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition">
            <div class="text-center">
                <i class="fas fa-hand-holding-usd text-blue-600 text-3xl mb-2"></i>
                <p class="text-sm text-gray-600">Configuración</p>
                <p class="text-sm text-gray-600">de Créditos</p>
            </div>
        </a>
        
        <a href="<?= BASE_URL ?>/productos-financieros/tasas" class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition">
            <div class="text-center">
                <i class="fas fa-percent text-green-600 text-3xl mb-2"></i>
                <p class="text-sm text-gray-600">Tasas y</p>
                <p class="text-sm text-gray-600">Comisiones</p>
            </div>
        </a>
        
        <a href="<?= BASE_URL ?>/productos-financieros/plazos" class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition">
            <div class="text-center">
                <i class="fas fa-calendar-alt text-purple-600 text-3xl mb-2"></i>
                <p class="text-sm text-gray-600">Plazos y</p>
                <p class="text-sm text-gray-600">Condiciones</p>
            </div>
        </a>
        
        <a href="<?= BASE_URL ?>/productos-financieros/amortizacion" class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition">
            <div class="text-center">
                <i class="fas fa-calculator text-orange-600 text-3xl mb-2"></i>
                <p class="text-sm text-gray-600">Esquemas de</p>
                <p class="text-sm text-gray-600">Amortización</p>
            </div>
        </a>
        
        <a href="<?= BASE_URL ?>/productos-financieros/beneficios" class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition">
            <div class="text-center">
                <i class="fas fa-gift text-red-600 text-3xl mb-2"></i>
                <p class="text-sm text-gray-600">Beneficios y</p>
                <p class="text-sm text-gray-600">Promociones</p>
            </div>
        </a>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Productos</p>
                    <p class="text-2xl font-bold text-gray-800"><?= count($productos ?? []) ?></p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-box-open text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Créditos Colocados</p>
                    <p class="text-2xl font-bold text-gray-800">
                        <?= array_sum(array_column($productos ?? [], 'total_creditos')) ?>
                    </p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Monto Total</p>
                    <p class="text-2xl font-bold text-gray-800">
                        $<?= number_format(array_sum(array_column($productos ?? [], 'monto_total')), 2) ?>
                    </p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-dollar-sign text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de productos -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h2 class="text-lg font-semibold text-gray-700">Catálogo de Productos</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empresa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tasa Interés</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plazo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Créditos</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($productos)): ?>
                <tr>
                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">No hay productos registrados</td>
                </tr>
                <?php else: ?>
                <?php foreach ($productos as $producto): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                        <?= htmlspecialchars($producto['nombre']) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        <?= htmlspecialchars($producto['empresa_nombre']) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                            <?= htmlspecialchars(ucfirst($producto['tipo'])) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <?= number_format($producto['tasa_interes_min'] * 100, 2) ?>% - 
                        <?= number_format($producto['tasa_interes_max'] * 100, 2) ?>%
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <?= $producto['plazo_min_meses'] ?> - <?= $producto['plazo_max_meses'] ?> meses
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        $<?= number_format($producto['monto_min'], 0) ?> - 
                        $<?= number_format($producto['monto_max'], 0) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                            <?= $producto['total_creditos'] ?? 0 ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full 
                            <?= $producto['activo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                            <?= $producto['activo'] ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="<?= BASE_URL ?>/productos-financieros/tasas/<?= $producto['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3" title="Tasas">
                            <i class="fas fa-percent"></i>
                        </a>
                        <a href="<?= BASE_URL ?>/productos-financieros/plazos/<?= $producto['id'] ?>" class="text-green-600 hover:text-green-900" title="Plazos">
                            <i class="fas fa-calendar-alt"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
