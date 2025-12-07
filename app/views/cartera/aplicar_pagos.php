<?php
/**
 * Vista de Aplicación de Pagos
 * Registro y aplicación de pagos a los créditos
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Aplicación de Pagos</h1>
        <button class="bg-primary-800 hover:bg-primary-700 text-white px-4 py-2 rounded">
            <i class="fas fa-plus mr-2"></i>Nuevo Pago
        </button>
    </div>

    <!-- Búsqueda de Crédito -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <h2 class="text-lg font-semibold mb-4">Buscar Crédito</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="text" placeholder="Número de crédito" class="border rounded px-3 py-2">
                <input type="text" placeholder="Número de socio" class="border rounded px-3 py-2">
                <button class="bg-primary-800 hover:bg-primary-700 text-white px-6 py-2 rounded">
                    <i class="fas fa-search mr-2"></i>Buscar
                </button>
            </div>
        </div>
    </div>

    <!-- Detalle del Crédito (si se encuentra) -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold">Detalle del Crédito</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div>
                    <label class="block text-sm text-gray-500 mb-1">Número de Crédito</label>
                    <p class="font-semibold">CRE-2024-001</p>
                </div>
                <div>
                    <label class="block text-sm text-gray-500 mb-1">Socio</label>
                    <p class="font-semibold">Juan Pérez</p>
                </div>
                <div>
                    <label class="block text-sm text-gray-500 mb-1">Saldo Actual</label>
                    <p class="font-semibold text-red-600">$25,000.00</p>
                </div>
                <div>
                    <label class="block text-sm text-gray-500 mb-1">Próximo Pago</label>
                    <p class="font-semibold">$2,500.00</p>
                </div>
            </div>

            <!-- Formulario de Pago -->
            <form method="POST" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Pago</label>
                        <input type="date" value="<?= date('Y-m-d') ?>" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Monto</label>
                        <input type="number" step="0.01" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Método de Pago</label>
                        <select class="w-full border rounded px-3 py-2">
                            <option>Efectivo</option>
                            <option>Transferencia</option>
                            <option>Cheque</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Referencia</label>
                    <input type="text" class="w-full border rounded px-3 py-2">
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-primary-800 hover:bg-primary-700 text-white px-6 py-2 rounded">
                        <i class="fas fa-save mr-2"></i>Aplicar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
