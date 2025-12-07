<?php
/**
 * Vista principal de Dispersión de Fondos
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Dispersión de Fondos</h1>
        <a href="<?= BASE_URL ?>/dispersion/coordinacion" class="bg-primary-800 hover:bg-primary-700 text-white px-4 py-2 rounded">
            <i class="fas fa-calendar-alt mr-2"></i>Coordinación
        </a>
    </div>

    <!-- Créditos pendientes de formalización -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h2 class="text-lg font-semibold text-gray-700">Créditos Aprobados Pendientes de Dispersión</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Socio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Aprobación</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($creditos)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No hay créditos pendientes de dispersión</td>
                </tr>
                <?php else: ?>
                <?php foreach ($creditos as $credito): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap font-medium"><?= htmlspecialchars($credito['numero_credito']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?= htmlspecialchars($credito['nombre'] . ' ' . $credito['apellido_paterno']) ?>
                        <div class="text-xs text-gray-500"><?= htmlspecialchars($credito['numero_socio']) ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($credito['producto_nombre'] ?? 'N/A') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">$<?= number_format($credito['monto_autorizado'] ?? 0, 2) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= date('d/m/Y', strtotime($credito['fecha_aprobacion'] ?? 'now')) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full 
                            <?= $credito['estatus'] === 'aprobado' ? 'bg-blue-100 text-blue-800' : '' ?>
                            <?= $credito['estatus'] === 'formalizacion' ? 'bg-yellow-100 text-yellow-800' : '' ?>">
                            <?= htmlspecialchars(ucfirst($credito['estatus'])) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="<?= BASE_URL ?>/dispersion/registrar/<?= $credito['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3" title="Registrar">
                            <i class="fas fa-check-circle"></i>
                        </a>
                        <a href="<?= BASE_URL ?>/dispersion/formalizacion/<?= $credito['id'] ?>" class="text-green-600 hover:text-green-900 mr-3" title="Formalización">
                            <i class="fas fa-file-signature"></i>
                        </a>
                        <a href="<?= BASE_URL ?>/dispersion/contratos/<?= $credito['id'] ?>" class="text-purple-600 hover:text-purple-900 mr-3" title="Contratos">
                            <i class="fas fa-file-contract"></i>
                        </a>
                        <a href="<?= BASE_URL ?>/dispersion/garantias/<?= $credito['id'] ?>" class="text-orange-600 hover:text-orange-900" title="Garantías">
                            <i class="fas fa-shield-alt"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
