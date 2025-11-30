<?php
/**
 * Controlador de Bitácora
 * Sistema de Gestión Integral de Caja de Ahorros
 */

require_once CORE_PATH . '/Controller.php';

class BitacoraController extends Controller {
    
    public function index() {
        $this->requireRole(['administrador']);
        
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 50;
        $fechaInicio = $_GET['fecha_inicio'] ?? '';
        $fechaFin = $_GET['fecha_fin'] ?? '';
        $usuario = $_GET['usuario'] ?? '';
        $accion = $_GET['accion'] ?? '';
        
        $conditions = '1=1';
        $params = [];
        
        if ($fechaInicio) {
            $conditions .= ' AND DATE(b.fecha) >= :fecha_inicio';
            $params['fecha_inicio'] = $fechaInicio;
        }
        if ($fechaFin) {
            $conditions .= ' AND DATE(b.fecha) <= :fecha_fin';
            $params['fecha_fin'] = $fechaFin;
        }
        if ($usuario) {
            $conditions .= ' AND b.usuario_id = :usuario';
            $params['usuario'] = $usuario;
        }
        if ($accion) {
            $conditions .= ' AND b.accion LIKE :accion';
            $params['accion'] = "%{$accion}%";
        }
        
        $total = $this->db->fetch(
            "SELECT COUNT(*) as total FROM bitacora b WHERE {$conditions}",
            $params
        )['total'];
        
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        
        $registros = $this->db->fetchAll(
            "SELECT b.*, u.nombre as usuario_nombre
             FROM bitacora b
             LEFT JOIN usuarios u ON b.usuario_id = u.id
             WHERE {$conditions}
             ORDER BY b.fecha DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        
        // Lista de usuarios para filtro
        $usuarios = $this->db->fetchAll("SELECT id, nombre FROM usuarios ORDER BY nombre");
        
        // Acciones disponibles
        $acciones = $this->db->fetchAll(
            "SELECT DISTINCT accion FROM bitacora ORDER BY accion"
        );
        
        $this->view('bitacora/index', [
            'pageTitle' => 'Bitácora de Acciones',
            'registros' => $registros,
            'usuarios' => $usuarios,
            'acciones' => $acciones,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'usuarioFilter' => $usuario,
            'accionFilter' => $accion
        ]);
    }
}
