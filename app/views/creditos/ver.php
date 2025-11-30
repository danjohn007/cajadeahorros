<!-- Header -->
<div class="mb-6">
    <a href="<?= BASE_URL ?>/creditos" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Volver a Créditos
    </a>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($credito['numero_credito']) ?></h2>
            <p class="text-gray-600"><?= htmlspecialchars($credito['tipo_credito']) ?></p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-2">
            <?php if (in_array($credito['estatus'], ['solicitud', 'en_revision']) && $_SESSION['user_role'] === 'administrador'): ?>
            <a href="<?= BASE_URL ?>/creditos/autorizar/<?= $credito['id'] ?>" 
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-check mr-2"></i> Revisar / Autorizar
            </a>
            <?php endif; ?>
            <?php if ($credito['estatus'] === 'activo'): ?>
            <a href="<?= BASE_URL ?>/creditos/pago/<?= $credito['id'] ?>" 
               class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                <i class="fas fa-dollar-sign mr-2"></i> Registrar Pago
            </a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/creditos/amortizacion/<?= $credito['id'] ?>" 
               class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                <i class="fas fa-table mr-2"></i> Amortización
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Info -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Datos del Crédito -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-file-invoice-dollar text-purple-500 mr-2"></i> Información del Crédito
            </h3>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="text-sm text-gray-500">Monto Solicitado</label>
                    <p class="font-medium">$<?= number_format($credito['monto_solicitado'], 2) ?></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Monto Autorizado</label>
                    <p class="font-medium">$<?= number_format($credito['monto_autorizado'] ?? 0, 2) ?></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Tasa de Interés</label>
                    <p class="font-medium"><?= number_format($credito['tasa_interes'] * 100, 2) ?>% mensual</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Plazo</label>
                    <p class="font-medium"><?= $credito['plazo_meses'] ?> meses</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Cuota Mensual</label>
                    <p class="font-medium text-purple-600">$<?= number_format($credito['monto_cuota'] ?? 0, 2) ?></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Saldo Actual</label>
                    <p class="font-medium text-lg">$<?= number_format($credito['saldo_actual'] ?? 0, 2) ?></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Pagos Realizados</label>
                    <p class="font-medium"><?= $credito['pagos_realizados'] ?> de <?= $credito['plazo_meses'] ?></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Estatus</label>
                    <?php
                    $statusColors = [
                        'solicitud' => 'bg-yellow-100 text-yellow-800',
                        'en_revision' => 'bg-blue-100 text-blue-800',
                        'autorizado' => 'bg-green-100 text-green-800',
                        'activo' => 'bg-green-100 text-green-800',
                        'liquidado' => 'bg-gray-100 text-gray-800',
                        'rechazado' => 'bg-red-100 text-red-800'
                    ];
                    $color = $statusColors[$credito['estatus']] ?? 'bg-gray-100 text-gray-800';
                    ?>
                    <span class="px-2 py-1 text-xs font-medium rounded-full <?= $color ?>">
                        <?= ucfirst(str_replace('_', ' ', $credito['estatus'])) ?>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Fechas -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-calendar text-blue-500 mr-2"></i> Fechas Importantes
            </h3>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="text-sm text-gray-500">Solicitud</label>
                    <p class="font-medium"><?= date('d/m/Y', strtotime($credito['fecha_solicitud'])) ?></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Autorización</label>
                    <p class="font-medium"><?= $credito['fecha_autorizacion'] ? date('d/m/Y', strtotime($credito['fecha_autorizacion'])) : '-' ?></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Primer Pago</label>
                    <p class="font-medium"><?= $credito['fecha_primer_pago'] ? date('d/m/Y', strtotime($credito['fecha_primer_pago'])) : '-' ?></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Último Pago</label>
                    <p class="font-medium"><?= $credito['fecha_ultimo_pago'] ? date('d/m/Y', strtotime($credito['fecha_ultimo_pago'])) : '-' ?></p>
                </div>
            </div>
            
            <?php if ($credito['autorizado_nombre']): ?>
            <div class="mt-4 pt-4 border-t">
                <p class="text-sm text-gray-500">
                    <i class="fas fa-user-check mr-1"></i>
                    Autorizado por: <span class="font-medium"><?= htmlspecialchars($credito['autorizado_nombre']) ?></span>
                </p>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Tabla de Amortización Preview -->
        <?php if (!empty($amortizacion)): ?>
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-table text-gray-500 mr-2"></i> Próximos Pagos
                    </h3>
                    <a href="<?= BASE_URL ?>/creditos/amortizacion/<?= $credito['id'] ?>" class="text-blue-600 hover:text-blue-800 text-sm">
                        Ver tabla completa
                    </a>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">No.</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Vencimiento</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Cuota</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Estatus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php 
                        $mostrados = 0;
                        foreach ($amortizacion as $pago): 
                            if ($pago['estatus'] === 'pagado') continue;
                            if ($mostrados >= 5) break;
                            $mostrados++;
                        ?>
                        <tr class="<?= strtotime($pago['fecha_vencimiento']) < time() && $pago['estatus'] !== 'pagado' ? 'bg-red-50' : '' ?>">
                            <td class="px-4 py-2 text-sm"><?= $pago['numero_pago'] ?></td>
                            <td class="px-4 py-2 text-sm"><?= date('d/m/Y', strtotime($pago['fecha_vencimiento'])) ?></td>
                            <td class="px-4 py-2 text-sm text-right font-medium">$<?= number_format($pago['monto_total'], 2) ?></td>
                            <td class="px-4 py-2">
                                <?php
                                $pagoColor = [
                                    'pendiente' => 'bg-yellow-100 text-yellow-800',
                                    'pagado' => 'bg-green-100 text-green-800',
                                    'vencido' => 'bg-red-100 text-red-800'
                                ];
                                $pColor = $pagoColor[$pago['estatus']] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full <?= $pColor ?>">
                                    <?= ucfirst($pago['estatus']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Pagos Realizados -->
        <?php if (!empty($pagos)): ?>
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-history text-green-500 mr-2"></i> Historial de Pagos
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Referencia</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($pagos as $pago): ?>
                        <tr>
                            <td class="px-4 py-2 text-sm"><?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?></td>
                            <td class="px-4 py-2 text-sm text-right font-medium text-green-600">$<?= number_format($pago['monto'], 2) ?></td>
                            <td class="px-4 py-2 text-sm"><?= htmlspecialchars($pago['referencia'] ?? '-') ?></td>
                            <td class="px-4 py-2 text-sm"><?= htmlspecialchars($pago['usuario_nombre'] ?? '-') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Socio Info -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-user text-blue-500 mr-2"></i> Datos del Socio
            </h3>
            
            <div class="text-center mb-4">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-blue-600 font-bold text-xl">
                        <?= strtoupper(substr($credito['nombre'], 0, 1) . substr($credito['apellido_paterno'], 0, 1)) ?>
                    </span>
                </div>
                <p class="font-medium"><?= htmlspecialchars($credito['nombre'] . ' ' . $credito['apellido_paterno']) ?></p>
                <p class="text-sm text-gray-500"><?= htmlspecialchars($credito['numero_socio']) ?></p>
            </div>
            
            <div class="space-y-2 text-sm">
                <?php if ($credito['telefono']): ?>
                <div class="flex items-center">
                    <i class="fas fa-phone text-gray-400 w-5"></i>
                    <span><?= htmlspecialchars($credito['telefono']) ?></span>
                </div>
                <?php endif; ?>
                <?php if ($credito['email']): ?>
                <div class="flex items-center">
                    <i class="fas fa-envelope text-gray-400 w-5"></i>
                    <span><?= htmlspecialchars($credito['email']) ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="border-t pt-4 mt-4">
                <a href="<?= BASE_URL ?>/socios/ver/<?= $credito['socio_id'] ?>" 
                   class="block text-center text-blue-600 hover:text-blue-800">
                    Ver perfil completo
                </a>
            </div>
        </div>
        
        <!-- Observaciones -->
        <?php if ($credito['observaciones']): ?>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-sticky-note text-yellow-500 mr-2"></i> Observaciones
            </h3>
            <p class="text-sm text-gray-600"><?= nl2br(htmlspecialchars($credito['observaciones'])) ?></p>
        </div>
        <?php endif; ?>
        
        <!-- Requisitos -->
        <?php if ($credito['requisitos']): ?>
        <div class="bg-yellow-50 rounded-xl p-6">
            <h3 class="font-semibold text-yellow-800 mb-2">
                <i class="fas fa-clipboard-list mr-1"></i> Requisitos
            </h3>
            <p class="text-sm text-yellow-700"><?= nl2br(htmlspecialchars($credito['requisitos'])) ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>
