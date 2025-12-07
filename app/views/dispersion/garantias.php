<?php
/**
 * Vista de Hoja de Garantías
 * Registro y administración de garantías asociadas al crédito
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Hoja de Garantías</h1>
        <button onclick="openModal('garantiaModal')" class="bg-primary-800 hover:bg-primary-700 text-white px-4 py-2 rounded">
            <i class="fas fa-plus mr-2"></i>Agregar Garantía
        </button>
    </div>

    <!-- Información del Crédito -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm text-gray-500 mb-1">Número de Crédito</label>
                    <p class="font-semibold"><?= htmlspecialchars($credito['numero_credito'] ?? 'N/A') ?></p>
                </div>
                <div>
                    <label class="block text-sm text-gray-500 mb-1">Acreditado</label>
                    <p class="font-semibold"><?= htmlspecialchars(($credito['nombre'] ?? '') . ' ' . ($credito['apellido_paterno'] ?? '')) ?></p>
                </div>
                <div>
                    <label class="block text-sm text-gray-500 mb-1">Monto del Crédito</label>
                    <p class="font-semibold text-green-600">$<?= number_format($credito['monto_solicitado'] ?? 0, 2) ?></p>
                </div>
                <div>
                    <label class="block text-sm text-gray-500 mb-1">Total Garantías</label>
                    <p class="font-semibold">$<?php
                    $totalGarantias = 0;
                    foreach ($garantias ?? [] as $g) {
                        $totalGarantias += $g['valor_estimado'] ?? 0;
                    }
                    echo number_format($totalGarantias, 2);
                    ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Garantías -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold">Garantías Registradas</h2>
        </div>
        <div class="p-6">
            <?php if (empty($garantias)): ?>
            <div class="text-center py-8">
                <i class="fas fa-home text-gray-400 text-5xl mb-4"></i>
                <p class="text-gray-500 mb-4">No hay garantías registradas</p>
                <button onclick="openModal('garantiaModal')" class="bg-primary-800 hover:bg-primary-700 text-white px-6 py-2 rounded">
                    <i class="fas fa-plus mr-2"></i>Agregar Primera Garantía
                </button>
            </div>
            <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($garantias as $garantia): ?>
                <div class="border rounded-lg p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start flex-1">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-home text-green-600 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800 mb-1"><?= htmlspecialchars($garantia['tipo'] ?? 'Garantía') ?></h3>
                                <p class="text-sm text-gray-600 mb-2"><?= htmlspecialchars($garantia['descripcion'] ?? 'N/A') ?></p>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-500">Valor Estimado</p>
                                        <p class="text-sm font-semibold text-green-600">$<?= number_format($garantia['valor_estimado'] ?? 0, 2) ?></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Fecha de Valuación</p>
                                        <p class="text-sm text-gray-800"><?= date('d/m/Y', strtotime($garantia['fecha_valuacion'] ?? 'now')) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para Agregar Garantía -->
<div id="garantiaModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Agregar Garantía</h3>
            <button onclick="closeModal('garantiaModal')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form method="POST" action="<?= BASE_URL ?>/dispersion/garantias/<?= $credito['id'] ?>">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Garantía</label>
                    <select name="tipo" class="w-full border rounded px-3 py-2" required>
                        <option value="">Seleccionar tipo</option>
                        <option value="inmueble">Inmueble</option>
                        <option value="vehiculo">Vehículo</option>
                        <option value="maquinaria">Maquinaria</option>
                        <option value="otros">Otros</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea name="descripcion" rows="3" class="w-full border rounded px-3 py-2" required></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Valor Estimado</label>
                    <input type="number" name="valor_estimado" step="0.01" class="w-full border rounded px-3 py-2" required>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-2">
                <button type="button" onclick="closeModal('garantiaModal')" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                    Cancelar
                </button>
                <button type="submit" class="bg-primary-800 hover:bg-primary-700 text-white px-4 py-2 rounded">
                    <i class="fas fa-save mr-2"></i>Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}
function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}
</script>
