<?php
/**
 * Vista de Pago Cancelado
 * Sistema de Gestión Integral de Caja de Ahorros
 */
$siteName = getSiteName();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Cancelado - <?= htmlspecialchars($siteName) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-times text-4xl text-yellow-500"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Pago Cancelado</h1>
            <p class="text-gray-600 mb-6">El proceso de pago ha sido cancelado. No se realizó ningún cargo.</p>
            <div class="bg-yellow-50 rounded-lg p-4 mb-6">
                <p class="text-yellow-700">Si tienes dudas, contacta a la administración de la Caja de Ahorros.</p>
            </div>
            <a href="<?= BASE_URL ?>" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-home mr-2"></i>Ir al Inicio
            </a>
        </div>
        <p class="text-center text-gray-500 text-sm mt-6">
            &copy; <?= date('Y') ?> <?= htmlspecialchars($siteName) ?>
        </p>
    </div>
</body>
</html>
<?php exit; ?>
