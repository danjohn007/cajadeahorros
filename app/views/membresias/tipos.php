<?php
/**
 * Vista de Tipos de Membresía
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <a href="<?= BASE_URL ?>/membresias" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
            <i class="fas fa-arrow-left mr-1"></i> Volver a Membresías
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Tipos de Membresía</h2>
        <p class="text-gray-600">Administra los tipos de membresía disponibles</p>
    </div>
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

<?php if (!empty($success)): ?>
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
    <?= htmlspecialchars($success) ?>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Formulario para crear tipo de membresía -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-plus-circle mr-2"></i>Nuevo Tipo de Membresía
        </h3>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="crear">
            
            <div class="mb-4">
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                <input type="text" name="nombre" id="nombre" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Ej: Membresía Premium">
            </div>
            
            <div class="mb-4">
                <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="2"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Descripción del tipo de membresía..."></textarea>
            </div>
            
            <div class="mb-4">
                <label for="precio" class="block text-sm font-medium text-gray-700 mb-1">Precio *</label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">$</span>
                    <input type="number" name="precio" id="precio" required min="0" step="0.01"
                           class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0.00">
                </div>
            </div>
            
            <div class="mb-4">
                <label for="duracion_dias" class="block text-sm font-medium text-gray-700 mb-1">Duración (días) *</label>
                <input type="number" name="duracion_dias" id="duracion_dias" required min="1" value="30"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">30 = 1 mes, 365 = 1 año</p>
            </div>
            
            <div class="mb-4">
                <label for="beneficios" class="block text-sm font-medium text-gray-700 mb-1">Beneficios</label>
                <textarea name="beneficios" id="beneficios" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Lista de beneficios..."></textarea>
            </div>
            
            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i>Crear Tipo
            </button>
        </form>
    </div>
    
    <!-- Lista de tipos -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-list mr-2"></i>Tipos Existentes
            </h3>
        </div>
        
        <?php if (empty($tipos)): ?>
        <div class="p-6 text-center text-gray-500">
            <i class="fas fa-inbox text-4xl mb-4"></i>
            <p>No hay tipos de membresía creados</p>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Nombre</th>
                        <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">Precio</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Duración</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Activas</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Estado</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($tipos as $tipo): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800"><?= htmlspecialchars($tipo['nombre']) ?></div>
                            <?php if ($tipo['descripcion']): ?>
                            <div class="text-sm text-gray-500"><?= htmlspecialchars($tipo['descripcion']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-right font-medium text-blue-600">
                            $<?= number_format($tipo['precio'], 2) ?>
                        </td>
                        <td class="px-4 py-3 text-center text-sm">
                            <?= $tipo['duracion_dias'] ?> días
                            <br>
                            <span class="text-gray-500">
                                (<?= round($tipo['duracion_dias'] / 30, 1) ?> meses)
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                <?= number_format($tipo['membresias_activas']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <?php if ($tipo['activo']): ?>
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Activo</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center space-x-2">
                                <!-- Editar -->
                                <button onclick="editarTipo(<?= $tipo['id'] ?>, '<?= htmlspecialchars(addslashes($tipo['nombre'])) ?>', '<?= htmlspecialchars(addslashes($tipo['descripcion'] ?? '')) ?>', <?= $tipo['precio'] ?>, <?= $tipo['duracion_dias'] ?>, '<?= htmlspecialchars(addslashes($tipo['beneficios'] ?? '')) ?>')" 
                                        class="text-blue-600 hover:text-blue-800" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <!-- Toggle estado -->
                                <form method="POST" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="action" value="toggle">
                                    <input type="hidden" name="id" value="<?= $tipo['id'] ?>">
                                    <button type="submit" class="<?= $tipo['activo'] ? 'text-orange-600 hover:text-orange-800' : 'text-green-600 hover:text-green-800' ?>" 
                                            title="<?= $tipo['activo'] ? 'Desactivar' : 'Activar' ?>">
                                        <i class="fas <?= $tipo['activo'] ? 'fa-toggle-on' : 'fa-toggle-off' ?>"></i>
                                    </button>
                                </form>
                                <!-- Eliminar -->
                                <?php if (($tipo['total_membresias'] ?? 0) == 0): ?>
                                <form method="POST" class="inline" onsubmit="return confirm('¿Está seguro de eliminar este tipo de membresía?')">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="action" value="eliminar">
                                    <input type="hidden" name="id" value="<?= $tipo['id'] ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <?php else: ?>
                                <span class="text-gray-300" title="No se puede eliminar, tiene membresías asociadas">
                                    <i class="fas fa-trash"></i>
                                </span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de Edición -->
<div id="modalEditar" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Editar Tipo de Membresía</h3>
        </div>
        <form method="POST" id="formEditar">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="actualizar">
            <input type="hidden" name="id" id="edit_id">
            
            <div class="p-6 space-y-4">
                <div>
                    <label for="edit_nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" name="nombre" id="edit_nombre" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="edit_descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea name="descripcion" id="edit_descripcion" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                
                <div>
                    <label for="edit_precio" class="block text-sm font-medium text-gray-700 mb-1">Precio *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">$</span>
                        <input type="number" name="precio" id="edit_precio" required min="0" step="0.01"
                               class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <div>
                    <label for="edit_duracion_dias" class="block text-sm font-medium text-gray-700 mb-1">Duración (días) *</label>
                    <input type="number" name="duracion_dias" id="edit_duracion_dias" required min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="edit_beneficios" class="block text-sm font-medium text-gray-700 mb-1">Beneficios</label>
                    <textarea name="beneficios" id="edit_beneficios" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                <button type="button" onclick="cerrarModal()" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-100">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editarTipo(id, nombre, descripcion, precio, duracionDias, beneficios) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nombre').value = nombre;
    document.getElementById('edit_descripcion').value = descripcion;
    document.getElementById('edit_precio').value = precio;
    document.getElementById('edit_duracion_dias').value = duracionDias;
    document.getElementById('edit_beneficios').value = beneficios;
    document.getElementById('modalEditar').classList.remove('hidden');
}

function cerrarModal() {
    document.getElementById('modalEditar').classList.add('hidden');
}

// Cerrar modal al hacer clic fuera
document.getElementById('modalEditar').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModal();
    }
});
</script>
