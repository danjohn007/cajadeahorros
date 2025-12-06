<?php
/**
 * Vista de Customer Journey
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <a href="<?= BASE_URL ?>/crm" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
            <i class="fas fa-arrow-left mr-1"></i> Volver a CRM
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Customer Journey</h2>
        <p class="text-gray-600">Gestión de prospectos y solicitudes de vinculación</p>
    </div>
</div>

<?php if (!empty($errors)): ?>
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
    <ul class="list-disc list-inside">
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<?php if (!empty($success)): ?>
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
    <?= htmlspecialchars($success) ?>
</div>
<?php endif; ?>

<!-- Estadísticas -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-2 bg-yellow-100 rounded-full mr-3">
                <i class="fas fa-clock text-yellow-600"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Pendientes</p>
                <p class="text-2xl font-bold text-yellow-600"><?= number_format($stats['pendientes']) ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-2 bg-green-100 rounded-full mr-3">
                <i class="fas fa-check text-green-600"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Aprobadas (mes)</p>
                <p class="text-2xl font-bold text-green-600"><?= number_format($stats['aprobadas_mes']) ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-2 bg-red-100 rounded-full mr-3">
                <i class="fas fa-times text-red-600"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Rechazadas (mes)</p>
                <p class="text-2xl font-bold text-red-600"><?= number_format($stats['rechazadas_mes']) ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-2 bg-blue-100 rounded-full mr-3">
                <i class="fas fa-user-slash text-blue-600"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Sin vincular</p>
                <p class="text-2xl font-bold text-blue-600"><?= number_format($stats['usuarios_sin_vincular']) ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-2 bg-purple-100 rounded-full mr-3">
                <i class="fas fa-user-edit text-purple-600"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Actualizaciones</p>
                <p class="text-2xl font-bold text-purple-600"><?= number_format($stats['actualizaciones_pendientes']) ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Solicitudes de Actualización Pendientes -->
<div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
    <div class="px-6 py-4 border-b bg-purple-50">
        <h3 class="text-lg font-semibold text-purple-800">
            <i class="fas fa-user-edit mr-2"></i>Solicitudes de Actualización Pendientes
        </h3>
    </div>
    
    <?php if (empty($solicitudesActualizacion)): ?>
    <div class="p-6 text-center text-gray-500">
        <i class="fas fa-inbox text-4xl mb-4"></i>
        <p>No hay solicitudes de actualización pendientes</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Fecha</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Socio</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Usuario</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Cambios Solicitados</th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($solicitudesActualizacion as $solicitud): ?>
                <?php 
                    $cambios = json_decode($solicitud['cambios_solicitados'], true);
                    $camposModificados = is_array($cambios) ? array_keys($cambios) : [];
                ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-600">
                        <?= date('d/m/Y H:i', strtotime($solicitud['created_at'])) ?>
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-800">
                        <?php if ($solicitud['numero_socio']): ?>
                            <?= htmlspecialchars($solicitud['socio_nombre'] . ' ' . $solicitud['apellido_paterno']) ?>
                            <br><span class="text-xs text-gray-500"><?= htmlspecialchars($solicitud['numero_socio']) ?></span>
                        <?php else: ?>
                            <span class="text-gray-400">Sin vincular</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <?= htmlspecialchars($solicitud['usuario_nombre'] ?? $solicitud['usuario_email']) ?>
                        <br><span class="text-xs text-gray-500"><?= htmlspecialchars($solicitud['usuario_email']) ?></span>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <button data-request-id="<?= (int)$solicitud['id'] ?>" class="toggle-cambios text-blue-600 hover:text-blue-800">
                            <i class="fas fa-eye mr-1"></i><?= count($camposModificados) ?> campo(s) modificado(s)
                        </button>
                        <div id="request-details-<?= (int)$solicitud['id'] ?>" class="hidden mt-2 text-xs bg-gray-50 p-2 rounded">
                            <?php foreach ($cambios as $campo => $valor): ?>
                                <div class="mb-1">
                                    <strong><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $campo))) ?>:</strong>
                                    <?= htmlspecialchars(is_array($valor) ? json_encode($valor) : $valor) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button onclick="revisarActualizacion(<?= (int)$solicitud['id'] ?>, <?= (int)$solicitud['socio_id'] ?>)" 
                                class="px-3 py-1 bg-purple-600 text-white rounded text-sm hover:bg-purple-700">
                            <i class="fas fa-edit mr-1"></i>Revisar
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- Solicitudes de Vinculación Pendientes -->
<div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
    <div class="px-6 py-4 border-b bg-yellow-50">
        <h3 class="text-lg font-semibold text-yellow-800">
            <i class="fas fa-bell mr-2"></i>Solicitudes de Vinculación Pendientes
        </h3>
    </div>
    
    <?php if (empty($solicitudesPendientes)): ?>
    <div class="p-6 text-center text-gray-500">
        <i class="fas fa-inbox text-4xl mb-4"></i>
        <p>No hay solicitudes pendientes</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Fecha</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Nombre</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Email</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Contacto</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Mensaje</th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($solicitudesPendientes as $solicitud): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-600">
                        <?= date('d/m/Y H:i', strtotime($solicitud['created_at'])) ?>
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-800">
                        <?= htmlspecialchars($solicitud['nombre']) ?>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        <?= htmlspecialchars($solicitud['usuario_email']) ?>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <?php if ($solicitud['celular']): ?>
                            <span class="block"><i class="fab fa-whatsapp text-green-600 mr-1"></i><?= htmlspecialchars($solicitud['celular']) ?></span>
                        <?php endif; ?>
                        <?php if ($solicitud['telefono']): ?>
                            <span class="block text-gray-500"><i class="fas fa-phone mr-1"></i><?= htmlspecialchars($solicitud['telefono']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate" title="<?= htmlspecialchars($solicitud['mensaje'] ?? '') ?>">
                        <?= htmlspecialchars($solicitud['mensaje'] ?? '-') ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button onclick="mostrarModalAprobacion(<?= $solicitud['id'] ?>, '<?= htmlspecialchars(addslashes($solicitud['nombre'])) ?>', '<?= htmlspecialchars($solicitud['usuario_email']) ?>', '<?= htmlspecialchars($solicitud['celular']) ?>')" 
                                class="px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700 mr-1">
                            <i class="fas fa-check mr-1"></i>Aprobar
                        </button>
                        <button onclick="mostrarModalRechazo(<?= $solicitud['id'] ?>)" 
                                class="px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">
                            <i class="fas fa-times mr-1"></i>Rechazar
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- Historial de Solicitudes -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-history mr-2"></i>Historial de Solicitudes
        </h3>
    </div>
    
    <?php if (empty($historialSolicitudes)): ?>
    <div class="p-6 text-center text-gray-500">
        <i class="fas fa-inbox text-4xl mb-4"></i>
        <p>No hay historial de solicitudes</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Fecha</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Nombre</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Email</th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Estado</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Socio Vinculado</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Revisado por</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($historialSolicitudes as $solicitud): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-600">
                        <?= date('d/m/Y H:i', strtotime($solicitud['fecha_revision'])) ?>
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-800">
                        <?= htmlspecialchars($solicitud['nombre']) ?>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        <?= htmlspecialchars($solicitud['usuario_email']) ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <?php if ($solicitud['estatus'] === 'aprobada'): ?>
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Aprobada</span>
                        <?php else: ?>
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Rechazada</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        <?= $solicitud['numero_socio'] ? htmlspecialchars($solicitud['numero_socio']) : '-' ?>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        <?= htmlspecialchars($solicitud['revisado_por_nombre'] ?? '-') ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- Modal de Aprobación -->
<div id="modalAprobacion" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-check-circle mr-2 text-green-600"></i>Aprobar Solicitud de Vinculación
            </h3>
        </div>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="aprobar">
            <input type="hidden" name="solicitud_id" id="aprobar_solicitud_id">
            
            <div class="p-6 space-y-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-sm text-blue-800"><strong>Usuario:</strong> <span id="aprobar_nombre"></span></p>
                    <p class="text-sm text-blue-800"><strong>Email:</strong> <span id="aprobar_email"></span></p>
                    <p class="text-sm text-blue-800"><strong>Celular:</strong> <span id="aprobar_celular"></span></p>
                </div>
                
                <div>
                    <label for="socio_search" class="block text-sm font-medium text-gray-700 mb-1">
                        Buscar Socio para Vincular *
                    </label>
                    <div class="relative">
                        <input type="hidden" name="socio_id" id="vincular_socio_id" required>
                        <input type="text" id="socio_search_vincular" 
                               placeholder="Buscar por nombre, número de socio, RFC..."
                               autocomplete="off"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
                        <div id="socio_results_vincular" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-48 overflow-y-auto hidden"></div>
                        <div id="socio_selected_vincular" class="mt-2 p-3 bg-green-50 rounded-lg hidden">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-medium text-green-800" id="vincular_socio_nombre"></p>
                                    <p class="text-sm text-green-600" id="vincular_socio_info"></p>
                                </div>
                                <button type="button" onclick="clearSocioVincular()" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                <button type="button" onclick="cerrarModalAprobacion()" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-100">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    <i class="fas fa-check mr-2"></i>Aprobar y Vincular
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Rechazo -->
<div id="modalRechazo" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-times-circle mr-2 text-red-600"></i>Rechazar Solicitud
            </h3>
        </div>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="rechazar">
            <input type="hidden" name="solicitud_id" id="rechazar_solicitud_id">
            
            <div class="p-6 space-y-4">
                <div>
                    <label for="notas_revision" class="block text-sm font-medium text-gray-700 mb-1">
                        Motivo del rechazo (opcional)
                    </label>
                    <textarea name="notas_revision" id="notas_revision" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500"
                              placeholder="Indique el motivo del rechazo..."></textarea>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                <button type="button" onclick="cerrarModalRechazo()" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-100">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    <i class="fas fa-times mr-2"></i>Rechazar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Event delegation for toggle cambios buttons
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(e) {
        if (e.target.closest('.toggle-cambios')) {
            const button = e.target.closest('.toggle-cambios');
            const id = button.getAttribute('data-request-id');
            // Validate that ID is a positive integer
            if (id && /^\d+$/.test(id) && parseInt(id, 10) > 0) {
                const element = document.getElementById('request-details-' + id);
                if (element) {
                    element.classList.toggle('hidden');
                }
            }
        }
    });
});

function mostrarModalAprobacion(id, nombre, email, celular) {
    document.getElementById('aprobar_solicitud_id').value = id;
    document.getElementById('aprobar_nombre').textContent = nombre;
    document.getElementById('aprobar_email').textContent = email;
    document.getElementById('aprobar_celular').textContent = celular;
    document.getElementById('vincular_socio_id').value = '';
    document.getElementById('socio_selected_vincular').classList.add('hidden');
    document.getElementById('socio_search_vincular').value = '';
    document.getElementById('modalAprobacion').classList.remove('hidden');
}

function cerrarModalAprobacion() {
    document.getElementById('modalAprobacion').classList.add('hidden');
}

function mostrarModalRechazo(id) {
    document.getElementById('rechazar_solicitud_id').value = id;
    document.getElementById('notas_revision').value = '';
    document.getElementById('modalRechazo').classList.remove('hidden');
}

function cerrarModalRechazo() {
    document.getElementById('modalRechazo').classList.add('hidden');
}

// Cerrar modales al hacer clic fuera
document.getElementById('modalAprobacion').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalAprobacion();
});
document.getElementById('modalRechazo').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalRechazo();
});

// Búsqueda de socio para vincular
const socioSearchVincular = document.getElementById('socio_search_vincular');
const socioResultsVincular = document.getElementById('socio_results_vincular');

if (socioSearchVincular) {
    let searchTimeout;
    
    socioSearchVincular.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            socioResultsVincular.classList.add('hidden');
            return;
        }
        
        searchTimeout = setTimeout(() => {
            fetch('<?= BASE_URL ?>/socios/buscar?q=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    if (data.results && data.results.length > 0) {
                        socioResultsVincular.innerHTML = data.results.map(socio => `
                            <div class="px-4 py-3 hover:bg-green-50 cursor-pointer border-b border-gray-100" 
                                 onclick="selectSocioVincular(${socio.id}, '${escapeHtml(socio.nombre)} ${escapeHtml(socio.apellido_paterno)}', '${escapeHtml(socio.numero_socio)}')">
                                <div class="font-medium">${escapeHtml(socio.nombre)} ${escapeHtml(socio.apellido_paterno)} ${escapeHtml(socio.apellido_materno || '')}</div>
                                <div class="text-sm text-gray-500">${escapeHtml(socio.numero_socio)} | RFC: ${escapeHtml(socio.rfc || 'N/A')}</div>
                            </div>
                        `).join('');
                        socioResultsVincular.classList.remove('hidden');
                    } else {
                        socioResultsVincular.innerHTML = '<div class="px-4 py-3 text-gray-500">No se encontraron socios</div>';
                        socioResultsVincular.classList.remove('hidden');
                    }
                })
                .catch(error => console.error('Error:', error));
        }, 300);
    });
    
    document.addEventListener('click', function(e) {
        if (!socioSearchVincular.contains(e.target) && !socioResultsVincular.contains(e.target)) {
            socioResultsVincular.classList.add('hidden');
        }
    });
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function selectSocioVincular(id, nombre, numeroSocio) {
    document.getElementById('vincular_socio_id').value = id;
    document.getElementById('vincular_socio_nombre').textContent = nombre;
    document.getElementById('vincular_socio_info').textContent = numeroSocio;
    document.getElementById('socio_selected_vincular').classList.remove('hidden');
    document.getElementById('socio_search_vincular').value = '';
    socioResultsVincular.classList.add('hidden');
}

function clearSocioVincular() {
    document.getElementById('vincular_socio_id').value = '';
    document.getElementById('socio_selected_vincular').classList.add('hidden');
    document.getElementById('socio_search_vincular').focus();
}

// Función para revisar solicitud de actualización
function revisarActualizacion(solicitudId, socioId) {
    // Validar y sanear los IDs
    solicitudId = parseInt(solicitudId, 10);
    socioId = parseInt(socioId, 10);
    
    if (isNaN(solicitudId) || solicitudId <= 0) {
        console.error('ID de solicitud inválido');
        return;
    }
    
    if (socioId && socioId > 0) {
        // Si tiene socio vinculado, ir a editar el socio
        const url = '<?= BASE_URL ?>/socios/editar/' + encodeURIComponent(socioId) + '?from_actualizacion=' + encodeURIComponent(solicitudId);
        window.location.href = url;
    } else {
        // Si no tiene socio, mostrar mensaje con mejor UX
        const detailsDiv = document.getElementById('request-details-' + solicitudId);
        if (detailsDiv) {
            // Crear notificación temporal
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-yellow-100 border border-yellow-400 text-yellow-800 px-6 py-4 rounded-lg shadow-lg z-50 max-w-md';
            notification.innerHTML = `
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle mr-3 mt-1"></i>
                    <div>
                        <p class="font-medium">Usuario sin vincular</p>
                        <p class="text-sm mt-1">Este usuario no tiene un socio vinculado. Por favor, vincúlelo primero o apruebe la solicitud de vinculación.</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-yellow-600 hover:text-yellow-800">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            document.body.appendChild(notification);
            // Auto-remove después de 5 segundos con verificación
            setTimeout(() => {
                if (notification && notification.parentElement) {
                    notification.remove();
                }
            }, 5000);
        }
    }
}
</script>
