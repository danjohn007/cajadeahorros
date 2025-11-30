<?php
/**
 * Configuración principal del sistema
 * Sistema de Gestión Integral de Caja de Ahorros
 */

// Auto-detect URL base
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptPath = dirname($_SERVER['SCRIPT_NAME'] ?? '');
$basePath = rtrim(str_replace('/public', '', $scriptPath), '/');

// Definir constantes del sistema
define('APP_NAME', 'Caja de Ahorros');
define('APP_VERSION', '1.0.0');
define('BASE_URL', $protocol . $host . $basePath);
define('PUBLIC_URL', BASE_URL . '/public');

// Rutas del sistema
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('CORE_PATH', ROOT_PATH . '/core');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');

// Configuración de base de datos
// IMPORTANTE: Cambiar estas credenciales antes de usar en producción
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'caja_ahorros');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');

// Configuración de sesiones
define('SESSION_NAME', 'caja_session');
define('SESSION_LIFETIME', 7200); // 2 horas

// Configuración de zona horaria
date_default_timezone_set('America/Mexico_City');

// Configuración de errores
// IMPORTANTE: Cambiar a false en producción
define('DEBUG_MODE', getenv('DEBUG_MODE') === 'true' || !getenv('DEBUG_MODE'));

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}
