<?php
/**
 * Vista de Contratos y Pagarés
 * Generación y gestión de documentos legales del crédito
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Contratos y Pagarés</h1>
        <div class="flex space-x-2">
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                <i class="fas fa-file-pdf mr-2"></i>Generar Contrato
            </button>
            <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                <i class="fas fa-file-signature mr-2"></i>Generar Pagaré
            </button>
        </div>
    </div>

    <!-- Información del Crédito -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold">Información del Crédito</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm text-gray-500 mb-1">Número de Crédito</label>
                    <p class="font-semibold"><?= htmlspecialchars($credito['numero_credito'] ?? 'N/A') ?></p>
                </div>
                <div>
                    <label class="block text-sm text-gray-500 mb-1">Acreditado</label>
                    <p class="font-semibold"><?= htmlspecialchars(($credito['nombre'] ?? '') . ' ' . ($credito['apellido_paterno'] ?? '')) ?></p>
                </div>
                <div>
                    <label class="block text-sm text-gray-500 mb-1">Monto</label>
                    <p class="font-semibold text-green-600">$<?= number_format($credito['monto_solicitado'] ?? 0, 2) ?></p>
                </div>
                <div>
                    <label class="block text-sm text-gray-500 mb-1">Plazo</label>
                    <p class="font-semibold"><?= $credito['plazo_meses'] ?? 0 ?> meses</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Documentos Disponibles -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold">Documentos Generados</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <i class="fas fa-file-contract text-blue-600 text-3xl"></i>
                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Generado</span>
                    </div>
                    <h3 class="font-semibold mb-2">Contrato de Crédito</h3>
                    <p class="text-sm text-gray-600 mb-3">Contrato legal del crédito con términos y condiciones</p>
                    <div class="flex space-x-2">
                        <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm">
                            <i class="fas fa-eye mr-1"></i>Ver
                        </button>
                        <button class="flex-1 bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded text-sm">
                            <i class="fas fa-download mr-1"></i>Descargar
                        </button>
                    </div>
                </div>

                <div class="border rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <i class="fas fa-file-signature text-green-600 text-3xl"></i>
                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Generado</span>
                    </div>
                    <h3 class="font-semibold mb-2">Pagaré</h3>
                    <p class="text-sm text-gray-600 mb-3">Documento promesa de pago del crédito</p>
                    <div class="flex space-x-2">
                        <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm">
                            <i class="fas fa-eye mr-1"></i>Ver
                        </button>
                        <button class="flex-1 bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded text-sm">
                            <i class="fas fa-download mr-1"></i>Descargar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
