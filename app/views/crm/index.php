<?php
/**
 * Vista de Informe CRM
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Informe CRM - Customer Relationship Management</h1>
        <p class="text-gray-600">Análisis completo del comportamiento y métricas de clientes</p>
    </div>
    <div class="flex space-x-2">
        <a href="<?= BASE_URL ?>/crm/customerjourney" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
            <i class="fas fa-route mr-2"></i>Customer Journey
        </a>
        <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            <i class="fas fa-print mr-2"></i>Imprimir
        </button>
    </div>
</div>

<!-- Accesos rápidos -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <a href="<?= BASE_URL ?>/crm/metricas" class="bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition">
        <i class="fas fa-chart-bar text-2xl text-blue-600 mb-2"></i>
        <p class="font-medium text-gray-800">Métricas de Clientes</p>
        <p class="text-sm text-gray-500">Ver métricas detalladas</p>
    </a>
    <a href="<?= BASE_URL ?>/crm/segmentos" class="bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition">
        <i class="fas fa-layer-group text-2xl text-purple-600 mb-2"></i>
        <p class="font-medium text-gray-800">Segmentos</p>
        <p class="text-sm text-gray-500">Gestionar segmentos</p>
    </a>
    <a href="<?= BASE_URL ?>/crm/interacciones" class="bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition">
        <i class="fas fa-comments text-2xl text-green-600 mb-2"></i>
        <p class="font-medium text-gray-800">Interacciones</p>
        <p class="text-sm text-gray-500">Historial de contactos</p>
    </a>
    <a href="<?= BASE_URL ?>/crm/customerjourney" class="bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition">
        <i class="fas fa-route text-2xl text-orange-600 mb-2"></i>
        <p class="font-medium text-gray-800">Customer Journey</p>
        <p class="text-sm text-gray-500">Prospectos y vinculación</p>
    </a>
</div>

<!-- KPIs Principales -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-md p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Clientes</p>
                <p class="text-2xl font-bold text-blue-600"><?= number_format($stats['total_clientes']) ?></p>
            </div>
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Clientes Activos</p>
                <p class="text-2xl font-bold text-green-600"><?= number_format($stats['clientes_activos']) ?></p>
            </div>
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">LTV Promedio</p>
                <p class="text-2xl font-bold text-blue-600">$<?= number_format($stats['ltv_promedio'], 2) ?></p>
            </div>
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-dollar-sign"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">En Riesgo</p>
                <p class="text-2xl font-bold text-orange-600"><?= number_format($stats['en_riesgo']) ?></p>
            </div>
            <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Clientes VIP</p>
                <p class="text-2xl font-bold text-yellow-600"><?= number_format($stats['vip']) ?></p>
            </div>
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                <i class="fas fa-crown"></i>
            </div>
        </div>
    </div>
</div>

<!-- Tabs de navegación -->
<div class="bg-white rounded-lg shadow-md mb-6">
    <div class="border-b">
        <nav class="flex -mb-px" id="crmTabs">
            <button class="crm-tab active px-6 py-4 text-sm font-medium border-b-2 border-blue-500 text-blue-600" 
                    data-tab="segmentacion">
                <i class="fas fa-chart-pie mr-2"></i>Segmentación
            </button>
            <button class="crm-tab px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700"
                    data-tab="actividad">
                <i class="fas fa-chart-line mr-2"></i>Actividad
            </button>
            <button class="crm-tab px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700"
                    data-tab="ventas">
                <i class="fas fa-shopping-cart mr-2"></i>Ventas
            </button>
            <button class="crm-tab px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700"
                    data-tab="embudo">
                <i class="fas fa-filter mr-2"></i>Embudo
            </button>
            <button class="crm-tab px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700"
                    data-tab="retencion">
                <i class="fas fa-heart mr-2"></i>Retención
            </button>
        </nav>
    </div>
    
    <!-- Contenido de Segmentación -->
    <div id="tab-segmentacion" class="crm-tab-content p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Distribución por Tipo de Cliente -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-users mr-2"></i>Distribución por Tipo de Cliente
                </h3>
                <?php if (empty($segmentacion)): ?>
                    <p class="text-gray-500 text-center py-4">No hay datos de segmentación</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php 
                        $totalClientes = array_sum(array_column($segmentacion, 'cantidad'));
                        foreach ($segmentacion as $seg): 
                            $porcentaje = $totalClientes > 0 ? ($seg['cantidad'] / $totalClientes) * 100 : 0;
                        ?>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full mr-2" style="background-color: <?= $seg['color'] ?>"></div>
                                    <span class="text-sm text-gray-700"><?= htmlspecialchars($seg['nombre']) ?></span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-sm text-gray-600 mr-4"><?= $seg['cantidad'] ?> clientes</span>
                                    <div class="w-24 bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full" style="width: <?= $porcentaje ?>%; background-color: <?= $seg['color'] ?>"></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Rendimiento de Ventas por Segmento -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-chart-bar mr-2"></i>Rendimiento de Ventas por Segmento
                </h3>
                <?php if (empty($rendimientoPorSegmento)): ?>
                    <p class="text-gray-500 text-center py-4">No hay datos de rendimiento</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($rendimientoPorSegmento as $rend): ?>
                            <div class="border-b pb-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-700"><?= htmlspecialchars($rend['nombre']) ?></span>
                                    <span class="text-sm text-gray-600">$<?= number_format($rend['ingresos_totales'], 2) ?></span>
                                </div>
                                <div class="text-xs text-gray-500">
                                    Promedio: $<?= number_format($rend['promedio'], 2) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Otros tabs (contenido simplificado) -->
    <div id="tab-actividad" class="crm-tab-content p-6 hidden">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-line mr-2"></i>Análisis de Actividad de Clientes
        </h3>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <div class="bg-blue-50 rounded-lg p-4">
                <p class="text-sm text-blue-600">Clientes Nuevos (este mes)</p>
                <p class="text-3xl font-bold text-blue-800"><?= number_format($actividadReciente['clientes_nuevos_mes'] ?? 0) ?></p>
            </div>
            
            <?php foreach ($actividadReciente['por_tipo'] ?? [] as $actividad): ?>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-600"><?= htmlspecialchars($actividad['tipo']) ?> (30 días)</p>
                <p class="text-2xl font-bold text-gray-800"><?= number_format($actividad['cantidad']) ?></p>
                <p class="text-sm text-green-600">$<?= number_format($actividad['total'], 2) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-4">
            <h4 class="font-medium text-gray-700 mb-3">Evolución Diaria (últimos 7 días)</h4>
            <?php if (empty($actividadReciente['evolucion_diaria'])): ?>
                <p class="text-gray-500">Sin datos disponibles</p>
            <?php else: ?>
                <div class="space-y-2">
                    <?php foreach ($actividadReciente['evolucion_diaria'] ?? [] as $dia): ?>
                    <div class="flex justify-between items-center p-2 bg-white rounded">
                        <span class="text-sm text-gray-600"><?= date('d/m', strtotime($dia['dia'])) ?></span>
                        <span class="text-sm font-medium"><?= number_format($dia['transacciones']) ?> transacciones</span>
                        <span class="text-sm text-green-600">$<?= number_format($dia['total'], 2) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div id="tab-ventas" class="crm-tab-content p-6 hidden">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-shopping-cart mr-2"></i>Análisis de Ventas (Créditos)
        </h3>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-medium text-gray-700 mb-3">Top Tipos de Crédito</h4>
                <?php if (empty($analisisVentas['top_tipos_credito'])): ?>
                    <p class="text-gray-500">Sin datos disponibles</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($analisisVentas['top_tipos_credito'] ?? [] as $tipo): ?>
                        <div class="flex justify-between items-center p-2 bg-white rounded">
                            <div>
                                <span class="font-medium"><?= htmlspecialchars($tipo['nombre']) ?></span>
                                <span class="text-xs text-gray-500 ml-2">(<?= number_format($tipo['cantidad']) ?> créditos)</span>
                            </div>
                            <span class="text-green-600 font-medium">$<?= number_format($tipo['total'], 2) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-medium text-gray-700 mb-3">Créditos por Mes (últimos 6 meses)</h4>
                <div class="mb-4 p-3 bg-blue-50 rounded">
                    <p class="text-sm text-blue-600">Ticket Promedio</p>
                    <p class="text-2xl font-bold text-blue-800">$<?= number_format($analisisVentas['ticket_promedio'] ?? 0, 2) ?></p>
                </div>
                <?php if (empty($analisisVentas['creditos_por_mes'])): ?>
                    <p class="text-gray-500">Sin datos disponibles</p>
                <?php else: ?>
                    <div class="space-y-2">
                        <?php foreach ($analisisVentas['creditos_por_mes'] ?? [] as $mes): ?>
                        <div class="flex justify-between items-center p-2 bg-white rounded">
                            <span class="text-sm"><?= $mes['mes'] ?></span>
                            <span class="text-sm"><?= number_format($mes['cantidad']) ?> créditos</span>
                            <span class="text-sm text-green-600">$<?= number_format($mes['total'], 2) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div id="tab-embudo" class="crm-tab-content p-6 hidden">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-filter mr-2"></i>Análisis de Embudo de Conversión
        </h3>
        
        <div class="mb-6 p-4 bg-green-50 rounded-lg">
            <p class="text-sm text-green-600">Tasa de Conversión General</p>
            <p class="text-3xl font-bold text-green-800"><?= $embudoConversion['tasa_conversion'] ?? 0 ?>%</p>
            <p class="text-xs text-green-600">Solicitudes que llegan a formalización o activo</p>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-6">
            <h4 class="font-medium text-gray-700 mb-4">Embudo de Créditos</h4>
            <div class="space-y-4">
                <?php 
                $embudoItems = [
                    ['label' => 'Solicitudes/En Revisión', 'key' => 'solicitudes', 'color' => 'bg-blue-500'],
                    ['label' => 'Autorizados', 'key' => 'autorizados', 'color' => 'bg-indigo-500'],
                    ['label' => 'Formalizados', 'key' => 'formalizados', 'color' => 'bg-purple-500'],
                    ['label' => 'Activos', 'key' => 'activos', 'color' => 'bg-green-500'],
                    ['label' => 'Liquidados', 'key' => 'liquidados', 'color' => 'bg-teal-500'],
                    ['label' => 'Rechazados', 'key' => 'rechazados', 'color' => 'bg-red-500'],
                ];
                $maxVal = max(array_values($embudoConversion['embudo'] ?? [1])) ?: 1;
                foreach ($embudoItems as $item):
                    $val = $embudoConversion['embudo'][$item['key']] ?? 0;
                    $width = ($val / $maxVal) * 100;
                ?>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span><?= $item['label'] ?></span>
                        <span class="font-medium"><?= number_format($val) ?></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div class="<?= $item['color'] ?> h-4 rounded-full" style="width: <?= $width ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <div id="tab-retencion" class="crm-tab-content p-6 hidden">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-heart mr-2"></i>Análisis de Retención
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-green-50 rounded-lg p-4">
                <p class="text-sm text-green-600">Tasa de Retención</p>
                <p class="text-3xl font-bold text-green-800"><?= $analisisRetencion['tasa_retencion'] ?? 0 ?>%</p>
            </div>
            <div class="bg-red-50 rounded-lg p-4">
                <p class="text-sm text-red-600">Bajas este Mes</p>
                <p class="text-3xl font-bold text-red-800"><?= number_format($analisisRetencion['bajas_mes'] ?? 0) ?></p>
            </div>
            <div class="bg-blue-50 rounded-lg p-4">
                <p class="text-sm text-blue-600">Total Socios Activos</p>
                <p class="text-3xl font-bold text-blue-800"><?= number_format($stats['total_clientes'] ?? 0) ?></p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-medium text-gray-700 mb-3">Distribución por Nivel de Riesgo</h4>
                <?php if (empty($analisisRetencion['distribucion_riesgo'])): ?>
                    <p class="text-gray-500">Sin datos disponibles</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php 
                        $riesgoColors = ['bajo' => 'bg-green-500', 'medio' => 'bg-yellow-500', 'alto' => 'bg-red-500'];
                        foreach ($analisisRetencion['distribucion_riesgo'] ?? [] as $riesgo): 
                        ?>
                        <div class="flex items-center justify-between p-2 bg-white rounded">
                            <div class="flex items-center">
                                <span class="w-3 h-3 rounded-full <?= $riesgoColors[$riesgo['nivel_riesgo']] ?? 'bg-gray-500' ?> mr-2"></span>
                                <span class="capitalize"><?= htmlspecialchars($riesgo['nivel_riesgo']) ?></span>
                            </div>
                            <span class="font-medium"><?= number_format($riesgo['cantidad']) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-medium text-gray-700 mb-3">Socios por Antigüedad</h4>
                <?php if (empty($analisisRetencion['por_antiguedad'])): ?>
                    <p class="text-gray-500">Sin datos disponibles</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($analisisRetencion['por_antiguedad'] ?? [] as $antiguedad): ?>
                        <div class="flex justify-between items-center p-2 bg-white rounded">
                            <span><?= htmlspecialchars($antiguedad['antiguedad']) ?></span>
                            <span class="font-medium"><?= number_format($antiguedad['cantidad']) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Clientes en Riesgo -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b bg-red-50">
            <h3 class="text-lg font-semibold text-red-800">
                <i class="fas fa-exclamation-triangle mr-2"></i>Clientes en Riesgo
            </h3>
        </div>
        <div class="p-4">
            <?php if (empty($clientesRiesgo)): ?>
                <p class="text-gray-500 text-center py-4">No hay clientes en riesgo</p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($clientesRiesgo as $cliente): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-800"><?= htmlspecialchars($cliente['nombre']) ?></p>
                                <p class="text-sm text-gray-500"><?= $cliente['dias_sin_actividad'] ?> días sin actividad</p>
                            </div>
                            <a href="<?= url('socios/ver/' . $cliente['id']) ?>" class="text-blue-600 hover:text-blue-800">
                                Ver <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Clientes VIP -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b bg-yellow-50">
            <h3 class="text-lg font-semibold text-yellow-800">
                <i class="fas fa-crown mr-2"></i>Clientes VIP
            </h3>
        </div>
        <div class="p-4">
            <?php if (empty($clientesVip)): ?>
                <p class="text-gray-500 text-center py-4">No hay clientes VIP</p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($clientesVip as $cliente): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-800"><?= htmlspecialchars($cliente['nombre']) ?></p>
                                <p class="text-sm text-gray-500">LTV: $<?= number_format($cliente['ltv'], 2) ?></p>
                            </div>
                            <a href="<?= url('socios/ver/' . $cliente['id']) ?>" class="text-blue-600 hover:text-blue-800">
                                Ver <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Accesos rápidos -->
<div class="bg-white rounded-lg shadow-md p-6 mt-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Acciones Rápidas</h3>
    <div class="flex flex-wrap gap-4">
        <a href="<?= url('crm/segmentos') ?>" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
            <i class="fas fa-layer-group mr-2"></i>Gestionar Segmentos
        </a>
        <a href="<?= url('crm/metricas') ?>" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
            <i class="fas fa-chart-bar mr-2"></i>Ver Métricas
        </a>
        <a href="<?= url('crm/interacciones') ?>" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
            <i class="fas fa-comments mr-2"></i>Interacciones
        </a>
        <a href="<?= url('crm/interaccion') ?>" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>Nueva Interacción
        </a>
    </div>
</div>

<script>
// Tab switching
document.querySelectorAll('.crm-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        // Remove active from all tabs
        document.querySelectorAll('.crm-tab').forEach(t => {
            t.classList.remove('active', 'border-blue-500', 'text-blue-600');
            t.classList.add('border-transparent', 'text-gray-500');
        });
        
        // Add active to clicked tab
        this.classList.add('active', 'border-blue-500', 'text-blue-600');
        this.classList.remove('border-transparent', 'text-gray-500');
        
        // Hide all content
        document.querySelectorAll('.crm-tab-content').forEach(c => c.classList.add('hidden'));
        
        // Show selected content
        const tabId = 'tab-' + this.dataset.tab;
        document.getElementById(tabId).classList.remove('hidden');
    });
});
</script>
