<?php
/**
 * Reporte de Nómina
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6">
    <a href="<?= url('reportes') ?>" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Volver a Reportes
    </a>
</div>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
        <p class="text-gray-600">Historial de procesamiento de archivos de nómina</p>
    </div>
    <a href="<?= url('nomina') ?>" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
        <i class="fas fa-file-import mr-2"></i>Ir a Nómina
    </a>
</div>

<!-- Resumen -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-gray-500 text-sm font-medium">Archivos Procesados</h3>
        <p class="text-3xl font-bold text-gray-800"><?= number_format($resumen['archivos_procesados'] ?? 0) ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-gray-500 text-sm font-medium">Monto Total Aplicado</h3>
        <p class="text-3xl font-bold text-green-600">$<?= number_format($resumen['monto_total_aplicado'] ?? 0, 2) ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-gray-500 text-sm font-medium">Socios Beneficiados</h3>
        <p class="text-3xl font-bold text-blue-600"><?= number_format($resumen['socios_beneficiados'] ?? 0) ?></p>
    </div>
</div>

<!-- Historial de Archivos -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">Últimos Archivos Procesados</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Carga</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Archivo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Período</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Registros</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Monto Aplicado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estatus</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($historial)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">No hay archivos de nómina procesados</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($historial as $archivo): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= date('d/m/Y H:i', strtotime($archivo['fecha_carga'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($archivo['nombre_archivo']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= htmlspecialchars($archivo['usuario_nombre'] ?? 'Sistema') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= htmlspecialchars($archivo['periodo'] ?? '-') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                <?= number_format($archivo['total_registros'] ?? 0) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 text-right font-medium">
                                $<?= number_format($archivo['monto_aplicado'] ?? 0, 2) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    <?php
                                    switch($archivo['estatus']) {
                                        case 'aplicado': echo 'bg-green-100 text-green-800'; break;
                                        case 'procesando': echo 'bg-yellow-100 text-yellow-800'; break;
                                        case 'pendiente': echo 'bg-blue-100 text-blue-800'; break;
                                        default: echo 'bg-gray-100 text-gray-800';
                                    }
                                    ?>">
                                    <?= ucfirst(htmlspecialchars($archivo['estatus'])) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
