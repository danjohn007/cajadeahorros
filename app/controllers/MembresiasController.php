<?php
/**
 * Controlador de Membresías
 * Sistema de Gestión Integral de Caja de Ahorros
 */

require_once CORE_PATH . '/Controller.php';

class MembresiasController extends Controller {
    
    public function index() {
        $this->requireAuth();
        
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 20;
        $estado = $_GET['estado'] ?? '';
        $buscar = $_GET['buscar'] ?? '';
        
        $conditions = '1=1';
        $params = [];
        
        if ($estado) {
            $conditions .= ' AND m.estatus = :estado';
            $params['estado'] = $estado;
        }
        
        if ($buscar) {
            $conditions .= ' AND (s.nombre LIKE :buscar1 OR s.apellido_paterno LIKE :buscar2 OR s.numero_socio LIKE :buscar3)';
            $buscarTerm = "%{$buscar}%";
            $params['buscar1'] = $buscarTerm;
            $params['buscar2'] = $buscarTerm;
            $params['buscar3'] = $buscarTerm;
        }
        
        $total = $this->db->fetch(
            "SELECT COUNT(*) as total FROM membresias m 
             JOIN socios s ON m.socio_id = s.id 
             WHERE {$conditions}",
            $params
        )['total'];
        
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        
        $membresias = $this->db->fetchAll(
            "SELECT m.*, 
                    CONCAT(s.nombre, ' ', s.apellido_paterno, ' ', COALESCE(s.apellido_materno, '')) as nombre_socio,
                    s.numero_socio,
                    tm.nombre as tipo_membresia,
                    tm.precio as precio_tipo,
                    DATEDIFF(m.fecha_fin, CURDATE()) as dias_restantes
             FROM membresias m
             JOIN socios s ON m.socio_id = s.id
             JOIN tipos_membresia tm ON m.tipo_membresia_id = tm.id
             WHERE {$conditions}
             ORDER BY m.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        
        // Estadísticas
        $stats = $this->getStats();
        
        $this->view('membresias/index', [
            'pageTitle' => 'Membresías',
            'membresias' => $membresias,
            'stats' => $stats,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'estado' => $estado,
            'buscar' => $buscar
        ]);
    }
    
    public function crear() {
        $this->requireRole(['administrador', 'operativo']);
        
        $errors = [];
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $socioId = (int)($_POST['socio_id'] ?? 0);
            $tipoMembresiaId = (int)($_POST['tipo_membresia_id'] ?? 0);
            $metodoPago = $this->sanitize($_POST['metodo_pago'] ?? 'efectivo');
            $referenciaPago = $this->sanitize($_POST['referencia_pago'] ?? '');
            $notas = $this->sanitize($_POST['notas'] ?? '');
            
            // Validaciones
            if (!$socioId) {
                $errors[] = 'Debe seleccionar un socio';
            }
            if (!$tipoMembresiaId) {
                $errors[] = 'Debe seleccionar un tipo de membresía';
            }
            
            if (empty($errors)) {
                // Obtener información del tipo de membresía
                $tipo = $this->db->fetch(
                    "SELECT * FROM tipos_membresia WHERE id = :id AND activo = 1",
                    ['id' => $tipoMembresiaId]
                );
                
                if (!$tipo) {
                    $errors[] = 'Tipo de membresía no válido';
                } else {
                    $fechaInicio = date('Y-m-d');
                    $fechaFin = date('Y-m-d', strtotime("+{$tipo['duracion_dias']} days"));
                    
                    $this->db->insert('membresias', [
                        'socio_id' => $socioId,
                        'tipo_membresia_id' => $tipoMembresiaId,
                        'fecha_inicio' => $fechaInicio,
                        'fecha_fin' => $fechaFin,
                        'monto_pagado' => $tipo['precio'],
                        'estatus' => 'activa',
                        'metodo_pago' => $metodoPago,
                        'referencia_pago' => $referenciaPago,
                        'notas' => $notas
                    ]);
                    
                    $membresiaId = $this->db->lastInsertId();
                    
                    // Registrar pago
                    $this->db->insert('pagos_membresia', [
                        'membresia_id' => $membresiaId,
                        'monto' => $tipo['precio'],
                        'metodo_pago' => $metodoPago,
                        'referencia' => $referenciaPago,
                        'usuario_id' => $_SESSION['user_id']
                    ]);
                    
                    $this->logAction('CREAR_MEMBRESIA', "Se creó membresía para socio ID: {$socioId}", 'membresias', $membresiaId);
                    $this->setFlash('success', 'Membresía creada exitosamente');
                    $this->redirect('membresias');
                }
            }
        }
        
        $tiposMembresia = $this->db->fetchAll(
            "SELECT * FROM tipos_membresia WHERE activo = 1 ORDER BY precio"
        );
        
        $socios = $this->db->fetchAll(
            "SELECT id, numero_socio, CONCAT(nombre, ' ', apellido_paterno, ' ', COALESCE(apellido_materno, '')) as nombre_completo 
             FROM socios WHERE estatus = 'activo' ORDER BY nombre"
        );
        
        $this->view('membresias/crear', [
            'pageTitle' => 'Nueva Membresía',
            'tiposMembresia' => $tiposMembresia,
            'socios' => $socios,
            'errors' => $errors
        ]);
    }
    
    public function ver() {
        $this->requireAuth();
        
        $id = (int)($this->params['id'] ?? 0);
        
        $membresia = $this->db->fetch(
            "SELECT m.*, 
                    CONCAT(s.nombre, ' ', s.apellido_paterno, ' ', COALESCE(s.apellido_materno, '')) as nombre_socio,
                    s.numero_socio, s.email, s.celular,
                    tm.nombre as tipo_membresia, tm.descripcion as tipo_descripcion, tm.beneficios
             FROM membresias m
             JOIN socios s ON m.socio_id = s.id
             JOIN tipos_membresia tm ON m.tipo_membresia_id = tm.id
             WHERE m.id = :id",
            ['id' => $id]
        );
        
        if (!$membresia) {
            $this->setFlash('error', 'Membresía no encontrada');
            $this->redirect('membresias');
        }
        
        $pagos = $this->db->fetchAll(
            "SELECT pm.*, u.nombre as usuario_nombre
             FROM pagos_membresia pm
             LEFT JOIN usuarios u ON pm.usuario_id = u.id
             WHERE pm.membresia_id = :id
             ORDER BY pm.fecha_pago DESC",
            ['id' => $id]
        );
        
        $this->view('membresias/ver', [
            'pageTitle' => 'Detalle de Membresía',
            'membresia' => $membresia,
            'pagos' => $pagos
        ]);
    }
    
    public function tipos() {
        $this->requireRole(['administrador']);
        
        $errors = [];
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $action = $_POST['action'] ?? '';
            
            if ($action === 'crear') {
                $nombre = $this->sanitize($_POST['nombre'] ?? '');
                $descripcion = $this->sanitize($_POST['descripcion'] ?? '');
                $precio = (float)($_POST['precio'] ?? 0);
                $duracionDias = (int)($_POST['duracion_dias'] ?? 30);
                $beneficios = $this->sanitize($_POST['beneficios'] ?? '');
                
                if (empty($nombre)) {
                    $errors[] = 'El nombre es requerido';
                } elseif ($precio <= 0) {
                    $errors[] = 'El precio debe ser mayor a cero';
                } else {
                    $this->db->insert('tipos_membresia', [
                        'nombre' => $nombre,
                        'descripcion' => $descripcion,
                        'precio' => $precio,
                        'duracion_dias' => $duracionDias,
                        'beneficios' => $beneficios,
                        'activo' => 1
                    ]);
                    
                    $this->logAction('CREAR_TIPO_MEMBRESIA', "Se creó tipo de membresía: {$nombre}", 'tipos_membresia', $this->db->lastInsertId());
                    $success = 'Tipo de membresía creado exitosamente';
                }
            }
        }
        
        $tipos = $this->db->fetchAll(
            "SELECT tm.*, 
                    (SELECT COUNT(*) FROM membresias m WHERE m.tipo_membresia_id = tm.id AND m.estatus = 'activa') as membresias_activas
             FROM tipos_membresia tm 
             ORDER BY tm.precio"
        );
        
        $this->view('membresias/tipos', [
            'pageTitle' => 'Tipos de Membresía',
            'tipos' => $tipos,
            'errors' => $errors,
            'success' => $success
        ]);
    }
    
    public function renovar() {
        $this->requireRole(['administrador', 'operativo']);
        
        $id = (int)($this->params['id'] ?? 0);
        
        $membresia = $this->db->fetch(
            "SELECT m.*, tm.precio, tm.duracion_dias
             FROM membresias m
             JOIN tipos_membresia tm ON m.tipo_membresia_id = tm.id
             WHERE m.id = :id",
            ['id' => $id]
        );
        
        if (!$membresia) {
            $this->setFlash('error', 'Membresía no encontrada');
            $this->redirect('membresias');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $metodoPago = $this->sanitize($_POST['metodo_pago'] ?? 'efectivo');
            $referenciaPago = $this->sanitize($_POST['referencia_pago'] ?? '');
            
            // Calcular nueva fecha de fin
            $fechaBase = ($membresia['estatus'] === 'activa' && $membresia['fecha_fin'] > date('Y-m-d')) 
                ? $membresia['fecha_fin'] 
                : date('Y-m-d');
            $nuevaFechaFin = date('Y-m-d', strtotime($fechaBase . " +{$membresia['duracion_dias']} days"));
            
            $this->db->update('membresias', [
                'fecha_fin' => $nuevaFechaFin,
                'estatus' => 'activa'
            ], 'id = :id', ['id' => $id]);
            
            // Registrar pago
            $this->db->insert('pagos_membresia', [
                'membresia_id' => $id,
                'monto' => $membresia['precio'],
                'metodo_pago' => $metodoPago,
                'referencia' => $referenciaPago,
                'usuario_id' => $_SESSION['user_id'],
                'notas' => 'Renovación de membresía'
            ]);
            
            $this->logAction('RENOVAR_MEMBRESIA', "Se renovó membresía ID: {$id}", 'membresias', $id);
            $this->setFlash('success', 'Membresía renovada exitosamente');
            $this->redirect('membresias/ver/' . $id);
        }
        
        $this->view('membresias/renovar', [
            'pageTitle' => 'Renovar Membresía',
            'membresia' => $membresia
        ]);
    }
    
    private function getStats() {
        return [
            'activas' => $this->db->fetch(
                "SELECT COUNT(*) as total FROM membresias WHERE estatus = 'activa'"
            )['total'],
            'vencidas' => $this->db->fetch(
                "SELECT COUNT(*) as total FROM membresias WHERE estatus = 'vencida'"
            )['total'],
            'por_vencer' => $this->db->fetch(
                "SELECT COUNT(*) as total FROM membresias 
                 WHERE estatus = 'activa' AND fecha_fin BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)"
            )['total'],
            'ingresos_mes' => $this->db->fetch(
                "SELECT COALESCE(SUM(monto), 0) as total FROM pagos_membresia 
                 WHERE MONTH(fecha_pago) = MONTH(CURDATE()) AND YEAR(fecha_pago) = YEAR(CURDATE())"
            )['total']
        ];
    }
}
