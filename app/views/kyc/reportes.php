<!-- Vista de reportes KYC -->
<div class="space-y-6">
    <div class="mb-6">
        <a href="<?= BASE_URL ?>/kyc" class="text-primary-600 hover:text-primary-800">
            <i class="fas fa-arrow-left mr-2"></i>Volver a verificaciones
        </a>
    </div>
    
    <!-- Estadísticas principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Verificaciones</p>
                    <p class="text-3xl font-bold text-gray-800"><?= number_format($stats['totalVerificaciones']) ?></p>
                </div>
                <div class="p-4 bg-blue-100 rounded-full">
                    <i class="fas fa-user-check text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Tasa de Aprobación</p>
                    <p class="text-3xl font-bold text-green-600">
                        <?= $stats['totalVerificaciones'] > 0 ? round(($stats['aprobados'] / $stats['totalVerificaciones']) * 100, 1) : 0 ?>%
                    </p>
                </div>
                <div class="p-4 bg-green-100 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Alto Riesgo</p>
                    <p class="text-3xl font-bold text-red-600"><?= number_format($stats['altoRiesgo']) ?></p>
                </div>
                <div class="p-4 bg-red-100 rounded-full">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Personas PEP</p>
                    <p class="text-3xl font-bold text-purple-600"><?= number_format($stats['pep']) ?></p>
                </div>
                <div class="p-4 bg-purple-100 rounded-full">
                    <i class="fas fa-user-tie text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Gráfica de verificaciones por mes -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-line text-primary-600 mr-2"></i>Verificaciones por Mes
            </h3>
            <canvas id="chartMensual" height="250"></canvas>
        </div>
        
        <!-- Gráfica de distribución por nivel de riesgo -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-pie text-primary-600 mr-2"></i>Distribución por Nivel de Riesgo
            </h3>
            <canvas id="chartRiesgo" height="250"></canvas>
        </div>
    </div>
    
    <!-- Verificaciones próximas a vencer -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-clock text-primary-600 mr-2"></i>Documentos Próximos a Vencer (30 días)
        </h3>
        
        <?php if (empty($proximasVencer)): ?>
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-check-circle text-4xl mb-2"></i>
            <p>No hay documentos próximos a vencer</p>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Socio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Documento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vencimiento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Días</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acción</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($proximasVencer as $v): ?>
                    <?php 
                    $dias = floor((strtotime($v['fecha_vencimiento']) - time()) / 86400);
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                <?= htmlspecialchars($v['nombre'] . ' ' . $v['apellido_paterno']) ?>
                            </div>
                            <div class="text-sm text-gray-500"><?= htmlspecialchars($v['numero_socio']) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= htmlspecialchars($v['tipo_documento']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= date('d/m/Y', strtotime($v['fecha_vencimiento'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full <?= $dias < 0 ? 'bg-red-100 text-red-800' : ($dias <= 7 ? 'bg-yellow-100 text-yellow-800' : 'bg-orange-100 text-orange-800') ?>">
                                <?= $dias < 0 ? 'Vencido hace ' . abs($dias) . ' días' : $dias . ' días' ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <a href="<?= BASE_URL ?>/kyc/ver/<?= $v['id'] ?>" 
                               class="text-primary-600 hover:text-primary-800">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Resumen de estados -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-tasks text-primary-600 mr-2"></i>Resumen por Estado
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                <div class="text-3xl font-bold text-yellow-600"><?= $stats['pendientes'] ?></div>
                <div class="text-sm text-yellow-700">Pendientes</div>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <div class="text-3xl font-bold text-green-600"><?= $stats['aprobados'] ?></div>
                <div class="text-sm text-green-700">Aprobados</div>
            </div>
            <div class="text-center p-4 bg-red-50 rounded-lg">
                <div class="text-3xl font-bold text-red-600"><?= $stats['rechazados'] ?></div>
                <div class="text-sm text-red-700">Rechazados</div>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-3xl font-bold text-gray-600"><?= $stats['altoRiesgo'] ?></div>
                <div class="text-sm text-gray-700">Alto Riesgo</div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos para gráfica mensual
    const datosMensuales = <?= json_encode($verificacionesPorMes) ?>;
    const labels = datosMensuales.map(d => {
        const [year, month] = d.mes.split('-');
        const date = new Date(year, month - 1);
        return date.toLocaleDateString('es-MX', { month: 'short', year: 'numeric' });
    });
    
    // Gráfica de verificaciones por mes
    new Chart(document.getElementById('chartMensual'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Aprobados',
                    data: datosMensuales.map(d => d.aprobados),
                    backgroundColor: '#10B981',
                    borderRadius: 4
                },
                {
                    label: 'Rechazados',
                    data: datosMensuales.map(d => d.rechazados),
                    backgroundColor: '#EF4444',
                    borderRadius: 4
                },
                {
                    label: 'Pendientes',
                    data: datosMensuales.map(d => d.total - d.aprobados - d.rechazados),
                    backgroundColor: '#F59E0B',
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                x: {
                    stacked: true
                },
                y: {
                    stacked: true,
                    beginAtZero: true
                }
            }
        }
    });
    
    // Datos para gráfica de riesgo
    const datosRiesgo = <?= json_encode($distribucionRiesgo) ?>;
    const riesgoLabels = {
        'bajo': 'Bajo',
        'medio': 'Medio',
        'alto': 'Alto'
    };
    const riesgoColors = {
        'bajo': '#10B981',
        'medio': '#F59E0B',
        'alto': '#EF4444'
    };
    
    // Gráfica de distribución por nivel de riesgo
    new Chart(document.getElementById('chartRiesgo'), {
        type: 'doughnut',
        data: {
            labels: datosRiesgo.map(d => riesgoLabels[d.nivel_riesgo] || d.nivel_riesgo),
            datasets: [{
                data: datosRiesgo.map(d => d.total),
                backgroundColor: datosRiesgo.map(d => riesgoColors[d.nivel_riesgo] || '#6B7280'),
                borderWidth: 0
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
});
</script>
