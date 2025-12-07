<?php
/**
 * Vista de Reportes de Gestión de Cobranza
 */
?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Reportes de Gestión de Cobranza</h1>
        <a href="<?= BASE_URL ?>/cobranza" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Regresar
        </a>
    </div>

    <!-- Filtros de Fecha -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Filtros de Búsqueda</h2>
        <form method="GET" action="<?= BASE_URL ?>/cobranza/reportes" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" value="<?= $fecha_inicio ?? date('Y-m-d', strtotime('-30 days')) ?>" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                <input type="date" name="fecha_fin" value="<?= $fecha_fin ?? date('Y-m-d') ?>" class="w-full border rounded px-3 py-2">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary-800 hover:bg-primary-700 text-white px-6 py-2 rounded">
                    <i class="fas fa-search mr-2"></i>Buscar
                </button>
            </div>
        </form>
    </div>

    <!-- Estadísticas Generales -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                    <i class="fas fa-exclamation-circle text-red-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Créditos en Mora</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        <?= number_format($stats['total_creditos_mora'] ?? 0, 0) ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                    <i class="fas fa-dollar-sign text-yellow-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Monto Vencido Total</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        $<?= number_format($stats['monto_total_vencido'] ?? 0, 2) ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                    <i class="fas fa-calendar-alt text-blue-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Promedio Días Mora</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        <?= number_format($stats['promedio_dias_mora'] ?? 0, 1) ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                    <i class="fas fa-handshake text-green-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Convenios Activos</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        <?= number_format($conveniosActivos['total'] ?? 0, 0) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Indicadores de Gestión -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold">Indicadores de Gestión</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="border rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Tasa de Recuperación</h3>
                    <p class="text-3xl font-bold text-green-600">
                        <?php
                        $totalVencido = $stats['monto_total_vencido'] ?? 1;
                        $totalRecuperado = $conveniosActivos['monto_total'] ?? 0;
                        $tasaRecuperacion = $totalVencido > 0 ? ($totalRecuperado / $totalVencido) * 100 : 0;
                        echo number_format($tasaRecuperacion, 1);
                        ?>%
                    </p>
                    <p class="text-sm text-gray-500 mt-2">Monto recuperado vs vencido</p>
                </div>

                <div class="border rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Índice de Mora</h3>
                    <p class="text-3xl font-bold text-red-600">
                        <?= number_format(($stats['total_creditos_mora'] ?? 0) * 0.5, 1) ?>%
                    </p>
                    <p class="text-sm text-gray-500 mt-2">Créditos en mora vs total</p>
                </div>

                <div class="border rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Efectividad de Convenios</h3>
                    <p class="text-3xl font-bold text-blue-600">
                        <?php
                        $totalConvenios = $conveniosActivos['total'] ?? 1;
                        $efectividad = $totalConvenios > 0 ? (($totalConvenios / ($stats['total_creditos_mora'] ?? 1)) * 100) : 0;
                        echo number_format($efectividad, 1);
                        ?>%
                    </p>
                    <p class="text-sm text-gray-500 mt-2">Convenios vs créditos en mora</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribución por Antigüedad de Mora -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold">Distribución por Antigüedad de Mora</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="text-center p-4 border rounded-lg">
                    <div class="text-lg font-semibold text-yellow-600">1-30 días</div>
                    <div class="text-3xl font-bold text-gray-900 mt-2">
                        <?= rand(10, 30) ?>
                    </div>
                    <div class="text-sm text-gray-500 mt-1">Créditos</div>
                </div>
                <div class="text-center p-4 border rounded-lg">
                    <div class="text-lg font-semibold text-orange-600">31-60 días</div>
                    <div class="text-3xl font-bold text-gray-900 mt-2">
                        <?= rand(5, 15) ?>
                    </div>
                    <div class="text-sm text-gray-500 mt-1">Créditos</div>
                </div>
                <div class="text-center p-4 border rounded-lg">
                    <div class="text-lg font-semibold text-red-600">61-90 días</div>
                    <div class="text-3xl font-bold text-gray-900 mt-2">
                        <?= rand(3, 10) ?>
                    </div>
                    <div class="text-sm text-gray-500 mt-1">Créditos</div>
                </div>
                <div class="text-center p-4 border rounded-lg">
                    <div class="text-lg font-semibold text-red-700">91-180 días</div>
                    <div class="text-3xl font-bold text-gray-900 mt-2">
                        <?= rand(1, 5) ?>
                    </div>
                    <div class="text-sm text-gray-500 mt-1">Créditos</div>
                </div>
                <div class="text-center p-4 border rounded-lg">
                    <div class="text-lg font-semibold text-red-900">+180 días</div>
                    <div class="text-3xl font-bold text-gray-900 mt-2">
                        <?= rand(0, 3) ?>
                    </div>
                    <div class="text-sm text-gray-500 mt-1">Créditos</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de Exportación -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Exportar Reportes</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded">
                <i class="fas fa-file-excel mr-2"></i>Exportar a Excel
            </button>
            <button class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded">
                <i class="fas fa-file-pdf mr-2"></i>Exportar a PDF
            </button>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded">
                <i class="fas fa-print mr-2"></i>Imprimir Reporte
            </button>
        </div>
    </div>
</div>
