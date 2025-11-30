<?php
/**
 * Controlador de Usuarios
 * Sistema de Gestión Integral de Caja de Ahorros
 */

require_once CORE_PATH . '/Controller.php';

class UsuariosController extends Controller {
    
    public function index() {
        $this->requireRole(['administrador']);
        
        $usuarios = $this->db->fetchAll(
            "SELECT * FROM usuarios ORDER BY nombre"
        );
        
        $this->view('usuarios/index', [
            'pageTitle' => 'Gestión de Usuarios',
            'usuarios' => $usuarios
        ]);
    }
    
    public function crear() {
        $this->requireRole(['administrador']);
        
        $errors = [];
        $data = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $data = $this->sanitize($_POST);
            
            // Validaciones
            if (empty($data['nombre'])) $errors[] = 'El nombre es requerido';
            if (empty($data['email'])) $errors[] = 'El correo es requerido';
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Correo electrónico inválido';
            if (empty($data['password'])) $errors[] = 'La contraseña es requerida';
            if (strlen($data['password']) < 6) $errors[] = 'La contraseña debe tener al menos 6 caracteres';
            if ($data['password'] !== $data['password_confirm']) $errors[] = 'Las contraseñas no coinciden';
            
            // Verificar email único
            $exists = $this->db->fetch(
                "SELECT id FROM usuarios WHERE email = :email",
                ['email' => $data['email']]
            );
            if ($exists) $errors[] = 'El correo ya está registrado';
            
            if (empty($errors)) {
                $userId = $this->db->insert('usuarios', [
                    'nombre' => $data['nombre'],
                    'email' => $data['email'],
                    'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                    'rol' => $data['rol'] ?? 'consulta',
                    'activo' => 1
                ]);
                
                $this->logAction('CREAR_USUARIO', "Se creó el usuario {$data['nombre']}", 'usuarios', $userId);
                
                $this->setFlash('success', 'Usuario creado exitosamente');
                $this->redirect('usuarios');
            }
        }
        
        $this->view('usuarios/form', [
            'pageTitle' => 'Nuevo Usuario',
            'action' => 'crear',
            'usuario' => $data,
            'errors' => $errors
        ]);
    }
    
    public function editar() {
        $this->requireRole(['administrador']);
        
        $id = $this->params['id'] ?? 0;
        $usuario = $this->db->fetch("SELECT * FROM usuarios WHERE id = :id", ['id' => $id]);
        
        if (!$usuario) {
            $this->setFlash('error', 'Usuario no encontrado');
            $this->redirect('usuarios');
        }
        
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $data = $this->sanitize($_POST);
            
            // Validaciones
            if (empty($data['nombre'])) $errors[] = 'El nombre es requerido';
            if (empty($data['email'])) $errors[] = 'El correo es requerido';
            
            // Verificar email único (excluyendo actual)
            $exists = $this->db->fetch(
                "SELECT id FROM usuarios WHERE email = :email AND id != :id",
                ['email' => $data['email'], 'id' => $id]
            );
            if ($exists) $errors[] = 'El correo ya está registrado';
            
            if (empty($errors)) {
                $updateData = [
                    'nombre' => $data['nombre'],
                    'email' => $data['email'],
                    'rol' => $data['rol'] ?? 'consulta',
                    'activo' => isset($data['activo']) ? 1 : 0
                ];
                
                // Si se proporciona nueva contraseña
                if (!empty($data['password'])) {
                    if (strlen($data['password']) < 6) {
                        $errors[] = 'La contraseña debe tener al menos 6 caracteres';
                    } elseif ($data['password'] !== $data['password_confirm']) {
                        $errors[] = 'Las contraseñas no coinciden';
                    } else {
                        $updateData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                    }
                }
                
                if (empty($errors)) {
                    $this->db->update('usuarios', $updateData, 'id = :id', ['id' => $id]);
                    
                    $this->logAction('EDITAR_USUARIO', "Se editó el usuario {$data['nombre']}", 'usuarios', $id);
                    
                    $this->setFlash('success', 'Usuario actualizado exitosamente');
                    $this->redirect('usuarios');
                }
            }
            
            $usuario = array_merge($usuario, $data);
        }
        
        $this->view('usuarios/form', [
            'pageTitle' => 'Editar Usuario',
            'action' => 'editar',
            'usuario' => $usuario,
            'errors' => $errors
        ]);
    }
    
    public function eliminar() {
        $this->requireRole(['administrador']);
        
        $id = $this->params['id'] ?? 0;
        
        // No permitir eliminar el propio usuario
        if ($id == $_SESSION['user_id']) {
            $this->setFlash('error', 'No puedes eliminar tu propio usuario');
            $this->redirect('usuarios');
        }
        
        $usuario = $this->db->fetch("SELECT * FROM usuarios WHERE id = :id", ['id' => $id]);
        
        if ($usuario) {
            $this->db->update('usuarios', ['activo' => 0], 'id = :id', ['id' => $id]);
            $this->logAction('DESACTIVAR_USUARIO', "Se desactivó el usuario {$usuario['nombre']}", 'usuarios', $id);
            $this->setFlash('success', 'Usuario desactivado exitosamente');
        }
        
        $this->redirect('usuarios');
    }
    
    public function perfil() {
        $this->requireAuth();
        
        $id = $_SESSION['user_id'];
        $usuario = $this->db->fetch("SELECT * FROM usuarios WHERE id = :id", ['id' => $id]);
        
        $errors = [];
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $data = $this->sanitize($_POST);
            
            // Cambiar contraseña
            if (!empty($data['password_actual'])) {
                if (!password_verify($data['password_actual'], $usuario['password'])) {
                    $errors[] = 'La contraseña actual es incorrecta';
                } elseif (strlen($data['password_nueva']) < 6) {
                    $errors[] = 'La nueva contraseña debe tener al menos 6 caracteres';
                } elseif ($data['password_nueva'] !== $data['password_confirmar']) {
                    $errors[] = 'Las contraseñas no coinciden';
                } else {
                    $this->db->update('usuarios', [
                        'password' => password_hash($data['password_nueva'], PASSWORD_DEFAULT)
                    ], 'id = :id', ['id' => $id]);
                    
                    $this->logAction('CAMBIAR_PASSWORD', 'Se cambió la contraseña', 'usuarios', $id);
                    $success = 'Contraseña actualizada exitosamente';
                }
            }
            
            // Actualizar datos básicos
            if (empty($errors) && !empty($data['nombre'])) {
                $this->db->update('usuarios', [
                    'nombre' => $data['nombre']
                ], 'id = :id', ['id' => $id]);
                
                $_SESSION['user_nombre'] = $data['nombre'];
                $usuario['nombre'] = $data['nombre'];
                
                if (empty($success)) {
                    $success = 'Perfil actualizado exitosamente';
                }
            }
        }
        
        $this->view('usuarios/perfil', [
            'pageTitle' => 'Mi Perfil',
            'usuario' => $usuario,
            'errors' => $errors,
            'success' => $success
        ]);
    }
}
