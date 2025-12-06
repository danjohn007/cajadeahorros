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
            $conditions .= " AND (s.nombre LIKE :search1 OR s.apellido_paterno LIKE :search2 OR ca.numero_cuenta LIKE :search3 OR s.numero_socio LIKE :search4)";
            $searchTerm = "%{$search}%";
            $params['search1'] = $searchTerm;
            $params['search2'] = $searchTerm;
            $params['search3'] = $searchTerm;
            $params['search4'] = $searchTerm;
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
        $movimientoRealizado = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $data = $this->sanitize($_POST);
            
            // Validaciones
            if (empty($data['socio_id'])) $errors[] = 'Debe seleccionar un socio';
            if (empty($data['tipo'])) $errors[] = 'Debe seleccionar el tipo de movimiento';
            if (empty($data['monto']) || $data['monto'] <= 0) $errors[] = 'El monto debe ser mayor a 0';
            if (empty($data['metodo_pago'])) $errors[] = 'Debe seleccionar un método de pago';
            
            // Verificar cuenta
            $cuenta = null;
            $socio = null;
            if (!empty($data['socio_id'])) {
                $cuenta = $this->db->fetch(
                    "SELECT ca.*, s.numero_socio, s.nombre, s.apellido_paterno, s.apellido_materno 
                     FROM cuentas_ahorro ca 
                     JOIN socios s ON ca.socio_id = s.id
                     WHERE ca.socio_id = :socio_id AND ca.estatus = 'activa'",
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
                    
                    // Handle file upload
                    $comprobanteArchivo = null;
                    if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] === UPLOAD_ERR_OK) {
                        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
                        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
                        $maxSize = 5 * 1024 * 1024; // 5MB
                        
                        // Get file extension
                        $ext = strtolower(pathinfo($_FILES['comprobante']['name'], PATHINFO_EXTENSION));
                        
                        // Validate extension first
                        if (!in_array($ext, $allowedExtensions)) {
                            $errors[] = 'Extensión de archivo no válida. Use JPG, PNG, GIF o PDF.';
                        } else {
                            // Use finfo for more secure MIME type detection
                            $finfo = finfo_open(FILEINFO_MIME_TYPE);
                            $fileType = finfo_file($finfo, $_FILES['comprobante']['tmp_name']);
                            finfo_close($finfo);
                            
                            $fileSize = $_FILES['comprobante']['size'];
                            
                            if (!in_array($fileType, $allowedTypes)) {
                                $errors[] = 'Formato de archivo no válido. El tipo MIME no coincide con la extensión.';
                            } elseif ($fileSize > $maxSize) {
                                $errors[] = 'El archivo es demasiado grande. Máximo 5MB.';
                            } else {
                                // Ensure uploads directory exists with restrictive permissions
                                $uploadsDir = UPLOADS_PATH . '/comprobantes';
                                if (!is_dir($uploadsDir)) {
                                    mkdir($uploadsDir, 0750, true);
                                }
                                
                                // Generate secure filename
                                $comprobanteArchivo = 'comprobante_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
                                
                                if (!move_uploaded_file($_FILES['comprobante']['tmp_name'], $uploadsDir . '/' . $comprobanteArchivo)) {
                                    $uploadError = error_get_last();
                                    $errors[] = 'Error al guardar el comprobante: ' . ($uploadError['message'] ?? 'Error desconocido');
                                    $comprobanteArchivo = null;
                                }
                            }
                        }
                    }
                    
                    // If there were errors uploading, rollback
                    if (!empty($errors)) {
                        $this->db->rollBack();
                    } else {
                        // Registrar movimiento
                        $this->db->insert('movimientos_ahorro', [
                            'cuenta_id' => $cuenta['id'],
                            'tipo' => $data['tipo'],
                            'monto' => $monto,
                            'saldo_anterior' => $saldoAnterior,
                            'saldo_nuevo' => $saldoNuevo,
                            'concepto' => $data['concepto'] ?? '',
                            'referencia' => $data['referencia'] ?? '',
                            'metodo_pago' => $data['metodo_pago'],
                            'comprobante' => $comprobanteArchivo,
                            'origen' => 'ventanilla',
                            'usuario_id' => $_SESSION['user_id'],
                            'fecha' => date('Y-m-d H:i:s')
                        ]);
                        
                        $movimientoId = $this->db->lastInsertId();
                        
                        // Actualizar saldo de cuenta
                        $this->db->update('cuentas_ahorro', 
                            ['saldo' => $saldoNuevo],
                            'id = :id',
                            ['id' => $cuenta['id']]
                        );
                        
                        $this->db->commit();
                        
                        $this->logAction('MOVIMIENTO_AHORRO', 
                            ucfirst($data['tipo']) . " de $" . number_format($monto, 2) . " en cuenta " . $cuenta['numero_cuenta'] . " - Método: " . $data['metodo_pago'],
                            'movimientos_ahorro',
                            $movimientoId
                        );
                        
                        // Store movement data for printing
                        $movimientoRealizado = [
                            'id' => $movimientoId,
                        'tipo' => $data['tipo'],
                        'monto' => $monto,
                        'saldo_anterior' => $saldoAnterior,
                        'saldo_nuevo' => $saldoNuevo,
                        'concepto' => $data['concepto'] ?? '',
                        'referencia' => $data['referencia'] ?? '',
                        'fecha' => date('Y-m-d H:i:s'),
                        'numero_cuenta' => $cuenta['numero_cuenta'],
                        'numero_socio' => $cuenta['numero_socio'],
                        'nombre_socio' => $cuenta['nombre'] . ' ' . $cuenta['apellido_paterno'] . ' ' . ($cuenta['apellido_materno'] ?? ''),
                            'usuario' => $_SESSION['user_name'] ?? 'Sistema'
                        ];
                        
                        $this->setFlash('success', 'Movimiento registrado exitosamente');
                        // Don't redirect - show form with print option
                        $data = []; // Clear form data
                    }
                    
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
            'errors' => $errors,
            'movimientoRealizado' => $movimientoRealizado
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
    
    public function cardex() {
        $this->requireAuth();
        
        $cuentaId = $this->params['id'] ?? 0;
        
        $cuenta = $this->db->fetch(
            "SELECT ca.*, s.numero_socio, s.nombre, s.apellido_paterno, s.apellido_materno,
                    s.rfc, s.curp, s.telefono, s.celular, s.email, s.direccion
             FROM cuentas_ahorro ca
             JOIN socios s ON ca.socio_id = s.id
             WHERE ca.id = :id",
            ['id' => $cuentaId]
        );
        
        if (!$cuenta) {
            $this->setFlash('error', 'Cuenta no encontrada');
            $this->redirect('ahorro');
        }
        
        // Get all movements for the cardex (complete history)
        $movimientos = $this->db->fetchAll(
            "SELECT m.*, u.nombre as usuario_nombre
             FROM movimientos_ahorro m
             LEFT JOIN usuarios u ON m.usuario_id = u.id
             WHERE m.cuenta_id = :cuenta_id
             ORDER BY m.fecha ASC",
            ['cuenta_id' => $cuentaId]
        );
        
        $this->view('ahorro/cardex', [
            'pageTitle' => 'Cardex del Socio',
            'cuenta' => $cuenta,
            'movimientos' => $movimientos
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
    
    public function cardex($id) {
        $this->requireAuth();
        
        // Get socio information
        $socio = $this->db->fetch(
            "SELECT s.*, ut.nombre as unidad_trabajo 
             FROM socios s
             LEFT JOIN unidades_trabajo ut ON s.unidad_trabajo_id = ut.id
             WHERE s.id = :id",
            ['id' => $id]
        );
        
        if (!$socio) {
            $this->setFlash('error', 'Socio no encontrado');
            $this->redirect('ahorro');
        }
        
        // Get cuenta de ahorro
        $cuenta = $this->db->fetch(
            "SELECT * FROM cuentas_ahorro WHERE socio_id = :socio_id ORDER BY id DESC LIMIT 1",
            ['socio_id' => $id]
        );
        
        if (!$cuenta) {
            $this->setFlash('error', 'El socio no tiene una cuenta de ahorro');
            $this->redirect('ahorro/socio/' . $id);
        }
        
        // Get all movimientos (transactions)
        $movimientos = $this->db->fetchAll(
            "SELECT ma.*, u.nombre as usuario_nombre
             FROM movimientos_ahorro ma
             LEFT JOIN usuarios u ON ma.usuario_id = u.id
             WHERE ma.cuenta_id = :cuenta_id
             ORDER BY ma.fecha DESC, ma.id DESC",
            ['cuenta_id' => $cuenta['id']]
        );
        
        $this->view('ahorro/cardex', [
            'pageTitle' => 'Cardex de Socio - ' . $socio['nombre'] . ' ' . $socio['apellido_paterno'],
            'socio' => $socio,
            'cuenta' => $cuenta,
            'movimientos' => $movimientos
        ]);
    }
}
