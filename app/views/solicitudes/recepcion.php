<?php
/**
 * Vista de Recepción de Solicitudes
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Recepción de Solicitudes</h1>
        <a href="<?= BASE_URL ?>/solicitudes" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Regresar
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="<?= BASE_URL ?>/solicitudes/recepcion" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Socio</label>
                    <select name="socio_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">Seleccionar socio</option>
                        <?php foreach ($socios as $socio): ?>
                        <option value="<?= $socio['id'] ?>">
                            <?= htmlspecialchars($socio['numero_socio'] . ' - ' . $socio['nombre'] . ' ' . $socio['apellido_paterno']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Producto Financiero</label>
                    <select name="producto_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">Seleccionar producto</option>
                        <?php foreach ($productos as $producto): ?>
                        <option value="<?= $producto['id'] ?>">
                            <?= htmlspecialchars($producto['nombre'] . ' (' . $producto['tipo'] . ')') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Monto Solicitado</label>
                    <input type="number" name="monto_solicitado" step="0.01" class="w-full border rounded px-3 py-2" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Plazo (meses)</label>
                    <input type="number" name="plazo" class="w-full border rounded px-3 py-2" required>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Destino del Crédito</label>
                    <textarea name="destino" rows="3" class="w-full border rounded px-3 py-2" required></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                    <textarea name="observaciones" rows="3" class="w-full border rounded px-3 py-2"></textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="<?= BASE_URL ?>/solicitudes" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded">
                    Cancelar
                </a>
                <button type="submit" class="bg-primary-800 hover:bg-primary-700 text-white px-6 py-2 rounded">
                    <i class="fas fa-save mr-2"></i>Registrar Solicitud
                </button>
            </div>
        </form>
    </div>
</div>
