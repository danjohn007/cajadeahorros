<?php
/**
 * Vista de detalle de transacción ESCROW
 * Sistema de Gestión Integral de Caja de Ahorros
 */
$saldoRetenido = $transaccion['monto_total'] - $transaccion['monto_liberado'];
$progreso = $transaccion['monto_total'] > 0 ? ($transaccion['monto_liberado'] / $transaccion['monto_total']) * 100 : 0;

$estatusColores = [
    'borrador' => 'bg-gray-100 text-gray-800',
    'pendiente_deposito' => 'bg-yellow-100 text-yellow-800',
    'fondos_depositados' => 'bg-blue-100 text-blue-800',
    'en_proceso' => 'bg-indigo-100 text-indigo-800',
    'entrega_confirmada' => 'bg-purple-100 text-purple-800',
    'liberado' => 'bg-green-100 text-green-800',
    'disputa' => 'bg-red-100 text-red-800',
    'cancelado' => 'bg-gray-100 text-gray-800',
    'reembolsado' => 'bg-orange-100 text-orange-800'
];
$colorClass = $estatusColores[$transaccion['estatus']] ?? 'bg-gray-100 text-gray-800';
?>

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
    <div>
        <div class="flex items-center gap-3 mb-2">
            <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($transaccion['numero_transaccion']) ?></h1>
            <span class="px-3 py-1 rounded-full text-sm font-semibold <?= $colorClass ?>">
                <?= ucfirst(str_replace('_', ' ', $transaccion['estatus'])) ?>
            </span>
        </div>
        <p class="text-gray-600"><?= htmlspecialchars($transaccion['titulo']) ?></p>
    </div>
    <div class="mt-4 md:mt-0 flex gap-2">
        <a href="<?= url('escrow') ?>" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Volver
        </a>
        <?php if ($transaccion['estatus'] === 'borrador'): ?>
        <a href="<?= url('escrow/editar/' . $transaccion['id']) ?>" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
            <i class="fas fa-edit mr-2"></i>Editar
        </a>
        <?php endif; ?>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Panel Principal -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Información General -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-info-circle mr-2 text-blue-600"></i>Información General
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm text-gray-500">Tipo de Transacción</label>
                    <p class="font-medium"><?= ucfirst($transaccion['tipo']) ?></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Fecha de Creación</label>
                    <p class="font-medium"><?= date('d/m/Y H:i', strtotime($transaccion['fecha_creacion'])) ?></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Fecha Límite</label>
                    <p class="font-medium"><?= $transaccion['fecha_limite'] ? date('d/m/Y', strtotime($transaccion['fecha_limite'])) : 'No definida' ?></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Creado por</label>
                    <p class="font-medium"><?= htmlspecialchars($transaccion['creador_nombre'] ?? 'Sistema') ?></p>
                </div>
            </div>
            
            <?php if ($transaccion['descripcion']): ?>
            <div class="mt-4 pt-4 border-t">
                <label class="text-sm text-gray-500">Descripción</label>
                <p class="mt-1"><?= nl2br(htmlspecialchars($transaccion['descripcion'])) ?></p>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Participantes -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-shopping-cart mr-2 text-blue-600"></i>Comprador
                </h3>
                <p class="font-medium text-lg"><?= htmlspecialchars($transaccion['comprador']) ?></p>
                <?php if ($transaccion['comprador_numero_socio']): ?>
                <p class="text-sm text-gray-500">Socio: <?= htmlspecialchars($transaccion['comprador_numero_socio']) ?></p>
                <?php endif; ?>
                <?php if ($transaccion['comprador_email']): ?>
                <p class="text-sm text-gray-500 mt-2"><i class="fas fa-envelope mr-2"></i><?= htmlspecialchars($transaccion['comprador_email']) ?></p>
                <?php endif; ?>
                <?php if ($transaccion['comprador_telefono']): ?>
                <p class="text-sm text-gray-500"><i class="fas fa-phone mr-2"></i><?= htmlspecialchars($transaccion['comprador_telefono']) ?></p>
                <?php endif; ?>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-store mr-2 text-green-600"></i>Vendedor
                </h3>
                <p class="font-medium text-lg"><?= htmlspecialchars($transaccion['vendedor']) ?></p>
                <?php if ($transaccion['vendedor_numero_socio']): ?>
                <p class="text-sm text-gray-500">Socio: <?= htmlspecialchars($transaccion['vendedor_numero_socio']) ?></p>
                <?php endif; ?>
                <?php if ($transaccion['vendedor_email']): ?>
                <p class="text-sm text-gray-500 mt-2"><i class="fas fa-envelope mr-2"></i><?= htmlspecialchars($transaccion['vendedor_email']) ?></p>
                <?php endif; ?>
                <?php if ($transaccion['vendedor_telefono']): ?>
                <p class="text-sm text-gray-500"><i class="fas fa-phone mr-2"></i><?= htmlspecialchars($transaccion['vendedor_telefono']) ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Hitos -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-tasks mr-2 text-purple-600"></i>Hitos / Milestones
                </h2>
                <?php if (in_array($transaccion['estatus'], ['borrador', 'pendiente_deposito', 'fondos_depositados', 'en_proceso'])): ?>
                <button onclick="document.getElementById('modalHito').classList.remove('hidden')" 
                        class="text-sm bg-purple-600 text-white px-3 py-1 rounded hover:bg-purple-700">
                    <i class="fas fa-plus mr-1"></i>Agregar Hito
                </button>
                <?php endif; ?>
            </div>
            
            <?php if (empty($hitos)): ?>
            <p class="text-gray-500 text-center py-8">No hay hitos definidos para esta transacción</p>
            <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($hitos as $hito): ?>
                <div class="border rounded-lg p-4 <?= $hito['estatus'] === 'completado' ? 'bg-green-50 border-green-200' : 'bg-gray-50' ?>">
                    <div class="flex justify-between items-start">
                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-8 h-8 bg-<?= $hito['estatus'] === 'completado' ? 'green' : 'gray' ?>-200 rounded-full flex items-center justify-center text-<?= $hito['estatus'] === 'completado' ? 'green' : 'gray' ?>-700 font-bold text-sm">
                                <?= $hito['estatus'] === 'completado' ? '✓' : $hito['numero_hito'] ?>
                            </span>
                            <div class="ml-3">
                                <p class="font-medium"><?= htmlspecialchars($hito['descripcion']) ?></p>
                                <p class="text-sm text-gray-500">Monto: $<?= number_format($hito['monto'], 2) ?></p>
                                <?php if ($hito['fecha_limite']): ?>
                                <p class="text-xs text-gray-400">Fecha límite: <?= date('d/m/Y', strtotime($hito['fecha_limite'])) ?></p>
                                <?php endif; ?>
                                <?php if ($hito['fecha_completado']): ?>
                                <p class="text-xs text-green-600">Completado: <?= date('d/m/Y H:i', strtotime($hito['fecha_completado'])) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if ($hito['estatus'] === 'pendiente' && in_array($transaccion['estatus'], ['fondos_depositados', 'en_proceso'])): ?>
                        <form method="POST" action="<?= url('escrow/hitos/' . $transaccion['id']) ?>" class="inline">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <input type="hidden" name="accion" value="completar">
                            <input type="hidden" name="hito_id" value="<?= $hito['id'] ?>">
                            <button type="submit" class="text-sm bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                                Completar
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Movimientos -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-exchange-alt mr-2 text-green-600"></i>Movimientos
            </h2>
            
            <?php if (empty($movimientos)): ?>
            <p class="text-gray-500 text-center py-8">No hay movimientos registrados</p>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Concepto</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($movimientos as $mov): ?>
                        <tr>
                            <td class="px-4 py-2 text-sm"><?= date('d/m/Y H:i', strtotime($mov['created_at'])) ?></td>
                            <td class="px-4 py-2">
                                <?php
                                $tipoColores = [
                                    'deposito' => 'bg-blue-100 text-blue-800',
                                    'liberacion' => 'bg-green-100 text-green-800',
                                    'comision' => 'bg-yellow-100 text-yellow-800',
                                    'reembolso' => 'bg-red-100 text-red-800'
                                ];
                                ?>
                                <span class="px-2 py-1 rounded text-xs font-medium <?= $tipoColores[$mov['tipo']] ?? 'bg-gray-100 text-gray-800' ?>">
                                    <?= ucfirst($mov['tipo']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-2 text-sm"><?= htmlspecialchars($mov['concepto']) ?></td>
                            <td class="px-4 py-2 text-sm text-right font-medium <?= in_array($mov['tipo'], ['deposito']) ? 'text-green-600' : 'text-red-600' ?>">
                                <?= in_array($mov['tipo'], ['deposito']) ? '+' : '-' ?>$<?= number_format($mov['monto'], 2) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Historial -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-history mr-2 text-gray-600"></i>Historial de Cambios
            </h2>
            
            <?php if (empty($historial)): ?>
            <p class="text-gray-500 text-center py-4">Sin historial de cambios</p>
            <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($historial as $h): ?>
                <div class="flex items-start text-sm border-l-2 border-gray-200 pl-4 py-2">
                    <div class="flex-1">
                        <p class="font-medium"><?= htmlspecialchars($h['accion']) ?></p>
                        <p class="text-gray-600"><?= htmlspecialchars($h['descripcion']) ?></p>
                        <p class="text-xs text-gray-400">
                            <?= date('d/m/Y H:i', strtotime($h['created_at'])) ?>
                            <?php if ($h['usuario_nombre']): ?>
                            - <?= htmlspecialchars($h['usuario_nombre']) ?>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Panel Lateral -->
    <div class="space-y-6">
        <!-- Resumen Financiero -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-dollar-sign mr-2 text-green-600"></i>Resumen Financiero
            </h2>
            
            <div class="space-y-4">
                <div>
                    <label class="text-sm text-gray-500">Monto Total</label>
                    <p class="text-2xl font-bold text-gray-800">$<?= number_format($transaccion['monto_total'], 2) ?></p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-500">Comisión (<?= number_format($transaccion['comision_porcentaje'], 2) ?>%)</label>
                        <p class="font-medium text-yellow-600">$<?= number_format($transaccion['comision_monto'], 2) ?></p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Liberado</label>
                        <p class="font-medium text-green-600">$<?= number_format($transaccion['monto_liberado'], 2) ?></p>
                    </div>
                </div>
                
                <div class="pt-4 border-t">
                    <label class="text-sm text-gray-500">Fondos Retenidos</label>
                    <p class="text-xl font-bold text-blue-600">$<?= number_format($saldoRetenido, 2) ?></p>
                </div>
                
                <div class="mt-4">
                    <div class="flex justify-between text-sm mb-1">
                        <span>Progreso</span>
                        <span><?= number_format($progreso, 1) ?>%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-green-500 h-3 rounded-full" style="width: <?= $progreso ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Acciones -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-bolt mr-2 text-yellow-600"></i>Acciones
            </h2>
            
            <div class="space-y-3">
                <?php if (in_array($transaccion['estatus'], ['borrador', 'pendiente_deposito'])): ?>
                <button onclick="document.getElementById('modalDeposito').classList.remove('hidden')" 
                        class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-money-bill-wave mr-2"></i>Registrar Depósito
                </button>
                <?php endif; ?>
                
                <?php if (in_array($transaccion['estatus'], ['fondos_depositados', 'en_proceso', 'entrega_confirmada']) && $saldoRetenido > 0): ?>
                <button onclick="document.getElementById('modalLiberar').classList.remove('hidden')" 
                        class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                    <i class="fas fa-unlock mr-2"></i>Liberar Fondos
                </button>
                <?php endif; ?>
                
                <?php if (!in_array($transaccion['estatus'], ['liberado', 'cancelado', 'reembolsado', 'disputa'])): ?>
                <button onclick="document.getElementById('modalDisputa').classList.remove('hidden')" 
                        class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Abrir Disputa
                </button>
                <?php endif; ?>
                
                <?php if (!in_array($transaccion['estatus'], ['liberado', 'cancelado', 'reembolsado'])): ?>
                <button onclick="document.getElementById('modalCancelar').classList.remove('hidden')" 
                        class="w-full bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                    <i class="fas fa-times mr-2"></i>Cancelar Transacción
                </button>
                <?php endif; ?>
                
                <button onclick="document.getElementById('modalDocumento').classList.remove('hidden')" 
                        class="w-full border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-upload mr-2"></i>Subir Documento
                </button>
            </div>
        </div>
        
        <!-- Documentos -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-file-alt mr-2 text-blue-600"></i>Documentos
            </h2>
            
            <?php if (empty($documentos)): ?>
            <p class="text-gray-500 text-sm text-center py-4">Sin documentos adjuntos</p>
            <?php else: ?>
            <div class="space-y-2">
                <?php foreach ($documentos as $doc): ?>
                <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-file-pdf text-red-500 mr-2"></i>
                        <div>
                            <p class="text-sm font-medium"><?= htmlspecialchars($doc['nombre_archivo']) ?></p>
                            <p class="text-xs text-gray-500"><?= htmlspecialchars($doc['tipo']) ?></p>
                        </div>
                    </div>
                    <a href="<?= url('uploads/' . $doc['ruta_archivo']) ?>" target="_blank" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Disputa Activa -->
        <?php if ($disputa): ?>
        <div class="bg-red-50 rounded-lg border border-red-200 p-6">
            <h2 class="text-lg font-semibold text-red-800 mb-4">
                <i class="fas fa-gavel mr-2"></i>Disputa Activa
            </h2>
            <p class="text-sm"><strong>Iniciada por:</strong> <?= ucfirst($disputa['iniciado_por']) ?></p>
            <p class="text-sm"><strong>Motivo:</strong> <?= htmlspecialchars($disputa['motivo']) ?></p>
            <p class="text-sm mt-2"><?= htmlspecialchars($disputa['descripcion']) ?></p>
            
            <?php if ($_SESSION['user_role'] === 'administrador'): ?>
            <button onclick="document.getElementById('modalResolverDisputa').classList.remove('hidden')" 
                    class="mt-4 w-full bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                Resolver Disputa
            </button>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Depósito -->
<div id="modalDeposito" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold mb-4">Registrar Depósito</h3>
        <form method="POST" action="<?= url('escrow/deposito/' . $transaccion['id']) ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Monto</label>
                    <input type="number" name="monto" step="0.01" required value="<?= $transaccion['monto_total'] ?>"
                           class="w-full rounded border-gray-300 px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Método de Pago</label>
                    <select name="metodo_pago" class="w-full rounded border-gray-300 px-3 py-2 border">
                        <option value="transferencia">Transferencia Bancaria</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="paypal">PayPal</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Referencia</label>
                    <input type="text" name="referencia" class="w-full rounded border-gray-300 px-3 py-2 border">
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modalDeposito').classList.add('hidden')" class="px-4 py-2 border rounded">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Registrar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Liberar -->
<div id="modalLiberar" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold mb-4">Liberar Fondos</h3>
        <form method="POST" action="<?= url('escrow/liberar/' . $transaccion['id']) ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <p class="text-sm text-gray-600 mb-4">Saldo disponible: <strong>$<?= number_format($saldoRetenido, 2) ?></strong></p>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Monto a Liberar</label>
                    <input type="number" name="monto" step="0.01" required max="<?= $saldoRetenido ?>"
                           class="w-full rounded border-gray-300 px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Concepto</label>
                    <input type="text" name="concepto" value="Liberación de fondos" class="w-full rounded border-gray-300 px-3 py-2 border">
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modalLiberar').classList.add('hidden')" class="px-4 py-2 border rounded">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Liberar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Disputa -->
<div id="modalDisputa" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold mb-4">Abrir Disputa</h3>
        <form method="POST" action="<?= url('escrow/disputa/' . $transaccion['id']) ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="accion" value="abrir">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Iniciada por</label>
                    <select name="iniciado_por" class="w-full rounded border-gray-300 px-3 py-2 border">
                        <option value="comprador">Comprador</option>
                        <option value="vendedor">Vendedor</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motivo</label>
                    <input type="text" name="motivo" required class="w-full rounded border-gray-300 px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea name="descripcion_disputa" rows="3" class="w-full rounded border-gray-300 px-3 py-2 border"></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modalDisputa').classList.add('hidden')" class="px-4 py-2 border rounded">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Abrir Disputa</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Cancelar -->
<div id="modalCancelar" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold mb-4">Cancelar Transacción</h3>
        <form method="POST" action="<?= url('escrow/cancelar/' . $transaccion['id']) ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motivo de Cancelación</label>
                    <textarea name="motivo_cancelacion" rows="3" required class="w-full rounded border-gray-300 px-3 py-2 border"></textarea>
                </div>
                <?php if ($saldoRetenido > 0): ?>
                <div class="flex items-center">
                    <input type="checkbox" name="reembolsar" id="reembolsar" class="rounded border-gray-300">
                    <label for="reembolsar" class="ml-2 text-sm text-gray-700">Reembolsar fondos al comprador ($<?= number_format($saldoRetenido, 2) ?>)</label>
                </div>
                <?php endif; ?>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modalCancelar').classList.add('hidden')" class="px-4 py-2 border rounded">Volver</button>
                <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Cancelar Transacción</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Hito -->
<div id="modalHito" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold mb-4">Agregar Hito</h3>
        <form method="POST" action="<?= url('escrow/hitos/' . $transaccion['id']) ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="accion" value="agregar">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <input type="text" name="descripcion_hito" required class="w-full rounded border-gray-300 px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Monto</label>
                    <input type="number" name="monto_hito" step="0.01" required class="w-full rounded border-gray-300 px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Límite</label>
                    <input type="date" name="fecha_limite_hito" class="w-full rounded border-gray-300 px-3 py-2 border">
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modalHito').classList.add('hidden')" class="px-4 py-2 border rounded">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">Agregar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Documento -->
<div id="modalDocumento" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold mb-4">Subir Documento</h3>
        <form method="POST" action="<?= url('escrow/documentos/' . $transaccion['id']) ?>" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Documento</label>
                    <select name="tipo_documento" class="w-full rounded border-gray-300 px-3 py-2 border">
                        <option value="contrato">Contrato</option>
                        <option value="evidencia">Evidencia de Entrega</option>
                        <option value="factura">Factura</option>
                        <option value="identificacion">Identificación</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Archivo</label>
                    <input type="file" name="documento" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" 
                           class="w-full rounded border-gray-300 px-3 py-2 border">
                    <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG, DOC (máx. 10MB)</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <input type="text" name="descripcion_documento" class="w-full rounded border-gray-300 px-3 py-2 border">
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modalDocumento').classList.add('hidden')" class="px-4 py-2 border rounded">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Subir</button>
            </div>
        </form>
    </div>
</div>

<?php if ($disputa): ?>
<!-- Modal Resolver Disputa -->
<div id="modalResolverDisputa" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold mb-4">Resolver Disputa</h3>
        <form method="POST" action="<?= url('escrow/disputa/' . $transaccion['id']) ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="accion" value="resolver">
            <input type="hidden" name="disputa_id" value="<?= $disputa['id'] ?>">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Resolución</label>
                    <select name="estatus_resolucion" class="w-full rounded border-gray-300 px-3 py-2 border">
                        <option value="resuelta_comprador">A favor del Comprador</option>
                        <option value="resuelta_vendedor">A favor del Vendedor</option>
                        <option value="resuelta_parcial">Resolución Parcial</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción de la Resolución</label>
                    <textarea name="resolucion" rows="3" required class="w-full rounded border-gray-300 px-3 py-2 border"></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modalResolverDisputa').classList.add('hidden')" class="px-4 py-2 border rounded">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Resolver</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
