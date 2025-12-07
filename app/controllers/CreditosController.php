<?php
/**
 * Controlador de Créditos
 * Sistema de Gestión Integral de Caja de Ahorros
 */

require_once CORE_PATH . '/Controller.php';

class CreditosController extends Controller {
    
    public function index() {
        $this->requireAuth();
        
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 15;
        $search = $_GET['q'] ?? '';
        $estatus = $_GET['estatus'] ?? '';
        $tipo = $_GET['tipo'] ?? '';
        
        // Estadísticas
        $stats = $this->getStats();
        
        $conditions = '1=1';
        $params = [];
        
        if ($search) {
            $conditions .= " AND (s.nombre LIKE :search1 OR s.apellido_paterno LIKE :search2 OR c.numero_credito LIKE :search3)";
            $params['search1'] = "%{$search}%";
            $params['search2'] = "%{$search}%";
            $params['search3'] = "%{$search}%";
        }
        if ($estatus) {
            $conditions .= " AND c.estatus = :estatus";
            $params['estatus'] = $estatus;
        }
        if ($tipo) {
            $conditions .= " AND c.tipo_credito_id = :tipo";
            $params['tipo'] = $tipo;
        }
        
        $total = $this->db->fetch(
            "SELECT COUNT(*) as total FROM creditos c
             JOIN socios s ON c.socio_id = s.id
             WHERE {$conditions}",
            $params
        )['total'];
        
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        
        $creditos = $this->db->fetchAll(
            "SELECT c.*, tc.nombre as tipo_credito,
                    s.numero_socio, s.nombre, s.apellido_paterno, s.apellido_materno,
                    u.nombre as autorizado_nombre
             FROM creditos c
             JOIN socios s ON c.socio_id = s.id
             JOIN tipos_credito tc ON c.tipo_credito_id = tc.id
             LEFT JOIN usuarios u ON c.autorizado_por = u.id
             WHERE {$conditions}
             ORDER BY c.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        
        $tiposCredito = $this->db->fetchAll("SELECT * FROM tipos_credito WHERE activo = 1");
        
        $this->view('creditos/index', [
            'pageTitle' => 'Gestión de Créditos',
            'creditos' => $creditos,
            'tiposCredito' => $tiposCredito,
            'stats' => $stats,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'search' => $search,
            'estatus' => $estatus,
            'tipo' => $tipo
        ]);
    }
    
    public function solicitud() {
        $this->requireRole(['administrador', 'operativo']);
        
        $socioId = $this->params['id'] ?? ($_GET['socio'] ?? null);
        $errors = [];
        $data = [];
        
        $tiposCredito = $this->db->fetchAll("SELECT * FROM tipos_credito WHERE activo = 1");
        
        // Si se proporciona socio_id, obtener información
        $socioPreseleccionado = null;
        if ($socioId) {
            $socioPreseleccionado = $this->db->fetch(
                "SELECT s.*, ca.saldo as saldo_ahorro
                 FROM socios s
                 LEFT JOIN cuentas_ahorro ca ON s.id = ca.socio_id AND ca.estatus = 'activa'
                 WHERE s.id = :id AND s.estatus = 'activo'",
                ['id' => $socioId]
            );
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $data = $this->sanitize($_POST);
            
            // Validaciones
            if (empty($data['socio_id'])) $errors[] = 'Debe seleccionar un socio';
            if (empty($data['tipo_credito_id'])) $errors[] = 'Debe seleccionar el tipo de crédito';
            if (empty($data['monto_solicitado']) || $data['monto_solicitado'] <= 0) $errors[] = 'El monto debe ser mayor a 0';
            if (empty($data['plazo_meses']) || $data['plazo_meses'] <= 0) $errors[] = 'El plazo debe ser mayor a 0';
            
            // Verificar tipo de crédito y límites
            if (!empty($data['tipo_credito_id'])) {
                $tipoCredito = $this->db->fetch(
                    "SELECT * FROM tipos_credito WHERE id = :id",
                    ['id' => $data['tipo_credito_id']]
                );
                
                if ($tipoCredito) {
                    if ($data['monto_solicitado'] < $tipoCredito['monto_minimo']) {
                        $errors[] = 'El monto mínimo para este tipo de crédito es $' . number_format($tipoCredito['monto_minimo'], 2);
                    }
                    if ($data['monto_solicitado'] > $tipoCredito['monto_maximo']) {
                        $errors[] = 'El monto máximo para este tipo de crédito es $' . number_format($tipoCredito['monto_maximo'], 2);
                    }
                    if ($data['plazo_meses'] < $tipoCredito['plazo_minimo']) {
                        $errors[] = 'El plazo mínimo es de ' . $tipoCredito['plazo_minimo'] . ' meses';
                    }
                    if ($data['plazo_meses'] > $tipoCredito['plazo_maximo']) {
                        $errors[] = 'El plazo máximo es de ' . $tipoCredito['plazo_maximo'] . ' meses';
                    }
                }
            }
            
            // Verificar créditos activos del socio
            if (!empty($data['socio_id'])) {
                $creditosActivos = $this->db->fetch(
                    "SELECT COUNT(*) as total FROM creditos 
                     WHERE socio_id = :socio_id AND estatus IN ('activo', 'formalizado', 'solicitud', 'en_revision')",
                    ['socio_id' => $data['socio_id']]
                )['total'];
                
                if ($creditosActivos >= 2) {
                    $errors[] = 'El socio ya tiene el máximo de créditos permitidos';
                }
                
                // Validar edad y plazo (Regla de negocio: >69 años = máximo 12 meses)
                $socio = $this->db->fetch(
                    "SELECT nombre, apellido_paterno, apellido_materno, fecha_nacimiento,
                     TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad
                     FROM socios WHERE id = :id",
                    ['id' => $data['socio_id']]
                );
                
                if ($socio && $socio['fecha_nacimiento']) {
                    $edad = $socio['edad'];
                    $plazo_solicitado = (int)$data['plazo_meses'];
                    
                    if ($edad > 69 && $plazo_solicitado > 12) {
                        $nombre_completo = trim("{$socio['nombre']} {$socio['apellido_paterno']} {$socio['apellido_materno']}");
                        $errors[] = "El solicitante {$nombre_completo} tiene {$edad} años. Por políticas de la institución, solicitantes mayores de 69 años solo pueden acceder a créditos con plazo máximo de 12 meses.";
                    }
                } else if ($socio && !$socio['fecha_nacimiento']) {
                    $errors[] = 'El socio no tiene registrada su fecha de nacimiento. Por favor, actualice el perfil del socio antes de continuar.';
                }
            }
            
            if (empty($errors)) {
                try {
                    // Generar número de crédito
                    $lastCredito = $this->db->fetch("SELECT numero_credito FROM creditos ORDER BY id DESC LIMIT 1");
                    $nextNum = 1;
                    if ($lastCredito && preg_match('/CRE-(\d+)/', $lastCredito['numero_credito'], $matches)) {
                        $nextNum = (int)$matches[1] + 1;
                    }
                    $numeroCredito = 'CRE-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
                    
                    $creditoId = $this->db->insert('creditos', [
                        'numero_credito' => $numeroCredito,
                        'socio_id' => $data['socio_id'],
                        'tipo_credito_id' => $data['tipo_credito_id'],
                        'monto_solicitado' => $data['monto_solicitado'],
                        'tasa_interes' => $tipoCredito['tasa_interes'],
                        'plazo_meses' => $data['plazo_meses'],
                        'fecha_solicitud' => date('Y-m-d'),
                        'estatus' => 'solicitud',
                        'observaciones' => $data['observaciones'] ?? ''
                    ]);
                    
                    $this->logAction('CREAR_CREDITO', 
                        "Se creó solicitud de crédito {$numeroCredito} por $" . number_format($data['monto_solicitado'], 2),
                        'creditos',
                        $creditoId
                    );
                    
                    $this->setFlash('success', 'Solicitud de crédito creada exitosamente');
                    $this->redirect('creditos/ver/' . $creditoId);
                    
                } catch (Exception $e) {
                    $errors[] = 'Error al crear la solicitud: ' . $e->getMessage();
                }
            }
        }
        
        $this->view('creditos/solicitud', [
            'pageTitle' => 'Nueva Solicitud de Crédito',
            'tiposCredito' => $tiposCredito,
            'socioPreseleccionado' => $socioPreseleccionado,
            'data' => $data,
            'errors' => $errors
        ]);
    }
    
    public function ver() {
        $this->requireAuth();
        
        $id = $this->params['id'] ?? 0;
        
        $credito = $this->db->fetch(
            "SELECT c.*, tc.nombre as tipo_credito, tc.requisitos,
                    s.numero_socio, s.nombre, s.apellido_paterno, s.apellido_materno, s.telefono, s.email,
                    u.nombre as autorizado_nombre
             FROM creditos c
             JOIN socios s ON c.socio_id = s.id
             JOIN tipos_credito tc ON c.tipo_credito_id = tc.id
             LEFT JOIN usuarios u ON c.autorizado_por = u.id
             WHERE c.id = :id",
            ['id' => $id]
        );
        
        if (!$credito) {
            $this->setFlash('error', 'Crédito no encontrado');
            $this->redirect('creditos');
        }
        
        // Obtener tabla de amortización
        $amortizacion = $this->db->fetchAll(
            "SELECT * FROM amortizacion WHERE credito_id = :id ORDER BY numero_pago",
            ['id' => $id]
        );
        
        // Obtener pagos realizados
        $pagos = $this->db->fetchAll(
            "SELECT p.*, u.nombre as usuario_nombre
             FROM pagos_credito p
             LEFT JOIN usuarios u ON p.usuario_id = u.id
             WHERE p.credito_id = :id
             ORDER BY p.fecha_pago DESC",
            ['id' => $id]
        );
        
        // Obtener documentos
        $documentos = $this->db->fetchAll(
            "SELECT * FROM documentos_credito WHERE credito_id = :id ORDER BY fecha_subida DESC",
            ['id' => $id]
        );
        
        $this->view('creditos/ver', [
            'pageTitle' => 'Detalle de Crédito',
            'credito' => $credito,
            'amortizacion' => $amortizacion,
            'pagos' => $pagos,
            'documentos' => $documentos
        ]);
    }
    
    public function autorizar() {
        $this->requireRole(['administrador']);
        
        $id = $this->params['id'] ?? 0;
        
        $credito = $this->db->fetch(
            "SELECT c.*, tc.tasa_interes
             FROM creditos c
             JOIN tipos_credito tc ON c.tipo_credito_id = tc.id
             WHERE c.id = :id AND c.estatus IN ('solicitud', 'en_revision')",
            ['id' => $id]
        );
        
        if (!$credito) {
            $this->setFlash('error', 'Crédito no encontrado o no puede ser autorizado');
            $this->redirect('creditos');
        }
        
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $accion = $_POST['accion'] ?? '';
            $montoAutorizado = (float)($_POST['monto_autorizado'] ?? $credito['monto_solicitado']);
            $observaciones = $this->sanitize($_POST['observaciones'] ?? '');
            
            if ($accion === 'autorizar') {
                try {
                    $this->db->beginTransaction();
                    
                    // Calcular cuota mensual (sistema francés)
                    $tasaMensual = $credito['tasa_interes'];
                    $plazo = $credito['plazo_meses'];
                    $cuota = $this->calcularCuota($montoAutorizado, $tasaMensual, $plazo);
                    
                    // Actualizar crédito
                    $this->db->update('creditos', [
                        'monto_autorizado' => $montoAutorizado,
                        'monto_cuota' => $cuota,
                        'saldo_actual' => $montoAutorizado,
                        'fecha_autorizacion' => date('Y-m-d'),
                        'fecha_formalizacion' => date('Y-m-d'),
                        'fecha_primer_pago' => date('Y-m-d', strtotime('+1 month')),
                        'estatus' => 'activo',
                        'autorizado_por' => $_SESSION['user_id'],
                        'observaciones' => $observaciones
                    ], 'id = :id', ['id' => $id]);
                    
                    // Generar tabla de amortización
                    $this->generarAmortizacion($id, $montoAutorizado, $tasaMensual, $plazo, $cuota);
                    
                    $this->db->commit();
                    
                    $this->logAction('AUTORIZAR_CREDITO', 
                        "Se autorizó crédito {$credito['numero_credito']} por $" . number_format($montoAutorizado, 2),
                        'creditos',
                        $id
                    );
                    
                    $this->setFlash('success', 'Crédito autorizado exitosamente');
                    $this->redirect('creditos/ver/' . $id);
                    
                } catch (Exception $e) {
                    $this->db->rollBack();
                    $errors[] = 'Error al autorizar: ' . $e->getMessage();
                }
                
            } elseif ($accion === 'rechazar') {
                $this->db->update('creditos', [
                    'estatus' => 'rechazado',
                    'observaciones' => $observaciones
                ], 'id = :id', ['id' => $id]);
                
                $this->logAction('RECHAZAR_CREDITO', 
                    "Se rechazó crédito {$credito['numero_credito']}",
                    'creditos',
                    $id
                );
                
                $this->setFlash('success', 'Crédito rechazado');
                $this->redirect('creditos/ver/' . $id);
            }
        }
        
        // Obtener información del socio
        $socio = $this->db->fetch(
            "SELECT s.*, ca.saldo as saldo_ahorro,
                    (SELECT COUNT(*) FROM creditos WHERE socio_id = s.id AND estatus IN ('activo', 'formalizado')) as creditos_activos,
                    (SELECT COALESCE(SUM(saldo_actual), 0) FROM creditos WHERE socio_id = s.id AND estatus IN ('activo', 'formalizado')) as deuda_total
             FROM socios s
             LEFT JOIN cuentas_ahorro ca ON s.id = ca.socio_id AND ca.estatus = 'activa'
             WHERE s.id = :id",
            ['id' => $credito['socio_id']]
        );
        
        $this->view('creditos/autorizar', [
            'pageTitle' => 'Autorizar Crédito',
            'credito' => $credito,
            'socio' => $socio,
            'errors' => $errors
        ]);
    }
    
    public function pago() {
        $this->requireRole(['administrador', 'operativo']);
        
        $id = $this->params['id'] ?? 0;
        
        // Validate ID is a positive integer
        if (!is_numeric($id) || (int)$id <= 0) {
            $this->setFlash('error', 'ID de crédito inválido');
            $this->redirect('creditos');
        }
        $id = (int)$id;
        
        $credito = $this->db->fetch(
            "SELECT c.*, s.numero_socio, s.nombre, s.apellido_paterno
             FROM creditos c
             JOIN socios s ON c.socio_id = s.id
             WHERE c.id = :id AND c.estatus = 'activo'",
            ['id' => $id]
        );
        
        if (!$credito) {
            $this->setFlash('error', 'Crédito no encontrado o no está activo');
            $this->redirect('creditos');
        }
        
        // Obtener próximo pago pendiente
        $proximoPago = $this->db->fetch(
            "SELECT * FROM amortizacion 
             WHERE credito_id = :id AND estatus IN ('pendiente', 'vencido')
             ORDER BY numero_pago LIMIT 1",
            ['id' => $id]
        );
        
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $monto = (float)($_POST['monto'] ?? 0);
            $referencia = $this->sanitize($_POST['referencia'] ?? '');
            $metodoPago = $this->sanitize($_POST['metodo_pago'] ?? 'efectivo');
            $observaciones = $this->sanitize($_POST['observaciones'] ?? '');
            
            // Validate payment method
            $metodosValidos = ['efectivo', 'transferencia', 'cheque', 'tarjeta_debito', 'tarjeta_credito', 'deposito', 'nomina', 'oxxo', 'spei', 'otro'];
            if (!in_array($metodoPago, $metodosValidos)) {
                $metodoPago = 'efectivo';
            }
            
            if ($monto <= 0) {
                $errors[] = 'El monto debe ser mayor a 0';
            }
            
            // Handle file upload
            $rutaComprobante = null;
            if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['comprobante'];
                
                // Validate file type using finfo extension
                $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
                
                // Check if finfo extension is available
                if (!extension_loaded('fileinfo')) {
                    $errors[] = 'La extensión fileinfo no está disponible en el servidor';
                } else {
                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $mimeType = $finfo->file($file['tmp_name']);
                    
                    if (!in_array($mimeType, $allowedTypes)) {
                        $errors[] = 'Tipo de archivo no permitido. Solo se permiten JPG, PNG y PDF';
                    } elseif ($file['size'] > 5 * 1024 * 1024) {
                        $errors[] = 'El archivo es demasiado grande. Máximo 5MB';
                    } else {
                        // Create directory if not exists - using already validated $id
                        $uploadDir = UPLOADS_PATH . '/comprobantes/' . $id;
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }
                        
                        // Generate unique filename based on validated mime type
                        $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'application/pdf' => 'pdf'];
                        $ext = $extensions[$mimeType] ?? 'bin';
                        $fileName = 'pago_' . date('Ymd_His') . '_' . uniqid('', true) . '.' . $ext;
                        $filePath = $uploadDir . '/' . $fileName;
                        
                        if (move_uploaded_file($file['tmp_name'], $filePath)) {
                            $rutaComprobante = 'uploads/comprobantes/' . $id . '/' . $fileName;
                        } else {
                            $errors[] = 'Error al guardar el comprobante';
                        }
                    }
                }
            }
            
            if (empty($errors) && $proximoPago) {
                try {
                    $this->db->beginTransaction();
                    
                    // Registrar pago with new fields
                    $pagoData = [
                        'credito_id' => $id,
                        'amortizacion_id' => $proximoPago['id'],
                        'monto' => $monto,
                        'monto_capital' => $proximoPago['monto_capital'],
                        'monto_interes' => $proximoPago['monto_interes'],
                        'fecha_pago' => date('Y-m-d H:i:s'),
                        'origen' => $metodoPago,
                        'referencia' => $referencia,
                        'usuario_id' => $_SESSION['user_id'],
                        'observaciones' => $observaciones
                    ];
                    
                    $pagoId = $this->db->insert('pagos_credito', $pagoData);
                    
                    // Save receipt file path if uploaded
                    if ($rutaComprobante) {
                        $this->db->insert('documentos_credito', [
                            'credito_id' => $id,
                            'tipo' => 'comprobante_pago',
                            'nombre_archivo' => basename($_FILES['comprobante']['name']),
                            'ruta_archivo' => $rutaComprobante,
                            'fecha_subida' => date('Y-m-d H:i:s'),
                            'usuario_id' => $_SESSION['user_id']
                        ]);
                    }
                    
                    // Actualizar amortización
                    $this->db->update('amortizacion', [
                        'fecha_pago' => date('Y-m-d'),
                        'monto_pagado' => $monto,
                        'estatus' => 'pagado'
                    ], 'id = :id', ['id' => $proximoPago['id']]);
                    
                    // Actualizar saldo del crédito
                    $nuevoSaldo = $credito['saldo_actual'] - $proximoPago['monto_capital'];
                    $pagosRealizados = $credito['pagos_realizados'] + 1;
                    
                    $estatusCredito = 'activo';
                    if ($nuevoSaldo <= 0) {
                        $estatusCredito = 'liquidado';
                        $nuevoSaldo = 0;
                    }
                    
                    $this->db->update('creditos', [
                        'saldo_actual' => $nuevoSaldo,
                        'pagos_realizados' => $pagosRealizados,
                        'estatus' => $estatusCredito
                    ], 'id = :id', ['id' => $id]);
                    
                    $this->db->commit();
                    
                    $this->logAction('PAGO_CREDITO', 
                        "Se registró pago de $" . number_format($monto, 2) . " ({$metodoPago}) en crédito {$credito['numero_credito']}",
                        'creditos',
                        $id
                    );
                    
                    $this->setFlash('success', 'Pago registrado exitosamente');
                    $this->redirect('creditos/ver/' . $id);
                    
                } catch (Exception $e) {
                    $this->db->rollBack();
                    $errors[] = 'Error al registrar pago: ' . $e->getMessage();
                }
            }
        }
        
        $this->view('creditos/pago', [
            'pageTitle' => 'Registrar Pago',
            'credito' => $credito,
            'proximoPago' => $proximoPago,
            'errors' => $errors
        ]);
    }
    
    public function amortizacion() {
        $this->requireAuth();
        
        $id = $this->params['id'] ?? 0;
        
        $credito = $this->db->fetch(
            "SELECT c.*, tc.nombre as tipo_credito,
                    s.numero_socio, s.nombre, s.apellido_paterno
             FROM creditos c
             JOIN socios s ON c.socio_id = s.id
             JOIN tipos_credito tc ON c.tipo_credito_id = tc.id
             WHERE c.id = :id",
            ['id' => $id]
        );
        
        if (!$credito) {
            $this->setFlash('error', 'Crédito no encontrado');
            $this->redirect('creditos');
        }
        
        $amortizacion = $this->db->fetchAll(
            "SELECT * FROM amortizacion WHERE credito_id = :id ORDER BY numero_pago",
            ['id' => $id]
        );
        
        $this->view('creditos/amortizacion', [
            'pageTitle' => 'Tabla de Amortización',
            'credito' => $credito,
            'amortizacion' => $amortizacion
        ]);
    }
    
    private function getStats() {
        $carteraTotal = $this->db->fetch(
            "SELECT COALESCE(SUM(saldo_actual), 0) as total FROM creditos WHERE estatus IN ('activo', 'formalizado')"
        )['total'];
        
        $creditosActivos = $this->db->fetch(
            "SELECT COUNT(*) as total FROM creditos WHERE estatus IN ('activo', 'formalizado')"
        )['total'];
        
        $solicitudesPendientes = $this->db->fetch(
            "SELECT COUNT(*) as total FROM creditos WHERE estatus IN ('solicitud', 'en_revision')"
        )['total'];
        
        $carteraVencida = $this->db->fetch(
            "SELECT COALESCE(SUM(monto_total), 0) as total FROM amortizacion 
             WHERE estatus = 'vencido' OR (estatus = 'pendiente' AND fecha_vencimiento < CURDATE())"
        )['total'];
        
        return [
            'carteraTotal' => $carteraTotal,
            'creditosActivos' => $creditosActivos,
            'solicitudesPendientes' => $solicitudesPendientes,
            'carteraVencida' => $carteraVencida
        ];
    }
    
    private function calcularCuota($monto, $tasaMensual, $plazo) {
        if ($tasaMensual == 0) {
            return $monto / $plazo;
        }
        return $monto * ($tasaMensual * pow(1 + $tasaMensual, $plazo)) / (pow(1 + $tasaMensual, $plazo) - 1);
    }
    
    private function generarAmortizacion($creditoId, $monto, $tasaMensual, $plazo, $cuota) {
        $saldo = $monto;
        $fechaPago = date('Y-m-d', strtotime('+1 month'));
        
        for ($i = 1; $i <= $plazo; $i++) {
            $interes = $saldo * $tasaMensual;
            $capital = $cuota - $interes;
            
            // Ajuste para el último pago
            if ($i == $plazo) {
                $capital = $saldo;
                $cuota = $capital + $interes;
            }
            
            $saldo -= $capital;
            
            $this->db->insert('amortizacion', [
                'credito_id' => $creditoId,
                'numero_pago' => $i,
                'fecha_vencimiento' => $fechaPago,
                'monto_capital' => round($capital, 2),
                'monto_interes' => round($interes, 2),
                'monto_total' => round($capital + $interes, 2),
                'saldo_restante' => round(max(0, $saldo), 2),
                'estatus' => 'pendiente'
            ]);
            
            $fechaPago = date('Y-m-d', strtotime($fechaPago . ' +1 month'));
        }
    }
    
    /**
     * Generación de propuestas de crédito
     */
    public function propuesta($id = null) {
        if ($id) {
            $credito = $this->db->fetch(
                "SELECT c.*, s.nombre, s.apellido_paterno, s.fecha_nacimiento,
                        pf.nombre as producto_nombre, pf.tasa_interes_min, pf.tasa_interes_max
                 FROM creditos c
                 JOIN socios s ON c.socio_id = s.id
                 LEFT JOIN productos_financieros pf ON c.producto_financiero_id = pf.id
                 WHERE c.id = ?",
                [$id]
            );
            
            if (!$credito) {
                $this->redirect('/creditos');
                return;
            }
            
            // Calcular edad
            $edad = floor((time() - strtotime($credito['fecha_nacimiento'])) / (365.25 * 24 * 60 * 60));
            
            $this->view('creditos/propuesta', [
                'pageTitle' => 'Propuesta de Crédito',
                'credito' => $credito,
                'edad' => $edad
            ]);
        } else {
            $this->redirect('/creditos');
        }
    }
    
    /**
     * Configuración de motor de reglas de crédito
     */
    public function motorReglas() {
        $this->requireAuth(['administrador']);
        
        $politicas = $this->db->fetchAll(
            "SELECT pc.*, pf.nombre as producto_nombre
             FROM politicas_credito pc
             LEFT JOIN productos_financieros pf ON pc.producto_id = pf.id
             ORDER BY pc.activo DESC, pc.nombre"
        );
        
        $this->view('creditos/motor_reglas', [
            'pageTitle' => 'Motor de Reglas de Crédito',
            'politicas' => $politicas
        ]);
    }
    
    /**
     * Ejecución de políticas de crédito
     */
    public function ejecutarPoliticas($credito_id) {
        $credito = $this->db->fetch("SELECT * FROM creditos WHERE id = ?", [$credito_id]);
        
        if (!$credito) {
            $this->jsonResponse(['success' => false, 'message' => 'Crédito no encontrado'], 404);
            return;
        }
        
        $socio = $this->db->fetch("SELECT * FROM socios WHERE id = ?", [$credito['socio_id']]);
        
        // Validar edad vs plazo
        $edad = floor((time() - strtotime($socio['fecha_nacimiento'])) / (365.25 * 24 * 60 * 60));
        $plazo_maximo = ($edad >= 69) ? 12 : 999;
        
        $validaciones = [
            'edad_plazo' => [
                'valido' => $credito['plazo_meses'] <= $plazo_maximo,
                'mensaje' => $credito['plazo_meses'] <= $plazo_maximo ? 'OK' : "El plazo máximo para la edad del solicitante es $plazo_maximo meses"
            ]
        ];
        
        // Validar requiere aval
        if ($credito['producto_financiero_id']) {
            $producto = $this->db->fetch(
                "SELECT * FROM productos_financieros WHERE id = ?",
                [$credito['producto_financiero_id']]
            );
            
            if ($producto && $producto['requiere_aval'] && $credito['monto_solicitado'] >= $producto['monto_requiere_aval']) {
                $avales_count = $this->db->fetch(
                    "SELECT COUNT(*) as total FROM avales_obligados WHERE credito_id = ? AND activo = 1",
                    [$credito_id]
                )['total'];
                
                $validaciones['requiere_aval'] = [
                    'valido' => $avales_count > 0,
                    'mensaje' => $avales_count > 0 ? 'OK' : 'Este crédito requiere al menos un aval'
                ];
            }
        }
        
        $this->jsonResponse([
            'success' => true,
            'validaciones' => $validaciones,
            'todas_validas' => !in_array(false, array_column($validaciones, 'valido'))
        ]);
    }
    
    /**
     * Documentación de garantías y avales
     */
    public function garantiasAvales($credito_id) {
        $credito = $this->db->fetch("SELECT * FROM creditos WHERE id = ?", [$credito_id]);
        
        if (!$credito) {
            $this->redirect('/creditos');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $tipo = $_POST['tipo'] ?? 'aval';
                
                if ($tipo === 'garantia') {
                    $this->db->insert('garantias', [
                        'credito_id' => $credito_id,
                        'tipo' => $_POST['tipo_garantia'],
                        'descripcion' => $_POST['descripcion'],
                        'valor_estimado' => $_POST['valor_estimado'],
                        'fecha_valuacion' => date('Y-m-d'),
                        'activo' => 1
                    ]);
                } else {
                    $this->db->insert('avales_obligados', [
                        'credito_id' => $credito_id,
                        'tipo' => $_POST['tipo_aval'],
                        'nombre' => $_POST['nombre'],
                        'apellido_paterno' => $_POST['apellido_paterno'],
                        'apellido_materno' => $_POST['apellido_materno'],
                        'rfc' => $_POST['rfc'],
                        'curp' => $_POST['curp'],
                        'telefono' => $_POST['telefono'],
                        'direccion' => $_POST['direccion'],
                        'activo' => 1
                    ]);
                }
                
                $this->setFlash('success', 'Registro agregado correctamente');
                $this->redirect('/creditos/garantias-avales/' . $credito_id);
            } catch (Exception $e) {
                $this->setFlash('error', 'Error al agregar registro');
            }
        }
        
        $avales = $this->db->fetchAll(
            "SELECT * FROM avales_obligados WHERE credito_id = ? AND activo = 1",
            [$credito_id]
        );
        
        $garantias = $this->db->fetchAll(
            "SELECT * FROM garantias WHERE credito_id = ? AND activo = 1",
            [$credito_id]
        );
        
        $this->view('creditos/garantias_avales', [
            'pageTitle' => 'Garantías y Avales',
            'credito' => $credito,
            'avales' => $avales,
            'garantias' => $garantias
        ]);
    }
    
    /**
     * Gestión de comité de crédito
     */
    public function comite() {
        $this->requireAuth(['administrador', 'operativo']);
        
        // Solicitudes pendientes de revisión
        $solicitudes = $this->db->fetchAll(
            "SELECT c.*, s.nombre, s.apellido_paterno,
                    pf.nombre as producto_nombre
             FROM creditos c
             JOIN socios s ON c.socio_id = s.id
             LEFT JOIN productos_financieros pf ON c.producto_financiero_id = pf.id
             WHERE c.estatus = 'revision'
             ORDER BY c.fecha_solicitud ASC"
        );
        
        $this->view('creditos/comite', [
            'pageTitle' => 'Comité de Crédito',
            'solicitudes' => $solicitudes
        ]);
    }
    
    /**
     * Autorización y rechazo de solicitudes (ampliación del método autorizar)
     */
    public function rechazar($id) {
        $credito = $this->db->fetch("SELECT * FROM creditos WHERE id = ?", [$id]);
        
        if (!$credito) {
            $this->redirect('/creditos');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $motivo_rechazo = $_POST['motivo_rechazo'] ?? '';
                
                if (empty($motivo_rechazo)) {
                    throw new Exception('Debe especificar el motivo del rechazo');
                }
                
                $this->db->update('creditos', $id, [
                    'estatus' => 'rechazado',
                    'motivo_rechazo' => $motivo_rechazo,
                    'fecha_rechazo' => date('Y-m-d')
                ]);
                
                $this->logAction(
                    $_SESSION['user_id'],
                    'rechazar_credito',
                    "Crédito rechazado #$id: $motivo_rechazo",
                    'creditos',
                    $id
                );
                
                $this->setFlash('success', 'Crédito rechazado correctamente');
                $this->redirect('/creditos');
            } catch (Exception $e) {
                $this->setFlash('error', 'Error al rechazar crédito: ' . $e->getMessage());
            }
        }
        
        $this->view('creditos/rechazar', [
            'pageTitle' => 'Rechazar Solicitud',
            'credito' => $credito
        ]);
    }
}
