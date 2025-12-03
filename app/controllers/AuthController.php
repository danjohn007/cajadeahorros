<?php
/**
 * Controlador de autenticación
 * Sistema de Gestión Integral de Caja de Ahorros
 */

require_once CORE_PATH . '/Controller.php';

class AuthController extends Controller {
    
    public function login() {
        // Si ya está autenticado, redirigir al dashboard
        if ($this->isLoggedIn()) {
            // Si es cliente, redirigir al portal cliente
            if ($_SESSION['user_role'] === 'cliente') {
                $this->redirect('cliente');
            }
            $this->redirect('dashboard');
        }
        
        $error = '';
        $email = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $this->sanitize($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            // Validar CSRF
            $token = $_POST['csrf_token'] ?? '';
            if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
                $error = 'Token de seguridad inválido. Por favor, recarga la página.';
            } else {
                // Buscar usuario
                $user = $this->db->fetch(
                    "SELECT * FROM usuarios WHERE email = :email AND activo = 1",
                    ['email' => $email]
                );
                
                if ($user && password_verify($password, $user['password'])) {
                    // Login exitoso
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_nombre'] = $user['nombre'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['rol'];
                    $_SESSION['requiere_cambio_password'] = $user['requiere_cambio_password'] ?? 0;
                    
                    // Actualizar último acceso
                    $this->db->update('usuarios', 
                        ['ultimo_acceso' => date('Y-m-d H:i:s')],
                        'id = :id',
                        ['id' => $user['id']]
                    );
                    
                    // Registrar en bitácora
                    $this->logAction('LOGIN', 'Inicio de sesión exitoso', 'usuarios', $user['id']);
                    
                    // Si requiere cambio de contraseña, redirigir al perfil
                    if (!empty($user['requiere_cambio_password'])) {
                        $this->setFlash('warning', 'Por seguridad, debes cambiar tu contraseña en el primer inicio de sesión.');
                        $this->redirect('usuarios/perfil');
                    }
                    
                    // Si es cliente, redirigir al portal cliente
                    if ($user['rol'] === 'cliente') {
                        $this->redirect('cliente');
                    }
                    
                    $this->redirect('dashboard');
                } else {
                    $error = 'Credenciales incorrectas. Por favor, verifica tu correo y contraseña.';
                    
                    // Registrar intento fallido
                    $this->db->insert('bitacora', [
                        'accion' => 'LOGIN_FALLIDO',
                        'descripcion' => 'Intento de login fallido para: ' . $email,
                        'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                        'fecha' => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }
        
        $this->viewPartial('auth/login', [
            'error' => $error,
            'email' => $email,
            'csrf_token' => $this->csrf_token()
        ]);
    }
    
    public function logout() {
        if ($this->isLoggedIn()) {
            $this->logAction('LOGOUT', 'Cierre de sesión', 'usuarios', $_SESSION['user_id']);
        }
        
        // Destruir sesión
        session_unset();
        session_destroy();
        
        $this->redirect('auth/login');
    }
    
    public function forgotPassword() {
        $success = '';
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $this->sanitize($_POST['email'] ?? '');
            
            $user = $this->db->fetch(
                "SELECT * FROM usuarios WHERE email = :email AND activo = 1",
                ['email' => $email]
            );
            
            if ($user) {
                // Generar token de recuperación
                $token = bin2hex(random_bytes(32));
                $expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                $this->db->update('usuarios',
                    [
                        'token_recuperacion' => $token,
                        'token_expiracion' => $expiration
                    ],
                    'id = :id',
                    ['id' => $user['id']]
                );
                
                // En producción aquí se enviaría el correo
                $success = 'Si el correo existe en nuestro sistema, recibirás instrucciones para recuperar tu contraseña.';
                
                $this->logAction('RECUPERAR_PASSWORD', 'Solicitud de recuperación de contraseña', 'usuarios', $user['id']);
            } else {
                // Por seguridad, mostrar el mismo mensaje
                $success = 'Si el correo existe en nuestro sistema, recibirás instrucciones para recuperar tu contraseña.';
            }
        }
        
        $this->viewPartial('auth/forgot-password', [
            'success' => $success,
            'error' => $error,
            'csrf_token' => $this->csrf_token()
        ]);
    }
    
    public function registro() {
        // Si ya está autenticado, redirigir
        if ($this->isLoggedIn()) {
            $this->redirect('dashboard');
        }
        
        $errors = [];
        $success = '';
        $data = [];
        $socioEncontrado = null;
        
        // Generar captcha
        if (!isset($_SESSION['captcha_num1']) || !isset($_SESSION['captcha_num2'])) {
            $_SESSION['captcha_num1'] = rand(1, 9);
            $_SESSION['captcha_num2'] = rand(1, 9);
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar CSRF
            $token = $_POST['csrf_token'] ?? '';
            if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
                $errors[] = 'Token de seguridad inválido. Por favor, recarga la página.';
            } else {
                $data = $this->sanitize($_POST);
                
                // Validar captcha
                $captchaRespuesta = (int)($_POST['captcha'] ?? 0);
                $captchaEsperado = $_SESSION['captcha_num1'] + $_SESSION['captcha_num2'];
                
                if ($captchaRespuesta !== $captchaEsperado) {
                    $errors[] = 'La respuesta del captcha es incorrecta';
                }
                
                // Validaciones
                if (empty($data['nombre'])) $errors[] = 'El nombre es requerido';
                if (empty($data['email'])) $errors[] = 'El correo electrónico es requerido';
                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'El correo electrónico no es válido';
                if (empty($data['password'])) $errors[] = 'La contraseña es requerida';
                if (strlen($data['password'] ?? '') < 6) $errors[] = 'La contraseña debe tener al menos 6 caracteres';
                if ($data['password'] !== ($data['password_confirm'] ?? '')) $errors[] = 'Las contraseñas no coinciden';
                
                // Validar teléfono (max 10 dígitos)
                if (!empty($data['telefono'])) {
                    $telefono = preg_replace('/[^0-9]/', '', $data['telefono']);
                    if (strlen($telefono) > 10) {
                        $errors[] = 'El teléfono debe tener máximo 10 dígitos';
                    }
                }
                
                // Validar celular (max 10 dígitos)
                if (!empty($data['celular'])) {
                    $celular = preg_replace('/[^0-9]/', '', $data['celular']);
                    if (strlen($celular) > 10) {
                        $errors[] = 'El celular debe tener máximo 10 dígitos';
                    }
                }
                
                // Validar términos
                if (empty($data['acepta_terminos'])) {
                    $errors[] = 'Debe aceptar los términos y condiciones';
                }
                
                // Verificar email único
                if (empty($errors)) {
                    $exists = $this->db->fetch(
                        "SELECT id FROM usuarios WHERE email = :email",
                        ['email' => $data['email']]
                    );
                    if ($exists) $errors[] = 'El correo electrónico ya está registrado';
                }
                
                if (empty($errors)) {
                    // Crear usuario cliente
                    $userId = $this->db->insert('usuarios', [
                        'nombre' => $data['nombre'],
                        'email' => $data['email'],
                        'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                        'telefono' => $data['telefono'] ?? null,
                        'celular' => $data['celular'] ?? null,
                        'rol' => 'cliente',
                        'activo' => 1,
                        'requiere_cambio_password' => 0
                    ]);
                    
                    // Buscar si existe un socio con este email, teléfono o celular y vincularlo
                    $socio = $this->buscarSocioParaVincular($data);
                    
                    if ($socio) {
                        // Vincular usuario con socio existente
                        $this->db->insert('usuarios_socios', [
                            'usuario_id' => $userId,
                            'socio_id' => $socio['id']
                        ]);
                    }
                    
                    // Registrar en bitácora
                    $this->db->insert('bitacora', [
                        'usuario_id' => $userId,
                        'accion' => 'REGISTRO_CLIENTE',
                        'descripcion' => 'Registro de cliente: ' . $data['nombre'] . ($socio ? ' - Vinculado a socio ' . $socio['numero_socio'] : ' - Cuenta no vinculada'),
                        'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                        'fecha' => date('Y-m-d H:i:s')
                    ]);
                    
                    $success = '¡Registro exitoso! Ahora puedes iniciar sesión con tus credenciales.';
                    if (!$socio) {
                        $success .= ' Tu cuenta aún no está vinculada a un socio. Podrás solicitar la vinculación desde tu portal.';
                    }
                    $data = []; // Limpiar datos
                    
                    // Regenerar captcha
                    $_SESSION['captcha_num1'] = rand(1, 9);
                    $_SESSION['captcha_num2'] = rand(1, 9);
                }
            }
            
            // Si hubo errores, regenerar captcha
            if (!empty($errors)) {
                $_SESSION['captcha_num1'] = rand(1, 9);
                $_SESSION['captcha_num2'] = rand(1, 9);
            }
        }
        
        $this->viewPartial('auth/registro', [
            'errors' => $errors,
            'success' => $success,
            'data' => $data,
            'captcha_num1' => $_SESSION['captcha_num1'],
            'captcha_num2' => $_SESSION['captcha_num2'],
            'csrf_token' => $this->csrf_token()
        ]);
    }
    
    /**
     * Buscar socio existente en la DB por email, teléfono o celular para vincular
     */
    private function buscarSocioParaVincular($data) {
        // Buscar por email
        if (!empty($data['email'])) {
            $socio = $this->db->fetch(
                "SELECT id, numero_socio FROM socios WHERE email = :email AND estatus = 'activo'",
                ['email' => $data['email']]
            );
            if ($socio) return $socio;
        }
        
        // Buscar por celular
        if (!empty($data['celular'])) {
            $celular = preg_replace('/[^0-9]/', '', $data['celular']);
            $socio = $this->db->fetch(
                "SELECT id, numero_socio FROM socios WHERE (celular = :celular OR celular = :celular2) AND estatus = 'activo'",
                ['celular' => $celular, 'celular2' => $data['celular']]
            );
            if ($socio) return $socio;
        }
        
        // Buscar por teléfono
        if (!empty($data['telefono'])) {
            $telefono = preg_replace('/[^0-9]/', '', $data['telefono']);
            $socio = $this->db->fetch(
                "SELECT id, numero_socio FROM socios WHERE (telefono = :telefono OR telefono = :telefono2) AND estatus = 'activo'",
                ['telefono' => $telefono, 'telefono2' => $data['telefono']]
            );
            if ($socio) return $socio;
        }
        
        return null;
    }
    
    /**
     * API para buscar socios por teléfono, email o WhatsApp en el registro público
     */
    public function buscarSocio() {
        header('Content-Type: application/json');
        
        $query = $_GET['q'] ?? '';
        if (strlen($query) < 3) {
            echo json_encode(['found' => false, 'socio' => null]);
            exit;
        }
        
        // Limpiar solo dígitos si parece un teléfono
        $queryDigits = preg_replace('/[^0-9]/', '', $query);
        
        // Buscar por email, teléfono o celular
        $socio = $this->db->fetch(
            "SELECT id, nombre, apellido_paterno, apellido_materno, email, telefono, celular, numero_socio
             FROM socios 
             WHERE estatus = 'activo' AND (
                 email = :email OR 
                 telefono = :tel1 OR telefono = :tel2 OR
                 celular = :cel1 OR celular = :cel2
             )
             LIMIT 1",
            [
                'email' => $query,
                'tel1' => $query,
                'tel2' => strlen($queryDigits) >= 10 ? $queryDigits : '',
                'cel1' => $query,
                'cel2' => strlen($queryDigits) >= 10 ? $queryDigits : ''
            ]
        );
        
        if ($socio) {
            echo json_encode([
                'found' => true,
                'socio' => [
                    'nombre' => $socio['nombre'] . ' ' . $socio['apellido_paterno'] . ' ' . ($socio['apellido_materno'] ?? ''),
                    'email' => $socio['email'] ? substr($socio['email'], 0, 3) . '***@***' . substr($socio['email'], strrpos($socio['email'], '.')) : '',
                    'telefono' => $socio['telefono'] ? '****' . substr($socio['telefono'], -4) : '',
                    'celular' => $socio['celular'] ? '****' . substr($socio['celular'], -4) : '',
                    'numero_socio' => $socio['numero_socio']
                ]
            ]);
        } else {
            echo json_encode(['found' => false, 'socio' => null]);
        }
        exit;
    }
}
