<?php
/**
 * Reporte de Cartera
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
        <p class="text-gray-600">Análisis de cartera por antigüedad de vencimiento</p>
    </div>
    <a href="<?= url('cartera/vencida') ?>" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
        <i class="fas fa-exclamation-circle mr-2"></i>Ver Cartera Vencida
    </a>
</div>

<!-- Resumen -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-gray-500 text-sm font-medium">Cartera Total</h3>
        <p class="text-3xl font-bold text-purple-600">$<?= number_format($analisis['cartera_total'] ?? 0, 2) ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-gray-500 text-sm font-medium">Créditos Activos</h3>
        <p class="text-3xl font-bold text-gray-800"><?= number_format($analisis['creditos_activos'] ?? 0) ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-gray-500 text-sm font-medium">Promedio por Crédito</h3>
        <p class="text-3xl font-bold text-blue-600">$<?= number_format($analisis['promedio_saldo'] ?? 0, 2) ?></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Cartera por Antigüedad -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Distribución por Antigüedad</h2>
        <canvas id="chartAntiguedad" height="300"></canvas>
    </div>
    
    <!-- Tabla de Antigüedad -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Detalle por Rango</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rango</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Pagos</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">%</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php 
                    $totalMonto = array_sum(array_column($porAntiguedad, 'monto'));
                    $colors = [
                        'Al corriente' => 'bg-green-100 text-green-800',
                        '1-30 días' => 'bg-yellow-100 text-yellow-800',
                        '31-60 días' => 'bg-orange-100 text-orange-800',
                        '61-90 días' => 'bg-red-100 text-red-800',
                        'Más de 90 días' => 'bg-red-200 text-red-900'
                    ];
                    foreach ($porAntiguedad as $rango): 
                        $porcentaje = $totalMonto > 0 ? ($rango['monto'] / $totalMonto) * 100 : 0;
                    ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-medium rounded-full <?= $colors[$rango['rango']] ?? 'bg-gray-100 text-gray-800' ?>">
                                    <?= htmlspecialchars($rango['rango']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right"><?= number_format($rango['pagos']) ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right">$<?= number_format($rango['monto'], 2) ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right"><?= number_format($porcentaje, 1) ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Total</th>
                        <th class="px-4 py-3 text-right text-sm font-medium text-gray-700"><?= number_format(array_sum(array_column($porAntiguedad, 'pagos'))) ?></th>
                        <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">$<?= number_format($totalMonto, 2) ?></th>
                        <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">100%</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labels = [<?= "'" . implode("','", array_column($porAntiguedad, 'rango')) . "'" ?>];
const data = [<?= implode(',', array_column($porAntiguedad, 'monto')) ?>];

new Chart(document.getElementById('chartAntiguedad'), {
    type: 'doughnut',
    data: {
        labels: labels,
        datasets: [{
            data: data,
            backgroundColor: ['#10B981', '#F59E0B', '#F97316', '#EF4444', '#991B1B']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': $' + context.raw.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>
