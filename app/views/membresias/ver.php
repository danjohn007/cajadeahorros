<?php
/**
 * Vista de Detalle de Membresía
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6">
    <a href="<?= url('membresias') ?>" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
        <i class="fas fa-arrow-left mr-2"></i>Volver a Membresías
    </a>
    
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
            <p class="text-gray-600">Información detallada de la membresía</p>
        </div>
    </div>
</div>

<!-- Información de la Membresía -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h2 class="text-xl font-semibold text-gray-800 mb-4">
        <i class="fas fa-id-card text-blue-600 mr-2"></i>Información de la Membresía
    </h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Número de Membresía</label>
            <p class="text-gray-900 font-semibold"><?= htmlspecialchars($membresia['numero_membresia']) ?></p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Membresía</label>
            <p class="text-gray-900"><?= htmlspecialchars($membresia['tipo_nombre']) ?></p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Socio</label>
            <p class="text-gray-900">
                <?= htmlspecialchars($membresia['socio_nombre'] . ' ' . $membresia['socio_apellido_paterno'] . ' ' . ($membresia['socio_apellido_materno'] ?? '')) ?>
            </p>
            <p class="text-sm text-gray-500"><?= htmlspecialchars($membresia['numero_socio']) ?></p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
            <span class="px-3 py-1 rounded-full text-sm font-medium <?= 
                $membresia['estado'] === 'activa' ? 'bg-green-100 text-green-800' : 
                ($membresia['estado'] === 'vencida' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') 
            ?>">
                <?= htmlspecialchars(ucfirst($membresia['estado'])) ?>
            </span>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Inicio</label>
            <p class="text-gray-900"><?= date('d/m/Y', strtotime($membresia['fecha_inicio'])) ?></p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Vencimiento</label>
            <p class="text-gray-900"><?= date('d/m/Y', strtotime($membresia['fecha_vencimiento'])) ?></p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Monto</label>
            <p class="text-gray-900 font-semibold text-lg">$<?= number_format($membresia['monto'], 2) ?></p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Periodicidad</label>
            <p class="text-gray-900"><?= htmlspecialchars(ucfirst($membresia['periodicidad'])) ?></p>
        </div>
    </div>
    
    <?php if (!empty($membresia['notas'])): ?>
    <div class="mt-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
        <p class="text-gray-900"><?= nl2br(htmlspecialchars($membresia['notas'])) ?></p>
    </div>
    <?php endif; ?>
</div>

<!-- Historial de Pagos -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-semibold text-gray-800 mb-4">
        <i class="fas fa-history text-blue-600 mr-2"></i>Historial de Pagos
    </h2>
    
    <?php if (empty($pagos)): ?>
    <div class="text-center py-8 text-gray-500">
        <i class="fas fa-inbox text-4xl mb-3"></i>
        <p>No hay pagos registrados para esta membresía</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de Pago</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Método</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referencia</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($pagos as $pago): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                        $<?= number_format($pago['monto'], 2) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?= htmlspecialchars($pago['metodo_pago'] ?? 'N/A') ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?= htmlspecialchars($pago['referencia'] ?? '-') ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?= htmlspecialchars($pago['usuario_nombre'] ?? 'Sistema') ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
