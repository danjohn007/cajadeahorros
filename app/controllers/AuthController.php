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
}
