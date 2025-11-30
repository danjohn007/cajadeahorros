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
            
            $campos = ['nombre_sitio', 'telefono_contacto', 'horario_atencion'];
            
            foreach ($campos as $campo) {
                if (isset($_POST[$campo])) {
                    $this->guardarConfiguracion($campo, $this->sanitize($_POST[$campo]));
                }
            }
            
            // Procesar logo si se subió
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $nombreArchivo = 'logo.' . $ext;
                    $ruta = PUBLIC_PATH . '/images/' . $nombreArchivo;
                    if (move_uploaded_file($_FILES['logo']['tmp_name'], $ruta)) {
                        $this->guardarConfiguracion('logo', $nombreArchivo);
                    }
                } else {
                    $errors[] = 'Formato de imagen no válido';
                }
            }
            
            if (empty($errors)) {
                $this->logAction('ACTUALIZAR_CONFIG', 'Se actualizaron configuraciones generales', 'configuraciones', null);
                $success = 'Configuraciones guardadas exitosamente';
            }
        }
        
        $config = $this->getConfiguraciones(['nombre_sitio', 'logo', 'telefono_contacto', 'horario_atencion']);
        
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
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $this->guardarConfiguracion('correo_sistema', $this->sanitize($_POST['correo_sistema'] ?? ''));
            
            $this->logAction('ACTUALIZAR_CONFIG', 'Se actualizó configuración de correo', 'configuraciones', null);
            $success = 'Configuración de correo guardada';
        }
        
        $config = $this->getConfiguraciones(['correo_sistema']);
        
        $this->view('configuraciones/correo', [
            'pageTitle' => 'Configuración de Correo',
            'config' => $config,
            'success' => $success
        ]);
    }
    
    public function estilos() {
        $this->requireRole(['administrador']);
        
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $this->guardarConfiguracion('color_primario', $this->sanitize($_POST['color_primario'] ?? '#1e40af'));
            $this->guardarConfiguracion('color_secundario', $this->sanitize($_POST['color_secundario'] ?? '#3b82f6'));
            
            $this->logAction('ACTUALIZAR_CONFIG', 'Se actualizaron estilos del sistema', 'configuraciones', null);
            $success = 'Estilos guardados exitosamente';
        }
        
        $config = $this->getConfiguraciones(['color_primario', 'color_secundario']);
        
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
            
            $this->guardarConfiguracion('paypal_client_id', $this->sanitize($_POST['paypal_client_id'] ?? ''));
            if (!empty($_POST['paypal_secret'])) {
                $this->guardarConfiguracion('paypal_secret', $this->sanitize($_POST['paypal_secret']));
            }
            
            $this->logAction('ACTUALIZAR_CONFIG', 'Se actualizó configuración de PayPal', 'configuraciones', null);
            $success = 'Configuración de PayPal guardada';
        }
        
        $config = $this->getConfiguraciones(['paypal_client_id', 'paypal_secret']);
        
        $this->view('configuraciones/paypal', [
            'pageTitle' => 'Configuración de PayPal',
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
