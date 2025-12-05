<?php
/**
 * Vista de Formulario de Inversión
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<!-- Header -->
<div class="mb-6">
    <a href="<?= url('inversionistas/ver/' . $inversionista['id']) ?>" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Volver a Inversionista
    </a>
    <h2 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h2>
    <p class="text-gray-600">
        Inversionista: <?= htmlspecialchars($inversionista['nombre'] . ' ' . $inversionista['apellido_paterno']) ?> 
        (<?= htmlspecialchars($inversionista['numero_inversionista']) ?>)
    </p>
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

<!-- Form -->
<form method="POST" class="bg-white rounded-xl shadow-sm">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    
    <!-- Datos de la Inversión -->
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-line text-green-500 mr-2"></i> Datos de la Inversión
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Monto de Inversión *</label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">$</span>
                    <input type="number" name="monto" step="0.01" min="0" required
                           value="<?= htmlspecialchars($inversion['monto'] ?? '') ?>"
                           class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tasa de Rendimiento Anual (%) *</label>
                <input type="number" name="tasa_rendimiento" step="0.01" min="0" max="100" required
                       value="<?= isset($inversion['tasa_rendimiento']) ? ($inversion['tasa_rendimiento'] * 100) : '12' ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">Ejemplo: 12 para 12% anual</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Plazo (meses) *</label>
                <input type="number" name="plazo_meses" min="1" max="120" required
                       value="<?= htmlspecialchars($inversion['plazo_meses'] ?? '12') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Inicio *</label>
                <input type="date" name="fecha_inicio" required
                       value="<?= htmlspecialchars($inversion['fecha_inicio'] ?? date('Y-m-d')) ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Vincular a Crédito (opcional)</label>
                <select name="credito_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Sin vincular</option>
                    <?php foreach ($creditos as $credito): ?>
                        <option value="<?= $credito['id'] ?>" <?= ($inversion['credito_id'] ?? '') == $credito['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($credito['numero_credito']) ?> - 
                            <?= htmlspecialchars($credito['tipo']) ?> - 
                            $<?= number_format($credito['monto_autorizado'], 2) ?> 
                            (<?= htmlspecialchars($credito['socio']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="text-xs text-gray-500 mt-1">Vincule esta inversión a un crédito específico para rastreo</p>
            </div>
        </div>
    </div>
    
    <!-- Notas -->
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-sticky-note text-yellow-500 mr-2"></i> Notas
        </h3>
        
        <textarea name="notas" rows="3"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Notas adicionales sobre esta inversión..."><?= htmlspecialchars($inversion['notas'] ?? '') ?></textarea>
    </div>
    
    <!-- Resumen Calculado -->
    <div class="p-6 bg-gray-50 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-calculator text-purple-500 mr-2"></i> Resumen Proyectado
        </h3>
        
        <div id="resumen" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-lg border">
                <p class="text-sm text-gray-500">Monto Inversión</p>
                <p class="text-xl font-bold text-gray-800" id="res-monto">$0.00</p>
            </div>
            <div class="bg-white p-4 rounded-lg border">
                <p class="text-sm text-gray-500">Rendimiento Mensual</p>
                <p class="text-xl font-bold text-blue-600" id="res-mensual">$0.00</p>
            </div>
            <div class="bg-white p-4 rounded-lg border">
                <p class="text-sm text-gray-500">Rendimiento Total</p>
                <p class="text-xl font-bold text-green-600" id="res-total">$0.00</p>
            </div>
            <div class="bg-white p-4 rounded-lg border">
                <p class="text-sm text-gray-500">Monto Final</p>
                <p class="text-xl font-bold text-purple-600" id="res-final">$0.00</p>
            </div>
        </div>
    </div>
    
    <!-- Actions -->
    <div class="p-6 bg-gray-50 flex justify-end space-x-4">
        <a href="<?= url('inversionistas/ver/' . $inversionista['id']) ?>" 
           class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
            Cancelar
        </a>
        <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
            <i class="fas fa-save mr-2"></i> Registrar Inversión
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const montoInput = document.querySelector('input[name="monto"]');
    const tasaInput = document.querySelector('input[name="tasa_rendimiento"]');
    const plazoInput = document.querySelector('input[name="plazo_meses"]');
    
    function calcularResumen() {
        const monto = parseFloat(montoInput.value) || 0;
        const tasaAnual = parseFloat(tasaInput.value) || 0;
        const plazo = parseInt(plazoInput.value) || 0;
        
        const tasaMensual = tasaAnual / 100 / 12;
        const rendimientoMensual = monto * tasaMensual;
        const rendimientoTotal = rendimientoMensual * plazo;
        const montoFinal = monto + rendimientoTotal;
        
        document.getElementById('res-monto').textContent = '$' + monto.toLocaleString('es-MX', {minimumFractionDigits: 2});
        document.getElementById('res-mensual').textContent = '$' + rendimientoMensual.toLocaleString('es-MX', {minimumFractionDigits: 2});
        document.getElementById('res-total').textContent = '$' + rendimientoTotal.toLocaleString('es-MX', {minimumFractionDigits: 2});
        document.getElementById('res-final').textContent = '$' + montoFinal.toLocaleString('es-MX', {minimumFractionDigits: 2});
    }
    
    montoInput.addEventListener('input', calcularResumen);
    tasaInput.addEventListener('input', calcularResumen);
    plazoInput.addEventListener('input', calcularResumen);
    
    // Initial calculation
    calcularResumen();
});
</script>
