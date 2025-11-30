<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Socios -->
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Socios</p>
                <p class="text-2xl font-bold text-gray-800"><?= number_format($stats['totalSocios']) ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-users text-blue-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4">
            <a href="<?= BASE_URL ?>/socios" class="text-sm text-blue-600 hover:text-blue-800">
                Ver todos <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
    
    <!-- Saldo Ahorro -->
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Saldo Total Ahorro</p>
                <p class="text-2xl font-bold text-gray-800">$<?= number_format($stats['saldoAhorro'], 2) ?></p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-wallet text-green-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4">
            <a href="<?= BASE_URL ?>/ahorro" class="text-sm text-green-600 hover:text-green-800">
                Ver detalle <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
    
    <!-- Cartera Créditos -->
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Cartera de Créditos</p>
                <p class="text-2xl font-bold text-gray-800">$<?= number_format($stats['carteraCreditos'], 2) ?></p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-hand-holding-usd text-purple-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center justify-between">
            <span class="text-sm text-gray-500"><?= $stats['creditosActivos'] ?> activos</span>
            <a href="<?= BASE_URL ?>/creditos" class="text-sm text-purple-600 hover:text-purple-800">
                Ver <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
    
    <!-- Cartera Vencida -->
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Cartera Vencida</p>
                <p class="text-2xl font-bold text-gray-800">$<?= number_format($stats['carteraVencida'], 2) ?></p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center justify-between">
            <span class="text-sm text-red-600"><?= $stats['porcentajeVencida'] ?>% del total</span>
            <a href="<?= BASE_URL ?>/cartera/vencida" class="text-sm text-red-600 hover:text-red-800">
                Ver <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
</div>

<!-- Quick Actions & Pending -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Acciones Rápidas -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-bolt text-yellow-500 mr-2"></i> Acciones Rápidas
        </h3>
        <div class="space-y-3">
            <a href="<?= BASE_URL ?>/socios/crear" 
               class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                <i class="fas fa-user-plus text-blue-600 w-8"></i>
                <span class="text-gray-700">Nuevo Socio</span>
            </a>
            <a href="<?= BASE_URL ?>/ahorro/movimiento" 
               class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition">
                <i class="fas fa-piggy-bank text-green-600 w-8"></i>
                <span class="text-gray-700">Registrar Ahorro</span>
            </a>
            <a href="<?= BASE_URL ?>/creditos/solicitud" 
               class="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                <i class="fas fa-file-invoice-dollar text-purple-600 w-8"></i>
                <span class="text-gray-700">Nueva Solicitud de Crédito</span>
            </a>
            <a href="<?= BASE_URL ?>/nomina/cargar" 
               class="flex items-center p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition">
                <i class="fas fa-file-upload text-orange-600 w-8"></i>
                <span class="text-gray-700">Cargar Nómina</span>
            </a>
        </div>
    </div>
    
    <!-- Solicitudes Pendientes -->
    <div class="bg-white rounded-xl shadow-sm p-6 lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-clock text-orange-500 mr-2"></i> Solicitudes Pendientes
            </h3>
            <?php if ($stats['solicitudesPendientes'] > 0): ?>
            <span class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full text-sm font-medium">
                <?= $stats['solicitudesPendientes'] ?> pendientes
            </span>
            <?php endif; ?>
        </div>
        
        <?php if (empty($creditosPendientes)): ?>
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-check-circle text-4xl text-green-400 mb-3"></i>
            <p>No hay solicitudes pendientes</p>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-sm text-gray-500 border-b">
                        <th class="pb-3 font-medium">Socio</th>
                        <th class="pb-3 font-medium">Tipo</th>
                        <th class="pb-3 font-medium">Monto</th>
                        <th class="pb-3 font-medium">Fecha</th>
                        <th class="pb-3 font-medium">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($creditosPendientes as $credito): ?>
                    <tr class="text-sm">
                        <td class="py-3"><?= htmlspecialchars($credito['nombre_socio']) ?></td>
                        <td class="py-3"><?= htmlspecialchars($credito['tipo_credito']) ?></td>
                        <td class="py-3 font-medium">$<?= number_format($credito['monto_solicitado'], 2) ?></td>
                        <td class="py-3 text-gray-500"><?= date('d/m/Y', strtotime($credito['fecha_solicitud'])) ?></td>
                        <td class="py-3">
                            <a href="<?= BASE_URL ?>/creditos/ver/<?= $credito['id'] ?>" 
                               class="text-blue-600 hover:text-blue-800">
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
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Movimientos de Ahorro -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-line text-blue-500 mr-2"></i> Movimientos de Ahorro
        </h3>
        <canvas id="chartMovimientos" height="250"></canvas>
    </div>
    
    <!-- Distribución de Créditos -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-pie text-purple-500 mr-2"></i> Distribución de Créditos
        </h3>
        <canvas id="chartCreditos" height="250"></canvas>
    </div>
</div>

<!-- Recent Activities -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Últimos Movimientos -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-history text-gray-500 mr-2"></i> Últimos Movimientos
            </h3>
            <a href="<?= BASE_URL ?>/ahorro" class="text-sm text-blue-600 hover:text-blue-800">
                Ver todos
            </a>
        </div>
        
        <div class="space-y-4">
            <?php foreach (array_slice($ultimosMovimientos, 0, 5) as $mov): ?>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center <?= $mov['tipo'] === 'aportacion' ? 'bg-green-100' : ($mov['tipo'] === 'retiro' ? 'bg-red-100' : 'bg-blue-100') ?>">
                        <i class="fas <?= $mov['tipo'] === 'aportacion' ? 'fa-arrow-down text-green-600' : ($mov['tipo'] === 'retiro' ? 'fa-arrow-up text-red-600' : 'fa-percentage text-blue-600') ?>"></i>
                    </div>
                    <div class="ml-3">
                        <p class="font-medium text-gray-800"><?= htmlspecialchars($mov['nombre_socio']) ?></p>
                        <p class="text-sm text-gray-500"><?= ucfirst($mov['tipo']) ?> - <?= date('d/m/Y H:i', strtotime($mov['fecha'])) ?></p>
                    </div>
                </div>
                <span class="font-semibold <?= $mov['tipo'] === 'aportacion' ? 'text-green-600' : ($mov['tipo'] === 'retiro' ? 'text-red-600' : 'text-blue-600') ?>">
                    <?= $mov['tipo'] === 'retiro' ? '-' : '+' ?>$<?= number_format($mov['monto'], 2) ?>
                </span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Socios Recientes -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-user-plus text-green-500 mr-2"></i> Socios Recientes
            </h3>
            <a href="<?= BASE_URL ?>/socios" class="text-sm text-blue-600 hover:text-blue-800">
                Ver todos
            </a>
        </div>
        
        <div class="space-y-4">
            <?php foreach ($sociosRecientes as $socio): ?>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-blue-600 font-semibold"><?= strtoupper(substr($socio['nombre'], 0, 1) . substr($socio['apellido_paterno'], 0, 1)) ?></span>
                    </div>
                    <div class="ml-3">
                        <p class="font-medium text-gray-800"><?= htmlspecialchars($socio['nombre'] . ' ' . $socio['apellido_paterno']) ?></p>
                        <p class="text-sm text-gray-500"><?= htmlspecialchars($socio['unidad_trabajo'] ?? 'Sin unidad') ?></p>
                    </div>
                </div>
                <a href="<?= BASE_URL ?>/socios/ver/<?= $socio['id'] ?>" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-eye"></i>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos para gráfica de movimientos
    const movimientosData = <?= json_encode($datosGraficas['movimientosMes']) ?>;
    const meses = movimientosData.map(m => {
        const [year, month] = m.mes.split('-');
        const date = new Date(year, month - 1);
        return date.toLocaleDateString('es-MX', { month: 'short', year: '2-digit' });
    });
    
    new Chart(document.getElementById('chartMovimientos'), {
        type: 'line',
        data: {
            labels: meses,
            datasets: [{
                label: 'Aportaciones',
                data: movimientosData.map(m => m.aportaciones),
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                fill: true,
                tension: 0.4
            }, {
                label: 'Retiros',
                data: movimientosData.map(m => m.retiros),
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => '$' + value.toLocaleString()
                    }
                }
            }
        }
    });
    
    // Datos para gráfica de créditos
    const creditosData = <?= json_encode($datosGraficas['creditosPorTipo']) ?>;
    
    new Chart(document.getElementById('chartCreditos'), {
        type: 'doughnut',
        data: {
            labels: creditosData.map(c => c.nombre),
            datasets: [{
                data: creditosData.map(c => c.monto),
                backgroundColor: [
                    '#3b82f6',
                    '#22c55e',
                    '#a855f7',
                    '#f59e0b',
                    '#ef4444'
                ],
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
