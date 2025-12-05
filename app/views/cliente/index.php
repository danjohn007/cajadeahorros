<?php
/**
 * Vista del Dashboard del Portal del Cliente
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-user-circle mr-2 text-blue-600"></i>Bienvenido, <?= htmlspecialchars($socio['nombre'] ?? $_SESSION['user_nombre']) ?>
    </h1>
    <p class="text-gray-600">Portal del Cliente - Consulta el estado de tus cuentas y créditos</p>
</div>

<!-- Resumen de Cuenta -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Saldo de Ahorro -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Saldo de Ahorro</p>
                <p class="text-2xl font-bold text-green-600">
                    $<?= number_format($cuentaAhorro['saldo'] ?? 0, 2) ?>
                </p>
            </div>
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-piggy-bank text-2xl"></i>
            </div>
        </div>
        <?php if ($cuentaAhorro): ?>
            <p class="text-xs text-gray-500 mt-2">
                Cuenta: <?= htmlspecialchars($cuentaAhorro['numero_cuenta']) ?>
            </p>
        <?php endif; ?>
    </div>
    
    <!-- Deuda Total -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Deuda Total</p>
                <p class="text-2xl font-bold <?= $deudaTotal > 0 ? 'text-red-600' : 'text-gray-800' ?>">
                    $<?= number_format($deudaTotal, 2) ?>
                </p>
            </div>
            <div class="p-3 rounded-full bg-red-100 text-red-600">
                <i class="fas fa-credit-card text-2xl"></i>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-2">
            <?= count($creditos) ?> crédito(s) activo(s)
        </p>
    </div>
    
    <!-- Pagos Vencidos -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Pagos Vencidos</p>
                <p class="text-2xl font-bold <?= count($pagosVencidos) > 0 ? 'text-orange-600' : 'text-green-600' ?>">
                    <?= count($pagosVencidos) ?>
                </p>
            </div>
            <div class="p-3 rounded-full <?= count($pagosVencidos) > 0 ? 'bg-orange-100 text-orange-600' : 'bg-green-100 text-green-600' ?>">
                <i class="fas fa-exclamation-triangle text-2xl"></i>
            </div>
        </div>
        <?php if (count($pagosVencidos) > 0): ?>
            <a href="<?= url('cliente/pagar') ?>" class="text-xs text-blue-600 hover:underline mt-2 inline-block">
                Ver pagos pendientes →
            </a>
        <?php else: ?>
            <p class="text-xs text-green-600 mt-2">
                <i class="fas fa-check-circle mr-1"></i>Al corriente
            </p>
        <?php endif; ?>
    </div>
</div>

<!-- Acciones Rápidas -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
    <a href="<?= url('cliente/estado-cuenta') ?>" class="bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition text-center border-2 border-blue-200">
        <i class="fas fa-file-invoice text-3xl text-blue-600 mb-2"></i>
        <p class="font-medium">Estado de Cuenta</p>
        <p class="text-sm text-gray-500">Ver resumen</p>
    </a>
    
    <a href="<?= url('cliente/cuenta') ?>" class="bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition text-center">
        <i class="fas fa-wallet text-3xl text-blue-600 mb-2"></i>
        <p class="font-medium">Mi Cuenta</p>
        <p class="text-sm text-gray-500">Ver movimientos</p>
    </a>
    
    <a href="<?= url('cliente/creditos') ?>" class="bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition text-center">
        <i class="fas fa-file-invoice-dollar text-3xl text-purple-600 mb-2"></i>
        <p class="font-medium">Mis Créditos</p>
        <p class="text-sm text-gray-500">Ver detalle</p>
    </a>
    
    <a href="<?= url('cliente/pagar') ?>" class="bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition text-center">
        <i class="fas fa-money-bill-wave text-3xl text-green-600 mb-2"></i>
        <p class="font-medium">Realizar Pago</p>
        <p class="text-sm text-gray-500">Pagar en línea</p>
    </a>
    
    <a href="<?= url('usuarios/perfil') ?>" class="bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition text-center">
        <i class="fas fa-user-cog text-3xl text-gray-600 mb-2"></i>
        <p class="font-medium">Mi Perfil</p>
        <p class="text-sm text-gray-500">Configuración</p>
    </a>
</div>

<?php if (!empty($creditos)): ?>
<!-- Lista de Créditos -->
<div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
    <div class="px-6 py-4 border-b">
        <h2 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-list mr-2 text-blue-600"></i>Mis Créditos Activos
        </h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Crédito</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Progreso</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($creditos as $credito): 
                    $progreso = $credito['monto_autorizado'] > 0 
                        ? (($credito['monto_autorizado'] - $credito['saldo_actual']) / $credito['monto_autorizado']) * 100 
                        : 0;
                ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-medium text-gray-900"><?= htmlspecialchars($credito['numero_credito']) ?></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            <?= htmlspecialchars($credito['tipo_credito']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                            $<?= number_format($credito['monto_autorizado'], 2) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-red-600">
                            $<?= number_format($credito['saldo_actual'], 2) ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: <?= min(100, $progreso) ?>%"></div>
                            </div>
                            <span class="text-xs text-gray-500"><?= number_format($progreso, 0) ?>% pagado</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                            <a href="<?= url('cliente/credito/' . $credito['id']) ?>" class="text-blue-600 hover:text-blue-800 mr-3">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                            <a href="<?= url('cliente/amortizacion/' . $credito['id']) ?>" class="text-purple-600 hover:text-purple-800">
                                <i class="fas fa-table"></i> Tabla
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($pagosVencidos)): ?>
<!-- Pagos Vencidos -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b bg-orange-50">
        <h2 class="text-lg font-semibold text-orange-800">
            <i class="fas fa-exclamation-circle mr-2"></i>Pagos Vencidos
        </h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Crédito</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Pago</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Vencimiento</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Días Vencido</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($pagosVencidos as $pago): 
                    $diasVencido = (strtotime(date('Y-m-d')) - strtotime($pago['fecha_vencimiento'])) / 86400;
                ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <?= htmlspecialchars($pago['numero_credito']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            Pago #<?= $pago['numero_pago'] ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            <?= date('d/m/Y', strtotime($pago['fecha_vencimiento'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-red-600">
                            $<?= number_format($pago['monto_total'], 2) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                <?= $diasVencido ?> días
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 bg-gray-50 border-t">
        <a href="<?= url('cliente/pagar') ?>" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            <i class="fas fa-credit-card mr-2"></i>Realizar Pago
        </a>
    </div>
</div>
<?php endif; ?>
