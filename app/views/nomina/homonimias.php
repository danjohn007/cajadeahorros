<!-- Header -->
<div class="mb-6">
    <a href="<?= BASE_URL ?>/nomina" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Volver a Nómina
    </a>
    <h2 class="text-2xl font-bold text-gray-800">Resolución de Homonimias</h2>
    <p class="text-gray-600">Registros que requieren asignación manual de socio</p>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <?php if (empty($registros)): ?>
    <div class="p-12 text-center text-gray-500">
        <i class="fas fa-check-circle text-4xl text-green-500 mb-3"></i>
        <p class="text-lg font-medium">No hay homonimias pendientes</p>
        <p class="text-sm">Todos los registros han sido resueltos</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Archivo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre en Nómina</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">RFC</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($registros as $reg): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <p class="text-sm font-medium"><?= htmlspecialchars($reg['nombre_archivo']) ?></p>
                        <p class="text-xs text-gray-500"><?= htmlspecialchars($reg['periodo']) ?></p>
                    </td>
                    <td class="px-6 py-4 text-sm"><?= htmlspecialchars($reg['nombre_nomina']) ?></td>
                    <td class="px-6 py-4 text-sm font-mono"><?= htmlspecialchars($reg['rfc']) ?></td>
                    <td class="px-6 py-4 text-sm text-right font-medium">$<?= number_format($reg['monto_descuento'], 2) ?></td>
                    <td class="px-6 py-4 text-right">
                        <a href="<?= BASE_URL ?>/nomina/resolver/<?= $reg['id'] ?>" 
                           class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition">
                            <i class="fas fa-user-edit mr-1"></i> Resolver
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
