<?php
/**
 * Vista Pública de Pago (sin autenticación)
 * Sistema de Gestión Integral de Caja de Ahorros
 */
$siteName = getSiteName();
$logoUrl = getLogo();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Pago - <?= htmlspecialchars($siteName) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php if ($paypalClientId): ?>
    <script src="https://www.paypal.com/sdk/js?client-id=<?= htmlspecialchars($paypalClientId) ?>&currency=MXN"></script>
    <?php endif; ?>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-lg w-full">
        <!-- Header -->
        <div class="text-center mb-8">
            <?php if ($logoUrl): ?>
                <img src="<?= htmlspecialchars($logoUrl) ?>" alt="<?= htmlspecialchars($siteName) ?>" class="h-16 mx-auto mb-4">
            <?php else: ?>
                <i class="fas fa-piggy-bank text-5xl text-blue-600 mb-4"></i>
            <?php endif; ?>
            <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($siteName) ?></h1>
            <p class="text-gray-600">Sistema de Pagos en Línea</p>
        </div>
        
        <!-- Payment Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Payment Info -->
            <div class="p-6 border-b">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Detalles del Pago</h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Crédito:</span>
                        <span class="font-medium"><?= htmlspecialchars($tokenData['numero_credito']) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Socio:</span>
                        <span class="font-medium"><?= htmlspecialchars($tokenData['nombre'] . ' ' . $tokenData['apellido_paterno']) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Número de Socio:</span>
                        <span class="font-medium"><?= htmlspecialchars($tokenData['numero_socio']) ?></span>
                    </div>
                    <hr>
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-lg font-medium text-gray-800">Monto a Pagar:</span>
                        <span class="text-2xl font-bold text-blue-600">$<?= number_format($tokenData['monto'], 2) ?> MXN</span>
                    </div>
                </div>
            </div>
            
            <!-- PayPal Button -->
            <div class="p-6">
                <?php if ($paypalClientId): ?>
                <div id="paypal-button-container"></div>
                <div id="loading-spinner" class="hidden text-center py-4">
                    <i class="fas fa-spinner fa-spin text-3xl text-blue-600"></i>
                    <p class="text-gray-600 mt-2">Procesando pago...</p>
                </div>
                <div id="success-message" class="hidden text-center py-4 bg-green-50 rounded-lg">
                    <i class="fas fa-check-circle text-5xl text-green-500 mb-4"></i>
                    <h3 class="text-xl font-bold text-green-800">¡Pago Exitoso!</h3>
                    <p class="text-green-600">Tu pago ha sido procesado correctamente.</p>
                </div>
                <div id="error-message" class="hidden text-center py-4 bg-red-50 rounded-lg">
                    <i class="fas fa-times-circle text-5xl text-red-500 mb-4"></i>
                    <h3 class="text-xl font-bold text-red-800">Error en el Pago</h3>
                    <p class="text-red-600" id="error-text"></p>
                </div>
                <?php else: ?>
                <div class="text-center py-4 bg-yellow-50 rounded-lg">
                    <i class="fas fa-exclamation-triangle text-3xl text-yellow-500 mb-2"></i>
                    <p class="text-yellow-700">El sistema de pagos en línea no está configurado.</p>
                    <p class="text-gray-600 mt-2">Por favor contacte a la administración.</p>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Security Info -->
            <div class="bg-gray-50 p-4 border-t">
                <div class="flex items-center justify-center text-sm text-gray-500">
                    <i class="fas fa-lock text-green-500 mr-2"></i>
                    <span>Pago seguro procesado por PayPal</span>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <p class="text-center text-gray-500 text-sm mt-6">
            &copy; <?= date('Y') ?> <?= htmlspecialchars($siteName) ?>. Todos los derechos reservados.
        </p>
    </div>
    
    <?php if ($paypalClientId): ?>
    <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?= number_format($tokenData['monto'], 2, '.', '') ?>',
                            currency_code: 'MXN'
                        },
                        description: 'Pago de crédito <?= htmlspecialchars($tokenData['numero_credito']) ?>'
                    }]
                });
            },
            onApprove: function(data, actions) {
                document.getElementById('paypal-button-container').classList.add('hidden');
                document.getElementById('loading-spinner').classList.remove('hidden');
                
                return actions.order.capture().then(function(details) {
                    // Send to server
                    fetch('<?= BASE_URL ?>/pago/procesar', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            token: '<?= htmlspecialchars($tokenData['token']) ?>',
                            orderID: data.orderID
                        })
                    })
                    .then(response => response.json())
                    .then(result => {
                        document.getElementById('loading-spinner').classList.add('hidden');
                        if (result.success) {
                            document.getElementById('success-message').classList.remove('hidden');
                        } else {
                            document.getElementById('error-message').classList.remove('hidden');
                            document.getElementById('error-text').textContent = result.message;
                        }
                    })
                    .catch(error => {
                        document.getElementById('loading-spinner').classList.add('hidden');
                        document.getElementById('error-message').classList.remove('hidden');
                        document.getElementById('error-text').textContent = 'Error de conexión';
                    });
                });
            },
            onCancel: function(data) {
                alert('Pago cancelado');
            },
            onError: function(err) {
                document.getElementById('error-message').classList.remove('hidden');
                document.getElementById('error-text').textContent = 'Error al procesar el pago';
            }
        }).render('#paypal-button-container');
    </script>
    <?php endif; ?>
</body>
</html>
<?php exit; ?>
