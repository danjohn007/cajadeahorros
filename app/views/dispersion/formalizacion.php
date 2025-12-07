<?php
/**
 * Vista de Proceso de Formalización
 * Gestión del proceso de formalización y validación de requisitos
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Proceso de Formalización</h1>
        <button onclick="window.history.back()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Regresar
        </button>
    </div>

    <p class="text-gray-600 mb-6">Gestión del proceso de formalización y validación de requisitos</p>

    <!-- Progreso del Proceso -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <div class="flex justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Progreso de Formalización</span>
                <span class="text-sm font-medium text-primary-800">
                    <?php
                    $completados = 0;
                    $total = count($checklist ?? []);
                    foreach ($checklist ?? [] as $item) {
                        if ($item['completado']) $completados++;
                    }
                    $progreso = $total > 0 ? ($completados / $total) * 100 : 0;
                    echo number_format($progreso, 0) . '%';
                    ?>
                </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="bg-primary-800 h-2.5 rounded-full" style="width: <?= $progreso ?>%"></div>
            </div>
        </div>
    </div>

    <!-- Checklist de Formalización -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold flex items-center">
                <i class="fas fa-tasks text-primary-800 mr-2"></i>Checklist de Requisitos
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                <?php foreach ($checklist ?? [] as $item): ?>
                <div class="flex items-center justify-between p-3 <?= $item['completado'] ? 'bg-green-50' : 'bg-gray-50' ?> rounded">
                    <div class="flex items-center flex-1">
                        <i class="fas <?= $item['completado'] ? 'fa-check-circle text-green-600' : 'fa-circle text-gray-400' ?> text-xl mr-3"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-800"><?= htmlspecialchars($item['nombre'] ?? 'Requisito') ?></p>
                            <p class="text-xs text-gray-500"><?= htmlspecialchars($item['descripcion'] ?? '') ?></p>
                            <?php if ($item['completado'] && $item['fecha_completado']): ?>
                            <p class="text-xs text-green-600 mt-1">
                                Completado el <?= date('d/m/Y', strtotime($item['fecha_completado'])) ?>
                                <?= $item['validado_por_nombre'] ? ' por ' . htmlspecialchars($item['validado_por_nombre']) : '' ?>
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="ml-4">
                        <?php if ($item['completado']): ?>
                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded font-semibold">Completo</span>
                        <?php else: ?>
                        <button class="px-3 py-1 text-xs bg-primary-800 hover:bg-primary-700 text-white rounded">
                            Marcar Completo
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
