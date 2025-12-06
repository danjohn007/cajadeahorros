<!-- Header -->
<div class="mb-6">
    <a href="<?= BASE_URL ?>/ahorro" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Volver a Ahorro
    </a>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                <?= htmlspecialchars($socio['nombre'] . ' ' . $socio['apellido_paterno'] . ' ' . ($socio['apellido_materno'] ?? '')) ?>
            </h2>
            <p class="text-gray-600"><?= htmlspecialchars($socio['numero_socio']) ?> - <?= htmlspecialchars($socio['unidad_trabajo'] ?? 'Sin unidad') ?></p>
        </div>
        <?php if (in_array($_SESSION['user_role'], ['administrador', 'operativo']) && $cuenta): ?>
        <div class="mt-4 md:mt-0 flex gap-2">
            <a href="<?= BASE_URL ?>/ahorro/cardex/<?= $cuenta['id'] ?>" 
               target="_blank"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-print mr-2"></i> Imprimir Cardex de Socio
            </a>
            <a href="<?= BASE_URL ?>/ahorro/movimiento?socio=<?= $socio['id'] ?>" 
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-exchange-alt mr-2"></i> Nuevo Movimiento
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Cuenta Info -->
    <div class="lg:col-span-1">
        <?php if ($cuenta): ?>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-wallet text-3xl text-green-600"></i>
                </div>
                <p class="text-sm text-gray-500 mb-1">Saldo Actual</p>
                <p class="text-4xl font-bold text-green-600">$<?= number_format($cuenta['saldo'], 2) ?></p>
                <p class="text-sm text-gray-500 mt-2"><?= $cuenta['numero_cuenta'] ?></p>
            </div>
            
            <div class="border-t pt-4 space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500">Tasa de Interés</span>
                    <span class="font-medium"><?= number_format($cuenta['tasa_interes'] * 100, 2) ?>% anual</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Fecha Apertura</span>
                    <span class="font-medium"><?= date('d/m/Y', strtotime($cuenta['fecha_apertura'])) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Estatus</span>
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
                </div>
                <?php if (!empty($socio['asesor_nombre'])): ?>
                <div class="flex justify-between">
                    <span class="text-gray-500">Asesor</span>
                    <span class="font-medium"><?= htmlspecialchars($socio['asesor_nombre']) ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="border-t pt-4 mt-4">
                <a href="<?= BASE_URL ?>/ahorro/historial/<?= $cuenta['id'] ?>" 
                   class="block text-center text-blue-600 hover:text-blue-800">
                    <i class="fas fa-history mr-1"></i> Ver historial completo
                </a>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="bg-white rounded-xl shadow-sm p-6 mt-6">
            <h3 class="font-semibold text-gray-800 mb-4">Resumen del Mes</h3>
            <?php
            $resumen = $this->db->fetch(
                "SELECT 
                    COALESCE(SUM(CASE WHEN tipo = 'aportacion' THEN monto ELSE 0 END), 0) as aportaciones,
                    COALESCE(SUM(CASE WHEN tipo = 'retiro' THEN monto ELSE 0 END), 0) as retiros,
                    COUNT(*) as movimientos
                 FROM movimientos_ahorro 
                 WHERE cuenta_id = :cuenta_id 
                 AND MONTH(fecha) = MONTH(CURDATE()) 
                 AND YEAR(fecha) = YEAR(CURDATE())",
                ['cuenta_id' => $cuenta['id']]
            );
            ?>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500">Aportaciones</span>
                    <span class="font-medium text-green-600">+$<?= number_format($resumen['aportaciones'], 2) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Retiros</span>
                    <span class="font-medium text-red-600">-$<?= number_format($resumen['retiros'], 2) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Movimientos</span>
                    <span class="font-medium"><?= $resumen['movimientos'] ?></span>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="bg-white rounded-xl shadow-sm p-6 text-center">
            <i class="fas fa-exclamation-circle text-4xl text-yellow-500 mb-4"></i>
            <p class="text-gray-600">Este socio no tiene cuenta de ahorro</p>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Movimientos -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-history text-gray-500 mr-2"></i> Últimos Movimientos
                </h3>
            </div>
            
            <?php if (empty($movimientos)): ?>
            <div class="p-12 text-center text-gray-500">
                <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                <p>No hay movimientos registrados</p>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Concepto</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($movimientos as $mov): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-600">
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
                            <td class="px-6 py-4 text-sm">
                                <p class="text-gray-900"><?= htmlspecialchars($mov['concepto'] ?? '-') ?></p>
                                <p class="text-xs text-gray-500">
                                    <?= ucfirst($mov['origen']) ?> 
                                    <?php if ($mov['usuario_nombre']): ?>
                                    - <?= htmlspecialchars($mov['usuario_nombre']) ?>
                                    <?php endif; ?>
                                </p>
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
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
