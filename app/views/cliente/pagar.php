<?php
/**
 * Vista de Pago del Cliente
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
        <p class="text-gray-600">Realiza el pago de tus créditos pendientes</p>
    </div>
    <a href="<?= url('cliente') ?>" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Volver al Portal
    </a>
</div>

<?php if (empty($creditos)): ?>
    <div class="bg-white rounded-lg shadow-md p-8 text-center">
        <div class="text-green-500 mb-4">
            <i class="fas fa-check-circle text-6xl"></i>
        </div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">¡Excelente!</h2>
        <p class="text-gray-600">No tienes créditos pendientes de pago</p>
    </div>
<?php else: ?>

<!-- Resumen de Deuda -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Deuda Total</h2>
            <p class="text-3xl font-bold text-red-600">$<?= number_format($deudaTotal, 2) ?></p>
        </div>
        <?php if (!$paypalEnabled): ?>
            <div class="text-yellow-600 bg-yellow-50 px-4 py-2 rounded-lg">
                <i class="fas fa-info-circle mr-2"></i>
                Pagos en línea no disponibles
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Lista de Créditos con Pagos Pendientes -->
<?php foreach ($creditos as $credito): ?>
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-4 border-b bg-gray-50">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">
                        <?= htmlspecialchars($credito['numero_credito']) ?>
                    </h3>
                    <p class="text-sm text-gray-600"><?= htmlspecialchars($credito['tipo_credito']) ?></p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Saldo Pendiente</p>
                    <p class="text-xl font-bold text-red-600">$<?= number_format($credito['saldo_actual'], 2) ?></p>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <?php 
            $pagosVencidos = $pagosVencidosPorCredito[$credito['id']] ?? [];
            if (!empty($pagosVencidos)): 
            ?>
                <h4 class="font-medium text-red-700 mb-3">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Pagos Vencidos (<?= count($pagosVencidos) ?>)
                </h4>
                
                <div class="overflow-x-auto mb-4">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-red-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">No. Pago</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Vencimiento</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Días Vencido</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php 
                            $totalVencido = 0;
                            foreach ($pagosVencidos as $pago): 
                                $diasVencido = (strtotime(date('Y-m-d')) - strtotime($pago['fecha_vencimiento'])) / 86400;
                                $totalVencido += $pago['monto_total'];
                            ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm">Pago #<?= $pago['numero_pago'] ?></td>
                                    <td class="px-4 py-2 text-sm"><?= date('d/m/Y', strtotime($pago['fecha_vencimiento'])) ?></td>
                                    <td class="px-4 py-2 text-sm text-right font-medium">$<?= number_format($pago['monto_total'], 2) ?></td>
                                    <td class="px-4 py-2 text-center">
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                            <?= $diasVencido ?> días
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-red-100">
                            <tr>
                                <td colspan="2" class="px-4 py-2 text-sm font-medium text-red-800">Total Vencido</td>
                                <td class="px-4 py-2 text-right text-lg font-bold text-red-800">$<?= number_format($totalVencido, 2) ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php else: ?>
                <div class="bg-green-50 p-4 rounded-lg mb-4">
                    <p class="text-green-700">
                        <i class="fas fa-check-circle mr-2"></i>
                        Sin pagos vencidos para este crédito
                    </p>
                </div>
            <?php endif; ?>
            
            <div class="flex justify-between items-center pt-4 border-t">
                <a href="<?= url('cliente/amortizacion/' . $credito['id']) ?>" class="text-purple-600 hover:text-purple-800">
                    <i class="fas fa-table mr-2"></i>Ver Tabla de Amortización
                </a>
                
                <?php if ($paypalEnabled && $credito['saldo_actual'] > 0): ?>
                    <div class="space-x-3">
                        <?php if (!empty($pagosVencidos)): ?>
                            <button type="button" 
                                    onclick="mostrarModalPago('<?= $credito['numero_credito'] ?>', <?= $totalVencido ?>, 'vencido')"
                                    class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                                <i class="fas fa-credit-card mr-2"></i>Pagar Vencidos ($<?= number_format($totalVencido, 2) ?>)
                            </button>
                        <?php endif; ?>
                        <button type="button" 
                                onclick="mostrarModalPago('<?= $credito['numero_credito'] ?>', <?= $credito['saldo_actual'] ?>, 'total')"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="fas fa-money-bill-wave mr-2"></i>Liquidar Todo ($<?= number_format($credito['saldo_actual'], 2) ?>)
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php if (!$paypalEnabled): ?>
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="font-medium text-blue-800 mb-2">
            <i class="fas fa-info-circle mr-2"></i>Información de Pago
        </h3>
        <p class="text-blue-700 mb-4">
            Los pagos en línea no están disponibles en este momento. Para realizar tu pago, acude a nuestras oficinas 
            o utiliza los siguientes medios:
        </p>
        <ul class="text-blue-700 list-disc list-inside space-y-1">
            <li>Pago en ventanilla en nuestras oficinas</li>
            <li>Transferencia bancaria (solicita los datos en oficinas)</li>
            <li>Descuento vía nómina (si aplica)</li>
        </ul>
        <div class="mt-4 pt-4 border-t border-blue-200">
            <p class="text-sm text-blue-600">
                <i class="fas fa-phone mr-2"></i>
                Teléfono: <?= htmlspecialchars(getConfig('telefono_contacto', 'No disponible')) ?>
            </p>
        </div>
    </div>
<?php endif; ?>

<?php endif; ?>

<!-- Modal de Pago (solo si PayPal está habilitado) -->
<?php if ($paypalEnabled): ?>
<?php 
    $paypalClientId = getConfig('paypal_client_id', '');
?>
<div id="modalPago" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold">Confirmar Pago</h3>
            <button onclick="cerrarModalPago()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6">
            <div id="infoPago" class="mb-6">
                <!-- Se llena dinámicamente -->
            </div>
            <div id="paypal-container">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        Tu pago será procesado de forma segura a través de PayPal.
                    </p>
                </div>
                <div id="paypal-button-container"></div>
                <div id="loading-spinner" class="hidden text-center py-4">
                    <i class="fas fa-spinner fa-spin text-3xl text-blue-600"></i>
                    <p class="text-gray-600 mt-2">Procesando pago...</p>
                </div>
            </div>
            <div id="success-message" class="hidden text-center py-8">
                <i class="fas fa-check-circle text-5xl text-green-500 mb-4"></i>
                <h3 class="text-xl font-bold text-green-800">¡Pago Exitoso!</h3>
                <p class="text-green-600 mb-4">Tu pago ha sido procesado correctamente.</p>
                <button onclick="location.reload()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-redo mr-2"></i>Actualizar Página
                </button>
            </div>
            <div id="error-message" class="hidden text-center py-8">
                <i class="fas fa-times-circle text-5xl text-red-500 mb-4"></i>
                <h3 class="text-xl font-bold text-red-800">Error en el Pago</h3>
                <p class="text-red-600 mb-4" id="error-text"></p>
                <button onclick="reiniciarPago()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    <i class="fas fa-redo mr-2"></i>Intentar de nuevo
                </button>
            </div>
            <div class="flex justify-end mt-4">
                <button id="btnCancelar" onclick="cerrarModalPago()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://www.paypal.com/sdk/js?client-id=<?= htmlspecialchars($paypalClientId) ?>&currency=MXN"></script>
<script>
let currentCredito = null;
let currentMonto = 0;
let currentTipo = null;
let paypalButtonsRendered = false;

function mostrarModalPago(credito, monto, tipo) {
    const modal = document.getElementById('modalPago');
    const infoPago = document.getElementById('infoPago');
    
    currentCredito = credito;
    currentMonto = monto;
    currentTipo = tipo;
    
    let tipoTexto = tipo === 'total' ? 'Liquidación Total' : 'Pagos Vencidos';
    
    infoPago.innerHTML = `
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-gray-600">Crédito:</span>
                <span class="font-medium">${credito}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Tipo:</span>
                <span class="font-medium">${tipoTexto}</span>
            </div>
            <div class="flex justify-between text-lg">
                <span class="text-gray-600">Monto a Pagar:</span>
                <span class="font-bold text-green-600">$${monto.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
            </div>
        </div>
    `;
    
    // Reset visibility
    document.getElementById('paypal-container').classList.remove('hidden');
    document.getElementById('success-message').classList.add('hidden');
    document.getElementById('error-message').classList.add('hidden');
    document.getElementById('btnCancelar').classList.remove('hidden');
    document.getElementById('loading-spinner').classList.add('hidden');
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Render PayPal buttons
    renderPayPalButtons();
}

function renderPayPalButtons() {
    const container = document.getElementById('paypal-button-container');
    container.innerHTML = '';
    
    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: currentMonto.toFixed(2),
                        currency_code: 'MXN'
                    },
                    description: 'Pago de crédito ' + currentCredito + ' - ' + (currentTipo === 'total' ? 'Liquidación Total' : 'Pagos Vencidos')
                }]
            });
        },
        onApprove: function(data, actions) {
            document.getElementById('paypal-button-container').classList.add('hidden');
            document.getElementById('loading-spinner').classList.remove('hidden');
            document.getElementById('btnCancelar').classList.add('hidden');
            
            return actions.order.capture().then(function(details) {
                // Process payment on server
                fetch('<?= BASE_URL ?>/cliente/procesarPago', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-Token': '<?= csrf_token() ?>'
                    },
                    body: JSON.stringify({
                        csrf_token: '<?= csrf_token() ?>',
                        credito: currentCredito,
                        monto: currentMonto,
                        tipo: currentTipo,
                        paypal_order_id: data.orderID,
                        payer_email: details.payer.email_address,
                        transaction_id: details.purchase_units[0].payments.captures[0].id
                    })
                })
                .then(response => response.json())
                .then(result => {
                    document.getElementById('loading-spinner').classList.add('hidden');
                    document.getElementById('paypal-container').classList.add('hidden');
                    if (result.success) {
                        document.getElementById('success-message').classList.remove('hidden');
                    } else {
                        document.getElementById('error-message').classList.remove('hidden');
                        document.getElementById('error-text').textContent = result.message || 'Error al procesar el pago';
                    }
                })
                .catch(error => {
                    document.getElementById('loading-spinner').classList.add('hidden');
                    document.getElementById('paypal-container').classList.add('hidden');
                    document.getElementById('error-message').classList.remove('hidden');
                    document.getElementById('error-text').textContent = 'Error de conexión. Por favor intenta de nuevo.';
                });
            });
        },
        onCancel: function(data) {
            // User cancelled, no action needed
        },
        onError: function(err) {
            document.getElementById('paypal-container').classList.add('hidden');
            document.getElementById('error-message').classList.remove('hidden');
            document.getElementById('error-text').textContent = 'Error al inicializar PayPal. Por favor intenta de nuevo.';
        }
    }).render('#paypal-button-container');
}

function reiniciarPago() {
    document.getElementById('paypal-container').classList.remove('hidden');
    document.getElementById('success-message').classList.add('hidden');
    document.getElementById('error-message').classList.add('hidden');
    document.getElementById('btnCancelar').classList.remove('hidden');
    renderPayPalButtons();
}

function cerrarModalPago() {
    const modal = document.getElementById('modalPago');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.getElementById('modalPago').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalPago();
    }
});
</script>
<?php endif; ?>
