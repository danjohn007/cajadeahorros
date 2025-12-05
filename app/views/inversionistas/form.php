<?php
/**
 * Vista de Formulario de Inversionista
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<!-- Header -->
<div class="mb-6">
    <a href="<?= url('inversionistas') ?>" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Volver a Inversionistas
    </a>
    <h2 class="text-2xl font-bold text-gray-800"><?= $action === 'crear' ? 'Nuevo Inversionista' : 'Editar Inversionista' ?></h2>
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
<form method="POST" action="<?= url('inversionistas/' . ($action === 'crear' ? 'crear' : 'editar/' . ($inversionista['id'] ?? ''))) ?>" 
      class="bg-white rounded-xl shadow-sm">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    
    <!-- Datos Personales -->
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-user text-blue-500 mr-2"></i> Datos Personales
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre(s) *</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($inversionista['nombre'] ?? '') ?>" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Apellido Paterno *</label>
                <input type="text" name="apellido_paterno" value="<?= htmlspecialchars($inversionista['apellido_paterno'] ?? '') ?>" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Apellido Materno</label>
                <input type="text" name="apellido_materno" value="<?= htmlspecialchars($inversionista['apellido_materno'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">RFC</label>
                <input type="text" name="rfc" value="<?= htmlspecialchars($inversionista['rfc'] ?? '') ?>" maxlength="13"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent uppercase">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">CURP</label>
                <input type="text" name="curp" value="<?= htmlspecialchars($inversionista['curp'] ?? '') ?>" maxlength="18"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent uppercase">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento</label>
                <input type="date" name="fecha_nacimiento" value="<?= htmlspecialchars($inversionista['fecha_nacimiento'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
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
                <input type="tel" name="telefono" value="<?= htmlspecialchars($inversionista['telefono'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Celular</label>
                <input type="tel" name="celular" value="<?= htmlspecialchars($inversionista['celular'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico *</label>
                <input type="email" name="email" value="<?= htmlspecialchars($inversionista['email'] ?? '') ?>" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                <input type="text" name="direccion" value="<?= htmlspecialchars($inversionista['direccion'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>
    </div>
    
    <!-- Datos Bancarios -->
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-university text-purple-500 mr-2"></i> Datos Bancarios
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Banco</label>
                <input type="text" name="banco" value="<?= htmlspecialchars($inversionista['banco'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cuenta Bancaria</label>
                <input type="text" name="cuenta_bancaria" value="<?= htmlspecialchars($inversionista['cuenta_bancaria'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">CLABE Interbancaria</label>
                <input type="text" name="clabe" value="<?= htmlspecialchars($inversionista['clabe'] ?? '') ?>" maxlength="18"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>
    </div>
    
    <!-- Notas y Estatus -->
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-sticky-note text-yellow-500 mr-2"></i> Notas y Configuración
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php if ($action === 'editar'): ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estatus</label>
                <select name="estatus" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="activo" <?= ($inversionista['estatus'] ?? '') === 'activo' ? 'selected' : '' ?>>Activo</option>
                    <option value="inactivo" <?= ($inversionista['estatus'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                </select>
            </div>
            <?php endif; ?>
            
            <?php if ($action === 'crear'): ?>
            <div>
                <label class="flex items-center mt-6">
                    <input type="checkbox" name="crear_usuario" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Crear cuenta de usuario para el inversionista</span>
                </label>
                <p class="text-xs text-gray-500 mt-1">Se enviará un email con credenciales de acceso</p>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
            <textarea name="notas" rows="3"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      placeholder="Notas adicionales sobre el inversionista..."><?= htmlspecialchars($inversionista['notas'] ?? '') ?></textarea>
        </div>
    </div>
    
    <!-- Actions -->
    <div class="p-6 bg-gray-50 flex justify-end space-x-4">
        <a href="<?= url('inversionistas') ?>" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
            Cancelar
        </a>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-save mr-2"></i> <?= $action === 'crear' ? 'Crear Inversionista' : 'Guardar Cambios' ?>
        </button>
    </div>
</form>
