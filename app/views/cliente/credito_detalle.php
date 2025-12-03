<?php
/**
 * Vista de Detalle de Crédito del Cliente
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
        <p class="text-gray-600">Crédito: <?= htmlspecialchars($credito['numero_credito']) ?></p>
    </div>
    <a href="<?= url('cliente/creditos') ?>" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Volver a Mis Créditos
    </a>
</div>

<!-- Información del Crédito -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <div>
            <p class="text-sm text-gray-500">Tipo de Crédito</p>
            <p class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($credito['tipo_credito']) ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Monto Autorizado</p>
            <p class="text-lg font-semibold text-gray-800">$<?= number_format($credito['monto_autorizado'], 2) ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Saldo Actual</p>
            <p class="text-lg font-semibold text-red-600">$<?= number_format($credito['saldo_actual'], 2) ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Estatus</p>
            <?php
            $estatusClases = [
                'activo' => 'bg-green-100 text-green-800',
                'formalizado' => 'bg-blue-100 text-blue-800',
                'liquidado' => 'bg-gray-100 text-gray-800'
            ];
            $estatusClase = $estatusClases[$credito['estatus']] ?? 'bg-gray-100 text-gray-800';
            ?>
            <span class="inline-block px-3 py-1 text-sm rounded-full <?= $estatusClase ?>">
                <?= ucfirst($credito['estatus']) ?>
            </span>
        </div>
    </div>
    
    <hr class="my-6">
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <div>
            <p class="text-sm text-gray-500">Plazo</p>
            <p class="text-gray-800"><?= $credito['plazo_meses'] ?> meses</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Tasa de Interés</p>
            <p class="text-gray-800"><?= number_format($credito['tasa_interes'] * 100, 2) ?>% mensual</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Cuota Mensual</p>
            <p class="text-gray-800">$<?= number_format($credito['monto_cuota'], 2) ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Pagos Realizados</p>
            <p class="text-gray-800"><?= $credito['pagos_realizados'] ?> de <?= $credito['plazo_meses'] ?></p>
        </div>
    </div>
    
    <hr class="my-6">
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <div>
            <p class="text-sm text-gray-500">Fecha de Solicitud</p>
            <p class="text-gray-800"><?= $credito['fecha_solicitud'] ? date('d/m/Y', strtotime($credito['fecha_solicitud'])) : 'N/A' ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Fecha de Autorización</p>
            <p class="text-gray-800"><?= $credito['fecha_autorizacion'] ? date('d/m/Y', strtotime($credito['fecha_autorizacion'])) : 'N/A' ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Fecha de Formalización</p>
            <p class="text-gray-800"><?= $credito['fecha_formalizacion'] ? date('d/m/Y', strtotime($credito['fecha_formalizacion'])) : 'N/A' ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Primer Pago</p>
            <p class="text-gray-800"><?= $credito['fecha_primer_pago'] ? date('d/m/Y', strtotime($credito['fecha_primer_pago'])) : 'N/A' ?></p>
        </div>
    </div>
    
    <div class="mt-6 flex justify-end">
        <a href="<?= url('cliente/amortizacion/' . $credito['id']) ?>" 
           class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
            <i class="fas fa-table mr-2"></i>Ver Tabla de Amortización
        </a>
    </div>
</div>

<!-- Historial de Pagos -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h2 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-receipt mr-2 text-green-600"></i>Historial de Pagos
        </h2>
    </div>
    
    <?php if (empty($pagos)): ?>
        <div class="p-8 text-center text-gray-500">
            <i class="fas fa-inbox text-4xl mb-4"></i>
            <p>No hay pagos registrados</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Capital</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Interés</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Origen</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($pagos as $pago): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <?= date('d/m/Y', strtotime($pago['fecha_pago'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-600">
                                $<?= number_format($pago['monto_capital'] ?? 0, 2) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-600">
                                $<?= number_format($pago['monto_interes'] ?? 0, 2) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-green-600">
                                $<?= number_format($pago['monto'], 2) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <?= ucfirst($pago['origen'] ?? 'N/A') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
