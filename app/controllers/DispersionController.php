<?php
/**
 * Controlador de Dispersión de Fondos
 * Gestión del proceso de dispersión y formalización de créditos
 */

require_once CORE_PATH . '/Controller.php';

class DispersionController extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->requireAuth();
    }

    /**
     * Vista principal de dispersión
     */
    public function index() {
        $creditos = $this->db->fetchAll(
            "SELECT c.*, 
                    s.numero_socio, s.nombre, s.apellido_paterno, s.apellido_materno,
                    pf.nombre as producto_nombre
             FROM creditos c
             JOIN socios s ON c.socio_id = s.id
             LEFT JOIN productos_financieros pf ON c.producto_financiero_id = pf.id
             WHERE c.estatus IN ('aprobado', 'formalizacion')
             ORDER BY c.fecha_solicitud DESC"
        );
        
        $this->view('dispersion/index', [
            'pageTitle' => 'Dispersión de Fondos',
            'creditos' => $creditos
        ]);
    }

    /**
     * Registro de nuevos créditos formalizados
     */
    public function registrar($id) {
        $credito = $this->db->fetch("SELECT * FROM creditos WHERE id = ?", [$id]);
        
        if (!$credito || $credito['estatus'] !== 'aprobado') {
            $this->redirect('/dispersion');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $fecha_formalizacion = $_POST['fecha_formalizacion'] ?? date('Y-m-d');
                $numero_contrato = $_POST['numero_contrato'] ?? '';
                $numero_pagare = $_POST['numero_pagare'] ?? '';
                
                $this->db->update('creditos', $id, [
                    'estatus' => 'formalizado',
                    'fecha_formalizacion' => $fecha_formalizacion,
                    'numero_contrato' => $numero_contrato,
                    'numero_pagare' => $numero_pagare
                ]);
                
                $this->logAction(
                    $_SESSION['user_id'],
                    'formalizar_credito',
                    "Crédito formalizado #$id - Contrato: $numero_contrato",
                    'creditos',
                    $id
                );
                
                $this->setFlash('success', 'Crédito registrado correctamente');
                $this->redirect('/dispersion');
            } catch (Exception $e) {
                $this->setFlash('error', 'Error al registrar crédito');
            }
        }
        
        $socio = $this->db->fetch("SELECT * FROM socios WHERE id = ?", [$credito['socio_id']]);
        
        $this->view('dispersion/registrar', [
            'pageTitle' => 'Registro de Crédito',
            'credito' => $credito,
            'socio' => $socio
        ]);
    }

    /**
     * Proceso de formalización
     */
    public function formalizacion($id) {
        $credito = $this->db->fetch(
            "SELECT c.*, s.nombre, s.apellido_paterno, s.apellido_materno,
                    pf.nombre as producto_nombre
             FROM creditos c
             JOIN socios s ON c.socio_id = s.id
             LEFT JOIN productos_financieros pf ON c.producto_financiero_id = pf.id
             WHERE c.id = ?",
            [$id]
        );
        
        if (!$credito) {
            $this->redirect('/dispersion');
            return;
        }
        
        // Obtener checklist de formalización
        $checklist = $this->db->fetchAll(
            "SELECT ci.*, 
                    COALESCE(cv.completado, 0) as completado,
                    cv.fecha_completado,
                    u.nombre as validado_por_nombre
             FROM checklist_items ci
             JOIN checklists_credito cc ON ci.checklist_id = cc.id
             LEFT JOIN checklist_validaciones cv ON ci.id = cv.checklist_item_id AND cv.credito_id = ?
             LEFT JOIN usuarios u ON cv.validado_por = u.id
             WHERE cc.tipo_operacion = 'apertura' AND cc.activo = 1
             ORDER BY ci.orden",
            [$id]
        );
        
        $this->view('dispersion/formalizacion', [
            'pageTitle' => 'Proceso de Formalización',
            'credito' => $credito,
            'checklist' => $checklist
        ]);
    }

    /**
     * Generación de contratos y pagarés
     */
    public function contratos($id) {
        $credito = $this->db->fetch(
            "SELECT c.*, s.*, 
                    pf.nombre as producto_nombre, pf.tasa_interes_min
             FROM creditos c
             JOIN socios s ON c.socio_id = s.id
             LEFT JOIN productos_financieros pf ON c.producto_financiero_id = pf.id
             WHERE c.id = ?",
            [$id]
        );
        
        if (!$credito) {
            $this->redirect('/dispersion');
            return;
        }
        
        // Obtener avales si existen
        $avales = $this->db->fetchAll(
            "SELECT * FROM avales_obligados WHERE credito_id = ? AND activo = 1",
            [$id]
        );
        
        // Obtener garantías
        $garantias = $this->db->fetchAll(
            "SELECT * FROM garantias WHERE credito_id = ? AND activo = 1",
            [$id]
        );
        
        $this->view('dispersion/contratos', [
            'pageTitle' => 'Contratos y Pagarés',
            'credito' => $credito,
            'avales' => $avales,
            'garantias' => $garantias
        ]);
    }

    /**
     * Emisión de hoja de garantías
     */
    public function garantias($id) {
        $credito = $this->db->fetch(
            "SELECT c.*, s.nombre, s.apellido_paterno, s.apellido_materno
             FROM creditos c
             JOIN socios s ON c.socio_id = s.id
             WHERE c.id = ?",
            [$id]
        );
        
        if (!$credito) {
            $this->redirect('/dispersion');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $tipo = $_POST['tipo'] ?? '';
                $descripcion = $_POST['descripcion'] ?? '';
                $valor_estimado = $_POST['valor_estimado'] ?? 0;
                
                $this->db->insert('garantias', [
                    'credito_id' => $id,
                    'tipo' => $tipo,
                    'descripcion' => $descripcion,
                    'valor_estimado' => $valor_estimado,
                    'fecha_valuacion' => date('Y-m-d'),
                    'activo' => 1
                ]);
                
                $this->setFlash('success', 'Garantía registrada correctamente');
                $this->redirect('/dispersion/garantias/' . $id);
            } catch (Exception $e) {
                $this->setFlash('error', 'Error al registrar garantía');
            }
        }
        
        $garantias = $this->db->fetchAll(
            "SELECT * FROM garantias WHERE credito_id = ? ORDER BY created_at DESC",
            [$id]
        );
        
        $this->view('dispersion/garantias', [
            'pageTitle' => 'Hoja de Garantías',
            'credito' => $credito,
            'garantias' => $garantias
        ]);
    }

    /**
     * Coordinación de dispersión de fondos
     */
    public function coordinacion() {
        $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-d', strtotime('-30 days'));
        $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
        
        $creditos = $this->db->fetchAll(
            "SELECT c.*, 
                    s.numero_socio, s.nombre, s.apellido_paterno,
                    pf.nombre as producto_nombre
             FROM creditos c
             JOIN socios s ON c.socio_id = s.id
             LEFT JOIN productos_financieros pf ON c.producto_financiero_id = pf.id
             WHERE c.estatus = 'formalizado'
             AND c.fecha_formalizacion BETWEEN ? AND ?
             ORDER BY c.fecha_formalizacion DESC",
            [$fecha_inicio, $fecha_fin]
        );
        
        // Calcular totales
        $totales = [
            'cantidad' => count($creditos),
            'monto_total' => array_sum(array_column($creditos, 'monto_autorizado'))
        ];
        
        $this->view('dispersion/coordinacion', [
            'pageTitle' => 'Coordinación de Dispersión',
            'creditos' => $creditos,
            'totales' => $totales,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin
        ]);
    }
}
