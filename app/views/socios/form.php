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
      class="bg-white rounded-xl shadow-sm">
    <input type="hidden" name="csrf_token" value="<?= $this->csrf_token() ?>">
    
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
