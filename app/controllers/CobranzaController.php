<?php
/**
 * Controlador de Cobranza
 * Gestión de estrategias de cobranza y seguimiento de pagos
 */

require_once CORE_PATH . '/Controller.php';

class CobranzaController extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->requireAuth();
    }

    /**
     * Vista principal de cobranza
     */
    public function index() {
        // Obtener cartera vencida
        $carteraVencida = $this->db->fetchAll(
            "SELECT c.*, s.nombre, s.apellido_paterno, s.telefono, s.celular,
                    COUNT(a.id) as pagos_vencidos,
                    SUM(a.monto_total) as monto_vencido,
                    MAX(DATEDIFF(CURDATE(), a.fecha_vencimiento)) as dias_maximo_mora
             FROM creditos c
             JOIN socios s ON c.socio_id = s.id
             JOIN amortizacion a ON c.id = a.credito_id
             WHERE c.estatus IN ('activo', 'formalizado')
             AND (a.estatus = 'vencido' OR (a.estatus = 'pendiente' AND a.fecha_vencimiento < CURDATE()))
             GROUP BY c.id
             ORDER BY dias_maximo_mora DESC
             LIMIT 50"
        );
        
        $this->view('cobranza/index', [
            'pageTitle' => 'Cobranza',
            'carteraVencida' => $carteraVencida
        ]);
    }

    /**
     * Administración de estrategias de cobranza
     */
    public function estrategias() {
        $this->view('cobranza/estrategias', [
            'pageTitle' => 'Estrategias de Cobranza'
        ]);
    }

    /**
     * Asignación y monitoreo de agentes de cobranza
     */
    public function agentes() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $credito_id = $_POST['credito_id'] ?? null;
                $usuario_id = $_POST['usuario_id'] ?? null;
                
                if (!$credito_id || !$usuario_id) {
                    throw new Exception('Datos incompletos');
                }
                
                // Asignar agente mediante observaciones o campo personalizado
                $this->db->update('creditos', $credito_id, [
                    'observaciones' => 'Asignado a agente de cobranza ID: ' . $usuario_id
                ]);
                
                $this->logAction(
                    $_SESSION['user_id'],
                    'asignar_agente_cobranza',
                    "Agente de cobranza asignado a crédito #$credito_id",
                    'creditos',
                    $credito_id
                );
                
                $this->jsonResponse(['success' => true]);
                return;
            } catch (Exception $e) {
                $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
                return;
            }
        }
        
        // Listar créditos vencidos sin asignar
        $creditosVencidos = $this->db->fetchAll(
            "SELECT c.*, s.nombre, s.apellido_paterno,
                    COUNT(a.id) as pagos_vencidos
             FROM creditos c
             JOIN socios s ON c.socio_id = s.id
             JOIN amortizacion a ON c.id = a.credito_id
             WHERE c.estatus IN ('activo', 'formalizado')
             AND (a.estatus = 'vencido' OR (a.estatus = 'pendiente' AND a.fecha_vencimiento < CURDATE()))
             GROUP BY c.id"
        );
        
        // Listar agentes de cobranza (usuarios operativos)
        $agentes = $this->db->fetchAll(
            "SELECT id, nombre, email FROM usuarios 
             WHERE rol IN ('administrador', 'operativo') AND activo = 1"
        );
        
        $this->view('cobranza/agentes', [
            'pageTitle' => 'Agentes de Cobranza',
            'creditosVencidos' => $creditosVencidos,
            'agentes' => $agentes
        ]);
    }

    /**
     * Seguimiento de compromisos de pago
     */
    public function compromisos() {
        $compromisos = $this->db->fetchAll(
            "SELECT cp.*, c.numero_credito, s.nombre, s.apellido_paterno, s.telefono
             FROM convenios_pago cp
             JOIN creditos c ON cp.credito_id = c.id
             JOIN socios s ON c.socio_id = s.id
             WHERE cp.estatus = 'activo'
             ORDER BY cp.fecha_primer_pago ASC"
        );
        
        $this->view('cobranza/compromisos', [
            'pageTitle' => 'Compromisos de Pago',
            'compromisos' => $compromisos
        ]);
    }

    /**
     * Generación de convenios de pago
     */
    public function convenios($credito_id = null) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $credito_id = $_POST['credito_id'] ?? null;
                $fecha_convenio = $_POST['fecha_convenio'] ?? date('Y-m-d');
                $monto_total = $_POST['monto_total'] ?? 0;
                $numero_cuotas = $_POST['numero_cuotas'] ?? 1;
                $fecha_primer_pago = $_POST['fecha_primer_pago'] ?? null;
                $observaciones = $_POST['observaciones'] ?? '';
                
                if (!$credito_id || !$monto_total || !$numero_cuotas) {
                    throw new Exception('Datos incompletos');
                }
                
                $monto_cuota = $monto_total / $numero_cuotas;
                
                $id = $this->db->insert('convenios_pago', [
                    'credito_id' => $credito_id,
                    'fecha_convenio' => $fecha_convenio,
                    'monto_total' => $monto_total,
                    'numero_cuotas' => $numero_cuotas,
                    'monto_cuota' => $monto_cuota,
                    'fecha_primer_pago' => $fecha_primer_pago,
                    'estatus' => 'activo',
                    'usuario_id' => $_SESSION['user_id'],
                    'observaciones' => $observaciones
                ]);
                
                $this->logAction(
                    $_SESSION['user_id'],
                    'crear_convenio_pago',
                    "Convenio de pago creado para crédito #$credito_id",
                    'convenios_pago',
                    $id
                );
                
                $this->setFlashMessage('Convenio de pago creado correctamente', 'success');
                $this->redirect('/cobranza/convenios');
            } catch (Exception $e) {
                $this->setFlashMessage('Error al crear convenio: ' . $e->getMessage(), 'error');
            }
        }
        
        $credito = null;
        if ($credito_id) {
            $credito = $this->db->fetch(
                "SELECT c.*, s.nombre, s.apellido_paterno,
                        SUM(a.monto_total) as saldo_vencido
                 FROM creditos c
                 JOIN socios s ON c.socio_id = s.id
                 LEFT JOIN amortizacion a ON c.id = a.credito_id 
                    AND (a.estatus = 'vencido' OR (a.estatus = 'pendiente' AND a.fecha_vencimiento < CURDATE()))
                 WHERE c.id = ?
                 GROUP BY c.id",
                [$credito_id]
            );
        }
        
        // Listar créditos vencidos para crear convenios
        $creditosVencidos = $this->db->fetchAll(
            "SELECT c.id, c.numero_credito, s.nombre, s.apellido_paterno,
                    COUNT(a.id) as pagos_vencidos,
                    SUM(a.monto_total) as monto_vencido
             FROM creditos c
             JOIN socios s ON c.socio_id = s.id
             JOIN amortizacion a ON c.id = a.credito_id
             WHERE c.estatus IN ('activo', 'formalizado')
             AND (a.estatus = 'vencido' OR (a.estatus = 'pendiente' AND a.fecha_vencimiento < CURDATE()))
             GROUP BY c.id"
        );
        
        $this->view('cobranza/convenios', [
            'pageTitle' => 'Convenios de Pago',
            'credito' => $credito,
            'creditosVencidos' => $creditosVencidos
        ]);
    }

    /**
     * Gestión de liquidaciones
     */
    public function liquidaciones() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $credito_id = $_POST['credito_id'] ?? null;
                $tipo = $_POST['tipo'] ?? 'total';
                $fecha_liquidacion = $_POST['fecha_liquidacion'] ?? date('Y-m-d');
                $saldo_capital = $_POST['saldo_capital'] ?? 0;
                $intereses_pendientes = $_POST['intereses_pendientes'] ?? 0;
                $descuento = $_POST['descuento'] ?? 0;
                $observaciones = $_POST['observaciones'] ?? '';
                
                if (!$credito_id) {
                    throw new Exception('Datos incompletos');
                }
                
                $total_liquidado = $saldo_capital + $intereses_pendientes - $descuento;
                
                $id = $this->db->insert('liquidaciones_credito', [
                    'credito_id' => $credito_id,
                    'tipo' => $tipo,
                    'fecha_liquidacion' => $fecha_liquidacion,
                    'saldo_capital' => $saldo_capital,
                    'intereses_pendientes' => $intereses_pendientes,
                    'total_liquidado' => $total_liquidado,
                    'descuento' => $descuento,
                    'usuario_id' => $_SESSION['user_id'],
                    'observaciones' => $observaciones
                ]);
                
                // Si es liquidación total, actualizar estatus del crédito
                if ($tipo === 'total') {
                    $this->db->update('creditos', $credito_id, [
                        'estatus' => 'liquidado',
                        'saldo_actual' => 0
                    ]);
                }
                
                $this->logAction(
                    $_SESSION['user_id'],
                    'liquidar_credito',
                    "Liquidación de crédito #$credito_id - Tipo: $tipo",
                    'liquidaciones_credito',
                    $id
                );
                
                $this->setFlashMessage('Liquidación registrada correctamente', 'success');
                $this->redirect('/cobranza/liquidaciones');
            } catch (Exception $e) {
                $this->setFlashMessage('Error al registrar liquidación', 'error');
            }
        }
        
        $liquidaciones = $this->db->fetchAll(
            "SELECT l.*, c.numero_credito, s.nombre, s.apellido_paterno
             FROM liquidaciones_credito l
             JOIN creditos c ON l.credito_id = c.id
             JOIN socios s ON c.socio_id = s.id
             ORDER BY l.fecha_liquidacion DESC
             LIMIT 100"
        );
        
        $this->view('cobranza/liquidaciones', [
            'pageTitle' => 'Liquidaciones',
            'liquidaciones' => $liquidaciones
        ]);
    }

    /**
     * Reportes de gestión de cobranza
     */
    public function reportes() {
        $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-d', strtotime('-30 days'));
        $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
        
        // Estadísticas de cobranza
        $stats = $this->db->fetch(
            "SELECT 
                COUNT(DISTINCT c.id) as total_creditos_mora,
                SUM(a.monto_total) as monto_total_vencido,
                AVG(DATEDIFF(CURDATE(), a.fecha_vencimiento)) as promedio_dias_mora
             FROM creditos c
             JOIN amortizacion a ON c.id = a.credito_id
             WHERE c.estatus IN ('activo', 'formalizado')
             AND (a.estatus = 'vencido' OR (a.estatus = 'pendiente' AND a.fecha_vencimiento < CURDATE()))"
        );
        
        // Convenios activos
        $conveniosActivos = $this->db->fetch(
            "SELECT COUNT(*) as total, SUM(monto_total) as monto_total
             FROM convenios_pago
             WHERE estatus = 'activo'"
        );
        
        $this->view('cobranza/reportes', [
            'pageTitle' => 'Reportes de Cobranza',
            'stats' => $stats,
            'conveniosActivos' => $conveniosActivos,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin
        ]);
    }
}
