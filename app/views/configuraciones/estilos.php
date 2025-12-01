<?php
/**
 * Configuración de Estilos
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Configuración de Estilos</h1>
        <p class="text-gray-600">Personaliza los colores del sistema</p>
    </div>
    <a href="<?= url('configuraciones') ?>" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Volver a Configuración
    </a>
</div>

<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        
        <?php if (!empty($success)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p><?= htmlspecialchars($success) ?></p>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?= url('configuraciones/estilos') ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Color Primario -->
                <div>
                    <label for="color_primario" class="block text-sm font-medium text-gray-700 mb-1">
                        Color Primario
                    </label>
                    <div class="flex items-center space-x-3">
                        <input type="color" id="color_primario" name="color_primario" 
                               value="<?= htmlspecialchars($config['color_primario'] ?? '#1e40af') ?>"
                               class="h-12 w-16 rounded border-gray-300 cursor-pointer">
                        <input type="text" id="color_primario_text" 
                               value="<?= htmlspecialchars($config['color_primario'] ?? '#1e40af') ?>"
                               class="flex-1 rounded-md border-gray-300 shadow-sm bg-gray-50 px-3 py-2 border" readonly>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Color principal del sistema (botones, enlaces)</p>
                </div>
                
                <!-- Color Secundario -->
                <div>
                    <label for="color_secundario" class="block text-sm font-medium text-gray-700 mb-1">
                        Color Secundario
                    </label>
                    <div class="flex items-center space-x-3">
                        <input type="color" id="color_secundario" name="color_secundario" 
                               value="<?= htmlspecialchars($config['color_secundario'] ?? '#3b82f6') ?>"
                               class="h-12 w-16 rounded border-gray-300 cursor-pointer">
                        <input type="text" id="color_secundario_text" 
                               value="<?= htmlspecialchars($config['color_secundario'] ?? '#3b82f6') ?>"
                               class="flex-1 rounded-md border-gray-300 shadow-sm bg-gray-50 px-3 py-2 border" readonly>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Color secundario (elementos destacados)</p>
                </div>
                
                <!-- Color de Acento -->
                <div>
                    <label for="color_acento" class="block text-sm font-medium text-gray-700 mb-1">
                        Color de Acento
                    </label>
                    <div class="flex items-center space-x-3">
                        <input type="color" id="color_acento" name="color_acento" 
                               value="<?= htmlspecialchars($config['color_acento'] ?? '#89ab37') ?>"
                               class="h-12 w-16 rounded border-gray-300 cursor-pointer">
                        <input type="text" id="color_acento_text" 
                               value="<?= htmlspecialchars($config['color_acento'] ?? '#89ab37') ?>"
                               class="flex-1 rounded-md border-gray-300 shadow-sm bg-gray-50 px-3 py-2 border" readonly>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Color de acento (éxito, confirmaciones)</p>
                </div>
            </div>
            
            <!-- Vista previa -->
            <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Vista Previa</h3>
                <div class="flex flex-wrap gap-4">
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
                    <button type="button" id="btn_preview_accent" 
                            style="background-color: <?= htmlspecialchars($config['color_acento'] ?? '#89ab37') ?>"
                            class="px-4 py-2 text-white rounded-md">
                        Botón Acento
                    </button>
                </div>
            </div>
            
            <div class="mt-8 flex justify-end">
                <button type="submit" id="btn_submit" 
                        style="background-color: <?= htmlspecialchars($config['color_acento'] ?? '#89ab37') ?>"
                        class="px-4 py-2 text-white rounded-md hover:opacity-90">
                    Guardar Estilos
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Update color text inputs when color picker changes
document.getElementById('color_primario').addEventListener('input', function() {
    document.getElementById('color_primario_text').value = this.value;
    document.getElementById('btn_preview_primary').style.backgroundColor = this.value;
});

document.getElementById('color_secundario').addEventListener('input', function() {
    document.getElementById('color_secundario_text').value = this.value;
    document.getElementById('btn_preview_secondary').style.backgroundColor = this.value;
});

document.getElementById('color_acento').addEventListener('input', function() {
    document.getElementById('color_acento_text').value = this.value;
    document.getElementById('btn_preview_accent').style.backgroundColor = this.value;
    document.getElementById('btn_submit').style.backgroundColor = this.value;
});
</script>
