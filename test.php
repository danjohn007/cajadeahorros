<?php
/**
 * Archivo de prueba de conexión y configuración
 * Sistema de Gestión Integral de Caja de Ahorros
 * 
 * Este archivo verifica:
 * - Conexión a la base de datos
 * - URL base configurada correctamente
 * - Requisitos del sistema
 * 
 * NOTA: Eliminar o proteger este archivo en producción
 */

// Solo mostrar errores en desarrollo
$configFile = __DIR__ . '/config/config.php';
$configLoaded = false;

if (file_exists($configFile)) {
    require_once $configFile;
    $configLoaded = true;
}

// Habilitar errores solo si DEBUG_MODE está activo
if (defined('DEBUG_MODE') && DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Función para mostrar estado
function showStatus($label, $status, $message = '') {
    $color = $status ? '#10B981' : '#EF4444';
    $icon = $status ? '✓' : '✗';
    echo "<div style='padding: 10px; margin: 5px 0; background: #f8f9fa; border-left: 4px solid {$color}; display: flex; align-items: center;'>";
    echo "<span style='color: {$color}; font-size: 20px; margin-right: 10px;'>{$icon}</span>";
    echo "<div><strong>{$label}</strong>";
    if ($message) {
        echo "<br><small style='color: #6b7280;'>{$message}</small>";
    }
    echo "</div></div>";
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Conexión - Caja de Ahorros</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f3f4f6;
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 { font-size: 24px; margin-bottom: 5px; }
        .header p { opacity: 0.9; }
        .content { padding: 30px; }
        .section { margin-bottom: 30px; }
        .section h2 { 
            font-size: 18px; 
            color: #374151; 
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }
        .info-box {
            background: #EFF6FF;
            border: 1px solid #BFDBFE;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }
        .info-box h3 { color: #1e40af; margin-bottom: 10px; }
        .info-box code {
            display: block;
            background: #1e293b;
            color: #10B981;
            padding: 15px;
            border-radius: 4px;
            margin-top: 10px;
            font-size: 13px;
            overflow-x: auto;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #1e40af;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
            font-weight: 500;
        }
        .btn:hover { background: #1e3a8a; }
        .btn.success { background: #10B981; }
        .btn.success:hover { background: #059669; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏦 Sistema de Gestión Integral</h1>
            <p>Caja de Ahorros - Prueba de Configuración</p>
        </div>
        
        <div class="content">
            <!-- Requisitos del Sistema -->
            <div class="section">
                <h2>📋 Requisitos del Sistema</h2>
                
                <?php
                // PHP Version
                $phpVersion = phpversion();
                $phpOk = version_compare($phpVersion, '7.4', '>=');
                showStatus("PHP Versión: {$phpVersion}", $phpOk, $phpOk ? 'Versión compatible' : 'Se requiere PHP 7.4 o superior');
                
                // PDO Extension
                $pdoOk = extension_loaded('pdo');
                showStatus("Extensión PDO", $pdoOk, $pdoOk ? 'Instalada' : 'Extensión PDO no encontrada');
                
                // PDO MySQL
                $pdoMysqlOk = extension_loaded('pdo_mysql');
                showStatus("Extensión PDO MySQL", $pdoMysqlOk, $pdoMysqlOk ? 'Instalada' : 'Extensión pdo_mysql no encontrada');
                
                // mbstring
                $mbstringOk = extension_loaded('mbstring');
                showStatus("Extensión mbstring", $mbstringOk, $mbstringOk ? 'Instalada' : 'Extensión mbstring no encontrada');
                
                // JSON
                $jsonOk = extension_loaded('json');
                showStatus("Extensión JSON", $jsonOk, $jsonOk ? 'Instalada' : 'Extensión json no encontrada');
                ?>
            </div>
            
            <!-- Archivos de Configuración -->
            <div class="section">
                <h2>📁 Archivos de Configuración</h2>
                
                <?php
                showStatus("Archivo config.php", $configLoaded, $configLoaded ? 'Cargado correctamente' : 'No se encontró config/config.php');
                
                $htaccessExists = file_exists(__DIR__ . '/.htaccess');
                showStatus("Archivo .htaccess", $htaccessExists, $htaccessExists ? 'Presente' : 'No encontrado - Las URLs amigables no funcionarán');
                
                $schemaExists = file_exists(__DIR__ . '/database/schema.sql');
                showStatus("Archivo schema.sql", $schemaExists, $schemaExists ? 'Presente' : 'No encontrado');
                ?>
            </div>
            
            <!-- URL Base -->
            <div class="section">
                <h2>🔗 Configuración de URL</h2>
                
                <?php
                if ($configLoaded && defined('BASE_URL')) {
                    showStatus("URL Base Configurada", true, BASE_URL);
                    
                    // Verificar que la URL actual coincida
                    $currentProtocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                    $currentHost = $_SERVER['HTTP_HOST'];
                    $currentPath = dirname($_SERVER['SCRIPT_NAME']);
                    $calculatedBase = $currentProtocol . '://' . $currentHost . $currentPath;
                    
                    echo "<div class='info-box'>";
                    echo "<h3>Información de URL</h3>";
                    echo "<p><strong>URL Base Configurada:</strong> " . (defined('BASE_URL') ? BASE_URL : 'No definida') . "</p>";
                    echo "<p><strong>URL Detectada:</strong> {$calculatedBase}</p>";
                    echo "</div>";
                } else {
                    showStatus("URL Base", false, 'No se pudo determinar la URL base');
                }
                ?>
            </div>
            
            <!-- Conexión a Base de Datos -->
            <div class="section">
                <h2>🗄️ Conexión a Base de Datos</h2>
                
                <?php
                if ($configLoaded && defined('DB_HOST')) {
                    try {
                        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
                        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                        ]);
                        
                        showStatus("Conexión a MySQL", true, 'Conexión exitosa a ' . DB_HOST);
                        showStatus("Base de datos: " . DB_NAME, true, 'Accesible');
                        
                        // Verificar tablas principales
                        $tables = ['usuarios', 'socios', 'cuentas_ahorro', 'creditos', 'configuraciones'];
                        $missingTables = [];
                        
                        foreach ($tables as $table) {
                            $stmt = $pdo->query("SHOW TABLES LIKE '{$table}'");
                            if ($stmt->rowCount() == 0) {
                                $missingTables[] = $table;
                            }
                        }
                        
                        if (empty($missingTables)) {
                            showStatus("Tablas del sistema", true, 'Todas las tablas principales existen');
                            
                            // Contar registros
                            $userCount = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
                            $socioCount = $pdo->query("SELECT COUNT(*) FROM socios")->fetchColumn();
                            
                            echo "<div class='info-box'>";
                            echo "<h3>Estadísticas de la Base de Datos</h3>";
                            echo "<p>👤 Usuarios registrados: <strong>{$userCount}</strong></p>";
                            echo "<p>👥 Socios registrados: <strong>{$socioCount}</strong></p>";
                            echo "</div>";
                        } else {
                            showStatus("Tablas del sistema", false, 'Faltan tablas: ' . implode(', ', $missingTables));
                            echo "<div class='info-box'>";
                            echo "<h3>Ejecutar Esquema SQL</h3>";
                            echo "<p>Importa el esquema de la base de datos:</p>";
                            echo "<code>mysql -u " . DB_USER . " -p " . DB_NAME . " < database/schema.sql</code>";
                            echo "</div>";
                        }
                        
                    } catch (PDOException $e) {
                        showStatus("Conexión a MySQL", false, 'Error: ' . $e->getMessage());
                        
                        echo "<div class='info-box'>";
                        echo "<h3>Verificar Configuración</h3>";
                        echo "<p>Revisa el archivo <code>config/config.php</code> con los siguientes valores:</p>";
                        echo "<code>define('DB_HOST', 'localhost');\ndefine('DB_NAME', 'cajadeahorros');\ndefine('DB_USER', 'tu_usuario');\ndefine('DB_PASS', 'tu_contraseña');</code>";
                        echo "</div>";
                    }
                } else {
                    showStatus("Configuración de BD", false, 'No se encontró la configuración de base de datos');
                }
                ?>
            </div>
            
            <!-- Directorios -->
            <div class="section">
                <h2>📂 Permisos de Directorios</h2>
                
                <?php
                $uploadsDir = __DIR__ . '/uploads';
                if (!is_dir($uploadsDir)) {
                    @mkdir($uploadsDir, 0777, true);
                }
                $uploadsWritable = is_writable($uploadsDir);
                showStatus("Directorio uploads/", $uploadsWritable, $uploadsWritable ? 'Escritura permitida' : 'Sin permisos de escritura');
                
                $publicDir = __DIR__ . '/public';
                $publicExists = is_dir($publicDir);
                showStatus("Directorio public/", $publicExists, $publicExists ? 'Existe' : 'No encontrado');
                ?>
            </div>
            
            <!-- Resumen -->
            <div class="section">
                <h2>📊 Resumen</h2>
                
                <?php
                $allOk = $phpOk && $pdoOk && $pdoMysqlOk && $configLoaded && $htaccessExists;
                
                if ($allOk) {
                    echo "<div style='background: #D1FAE5; border: 1px solid #6EE7B7; border-radius: 8px; padding: 20px; text-align: center;'>";
                    echo "<h3 style='color: #047857; margin-bottom: 10px;'>✅ Sistema Listo</h3>";
                    echo "<p style='color: #065F46;'>Todos los requisitos están cumplidos. El sistema está listo para usarse.</p>";
                    echo "<a href='" . (defined('BASE_URL') ? BASE_URL : './') . "' class='btn success'>Ir al Sistema</a>";
                    echo "</div>";
                } else {
                    echo "<div style='background: #FEE2E2; border: 1px solid #FCA5A5; border-radius: 8px; padding: 20px; text-align: center;'>";
                    echo "<h3 style='color: #B91C1C; margin-bottom: 10px;'>⚠️ Configuración Incompleta</h3>";
                    echo "<p style='color: #991B1B;'>Algunos requisitos no están cumplidos. Revisa los errores arriba.</p>";
                    echo "</div>";
                }
                ?>
            </div>
            
            <!-- Información Adicional -->
            <div class="section">
                <h2>ℹ️ Información del Servidor</h2>
                <div class="info-box">
                    <p><strong>Servidor Web:</strong> <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Desconocido' ?></p>
                    <p><strong>Sistema Operativo:</strong> <?= PHP_OS ?></p>
                    <p><strong>Directorio del Proyecto:</strong> <?= __DIR__ ?></p>
                    <p><strong>Fecha/Hora del Servidor:</strong> <?= date('Y-m-d H:i:s') ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <p style="text-align: center; margin-top: 20px; color: #6b7280;">
        Sistema de Gestión Integral de Caja de Ahorros v1.0.0
    </p>
</body>
</html>
