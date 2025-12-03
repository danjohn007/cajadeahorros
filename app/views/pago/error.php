<?php
/**
 * Vista de Error de Pago
 * Sistema de Gestión Integral de Caja de Ahorros
 */
$siteName = getSiteName();
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
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <i class="fas fa-exclamation-triangle text-6xl text-yellow-500 mb-6"></i>
            <h1 class="text-2xl font-bold text-gray-800 mb-4"><?= htmlspecialchars($pageTitle) ?></h1>
            <p class="text-gray-600 mb-6"><?= htmlspecialchars($mensaje) ?></p>
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
