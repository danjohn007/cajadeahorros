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
        <p class="text-gray-500">Análisis de actividad de clientes - En desarrollo</p>
    </div>
    <div id="tab-ventas" class="crm-tab-content p-6 hidden">
        <p class="text-gray-500">Análisis de ventas - En desarrollo</p>
    </div>
    <div id="tab-embudo" class="crm-tab-content p-6 hidden">
        <p class="text-gray-500">Análisis de embudo de conversión - En desarrollo</p>
    </div>
    <div id="tab-retencion" class="crm-tab-content p-6 hidden">
        <p class="text-gray-500">Análisis de retención - En desarrollo</p>
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
