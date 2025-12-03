<?php
/**
 * Vista de Formulario para Registrar Interacción
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6">
    <a href="<?= BASE_URL ?>/crm/interacciones" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Volver a Interacciones
    </a>
    <h2 class="text-2xl font-bold text-gray-800">Registrar Interacción</h2>
    <p class="text-gray-600">Registra una nueva comunicación o seguimiento con un cliente</p>
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

<div class="bg-white rounded-lg shadow-md p-6">
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Selección de Cliente -->
            <div class="md:col-span-2">
                <label for="socio_id" class="block text-sm font-medium text-gray-700 mb-1">Cliente *</label>
                <?php if ($socio): ?>
                    <input type="hidden" name="socio_id" value="<?= $socio['id'] ?>">
                    <div class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-md">
                        <strong><?= htmlspecialchars($socio['nombre']) ?></strong> - <?= htmlspecialchars($socio['numero_socio']) ?>
                    </div>
                <?php else: ?>
                    <select name="socio_id" id="socio_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Seleccione un cliente</option>
                        <?php foreach ($socios as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nombre'] . ' - ' . $s['numero_socio']) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>
            
            <!-- Tipo de Interacción -->
            <div>
                <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Interacción *</label>
                <select name="tipo" id="tipo" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Seleccione tipo</option>
                    <option value="llamada">📞 Llamada telefónica</option>
                    <option value="email">📧 Correo electrónico</option>
                    <option value="visita">🚶 Visita presencial</option>
                    <option value="whatsapp">💬 WhatsApp</option>
                    <option value="reunion">👥 Reunión</option>
                    <option value="otro">📝 Otro</option>
                </select>
            </div>
            
            <!-- Asunto -->
            <div>
                <label for="asunto" class="block text-sm font-medium text-gray-700 mb-1">Asunto</label>
                <input type="text" name="asunto" id="asunto"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Ej: Seguimiento de crédito">
            </div>
            
            <!-- Descripción -->
            <div class="md:col-span-2">
                <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción *</label>
                <textarea name="descripcion" id="descripcion" rows="4" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Detalle de la interacción con el cliente..."></textarea>
            </div>
            
            <!-- Resultado -->
            <div class="md:col-span-2">
                <label for="resultado" class="block text-sm font-medium text-gray-700 mb-1">Resultado / Conclusión</label>
                <textarea name="resultado" id="resultado" rows="2"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Resultado de la interacción..."></textarea>
            </div>
            
            <!-- Seguimiento -->
            <div class="md:col-span-2">
                <div class="flex items-center mb-4">
                    <input type="checkbox" name="seguimiento_requerido" id="seguimiento_requerido" 
                           class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                           onchange="toggleFechaSeguimiento()">
                    <label for="seguimiento_requerido" class="ml-2 text-sm font-medium text-gray-700">
                        Requiere seguimiento
                    </label>
                </div>
                
                <div id="fecha_seguimiento_container" class="hidden">
                    <label for="fecha_seguimiento" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Seguimiento</label>
                    <input type="date" name="fecha_seguimiento" id="fecha_seguimiento"
                           class="w-full md:w-auto px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                           min="<?= date('Y-m-d') ?>">
                </div>
            </div>
        </div>
        
        <!-- Botones -->
        <div class="mt-6 flex justify-end space-x-4">
            <a href="<?= BASE_URL ?>/crm/interacciones" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i>Registrar Interacción
            </button>
        </div>
    </form>
</div>

<script>
function toggleFechaSeguimiento() {
    const checkbox = document.getElementById('seguimiento_requerido');
    const container = document.getElementById('fecha_seguimiento_container');
    
    if (checkbox.checked) {
        container.classList.remove('hidden');
    } else {
        container.classList.add('hidden');
    }
}
</script>
