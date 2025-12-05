<?php
/**
 * Vista de Detalle de Inversionista
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<!-- Header -->
<div class="mb-6 flex justify-between items-center">
    <div>
        <a href="<?= url('inversionistas') ?>" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
            <i class="fas fa-arrow-left mr-1"></i> Volver a Inversionistas
        </a>
        <h2 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h2>
        <p class="text-gray-600"><?= htmlspecialchars($inversionista['numero_inversionista']) ?></p>
    </div>
    <div class="flex space-x-2">
        <a href="<?= url('inversionistas/inversion/' . $inversionista['id']) ?>" 
           class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            <i class="fas fa-plus mr-2"></i>Nueva Inversión
        </a>
        <a href="<?= url('inversionistas/editar/' . $inversionista['id']) ?>" 
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-edit mr-2"></i>Editar
        </a>
    </div>
</div>

<!-- Resumen Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-md p-4">
        <p class="text-sm text-gray-500">Total Invertido</p>
        <p class="text-2xl font-bold text-green-600">$<?= number_format($totalInvertido, 2) ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-4">
        <p class="text-sm text-gray-500">Rendimientos Pagados</p>
        <p class="text-2xl font-bold text-blue-600">$<?= number_format($totalRendimientos, 2) ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-4">
        <p class="text-sm text-gray-500">Inversiones Activas</p>
        <p class="text-2xl font-bold text-purple-600"><?= count(array_filter($inversiones, function($i) { return $i['estatus'] === 'activa'; })) ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-4">
        <p class="text-sm text-gray-500">Estatus</p>
        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium <?= $inversionista['estatus'] === 'activo' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
            <?= ucfirst($inversionista['estatus']) ?>
        </span>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Información Personal -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-user text-blue-600 mr-2"></i>Información Personal
        </h3>
        
        <div class="space-y-3 text-sm">
            <div>
                <span class="text-gray-500">Nombre Completo:</span>
                <p class="font-medium"><?= htmlspecialchars($inversionista['nombre'] . ' ' . $inversionista['apellido_paterno'] . ' ' . ($inversionista['apellido_materno'] ?? '')) ?></p>
            </div>
            <?php if ($inversionista['rfc']): ?>
            <div>
                <span class="text-gray-500">RFC:</span>
                <p class="font-medium"><?= htmlspecialchars($inversionista['rfc']) ?></p>
            </div>
            <?php endif; ?>
            <?php if ($inversionista['curp']): ?>
            <div>
                <span class="text-gray-500">CURP:</span>
                <p class="font-medium"><?= htmlspecialchars($inversionista['curp']) ?></p>
            </div>
            <?php endif; ?>
            <?php if ($inversionista['fecha_nacimiento']): ?>
            <div>
                <span class="text-gray-500">Fecha de Nacimiento:</span>
                <p class="font-medium"><?= date('d/m/Y', strtotime($inversionista['fecha_nacimiento'])) ?></p>
            </div>
            <?php endif; ?>
            <div>
                <span class="text-gray-500">Fecha de Alta:</span>
                <p class="font-medium"><?= date('d/m/Y', strtotime($inversionista['fecha_alta'])) ?></p>
            </div>
        </div>
        
        <hr class="my-4">
        
        <h4 class="font-medium text-gray-700 mb-3">
            <i class="fas fa-phone text-green-600 mr-2"></i>Contacto
        </h4>
        <div class="space-y-2 text-sm">
            <?php if ($inversionista['email']): ?>
            <p><i class="fas fa-envelope w-5 text-gray-400"></i><?= htmlspecialchars($inversionista['email']) ?></p>
            <?php endif; ?>
            <?php if ($inversionista['telefono']): ?>
            <p><i class="fas fa-phone w-5 text-gray-400"></i><?= htmlspecialchars($inversionista['telefono']) ?></p>
            <?php endif; ?>
            <?php if ($inversionista['celular']): ?>
            <p><i class="fas fa-mobile-alt w-5 text-gray-400"></i><?= htmlspecialchars($inversionista['celular']) ?></p>
            <?php endif; ?>
            <?php if ($inversionista['direccion']): ?>
            <p><i class="fas fa-map-marker-alt w-5 text-gray-400"></i><?= htmlspecialchars($inversionista['direccion']) ?></p>
            <?php endif; ?>
        </div>
        
        <?php if ($inversionista['banco'] || $inversionista['cuenta_bancaria'] || $inversionista['clabe']): ?>
        <hr class="my-4">
        
        <h4 class="font-medium text-gray-700 mb-3">
            <i class="fas fa-university text-purple-600 mr-2"></i>Datos Bancarios
        </h4>
        <div class="space-y-2 text-sm">
            <?php if ($inversionista['banco']): ?>
            <p><strong>Banco:</strong> <?= htmlspecialchars($inversionista['banco']) ?></p>
            <?php endif; ?>
            <?php if ($inversionista['cuenta_bancaria']): ?>
            <p><strong>Cuenta:</strong> <?= htmlspecialchars($inversionista['cuenta_bancaria']) ?></p>
            <?php endif; ?>
            <?php if ($inversionista['clabe']): ?>
            <p><strong>CLABE:</strong> <?= htmlspecialchars($inversionista['clabe']) ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Inversiones -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Lista de Inversiones -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-chart-line text-green-600 mr-2"></i>Inversiones
                </h3>
            </div>
            
            <?php if (empty($inversiones)): ?>
                <div class="p-6 text-center text-gray-500">
                    <i class="fas fa-chart-line text-4xl mb-2"></i>
                    <p>No hay inversiones registradas</p>
                    <a href="<?= url('inversionistas/inversion/' . $inversionista['id']) ?>" 
                       class="inline-block mt-3 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-plus mr-2"></i>Registrar Primera Inversión
                    </a>
                </div>
            <?php else: ?>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Inversión</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tasa</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periodo</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estatus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($inversiones as $inv): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm">
                                    <p class="font-medium text-gray-900"><?= htmlspecialchars($inv['numero_inversion']) ?></p>
                                    <?php if ($inv['numero_credito']): ?>
                                        <p class="text-xs text-gray-500">
                                            <i class="fas fa-link mr-1"></i>Crédito: <?= htmlspecialchars($inv['numero_credito']) ?>
                                        </p>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-medium text-green-600">
                                    $<?= number_format($inv['monto'], 2) ?>
                                </td>
                                <td class="px-4 py-3 text-center text-sm">
                                    <?= number_format($inv['tasa_rendimiento'] * 100, 2) ?>%
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    <?= date('d/m/Y', strtotime($inv['fecha_inicio'])) ?> - 
                                    <?= date('d/m/Y', strtotime($inv['fecha_fin'])) ?>
                                    <br>
                                    <span class="text-xs">(<?= $inv['plazo_meses'] ?> meses)</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        <?php
                                        switch($inv['estatus']) {
                                            case 'activa': echo 'bg-green-100 text-green-800'; break;
                                            case 'liquidada': echo 'bg-blue-100 text-blue-800'; break;
                                            case 'cancelada': echo 'bg-red-100 text-red-800'; break;
                                            default: echo 'bg-gray-100 text-gray-800';
                                        }
                                        ?>">
                                        <?= ucfirst($inv['estatus']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <!-- Últimos Rendimientos -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-dollar-sign text-blue-600 mr-2"></i>Últimos Rendimientos
                </h3>
            </div>
            
            <?php if (empty($rendimientos)): ?>
                <div class="p-6 text-center text-gray-500">
                    <i class="fas fa-dollar-sign text-4xl mb-2"></i>
                    <p>No hay rendimientos registrados</p>
                </div>
            <?php else: ?>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Inversión</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estatus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($rendimientos as $rend): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <?= htmlspecialchars($rend['numero_inversion']) ?>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    <?= date('d/m/Y', strtotime($rend['fecha_pago'])) ?>
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-medium text-blue-600">
                                    $<?= number_format($rend['monto'], 2) ?>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        <?= $rend['estatus'] === 'pagado' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                        <?= ucfirst($rend['estatus']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($inversionista['notas']): ?>
<div class="mt-6 bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-3">
        <i class="fas fa-sticky-note text-yellow-600 mr-2"></i>Notas
    </h3>
    <p class="text-gray-700"><?= nl2br(htmlspecialchars($inversionista['notas'])) ?></p>
</div>
<?php endif; ?>
