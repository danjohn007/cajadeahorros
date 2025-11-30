<?php
/**
 * Controlador base del sistema
 * Sistema de Gestión Integral de Caja de Ahorros
 */

abstract class Controller {
    protected $params = [];
    protected $db;

    public function __construct($params = []) {
        $this->params = $params;
        $this->db = Database::getInstance();
    }

    protected function view($view, $data = []) {
        extract($data);
        $viewFile = APP_PATH . '/views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            ob_start();
            require $viewFile;
            $content = ob_get_clean();
            
            // Cargar el layout principal
            require APP_PATH . '/views/layouts/main.php';
        } else {
            throw new Exception("Vista {$view} no encontrada");
        }
    }

    protected function viewPartial($view, $data = []) {
        extract($data);
        $viewFile = APP_PATH . '/views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            throw new Exception("Vista parcial {$view} no encontrada");
        }
    }

    protected function redirect($url) {
        header('Location: ' . BASE_URL . '/' . ltrim($url, '/'));
        exit;
    }

    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
        }
    }

    protected function requireRole($roles) {
        $this->requireAuth();
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        if (!in_array($_SESSION['user_role'], $roles)) {
            $this->redirect('dashboard?error=acceso_denegado');
        }
    }

    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    protected function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'nombre' => $_SESSION['user_nombre'],
                'email' => $_SESSION['user_email'],
                'role' => $_SESSION['user_role']
            ];
        }
        return null;
    }

    protected function setFlash($type, $message) {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }

    protected function getFlash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    protected function csrf_token() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function validateCsrf() {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            $this->setFlash('error', 'Token de seguridad inválido');
            $this->redirect($_SERVER['HTTP_REFERER'] ?? 'dashboard');
        }
    }

    protected function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    protected function logAction($action, $description, $entity = null, $entityId = null) {
        $this->db->insert('bitacora', [
            'usuario_id' => $_SESSION['user_id'] ?? null,
            'accion' => $action,
            'descripcion' => $description,
            'entidad' => $entity,
            'entidad_id' => $entityId,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'fecha' => date('Y-m-d H:i:s')
        ]);
    }
}
