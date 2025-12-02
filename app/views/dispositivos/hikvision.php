<?php
/**
 * Vista de Dispositivos HikVision
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6">
    <a href="<?= url('dispositivos') ?>" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Volver a Dispositivos
    </a>
    <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
    <p class="text-gray-600">Cámaras y sistemas de videovigilancia</p>
</div>

<!-- Lista de dispositivos HikVision -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (empty($dispositivos)): ?>
        <div class="col-span-full bg-white rounded-lg shadow-md p-8 text-center">
            <i class="fas fa-video text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 mb-4">No hay dispositivos HikVision registrados</p>
            <a href="<?= url('dispositivos/crear') ?>" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                <i class="fas fa-plus mr-2"></i>Agregar Dispositivo HikVision
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($dispositivos as $disp): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-4">
                    <div class="flex items-center justify-between text-white">
                        <div class="flex items-center">
                            <i class="fas fa-video text-2xl mr-3"></i>
                            <div>
                                <h3 class="font-bold"><?= htmlspecialchars($disp['nombre']) ?></h3>
                                <p class="text-purple-100 text-sm"><?= htmlspecialchars($disp['modelo'] ?: 'HikVision') ?></p>
                            </div>
                        </div>
                        <?php if ($disp['grabacion_activa'] ?? false): ?>
                            <span class="flex items-center">
                                <span class="w-2 h-2 rounded-full bg-red-500 mr-1 animate-pulse"></span>
                                <span class="text-xs">REC</span>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500">Canales</p>
                            <p class="text-lg font-bold text-gray-800">
                                <?= $disp['canales'] ?? 1 ?>
                            </p>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500">Grabación</p>
                            <p class="text-lg font-bold <?= ($disp['grabacion_activa'] ?? false) ? 'text-green-600' : 'text-gray-600' ?>">
                                <?= ($disp['grabacion_activa'] ?? false) ? 'Activa' : 'Inactiva' ?>
                            </p>
                        </div>
                    </div>
                    
                    <?php if ($disp['ubicacion']): ?>
                        <p class="text-sm text-gray-600 mb-2">
                            <i class="fas fa-map-marker-alt mr-1"></i><?= htmlspecialchars($disp['ubicacion']) ?>
                        </p>
                    <?php endif; ?>
                    
                    <?php if ($disp['ip_address']): ?>
                        <p class="text-sm text-gray-600 mb-2">
                            <i class="fas fa-network-wired mr-1"></i><?= htmlspecialchars($disp['ip_address']) ?>
                        </p>
                    <?php endif; ?>
                    
                    <div class="flex text-sm text-gray-500 space-x-4 mb-4">
                        <span><i class="fas fa-globe mr-1"></i>HTTP: <?= $disp['puerto_http'] ?? 80 ?></span>
                        <span><i class="fas fa-play mr-1"></i>RTSP: <?= $disp['puerto_rtsp'] ?? 554 ?></span>
                    </div>
                    
                    <div class="flex items-center mb-4">
                        <?php if ($disp['deteccion_movimiento'] ?? false): ?>
                            <span class="inline-flex items-center px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full mr-2">
                                <i class="fas fa-running mr-1"></i>Detección de movimiento
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex space-x-2">
                        <a href="<?= url('dispositivos/ver/' . $disp['dispositivo_id']) ?>" 
                           class="flex-1 text-center px-3 py-2 border border-gray-300 rounded-md text-sm hover:bg-gray-50">
                            Ver Detalles
                        </a>
                        <a href="<?= url('dispositivos/hikvisionConfig/' . $disp['dispositivo_id']) ?>" 
                           class="flex-1 text-center px-3 py-2 bg-purple-600 text-white rounded-md text-sm hover:bg-purple-700">
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

<!-- Información sobre HikVision -->
<div class="mt-6 bg-purple-50 rounded-lg p-6">
    <h3 class="font-semibold text-purple-800 mb-2">
        <i class="fas fa-info-circle mr-2"></i>Acerca de HikVision
    </h3>
    <p class="text-purple-700 text-sm mb-4">
        Los dispositivos HikVision son cámaras y NVR de videovigilancia que permiten monitorear y grabar en tiempo real.
    </p>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
        <div class="bg-white rounded-lg p-4">
            <i class="fas fa-eye text-purple-600 text-xl mb-2"></i>
            <h4 class="font-medium text-gray-800">Visualización en Vivo</h4>
            <p class="text-gray-600">Monitoreo en tiempo real desde cualquier dispositivo.</p>
        </div>
        <div class="bg-white rounded-lg p-4">
            <i class="fas fa-database text-purple-600 text-xl mb-2"></i>
            <h4 class="font-medium text-gray-800">Grabación Continua</h4>
            <p class="text-gray-600">Almacenamiento de video para revisión posterior.</p>
        </div>
        <div class="bg-white rounded-lg p-4">
            <i class="fas fa-running text-purple-600 text-xl mb-2"></i>
            <h4 class="font-medium text-gray-800">Detección de Movimiento</h4>
            <p class="text-gray-600">Alertas automáticas ante eventos detectados.</p>
        </div>
    </div>
</div>
