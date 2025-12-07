<?php
/**
 * Vista de Garantías y Avales
 * Registro y administración de garantías reales y avales personales asociados a los créditos
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Garantías y Avales</h1>
        <div class="flex space-x-2">
            <button onclick="openModal('avalModal')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                <i class="fas fa-user-plus mr-2"></i>Agregar Aval
            </button>
            <button onclick="openModal('garantiaModal')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                <i class="fas fa-plus mr-2"></i>Agregar Garantía
            </button>
        </div>
    </div>

    <p class="text-gray-600 mb-6">Registro y administración de garantías reales y avales personales asociados a los créditos</p>

    <!-- Información del Crédito -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold flex items-center">
                <i class="fas fa-file-contract text-primary-800 mr-2"></i>Información del Crédito
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Número de Crédito</label>
                    <p class="font-semibold"><?= htmlspecialchars($credito['numero_credito'] ?? 'N/A') ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Monto</label>
                    <p class="font-semibold">$<?= number_format($credito['monto_solicitado'] ?? 0, 2) ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Plazo</label>
                    <p class="font-semibold"><?= $credito['plazo_meses'] ?? 0 ?> meses</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Estado</label>
                    <p class="font-semibold">
                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">
                            <?= ucfirst($credito['estatus'] ?? 'N/A') ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Garantías Registradas -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6 border-b flex items-center justify-between">
            <h2 class="text-lg font-semibold flex items-center">
                <i class="fas fa-home text-primary-800 mr-2"></i>Garantías Reales
            </h2>
            <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm font-medium rounded">
                <?= count($garantias ?? []) ?> garantías
            </span>
        </div>
        <div class="p-6">
            <?php if (empty($garantias)): ?>
            <div class="text-center py-8">
                <i class="fas fa-home text-gray-400 text-5xl mb-4"></i>
                <p class="text-gray-500 mb-4">No hay garantías registradas</p>
                <button onclick="openModal('garantiaModal')" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded">
                    <i class="fas fa-plus mr-2"></i>Agregar Primera Garantía
                </button>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($garantias as $garantia): ?>
                <div class="border rounded-lg p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-home text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800"><?= htmlspecialchars($garantia['tipo'] ?? 'Garantía') ?></h3>
                                <p class="text-sm text-gray-500">ID: <?= $garantia['id'] ?></p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Activa</span>
                    </div>
                    <div class="space-y-2">
                        <div>
                            <p class="text-xs text-gray-500">Descripción</p>
                            <p class="text-sm text-gray-800"><?= htmlspecialchars($garantia['descripcion'] ?? 'N/A') ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Valor Estimado</p>
                            <p class="text-sm font-semibold text-green-600">$<?= number_format($garantia['valor_estimado'] ?? 0, 2) ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Fecha de Valuación</p>
                            <p class="text-sm text-gray-800"><?= date('d/m/Y', strtotime($garantia['fecha_valuacion'] ?? 'now')) ?></p>
                        </div>
                    </div>
                    <div class="mt-4 flex space-x-2">
                        <button class="flex-1 text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded">
                            <i class="fas fa-eye mr-1"></i>Ver Detalles
                        </button>
                        <button class="flex-1 text-xs bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded">
                            <i class="fas fa-edit mr-1"></i>Editar
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Avales Registrados -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b flex items-center justify-between">
            <h2 class="text-lg font-semibold flex items-center">
                <i class="fas fa-users text-primary-800 mr-2"></i>Avales y Obligados Solidarios
            </h2>
            <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm font-medium rounded">
                <?= count($avales ?? []) ?> avales
            </span>
        </div>
        <div class="p-6">
            <?php if (empty($avales)): ?>
            <div class="text-center py-8">
                <i class="fas fa-users text-gray-400 text-5xl mb-4"></i>
                <p class="text-gray-500 mb-4">No hay avales registrados</p>
                <button onclick="openModal('avalModal')" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">
                    <i class="fas fa-user-plus mr-2"></i>Agregar Primer Aval
                </button>
            </div>
            <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($avales as $aval): ?>
                <div class="border rounded-lg p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start flex-1">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-user text-blue-600 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800 mb-1">
                                    <?= htmlspecialchars(($aval['nombre'] ?? '') . ' ' . ($aval['apellido_paterno'] ?? '') . ' ' . ($aval['apellido_materno'] ?? '')) ?>
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                                    <div>
                                        <p class="text-xs text-gray-500">Tipo</p>
                                        <p class="text-sm text-gray-800"><?= ucfirst($aval['tipo'] ?? 'N/A') ?></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">RFC</p>
                                        <p class="text-sm text-gray-800"><?= htmlspecialchars($aval['rfc'] ?? 'N/A') ?></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">CURP</p>
                                        <p class="text-sm text-gray-800"><?= htmlspecialchars($aval['curp'] ?? 'N/A') ?></p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                                    <div>
                                        <p class="text-xs text-gray-500">Teléfono</p>
                                        <p class="text-sm text-gray-800"><?= htmlspecialchars($aval['telefono'] ?? 'N/A') ?></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Dirección</p>
                                        <p class="text-sm text-gray-800"><?= htmlspecialchars($aval['direccion'] ?? 'N/A') ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ml-4">
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Activo</span>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end space-x-2">
                        <button class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                            <i class="fas fa-eye mr-1"></i>Ver Completo
                        </button>
                        <button class="text-xs bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                            <i class="fas fa-edit mr-1"></i>Editar
                        </button>
                        <button class="text-xs bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                            <i class="fas fa-trash mr-1"></i>Eliminar
                        </button>
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
            <h3 class="text-xl font-semibold">Agregar Garantía Real</h3>
            <button onclick="closeModal('garantiaModal')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form method="POST" action="<?= BASE_URL ?>/creditos/garantias-avales/<?= $credito['id'] ?>">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <input type="hidden" name="tipo" value="garantia">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Garantía</label>
                    <select name="tipo_garantia" class="w-full border rounded px-3 py-2" required>
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
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                    <i class="fas fa-save mr-2"></i>Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para Agregar Aval -->
<div id="avalModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Agregar Aval u Obligado Solidario</h3>
            <button onclick="closeModal('avalModal')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form method="POST" action="<?= BASE_URL ?>/creditos/garantias-avales/<?= $credito['id'] ?>">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <input type="hidden" name="tipo" value="aval">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Aval</label>
                    <select name="tipo_aval" class="w-full border rounded px-3 py-2" required>
                        <option value="">Seleccionar tipo</option>
                        <option value="aval">Aval</option>
                        <option value="obligado_solidario">Obligado Solidario</option>
                        <option value="fiador">Fiador</option>
                    </select>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre(s)</label>
                        <input type="text" name="nombre" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Apellido Paterno</label>
                        <input type="text" name="apellido_paterno" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Apellido Materno</label>
                        <input type="text" name="apellido_materno" class="w-full border rounded px-3 py-2">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">RFC</label>
                        <input type="text" name="rfc" maxlength="13" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">CURP</label>
                        <input type="text" name="curp" maxlength="18" class="w-full border rounded px-3 py-2" required>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                    <input type="tel" name="telefono" class="w-full border rounded px-3 py-2" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dirección Completa</label>
                    <textarea name="direccion" rows="3" class="w-full border rounded px-3 py-2" required></textarea>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-2">
                <button type="button" onclick="closeModal('avalModal')" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                    Cancelar
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
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
