<?php
/**
 * Vista de Evaluación Preliminar de Requisitos
 * Validación automática de requisitos básicos y excluyentes para optimizar el proceso de análisis crediticio
 */

// Mock statistics data (in production would come from database)
$stats = [
    'total' => 247,
    'aprobadas' => 189,
    'rechazadas' => 34,
    'pendientes' => 24
];

// Mock evaluations data (in production would come from database with applied filters)
$evaluaciones = [
    [
        'id' => 1,
        'numero_solicitud' => 'SOL-2024-001',
        'cliente' => 'Juan Carlos Pérez Martínez',
        'tipo_producto' => 'Crédito Personal',
        'monto_solicitado' => 150000.00,
        'fecha_evaluacion' => '15/03/2024 10:30',
        'agente_ventas' => 'María González',
        'progreso' => 100,
        'estado' => 'aprobado',
        'requisitos_basicos' => [
            ['nombre' => 'Edad mínima (18 años)', 'cumple' => true],
            ['nombre' => 'Identificación oficial vigente', 'cumple' => true],
            ['nombre' => 'Comprobante de ingresos', 'cumple' => true]
        ],
        'requisitos_excluyentes' => [
            ['nombre' => 'Sin reporte en buró negativo', 'cumple' => true],
            ['nombre' => 'Capacidad de pago suficiente', 'cumple' => true],
            ['nombre' => 'Sin antecedentes penales', 'cumple' => true]
        ],
        'validaciones_pendientes' => [],
        'motivo_rechazo' => ''
    ],
    [
        'id' => 2,
        'numero_solicitud' => 'SOL-2024-002',
        'cliente' => 'Ana María Rodríguez López',
        'tipo_producto' => 'Crédito Hipotecario',
        'monto_solicitado' => 850000.00,
        'fecha_evaluacion' => '14/03/2024 14:15',
        'agente_ventas' => 'Carlos Rodríguez',
        'progreso' => 60,
        'estado' => 'rechazado',
        'requisitos_basicos' => [
            ['nombre' => 'Edad mínima (18 años)', 'cumple' => true],
            ['nombre' => 'Identificación oficial vigente', 'cumple' => true],
            ['nombre' => 'Comprobante de ingresos', 'cumple' => false]
        ],
        'requisitos_excluyentes' => [
            ['nombre' => 'Sin reporte en buró negativo', 'cumple' => false],
            ['nombre' => 'Capacidad de pago suficiente', 'cumple' => true],
            ['nombre' => 'Sin antecedentes penales', 'cumple' => true]
        ],
        'validaciones_pendientes' => [],
        'motivo_rechazo' => 'Reporte negativo en buró de crédito y documentación de ingresos insuficiente.'
    ],
    [
        'id' => 3,
        'numero_solicitud' => 'SOL-2024-003',
        'cliente' => 'Roberto Martínez Sánchez',
        'tipo_producto' => 'Crédito Automotriz',
        'monto_solicitado' => 320000.00,
        'fecha_evaluacion' => '16/03/2024 09:45',
        'agente_ventas' => 'Ana Martínez',
        'progreso' => 75,
        'estado' => 'en_proceso',
        'requisitos_basicos' => [
            ['nombre' => 'Edad mínima (18 años)', 'cumple' => true],
            ['nombre' => 'Identificación oficial vigente', 'cumple' => true],
            ['nombre' => 'Comprobante de ingresos', 'cumple' => true]
        ],
        'requisitos_excluyentes' => [
            ['nombre' => 'Sin reporte en buró negativo', 'cumple' => 'pendiente'],
            ['nombre' => 'Capacidad de pago suficiente', 'cumple' => true],
            ['nombre' => 'Sin antecedentes penales', 'cumple' => true]
        ],
        'validaciones_pendientes' => ['Verificación de capacidad de pago y consulta de antecedentes penales en proceso.'],
        'motivo_rechazo' => ''
    ],
    [
        'id' => 4,
        'numero_solicitud' => 'SOL-2024-004',
        'cliente' => 'Carmen Elena Vásquez Torres',
        'tipo_producto' => 'Tarjeta de Crédito',
        'monto_solicitado' => 50000.00,
        'fecha_evaluacion' => '16/03/2024 16:20',
        'agente_ventas' => 'Luis Hernández',
        'progreso' => 25,
        'estado' => 'pendiente',
        'requisitos_basicos' => [
            ['nombre' => 'Edad mínima (18 años)', 'cumple' => true],
            ['nombre' => 'Identificación oficial vigente', 'cumple' => 'pendiente'],
            ['nombre' => 'Comprobante de ingresos', 'cumple' => 'pendiente']
        ],
        'requisitos_excluyentes' => [
            ['nombre' => 'Sin reporte en buró negativo', 'cumple' => 'pendiente'],
            ['nombre' => 'Capacidad de pago suficiente', 'cumple' => 'pendiente'],
            ['nombre' => 'Sin antecedentes penales', 'cumple' => 'pendiente']
        ],
        'validaciones_pendientes' => ['La evaluación iniciará una vez que se complete la documentación requerida.'],
        'motivo_rechazo' => ''
    ]
];
?>
<div class="container mx-auto px-4 py-6">
    <!-- Encabezado -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-primary-800">Evaluación Preliminar de Requisitos</h1>
            <p class="text-gray-600 mt-1">Validación automática de requisitos básicos y excluyentes para optimizar el proceso de análisis crediticio</p>
        </div>
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>Nueva Evaluación
        </button>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-4xl font-bold text-blue-600"><?= $stats['total'] ?></div>
            <div class="text-gray-600 text-sm mt-2 uppercase">Evaluaciones Totales</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-4xl font-bold text-green-600"><?= $stats['aprobadas'] ?></div>
            <div class="text-gray-600 text-sm mt-2 uppercase">Aprobadas</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-4xl font-bold text-red-600"><?= $stats['rechazadas'] ?></div>
            <div class="text-gray-600 text-sm mt-2 uppercase">Rechazadas</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-4xl font-bold text-yellow-600"><?= $stats['pendientes'] ?></div>
            <div class="text-gray-600 text-sm mt-2 uppercase">Pendientes</div>
        </div>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Filtros de Búsqueda</h3>
        <form method="GET" action="<?= BASE_URL ?>/solicitudes/evaluacion">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Número de Solicitud</label>
                    <input type="text" name="numero_solicitud" placeholder="SOL-2024-001" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Cliente</label>
                    <input type="text" name="nombre_cliente" placeholder="Nombre completo" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select name="estado" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todos los Estados</option>
                        <option value="aprobado">Aprobado</option>
                        <option value="rechazado">Rechazado</option>
                        <option value="en_proceso">En Proceso</option>
                        <option value="pendiente">Pendiente</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Desde</label>
                    <input type="date" name="fecha_desde" value="<?= date('Y-01-01') ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Hasta</label>
                    <input type="date" name="fecha_hasta" value="<?= date('Y-12-31') ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Producto</label>
                    <select name="tipo_producto" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todos los Productos</option>
                        <option value="personal">Crédito Personal</option>
                        <option value="hipotecario">Crédito Hipotecario</option>
                        <option value="automotriz">Crédito Automotriz</option>
                        <option value="tarjeta">Tarjeta de Crédito</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Agente de Ventas</label>
                    <select name="agente_ventas" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todos los Agentes</option>
                        <option value="maria">María González</option>
                        <option value="carlos">Carlos Rodríguez</option>
                        <option value="ana">Ana Martínez</option>
                        <option value="luis">Luis Hernández</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sucursal</label>
                    <select name="sucursal" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todas las Sucursales</option>
                        <option value="matriz">Matriz</option>
                        <option value="norte">Norte</option>
                        <option value="sur">Sur</option>
                    </select>
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center justify-center">
                        <i class="fas fa-search mr-2"></i>Filtrar
                    </button>
                    <button type="button" onclick="window.location.href='<?= BASE_URL ?>/solicitudes/evaluacion'" 
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg flex items-center justify-center">
                        <i class="fas fa-times mr-2"></i>Limpiar
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Lista de Evaluaciones -->
    <div class="space-y-4">
        <?php foreach ($evaluaciones as $eval): ?>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- Encabezado de la evaluación -->
            <div class="bg-primary-800 text-white p-4 flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <span class="font-bold text-lg"><?= htmlspecialchars($eval['numero_solicitud']) ?></span>
                    <span>-</span>
                    <span class="text-lg"><?= htmlspecialchars($eval['cliente']) ?></span>
                    <?php if ($eval['estado'] === 'aprobado'): ?>
                        <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-semibold">APROBADO</span>
                    <?php elseif ($eval['estado'] === 'rechazado'): ?>
                        <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-semibold">RECHAZADO</span>
                    <?php elseif ($eval['estado'] === 'en_proceso'): ?>
                        <span class="bg-cyan-500 text-white px-3 py-1 rounded-full text-sm font-semibold">EN PROCESO</span>
                    <?php else: ?>
                        <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-sm font-semibold">PENDIENTE</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Información de la evaluación -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <div class="bg-gray-50 p-4 rounded">
                        <div class="text-sm text-gray-600 mb-1">Tipo de Producto</div>
                        <div class="font-semibold"><?= htmlspecialchars($eval['tipo_producto']) ?></div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded">
                        <div class="text-sm text-gray-600 mb-1">Monto Solicitado</div>
                        <div class="font-semibold">$<?= number_format($eval['monto_solicitado'], 2) ?></div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded">
                        <div class="text-sm text-gray-600 mb-1">Fecha de Evaluación</div>
                        <div class="font-semibold"><?= htmlspecialchars($eval['fecha_evaluacion']) ?></div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded">
                        <div class="text-sm text-gray-600 mb-1">Agente de Ventas</div>
                        <div class="font-semibold"><?= htmlspecialchars($eval['agente_ventas']) ?></div>
                    </div>
                </div>

                <!-- Progreso de Evaluación -->
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-semibold text-gray-700">Progreso de Evaluación</span>
                        <span class="text-sm font-bold 
                            <?= $eval['progreso'] == 100 ? 'text-green-600' : '' ?>
                            <?= $eval['progreso'] >= 60 && $eval['progreso'] < 100 ? 'text-red-600' : '' ?>
                            <?= $eval['progreso'] >= 25 && $eval['progreso'] < 60 ? 'text-cyan-600' : '' ?>
                            <?= $eval['progreso'] < 25 ? 'text-yellow-600' : '' ?>">
                            <?= $eval['progreso'] ?>%
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="h-3 rounded-full 
                            <?= $eval['progreso'] == 100 ? 'bg-green-500' : '' ?>
                            <?= $eval['progreso'] >= 60 && $eval['progreso'] < 100 ? 'bg-red-500' : '' ?>
                            <?= $eval['progreso'] >= 25 && $eval['progreso'] < 60 ? 'bg-cyan-500' : '' ?>
                            <?= $eval['progreso'] < 25 ? 'bg-yellow-500' : '' ?>" 
                             style="width: <?= $eval['progreso'] ?>%"></div>
                    </div>
                </div>

                <!-- Requisitos -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <!-- Requisitos Básicos -->
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3">Requisitos Básicos</h4>
                        <div class="space-y-2">
                            <?php foreach ($eval['requisitos_basicos'] as $req): ?>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-700"><?= htmlspecialchars($req['nombre']) ?></span>
                                <?php if ($req['cumple'] === true): ?>
                                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                                <?php elseif ($req['cumple'] === false): ?>
                                    <i class="fas fa-times-circle text-red-500 text-xl"></i>
                                <?php else: ?>
                                    <i class="fas fa-clock text-yellow-500 text-xl"></i>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Requisitos Excluyentes -->
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3">Requisitos Excluyentes</h4>
                        <div class="space-y-2">
                            <?php foreach ($eval['requisitos_excluyentes'] as $req): ?>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-700"><?= htmlspecialchars($req['nombre']) ?></span>
                                <?php if ($req['cumple'] === true): ?>
                                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                                <?php elseif ($req['cumple'] === false): ?>
                                    <i class="fas fa-times-circle text-red-500 text-xl"></i>
                                <?php else: ?>
                                    <i class="fas fa-clock text-yellow-500 text-xl"></i>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Motivo de rechazo -->
                <?php if ($eval['estado'] === 'rechazado' && !empty($eval['motivo_rechazo'])): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-red-500 mt-1 mr-3"></i>
                        <div>
                            <h5 class="font-semibold text-red-800 mb-1">Motivo de Rechazo</h5>
                            <p class="text-sm text-red-700"><?= htmlspecialchars($eval['motivo_rechazo']) ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Validaciones pendientes -->
                <?php if (!empty($eval['validaciones_pendientes'])): ?>
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                        <div>
                            <h5 class="font-semibold text-blue-800 mb-1">Validaciones Pendientes</h5>
                            <?php foreach ($eval['validaciones_pendientes'] as $validacion): ?>
                            <p class="text-sm text-blue-700"><?= htmlspecialchars($validacion) ?></p>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Botones de acción -->
                <div class="flex flex-wrap gap-2">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center">
                        <i class="fas fa-eye mr-2"></i>Ver Detalles
                    </button>
                    <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded flex items-center">
                        <i class="fas fa-history mr-2"></i>Historial
                    </button>
                    <button class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded flex items-center">
                        <i class="fas fa-file-alt mr-2"></i>Reporte
                    </button>
                    <?php if ($eval['estado'] === 'aprobado'): ?>
                        <button class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded flex items-center">
                            <i class="fas fa-redo mr-2"></i>Re-evaluar
                        </button>
                    <?php elseif ($eval['estado'] === 'rechazado'): ?>
                        <button class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded flex items-center">
                            <i class="fas fa-archive mr-2"></i>Archivar
                        </button>
                    <?php elseif ($eval['estado'] === 'en_proceso'): ?>
                        <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded flex items-center">
                            <i class="fas fa-play mr-2"></i>Continuar
                        </button>
                    <?php else: ?>
                        <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded flex items-center">
                            <i class="fas fa-play mr-2"></i>Iniciar Evaluación
                        </button>
                        <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded flex items-center">
                            <i class="fas fa-times mr-2"></i>Cancelar
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
