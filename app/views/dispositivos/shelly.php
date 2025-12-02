<?php
/**
 * Vista de Dispositivos Shelly Cloud
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6">
    <a href="<?= url('dispositivos') ?>" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Volver a Dispositivos
    </a>
    <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
    <p class="text-gray-600">Controles inteligentes y medidores de energía</p>
</div>

<!-- Lista de dispositivos Shelly -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (empty($dispositivos)): ?>
        <div class="col-span-full bg-white rounded-lg shadow-md p-8 text-center">
            <i class="fas fa-bolt text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 mb-4">No hay dispositivos Shelly registrados</p>
            <a href="<?= url('dispositivos/crear') ?>" class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                <i class="fas fa-plus mr-2"></i>Agregar Dispositivo Shelly
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($dispositivos as $disp): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-4">
                    <div class="flex items-center justify-between text-white">
                        <div class="flex items-center">
                            <i class="fas fa-bolt text-2xl mr-3"></i>
                            <div>
                                <h3 class="font-bold"><?= htmlspecialchars($disp['nombre']) ?></h3>
                                <p class="text-orange-100 text-sm"><?= htmlspecialchars($disp['modelo'] ?: 'Shelly') ?></p>
                            </div>
                        </div>
                        <?php 
                        $estadoClass = match($disp['estado_actual'] ?? 'unknown') {
                            'on' => 'bg-green-400',
                            'off' => 'bg-gray-400',
                            default => 'bg-yellow-400'
                        };
                        ?>
                        <span class="w-3 h-3 rounded-full <?= $estadoClass ?>"></span>
                    </div>
                </div>
                
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500">Estado</p>
                            <p class="text-lg font-bold text-gray-800">
                                <?= ucfirst($disp['estado_actual'] ?? 'Desconocido') ?>
                            </p>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500">Potencia</p>
                            <p class="text-lg font-bold text-orange-600">
                                <?= number_format($disp['potencia_actual'] ?? 0, 1) ?> W
                            </p>
                        </div>
                    </div>
                    
                    <?php if ($disp['ubicacion']): ?>
                        <p class="text-sm text-gray-600 mb-2">
                            <i class="fas fa-map-marker-alt mr-1"></i><?= htmlspecialchars($disp['ubicacion']) ?>
                        </p>
                    <?php endif; ?>
                    
                    <?php if ($disp['ip_address']): ?>
                        <p class="text-sm text-gray-600 mb-4">
                            <i class="fas fa-network-wired mr-1"></i><?= htmlspecialchars($disp['ip_address']) ?>
                        </p>
                    <?php endif; ?>
                    
                    <div class="flex space-x-2">
                        <a href="<?= url('dispositivos/ver/' . $disp['dispositivo_id']) ?>" 
                           class="flex-1 text-center px-3 py-2 border border-gray-300 rounded-md text-sm hover:bg-gray-50">
                            Ver Detalles
                        </a>
                        <a href="<?= url('dispositivos/shellyConfig/' . $disp['dispositivo_id']) ?>" 
                           class="flex-1 text-center px-3 py-2 bg-orange-600 text-white rounded-md text-sm hover:bg-orange-700">
                            <i class="fas fa-cog mr-1"></i>Configurar
                        </a>
                    </div>
                </div>
                
                <?php if ($disp['ultima_actualizacion']): ?>
                    <div class="px-4 py-2 bg-gray-50 text-xs text-gray-500 border-t">
                        Última actualización: <?= date('d/m/Y H:i', strtotime($disp['ultima_actualizacion'])) ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Información sobre Shelly Cloud -->
<div class="mt-6 bg-orange-50 rounded-lg p-6">
    <h3 class="font-semibold text-orange-800 mb-2">
        <i class="fas fa-info-circle mr-2"></i>Acerca de Shelly Cloud
    </h3>
    <p class="text-orange-700 text-sm mb-4">
        Los dispositivos Shelly son controles inteligentes que permiten automatizar y monitorear el consumo eléctrico.
    </p>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
        <div class="bg-white rounded-lg p-4">
            <i class="fas fa-power-off text-orange-600 text-xl mb-2"></i>
            <h4 class="font-medium text-gray-800">Control Remoto</h4>
            <p class="text-gray-600">Enciende y apaga dispositivos desde cualquier lugar.</p>
        </div>
        <div class="bg-white rounded-lg p-4">
            <i class="fas fa-chart-line text-orange-600 text-xl mb-2"></i>
            <h4 class="font-medium text-gray-800">Monitoreo de Energía</h4>
            <p class="text-gray-600">Mide el consumo eléctrico en tiempo real.</p>
        </div>
        <div class="bg-white rounded-lg p-4">
            <i class="fas fa-clock text-orange-600 text-xl mb-2"></i>
            <h4 class="font-medium text-gray-800">Programación</h4>
            <p class="text-gray-600">Programa horarios de encendido y apagado.</p>
        </div>
    </div>
</div>
