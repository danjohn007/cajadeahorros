<!-- Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Padrón de Socios</h2>
        <p class="text-gray-600">Gestiona la información de los socios de la caja</p>
    </div>
    <?php if (in_array($_SESSION['user_role'], ['administrador', 'operativo'])): ?>
    <a href="<?= BASE_URL ?>/socios/crear" 
       class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
        <i class="fas fa-user-plus mr-2"></i> Nuevo Socio
    </a>
    <?php endif; ?>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" action="<?= BASE_URL ?>/socios" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <div class="relative">
                <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" 
                       placeholder="Buscar por nombre, RFC, CURP o número de socio..."
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
        <div class="md:w-48">
            <select name="estatus" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Todos los estatus</option>
                <option value="activo" <?= $estatus === 'activo' ? 'selected' : '' ?>>Activo</option>
                <option value="inactivo" <?= $estatus === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                <option value="suspendido" <?= $estatus === 'suspendido' ? 'selected' : '' ?>>Suspendido</option>
                <option value="baja" <?= $estatus === 'baja' ? 'selected' : '' ?>>Baja</option>
            </select>
        </div>
        <button type="submit" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
            <i class="fas fa-filter mr-2"></i> Filtrar
        </button>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Socio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RFC / CURP</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unidad de Trabajo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo Ahorro</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estatus</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($socios)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-users text-4xl mb-3 text-gray-300"></i>
                        <p>No se encontraron socios</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($socios as $socio): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-semibold text-sm">
                                    <?= strtoupper(substr($socio['nombre'], 0, 1) . substr($socio['apellido_paterno'], 0, 1)) ?>
                                </span>
                            </div>
                            <div class="ml-3">
                                <p class="font-medium text-gray-900">
                                    <?= htmlspecialchars($socio['nombre'] . ' ' . $socio['apellido_paterno'] . ' ' . ($socio['apellido_materno'] ?? '')) ?>
                                </p>
                                <p class="text-sm text-gray-500"><?= htmlspecialchars($socio['numero_socio']) ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-900"><?= htmlspecialchars($socio['rfc'] ?? '-') ?></p>
                        <p class="text-xs text-gray-500"><?= htmlspecialchars($socio['curp'] ?? '-') ?></p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-900"><?= htmlspecialchars($socio['unidad_trabajo'] ?? '-') ?></p>
                        <p class="text-xs text-gray-500"><?= htmlspecialchars($socio['puesto'] ?? '') ?></p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-medium text-green-600">$<?= number_format($socio['saldo_ahorro'] ?? 0, 2) ?></p>
                        <?php if ($socio['creditos_activos'] > 0): ?>
                        <p class="text-xs text-gray-500"><?= $socio['creditos_activos'] ?> crédito(s) activo(s)</p>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php
                        $statusColors = [
                            'activo' => 'bg-green-100 text-green-800',
                            'inactivo' => 'bg-gray-100 text-gray-800',
                            'suspendido' => 'bg-yellow-100 text-yellow-800',
                            'baja' => 'bg-red-100 text-red-800'
                        ];
                        $color = $statusColors[$socio['estatus']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <span class="px-2 py-1 text-xs font-medium rounded-full <?= $color ?>">
                            <?= ucfirst($socio['estatus']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="<?= BASE_URL ?>/socios/ver/<?= $socio['id'] ?>" 
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Ver detalle">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/socios/estado-cuenta/<?= $socio['id'] ?>" 
                               class="p-2 text-green-600 hover:bg-green-50 rounded-lg" title="Estado de Cuenta">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </a>
                            <?php if (in_array($_SESSION['user_role'], ['administrador', 'operativo'])): ?>
                            <a href="<?= BASE_URL ?>/socios/editar/<?= $socio['id'] ?>" 
                               class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php endif; ?>
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
                Mostrando <?= count($socios) ?> de <?= $total ?> socios
            </p>
            <div class="flex space-x-2">
                <?php if ($page > 1): ?>
                <a href="<?= BASE_URL ?>/socios?page=<?= $page - 1 ?>&q=<?= urlencode($search) ?>&estatus=<?= urlencode($estatus) ?>" 
                   class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                <a href="<?= BASE_URL ?>/socios?page=<?= $i ?>&q=<?= urlencode($search) ?>&estatus=<?= urlencode($estatus) ?>" 
                   class="px-3 py-1 border rounded-lg <?= $i === $page ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 hover:bg-gray-50' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                <a href="<?= BASE_URL ?>/socios?page=<?= $page + 1 ?>&q=<?= urlencode($search) ?>&estatus=<?= urlencode($estatus) ?>" 
                   class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
