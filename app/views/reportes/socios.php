<?php
/**
 * Reporte de Socios
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6">
    <a href="<?= url('reportes') ?>" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Volver a Reportes
    </a>
</div>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
        <p class="text-gray-600">Análisis de socios registrados en el sistema</p>
    </div>
    <a href="<?= url('reportes/exportar/socios') ?>" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
        <i class="fas fa-download mr-2"></i>Exportar CSV
    </a>
</div>

<!-- Filtros -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form method="GET" action="<?= url('reportes/socios') ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
            <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($fechaInicio) ?>" 
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
            <input type="date" name="fecha_fin" value="<?= htmlspecialchars($fechaFin) ?>" 
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div class="md:col-span-2 flex items-end">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                <i class="fas fa-search mr-2"></i>Aplicar Filtros
            </button>
        </div>
    </form>
</div>

<!-- Resumen -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-gray-500 text-sm font-medium">Total Socios</h3>
        <p class="text-3xl font-bold text-gray-800"><?= number_format($totales['total']) ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-gray-500 text-sm font-medium">Activos</h3>
        <p class="text-3xl font-bold text-green-600"><?= number_format($totales['activos']) ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-gray-500 text-sm font-medium">Inactivos</h3>
        <p class="text-3xl font-bold text-yellow-600"><?= number_format($totales['inactivos']) ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-gray-500 text-sm font-medium">Bajas</h3>
        <p class="text-3xl font-bold text-red-600"><?= number_format($totales['bajas']) ?></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Altas y Bajas por Mes -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Altas y Bajas por Mes</h2>
        <?php if (empty($altasBajas)): ?>
            <p class="text-gray-500 text-center py-8">No hay datos para el período seleccionado</p>
        <?php else: ?>
            <canvas id="chartAltasBajas" height="250"></canvas>
        <?php endif; ?>
    </div>
    
    <!-- Por Unidad de Trabajo -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Distribución por Unidad de Trabajo</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unidad</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Socios</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo Ahorro</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($porUnidad as $unidad): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900"><?= htmlspecialchars($unidad['unidad'] ?? 'Sin asignar') ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right"><?= number_format($unidad['total']) ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right">$<?= number_format($unidad['saldo_total'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if (!empty($altasBajas)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('chartAltasBajas'), {
    type: 'bar',
    data: {
        labels: [<?= "'" . implode("','", array_column($altasBajas, 'mes')) . "'" ?>],
        datasets: [
            {
                label: 'Altas',
                data: [<?= implode(',', array_column($altasBajas, 'altas')) ?>],
                backgroundColor: '#10B981'
            },
            {
                label: 'Bajas',
                data: [<?= implode(',', array_column($altasBajas, 'bajas')) ?>],
                backgroundColor: '#EF4444'
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
<?php endif; ?>
