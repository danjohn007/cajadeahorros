<!-- Header -->
<div class="mb-6">
    <a href="<?= BASE_URL ?>/cartera" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Volver a Cartera
    </a>
    <h2 class="text-2xl font-bold text-gray-800">Socios en Mora</h2>
    <p class="text-gray-600">Listado para campañas de cobranza</p>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Socio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contacto</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Créditos</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto Adeudado</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Max. Días</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($mora)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-check-circle text-4xl text-green-500 mb-3"></i>
                        <p>No hay socios en mora</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($mora as $item): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <p class="font-medium"><?= htmlspecialchars($item['nombre'] . ' ' . $item['apellido_paterno'] . ' ' . ($item['apellido_materno'] ?? '')) ?></p>
                        <p class="text-sm text-gray-500"><?= htmlspecialchars($item['numero_socio']) ?></p>
                    </td>
                    <td class="px-6 py-4">
                        <div class="space-y-1 text-sm">
                            <?php if ($item['telefono']): ?>
                            <p><i class="fas fa-phone text-gray-400 w-4"></i> <?= htmlspecialchars($item['telefono']) ?></p>
                            <?php endif; ?>
                            <?php if ($item['celular']): ?>
                            <p><i class="fas fa-mobile text-gray-400 w-4"></i> <?= htmlspecialchars($item['celular']) ?></p>
                            <?php endif; ?>
                            <?php if ($item['email']): ?>
                            <p><i class="fas fa-envelope text-gray-400 w-4"></i> <?= htmlspecialchars($item['email']) ?></p>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-lg font-bold"><?= $item['creditos_mora'] ?></span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <p class="text-lg font-bold text-red-600">$<?= number_format($item['monto_adeudado'], 2) ?></p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            <?= $item['max_dias_vencido'] > 90 ? 'bg-red-200 text-red-800' : 
                                ($item['max_dias_vencido'] > 60 ? 'bg-red-100 text-red-700' : 
                                ($item['max_dias_vencido'] > 30 ? 'bg-orange-100 text-orange-700' : 'bg-yellow-100 text-yellow-700')) ?>">
                            <?= $item['max_dias_vencido'] ?> días
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="<?= BASE_URL ?>/socios/ver/<?= $item['id'] ?>" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-eye mr-1"></i> Ver
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
