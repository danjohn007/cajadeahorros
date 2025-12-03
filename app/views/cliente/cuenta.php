<?php
/**
 * Vista de Cuenta de Ahorro del Cliente
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
        <p class="text-gray-600">Detalle de tu cuenta de ahorro y movimientos</p>
    </div>
    <a href="<?= url('cliente') ?>" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Volver al Portal
    </a>
</div>

<!-- Resumen de Cuenta -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <p class="text-sm text-gray-500">Número de Cuenta</p>
            <p class="text-xl font-bold text-gray-800"><?= htmlspecialchars($cuentaAhorro['numero_cuenta']) ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Saldo Actual</p>
            <p class="text-2xl font-bold text-green-600">$<?= number_format($cuentaAhorro['saldo'], 2) ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Fecha de Apertura</p>
            <p class="text-lg text-gray-800">
                <?= $cuentaAhorro['fecha_apertura'] ? date('d/m/Y', strtotime($cuentaAhorro['fecha_apertura'])) : 'N/A' ?>
            </p>
        </div>
    </div>
</div>

<!-- Historial de Movimientos -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h2 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-history mr-2 text-blue-600"></i>Últimos Movimientos
        </h2>
    </div>
    
    <?php if (empty($movimientos)): ?>
        <div class="p-8 text-center text-gray-500">
            <i class="fas fa-inbox text-4xl mb-4"></i>
            <p>No hay movimientos registrados</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Concepto</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($movimientos as $mov): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= date('d/m/Y H:i', strtotime($mov['fecha'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $tipoClases = [
                                    'aportacion' => 'bg-green-100 text-green-800',
                                    'retiro' => 'bg-red-100 text-red-800',
                                    'interes' => 'bg-blue-100 text-blue-800',
                                    'ajuste' => 'bg-yellow-100 text-yellow-800'
                                ];
                                $clase = $tipoClases[$mov['tipo']] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="px-2 py-1 text-xs rounded-full <?= $clase ?>">
                                    <?= ucfirst($mov['tipo']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?= htmlspecialchars($mov['concepto'] ?? '-') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium <?= in_array($mov['tipo'], ['aportacion', 'interes']) ? 'text-green-600' : 'text-red-600' ?>">
                                <?= in_array($mov['tipo'], ['aportacion', 'interes']) ? '+' : '-' ?>$<?= number_format($mov['monto'], 2) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                                $<?= number_format($mov['saldo_nuevo'] ?? 0, 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
