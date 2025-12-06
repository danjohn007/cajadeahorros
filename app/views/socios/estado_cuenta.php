<?php
/**
 * Vista de Estado de Cuenta del Socio
 * Sistema de Gestión Integral de Caja de Ahorros
 */
$siteName = getSiteName();
$logoUrl = getLogo();
$emailContacto = getConfig('email_contacto', '');
$telefonoContacto = getConfig('telefono_contacto', '');
$direccionOficina = getConfig('direccion_oficina', '');
$meses = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];
?>

<!-- Header -->
<div class="mb-6 flex justify-between items-center no-print">
    <div>
        <a href="<?= BASE_URL ?>/socios" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
            <i class="fas fa-arrow-left mr-1"></i> Volver a Socios
        </a>
        <h2 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h2>
        <p class="text-gray-600">
            <?= htmlspecialchars($socio['nombre'] . ' ' . $socio['apellido_paterno'] . ' ' . ($socio['apellido_materno'] ?? '')) ?> - 
            <?= htmlspecialchars($socio['numero_socio']) ?>
        </p>
    </div>
    <div class="flex space-x-2">
        <button onclick="window.print()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
            <i class="fas fa-print mr-2"></i> Imprimir
        </button>
    </div>
</div>

<!-- Filtro de Fecha -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6 no-print">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Mes</label>
            <select name="mes" class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                <?php foreach ($meses as $num => $nombre): ?>
                    <option value="<?= $num ?>" <?= $num == $mes ? 'selected' : '' ?>><?= $nombre ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Año</label>
            <select name="anio" class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                    <option value="<?= $y ?>" <?= $y == $anio ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            <i class="fas fa-filter mr-2"></i>Filtrar
        </button>
    </form>
</div>

<!-- Estado de Cuenta Imprimible -->
<div class="bg-white rounded-xl shadow-sm p-6" id="estado-cuenta-print">
    <!-- Header para impresión -->
    <div class="text-center border-b-2 border-gray-800 pb-4 mb-4 print-header">
        <?php if ($logoUrl): ?>
            <img src="<?= htmlspecialchars($logoUrl) ?>" alt="<?= htmlspecialchars($siteName) ?>" class="h-16 mx-auto mb-2">
        <?php endif; ?>
        <h1 class="text-2xl font-bold"><?= htmlspecialchars($siteName) ?></h1>
        <?php if ($direccionOficina): ?>
            <p class="text-xs text-gray-600"><?= htmlspecialchars($direccionOficina) ?></p>
        <?php endif; ?>
        <?php if ($telefonoContacto || $emailContacto): ?>
            <p class="text-xs text-gray-600">
                <?php if ($telefonoContacto): ?>Tel: <?= htmlspecialchars($telefonoContacto) ?><?php endif; ?>
                <?php if ($telefonoContacto && $emailContacto): ?> | <?php endif; ?>
                <?php if ($emailContacto): ?>Email: <?= htmlspecialchars($emailContacto) ?><?php endif; ?>
            </p>
        <?php endif; ?>
        <h2 class="text-lg font-semibold mt-2">ESTADO DE CUENTA</h2>
        <p class="text-sm text-gray-600"><?= $meses[$mes] ?> <?= $anio ?></p>
        <p class="text-sm text-gray-600">Fecha de impresión: <?= date('d/m/Y H:i:s') ?></p>
    </div>
    
    <!-- Información del Socio -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 border rounded-lg p-4 bg-gray-50">
        <div>
            <h3 class="font-bold text-gray-800 mb-2 border-b pb-1">Datos del Socio</h3>
            <p class="text-sm"><strong>Número de Socio:</strong> <?= htmlspecialchars($socio['numero_socio']) ?></p>
            <p class="text-sm"><strong>Nombre:</strong> <?= htmlspecialchars($socio['nombre'] . ' ' . $socio['apellido_paterno'] . ' ' . ($socio['apellido_materno'] ?? '')) ?></p>
            <?php if ($socio['rfc']): ?>
            <p class="text-sm"><strong>RFC:</strong> <?= htmlspecialchars($socio['rfc']) ?></p>
            <?php endif; ?>
            <?php if ($socio['curp']): ?>
            <p class="text-sm"><strong>CURP:</strong> <?= htmlspecialchars($socio['curp']) ?></p>
            <?php endif; ?>
            <?php if ($socio['telefono'] || $socio['celular']): ?>
            <p class="text-sm"><strong>Teléfono:</strong> <?= htmlspecialchars($socio['telefono'] ?: $socio['celular']) ?></p>
            <?php endif; ?>
            <?php if ($socio['email']): ?>
            <p class="text-sm"><strong>Email:</strong> <?= htmlspecialchars($socio['email']) ?></p>
            <?php endif; ?>
        </div>
        <div>
            <h3 class="font-bold text-gray-800 mb-2 border-b pb-1">Información de Empleo</h3>
            <?php if ($socio['unidad_trabajo']): ?>
            <p class="text-sm"><strong>Unidad de Trabajo:</strong> <?= htmlspecialchars($socio['unidad_trabajo']) ?></p>
            <?php endif; ?>
            <?php if ($socio['puesto']): ?>
            <p class="text-sm"><strong>Puesto:</strong> <?= htmlspecialchars($socio['puesto']) ?></p>
            <?php endif; ?>
            <?php if ($socio['numero_empleado']): ?>
            <p class="text-sm"><strong>No. Empleado:</strong> <?= htmlspecialchars($socio['numero_empleado']) ?></p>
            <?php endif; ?>
            <p class="text-sm"><strong>Estatus:</strong> <?= ucfirst($socio['estatus']) ?></p>
            <?php if (!empty($socio['asesor_nombre'])): ?>
            <p class="text-sm"><strong>Asesor:</strong> <?= htmlspecialchars($socio['asesor_nombre']) ?></p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Resumen de Ahorro -->
    <?php if ($cuentaAhorro): ?>
    <div class="mb-6">
        <h3 class="font-bold text-gray-800 mb-3 border-b-2 border-blue-500 pb-1">
            <i class="fas fa-piggy-bank text-blue-600 mr-2"></i>CUENTA DE AHORRO
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
            <div class="bg-blue-50 rounded-lg p-3">
                <p class="text-xs text-gray-500">Número de Cuenta</p>
                <p class="font-bold text-blue-600"><?= htmlspecialchars($cuentaAhorro['numero_cuenta']) ?></p>
            </div>
            <div class="bg-green-50 rounded-lg p-3">
                <p class="text-xs text-gray-500">Saldo Actual</p>
                <p class="font-bold text-green-600">$<?= number_format($cuentaAhorro['saldo'], 2) ?></p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-500">Fecha Apertura</p>
                <p class="font-medium"><?= date('d/m/Y', strtotime($cuentaAhorro['fecha_apertura'])) ?></p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-500">Tasa de Interés</p>
                <p class="font-medium"><?= ($cuentaAhorro['tasa_interes'] * 100) ?>%</p>
            </div>
        </div>
        
        <!-- Movimientos del mes -->
        <h4 class="font-medium text-gray-700 mb-2">Movimientos del Periodo</h4>
        <table class="w-full text-sm border-collapse mb-4">
            <thead>
                <tr class="bg-gray-200">
                    <th class="px-3 py-2 text-left border">Fecha</th>
                    <th class="px-3 py-2 text-left border">Concepto</th>
                    <th class="px-3 py-2 text-right border">Cargo</th>
                    <th class="px-3 py-2 text-right border">Abono</th>
                    <th class="px-3 py-2 text-right border">Saldo</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($movimientosAhorro)): ?>
                <tr>
                    <td colspan="5" class="px-3 py-3 text-center text-gray-500 border">
                        Sin movimientos en este periodo
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($movimientosAhorro as $mov): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 border"><?= date('d/m/Y', strtotime($mov['fecha'])) ?></td>
                        <td class="px-3 py-2 border"><?= htmlspecialchars($mov['concepto'] ?: ucfirst($mov['tipo'])) ?></td>
                        <td class="px-3 py-2 text-right border text-red-600">
                            <?= $mov['tipo'] === 'retiro' ? '$' . number_format($mov['monto'], 2) : '-' ?>
                        </td>
                        <td class="px-3 py-2 text-right border text-green-600">
                            <?= $mov['tipo'] !== 'retiro' ? '$' . number_format($mov['monto'], 2) : '-' ?>
                        </td>
                        <td class="px-3 py-2 text-right border font-medium">$<?= number_format($mov['saldo_nuevo'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    
    <!-- Créditos -->
    <?php if (!empty($creditos)): ?>
    <div class="mb-6">
        <h3 class="font-bold text-gray-800 mb-3 border-b-2 border-orange-500 pb-1">
            <i class="fas fa-hand-holding-usd text-orange-600 mr-2"></i>CRÉDITOS ACTIVOS
        </h3>
        
        <?php foreach ($creditos as $credito): ?>
        <div class="border rounded-lg p-4 mb-4 bg-gray-50">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-3">
                <div>
                    <p class="text-xs text-gray-500">No. Crédito</p>
                    <p class="font-bold text-orange-600"><?= htmlspecialchars($credito['numero_credito']) ?></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Tipo</p>
                    <p class="font-medium"><?= htmlspecialchars($credito['tipo_credito']) ?></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Monto Autorizado</p>
                    <p class="font-medium">$<?= number_format($credito['monto_autorizado'], 2) ?></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Saldo Actual</p>
                    <p class="font-bold text-red-600">$<?= number_format($credito['saldo_actual'], 2) ?></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Cuota Mensual</p>
                    <p class="font-medium">$<?= number_format($credito['monto_cuota'], 2) ?></p>
                </div>
            </div>
            
            <!-- Pagos del periodo -->
            <?php if (isset($pagosCredito[$credito['id']]) && !empty($pagosCredito[$credito['id']])): ?>
            <h5 class="font-medium text-gray-700 mb-2 text-sm">Pagos realizados en el periodo:</h5>
            <table class="w-full text-sm border-collapse mb-3">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-2 py-1 text-left border">Fecha</th>
                        <th class="px-2 py-1 text-center border">Pago #</th>
                        <th class="px-2 py-1 text-right border">Capital</th>
                        <th class="px-2 py-1 text-right border">Interés</th>
                        <th class="px-2 py-1 text-right border">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pagosCredito[$credito['id']] as $pago): ?>
                    <tr>
                        <td class="px-2 py-1 border"><?= date('d/m/Y', strtotime($pago['fecha_pago'])) ?></td>
                        <td class="px-2 py-1 text-center border"><?= $pago['numero_pago'] ?? '-' ?></td>
                        <td class="px-2 py-1 text-right border">$<?= number_format($pago['monto_capital'], 2) ?></td>
                        <td class="px-2 py-1 text-right border">$<?= number_format($pago['monto_interes'], 2) ?></td>
                        <td class="px-2 py-1 text-right border font-medium">$<?= number_format($pago['monto'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
            
            <!-- Próximos pagos -->
            <?php if (isset($amortizacionPendiente[$credito['id']]) && !empty($amortizacionPendiente[$credito['id']])): ?>
            <h5 class="font-medium text-gray-700 mb-2 text-sm">Próximos pagos:</h5>
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-orange-100">
                        <th class="px-2 py-1 text-center border">Pago #</th>
                        <th class="px-2 py-1 text-left border">Vencimiento</th>
                        <th class="px-2 py-1 text-right border">Monto</th>
                        <th class="px-2 py-1 text-center border">Estatus</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($amortizacionPendiente[$credito['id']] as $amort): ?>
                    <tr>
                        <td class="px-2 py-1 text-center border"><?= $amort['numero_pago'] ?></td>
                        <td class="px-2 py-1 border <?= $amort['estatus'] === 'vencido' ? 'text-red-600 font-bold' : '' ?>">
                            <?= date('d/m/Y', strtotime($amort['fecha_vencimiento'])) ?>
                        </td>
                        <td class="px-2 py-1 text-right border">$<?= number_format($amort['monto_total'], 2) ?></td>
                        <td class="px-2 py-1 text-center border">
                            <?php if ($amort['estatus'] === 'vencido'): ?>
                                <span class="px-2 py-0.5 bg-red-100 text-red-800 rounded text-xs">Vencido</span>
                            <?php else: ?>
                                <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded text-xs">Pendiente</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <!-- Footer -->
    <div class="mt-6 pt-4 border-t text-center text-sm text-gray-500 print-footer">
        <p>Este documento es un resumen informativo y no tiene validez fiscal.</p>
        <p>Para cualquier aclaración, favor de acudir a las oficinas de la Caja de Ahorros.</p>
    </div>
</div>

<style>
@media print {
    /* Ocultar todos los elementos excepto el área de impresión */
    body * {
        visibility: hidden;
    }
    
    #estado-cuenta-print, #estado-cuenta-print * {
        visibility: visible;
    }
    
    #estado-cuenta-print {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none !important;
        border-radius: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    
    .no-print, .no-print * {
        visibility: hidden !important;
        display: none !important;
    }
    
    body {
        margin: 0;
        padding: 0;
        font-size: 11px;
    }
    
    table {
        font-size: 9px !important;
        page-break-inside: auto;
    }
    
    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
    
    thead {
        display: table-header-group;
    }
    
    img {
        max-width: 100px !important;
        height: auto !important;
    }
    
    @page {
        size: A4 portrait;
        margin: 1.5cm;
    }
    
    * {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>
