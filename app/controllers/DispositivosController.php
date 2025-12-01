<?php
/**
 * Controlador de Dispositivos IoT
 * Shelly Cloud y HikVision
 * Sistema de Gestión Integral de Caja de Ahorros
 */

require_once CORE_PATH . '/Controller.php';

class DispositivosController extends Controller {
    
    public function index() {
        $this->requireRole(['administrador']);
        
        $tipo = $_GET['tipo'] ?? '';
        
        $conditions = '1=1';
        $params = [];
        
        if ($tipo) {
            $conditions .= ' AND d.tipo = :tipo';
            $params['tipo'] = $tipo;
        }
        
        $dispositivos = $this->db->fetchAll(
            "SELECT d.*, 
                    CASE 
                        WHEN d.tipo = 'shelly' THEN sc.estado_actual
                        WHEN d.tipo = 'hikvision' THEN IF(hc.grabacion_activa, 'grabando', 'inactivo')
                        ELSE 'desconocido'
                    END as estado_detalle,
                    CASE 
                        WHEN d.tipo = 'shelly' THEN sc.potencia_actual
                        ELSE NULL
                    END as potencia
             FROM dispositivos d
             LEFT JOIN shelly_config sc ON d.id = sc.dispositivo_id
             LEFT JOIN hikvision_config hc ON d.id = hc.dispositivo_id
             WHERE {$conditions}
             ORDER BY d.tipo, d.nombre",
            $params
        );
        
        // Estadísticas
        $stats = [
            'total' => $this->db->fetch("SELECT COUNT(*) as total FROM dispositivos")['total'],
            'activos' => $this->db->fetch("SELECT COUNT(*) as total FROM dispositivos WHERE activo = 1")['total'],
            'shelly' => $this->db->fetch("SELECT COUNT(*) as total FROM dispositivos WHERE tipo = 'shelly'")['total'],
            'hikvision' => $this->db->fetch("SELECT COUNT(*) as total FROM dispositivos WHERE tipo = 'hikvision'")['total']
        ];
        
        $this->view('dispositivos/index', [
            'pageTitle' => 'Dispositivos IoT',
            'dispositivos' => $dispositivos,
            'stats' => $stats,
            'tipoFilter' => $tipo
        ]);
    }
    
    public function crear() {
        $this->requireRole(['administrador']);
        
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $nombre = $this->sanitize($_POST['nombre'] ?? '');
            $tipo = $this->sanitize($_POST['tipo'] ?? '');
            $modelo = $this->sanitize($_POST['modelo'] ?? '');
            $ubicacion = $this->sanitize($_POST['ubicacion'] ?? '');
            $ipAddress = $this->sanitize($_POST['ip_address'] ?? '');
            $macAddress = $this->sanitize($_POST['mac_address'] ?? '');
            $descripcion = $this->sanitize($_POST['descripcion'] ?? '');
            
            if (empty($nombre)) {
                $errors[] = 'El nombre es requerido';
            }
            if (!in_array($tipo, ['shelly', 'hikvision', 'otro'])) {
                $errors[] = 'Tipo de dispositivo inválido';
            }
            
            if (empty($errors)) {
                $this->db->insert('dispositivos', [
                    'nombre' => $nombre,
                    'tipo' => $tipo,
                    'modelo' => $modelo,
                    'ubicacion' => $ubicacion,
                    'ip_address' => $ipAddress,
                    'mac_address' => $macAddress,
                    'descripcion' => $descripcion,
                    'activo' => 1
                ]);
                
                $dispositivoId = $this->db->lastInsertId();
                
                // Crear configuración según tipo
                if ($tipo === 'shelly') {
                    $this->db->insert('shelly_config', [
                        'dispositivo_id' => $dispositivoId,
                        'estado_actual' => 'unknown'
                    ]);
                } elseif ($tipo === 'hikvision') {
                    $this->db->insert('hikvision_config', [
                        'dispositivo_id' => $dispositivoId
                    ]);
                }
                
                $this->logAction('CREAR_DISPOSITIVO', "Se creó dispositivo: {$nombre}", 'dispositivos', $dispositivoId);
                $this->setFlash('success', 'Dispositivo creado exitosamente');
                $this->redirect('dispositivos');
            }
        }
        
        $this->view('dispositivos/crear', [
            'pageTitle' => 'Nuevo Dispositivo',
            'errors' => $errors
        ]);
    }
    
    public function ver() {
        $this->requireRole(['administrador']);
        
        $id = (int)($this->params['id'] ?? 0);
        
        $dispositivo = $this->db->fetch(
            "SELECT * FROM dispositivos WHERE id = :id",
            ['id' => $id]
        );
        
        if (!$dispositivo) {
            $this->setFlash('error', 'Dispositivo no encontrado');
            $this->redirect('dispositivos');
        }
        
        // Obtener configuración específica
        $config = null;
        if ($dispositivo['tipo'] === 'shelly') {
            $config = $this->db->fetch(
                "SELECT * FROM shelly_config WHERE dispositivo_id = :id",
                ['id' => $id]
            );
        } elseif ($dispositivo['tipo'] === 'hikvision') {
            $config = $this->db->fetch(
                "SELECT * FROM hikvision_config WHERE dispositivo_id = :id",
                ['id' => $id]
            );
        }
        
        // Últimos eventos
        $eventos = $this->db->fetchAll(
            "SELECT * FROM eventos_dispositivos WHERE dispositivo_id = :id ORDER BY fecha DESC LIMIT 20",
            ['id' => $id]
        );
        
        // Programaciones
        $programaciones = $this->db->fetchAll(
            "SELECT * FROM programacion_dispositivos WHERE dispositivo_id = :id ORDER BY hora_inicio",
            ['id' => $id]
        );
        
        $this->view('dispositivos/ver', [
            'pageTitle' => 'Detalle del Dispositivo',
            'dispositivo' => $dispositivo,
            'config' => $config,
            'eventos' => $eventos,
            'programaciones' => $programaciones
        ]);
    }
    
    public function editar() {
        $this->requireRole(['administrador']);
        
        $id = (int)($this->params['id'] ?? 0);
        $errors = [];
        
        $dispositivo = $this->db->fetch(
            "SELECT * FROM dispositivos WHERE id = :id",
            ['id' => $id]
        );
        
        if (!$dispositivo) {
            $this->setFlash('error', 'Dispositivo no encontrado');
            $this->redirect('dispositivos');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $nombre = $this->sanitize($_POST['nombre'] ?? '');
            $modelo = $this->sanitize($_POST['modelo'] ?? '');
            $ubicacion = $this->sanitize($_POST['ubicacion'] ?? '');
            $ipAddress = $this->sanitize($_POST['ip_address'] ?? '');
            $macAddress = $this->sanitize($_POST['mac_address'] ?? '');
            $descripcion = $this->sanitize($_POST['descripcion'] ?? '');
            $activo = isset($_POST['activo']) ? 1 : 0;
            
            if (empty($nombre)) {
                $errors[] = 'El nombre es requerido';
            }
            
            if (empty($errors)) {
                $this->db->update('dispositivos', [
                    'nombre' => $nombre,
                    'modelo' => $modelo,
                    'ubicacion' => $ubicacion,
                    'ip_address' => $ipAddress,
                    'mac_address' => $macAddress,
                    'descripcion' => $descripcion,
                    'activo' => $activo
                ], 'id = :id', ['id' => $id]);
                
                $this->logAction('EDITAR_DISPOSITIVO', "Se editó dispositivo ID: {$id}", 'dispositivos', $id);
                $this->setFlash('success', 'Dispositivo actualizado exitosamente');
                $this->redirect('dispositivos/ver/' . $id);
            }
        }
        
        $this->view('dispositivos/editar', [
            'pageTitle' => 'Editar Dispositivo',
            'dispositivo' => $dispositivo,
            'errors' => $errors
        ]);
    }
    
    public function shelly() {
        $this->requireRole(['administrador']);
        
        $dispositivos = $this->db->fetchAll(
            "SELECT d.*, sc.*
             FROM dispositivos d
             JOIN shelly_config sc ON d.id = sc.dispositivo_id
             WHERE d.tipo = 'shelly'
             ORDER BY d.nombre"
        );
        
        $this->view('dispositivos/shelly', [
            'pageTitle' => 'Dispositivos Shelly Cloud',
            'dispositivos' => $dispositivos
        ]);
    }
    
    public function shellyConfig() {
        $this->requireRole(['administrador']);
        
        $id = (int)($this->params['id'] ?? 0);
        $errors = [];
        
        $dispositivo = $this->db->fetch(
            "SELECT d.*, sc.* 
             FROM dispositivos d
             JOIN shelly_config sc ON d.id = sc.dispositivo_id
             WHERE d.id = :id AND d.tipo = 'shelly'",
            ['id' => $id]
        );
        
        if (!$dispositivo) {
            $this->setFlash('error', 'Dispositivo Shelly no encontrado');
            $this->redirect('dispositivos/shelly');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $cloudKey = $this->sanitize($_POST['cloud_key'] ?? '');
            $cloudServer = $this->sanitize($_POST['cloud_server'] ?? '');
            $authToken = $this->sanitize($_POST['auth_token'] ?? '');
            $channel = (int)($_POST['channel'] ?? 0);
            
            $this->db->update('shelly_config', [
                'cloud_key' => $cloudKey,
                'cloud_server' => $cloudServer,
                'auth_token' => $authToken,
                'channel' => $channel
            ], 'dispositivo_id = :id', ['id' => $id]);
            
            $this->logAction('CONFIG_SHELLY', "Se configuró dispositivo Shelly ID: {$id}", 'shelly_config', $id);
            $this->setFlash('success', 'Configuración Shelly actualizada');
            $this->redirect('dispositivos/ver/' . $id);
        }
        
        $this->view('dispositivos/shelly_config', [
            'pageTitle' => 'Configurar Shelly',
            'dispositivo' => $dispositivo,
            'errors' => $errors
        ]);
    }
    
    public function hikvision() {
        $this->requireRole(['administrador']);
        
        $dispositivos = $this->db->fetchAll(
            "SELECT d.*, hc.*
             FROM dispositivos d
             JOIN hikvision_config hc ON d.id = hc.dispositivo_id
             WHERE d.tipo = 'hikvision'
             ORDER BY d.nombre"
        );
        
        $this->view('dispositivos/hikvision', [
            'pageTitle' => 'Dispositivos HikVision',
            'dispositivos' => $dispositivos
        ]);
    }
    
    public function hikvisionConfig() {
        $this->requireRole(['administrador']);
        
        $id = (int)($this->params['id'] ?? 0);
        $errors = [];
        
        $dispositivo = $this->db->fetch(
            "SELECT d.*, hc.* 
             FROM dispositivos d
             JOIN hikvision_config hc ON d.id = hc.dispositivo_id
             WHERE d.id = :id AND d.tipo = 'hikvision'",
            ['id' => $id]
        );
        
        if (!$dispositivo) {
            $this->setFlash('error', 'Dispositivo HikVision no encontrado');
            $this->redirect('dispositivos/hikvision');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $usuario = $this->sanitize($_POST['usuario'] ?? '');
            $password = $_POST['password'] ?? '';
            $puertoHttp = (int)($_POST['puerto_http'] ?? 80);
            $puertoRtsp = (int)($_POST['puerto_rtsp'] ?? 554);
            $canales = (int)($_POST['canales'] ?? 1);
            $grabacionActiva = isset($_POST['grabacion_activa']) ? 1 : 0;
            $deteccionMovimiento = isset($_POST['deteccion_movimiento']) ? 1 : 0;
            
            $data = [
                'usuario' => $usuario,
                'puerto_http' => $puertoHttp,
                'puerto_rtsp' => $puertoRtsp,
                'canales' => $canales,
                'grabacion_activa' => $grabacionActiva,
                'deteccion_movimiento' => $deteccionMovimiento
            ];
            
            // Solo actualizar password si se proporcionó uno nuevo
            if (!empty($password)) {
                // Use proper encryption for storing the password
                // In production, consider using PHP's openssl_encrypt with a secure key
                $key = DB_PASS; // Use database password as encryption key
                $iv = openssl_random_pseudo_bytes(16);
                $encrypted = openssl_encrypt($password, 'AES-256-CBC', $key, 0, $iv);
                $data['password_encrypted'] = base64_encode($iv . $encrypted);
            }
            
            $this->db->update('hikvision_config', $data, 'dispositivo_id = :id', ['id' => $id]);
            
            $this->logAction('CONFIG_HIKVISION', "Se configuró dispositivo HikVision ID: {$id}", 'hikvision_config', $id);
            $this->setFlash('success', 'Configuración HikVision actualizada');
            $this->redirect('dispositivos/ver/' . $id);
        }
        
        $this->view('dispositivos/hikvision_config', [
            'pageTitle' => 'Configurar HikVision',
            'dispositivo' => $dispositivo,
            'errors' => $errors
        ]);
    }
    
    public function eventos() {
        $this->requireRole(['administrador']);
        
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 50;
        $dispositivoId = (int)($_GET['dispositivo'] ?? 0);
        
        $conditions = '1=1';
        $params = [];
        
        if ($dispositivoId) {
            $conditions .= ' AND e.dispositivo_id = :dispositivo';
            $params['dispositivo'] = $dispositivoId;
        }
        
        $total = $this->db->fetch(
            "SELECT COUNT(*) as total FROM eventos_dispositivos e WHERE {$conditions}",
            $params
        )['total'];
        
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        
        $eventos = $this->db->fetchAll(
            "SELECT e.*, d.nombre as dispositivo_nombre, d.tipo as dispositivo_tipo
             FROM eventos_dispositivos e
             JOIN dispositivos d ON e.dispositivo_id = d.id
             WHERE {$conditions}
             ORDER BY e.fecha DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        
        $dispositivos = $this->db->fetchAll("SELECT id, nombre FROM dispositivos ORDER BY nombre");
        
        $this->view('dispositivos/eventos', [
            'pageTitle' => 'Eventos de Dispositivos',
            'eventos' => $eventos,
            'dispositivos' => $dispositivos,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'dispositivoFilter' => $dispositivoId
        ]);
    }
    
    public function programacion() {
        $this->requireRole(['administrador']);
        
        $errors = [];
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $action = $_POST['action'] ?? '';
            
            if ($action === 'crear') {
                $dispositivoId = (int)($_POST['dispositivo_id'] ?? 0);
                $nombre = $this->sanitize($_POST['nombre'] ?? '');
                $accion = $this->sanitize($_POST['accion'] ?? '');
                $horaInicio = $this->sanitize($_POST['hora_inicio'] ?? '');
                $horaFin = $this->sanitize($_POST['hora_fin'] ?? '');
                $diasSemana = implode(',', $_POST['dias_semana'] ?? []);
                
                if (!$dispositivoId || empty($nombre) || empty($horaInicio)) {
                    $errors[] = 'Datos incompletos para la programación';
                } else {
                    $this->db->insert('programacion_dispositivos', [
                        'dispositivo_id' => $dispositivoId,
                        'nombre' => $nombre,
                        'accion' => $accion,
                        'hora_inicio' => $horaInicio,
                        'hora_fin' => $horaFin ?: null,
                        'dias_semana' => $diasSemana,
                        'activo' => 1
                    ]);
                    
                    $success = 'Programación creada exitosamente';
                }
            } elseif ($action === 'toggle') {
                $progId = (int)($_POST['id'] ?? 0);
                $prog = $this->db->fetch("SELECT activo FROM programacion_dispositivos WHERE id = :id", ['id' => $progId]);
                if ($prog) {
                    $nuevoEstado = $prog['activo'] ? 0 : 1;
                    $this->db->update('programacion_dispositivos', ['activo' => $nuevoEstado], 'id = :id', ['id' => $progId]);
                    $success = $nuevoEstado ? 'Programación activada' : 'Programación desactivada';
                }
            }
        }
        
        $programaciones = $this->db->fetchAll(
            "SELECT p.*, d.nombre as dispositivo_nombre
             FROM programacion_dispositivos p
             JOIN dispositivos d ON p.dispositivo_id = d.id
             ORDER BY d.nombre, p.hora_inicio"
        );
        
        $dispositivos = $this->db->fetchAll("SELECT id, nombre FROM dispositivos WHERE activo = 1 ORDER BY nombre");
        
        $this->view('dispositivos/programacion', [
            'pageTitle' => 'Programación de Dispositivos',
            'programaciones' => $programaciones,
            'dispositivos' => $dispositivos,
            'errors' => $errors,
            'success' => $success
        ]);
    }
}
