<?php
/**
 * Vista de Generación de Propuestas de Crédito
 * Elaboración y presentación de propuestas personalizadas según las necesidades del cliente
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Generación de Propuestas</h1>
        <div class="flex space-x-2">
            <button onclick="window.history.back()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Regresar
            </button>
            <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                <i class="fas fa-file-pdf mr-2"></i>Generar PDF
            </button>
            <button class="bg-primary-800 hover:bg-primary-700 text-white px-4 py-2 rounded">
                <i class="fas fa-save mr-2"></i>Guardar Propuesta
            </button>
        </div>
    </div>

    <p class="text-gray-600 mb-6">Elaboración y presentación de propuestas personalizadas según las necesidades del cliente</p>

    <!-- Información del Solicitante -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold flex items-center">
                <i class="fas fa-user text-primary-800 mr-2"></i>Información del Solicitante
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Nombre Completo</label>
                    <p class="font-semibold"><?= htmlspecialchars(($credito['nombre'] ?? '') . ' ' . ($credito['apellido_paterno'] ?? '')) ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Edad</label>
                    <p class="font-semibold"><?= $edad ?? 'N/A' ?> años</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Número de Crédito</label>
                    <p class="font-semibold"><?= htmlspecialchars($credito['numero_credito'] ?? 'N/A') ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Solicitud</label>
                    <p class="font-semibold"><?= date('d/m/Y', strtotime($credito['fecha_solicitud'] ?? 'now')) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalle de la Propuesta -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold flex items-center">
                <i class="fas fa-file-contract text-primary-800 mr-2"></i>Detalle de la Propuesta
            </h2>
        </div>
        <div class="p-6">
            <form method="POST" action="<?= BASE_URL ?>/creditos/propuesta/<?= $credito['id'] ?>">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Producto Financiero</label>
                        <select name="producto_id" class="w-full border rounded px-3 py-2" required>
                            <option value=""><?= htmlspecialchars($credito['producto_nombre'] ?? 'Seleccionar producto') ?></option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Crédito</label>
                        <select name="tipo_credito" class="w-full border rounded px-3 py-2" required>
                            <option>Simple</option>
                            <option>Revolvente</option>
                            <option>Prendario</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Monto Solicitado</label>
                        <input type="number" name="monto" value="<?= $credito['monto_solicitado'] ?? 0 ?>" step="0.01" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Plazo (meses)</label>
                        <input type="number" name="plazo" value="<?= $credito['plazo_meses'] ?? 12 ?>" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tasa de Interés Anual (%)</label>
                        <input type="number" name="tasa" value="<?= $credito['tasa_interes_anual'] ?? 15 ?>" step="0.01" class="w-full border rounded px-3 py-2" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Frecuencia de Pago</label>
                        <select name="frecuencia" class="w-full border rounded px-3 py-2" required>
                            <option>Mensual</option>
                            <option>Quincenal</option>
                            <option>Semanal</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Método de Pago</label>
                        <select name="metodo_pago" class="w-full border rounded px-3 py-2" required>
                            <option>Débito Automático</option>
                            <option>Transferencia</option>
                            <option>Efectivo</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Destino del Crédito</label>
                    <textarea name="destino" rows="3" class="w-full border rounded px-3 py-2"><?= htmlspecialchars($credito['destino_credito'] ?? '') ?></textarea>
                </div>
            </form>
        </div>
    </div>

    <!-- Simulación de Pagos -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold flex items-center">
                <i class="fas fa-calculator text-primary-800 mr-2"></i>Simulación de Pagos
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-1">Pago Mensual</p>
                    <p class="text-2xl font-bold text-blue-600">
                        $<?php 
                        $monto = $credito['monto_solicitado'] ?? 0;
                        $plazo = $credito['plazo_meses'] ?? 12;
                        $tasa = ($credito['tasa_interes_anual'] ?? 15) / 100 / 12;
                        $pagoMensual = $plazo > 0 ? $monto * ($tasa * pow(1 + $tasa, $plazo)) / (pow(1 + $tasa, $plazo) - 1) : 0;
                        echo number_format($pagoMensual, 2);
                        ?>
                    </p>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-1">Total a Pagar</p>
                    <p class="text-2xl font-bold text-green-600">$<?= number_format($pagoMensual * $plazo, 2) ?></p>
                </div>
                <div class="bg-yellow-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-1">Total Intereses</p>
                    <p class="text-2xl font-bold text-yellow-600">$<?= number_format(($pagoMensual * $plazo) - $monto, 2) ?></p>
                </div>
                <div class="bg-purple-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-1">CAT (%)</p>
                    <p class="text-2xl font-bold text-purple-600">
                        <?= number_format((($credito['tasa_interes_anual'] ?? 15) * 1.25), 2) ?>%
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pago</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capital</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Interés</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pago Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        $saldo = $monto;
                        for ($i = 1; $i <= min(5, $plazo); $i++):
                            $interes = $saldo * $tasa;
                            $capital = $pagoMensual - $interes;
                            $saldo -= $capital;
                            $fecha = date('d/m/Y', strtotime("+$i month"));
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm"><?= $i ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm"><?= $fecha ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">$<?= number_format($capital, 2) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">$<?= number_format($interes, 2) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">$<?= number_format($pagoMensual, 2) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">$<?= number_format($saldo, 2) ?></td>
                        </tr>
                        <?php endfor; ?>
                        <?php if ($plazo > 5): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                ... y <?= $plazo - 5 ?> pagos más
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Condiciones y Términos -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold flex items-center">
                <i class="fas fa-file-alt text-primary-800 mr-2"></i>Condiciones y Términos
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-gray-800">Requisitos de Aprobación</h3>
                        <p class="text-sm text-gray-600">El crédito está sujeto a aprobación según análisis crediticio y capacidad de pago.</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-gray-800">Garantías</h3>
                        <p class="text-sm text-gray-600">Pueden requerirse garantías o avales según el monto y perfil del solicitante.</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-gray-800">Seguros</h3>
                        <p class="text-sm text-gray-600">Se incluye seguro de vida y desempleo según política de la institución.</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-gray-800">Pagos Anticipados</h3>
                        <p class="text-sm text-gray-600">Se permite liquidación anticipada sin penalización.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="flex justify-between items-center">
        <button type="button" onclick="window.history.back()" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-times mr-2"></i>Cancelar
        </button>
        <div class="flex space-x-2">
            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">
                <i class="fas fa-envelope mr-2"></i>Enviar al Cliente
            </button>
            <button type="button" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded">
                <i class="fas fa-file-pdf mr-2"></i>Generar PDF
            </button>
            <button type="submit" class="bg-primary-800 hover:bg-primary-700 text-white px-6 py-2 rounded">
                <i class="fas fa-check mr-2"></i>Aprobar y Continuar
            </button>
        </div>
    </div>
</div>
