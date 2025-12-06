<?php
/**
 * Vista de Cardex del Socio - Historial completo de movimientos para impresión
 * Sistema de Gestión Integral de Caja de Ahorros
 */
$siteName = getSiteName();
$logoUrl = getLogo();
$emailContacto = getConfig('email_contacto', '');
$telefonoContacto = getConfig('telefono_contacto', '');
$direccionOficina = getConfig('direccion_oficina', '');
?>

<!-- Header -->
<div class="mb-6 flex justify-between items-center no-print">
    <div>
        <a href="<?= BASE_URL ?>/ahorro" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
            <i class="fas fa-arrow-left mr-1"></i> Volver a Ahorro
        </a>
        <h2 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h2>
        <p class="text-gray-600">
            <?= htmlspecialchars($cuenta['nombre'] . ' ' . $cuenta['apellido_paterno'] . ' ' . ($cuenta['apellido_materno'] ?? '')) ?> - 
            <?= htmlspecialchars($cuenta['numero_cuenta']) ?>
        </p>
    </div>
    <button onclick="window.print()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
        <i class="fas fa-print mr-2"></i> Imprimir Cardex
    </button>
</div>

<!-- Printable Cardex -->
<div class="bg-white rounded-xl shadow-sm p-6" id="cardex-print">
    <!-- Header for print -->
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
        <h2 class="text-lg font-semibold mt-2">CARDEX DEL SOCIO</h2>
        <p class="text-sm text-gray-600">Fecha de impresión: <?= date('d/m/Y H:i:s') ?></p>
    </div>
    
    <!-- Socio Info -->
    <div class="grid grid-cols-2 gap-4 mb-6 border rounded-lg p-4 bg-gray-50">
        <div>
            <p class="text-sm"><strong>Número de Socio:</strong> <?= htmlspecialchars($cuenta['numero_socio']) ?></p>
            <p class="text-sm"><strong>Nombre:</strong> <?= htmlspecialchars($cuenta['nombre'] . ' ' . $cuenta['apellido_paterno'] . ' ' . ($cuenta['apellido_materno'] ?? '')) ?></p>
            <?php if ($cuenta['rfc']): ?>
            <p class="text-sm"><strong>RFC:</strong> <?= htmlspecialchars($cuenta['rfc']) ?></p>
            <?php endif; ?>
            <?php if ($cuenta['curp']): ?>
            <p class="text-sm"><strong>CURP:</strong> <?= htmlspecialchars($cuenta['curp']) ?></p>
            <?php endif; ?>
        </div>
        <div>
            <p class="text-sm"><strong>Número de Cuenta:</strong> <?= htmlspecialchars($cuenta['numero_cuenta']) ?></p>
            <p class="text-sm"><strong>Fecha Apertura:</strong> <?= date('d/m/Y', strtotime($cuenta['fecha_apertura'])) ?></p>
            <p class="text-sm"><strong>Estatus:</strong> <?= ucfirst($cuenta['estatus']) ?></p>
            <?php if (!empty($cuenta['asesor_nombre'])): ?>
            <p class="text-sm"><strong>Asesor:</strong> <?= htmlspecialchars($cuenta['asesor_nombre']) ?></p>
            <?php endif; ?>
            <p class="text-sm text-lg"><strong>Saldo Actual: $<?= number_format($cuenta['saldo'], 2) ?></strong></p>
        </div>
    </div>
    
    <!-- Movements Table -->
    <table class="w-full text-sm border-collapse">
        <thead>
            <tr class="bg-gray-800 text-white">
                <th class="px-3 py-2 text-left border">Fecha</th>
                <th class="px-3 py-2 text-left border">Tipo</th>
                <th class="px-3 py-2 text-left border">Concepto</th>
                <th class="px-3 py-2 text-right border">Cargo</th>
                <th class="px-3 py-2 text-right border">Abono</th>
                <th class="px-3 py-2 text-right border">Saldo</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($movimientos)): ?>
            <tr>
                <td colspan="6" class="px-3 py-4 text-center text-gray-500 border">
                    No hay movimientos registrados
                </td>
            </tr>
            <?php else: ?>
                <!-- Initial Row -->
                <tr class="bg-gray-100">
                    <td class="px-3 py-2 border"><?= date('d/m/Y', strtotime($cuenta['fecha_apertura'])) ?></td>
                    <td class="px-3 py-2 border">APERTURA</td>
                    <td class="px-3 py-2 border">Apertura de cuenta</td>
                    <td class="px-3 py-2 text-right border">-</td>
                    <td class="px-3 py-2 text-right border">-</td>
                    <td class="px-3 py-2 text-right border font-medium">$0.00</td>
                </tr>
                <?php foreach ($movimientos as $mov): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-3 py-2 border"><?= date('d/m/Y H:i', strtotime($mov['fecha'])) ?></td>
                    <td class="px-3 py-2 border">
                        <span class="<?= $mov['tipo'] === 'retiro' ? 'text-red-600' : 'text-green-600' ?>">
                            <?= strtoupper($mov['tipo']) ?>
                        </span>
                    </td>
                    <td class="px-3 py-2 border">
                        <?= htmlspecialchars($mov['concepto'] ?: ucfirst($mov['tipo'])) ?>
                        <?php if ($mov['referencia']): ?>
                            <br><span class="text-xs text-gray-500">Ref: <?= htmlspecialchars($mov['referencia']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="px-3 py-2 text-right border text-red-600">
                        <?= $mov['tipo'] === 'retiro' ? '$' . number_format($mov['monto'], 2) : '-' ?>
                    </td>
                    <td class="px-3 py-2 text-right border text-green-600">
                        <?= $mov['tipo'] !== 'retiro' ? '$' . number_format($mov['monto'], 2) : '-' ?>
                    </td>
                    <td class="px-3 py-2 text-right border font-medium">
                        $<?= number_format($mov['saldo_nuevo'], 2) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="bg-gray-800 text-white font-bold">
                <td colspan="3" class="px-3 py-2 border text-right">SALDO ACTUAL:</td>
                <td class="px-3 py-2 text-right border">
                    $<?= number_format(array_sum(array_map(function($m) { return $m['tipo'] === 'retiro' ? $m['monto'] : 0; }, $movimientos)), 2) ?>
                </td>
                <td class="px-3 py-2 text-right border">
                    $<?= number_format(array_sum(array_map(function($m) { return $m['tipo'] !== 'retiro' ? $m['monto'] : 0; }, $movimientos)), 2) ?>
                </td>
                <td class="px-3 py-2 text-right border">$<?= number_format($cuenta['saldo'], 2) ?></td>
            </tr>
        </tfoot>
    </table>
    
    <!-- Footer for print -->
    <div class="mt-6 pt-4 border-t text-center text-sm text-gray-500 print-footer">
        <p>Este documento es un resumen de movimientos y no tiene validez fiscal.</p>
        <p>Total de movimientos: <?= count($movimientos) ?></p>
    </div>
</div>

<style>
@media print {
    /* Ocultar elementos del sistema que no deben aparecer en la impresión */
    aside, nav, header:not(#cardex-print header), footer, 
    .sidebar, .no-print, button:not(#cardex-print button) {
        display: none !important;
    }
    
    /* Asegurar que el contenido principal ocupe toda la página */
    body {
        margin: 0;
        padding: 0;
        font-size: 11px;
    }
    
    #cardex-print {
        position: static;
        width: 100%;
        max-width: 100%;
        box-shadow: none !important;
        border-radius: 0 !important;
        padding: 20px !important;
        margin: 0 !important;
    }
    
    /* Ajustar tamaño de fuente para impresión */
    table {
        font-size: 9px !important;
        page-break-inside: auto;
    }
    
    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
    
    .print-header {
        display: block !important;
        page-break-after: avoid;
    }
    
    .print-footer {
        display: block !important;
        text-align: center;
        padding: 10px 0;
        margin-top: 20px;
        border-top: 1px solid #ccc;
    }
    
    /* Asegurar que el logo se imprima correctamente */
    img {
        max-width: 100%;
        height: auto;
    }
    
    /* Usar page margins en lugar de position fixed */
    @page {
        margin: 2cm;
    }
}

@media screen {
    .print-header {
        display: block;
    }
}
</style>
