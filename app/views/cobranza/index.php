<?php
/**
 * Vista principal de Cobranza
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestión de Cobranza</h1>
        <div class="flex gap-2">
            <a href="<?= BASE_URL ?>/cobranza/convenios" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                <i class="fas fa-handshake mr-2"></i>Convenios
            </a>
            <a href="<?= BASE_URL ?>/cobranza/reportes" class="bg-primary-800 hover:bg-primary-700 text-white px-4 py-2 rounded">
                <i class="fas fa-chart-bar mr-2"></i>Reportes
            </a>
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Créditos en Mora</p>
                    <p class="text-2xl font-bold text-gray-800"><?= count($carteraVencida ?? []) ?></p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Monto Vencido</p>
                    <p class="text-2xl font-bold text-gray-800">
                        $<?= number_format(array_sum(array_column($carteraVencida ?? [], 'monto_vencido')), 2) ?>
                    </p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-dollar-sign text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Mora Promedio</p>
                    <p class="text-2xl font-bold text-gray-800">
                        <?php 
                        $dias_promedio = count($carteraVencida ?? []) > 0 
                            ? round(array_sum(array_column($carteraVencida, 'dias_maximo_mora')) / count($carteraVencida)) 
                            : 0;
                        echo $dias_promedio . ' días';
                        ?>
                    </p>
                </div>
                <div class="bg-orange-100 rounded-full p-3">
                    <i class="fas fa-clock text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Acciones del Día</p>
                    <p class="text-2xl font-bold text-gray-800">0</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-tasks text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartera vencida -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-700">Cartera Vencida - Alta Prioridad</h2>
            <a href="<?= BASE_URL ?>/cobranza/agentes" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-user-tie mr-1"></i>Asignar Agentes
            </a>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Crédito</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Socio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contacto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pagos Vencidos</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto Vencido</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Días Mora</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($carteraVencida)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No hay cartera vencida</td>
                </tr>
                <?php else: ?>
                <?php foreach ($carteraVencida as $item): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap font-medium"><?= htmlspecialchars($item['numero_credito']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?= htmlspecialchars($item['nombre'] . ' ' . $item['apellido_paterno']) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm"><?= htmlspecialchars($item['telefono'] ?? 'N/A') ?></div>
                        <div class="text-xs text-gray-500"><?= htmlspecialchars($item['celular'] ?? '') ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-sm">
                            <?= $item['pagos_vencidos'] ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-red-600 font-semibold">
                        $<?= number_format($item['monto_vencido'], 2) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 
                            <?= $item['dias_maximo_mora'] > 90 ? 'bg-red-100 text-red-800' : '' ?>
                            <?= $item['dias_maximo_mora'] > 60 && $item['dias_maximo_mora'] <= 90 ? 'bg-orange-100 text-orange-800' : '' ?>
                            <?= $item['dias_maximo_mora'] <= 60 ? 'bg-yellow-100 text-yellow-800' : '' ?>
                            rounded-full text-sm">
                            <?= $item['dias_maximo_mora'] ?> días
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="<?= BASE_URL ?>/cobranza/convenios/<?= $item['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3" title="Convenio">
                            <i class="fas fa-handshake"></i>
                        </a>
                        <a href="<?= BASE_URL ?>/creditos/ver/<?= $item['id'] ?>" class="text-green-600 hover:text-green-900" title="Ver Crédito">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
