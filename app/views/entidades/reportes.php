<?php
/**
 * Vista de Reportes de Estructura Organizacional
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Reportes de Estructura Organizacional</h1>
        <a href="<?= BASE_URL ?>/entidades" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Regresar
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">
                <i class="fas fa-building text-blue-600 mr-2"></i>Resumen por Empresa
            </h2>
            <div class="space-y-3">
                <?php if (empty($resumenEmpresas)): ?>
                <p class="text-gray-500 text-sm">No hay datos disponibles</p>
                <?php else: ?>
                <?php foreach ($resumenEmpresas as $empresa): ?>
                <div class="border-b pb-3">
                    <div class="flex justify-between items-start mb-2">
                        <span class="font-semibold text-gray-800"><?= htmlspecialchars($empresa['empresa_nombre'] ?? 'N/A') ?></span>
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                            <?= $empresa['total_unidades'] ?? 0 ?> unidades
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-sm text-gray-600">
                        <div>
                            <i class="fas fa-users text-gray-400 mr-1"></i>
                            Promotores: <?= $empresa['total_promotores'] ?? 0 ?>
                        </div>
                        <div>
                            <i class="fas fa-dollar-sign text-gray-400 mr-1"></i>
                            Créditos: <?= $empresa['total_creditos'] ?? 0 ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">
                <i class="fas fa-sitemap text-green-600 mr-2"></i>Resumen por Unidad
            </h2>
            <div class="space-y-3">
                <?php if (empty($resumenUnidades)): ?>
                <p class="text-gray-500 text-sm">No hay datos disponibles</p>
                <?php else: ?>
                <?php foreach ($resumenUnidades as $unidad): ?>
                <div class="border-b pb-3">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <span class="font-semibold text-gray-800"><?= htmlspecialchars($unidad['unidad_nombre'] ?? 'N/A') ?></span>
                            <div class="text-xs text-gray-500"><?= htmlspecialchars($unidad['empresa_nombre'] ?? '') ?></div>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                            <?= $unidad['total_promotores'] ?? 0 ?> promotores
                        </span>
                    </div>
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-file-invoice-dollar text-gray-400 mr-1"></i>
                        Créditos Activos: <?= $unidad['total_creditos'] ?? 0 ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Opciones de Reporte</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <button class="border-2 border-blue-500 text-blue-600 hover:bg-blue-50 px-4 py-3 rounded-lg">
                <i class="fas fa-file-pdf text-xl mb-2"></i>
                <div class="text-sm font-semibold">Exportar a PDF</div>
            </button>
            <button class="border-2 border-green-500 text-green-600 hover:bg-green-50 px-4 py-3 rounded-lg">
                <i class="fas fa-file-excel text-xl mb-2"></i>
                <div class="text-sm font-semibold">Exportar a Excel</div>
            </button>
            <button class="border-2 border-purple-500 text-purple-600 hover:bg-purple-50 px-4 py-3 rounded-lg">
                <i class="fas fa-chart-bar text-xl mb-2"></i>
                <div class="text-sm font-semibold">Ver Gráficos</div>
            </button>
        </div>
    </div>
</div>
