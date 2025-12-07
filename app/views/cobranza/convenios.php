<?php
/**
 * Vista de Convenios de Pago
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Convenios de Pago</h1>
        <a href="<?= BASE_URL ?>/cobranza" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Regresar
        </a>
    </div>

    <?php if (isset($credito)): ?>
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Nuevo Convenio de Pago</h2>
        <form method="POST" action="<?= BASE_URL ?>/cobranza/convenios" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <input type="hidden" name="credito_id" value="<?= $credito['id'] ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Monto del Convenio</label>
                    <input type="number" name="monto" step="0.01" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Número de Pagos</label>
                    <input type="number" name="numero_pagos" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Primer Pago</label>
                    <input type="date" name="fecha_primer_pago" class="w-full border rounded px-3 py-2" required>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                <textarea name="observaciones" rows="3" class="w-full border rounded px-3 py-2"></textarea>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-primary-800 hover:bg-primary-700 text-white px-6 py-2 rounded">
                    <i class="fas fa-save mr-2"></i>Registrar Convenio
                </button>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold">Créditos con Mora para Convenio</h2>
        </div>
        
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Crédito</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Socio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Días Mora</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto Vencido</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($creditosVencidos)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay créditos vencidos</td>
                </tr>
                <?php else: ?>
                <?php foreach ($creditosVencidos as $cred): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($cred['numero_credito'] ?? 'N/A') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?= htmlspecialchars($cred['nombre'] . ' ' . $cred['apellido_paterno']) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                            <?= $cred['dias_mora_max'] ?> días
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">$<?= number_format($cred['monto_vencido'] ?? 0, 2) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="<?= BASE_URL ?>/cobranza/convenios?credito_id=<?= $cred['id'] ?>" class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-handshake"></i> Crear Convenio
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
