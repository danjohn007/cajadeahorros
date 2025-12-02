<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-500">
        <p class="text-sm text-gray-500">Saldo Total Ahorro</p>
        <p class="text-2xl font-bold text-gray-800">$<?= number_format($stats['saldoTotal'], 2) ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-500">
        <p class="text-sm text-gray-500">Cuentas Activas</p>
        <p class="text-2xl font-bold text-gray-800"><?= number_format($stats['totalCuentas']) ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-purple-500">
        <p class="text-sm text-gray-500">Aportaciones del Mes</p>
        <p class="text-2xl font-bold text-green-600">+$<?= number_format($stats['aportacionesMes'], 2) ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-orange-500">
        <p class="text-sm text-gray-500">Retiros del Mes</p>
        <p class="text-2xl font-bold text-red-600">-$<?= number_format($stats['retirosMes'], 2) ?></p>
    </div>
</div>

<!-- Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Cuentas de Ahorro</h2>
        <p class="text-gray-600">Administra las cuentas y movimientos de ahorro</p>
    </div>
    <?php if (in_array($_SESSION['user_role'], ['administrador', 'operativo'])): ?>
    <a href="<?= BASE_URL ?>/ahorro/movimiento" 
       class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
        <i class="fas fa-exchange-alt mr-2"></i> Nuevo Movimiento
    </a>
    <?php endif; ?>
</div>

<!-- Search -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" action="<?= BASE_URL ?>/ahorro" class="flex gap-4">
        <div class="flex-1">
            <div class="relative">
                <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" 
                       placeholder="Buscar por nombre, número de cuenta o número de socio..."
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
        <button type="submit" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
            <i class="fas fa-search mr-2"></i> Buscar
        </button>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Socio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cuenta</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Último Movimiento</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estatus</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($cuentas)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-wallet text-4xl mb-3 text-gray-300"></i>
                        <p>No se encontraron cuentas</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($cuentas as $cuenta): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-wallet text-green-600"></i>
                            </div>
                            <div class="ml-3">
                                <p class="font-medium text-gray-900">
                                    <?= htmlspecialchars($cuenta['nombre'] . ' ' . $cuenta['apellido_paterno']) ?>
                                </p>
                                <p class="text-sm text-gray-500"><?= htmlspecialchars($cuenta['numero_socio']) ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-mono text-sm"><?= htmlspecialchars($cuenta['numero_cuenta']) ?></p>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <p class="text-lg font-bold text-green-600">$<?= number_format($cuenta['saldo'], 2) ?></p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-600">
                            <?= $cuenta['ultimo_movimiento'] ? date('d/m/Y', strtotime($cuenta['ultimo_movimiento'])) : '-' ?>
                        </p>
                    </td>
                    <td class="px-6 py-4">
                        <?php
                        $statusColors = [
                            'activa' => 'bg-green-100 text-green-800',
                            'inactiva' => 'bg-gray-100 text-gray-800',
                            'bloqueada' => 'bg-red-100 text-red-800'
                        ];
                        $color = $statusColors[$cuenta['estatus']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <span class="px-2 py-1 text-xs font-medium rounded-full <?= $color ?>">
                            <?= ucfirst($cuenta['estatus']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="<?= BASE_URL ?>/ahorro/socio/<?= $cuenta['socio_id'] ?>" 
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Ver detalle">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/ahorro/historial/<?= $cuenta['id'] ?>" 
                               class="p-2 text-gray-600 hover:bg-gray-50 rounded-lg" title="Historial">
                                <i class="fas fa-history"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/ahorro/cardex/<?= $cuenta['id'] ?>" 
                               class="p-2 text-green-600 hover:bg-green-50 rounded-lg" title="Imprimir Cardex del Socio">
                                <i class="fas fa-print"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="px-6 py-4 border-t border-gray-200">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-600">
                Mostrando <?= count($cuentas) ?> de <?= $total ?> cuentas
            </p>
            <div class="flex space-x-2">
                <?php if ($page > 1): ?>
                <a href="<?= BASE_URL ?>/ahorro?page=<?= $page - 1 ?>&q=<?= urlencode($search) ?>" 
                   class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                <a href="<?= BASE_URL ?>/ahorro?page=<?= $i ?>&q=<?= urlencode($search) ?>" 
                   class="px-3 py-1 border rounded-lg <?= $i === $page ? 'bg-green-600 text-white border-green-600' : 'border-gray-300 hover:bg-gray-50' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                <a href="<?= BASE_URL ?>/ahorro?page=<?= $page + 1 ?>&q=<?= urlencode($search) ?>" 
                   class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
