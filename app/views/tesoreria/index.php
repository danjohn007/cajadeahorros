<?php
/**
 * Vista Principal de Tesorería
 * Módulo de proyecciones financieras y flujos de efectivo
 */
?>

<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Módulo de Tesorería</h2>
        <p class="text-gray-600">Gestión de proyecciones financieras y flujos de efectivo</p>
    </div>

    <!-- Resumen de Cartera -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Cartera Total</p>
                    <p class="text-2xl font-bold text-gray-800" id="saldo-total">$0.00</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-wallet text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Cartera Vigente</p>
                    <p class="text-2xl font-bold text-green-600" id="saldo-vigente">$0.00</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Cartera Vencida</p>
                    <p class="text-2xl font-bold text-red-600" id="saldo-vencido">$0.00</p>
                </div>
                <div class="bg-red-100 p-3 rounded-full">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">% Cartera Vigente</p>
                    <p class="text-2xl font-bold text-blue-600" id="porcentaje-vigente">0%</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-percent text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Pestañas -->
    <div class="bg-white rounded-lg shadow mb-6" x-data="{ activeTab: 'proyecciones' }">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button @click="activeTab = 'proyecciones'" 
                        :class="activeTab === 'proyecciones' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-6 border-b-2 font-medium text-sm">
                    <i class="fas fa-chart-line mr-2"></i>Proyecciones Financieras
                </button>
                <button @click="activeTab = 'flujos'" 
                        :class="activeTab === 'flujos' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-6 border-b-2 font-medium text-sm">
                    <i class="fas fa-exchange-alt mr-2"></i>Flujos de Efectivo
                </button>
                <button @click="activeTab = 'manual'" 
                        :class="activeTab === 'manual' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-6 border-b-2 font-medium text-sm">
                    <i class="fas fa-edit mr-2"></i>Registro Manual
                </button>
            </nav>
        </div>

        <!-- Tab: Proyecciones Financieras -->
        <div x-show="activeTab === 'proyecciones'" class="p-6">
            <div class="mb-4 flex space-x-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                    <input type="date" id="fecha-inicio-proyeccion" 
                           class="border border-gray-300 rounded px-3 py-2"
                           value="<?= date('Y-m-d') ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                    <input type="date" id="fecha-fin-proyeccion" 
                           class="border border-gray-300 rounded px-3 py-2"
                           value="<?= date('Y-m-d', strtotime('+6 months')) ?>">
                </div>
                <div class="flex items-end">
                    <button onclick="cargarProyecciones()" 
                            class="bg-primary-600 text-white px-6 py-2 rounded hover:bg-primary-700">
                        <i class="fas fa-search mr-2"></i>Consultar
                    </button>
                </div>
            </div>

            <div id="proyecciones-container">
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-chart-line text-4xl mb-2"></i>
                    <p>Seleccione un rango de fechas y presione Consultar</p>
                </div>
            </div>

            <!-- Gráfica de Proyecciones -->
            <div class="mt-6">
                <canvas id="chart-proyecciones" height="80"></canvas>
            </div>
        </div>

        <!-- Tab: Flujos de Efectivo -->
        <div x-show="activeTab === 'flujos'" class="p-6">
            <div class="mb-4 flex space-x-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                    <input type="date" id="fecha-inicio-flujo" 
                           class="border border-gray-300 rounded px-3 py-2"
                           value="<?= date('Y-m-d', strtotime('-6 months')) ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                    <input type="date" id="fecha-fin-flujo" 
                           class="border border-gray-300 rounded px-3 py-2"
                           value="<?= date('Y-m-d') ?>">
                </div>
                <div class="flex items-end">
                    <button onclick="cargarFlujos()" 
                            class="bg-primary-600 text-white px-6 py-2 rounded hover:bg-primary-700">
                        <i class="fas fa-search mr-2"></i>Consultar
                    </button>
                </div>
            </div>

            <div id="flujos-container">
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-exchange-alt text-4xl mb-2"></i>
                    <p>Seleccione un rango de fechas y presione Consultar</p>
                </div>
            </div>

            <!-- Gráfica de Flujos -->
            <div class="mt-6">
                <canvas id="chart-flujos" height="80"></canvas>
            </div>
        </div>

        <!-- Tab: Registro Manual -->
        <div x-show="activeTab === 'manual'" class="p-6">
            <form id="form-proyeccion-manual" class="max-w-2xl">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Proyección</label>
                        <input type="date" name="fecha_proyeccion" required
                               class="w-full border border-gray-300 rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                        <select name="tipo" required class="w-full border border-gray-300 rounded px-3 py-2">
                            <option value="ingreso">Ingreso</option>
                            <option value="egreso">Egreso</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Concepto</label>
                        <input type="text" name="concepto" required
                               class="w-full border border-gray-300 rounded px-3 py-2"
                               placeholder="Descripción de la proyección">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Monto Proyectado</label>
                        <input type="number" name="monto_proyectado" step="0.01" required
                               class="w-full border border-gray-300 rounded px-3 py-2"
                               placeholder="0.00">
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit" 
                            class="bg-primary-600 text-white px-6 py-2 rounded hover:bg-primary-700">
                        <i class="fas fa-save mr-2"></i>Guardar Proyección
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let chartProyecciones = null;
let chartFlujos = null;

// Cargar resumen de cartera al iniciar
document.addEventListener('DOMContentLoaded', function() {
    cargarResumenCartera();
});

async function cargarResumenCartera() {
    try {
        const response = await fetch('<?= BASE_URL ?>/tesoreria/resumen-cartera');
        const data = await response.json();
        
        if (data.success) {
            const resumen = data.resumen;
            document.getElementById('saldo-total').textContent = '$' + parseFloat(resumen.saldo_total || 0).toLocaleString('es-MX', {minimumFractionDigits: 2});
            document.getElementById('saldo-vigente').textContent = '$' + parseFloat(resumen.saldo_vigente || 0).toLocaleString('es-MX', {minimumFractionDigits: 2});
            document.getElementById('saldo-vencido').textContent = '$' + parseFloat(resumen.saldo_vencido || 0).toLocaleString('es-MX', {minimumFractionDigits: 2});
            document.getElementById('porcentaje-vigente').textContent = parseFloat(resumen.porcentaje_cartera_vigente || 0).toFixed(2) + '%';
        }
    } catch (error) {
        console.error('Error al cargar resumen:', error);
    }
}

async function cargarProyecciones() {
    const fechaInicio = document.getElementById('fecha-inicio-proyeccion').value;
    const fechaFin = document.getElementById('fecha-fin-proyeccion').value;
    
    try {
        const response = await fetch(`<?= BASE_URL ?>/tesoreria/proyecciones?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`);
        const data = await response.json();
        
        if (data.success) {
            mostrarProyecciones(data.proyecciones_mensuales);
            renderChartProyecciones(data.proyecciones_mensuales);
        }
    } catch (error) {
        console.error('Error al cargar proyecciones:', error);
    }
}

function mostrarProyecciones(proyecciones) {
    const container = document.getElementById('proyecciones-container');
    
    if (proyecciones.length === 0) {
        container.innerHTML = '<div class="text-center py-8 text-gray-500">No hay proyecciones para el período seleccionado</div>';
        return;
    }
    
    let html = '<div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200">';
    html += '<thead class="bg-gray-50"><tr>';
    html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Período</th>';
    html += '<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Capital</th>';
    html += '<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Interés</th>';
    html += '<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>';
    html += '<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acumulado</th>';
    html += '</tr></thead><tbody class="bg-white divide-y divide-gray-200">';
    
    proyecciones.forEach(p => {
        html += '<tr>';
        html += `<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${p.periodo}</td>`;
        html += `<td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">$${parseFloat(p.capital_proyectado).toLocaleString('es-MX', {minimumFractionDigits: 2})}</td>`;
        html += `<td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">$${parseFloat(p.interes_proyectado).toLocaleString('es-MX', {minimumFractionDigits: 2})}</td>`;
        html += `<td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900">$${parseFloat(p.monto_total_proyectado).toLocaleString('es-MX', {minimumFractionDigits: 2})}</td>`;
        html += `<td class="px-6 py-4 whitespace-nowrap text-sm text-right text-blue-600">$${parseFloat(p.acumulado_total).toLocaleString('es-MX', {minimumFractionDigits: 2})}</td>`;
        html += '</tr>';
    });
    
    html += '</tbody></table></div>';
    container.innerHTML = html;
}

function renderChartProyecciones(proyecciones) {
    const ctx = document.getElementById('chart-proyecciones').getContext('2d');
    
    if (chartProyecciones) {
        chartProyecciones.destroy();
    }
    
    chartProyecciones = new Chart(ctx, {
        type: 'line',
        data: {
            labels: proyecciones.map(p => p.periodo),
            datasets: [{
                label: 'Capital',
                data: proyecciones.map(p => parseFloat(p.capital_proyectado)),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
            }, {
                label: 'Interés',
                data: proyecciones.map(p => parseFloat(p.interes_proyectado)),
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Proyecciones Mensuales'
                }
            }
        }
    });
}

async function cargarFlujos() {
    const fechaInicio = document.getElementById('fecha-inicio-flujo').value;
    const fechaFin = document.getElementById('fecha-fin-flujo').value;
    
    try {
        const response = await fetch(`<?= BASE_URL ?>/tesoreria/flujos?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`);
        const data = await response.json();
        
        if (data.success) {
            mostrarFlujos(data.comparacion);
            renderChartFlujos(data.comparacion);
        }
    } catch (error) {
        console.error('Error al cargar flujos:', error);
    }
}

function mostrarFlujos(comparacion) {
    const container = document.getElementById('flujos-container');
    
    if (comparacion.length === 0) {
        container.innerHTML = '<div class="text-center py-8 text-gray-500">No hay flujos para el período seleccionado</div>';
        return;
    }
    
    let html = '<div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200">';
    html += '<thead class="bg-gray-50"><tr>';
    html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Período</th>';
    html += '<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Proyectado</th>';
    html += '<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Real</th>';
    html += '<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Variación</th>';
    html += '<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">% Cumplimiento</th>';
    html += '</tr></thead><tbody class="bg-white divide-y divide-gray-200">';
    
    comparacion.forEach(c => {
        const variacionClass = parseFloat(c.variacion) >= 0 ? 'text-green-600' : 'text-red-600';
        html += '<tr>';
        html += `<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${c.periodo}</td>`;
        html += `<td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">$${parseFloat(c.monto_proyectado).toLocaleString('es-MX', {minimumFractionDigits: 2})}</td>`;
        html += `<td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">$${parseFloat(c.monto_real).toLocaleString('es-MX', {minimumFractionDigits: 2})}</td>`;
        html += `<td class="px-6 py-4 whitespace-nowrap text-sm text-right ${variacionClass}">$${parseFloat(c.variacion).toLocaleString('es-MX', {minimumFractionDigits: 2})}</td>`;
        html += `<td class="px-6 py-4 whitespace-nowrap text-sm text-right text-blue-600">${parseFloat(c.porcentaje_cumplimiento).toFixed(2)}%</td>`;
        html += '</tr>';
    });
    
    html += '</tbody></table></div>';
    container.innerHTML = html;
}

function renderChartFlujos(comparacion) {
    const ctx = document.getElementById('chart-flujos').getContext('2d');
    
    if (chartFlujos) {
        chartFlujos.destroy();
    }
    
    chartFlujos = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: comparacion.map(c => c.periodo),
            datasets: [{
                label: 'Proyectado',
                data: comparacion.map(c => parseFloat(c.monto_proyectado)),
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1
            }, {
                label: 'Real',
                data: comparacion.map(c => parseFloat(c.monto_real)),
                backgroundColor: 'rgba(16, 185, 129, 0.5)',
                borderColor: 'rgb(16, 185, 129)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Flujos de Efectivo: Real vs Proyectado'
                }
            }
        }
    });
}

// Registro manual
document.getElementById('form-proyeccion-manual')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('<?= BASE_URL ?>/tesoreria/proyeccion', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Proyección registrada exitosamente');
            this.reset();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al registrar proyección');
    }
});
</script>
