<?php
/**
 * Vista de Asignación de Promotores
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Asignación de Promotores</h1>
        <a href="<?= BASE_URL ?>/solicitudes" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Regresar
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Solicitud</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Solicitante</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Promotor Actual</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asignar a</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acción</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($solicitudes)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay solicitudes pendientes de asignación</td>
                </tr>
                <?php else: ?>
                <?php foreach ($solicitudes as $sol): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($sol['numero_credito'] ?? 'N/A') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?= htmlspecialchars($sol['nombre'] . ' ' . $sol['apellido_paterno']) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">$<?= number_format($sol['monto_solicitado'] ?? 0, 2) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?= htmlspecialchars($sol['promotor_nombre'] ?? 'Sin asignar') ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <form method="POST" action="<?= BASE_URL ?>/solicitudes/asignar/<?= $sol['id'] ?>" class="inline">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                            <select name="promotor_id" class="border rounded px-2 py-1">
                                <option value="">Seleccionar</option>
                                <?php foreach ($promotores as $promotor): ?>
                                <option value="<?= $promotor['id'] ?>" <?= ($sol['promotor_id'] ?? '') == $promotor['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($promotor['nombre'] . ' ' . $promotor['apellido_paterno']) ?>
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
