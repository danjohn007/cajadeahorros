<!-- Vista de detalle de verificación KYC -->
<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="<?= BASE_URL ?>/kyc" class="text-primary-600 hover:text-primary-800">
            <i class="fas fa-arrow-left mr-2"></i>Volver a verificaciones
        </a>
    </div>
    
    <!-- Cabecera con estatus -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0 h-16 w-16 bg-primary-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-check text-primary-600 text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">
                        <?= htmlspecialchars($verificacion['nombre'] . ' ' . $verificacion['apellido_paterno'] . ' ' . ($verificacion['apellido_materno'] ?? '')) ?>
                    </h2>
                    <p class="text-gray-500">
                        <?= htmlspecialchars($verificacion['numero_socio']) ?> | 
                        RFC: <?= htmlspecialchars($verificacion['rfc'] ?? 'N/A') ?>
                    </p>
                </div>
            </div>
            
            <div class="flex flex-wrap gap-2">
                <?php
                $estatusClases = [
                    'pendiente' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                    'aprobado' => 'bg-green-100 text-green-800 border-green-300',
                    'rechazado' => 'bg-red-100 text-red-800 border-red-300',
                    'vencido' => 'bg-gray-100 text-gray-800 border-gray-300'
                ];
                $riesgoClases = [
                    'bajo' => 'bg-green-100 text-green-800',
                    'medio' => 'bg-yellow-100 text-yellow-800',
                    'alto' => 'bg-red-100 text-red-800'
                ];
                ?>
                <span class="px-3 py-1 text-sm rounded-full border <?= $estatusClases[$verificacion['estatus']] ?? '' ?>">
                    <i class="fas fa-<?= $verificacion['estatus'] === 'aprobado' ? 'check-circle' : ($verificacion['estatus'] === 'rechazado' ? 'times-circle' : 'clock') ?> mr-1"></i>
                    <?= ucfirst($verificacion['estatus']) ?>
                </span>
                <span class="px-3 py-1 text-sm rounded-full <?= $riesgoClases[$verificacion['nivel_riesgo']] ?? 'bg-gray-100' ?>">
                    <i class="fas fa-shield-alt mr-1"></i>Riesgo <?= ucfirst($verificacion['nivel_riesgo']) ?>
                </span>
                <?php if ($verificacion['pep']): ?>
                <span class="px-3 py-1 text-sm rounded-full bg-purple-100 text-purple-800">
                    <i class="fas fa-user-tie mr-1"></i>PEP
                </span>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Acciones -->
        <div class="mt-4 pt-4 border-t flex flex-wrap gap-2">
            <?php if (in_array($_SESSION['user_role'], ['administrador', 'operativo'])): ?>
            <a href="<?= BASE_URL ?>/kyc/editar/<?= $verificacion['id'] ?>" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                <i class="fas fa-edit mr-1"></i>Editar
            </a>
            <?php endif; ?>
            
            <?php if ($_SESSION['user_role'] === 'administrador' && $verificacion['estatus'] === 'pendiente'): ?>
            <button onclick="document.getElementById('modalAprobar').classList.remove('hidden')"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">
                <i class="fas fa-check mr-1"></i>Aprobar
            </button>
            <button onclick="document.getElementById('modalRechazar').classList.remove('hidden')"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm">
                <i class="fas fa-times mr-1"></i>Rechazar
            </button>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Información del Documento -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-id-card text-primary-600 mr-2"></i>Documento de Identidad
            </h3>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Tipo de Documento:</dt>
                    <dd class="font-medium"><?= htmlspecialchars($verificacion['tipo_documento']) ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Número:</dt>
                    <dd class="font-medium"><?= htmlspecialchars($verificacion['numero_documento']) ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">País de Emisión:</dt>
                    <dd class="font-medium"><?= htmlspecialchars($verificacion['pais_emision'] ?? 'México') ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Fecha Emisión:</dt>
                    <dd class="font-medium"><?= $verificacion['fecha_emision'] ? date('d/m/Y', strtotime($verificacion['fecha_emision'])) : 'N/A' ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Fecha Vencimiento:</dt>
                    <dd class="font-medium <?= $verificacion['fecha_vencimiento'] && strtotime($verificacion['fecha_vencimiento']) < time() ? 'text-red-600' : '' ?>">
                        <?= $verificacion['fecha_vencimiento'] ? date('d/m/Y', strtotime($verificacion['fecha_vencimiento'])) : 'N/A' ?>
                        <?php if ($verificacion['fecha_vencimiento'] && strtotime($verificacion['fecha_vencimiento']) < time()): ?>
                        <span class="text-red-500 text-xs ml-1">(Vencido)</span>
                        <?php endif; ?>
                    </dd>
                </div>
            </dl>
        </div>
        
        <!-- Estado de Verificaciones -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-check-double text-primary-600 mr-2"></i>Estado de Verificaciones
            </h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-gray-700">Documento Verificado</span>
                    <?php if ($verificacion['documento_verificado']): ?>
                    <span class="text-green-600"><i class="fas fa-check-circle"></i> Verificado</span>
                    <?php else: ?>
                    <span class="text-red-600"><i class="fas fa-times-circle"></i> No verificado</span>
                    <?php endif; ?>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-700">Dirección Verificada</span>
                    <?php if ($verificacion['direccion_verificada']): ?>
                    <span class="text-green-600"><i class="fas fa-check-circle"></i> Verificada</span>
                    <?php else: ?>
                    <span class="text-red-600"><i class="fas fa-times-circle"></i> No verificada</span>
                    <?php endif; ?>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-700">Identidad Verificada</span>
                    <?php if ($verificacion['identidad_verificada']): ?>
                    <span class="text-green-600"><i class="fas fa-check-circle"></i> Verificada</span>
                    <?php else: ?>
                    <span class="text-red-600"><i class="fas fa-times-circle"></i> No verificada</span>
                    <?php endif; ?>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-700">PEP (Persona Políticamente Expuesta)</span>
                    <?php if ($verificacion['pep']): ?>
                    <span class="text-purple-600 font-medium"><i class="fas fa-user-tie"></i> Sí</span>
                    <?php else: ?>
                    <span class="text-gray-500">No</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Datos del Socio -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-user text-primary-600 mr-2"></i>Datos del Socio
            </h3>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-gray-500">CURP:</dt>
                    <dd class="font-medium"><?= htmlspecialchars($verificacion['curp'] ?? 'N/A') ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Fecha Nacimiento:</dt>
                    <dd class="font-medium"><?= $verificacion['fecha_nacimiento'] ? date('d/m/Y', strtotime($verificacion['fecha_nacimiento'])) : 'N/A' ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Teléfono:</dt>
                    <dd class="font-medium"><?= htmlspecialchars($verificacion['telefono'] ?? 'N/A') ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Email:</dt>
                    <dd class="font-medium"><?= htmlspecialchars($verificacion['email'] ?? 'N/A') ?></dd>
                </div>
            </dl>
            <div class="mt-4 pt-4 border-t">
                <dt class="text-gray-500 mb-1">Dirección:</dt>
                <dd class="text-sm">
                    <?= htmlspecialchars($verificacion['direccion'] ?? '') ?><br>
                    <?= htmlspecialchars(implode(', ', array_filter([$verificacion['colonia'] ?? '', $verificacion['municipio'] ?? '', $verificacion['estado'] ?? '', $verificacion['codigo_postal'] ?? '']))) ?>
                </dd>
            </div>
        </div>
        
        <!-- Información Económica -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-briefcase text-primary-600 mr-2"></i>Información Económica
            </h3>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Fuente de Ingresos:</dt>
                    <dd class="font-medium"><?= htmlspecialchars($verificacion['fuente_ingresos'] ?? 'No especificado') ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Actividad Económica:</dt>
                    <dd class="font-medium"><?= htmlspecialchars($verificacion['actividad_economica'] ?? 'No especificado') ?></dd>
                </div>
            </dl>
        </div>
    </div>
    
    <!-- Documentos Adjuntos -->
    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-file-alt text-primary-600 mr-2"></i>Documentos Adjuntos
            </h3>
            <?php if (in_array($_SESSION['user_role'], ['administrador', 'operativo'])): ?>
            <button onclick="document.getElementById('modalDocumento').classList.remove('hidden')"
                    class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition text-sm">
                <i class="fas fa-upload mr-1"></i>Subir Documento
            </button>
            <?php endif; ?>
        </div>
        
        <?php if (empty($documentos)): ?>
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-folder-open text-4xl mb-2"></i>
            <p>No hay documentos adjuntos</p>
        </div>
        <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($documentos as $doc): ?>
            <div class="border rounded-lg p-4 hover:shadow-md transition">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-gray-100 rounded flex items-center justify-center">
                        <?php
                        $ext = strtolower(pathinfo($doc['nombre_archivo'], PATHINFO_EXTENSION));
                        $icon = 'fa-file';
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) $icon = 'fa-file-image';
                        elseif ($ext === 'pdf') $icon = 'fa-file-pdf';
                        ?>
                        <i class="fas <?= $icon ?> text-gray-500"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate"><?= htmlspecialchars($doc['nombre_archivo']) ?></p>
                        <p class="text-xs text-gray-500"><?= htmlspecialchars($doc['tipo_documento']) ?> | <?= date('d/m/Y', strtotime($doc['fecha_subida'])) ?></p>
                    </div>
                    <a href="<?= BASE_URL ?>/kyc/descargar/<?= (int)$doc['id'] ?>" target="_blank"
                       class="text-primary-600 hover:text-primary-800" title="Descargar">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Historial -->
    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-history text-primary-600 mr-2"></i>Historial de la Verificación
        </h3>
        
        <?php if (empty($historial)): ?>
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-clock text-4xl mb-2"></i>
            <p>No hay historial de cambios</p>
        </div>
        <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($historial as $h): ?>
            <div class="flex items-start space-x-3 border-l-2 border-primary-200 pl-4">
                <div class="flex-shrink-0 w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-edit text-primary-600 text-sm"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($h['accion']) ?></p>
                    <p class="text-sm text-gray-600"><?= htmlspecialchars($h['descripcion']) ?></p>
                    <p class="text-xs text-gray-500 mt-1">
                        <?= htmlspecialchars($h['usuario_nombre'] ?? 'Sistema') ?> - <?= date('d/m/Y H:i', strtotime($h['created_at'])) ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Observaciones -->
    <?php if ($verificacion['observaciones']): ?>
    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-comment-alt text-primary-600 mr-2"></i>Observaciones
        </h3>
        <p class="text-gray-700"><?= nl2br(htmlspecialchars($verificacion['observaciones'])) ?></p>
    </div>
    <?php endif; ?>
    
    <!-- Info de verificación -->
    <?php if ($verificacion['verificado_por']): ?>
    <div class="bg-gray-50 rounded-lg p-4 mt-6 text-sm text-gray-600">
        <i class="fas fa-info-circle mr-1"></i>
        Verificado por <strong><?= htmlspecialchars($verificacion['verificado_nombre']) ?></strong> 
        el <?= date('d/m/Y H:i', strtotime($verificacion['fecha_verificacion'])) ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Aprobar -->
<div id="modalAprobar" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Aprobar Verificación KYC</h3>
        <form method="POST" action="<?= BASE_URL ?>/kyc/aprobar/<?= $verificacion['id'] ?>">
            <input type="hidden" name="csrf_token" value="<?= $this->csrf_token() ?>">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones (opcional)</label>
                <textarea name="observaciones" rows="3" 
                          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500"
                          placeholder="Notas sobre la aprobación..."></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('modalAprobar').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-check mr-1"></i>Aprobar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Rechazar -->
<div id="modalRechazar" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Rechazar Verificación KYC</h3>
        <form method="POST" action="<?= BASE_URL ?>/kyc/rechazar/<?= $verificacion['id'] ?>">
            <input type="hidden" name="csrf_token" value="<?= $this->csrf_token() ?>">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Motivo del Rechazo *</label>
                <textarea name="motivo" rows="3" required
                          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500"
                          placeholder="Indique el motivo del rechazo..."></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('modalRechazar').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    <i class="fas fa-times mr-1"></i>Rechazar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Subir Documento -->
<div id="modalDocumento" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Subir Documento</h3>
        <form method="POST" action="<?= BASE_URL ?>/kyc/documentos/<?= $verificacion['id'] ?>" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $this->csrf_token() ?>">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Documento</label>
                <select name="tipo_documento" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="identificacion">Identificación Oficial</option>
                    <option value="comprobante_domicilio">Comprobante de Domicilio</option>
                    <option value="comprobante_ingresos">Comprobante de Ingresos</option>
                    <option value="fotografia">Fotografía</option>
                    <option value="otro">Otro</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Archivo</label>
                <input type="file" name="documento" required accept=".jpg,.jpeg,.png,.pdf"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500">
                <p class="text-xs text-gray-500 mt-1">Formatos permitidos: JPG, PNG, PDF. Máximo 5MB</p>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('modalDocumento').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                    <i class="fas fa-upload mr-1"></i>Subir
                </button>
            </div>
        </form>
    </div>
</div>
