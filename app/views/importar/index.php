<?php
/**
 * Vista de Importación de Clientes
 * Sistema de Gestión Integral de Caja de Ahorros
 */
?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
    <p class="text-gray-600">Importa clientes desde archivos Excel o CSV</p>
</div>

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
        
        <!-- Plantilla de ejemplo -->
        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Formato del archivo</h3>
            <p class="text-sm text-gray-600 mb-2">El archivo debe contener las siguientes columnas:</p>
            <ul class="text-sm text-gray-600 list-disc list-inside mb-4">
                <li><strong>nombre</strong> (requerido)</li>
                <li><strong>apellido_paterno</strong> o <strong>apellidos</strong> (requerido)</li>
                <li>apellido_materno</li>
                <li>email o correo</li>
                <li>telefono</li>
                <li>celular o movil</li>
                <li>rfc</li>
                <li>curp</li>
            </ul>
            <a href="<?= url('importar/plantilla') ?>" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                <i class="fas fa-download mr-2"></i>Descargar Plantilla
            </a>
        </div>
    </div>
    
    <!-- Últimas importaciones -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Últimas Importaciones</h2>
            <a href="<?= url('importar/historial') ?>" class="text-blue-600 hover:text-blue-800 text-sm">Ver historial</a>
        </div>
        
        <?php if (empty($importaciones)): ?>
            <p class="text-gray-500 text-center py-8">No hay importaciones registradas</p>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($importaciones as $imp): ?>
                    <div class="border rounded-lg p-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium text-gray-800"><?= htmlspecialchars($imp['nombre_archivo']) ?></p>
                                <p class="text-sm text-gray-500"><?= date('d/m/Y H:i', strtotime($imp['fecha_inicio'])) ?></p>
                            </div>
                            <?php
                            $statusColors = [
                                'completado' => 'green',
                                'parcial' => 'yellow',
                                'error' => 'red',
                                'procesando' => 'blue'
                            ];
                            $color = $statusColors[$imp['estatus']] ?? 'gray';
                            ?>
                            <span class="px-2 py-1 text-xs rounded-full bg-<?= $color ?>-100 text-<?= $color ?>-800">
                                <?= ucfirst($imp['estatus']) ?>
                            </span>
                        </div>
                        <div class="mt-2 flex text-sm text-gray-600 space-x-4">
                            <span><i class="fas fa-file mr-1"></i> <?= $imp['total_registros'] ?> total</span>
                            <span class="text-green-600"><i class="fas fa-check mr-1"></i> <?= $imp['registros_exitosos'] ?> OK</span>
                            <?php if ($imp['registros_error'] > 0): ?>
                                <span class="text-red-600"><i class="fas fa-times mr-1"></i> <?= $imp['registros_error'] ?> errores</span>
                            <?php endif; ?>
                        </div>
                        <div class="mt-2">
                            <a href="<?= url('importar/detalle/' . $imp['id']) ?>" class="text-blue-600 hover:text-blue-800 text-sm">
                                Ver detalles <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function updateFileName(input) {
    const fileName = input.files[0]?.name || 'Formatos aceptados: XLSX, XLS, CSV';
    document.getElementById('file-name').textContent = fileName;
}
</script>
