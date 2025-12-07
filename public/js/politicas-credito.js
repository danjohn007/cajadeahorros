/**
 * Validaciones de Políticas de Crédito
 * Frontend validation for credit policies
 */

const PoliticasCredito = {
    /**
     * Valida edad y plazo de un solicitante
     * @param {number} socioId - ID del socio
     * @param {number} plazoMeses - Plazo en meses solicitado
     * @returns {Promise<object>} Resultado de la validación
     */
    async validarEdadPlazo(socioId, plazoMeses) {
        try {
            const response = await fetch('/politicas/validar-edad-plazo', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    socio_id: socioId,
                    plazo_meses: plazoMeses
                })
            });

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error al validar edad y plazo:', error);
            return {
                success: false,
                message: 'Error al validar edad y plazo'
            };
        }
    },

    /**
     * Valida si un crédito requiere aval
     * @param {number} productoId - ID del producto financiero
     * @param {number} monto - Monto solicitado
     * @returns {Promise<object>} Resultado de la validación
     */
    async validarRequiereAval(productoId, monto) {
        try {
            const response = await fetch('/politicas/validar-aval', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    producto_id: productoId,
                    monto: monto
                })
            });

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error al validar requerimiento de aval:', error);
            return {
                success: false,
                message: 'Error al validar requerimiento de aval'
            };
        }
    },

    /**
     * Muestra un mensaje de alerta
     * @param {string} message - Mensaje a mostrar
     * @param {string} type - Tipo de alerta (success, warning, error, info)
     */
    mostrarAlerta(message, type = 'info') {
        // Implementación simple de alerta
        // Puede ser reemplazada por una librería de notificaciones
        const alertClass = {
            'success': 'bg-green-100 border-green-400 text-green-700',
            'warning': 'bg-yellow-100 border-yellow-400 text-yellow-700',
            'error': 'bg-red-100 border-red-400 text-red-700',
            'info': 'bg-blue-100 border-blue-400 text-blue-700'
        }[type] || 'bg-blue-100 border-blue-400 text-blue-700';

        const alertHtml = `
            <div class="${alertClass} border px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">${message}</span>
            </div>
        `;

        // Buscar un contenedor de alertas o insertar al inicio del formulario
        const alertContainer = document.getElementById('alert-container');
        if (alertContainer) {
            alertContainer.innerHTML = alertHtml;
        } else {
            const form = document.querySelector('form');
            if (form) {
                form.insertAdjacentHTML('afterbegin', alertHtml);
            }
        }

        // Auto-remover después de 10 segundos
        setTimeout(() => {
            const alert = document.querySelector('[role="alert"]');
            if (alert) {
                alert.remove();
            }
        }, 10000);
    },

    /**
     * Inicializa las validaciones en el formulario de solicitud de crédito
     */
    inicializarValidacionesFormulario() {
        const socioSelect = document.getElementById('socio_id');
        const plazoInput = document.getElementById('plazo_meses');
        const montoInput = document.getElementById('monto_solicitado');
        const productoSelect = document.getElementById('tipo_credito_id');

        if (!socioSelect || !plazoInput) {
            return; // No es el formulario correcto
        }

        // Validar al cambiar el plazo o el socio
        const validarEdad = async () => {
            const socioId = socioSelect.value;
            const plazoMeses = parseInt(plazoInput.value);

            if (socioId && plazoMeses > 0) {
                const resultado = await this.validarEdadPlazo(socioId, plazoMeses);

                if (resultado.success && !resultado.valido) {
                    this.mostrarAlerta(resultado.mensaje, 'warning');
                    
                    // Opcionalmente, ajustar el plazo máximo
                    if (resultado.plazo_maximo) {
                        plazoInput.setAttribute('data-plazo-maximo', resultado.plazo_maximo);
                    }
                }
            }
        };

        // Validar aval cuando cambie el monto o producto
        const validarAval = async () => {
            const productoId = productoSelect.value;
            const monto = parseFloat(montoInput.value);

            if (productoId && monto > 0) {
                const resultado = await this.validarRequiereAval(productoId, monto);

                if (resultado.success && resultado.requiere_aval) {
                    this.mostrarAlerta(resultado.mensaje, 'info');
                }
            }
        };

        // Agregar listeners
        if (plazoInput) {
            plazoInput.addEventListener('blur', validarEdad);
            plazoInput.addEventListener('change', validarEdad);
        }

        if (socioSelect) {
            socioSelect.addEventListener('change', validarEdad);
        }

        if (montoInput && productoSelect) {
            montoInput.addEventListener('blur', validarAval);
            montoInput.addEventListener('change', validarAval);
            productoSelect.addEventListener('change', validarAval);
        }
    },

    /**
     * Obtener checklist para un tipo de operación
     * @param {string} tipoOperacion - apertura, renovacion, reestructura
     * @param {number} productoId - (opcional) ID del producto
     * @returns {Promise<object>} Checklists
     */
    async obtenerChecklist(tipoOperacion, productoId = null) {
        try {
            let url = `/politicas/checklist?tipo_operacion=${tipoOperacion}`;
            if (productoId) {
                url += `&producto_id=${productoId}`;
            }

            const response = await fetch(url);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error al obtener checklist:', error);
            return {
                success: false,
                message: 'Error al obtener checklist'
            };
        }
    },

    /**
     * Marcar un item del checklist como completado
     * @param {number} creditoId - ID del crédito
     * @param {number} checklistItemId - ID del item del checklist
     * @param {boolean} completado - Si está completado o no
     * @param {string} observaciones - Observaciones opcionales
     * @returns {Promise<object>} Resultado
     */
    async marcarItemCompletado(creditoId, checklistItemId, completado = true, observaciones = null) {
        try {
            const response = await fetch('/politicas/checklist/marcar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    credito_id: creditoId,
                    checklist_item_id: checklistItemId,
                    completado: completado,
                    observaciones: observaciones
                })
            });

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error al marcar item:', error);
            return {
                success: false,
                message: 'Error al marcar item del checklist'
            };
        }
    }
};

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    PoliticasCredito.inicializarValidacionesFormulario();
});

// Exportar para uso global
window.PoliticasCredito = PoliticasCredito;
