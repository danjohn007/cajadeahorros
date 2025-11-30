<?php
/**
 * Punto de entrada principal del sistema
 * Sistema de Gestión Integral de Caja de Ahorros
 */

// Cargar configuración
require_once __DIR__ . '/config/config.php';

// Autoload de clases del core
spl_autoload_register(function ($class) {
    $paths = [
        CORE_PATH . '/' . $class . '.php',
        APP_PATH . '/models/' . $class . '.php',
        APP_PATH . '/controllers/' . $class . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Cargar rutas
require_once __DIR__ . '/config/routes.php';

// Obtener la URL solicitada
$url = $_GET['url'] ?? '';

// Despachar la solicitud
$router->dispatch($url);
