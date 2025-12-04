<?php
/**
 * Vista de resultados de consulta al Buró de Crédito
 * Sistema de Gestión Integral de Caja de Ahorros
 */
$siteName = getSiteName();
$logoUrl = getLogo();

// Determinar color del score
$score = $consulta['resultado_score'] ?? 0;
$scoreColor = 'gray';
$scoreLabel = 'Sin Información';
if ($score > 0) {
    if ($score >= 720) {
        $scoreColor = 'green';
        $scoreLabel = 'Excelente';
    } elseif ($score >= 650) {
        $scoreColor = 'blue';
        $scoreLabel = 'Bueno';
    } elseif ($score >= 580) {
        $scoreColor = 'yellow';
        $scoreLabel = 'Regular';
    } else {
        $scoreColor = 'red';
        $scoreLabel = 'Bajo';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - <?= htmlspecialchars($siteName) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-8">
            <?php if ($logoUrl): ?>
                <img src="<?= htmlspecialchars($logoUrl) ?>" alt="<?= htmlspecialchars($siteName) ?>" class="h-12 mx-auto mb-4">
            <?php endif; ?>
            <h1 class="text-2xl font-bold text-gray-800">Resultado de Consulta al Buró de Crédito</h1>
            <p class="text-gray-600">Consulta realizada el <?= date('d/m/Y H:i', strtotime($consulta['fecha_consulta'] ?? $consulta['created_at'])) ?></p>
        </div>
        
        <?php if ($consulta['estatus'] === 'error'): ?>
        <!-- Error -->
        <div class="bg-red-50 border border-red-200 rounded-xl p-8 text-center">
            <i class="fas fa-times-circle text-6xl text-red-500 mb-4"></i>
            <h2 class="text-xl font-semibold text-red-800 mb-2">Error en la Consulta</h2>
            <p class="text-red-700"><?= htmlspecialchars($consulta['error_mensaje'] ?? 'Ocurrió un error al procesar la consulta') ?></p>
            <a href="<?= url('buro/consulta') ?>" class="inline-block mt-4 text-blue-600 hover:underline">
                <i class="fas fa-redo mr-1"></i> Intentar nuevamente
            </a>
        </div>
        
        <?php elseif ($consulta['estatus'] === 'consultado' && $resultado): ?>
        
        <!-- Score Principal -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
            <div class="grid md:grid-cols-3 gap-8 items-center">
                <!-- Score Gauge -->
                <div class="text-center">
                    <div class="relative inline-flex items-center justify-center">
                        <svg class="w-40 h-40">
                            <circle cx="80" cy="80" r="70" fill="none" stroke="#e5e7eb" stroke-width="12"/>
                            <circle cx="80" cy="80" r="70" fill="none" stroke="<?= $scoreColor === 'green' ? '#22c55e' : ($scoreColor === 'blue' ? '#3b82f6' : ($scoreColor === 'yellow' ? '#eab308' : ($scoreColor === 'red' ? '#ef4444' : '#9ca3af'))) ?>" 
                                    stroke-width="12" stroke-linecap="round"
                                    stroke-dasharray="<?= ($score / 850) * 440 ?> 440"
                                    transform="rotate(-90 80 80)"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-4xl font-bold text-<?= $scoreColor ?>-600"><?= $score ?></span>
                            <span class="text-sm text-gray-500">de 850</span>
                        </div>
                    </div>
                    <p class="mt-2 text-lg font-semibold text-<?= $scoreColor ?>-600"><?= $scoreLabel ?></p>
                </div>
                
                <!-- Información Principal -->
                <div class="md:col-span-2">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">
                        <?= htmlspecialchars($consulta['nombre_consultado'] ?? $resultado['nombre_completo'] ?? 'N/A') ?>
                    </h2>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm text-gray-500">Identificador</label>
                            <p class="font-medium font-mono"><?= htmlspecialchars($consulta['identificador']) ?></p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Tipo de Consulta</label>
                            <p class="font-medium"><?= strtoupper($consulta['tipo_consulta']) ?></p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Nivel de Riesgo</label>
                            <p class="font-medium capitalize"><?= htmlspecialchars($resultado['nivel_riesgo'] ?? 'N/A') ?></p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Historial</label>
                            <p class="font-medium"><?= $resultado['historial_meses'] ?? 0 ?> meses</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Detalles -->
        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <!-- Cuentas -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-credit-card mr-2 text-blue-600"></i>Cuentas de Crédito
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">Cuentas Activas</span>
                        <span class="text-2xl font-bold text-blue-600"><?= $resultado['cuentas_activas'] ?? 0 ?></span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">Cuentas Cerradas</span>
                        <span class="text-2xl font-bold text-gray-600"><?= $resultado['cuentas_cerradas'] ?? 0 ?></span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">Créditos Vigentes</span>
                        <span class="text-2xl font-bold text-green-600"><?= $resultado['creditos_vigentes'] ?? 0 ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Comportamiento -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-chart-line mr-2 text-green-600"></i>Comportamiento de Pago
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">Pagos Puntuales</span>
                        <span class="text-2xl font-bold text-green-600"><?= $resultado['pagos_puntuales'] ?? '0%' ?></span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">Deuda Estimada</span>
                        <span class="text-2xl font-bold text-gray-800">$<?= number_format($resultado['monto_total_deuda'] ?? 0, 0) ?></span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">Consultas Recientes</span>
                        <span class="text-2xl font-bold text-yellow-600"><?= $resultado['consultas_recientes'] ?? 0 ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Escala de Score -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-tachometer-alt mr-2 text-purple-600"></i>Interpretación del Score
            </h3>
            <div class="relative pt-1">
                <div class="flex mb-2 items-center justify-between text-xs">
                    <span class="text-red-600 font-semibold">300 - Muy Bajo</span>
                    <span class="text-yellow-600 font-semibold">580 - Regular</span>
                    <span class="text-blue-600 font-semibold">650 - Bueno</span>
                    <span class="text-green-600 font-semibold">720+ - Excelente</span>
                </div>
                <div class="flex rounded-full h-4 bg-gradient-to-r from-red-500 via-yellow-500 via-blue-500 to-green-500">
                    <div class="relative w-full">
                        <div class="absolute bg-white w-4 h-4 rounded-full border-2 border-gray-800 shadow-lg"
                             style="left: <?= min(100, max(0, (($score - 300) / 550) * 100)) ?>%; transform: translateX(-50%);"></div>
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <p class="text-sm text-gray-600">
                        Tu score de <strong><?= $score ?></strong> se considera <strong class="text-<?= $scoreColor ?>-600"><?= $scoreLabel ?></strong>.
                        <?php if ($score >= 720): ?>
                        Tienes un excelente historial crediticio.
                        <?php elseif ($score >= 650): ?>
                        Tienes un buen historial, pero hay espacio para mejorar.
                        <?php elseif ($score >= 580): ?>
                        Tu historial es regular. Considera mejorar tus hábitos de pago.
                        <?php else: ?>
                        Tu score necesita atención. Revisa tu historial de pagos.
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
        
        <?php if (isset($resultado['mensaje'])): ?>
        <!-- Mensaje del Sistema -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-yellow-500 mt-1 mr-3"></i>
                <p class="text-sm text-yellow-700"><?= htmlspecialchars($resultado['mensaje']) ?></p>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Acciones -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 flex items-center justify-center">
                <i class="fas fa-print mr-2"></i> Imprimir Reporte
            </button>
            <a href="<?= url('buro/consulta') ?>" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 flex items-center justify-center">
                <i class="fas fa-redo mr-2"></i> Nueva Consulta
            </a>
        </div>
        
        <?php else: ?>
        <!-- Estado Pendiente -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
            <i class="fas fa-clock text-6xl text-yellow-500 mb-4"></i>
            <h2 class="text-xl font-semibold text-yellow-800 mb-2">Consulta en Proceso</h2>
            <p class="text-yellow-700">Tu consulta está siendo procesada. Por favor espera unos momentos.</p>
        </div>
        <?php endif; ?>
        
        <!-- Footer -->
        <footer class="text-center mt-8 text-sm text-gray-500">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($siteName) ?>. Todos los derechos reservados.</p>
            <p class="mt-1">Este reporte es únicamente informativo y no constituye una oferta de crédito.</p>
        </footer>
    </div>
    
    <style>
        @media print {
            body { background: white !important; }
            .no-print { display: none !important; }
        }
    </style>
</body>
</html>
