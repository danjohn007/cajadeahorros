<?php
/**
 * Vista Principal de Reportes CNBV
 * Módulo de reportes regulatorios
 */
?>

<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Reportes Regulatorios CNBV</h2>
        <p class="text-gray-600">Comisión Nacional Bancaria y de Valores</p>
    </div>

    <!-- Tarjetas de acción rápida -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <div class="bg-blue-100 p-3 rounded-full mr-4">
                    <i class="fas fa-file-invoice text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Situación Financiera</h3>
                    <p class="text-sm text-gray-600">Reporte mensual de estados</p>
                </div>
            </div>
            <button onclick="modalGenerar('situacion_financiera')" 
                    class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>Generar Reporte
            </button>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <div class="bg-green-100 p-3 rounded-full mr-4">
                    <i class="fas fa-chart-pie text-green-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Cartera Crediticia</h3>
                    <p class="text-sm text-gray-600">Detalle de créditos activos</p>
                </div>
            </div>
            <button onclick="modalGenerar('cartera')" 
                    class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                <i class="fas fa-plus mr-2"></i>Generar Reporte
            </button>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <div class="bg-purple-100 p-3 rounded-full mr-4">
                    <i class="fas fa-info-circle text-purple-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Información</h3>
                    <p class="text-sm text-gray-600">Guías de la CNBV</p>
                </div>
            </div>
            <a href="https://www.gob.mx/cnbv/documentos/guias-de-apoyo-reportes-regulatorios-de-situacion-financiera" 
               target="_blank"
               class="w-full block text-center bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                <i class="fas fa-external-link-alt mr-2"></i>Ver Guías
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Filtrar Reportes</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Período</label>
                <input type="month" id="filtro-periodo" 
                       class="w-full border border-gray-300 rounded px-3 py-2"
                       value="<?= date('Y-m') ?>">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Reporte</label>
                <select id="filtro-tipo" class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">Todos</option>
                    <option value="Situación Financiera">Situación Financiera</option>
                    <option value="Cartera Crediticia">Cartera Crediticia</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estatus</label>
                <select id="filtro-estatus" class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">Todos</option>
                    <option value="generado">Generado</option>
                    <option value="enviado">Enviado</option>
                    <option value="aceptado">Aceptado</option>
                    <option value="rechazado">Rechazado</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="cargarReportes()" 
                        class="w-full bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
                    <i class="fas fa-search mr-2"></i>Buscar
                </button>
            </div>
        </div>
    </div>

    <!-- Lista de reportes -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Reportes Generados</h3>
        </div>
        <div id="reportes-container" class="p-6">
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-file-alt text-4xl mb-2"></i>
                <p>Cargando reportes...</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal para generar reporte -->
<div id="modal-generar" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900 mb-4" id="modal-titulo">Generar Reporte</h3>
            <form id="form-generar">
                <input type="hidden" id="tipo-reporte" name="tipo_reporte">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Período</label>
                    <input type="month" name="periodo" required
                           class="w-full border border-gray-300 rounded px-3 py-2"
                           value="<?= date('Y-m') ?>">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Formato</label>
                    <select name="formato" required class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="EXCEL">Excel/CSV</option>
                        <option value="XML">XML</option>
                    </select>
                </div>
                
                <div class="flex space-x-3">
                    <button type="button" onclick="cerrarModal()" 
                            class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
                        <i class="fas fa-check mr-2"></i>Generar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Cargar reportes al iniciar
document.addEventListener('DOMContentLoaded', function() {
    cargarReportes();
});

async function cargarReportes() {
    const periodo = document.getElementById('filtro-periodo').value;
    const tipo = document.getElementById('filtro-tipo').value;
    const estatus = document.getElementById('filtro-estatus').value;
    
    const params = new URLSearchParams();
    if (periodo) params.append('periodo', periodo);
    if (tipo) params.append('tipo_reporte', tipo);
    if (estatus) params.append('estatus', estatus);
    
    try {
        const response = await fetch(`<?= BASE_URL ?>/cnbv/reportes?${params.toString()}`);
        const data = await response.json();
        
        if (data.success) {
            mostrarReportes(data.reportes);
        }
    } catch (error) {
        console.error('Error al cargar reportes:', error);
        document.getElementById('reportes-container').innerHTML = 
            '<div class="text-center py-8 text-red-500">Error al cargar reportes</div>';
    }
}

function mostrarReportes(reportes) {
    const container = document.getElementById('reportes-container');
    
    if (reportes.length === 0) {
        container.innerHTML = '<div class="text-center py-8 text-gray-500">No hay reportes generados</div>';
        return;
    }
    
    let html = '<div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200">';
    html += '<thead class="bg-gray-50"><tr>';
    html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>';
    html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Período</th>';
    html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>';
    html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Formato</th>';
    html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Generación</th>';
    html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estatus</th>';
    html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>';
    html += '</tr></thead><tbody class="bg-white divide-y divide-gray-200">';
    
    reportes.forEach(r => {
        const estatusColor = {
            'generado': 'bg-blue-100 text-blue-800',
            'enviado': 'bg-yellow-100 text-yellow-800',
            'aceptado': 'bg-green-100 text-green-800',
            'rechazado': 'bg-red-100 text-red-800'
        }[r.estatus] || 'bg-gray-100 text-gray-800';
        
        html += '<tr>';
        html += `<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${r.id}</td>`;
        html += `<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${r.periodo}</td>`;
        html += `<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${r.tipo_reporte}</td>`;
        html += `<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${r.formato}</td>`;
        html += `<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formatearFecha(r.fecha_generacion)}</td>`;
        html += `<td class="px-6 py-4 whitespace-nowrap"><span class="px-2 py-1 text-xs rounded ${estatusColor}">${r.estatus}</span></td>`;
        html += '<td class="px-6 py-4 whitespace-nowrap text-sm">';
        if (r.archivo) {
            html += `<a href="<?= BASE_URL ?>/uploads/reportes_cnbv/${r.archivo}" target="_blank" class="text-blue-600 hover:text-blue-800 mr-2"><i class="fas fa-download"></i></a>`;
        }
        html += `<button onclick="verDetalle(${r.id})" class="text-gray-600 hover:text-gray-800"><i class="fas fa-eye"></i></button>`;
        html += '</td>';
        html += '</tr>';
    });
    
    html += '</tbody></table></div>';
    container.innerHTML = html;
}

function formatearFecha(fecha) {
    if (!fecha) return '-';
    const d = new Date(fecha);
    return d.toLocaleDateString('es-MX') + ' ' + d.toLocaleTimeString('es-MX', {hour: '2-digit', minute: '2-digit'});
}

function modalGenerar(tipo) {
    const modal = document.getElementById('modal-generar');
    const titulo = document.getElementById('modal-titulo');
    const tipoInput = document.getElementById('tipo-reporte');
    
    if (tipo === 'situacion_financiera') {
        titulo.textContent = 'Generar Reporte de Situación Financiera';
        tipoInput.value = 'situacion_financiera';
    } else if (tipo === 'cartera') {
        titulo.textContent = 'Generar Reporte de Cartera Crediticia';
        tipoInput.value = 'cartera';
    }
    
    modal.classList.remove('hidden');
}

function cerrarModal() {
    document.getElementById('modal-generar').classList.add('hidden');
    document.getElementById('form-generar').reset();
}

document.getElementById('form-generar').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    const tipoReporte = data.tipo_reporte;
    delete data.tipo_reporte;
    
    let endpoint = '';
    if (tipoReporte === 'situacion_financiera') {
        endpoint = '<?= BASE_URL ?>/cnbv/generar-situacion-financiera';
    } else if (tipoReporte === 'cartera') {
        endpoint = '<?= BASE_URL ?>/cnbv/generar-cartera';
    }
    
    try {
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Reporte generado exitosamente');
            cerrarModal();
            cargarReportes();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al generar reporte');
    }
});

function verDetalle(reporteId) {
    // Implementar vista detallada del reporte
    alert('Ver detalle del reporte #' + reporteId);
}
</script>
