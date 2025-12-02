<?php
/**
 * Vista de Importación de Clientes desde Excel
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6">
    <a href="<?= url('importar') ?>" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Volver
    </a>
    <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
    <p class="text-gray-600">Importa clientes desde archivos Excel o CSV</p>
</div>

<?php if (!empty($errors)): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
        <ul class="list-disc list-inside">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
        <p><?= htmlspecialchars($success) ?></p>
        <?php if ($importacionId): ?>
            <a href="<?= url('importar/detalle/' . $importacionId) ?>" class="text-green-800 underline mt-2 inline-block">
                Ver detalles de la importación
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Formulario de importación -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-file-excel text-green-600 mr-2"></i>Importar desde Excel
        </h2>
        
        <form method="POST" action="<?= url('importar/clientes') ?>" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-500 transition-colors">
                <input type="file" id="archivo" name="archivo" accept=".xlsx,.xls,.csv" class="hidden" onchange="updateFileName(this)">
                <label for="archivo" class="cursor-pointer">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600 mb-2">Arrastra un archivo aquí o haz clic para seleccionar</p>
                    <p id="file-name" class="text-sm text-gray-500">Formatos aceptados: XLSX, XLS, CSV</p>
                </label>
            </div>
            
            <button type="submit" class="mt-4 w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                <i class="fas fa-upload mr-2"></i>Procesar Archivo
            </button>
        </form>
        
        <!-- Descargar plantilla -->
        <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <h3 class="text-sm font-medium text-blue-800 mb-2">
                <i class="fas fa-download mr-1"></i>Descargar Plantilla
            </h3>
            <p class="text-sm text-blue-700 mb-3">Descarga la plantilla de Excel con el formato correcto para importar clientes.</p>
            <a href="<?= url('importar/plantilla') ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                <i class="fas fa-file-excel mr-2"></i>Descargar Plantilla Excel
            </a>
        </div>
    </div>
    
    <!-- Instrucciones -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-info-circle text-blue-600 mr-2"></i>Instrucciones
        </h2>
        
        <div class="space-y-4">
            <div class="p-4 bg-gray-50 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Formato del archivo</h3>
                <p class="text-sm text-gray-600 mb-2">El archivo debe contener las siguientes columnas:</p>
                <ul class="text-sm text-gray-600 list-disc list-inside space-y-1">
                    <li><strong>nombre</strong> (requerido)</li>
                    <li><strong>apellido_paterno</strong> o <strong>apellidos</strong> (requerido)</li>
                    <li>apellido_materno</li>
                    <li>email o correo</li>
                    <li>telefono</li>
                    <li>celular o movil</li>
                    <li>rfc</li>
                    <li>curp</li>
                </ul>
            </div>
            
            <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                <h3 class="text-sm font-medium text-yellow-800 mb-2">
                    <i class="fas fa-exclamation-triangle mr-1"></i>Consideraciones
                </h3>
                <ul class="text-sm text-yellow-700 list-disc list-inside space-y-1">
                    <li>La primera fila debe contener los nombres de las columnas</li>
                    <li>El tamaño máximo del archivo es 5MB</li>
                    <li>Los registros con RFC o CURP duplicados serán marcados</li>
                    <li>Se creará automáticamente una cuenta de ahorro para cada socio</li>
                </ul>
            </div>
            
            <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                <h3 class="text-sm font-medium text-green-800 mb-2">
                    <i class="fas fa-check-circle mr-1"></i>Buenas prácticas
                </h3>
                <ul class="text-sm text-green-700 list-disc list-inside space-y-1">
                    <li>Descargue y utilice la plantilla proporcionada</li>
                    <li>Verifique los datos antes de importar</li>
                    <li>Procese archivos pequeños primero para pruebas</li>
                    <li>Revise el resultado de cada importación</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function updateFileName(input) {
    const fileName = input.files[0]?.name || 'Formatos aceptados: XLSX, XLS, CSV';
    document.getElementById('file-name').textContent = fileName;
}
</script>
