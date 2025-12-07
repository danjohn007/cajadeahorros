<?php
// Get system configuration for colors and branding
$systemColors = getSystemColors();
$siteName = getSiteName();
$logoUrl = getLogo();
$colorPrimario = $systemColors['color_primario'];
$colorSecundario = $systemColors['color_secundario'];
$colorAcento = $systemColors['color_acento'];
$textoCopyright = getConfig('texto_copyright', '© ' . date('Y') . ' ' . APP_NAME . '. Todos los derechos reservados.');

// Get disabled modules from configuration
$modulosDeshabilitadosJson = getConfig('modulos_deshabilitados', '[]');
$modulosDeshabilitados = json_decode($modulosDeshabilitadosJson, true) ?: [];

// Get user avatar for top navigation
$userAvatar = '';
if (isset($_SESSION['user_id'])) {
    $db = Database::getInstance();
    $userInfo = $db->fetch("SELECT avatar FROM usuarios WHERE id = :id", ['id' => $_SESSION['user_id']]);
    $userAvatar = $userInfo['avatar'] ?? '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? $siteName ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '<?= adjustColor($colorPrimario, 0.95) ?>',
                            100: '<?= adjustColor($colorPrimario, 0.9) ?>',
                            200: '<?= adjustColor($colorPrimario, 0.8) ?>',
                            300: '<?= adjustColor($colorPrimario, 0.6) ?>',
                            400: '<?= adjustColor($colorPrimario, 0.4) ?>',
                            500: '<?= $colorSecundario ?>',
                            600: '<?= adjustColor($colorPrimario, 0.1) ?>',
                            700: '<?= adjustColor($colorPrimario, 0.05) ?>',
                            800: '<?= $colorPrimario ?>',
                            900: '<?= adjustColor($colorPrimario, -0.1) ?>',
                        },
                        accent: '<?= $colorAcento ?>'
                    }
                }
            }
        }
    </script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- FullCalendar -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-link:hover { background-color: rgba(255, 255, 255, 0.1); }
        .sidebar-link.active { background-color: rgba(255, 255, 255, 0.2); border-left: 4px solid #fff; }
    </style>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen" x-data="{ sidebarOpen: true, dropdownOpen: false }">
    
    <?php if (isset($_SESSION['user_id'])): ?>
    <!-- Layout con Sidebar -->
    <div class="flex min-h-screen">
        
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'" 
               class="bg-primary-800 text-white transition-all duration-300 fixed h-full z-30 flex flex-col">
            
            <!-- Logo -->
            <div class="flex items-center justify-between p-4 border-b border-primary-700 flex-shrink-0">
                <div class="flex items-center space-x-3" x-show="sidebarOpen">
                    <?php if ($logoUrl): ?>
                        <img src="<?= htmlspecialchars($logoUrl) ?>" alt="<?= htmlspecialchars($siteName) ?>" class="h-8 w-auto">
                    <?php else: ?>
                        <i class="fas fa-piggy-bank text-2xl"></i>
                    <?php endif; ?>
                    <span class="font-bold text-lg"><?= htmlspecialchars($siteName) ?></span>
                </div>
                <button @click="sidebarOpen = !sidebarOpen" class="text-white hover:text-gray-300">
                    <i :class="sidebarOpen ? 'fa-chevron-left' : 'fa-chevron-right'" class="fas"></i>
                </button>
            </div>
            
            <!-- Navigation with scroll -->
            <nav class="mt-4 flex-1 overflow-y-auto pb-4">
                <?php if ($_SESSION['user_role'] === 'cliente'): ?>
                <!-- Menú para CLIENTE - Solo acceso a su portal -->
                <a href="<?= BASE_URL ?>/cliente" 
                   class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'cliente') !== false && strpos($_SERVER['REQUEST_URI'], 'cliente/') === false ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt w-6"></i>
                    <span class="ml-3" x-show="sidebarOpen">Mi Portal</span>
                </a>
                
                <a href="<?= BASE_URL ?>/cliente/estado-cuenta" 
                   class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'cliente/estado-cuenta') !== false ? 'active' : '' ?>">
                    <i class="fas fa-file-invoice w-6"></i>
                    <span class="ml-3" x-show="sidebarOpen">Estado de Cuenta</span>
                </a>
                
                <a href="<?= BASE_URL ?>/cliente/cuenta" 
                   class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'cliente/cuenta') !== false ? 'active' : '' ?>">
                    <i class="fas fa-wallet w-6"></i>
                    <span class="ml-3" x-show="sidebarOpen">Mi Cuenta de Ahorro</span>
                </a>
                
                <a href="<?= BASE_URL ?>/cliente/creditos" 
                   class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'cliente/credito') !== false ? 'active' : '' ?>">
                    <i class="fas fa-credit-card w-6"></i>
                    <span class="ml-3" x-show="sidebarOpen">Mis Créditos</span>
                </a>
                
                <a href="<?= BASE_URL ?>/cliente/pagar" 
                   class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'cliente/pagar') !== false ? 'active' : '' ?>">
                    <i class="fas fa-money-bill-wave w-6"></i>
                    <span class="ml-3" x-show="sidebarOpen">Realizar Pago</span>
                </a>
                
                <div class="border-t border-primary-700 mt-4 pt-4">
                    <a href="<?= BASE_URL ?>/usuarios/perfil" 
                       class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'usuarios/perfil') !== false ? 'active' : '' ?>">
                        <i class="fas fa-user-cog w-6"></i>
                        <span class="ml-3" x-show="sidebarOpen">Mi Perfil</span>
                    </a>
                </div>
                <?php else: ?>
                <!-- Menú para usuarios administrativos (administrador, operativo, consulta) -->
                <a href="<?= BASE_URL ?>/dashboard" 
                   class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt w-6"></i>
                    <span class="ml-3" x-show="sidebarOpen">Dashboard</span>
                </a>
                
                <a href="<?= BASE_URL ?>/socios" 
                   class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'socios') !== false ? 'active' : '' ?>">
                    <i class="fas fa-users w-6"></i>
                    <span class="ml-3" x-show="sidebarOpen">Socios</span>
                </a>
                
                <a href="<?= BASE_URL ?>/ahorro" 
                   class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'ahorro') !== false ? 'active' : '' ?>">
                    <i class="fas fa-wallet w-6"></i>
                    <span class="ml-3" x-show="sidebarOpen">Ahorro</span>
                </a>
                
                <!-- SOLICITUDES -->
                <div x-data="{ open: <?= strpos($_SERVER['REQUEST_URI'], 'solicitudes') !== false ? 'true' : 'false' ?> }">
                    <button @click="open = !open" class="sidebar-link flex items-center justify-between w-full px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'solicitudes') !== false ? 'active' : '' ?>">
                        <div class="flex items-center">
                            <i class="fas fa-file-alt w-6"></i>
                            <span class="ml-3" x-show="sidebarOpen">Solicitudes</span>
                        </div>
                        <i :class="open ? 'fa-chevron-down' : 'fa-chevron-right'" class="fas text-xs" x-show="sidebarOpen"></i>
                    </button>
                    <div x-show="open && sidebarOpen" x-collapse class="bg-primary-900">
                        <a href="<?= BASE_URL ?>/solicitudes/recepcion" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Recepción de Solicitudes</a>
                        <a href="<?= BASE_URL ?>/solicitudes" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Captura y Verificación</a>
                        <a href="<?= BASE_URL ?>/solicitudes" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Evaluación Preliminar</a>
                        <a href="<?= BASE_URL ?>/solicitudes/expedientes" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Gestión de Expedientes</a>
                        <a href="<?= BASE_URL ?>/solicitudes/asignacion" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Asignación Fuerza de Ventas</a>
                    </div>
                </div>
                
                <!-- CRÉDITOS -->
                <div x-data="{ open: <?= strpos($_SERVER['REQUEST_URI'], 'creditos') !== false ? 'true' : 'false' ?> }">
                    <button @click="open = !open" class="sidebar-link flex items-center justify-between w-full px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'creditos') !== false ? 'active' : '' ?>">
                        <div class="flex items-center">
                            <i class="fas fa-hand-holding-usd w-6"></i>
                            <span class="ml-3" x-show="sidebarOpen">Créditos</span>
                        </div>
                        <i :class="open ? 'fa-chevron-down' : 'fa-chevron-right'" class="fas text-xs" x-show="sidebarOpen"></i>
                    </button>
                    <div x-show="open && sidebarOpen" x-collapse class="bg-primary-900">
                        <a href="<?= BASE_URL ?>/creditos" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Generación de Propuestas</a>
                        <a href="<?= BASE_URL ?>/creditos/comite" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Comité de Crédito</a>
                        <a href="<?= BASE_URL ?>/creditos" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Autorización y Rechazo</a>
                        <a href="<?= BASE_URL ?>/creditos/motor-reglas" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Motor de Reglas</a>
                        <a href="<?= BASE_URL ?>/creditos" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Garantías y Avales</a>
                        <a href="<?= BASE_URL ?>/creditos" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Ejecución de Políticas</a>
                    </div>
                </div>
                
                <!-- DISPERSIÓN -->
                <div x-data="{ open: <?= strpos($_SERVER['REQUEST_URI'], 'dispersion') !== false ? 'true' : 'false' ?> }">
                    <button @click="open = !open" class="sidebar-link flex items-center justify-between w-full px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'dispersion') !== false ? 'active' : '' ?>">
                        <div class="flex items-center">
                            <i class="fas fa-money-check-alt w-6"></i>
                            <span class="ml-3" x-show="sidebarOpen">Dispersión</span>
                        </div>
                        <i :class="open ? 'fa-chevron-down' : 'fa-chevron-right'" class="fas text-xs" x-show="sidebarOpen"></i>
                    </button>
                    <div x-show="open && sidebarOpen" x-collapse class="bg-primary-900">
                        <a href="<?= BASE_URL ?>/dispersion" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Registro de Créditos</a>
                        <a href="<?= BASE_URL ?>/dispersion" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Proceso de Formalización</a>
                        <a href="<?= BASE_URL ?>/dispersion" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Contratos y Pagarés</a>
                        <a href="<?= BASE_URL ?>/dispersion" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Hoja de Garantías</a>
                        <a href="<?= BASE_URL ?>/dispersion/coordinacion" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Coordinación de Dispersión</a>
                    </div>
                </div>
                
                <a href="<?= BASE_URL ?>/nomina" 
                   class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'nomina') !== false ? 'active' : '' ?>">
                    <i class="fas fa-file-invoice-dollar w-6"></i>
                    <span class="ml-3" x-show="sidebarOpen">Nómina</span>
                </a>
                
                <!-- CARTERA -->
                <div x-data="{ open: <?= strpos($_SERVER['REQUEST_URI'], 'cartera') !== false ? 'true' : 'false' ?> }">
                    <button @click="open = !open" class="sidebar-link flex items-center justify-between w-full px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'cartera') !== false ? 'active' : '' ?>">
                        <div class="flex items-center">
                            <i class="fas fa-chart-pie w-6"></i>
                            <span class="ml-3" x-show="sidebarOpen">Cartera</span>
                        </div>
                        <i :class="open ? 'fa-chevron-down' : 'fa-chevron-right'" class="fas text-xs" x-show="sidebarOpen"></i>
                    </button>
                    <div x-show="open && sidebarOpen" x-collapse class="bg-primary-900">
                        <a href="<?= BASE_URL ?>/cartera" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Aplicación de Pagos</a>
                        <a href="<?= BASE_URL ?>/cartera/vigente" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Carteras Vigentes</a>
                        <a href="<?= BASE_URL ?>/cartera" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Estados de Cuenta</a>
                        <a href="<?= BASE_URL ?>/cartera/gestion-vencida" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Cartera Vencida</a>
                        <a href="<?= BASE_URL ?>/cartera/prepagos" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Prepagos y Liquidaciones</a>
                        <a href="<?= BASE_URL ?>/cartera/traspasos" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Traspasos de Cartera</a>
                    </div>
                </div>
                
                <!-- COBRANZA -->
                <div x-data="{ open: <?= strpos($_SERVER['REQUEST_URI'], 'cobranza') !== false ? 'true' : 'false' ?> }">
                    <button @click="open = !open" class="sidebar-link flex items-center justify-between w-full px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'cobranza') !== false ? 'active' : '' ?>">
                        <div class="flex items-center">
                            <i class="fas fa-phone-volume w-6"></i>
                            <span class="ml-3" x-show="sidebarOpen">Cobranza</span>
                        </div>
                        <i :class="open ? 'fa-chevron-down' : 'fa-chevron-right'" class="fas text-xs" x-show="sidebarOpen"></i>
                    </button>
                    <div x-show="open && sidebarOpen" x-collapse class="bg-primary-900">
                        <a href="<?= BASE_URL ?>/cobranza/estrategias" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Estrategias de Cobranza</a>
                        <a href="<?= BASE_URL ?>/cobranza/agentes" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Agentes de Cobranza</a>
                        <a href="<?= BASE_URL ?>/cobranza/convenios" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Convenios de Pago</a>
                        <a href="<?= BASE_URL ?>/cobranza/liquidaciones" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Liquidaciones</a>
                        <a href="<?= BASE_URL ?>/cobranza/reportes" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Reportes de Gestión</a>
                        <a href="<?= BASE_URL ?>/cobranza/compromisos" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Compromisos de Pago</a>
                    </div>
                </div>
                
                <a href="<?= BASE_URL ?>/reportes" 
                   class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'reportes') !== false ? 'active' : '' ?>">
                    <i class="fas fa-file-alt w-6"></i>
                    <span class="ml-3" x-show="sidebarOpen">Reportes</span>
                </a>
                
                <!-- PRODUCTOS FINANCIEROS -->
                <?php if ($_SESSION['user_role'] === 'administrador' || $_SESSION['user_role'] === 'operativo'): ?>
                <div x-data="{ open: <?= strpos($_SERVER['REQUEST_URI'], 'productos-financieros') !== false ? 'true' : 'false' ?> }">
                    <button @click="open = !open" class="sidebar-link flex items-center justify-between w-full px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'productos-financieros') !== false ? 'active' : '' ?>">
                        <div class="flex items-center">
                            <i class="fas fa-box-open w-6"></i>
                            <span class="ml-3" x-show="sidebarOpen">Productos Financieros</span>
                        </div>
                        <i :class="open ? 'fa-chevron-down' : 'fa-chevron-right'" class="fas text-xs" x-show="sidebarOpen"></i>
                    </button>
                    <div x-show="open && sidebarOpen" x-collapse class="bg-primary-900">
                        <a href="<?= BASE_URL ?>/productos-financieros/creditos" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Configuración de Créditos</a>
                        <a href="<?= BASE_URL ?>/productos-financieros/tasas" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Tasas y Comisiones</a>
                        <a href="<?= BASE_URL ?>/productos-financieros/plazos" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Plazos y Condiciones</a>
                        <a href="<?= BASE_URL ?>/productos-financieros/amortizacion" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Esquemas de Amortización</a>
                        <a href="<?= BASE_URL ?>/productos-financieros/beneficios" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Beneficios y Promociones</a>
                    </div>
                </div>
                
                <!-- ENTIDADES -->
                <div x-data="{ open: <?= strpos($_SERVER['REQUEST_URI'], 'entidades') !== false ? 'true' : 'false' ?> }">
                    <button @click="open = !open" class="sidebar-link flex items-center justify-between w-full px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'entidades') !== false ? 'active' : '' ?>">
                        <div class="flex items-center">
                            <i class="fas fa-building w-6"></i>
                            <span class="ml-3" x-show="sidebarOpen">Entidades</span>
                        </div>
                        <i :class="open ? 'fa-chevron-down' : 'fa-chevron-right'" class="fas text-xs" x-show="sidebarOpen"></i>
                    </button>
                    <div x-show="open && sidebarOpen" x-collapse class="bg-primary-900">
                        <a href="<?= BASE_URL ?>/entidades/empresas" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Empresas del Grupo</a>
                        <a href="<?= BASE_URL ?>/entidades/unidades" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Unidades de Negocio</a>
                        <a href="<?= BASE_URL ?>/entidades/catalogos" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Catálogos Corporativos</a>
                        <a href="<?= BASE_URL ?>/entidades/politicas" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Políticas Institucionales</a>
                        <a href="<?= BASE_URL ?>/entidades/reportes" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Estructura Organizacional</a>
                    </div>
                </div>
                
                <!-- TESORERÍA -->
                <div x-data="{ open: <?= strpos($_SERVER['REQUEST_URI'], 'tesoreria') !== false ? 'true' : 'false' ?> }">
                    <button @click="open = !open" class="sidebar-link flex items-center justify-between w-full px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'tesoreria') !== false ? 'active' : '' ?>">
                        <div class="flex items-center">
                            <i class="fas fa-money-bill-trend-up w-6"></i>
                            <span class="ml-3" x-show="sidebarOpen">Tesorería</span>
                        </div>
                        <i :class="open ? 'fa-chevron-down' : 'fa-chevron-right'" class="fas text-xs" x-show="sidebarOpen"></i>
                    </button>
                    <div x-show="open && sidebarOpen" x-collapse class="bg-primary-900">
                        <a href="<?= BASE_URL ?>/tesoreria" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Proyección de Flujos</a>
                        <a href="<?= BASE_URL ?>/tesoreria" class="block px-8 py-2 text-sm text-gray-300 hover:bg-primary-700">Cálculo Capital e Intereses</a>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($_SESSION['user_role'] === 'administrador'): ?>
                <a href="<?= BASE_URL ?>/cnbv" 
                   class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'cnbv') !== false ? 'active' : '' ?>">
                    <i class="fas fa-file-shield w-6"></i>
                    <span class="ml-3" x-show="sidebarOpen">Reportes CNBV</span>
                </a>
                <?php endif; ?>
                
                <!-- Nuevos Módulos (controlados por configuración) -->
                <?php if (isModuloEnabled('financiero', $modulosDeshabilitados, $_SESSION['user_role'])): ?>
                <a href="<?= BASE_URL ?>/financiero" 
                   class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'financiero') !== false ? 'active' : '' ?>">
                    <i class="fas fa-coins w-6"></i>
                    <span class="ml-3" x-show="sidebarOpen">Módulo Financiero</span>
                </a>
                <?php endif; ?>
                
                <?php if (isModuloEnabled('membresias', $modulosDeshabilitados, $_SESSION['user_role'])): ?>
                <a href="<?= BASE_URL ?>/membresias" 
                   class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'membresias') !== false ? 'active' : '' ?>">
                    <i class="fas fa-id-card w-6"></i>
                    <span class="ml-3" x-show="sidebarOpen">Membresías</span>
                </a>
                <?php endif; ?>
                
                <?php if (isModuloEnabled('inversionistas', $modulosDeshabilitados, $_SESSION['user_role'])): ?>
                <a href="<?= BASE_URL ?>/inversionistas" 
                   class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'inversionistas') !== false ? 'active' : '' ?>">
                    <i class="fas fa-chart-line w-6"></i>
                    <span class="ml-3" x-show="sidebarOpen">Inversionistas</span>
                </a>
                <?php endif; ?>
                
                <?php if (isModuloEnabled('crm', $modulosDeshabilitados, $_SESSION['user_role'])): ?>
                <a href="<?= BASE_URL ?>/crm" 
                   class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'crm') !== false ? 'active' : '' ?>">
                    <i class="fas fa-address-book w-6"></i>
                    <span class="ml-3" x-show="sidebarOpen">Informe CRM</span>
                </a>
                <?php endif; ?>
                
                <?php if (isModuloEnabled('kyc', $modulosDeshabilitados, $_SESSION['user_role'])): ?>
                <a href="<?= BASE_URL ?>/kyc" 
                   class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'kyc') !== false ? 'active' : '' ?>">
                    <i class="fas fa-user-shield w-6"></i>
                    <span class="ml-3" x-show="sidebarOpen">Sistema KYC</span>
                </a>
                <?php endif; ?>
                
                <?php if (isModuloEnabled('escrow', $modulosDeshabilitados, $_SESSION['user_role'])): ?>
                <a href="<?= BASE_URL ?>/escrow" 
                   class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'escrow') !== false ? 'active' : '' ?>">
                    <i class="fas fa-handshake w-6"></i>
                    <span class="ml-3" x-show="sidebarOpen">Sistema ESCROW</span>
                </a>
                <?php endif; ?>
                
                <?php if ($_SESSION['user_role'] === 'administrador' || $_SESSION['user_role'] === 'programador'): ?>
                <div class="border-t border-primary-700 mt-4 pt-4">
                    <a href="<?= BASE_URL ?>/importar" 
                       class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'importar') !== false ? 'active' : '' ?>">
                        <i class="fas fa-file-import w-6"></i>
                        <span class="ml-3" x-show="sidebarOpen">Importar Clientes</span>
                    </a>
                    
                    <a href="<?= BASE_URL ?>/dispositivos" 
                       class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'dispositivos') !== false ? 'active' : '' ?>">
                        <i class="fas fa-microchip w-6"></i>
                        <span class="ml-3" x-show="sidebarOpen">Dispositivos IoT</span>
                    </a>
                    
                    <a href="<?= BASE_URL ?>/auditoria" 
                       class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'auditoria') !== false ? 'active' : '' ?>">
                        <i class="fas fa-shield-alt w-6"></i>
                        <span class="ml-3" x-show="sidebarOpen">Auditoría</span>
                    </a>
                    
                    <a href="<?= BASE_URL ?>/usuarios" 
                       class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'usuarios') !== false ? 'active' : '' ?>">
                        <i class="fas fa-user-cog w-6"></i>
                        <span class="ml-3" x-show="sidebarOpen">Usuarios</span>
                    </a>
                    
                    <a href="<?= BASE_URL ?>/configuraciones" 
                       class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'configuraciones') !== false ? 'active' : '' ?>">
                        <i class="fas fa-cog w-6"></i>
                        <span class="ml-3" x-show="sidebarOpen">Configuraciones</span>
                    </a>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <div :class="sidebarOpen ? 'ml-64' : 'ml-20'" class="flex-1 transition-all duration-300">
            
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm sticky top-0 z-20">
                <div class="flex items-center justify-between px-6 py-4">
                    <div>
                        <h1 class="text-xl font-semibold text-gray-800"><?= $pageTitle ?? 'Dashboard' ?></h1>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <div class="relative" x-data="notificaciones()">
                            <button @click="toggle()" class="relative text-gray-600 hover:text-gray-800">
                                <i class="fas fa-bell text-xl"></i>
                                <span x-show="noLeidas > 0" x-text="noLeidas" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"></span>
                            </button>
                            
                            <!-- Dropdown de notificaciones -->
                            <div x-show="open" @click.away="open = false" x-cloak
                                 class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg py-2 z-50 max-h-96 overflow-y-auto">
                                <div class="px-4 py-2 border-b flex justify-between items-center">
                                    <span class="font-semibold text-gray-700">Notificaciones</span>
                                    <button @click="marcarTodasLeidas()" x-show="noLeidas > 0" class="text-xs text-blue-600 hover:text-blue-800">
                                        Marcar todas como leídas
                                    </button>
                                </div>
                                <template x-if="notificaciones.length === 0">
                                    <div class="px-4 py-8 text-center text-gray-500">
                                        <i class="fas fa-bell-slash text-3xl mb-2"></i>
                                        <p>No hay notificaciones</p>
                                    </div>
                                </template>
                                <template x-for="n in notificaciones" :key="n.id">
                                    <a :href="n.url ? '<?= BASE_URL ?>/' + n.url : '#'" 
                                       @click="marcarLeida(n.id)"
                                       :class="{'bg-blue-50': n.leida == 0}"
                                       class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                                        <div class="flex items-start">
                                            <div :class="{
                                                'bg-green-100 text-green-600': n.tipo === 'success',
                                                'bg-yellow-100 text-yellow-600': n.tipo === 'warning',
                                                'bg-red-100 text-red-600': n.tipo === 'error',
                                                'bg-blue-100 text-blue-600': n.tipo === 'info' || n.tipo === 'vinculacion'
                                            }" class="p-2 rounded-full mr-3 flex-shrink-0">
                                                <i :class="{
                                                    'fa-check': n.tipo === 'success',
                                                    'fa-exclamation': n.tipo === 'warning',
                                                    'fa-times': n.tipo === 'error',
                                                    'fa-info': n.tipo === 'info',
                                                    'fa-link': n.tipo === 'vinculacion'
                                                }" class="fas text-sm"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-800 truncate" x-text="n.titulo"></p>
                                                <p class="text-xs text-gray-500 truncate" x-text="n.mensaje"></p>
                                                <p class="text-xs text-gray-400 mt-1" x-text="formatDate(n.created_at)"></p>
                                            </div>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </div>
                        
                        <!-- User Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900">
                                <?php if ($userAvatar && file_exists(UPLOADS_PATH . '/avatars/' . $userAvatar)): ?>
                                    <img src="<?= BASE_URL ?>/uploads/avatars/<?= htmlspecialchars($userAvatar) ?>" 
                                         alt="Avatar" class="w-8 h-8 rounded-full object-cover border-2 border-primary-200">
                                <?php else: ?>
                                    <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center text-white">
                                        <i class="fas fa-user"></i>
                                    </div>
                                <?php endif; ?>
                                <span><?= $_SESSION['user_nombre'] ?? 'Usuario' ?></span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" x-cloak
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">
                                <a href="<?= BASE_URL ?>/usuarios/perfil" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user-circle mr-2"></i> Mi Perfil
                                </a>
                                <hr class="my-2">
                                <a href="<?= BASE_URL ?>/auth/logout" class="block px-4 py-2 text-red-600 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Flash Messages -->
            <?php if ($flash = $this->getFlash()): ?>
            <div class="mx-6 mt-4">
                <div class="p-4 rounded-lg <?= $flash['type'] === 'success' ? 'bg-green-100 text-green-800' : ($flash['type'] === 'error' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') ?>">
                    <div class="flex items-center">
                        <i class="fas <?= $flash['type'] === 'success' ? 'fa-check-circle' : ($flash['type'] === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle') ?> mr-2"></i>
                        <?= $flash['message'] ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Page Content -->
            <main class="p-6">
                <?= $content ?>
            </main>
            
            <!-- Footer -->
            <footer class="bg-white border-t mt-auto py-4 px-6">
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <span><?= htmlspecialchars($textoCopyright) ?></span>
                    <span>Versión <?= APP_VERSION ?></span>
                </div>
            </footer>
        </div>
    </div>
    
    <?php else: ?>
    <!-- Layout sin Sidebar (Login) -->
    <main>
        <?= $content ?>
    </main>
    <?php endif; ?>
    
    <script>
        // Funciones globales de JavaScript
        function formatCurrency(amount) {
            return new Intl.NumberFormat('es-MX', {
                style: 'currency',
                currency: 'MXN'
            }).format(amount);
        }
        
        function formatDate(date) {
            return new Date(date).toLocaleDateString('es-MX', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
        
        // Componente de notificaciones para Alpine.js
        function notificaciones() {
            return {
                open: false,
                notificaciones: [],
                noLeidas: 0,
                init() {
                    this.cargarNotificaciones();
                    // Actualizar cada 2 minutos para reducir carga del servidor
                    setInterval(() => this.cargarNotificaciones(), 120000);
                },
                toggle() {
                    this.open = !this.open;
                    if (this.open) {
                        this.cargarNotificaciones();
                    }
                },
                cargarNotificaciones() {
                    fetch('<?= BASE_URL ?>/api/notificaciones')
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                this.notificaciones = data.notificaciones;
                                this.noLeidas = data.no_leidas;
                            }
                        })
                        .catch(e => console.error('Error cargando notificaciones:', e));
                },
                marcarLeida(id) {
                    fetch('<?= BASE_URL ?>/api/notificaciones/marcarLeida/' + id)
                        .then(() => this.cargarNotificaciones())
                        .catch(e => console.error('Error:', e));
                },
                marcarTodasLeidas() {
                    fetch('<?= BASE_URL ?>/api/notificaciones/marcarTodasLeidas')
                        .then(() => {
                            this.cargarNotificaciones();
                        })
                        .catch(e => console.error('Error:', e));
                },
                formatDate(dateStr) {
                    const date = new Date(dateStr);
                    const now = new Date();
                    const diff = now - date;
                    const mins = Math.floor(diff / 60000);
                    const hours = Math.floor(diff / 3600000);
                    const days = Math.floor(diff / 86400000);
                    
                    if (mins < 1) return 'Ahora';
                    if (mins < 60) return mins + ' min';
                    if (hours < 24) return hours + 'h';
                    if (days < 7) return days + 'd';
                    return date.toLocaleDateString('es-MX');
                }
            };
        }
    </script>
</body>
</html>
