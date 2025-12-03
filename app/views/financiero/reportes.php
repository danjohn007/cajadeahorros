<?php
/**
 * Vista de Reportes Financieros
 * Sistema de Gestión Integral de Caja de Ahorros
 */

$meses = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
        <p class="text-gray-600">Análisis de ingresos y egresos</p>
    </div>
    <a href="<?= url('financiero') ?>" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Volver a Módulo Financiero
    </a>
</div>

<!-- Filtros -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form method="GET" action="<?= url('financiero/reportes') ?>" class="flex flex-wrap gap-4 items-end">
        <div class="w-32">
            <label class="block text-sm font-medium text-gray-700 mb-1">Año</label>
            <select name="anio" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                    <option value="<?= $y ?>" <?= $año == $y ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="w-40">
            <label class="block text-sm font-medium text-gray-700 mb-1">Mes</label>
            <select name="mes" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                <option value="0">Todo el año</option>
                <?php foreach ($meses as $num => $nombre): ?>
                    <option value="<?= $num ?>" <?= $mes == $num ? 'selected' : '' ?>><?= $nombre ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            <i class="fas fa-filter mr-2"></i>Filtrar
        </button>
    </form>
</div>

<!-- Resumen de Totales -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Ingresos</p>
                <p class="text-2xl font-bold text-green-600">$<?= number_format($totales['ingresos'] ?? 0, 2) ?></p>
            </div>
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-arrow-up text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Egresos</p>
                <p class="text-2xl font-bold text-red-600">$<?= number_format($totales['egresos'] ?? 0, 2) ?></p>
            </div>
            <div class="p-3 rounded-full bg-red-100 text-red-600">
                <i class="fas fa-arrow-down text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <?php $balance = ($totales['ingresos'] ?? 0) - ($totales['egresos'] ?? 0); ?>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Balance</p>
                <p class="text-2xl font-bold <?= $balance >= 0 ? 'text-blue-600' : 'text-red-600' ?>">
                    $<?= number_format($balance, 2) ?>
                </p>
            </div>
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-balance-scale text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Resumen por Categoría -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-pie mr-2 text-blue-600"></i>Por Categoría
        </h2>
        
        <?php if (empty($resumenCategoria)): ?>
            <p class="text-gray-500 text-center py-8">No hay datos para el período seleccionado</p>
        <?php else: ?>
            <!-- Ingresos -->
            <h3 class="text-sm font-medium text-green-700 mb-2 mt-4">Ingresos</h3>
            <div class="space-y-2 mb-4">
                <?php 
                $hayIngresos = false;
                foreach ($resumenCategoria as $cat): 
                    if ($cat['tipo'] === 'ingreso'):
                        $hayIngresos = true;
                ?>
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                        <div class="flex items-center">
                            <span class="w-3 h-3 rounded-full mr-2" style="background-color: <?= htmlspecialchars($cat['color'] ?? '#3b82f6') ?>"></span>
                            <span class="text-sm"><?= htmlspecialchars($cat['nombre']) ?></span>
                        </div>
                        <span class="text-sm font-medium text-green-600">$<?= number_format($cat['total'], 2) ?></span>
                    </div>
                <?php 
                    endif;
                endforeach; 
                if (!$hayIngresos):
                ?>
                    <p class="text-gray-400 text-sm">Sin ingresos registrados</p>
                <?php endif; ?>
            </div>
            
            <!-- Egresos -->
            <h3 class="text-sm font-medium text-red-700 mb-2">Egresos</h3>
            <div class="space-y-2">
                <?php 
                $hayEgresos = false;
                foreach ($resumenCategoria as $cat): 
                    if ($cat['tipo'] === 'egreso'):
                        $hayEgresos = true;
                ?>
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                        <div class="flex items-center">
                            <span class="w-3 h-3 rounded-full mr-2" style="background-color: <?= htmlspecialchars($cat['color'] ?? '#ef4444') ?>"></span>
                            <span class="text-sm"><?= htmlspecialchars($cat['nombre']) ?></span>
                        </div>
                        <span class="text-sm font-medium text-red-600">$<?= number_format($cat['total'], 2) ?></span>
                    </div>
                <?php 
                    endif;
                endforeach; 
                if (!$hayEgresos):
                ?>
                    <p class="text-gray-400 text-sm">Sin egresos registrados</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Evolución Mensual -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-line mr-2 text-blue-600"></i>Evolución Mensual <?= $año ?>
        </h2>
        
        <?php if (empty($evolucionMensual)): ?>
            <p class="text-gray-500 text-center py-8">No hay datos para mostrar</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2 text-sm font-medium text-gray-500">Mes</th>
                            <th class="text-right py-2 text-sm font-medium text-gray-500">Ingresos</th>
                            <th class="text-right py-2 text-sm font-medium text-gray-500">Egresos</th>
                            <th class="text-right py-2 text-sm font-medium text-gray-500">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($evolucionMensual as $item): 
                            $mesNum = (int)substr($item['periodo'], 5, 2);
                            $balanceMes = $item['ingresos'] - $item['egresos'];
                        ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-2 text-sm"><?= $meses[$mesNum] ?? $item['periodo'] ?></td>
                                <td class="py-2 text-sm text-right text-green-600">$<?= number_format($item['ingresos'], 2) ?></td>
                                <td class="py-2 text-sm text-right text-red-600">$<?= number_format($item['egresos'], 2) ?></td>
                                <td class="py-2 text-sm text-right font-medium <?= $balanceMes >= 0 ? 'text-blue-600' : 'text-red-600' ?>">
                                    $<?= number_format($balanceMes, 2) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
