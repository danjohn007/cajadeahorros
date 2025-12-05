<?php
/**
 * Vista de Categorías Financieras
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
        <p class="text-gray-600">Gestión de categorías para transacciones financieras</p>
    </div>
    <a href="<?= url('financiero') ?>" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Volver a Módulo Financiero
    </a>
</div>

<?php if (!empty($success)): ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
        <p><?= htmlspecialchars($success) ?></p>
    </div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
        <ul class="list-disc list-inside">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Formulario de nueva/editar categoría -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">
            <?php if (isset($editCategoria) && $editCategoria): ?>
                <i class="fas fa-edit mr-2 text-blue-600"></i>Editar Categoría
            <?php else: ?>
                <i class="fas fa-plus-circle mr-2 text-blue-600"></i>Nueva Categoría
            <?php endif; ?>
        </h2>
        
        <form method="POST" action="<?= url('financiero/categorias') ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <?php if (isset($editCategoria) && $editCategoria): ?>
                <input type="hidden" name="action" value="editar">
                <input type="hidden" name="id" value="<?= $editCategoria['id'] ?>">
            <?php else: ?>
                <input type="hidden" name="action" value="crear">
            <?php endif; ?>
            
            <div class="space-y-4">
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" required
                           placeholder="Nombre de la categoría"
                           value="<?= htmlspecialchars($editCategoria['nombre'] ?? '') ?>"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                </div>
                
                <div>
                    <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
                    <select id="tipo" name="tipo" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                        <option value="">Seleccionar...</option>
                        <option value="ingreso" <?= (($editCategoria['tipo'] ?? '') === 'ingreso') ? 'selected' : '' ?>>Ingreso</option>
                        <option value="egreso" <?= (($editCategoria['tipo'] ?? '') === 'egreso') ? 'selected' : '' ?>>Egreso</option>
                    </select>
                </div>
                
                <div>
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="2"
                              placeholder="Descripción de la categoría"
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border"><?= htmlspecialchars($editCategoria['descripcion'] ?? '') ?></textarea>
                </div>
                
                <div>
                    <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                    <input type="color" id="color" name="color" value="<?= htmlspecialchars($editCategoria['color'] ?? '#3b82f6') ?>"
                           class="w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 border">
                </div>
                
                <div>
                    <label for="icono" class="block text-sm font-medium text-gray-700 mb-1">Icono (Font Awesome)</label>
                    <input type="text" id="icono" name="icono" value="<?= htmlspecialchars($editCategoria['icono'] ?? 'fas fa-tag') ?>"
                           placeholder="fas fa-tag"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                </div>
                
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        <?php if (isset($editCategoria) && $editCategoria): ?>
                            <i class="fas fa-save mr-2"></i>Guardar Cambios
                        <?php else: ?>
                            <i class="fas fa-plus mr-2"></i>Crear Categoría
                        <?php endif; ?>
                    </button>
                    <?php if (isset($editCategoria) && $editCategoria): ?>
                        <a href="<?= url('financiero/categorias') ?>" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancelar
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Lista de categorías -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="text-lg font-semibold text-gray-800">Categorías Existentes</h2>
            </div>
            
            <!-- Categorías de Ingreso -->
            <div class="p-6 border-b">
                <h3 class="text-md font-medium text-green-700 mb-4">
                    <i class="fas fa-arrow-up mr-2"></i>Ingresos
                </h3>
                <div class="space-y-2">
                    <?php 
                    $ingresosExisten = false;
                    foreach ($categorias as $cat): 
                        if ($cat['tipo'] === 'ingreso'):
                            $ingresosExisten = true;
                    ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <span class="w-4 h-4 rounded-full mr-3" style="background-color: <?= htmlspecialchars($cat['color']) ?>"></span>
                                <div>
                                    <span class="font-medium text-gray-800"><?= htmlspecialchars($cat['nombre']) ?></span>
                                    <?php if ($cat['descripcion']): ?>
                                        <p class="text-xs text-gray-500"><?= htmlspecialchars($cat['descripcion']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-500"><?= $cat['num_transacciones'] ?? 0 ?> transacciones</span>
                                
                                <!-- Edit Button -->
                                <a href="<?= url('financiero/categorias?editar=' . $cat['id']) ?>" 
                                   class="text-blue-600 hover:text-blue-800 p-1" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <!-- Toggle Button -->
                                <form method="POST" action="<?= url('financiero/categorias') ?>" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="action" value="toggle">
                                    <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                    <button type="submit" class="<?= $cat['activo'] ? 'text-green-600' : 'text-gray-400' ?> hover:text-gray-600 p-1"
                                            title="<?= $cat['activo'] ? 'Desactivar' : 'Activar' ?>">
                                        <i class="fas <?= $cat['activo'] ? 'fa-toggle-on' : 'fa-toggle-off' ?>"></i>
                                    </button>
                                </form>
                                
                                <!-- Delete Button -->
                                <?php if (($cat['num_transacciones'] ?? 0) == 0): ?>
                                <form method="POST" action="<?= url('financiero/categorias') ?>" class="inline" 
                                      onsubmit="return confirm('¿Está seguro de eliminar esta categoría?')">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="action" value="eliminar">
                                    <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-800 p-1" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php 
                        endif;
                    endforeach; 
                    if (!$ingresosExisten):
                    ?>
                        <p class="text-gray-500 text-sm">No hay categorías de ingreso</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Categorías de Egreso -->
            <div class="p-6">
                <h3 class="text-md font-medium text-red-700 mb-4">
                    <i class="fas fa-arrow-down mr-2"></i>Egresos
                </h3>
                <div class="space-y-2">
                    <?php 
                    $egresosExisten = false;
                    foreach ($categorias as $cat): 
                        if ($cat['tipo'] === 'egreso'):
                            $egresosExisten = true;
                    ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <span class="w-4 h-4 rounded-full mr-3" style="background-color: <?= htmlspecialchars($cat['color']) ?>"></span>
                                <div>
                                    <span class="font-medium text-gray-800"><?= htmlspecialchars($cat['nombre']) ?></span>
                                    <?php if ($cat['descripcion']): ?>
                                        <p class="text-xs text-gray-500"><?= htmlspecialchars($cat['descripcion']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-500"><?= $cat['num_transacciones'] ?? 0 ?> transacciones</span>
                                
                                <!-- Edit Button -->
                                <a href="<?= url('financiero/categorias?editar=' . $cat['id']) ?>" 
                                   class="text-blue-600 hover:text-blue-800 p-1" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <!-- Toggle Button -->
                                <form method="POST" action="<?= url('financiero/categorias') ?>" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="action" value="toggle">
                                    <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                    <button type="submit" class="<?= $cat['activo'] ? 'text-green-600' : 'text-gray-400' ?> hover:text-gray-600 p-1"
                                            title="<?= $cat['activo'] ? 'Desactivar' : 'Activar' ?>">
                                        <i class="fas <?= $cat['activo'] ? 'fa-toggle-on' : 'fa-toggle-off' ?>"></i>
                                    </button>
                                </form>
                                
                                <!-- Delete Button -->
                                <?php if (($cat['num_transacciones'] ?? 0) == 0): ?>
                                <form method="POST" action="<?= url('financiero/categorias') ?>" class="inline"
                                      onsubmit="return confirm('¿Está seguro de eliminar esta categoría?')">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="action" value="eliminar">
                                    <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-800 p-1" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php 
                        endif;
                    endforeach; 
                    if (!$egresosExisten):
                    ?>
                        <p class="text-gray-500 text-sm">No hay categorías de egreso</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
