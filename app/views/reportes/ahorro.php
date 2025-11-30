<?php
/**
 * Reporte de Ahorro
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
        <p class="text-gray-600">Análisis de movimientos y saldos de ahorro</p>
    </div>
    <a href="<?= url('reportes/exportar/ahorro') ?>" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
        <i class="fas fa-download mr-2"></i>Exportar CSV
    </a>
</div>

<!-- Filtros -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form method="GET" action="<?= url('reportes/ahorro') ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-gray-500 text-sm font-medium">Saldo Total</h3>
        <p class="text-3xl font-bold text-green-600">$<?= number_format($totales['saldo_total'], 2) ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-gray-500 text-sm font-medium">Cuentas Activas</h3>
        <p class="text-3xl font-bold text-gray-800"><?= number_format($totales['cuentas_activas']) ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-gray-500 text-sm font-medium">Promedio por Cuenta</h3>
        <p class="text-3xl font-bold text-blue-600">$<?= number_format($totales['promedio'], 2) ?></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Movimientos por Mes -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Movimientos por Mes</h2>
        <?php if (empty($movimientosMes)): ?>
            <p class="text-gray-500 text-center py-8">No hay datos para el período seleccionado</p>
        <?php else: ?>
            <canvas id="chartMovimientos" height="250"></canvas>
        <?php endif; ?>
    </div>
    
    <!-- Top Ahorradores -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Top 10 Ahorradores</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Socio</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($topAhorradores as $i => $ah): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-500"><?= $i + 1 ?></td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($ah['nombre']) ?></div>
                                <div class="text-sm text-gray-500"><?= htmlspecialchars($ah['numero_socio']) ?></div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right font-medium">
                                $<?= number_format($ah['saldo'], 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if (!empty($movimientosMes)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('chartMovimientos'), {
    type: 'bar',
    data: {
        labels: [<?= "'" . implode("','", array_column($movimientosMes, 'mes')) . "'" ?>],
        datasets: [
            {
                label: 'Aportaciones',
                data: [<?= implode(',', array_column($movimientosMes, 'aportaciones')) ?>],
                backgroundColor: '#10B981'
            },
            {
                label: 'Retiros',
                data: [<?= implode(',', array_column($movimientosMes, 'retiros')) ?>],
                backgroundColor: '#EF4444'
            },
            {
                label: 'Intereses',
                data: [<?= implode(',', array_column($movimientosMes, 'intereses')) ?>],
                backgroundColor: '#3B82F6'
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
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
