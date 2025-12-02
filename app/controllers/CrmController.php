<?php
/**
 * Controlador de Informe CRM
 * Sistema de Gestión Integral de Caja de Ahorros
 */

require_once CORE_PATH . '/Controller.php';

class CrmController extends Controller {
    
    public function index() {
        $this->requireAuth();
        
        // Estadísticas principales
        $stats = $this->getStats();
        
        // Distribución por segmento
        $segmentacion = $this->db->fetchAll(
            "SELECT sc.nombre, sc.color, COUNT(ss.socio_id) as cantidad
             FROM segmentos_clientes sc
             LEFT JOIN socios_segmentos ss ON sc.id = ss.segmento_id
             WHERE sc.activo = 1
             GROUP BY sc.id, sc.nombre, sc.color
             ORDER BY cantidad DESC"
        );
        
        // Agregar clientes sin segmento
        $sinSegmento = $this->db->fetch(
            "SELECT COUNT(*) as total FROM socios s 
             WHERE s.estatus = 'activo' 
             AND s.id NOT IN (SELECT DISTINCT socio_id FROM socios_segmentos)"
        )['total'];
        
        if ($sinSegmento > 0) {
            array_unshift($segmentacion, [
                'nombre' => 'Sin compras',
                'color' => '#6b7280',
                'cantidad' => $sinSegmento
            ]);
        }
        
        // Rendimiento por segmento
        $rendimientoPorSegmento = $this->db->fetchAll(
            "SELECT sc.nombre, 
                    COALESCE(SUM(mc.ltv), 0) as ingresos_totales,
                    COALESCE(AVG(mc.ltv), 0) as promedio
             FROM segmentos_clientes sc
             LEFT JOIN socios_segmentos ss ON sc.id = ss.segmento_id
             LEFT JOIN metricas_crm mc ON ss.socio_id = mc.socio_id
             WHERE sc.activo = 1
             GROUP BY sc.id, sc.nombre
             ORDER BY ingresos_totales DESC"
        );
        
        // Clientes en riesgo
        $clientesRiesgo = $this->db->fetchAll(
            "SELECT s.id, s.numero_socio, 
                    CONCAT(s.nombre, ' ', s.apellido_paterno) as nombre,
                    s.email, s.celular,
                    mc.ltv, mc.dias_sin_actividad, mc.ultima_transaccion
             FROM socios s
             JOIN metricas_crm mc ON s.id = mc.socio_id
             WHERE mc.nivel_riesgo = 'alto' AND s.estatus = 'activo'
             ORDER BY mc.dias_sin_actividad DESC
             LIMIT 10"
        );
        
        // Clientes VIP
        $clientesVip = $this->db->fetchAll(
            "SELECT s.id, s.numero_socio,
                    CONCAT(s.nombre, ' ', s.apellido_paterno) as nombre,
                    mc.ltv, mc.frecuencia_transacciones
             FROM socios s
             JOIN metricas_crm mc ON s.id = mc.socio_id
             WHERE mc.es_vip = 1 AND s.estatus = 'activo'
             ORDER BY mc.ltv DESC
             LIMIT 10"
        );
        
        $this->view('crm/index', [
            'pageTitle' => 'Informe CRM',
            'stats' => $stats,
            'segmentacion' => $segmentacion,
            'rendimientoPorSegmento' => $rendimientoPorSegmento,
            'clientesRiesgo' => $clientesRiesgo,
            'clientesVip' => $clientesVip
        ]);
    }
    
    public function segmentos() {
        $this->requireRole(['administrador']);
        
        $errors = [];
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $action = $_POST['action'] ?? '';
            
            if ($action === 'crear') {
                $nombre = $this->sanitize($_POST['nombre'] ?? '');
                $descripcion = $this->sanitize($_POST['descripcion'] ?? '');
                $color = $this->sanitize($_POST['color'] ?? '#3b82f6');
                
                if (empty($nombre)) {
                    $errors[] = 'El nombre es requerido';
                } else {
                    $this->db->insert('segmentos_clientes', [
                        'nombre' => $nombre,
                        'descripcion' => $descripcion,
                        'color' => $color,
                        'activo' => 1
                    ]);
                    
                    $this->logAction('CREAR_SEGMENTO', "Se creó segmento: {$nombre}", 'segmentos_clientes', $this->db->lastInsertId());
                    $success = 'Segmento creado exitosamente';
                }
            } elseif ($action === 'actualizar_clientes') {
                // Actualizar métricas CRM para todos los socios activos
                $socios = $this->db->fetchAll("SELECT id FROM socios WHERE estatus = 'activo'");
                foreach ($socios as $socio) {
                    $this->db->execute("CALL sp_actualizar_metricas_crm(:id)", ['id' => $socio['id']]);
                }
                $success = 'Métricas CRM actualizadas para ' . count($socios) . ' clientes';
            }
        }
        
        $segmentos = $this->db->fetchAll(
            "SELECT sc.*, 
                    (SELECT COUNT(*) FROM socios_segmentos ss WHERE ss.segmento_id = sc.id) as num_clientes
             FROM segmentos_clientes sc
             ORDER BY sc.nombre"
        );
        
        $this->view('crm/segmentos', [
            'pageTitle' => 'Segmentos de Clientes',
            'segmentos' => $segmentos,
            'errors' => $errors,
            'success' => $success
        ]);
    }
    
    public function metricas() {
        $this->requireAuth();
        
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 20;
        $riesgo = $_GET['riesgo'] ?? '';
        $vip = isset($_GET['vip']) ? $_GET['vip'] : '';
        
        $conditions = 's.estatus = "activo"';
        $params = [];
        
        if ($riesgo) {
            $conditions .= ' AND mc.nivel_riesgo = :riesgo';
            $params['riesgo'] = $riesgo;
        }
        if ($vip === '1') {
            $conditions .= ' AND mc.es_vip = 1';
        }
        
        $total = $this->db->fetch(
            "SELECT COUNT(*) as total FROM socios s
             LEFT JOIN metricas_crm mc ON s.id = mc.socio_id
             WHERE {$conditions}",
            $params
        )['total'];
        
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        
        $clientes = $this->db->fetchAll(
            "SELECT s.id, s.numero_socio, 
                    CONCAT(s.nombre, ' ', s.apellido_paterno) as nombre,
                    s.email, s.celular,
                    COALESCE(mc.ltv, 0) as ltv,
                    COALESCE(mc.frecuencia_transacciones, 0) as frecuencia,
                    mc.ultima_transaccion,
                    COALESCE(mc.dias_sin_actividad, 0) as dias_inactivo,
                    COALESCE(mc.nivel_riesgo, 'bajo') as nivel_riesgo,
                    COALESCE(mc.es_vip, 0) as es_vip
             FROM socios s
             LEFT JOIN metricas_crm mc ON s.id = mc.socio_id
             WHERE {$conditions}
             ORDER BY mc.ltv DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        
        $this->view('crm/metricas', [
            'pageTitle' => 'Métricas de Clientes',
            'clientes' => $clientes,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'riesgo' => $riesgo,
            'vip' => $vip
        ]);
    }
    
    public function interacciones() {
        $this->requireAuth();
        
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 20;
        $tipo = $_GET['tipo'] ?? '';
        
        $conditions = '1=1';
        $params = [];
        
        if ($tipo) {
            $conditions .= ' AND i.tipo = :tipo';
            $params['tipo'] = $tipo;
        }
        
        $total = $this->db->fetch(
            "SELECT COUNT(*) as total FROM interacciones_clientes i WHERE {$conditions}",
            $params
        )['total'];
        
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        
        $interacciones = $this->db->fetchAll(
            "SELECT i.*, 
                    CONCAT(s.nombre, ' ', s.apellido_paterno) as cliente_nombre,
                    s.numero_socio,
                    u.nombre as usuario_nombre
             FROM interacciones_clientes i
             JOIN socios s ON i.socio_id = s.id
             LEFT JOIN usuarios u ON i.usuario_id = u.id
             WHERE {$conditions}
             ORDER BY i.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        
        $this->view('crm/interacciones', [
            'pageTitle' => 'Interacciones con Clientes',
            'interacciones' => $interacciones,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'tipoFilter' => $tipo
        ]);
    }
    
    public function interaccion() {
        $this->requireRole(['administrador', 'operativo']);
        
        $socioId = isset($this->params['id']) ? (int)$this->params['id'] : 0;
        
        if (!$socioId) {
            $socioId = (int)($_GET['socio_id'] ?? 0);
        }
        
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $socioIdPost = (int)($_POST['socio_id'] ?? 0);
            $tipo = $this->sanitize($_POST['tipo'] ?? '');
            $asunto = $this->sanitize($_POST['asunto'] ?? '');
            $descripcion = $this->sanitize($_POST['descripcion'] ?? '');
            $resultado = $this->sanitize($_POST['resultado'] ?? '');
            $seguimientoRequerido = isset($_POST['seguimiento_requerido']) ? 1 : 0;
            $fechaSeguimiento = $this->sanitize($_POST['fecha_seguimiento'] ?? '');
            
            if (!$socioIdPost) {
                $errors[] = 'Debe seleccionar un cliente';
            }
            if (!$tipo) {
                $errors[] = 'El tipo de interacción es requerido';
            }
            if (empty($descripcion)) {
                $errors[] = 'La descripción es requerida';
            }
            
            if (empty($errors)) {
                $this->db->insert('interacciones_clientes', [
                    'socio_id' => $socioIdPost,
                    'tipo' => $tipo,
                    'asunto' => $asunto,
                    'descripcion' => $descripcion,
                    'resultado' => $resultado,
                    'seguimiento_requerido' => $seguimientoRequerido,
                    'fecha_seguimiento' => $seguimientoRequerido && $fechaSeguimiento ? $fechaSeguimiento : null,
                    'usuario_id' => $_SESSION['user_id']
                ]);
                
                $this->logAction('CREAR_INTERACCION', "Interacción registrada para socio ID: {$socioIdPost}", 'interacciones_clientes', $this->db->lastInsertId());
                $this->setFlash('success', 'Interacción registrada exitosamente');
                $this->redirect('crm/interacciones');
            }
        }
        
        $socio = null;
        if ($socioId) {
            $socio = $this->db->fetch(
                "SELECT id, numero_socio, CONCAT(nombre, ' ', apellido_paterno) as nombre 
                 FROM socios WHERE id = :id",
                ['id' => $socioId]
            );
        }
        
        $socios = $this->db->fetchAll(
            "SELECT id, numero_socio, CONCAT(nombre, ' ', apellido_paterno) as nombre 
             FROM socios WHERE estatus = 'activo' ORDER BY nombre"
        );
        
        $this->view('crm/interaccion', [
            'pageTitle' => 'Registrar Interacción',
            'socio' => $socio,
            'socios' => $socios,
            'errors' => $errors
        ]);
    }
    
    private function getStats() {
        return [
            'total_clientes' => $this->db->fetch(
                "SELECT COUNT(*) as total FROM socios WHERE estatus = 'activo'"
            )['total'],
            'clientes_activos' => $this->db->fetch(
                "SELECT COUNT(*) as total FROM metricas_crm WHERE dias_sin_actividad <= 30"
            )['total'] ?? 0,
            'ltv_promedio' => $this->db->fetch(
                "SELECT COALESCE(AVG(ltv), 0) as promedio FROM metricas_crm"
            )['promedio'] ?? 0,
            'en_riesgo' => $this->db->fetch(
                "SELECT COUNT(*) as total FROM metricas_crm WHERE nivel_riesgo = 'alto'"
            )['total'] ?? 0,
            'vip' => $this->db->fetch(
                "SELECT COUNT(*) as total FROM metricas_crm WHERE es_vip = 1"
            )['total'] ?? 0
        ];
    }
}
