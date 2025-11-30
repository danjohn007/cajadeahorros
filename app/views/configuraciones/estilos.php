<?php
/**
 * Configuración de Estilos
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6">
    <a href="<?= url('configuraciones') ?>" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Volver a Configuraciones
    </a>
</div>

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6"><?= htmlspecialchars($pageTitle) ?></h1>
        
        <?php if (!empty($success)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p><?= htmlspecialchars($success) ?></p>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?= url('configuraciones/estilos') ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="space-y-6">
                <div>
                    <label for="color_primario" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-palette mr-2"></i>Color Primario
                    </label>
                    <div class="flex items-center space-x-4">
                        <input type="color" id="color_primario" name="color_primario" 
                               value="<?= htmlspecialchars($config['color_primario'] ?? '#1e40af') ?>"
                               class="h-10 w-20 rounded border-gray-300">
                        <input type="text" readonly id="color_primario_text" 
                               value="<?= htmlspecialchars($config['color_primario'] ?? '#1e40af') ?>"
                               class="rounded-md border-gray-300 shadow-sm bg-gray-50">
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Color principal usado en botones y encabezados</p>
                </div>
                
                <div>
                    <label for="color_secundario" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-palette mr-2"></i>Color Secundario
                    </label>
                    <div class="flex items-center space-x-4">
                        <input type="color" id="color_secundario" name="color_secundario" 
                               value="<?= htmlspecialchars($config['color_secundario'] ?? '#3b82f6') ?>"
                               class="h-10 w-20 rounded border-gray-300">
                        <input type="text" readonly id="color_secundario_text" 
                               value="<?= htmlspecialchars($config['color_secundario'] ?? '#3b82f6') ?>"
                               class="rounded-md border-gray-300 shadow-sm bg-gray-50">
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Color secundario para acentos y hover</p>
                </div>
                
                <!-- Vista previa -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Vista Previa</h3>
                    <div class="flex space-x-4">
                        <button type="button" id="btn_preview_primary" 
                                style="background-color: <?= htmlspecialchars($config['color_primario'] ?? '#1e40af') ?>"
                                class="px-4 py-2 text-white rounded-md">
                            Botón Primario
                        </button>
                        <button type="button" id="btn_preview_secondary" 
                                style="background-color: <?= htmlspecialchars($config['color_secundario'] ?? '#3b82f6') ?>"
                                class="px-4 py-2 text-white rounded-md">
                            Botón Secundario
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Guardar Estilos
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('color_primario').addEventListener('input', function() {
    document.getElementById('color_primario_text').value = this.value;
    document.getElementById('btn_preview_primary').style.backgroundColor = this.value;
});

document.getElementById('color_secundario').addEventListener('input', function() {
    document.getElementById('color_secundario_text').value = this.value;
    document.getElementById('btn_preview_secondary').style.backgroundColor = this.value;
});
</script>
