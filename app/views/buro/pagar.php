<?php
/**
 * Vista de pago para consulta al Buró de Crédito
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
    <title><?= htmlspecialchars($pageTitle) ?> - <?= htmlspecialchars($siteName) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php if ($paypalClientId): ?>
    <script src="https://www.paypal.com/sdk/js?client-id=<?= htmlspecialchars($paypalClientId) ?>&currency=MXN"></script>
    <?php endif; ?>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-lg w-full">
        <!-- Header -->
        <div class="text-center mb-8">
            <?php if ($logoUrl): ?>
                <img src="<?= htmlspecialchars($logoUrl) ?>" alt="<?= htmlspecialchars($siteName) ?>" class="h-16 mx-auto mb-4">
            <?php else: ?>
                <i class="fas fa-search-dollar text-5xl text-blue-600 mb-4"></i>
            <?php endif; ?>
            <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($siteName) ?></h1>
            <p class="text-gray-600">Consulta al Buró de Crédito</p>
        </div>
        
        <!-- Payment Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-blue-600 text-white px-6 py-4">
                <h2 class="text-lg font-semibold">Confirmar Pago</h2>
                <p class="text-blue-100 text-sm">Complete el pago para realizar la consulta</p>
            </div>
            
            <!-- Detalles de la Consulta -->
            <div class="p-6 border-b">
                <h3 class="text-sm font-medium text-gray-500 mb-4">Detalles de la Consulta</h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tipo de Consulta:</span>
                        <span class="font-medium"><?= strtoupper($consulta['tipo_consulta']) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Identificador:</span>
                        <span class="font-medium font-mono"><?= htmlspecialchars($consulta['identificador']) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Email:</span>
                        <span class="font-medium"><?= htmlspecialchars($consulta['email_solicitante']) ?></span>
                    </div>
                    <hr>
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-lg font-medium text-gray-800">Total a Pagar:</span>
                        <span class="text-3xl font-bold text-blue-600">$<?= number_format($consulta['costo'], 2) ?></span>
                    </div>
                    <p class="text-xs text-gray-500 text-right">MXN (IVA incluido)</p>
                </div>
            </div>
            
            <!-- PayPal Button -->
            <div class="p-6">
                <?php if ($paypalClientId): ?>
                <div id="paypal-button-container"></div>
                
                <div id="loading-spinner" class="hidden text-center py-8">
                    <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
                    <p class="text-gray-600">Procesando pago y consultando Buró...</p>
                    <p class="text-sm text-gray-500 mt-2">Esto puede tomar unos segundos</p>
                </div>
                
                <div id="success-message" class="hidden text-center py-8">
                    <i class="fas fa-check-circle text-6xl text-green-500 mb-4"></i>
                    <h3 class="text-xl font-bold text-green-800 mb-2">¡Pago Exitoso!</h3>
                    <p class="text-green-600 mb-4">Tu consulta ha sido procesada correctamente.</p>
                    <a href="#" id="resultado-link" class="inline-block bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                        Ver Resultado
                    </a>
                </div>
                
                <div id="error-message" class="hidden text-center py-8">
                    <i class="fas fa-times-circle text-6xl text-red-500 mb-4"></i>
                    <h3 class="text-xl font-bold text-red-800 mb-2">Error en el Proceso</h3>
                    <p class="text-red-600" id="error-text"></p>
                    <button onclick="location.reload()" class="mt-4 text-blue-600 hover:underline">
                        Intentar nuevamente
                    </button>
                </div>
                <?php else: ?>
                <div class="text-center py-8 bg-yellow-50 rounded-lg">
                    <i class="fas fa-exclamation-triangle text-3xl text-yellow-500 mb-2"></i>
                    <p class="text-yellow-700">El sistema de pagos no está configurado.</p>
                    <p class="text-gray-600 mt-2">Por favor contacte a la administración.</p>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Seguridad -->
            <div class="bg-gray-50 px-6 py-4 border-t">
                <div class="flex items-center justify-center text-sm text-gray-500">
                    <i class="fas fa-lock text-green-500 mr-2"></i>
                    <span>Pago seguro procesado por PayPal</span>
                </div>
            </div>
        </div>
        
        <!-- Volver -->
        <div class="text-center mt-6">
            <a href="<?= url('buro/consulta') ?>" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left mr-1"></i> Volver al formulario
            </a>
        </div>
        
        <!-- Footer -->
        <p class="text-center text-gray-500 text-sm mt-6">
            &copy; <?= date('Y') ?> <?= htmlspecialchars($siteName) ?>
        </p>
    </div>
    
    <?php if ($paypalClientId): ?>
    <script>
        paypal.Buttons({
            style: {
                layout: 'vertical',
                color: 'blue',
                shape: 'rect',
                label: 'pay'
            },
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?= number_format($consulta['costo'], 2, '.', '') ?>',
                            currency_code: 'MXN'
                        },
                        description: 'Consulta Buró de Crédito - <?= strtoupper($consulta['tipo_consulta']) ?>: <?= htmlspecialchars($consulta['identificador']) ?>'
                    }]
                });
            },
            onApprove: function(data, actions) {
                document.getElementById('paypal-button-container').classList.add('hidden');
                document.getElementById('loading-spinner').classList.remove('hidden');
                
                return actions.order.capture().then(function(details) {
                    fetch('<?= BASE_URL ?>/buro/procesar', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            token: '<?= htmlspecialchars($consulta['token_consulta']) ?>',
                            orderID: data.orderID
                        })
                    })
                    .then(response => response.json())
                    .then(result => {
                        document.getElementById('loading-spinner').classList.add('hidden');
                        if (result.success) {
                            document.getElementById('success-message').classList.remove('hidden');
                            if (result.redirect) {
                                document.getElementById('resultado-link').href = result.redirect;
                            }
                        } else {
                            document.getElementById('error-message').classList.remove('hidden');
                            document.getElementById('error-text').textContent = result.message || 'Error desconocido';
                        }
                    })
                    .catch(error => {
                        document.getElementById('loading-spinner').classList.add('hidden');
                        document.getElementById('error-message').classList.remove('hidden');
                        document.getElementById('error-text').textContent = 'Error de conexión. Por favor intente nuevamente.';
                    });
                });
            },
            onCancel: function(data) {
                // El usuario canceló el pago
            },
            onError: function(err) {
                document.getElementById('error-message').classList.remove('hidden');
                document.getElementById('error-text').textContent = 'Error al procesar el pago. Por favor intente nuevamente.';
            }
        }).render('#paypal-button-container');
    </script>
    <?php endif; ?>
</body>
</html>
