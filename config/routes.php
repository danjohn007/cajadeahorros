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
$router->add('auth/registro', ['controller' => 'auth', 'action' => 'registro']);

// Portal del Cliente
$router->add('cliente', ['controller' => 'cliente', 'action' => 'index']);
$router->add('cliente/cuenta', ['controller' => 'cliente', 'action' => 'cuenta']);
$router->add('cliente/creditos', ['controller' => 'cliente', 'action' => 'creditos']);
$router->add('cliente/credito/{id}', ['controller' => 'cliente', 'action' => 'credito']);
$router->add('cliente/amortizacion/{id}', ['controller' => 'cliente', 'action' => 'amortizacion']);
$router->add('cliente/pagar', ['controller' => 'cliente', 'action' => 'pagar']);
$router->add('cliente/procesarPago', ['controller' => 'cliente', 'action' => 'procesarPago']);
$router->add('cliente/solicitarVinculacion', ['controller' => 'cliente', 'action' => 'solicitarVinculacion']);
$router->add('cliente/estado-cuenta', ['controller' => 'cliente', 'action' => 'estadoCuenta']);

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
$router->add('socios/estado-cuenta/{id}', ['controller' => 'socios', 'action' => 'estadoCuenta']);

// Gestión de Ahorro
$router->add('ahorro', ['controller' => 'ahorro', 'action' => 'index']);
$router->add('ahorro/socio/{id}', ['controller' => 'ahorro', 'action' => 'socio']);
$router->add('ahorro/movimiento', ['controller' => 'ahorro', 'action' => 'movimiento']);
$router->add('ahorro/historial/{id}', ['controller' => 'ahorro', 'action' => 'historial']);
$router->add('ahorro/cardex/{id}', ['controller' => 'ahorro', 'action' => 'cardex']);

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
$router->add('configuraciones/testEmail', ['controller' => 'configuraciones', 'action' => 'testEmail']);
$router->add('configuraciones/modulos', ['controller' => 'configuraciones', 'action' => 'modulos']);
$router->add('configuraciones/chatbot', ['controller' => 'configuraciones', 'action' => 'chatbot']);

// Gestión de Usuarios
$router->add('usuarios', ['controller' => 'usuarios', 'action' => 'index']);
$router->add('usuarios/crear', ['controller' => 'usuarios', 'action' => 'crear']);
$router->add('usuarios/editar/{id}', ['controller' => 'usuarios', 'action' => 'editar']);
$router->add('usuarios/eliminar/{id}', ['controller' => 'usuarios', 'action' => 'eliminar']);
$router->add('usuarios/perfil', ['controller' => 'usuarios', 'action' => 'perfil']);

// Bitácora
$router->add('bitacora', ['controller' => 'bitacora', 'action' => 'index']);

// Membresías
$router->add('membresias', ['controller' => 'membresias', 'action' => 'index']);
$router->add('membresias/crear', ['controller' => 'membresias', 'action' => 'crear']);
$router->add('membresias/editar/{id}', ['controller' => 'membresias', 'action' => 'editar']);
$router->add('membresias/ver/{id}', ['controller' => 'membresias', 'action' => 'ver']);
$router->add('membresias/renovar/{id}', ['controller' => 'membresias', 'action' => 'renovar']);
$router->add('membresias/tipos', ['controller' => 'membresias', 'action' => 'tipos']);

// Módulo Financiero
$router->add('financiero', ['controller' => 'financiero', 'action' => 'index']);
$router->add('financiero/transaccion', ['controller' => 'financiero', 'action' => 'transaccion']);
$router->add('financiero/transaccion/{id}', ['controller' => 'financiero', 'action' => 'transaccion']);
$router->add('financiero/categorias', ['controller' => 'financiero', 'action' => 'categorias']);
$router->add('financiero/reportes', ['controller' => 'financiero', 'action' => 'reportes']);
$router->add('financiero/presupuestos', ['controller' => 'financiero', 'action' => 'presupuestos']);
$router->add('financiero/proveedores', ['controller' => 'financiero', 'action' => 'proveedores']);
$router->add('financiero/proveedor', ['controller' => 'financiero', 'action' => 'proveedor']);
$router->add('financiero/proveedor/{id}', ['controller' => 'financiero', 'action' => 'proveedor']);

// Importar Clientes
$router->add('importar', ['controller' => 'importar', 'action' => 'index']);
$router->add('importar/clientes', ['controller' => 'importar', 'action' => 'clientes']);
$router->add('importar/procesar/{id}', ['controller' => 'importar', 'action' => 'procesar']);
$router->add('importar/historial', ['controller' => 'importar', 'action' => 'historial']);
$router->add('importar/detalle/{id}', ['controller' => 'importar', 'action' => 'detalle']);
$router->add('importar/plantilla', ['controller' => 'importar', 'action' => 'plantilla']);

// Auditoría (Logs extendidos)
$router->add('auditoria', ['controller' => 'auditoria', 'action' => 'index']);
$router->add('auditoria/logs', ['controller' => 'auditoria', 'action' => 'logs']);
$router->add('auditoria/sesiones', ['controller' => 'auditoria', 'action' => 'sesiones']);
$router->add('auditoria/cambios', ['controller' => 'auditoria', 'action' => 'cambios']);

// Informe CRM
$router->add('crm', ['controller' => 'crm', 'action' => 'index']);
$router->add('crm/segmentos', ['controller' => 'crm', 'action' => 'segmentos']);
$router->add('crm/metricas', ['controller' => 'crm', 'action' => 'metricas']);
$router->add('crm/interacciones', ['controller' => 'crm', 'action' => 'interacciones']);
$router->add('crm/interaccion', ['controller' => 'crm', 'action' => 'interaccion']);
$router->add('crm/interaccion/{id}', ['controller' => 'crm', 'action' => 'interaccion']);
$router->add('crm/customerjourney', ['controller' => 'crm', 'action' => 'customerjourney']);

// Dispositivos IoT (Shelly Cloud y HikVision)
$router->add('dispositivos', ['controller' => 'dispositivos', 'action' => 'index']);
$router->add('dispositivos/crear', ['controller' => 'dispositivos', 'action' => 'crear']);
$router->add('dispositivos/editar/{id}', ['controller' => 'dispositivos', 'action' => 'editar']);
$router->add('dispositivos/ver/{id}', ['controller' => 'dispositivos', 'action' => 'ver']);
$router->add('dispositivos/shelly', ['controller' => 'dispositivos', 'action' => 'shelly']);
$router->add('dispositivos/shelly/{id}', ['controller' => 'dispositivos', 'action' => 'shellyConfig']);
$router->add('dispositivos/hikvision', ['controller' => 'dispositivos', 'action' => 'hikvision']);
$router->add('dispositivos/hikvision/{id}', ['controller' => 'dispositivos', 'action' => 'hikvisionConfig']);
$router->add('dispositivos/eventos', ['controller' => 'dispositivos', 'action' => 'eventos']);
$router->add('dispositivos/programacion', ['controller' => 'dispositivos', 'action' => 'programacion']);

// Pagos Online (PayPal)
$router->add('pago/enlace/{id}', ['controller' => 'pago', 'action' => 'enlace']);
$router->add('pago/cuota/{id}', ['controller' => 'pago', 'action' => 'cuota']);
$router->add('pago/publico/{token}', ['controller' => 'pago', 'action' => 'publico']);
$router->add('pago/procesar', ['controller' => 'pago', 'action' => 'procesar']);
$router->add('pago/exito', ['controller' => 'pago', 'action' => 'exito']);
$router->add('pago/cancelado', ['controller' => 'pago', 'action' => 'cancelado']);

// Sistema KYC (Know Your Customer)
$router->add('kyc', ['controller' => 'kyc', 'action' => 'index']);
$router->add('kyc/crear', ['controller' => 'kyc', 'action' => 'crear']);
$router->add('kyc/editar/{id}', ['controller' => 'kyc', 'action' => 'editar']);
$router->add('kyc/ver/{id}', ['controller' => 'kyc', 'action' => 'ver']);
$router->add('kyc/aprobar/{id}', ['controller' => 'kyc', 'action' => 'aprobar']);
$router->add('kyc/rechazar/{id}', ['controller' => 'kyc', 'action' => 'rechazar']);
$router->add('kyc/documentos/{id}', ['controller' => 'kyc', 'action' => 'documentos']);
$router->add('kyc/descargar/{id}', ['controller' => 'kyc', 'action' => 'descargar']);
$router->add('kyc/reportes', ['controller' => 'kyc', 'action' => 'reportes']);

// Sistema ESCROW
$router->add('escrow', ['controller' => 'escrow', 'action' => 'index']);
$router->add('escrow/crear', ['controller' => 'escrow', 'action' => 'crear']);
$router->add('escrow/editar/{id}', ['controller' => 'escrow', 'action' => 'editar']);
$router->add('escrow/ver/{id}', ['controller' => 'escrow', 'action' => 'ver']);
$router->add('escrow/deposito/{id}', ['controller' => 'escrow', 'action' => 'deposito']);
$router->add('escrow/liberar/{id}', ['controller' => 'escrow', 'action' => 'liberar']);
$router->add('escrow/disputa/{id}', ['controller' => 'escrow', 'action' => 'disputa']);
$router->add('escrow/cancelar/{id}', ['controller' => 'escrow', 'action' => 'cancelar']);
$router->add('escrow/documentos/{id}', ['controller' => 'escrow', 'action' => 'documentos']);
$router->add('escrow/hitos/{id}', ['controller' => 'escrow', 'action' => 'hitos']);

// Api Buró de Crédito - Configuración
$router->add('configuraciones/buro', ['controller' => 'configuraciones', 'action' => 'buro']);

// Consulta Pública Buró de Crédito
$router->add('buro/consulta', ['controller' => 'buro', 'action' => 'consulta']);
$router->add('buro/pagar', ['controller' => 'buro', 'action' => 'pagar']);
$router->add('buro/procesar', ['controller' => 'buro', 'action' => 'procesar']);
$router->add('buro/resultado/{token}', ['controller' => 'buro', 'action' => 'resultado']);

// Inversionistas
$router->add('inversionistas', ['controller' => 'inversionistas', 'action' => 'index']);
$router->add('inversionistas/crear', ['controller' => 'inversionistas', 'action' => 'crear']);
$router->add('inversionistas/editar/{id}', ['controller' => 'inversionistas', 'action' => 'editar']);
$router->add('inversionistas/ver/{id}', ['controller' => 'inversionistas', 'action' => 'ver']);
$router->add('inversionistas/inversion/{id}', ['controller' => 'inversionistas', 'action' => 'inversion']);
$router->add('inversionistas/buscar', ['controller' => 'inversionistas', 'action' => 'buscar']);

// API endpoints
$router->add('api/socios/buscar', ['controller' => 'api', 'action' => 'buscarSocios']);
$router->add('api/dashboard/stats', ['controller' => 'api', 'action' => 'dashboardStats']);
$router->add('api/qr', ['controller' => 'api', 'action' => 'qr']);
$router->add('api/qr/masivo', ['controller' => 'api', 'action' => 'generarQRMasivo']);
$router->add('api/notificaciones', ['controller' => 'api', 'action' => 'notificaciones']);
$router->add('api/notificaciones/marcarLeida/{id}', ['controller' => 'api', 'action' => 'marcarNotificacionLeida']);
$router->add('api/notificaciones/marcarTodasLeidas', ['controller' => 'api', 'action' => 'marcarTodasNotificacionesLeidas']);

// Página pública de Soporte Técnico
$router->add('soporte', ['controller' => 'soporte', 'action' => 'index']);
$router->add('public/soporte-tecnico', ['controller' => 'soporte', 'action' => 'index']);
$router->add('soporte-tecnico', ['controller' => 'soporte', 'action' => 'index']);

// Recuperación de contraseña
$router->add('auth/reset-password', ['controller' => 'auth', 'action' => 'resetPassword']);

// Políticas de Crédito
$router->add('politicas/validar-edad-plazo', ['controller' => 'politicasCredito', 'action' => 'validarEdadPlazo']);
$router->add('politicas/validar-aval', ['controller' => 'politicasCredito', 'action' => 'validarRequiereAval']);
$router->add('politicas/checklist', ['controller' => 'politicasCredito', 'action' => 'obtenerChecklist']);
$router->add('politicas/checklist/validar', ['controller' => 'politicasCredito', 'action' => 'validarChecklistCredito']);
$router->add('politicas/checklist/marcar', ['controller' => 'politicasCredito', 'action' => 'marcarItemCompletado']);

// Tesorería
$router->add('tesoreria', ['controller' => 'tesoreria', 'action' => 'index']);
$router->add('tesoreria/proyecciones', ['controller' => 'tesoreria', 'action' => 'obtenerProyecciones']);
$router->add('tesoreria/flujos', ['controller' => 'tesoreria', 'action' => 'obtenerFlujosEfectivo']);
$router->add('tesoreria/proyeccion', ['controller' => 'tesoreria', 'action' => 'registrarProyeccion']);
$router->add('tesoreria/resumen-cartera', ['controller' => 'tesoreria', 'action' => 'obtenerResumenCartera']);

// Reportes CNBV
$router->add('cnbv', ['controller' => 'cnbv', 'action' => 'index']);
$router->add('cnbv/reportes', ['controller' => 'cnbv', 'action' => 'listarReportes']);
$router->add('cnbv/generar-situacion-financiera', ['controller' => 'cnbv', 'action' => 'generarReporteSituacionFinanciera']);
$router->add('cnbv/generar-cartera', ['controller' => 'cnbv', 'action' => 'generarReporteCartera']);
