<?php
/**
 * Vista de Expediente Digital - Detalle
 * Gestión de documentos de una solicitud específica
 */
?>
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-primary-800">Gestión de Expedientes Digitales</h1>
            <p class="text-gray-600 mt-1">Centraliza y organiza todos los documentos y evidencias relacionados con solicitudes de crédito</p>
        </div>
        <div class="flex space-x-2">
            <button class="bg-white border border-primary-800 text-primary-800 hover:bg-primary-50 px-4 py-2 rounded flex items-center">
                <i class="fas fa-download mr-2"></i>Exportar
            </button>
            <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" 
                    class="bg-primary-800 hover:bg-primary-700 text-white px-4 py-2 rounded flex items-center">
                <i class="fas fa-cloud-upload-alt mr-2"></i>Subir Documento
            </button>
        </div>
    </div>

    <!-- Información de la Solicitud -->
    <div class="bg-gradient-to-r from-primary-800 to-primary-900 rounded-lg shadow-lg p-6 mb-6 text-white">
        <h2 class="text-xl font-bold mb-4">Información de la Solicitud</h2>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <div class="text-sm text-primary-100">No. Solicitud:</div>
                <div class="text-lg font-bold"><?= htmlspecialchars($solicitud['numero_credito'] ?? 'N/A') ?></div>
            </div>
            <div>
                <div class="text-sm text-primary-100">Cliente:</div>
                <div class="text-lg font-bold"><?= htmlspecialchars($solicitud['nombre'] . ' ' . $solicitud['apellido_paterno'] . ' ' . ($solicitud['apellido_materno'] ?? '')) ?></div>
            </div>
            <div>
                <div class="text-sm text-primary-100">Producto:</div>
                <div class="text-lg font-bold"><?= htmlspecialchars($solicitud['producto_nombre'] ?? 'Crédito Personal') ?></div>
            </div>
            <div>
                <div class="text-sm text-primary-100">Monto Solicitado:</div>
                <div class="text-lg font-bold">$<?= number_format($solicitud['monto_solicitado'] ?? 0, 2) ?> MXN</div>
            </div>
            <div>
                <div class="text-sm text-primary-100">Fecha de Solicitud:</div>
                <div class="text-lg font-bold"><?= date('d/m/Y', strtotime($solicitud['fecha_solicitud'] ?? 'now')) ?></div>
            </div>
        </div>
        <div class="mt-4">
            <div class="text-sm text-primary-100">Estado:</div>
            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-yellow-400 text-yellow-900 mt-1">
                <i class="fas fa-circle mr-2" style="font-size: 8px;"></i>
                En Revisión
            </span>
        </div>
    </div>

    <!-- Estadísticas de Documentos -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-3xl font-bold text-blue-600"><?= $stats['total'] ?? 0 ?></div>
            <div class="text-sm text-gray-600 mt-1">Total Documentos</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-3xl font-bold text-green-600"><?= $stats['validados'] ?? 0 ?></div>
            <div class="text-sm text-gray-600 mt-1">Validados</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-3xl font-bold text-yellow-600"><?= $stats['pendientes'] ?? 0 ?></div>
            <div class="text-sm text-gray-600 mt-1">Pendientes</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-3xl font-bold text-red-600"><?= $stats['rechazados'] ?? 0 ?></div>
            <div class="text-sm text-gray-600 mt-1">Rechazados</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-3xl font-bold text-gray-600"><?= $stats['faltantes'] ?? 0 ?></div>
            <div class="text-sm text-gray-600 mt-1">Faltantes</div>
        </div>
    </div>

    <!-- Progreso de Completitud -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Progreso de Completitud del Expediente</h3>
        
        <div class="mb-4">
            <div class="flex justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Documentos Requeridos</span>
                <span class="text-sm font-bold text-primary-800">
                    <?= $stats['total'] ?? 0 ?>/<?= $documentos_requeridos ?? 12 ?> (<?= round((($stats['total'] ?? 0) / ($documentos_requeridos ?? 12)) * 100) ?>%)
                </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-primary-800 h-3 rounded-full" style="width: <?= round((($stats['total'] ?? 0) / ($documentos_requeridos ?? 12)) * 100) ?>%"></div>
            </div>
        </div>

        <div>
            <div class="flex justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Validación Completada</span>
                <span class="text-sm font-bold text-green-600">
                    <?= $stats['validados'] ?? 0 ?>/<?= $stats['total'] ?? 1 ?> (<?= $stats['total'] > 0 ? round((($stats['validados'] ?? 0) / $stats['total']) * 100) : 0 ?>%)
                </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-green-600 h-3 rounded-full" style="width: <?= $stats['total'] > 0 ? round((($stats['validados'] ?? 0) / $stats['total']) * 100) : 0 ?>%"></div>
            </div>
        </div>
    </div>

    <!-- Filtros de Documentos -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Filtros de Documentos</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Categoría:</label>
                <select class="w-full border rounded px-3 py-2">
                    <option value="">Todas las categorías</option>
                    <option value="identificacion">Identificación Oficial</option>
                    <option value="comprobante_ingresos">Comprobante de Ingresos</option>
                    <option value="comprobante_domicilio">Comprobante de Domicilio</option>
                    <option value="estados_cuenta">Estados de Cuenta</option>
                    <option value="referencias">Referencias</option>
                    <option value="garantia">Avalúo de Garantía</option>
                    <option value="otros">Otros</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado:</label>
                <select class="w-full border rounded px-3 py-2">
                    <option value="">Todos los estados</option>
                    <option value="validado">Validado</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="rechazado">Rechazado</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Desde:</label>
                <input type="date" class="w-full border rounded px-3 py-2" value="<?= date('Y-m-d', strtotime('-30 days')) ?>">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Hasta:</label>
                <input type="date" class="w-full border rounded px-3 py-2" value="<?= date('Y-m-d') ?>">
            </div>
        </div>
        <div class="flex justify-start space-x-4 mt-4">
            <button class="bg-primary-800 hover:bg-primary-700 text-white px-4 py-2 rounded flex items-center">
                <i class="fas fa-search mr-2"></i>Filtrar
            </button>
            <button class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded flex items-center">
                <i class="fas fa-times mr-2"></i>Limpiar Filtros
            </button>
        </div>
    </div>

    <!-- Área de Drag & Drop -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-primary-500 transition-colors cursor-pointer"
             onclick="document.getElementById('fileInput').click()">
            <i class="fas fa-cloud-upload-alt text-6xl text-primary-500 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Arrastra y suelta documentos aquí</h3>
            <p class="text-sm text-gray-500 mb-4">o haz clic para seleccionar archivos</p>
            <button type="button" class="bg-primary-800 hover:bg-primary-700 text-white px-6 py-2 rounded">
                <i class="fas fa-folder-open mr-2"></i>Seleccionar Archivos
            </button>
            <p class="text-xs text-gray-400 mt-4">Formatos soportados: PDF, JPG, PNG, DOC, DOCX, XLS, XLSX (Máx. 10MB por archivo)</p>
            <input type="file" id="fileInput" multiple class="hidden" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
        </div>
    </div>

    <!-- Lista de Documentos -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Documentos del Expediente</h3>
        
        <?php if (empty($documentos)): ?>
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-folder-open text-6xl mb-4"></i>
            <p>No hay documentos cargados en este expediente</p>
        </div>
        <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($documentos as $doc): ?>
            <?php
                $estado_clase = 'bg-gray-100 text-gray-800';
                $estado_texto = 'No subido';
                $estado_icono = 'fa-times-circle';
                
                if ($doc['id']) {
                    if (isset($doc['revisado']) && $doc['revisado'] == 1) {
                        $estado_clase = 'bg-green-100 text-green-800';
                        $estado_texto = 'Validado';
                        $estado_icono = 'fa-check-circle';
                    } else if (isset($doc['revisado']) && $doc['revisado'] == -1) {
                        $estado_clase = 'bg-red-100 text-red-800';
                        $estado_texto = 'Rechazado';
                        $estado_icono = 'fa-times-circle';
                    } else {
                        $estado_clase = 'bg-yellow-100 text-yellow-800';
                        $estado_texto = 'Pendiente';
                        $estado_icono = 'fa-clock';
                    }
                }
                
                // Determinar icono de tipo de archivo
                $extension = pathinfo($doc['nombre_archivo'] ?? '', PATHINFO_EXTENSION);
                $file_icon = 'fa-file';
                $file_color = 'text-gray-500';
                
                if (in_array(strtolower($extension), ['pdf'])) {
                    $file_icon = 'fa-file-pdf';
                    $file_color = 'text-red-500';
                } else if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png'])) {
                    $file_icon = 'fa-file-image';
                    $file_color = 'text-blue-500';
                } else if (in_array(strtolower($extension), ['doc', 'docx'])) {
                    $file_icon = 'fa-file-word';
                    $file_color = 'text-blue-600';
                } else if (in_array(strtolower($extension), ['xls', 'xlsx'])) {
                    $file_icon = 'fa-file-excel';
                    $file_color = 'text-green-600';
                }
                
                // Obtener tamaño del archivo si existe
                $file_size_text = 'N/A';
                if (isset($doc['ruta_archivo']) && file_exists($doc['ruta_archivo'])) {
                    $file_size = filesize($doc['ruta_archivo']);
                    $file_size_text = $file_size < 1024 ? $file_size . ' B' : ($file_size < 1048576 ? round($file_size / 1024, 1) . ' KB' : round($file_size / 1048576, 1) . ' MB');
                }
                
                // Número de páginas (solo mostrar si está disponible)
                $num_paginas = isset($doc['num_paginas']) ? $doc['num_paginas'] : 'N/A';
            ?>
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-start mb-3">
                    <div class="flex-shrink-0 mr-3">
                        <i class="fas <?= $file_icon ?> text-4xl <?= $file_color ?>"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-gray-900 truncate"><?= htmlspecialchars($doc['tipo'] ?? 'Documento') ?></h4>
                        <p class="text-xs text-gray-500">Subido: <?= date('d/m/Y H:i A', strtotime($doc['fecha_subida'] ?? 'now')) ?></p>
                        <?php if ($file_size_text !== 'N/A'): ?>
                        <p class="text-xs text-gray-500">Tamaño: <?= $file_size_text ?><?= $num_paginas !== 'N/A' ? ' | Páginas: ' . $num_paginas : '' ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mb-3">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $estado_clase ?>">
                        <i class="fas <?= $estado_icono ?> mr-1"></i>
                        <?= $estado_texto ?>
                    </span>
                </div>
                
                <div class="flex space-x-2">
                    <?php if ($doc['id']): ?>
                    <button class="flex-1 bg-primary-100 text-primary-800 hover:bg-primary-200 px-3 py-1 rounded text-xs font-medium">
                        <i class="fas fa-eye mr-1"></i>Ver
                    </button>
                    <button class="flex-1 bg-gray-100 text-gray-700 hover:bg-gray-200 px-3 py-1 rounded text-xs font-medium">
                        <i class="fas fa-download mr-1"></i>Descargar
                    </button>
                    <?php if (!isset($doc['revisado']) || $doc['revisado'] == 0): ?>
                    <button class="flex-1 bg-green-100 text-green-700 hover:bg-green-200 px-3 py-1 rounded text-xs font-medium">
                        <i class="fas fa-check mr-1"></i>Validar
                    </button>
                    <?php endif; ?>
                    <?php if (isset($doc['revisado']) && $doc['revisado'] == -1): ?>
                    <button class="flex-1 bg-blue-100 text-blue-700 hover:bg-blue-200 px-3 py-1 rounded text-xs font-medium">
                        <i class="fas fa-sync mr-1"></i>Reemplazar
                    </button>
                    <?php endif; ?>
                    <?php else: ?>
                    <button class="flex-1 bg-primary-800 text-white hover:bg-primary-700 px-3 py-1 rounded text-xs font-medium">
                        <i class="fas fa-upload mr-1"></i>Subir Documento
                    </button>
                    <?php endif; ?>
                </div>
                
                <?php if (isset($doc['revisado']) && $doc['revisado'] == -1 && isset($doc['motivo_rechazo'])): ?>
                <div class="mt-3 p-2 bg-red-50 border border-red-200 rounded text-xs text-red-700">
                    <strong>Motivo:</strong> <?= htmlspecialchars($doc['motivo_rechazo']) ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Historial de Actividades -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Historial de Actividades</h3>
        
        <div class="relative border-l-2 border-primary-200 ml-3 pl-6 space-y-6">
            <!-- Actividad 1 -->
            <div class="relative">
                <div class="absolute -left-8 mt-1 w-4 h-4 bg-primary-800 rounded-full border-2 border-white"></div>
                <div class="text-xs text-gray-500 mb-1"><?= date('d/m/Y - H:i A', strtotime('-2 hours')) ?></div>
                <h4 class="font-semibold text-gray-900">Documento subido</h4>
                <p class="text-sm text-gray-600">Se subió el avalúo de garantía por el usuario: <?= htmlspecialchars($_SESSION['user_name'] ?? 'Ana López') ?></p>
            </div>
            
            <!-- Actividad 2 -->
            <div class="relative">
                <div class="absolute -left-8 mt-1 w-4 h-4 bg-green-600 rounded-full border-2 border-white"></div>
                <div class="text-xs text-gray-500 mb-1"><?= date('d/m/Y - H:i A', strtotime('-5 hours')) ?></div>
                <h4 class="font-semibold text-gray-900">Documento validado</h4>
                <p class="text-sm text-gray-600">Estados de cuenta bancarios validados por el analista: Carlos Mendoza</p>
            </div>
            
            <!-- Actividad 3 -->
            <div class="relative">
                <div class="absolute -left-8 mt-1 w-4 h-4 bg-red-600 rounded-full border-2 border-white"></div>
                <div class="text-xs text-gray-500 mb-1"><?= date('d/m/Y - H:i A', strtotime('-1 day')) ?></div>
                <h4 class="font-semibold text-gray-900">Documento rechazado</h4>
                <p class="text-sm text-gray-600">Referencias personales rechazadas por información incompleta</p>
            </div>
            
            <!-- Actividad 4 -->
            <div class="relative">
                <div class="absolute -left-8 mt-1 w-4 h-4 bg-gray-400 rounded-full border-2 border-white"></div>
                <div class="text-xs text-gray-500 mb-1"><?= date('d/m/Y - H:i A', strtotime('-2 days')) ?></div>
                <h4 class="font-semibold text-gray-900">Expediente creado</h4>
                <p class="text-sm text-gray-600">Se creó el expediente digital para la solicitud</p>
            </div>
        </div>
    </div>

    <!-- Botón de Regresar -->
    <div class="mt-6">
        <a href="<?= BASE_URL ?>/solicitudes/expedientes" 
           class="inline-flex items-center bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Regresar a Lista de Expedientes
        </a>
    </div>
</div>

<!-- Modal de Subir Documento -->
<div id="uploadModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">Subir Documento</h3>
            <button onclick="document.getElementById('uploadModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Documento</label>
                <select name="tipo_documento" class="w-full border rounded px-3 py-2" required>
                    <option value="">Seleccionar tipo</option>
                    <option value="Identificación Oficial (INE)">Identificación Oficial (INE)</option>
                    <option value="Comprobante de Ingresos">Comprobante de Ingresos</option>
                    <option value="Comprobante de Domicilio">Comprobante de Domicilio</option>
                    <option value="Estados de Cuenta Bancarios">Estados de Cuenta Bancarios</option>
                    <option value="Referencias Personales">Referencias Personales</option>
                    <option value="Avalúo de Garantía">Avalúo de Garantía</option>
                    <option value="Carta de No Adeudo">Carta de No Adeudo</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Archivo</label>
                <input type="file" name="archivo" class="w-full border rounded px-3 py-2" required>
            </div>
            
            <div class="flex justify-end space-x-2">
                <button type="button" 
                        onclick="document.getElementById('uploadModal').classList.add('hidden')"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded">
                    Cancelar
                </button>
                <button type="submit" class="bg-primary-800 hover:bg-primary-700 text-white px-4 py-2 rounded">
                    <i class="fas fa-upload mr-2"></i>Subir
                </button>
            </div>
        </form>
    </div>
</div>
