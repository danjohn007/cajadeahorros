<!-- Header -->
<div class="mb-6">
    <a href="<?= BASE_URL ?>/ahorro" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Volver a Ahorro
    </a>
    <h2 class="text-2xl font-bold text-gray-800">Registrar Movimiento de Ahorro</h2>
</div>

<!-- Errors -->
<?php if (!empty($errors)): ?>
<div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg">
    <div class="flex items-start">
        <i class="fas fa-exclamation-circle mt-0.5 mr-2"></i>
        <div>
            <p class="font-medium">Por favor corrige los siguientes errores:</p>
            <ul class="mt-2 list-disc list-inside">
                <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form -->
    <div class="lg:col-span-2">
        <form method="POST" action="<?= BASE_URL ?>/ahorro/movimiento" class="bg-white rounded-xl shadow-sm">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-exchange-alt text-green-500 mr-2"></i> Datos del Movimiento
                </h3>
                
                <div class="space-y-4">
                    <!-- Socio Select -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Socio *</label>
                        <select name="socio_id" id="socio_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="">Seleccionar socio...</option>
                            <?php foreach ($socios as $s): ?>
                            <option value="<?= $s['id'] ?>" 
                                    data-saldo="<?= $s['saldo'] ?>"
                                    data-cuenta="<?= $s['numero_cuenta'] ?>"
                                    <?= ($data['socio_id'] ?? ($_GET['socio'] ?? '')) == $s['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['numero_socio'] . ' - ' . $s['nombre'] . ' ' . $s['apellido_paterno']) ?>
                                (Saldo: $<?= number_format($s['saldo'], 2) ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Tipo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Movimiento *</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 <?= ($data['tipo'] ?? '') === 'aportacion' ? 'border-green-500 bg-green-50' : 'border-gray-300' ?>">
                                <input type="radio" name="tipo" value="aportacion" required 
                                       <?= ($data['tipo'] ?? '') === 'aportacion' ? 'checked' : '' ?>
                                       class="hidden" onchange="this.closest('form').querySelectorAll('label.flex').forEach(l => l.classList.remove('border-green-500', 'bg-green-50')); this.closest('label').classList.add('border-green-500', 'bg-green-50');">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-arrow-down text-green-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">Aportación</p>
                                    <p class="text-sm text-gray-500">Depósito de ahorro</p>
                                </div>
                            </label>
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 <?= ($data['tipo'] ?? '') === 'retiro' ? 'border-red-500 bg-red-50' : 'border-gray-300' ?>">
                                <input type="radio" name="tipo" value="retiro" required 
                                       <?= ($data['tipo'] ?? '') === 'retiro' ? 'checked' : '' ?>
                                       class="hidden" onchange="this.closest('form').querySelectorAll('label.flex').forEach(l => l.classList.remove('border-red-500', 'bg-red-50')); this.closest('label').classList.add('border-red-500', 'bg-red-50');">
                                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-arrow-up text-red-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">Retiro</p>
                                    <p class="text-sm text-gray-500">Retiro de ahorro</p>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Monto -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Monto *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                            <input type="number" name="monto" id="monto" step="0.01" min="0.01" required
                                   value="<?= htmlspecialchars($data['monto'] ?? '') ?>"
                                   class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        </div>
                    </div>
                    
                    <!-- Concepto -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Concepto</label>
                        <input type="text" name="concepto" value="<?= htmlspecialchars($data['concepto'] ?? '') ?>"
                               placeholder="Ej: Aportación quincenal, Retiro personal..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <!-- Referencia -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Referencia</label>
                        <input type="text" name="referencia" value="<?= htmlspecialchars($data['referencia'] ?? '') ?>"
                               placeholder="Número de recibo, folio, etc."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="p-6 bg-gray-50 flex justify-end space-x-4">
                <a href="<?= BASE_URL ?>/ahorro" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-save mr-2"></i> Registrar Movimiento
                </button>
            </div>
        </form>
    </div>
    
    <!-- Info Panel -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm p-6" id="socioInfo" style="display: none;">
            <h3 class="font-semibold text-gray-800 mb-4">Información de Cuenta</h3>
            <div class="text-center mb-4">
                <p class="text-sm text-gray-500">Saldo Actual</p>
                <p class="text-3xl font-bold text-green-600" id="saldoActual">$0.00</p>
                <p class="text-sm text-gray-500 mt-1" id="numeroCuenta"></p>
            </div>
            <div class="border-t pt-4">
                <p class="text-sm text-gray-500 mb-2">Saldo después del movimiento:</p>
                <p class="text-2xl font-bold" id="saldoNuevo">$0.00</p>
            </div>
        </div>
        
        <div class="bg-blue-50 rounded-xl p-6 mt-6">
            <h3 class="font-semibold text-blue-800 mb-2">
                <i class="fas fa-info-circle mr-1"></i> Información
            </h3>
            <ul class="text-sm text-blue-700 space-y-2">
                <li>• Las aportaciones incrementan el saldo del socio</li>
                <li>• Los retiros disminuyen el saldo disponible</li>
                <li>• No se permiten retiros mayores al saldo</li>
                <li>• Todos los movimientos quedan registrados en la bitácora</li>
            </ul>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const socioSelect = document.getElementById('socio_id');
    const montoInput = document.getElementById('monto');
    const tipoInputs = document.querySelectorAll('input[name="tipo"]');
    const socioInfo = document.getElementById('socioInfo');
    const saldoActual = document.getElementById('saldoActual');
    const numeroCuenta = document.getElementById('numeroCuenta');
    const saldoNuevo = document.getElementById('saldoNuevo');
    
    function updateInfo() {
        const option = socioSelect.options[socioSelect.selectedIndex];
        if (option && option.value) {
            const saldo = parseFloat(option.dataset.saldo) || 0;
            const cuenta = option.dataset.cuenta || '';
            const monto = parseFloat(montoInput.value) || 0;
            const tipo = document.querySelector('input[name="tipo"]:checked')?.value;
            
            socioInfo.style.display = 'block';
            saldoActual.textContent = '$' + saldo.toLocaleString('es-MX', {minimumFractionDigits: 2});
            numeroCuenta.textContent = cuenta;
            
            let nuevoSaldo = saldo;
            if (tipo === 'aportacion') {
                nuevoSaldo = saldo + monto;
                saldoNuevo.className = 'text-2xl font-bold text-green-600';
            } else if (tipo === 'retiro') {
                nuevoSaldo = saldo - monto;
                saldoNuevo.className = nuevoSaldo < 0 ? 'text-2xl font-bold text-red-600' : 'text-2xl font-bold text-orange-600';
            }
            saldoNuevo.textContent = '$' + nuevoSaldo.toLocaleString('es-MX', {minimumFractionDigits: 2});
        } else {
            socioInfo.style.display = 'none';
        }
    }
    
    socioSelect.addEventListener('change', updateInfo);
    montoInput.addEventListener('input', updateInfo);
    tipoInputs.forEach(input => input.addEventListener('change', updateInfo));
    
    // Initial check
    if (socioSelect.value) {
        updateInfo();
    }
});
</script>
