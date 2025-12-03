<?php
/**
 * Vista de Registro de Cambios
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
        <p class="text-gray-600">Historial de modificaciones a datos del sistema</p>
    </div>
    <a href="<?= url('auditoria') ?>" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Volver a Auditoría
    </a>
</div>

<!-- Filtros -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form method="GET" action="<?= url('auditoria/cambios') ?>" class="flex flex-wrap gap-4 items-end">
        <div class="w-48">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tabla</label>
            <select name="tabla" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                <option value="">Todas</option>
                <?php foreach ($tablas ?? [] as $t): ?>
                    <option value="<?= htmlspecialchars($t['tabla']) ?>" <?= ($tablaFilter ?? '') === $t['tabla'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['tabla']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="w-40">
            <label class="block text-sm font-medium text-gray-700 mb-1">Operación</label>
            <select name="operacion" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                <option value="">Todas</option>
                <option value="INSERT" <?= ($operacion ?? '') === 'INSERT' ? 'selected' : '' ?>>Inserción</option>
                <option value="UPDATE" <?= ($operacion ?? '') === 'UPDATE' ? 'selected' : '' ?>>Actualización</option>
                <option value="DELETE" <?= ($operacion ?? '') === 'DELETE' ? 'selected' : '' ?>>Eliminación</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            <i class="fas fa-filter mr-2"></i>Filtrar
        </button>
        <a href="<?= url('auditoria/cambios') ?>" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
            <i class="fas fa-times"></i>
        </a>
    </form>
</div>

<!-- Tabla de Cambios -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">Historial de Cambios</h2>
        <span class="text-sm text-gray-600">Total: <?= number_format($total ?? 0) ?> registros</span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operación</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tabla</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registro ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detalles</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($cambios)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            No hay registros de cambios que coincidan con los filtros
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($cambios as $cambio): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= date('d/m/Y H:i:s', strtotime($cambio['fecha'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $opClases = [
                                    'INSERT' => 'bg-green-100 text-green-800',
                                    'UPDATE' => 'bg-yellow-100 text-yellow-800',
                                    'DELETE' => 'bg-red-100 text-red-800'
                                ];
                                $opIconos = [
                                    'INSERT' => 'fa-plus',
                                    'UPDATE' => 'fa-edit',
                                    'DELETE' => 'fa-trash'
                                ];
                                $operacionActual = $cambio['operacion'] ?? 'UPDATE';
                                $clase = $opClases[$operacionActual] ?? 'bg-gray-100 text-gray-800';
                                $icono = $opIconos[$operacionActual] ?? 'fa-edit';
                                ?>
                                <span class="px-2 py-1 text-xs rounded-full <?= $clase ?>">
                                    <i class="fas <?= $icono ?> mr-1"></i><?= htmlspecialchars($operacionActual) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= htmlspecialchars($cambio['tabla'] ?? '-') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                #<?= htmlspecialchars($cambio['registro_id'] ?? '-') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= htmlspecialchars($cambio['usuario_nombre'] ?? 'Sistema') ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?php if (!empty($cambio['datos_anteriores']) || !empty($cambio['datos_nuevos'])): ?>
                                    <button type="button" 
                                            onclick="mostrarDetalles(<?= htmlspecialchars(json_encode($cambio)) ?>)"
                                            class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-eye mr-1"></i>Ver detalles
                                    </button>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Paginación -->
    <?php if (($totalPages ?? 0) > 1): ?>
        <div class="px-6 py-4 bg-gray-50 border-t flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Página <?= $page ?> de <?= $totalPages ?> (<?= number_format($total) ?> registros)
            </div>
            <div class="flex space-x-2">
                <?php if ($page > 1): ?>
                    <a href="<?= url('auditoria/cambios') ?>?page=<?= $page - 1 ?>&tabla=<?= urlencode($tablaFilter ?? '') ?>&operacion=<?= urlencode($operacion ?? '') ?>" 
                       class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-100 text-sm">
                        <i class="fas fa-chevron-left"></i> Anterior
                    </a>
                <?php endif; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="<?= url('auditoria/cambios') ?>?page=<?= $page + 1 ?>&tabla=<?= urlencode($tablaFilter ?? '') ?>&operacion=<?= urlencode($operacion ?? '') ?>" 
                       class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-100 text-sm">
                        Siguiente <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal de Detalles -->
<div id="modalDetalles" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold">Detalles del Cambio</h3>
            <button onclick="cerrarModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="contenidoModal" class="p-6 overflow-y-auto max-h-[60vh]">
            <!-- Contenido dinámico -->
        </div>
    </div>
</div>

<script>
function mostrarDetalles(cambio) {
    const modal = document.getElementById('modalDetalles');
    const contenido = document.getElementById('contenidoModal');
    
    let html = '';
    
    if (cambio.datos_anteriores) {
        html += '<div class="mb-4"><h4 class="font-medium text-red-600 mb-2">Datos Anteriores:</h4>';
        html += '<pre class="bg-red-50 p-3 rounded text-sm overflow-x-auto">' + 
                escapeHtml(JSON.stringify(JSON.parse(cambio.datos_anteriores || '{}'), null, 2)) + '</pre></div>';
    }
    
    if (cambio.datos_nuevos) {
        html += '<div><h4 class="font-medium text-green-600 mb-2">Datos Nuevos:</h4>';
        html += '<pre class="bg-green-50 p-3 rounded text-sm overflow-x-auto">' + 
                escapeHtml(JSON.stringify(JSON.parse(cambio.datos_nuevos || '{}'), null, 2)) + '</pre></div>';
    }
    
    if (!html) {
        html = '<p class="text-gray-500">No hay detalles disponibles</p>';
    }
    
    contenido.innerHTML = html;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function cerrarModal() {
    const modal = document.getElementById('modalDetalles');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Cerrar modal al hacer clic fuera
document.getElementById('modalDetalles').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModal();
    }
});
</script>
