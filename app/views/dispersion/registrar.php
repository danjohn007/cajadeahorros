<?php
/**
 * Vista de Registro de Créditos
 * Registro formal de créditos aprobados previo a la dispersión
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Registro de Créditos</h1>
        <button onclick="window.history.back()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Regresar
        </button>
    </div>

    <p class="text-gray-600 mb-6">Registro formal de créditos aprobados previo a la dispersión</p>

    <!-- Información del Crédito -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold flex items-center">
                <i class="fas fa-file-contract text-primary-800 mr-2"></i>Información del Crédito
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Número de Crédito</label>
                    <p class="font-semibold"><?= htmlspecialchars($credito['numero_credito'] ?? 'N/A') ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Socio</label>
                    <p class="font-semibold"><?= htmlspecialchars(($socio['nombre'] ?? '') . ' ' . ($socio['apellido_paterno'] ?? '')) ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Monto Aprobado</label>
                    <p class="font-semibold text-green-600">$<?= number_format($credito['monto_solicitado'] ?? 0, 2) ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Estado</label>
                    <p class="font-semibold">
                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">
                            <?= ucfirst($credito['estatus'] ?? 'aprobado') ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de Registro -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold flex items-center">
                <i class="fas fa-edit text-primary-800 mr-2"></i>Datos de Formalización
            </h2>
        </div>
        <div class="p-6">
            <form method="POST" action="<?= BASE_URL ?>/dispersion/registrar/<?= $credito['id'] ?>">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Formalización *</label>
                        <input type="date" name="fecha_formalizacion" value="<?= date('Y-m-d') ?>" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Número de Contrato *</label>
                        <input type="text" name="numero_contrato" placeholder="CONT-2024-001" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Número de Pagaré *</label>
                        <input type="text" name="numero_pagare" placeholder="PAG-2024-001" class="w-full border rounded px-3 py-2" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cuenta de Dispersión</label>
                        <select name="cuenta_dispersion" class="w-full border rounded px-3 py-2">
                            <option>Cuenta 001 - Dispersión General</option>
                            <option>Cuenta 002 - Dispersión Especial</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Método de Dispersión</label>
                        <select name="metodo_dispersion" class="w-full border rounded px-3 py-2">
                            <option>Transferencia Bancaria</option>
                            <option>Cheque</option>
                            <option>Efectivo</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                    <textarea name="observaciones" rows="3" class="w-full border rounded px-3 py-2"></textarea>
                </div>

                <div class="mt-6 flex justify-end space-x-2">
                    <button type="button" onclick="window.history.back()" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-primary-800 hover:bg-primary-700 text-white px-6 py-2 rounded">
                        <i class="fas fa-save mr-2"></i>Registrar y Formalizar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Checklist de Verificación -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold flex items-center">
                <i class="fas fa-clipboard-check text-primary-800 mr-2"></i>Checklist de Verificación
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                <div class="flex items-center p-3 bg-green-50 rounded">
                    <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">Aprobación del comité de crédito</p>
                    </div>
                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded font-semibold">Completo</span>
                </div>

                <div class="flex items-center p-3 bg-green-50 rounded">
                    <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">Documentación completa</p>
                    </div>
                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded font-semibold">Completo</span>
                </div>

                <div class="flex items-center p-3 bg-green-50 rounded">
                    <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">Validación de políticas institucionales</p>
                    </div>
                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded font-semibold">Completo</span>
                </div>

                <div class="flex items-center p-3 bg-green-50 rounded">
                    <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">Avales y garantías registradas</p>
                    </div>
                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded font-semibold">Completo</span>
                </div>

                <div class="flex items-center p-3 bg-yellow-50 rounded">
                    <i class="fas fa-clock text-yellow-600 text-xl mr-3"></i>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">Firma de contrato y pagaré</p>
                    </div>
                    <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded font-semibold">Pendiente</span>
                </div>
            </div>
        </div>
    </div>
</div>
