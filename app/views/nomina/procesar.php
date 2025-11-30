<!-- Header -->
<div class="mb-6">
    <a href="<?= BASE_URL ?>/nomina" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Volver a Nómina
    </a>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Procesar Nómina</h2>
            <p class="text-gray-600"><?= htmlspecialchars($archivo['nombre_archivo']) ?></p>
        </div>
        <?php if ($archivo['estatus'] !== 'aplicado'): ?>
        <div class="mt-4 md:mt-0 flex space-x-2">
            <form method="POST" action="<?= BASE_URL ?>/nomina/procesar/<?= $archivo['id'] ?>" class="inline">
                <input type="hidden" name="csrf_token" value="<?= $this->csrf_token() ?>">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-sync mr-2"></i> Ejecutar Matching
                </button>
            </form>
            <?php if ($stats['coincidencia'] > 0 && $_SESSION['user_role'] === 'administrador'): ?>
            <form method="POST" action="<?= BASE_URL ?>/nomina/aplicar/<?= $archivo['id'] ?>" 
                  onsubmit="return confirm('¿Está seguro de aplicar estos movimientos?')">
                <input type="hidden" name="csrf_token" value="<?= $this->csrf_token() ?>">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-check mr-2"></i> Aplicar Nómina
                </button>
            </form>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-4 text-center">
        <p class="text-2xl font-bold text-gray-800"><?= $stats['total'] ?></p>
        <p class="text-sm text-gray-500">Total</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 text-center border-l-4 border-green-500">
        <p class="text-2xl font-bold text-green-600"><?= $stats['coincidencia'] ?></p>
        <p class="text-sm text-gray-500">Coincidencias</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 text-center border-l-4 border-yellow-500">
        <p class="text-2xl font-bold text-yellow-600"><?= $stats['homonimia'] ?></p>
        <p class="text-sm text-gray-500">Homonimias</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 text-center border-l-4 border-red-500">
        <p class="text-2xl font-bold text-red-600"><?= $stats['sin_coincidencia'] ?></p>
        <p class="text-sm text-gray-500">Sin Coincidencia</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 text-center border-l-4 border-blue-500">
        <p class="text-2xl font-bold text-blue-600"><?= $stats['aplicado'] ?></p>
        <p class="text-sm text-gray-500">Aplicados</p>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre Nómina</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">RFC</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">CURP</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Socio Asignado</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estatus</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($registros as $reg): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm"><?= htmlspecialchars($reg['nombre_nomina']) ?></td>
                    <td class="px-4 py-3 text-sm font-mono"><?= htmlspecialchars($reg['rfc']) ?></td>
                    <td class="px-4 py-3 text-sm font-mono"><?= htmlspecialchars($reg['curp']) ?></td>
                    <td class="px-4 py-3 text-sm text-right font-medium">$<?= number_format($reg['monto_descuento'], 2) ?></td>
                    <td class="px-4 py-3 text-sm">
                        <?php if ($reg['socio_id']): ?>
                        <span class="text-green-600">
                            <?= htmlspecialchars($reg['numero_socio'] . ' - ' . $reg['socio_nombre'] . ' ' . $reg['socio_apellido']) ?>
                        </span>
                        <?php else: ?>
                        <span class="text-gray-400">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3">
                        <?php
                        $statusColors = [
                            'pendiente' => 'bg-gray-100 text-gray-800',
                            'coincidencia' => 'bg-green-100 text-green-800',
                            'homonimia' => 'bg-yellow-100 text-yellow-800',
                            'sin_coincidencia' => 'bg-red-100 text-red-800',
                            'aplicado' => 'bg-blue-100 text-blue-800'
                        ];
                        $color = $statusColors[$reg['estatus']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <span class="px-2 py-1 text-xs font-medium rounded-full <?= $color ?>">
                            <?= ucfirst(str_replace('_', ' ', $reg['estatus'])) ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <?php if ($reg['estatus'] === 'homonimia'): ?>
                        <a href="<?= BASE_URL ?>/nomina/resolver/<?= $reg['id'] ?>" 
                           class="text-yellow-600 hover:text-yellow-800">
                            <i class="fas fa-user-edit mr-1"></i> Resolver
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
