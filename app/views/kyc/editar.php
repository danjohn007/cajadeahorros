<!-- Formulario para editar verificación KYC -->
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="<?= BASE_URL ?>/kyc/ver/<?= $verificacion['id'] ?>" class="text-primary-600 hover:text-primary-800">
            <i class="fas fa-arrow-left mr-2"></i>Volver al detalle
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
        
        <!-- Socio (no editable) -->
        <div class="border-b pb-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i class="fas fa-user mr-2 text-primary-600"></i>Socio
            </h3>
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0 h-12 w-12 bg-primary-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-primary-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">
                            <?= htmlspecialchars($verificacion['nombre'] . ' ' . $verificacion['apellido_paterno'] . ' ' . ($verificacion['apellido_materno'] ?? '')) ?>
                        </p>
                        <p class="text-sm text-gray-500"><?= htmlspecialchars($verificacion['numero_socio']) ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Estatus y Nivel de Riesgo -->
        <div class="border-b pb-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i class="fas fa-tasks mr-2 text-primary-600"></i>Estado de la Verificación
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estatus *</label>
                    <select name="estatus" required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="pendiente" <?= $verificacion['estatus'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="aprobado" <?= $verificacion['estatus'] === 'aprobado' ? 'selected' : '' ?>>Aprobado</option>
                        <option value="rechazado" <?= $verificacion['estatus'] === 'rechazado' ? 'selected' : '' ?>>Rechazado</option>
                        <option value="vencido" <?= $verificacion['estatus'] === 'vencido' ? 'selected' : '' ?>>Vencido</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nivel de Riesgo *</label>
                    <select name="nivel_riesgo" required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="bajo" <?= $verificacion['nivel_riesgo'] === 'bajo' ? 'selected' : '' ?>>Bajo</option>
                        <option value="medio" <?= $verificacion['nivel_riesgo'] === 'medio' ? 'selected' : '' ?>>Medio</option>
                        <option value="alto" <?= $verificacion['nivel_riesgo'] === 'alto' ? 'selected' : '' ?>>Alto</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Documento de Identidad -->
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
                        <option value="INE" <?= $verificacion['tipo_documento'] === 'INE' ? 'selected' : '' ?>>INE / IFE</option>
                        <option value="Pasaporte" <?= $verificacion['tipo_documento'] === 'Pasaporte' ? 'selected' : '' ?>>Pasaporte</option>
                        <option value="Cedula Profesional" <?= $verificacion['tipo_documento'] === 'Cedula Profesional' ? 'selected' : '' ?>>Cédula Profesional</option>
                        <option value="Cartilla Militar" <?= $verificacion['tipo_documento'] === 'Cartilla Militar' ? 'selected' : '' ?>>Cartilla del Servicio Militar</option>
                        <option value="Licencia de Conducir" <?= $verificacion['tipo_documento'] === 'Licencia de Conducir' ? 'selected' : '' ?>>Licencia de Conducir</option>
                        <option value="Otro" <?= $verificacion['tipo_documento'] === 'Otro' ? 'selected' : '' ?>>Otro</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Número de Documento *</label>
                    <input type="text" name="numero_documento" 
                           value="<?= htmlspecialchars($verificacion['numero_documento']) ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500"
                           required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Emisión</label>
                    <input type="date" name="fecha_emision" 
                           value="<?= htmlspecialchars($verificacion['fecha_emision'] ?? '') ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Vencimiento</label>
                    <input type="date" name="fecha_vencimiento" 
                           value="<?= htmlspecialchars($verificacion['fecha_vencimiento'] ?? '') ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">País de Emisión</label>
                    <input type="text" name="pais_emision" 
                           value="<?= htmlspecialchars($verificacion['pais_emision'] ?? 'México') ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
        </div>
        
        <!-- Verificaciones -->
        <div class="border-b pb-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i class="fas fa-check-double mr-2 text-primary-600"></i>Estado de Verificaciones
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center">
                    <input type="checkbox" name="documento_verificado" id="documento_verificado"
                           <?= $verificacion['documento_verificado'] ? 'checked' : '' ?>
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="documento_verificado" class="ml-2 text-sm text-gray-700">
                        Documento verificado (autenticidad confirmada)
                    </label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="direccion_verificada" id="direccion_verificada"
                           <?= $verificacion['direccion_verificada'] ? 'checked' : '' ?>
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="direccion_verificada" class="ml-2 text-sm text-gray-700">
                        Dirección verificada (comprobante de domicilio)
                    </label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="identidad_verificada" id="identidad_verificada"
                           <?= $verificacion['identidad_verificada'] ? 'checked' : '' ?>
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="identidad_verificada" class="ml-2 text-sm text-gray-700">
                        Identidad verificada (comparación biométrica)
                    </label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="pep" id="pep"
                           <?= $verificacion['pep'] ? 'checked' : '' ?>
                           class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                    <label for="pep" class="ml-2 text-sm text-gray-700">
                        <span class="text-red-600 font-medium">PEP</span> (Persona Políticamente Expuesta)
                    </label>
                </div>
            </div>
        </div>
        
        <!-- Información Económica -->
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
                        <option value="Empleo" <?= ($verificacion['fuente_ingresos'] ?? '') === 'Empleo' ? 'selected' : '' ?>>Empleo</option>
                        <option value="Negocio Propio" <?= ($verificacion['fuente_ingresos'] ?? '') === 'Negocio Propio' ? 'selected' : '' ?>>Negocio Propio</option>
                        <option value="Inversiones" <?= ($verificacion['fuente_ingresos'] ?? '') === 'Inversiones' ? 'selected' : '' ?>>Inversiones</option>
                        <option value="Pensión" <?= ($verificacion['fuente_ingresos'] ?? '') === 'Pensión' ? 'selected' : '' ?>>Pensión</option>
                        <option value="Remesas" <?= ($verificacion['fuente_ingresos'] ?? '') === 'Remesas' ? 'selected' : '' ?>>Remesas</option>
                        <option value="Otro" <?= ($verificacion['fuente_ingresos'] ?? '') === 'Otro' ? 'selected' : '' ?>>Otro</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Actividad Económica / Giro</label>
                    <input type="text" name="actividad_economica" 
                           value="<?= htmlspecialchars($verificacion['actividad_economica'] ?? '') ?>"
                           placeholder="Ej: Comercio al por menor, Servicios profesionales..."
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
        </div>
        
        <!-- Observaciones -->
        <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i class="fas fa-comment-alt mr-2 text-primary-600"></i>Observaciones
            </h3>
            
            <textarea name="observaciones" rows="3"
                      placeholder="Notas adicionales sobre la verificación..."
                      class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500"><?= htmlspecialchars($verificacion['observaciones'] ?? '') ?></textarea>
        </div>
        
        <!-- Botones -->
        <div class="flex justify-end space-x-4 pt-4 border-t">
            <a href="<?= BASE_URL ?>/kyc/ver/<?= $verificacion['id'] ?>" 
               class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                <i class="fas fa-save mr-2"></i>Guardar Cambios
            </button>
        </div>
    </form>
</div>
