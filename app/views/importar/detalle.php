<?php
/**
 * Vista de Detalle de Importación
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6">
    <a href="<?= url('importar/historial') ?>" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Volver al Historial
    </a>
    <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
    <p class="text-gray-600">Archivo: <?= htmlspecialchars($importacion['nombre_archivo']) ?></p>
</div>

<!-- Resumen de la importación -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-md p-4">
        <p class="text-sm text-gray-500">Total Registros</p>
        <p class="text-2xl font-bold text-gray-800"><?= $importacion['total_registros'] ?? 0 ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-4">
        <p class="text-sm text-gray-500">Exitosos</p>
        <p class="text-2xl font-bold text-green-600"><?= $importacion['registros_exitosos'] ?? 0 ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-4">
        <p class="text-sm text-gray-500">Errores</p>
        <p class="text-2xl font-bold text-red-600"><?= $importacion['registros_error'] ?? 0 ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-4">
        <p class="text-sm text-gray-500">Estatus</p>
        <?php
        $statusColors = [
            'completado' => 'green',
            'parcial' => 'yellow',
            'error' => 'red',
            'procesando' => 'blue'
        ];
        $color = $statusColors[$importacion['estatus']] ?? 'gray';
        ?>
        <span class="inline-block px-3 py-1 text-lg rounded-full bg-<?= $color ?>-100 text-<?= $color ?>-800 font-medium">
            <?= ucfirst($importacion['estatus']) ?>
        </span>
    </div>
</div>

<!-- Información adicional -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Información de la Importación</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <p class="text-sm text-gray-500">Fecha de Inicio</p>
            <p class="font-medium"><?= date('d/m/Y H:i:s', strtotime($importacion['fecha_inicio'])) ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Fecha de Fin</p>
            <p class="font-medium"><?= $importacion['fecha_fin'] ? date('d/m/Y H:i:s', strtotime($importacion['fecha_fin'])) : 'En proceso' ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Usuario</p>
            <p class="font-medium"><?= htmlspecialchars($importacion['usuario_nombre'] ?? 'Sistema') ?></p>
        </div>
    </div>
    <?php if (!empty($importacion['notas'])): ?>
        <div class="mt-4">
            <p class="text-sm text-gray-500">Notas</p>
            <p class="text-gray-700"><?= htmlspecialchars($importacion['notas']) ?></p>
        </div>
    <?php endif; ?>
</div>

<!-- Detalle de registros -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h2 class="text-lg font-semibold text-gray-800">Detalle de Registros</h2>
    </div>
    
    <?php if (empty($detalles)): ?>
        <div class="p-8 text-center text-gray-500">
            <i class="fas fa-info-circle text-4xl mb-4"></i>
            <p>No hay detalles disponibles para esta importación</p>
        </div>
    <?php else: ?>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fila</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estatus</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Datos</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mensaje</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entidad</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($detalles as $det): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $det['fila'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                            $detStatusColors = [
                                'exitoso' => 'green',
                                'error' => 'red',
                                'duplicado' => 'yellow'
                            ];
                            $detColor = $detStatusColors[$det['estatus']] ?? 'gray';
                            ?>
                            <span class="px-2 py-1 text-xs rounded-full bg-<?= $detColor ?>-100 text-<?= $detColor ?>-800">
                                <?= ucfirst($det['estatus']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-md truncate" title="<?= htmlspecialchars($det['datos_originales']) ?>">
                            <?php 
                            $datos = json_decode($det['datos_originales'], true);
                            if ($datos) {
                                echo htmlspecialchars(implode(', ', array_slice($datos, 0, 3)));
                            } else {
                                echo htmlspecialchars(substr($det['datos_originales'] ?? '', 0, 50));
                            }
                            ?>
                        </td>
                        <td class="px-6 py-4 text-sm <?= $det['estatus'] === 'error' ? 'text-red-600' : 'text-gray-500' ?>">
                            <?= htmlspecialchars($det['mensaje_error'] ?? '-') ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <?php if ($det['entidad_id']): ?>
                                <a href="<?= url('socios/ver/' . $det['entidad_id']) ?>" class="text-blue-600 hover:text-blue-800">
                                    Ver socio #<?= $det['entidad_id'] ?>
                                </a>
                            <?php else: ?>
                                <span class="text-gray-400">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
