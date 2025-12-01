<!-- Header -->
<div class="mb-6">
    <a href="<?= BASE_URL ?>/cartera" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Volver a Cartera
    </a>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Cartera Vencida</h2>
            <p class="text-gray-600">Pagos vencidos por antigüedad</p>
        </div>
        <a href="<?= BASE_URL ?>/cartera/exportar?tipo=vencida" class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition">
            <i class="fas fa-file-excel mr-2"></i> Exportar a Excel
        </a>
    </div>
</div>

<!-- Rangos -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <a href="<?= BASE_URL ?>/cartera/vencida?rango=1-30" 
       class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition <?= $rangoFilter === '1-30' ? 'ring-2 ring-yellow-500' : '' ?>">
        <p class="text-sm text-gray-500">1-30 días</p>
        <p class="text-2xl font-bold text-yellow-600"><?= $porRango['1-30'] ?></p>
        <p class="text-sm text-gray-500">$<?= number_format($montosPorRango['1-30'], 2) ?></p>
    </a>
    <a href="<?= BASE_URL ?>/cartera/vencida?rango=31-60" 
       class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition <?= $rangoFilter === '31-60' ? 'ring-2 ring-orange-500' : '' ?>">
        <p class="text-sm text-gray-500">31-60 días</p>
        <p class="text-2xl font-bold text-orange-600"><?= $porRango['31-60'] ?></p>
        <p class="text-sm text-gray-500">$<?= number_format($montosPorRango['31-60'], 2) ?></p>
    </a>
    <a href="<?= BASE_URL ?>/cartera/vencida?rango=61-90" 
       class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition <?= $rangoFilter === '61-90' ? 'ring-2 ring-red-500' : '' ?>">
        <p class="text-sm text-gray-500">61-90 días</p>
        <p class="text-2xl font-bold text-red-600"><?= $porRango['61-90'] ?></p>
        <p class="text-sm text-gray-500">$<?= number_format($montosPorRango['61-90'], 2) ?></p>
    </a>
    <a href="<?= BASE_URL ?>/cartera/vencida?rango=90%2B" 
       class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition <?= $rangoFilter === '90+' ? 'ring-2 ring-red-700' : '' ?>">
        <p class="text-sm text-gray-500">+90 días</p>
        <p class="text-2xl font-bold text-red-800"><?= $porRango['90+'] ?></p>
        <p class="text-sm text-gray-500">$<?= number_format($montosPorRango['90+'], 2) ?></p>
    </a>
</div>

<?php if ($rangoFilter): ?>
<div class="mb-4">
    <a href="<?= BASE_URL ?>/cartera/vencida" class="text-sm text-blue-600 hover:text-blue-800">
        <i class="fas fa-times mr-1"></i> Limpiar filtro
    </a>
</div>
<?php endif; ?>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Crédito</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Socio</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contacto</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Pago #</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vencimiento</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Días</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($vencida)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-check-circle text-4xl text-green-500 mb-3"></i>
                        <p>No hay pagos vencidos</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($vencida as $item): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <a href="<?= BASE_URL ?>/creditos/ver/<?= $item['credito_id'] ?? '' ?>" class="text-blue-600 hover:text-blue-800">
                            <?= htmlspecialchars($item['numero_credito'] ?? '') ?>
                        </a>
                        <p class="text-xs text-gray-500"><?= htmlspecialchars($item['tipo_credito'] ?? '') ?></p>
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-medium"><?= htmlspecialchars($item['nombre_socio'] ?? 'Sin nombre') ?></p>
                        <p class="text-xs text-gray-500"><?= htmlspecialchars($item['numero_socio'] ?? '') ?></p>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <?php if (!empty($item['telefono'])): ?><p><i class="fas fa-phone text-gray-400 mr-1"></i><?= htmlspecialchars($item['telefono']) ?></p><?php endif; ?>
                        <?php if (!empty($item['celular'])): ?><p><i class="fas fa-mobile text-gray-400 mr-1"></i><?= htmlspecialchars($item['celular']) ?></p><?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-center"><?= $item['numero_pago'] ?? 0 ?></td>
                    <td class="px-4 py-3 text-sm text-red-600">
                        <?= !empty($item['fecha_vencimiento']) ? date('d/m/Y', strtotime($item['fecha_vencimiento'])) : '-' ?>
                    </td>
                    <td class="px-4 py-3 text-right font-medium">$<?= number_format($item['monto_vencido'] ?? 0, 2) ?></td>
                    <td class="px-4 py-3 text-center">
                        <?php $diasVencido = $item['dias_vencido'] ?? 0; ?>
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            <?= $diasVencido > 90 ? 'bg-red-200 text-red-800' : 
                                ($diasVencido > 60 ? 'bg-red-100 text-red-700' : 
                                ($diasVencido > 30 ? 'bg-orange-100 text-orange-700' : 'bg-yellow-100 text-yellow-700')) ?>">
                            <?= $diasVencido ?> días
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
