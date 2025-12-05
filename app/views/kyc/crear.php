<!-- Formulario para crear nueva verificación KYC -->
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="<?= BASE_URL ?>/kyc" class="text-primary-600 hover:text-primary-800">
            <i class="fas fa-arrow-left mr-2"></i>Volver a verificaciones
        </a>
    </div>
    
    <?php if (!empty($errors)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul class="list-disc list-inside">
            <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    
    <form method="POST" class="bg-white rounded-lg shadow p-6 space-y-6">
        <input type="hidden" name="csrf_token" value="<?= $this->csrf_token() ?>">
        
        <!-- Sección: Datos del Socio -->
        <div class="border-b pb-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i class="fas fa-user mr-2 text-primary-600"></i>Datos del Socio
            </h3>
            
            <?php if ($socioPreseleccionado): ?>
            <input type="hidden" name="socio_id" value="<?= $socioPreseleccionado['id'] ?>">
            <div class="bg-primary-50 p-4 rounded-lg">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0 h-12 w-12 bg-primary-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-primary-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">
                            <?= htmlspecialchars($socioPreseleccionado['nombre'] . ' ' . $socioPreseleccionado['apellido_paterno'] . ' ' . ($socioPreseleccionado['apellido_materno'] ?? '')) ?>
                        </p>
                        <p class="text-sm text-gray-500">
                            <?= htmlspecialchars($socioPreseleccionado['numero_socio']) ?> | 
                            RFC: <?= htmlspecialchars($socioPreseleccionado['rfc'] ?? 'N/A') ?>
                        </p>
                        <?php if ($socioPreseleccionado['verificaciones_previas'] > 0): ?>
                        <p class="text-sm text-yellow-600">
                            <i class="fas fa-info-circle"></i> Este socio tiene <?= $socioPreseleccionado['verificaciones_previas'] ?> verificación(es) previa(s)
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div x-data="{ socioId: '', socioNombre: '' }">
                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar Socio *</label>
                <div class="relative">
                    <input type="text" 
                           id="buscarSocio"
                           placeholder="Escriba nombre, número de socio o RFC..."
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500"
                           autocomplete="off">
                    <input type="hidden" name="socio_id" id="socio_id" x-model="socioId" required>
                    <div id="resultadosSocio" class="absolute z-10 w-full bg-white border rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto"></div>
                </div>
                <div id="socioSeleccionado" class="mt-2 bg-green-50 p-3 rounded-lg hidden">
                    <span class="text-green-700" id="socioInfo"></span>
                    <button type="button" onclick="limpiarSocio()" class="ml-2 text-red-500 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Sección: Documento de Identidad -->
        <div class="border-b pb-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i class="fas fa-id-card mr-2 text-primary-600"></i>Documento de Identidad
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Documento *</label>
                    <select name="tipo_documento" required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">Seleccione...</option>
                        <option value="INE" <?= ($data['tipo_documento'] ?? '') === 'INE' ? 'selected' : '' ?>>INE / IFE</option>
                        <option value="Pasaporte" <?= ($data['tipo_documento'] ?? '') === 'Pasaporte' ? 'selected' : '' ?>>Pasaporte</option>
                        <option value="Cedula Profesional" <?= ($data['tipo_documento'] ?? '') === 'Cedula Profesional' ? 'selected' : '' ?>>Cédula Profesional</option>
                        <option value="Cartilla Militar" <?= ($data['tipo_documento'] ?? '') === 'Cartilla Militar' ? 'selected' : '' ?>>Cartilla del Servicio Militar</option>
                        <option value="Licencia de Conducir" <?= ($data['tipo_documento'] ?? '') === 'Licencia de Conducir' ? 'selected' : '' ?>>Licencia de Conducir</option>
                        <option value="Otro" <?= ($data['tipo_documento'] ?? '') === 'Otro' ? 'selected' : '' ?>>Otro</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Número de Documento *</label>
                    <input type="text" name="numero_documento" 
                           value="<?= htmlspecialchars($data['numero_documento'] ?? '') ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500"
                           required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Emisión</label>
                    <input type="date" name="fecha_emision" 
                           value="<?= htmlspecialchars($data['fecha_emision'] ?? '') ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Vencimiento</label>
                    <input type="date" name="fecha_vencimiento" 
                           value="<?= htmlspecialchars($data['fecha_vencimiento'] ?? '') ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">País de Emisión</label>
                    <input type="text" name="pais_emision" 
                           value="<?= htmlspecialchars($data['pais_emision'] ?? 'México') ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
        </div>
        
        <!-- Sección: Verificaciones -->
        <div class="border-b pb-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i class="fas fa-check-double mr-2 text-primary-600"></i>Estado de Verificaciones
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center">
                    <input type="checkbox" name="documento_verificado" id="documento_verificado"
                           <?= isset($data['documento_verificado']) ? 'checked' : '' ?>
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="documento_verificado" class="ml-2 text-sm text-gray-700">
                        Documento verificado (autenticidad confirmada)
                    </label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="direccion_verificada" id="direccion_verificada"
                           <?= isset($data['direccion_verificada']) ? 'checked' : '' ?>
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="direccion_verificada" class="ml-2 text-sm text-gray-700">
                        Dirección verificada (comprobante de domicilio)
                    </label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="identidad_verificada" id="identidad_verificada"
                           <?= isset($data['identidad_verificada']) ? 'checked' : '' ?>
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="identidad_verificada" class="ml-2 text-sm text-gray-700">
                        Identidad verificada (comparación biométrica)
                    </label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="pep" id="pep"
                           <?= isset($data['pep']) ? 'checked' : '' ?>
                           class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                    <label for="pep" class="ml-2 text-sm text-gray-700">
                        <span class="text-red-600 font-medium">PEP</span> (Persona Políticamente Expuesta)
                    </label>
                </div>
            </div>
        </div>
        
        <!-- Sección: Información Económica -->
        <div class="border-b pb-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i class="fas fa-briefcase mr-2 text-primary-600"></i>Información Económica
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fuente de Ingresos</label>
                    <select name="fuente_ingresos"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">Seleccione...</option>
                        <option value="Empleo" <?= ($data['fuente_ingresos'] ?? '') === 'Empleo' ? 'selected' : '' ?>>Empleo</option>
                        <option value="Negocio Propio" <?= ($data['fuente_ingresos'] ?? '') === 'Negocio Propio' ? 'selected' : '' ?>>Negocio Propio</option>
                        <option value="Inversiones" <?= ($data['fuente_ingresos'] ?? '') === 'Inversiones' ? 'selected' : '' ?>>Inversiones</option>
                        <option value="Pensión" <?= ($data['fuente_ingresos'] ?? '') === 'Pensión' ? 'selected' : '' ?>>Pensión</option>
                        <option value="Remesas" <?= ($data['fuente_ingresos'] ?? '') === 'Remesas' ? 'selected' : '' ?>>Remesas</option>
                        <option value="Otro" <?= ($data['fuente_ingresos'] ?? '') === 'Otro' ? 'selected' : '' ?>>Otro</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Actividad Económica / Giro</label>
                    <input type="text" name="actividad_economica" 
                           value="<?= htmlspecialchars($data['actividad_economica'] ?? '') ?>"
                           placeholder="Ej: Comercio al por menor, Servicios profesionales..."
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
        </div>
        
        <!-- Sección: Observaciones -->
        <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i class="fas fa-comment-alt mr-2 text-primary-600"></i>Observaciones
            </h3>
            
            <textarea name="observaciones" rows="3"
                      placeholder="Notas adicionales sobre la verificación..."
                      class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500"><?= htmlspecialchars($data['observaciones'] ?? '') ?></textarea>
        </div>
        
        <!-- Botones -->
        <div class="flex justify-end space-x-4 pt-4 border-t">
            <a href="<?= BASE_URL ?>/kyc" 
               class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                <i class="fas fa-save mr-2"></i>Guardar Verificación
            </button>
        </div>
    </form>
</div>

<?php if (!$socioPreseleccionado): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const buscarInput = document.getElementById('buscarSocio');
    const resultadosDiv = document.getElementById('resultadosSocio');
    const socioIdInput = document.getElementById('socio_id');
    const socioSeleccionadoDiv = document.getElementById('socioSeleccionado');
    const socioInfoSpan = document.getElementById('socioInfo');
    
    let timeout = null;
    
    buscarInput.addEventListener('input', function() {
        clearTimeout(timeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            resultadosDiv.classList.add('hidden');
            return;
        }
        
        timeout = setTimeout(function() {
            fetch('<?= BASE_URL ?>/socios/buscar?q=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    if (data.results && data.results.length > 0) {
                        resultadosDiv.innerHTML = data.results.map(socio => {
                            const nombreCompleto = (socio.nombre + ' ' + socio.apellido_paterno + ' ' + (socio.apellido_materno || '')).trim();
                            const div = document.createElement('div');
                            div.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer socio-result';
                            div.setAttribute('data-id', socio.id);
                            div.setAttribute('data-numero', socio.numero_socio || '');
                            div.setAttribute('data-nombre', nombreCompleto);
                            div.setAttribute('data-rfc', socio.rfc || 'N/A');
                            div.innerHTML = `<div class="font-medium">${escapeHtml(nombreCompleto)}</div>
                                <div class="text-sm text-gray-500">${escapeHtml(socio.numero_socio || '')} | RFC: ${escapeHtml(socio.rfc || 'N/A')}</div>`;
                            return div.outerHTML;
                        }).join('');
                        
                        // Add event listeners to results
                        resultadosDiv.querySelectorAll('.socio-result').forEach(el => {
                            el.addEventListener('click', function() {
                                seleccionarSocio(
                                    this.getAttribute('data-id'),
                                    this.getAttribute('data-numero'),
                                    this.getAttribute('data-nombre'),
                                    this.getAttribute('data-rfc')
                                );
                            });
                        });
                        
                        resultadosDiv.classList.remove('hidden');
                    } else {
                        resultadosDiv.innerHTML = '<div class="px-4 py-2 text-gray-500">No se encontraron socios</div>';
                        resultadosDiv.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }, 300);
    });
    
    // Cerrar resultados al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!buscarInput.contains(e.target) && !resultadosDiv.contains(e.target)) {
            resultadosDiv.classList.add('hidden');
        }
    });
});

// Escape HTML to prevent XSS
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function seleccionarSocio(id, numeroSocio, nombre, rfc) {
    document.getElementById('socio_id').value = id;
    document.getElementById('buscarSocio').value = '';
    document.getElementById('resultadosSocio').classList.add('hidden');
    document.getElementById('socioInfo').textContent = nombre + ' (' + numeroSocio + ') - RFC: ' + rfc;
    document.getElementById('socioSeleccionado').classList.remove('hidden');
    document.getElementById('buscarSocio').classList.add('hidden');
}

function limpiarSocio() {
    document.getElementById('socio_id').value = '';
    document.getElementById('socioSeleccionado').classList.add('hidden');
    document.getElementById('buscarSocio').classList.remove('hidden');
    document.getElementById('buscarSocio').value = '';
}
</script>
<?php endif; ?>
