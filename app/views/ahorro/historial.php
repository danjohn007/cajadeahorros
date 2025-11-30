<!-- Header -->
<div class="mb-6">
    <a href="<?= BASE_URL ?>/ahorro/socio/<?= $cuenta['socio_id'] ?? '' ?>" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Volver
    </a>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Historial de Movimientos</h2>
            <p class="text-gray-600">
                <?= htmlspecialchars($cuenta['nombre'] . ' ' . $cuenta['apellido_paterno']) ?> - 
                <?= htmlspecialchars($cuenta['numero_cuenta']) ?>
            </p>
        </div>
        <div class="mt-4 md:mt-0">
            <span class="text-sm text-gray-500">Saldo Actual:</span>
            <span class="text-xl font-bold text-green-600 ml-2">$<?= number_format($cuenta['saldo'], 2) ?></span>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" action="<?= BASE_URL ?>/ahorro/historial/<?= $cuenta['id'] ?>" class="flex flex-wrap gap-4">
        <div>
            <label class="block text-sm text-gray-600 mb-1">Fecha Inicio</label>
            <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($fechaInicio) ?>"
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Fecha Fin</label>
            <input type="date" name="fecha_fin" value="<?= htmlspecialchars($fechaFin) ?>"
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Tipo</label>
            <select name="tipo" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                <option value="">Todos</option>
                <option value="aportacion" <?= $tipo === 'aportacion' ? 'selected' : '' ?>>Aportaciones</option>
                <option value="retiro" <?= $tipo === 'retiro' ? 'selected' : '' ?>>Retiros</option>
                <option value="interes" <?= $tipo === 'interes' ? 'selected' : '' ?>>Intereses</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                <i class="fas fa-filter mr-2"></i> Filtrar
            </button>
        </div>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Concepto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Origen</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($movimientos)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                        <p>No hay movimientos que coincidan con los filtros</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($movimientos as $mov): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm">
                        <?= date('d/m/Y H:i', strtotime($mov['fecha'])) ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php
                        $tipoColors = [
                            'aportacion' => 'bg-green-100 text-green-800',
                            'retiro' => 'bg-red-100 text-red-800',
                            'interes' => 'bg-blue-100 text-blue-800',
                            'ajuste' => 'bg-yellow-100 text-yellow-800'
                        ];
                        $tColor = $tipoColors[$mov['tipo']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <span class="px-2 py-1 text-xs font-medium rounded-full <?= $tColor ?>">
                            <?= ucfirst($mov['tipo']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-900"><?= htmlspecialchars($mov['concepto'] ?? '-') ?></p>
                        <?php if ($mov['referencia']): ?>
                        <p class="text-xs text-gray-500">Ref: <?= htmlspecialchars($mov['referencia']) ?></p>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <p class="text-gray-600"><?= ucfirst($mov['origen']) ?></p>
                        <?php if ($mov['usuario_nombre']): ?>
                        <p class="text-xs text-gray-500"><?= htmlspecialchars($mov['usuario_nombre']) ?></p>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="font-medium <?= $mov['tipo'] === 'retiro' ? 'text-red-600' : 'text-green-600' ?>">
                            <?= $mov['tipo'] === 'retiro' ? '-' : '+' ?>$<?= number_format($mov['monto'], 2) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-medium">
                        $<?= number_format($mov['saldo_nuevo'], 2) ?>
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
                Mostrando <?= count($movimientos) ?> de <?= $total ?> movimientos
            </p>
            <div class="flex space-x-2">
                <?php if ($page > 1): ?>
                <a href="<?= BASE_URL ?>/ahorro/historial/<?= $cuenta['id'] ?>?page=<?= $page - 1 ?>&fecha_inicio=<?= urlencode($fechaInicio) ?>&fecha_fin=<?= urlencode($fechaFin) ?>&tipo=<?= urlencode($tipo) ?>" 
                   class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                <a href="<?= BASE_URL ?>/ahorro/historial/<?= $cuenta['id'] ?>?page=<?= $i ?>&fecha_inicio=<?= urlencode($fechaInicio) ?>&fecha_fin=<?= urlencode($fechaFin) ?>&tipo=<?= urlencode($tipo) ?>" 
                   class="px-3 py-1 border rounded-lg <?= $i === $page ? 'bg-green-600 text-white border-green-600' : 'border-gray-300 hover:bg-gray-50' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                <a href="<?= BASE_URL ?>/ahorro/historial/<?= $cuenta['id'] ?>?page=<?= $page + 1 ?>&fecha_inicio=<?= urlencode($fechaInicio) ?>&fecha_fin=<?= urlencode($fechaFin) ?>&tipo=<?= urlencode($tipo) ?>" 
                   class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
