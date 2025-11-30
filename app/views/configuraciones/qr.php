<?php
/**
 * Generador de Códigos QR
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6">
    <a href="<?= url('configuraciones') ?>" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Volver a Configuraciones
    </a>
</div>

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6"><?= htmlspecialchars($pageTitle) ?></h1>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Generador Individual -->
            <div class="border rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-qrcode mr-2 text-blue-600"></i>Generar QR Individual
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label for="qr_contenido" class="block text-sm font-medium text-gray-700 mb-1">Contenido del QR</label>
                        <textarea id="qr_contenido" rows="3" 
                                  placeholder="Texto, URL o datos para el código QR"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>
                    
                    <div>
                        <label for="qr_tamano" class="block text-sm font-medium text-gray-700 mb-1">Tamaño (px)</label>
                        <select id="qr_tamano" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="150">150 x 150</option>
                            <option value="200" selected>200 x 200</option>
                            <option value="300">300 x 300</option>
                            <option value="400">400 x 400</option>
                        </select>
                    </div>
                    
                    <button type="button" onclick="generarQR()" 
                            class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        <i class="fas fa-qrcode mr-2"></i>Generar QR
                    </button>
                </div>
                
                <div id="qr_resultado" class="mt-6 text-center hidden">
                    <img id="qr_imagen" src="" alt="Código QR" class="mx-auto border p-2 rounded">
                    <a id="qr_descargar" href="" download="qr-code.png" 
                       class="inline-block mt-4 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        <i class="fas fa-download mr-2"></i>Descargar
                    </a>
                </div>
            </div>
            
            <!-- Generador Masivo -->
            <div class="border rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-layer-group mr-2 text-purple-600"></i>Generar QRs Masivos
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label for="qr_tipo_masivo" class="block text-sm font-medium text-gray-700 mb-1">Tipo de QR</label>
                        <select id="qr_tipo_masivo" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="socios">Fichas de Socios</option>
                            <option value="creditos">Referencias de Créditos</option>
                            <option value="cuentas">Cuentas de Ahorro</option>
                        </select>
                    </div>
                    
                    <p class="text-sm text-gray-600">
                        Genera códigos QR para todos los registros del tipo seleccionado. 
                        Los QR contendrán la información relevante de cada registro.
                    </p>
                    
                    <button type="button" onclick="generarQRMasivo()" 
                            class="w-full px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                        <i class="fas fa-layer-group mr-2"></i>Generar QRs Masivos
                    </button>
                </div>
                
                <div id="qr_masivo_resultado" class="mt-6 hidden">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="text-green-700">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span id="qr_masivo_mensaje"></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Documentación de la API -->
        <div class="mt-8 border-t pt-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-code mr-2 text-gray-600"></i>API para Generación de QR
            </h2>
            
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="font-medium text-gray-800 mb-2">Endpoint</h3>
                <code class="block bg-gray-800 text-green-400 p-3 rounded text-sm">
                    POST <?= url('api/qr') ?>
                </code>
                
                <h3 class="font-medium text-gray-800 mt-4 mb-2">Parámetros</h3>
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Parámetro</th>
                            <th class="text-left py-2">Tipo</th>
                            <th class="text-left py-2">Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b">
                            <td class="py-2"><code>contenido</code></td>
                            <td class="py-2">string</td>
                            <td class="py-2">Texto o URL para codificar en el QR</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2"><code>tamano</code></td>
                            <td class="py-2">int</td>
                            <td class="py-2">Tamaño en píxeles (opcional, default: 200)</td>
                        </tr>
                    </tbody>
                </table>
                
                <h3 class="font-medium text-gray-800 mt-4 mb-2">Ejemplo de Uso</h3>
                <pre class="bg-gray-800 text-green-400 p-3 rounded text-sm overflow-x-auto">
curl -X POST <?= url('api/qr') ?> \
  -H "Content-Type: application/json" \
  -d '{"contenido": "https://example.com", "tamano": 300}'
                </pre>
            </div>
        </div>
    </div>
</div>

<script>
function generarQR() {
    const contenido = document.getElementById('qr_contenido').value;
    const tamano = document.getElementById('qr_tamano').value;
    
    if (!contenido) {
        alert('Por favor ingresa el contenido del QR');
        return;
    }
    
    // Usamos una API pública de QR para demostración
    const url = `https://api.qrserver.com/v1/create-qr-code/?size=${tamano}x${tamano}&data=${encodeURIComponent(contenido)}`;
    
    document.getElementById('qr_imagen').src = url;
    document.getElementById('qr_descargar').href = url;
    document.getElementById('qr_resultado').classList.remove('hidden');
}

function generarQRMasivo() {
    const tipo = document.getElementById('qr_tipo_masivo').value;
    
    // Simulación - en producción se conectaría al backend
    document.getElementById('qr_masivo_mensaje').textContent = 
        `Generación de QRs para ${tipo} iniciada. Los archivos estarán disponibles en breve.`;
    document.getElementById('qr_masivo_resultado').classList.remove('hidden');
}
</script>
