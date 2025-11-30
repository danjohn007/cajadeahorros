<?php
/**
 * Controlador de Ahorro
 * Sistema de Gestión Integral de Caja de Ahorros
 */

require_once CORE_PATH . '/Controller.php';

class AhorroController extends Controller {
    
    public function index() {
        $this->requireAuth();
        
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 15;
        $search = $_GET['q'] ?? '';
        
        // Estadísticas globales
        $stats = $this->getStats();
        
        // Cuentas de ahorro
        $conditions = '1=1';
        $params = [];
        
        if ($search) {
            $conditions .= " AND (s.nombre LIKE :search OR s.apellido_paterno LIKE :search OR ca.numero_cuenta LIKE :search OR s.numero_socio LIKE :search)";
            $params['search'] = "%{$search}%";
        }
        
        $total = $this->db->fetch(
            "SELECT COUNT(*) as total FROM cuentas_ahorro ca
             JOIN socios s ON ca.socio_id = s.id
             WHERE {$conditions}",
            $params
        )['total'];
        
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        
        $cuentas = $this->db->fetchAll(
            "SELECT ca.*, 
                    s.numero_socio, s.nombre, s.apellido_paterno, s.apellido_materno,
                    (SELECT MAX(fecha) FROM movimientos_ahorro WHERE cuenta_id = ca.id) as ultimo_movimiento
             FROM cuentas_ahorro ca
             JOIN socios s ON ca.socio_id = s.id
             WHERE {$conditions}
             ORDER BY ca.saldo DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        
        $this->view('ahorro/index', [
            'pageTitle' => 'Gestión de Ahorro',
            'cuentas' => $cuentas,
            'stats' => $stats,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'search' => $search
        ]);
    }
    
    public function socio() {
        $this->requireAuth();
        
        $socioId = $this->params['id'] ?? 0;
        
        $socio = $this->db->fetch(
            "SELECT s.*, ut.nombre as unidad_trabajo
             FROM socios s
             LEFT JOIN unidades_trabajo ut ON s.unidad_trabajo_id = ut.id
             WHERE s.id = :id",
            ['id' => $socioId]
        );
        
        if (!$socio) {
            $this->setFlash('error', 'Socio no encontrado');
            $this->redirect('ahorro');
        }
        
        $cuenta = $this->db->fetch(
            "SELECT * FROM cuentas_ahorro WHERE socio_id = :id",
            ['id' => $socioId]
        );
        
        $movimientos = [];
        if ($cuenta) {
            $movimientos = $this->db->fetchAll(
                "SELECT m.*, u.nombre as usuario_nombre
                 FROM movimientos_ahorro m
                 LEFT JOIN usuarios u ON m.usuario_id = u.id
                 WHERE m.cuenta_id = :cuenta_id
                 ORDER BY m.fecha DESC
                 LIMIT 50",
                ['cuenta_id' => $cuenta['id']]
            );
        }
        
        $this->view('ahorro/socio', [
            'pageTitle' => 'Ahorro de Socio',
            'socio' => $socio,
            'cuenta' => $cuenta,
            'movimientos' => $movimientos
        ]);
    }
    
    public function movimiento() {
        $this->requireRole(['administrador', 'operativo']);
        
        $errors = [];
        $data = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $data = $this->sanitize($_POST);
            
            // Validaciones
            if (empty($data['socio_id'])) $errors[] = 'Debe seleccionar un socio';
            if (empty($data['tipo'])) $errors[] = 'Debe seleccionar el tipo de movimiento';
            if (empty($data['monto']) || $data['monto'] <= 0) $errors[] = 'El monto debe ser mayor a 0';
            
            // Verificar cuenta
            $cuenta = null;
            if (!empty($data['socio_id'])) {
                $cuenta = $this->db->fetch(
                    "SELECT * FROM cuentas_ahorro WHERE socio_id = :socio_id AND estatus = 'activa'",
                    ['socio_id' => $data['socio_id']]
                );
                
                if (!$cuenta) {
                    $errors[] = 'El socio no tiene cuenta de ahorro activa';
                } elseif ($data['tipo'] === 'retiro' && $cuenta['saldo'] < $data['monto']) {
                    $errors[] = 'Saldo insuficiente para el retiro';
                }
            }
            
            if (empty($errors) && $cuenta) {
                try {
                    $this->db->beginTransaction();
                    
                    $saldoAnterior = $cuenta['saldo'];
                    $monto = (float)$data['monto'];
                    
                    if ($data['tipo'] === 'retiro') {
                        $saldoNuevo = $saldoAnterior - $monto;
                    } else {
                        $saldoNuevo = $saldoAnterior + $monto;
                    }
                    
                    // Registrar movimiento
                    $this->db->insert('movimientos_ahorro', [
                        'cuenta_id' => $cuenta['id'],
                        'tipo' => $data['tipo'],
                        'monto' => $monto,
                        'saldo_anterior' => $saldoAnterior,
                        'saldo_nuevo' => $saldoNuevo,
                        'concepto' => $data['concepto'] ?? '',
                        'referencia' => $data['referencia'] ?? '',
                        'origen' => 'ventanilla',
                        'usuario_id' => $_SESSION['user_id'],
                        'fecha' => date('Y-m-d H:i:s')
                    ]);
                    
                    // Actualizar saldo de cuenta
                    $this->db->update('cuentas_ahorro', 
                        ['saldo' => $saldoNuevo],
                        'id = :id',
                        ['id' => $cuenta['id']]
                    );
                    
                    $this->db->commit();
                    
                    $this->logAction('MOVIMIENTO_AHORRO', 
                        ucfirst($data['tipo']) . " de $" . number_format($monto, 2) . " en cuenta " . $cuenta['numero_cuenta'],
                        'movimientos_ahorro',
                        $this->db->lastInsertId()
                    );
                    
                    $this->setFlash('success', 'Movimiento registrado exitosamente');
                    $this->redirect('ahorro/socio/' . $data['socio_id']);
                    
                } catch (Exception $e) {
                    $this->db->rollBack();
                    $errors[] = 'Error al registrar el movimiento: ' . $e->getMessage();
                }
            }
        }
        
        // Para búsqueda de socios
        $socios = $this->db->fetchAll(
            "SELECT s.id, s.numero_socio, s.nombre, s.apellido_paterno, s.apellido_materno,
                    ca.saldo, ca.numero_cuenta
             FROM socios s
             JOIN cuentas_ahorro ca ON s.id = ca.socio_id
             WHERE s.estatus = 'activo' AND ca.estatus = 'activa'
             ORDER BY s.apellido_paterno, s.nombre
             LIMIT 100"
        );
        
        $this->view('ahorro/movimiento', [
            'pageTitle' => 'Registrar Movimiento de Ahorro',
            'socios' => $socios,
            'data' => $data,
            'errors' => $errors
        ]);
    }
    
    public function historial() {
        $this->requireAuth();
        
        $cuentaId = $this->params['id'] ?? 0;
        
        $cuenta = $this->db->fetch(
            "SELECT ca.*, s.numero_socio, s.nombre, s.apellido_paterno, s.apellido_materno
             FROM cuentas_ahorro ca
             JOIN socios s ON ca.socio_id = s.id
             WHERE ca.id = :id",
            ['id' => $cuentaId]
        );
        
        if (!$cuenta) {
            $this->setFlash('error', 'Cuenta no encontrada');
            $this->redirect('ahorro');
        }
        
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 20;
        $fechaInicio = $_GET['fecha_inicio'] ?? '';
        $fechaFin = $_GET['fecha_fin'] ?? '';
        $tipo = $_GET['tipo'] ?? '';
        
        $conditions = 'cuenta_id = :cuenta_id';
        $params = ['cuenta_id' => $cuentaId];
        
        if ($fechaInicio) {
            $conditions .= ' AND DATE(fecha) >= :fecha_inicio';
            $params['fecha_inicio'] = $fechaInicio;
        }
        if ($fechaFin) {
            $conditions .= ' AND DATE(fecha) <= :fecha_fin';
            $params['fecha_fin'] = $fechaFin;
        }
        if ($tipo) {
            $conditions .= ' AND tipo = :tipo';
            $params['tipo'] = $tipo;
        }
        
        $total = $this->db->fetch(
            "SELECT COUNT(*) as total FROM movimientos_ahorro WHERE {$conditions}",
            $params
        )['total'];
        
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        
        $movimientos = $this->db->fetchAll(
            "SELECT m.*, u.nombre as usuario_nombre
             FROM movimientos_ahorro m
             LEFT JOIN usuarios u ON m.usuario_id = u.id
             WHERE {$conditions}
             ORDER BY m.fecha DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        
        $this->view('ahorro/historial', [
            'pageTitle' => 'Historial de Movimientos',
            'cuenta' => $cuenta,
            'movimientos' => $movimientos,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'tipo' => $tipo
        ]);
    }
    
    private function getStats() {
        $saldoTotal = $this->db->fetch(
            "SELECT COALESCE(SUM(saldo), 0) as total FROM cuentas_ahorro WHERE estatus = 'activa'"
        )['total'];
        
        $totalCuentas = $this->db->fetch(
            "SELECT COUNT(*) as total FROM cuentas_ahorro WHERE estatus = 'activa'"
        )['total'];
        
        $aportacionesMes = $this->db->fetch(
            "SELECT COALESCE(SUM(monto), 0) as total FROM movimientos_ahorro 
             WHERE tipo = 'aportacion' AND MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())"
        )['total'];
        
        $retirosMes = $this->db->fetch(
            "SELECT COALESCE(SUM(monto), 0) as total FROM movimientos_ahorro 
             WHERE tipo = 'retiro' AND MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())"
        )['total'];
        
        return [
            'saldoTotal' => $saldoTotal,
            'totalCuentas' => $totalCuentas,
            'aportacionesMes' => $aportacionesMes,
            'retirosMes' => $retirosMes
        ];
    }
}
