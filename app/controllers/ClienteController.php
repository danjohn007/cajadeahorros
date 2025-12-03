<?php
/**
 * Controlador del Portal del Cliente
 * Sistema de Gestión Integral de Caja de Ahorros
 */

require_once CORE_PATH . '/Controller.php';

class ClienteController extends Controller {
    
    private $socioId = null;
    
    public function __construct($params = []) {
        parent::__construct($params);
        $this->requireClienteAuth();
    }
    
    private function requireClienteAuth() {
        $this->requireAuth();
        
        // Verificar que el usuario sea cliente
        if ($_SESSION['user_role'] !== 'cliente') {
            $this->redirect('dashboard');
        }
        
        // Obtener socio vinculado
        $vinculo = $this->db->fetch(
            "SELECT socio_id FROM usuarios_socios WHERE usuario_id = :user_id",
            ['user_id' => $_SESSION['user_id']]
        );
        
        if ($vinculo) {
            $this->socioId = $vinculo['socio_id'];
        }
    }
    
    public function index() {
        if (!$this->socioId) {
            // Verificar si hay solicitud de vinculación pendiente
            $solicitudPendiente = $this->db->fetch(
                "SELECT * FROM solicitudes_vinculacion WHERE usuario_id = :user_id AND estatus IN ('pendiente', 'en_revision') ORDER BY created_at DESC LIMIT 1",
                ['user_id' => $_SESSION['user_id']]
            );
            
            $this->view('cliente/sin_vinculo', [
                'pageTitle' => 'Portal del Cliente',
                'solicitudPendiente' => $solicitudPendiente
            ]);
            return;
        }
        
        // Obtener información del socio
        $socio = $this->db->fetch(
            "SELECT s.*, ut.nombre as unidad_trabajo
             FROM socios s
             LEFT JOIN unidades_trabajo ut ON s.unidad_trabajo_id = ut.id
             WHERE s.id = :id",
            ['id' => $this->socioId]
        );
        
        // Obtener cuenta de ahorro
        $cuentaAhorro = $this->db->fetch(
            "SELECT * FROM cuentas_ahorro WHERE socio_id = :socio_id AND estatus = 'activa'",
            ['socio_id' => $this->socioId]
        );
        
        // Obtener créditos activos
        $creditos = $this->db->fetchAll(
            "SELECT c.*, tc.nombre as tipo_credito
             FROM creditos c
             JOIN tipos_credito tc ON c.tipo_credito_id = tc.id
             WHERE c.socio_id = :socio_id AND c.estatus IN ('activo', 'formalizado')
             ORDER BY c.fecha_formalizacion DESC",
            ['socio_id' => $this->socioId]
        );
        
        // Calcular deuda total
        $deudaTotal = 0;
        foreach ($creditos as $credito) {
            $deudaTotal += $credito['saldo_actual'] ?? 0;
        }
        
        // Obtener pagos vencidos
        $pagosVencidos = $this->db->fetchAll(
            "SELECT a.*, c.numero_credito
             FROM amortizacion a
             JOIN creditos c ON a.credito_id = c.id
             WHERE c.socio_id = :socio_id 
             AND (a.estatus = 'vencido' OR (a.estatus = 'pendiente' AND a.fecha_vencimiento < CURDATE()))
             ORDER BY a.fecha_vencimiento ASC",
            ['socio_id' => $this->socioId]
        );
        
        $this->view('cliente/index', [
            'pageTitle' => 'Mi Portal',
            'socio' => $socio,
            'cuentaAhorro' => $cuentaAhorro,
            'creditos' => $creditos,
            'deudaTotal' => $deudaTotal,
            'pagosVencidos' => $pagosVencidos
        ]);
    }
    
    public function cuenta() {
        if (!$this->socioId) {
            $this->redirect('cliente');
        }
        
        // Obtener cuenta de ahorro
        $cuentaAhorro = $this->db->fetch(
            "SELECT * FROM cuentas_ahorro WHERE socio_id = :socio_id AND estatus = 'activa'",
            ['socio_id' => $this->socioId]
        );
        
        if (!$cuentaAhorro) {
            $this->setFlash('warning', 'No tienes cuenta de ahorro activa');
            $this->redirect('cliente');
        }
        
        // Obtener últimos movimientos
        $movimientos = $this->db->fetchAll(
            "SELECT * FROM movimientos_ahorro 
             WHERE cuenta_id = :cuenta_id 
             ORDER BY fecha DESC 
             LIMIT 50",
            ['cuenta_id' => $cuentaAhorro['id']]
        );
        
        $this->view('cliente/cuenta', [
            'pageTitle' => 'Mi Cuenta de Ahorro',
            'cuentaAhorro' => $cuentaAhorro,
            'movimientos' => $movimientos
        ]);
    }
    
    public function creditos() {
        if (!$this->socioId) {
            $this->redirect('cliente');
        }
        
        // Obtener todos los créditos
        $creditos = $this->db->fetchAll(
            "SELECT c.*, tc.nombre as tipo_credito
             FROM creditos c
             JOIN tipos_credito tc ON c.tipo_credito_id = tc.id
             WHERE c.socio_id = :socio_id
             ORDER BY c.fecha_solicitud DESC",
            ['socio_id' => $this->socioId]
        );
        
        $this->view('cliente/creditos', [
            'pageTitle' => 'Mis Créditos',
            'creditos' => $creditos
        ]);
    }
    
    public function credito() {
        if (!$this->socioId) {
            $this->redirect('cliente');
        }
        
        $creditoId = $this->params['id'] ?? 0;
        
        // Verificar que el crédito pertenezca al socio
        $credito = $this->db->fetch(
            "SELECT c.*, tc.nombre as tipo_credito
             FROM creditos c
             JOIN tipos_credito tc ON c.tipo_credito_id = tc.id
             WHERE c.id = :id AND c.socio_id = :socio_id",
            ['id' => $creditoId, 'socio_id' => $this->socioId]
        );
        
        if (!$credito) {
            $this->setFlash('error', 'Crédito no encontrado');
            $this->redirect('cliente/creditos');
        }
        
        // Obtener tabla de amortización
        $amortizacion = $this->db->fetchAll(
            "SELECT * FROM amortizacion WHERE credito_id = :credito_id ORDER BY numero_pago",
            ['credito_id' => $creditoId]
        );
        
        // Obtener pagos realizados
        $pagos = $this->db->fetchAll(
            "SELECT * FROM pagos_credito WHERE credito_id = :credito_id ORDER BY fecha_pago DESC",
            ['credito_id' => $creditoId]
        );
        
        $this->view('cliente/credito_detalle', [
            'pageTitle' => 'Detalle de Crédito',
            'credito' => $credito,
            'amortizacion' => $amortizacion,
            'pagos' => $pagos
        ]);
    }
    
    public function amortizacion() {
        if (!$this->socioId) {
            $this->redirect('cliente');
        }
        
        $creditoId = $this->params['id'] ?? 0;
        
        // Verificar que el crédito pertenezca al socio
        $credito = $this->db->fetch(
            "SELECT c.*, tc.nombre as tipo_credito
             FROM creditos c
             JOIN tipos_credito tc ON c.tipo_credito_id = tc.id
             WHERE c.id = :id AND c.socio_id = :socio_id",
            ['id' => $creditoId, 'socio_id' => $this->socioId]
        );
        
        if (!$credito) {
            $this->setFlash('error', 'Crédito no encontrado');
            $this->redirect('cliente/creditos');
        }
        
        // Obtener tabla de amortización completa
        $amortizacion = $this->db->fetchAll(
            "SELECT * FROM amortizacion WHERE credito_id = :credito_id ORDER BY numero_pago",
            ['credito_id' => $creditoId]
        );
        
        // Calcular totales
        $totalPagado = 0;
        $totalPendiente = 0;
        $totalVencido = 0;
        
        foreach ($amortizacion as $pago) {
            if ($pago['estatus'] === 'pagado') {
                $totalPagado += $pago['monto_total'];
            } elseif ($pago['estatus'] === 'vencido' || ($pago['estatus'] === 'pendiente' && $pago['fecha_vencimiento'] < date('Y-m-d'))) {
                $totalVencido += $pago['monto_total'];
            } else {
                $totalPendiente += $pago['monto_total'];
            }
        }
        
        $this->view('cliente/amortizacion', [
            'pageTitle' => 'Tabla de Amortización',
            'credito' => $credito,
            'amortizacion' => $amortizacion,
            'totalPagado' => $totalPagado,
            'totalPendiente' => $totalPendiente,
            'totalVencido' => $totalVencido
        ]);
    }
    
    public function pagar() {
        if (!$this->socioId) {
            $this->redirect('cliente');
        }
        
        // Obtener créditos activos con saldo
        $creditos = $this->db->fetchAll(
            "SELECT c.*, tc.nombre as tipo_credito,
                    (SELECT SUM(monto_total) FROM amortizacion 
                     WHERE credito_id = c.id 
                     AND (estatus = 'vencido' OR (estatus = 'pendiente' AND fecha_vencimiento <= CURDATE()))) as monto_vencido
             FROM creditos c
             JOIN tipos_credito tc ON c.tipo_credito_id = tc.id
             WHERE c.socio_id = :socio_id AND c.estatus IN ('activo', 'formalizado') AND c.saldo_actual > 0
             ORDER BY c.numero_credito",
            ['socio_id' => $this->socioId]
        );
        
        // Calcular deuda total
        $deudaTotal = 0;
        foreach ($creditos as $credito) {
            $deudaTotal += $credito['saldo_actual'];
        }
        
        // Obtener pagos vencidos por crédito
        $pagosVencidosPorCredito = [];
        foreach ($creditos as $credito) {
            $pagosVencidosPorCredito[$credito['id']] = $this->db->fetchAll(
                "SELECT * FROM amortizacion 
                 WHERE credito_id = :credito_id 
                 AND (estatus = 'vencido' OR (estatus = 'pendiente' AND fecha_vencimiento <= CURDATE()))
                 ORDER BY numero_pago",
                ['credito_id' => $credito['id']]
            );
        }
        
        // Verificar configuración PayPal
        $paypalEnabled = getConfig('paypal_enabled', '0') === '1';
        $paypalClientId = getConfig('paypal_client_id', '');
        
        $this->view('cliente/pagar', [
            'pageTitle' => 'Realizar Pago',
            'creditos' => $creditos,
            'deudaTotal' => $deudaTotal,
            'pagosVencidosPorCredito' => $pagosVencidosPorCredito,
            'paypalEnabled' => $paypalEnabled && !empty($paypalClientId)
        ]);
    }
    
    /**
     * Solicitar vinculación de cuenta con socio
     */
    public function solicitarVinculacion() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('cliente');
        }
        
        $this->validateCsrf();
        
        // Verificar que no esté ya vinculado
        if ($this->socioId) {
            $this->setFlash('error', 'Tu cuenta ya está vinculada a un socio');
            $this->redirect('cliente');
        }
        
        // Verificar que no tenga solicitud pendiente
        $solicitudExistente = $this->db->fetch(
            "SELECT id FROM solicitudes_vinculacion WHERE usuario_id = :user_id AND estatus IN ('pendiente', 'en_revision')",
            ['user_id' => $_SESSION['user_id']]
        );
        
        if ($solicitudExistente) {
            $this->setFlash('warning', 'Ya tienes una solicitud de vinculación pendiente');
            $this->redirect('cliente');
        }
        
        $telefono = $this->sanitize($_POST['telefono'] ?? '');
        $celular = $this->sanitize($_POST['celular'] ?? '');
        $mensaje = $this->sanitize($_POST['mensaje'] ?? '');
        
        if (empty($celular)) {
            $this->setFlash('error', 'El celular/WhatsApp es requerido');
            $this->redirect('cliente');
        }
        
        // Crear solicitud de vinculación
        $solicitudId = $this->db->insert('solicitudes_vinculacion', [
            'usuario_id' => $_SESSION['user_id'],
            'nombre' => $_SESSION['user_nombre'],
            'email' => $_SESSION['user_email'],
            'telefono' => $telefono,
            'celular' => $celular,
            'whatsapp' => $celular,
            'mensaje' => $mensaje,
            'estatus' => 'pendiente'
        ]);
        
        // Crear notificaciones para administradores
        $admins = $this->db->fetchAll(
            "SELECT id FROM usuarios WHERE rol IN ('administrador', 'operativo') AND activo = 1"
        );
        
        foreach ($admins as $admin) {
            $this->db->insert('notificaciones', [
                'usuario_id' => $admin['id'],
                'tipo' => 'vinculacion',
                'titulo' => 'Nueva solicitud de vinculación',
                'mensaje' => "El usuario {$_SESSION['user_nombre']} ({$_SESSION['user_email']}) ha solicitado vincular su cuenta.",
                'url' => 'crm/customerjourney'
            ]);
        }
        
        $this->logAction('SOLICITUD_VINCULACION', 
            "El usuario solicitó vincular su cuenta. Celular: {$celular}", 
            'solicitudes_vinculacion', 
            $solicitudId
        );
        
        $this->setFlash('success', 'Tu solicitud de vinculación ha sido enviada. Te notificaremos cuando sea procesada.');
        $this->redirect('cliente');
    }
}
