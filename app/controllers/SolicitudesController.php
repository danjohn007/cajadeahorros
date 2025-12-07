<?php
/**
 * Controlador de Solicitudes de Crédito
 * Gestión del proceso de solicitud de créditos
 */

require_once CORE_PATH . '/Controller.php';

class SolicitudesController extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->requireAuth();
    }

    /**
     * Vista principal de solicitudes
     */
    public function index() {
        $estadoFilter = $_GET['estado'] ?? '';
        
        $sql = "SELECT s.*, 
                       so.numero_socio, so.nombre, so.apellido_paterno, so.apellido_materno,
                       fv.nombre as promotor_nombre,
                       pf.nombre as producto_nombre
                FROM creditos s
                LEFT JOIN socios so ON s.socio_id = so.id
                LEFT JOIN fuerza_ventas fv ON s.promotor_id = fv.id
                LEFT JOIN productos_financieros pf ON s.producto_financiero_id = pf.id
                WHERE s.estatus IN ('solicitado', 'revision', 'aprobado', 'rechazado')";
        
        $params = [];
        if ($estadoFilter) {
            $sql .= " AND s.estatus = ?";
            $params[] = $estadoFilter;
        }
        
        $sql .= " ORDER BY s.created_at DESC LIMIT 100";
        
        $solicitudes = $this->db->fetchAll($sql, $params);
        
        $this->view('solicitudes/index', [
            'pageTitle' => 'Solicitudes de Crédito',
            'solicitudes' => $solicitudes
        ]);
    }

    /**
     * Recepción de solicitudes de crédito
     */
    public function recepcion() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $socio_id = $_POST['socio_id'] ?? null;
                $producto_financiero_id = $_POST['producto_financiero_id'] ?? null;
                $monto_solicitado = $_POST['monto_solicitado'] ?? 0;
                $plazo_meses = $_POST['plazo_meses'] ?? 0;
                $destino_credito = $_POST['destino_credito'] ?? '';
                
                if (!$socio_id || !$producto_financiero_id) {
                    throw new Exception('Datos incompletos');
                }
                
                // Generar número de solicitud
                $numero_solicitud = 'SOL-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                
                $id = $this->db->insert('creditos', [
                    'numero_credito' => $numero_solicitud,
                    'socio_id' => $socio_id,
                    'producto_financiero_id' => $producto_financiero_id,
                    'monto_solicitado' => $monto_solicitado,
                    'plazo_meses' => $plazo_meses,
                    'destino_credito' => $destino_credito,
                    'estatus' => 'solicitado',
                    'fecha_solicitud' => date('Y-m-d'),
                    'tipo_credito_id' => 1
                ]);
                
                $this->logAction(
                    $_SESSION['user_id'],
                    'crear_solicitud',
                    "Solicitud de crédito creada: $numero_solicitud",
                    'creditos',
                    $id
                );
                
                $this->redirect('/solicitudes');
            } catch (Exception $e) {
                $this->setFlashMessage('Error al crear solicitud: ' . $e->getMessage(), 'error');
                $this->redirect('/solicitudes/recepcion');
            }
        }
        
        // Obtener catálogos
        $socios = $this->db->fetchAll("SELECT id, numero_socio, nombre, apellido_paterno FROM socios WHERE estatus = 'activo' ORDER BY nombre");
        $productos = $this->db->fetchAll("SELECT id, nombre, tipo FROM productos_financieros WHERE activo = 1");
        
        $this->view('solicitudes/recepcion', [
            'pageTitle' => 'Recepción de Solicitudes',
            'socios' => $socios,
            'productos' => $productos
        ]);
    }

    /**
     * Captura y verificación de datos del solicitante
     */
    public function captura($id) {
        $solicitud = $this->db->fetch("SELECT * FROM creditos WHERE id = ?", [$id]);
        
        if (!$solicitud) {
            $this->redirect('/solicitudes');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Actualizar datos del solicitante
                $this->db->update('creditos', $id, [
                    'estatus' => 'revision',
                    'observaciones' => $_POST['observaciones'] ?? ''
                ]);
                
                $this->setFlashMessage('Datos capturados correctamente', 'success');
                $this->redirect('/solicitudes');
            } catch (Exception $e) {
                $this->setFlashMessage('Error al capturar datos', 'error');
            }
        }
        
        $socio = $this->db->fetch("SELECT * FROM socios WHERE id = ?", [$solicitud['socio_id']]);
        
        $this->view('solicitudes/captura', [
            'pageTitle' => 'Captura de Datos',
            'solicitud' => $solicitud,
            'socio' => $socio
        ]);
    }

    /**
     * Evaluación preliminar de requisitos
     */
    public function evaluacion($id) {
        $solicitud = $this->db->fetch("SELECT c.*, s.fecha_nacimiento, s.nombre, s.apellido_paterno 
                                       FROM creditos c 
                                       JOIN socios s ON c.socio_id = s.id 
                                       WHERE c.id = ?", [$id]);
        
        if (!$solicitud) {
            $this->redirect('/solicitudes');
            return;
        }
        
        // Validar edad vs plazo
        $edad = floor((time() - strtotime($solicitud['fecha_nacimiento'])) / (365.25 * 24 * 60 * 60));
        $plazo_maximo_permitido = ($edad >= 69) ? 12 : $solicitud['plazo_meses'];
        
        // Obtener checklist
        $checklist = $this->db->fetchAll(
            "SELECT ci.* FROM checklist_items ci
             JOIN checklists_credito cc ON ci.checklist_id = cc.id
             WHERE cc.tipo_operacion = 'apertura' AND cc.activo = 1
             ORDER BY ci.orden"
        );
        
        $this->view('solicitudes/evaluacion', [
            'pageTitle' => 'Evaluación Preliminar',
            'solicitud' => $solicitud,
            'edad' => $edad,
            'plazo_maximo_permitido' => $plazo_maximo_permitido,
            'checklist' => $checklist
        ]);
    }

    /**
     * Gestión de expedientes digitales
     */
    public function expediente($id) {
        $solicitud = $this->db->fetch("SELECT * FROM creditos WHERE id = ?", [$id]);
        
        if (!$solicitud) {
            $this->redirect('/solicitudes');
            return;
        }
        
        // Obtener documentos
        $documentos = $this->db->fetchAll(
            "SELECT * FROM documentos_credito WHERE credito_id = ? ORDER BY created_at DESC",
            [$id]
        );
        
        $this->view('solicitudes/expediente', [
            'pageTitle' => 'Expediente Digital',
            'solicitud' => $solicitud,
            'documentos' => $documentos
        ]);
    }

    /**
     * Asignación y seguimiento a la fuerza de ventas
     */
    public function asignacion() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $solicitud_id = $_POST['solicitud_id'] ?? null;
                $promotor_id = $_POST['promotor_id'] ?? null;
                
                if (!$solicitud_id || !$promotor_id) {
                    throw new Exception('Datos incompletos');
                }
                
                $this->db->update('creditos', $solicitud_id, [
                    'promotor_id' => $promotor_id
                ]);
                
                $this->logAction(
                    $_SESSION['user_id'],
                    'asignar_promotor',
                    "Promotor asignado a solicitud #$solicitud_id",
                    'creditos',
                    $solicitud_id
                );
                
                $this->jsonResponse(['success' => true]);
                return;
            } catch (Exception $e) {
                $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
                return;
            }
        }
        
        // Listar solicitudes sin asignar
        $solicitudes = $this->db->fetchAll(
            "SELECT c.*, s.nombre, s.apellido_paterno 
             FROM creditos c
             JOIN socios s ON c.socio_id = s.id
             WHERE c.promotor_id IS NULL AND c.estatus = 'solicitado'"
        );
        
        // Listar fuerza de ventas
        $promotores = $this->db->fetchAll(
            "SELECT id, nombre, apellido_paterno, puesto 
             FROM fuerza_ventas 
             WHERE activo = 1"
        );
        
        $this->view('solicitudes/asignacion', [
            'pageTitle' => 'Asignación de Promotores',
            'solicitudes' => $solicitudes,
            'promotores' => $promotores
        ]);
    }
}
