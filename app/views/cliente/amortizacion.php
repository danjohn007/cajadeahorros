<?php
/**
 * Vista de Tabla de Amortización del Cliente
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
        <p class="text-gray-600">Crédito: <?= htmlspecialchars($credito['numero_credito']) ?> - <?= htmlspecialchars($credito['tipo_credito']) ?></p>
    </div>
    <a href="<?= url('cliente/credito/' . $credito['id']) ?>" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Volver al Detalle
    </a>
</div>

<!-- Resumen -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-md p-4">
        <p class="text-sm text-gray-500">Total del Crédito</p>
        <p class="text-xl font-bold text-gray-800">$<?= number_format($credito['monto_autorizado'], 2) ?></p>
    </div>
    <div class="bg-green-50 rounded-lg shadow-md p-4">
        <p class="text-sm text-green-600">Total Pagado</p>
        <p class="text-xl font-bold text-green-600">$<?= number_format($totalPagado, 2) ?></p>
    </div>
    <div class="bg-red-50 rounded-lg shadow-md p-4">
        <p class="text-sm text-red-600">Total Vencido</p>
        <p class="text-xl font-bold text-red-600">$<?= number_format($totalVencido, 2) ?></p>
    </div>
    <div class="bg-blue-50 rounded-lg shadow-md p-4">
        <p class="text-sm text-blue-600">Total Pendiente</p>
        <p class="text-xl font-bold text-blue-600">$<?= number_format($totalPendiente, 2) ?></p>
    </div>
</div>

<?php if ($totalVencido > 0): ?>
<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
    <div class="flex items-center">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <strong>Tienes pagos vencidos por $<?= number_format($totalVencido, 2) ?></strong>
    </div>
    <p class="mt-1 text-sm">Te recomendamos ponerte al corriente lo antes posible para evitar cargos adicionales.</p>
    <a href="<?= url('cliente/pagar') ?>" class="inline-block mt-2 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
        <i class="fas fa-credit-card mr-1"></i>Realizar Pago
    </a>
</div>
<?php endif; ?>

<!-- Tabla de Amortización -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h2 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-table mr-2 text-purple-600"></i>Tabla de Pagos
        </h2>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">No.</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Vencimiento</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Capital</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Interés</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estatus</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Pago</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($amortizacion as $pago): 
                    $esVencido = $pago['estatus'] === 'vencido' || 
                                 ($pago['estatus'] === 'pendiente' && $pago['fecha_vencimiento'] < date('Y-m-d'));
                    
                    $rowClass = '';
                    if ($pago['estatus'] === 'pagado') {
                        $rowClass = 'bg-green-50';
                    } elseif ($esVencido) {
                        $rowClass = 'bg-red-50';
                    }
                    
                    $estatusClases = [
                        'pagado' => 'bg-green-100 text-green-800',
                        'vencido' => 'bg-red-100 text-red-800',
                        'pendiente' => 'bg-yellow-100 text-yellow-800',
                        'parcial' => 'bg-orange-100 text-orange-800'
                    ];
                    
                    if ($esVencido && $pago['estatus'] === 'pendiente') {
                        $estatusClase = 'bg-red-100 text-red-800';
                        $estatusTexto = 'Vencido';
                    } else {
                        $estatusClase = $estatusClases[$pago['estatus']] ?? 'bg-gray-100 text-gray-800';
                        $estatusTexto = ucfirst($pago['estatus']);
                    }
                ?>
                    <tr class="hover:bg-gray-100 <?= $rowClass ?>">
                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm font-medium text-gray-900">
                            <?= $pago['numero_pago'] ?>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                            <?= date('d/m/Y', strtotime($pago['fecha_vencimiento'])) ?>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-600">
                            $<?= number_format($pago['monto_capital'], 2) ?>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-600">
                            $<?= number_format($pago['monto_interes'], 2) ?>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-medium text-gray-900">
                            $<?= number_format($pago['monto_total'], 2) ?>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-600">
                            $<?= number_format($pago['saldo_restante'] ?? 0, 2) ?>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <span class="px-2 py-1 text-xs rounded-full <?= $estatusClase ?>">
                                <?= $estatusTexto ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                            <?= $pago['fecha_pago'] ? date('d/m/Y', strtotime($pago['fecha_pago'])) : '-' ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6 flex justify-between">
    <a href="<?= url('cliente/creditos') ?>" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
        <i class="fas fa-arrow-left mr-2"></i>Volver a Mis Créditos
    </a>
    <?php if ($totalVencido > 0 || $totalPendiente > 0): ?>
        <a href="<?= url('cliente/pagar') ?>" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            <i class="fas fa-credit-card mr-2"></i>Realizar Pago
        </a>
    <?php endif; ?>
</div>
