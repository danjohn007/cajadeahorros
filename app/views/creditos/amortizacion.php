<!-- Header -->
<div class="mb-6">
    <a href="<?= BASE_URL ?>/creditos/ver/<?= $credito['id'] ?>" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Volver al Crédito
    </a>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Tabla de Amortización</h2>
            <p class="text-gray-600">
                <?= htmlspecialchars($credito['numero_credito']) ?> - 
                <?= htmlspecialchars($credito['nombre'] . ' ' . $credito['apellido_paterno']) ?>
            </p>
        </div>
        <div class="mt-4 md:mt-0">
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                <i class="fas fa-print mr-2"></i> Imprimir
            </button>
        </div>
    </div>
</div>

<!-- Resumen -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-4">
        <p class="text-sm text-gray-500">Monto</p>
        <p class="text-lg font-bold text-purple-600">$<?= number_format($credito['monto_autorizado'], 2) ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4">
        <p class="text-sm text-gray-500">Tasa</p>
        <p class="text-lg font-bold"><?= number_format($credito['tasa_interes'] * 100, 2) ?>%</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4">
        <p class="text-sm text-gray-500">Plazo</p>
        <p class="text-lg font-bold"><?= $credito['plazo_meses'] ?> meses</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4">
        <p class="text-sm text-gray-500">Cuota</p>
        <p class="text-lg font-bold">$<?= number_format($credito['monto_cuota'], 2) ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4">
        <p class="text-sm text-gray-500">Saldo Actual</p>
        <p class="text-lg font-bold text-green-600">$<?= number_format($credito['saldo_actual'], 2) ?></p>
    </div>
</div>

<!-- Tabla -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No.</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vencimiento</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Capital</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Interés</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cuota</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Pago</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estatus</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php 
                $totalCapital = 0;
                $totalInteres = 0;
                $totalCuota = 0;
                
                foreach ($amortizacion as $pago): 
                    $totalCapital += $pago['monto_capital'];
                    $totalInteres += $pago['monto_interes'];
                    $totalCuota += $pago['monto_total'];
                    
                    $esVencido = strtotime($pago['fecha_vencimiento']) < time() && $pago['estatus'] !== 'pagado';
                ?>
                <tr class="<?= $esVencido ? 'bg-red-50' : ($pago['estatus'] === 'pagado' ? 'bg-green-50' : '') ?>">
                    <td class="px-4 py-3 text-sm font-medium"><?= $pago['numero_pago'] ?></td>
                    <td class="px-4 py-3 text-sm <?= $esVencido ? 'text-red-600 font-medium' : '' ?>">
                        <?= date('d/m/Y', strtotime($pago['fecha_vencimiento'])) ?>
                    </td>
                    <td class="px-4 py-3 text-sm text-right">$<?= number_format($pago['monto_capital'], 2) ?></td>
                    <td class="px-4 py-3 text-sm text-right">$<?= number_format($pago['monto_interes'], 2) ?></td>
                    <td class="px-4 py-3 text-sm text-right font-medium">$<?= number_format($pago['monto_total'], 2) ?></td>
                    <td class="px-4 py-3 text-sm text-right">$<?= number_format($pago['saldo_restante'], 2) ?></td>
                    <td class="px-4 py-3 text-sm">
                        <?= $pago['fecha_pago'] ? date('d/m/Y', strtotime($pago['fecha_pago'])) : '-' ?>
                    </td>
                    <td class="px-4 py-3">
                        <?php
                        $pagoColors = [
                            'pendiente' => 'bg-yellow-100 text-yellow-800',
                            'pagado' => 'bg-green-100 text-green-800',
                            'vencido' => 'bg-red-100 text-red-800',
                            'parcial' => 'bg-orange-100 text-orange-800'
                        ];
                        $pColor = $pagoColors[$pago['estatus']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <span class="px-2 py-1 text-xs font-medium rounded-full <?= $pColor ?>">
                            <?= ucfirst($pago['estatus']) ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="bg-gray-100 font-medium">
                <tr>
                    <td class="px-4 py-3 text-sm" colspan="2">TOTALES</td>
                    <td class="px-4 py-3 text-sm text-right">$<?= number_format($totalCapital, 2) ?></td>
                    <td class="px-4 py-3 text-sm text-right">$<?= number_format($totalInteres, 2) ?></td>
                    <td class="px-4 py-3 text-sm text-right">$<?= number_format($totalCuota, 2) ?></td>
                    <td class="px-4 py-3 text-sm text-right" colspan="3">-</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<style>
    @media print {
        .sidebar-link, header, footer, .no-print { display: none !important; }
        body { background: white !important; }
        .bg-white { box-shadow: none !important; }
    }
</style>
