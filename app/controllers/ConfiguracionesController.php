<?php
/**
 * Controlador de Configuraciones
 * Sistema de Gestión Integral de Caja de Ahorros
 */

require_once CORE_PATH . '/Controller.php';

class ConfiguracionesController extends Controller {
    
    public function index() {
        $this->requireRole(['administrador']);
        
        $configuraciones = $this->db->fetchAll("SELECT * FROM configuraciones ORDER BY id");
        
        // Agrupar por categoría
        $grupos = [
            'general' => ['nombre_sitio', 'logo', 'telefono_contacto', 'horario_atencion'],
            'correo' => ['correo_sistema'],
            'estilos' => ['color_primario', 'color_secundario'],
            'pagos' => ['paypal_client_id', 'paypal_secret'],
            'tasas' => ['tasa_interes_ahorro', 'tasa_mora']
        ];
        
        $this->view('configuraciones/index', [
            'pageTitle' => 'Configuraciones del Sistema',
            'configuraciones' => $configuraciones,
            'grupos' => $grupos
        ]);
    }
    
    public function general() {
        $this->requireRole(['administrador']);
        
        $errors = [];
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $campos = ['nombre_sitio', 'telefono_contacto', 'horario_atencion', 'email_contacto', 'texto_copyright'];
            
            foreach ($campos as $campo) {
                if (isset($_POST[$campo])) {
                    $this->guardarConfiguracion($campo, $this->sanitize($_POST[$campo]));
                }
            }
            
            // Procesar logo si se subió
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/svg+xml', 'image/gif'];
                $maxSize = 2 * 1024 * 1024; // 2MB
                
                $fileType = mime_content_type($_FILES['logo']['tmp_name']);
                $fileSize = $_FILES['logo']['size'];
                
                if (!in_array($fileType, $allowedTypes)) {
                    $errors[] = 'Formato de imagen no válido. Use JPG, PNG, SVG o GIF.';
                } elseif ($fileSize > $maxSize) {
                    $errors[] = 'El archivo es demasiado grande. Máximo 2MB.';
                } else {
                    $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
                    $nombreArchivo = 'logo_' . time() . '.' . $ext;
                    
                    // Ensure images directory exists
                    $imagesDir = PUBLIC_PATH . '/images';
                    if (!is_dir($imagesDir)) {
                        mkdir($imagesDir, 0755, true);
                    }
                    
                    $ruta = $imagesDir . '/' . $nombreArchivo;
                    if (move_uploaded_file($_FILES['logo']['tmp_name'], $ruta)) {
                        $this->guardarConfiguracion('logo', $nombreArchivo);
                    } else {
                        $errors[] = 'Error al guardar el archivo';
                    }
                }
            }
            
            if (empty($errors)) {
                // Clear config cache so changes take effect immediately
                clearConfigCache();
                $this->logAction('ACTUALIZAR_CONFIG', 'Se actualizaron configuraciones generales', 'configuraciones', null);
                $success = 'Configuraciones guardadas exitosamente';
            }
        }
        
        $config = $this->getConfiguraciones(['nombre_sitio', 'logo', 'telefono_contacto', 'horario_atencion', 'email_contacto', 'texto_copyright']);
        
        $this->view('configuraciones/general', [
            'pageTitle' => 'Configuración General',
            'config' => $config,
            'errors' => $errors,
            'success' => $success
        ]);
    }
    
    public function correo() {
        $this->requireRole(['administrador']);
        
        $success = '';
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            // Save SMTP configuration
            $smtpFields = ['smtp_host', 'smtp_port', 'smtp_user', 'smtp_from_name', 'smtp_encryption', 'correo_sistema'];
            
            foreach ($smtpFields as $field) {
                if (isset($_POST[$field])) {
                    $this->guardarConfiguracion($field, $this->sanitize($_POST[$field]));
                }
            }
            
            // Handle password separately (only update if provided)
            if (!empty($_POST['smtp_password'])) {
                $this->guardarConfiguracion('smtp_password', $this->sanitize($_POST['smtp_password']));
            }
            
            // Clear config cache so changes take effect immediately
            clearConfigCache();
            
            $this->logAction('ACTUALIZAR_CONFIG', 'Se actualizó configuración de correo SMTP', 'configuraciones', null);
            $success = 'Configuración de correo guardada exitosamente';
        }
        
        $config = $this->getConfiguraciones([
            'smtp_host', 'smtp_port', 'smtp_user', 'smtp_password', 
            'smtp_from_name', 'smtp_encryption', 'correo_sistema'
        ]);
        
        $this->view('configuraciones/correo', [
            'pageTitle' => 'Configuración de Correo',
            'config' => $config,
            'success' => $success,
            'errors' => $errors
        ]);
    }
    
    public function estilos() {
        $this->requireRole(['administrador']);
        
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            // Validate color format (hex colors)
            $colorFields = ['color_primario', 'color_secundario', 'color_acento'];
            
            foreach ($colorFields as $field) {
                $color = $_POST[$field] ?? '';
                if (preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
                    $this->guardarConfiguracion($field, $color);
                }
            }
            
            // Clear config cache so changes take effect immediately
            clearConfigCache();
            
            $this->logAction('ACTUALIZAR_CONFIG', 'Se actualizaron estilos del sistema', 'configuraciones', null);
            $success = 'Estilos guardados exitosamente';
        }
        
        $config = $this->getConfiguraciones(['color_primario', 'color_secundario', 'color_acento']);
        
        $this->view('configuraciones/estilos', [
            'pageTitle' => 'Configuración de Estilos',
            'config' => $config,
            'success' => $success
        ]);
    }
    
    public function paypal() {
        $this->requireRole(['administrador']);
        
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            // Save PayPal configuration
            $this->guardarConfiguracion('paypal_enabled', isset($_POST['paypal_enabled']) ? '1' : '0');
            $this->guardarConfiguracion('paypal_mode', $this->sanitize($_POST['paypal_mode'] ?? 'sandbox'));
            $this->guardarConfiguracion('paypal_client_id', $this->sanitize($_POST['paypal_client_id'] ?? ''));
            
            if (!empty($_POST['paypal_secret'])) {
                $this->guardarConfiguracion('paypal_secret', $this->sanitize($_POST['paypal_secret']));
            }
            
            // Clear config cache so changes take effect immediately
            clearConfigCache();
            
            $this->logAction('ACTUALIZAR_CONFIG', 'Se actualizó configuración de PayPal', 'configuraciones', null);
            $success = 'Configuración de PayPal guardada';
        }
        
        $config = $this->getConfiguraciones(['paypal_enabled', 'paypal_mode', 'paypal_client_id', 'paypal_secret']);
        
        $this->view('configuraciones/paypal', [
            'pageTitle' => 'Configuración de Pagos',
            'config' => $config,
            'success' => $success
        ]);
    }
    
    public function qr() {
        $this->requireRole(['administrador']);
        
        $this->view('configuraciones/qr', [
            'pageTitle' => 'Generador de QR'
        ]);
    }
    
    public function buro() {
        $this->requireRole(['administrador']);
        
        $success = '';
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            // Validar costo
            $costoConsulta = floatval($_POST['buro_costo_consulta'] ?? 0);
            if ($costoConsulta < 0) {
                $errors[] = 'El costo de consulta no puede ser negativo';
            }
            
            if (empty($errors)) {
                // Guardar configuraciones
                $this->guardarConfiguracion('buro_api_enabled', isset($_POST['buro_api_enabled']) ? '1' : '0');
                $this->guardarConfiguracion('buro_api_url', $this->sanitize($_POST['buro_api_url'] ?? 'https://apif.burodecredito.com.mx'));
                $this->guardarConfiguracion('buro_api_username', $this->sanitize($_POST['buro_api_username'] ?? ''));
                $this->guardarConfiguracion('buro_api_key', $this->sanitize($_POST['buro_api_key'] ?? ''));
                $this->guardarConfiguracion('buro_costo_consulta', number_format($costoConsulta, 2, '.', ''));
                
                // Solo actualizar contraseña si se proporciona
                if (!empty($_POST['buro_api_password'])) {
                    $this->guardarConfiguracion('buro_api_password', $this->sanitize($_POST['buro_api_password']));
                }
                
                // Clear config cache
                clearConfigCache();
                
                $this->logAction('ACTUALIZAR_CONFIG', 'Se actualizó configuración de API Buró de Crédito', 'configuraciones', null);
                $success = 'Configuración de Buró de Crédito guardada exitosamente';
            }
        }
        
        $config = $this->getConfiguraciones([
            'buro_api_enabled', 'buro_api_url', 'buro_api_username', 
            'buro_api_password', 'buro_api_key', 'buro_costo_consulta'
        ]);
        
        $this->view('configuraciones/buro', [
            'pageTitle' => 'API Buró de Crédito',
            'config' => $config,
            'success' => $success,
            'errors' => $errors
        ]);
    }
    
    public function testEmail() {
        $this->requireRole(['administrador']);
        
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $testEmail = filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL);
        
        if (!$testEmail) {
            echo json_encode(['success' => false, 'message' => 'Correo de destino inválido']);
            exit;
        }
        
        // Get SMTP configuration
        $config = $this->getConfiguraciones([
            'smtp_host', 'smtp_port', 'smtp_user', 'smtp_password', 
            'smtp_from_name', 'smtp_encryption', 'correo_sistema'
        ]);
        
        if (empty($config['smtp_host']) || empty($config['smtp_user']) || empty($config['smtp_password'])) {
            echo json_encode(['success' => false, 'message' => 'Configuración SMTP incompleta. Por favor configure el servidor SMTP primero.']);
            exit;
        }
        
        try {
            $smtpHost = $config['smtp_host'];
            $smtpPort = (int)($config['smtp_port'] ?: 587);
            $smtpUser = $config['smtp_user'];
            $smtpPass = $config['smtp_password'];
            $smtpEncryption = $config['smtp_encryption'] ?: 'tls';
            $fromEmail = $config['correo_sistema'] ?: $smtpUser;
            $fromName = $config['smtp_from_name'] ?: 'Sistema Caja de Ahorros';
            
            // Use socket connection to send email via SMTP
            $result = $this->sendSmtpEmail(
                $smtpHost, 
                $smtpPort, 
                $smtpUser, 
                $smtpPass, 
                $smtpEncryption,
                $fromEmail, 
                $fromName, 
                $testEmail, 
                'Correo de Prueba - Sistema Caja de Ahorros',
                "Este es un correo de prueba para verificar la configuración SMTP.\n\nSi recibe este mensaje, la configuración es correcta.\n\nFecha: " . date('Y-m-d H:i:s') . "\n\nSistema de Gestión Integral de Caja de Ahorros"
            );
            
            if ($result === true) {
                $this->logAction('TEST_EMAIL', "Correo de prueba enviado a: {$testEmail}", 'configuraciones', null);
                echo json_encode(['success' => true, 'message' => 'Correo de prueba enviado exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al enviar: ' . $result]);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    private function sendSmtpEmail($host, $port, $user, $pass, $encryption, $fromEmail, $fromName, $to, $subject, $body) {
        // Determine connection type based on port and encryption
        $secure = '';
        
        if ($encryption === 'ssl' || $port == 465) {
            $secure = 'ssl://';
        }
        
        $errno = 0;
        $errstr = '';
        $timeout = 30;
        
        // Create stream context for SSL/TLS
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);
        
        // Connect to SMTP server
        $socket = @stream_socket_client(
            $secure . $host . ':' . $port,
            $errno,
            $errstr,
            $timeout,
            STREAM_CLIENT_CONNECT,
            $context
        );
        
        if (!$socket) {
            // Provide more helpful error messages
            if ($errno === 0) {
                return "No se pudo conectar al servidor SMTP '{$host}:{$port}'. Verifique que el servidor y puerto sean correctos y que el firewall permita la conexión.";
            }
            return "No se pudo conectar al servidor SMTP: {$errstr} (Error {$errno})";
        }
        
        // Set socket timeout
        stream_set_timeout($socket, $timeout);
        
        // Read greeting (may be multi-line)
        $response = $this->getSmtpResponse($socket);
        if (!$response || substr($response, 0, 3) != '220') {
            fclose($socket);
            return "Error en respuesta inicial del servidor: " . ($response ?: "Sin respuesta");
        }
        
        // Send EHLO and capture capabilities
        fputs($socket, "EHLO localhost\r\n");
        $ehloResponse = $this->getSmtpResponse($socket);
        
        // Parse server capabilities
        $supportsStartTls = (strpos($ehloResponse, 'STARTTLS') !== false);
        $supportsAuthLogin = (strpos($ehloResponse, 'AUTH') !== false && strpos($ehloResponse, 'LOGIN') !== false);
        $supportsAuthPlain = (strpos($ehloResponse, 'AUTH') !== false && strpos($ehloResponse, 'PLAIN') !== false);
        
        // Start TLS if needed and supported (for port 587 or explicit TLS)
        if ($encryption === 'tls' && $port != 465) {
            if (!$supportsStartTls) {
                // Try to continue without STARTTLS for servers that don't support it
                // This might work for local/internal SMTP servers
            } else {
                fputs($socket, "STARTTLS\r\n");
                $response = $this->getSmtpResponse($socket);
                if ($response && substr($response, 0, 3) == '220') {
                    // Enable TLS on the socket
                    $cryptoEnabled = @stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT);
                    if (!$cryptoEnabled) {
                        // Try with TLS 1.1 or 1.0
                        $cryptoEnabled = @stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                    }
                    
                    if ($cryptoEnabled) {
                        // Send EHLO again after STARTTLS
                        fputs($socket, "EHLO localhost\r\n");
                        $ehloResponse = $this->getSmtpResponse($socket);
                        // Update capabilities after TLS
                        $supportsAuthLogin = (strpos($ehloResponse, 'AUTH') !== false && strpos($ehloResponse, 'LOGIN') !== false);
                        $supportsAuthPlain = (strpos($ehloResponse, 'AUTH') !== false && strpos($ehloResponse, 'PLAIN') !== false);
                    }
                    // If TLS fails, continue without it (some servers allow this)
                }
            }
        }
        
        // Try to authenticate if credentials provided
        $authSuccess = false;
        if (!empty($user) && !empty($pass)) {
            // Try AUTH LOGIN first
            if ($supportsAuthLogin) {
                fputs($socket, "AUTH LOGIN\r\n");
                $response = fgets($socket, 515);
                if ($response && substr($response, 0, 3) == '334') {
                    fputs($socket, base64_encode($user) . "\r\n");
                    $response = fgets($socket, 515);
                    if ($response && substr($response, 0, 3) == '334') {
                        fputs($socket, base64_encode($pass) . "\r\n");
                        $response = fgets($socket, 515);
                        if ($response && substr($response, 0, 3) == '235') {
                            $authSuccess = true;
                        }
                    }
                }
            }
            
            // Try AUTH PLAIN if LOGIN failed
            if (!$authSuccess && $supportsAuthPlain) {
                $authString = base64_encode("\0" . $user . "\0" . $pass);
                fputs($socket, "AUTH PLAIN {$authString}\r\n");
                $response = fgets($socket, 515);
                if ($response && substr($response, 0, 3) == '235') {
                    $authSuccess = true;
                }
            }
            
            // If neither worked and auth was attempted
            if (!$authSuccess && ($supportsAuthLogin || $supportsAuthPlain)) {
                fclose($socket);
                return "Error de autenticación: Usuario o contraseña SMTP incorrectos. Verifique las credenciales.";
            }
            
            // If server doesn't support any auth method, try without auth
            if (!$authSuccess && !$supportsAuthLogin && !$supportsAuthPlain) {
                // Continue without authentication (might work for internal servers)
            }
        }
        
        // Send email
        fputs($socket, "MAIL FROM:<{$fromEmail}>\r\n");
        $response = $this->getSmtpResponse($socket);
        if (!$response || substr($response, 0, 3) != '250') {
            fclose($socket);
            return "Error en MAIL FROM: " . ($response ?: "Sin respuesta") . ". Verifique el correo del remitente.";
        }
        
        fputs($socket, "RCPT TO:<{$to}>\r\n");
        $response = $this->getSmtpResponse($socket);
        if (!$response || substr($response, 0, 3) != '250') {
            fclose($socket);
            return "Error en RCPT TO: " . ($response ?: "Sin respuesta") . ". Verifique el correo del destinatario.";
        }
        
        fputs($socket, "DATA\r\n");
        $response = $this->getSmtpResponse($socket);
        if (!$response || substr($response, 0, 3) != '354') {
            fclose($socket);
            return "Error en DATA: " . ($response ?: "Sin respuesta");
        }
        
        // Build email message
        $headers = "From: {$fromName} <{$fromEmail}>\r\n";
        $headers .= "To: {$to}\r\n";
        $headers .= "Subject: {$subject}\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers .= "Date: " . date('r') . "\r\n";
        
        fputs($socket, $headers . "\r\n" . $body . "\r\n.\r\n");
        $response = $this->getSmtpResponse($socket);
        if (!$response || substr($response, 0, 3) != '250') {
            fclose($socket);
            return "Error al enviar mensaje: " . ($response ?: "Sin respuesta");
        }
        
        // Quit
        fputs($socket, "QUIT\r\n");
        fclose($socket);
        
        return true;
    }
    
    private function getSmtpResponse($socket) {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) == ' ') {
                break;
            }
        }
        return $response;
    }
    
    public function modulos() {
        $this->requireRole(['programador']);
        
        $success = '';
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            // Get disabled modules from checkboxes
            $modulosDeshabilitados = [];
            $modulosPosibles = ['financiero', 'membresias', 'inversionistas', 'crm', 'kyc', 'escrow'];
            
            foreach ($modulosPosibles as $modulo) {
                if (isset($_POST['deshabilitar_' . $modulo])) {
                    $modulosDeshabilitados[] = $modulo;
                }
            }
            
            // Save as JSON
            $this->guardarConfiguracion('modulos_deshabilitados', json_encode($modulosDeshabilitados));
            
            // Clear config cache so changes take effect immediately
            clearConfigCache();
            
            $this->logAction('ACTUALIZAR_MODULOS', 'Se actualizó configuración de módulos especiales', 'configuraciones', null);
            $success = 'Configuración de módulos guardada exitosamente';
        }
        
        $config = $this->getConfiguraciones(['modulos_deshabilitados']);
        $modulosDeshabilitados = json_decode($config['modulos_deshabilitados'] ?: '[]', true) ?: [];
        
        $this->view('configuraciones/modulos', [
            'pageTitle' => 'Módulos Especiales',
            'modulosDeshabilitados' => $modulosDeshabilitados,
            'success' => $success,
            'errors' => $errors
        ]);
    }
    
    public function chatbot() {
        $this->requireRole(['administrador', 'programador']);
        
        $success = '';
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            // Save chatbot configuration
            $campos = [
                'chatbot_whatsapp_numero',
                'chatbot_url_publica',
                'chatbot_mensaje_bienvenida',
                'chatbot_mensaje_horario',
                'chatbot_mensaje_fuera_horario'
            ];
            
            foreach ($campos as $campo) {
                if (isset($_POST[$campo])) {
                    $this->guardarConfiguracion($campo, $this->sanitize($_POST[$campo]));
                }
            }
            
            // Clear config cache so changes take effect immediately
            clearConfigCache();
            
            $this->logAction('ACTUALIZAR_CHATBOT', 'Se actualizó configuración del chatbot', 'configuraciones', null);
            $success = 'Configuración del chatbot guardada exitosamente';
        }
        
        $config = $this->getConfiguraciones([
            'chatbot_whatsapp_numero',
            'chatbot_url_publica',
            'chatbot_mensaje_bienvenida',
            'chatbot_mensaje_horario',
            'chatbot_mensaje_fuera_horario'
        ]);
        
        $this->view('configuraciones/chatbot', [
            'pageTitle' => 'Configuración del Chatbot',
            'config' => $config,
            'success' => $success,
            'errors' => $errors
        ]);
    }
    
    private function guardarConfiguracion($clave, $valor) {
        $existe = $this->db->fetch(
            "SELECT id FROM configuraciones WHERE clave = :clave",
            ['clave' => $clave]
        );
        
        if ($existe) {
            $this->db->update('configuraciones', ['valor' => $valor], 'clave = :clave', ['clave' => $clave]);
        } else {
            $this->db->insert('configuraciones', ['clave' => $clave, 'valor' => $valor]);
        }
    }
    
    private function getConfiguraciones($claves) {
        $config = [];
        foreach ($claves as $clave) {
            $row = $this->db->fetch(
                "SELECT valor FROM configuraciones WHERE clave = :clave",
                ['clave' => $clave]
            );
            $config[$clave] = $row['valor'] ?? '';
        }
        return $config;
    }
}
