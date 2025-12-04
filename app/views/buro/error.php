<?php
/**
 * Vista de error para consulta al Buró de Crédito
 * Sistema de Gestión Integral de Caja de Ahorros
 */
$siteName = getSiteName();
$logoUrl = getLogo();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - <?= htmlspecialchars($siteName) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <?php if ($logoUrl): ?>
                <img src="<?= htmlspecialchars($logoUrl) ?>" alt="<?= htmlspecialchars($siteName) ?>" class="h-12 mx-auto mb-6">
            <?php endif; ?>
            
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-exclamation-triangle text-4xl text-red-500"></i>
            </div>
            
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Error</h1>
            <p class="text-gray-600 mb-6"><?= htmlspecialchars($mensaje ?? 'Ha ocurrido un error inesperado.') ?></p>
            
            <div class="space-y-3">
                <a href="<?= url('buro/consulta') ?>" class="block w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-redo mr-2"></i> Intentar Nuevamente
                </a>
                <a href="<?= url('') ?>" class="block w-full border border-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-home mr-2"></i> Ir al Inicio
                </a>
            </div>
        </div>
        
        <p class="text-center text-gray-500 text-sm mt-6">
            &copy; <?= date('Y') ?> <?= htmlspecialchars($siteName) ?>
        </p>
    </div>
</body>
</html>
