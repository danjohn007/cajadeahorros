<?php
/**
 * Vista de Gestión de Expedientes Digitales
 * Lista general de solicitudes con sus expedientes
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-primary-800">Gestión de Expedientes Digitales</h1>
            <p class="text-gray-600 mt-1">Centraliza y organiza todos los documentos y evidencias relacionados con solicitudes de crédito</p>
        </div>
        <div class="flex space-x-2">
            <button class="bg-white border border-primary-800 text-primary-800 hover:bg-primary-50 px-4 py-2 rounded flex items-center">
                <i class="fas fa-download mr-2"></i>Exportar
            </button>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <select class="border rounded px-3 py-2" onchange="this.form?.submit()">
                <option value="">Todos los estados</option>
                <option value="solicitado">Solicitado</option>
                <option value="revision">En Revisión</option>
                <option value="aprobado">Aprobado</option>
            </select>
            <input type="text" placeholder="Buscar por No. Solicitud o Cliente" class="border rounded px-3 py-2">
            <input type="date" placeholder="Fecha desde" class="border rounded px-3 py-2">
            <input type="date" placeholder="Fecha hasta" class="border rounded px-3 py-2">
        </div>
    </div>

    <!-- Tabla de solicitudes -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Solicitud</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documentos</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progreso</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($solicitudes)): ?>
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">No hay solicitudes registradas</td>
                </tr>
                <?php else: ?>
                <?php foreach ($solicitudes as $sol): ?>
                <?php
                    $total_docs = $sol['total_documentos'] ?? 0;
                    $docs_validados = $sol['documentos_validados'] ?? 0;
                    $docs_pendientes = $sol['documentos_pendientes'] ?? 0;
                    $progreso = $total_docs > 0 ? round(($docs_validados / 12) * 100) : 0;
                ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($sol['numero_credito'] ?? 'N/A') ?></div>
                        <div class="text-xs text-gray-500"><?= date('d/m/Y', strtotime($sol['fecha_solicitud'] ?? 'now')) ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">
                            <?= htmlspecialchars($sol['nombre'] . ' ' . $sol['apellido_paterno']) ?>
                        </div>
                        <div class="text-xs text-gray-500"><?= htmlspecialchars($sol['numero_socio'] ?? '') ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?= htmlspecialchars($sol['producto_nombre'] ?? 'N/A') ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">$<?= number_format($sol['monto_solicitado'] ?? 0, 2) ?> MXN</div>
                        <div class="text-xs text-gray-500"><?= date('d/m/Y', strtotime($sol['fecha_solicitud'] ?? 'now')) ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                            <?php if ($sol['estatus'] === 'revision'): ?>bg-yellow-100 text-yellow-800
                            <?php elseif ($sol['estatus'] === 'solicitado'): ?>bg-blue-100 text-blue-800
                            <?php elseif ($sol['estatus'] === 'aprobado'): ?>bg-green-100 text-green-800
                            <?php else: ?>bg-gray-100 text-gray-800<?php endif; ?>">
                            <i class="fas fa-circle mr-1" style="font-size: 6px;"></i>
                            <?= htmlspecialchars(ucfirst($sol['estatus'])) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex space-x-2 text-xs">
                            <span class="text-blue-600 font-medium"><?= $total_docs ?></span>
                            <span class="text-gray-400">Total</span>
                            <span class="text-green-600 font-medium"><?= $docs_validados ?></span>
                            <span class="text-gray-400">Valid.</span>
                            <span class="text-yellow-600 font-medium"><?= $docs_pendientes ?></span>
                            <span class="text-gray-400">Pend.</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                <div class="bg-primary-800 h-2 rounded-full" style="width: <?= $progreso ?>%"></div>
                            </div>
                            <span class="text-xs text-gray-600 font-medium"><?= $progreso ?>%</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="<?= BASE_URL ?>/solicitudes/expediente/<?= $sol['id'] ?>" 
                           class="text-primary-800 hover:text-primary-900 font-medium">
                            <i class="fas fa-folder-open mr-1"></i>Ver Expediente
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
