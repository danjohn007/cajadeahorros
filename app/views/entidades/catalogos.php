<?php
/**
 * Vista de Catálogos Corporativos
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Catálogos Corporativos</h1>
        <a href="<?= BASE_URL ?>/entidades" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Regresar
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">
                <i class="fas fa-box text-blue-600 mr-2"></i>Productos Financieros
            </h2>
            <div class="space-y-2">
                <?php if (empty($productos)): ?>
                <p class="text-gray-500 text-sm">No hay productos configurados</p>
                <?php else: ?>
                <?php foreach ($productos as $producto): ?>
                <div class="flex justify-between items-center p-2 border-b">
                    <span class="text-sm"><?= htmlspecialchars($producto['nombre']) ?></span>
                    <span class="px-2 py-1 text-xs rounded-full <?= $producto['activo'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                        <?= $producto['activo'] ? 'Activo' : 'Inactivo' ?>
                    </span>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <a href="<?= BASE_URL ?>/productos_financieros" class="mt-4 inline-block text-blue-600 hover:text-blue-900 text-sm">
                <i class="fas fa-arrow-right mr-1"></i>Ver todos los productos
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">
                <i class="fas fa-file-alt text-green-600 mr-2"></i>Políticas de Crédito
            </h2>
            <div class="space-y-2">
                <?php if (empty($politicas)): ?>
                <p class="text-gray-500 text-sm">No hay políticas configuradas</p>
                <?php else: ?>
                <?php foreach ($politicas as $politica): ?>
                <div class="flex justify-between items-center p-2 border-b">
                    <span class="text-sm"><?= htmlspecialchars($politica['nombre']) ?></span>
                    <span class="px-2 py-1 text-xs rounded-full <?= $politica['activo'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                        <?= $politica['activo'] ? 'Activa' : 'Inactiva' ?>
                    </span>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <a href="<?= BASE_URL ?>/entidades/politicas" class="mt-4 inline-block text-blue-600 hover:text-blue-900 text-sm">
                <i class="fas fa-arrow-right mr-1"></i>Ver todas las políticas
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">
                <i class="fas fa-tags text-orange-600 mr-2"></i>Tipos de Crédito
            </h2>
            <p class="text-sm text-gray-600 mb-3">Catálogo de tipos de crédito disponibles en el sistema</p>
            <ul class="space-y-2 text-sm">
                <li class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    <span>Crédito Personal</span>
                </li>
                <li class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    <span>Crédito de Nómina</span>
                </li>
                <li class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    <span>Crédito Hipotecario</span>
                </li>
                <li class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    <span>Crédito Automotriz</span>
                </li>
            </ul>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">
                <i class="fas fa-cogs text-purple-600 mr-2"></i>Configuraciones Generales
            </h2>
            <p class="text-sm text-gray-600 mb-3">Parámetros y configuraciones del sistema</p>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between items-center p-2 border-b">
                    <span>Tasa de Interés Base</span>
                    <span class="font-semibold">12.5%</span>
                </div>
                <div class="flex justify-between items-center p-2 border-b">
                    <span>Tasa de Mora Default</span>
                    <span class="font-semibold">2.5%</span>
                </div>
                <div class="flex justify-between items-center p-2 border-b">
                    <span>Días de Gracia</span>
                    <span class="font-semibold">5 días</span>
                </div>
            </div>
        </div>
    </div>
</div>
