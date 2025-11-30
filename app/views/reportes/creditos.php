<?php
/**
 * Reporte de Créditos
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
        <p class="text-gray-600">Análisis de créditos otorgados y cartera</p>
    </div>
</div>

<!-- Filtros -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form method="GET" action="<?= url('reportes/creditos') ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
        <h3 class="text-gray-500 text-sm font-medium">Créditos Activos</h3>
        <p class="text-3xl font-bold text-gray-800"><?= number_format($resumen['total_activos'] ?? 0) ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-gray-500 text-sm font-medium">Total Otorgado</h3>
        <p class="text-3xl font-bold text-blue-600">$<?= number_format($resumen['total_otorgado'] ?? 0, 2) ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-gray-500 text-sm font-medium">Cartera Actual</h3>
        <p class="text-3xl font-bold text-purple-600">$<?= number_format($resumen['cartera_total'] ?? 0, 2) ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-gray-500 text-sm font-medium">Promedio Crédito</h3>
        <p class="text-3xl font-bold text-green-600">$<?= number_format($resumen['promedio_monto'] ?? 0, 2) ?></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Créditos por Mes -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Créditos Otorgados por Mes</h2>
        <?php if (empty($creditosMes)): ?>
            <p class="text-gray-500 text-center py-8">No hay datos para el período seleccionado</p>
        <?php else: ?>
            <canvas id="chartCreditosMes" height="250"></canvas>
        <?php endif; ?>
    </div>
    
    <!-- Por Tipo de Crédito -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Cartera por Tipo de Crédito</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto Otorgado</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo Actual</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($porTipo as $tipo): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900"><?= htmlspecialchars($tipo['nombre']) ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right"><?= number_format($tipo['cantidad']) ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right">$<?= number_format($tipo['monto_otorgado'], 2) ?></td>
                            <td class="px-4 py-3 text-sm font-medium text-purple-600 text-right">$<?= number_format($tipo['saldo_actual'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if (!empty($creditosMes)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('chartCreditosMes'), {
    type: 'line',
    data: {
        labels: [<?= "'" . implode("','", array_column($creditosMes, 'mes')) . "'" ?>],
        datasets: [
            {
                label: 'Cantidad',
                data: [<?= implode(',', array_column($creditosMes, 'cantidad')) ?>],
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                yAxisID: 'y'
            },
            {
                label: 'Monto ($)',
                data: [<?= implode(',', array_column($creditosMes, 'monto_total')) ?>],
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                yAxisID: 'y1'
            }
        ]
    },
    options: {
        responsive: true,
        interaction: {
            mode: 'index',
            intersect: false
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Cantidad'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                grid: {
                    drawOnChartArea: false
                },
                title: {
                    display: true,
                    text: 'Monto ($)'
                },
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>
<?php endif; ?>
