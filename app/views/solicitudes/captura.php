<?php
/**
 * Vista de Captura y Verificación de Datos del Solicitante
 * Registro detallado y validación de información personal, financiera y laboral del solicitante
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Captura y Verificación de Datos del Solicitante</h1>
        <div class="flex space-x-2">
            <button onclick="window.history.back()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Ver Historial
            </button>
            <button class="bg-primary-800 hover:bg-primary-700 text-white px-4 py-2 rounded">
                <i class="fas fa-save mr-2"></i>Guardar y Continuar
            </button>
        </div>
    </div>

    <p class="text-gray-600 mb-6">Registro detallado y validación de información personal, financiera y laboral del solicitante</p>

    <!-- Progress Bar -->
    <div class="mb-8">
        <div class="flex justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">Progreso de Captura</span>
            <span class="text-sm font-medium text-primary-800">75%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2.5">
            <div class="bg-primary-800 h-2.5 rounded-full" style="width: 75%"></div>
        </div>
        <div class="flex justify-between mt-2 text-xs text-gray-600">
            <span class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-1"></i>Datos Personales</span>
            <span class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-1"></i>Información Financiera</span>
            <span class="flex items-center text-yellow-600"><i class="fas fa-circle text-yellow-500 mr-1"></i>Datos Laborales</span>
            <span class="flex items-center text-gray-400"><i class="fas fa-circle mr-1"></i>Documentación</span>
        </div>
    </div>

    <form method="POST" action="<?= BASE_URL ?>/solicitudes/captura/<?= $solicitud['id'] ?>" id="capturaForm">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

        <!-- Información Personal -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6 border-b flex items-center justify-between">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-user text-primary-800 mr-2"></i>Información Personal
                </h2>
                <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                    <i class="fas fa-check mr-1"></i>Verificado
                </span>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre(s)</label>
                        <input type="text" value="<?= htmlspecialchars($socio['nombre'] ?? '') ?>" class="w-full border rounded px-3 py-2 bg-gray-50" readonly>
                        <span class="text-xs text-green-600 mt-1 flex items-center"><i class="fas fa-check-circle mr-1"></i>Verificado</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Apellido Paterno</label>
                        <input type="text" value="<?= htmlspecialchars($socio['apellido_paterno'] ?? '') ?>" class="w-full border rounded px-3 py-2 bg-gray-50" readonly>
                        <span class="text-xs text-green-600 mt-1 flex items-center"><i class="fas fa-check-circle mr-1"></i>Verificado</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Apellido Materno</label>
                        <input type="text" value="<?= htmlspecialchars($socio['apellido_materno'] ?? '') ?>" class="w-full border rounded px-3 py-2 bg-gray-50" readonly>
                        <span class="text-xs text-green-600 mt-1 flex items-center"><i class="fas fa-check-circle mr-1"></i>Verificado</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Nacimiento</label>
                        <input type="date" value="<?= $socio['fecha_nacimiento'] ?? '' ?>" class="w-full border rounded px-3 py-2">
                        <span class="text-xs text-green-600 mt-1 flex items-center"><i class="fas fa-check-circle mr-1"></i>Verificado</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Género</label>
                        <select class="w-full border rounded px-3 py-2">
                            <option>Masculino</option>
                            <option>Femenino</option>
                        </select>
                        <span class="text-xs text-green-600 mt-1 flex items-center"><i class="fas fa-check-circle mr-1"></i>Verificado</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estado Civil</label>
                        <select class="w-full border rounded px-3 py-2">
                            <option>Casado</option>
                            <option>Soltero</option>
                            <option>Divorciado</option>
                            <option>Viudo</option>
                        </select>
                        <span class="text-xs text-green-600 mt-1 flex items-center"><i class="fas fa-check-circle mr-1"></i>Verificado</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nacionalidad</label>
                        <select class="w-full border rounded px-3 py-2">
                            <option>Mexicana</option>
                            <option>Extranjera</option>
                        </select>
                        <span class="text-xs text-green-600 mt-1 flex items-center"><i class="fas fa-check-circle mr-1"></i>Verificado</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">CURP</label>
                        <input type="text" value="<?= $socio['curp'] ?? '' ?>" class="w-full border rounded px-3 py-2" maxlength="18">
                        <span class="text-xs text-green-600 mt-1 flex items-center"><i class="fas fa-check-circle mr-1"></i>Verificado</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">RFC</label>
                        <input type="text" value="<?= $socio['rfc'] ?? '' ?>" class="w-full border rounded px-3 py-2" maxlength="13">
                        <span class="text-xs text-yellow-600 mt-1 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>Pendiente</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Clave de Elector (INE)</label>
                        <input type="text" class="w-full border rounded px-3 py-2">
                        <span class="text-xs text-green-600 mt-1 flex items-center"><i class="fas fa-check-circle mr-1"></i>Verificado</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Correo Electrónico</label>
                        <input type="email" value="<?= $socio['email'] ?? '' ?>" class="w-full border rounded px-3 py-2">
                        <span class="text-xs text-green-600 mt-1 flex items-center"><i class="fas fa-check-circle mr-1"></i>Verificado</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                        <input type="tel" value="<?= $socio['telefono'] ?? '' ?>" class="w-full border rounded px-3 py-2">
                        <span class="text-xs text-green-600 mt-1 flex items-center"><i class="fas fa-check-circle mr-1"></i>Verificado</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Celular</label>
                        <input type="tel" value="<?= $socio['celular'] ?? '' ?>" class="w-full border rounded px-3 py-2">
                        <span class="text-xs text-green-600 mt-1 flex items-center"><i class="fas fa-check-circle mr-1"></i>Verificado</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información de Domicilio -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6 border-b flex items-center justify-between">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-home text-primary-800 mr-2"></i>Información de Domicilio
                </h2>
                <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                    <i class="fas fa-check mr-1"></i>Verificado
                </span>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Calle</label>
                        <input type="text" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Número Exterior</label>
                        <input type="text" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Número Interior</label>
                        <input type="text" class="w-full border rounded px-3 py-2">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Colonia</label>
                        <input type="text" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ciudad/Municipio</label>
                        <input type="text" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                        <select class="w-full border rounded px-3 py-2">
                            <option>Ciudad de México</option>
                            <option>Estado de México</option>
                            <option>Jalisco</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Código Postal</label>
                        <input type="text" class="w-full border rounded px-3 py-2" maxlength="5">
                        <span class="text-xs text-green-600 mt-1 flex items-center"><i class="fas fa-check-circle mr-1"></i>Verificado</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Vivienda</label>
                        <select class="w-full border rounded px-3 py-2">
                            <option>Propia</option>
                            <option>Rentada</option>
                            <option>Familiar</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Años en el Domicilio</label>
                        <input type="number" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Renta Mensual</label>
                        <input type="number" step="0.01" class="w-full border rounded px-3 py-2">
                    </div>
                </div>

                <div class="mt-4">
                    <button type="button" class="text-blue-600 hover:text-blue-800 text-sm">
                        <i class="fas fa-map-marker-alt mr-1"></i>Validar Ubicación
                    </button>
                </div>
            </div>
        </div>

        <!-- Información Financiera -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6 border-b flex items-center justify-between">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-dollar-sign text-primary-800 mr-2"></i>Información Financiera
                </h2>
                <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                    <i class="fas fa-check mr-1"></i>Verificado
                </span>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ingresos Mensuales</label>
                        <input type="number" step="0.01" class="w-full border rounded px-3 py-2">
                        <span class="text-xs text-green-600 mt-1 flex items-center"><i class="fas fa-check-circle mr-1"></i>Verificado</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ingresos Adicionales</label>
                        <input type="number" step="0.01" class="w-full border rounded px-3 py-2">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gastos Mensuales</label>
                        <input type="number" step="0.01" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fuente Principal de Ingresos</label>
                        <select class="w-full border rounded px-3 py-2">
                            <option>Empleado</option>
                            <option>Independiente</option>
                            <option>Negocio Propio</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cuenta Bancaria Principal</label>
                        <select class="w-full border rounded px-3 py-2">
                            <option>BBVA México</option>
                            <option>Santander</option>
                            <option>Banamex</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Número de Cuenta</label>
                        <input type="text" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">CLABE Interbancaria</label>
                        <input type="text" class="w-full border rounded px-3 py-2" maxlength="18">
                        <span class="text-xs text-green-600 mt-1 flex items-center"><i class="fas fa-check-circle mr-1"></i>Verificado</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Límite Total de Crédito</label>
                        <input type="number" step="0.01" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deuda Actual</label>
                        <input type="number" step="0.01" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Número de Tarjetas de Crédito</label>
                        <input type="number" class="w-full border rounded px-3 py-2">
                    </div>
                </div>
            </div>
        </div>

        <!-- Información Laboral -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6 border-b flex items-center justify-between">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-briefcase text-primary-800 mr-2"></i>Información Laboral
                </h2>
                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                    <i class="fas fa-clock mr-1"></i>Pendiente
                </span>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de la Empresa</label>
                        <input type="text" class="w-full border rounded px-3 py-2">
                        <span class="text-xs text-yellow-600 mt-1 flex items-center"><i class="fas fa-clock mr-1"></i>Pendiente</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Puesto</label>
                        <input type="text" class="w-full border rounded px-3 py-2">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Ingreso</label>
                        <input type="date" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Empleo</label>
                        <select class="w-full border rounded px-3 py-2">
                            <option>Tiempo Completo</option>
                            <option>Tiempo Parcial</option>
                            <option>Por Contrato</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Años de Antigüedad</label>
                        <input type="number" class="w-full border rounded px-3 py-2">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono de la Empresa</label>
                        <input type="tel" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Supervisor</label>
                        <input type="text" class="w-full border rounded px-3 py-2">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dirección de la Empresa</label>
                    <textarea rows="2" class="w-full border rounded px-3 py-2"></textarea>
                </div>

                <div class="mt-4">
                    <button type="button" class="text-blue-600 hover:text-blue-800 text-sm">
                        <i class="fas fa-building mr-1"></i>Validar Empresa
                    </button>
                </div>
            </div>
        </div>

        <!-- Validación con Fuentes Externas -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6 border-b">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-shield-alt text-primary-800 mr-2"></i>Validación con Fuentes Externas
                </h2>
            </div>
            <div class="p-6">
                <div class="bg-blue-50 rounded-lg p-4 mb-4">
                    <p class="text-sm text-blue-700">
                        <i class="fas fa-info-circle mr-2"></i>Los datos se validan automáticamente con fuentes externas. Asegúrese de que la información sea exacta y esté actualizada.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-semibold text-gray-700 flex items-center">
                                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-red-600 font-bold">BC</span>
                                </div>
                                Buró de Crédito
                            </h3>
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded">
                                <i class="fas fa-check mr-1"></i>Verificado
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">Historial crediticio y score</p>
                        <div class="text-sm">
                            <p class="text-gray-700">Score: <span class="font-semibold text-green-600">750</span></p>
                        </div>
                    </div>

                    <div class="border rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-semibold text-gray-700 flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-blue-600 font-bold">CC</span>
                                </div>
                                Círculo de Crédito
                            </h3>
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded">
                                <i class="fas fa-check mr-1"></i>Verificado
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">Información crediticia complementaria</p>
                        <div class="text-sm">
                            <p class="text-gray-700">Score: <span class="font-semibold text-green-600">720</span></p>
                        </div>
                    </div>

                    <div class="border rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-semibold text-gray-700 flex items-center">
                                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-yellow-600 font-bold">SAT</span>
                                </div>
                                SAT
                            </h3>
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded">
                                <i class="fas fa-clock mr-1"></i>En proceso...
                            </span>
                        </div>
                        <p class="text-sm text-gray-600">Validación de RFC y situación fiscal</p>
                    </div>

                    <div class="border rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-semibold text-gray-700 flex items-center">
                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-purple-600 font-bold">IMSS</span>
                                </div>
                                IMSS
                            </h3>
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded">
                                <i class="fas fa-check mr-1"></i>Verificado
                            </span>
                        </div>
                        <p class="text-sm text-gray-600">Validación de empleo y cotizaciones</p>
                    </div>
                </div>

                <div class="mt-4 flex space-x-2">
                    <button type="button" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                        <i class="fas fa-sync-alt mr-2"></i>Actualizar
                    </button>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="flex justify-between items-center">
            <button type="button" onclick="window.history.back()" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-times mr-2"></i>Cancelar
            </button>
            <div class="flex space-x-2">
                <button type="button" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded">
                    <i class="fas fa-save mr-2"></i>Guardar Borrador
                </button>
                <button type="button" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-2 rounded">
                    <i class="fas fa-user mr-2"></i>Solicitar Información Adicional
                </button>
                <button type="submit" class="bg-primary-800 hover:bg-primary-700 text-white px-6 py-2 rounded">
                    <i class="fas fa-arrow-right mr-2"></i>Completar Captura
                </button>
            </div>
        </div>
    </form>
</div>
