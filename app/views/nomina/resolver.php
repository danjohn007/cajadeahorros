<!-- Header -->
<div class="mb-6">
    <a href="<?= BASE_URL ?>/nomina/homonimias" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Volver a Homonimias
    </a>
    <h2 class="text-2xl font-bold text-gray-800">Resolver Homonimia</h2>
</div>

<!-- Errors -->
<?php if (!empty($errors)): ?>
<div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg">
    <ul class="list-disc list-inside">
        <?php foreach ($errors as $error): ?>
        <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Registro de Nómina -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">
                <i class="fas fa-file-invoice text-orange-500 mr-2"></i> Datos de Nómina
            </h3>
            
            <div class="space-y-3">
                <div>
                    <label class="text-sm text-gray-500">Nombre</label>
                    <p class="font-medium"><?= htmlspecialchars($registro['nombre_nomina']) ?></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">RFC</label>
                    <p class="font-mono"><?= htmlspecialchars($registro['rfc'] ?? '-') ?></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">CURP</label>
                    <p class="font-mono text-xs"><?= htmlspecialchars($registro['curp'] ?? '-') ?></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">No. Empleado</label>
                    <p class="font-medium"><?= htmlspecialchars($registro['numero_empleado'] ?? '-') ?></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Monto</label>
                    <p class="text-xl font-bold text-green-600">$<?= number_format($registro['monto_descuento'], 2) ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Selección de Socio -->
    <div class="lg:col-span-2">
        <form method="POST" action="<?= BASE_URL ?>/nomina/resolver/<?= $registro['id'] ?>" class="bg-white rounded-xl shadow-sm">
            <input type="hidden" name="csrf_token" value="<?= $this->csrf_token() ?>">
            
            <div class="p-6 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800 mb-4">
                    <i class="fas fa-users text-blue-500 mr-2"></i> Seleccionar Socio Correcto
                </h3>
                
                <?php if (empty($posiblesCoincidencias)): ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-search text-4xl text-gray-300 mb-3"></i>
                    <p>No se encontraron posibles coincidencias</p>
                </div>
                <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($posiblesCoincidencias as $socio): ?>
                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-blue-50 transition">
                        <input type="radio" name="socio_id" value="<?= $socio['id'] ?>" required
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                        <div class="ml-4 flex-1">
                            <p class="font-medium text-gray-800">
                                <?= htmlspecialchars($socio['nombre'] . ' ' . $socio['apellido_paterno'] . ' ' . ($socio['apellido_materno'] ?? '')) ?>
                            </p>
                            <p class="text-sm text-gray-500">
                                <?= htmlspecialchars($socio['numero_socio']) ?>
                                <?php if ($socio['rfc']): ?> | RFC: <?= htmlspecialchars($socio['rfc']) ?><?php endif; ?>
                            </p>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="p-6 bg-gray-50 flex justify-end space-x-4">
                <a href="<?= BASE_URL ?>/nomina/homonimias" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-check mr-2"></i> Confirmar Selección
                </button>
            </div>
        </form>
    </div>
</div>
