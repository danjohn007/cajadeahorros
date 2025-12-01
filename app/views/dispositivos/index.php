<?php
/**
 * Vista de Dispositivos IoT
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
        <p class="text-gray-600">Gestión de dispositivos Shelly Cloud y HikVision</p>
    </div>
    <a href="<?= url('dispositivos/crear') ?>" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
        <i class="fas fa-plus mr-2"></i>Nuevo Dispositivo
    </a>
</div>

<!-- Estadísticas -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-microchip text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Total Dispositivos</p>
                <p class="text-2xl font-bold text-gray-800"><?= $stats['total'] ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-check-circle text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Activos</p>
                <p class="text-2xl font-bold text-gray-800"><?= $stats['activos'] ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                <i class="fas fa-bolt text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Shelly Cloud</p>
                <p class="text-2xl font-bold text-gray-800"><?= $stats['shelly'] ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <i class="fas fa-video text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">HikVision</p>
                <p class="text-2xl font-bold text-gray-800"><?= $stats['hikvision'] ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <div class="flex flex-wrap gap-4 items-center">
        <span class="text-sm text-gray-600">Filtrar por tipo:</span>
        <a href="<?= url('dispositivos') ?>" 
           class="px-3 py-1 rounded-full text-sm <?= !$tipoFilter ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?>">
            Todos
        </a>
        <a href="<?= url('dispositivos') ?>?tipo=shelly" 
           class="px-3 py-1 rounded-full text-sm <?= $tipoFilter === 'shelly' ? 'bg-orange-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?>">
            <i class="fas fa-bolt mr-1"></i>Shelly
        </a>
        <a href="<?= url('dispositivos') ?>?tipo=hikvision" 
           class="px-3 py-1 rounded-full text-sm <?= $tipoFilter === 'hikvision' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?>">
            <i class="fas fa-video mr-1"></i>HikVision
        </a>
        
        <div class="ml-auto flex gap-2">
            <a href="<?= url('dispositivos/eventos') ?>" class="px-3 py-1 border rounded text-sm hover:bg-gray-50">
                <i class="fas fa-history mr-1"></i>Eventos
            </a>
            <a href="<?= url('dispositivos/programacion') ?>" class="px-3 py-1 border rounded text-sm hover:bg-gray-50">
                <i class="fas fa-clock mr-1"></i>Programación
            </a>
        </div>
    </div>
</div>

<!-- Lista de dispositivos -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (empty($dispositivos)): ?>
        <div class="col-span-full bg-white rounded-lg shadow-md p-8 text-center">
            <i class="fas fa-microchip text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">No hay dispositivos registrados</p>
            <a href="<?= url('dispositivos/crear') ?>" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                Agregar primer dispositivo
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($dispositivos as $disp): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center">
                            <?php if ($disp['tipo'] === 'shelly'): ?>
                                <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                                    <i class="fas fa-bolt text-xl"></i>
                                </div>
                            <?php elseif ($disp['tipo'] === 'hikvision'): ?>
                                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                    <i class="fas fa-video text-xl"></i>
                                </div>
                            <?php else: ?>
                                <div class="p-3 rounded-full bg-gray-100 text-gray-600">
                                    <i class="fas fa-microchip text-xl"></i>
                                </div>
                            <?php endif; ?>
                            <div class="ml-4">
                                <h3 class="font-semibold text-gray-800"><?= htmlspecialchars($disp['nombre']) ?></h3>
                                <p class="text-sm text-gray-500"><?= htmlspecialchars($disp['modelo'] ?: 'Sin modelo') ?></p>
                            </div>
                        </div>
                        <?php if ($disp['activo']): ?>
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Activo</span>
                        <?php else: ?>
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Inactivo</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mt-4 space-y-2 text-sm">
                        <?php if ($disp['ubicacion']): ?>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-map-marker-alt w-5"></i>
                                <span><?= htmlspecialchars($disp['ubicacion']) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($disp['ip_address']): ?>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-network-wired w-5"></i>
                                <span><?= htmlspecialchars($disp['ip_address']) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($disp['estado_detalle']): ?>
                            <div class="flex items-center">
                                <?php 
                                $estadoColor = match($disp['estado_detalle']) {
                                    'on', 'grabando' => 'text-green-600',
                                    'off', 'inactivo' => 'text-gray-600',
                                    default => 'text-yellow-600'
                                };
                                ?>
                                <i class="fas fa-circle w-5 <?= $estadoColor ?>" style="font-size: 0.5rem;"></i>
                                <span class="<?= $estadoColor ?>"><?= ucfirst($disp['estado_detalle']) ?></span>
                                <?php if ($disp['potencia']): ?>
                                    <span class="ml-2 text-gray-500">(<?= $disp['potencia'] ?>W)</span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t flex justify-between">
                        <a href="<?= url('dispositivos/ver/' . $disp['id']) ?>" 
                           class="text-blue-600 hover:text-blue-800 text-sm">
                            Ver detalles
                        </a>
                        <a href="<?= url('dispositivos/editar/' . $disp['id']) ?>" 
                           class="text-gray-600 hover:text-gray-800 text-sm">
                            <i class="fas fa-cog"></i> Configurar
                        </a>
                    </div>
                </div>
                
                <?php if ($disp['ultima_conexion']): ?>
                    <div class="px-6 py-2 bg-gray-50 text-xs text-gray-500">
                        Última conexión: <?= date('d/m/Y H:i', strtotime($disp['ultima_conexion'])) ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Accesos directos a tipos -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
    <a href="<?= url('dispositivos/shelly') ?>" 
       class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg shadow-md p-6 text-white hover:shadow-lg transition-shadow">
        <div class="flex items-center">
            <i class="fas fa-bolt text-4xl"></i>
            <div class="ml-4">
                <h3 class="text-xl font-bold">Shelly Cloud</h3>
                <p class="text-orange-100">Controles inteligentes y medidores de energía</p>
            </div>
        </div>
    </a>
    
    <a href="<?= url('dispositivos/hikvision') ?>" 
       class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-md p-6 text-white hover:shadow-lg transition-shadow">
        <div class="flex items-center">
            <i class="fas fa-video text-4xl"></i>
            <div class="ml-4">
                <h3 class="text-xl font-bold">HikVision</h3>
                <p class="text-purple-100">Cámaras y sistemas de videovigilancia</p>
            </div>
        </div>
    </a>
</div>
