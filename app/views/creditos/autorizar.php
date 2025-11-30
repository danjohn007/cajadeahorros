<!-- Header -->
<div class="mb-6">
    <a href="<?= BASE_URL ?>/creditos/ver/<?= $credito['id'] ?>" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Volver al Crédito
    </a>
    <h2 class="text-2xl font-bold text-gray-800">Autorizar Crédito</h2>
    <p class="text-gray-600"><?= htmlspecialchars($credito['numero_credito']) ?></p>
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
        <form method="POST" action="<?= BASE_URL ?>/creditos/autorizar/<?= $credito['id'] ?>" class="bg-white rounded-xl shadow-sm">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <!-- Resumen Solicitud -->
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Resumen de la Solicitud</h3>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div>
                        <label class="text-sm text-gray-500">Monto Solicitado</label>
                        <p class="text-xl font-bold text-purple-600">$<?= number_format($credito['monto_solicitado'], 2) ?></p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Plazo</label>
                        <p class="text-xl font-bold"><?= $credito['plazo_meses'] ?> meses</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Tasa de Interés</label>
                        <p class="text-xl font-bold"><?= number_format($credito['tasa_interes'] * 100, 2) ?>%</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Fecha Solicitud</label>
                        <p class="text-xl font-bold"><?= date('d/m/Y', strtotime($credito['fecha_solicitud'])) ?></p>
                    </div>
                </div>
                
                <!-- Monto a autorizar -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Monto a Autorizar *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                        <input type="number" name="monto_autorizado" id="monto_autorizado" 
                               step="100" min="1000" required
                               value="<?= $credito['monto_solicitado'] ?>"
                               class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Puede modificar el monto si lo considera necesario</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                    <textarea name="observaciones" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                              placeholder="Notas sobre la autorización o rechazo..."><?= htmlspecialchars($credito['observaciones'] ?? '') ?></textarea>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="p-6 bg-gray-50 flex justify-end space-x-4">
                <button type="submit" name="accion" value="rechazar" 
                        class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                        onclick="return confirm('¿Está seguro de rechazar esta solicitud?')">
                    <i class="fas fa-times mr-2"></i> Rechazar
                </button>
                <button type="submit" name="accion" value="autorizar" 
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-check mr-2"></i> Autorizar
                </button>
            </div>
        </form>
    </div>
    
    <!-- Sidebar - Info Socio -->
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Información del Socio</h3>
            
            <div class="text-center mb-4">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-blue-600 font-bold text-xl">
                        <?= strtoupper(substr($socio['nombre'], 0, 1) . substr($socio['apellido_paterno'], 0, 1)) ?>
                    </span>
                </div>
                <p class="font-medium"><?= htmlspecialchars($socio['nombre'] . ' ' . $socio['apellido_paterno']) ?></p>
                <p class="text-sm text-gray-500"><?= htmlspecialchars($socio['numero_socio']) ?></p>
            </div>
            
            <div class="space-y-3 border-t pt-4">
                <div class="flex justify-between">
                    <span class="text-gray-500">Saldo de Ahorro</span>
                    <span class="font-medium text-green-600">$<?= number_format($socio['saldo_ahorro'] ?? 0, 2) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Créditos Activos</span>
                    <span class="font-medium"><?= $socio['creditos_activos'] ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Deuda Total</span>
                    <span class="font-medium text-red-600">$<?= number_format($socio['deuda_total'] ?? 0, 2) ?></span>
                </div>
                <?php if ($socio['salario_mensual']): ?>
                <div class="flex justify-between">
                    <span class="text-gray-500">Salario Mensual</span>
                    <span class="font-medium">$<?= number_format($socio['salario_mensual'], 2) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Calculadora -->
        <div class="bg-purple-50 rounded-xl p-6">
            <h3 class="font-semibold text-purple-800 mb-4">
                <i class="fas fa-calculator mr-1"></i> Cuota Estimada
            </h3>
            <div class="text-center">
                <p class="text-3xl font-bold text-purple-600" id="cuotaEstimada">$0.00</p>
                <p class="text-sm text-purple-700 mt-1">por mes</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const montoInput = document.getElementById('monto_autorizado');
    const cuotaEstimada = document.getElementById('cuotaEstimada');
    const tasa = <?= $credito['tasa_interes'] ?>;
    const plazo = <?= $credito['plazo_meses'] ?>;
    
    function calcularCuota() {
        const monto = parseFloat(montoInput.value) || 0;
        let cuota;
        
        if (tasa === 0) {
            cuota = monto / plazo;
        } else {
            cuota = monto * (tasa * Math.pow(1 + tasa, plazo)) / (Math.pow(1 + tasa, plazo) - 1);
        }
        
        cuotaEstimada.textContent = '$' + cuota.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
    
    montoInput.addEventListener('input', calcularCuota);
    calcularCuota();
});
</script>
