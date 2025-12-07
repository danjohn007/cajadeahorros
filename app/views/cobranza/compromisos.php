<?php
/**
 * Vista de Compromisos de Pago
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Compromisos de Pago</h1>
        <a href="<?= BASE_URL ?>/cobranza" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Regresar
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold">Seguimiento de Compromisos Activos</h2>
        </div>
        
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Crédito</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Socio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teléfono</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto Comprometido</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Compromiso</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($compromisos)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No hay compromisos registrados</td>
                </tr>
                <?php else: ?>
                <?php foreach ($compromisos as $compromiso): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-medium text-gray-900">
                            <?= htmlspecialchars($compromiso['numero_credito'] ?? 'N/A') ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            <?= htmlspecialchars($compromiso['nombre'] . ' ' . $compromiso['apellido_paterno']) ?>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-600">
                            <?= htmlspecialchars($compromiso['telefono'] ?? 'N/A') ?>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-semibold text-green-600">
                            $<?= number_format($compromiso['monto_total'] ?? 0, 2) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-600">
                            <?= date('d/m/Y', strtotime($compromiso['fecha_primer_pago'] ?? 'now')) ?>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php
                        $fechaCompromiso = strtotime($compromiso['fecha_primer_pago'] ?? 'now');
                        $hoy = strtotime('today');
                        $estatus = 'pendiente';
                        $colorClass = 'bg-yellow-100 text-yellow-800';
                        
                        if ($fechaCompromiso < $hoy) {
                            $estatus = 'vencido';
                            $colorClass = 'bg-red-100 text-red-800';
                        } elseif ($fechaCompromiso == $hoy) {
                            $estatus = 'hoy';
                            $colorClass = 'bg-blue-100 text-blue-800';
                        }
                        ?>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $colorClass ?>">
                            <?= ucfirst($estatus) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="<?= BASE_URL ?>/cobranza/convenios/<?= $compromiso['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                        <a href="<?= BASE_URL ?>/cobranza/contactar/<?= $compromiso['credito_id'] ?>" class="text-green-600 hover:text-green-900">
                            <i class="fas fa-phone"></i> Contactar
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Resumen de Compromisos -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                    <i class="fas fa-handshake text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Compromisos</p>
                    <p class="text-2xl font-semibold text-gray-900"><?= count($compromisos ?? []) ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Pendientes</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        <?php
                        $pendientes = 0;
                        foreach ($compromisos ?? [] as $c) {
                            if (strtotime($c['fecha_primer_pago']) > strtotime('today')) $pendientes++;
                        }
                        echo $pendientes;
                        ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Para Hoy</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        <?php
                        $hoy = 0;
                        foreach ($compromisos ?? [] as $c) {
                            if (date('Y-m-d', strtotime($c['fecha_primer_pago'])) == date('Y-m-d')) $hoy++;
                        }
                        echo $hoy;
                        ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Vencidos</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        <?php
                        $vencidos = 0;
                        foreach ($compromisos ?? [] as $c) {
                            if (strtotime($c['fecha_primer_pago']) < strtotime('today')) $vencidos++;
                        }
                        echo $vencidos;
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
