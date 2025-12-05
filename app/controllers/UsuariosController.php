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
        
        // Get linked socio if client
        $socio = null;
        $solicitudesPendientes = [];
        if ($_SESSION['user_role'] === 'cliente') {
            $vinculo = $this->db->fetch(
                "SELECT socio_id FROM usuarios_socios WHERE usuario_id = :user_id",
                ['user_id' => $id]
            );
            if ($vinculo) {
                $socio = $this->db->fetch(
                    "SELECT * FROM socios WHERE id = :id",
                    ['id' => $vinculo['socio_id']]
                );
            }
        }
        
        // Get pending profile update requests
        $solicitudesPendientes = $this->db->fetchAll(
            "SELECT * FROM solicitudes_actualizacion_perfil WHERE usuario_id = :user_id ORDER BY created_at DESC LIMIT 5",
            ['user_id' => $id]
        );
        
        $errors = [];
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $data = $this->sanitize($_POST);
            $action = $data['action'] ?? 'update_profile';
            
            if ($action === 'update_password') {
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
                            'password' => password_hash($data['password_nueva'], PASSWORD_DEFAULT),
                            'requiere_cambio_password' => 0
                        ], 'id = :id', ['id' => $id]);
                        
                        // Actualizar sesión
                        $_SESSION['requiere_cambio_password'] = 0;
                        
                        $this->logAction('CAMBIAR_PASSWORD', 'Se cambió la contraseña', 'usuarios', $id);
                        $success = 'Contraseña actualizada exitosamente';
                    }
                }
            } elseif ($action === 'update_profile') {
                // Handle profile image upload
                $avatarFileName = $usuario['avatar'] ?? '';
                if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES['avatar'];
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                    $maxSize = 2 * 1024 * 1024; // 2MB
                    
                    $fileType = mime_content_type($file['tmp_name']);
                    if (in_array($fileType, $allowedTypes) && $file['size'] <= $maxSize) {
                        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                        $nombreArchivo = 'avatar_' . $id . '_' . time() . '.' . $ext;
                        
                        $uploadDir = UPLOADS_PATH . '/avatars';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }
                        
                        $ruta = $uploadDir . '/' . $nombreArchivo;
                        if (move_uploaded_file($file['tmp_name'], $ruta)) {
                            // Delete old avatar if exists
                            if ($avatarFileName && file_exists(UPLOADS_PATH . '/avatars/' . $avatarFileName)) {
                                unlink(UPLOADS_PATH . '/avatars/' . $avatarFileName);
                            }
                            $avatarFileName = $nombreArchivo;
                        }
                    } else {
                        $errors[] = 'El archivo de imagen debe ser JPEG, PNG o GIF y no exceder 2MB';
                    }
                }
                
                // Actualizar datos básicos
                if (empty($errors) && !empty($data['nombre'])) {
                    $this->db->update('usuarios', [
                        'nombre' => $data['nombre'],
                        'telefono' => $data['telefono'] ?? null,
                        'celular' => $data['celular'] ?? null,
                        'avatar' => $avatarFileName ?: null
                    ], 'id = :id', ['id' => $id]);
                    
                    $_SESSION['user_nombre'] = $data['nombre'];
                    $usuario['nombre'] = $data['nombre'];
                    $usuario['telefono'] = $data['telefono'] ?? null;
                    $usuario['celular'] = $data['celular'] ?? null;
                    $usuario['avatar'] = $avatarFileName ?: null;
                    
                    $this->logAction('ACTUALIZAR_PERFIL', 'Se actualizó el perfil', 'usuarios', $id);
                    $success = 'Perfil actualizado exitosamente';
                }
            } elseif ($action === 'request_update') {
                // Client requesting contact info update that needs review
                $cambiosSolicitados = [];
                if (!empty($data['nuevo_email']) && $data['nuevo_email'] !== $usuario['email']) {
                    $cambiosSolicitados['email'] = $data['nuevo_email'];
                }
                if (!empty($data['nuevo_telefono']) && $data['nuevo_telefono'] !== ($usuario['telefono'] ?? '')) {
                    $cambiosSolicitados['telefono'] = $data['nuevo_telefono'];
                }
                if (!empty($data['nuevo_celular']) && $data['nuevo_celular'] !== ($usuario['celular'] ?? '')) {
                    $cambiosSolicitados['celular'] = $data['nuevo_celular'];
                }
                if (!empty($data['nueva_direccion']) && $socio && $data['nueva_direccion'] !== ($socio['direccion'] ?? '')) {
                    $cambiosSolicitados['direccion'] = $data['nueva_direccion'];
                }
                
                if (!empty($cambiosSolicitados)) {
                    $this->db->insert('solicitudes_actualizacion_perfil', [
                        'usuario_id' => $id,
                        'socio_id' => $socio ? $socio['id'] : null,
                        'cambios_solicitados' => json_encode($cambiosSolicitados),
                        'estatus' => 'pendiente'
                    ]);
                    
                    // Notify admins/operatives
                    $admins = $this->db->fetchAll(
                        "SELECT id FROM usuarios WHERE rol IN ('administrador', 'operativo') AND activo = 1"
                    );
                    
                    foreach ($admins as $admin) {
                        $this->db->insert('notificaciones', [
                            'usuario_id' => $admin['id'],
                            'tipo' => 'info',
                            'titulo' => 'Solicitud de actualización de perfil',
                            'mensaje' => "El usuario {$usuario['nombre']} ha solicitado actualizar su información de contacto.",
                            'url' => 'crm/customerjourney'
                        ]);
                    }
                    
                    $this->logAction('SOLICITAR_ACTUALIZACION', 'Se solicitó actualización de datos de contacto', 'usuarios', $id);
                    $success = 'Tu solicitud de actualización ha sido enviada. Un operativo la revisará pronto.';
                } else {
                    $errors[] = 'No hay cambios para solicitar';
                }
            }
        }
        
        $this->view('usuarios/perfil', [
            'pageTitle' => 'Mi Perfil',
            'usuario' => $usuario,
            'socio' => $socio,
            'solicitudesPendientes' => $solicitudesPendientes,
            'errors' => $errors,
            'success' => $success
        ]);
    }
}
