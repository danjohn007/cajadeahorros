<!-- Header -->
<div class="mb-6">
    <a href="<?= BASE_URL ?>/socios" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Volver a Socios
    </a>
    <h2 class="text-2xl font-bold text-gray-800"><?= $action === 'crear' ? 'Nuevo Socio' : 'Editar Socio' ?></h2>
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
<form method="POST" action="<?= BASE_URL ?>/socios/<?= $action === 'crear' ? 'crear' : 'editar/' . ($socio['id'] ?? '') ?>" 
      class="bg-white rounded-xl shadow-sm" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    
    <!-- Datos Personales -->
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-user text-blue-500 mr-2"></i> Datos Personales
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre(s) *</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($socio['nombre'] ?? '') ?>" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Apellido Paterno *</label>
                <input type="text" name="apellido_paterno" value="<?= htmlspecialchars($socio['apellido_paterno'] ?? '') ?>" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Apellido Materno</label>
                <input type="text" name="apellido_materno" value="<?= htmlspecialchars($socio['apellido_materno'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">RFC</label>
                <input type="text" name="rfc" value="<?= htmlspecialchars($socio['rfc'] ?? '') ?>" maxlength="13"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent uppercase">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">CURP</label>
                <input type="text" name="curp" value="<?= htmlspecialchars($socio['curp'] ?? '') ?>" maxlength="18"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent uppercase">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento</label>
                <input type="date" name="fecha_nacimiento" value="<?= htmlspecialchars($socio['fecha_nacimiento'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Género</label>
                <select name="genero" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Seleccionar...</option>
                    <option value="M" <?= ($socio['genero'] ?? '') === 'M' ? 'selected' : '' ?>>Masculino</option>
                    <option value="F" <?= ($socio['genero'] ?? '') === 'F' ? 'selected' : '' ?>>Femenino</option>
                    <option value="O" <?= ($socio['genero'] ?? '') === 'O' ? 'selected' : '' ?>>Otro</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado Civil</label>
                <select name="estado_civil" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Seleccionar...</option>
                    <option value="soltero" <?= ($socio['estado_civil'] ?? '') === 'soltero' ? 'selected' : '' ?>>Soltero(a)</option>
                    <option value="casado" <?= ($socio['estado_civil'] ?? '') === 'casado' ? 'selected' : '' ?>>Casado(a)</option>
                    <option value="divorciado" <?= ($socio['estado_civil'] ?? '') === 'divorciado' ? 'selected' : '' ?>>Divorciado(a)</option>
                    <option value="viudo" <?= ($socio['estado_civil'] ?? '') === 'viudo' ? 'selected' : '' ?>>Viudo(a)</option>
                    <option value="union_libre" <?= ($socio['estado_civil'] ?? '') === 'union_libre' ? 'selected' : '' ?>>Unión Libre</option>
                </select>
            </div>
            <?php if ($action === 'editar'): ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estatus</label>
                <select name="estatus" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="activo" <?= ($socio['estatus'] ?? '') === 'activo' ? 'selected' : '' ?>>Activo</option>
                    <option value="inactivo" <?= ($socio['estatus'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                    <option value="suspendido" <?= ($socio['estatus'] ?? '') === 'suspendido' ? 'selected' : '' ?>>Suspendido</option>
                </select>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Contacto -->
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-phone text-green-500 mr-2"></i> Información de Contacto
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                <input type="tel" name="telefono" value="<?= htmlspecialchars($socio['telefono'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Celular</label>
                <input type="tel" name="celular" value="<?= htmlspecialchars($socio['celular'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                <input type="email" name="email" value="<?= htmlspecialchars($socio['email'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>
    </div>
    
    <!-- Dirección -->
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-map-marker-alt text-red-500 mr-2"></i> Dirección
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Dirección (Calle y Número)</label>
                <input type="text" name="direccion" value="<?= htmlspecialchars($socio['direccion'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Colonia</label>
                <input type="text" name="colonia" value="<?= htmlspecialchars($socio['colonia'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Municipio</label>
                <input type="text" name="municipio" value="<?= htmlspecialchars($socio['municipio'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <input type="text" name="estado" value="<?= htmlspecialchars($socio['estado'] ?? 'Querétaro') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Código Postal</label>
                <input type="text" name="codigo_postal" value="<?= htmlspecialchars($socio['codigo_postal'] ?? '') ?>" maxlength="5"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>
    </div>
    
    <!-- Información Laboral -->
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-briefcase text-purple-500 mr-2"></i> Información Laboral
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Unidad de Trabajo</label>
                <select name="unidad_trabajo_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Seleccionar...</option>
                    <?php foreach ($unidades as $unidad): ?>
                    <option value="<?= $unidad['id'] ?>" <?= ($socio['unidad_trabajo_id'] ?? '') == $unidad['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($unidad['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Puesto</label>
                <input type="text" name="puesto" value="<?= htmlspecialchars($socio['puesto'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Número de Empleado</label>
                <input type="text" name="numero_empleado" value="<?= htmlspecialchars($socio['numero_empleado'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Ingreso</label>
                <input type="date" name="fecha_ingreso_trabajo" value="<?= htmlspecialchars($socio['fecha_ingreso_trabajo'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Salario Mensual</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                    <input type="number" name="salario_mensual" value="<?= htmlspecialchars($socio['salario_mensual'] ?? '') ?>" 
                           step="0.01" min="0"
                           class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Beneficiario -->
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-users text-orange-500 mr-2"></i> Beneficiario
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Beneficiario</label>
                <input type="text" name="beneficiario_nombre" value="<?= htmlspecialchars($socio['beneficiario_nombre'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Parentesco</label>
                <input type="text" name="beneficiario_parentesco" value="<?= htmlspecialchars($socio['beneficiario_parentesco'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono del Beneficiario</label>
                <input type="tel" name="beneficiario_telefono" value="<?= htmlspecialchars($socio['beneficiario_telefono'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>
    </div>
    
    <!-- Identificación Oficial -->
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-id-card text-teal-500 mr-2"></i> Identificación Oficial
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Adjuntar Identificación</label>
                <div class="flex items-center space-x-4">
                    <!-- File Upload -->
                    <div class="flex-1">
                        <input type="file" name="identificacion_oficial" id="identificacion_oficial" 
                               accept="image/*" capture="environment"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               onchange="previewImage(this)">
                        <p class="text-xs text-gray-500 mt-1">Formatos: JPG, PNG, JPEG. Max 5MB</p>
                    </div>
                    <!-- Camera Button for Mobile -->
                    <button type="button" onclick="openCamera()" 
                            class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition"
                            title="Tomar foto con cámara">
                        <i class="fas fa-camera"></i>
                    </button>
                </div>
                
                <!-- Image Preview -->
                <div id="imagePreviewContainer" class="mt-4 hidden">
                    <p class="text-sm font-medium text-gray-700 mb-2">Vista previa:</p>
                    <img id="imagePreview" src="" alt="Vista previa" 
                         class="max-w-xs max-h-48 rounded-lg border border-gray-300">
                </div>
                
                <?php if (!empty($socio['identificacion_oficial'])): ?>
                <div class="mt-4">
                    <p class="text-sm font-medium text-gray-700 mb-2">Identificación actual:</p>
                    <img src="<?= BASE_URL ?>/uploads/identificaciones/<?= htmlspecialchars($socio['identificacion_oficial']) ?>" 
                         alt="Identificación actual" class="max-w-xs max-h-48 rounded-lg border border-gray-300">
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Camera Modal -->
            <div id="cameraModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center">
                <div class="bg-white rounded-lg p-4 max-w-lg w-full mx-4">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-lg font-semibold">Capturar Identificación</h4>
                        <button type="button" onclick="closeCamera()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <video id="cameraVideo" autoplay playsinline class="w-full rounded-lg"></video>
                    <canvas id="cameraCanvas" class="hidden"></canvas>
                    <div class="mt-4 flex justify-center space-x-4">
                        <button type="button" onclick="takePhoto()" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-camera mr-2"></i>Capturar
                        </button>
                        <button type="button" onclick="closeCamera()" 
                                class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-100">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Observaciones -->
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-sticky-note text-yellow-500 mr-2"></i> Observaciones
        </h3>
        
        <textarea name="observaciones" rows="3"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Notas adicionales sobre el socio..."><?= htmlspecialchars($socio['observaciones'] ?? '') ?></textarea>
    </div>
    
    <!-- Actions -->
    <div class="p-6 bg-gray-50 flex justify-end space-x-4">
        <a href="<?= BASE_URL ?>/socios" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
            Cancelar
        </a>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-save mr-2"></i> <?= $action === 'crear' ? 'Crear Socio' : 'Guardar Cambios' ?>
        </button>
    </div>
</form>

<script>
let videoStream = null;

function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const container = document.getElementById('imagePreviewContainer');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            container.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

async function openCamera() {
    const modal = document.getElementById('cameraModal');
    const video = document.getElementById('cameraVideo');
    
    try {
        videoStream = await navigator.mediaDevices.getUserMedia({ 
            video: { 
                facingMode: 'environment',
                width: { ideal: 1280 },
                height: { ideal: 720 }
            } 
        });
        video.srcObject = videoStream;
        modal.classList.remove('hidden');
    } catch (err) {
        alert('No se pudo acceder a la cámara. Asegúrese de dar permisos de cámara.');
        console.error('Error accessing camera:', err);
    }
}

function closeCamera() {
    const modal = document.getElementById('cameraModal');
    const video = document.getElementById('cameraVideo');
    
    if (videoStream) {
        videoStream.getTracks().forEach(track => track.stop());
        videoStream = null;
    }
    video.srcObject = null;
    modal.classList.add('hidden');
}

function takePhoto() {
    const video = document.getElementById('cameraVideo');
    const canvas = document.getElementById('cameraCanvas');
    const preview = document.getElementById('imagePreview');
    const container = document.getElementById('imagePreviewContainer');
    const input = document.getElementById('identificacion_oficial');
    
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    
    canvas.toBlob(function(blob) {
        // Generate a unique file name using timestamp and random string
        const uniqueId = Date.now().toString(36) + Math.random().toString(36).substr(2, 9);
        const file = new File([blob], 'identificacion_' + uniqueId + '.jpg', { type: 'image/jpeg' });
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        input.files = dataTransfer.files;
        
        preview.src = URL.createObjectURL(blob);
        container.classList.remove('hidden');
        
        closeCamera();
    }, 'image/jpeg', 0.8);
}
</script>
