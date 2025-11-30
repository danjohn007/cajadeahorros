<!-- Header -->
<div class="mb-6">
    <a href="<?= BASE_URL ?>/socios/ver/<?= $socio['id'] ?>" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Volver al Socio
    </a>
    <h2 class="text-2xl font-bold text-gray-800">Historial de Cambios</h2>
    <p class="text-gray-600">
        <?= htmlspecialchars($socio['nombre'] . ' ' . $socio['apellido_paterno'] . ' ' . ($socio['apellido_materno'] ?? '')) ?>
        (<?= htmlspecialchars($socio['numero_socio']) ?>)
    </p>
</div>

<!-- Timeline -->
<div class="bg-white rounded-xl shadow-sm p-6">
    <?php if (empty($historial)): ?>
    <div class="text-center py-12 text-gray-500">
        <i class="fas fa-history text-4xl mb-3 text-gray-300"></i>
        <p>No hay cambios registrados</p>
    </div>
    <?php else: ?>
    <div class="relative">
        <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
        
        <div class="space-y-6">
            <?php foreach ($historial as $item): ?>
            <div class="relative pl-10">
                <div class="absolute left-2 w-4 h-4 bg-blue-500 rounded-full border-2 border-white"></div>
                
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-medium text-gray-800">
                            Campo: <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $item['campo_modificado']))) ?>
                        </span>
                        <span class="text-sm text-gray-500">
                            <?= date('d/m/Y H:i', strtotime($item['fecha'])) ?>
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Valor anterior:</span>
                            <p class="font-medium text-red-600">
                                <?= htmlspecialchars($item['valor_anterior'] ?? '(vacío)') ?>
                            </p>
                        </div>
                        <div>
                            <span class="text-gray-500">Valor nuevo:</span>
                            <p class="font-medium text-green-600">
                                <?= htmlspecialchars($item['valor_nuevo'] ?? '(vacío)') ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="mt-2 text-sm text-gray-500">
                        <i class="fas fa-user mr-1"></i>
                        <?= htmlspecialchars($item['usuario_nombre'] ?? 'Sistema') ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
