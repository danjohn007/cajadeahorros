<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-purple-500">
        <p class="text-sm text-gray-500">Cartera Total</p>
        <p class="text-2xl font-bold text-gray-800">$<?= number_format($stats['carteraTotal'], 2) ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-500">
        <p class="text-sm text-gray-500">Créditos Activos</p>
        <p class="text-2xl font-bold text-gray-800"><?= $stats['creditosActivos'] ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-red-500">
        <p class="text-sm text-gray-500">Cartera Vencida</p>
        <p class="text-2xl font-bold text-red-600">$<?= number_format($stats['carteraVencida'], 2) ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-yellow-500">
        <p class="text-sm text-gray-500">Socios en Mora</p>
        <p class="text-2xl font-bold text-yellow-600"><?= $stats['sociosMora'] ?></p>
    </div>
</div>

<!-- Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Cartera de Créditos</h2>
        <p class="text-gray-600">Análisis y seguimiento de cartera</p>
    </div>
    <div class="mt-4 md:mt-0 flex space-x-2">
        <a href="<?= BASE_URL ?>/cartera/vencida" class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">
            <i class="fas fa-exclamation-triangle mr-2"></i> Cartera Vencida
        </a>
        <a href="<?= BASE_URL ?>/cartera/mora" class="inline-flex items-center px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition">
            <i class="fas fa-users mr-2"></i> Socios en Mora
        </a>
        <a href="<?= BASE_URL ?>/cartera/exportar?tipo=cartera" class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition">
            <i class="fas fa-file-excel mr-2"></i> Exportar
        </a>
    </div>
</div>

<!-- Indicador de Morosidad -->
<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-gray-800">Índice de Morosidad</h3>
        <span class="text-2xl font-bold <?= $stats['porcentajeVencida'] > 10 ? 'text-red-600' : ($stats['porcentajeVencida'] > 5 ? 'text-yellow-600' : 'text-green-600') ?>">
            <?= $stats['porcentajeVencida'] ?>%
        </span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-4">
        <div class="h-4 rounded-full <?= $stats['porcentajeVencida'] > 10 ? 'bg-red-600' : ($stats['porcentajeVencida'] > 5 ? 'bg-yellow-500' : 'bg-green-500') ?>" 
             style="width: <?= min($stats['porcentajeVencida'], 100) ?>%"></div>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-200">
        <h3 class="font-semibold text-gray-800">Créditos Activos</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Crédito</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Socio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Pagos Vencidos</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($cartera as $item): ?>
                <tr class="hover:bg-gray-50 <?= $item['pagos_vencidos'] > 0 ? 'bg-red-50' : '' ?>">
                    <td class="px-6 py-4">
                        <p class="font-mono font-medium"><?= htmlspecialchars($item['numero_credito']) ?></p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-medium"><?= htmlspecialchars($item['nombre'] . ' ' . $item['apellido_paterno']) ?></p>
                        <p class="text-sm text-gray-500"><?= htmlspecialchars($item['numero_socio']) ?></p>
                    </td>
                    <td class="px-6 py-4 text-sm"><?= htmlspecialchars($item['tipo_credito']) ?></td>
                    <td class="px-6 py-4 text-right font-medium">$<?= number_format($item['saldo_actual'], 2) ?></td>
                    <td class="px-6 py-4 text-center">
                        <?php if ($item['pagos_vencidos'] > 0): ?>
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                            <?= $item['pagos_vencidos'] ?> vencido(s)
                        </span>
                        <?php else: ?>
                        <span class="text-green-600"><i class="fas fa-check"></i></span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="<?= BASE_URL ?>/creditos/ver/<?= $item['id'] ?>" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
