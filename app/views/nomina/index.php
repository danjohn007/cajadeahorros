<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-500">
        <p class="text-sm text-gray-500">Total Archivos</p>
        <p class="text-2xl font-bold text-gray-800"><?= $stats['totalArchivos'] ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-yellow-500">
        <p class="text-sm text-gray-500">Pendientes de Procesar</p>
        <p class="text-2xl font-bold text-yellow-600"><?= $stats['pendientes'] ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-500">
        <p class="text-sm text-gray-500">Aplicados</p>
        <p class="text-2xl font-bold text-green-600"><?= $stats['procesados'] ?></p>
    </div>
</div>

<!-- Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Procesamiento de Nómina</h2>
        <p class="text-gray-600">Carga y procesa archivos de descuentos vía nómina</p>
    </div>
    <?php if (in_array($_SESSION['user_role'], ['administrador', 'operativo'])): ?>
    <div class="mt-4 md:mt-0 flex space-x-2">
        <a href="<?= BASE_URL ?>/nomina/homonimias" 
           class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
            <i class="fas fa-users mr-2"></i> Homonimias
        </a>
        <a href="<?= BASE_URL ?>/nomina/cargar" 
           class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
            <i class="fas fa-upload mr-2"></i> Cargar Archivo
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Archivo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periodo</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Registros</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Carga</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estatus</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($archivos)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-file-upload text-4xl mb-3 text-gray-300"></i>
                        <p>No hay archivos de nómina cargados</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($archivos as $archivo): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <i class="fas fa-file-excel text-green-500 mr-3 text-xl"></i>
                            <p class="font-medium text-gray-900"><?= htmlspecialchars($archivo['nombre_archivo']) ?></p>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm"><?= htmlspecialchars($archivo['periodo'] ?? '-') ?></p>
                        <?php if ($archivo['fecha_nomina']): ?>
                        <p class="text-xs text-gray-500"><?= date('d/m/Y', strtotime($archivo['fecha_nomina'])) ?></p>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <p class="font-medium"><?= $archivo['total_registros'] ?></p>
                        <p class="text-xs text-gray-500">
                            <?= $archivo['registros_procesados'] ?> aplicados
                        </p>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <?= date('d/m/Y H:i', strtotime($archivo['fecha_carga'])) ?>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <?= htmlspecialchars($archivo['usuario_nombre'] ?? '-') ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php
                        $statusColors = [
                            'cargado' => 'bg-blue-100 text-blue-800',
                            'procesando' => 'bg-yellow-100 text-yellow-800',
                            'pendiente_revision' => 'bg-orange-100 text-orange-800',
                            'aplicado' => 'bg-green-100 text-green-800',
                            'error' => 'bg-red-100 text-red-800'
                        ];
                        $color = $statusColors[$archivo['estatus']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <span class="px-2 py-1 text-xs font-medium rounded-full <?= $color ?>">
                            <?= ucfirst(str_replace('_', ' ', $archivo['estatus'])) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="<?= BASE_URL ?>/nomina/procesar/<?= $archivo['id'] ?>" 
                           class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-eye mr-1"></i> Ver
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
