<?php
/**
 * Vista de Lista de Créditos del Cliente
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
        <p class="text-gray-600">Historial y estado de tus créditos</p>
    </div>
    <a href="<?= url('cliente') ?>" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Volver al Portal
    </a>
</div>

<?php if (empty($creditos)): ?>
    <div class="bg-white rounded-lg shadow-md p-8 text-center">
        <i class="fas fa-file-invoice text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">No tienes créditos registrados</p>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 gap-6">
        <?php foreach ($creditos as $credito): 
            $progreso = $credito['monto_autorizado'] > 0 
                ? (($credito['monto_autorizado'] - $credito['saldo_actual']) / $credito['monto_autorizado']) * 100 
                : 0;
            
            $estatusClases = [
                'activo' => 'bg-green-100 text-green-800',
                'formalizado' => 'bg-blue-100 text-blue-800',
                'liquidado' => 'bg-gray-100 text-gray-800',
                'solicitud' => 'bg-yellow-100 text-yellow-800',
                'en_revision' => 'bg-yellow-100 text-yellow-800',
                'rechazado' => 'bg-red-100 text-red-800'
            ];
            $estatusClase = $estatusClases[$credito['estatus']] ?? 'bg-gray-100 text-gray-800';
        ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">
                                <?= htmlspecialchars($credito['numero_credito']) ?>
                            </h3>
                            <p class="text-sm text-gray-600"><?= htmlspecialchars($credito['tipo_credito']) ?></p>
                        </div>
                        <span class="px-3 py-1 text-sm rounded-full <?= $estatusClase ?>">
                            <?= ucfirst(str_replace('_', ' ', $credito['estatus'])) ?>
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                        <div>
                            <p class="text-xs text-gray-500">Monto Autorizado</p>
                            <p class="text-lg font-semibold text-gray-800">
                                $<?= number_format($credito['monto_autorizado'] ?? 0, 2) ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Saldo Actual</p>
                            <p class="text-lg font-semibold text-red-600">
                                $<?= number_format($credito['saldo_actual'] ?? 0, 2) ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Plazo</p>
                            <p class="text-lg font-semibold text-gray-800">
                                <?= $credito['plazo_meses'] ?> meses
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Cuota Mensual</p>
                            <p class="text-lg font-semibold text-gray-800">
                                $<?= number_format($credito['monto_cuota'] ?? 0, 2) ?>
                            </p>
                        </div>
                    </div>
                    
                    <?php if (in_array($credito['estatus'], ['activo', 'formalizado'])): ?>
                        <div class="mb-4">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Progreso de pago</span>
                                <span class="font-medium"><?= number_format($progreso, 1) ?>%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-green-600 h-3 rounded-full transition-all" style="width: <?= min(100, $progreso) ?>%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>Pagos: <?= $credito['pagos_realizados'] ?>/<?= $credito['plazo_meses'] ?></span>
                                <?php if ($credito['pagos_vencidos'] > 0): ?>
                                    <span class="text-red-600"><?= $credito['pagos_vencidos'] ?> pago(s) vencido(s)</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="flex justify-end space-x-3">
                        <a href="<?= url('cliente/credito/' . $credito['id']) ?>" 
                           class="px-4 py-2 text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-50">
                            <i class="fas fa-eye mr-2"></i>Ver Detalle
                        </a>
                        <?php if (in_array($credito['estatus'], ['activo', 'formalizado'])): ?>
                            <a href="<?= url('cliente/amortizacion/' . $credito['id']) ?>" 
                               class="px-4 py-2 text-purple-600 border border-purple-600 rounded-lg hover:bg-purple-50">
                                <i class="fas fa-table mr-2"></i>Tabla de Amortización
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
