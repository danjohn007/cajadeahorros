<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-purple-500">
        <p class="text-sm text-gray-500">Cartera Total</p>
        <p class="text-2xl font-bold text-gray-800">$<?= number_format($stats['carteraTotal'], 2) ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-500">
        <p class="text-sm text-gray-500">Créditos Activos</p>
        <p class="text-2xl font-bold text-gray-800"><?= number_format($stats['creditosActivos']) ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-yellow-500">
        <p class="text-sm text-gray-500">Solicitudes Pendientes</p>
        <p class="text-2xl font-bold text-yellow-600"><?= number_format($stats['solicitudesPendientes']) ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-red-500">
        <p class="text-sm text-gray-500">Cartera Vencida</p>
        <p class="text-2xl font-bold text-red-600">$<?= number_format($stats['carteraVencida'], 2) ?></p>
    </div>
</div>

<!-- Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Gestión de Créditos</h2>
        <p class="text-gray-600">Administra solicitudes, autorizaciones y seguimiento de créditos</p>
    </div>
    <?php if (in_array($_SESSION['user_role'], ['administrador', 'operativo'])): ?>
    <a href="<?= BASE_URL ?>/creditos/solicitud" 
       class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
        <i class="fas fa-plus mr-2"></i> Nueva Solicitud
    </a>
    <?php endif; ?>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" action="<?= BASE_URL ?>/creditos" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" 
                   placeholder="Buscar por nombre o número de crédito..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
        </div>
        <div class="w-48">
            <select name="tipo" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                <option value="">Todos los tipos</option>
                <?php foreach ($tiposCredito as $tc): ?>
                <option value="<?= $tc['id'] ?>" <?= $tipo == $tc['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($tc['nombre']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="w-48">
            <select name="estatus" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                <option value="">Todos los estatus</option>
                <option value="solicitud" <?= $estatus === 'solicitud' ? 'selected' : '' ?>>Solicitud</option>
                <option value="en_revision" <?= $estatus === 'en_revision' ? 'selected' : '' ?>>En Revisión</option>
                <option value="autorizado" <?= $estatus === 'autorizado' ? 'selected' : '' ?>>Autorizado</option>
                <option value="activo" <?= $estatus === 'activo' ? 'selected' : '' ?>>Activo</option>
                <option value="liquidado" <?= $estatus === 'liquidado' ? 'selected' : '' ?>>Liquidado</option>
                <option value="rechazado" <?= $estatus === 'rechazado' ? 'selected' : '' ?>>Rechazado</option>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Crédito</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Socio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estatus</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($creditos)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-hand-holding-usd text-4xl mb-3 text-gray-300"></i>
                        <p>No se encontraron créditos</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($creditos as $credito): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <p class="font-mono font-medium"><?= htmlspecialchars($credito['numero_credito']) ?></p>
                        <p class="text-xs text-gray-500"><?= date('d/m/Y', strtotime($credito['fecha_solicitud'])) ?></p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-900">
                            <?= htmlspecialchars($credito['nombre'] . ' ' . $credito['apellido_paterno']) ?>
                        </p>
                        <p class="text-sm text-gray-500"><?= htmlspecialchars($credito['numero_socio']) ?></p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm"><?= htmlspecialchars($credito['tipo_credito']) ?></p>
                        <p class="text-xs text-gray-500"><?= $credito['plazo_meses'] ?> meses</p>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <p class="font-medium">$<?= number_format($credito['monto_autorizado'] ?? $credito['monto_solicitado'], 2) ?></p>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <p class="font-medium text-purple-600">$<?= number_format($credito['saldo_actual'] ?? 0, 2) ?></p>
                    </td>
                    <td class="px-6 py-4">
                        <?php
                        $statusColors = [
                            'solicitud' => 'bg-yellow-100 text-yellow-800',
                            'en_revision' => 'bg-blue-100 text-blue-800',
                            'autorizado' => 'bg-green-100 text-green-800',
                            'activo' => 'bg-green-100 text-green-800',
                            'formalizado' => 'bg-green-100 text-green-800',
                            'liquidado' => 'bg-gray-100 text-gray-800',
                            'rechazado' => 'bg-red-100 text-red-800',
                            'castigado' => 'bg-red-100 text-red-800'
                        ];
                        $color = $statusColors[$credito['estatus']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <span class="px-2 py-1 text-xs font-medium rounded-full <?= $color ?>">
                            <?= ucfirst(str_replace('_', ' ', $credito['estatus'])) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="<?= BASE_URL ?>/creditos/ver/<?= $credito['id'] ?>" 
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Ver detalle">
                                <i class="fas fa-eye"></i>
                            </a>
                            <?php if (in_array($credito['estatus'], ['solicitud', 'en_revision']) && $_SESSION['user_role'] === 'administrador'): ?>
                            <a href="<?= BASE_URL ?>/creditos/autorizar/<?= $credito['id'] ?>" 
                               class="p-2 text-green-600 hover:bg-green-50 rounded-lg" title="Autorizar">
                                <i class="fas fa-check"></i>
                            </a>
                            <?php endif; ?>
                            <?php if ($credito['estatus'] === 'activo'): ?>
                            <a href="<?= BASE_URL ?>/creditos/pago/<?= $credito['id'] ?>" 
                               class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg" title="Registrar pago">
                                <i class="fas fa-dollar-sign"></i>
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
                Mostrando <?= count($creditos) ?> de <?= $total ?> créditos
            </p>
            <div class="flex space-x-2">
                <?php if ($page > 1): ?>
                <a href="<?= BASE_URL ?>/creditos?page=<?= $page - 1 ?>&q=<?= urlencode($search) ?>&estatus=<?= urlencode($estatus) ?>&tipo=<?= urlencode($tipo) ?>" 
                   class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                <a href="<?= BASE_URL ?>/creditos?page=<?= $i ?>&q=<?= urlencode($search) ?>&estatus=<?= urlencode($estatus) ?>&tipo=<?= urlencode($tipo) ?>" 
                   class="px-3 py-1 border rounded-lg <?= $i === $page ? 'bg-purple-600 text-white border-purple-600' : 'border-gray-300 hover:bg-gray-50' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                <a href="<?= BASE_URL ?>/creditos?page=<?= $page + 1 ?>&q=<?= urlencode($search) ?>&estatus=<?= urlencode($estatus) ?>&tipo=<?= urlencode($tipo) ?>" 
                   class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
