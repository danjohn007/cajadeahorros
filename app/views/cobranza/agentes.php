<?php
/**
 * Vista de Agentes de Cobranza
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Agentes de Cobranza</h1>
        <a href="<?= BASE_URL ?>/cobranza" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Regresar
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold mb-4">Asignación de Cartera Vencida</h2>
        </div>
        
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Crédito</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Socio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Días Mora</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto Vencido</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asignar a</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acción</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($creditosVencidos)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay créditos vencidos para asignar</td>
                </tr>
                <?php else: ?>
                <?php foreach ($creditosVencidos as $credito): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($credito['numero_credito'] ?? 'N/A') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?= htmlspecialchars($credito['nombre'] . ' ' . $credito['apellido_paterno']) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full 
                            <?= $credito['dias_mora_max'] <= 30 ? 'bg-yellow-100 text-yellow-800' : '' ?>
                            <?= $credito['dias_mora_max'] > 30 && $credito['dias_mora_max'] <= 90 ? 'bg-orange-100 text-orange-800' : '' ?>
                            <?= $credito['dias_mora_max'] > 90 ? 'bg-red-100 text-red-800' : '' ?>">
                            <?= $credito['dias_mora_max'] ?> días
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">$<?= number_format($credito['monto_vencido'] ?? 0, 2) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <form method="POST" action="<?= BASE_URL ?>/cobranza/asignar/<?= $credito['id'] ?>" class="inline">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                            <select name="agente_id" class="border rounded px-2 py-1">
                                <option value="">Seleccionar</option>
                                <?php foreach ($agentes as $agente): ?>
                                <option value="<?= $agente['id'] ?>">
                                    <?= htmlspecialchars($agente['nombre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                            <button type="submit" class="bg-primary-800 hover:bg-primary-700 text-white px-3 py-1 rounded text-sm">
                                Asignar
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
