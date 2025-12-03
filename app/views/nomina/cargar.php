<!-- Header -->
<div class="mb-6">
    <a href="<?= BASE_URL ?>/nomina" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Volver a Nómina
    </a>
    <h2 class="text-2xl font-bold text-gray-800">Cargar Archivo de Nómina</h2>
</div>

<!-- Errors -->
<?php if (!empty($errors)): ?>
<div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg">
    <ul class="list-disc list-inside">
        <?php foreach ($errors as $error): ?>
        <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<?php if (!empty($preview)): ?>
<!-- Preview Section -->
<div class="bg-white rounded-xl shadow-sm mb-6">
    <div class="p-6 border-b border-gray-200 bg-blue-50">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-blue-800">
                    <i class="fas fa-eye mr-2"></i> Vista Previa del Archivo
                </h3>
                <p class="text-sm text-blue-600 mt-1">
                    Archivo: <?= htmlspecialchars($preview['nombre_archivo']) ?> | 
                    Total: <?= number_format($preview['total_registros']) ?> registros
                </p>
            </div>
            <form method="POST" action="<?= BASE_URL ?>/nomina/cargar" class="inline">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="confirm_import" value="1">
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-check mr-2"></i> Confirmar e Importar
                </button>
            </form>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">#</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">RFC</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">CURP</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Nombre</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">No. Empleado</th>
                    <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">Monto</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Concepto</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($preview['registros'] as $index => $reg): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-500"><?= $index + 1 ?></td>
                    <td class="px-4 py-3 text-sm"><?= htmlspecialchars($reg['rfc'] ?? '-') ?></td>
                    <td class="px-4 py-3 text-sm"><?= htmlspecialchars($reg['curp'] ?? '-') ?></td>
                    <td class="px-4 py-3 text-sm font-medium"><?= htmlspecialchars($reg['nombre'] ?? '-') ?></td>
                    <td class="px-4 py-3 text-sm"><?= htmlspecialchars($reg['numero_empleado'] ?? '-') ?></td>
                    <td class="px-4 py-3 text-sm text-right font-medium text-green-600">$<?= number_format($reg['monto'] ?? 0, 2) ?></td>
                    <td class="px-4 py-3 text-sm"><?= htmlspecialchars($reg['concepto'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <?php if ($preview['total_registros'] > 50): ?>
    <div class="p-4 bg-yellow-50 border-t text-yellow-700 text-sm">
        <i class="fas fa-info-circle mr-1"></i>
        Mostrando los primeros 50 de <?= number_format($preview['total_registros']) ?> registros. 
        El total será importado al confirmar.
    </div>
    <?php endif; ?>
    
    <div class="p-6 bg-gray-50 flex justify-between">
        <a href="<?= BASE_URL ?>/nomina/cargar" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
            <i class="fas fa-times mr-2"></i> Cancelar y Subir Otro
        </a>
        <form method="POST" action="<?= BASE_URL ?>/nomina/cargar" class="inline">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="confirm_import" value="1">
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-check mr-2"></i> Confirmar e Importar <?= number_format($preview['total_registros']) ?> Registros
            </button>
        </form>
    </div>
</div>
<?php else: ?>
<!-- Upload Form -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form -->
    <div class="lg:col-span-2">
        <form method="POST" action="<?= BASE_URL ?>/nomina/cargar" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-file-upload text-orange-500 mr-2"></i> Seleccionar Archivo
                </h3>
                
                <div class="space-y-4">
                    <!-- Archivo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Archivo de Nómina *</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-orange-400 transition" 
                             id="dropZone">
                            <input type="file" name="archivo" id="archivo" required accept=".csv,.xlsx,.xls"
                                   class="hidden" onchange="updateFileName(this)">
                            <label for="archivo" class="cursor-pointer">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                <p class="text-gray-600">Arrastra el archivo aquí o <span class="text-orange-600">haz clic para seleccionar</span></p>
                                <p class="text-sm text-gray-400 mt-1">Formatos: CSV, Excel (.xlsx, .xls)</p>
                            </label>
                            <p id="fileName" class="mt-2 text-orange-600 font-medium hidden"></p>
                        </div>
                    </div>
                    
                    <!-- Periodo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Periodo</label>
                        <input type="text" name="periodo" placeholder="Ej: Quincena 1 - Enero 2024"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>
                    
                    <!-- Fecha de Nómina -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nómina</label>
                        <input type="date" name="fecha_nomina" value="<?= date('Y-m-d') ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>
            </div>
            
            <div class="p-6 bg-gray-50 flex justify-end space-x-4">
                <a href="<?= BASE_URL ?>/nomina" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                    Cancelar
                </a>
                <button type="submit" name="preview" value="1" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-eye mr-2"></i> Vista Previa
                </button>
                <button type="submit" class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                    <i class="fas fa-upload mr-2"></i> Cargar Directamente
                </button>
            </div>
        </form>
    </div>
    
    <!-- Info -->
    <div class="lg:col-span-1">
        <div class="bg-blue-50 rounded-xl p-6">
            <h3 class="font-semibold text-blue-800 mb-4">
                <i class="fas fa-info-circle mr-1"></i> Formato del Archivo
            </h3>
            <p class="text-sm text-blue-700 mb-4">El archivo debe contener las siguientes columnas en orden:</p>
            <ol class="text-sm text-blue-700 list-decimal list-inside space-y-1">
                <li>RFC</li>
                <li>CURP</li>
                <li>Nombre Completo</li>
                <li>Número de Empleado</li>
                <li>Monto de Descuento</li>
                <li>Concepto</li>
            </ol>
            <div class="mt-4 p-3 bg-blue-100 rounded-lg">
                <p class="text-xs text-blue-800">
                    <i class="fas fa-lightbulb mr-1"></i>
                    La primera fila debe contener los encabezados
                </p>
            </div>
        </div>
        
        <div class="bg-green-50 rounded-xl p-6 mt-4">
            <h3 class="font-semibold text-green-800 mb-4">
                <i class="fas fa-check-circle mr-1"></i> Vista Previa
            </h3>
            <p class="text-sm text-green-700">
                Use el botón "Vista Previa" para revisar los registros antes de importarlos al sistema.
            </p>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
function updateFileName(input) {
    const fileName = document.getElementById('fileName');
    if (input.files.length > 0) {
        fileName.textContent = input.files[0].name;
        fileName.classList.remove('hidden');
    } else {
        fileName.classList.add('hidden');
    }
}

// Drag and drop
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('archivo');

dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('border-orange-400', 'bg-orange-50');
});

dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('border-orange-400', 'bg-orange-50');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-orange-400', 'bg-orange-50');
    fileInput.files = e.dataTransfer.files;
    updateFileName(fileInput);
});
</script>
