/**
 * JavaScript principal del sistema
 * Sistema de Gestión Integral de Caja de Ahorros
 */

// Utilidades
const Utils = {
    // Formatear número como moneda
    formatCurrency: (amount) => {
        return new Intl.NumberFormat('es-MX', {
            style: 'currency',
            currency: 'MXN'
        }).format(amount);
    },
    
    // Formatear fecha
    formatDate: (date) => {
        return new Intl.DateTimeFormat('es-MX', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        }).format(new Date(date));
    },
    
    // Mostrar notificación
    showNotification: (message, type = 'info') => {
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        };
        
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg text-white ${colors[type]} shadow-lg transform transition-all duration-300 translate-x-full`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Animar entrada
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 10);
        
        // Remover después de 5 segundos
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    },
    
    // Confirmar acción
    confirm: (message) => {
        return window.confirm(message);
    }
};

// Inicialización al cargar el DOM
document.addEventListener('DOMContentLoaded', () => {
    // Auto-cerrar alertas flash después de 5 segundos
    const flashMessages = document.querySelectorAll('[role="alert"]');
    flashMessages.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
    
    // Agregar confirmación a enlaces de eliminación
    document.querySelectorAll('a[onclick*="confirm"]').forEach(link => {
        link.addEventListener('click', (e) => {
            if (!confirm(link.dataset.confirm || '¿Está seguro de realizar esta acción?')) {
                e.preventDefault();
            }
        });
    });
    
    // Inicializar tooltips si existen
    initTooltips();
    
    // Inicializar búsqueda dinámica
    initDynamicSearch();
});

// Inicializar tooltips
function initTooltips() {
    document.querySelectorAll('[data-tooltip]').forEach(element => {
        element.addEventListener('mouseenter', (e) => {
            const tooltip = document.createElement('div');
            tooltip.className = 'absolute z-50 px-2 py-1 text-xs text-white bg-gray-800 rounded shadow-lg';
            tooltip.textContent = element.dataset.tooltip;
            tooltip.style.top = (e.target.offsetTop - 30) + 'px';
            tooltip.style.left = e.target.offsetLeft + 'px';
            tooltip.id = 'tooltip-temp';
            element.parentElement.appendChild(tooltip);
        });
        
        element.addEventListener('mouseleave', () => {
            const tooltip = document.getElementById('tooltip-temp');
            if (tooltip) tooltip.remove();
        });
    });
}

// Búsqueda dinámica
function initDynamicSearch() {
    const searchInput = document.getElementById('search-dynamic');
    if (!searchInput) return;
    
    let timeout = null;
    searchInput.addEventListener('input', (e) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            const query = e.target.value;
            if (query.length >= 3) {
                // Realizar búsqueda AJAX
                fetch(`${window.BASE_URL || ''}/api/socios/buscar?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        displaySearchResults(data);
                    })
                    .catch(err => console.error('Error en búsqueda:', err));
            }
        }, 300);
    });
}

// Mostrar resultados de búsqueda
function displaySearchResults(results) {
    const container = document.getElementById('search-results');
    if (!container) return;
    
    container.innerHTML = '';
    
    if (results.length === 0) {
        container.innerHTML = '<p class="p-4 text-gray-500">No se encontraron resultados</p>';
        return;
    }
    
    results.forEach(item => {
        const div = document.createElement('div');
        div.className = 'p-3 hover:bg-gray-100 cursor-pointer border-b';
        div.innerHTML = `
            <div class="font-medium">${item.nombre}</div>
            <div class="text-sm text-gray-500">No. Socio: ${item.numero_socio}</div>
        `;
        div.addEventListener('click', () => {
            window.location.href = `${window.BASE_URL || ''}/socios/ver/${item.id}`;
        });
        container.appendChild(div);
    });
}

// Validación de formularios
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('border-red-500');
            isValid = false;
        } else {
            field.classList.remove('border-red-500');
        }
    });
    
    return isValid;
}

// Exportar a Excel (simulado - descarga CSV)
function exportToExcel(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const rowData = [];
        cols.forEach(col => {
            rowData.push('"' + col.innerText.replace(/"/g, '""') + '"');
        });
        csv.push(rowData.join(','));
    });
    
    const csvContent = csv.join('\n');
    const blob = new Blob(['\ufeff' + csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename + '.csv';
    link.click();
}

// Imprimir contenido
function printContent(elementId) {
    const content = document.getElementById(elementId);
    if (!content) return;
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>Imprimir</title>
            <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
            <style>
                body { padding: 20px; }
                @media print {
                    body { padding: 0; }
                }
            </style>
        </head>
        <body>
            ${content.innerHTML}
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}
