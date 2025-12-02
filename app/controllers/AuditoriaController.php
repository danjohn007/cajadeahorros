<?php
/**
 * Controlador de Auditoría
 * Sistema de Gestión Integral de Caja de Ahorros
 */

require_once CORE_PATH . '/Controller.php';

class AuditoriaController extends Controller {
    
    public function index() {
        $this->requireRole(['administrador']);
        
        // Estadísticas generales
        $stats = [
            'acciones_hoy' => $this->db->fetch(
                "SELECT COUNT(*) as total FROM bitacora WHERE DATE(fecha) = CURDATE()"
            )['total'],
            'usuarios_activos' => $this->db->fetch(
                "SELECT COUNT(DISTINCT usuario_id) as total FROM bitacora WHERE fecha >= DATE_SUB(NOW(), INTERVAL 24 HOUR)"
            )['total'],
            'errores_sistema' => $this->db->fetch(
                "SELECT COUNT(*) as total FROM logs_sistema WHERE nivel IN ('error', 'critical') AND DATE(fecha) = CURDATE()"
            )['total'] ?? 0,
            'sesiones_activas' => $this->db->fetch(
                "SELECT COUNT(*) as total FROM sesiones_usuario WHERE activa = 1"
            )['total'] ?? 0
        ];
        
        // Últimas acciones
        $ultimasAcciones = $this->db->fetchAll(
            "SELECT b.*, u.nombre as usuario_nombre
             FROM bitacora b
             LEFT JOIN usuarios u ON b.usuario_id = u.id
             ORDER BY b.fecha DESC
             LIMIT 10"
        );
        
        // Acciones por tipo
        $accionesPorTipo = $this->db->fetchAll(
            "SELECT accion, COUNT(*) as total
             FROM bitacora
             WHERE fecha >= DATE_SUB(NOW(), INTERVAL 7 DAY)
             GROUP BY accion
             ORDER BY total DESC
             LIMIT 10"
        );
        
        $this->view('auditoria/index', [
            'pageTitle' => 'Auditoría del Sistema',
            'stats' => $stats,
            'ultimasAcciones' => $ultimasAcciones,
            'accionesPorTipo' => $accionesPorTipo
        ]);
    }
    
    public function logs() {
        $this->requireRole(['administrador']);
        
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 50;
        $nivel = $_GET['nivel'] ?? '';
        $modulo = $_GET['modulo'] ?? '';
        $fechaInicio = $_GET['fecha_inicio'] ?? '';
        $fechaFin = $_GET['fecha_fin'] ?? '';
        
        $conditions = '1=1';
        $params = [];
        
        if ($nivel) {
            $conditions .= ' AND l.nivel = :nivel';
            $params['nivel'] = $nivel;
        }
        if ($modulo) {
            $conditions .= ' AND l.modulo = :modulo';
            $params['modulo'] = $modulo;
        }
        if ($fechaInicio) {
            $conditions .= ' AND DATE(l.fecha) >= :fecha_inicio';
            $params['fecha_inicio'] = $fechaInicio;
        }
        if ($fechaFin) {
            $conditions .= ' AND DATE(l.fecha) <= :fecha_fin';
            $params['fecha_fin'] = $fechaFin;
        }
        
        $total = $this->db->fetch(
            "SELECT COUNT(*) as total FROM logs_sistema l WHERE {$conditions}",
            $params
        )['total'] ?? 0;
        
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        
        $logs = $this->db->fetchAll(
            "SELECT l.*, u.nombre as usuario_nombre
             FROM logs_sistema l
             LEFT JOIN usuarios u ON l.usuario_id = u.id
             WHERE {$conditions}
             ORDER BY l.fecha DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        ) ?? [];
        
        $modulos = $this->db->fetchAll(
            "SELECT DISTINCT modulo FROM logs_sistema WHERE modulo IS NOT NULL ORDER BY modulo"
        ) ?? [];
        
        $this->view('auditoria/logs', [
            'pageTitle' => 'Logs del Sistema',
            'logs' => $logs,
            'modulos' => $modulos,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'nivel' => $nivel,
            'moduloFilter' => $modulo,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin
        ]);
    }
    
    public function sesiones() {
        $this->requireRole(['administrador']);
        
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 50;
        
        $total = $this->db->fetch("SELECT COUNT(*) as total FROM sesiones_usuario")['total'] ?? 0;
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        
        $sesiones = $this->db->fetchAll(
            "SELECT s.*, u.nombre as usuario_nombre, u.email
             FROM sesiones_usuario s
             JOIN usuarios u ON s.usuario_id = u.id
             ORDER BY s.fecha_ultimo_acceso DESC
             LIMIT {$perPage} OFFSET {$offset}"
        ) ?? [];
        
        // Estadísticas de sesiones
        $stats = [
            'activas' => $this->db->fetch(
                "SELECT COUNT(*) as total FROM sesiones_usuario WHERE activa = 1"
            )['total'] ?? 0,
            'hoy' => $this->db->fetch(
                "SELECT COUNT(*) as total FROM sesiones_usuario WHERE DATE(fecha_inicio) = CURDATE()"
            )['total'] ?? 0
        ];
        
        $this->view('auditoria/sesiones', [
            'pageTitle' => 'Sesiones de Usuario',
            'sesiones' => $sesiones,
            'stats' => $stats,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }
    
    public function cambios() {
        $this->requireRole(['administrador']);
        
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 50;
        $tabla = $_GET['tabla'] ?? '';
        $operacion = $_GET['operacion'] ?? '';
        
        $conditions = '1=1';
        $params = [];
        
        if ($tabla) {
            $conditions .= ' AND c.tabla = :tabla';
            $params['tabla'] = $tabla;
        }
        if ($operacion) {
            $conditions .= ' AND c.operacion = :operacion';
            $params['operacion'] = $operacion;
        }
        
        $total = $this->db->fetch(
            "SELECT COUNT(*) as total FROM cambios_registro c WHERE {$conditions}",
            $params
        )['total'] ?? 0;
        
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        
        $cambios = $this->db->fetchAll(
            "SELECT c.*, u.nombre as usuario_nombre
             FROM cambios_registro c
             LEFT JOIN usuarios u ON c.usuario_id = u.id
             WHERE {$conditions}
             ORDER BY c.fecha DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        ) ?? [];
        
        $tablas = $this->db->fetchAll(
            "SELECT DISTINCT tabla FROM cambios_registro ORDER BY tabla"
        ) ?? [];
        
        $this->view('auditoria/cambios', [
            'pageTitle' => 'Registro de Cambios',
            'cambios' => $cambios,
            'tablas' => $tablas,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'tablaFilter' => $tabla,
            'operacion' => $operacion
        ]);
    }
}
