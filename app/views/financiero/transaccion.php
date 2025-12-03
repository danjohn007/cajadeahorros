<?php
/**
 * Vista de Formulario de Transacción Financiera
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6">
    <a href="<?= url('financiero') ?>" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Volver a Módulo Financiero
    </a>
</div>

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6"><?= htmlspecialchars($pageTitle) ?></h1>
        
        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?= url('financiero/transaccion' . ($transaccion ? '/' . $transaccion['id'] : '')) ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="space-y-6">
                <!-- Tipo de Transacción -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Transacción *</label>
                    <div class="flex space-x-4">
                        <label class="flex items-center">
                            <input type="radio" name="tipo" value="ingreso" 
                                   <?= ($transaccion['tipo'] ?? '') === 'ingreso' ? 'checked' : '' ?>
                                   class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                            <span class="ml-2 text-green-600 font-medium">
                                <i class="fas fa-arrow-up mr-1"></i>Ingreso
                            </span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="tipo" value="egreso" 
                                   <?= ($transaccion['tipo'] ?? '') === 'egreso' ? 'checked' : '' ?>
                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300">
                            <span class="ml-2 text-red-600 font-medium">
                                <i class="fas fa-arrow-down mr-1"></i>Egreso
                            </span>
                        </label>
                    </div>
                </div>
                
                <!-- Categoría -->
                <div>
                    <label for="categoria_id" class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                    <select id="categoria_id" name="categoria_id"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                        <option value="">Sin categoría</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>" 
                                    data-tipo="<?= $cat['tipo'] ?>"
                                    <?= ($transaccion['categoria_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nombre']) ?> (<?= ucfirst($cat['tipo']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Concepto -->
                <div>
                    <label for="concepto" class="block text-sm font-medium text-gray-700 mb-1">Concepto *</label>
                    <input type="text" id="concepto" name="concepto" required
                           value="<?= htmlspecialchars($transaccion['concepto'] ?? '') ?>"
                           placeholder="Descripción de la transacción"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Monto -->
                    <div>
                        <label for="monto" class="block text-sm font-medium text-gray-700 mb-1">Monto *</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                            <input type="number" id="monto" name="monto" required
                                   value="<?= $transaccion['monto'] ?? '' ?>"
                                   step="0.01" min="0.01"
                                   placeholder="0.00"
                                   class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                        </div>
                    </div>
                    
                    <!-- Fecha -->
                    <div>
                        <label for="fecha" class="block text-sm font-medium text-gray-700 mb-1">Fecha *</label>
                        <input type="date" id="fecha" name="fecha" required
                               value="<?= $transaccion['fecha'] ?? date('Y-m-d') ?>"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Método de Pago -->
                    <div>
                        <label for="metodo_pago" class="block text-sm font-medium text-gray-700 mb-1">Método de Pago</label>
                        <select id="metodo_pago" name="metodo_pago"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                            <option value="efectivo" <?= ($transaccion['metodo_pago'] ?? '') === 'efectivo' ? 'selected' : '' ?>>Efectivo</option>
                            <option value="transferencia" <?= ($transaccion['metodo_pago'] ?? '') === 'transferencia' ? 'selected' : '' ?>>Transferencia</option>
                            <option value="cheque" <?= ($transaccion['metodo_pago'] ?? '') === 'cheque' ? 'selected' : '' ?>>Cheque</option>
                            <option value="tarjeta" <?= ($transaccion['metodo_pago'] ?? '') === 'tarjeta' ? 'selected' : '' ?>>Tarjeta</option>
                        </select>
                    </div>
                    
                    <!-- Referencia -->
                    <div>
                        <label for="referencia" class="block text-sm font-medium text-gray-700 mb-1">Referencia</label>
                        <input type="text" id="referencia" name="referencia"
                               value="<?= htmlspecialchars($transaccion['referencia'] ?? '') ?>"
                               placeholder="Número de referencia"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                    </div>
                </div>
                
                <!-- Proveedor/Cliente -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="socio_id" class="block text-sm font-medium text-gray-700 mb-1">Socio (Cliente)</label>
                        <select id="socio_id" name="socio_id"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                            <option value="">Seleccionar socio...</option>
                            <?php foreach ($socios as $s): ?>
                                <option value="<?= $s['id'] ?>" <?= ($transaccion['socio_id'] ?? '') == $s['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($s['numero_socio'] . ' - ' . $s['nombre_completo']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Para ingresos de socios</p>
                    </div>
                    
                    <div>
                        <label for="proveedor_id" class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label>
                        <select id="proveedor_id" name="proveedor_id"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                            <option value="">Seleccionar proveedor...</option>
                            <?php foreach ($proveedores as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= ($transaccion['proveedor_id'] ?? '') == $p['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Para egresos a proveedores</p>
                    </div>
                </div>
                
                <!-- Nombre Proveedor/Cliente (alternativo) -->
                <div>
                    <label for="proveedor" class="block text-sm font-medium text-gray-700 mb-1">Nombre Proveedor/Cliente (texto libre)</label>
                    <input type="text" id="proveedor" name="proveedor"
                           value="<?= htmlspecialchars($transaccion['proveedor'] ?? '') ?>"
                           placeholder="Usar si no está en los catálogos"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                </div>
                
                <!-- Notas -->
                <div>
                    <label for="notas" class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                    <textarea id="notas" name="notas" rows="3"
                              placeholder="Notas adicionales..."
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border"><?= htmlspecialchars($transaccion['notas'] ?? '') ?></textarea>
                </div>
            </div>
            
            <div class="mt-8 flex justify-end space-x-4">
                <a href="<?= url('financiero') ?>" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i><?= $transaccion ? 'Guardar Cambios' : 'Registrar Transacción' ?>
                </button>
            </div>
        </form>
    </div>
</div>
