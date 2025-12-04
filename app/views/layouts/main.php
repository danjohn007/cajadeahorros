<?php
// Get system configuration for colors and branding
$systemColors = getSystemColors();
$siteName = getSiteName();
$logoUrl = getLogo();
$colorPrimario = $systemColors['color_primario'];
$colorSecundario = $systemColors['color_secundario'];
$colorAcento = $systemColors['color_acento'];
$textoCopyright = getConfig('texto_copyright', '© ' . date('Y') . ' ' . APP_NAME . '. Todos los derechos reservados.');
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
               class="bg-primary-800 text-white transition-all duration-300 fixed h-full z-30">
            
            <!-- Logo -->
            <div class="flex items-center justify-between p-4 border-b border-primary-700">
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
            
            <!-- Navigation -->
            <nav class="mt-4">
                <?php if ($_SESSION['user_role'] === 'cliente'): ?>
                <!-- Menú para CLIENTE - Solo acceso a su portal -->
                <a href="<?= BASE_URL ?>/cliente" 
                   class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'cliente') !== false && strpos($_SERVER['REQUEST_URI'], 'cliente/') === false ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt w-6"></i>
                    <span class="ml-3" x-show="sidebarOpen">Mi Portal</span>
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
                
                <a href="<?= BASE_URL ?>/creditos" 
                   class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'creditos') !== false ? 'active' : '' ?>">
                    <i class="fas fa-hand-holding-usd w-6"></i>
                    <span class="ml-3" x-show="sidebarOpen">Créditos</span>
                </a>
                
                <a href="<?= BASE_URL ?>/nomina" 
                   class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'nomina') !== false ? 'active' : '' ?>">
                    <i class="fas fa-file-invoice-dollar w-6"></i>
                    <span class="ml-3" x-show="sidebarOpen">Nómina</span>
                </a>
                
                <a href="<?= BASE_URL ?>/cartera" 
                   class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'cartera') !== false ? 'active' : '' ?>">
                    <i class="fas fa-chart-pie w-6"></i>
                    <span class="ml-3" x-show="sidebarOpen">Cartera</span>
                </a>
                
                <a href="<?= BASE_URL ?>/reportes" 
                   class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'reportes') !== false ? 'active' : '' ?>">
                    <i class="fas fa-file-alt w-6"></i>
                    <span class="ml-3" x-show="sidebarOpen">Reportes</span>
                </a>
                
                <!-- Nuevos Módulos -->
                <a href="<?= BASE_URL ?>/financiero" 
                   class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'financiero') !== false ? 'active' : '' ?>">
                    <i class="fas fa-coins w-6"></i>
                    <span class="ml-3" x-show="sidebarOpen">Módulo Financiero</span>
                </a>
                
                <a href="<?= BASE_URL ?>/membresias" 
                   class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'membresias') !== false ? 'active' : '' ?>">
                    <i class="fas fa-id-card w-6"></i>
                    <span class="ml-3" x-show="sidebarOpen">Membresías</span>
                </a>
                
                <a href="<?= BASE_URL ?>/crm" 
                   class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'crm') !== false ? 'active' : '' ?>">
                    <i class="fas fa-chart-line w-6"></i>
                    <span class="ml-3" x-show="sidebarOpen">Informe CRM</span>
                </a>
                
                <a href="<?= BASE_URL ?>/kyc" 
                   class="sidebar-link flex items-center px-4 py-3 text-gray-100 hover:bg-primary-700 <?= strpos($_SERVER['REQUEST_URI'], 'kyc') !== false ? 'active' : '' ?>">
                    <i class="fas fa-user-shield w-6"></i>
                    <span class="ml-3" x-show="sidebarOpen">Sistema KYC</span>
                </a>
                
                <?php if ($_SESSION['user_role'] === 'administrador'): ?>
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
                                <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center text-white">
                                    <i class="fas fa-user"></i>
                                </div>
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
