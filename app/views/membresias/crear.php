<?php
/**
 * Vista de Crear Membresía
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6">
    <a href="<?= url('membresias') ?>" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Volver a Membresías
    </a>
    <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
    <p class="text-gray-600">Registrar una nueva membresía para un socio</p>
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

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="POST" action="<?= url('membresias/crear') ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="space-y-6">
                <!-- Socio -->
                <div>
                    <label for="socio_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Socio <span class="text-red-500">*</span>
                    </label>
                    <select id="socio_id" name="socio_id" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                        <option value="">Seleccionar socio...</option>
                        <?php foreach ($socios as $socio): ?>
                            <option value="<?= $socio['id'] ?>">
                                <?= htmlspecialchars($socio['numero_socio'] . ' - ' . $socio['nombre_completo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Tipo de Membresía -->
                <div>
                    <label for="tipo_membresia_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Tipo de Membresía <span class="text-red-500">*</span>
                    </label>
                    <select id="tipo_membresia_id" name="tipo_membresia_id" required onchange="updatePrice()"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                        <option value="">Seleccionar tipo...</option>
                        <?php foreach ($tiposMembresia as $tipo): ?>
                            <option value="<?= $tipo['id'] ?>" data-precio="<?= $tipo['precio'] ?>" data-duracion="<?= $tipo['duracion_dias'] ?>">
                                <?= htmlspecialchars($tipo['nombre']) ?> - $<?= number_format($tipo['precio'], 2) ?> (<?= $tipo['duracion_dias'] ?> días)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Resumen de precio -->
                <div id="precio-resumen" class="p-4 bg-blue-50 rounded-lg hidden">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">Precio de la membresía:</span>
                        <span id="precio-display" class="text-2xl font-bold text-blue-600">$0.00</span>
                    </div>
                    <div class="text-sm text-gray-500 mt-1">
                        Duración: <span id="duracion-display">0</span> días
                    </div>
                </div>
                
                <!-- Método de Pago -->
                <div>
                    <label for="metodo_pago" class="block text-sm font-medium text-gray-700 mb-1">
                        Método de Pago
                    </label>
                    <select id="metodo_pago" name="metodo_pago"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="tarjeta">Tarjeta</option>
                        <option value="paypal">PayPal</option>
                    </select>
                </div>
                
                <!-- Referencia de Pago -->
                <div>
                    <label for="referencia_pago" class="block text-sm font-medium text-gray-700 mb-1">
                        Referencia de Pago
                    </label>
                    <input type="text" id="referencia_pago" name="referencia_pago"
                           placeholder="Número de transacción, folio, etc."
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                </div>
                
                <!-- Notas -->
                <div>
                    <label for="notas" class="block text-sm font-medium text-gray-700 mb-1">
                        Notas
                    </label>
                    <textarea id="notas" name="notas" rows="3"
                              placeholder="Observaciones adicionales..."
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border"></textarea>
                </div>
            </div>
            
            <div class="mt-8 flex justify-end space-x-4">
                <a href="<?= url('membresias') ?>" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Crear Membresía
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function updatePrice() {
    const select = document.getElementById('tipo_membresia_id');
    const option = select.options[select.selectedIndex];
    const resumen = document.getElementById('precio-resumen');
    
    if (option.value) {
        const precio = parseFloat(option.dataset.precio) || 0;
        const duracion = parseInt(option.dataset.duracion) || 0;
        
        document.getElementById('precio-display').textContent = '$' + precio.toLocaleString('es-MX', {minimumFractionDigits: 2});
        document.getElementById('duracion-display').textContent = duracion;
        resumen.classList.remove('hidden');
    } else {
        resumen.classList.add('hidden');
    }
}
</script>
