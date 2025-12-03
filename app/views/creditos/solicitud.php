<!-- Header -->
<div class="mb-6">
    <a href="<?= BASE_URL ?>/creditos" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Volver a Créditos
    </a>
    <h2 class="text-2xl font-bold text-gray-800">Nueva Solicitud de Crédito</h2>
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
        <form method="POST" action="<?= BASE_URL ?>/creditos/solicitud" class="bg-white rounded-xl shadow-sm">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-file-invoice-dollar text-purple-500 mr-2"></i> Datos de la Solicitud
                </h3>
                
                <div class="space-y-4">
                    <!-- Socio -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Socio *</label>
                        <?php if ($socioPreseleccionado): ?>
                        <input type="hidden" name="socio_id" value="<?= $socioPreseleccionado['id'] ?>">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="font-medium"><?= htmlspecialchars($socioPreseleccionado['nombre'] . ' ' . $socioPreseleccionado['apellido_paterno']) ?></p>
                            <p class="text-sm text-gray-500"><?= htmlspecialchars($socioPreseleccionado['numero_socio']) ?></p>
                        </div>
                        <?php else: ?>
                        <div class="relative">
                            <input type="hidden" name="socio_id" id="socio_id_hidden" value="<?= htmlspecialchars($data['socio_id'] ?? '') ?>" required>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" id="socio_search" 
                                       placeholder="Buscar por nombre, número de socio, RFC o CURP..."
                                       autocomplete="off"
                                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            </div>
                            <div id="socio_results" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto hidden"></div>
                            <div id="socio_selected" class="mt-2 p-3 bg-purple-50 rounded-lg hidden">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-medium text-purple-800" id="socio_nombre"></p>
                                        <p class="text-sm text-purple-600" id="socio_info"></p>
                                    </div>
                                    <button type="button" onclick="clearSocio()" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Escribe para buscar por nombre, número de socio, RFC o CURP</p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Tipo de Crédito -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Crédito *</label>
                        <select name="tipo_credito_id" id="tipo_credito_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            <option value="">Seleccionar tipo...</option>
                            <?php foreach ($tiposCredito as $tc): ?>
                            <option value="<?= $tc['id'] ?>" 
                                    data-tasa="<?= $tc['tasa_interes'] ?>"
                                    data-min="<?= $tc['monto_minimo'] ?>"
                                    data-max="<?= $tc['monto_maximo'] ?>"
                                    data-plazo-min="<?= $tc['plazo_minimo'] ?>"
                                    data-plazo-max="<?= $tc['plazo_maximo'] ?>"
                                    <?= ($data['tipo_credito_id'] ?? '') == $tc['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tc['nombre']) ?> (<?= number_format($tc['tasa_interes'] * 100, 2) ?>% mensual)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Info del tipo seleccionado -->
                    <div id="tipoInfo" class="hidden p-4 bg-purple-50 rounded-lg">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Monto:</span>
                                <span class="font-medium" id="rangoMonto"></span>
                            </div>
                            <div>
                                <span class="text-gray-600">Plazo:</span>
                                <span class="font-medium" id="rangoPlazo"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Monto -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Monto Solicitado *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                            <input type="number" name="monto_solicitado" id="monto_solicitado" 
                                   step="100" min="1000" required
                                   value="<?= htmlspecialchars($data['monto_solicitado'] ?? '') ?>"
                                   class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        </div>
                    </div>
                    
                    <!-- Plazo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Plazo (meses) *</label>
                        <input type="number" name="plazo_meses" id="plazo_meses" 
                               min="1" required
                               value="<?= htmlspecialchars($data['plazo_meses'] ?? '') ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    
                    <!-- Observaciones -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                        <textarea name="observaciones" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                                  placeholder="Notas adicionales sobre la solicitud..."><?= htmlspecialchars($data['observaciones'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="p-6 bg-gray-50 flex justify-end space-x-4">
                <a href="<?= BASE_URL ?>/creditos" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    <i class="fas fa-paper-plane mr-2"></i> Enviar Solicitud
                </button>
            </div>
        </form>
    </div>
    
    <!-- Sidebar -->
    <div class="lg:col-span-1">
        <!-- Simulador -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">
                <i class="fas fa-calculator text-purple-500 mr-2"></i> Simulador de Pago
            </h3>
            
            <div id="simulador" class="text-center text-gray-500">
                <p>Ingresa los datos para simular el pago mensual</p>
            </div>
            
            <div id="resultadoSimulador" class="hidden">
                <div class="text-center mb-4">
                    <p class="text-sm text-gray-500">Pago Mensual Estimado</p>
                    <p class="text-3xl font-bold text-purple-600" id="pagoMensual">$0.00</p>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Monto:</span>
                        <span id="resumenMonto" class="font-medium">$0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tasa mensual:</span>
                        <span id="resumenTasa" class="font-medium">0%</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Plazo:</span>
                        <span id="resumenPlazo" class="font-medium">0 meses</span>
                    </div>
                    <div class="flex justify-between border-t pt-2 mt-2">
                        <span class="text-gray-500">Total a pagar:</span>
                        <span id="resumenTotal" class="font-medium">$0.00</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Info -->
        <div class="bg-blue-50 rounded-xl p-6 mt-6">
            <h3 class="font-semibold text-blue-800 mb-2">
                <i class="fas fa-info-circle mr-1"></i> Información
            </h3>
            <ul class="text-sm text-blue-700 space-y-2">
                <li>• La solicitud será revisada por el área administrativa</li>
                <li>• El monto autorizado puede diferir del solicitado</li>
                <li>• Se requiere cumplir con los requisitos del tipo de crédito</li>
                <li>• El primer pago se programa un mes después de la formalización</li>
            </ul>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipoSelect = document.getElementById('tipo_credito_id');
    const montoInput = document.getElementById('monto_solicitado');
    const plazoInput = document.getElementById('plazo_meses');
    const tipoInfo = document.getElementById('tipoInfo');
    
    function actualizarTipoInfo() {
        const option = tipoSelect.options[tipoSelect.selectedIndex];
        if (option && option.value) {
            tipoInfo.classList.remove('hidden');
            document.getElementById('rangoMonto').textContent = 
                '$' + parseFloat(option.dataset.min).toLocaleString() + ' - $' + parseFloat(option.dataset.max).toLocaleString();
            document.getElementById('rangoPlazo').textContent = 
                option.dataset.plazoMin + ' - ' + option.dataset.plazoMax + ' meses';
        } else {
            tipoInfo.classList.add('hidden');
        }
    }
    
    function simularPago() {
        const option = tipoSelect.options[tipoSelect.selectedIndex];
        const monto = parseFloat(montoInput.value) || 0;
        const plazo = parseInt(plazoInput.value) || 0;
        
        if (option && option.value && monto > 0 && plazo > 0) {
            const tasa = parseFloat(option.dataset.tasa) || 0;
            let cuota;
            
            if (tasa === 0) {
                cuota = monto / plazo;
            } else {
                cuota = monto * (tasa * Math.pow(1 + tasa, plazo)) / (Math.pow(1 + tasa, plazo) - 1);
            }
            
            const total = cuota * plazo;
            
            document.getElementById('simulador').classList.add('hidden');
            document.getElementById('resultadoSimulador').classList.remove('hidden');
            document.getElementById('pagoMensual').textContent = '$' + cuota.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('resumenMonto').textContent = '$' + monto.toLocaleString('es-MX', {minimumFractionDigits: 2});
            document.getElementById('resumenTasa').textContent = (tasa * 100).toFixed(2) + '%';
            document.getElementById('resumenPlazo').textContent = plazo + ' meses';
            document.getElementById('resumenTotal').textContent = '$' + total.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        } else {
            document.getElementById('simulador').classList.remove('hidden');
            document.getElementById('resultadoSimulador').classList.add('hidden');
        }
    }
    
    tipoSelect.addEventListener('change', function() {
        actualizarTipoInfo();
        simularPago();
    });
    montoInput.addEventListener('input', simularPago);
    plazoInput.addEventListener('input', simularPago);
    
    // Initial
    actualizarTipoInfo();
    simularPago();
    
    // Socio search functionality
    const socioSearch = document.getElementById('socio_search');
    const socioResults = document.getElementById('socio_results');
    const socioIdHidden = document.getElementById('socio_id_hidden');
    const socioSelected = document.getElementById('socio_selected');
    
    if (socioSearch) {
        let searchTimeout;
        
        socioSearch.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                socioResults.classList.add('hidden');
                return;
            }
            
            searchTimeout = setTimeout(() => {
                fetch('<?= BASE_URL ?>/socios/buscar?q=' + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
                        if (data.results && data.results.length > 0) {
                            socioResults.innerHTML = data.results.map(socio => `
                                <div class="px-4 py-3 hover:bg-purple-50 cursor-pointer border-b border-gray-100" 
                                     onclick="selectSocio(${socio.id}, '${escapeHtml(socio.nombre)} ${escapeHtml(socio.apellido_paterno)} ${escapeHtml(socio.apellido_materno || '')}', '${escapeHtml(socio.numero_socio)}')">
                                    <div class="font-medium">${escapeHtml(socio.nombre)} ${escapeHtml(socio.apellido_paterno)} ${escapeHtml(socio.apellido_materno || '')}</div>
                                    <div class="text-sm text-gray-500">${escapeHtml(socio.numero_socio)} | RFC: ${escapeHtml(socio.rfc || 'N/A')}</div>
                                </div>
                            `).join('');
                            socioResults.classList.remove('hidden');
                        } else {
                            socioResults.innerHTML = '<div class="px-4 py-3 text-gray-500">No se encontraron socios</div>';
                            socioResults.classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error buscando socios:', error);
                    });
            }, 300);
        });
        
        // Close results when clicking outside
        document.addEventListener('click', function(e) {
            if (!socioSearch.contains(e.target) && !socioResults.contains(e.target)) {
                socioResults.classList.add('hidden');
            }
        });
    }
});

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function selectSocio(id, nombre, numeroSocio) {
    document.getElementById('socio_id_hidden').value = id;
    document.getElementById('socio_nombre').textContent = nombre;
    document.getElementById('socio_info').textContent = numeroSocio;
    document.getElementById('socio_selected').classList.remove('hidden');
    document.getElementById('socio_search').value = '';
    document.getElementById('socio_results').classList.add('hidden');
}

function clearSocio() {
    document.getElementById('socio_id_hidden').value = '';
    document.getElementById('socio_selected').classList.add('hidden');
    document.getElementById('socio_search').focus();
}
</script>
