<?php
/**
 * Vista del Módulo Financiero
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
        <p class="text-gray-600">Control de ingresos y egresos</p>
    </div>
    <div class="flex space-x-3">
        <a href="<?= url('financiero/proveedores') ?>" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
            <i class="fas fa-truck mr-2"></i>Proveedores
        </a>
        <a href="<?= url('financiero/categorias') ?>" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
            <i class="fas fa-tags mr-2"></i>Categorías
        </a>
        <a href="<?= url('financiero/reportes') ?>" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
            <i class="fas fa-chart-bar mr-2"></i>Reportes
        </a>
        <a href="<?= url('financiero/transaccion') ?>" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>Nueva Transacción
        </a>
    </div>
</div>

<!-- Resumen Financiero -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Ingresos</p>
                <p class="text-2xl font-bold text-green-600">$<?= number_format($resumen['ingresos'], 2) ?></p>
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
                <p class="text-2xl font-bold text-red-600">$<?= number_format($resumen['egresos'], 2) ?></p>
            </div>
            <div class="p-3 rounded-full bg-red-100 text-red-600">
                <i class="fas fa-arrow-down text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Balance</p>
                <p class="text-2xl font-bold <?= $resumen['balance'] >= 0 ? 'text-blue-600' : 'text-red-600' ?>">
                    $<?= number_format($resumen['balance'], 2) ?>
                </p>
            </div>
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-balance-scale text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Gráficas -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Gráfica 1: Evolución Diaria -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-line mr-2 text-blue-500"></i>Evolución Diaria
        </h3>
        <canvas id="chartEvolucion" height="200"></canvas>
    </div>
    
    <!-- Gráfica 2: Distribución por Categoría -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-pie mr-2 text-purple-500"></i>Distribución por Categoría
        </h3>
        <canvas id="chartDistribucion" height="200"></canvas>
    </div>
    
    <!-- Gráfica 3: Top Egresos -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-bar mr-2 text-red-500"></i>Top 5 Egresos por Categoría
        </h3>
        <canvas id="chartTopEgresos" height="200"></canvas>
    </div>
    
    <!-- Gráfica 4: Comparación Mensual -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-area mr-2 text-green-500"></i>Comparación Mensual
        </h3>
        <canvas id="chartMensual" height="200"></canvas>
    </div>
</div>

<!-- Filtros -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form method="GET" action="<?= url('financiero') ?>" class="flex flex-wrap gap-4 items-end">
        <div class="w-40">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
            <select name="tipo" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                <option value="">Todos</option>
                <option value="ingreso" <?= $tipo === 'ingreso' ? 'selected' : '' ?>>Ingreso</option>
                <option value="egreso" <?= $tipo === 'egreso' ? 'selected' : '' ?>>Egreso</option>
            </select>
        </div>
        <div class="w-48">
            <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
            <select name="categoria" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                <option value="">Todas</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $categoriaFilter == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="w-40">
            <label class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
            <input type="date" name="fecha_inicio" value="<?= $fechaInicio ?>" 
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
        </div>
        <div class="w-40">
            <label class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
            <input type="date" name="fecha_fin" value="<?= $fechaFin ?>" 
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
            <i class="fas fa-filter mr-2"></i>Filtrar
        </button>
    </form>
</div>

<!-- Tabla de transacciones -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Concepto</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if (empty($transacciones)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay transacciones registradas</td>
                </tr>
            <?php else: ?>
                <?php foreach ($transacciones as $t): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= date('d/m/Y', strtotime($t['fecha'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $t['tipo'] === 'ingreso' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                <?= ucfirst($t['tipo']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($t['categoria_nombre']): ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" 
                                      style="background-color: <?= $t['categoria_color'] ?>20; color: <?= $t['categoria_color'] ?>">
                                    <?= htmlspecialchars($t['categoria_nombre']) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-gray-400">Sin categoría</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900"><?= htmlspecialchars($t['concepto']) ?></div>
                            <?php if ($t['referencia']): ?>
                                <div class="text-xs text-gray-500">Ref: <?= htmlspecialchars($t['referencia']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium <?= $t['tipo'] === 'ingreso' ? 'text-green-600' : 'text-red-600' ?>">
                            <?= $t['tipo'] === 'ingreso' ? '+' : '-' ?>$<?= number_format($t['monto'], 2) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="<?= url('financiero/transaccion/' . $t['id']) ?>" class="text-blue-600 hover:text-blue-900">Editar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <?php if ($totalPages > 1): ?>
        <div class="px-6 py-4 bg-gray-50 border-t flex justify-between items-center">
            <span class="text-sm text-gray-700">
                Página <?= $page ?> de <?= $totalPages ?> (<?= $total ?> registros)
            </span>
            <div class="flex space-x-2">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>&tipo=<?= $tipo ?>&categoria=<?= $categoriaFilter ?>&fecha_inicio=<?= $fechaInicio ?>&fecha_fin=<?= $fechaFin ?>" 
                       class="px-3 py-1 border rounded text-sm hover:bg-gray-100">Anterior</a>
                <?php endif; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>&tipo=<?= $tipo ?>&categoria=<?= $categoriaFilter ?>&fecha_inicio=<?= $fechaInicio ?>&fecha_fin=<?= $fechaFin ?>" 
                       class="px-3 py-1 border rounded text-sm hover:bg-gray-100">Siguiente</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Chart data from PHP (properly encoded for JavaScript)
const chartData = <?= json_encode($chartData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

// Chart 1: Daily Evolution
const ctxEvolucion = document.getElementById('chartEvolucion').getContext('2d');
new Chart(ctxEvolucion, {
    type: 'line',
    data: {
        labels: chartData.evolucion_diaria.map(d => d.fecha),
        datasets: [
            {
                label: 'Ingresos',
                data: chartData.evolucion_diaria.map(d => parseFloat(d.ingresos)),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.3
            },
            {
                label: 'Egresos',
                data: chartData.evolucion_diaria.map(d => parseFloat(d.egresos)),
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                fill: true,
                tension: 0.3
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' }
        },
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

// Chart 2: Category Distribution (Pie)
const ctxDistribucion = document.getElementById('chartDistribucion').getContext('2d');
const categoriaData = chartData.distribucion_categorias.filter(d => d.nombre);
new Chart(ctxDistribucion, {
    type: 'doughnut',
    data: {
        labels: categoriaData.map(d => d.nombre + ' (' + d.tipo + ')'),
        datasets: [{
            data: categoriaData.map(d => parseFloat(d.total)),
            backgroundColor: categoriaData.map(d => d.color || '#6b7280'),
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'right', labels: { boxWidth: 12 } }
        }
    }
});

// Chart 3: Top Expenses (Bar)
const ctxTopEgresos = document.getElementById('chartTopEgresos').getContext('2d');
new Chart(ctxTopEgresos, {
    type: 'bar',
    data: {
        labels: chartData.top_egresos.map(d => d.nombre),
        datasets: [{
            label: 'Egresos',
            data: chartData.top_egresos.map(d => parseFloat(d.total)),
            backgroundColor: chartData.top_egresos.map(d => d.color || '#ef4444'),
            borderRadius: 4
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: {
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

// Chart 4: Monthly Comparison (Area)
const ctxMensual = document.getElementById('chartMensual').getContext('2d');
new Chart(ctxMensual, {
    type: 'bar',
    data: {
        labels: chartData.comparacion_mensual.map(d => {
            const [year, month] = d.mes.split('-');
            return new Date(year, month - 1).toLocaleDateString('es-MX', { month: 'short', year: '2-digit' });
        }),
        datasets: [
            {
                label: 'Ingresos',
                data: chartData.comparacion_mensual.map(d => parseFloat(d.ingresos)),
                backgroundColor: '#10b981'
            },
            {
                label: 'Egresos',
                data: chartData.comparacion_mensual.map(d => parseFloat(d.egresos)),
                backgroundColor: '#ef4444'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' }
        },
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
