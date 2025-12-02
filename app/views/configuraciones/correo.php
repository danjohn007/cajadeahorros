<?php
/**
 * Configuración de Correo SMTP
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Configuración de Correo</h1>
        <p class="text-gray-600">Configuración SMTP para envío de correos electrónicos</p>
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
        
        <form method="POST" action="<?= url('configuraciones/correo') ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Servidor SMTP -->
                <div>
                    <label for="smtp_host" class="block text-sm font-medium text-gray-700 mb-1">
                        Servidor SMTP
                    </label>
                    <input type="text" id="smtp_host" name="smtp_host" 
                           value="<?= htmlspecialchars($config['smtp_host'] ?? '') ?>"
                           placeholder="smtp.ejemplo.com"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                </div>
                
                <!-- Puerto SMTP -->
                <div>
                    <label for="smtp_port" class="block text-sm font-medium text-gray-700 mb-1">
                        Puerto SMTP
                    </label>
                    <input type="number" id="smtp_port" name="smtp_port" 
                           value="<?= htmlspecialchars($config['smtp_port'] ?? '587') ?>"
                           placeholder="587"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                    <p class="text-sm text-gray-500 mt-1">Común: 587 (TLS) o 465 (SSL)</p>
                </div>
                
                <!-- Usuario SMTP -->
                <div>
                    <label for="smtp_user" class="block text-sm font-medium text-gray-700 mb-1">
                        Usuario SMTP
                    </label>
                    <input type="text" id="smtp_user" name="smtp_user" 
                           value="<?= htmlspecialchars($config['smtp_user'] ?? '') ?>"
                           placeholder="usuario@ejemplo.com"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                </div>
                
                <!-- Contraseña SMTP -->
                <div>
                    <label for="smtp_password" class="block text-sm font-medium text-gray-700 mb-1">
                        Contraseña SMTP
                    </label>
                    <input type="password" id="smtp_password" name="smtp_password" 
                           placeholder="<?= !empty($config['smtp_password']) ? '••••••••' : 'Ingrese la contraseña' ?>"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                    <p class="text-sm text-gray-500 mt-1">Dejar en blanco para mantener la contraseña actual</p>
                </div>
            </div>
            
            <div class="mt-6 space-y-6">
                <!-- Nombre del Remitente -->
                <div>
                    <label for="smtp_from_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre del Remitente
                    </label>
                    <input type="text" id="smtp_from_name" name="smtp_from_name" 
                           value="<?= htmlspecialchars($config['smtp_from_name'] ?? '') ?>"
                           placeholder="Nombre que aparecerá como remitente"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                </div>
                
                <!-- Correo del Sistema -->
                <div>
                    <label for="correo_sistema" class="block text-sm font-medium text-gray-700 mb-1">
                        Correo del Sistema (From)
                    </label>
                    <input type="email" id="correo_sistema" name="correo_sistema" 
                           value="<?= htmlspecialchars($config['correo_sistema'] ?? '') ?>"
                           placeholder="sistema@ejemplo.com"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                </div>
                
                <!-- Tipo de Encriptación -->
                <div>
                    <label for="smtp_encryption" class="block text-sm font-medium text-gray-700 mb-1">
                        Tipo de Encriptación
                    </label>
                    <select id="smtp_encryption" name="smtp_encryption" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                        <option value="tls" <?= ($config['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS</option>
                        <option value="ssl" <?= ($config['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                        <option value="" <?= ($config['smtp_encryption'] ?? '') === '' ? 'selected' : '' ?>>Sin encriptación</option>
                    </select>
                </div>
            </div>
            
            <!-- Sección de prueba -->
            <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                <h3 class="font-medium text-gray-700 mb-2">Probar configuración</h3>
                <p class="text-sm text-gray-600 mb-4">Envía un correo de prueba para verificar la configuración.</p>
                <div class="flex items-center space-x-4">
                    <input type="email" id="test_email" placeholder="Correo de destino" 
                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                    <button type="button" id="btn_test_email" 
                            class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                        <span id="btn_text">Enviar Correo de Prueba</span>
                        <i id="btn_spinner" class="fas fa-spinner fa-spin ml-2 hidden"></i>
                    </button>
                </div>
                <div id="test_result" class="mt-4 hidden"></div>
            </div>
            
            <div class="mt-8 flex justify-end">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Guardar Configuración
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('btn_test_email').addEventListener('click', function() {
    const email = document.getElementById('test_email').value;
    const btn = this;
    const btnText = document.getElementById('btn_text');
    const spinner = document.getElementById('btn_spinner');
    const resultDiv = document.getElementById('test_result');
    
    if (!email) {
        alert('Por favor ingrese un correo de destino');
        return;
    }
    
    // Disable button and show spinner
    btn.disabled = true;
    btnText.textContent = 'Enviando...';
    spinner.classList.remove('hidden');
    resultDiv.classList.add('hidden');
    
    fetch('<?= url('configuraciones/testEmail') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email: email })
    })
    .then(response => response.json())
    .then(data => {
        resultDiv.classList.remove('hidden');
        if (data.success) {
            resultDiv.className = 'mt-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg';
            resultDiv.innerHTML = '<i class="fas fa-check-circle mr-2"></i>' + data.message;
        } else {
            resultDiv.className = 'mt-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg';
            resultDiv.innerHTML = '<i class="fas fa-times-circle mr-2"></i>' + data.message;
        }
    })
    .catch(error => {
        resultDiv.classList.remove('hidden');
        resultDiv.className = 'mt-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg';
        resultDiv.innerHTML = '<i class="fas fa-times-circle mr-2"></i>Error de conexión: ' + error.message;
    })
    .finally(() => {
        btn.disabled = false;
        btnText.textContent = 'Enviar Correo de Prueba';
        spinner.classList.add('hidden');
    });
});
</script>
