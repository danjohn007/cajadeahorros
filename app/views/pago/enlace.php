<?php
/**
 * Vista de Enlace de Pago
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6">
    <a href="<?= BASE_URL ?>/cartera" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Volver a Cartera
    </a>
    <h2 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h2>
    <p class="text-gray-600">Comparte este enlace con el cliente para que realice su pago en línea</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Información del Crédito -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-file-invoice-dollar text-blue-600 mr-2"></i>Información del Crédito
        </h3>
        
        <div class="space-y-3">
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-600">Número de Crédito:</span>
                <span class="font-medium"><?= htmlspecialchars($credito['numero_credito']) ?></span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-600">Socio:</span>
                <span class="font-medium"><?= htmlspecialchars($credito['nombre'] . ' ' . $credito['apellido_paterno']) ?></span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-600">Número de Socio:</span>
                <span class="font-medium"><?= htmlspecialchars($credito['numero_socio']) ?></span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-600">Saldo Actual:</span>
                <span class="font-bold text-red-600 text-lg">$<?= number_format($credito['saldo_actual'], 2) ?></span>
            </div>
            <?php if ($credito['email']): ?>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-600">Email:</span>
                <span class="font-medium"><?= htmlspecialchars($credito['email']) ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Enlace de Pago -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fab fa-paypal text-blue-600 mr-2"></i>Enlace de Pago
        </h3>
        
        <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <p class="text-sm text-gray-600 mb-2">Enlace para pagar el total de la deuda:</p>
            <div class="flex items-center">
                <input type="text" id="enlace-pago" value="<?= htmlspecialchars($enlacePago) ?>" readonly
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md bg-white text-sm">
                <button onclick="copiarEnlace()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700 transition">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
            <p id="copiado-msg" class="text-green-600 text-sm mt-2 hidden">¡Enlace copiado al portapapeles!</p>
        </div>
        
        <div class="space-y-3">
            <p class="text-sm text-gray-600">
                <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                El enlace es válido por 7 días
            </p>
            <p class="text-sm text-gray-600">
                <i class="fas fa-lock text-green-500 mr-1"></i>
                Pago seguro procesado por PayPal
            </p>
        </div>
        
        <hr class="my-4">
        
        <h4 class="font-medium text-gray-800 mb-3">Compartir enlace:</h4>
        <div class="flex space-x-2">
            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $credito['celular'] ?? '') ?>?text=<?= urlencode('Enlace para realizar tu pago: ' . $enlacePago) ?>" 
               target="_blank"
               class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                <i class="fab fa-whatsapp mr-2"></i>WhatsApp
            </a>
            <?php if ($credito['email']): ?>
            <a href="mailto:<?= htmlspecialchars($credito['email']) ?>?subject=Enlace de Pago&body=<?= urlencode('Realiza tu pago en el siguiente enlace: ' . $enlacePago) ?>" 
               class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                <i class="fas fa-envelope mr-2"></i>Email
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function copiarEnlace() {
    const input = document.getElementById('enlace-pago');
    input.select();
    input.setSelectionRange(0, 99999);
    document.execCommand('copy');
    
    const msg = document.getElementById('copiado-msg');
    msg.classList.remove('hidden');
    setTimeout(() => msg.classList.add('hidden'), 3000);
}
</script>
