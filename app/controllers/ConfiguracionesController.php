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
        
        // Connect to SMTP server
        $socket = @fsockopen($secure . $host, $port, $errno, $errstr, $timeout);
        
        if (!$socket) {
            return "No se pudo conectar al servidor SMTP: {$errstr} ({$errno})";
        }
        
        // Set socket timeout
        stream_set_timeout($socket, $timeout);
        
        // Read greeting
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '220') {
            fclose($socket);
            return "Error en respuesta inicial del servidor: {$response}";
        }
        
        // Send EHLO
        fputs($socket, "EHLO {$host}\r\n");
        $response = $this->getSmtpResponse($socket);
        
        // Start TLS if needed (for port 587 or explicit TLS)
        if ($encryption === 'tls' && $port != 465) {
            fputs($socket, "STARTTLS\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '220') {
                fclose($socket);
                return "Error al iniciar TLS: {$response}";
            }
            
            // Enable TLS on the socket
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                fclose($socket);
                return "Error al habilitar encriptación TLS";
            }
            
            // Send EHLO again after STARTTLS
            fputs($socket, "EHLO {$host}\r\n");
            $response = $this->getSmtpResponse($socket);
        }
        
        // Authenticate
        fputs($socket, "AUTH LOGIN\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '334') {
            fclose($socket);
            return "Error en autenticación: {$response}";
        }
        
        fputs($socket, base64_encode($user) . "\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '334') {
            fclose($socket);
            return "Error en usuario: {$response}";
        }
        
        fputs($socket, base64_encode($pass) . "\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '235') {
            fclose($socket);
            return "Error en contraseña: {$response}";
        }
        
        // Send email
        fputs($socket, "MAIL FROM:<{$fromEmail}>\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '250') {
            fclose($socket);
            return "Error en MAIL FROM: {$response}";
        }
        
        fputs($socket, "RCPT TO:<{$to}>\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '250') {
            fclose($socket);
            return "Error en RCPT TO: {$response}";
        }
        
        fputs($socket, "DATA\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '354') {
            fclose($socket);
            return "Error en DATA: {$response}";
        }
        
        // Build email message
        $headers = "From: {$fromName} <{$fromEmail}>\r\n";
        $headers .= "To: {$to}\r\n";
        $headers .= "Subject: {$subject}\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers .= "Date: " . date('r') . "\r\n";
        
        fputs($socket, $headers . "\r\n" . $body . "\r\n.\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '250') {
            fclose($socket);
            return "Error al enviar mensaje: {$response}";
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
