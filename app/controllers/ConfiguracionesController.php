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
            
            $campos = ['nombre_sitio', 'telefono_contacto', 'horario_atencion', 'email_contacto', 'cuota_mantenimiento', 'texto_copyright'];
            
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
        
        $config = $this->getConfiguraciones(['nombre_sitio', 'logo', 'telefono_contacto', 'horario_atencion', 'email_contacto', 'cuota_mantenimiento', 'texto_copyright']);
        
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
