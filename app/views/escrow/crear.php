<?php
/**
 * Vista de creación de transacción ESCROW
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
        <p class="text-gray-600">Complete los datos para crear una nueva transacción ESCROW</p>
    </div>
    <a href="<?= url('escrow') ?>" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Volver
    </a>
</div>

<?php if (!empty($errors)): ?>
<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
    <ul class="list-disc list-inside">
        <?php foreach ($errors as $error): ?>
        <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST" action="<?= url('escrow/crear') ?>" class="space-y-6">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    
    <!-- Información General -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-info-circle mr-2 text-blue-600"></i>Información General
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="titulo" class="block text-sm font-medium text-gray-700 mb-1">
                    Título de la Transacción <span class="text-red-500">*</span>
                </label>
                <input type="text" id="titulo" name="titulo" required
                       value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>"
                       placeholder="Ej: Compra de vehículo, Proyecto de construcción..."
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
            </div>
            
            <div>
                <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">
                    Tipo de Transacción
                </label>
                <select id="tipo" name="tipo" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                    <option value="compraventa" <?= ($_POST['tipo'] ?? '') === 'compraventa' ? 'selected' : '' ?>>Compraventa</option>
                    <option value="servicio" <?= ($_POST['tipo'] ?? '') === 'servicio' ? 'selected' : '' ?>>Prestación de Servicios</option>
                    <option value="proyecto" <?= ($_POST['tipo'] ?? '') === 'proyecto' ? 'selected' : '' ?>>Proyecto/Obra</option>
                    <option value="otro" <?= ($_POST['tipo'] ?? '') === 'otro' ? 'selected' : '' ?>>Otro</option>
                </select>
            </div>
            
            <div class="md:col-span-2">
                <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">
                    Descripción
                </label>
                <textarea id="descripcion" name="descripcion" rows="3"
                          placeholder="Describa los detalles de la transacción, condiciones, entregables, etc."
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border"><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
            </div>
        </div>
    </div>
    
    <!-- Montos -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-dollar-sign mr-2 text-green-600"></i>Información Financiera
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label for="monto_total" class="block text-sm font-medium text-gray-700 mb-1">
                    Monto Total <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                    <input type="number" id="monto_total" name="monto_total" required
                           value="<?= htmlspecialchars($_POST['monto_total'] ?? '') ?>"
                           step="0.01" min="<?= getConfig('escrow_monto_minimo', '100') ?>"
                           placeholder="0.00"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 pl-8 pr-4 py-2 border">
                </div>
                <p class="text-xs text-gray-500 mt-1">Monto mínimo: $<?= number_format(floatval(getConfig('escrow_monto_minimo', '100')), 2) ?></p>
            </div>
            
            <div>
                <label for="comision_porcentaje" class="block text-sm font-medium text-gray-700 mb-1">
                    Comisión ESCROW (%)
                </label>
                <input type="number" id="comision_porcentaje" name="comision_porcentaje"
                       value="<?= htmlspecialchars($_POST['comision_porcentaje'] ?? $comisionDefecto) ?>"
                       step="0.01" min="0" max="10"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
            </div>
            
            <div>
                <label for="fecha_limite" class="block text-sm font-medium text-gray-700 mb-1">
                    Fecha Límite
                </label>
                <input type="date" id="fecha_limite" name="fecha_limite"
                       value="<?= htmlspecialchars($_POST['fecha_limite'] ?? date('Y-m-d', strtotime('+' . $diasLimiteDefecto . ' days'))) ?>"
                       min="<?= date('Y-m-d') ?>"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
            </div>
        </div>
    </div>
    
    <!-- Comprador -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-shopping-cart mr-2 text-blue-600"></i>Datos del Comprador
        </h2>
        
        <div class="mb-4">
            <label class="inline-flex items-center">
                <input type="radio" name="comprador_tipo" value="socio" checked
                       class="form-radio text-blue-600" onchange="toggleCompradorFields()">
                <span class="ml-2">Socio registrado</span>
            </label>
            <label class="inline-flex items-center ml-6">
                <input type="radio" name="comprador_tipo" value="externo"
                       class="form-radio text-blue-600" onchange="toggleCompradorFields()">
                <span class="ml-2">Persona externa</span>
            </label>
        </div>
        
        <div id="comprador_socio_fields">
            <label for="comprador_id" class="block text-sm font-medium text-gray-700 mb-1">
                Seleccionar Socio
            </label>
            <select id="comprador_id" name="comprador_id" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                <option value="">-- Seleccione un socio --</option>
                <?php foreach ($socios as $socio): ?>
                <option value="<?= $socio['id'] ?>" <?= ($_POST['comprador_id'] ?? '') == $socio['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($socio['numero_socio'] . ' - ' . $socio['nombre'] . ' ' . $socio['apellido_paterno']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div id="comprador_externo_fields" class="hidden grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div>
                <label for="comprador_nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
                <input type="text" id="comprador_nombre" name="comprador_nombre"
                       value="<?= htmlspecialchars($_POST['comprador_nombre'] ?? '') ?>"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
            </div>
            <div>
                <label for="comprador_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="comprador_email" name="comprador_email"
                       value="<?= htmlspecialchars($_POST['comprador_email'] ?? '') ?>"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
            </div>
            <div>
                <label for="comprador_telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                <input type="tel" id="comprador_telefono" name="comprador_telefono"
                       value="<?= htmlspecialchars($_POST['comprador_telefono'] ?? '') ?>"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
            </div>
        </div>
    </div>
    
    <!-- Vendedor -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-store mr-2 text-green-600"></i>Datos del Vendedor
        </h2>
        
        <div class="mb-4">
            <label class="inline-flex items-center">
                <input type="radio" name="vendedor_tipo" value="socio" checked
                       class="form-radio text-blue-600" onchange="toggleVendedorFields()">
                <span class="ml-2">Socio registrado</span>
            </label>
            <label class="inline-flex items-center ml-6">
                <input type="radio" name="vendedor_tipo" value="externo"
                       class="form-radio text-blue-600" onchange="toggleVendedorFields()">
                <span class="ml-2">Persona externa</span>
            </label>
        </div>
        
        <div id="vendedor_socio_fields">
            <label for="vendedor_id" class="block text-sm font-medium text-gray-700 mb-1">
                Seleccionar Socio
            </label>
            <select id="vendedor_id" name="vendedor_id" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                <option value="">-- Seleccione un socio --</option>
                <?php foreach ($socios as $socio): ?>
                <option value="<?= $socio['id'] ?>" <?= ($_POST['vendedor_id'] ?? '') == $socio['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($socio['numero_socio'] . ' - ' . $socio['nombre'] . ' ' . $socio['apellido_paterno']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div id="vendedor_externo_fields" class="hidden grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div>
                <label for="vendedor_nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
                <input type="text" id="vendedor_nombre" name="vendedor_nombre"
                       value="<?= htmlspecialchars($_POST['vendedor_nombre'] ?? '') ?>"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
            </div>
            <div>
                <label for="vendedor_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="vendedor_email" name="vendedor_email"
                       value="<?= htmlspecialchars($_POST['vendedor_email'] ?? '') ?>"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
            </div>
            <div>
                <label for="vendedor_telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                <input type="tel" id="vendedor_telefono" name="vendedor_telefono"
                       value="<?= htmlspecialchars($_POST['vendedor_telefono'] ?? '') ?>"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
            </div>
        </div>
    </div>
    
    <!-- Botones -->
    <div class="flex justify-end space-x-4">
        <a href="<?= url('escrow') ?>" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
            Cancelar
        </a>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-save mr-2"></i>Crear Transacción
        </button>
    </div>
</form>

<script>
function toggleCompradorFields() {
    const tipo = document.querySelector('input[name="comprador_tipo"]:checked').value;
    document.getElementById('comprador_socio_fields').classList.toggle('hidden', tipo !== 'socio');
    document.getElementById('comprador_externo_fields').classList.toggle('hidden', tipo !== 'externo');
}

function toggleVendedorFields() {
    const tipo = document.querySelector('input[name="vendedor_tipo"]:checked').value;
    document.getElementById('vendedor_socio_fields').classList.toggle('hidden', tipo !== 'socio');
    document.getElementById('vendedor_externo_fields').classList.toggle('hidden', tipo !== 'externo');
}
</script>
