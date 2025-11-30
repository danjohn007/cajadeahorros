<!-- Header -->
<div class="mb-6">
    <a href="<?= BASE_URL ?>/creditos/ver/<?= $credito['id'] ?>" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Volver al Crédito
    </a>
    <h2 class="text-2xl font-bold text-gray-800">Registrar Pago</h2>
    <p class="text-gray-600"><?= htmlspecialchars($credito['numero_credito']) ?> - 
        <?= htmlspecialchars($credito['nombre'] . ' ' . $credito['apellido_paterno']) ?>
    </p>
</div>

<!-- Errors -->
<?php if (!empty($errors)): ?>
<div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg">
    <ul class="list-disc list-inside">
        <?php foreach ($errors as $error): ?>
        <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form -->
    <div class="lg:col-span-2">
        <?php if ($proximoPago): ?>
        <form method="POST" action="<?= BASE_URL ?>/creditos/pago/<?= $credito['id'] ?>" class="bg-white rounded-xl shadow-sm">
            <input type="hidden" name="csrf_token" value="<?= $this->csrf_token() ?>">
            
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Pago #<?= $proximoPago['numero_pago'] ?></h3>
                
                <div class="grid grid-cols-2 gap-4 mb-6 p-4 bg-purple-50 rounded-lg">
                    <div>
                        <label class="text-sm text-gray-500">Fecha de Vencimiento</label>
                        <p class="text-lg font-bold <?= strtotime($proximoPago['fecha_vencimiento']) < time() ? 'text-red-600' : 'text-gray-800' ?>">
                            <?= date('d/m/Y', strtotime($proximoPago['fecha_vencimiento'])) ?>
                            <?php if (strtotime($proximoPago['fecha_vencimiento']) < time()): ?>
                            <span class="text-sm">(Vencido)</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Monto a Pagar</label>
                        <p class="text-2xl font-bold text-purple-600">$<?= number_format($proximoPago['monto_total'], 2) ?></p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Capital</label>
                        <p class="font-medium">$<?= number_format($proximoPago['monto_capital'], 2) ?></p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Intereses</label>
                        <p class="font-medium">$<?= number_format($proximoPago['monto_interes'], 2) ?></p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Monto del Pago *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                            <input type="number" name="monto" step="0.01" min="0.01" required
                                   value="<?= $proximoPago['monto_total'] ?>"
                                   class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Referencia / Folio</label>
                        <input type="text" name="referencia"
                               placeholder="Número de recibo, folio, etc."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
            </div>
            
            <div class="p-6 bg-gray-50 flex justify-end space-x-4">
                <a href="<?= BASE_URL ?>/creditos/ver/<?= $credito['id'] ?>" 
                   class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-check mr-2"></i> Registrar Pago
                </button>
            </div>
        </form>
        <?php else: ?>
        <div class="bg-white rounded-xl shadow-sm p-6 text-center">
            <i class="fas fa-check-circle text-4xl text-green-500 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">No hay pagos pendientes</h3>
            <p class="text-gray-600">Este crédito no tiene pagos pendientes por realizar.</p>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Sidebar -->
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Resumen del Crédito</h3>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500">Saldo Actual</span>
                    <span class="font-medium text-purple-600">$<?= number_format($credito['saldo_actual'], 2) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Cuota Mensual</span>
                    <span class="font-medium">$<?= number_format($credito['monto_cuota'], 2) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Pagos Realizados</span>
                    <span class="font-medium"><?= $credito['pagos_realizados'] ?> de <?= $credito['plazo_meses'] ?></span>
                </div>
            </div>
            
            <!-- Progress bar -->
            <div class="mt-4">
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-500">Progreso</span>
                    <span class="font-medium"><?= round(($credito['pagos_realizados'] / $credito['plazo_meses']) * 100) ?>%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-purple-600 h-2 rounded-full" style="width: <?= ($credito['pagos_realizados'] / $credito['plazo_meses']) * 100 ?>%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
