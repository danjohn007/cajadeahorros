<?php
/**
 * Vista de Reportes y Tableros
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
    <p class="text-gray-600">Visualización de indicadores clave y acceso a reportes detallados</p>
</div>

<!-- Tablero Ejecutivo -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Socios -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-users text-2xl"></i>
            </div>
            <a href="<?= url('reportes/socios') ?>" class="text-blue-600 hover:text-blue-800 text-sm">
                Ver detalle <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <h3 class="text-gray-500 text-sm font-medium">Total Socios Activos</h3>
        <p class="text-3xl font-bold text-gray-800"><?= number_format($indicadores['socios']['total']) ?></p>
        <p class="text-sm text-green-600 mt-1">
            <i class="fas fa-plus-circle mr-1"></i>
            <?= number_format($indicadores['socios']['nuevos_mes']) ?> nuevos este mes
        </p>
    </div>
    
    <!-- Ahorro -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-piggy-bank text-2xl"></i>
            </div>
            <a href="<?= url('reportes/ahorro') ?>" class="text-blue-600 hover:text-blue-800 text-sm">
                Ver detalle <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <h3 class="text-gray-500 text-sm font-medium">Saldo Total Ahorro</h3>
        <p class="text-3xl font-bold text-gray-800">$<?= number_format($indicadores['ahorro']['saldo_total'], 2) ?></p>
        <p class="text-sm text-gray-600 mt-1">
            <i class="fas fa-chart-line mr-1"></i>
            $<?= number_format($indicadores['ahorro']['aportaciones_mes'], 2) ?> aportaciones mes
        </p>
    </div>
    
    <!-- Cartera Total -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <i class="fas fa-hand-holding-usd text-2xl"></i>
            </div>
            <a href="<?= url('reportes/creditos') ?>" class="text-blue-600 hover:text-blue-800 text-sm">
                Ver detalle <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <h3 class="text-gray-500 text-sm font-medium">Cartera Total</h3>
        <p class="text-3xl font-bold text-gray-800">$<?= number_format($indicadores['creditos']['cartera_total'], 2) ?></p>
        <p class="text-sm text-gray-600 mt-1">
            <i class="fas fa-file-invoice-dollar mr-1"></i>
            <?= number_format($indicadores['creditos']['creditos_activos']) ?> créditos activos
        </p>
    </div>
    
    <!-- Cartera Vencida -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 rounded-full bg-red-100 text-red-600">
                <i class="fas fa-exclamation-triangle text-2xl"></i>
            </div>
            <a href="<?= url('reportes/cartera') ?>" class="text-blue-600 hover:text-blue-800 text-sm">
                Ver detalle <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <h3 class="text-gray-500 text-sm font-medium">Cartera Vencida</h3>
        <p class="text-3xl font-bold text-red-600">$<?= number_format($indicadores['cartera_vencida']['monto'], 2) ?></p>
        <p class="text-sm text-red-600 mt-1">
            <i class="fas fa-user-times mr-1"></i>
            <?= number_format($indicadores['cartera_vencida']['socios_mora']) ?> socios en mora
        </p>
    </div>
</div>

<!-- Accesos Rápidos a Reportes -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Reportes Disponibles -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-bar mr-2 text-blue-600"></i>Reportes Disponibles
        </h2>
        <div class="space-y-3">
            <a href="<?= url('reportes/socios') ?>" class="flex items-center p-3 rounded-lg hover:bg-gray-50 border border-gray-200 transition-colors">
                <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-users"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-medium text-gray-800">Reporte de Socios</h3>
                    <p class="text-sm text-gray-500">Altas, bajas y distribución por unidad</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </a>
            
            <a href="<?= url('reportes/ahorro') ?>" class="flex items-center p-3 rounded-lg hover:bg-gray-50 border border-gray-200 transition-colors">
                <div class="p-2 rounded-full bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-piggy-bank"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-medium text-gray-800">Reporte de Ahorro</h3>
                    <p class="text-sm text-gray-500">Movimientos, saldos y top ahorradores</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </a>
            
            <a href="<?= url('reportes/creditos') ?>" class="flex items-center p-3 rounded-lg hover:bg-gray-50 border border-gray-200 transition-colors">
                <div class="p-2 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-medium text-gray-800">Reporte de Créditos</h3>
                    <p class="text-sm text-gray-500">Créditos otorgados y cartera por tipo</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </a>
            
            <a href="<?= url('reportes/cartera') ?>" class="flex items-center p-3 rounded-lg hover:bg-gray-50 border border-gray-200 transition-colors">
                <div class="p-2 rounded-full bg-red-100 text-red-600 mr-4">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-medium text-gray-800">Reporte de Cartera</h3>
                    <p class="text-sm text-gray-500">Análisis de cartera por antigüedad</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </a>
            
            <a href="<?= url('reportes/nomina') ?>" class="flex items-center p-3 rounded-lg hover:bg-gray-50 border border-gray-200 transition-colors">
                <div class="p-2 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-medium text-gray-800">Reporte de Nómina</h3>
                    <p class="text-sm text-gray-500">Historial de procesamiento de nóminas</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </a>
        </div>
    </div>
    
    <!-- Exportaciones -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-download mr-2 text-green-600"></i>Exportar Datos
        </h2>
        <p class="text-gray-600 mb-4">Descarga reportes en formato CSV para análisis externo</p>
        
        <div class="space-y-3">
            <a href="<?= url('reportes/exportar/socios') ?>" class="flex items-center p-3 rounded-lg hover:bg-green-50 border border-green-200 transition-colors">
                <div class="p-2 rounded-full bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-file-csv"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-medium text-gray-800">Exportar Socios</h3>
                    <p class="text-sm text-gray-500">Padrón completo de socios</p>
                </div>
                <i class="fas fa-download text-green-600"></i>
            </a>
            
            <a href="<?= url('reportes/exportar/ahorro') ?>" class="flex items-center p-3 rounded-lg hover:bg-green-50 border border-green-200 transition-colors">
                <div class="p-2 rounded-full bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-file-csv"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-medium text-gray-800">Exportar Cuentas de Ahorro</h3>
                    <p class="text-sm text-gray-500">Saldos de todas las cuentas</p>
                </div>
                <i class="fas fa-download text-green-600"></i>
            </a>
        </div>
        
        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
            <p class="text-sm text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                Los archivos CSV pueden abrirse en Excel o cualquier hoja de cálculo
            </p>
        </div>
    </div>
</div>

<!-- Gráficas -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Distribución de Cartera</h2>
        <canvas id="chartCartera" height="250"></canvas>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Ahorro vs Créditos</h2>
        <canvas id="chartComparativo" height="250"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfica de distribución de cartera
new Chart(document.getElementById('chartCartera'), {
    type: 'doughnut',
    data: {
        labels: ['Cartera Vigente', 'Cartera Vencida'],
        datasets: [{
            data: [
                <?= $indicadores['creditos']['cartera_total'] - $indicadores['cartera_vencida']['monto'] ?>,
                <?= $indicadores['cartera_vencida']['monto'] ?>
            ],
            backgroundColor: ['#10B981', '#EF4444']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Gráfica comparativa
new Chart(document.getElementById('chartComparativo'), {
    type: 'bar',
    data: {
        labels: ['Ahorro Total', 'Cartera Créditos'],
        datasets: [{
            label: 'Monto en MXN',
            data: [
                <?= $indicadores['ahorro']['saldo_total'] ?>,
                <?= $indicadores['creditos']['cartera_total'] ?>
            ],
            backgroundColor: ['#10B981', '#8B5CF6']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
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
