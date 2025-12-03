<?php
/**
 * Vista de Segmentos de Clientes
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <a href="<?= BASE_URL ?>/crm" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
            <i class="fas fa-arrow-left mr-1"></i> Volver a CRM
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Segmentos de Clientes</h2>
        <p class="text-gray-600">Gestiona los segmentos para clasificar a tus clientes</p>
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
    <!-- Formulario para crear segmento -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-plus-circle mr-2"></i>Nuevo Segmento
        </h3>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="crear">
            
            <div class="mb-4">
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre del Segmento *</label>
                <input type="text" name="nombre" id="nombre" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Ej: Clientes Frecuentes">
            </div>
            
            <div class="mb-4">
                <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Descripción del segmento..."></textarea>
            </div>
            
            <div class="mb-4">
                <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                <input type="color" name="color" id="color" value="#3b82f6"
                       class="w-full h-10 border border-gray-300 rounded-md cursor-pointer">
            </div>
            
            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i>Crear Segmento
            </button>
        </form>
        
        <hr class="my-6">
        
        <!-- Actualizar métricas -->
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="actualizar_clientes">
            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                <i class="fas fa-sync-alt mr-2"></i>Actualizar Métricas CRM
            </button>
            <p class="text-xs text-gray-500 mt-2">Recalcula las métricas para todos los clientes activos</p>
        </form>
    </div>
    
    <!-- Lista de segmentos -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-layer-group mr-2"></i>Segmentos Existentes
            </h3>
        </div>
        
        <?php if (empty($segmentos)): ?>
        <div class="p-6 text-center text-gray-500">
            <i class="fas fa-inbox text-4xl mb-4"></i>
            <p>No hay segmentos creados</p>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Color</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Nombre</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Descripción</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Clientes</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($segmentos as $segmento): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="w-6 h-6 rounded-full" style="background-color: <?= htmlspecialchars($segmento['color']) ?>"></div>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800">
                            <?= htmlspecialchars($segmento['nombre']) ?>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            <?= htmlspecialchars($segmento['descripcion'] ?? 'Sin descripción') ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                <?= number_format($segmento['num_clientes']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <?php if ($segmento['activo']): ?>
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Activo</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Inactivo</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
