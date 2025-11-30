<?php
/**
 * Definición de rutas del sistema
 * Sistema de Gestión Integral de Caja de Ahorros
 */

$router = new Router();

// Rutas de autenticación
$router->add('', ['controller' => 'auth', 'action' => 'login']);
$router->add('auth/login', ['controller' => 'auth', 'action' => 'login']);
$router->add('auth/logout', ['controller' => 'auth', 'action' => 'logout']);
$router->add('auth/forgot-password', ['controller' => 'auth', 'action' => 'forgotPassword']);

// Dashboard
$router->add('dashboard', ['controller' => 'dashboard', 'action' => 'index']);

// Gestión de Socios
$router->add('socios', ['controller' => 'socios', 'action' => 'index']);
$router->add('socios/crear', ['controller' => 'socios', 'action' => 'crear']);
$router->add('socios/editar/{id}', ['controller' => 'socios', 'action' => 'editar']);
$router->add('socios/ver/{id}', ['controller' => 'socios', 'action' => 'ver']);
$router->add('socios/eliminar/{id}', ['controller' => 'socios', 'action' => 'eliminar']);
$router->add('socios/historial/{id}', ['controller' => 'socios', 'action' => 'historial']);
$router->add('socios/buscar', ['controller' => 'socios', 'action' => 'buscar']);

// Gestión de Ahorro
$router->add('ahorro', ['controller' => 'ahorro', 'action' => 'index']);
$router->add('ahorro/socio/{id}', ['controller' => 'ahorro', 'action' => 'socio']);
$router->add('ahorro/movimiento', ['controller' => 'ahorro', 'action' => 'movimiento']);
$router->add('ahorro/historial/{id}', ['controller' => 'ahorro', 'action' => 'historial']);

// Gestión de Créditos
$router->add('creditos', ['controller' => 'creditos', 'action' => 'index']);
$router->add('creditos/solicitud', ['controller' => 'creditos', 'action' => 'solicitud']);
$router->add('creditos/solicitud/{id}', ['controller' => 'creditos', 'action' => 'solicitud']);
$router->add('creditos/ver/{id}', ['controller' => 'creditos', 'action' => 'ver']);
$router->add('creditos/autorizar/{id}', ['controller' => 'creditos', 'action' => 'autorizar']);
$router->add('creditos/amortizacion/{id}', ['controller' => 'creditos', 'action' => 'amortizacion']);
$router->add('creditos/pago/{id}', ['controller' => 'creditos', 'action' => 'pago']);
$router->add('creditos/documentos/{id}', ['controller' => 'creditos', 'action' => 'documentos']);

// Nómina y Descuentos
$router->add('nomina', ['controller' => 'nomina', 'action' => 'index']);
$router->add('nomina/cargar', ['controller' => 'nomina', 'action' => 'cargar']);
$router->add('nomina/procesar/{id}', ['controller' => 'nomina', 'action' => 'procesar']);
$router->add('nomina/homonimias', ['controller' => 'nomina', 'action' => 'homonimias']);
$router->add('nomina/resolver/{id}', ['controller' => 'nomina', 'action' => 'resolver']);
$router->add('nomina/aplicar/{id}', ['controller' => 'nomina', 'action' => 'aplicar']);
$router->add('nomina/historial', ['controller' => 'nomina', 'action' => 'historial']);

// Cartera y Cobranza
$router->add('cartera', ['controller' => 'cartera', 'action' => 'index']);
$router->add('cartera/vencida', ['controller' => 'cartera', 'action' => 'vencida']);
$router->add('cartera/mora', ['controller' => 'cartera', 'action' => 'mora']);
$router->add('cartera/exportar', ['controller' => 'cartera', 'action' => 'exportar']);

// Reportes
$router->add('reportes', ['controller' => 'reportes', 'action' => 'index']);
$router->add('reportes/socios', ['controller' => 'reportes', 'action' => 'socios']);
$router->add('reportes/ahorro', ['controller' => 'reportes', 'action' => 'ahorro']);
$router->add('reportes/creditos', ['controller' => 'reportes', 'action' => 'creditos']);
$router->add('reportes/cartera', ['controller' => 'reportes', 'action' => 'cartera']);
$router->add('reportes/nomina', ['controller' => 'reportes', 'action' => 'nomina']);
$router->add('reportes/exportar/{tipo}', ['controller' => 'reportes', 'action' => 'exportar']);

// Configuraciones del Sistema
$router->add('configuraciones', ['controller' => 'configuraciones', 'action' => 'index']);
$router->add('configuraciones/general', ['controller' => 'configuraciones', 'action' => 'general']);
$router->add('configuraciones/correo', ['controller' => 'configuraciones', 'action' => 'correo']);
$router->add('configuraciones/estilos', ['controller' => 'configuraciones', 'action' => 'estilos']);
$router->add('configuraciones/paypal', ['controller' => 'configuraciones', 'action' => 'paypal']);
$router->add('configuraciones/qr', ['controller' => 'configuraciones', 'action' => 'qr']);

// Gestión de Usuarios
$router->add('usuarios', ['controller' => 'usuarios', 'action' => 'index']);
$router->add('usuarios/crear', ['controller' => 'usuarios', 'action' => 'crear']);
$router->add('usuarios/editar/{id}', ['controller' => 'usuarios', 'action' => 'editar']);
$router->add('usuarios/eliminar/{id}', ['controller' => 'usuarios', 'action' => 'eliminar']);
$router->add('usuarios/perfil', ['controller' => 'usuarios', 'action' => 'perfil']);

// Bitácora
$router->add('bitacora', ['controller' => 'bitacora', 'action' => 'index']);

// API endpoints
$router->add('api/socios/buscar', ['controller' => 'api', 'action' => 'buscarSocios']);
$router->add('api/dashboard/stats', ['controller' => 'api', 'action' => 'dashboardStats']);
