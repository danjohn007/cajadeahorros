<?php
/**
 * Controlador del Sistema ESCROW
 * Sistema de Gestión Integral de Caja de Ahorros
 */

require_once CORE_PATH . '/Controller.php';

class EscrowController extends Controller {
    
    /**
     * Lista de transacciones ESCROW
     */
    public function index() {
        $this->requireAuth();
        
        // Filtros
        $estatus = $_GET['estatus'] ?? '';
        $buscar = $_GET['buscar'] ?? '';
        $fechaDesde = $_GET['fecha_desde'] ?? '';
        $fechaHasta = $_GET['fecha_hasta'] ?? '';
        
        $where = "1=1";
        $params = [];
        
        if ($estatus) {
            $where .= " AND et.estatus = :estatus";
            $params['estatus'] = $estatus;
        }
        
        if ($buscar) {
            $where .= " AND (et.numero_transaccion LIKE :buscar OR et.titulo LIKE :buscar2 
                       OR CONCAT(sc.nombre, ' ', sc.apellido_paterno) LIKE :buscar3
                       OR CONCAT(sv.nombre, ' ', sv.apellido_paterno) LIKE :buscar4
                       OR et.comprador_nombre LIKE :buscar5 OR et.vendedor_nombre LIKE :buscar6)";
            $params['buscar'] = "%{$buscar}%";
            $params['buscar2'] = "%{$buscar}%";
            $params['buscar3'] = "%{$buscar}%";
            $params['buscar4'] = "%{$buscar}%";
            $params['buscar5'] = "%{$buscar}%";
            $params['buscar6'] = "%{$buscar}%";
        }
        
        if ($fechaDesde) {
            $where .= " AND DATE(et.fecha_creacion) >= :fecha_desde";
            $params['fecha_desde'] = $fechaDesde;
        }
        
        if ($fechaHasta) {
            $where .= " AND DATE(et.fecha_creacion) <= :fecha_hasta";
            $params['fecha_hasta'] = $fechaHasta;
        }
        
        $transacciones = $this->db->fetchAll(
            "SELECT et.*, 
                    COALESCE(CONCAT(sc.nombre, ' ', sc.apellido_paterno), et.comprador_nombre) as comprador,
                    COALESCE(CONCAT(sv.nombre, ' ', sv.apellido_paterno), et.vendedor_nombre) as vendedor,
                    (SELECT COUNT(*) FROM escrow_hitos WHERE transaccion_id = et.id) as total_hitos,
                    (SELECT COUNT(*) FROM escrow_hitos WHERE transaccion_id = et.id AND estatus = 'completado') as hitos_completados
             FROM escrow_transacciones et
             LEFT JOIN socios sc ON et.comprador_id = sc.id
             LEFT JOIN socios sv ON et.vendedor_id = sv.id
             WHERE {$where}
             ORDER BY et.created_at DESC",
            $params
        );
        
        // Estadísticas
        $stats = $this->db->fetch(
            "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN estatus IN ('pendiente_deposito', 'fondos_depositados', 'en_proceso') THEN 1 ELSE 0 END) as activas,
                SUM(CASE WHEN estatus = 'liberado' THEN 1 ELSE 0 END) as completadas,
                SUM(CASE WHEN estatus = 'disputa' THEN 1 ELSE 0 END) as disputas,
                SUM(monto_total) as monto_total,
                SUM(CASE WHEN estatus IN ('fondos_depositados', 'en_proceso', 'entrega_confirmada') THEN monto_total - monto_liberado ELSE 0 END) as monto_retenido,
                SUM(comision_monto) as comisiones_totales
             FROM escrow_transacciones"
        );
        
        $this->view('escrow/index', [
            'pageTitle' => 'Sistema ESCROW',
            'transacciones' => $transacciones,
            'stats' => $stats,
            'filtros' => [
                'estatus' => $estatus,
                'buscar' => $buscar,
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta
            ]
        ]);
    }
    
    /**
     * Crear nueva transacción ESCROW
     */
    public function crear() {
        $this->requireRole(['administrador', 'operativo']);
        
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            // Validaciones
            $titulo = $this->sanitize($_POST['titulo'] ?? '');
            $tipo = $_POST['tipo'] ?? 'compraventa';
            $descripcion = $this->sanitize($_POST['descripcion'] ?? '');
            $montoTotal = floatval($_POST['monto_total'] ?? 0);
            $comisionPorcentaje = floatval($_POST['comision_porcentaje'] ?? 2.50);
            $fechaLimite = $_POST['fecha_limite'] ?? null;
            
            // Comprador
            $compradorId = !empty($_POST['comprador_id']) ? intval($_POST['comprador_id']) : null;
            $compradorNombre = $this->sanitize($_POST['comprador_nombre'] ?? '');
            $compradorEmail = filter_var($_POST['comprador_email'] ?? '', FILTER_VALIDATE_EMAIL) ?: null;
            $compradorTelefono = $this->sanitize($_POST['comprador_telefono'] ?? '');
            
            // Vendedor
            $vendedorId = !empty($_POST['vendedor_id']) ? intval($_POST['vendedor_id']) : null;
            $vendedorNombre = $this->sanitize($_POST['vendedor_nombre'] ?? '');
            $vendedorEmail = filter_var($_POST['vendedor_email'] ?? '', FILTER_VALIDATE_EMAIL) ?: null;
            $vendedorTelefono = $this->sanitize($_POST['vendedor_telefono'] ?? '');
            
            if (empty($titulo)) {
                $errors[] = 'El título es requerido';
            }
            
            if ($montoTotal <= 0) {
                $errors[] = 'El monto debe ser mayor a cero';
            }
            
            $montoMinimo = floatval(getConfig('escrow_monto_minimo', '100'));
            if ($montoTotal < $montoMinimo) {
                $errors[] = "El monto mínimo es de $" . number_format($montoMinimo, 2);
            }
            
            if (!$compradorId && empty($compradorNombre)) {
                $errors[] = 'Debe especificar un comprador';
            }
            
            if (!$vendedorId && empty($vendedorNombre)) {
                $errors[] = 'Debe especificar un vendedor';
            }
            
            if (empty($errors)) {
                $comisionMonto = round($montoTotal * ($comisionPorcentaje / 100), 2);
                
                $transaccionId = $this->db->insert('escrow_transacciones', [
                    'tipo' => $tipo,
                    'titulo' => $titulo,
                    'descripcion' => $descripcion,
                    'comprador_id' => $compradorId,
                    'comprador_nombre' => $compradorNombre,
                    'comprador_email' => $compradorEmail,
                    'comprador_telefono' => $compradorTelefono,
                    'vendedor_id' => $vendedorId,
                    'vendedor_nombre' => $vendedorNombre,
                    'vendedor_email' => $vendedorEmail,
                    'vendedor_telefono' => $vendedorTelefono,
                    'monto_total' => $montoTotal,
                    'comision_porcentaje' => $comisionPorcentaje,
                    'comision_monto' => $comisionMonto,
                    'fecha_limite' => $fechaLimite ?: null,
                    'estatus' => 'borrador',
                    'usuario_creador' => $_SESSION['user_id']
                ]);
                
                // Registrar en historial
                $this->db->insert('escrow_historial', [
                    'transaccion_id' => $transaccionId,
                    'accion' => 'CREACION',
                    'descripcion' => 'Transacción ESCROW creada',
                    'estatus_nuevo' => 'borrador',
                    'usuario_id' => $_SESSION['user_id'],
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? ''
                ]);
                
                $this->logAction('CREAR_ESCROW', "Se creó transacción ESCROW #{$transaccionId}", 'escrow_transacciones', $transaccionId);
                $this->setFlash('success', 'Transacción ESCROW creada exitosamente');
                $this->redirect('escrow/ver/' . $transaccionId);
            }
        }
        
        // Obtener socios para select
        $socios = $this->db->fetchAll(
            "SELECT id, numero_socio, nombre, apellido_paterno, apellido_materno, email, celular 
             FROM socios WHERE estatus = 'activo' ORDER BY nombre"
        );
        
        $this->view('escrow/crear', [
            'pageTitle' => 'Nueva Transacción ESCROW',
            'socios' => $socios,
            'errors' => $errors,
            'comisionDefecto' => getConfig('escrow_comision_porcentaje', '2.50'),
            'diasLimiteDefecto' => getConfig('escrow_dias_limite_defecto', '30')
        ]);
    }
    
    /**
     * Ver detalle de transacción ESCROW
     */
    public function ver() {
        $this->requireAuth();
        
        $id = $this->params['id'] ?? 0;
        
        $transaccion = $this->db->fetch(
            "SELECT et.*, 
                    COALESCE(CONCAT(sc.nombre, ' ', sc.apellido_paterno), et.comprador_nombre) as comprador,
                    sc.numero_socio as comprador_numero_socio,
                    COALESCE(CONCAT(sv.nombre, ' ', sv.apellido_paterno), et.vendedor_nombre) as vendedor,
                    sv.numero_socio as vendedor_numero_socio,
                    u.nombre as creador_nombre
             FROM escrow_transacciones et
             LEFT JOIN socios sc ON et.comprador_id = sc.id
             LEFT JOIN socios sv ON et.vendedor_id = sv.id
             LEFT JOIN usuarios u ON et.usuario_creador = u.id
             WHERE et.id = :id",
            ['id' => $id]
        );
        
        if (!$transaccion) {
            $this->setFlash('error', 'Transacción no encontrada');
            $this->redirect('escrow');
        }
        
        // Obtener hitos
        $hitos = $this->db->fetchAll(
            "SELECT h.*, u.nombre as confirmado_por_nombre
             FROM escrow_hitos h
             LEFT JOIN usuarios u ON h.confirmado_por = u.id
             WHERE h.transaccion_id = :id
             ORDER BY h.numero_hito",
            ['id' => $id]
        );
        
        // Obtener movimientos
        $movimientos = $this->db->fetchAll(
            "SELECT m.*, u.nombre as usuario_nombre
             FROM escrow_movimientos m
             LEFT JOIN usuarios u ON m.usuario_id = u.id
             WHERE m.transaccion_id = :id
             ORDER BY m.created_at DESC",
            ['id' => $id]
        );
        
        // Obtener documentos
        $documentos = $this->db->fetchAll(
            "SELECT d.*, u.nombre as subido_por_nombre
             FROM escrow_documentos d
             LEFT JOIN usuarios u ON d.subido_por = u.id
             WHERE d.transaccion_id = :id
             ORDER BY d.created_at DESC",
            ['id' => $id]
        );
        
        // Obtener historial
        $historial = $this->db->fetchAll(
            "SELECT h.*, u.nombre as usuario_nombre
             FROM escrow_historial h
             LEFT JOIN usuarios u ON h.usuario_id = u.id
             WHERE h.transaccion_id = :id
             ORDER BY h.created_at DESC",
            ['id' => $id]
        );
        
        // Obtener disputa activa si existe
        $disputa = $this->db->fetch(
            "SELECT * FROM escrow_disputas WHERE transaccion_id = :id AND estatus IN ('abierta', 'en_revision')",
            ['id' => $id]
        );
        
        $this->view('escrow/ver', [
            'pageTitle' => 'Transacción ' . $transaccion['numero_transaccion'],
            'transaccion' => $transaccion,
            'hitos' => $hitos,
            'movimientos' => $movimientos,
            'documentos' => $documentos,
            'historial' => $historial,
            'disputa' => $disputa
        ]);
    }
    
    /**
     * Editar transacción ESCROW
     */
    public function editar() {
        $this->requireRole(['administrador', 'operativo']);
        
        $id = $this->params['id'] ?? 0;
        
        $transaccion = $this->db->fetch(
            "SELECT * FROM escrow_transacciones WHERE id = :id",
            ['id' => $id]
        );
        
        if (!$transaccion) {
            $this->setFlash('error', 'Transacción no encontrada');
            $this->redirect('escrow');
        }
        
        // Solo se puede editar si está en borrador
        if ($transaccion['estatus'] !== 'borrador') {
            $this->setFlash('error', 'Solo se pueden editar transacciones en estado borrador');
            $this->redirect('escrow/ver/' . $id);
        }
        
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $titulo = $this->sanitize($_POST['titulo'] ?? '');
            $tipo = $_POST['tipo'] ?? 'compraventa';
            $descripcion = $this->sanitize($_POST['descripcion'] ?? '');
            $montoTotal = floatval($_POST['monto_total'] ?? 0);
            $comisionPorcentaje = floatval($_POST['comision_porcentaje'] ?? 2.50);
            $fechaLimite = $_POST['fecha_limite'] ?? null;
            
            // Comprador
            $compradorId = !empty($_POST['comprador_id']) ? intval($_POST['comprador_id']) : null;
            $compradorNombre = $this->sanitize($_POST['comprador_nombre'] ?? '');
            $compradorEmail = filter_var($_POST['comprador_email'] ?? '', FILTER_VALIDATE_EMAIL) ?: null;
            $compradorTelefono = $this->sanitize($_POST['comprador_telefono'] ?? '');
            
            // Vendedor
            $vendedorId = !empty($_POST['vendedor_id']) ? intval($_POST['vendedor_id']) : null;
            $vendedorNombre = $this->sanitize($_POST['vendedor_nombre'] ?? '');
            $vendedorEmail = filter_var($_POST['vendedor_email'] ?? '', FILTER_VALIDATE_EMAIL) ?: null;
            $vendedorTelefono = $this->sanitize($_POST['vendedor_telefono'] ?? '');
            
            if (empty($titulo)) {
                $errors[] = 'El título es requerido';
            }
            
            if ($montoTotal <= 0) {
                $errors[] = 'El monto debe ser mayor a cero';
            }
            
            if (empty($errors)) {
                $comisionMonto = round($montoTotal * ($comisionPorcentaje / 100), 2);
                
                $this->db->update('escrow_transacciones', [
                    'tipo' => $tipo,
                    'titulo' => $titulo,
                    'descripcion' => $descripcion,
                    'comprador_id' => $compradorId,
                    'comprador_nombre' => $compradorNombre,
                    'comprador_email' => $compradorEmail,
                    'comprador_telefono' => $compradorTelefono,
                    'vendedor_id' => $vendedorId,
                    'vendedor_nombre' => $vendedorNombre,
                    'vendedor_email' => $vendedorEmail,
                    'vendedor_telefono' => $vendedorTelefono,
                    'monto_total' => $montoTotal,
                    'comision_porcentaje' => $comisionPorcentaje,
                    'comision_monto' => $comisionMonto,
                    'fecha_limite' => $fechaLimite ?: null
                ], 'id = :id', ['id' => $id]);
                
                $this->db->insert('escrow_historial', [
                    'transaccion_id' => $id,
                    'accion' => 'EDICION',
                    'descripcion' => 'Transacción editada',
                    'usuario_id' => $_SESSION['user_id'],
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? ''
                ]);
                
                $this->logAction('EDITAR_ESCROW', "Se editó transacción ESCROW #{$id}", 'escrow_transacciones', $id);
                $this->setFlash('success', 'Transacción actualizada exitosamente');
                $this->redirect('escrow/ver/' . $id);
            }
        }
        
        $socios = $this->db->fetchAll(
            "SELECT id, numero_socio, nombre, apellido_paterno, apellido_materno, email, celular 
             FROM socios WHERE estatus = 'activo' ORDER BY nombre"
        );
        
        $this->view('escrow/editar', [
            'pageTitle' => 'Editar Transacción ESCROW',
            'transaccion' => $transaccion,
            'socios' => $socios,
            'errors' => $errors
        ]);
    }
    
    /**
     * Registrar depósito
     */
    public function deposito() {
        $this->requireRole(['administrador', 'operativo']);
        
        $id = $this->params['id'] ?? 0;
        
        $transaccion = $this->db->fetch(
            "SELECT * FROM escrow_transacciones WHERE id = :id",
            ['id' => $id]
        );
        
        if (!$transaccion) {
            $this->setFlash('error', 'Transacción no encontrada');
            $this->redirect('escrow');
        }
        
        if (!in_array($transaccion['estatus'], ['borrador', 'pendiente_deposito'])) {
            $this->setFlash('error', 'Esta transacción no permite registrar depósitos');
            $this->redirect('escrow/ver/' . $id);
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $monto = floatval($_POST['monto'] ?? 0);
            $metodoPago = $this->sanitize($_POST['metodo_pago'] ?? '');
            $referencia = $this->sanitize($_POST['referencia'] ?? '');
            
            if ($monto <= 0) {
                $this->setFlash('error', 'El monto debe ser mayor a cero');
                $this->redirect('escrow/ver/' . $id);
            }
            
            $this->db->beginTransaction();
            
            try {
                // Registrar movimiento
                $this->db->insert('escrow_movimientos', [
                    'transaccion_id' => $id,
                    'tipo' => 'deposito',
                    'monto' => $monto,
                    'concepto' => 'Depósito del comprador',
                    'metodo_pago' => $metodoPago,
                    'referencia_pago' => $referencia,
                    'usuario_id' => $_SESSION['user_id']
                ]);
                
                // Actualizar estado
                $nuevoEstatus = $monto >= $transaccion['monto_total'] ? 'fondos_depositados' : 'pendiente_deposito';
                
                $this->db->update('escrow_transacciones', [
                    'estatus' => $nuevoEstatus,
                    'fecha_deposito' => date('Y-m-d H:i:s')
                ], 'id = :id', ['id' => $id]);
                
                $this->db->insert('escrow_historial', [
                    'transaccion_id' => $id,
                    'accion' => 'DEPOSITO',
                    'descripcion' => "Depósito de $" . number_format($monto, 2) . " registrado",
                    'estatus_anterior' => $transaccion['estatus'],
                    'estatus_nuevo' => $nuevoEstatus,
                    'usuario_id' => $_SESSION['user_id'],
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? ''
                ]);
                
                $this->db->commit();
                
                $this->logAction('DEPOSITO_ESCROW', "Depósito registrado en transacción ESCROW #{$id}", 'escrow_transacciones', $id);
                $this->setFlash('success', 'Depósito registrado exitosamente');
                
            } catch (Exception $e) {
                $this->db->rollBack();
                $this->setFlash('error', 'Error al registrar el depósito: ' . $e->getMessage());
            }
        }
        
        $this->redirect('escrow/ver/' . $id);
    }
    
    /**
     * Liberar fondos
     */
    public function liberar() {
        $this->requireRole(['administrador']);
        
        $id = $this->params['id'] ?? 0;
        
        $transaccion = $this->db->fetch(
            "SELECT * FROM escrow_transacciones WHERE id = :id",
            ['id' => $id]
        );
        
        if (!$transaccion) {
            $this->setFlash('error', 'Transacción no encontrada');
            $this->redirect('escrow');
        }
        
        if (!in_array($transaccion['estatus'], ['fondos_depositados', 'en_proceso', 'entrega_confirmada'])) {
            $this->setFlash('error', 'Esta transacción no permite liberar fondos');
            $this->redirect('escrow/ver/' . $id);
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $monto = floatval($_POST['monto'] ?? 0);
            $concepto = $this->sanitize($_POST['concepto'] ?? 'Liberación de fondos');
            
            $saldoRetenido = $transaccion['monto_total'] - $transaccion['monto_liberado'];
            
            if ($monto <= 0 || $monto > $saldoRetenido) {
                $this->setFlash('error', 'Monto inválido. Saldo disponible: $' . number_format($saldoRetenido, 2));
                $this->redirect('escrow/ver/' . $id);
            }
            
            $this->db->beginTransaction();
            
            try {
                // Registrar movimiento de liberación
                $this->db->insert('escrow_movimientos', [
                    'transaccion_id' => $id,
                    'tipo' => 'liberacion',
                    'monto' => $monto,
                    'concepto' => $concepto,
                    'usuario_id' => $_SESSION['user_id']
                ]);
                
                $nuevoMontoLiberado = $transaccion['monto_liberado'] + $monto;
                $nuevoEstatus = $nuevoMontoLiberado >= $transaccion['monto_total'] ? 'liberado' : $transaccion['estatus'];
                
                // Registrar comisión si es liberación total
                if ($nuevoEstatus === 'liberado' && $transaccion['comision_monto'] > 0) {
                    $this->db->insert('escrow_movimientos', [
                        'transaccion_id' => $id,
                        'tipo' => 'comision',
                        'monto' => $transaccion['comision_monto'],
                        'concepto' => 'Comisión ESCROW',
                        'usuario_id' => $_SESSION['user_id']
                    ]);
                }
                
                $this->db->update('escrow_transacciones', [
                    'monto_liberado' => $nuevoMontoLiberado,
                    'estatus' => $nuevoEstatus,
                    'fecha_liberacion' => $nuevoEstatus === 'liberado' ? date('Y-m-d H:i:s') : null
                ], 'id = :id', ['id' => $id]);
                
                $this->db->insert('escrow_historial', [
                    'transaccion_id' => $id,
                    'accion' => 'LIBERACION',
                    'descripcion' => "Fondos liberados: $" . number_format($monto, 2),
                    'estatus_anterior' => $transaccion['estatus'],
                    'estatus_nuevo' => $nuevoEstatus,
                    'usuario_id' => $_SESSION['user_id'],
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? ''
                ]);
                
                $this->db->commit();
                
                $this->logAction('LIBERAR_ESCROW', "Fondos liberados en transacción ESCROW #{$id}", 'escrow_transacciones', $id);
                $this->setFlash('success', 'Fondos liberados exitosamente');
                
            } catch (Exception $e) {
                $this->db->rollBack();
                $this->setFlash('error', 'Error al liberar fondos: ' . $e->getMessage());
            }
        }
        
        $this->redirect('escrow/ver/' . $id);
    }
    
    /**
     * Gestionar disputas
     */
    public function disputa() {
        $this->requireRole(['administrador', 'operativo']);
        
        $id = $this->params['id'] ?? 0;
        
        $transaccion = $this->db->fetch(
            "SELECT * FROM escrow_transacciones WHERE id = :id",
            ['id' => $id]
        );
        
        if (!$transaccion) {
            $this->setFlash('error', 'Transacción no encontrada');
            $this->redirect('escrow');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $accion = $_POST['accion'] ?? '';
            
            if ($accion === 'abrir') {
                $iniciadoPor = $_POST['iniciado_por'] ?? 'comprador';
                $motivo = $this->sanitize($_POST['motivo'] ?? '');
                $descripcion = $this->sanitize($_POST['descripcion_disputa'] ?? '');
                
                $this->db->insert('escrow_disputas', [
                    'transaccion_id' => $id,
                    'iniciado_por' => $iniciadoPor,
                    'motivo' => $motivo,
                    'descripcion' => $descripcion,
                    'estatus' => 'abierta'
                ]);
                
                $this->db->update('escrow_transacciones', [
                    'estatus' => 'disputa'
                ], 'id = :id', ['id' => $id]);
                
                $this->db->insert('escrow_historial', [
                    'transaccion_id' => $id,
                    'accion' => 'DISPUTA_ABIERTA',
                    'descripcion' => "Disputa abierta por {$iniciadoPor}: {$motivo}",
                    'estatus_anterior' => $transaccion['estatus'],
                    'estatus_nuevo' => 'disputa',
                    'usuario_id' => $_SESSION['user_id'],
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? ''
                ]);
                
                $this->setFlash('success', 'Disputa registrada exitosamente');
                
            } elseif ($accion === 'resolver') {
                $disputaId = intval($_POST['disputa_id'] ?? 0);
                $resolucion = $this->sanitize($_POST['resolucion'] ?? '');
                $estatusResolucion = $_POST['estatus_resolucion'] ?? 'resuelta_parcial';
                
                $this->db->update('escrow_disputas', [
                    'resolucion' => $resolucion,
                    'estatus' => $estatusResolucion,
                    'resuelto_por' => $_SESSION['user_id'],
                    'fecha_resolucion' => date('Y-m-d H:i:s')
                ], 'id = :id', ['id' => $disputaId]);
                
                $nuevoEstatus = 'en_proceso';
                $this->db->update('escrow_transacciones', [
                    'estatus' => $nuevoEstatus
                ], 'id = :id', ['id' => $id]);
                
                $this->db->insert('escrow_historial', [
                    'transaccion_id' => $id,
                    'accion' => 'DISPUTA_RESUELTA',
                    'descripcion' => "Disputa resuelta: {$estatusResolucion}",
                    'estatus_anterior' => 'disputa',
                    'estatus_nuevo' => $nuevoEstatus,
                    'usuario_id' => $_SESSION['user_id'],
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? ''
                ]);
                
                $this->setFlash('success', 'Disputa resuelta exitosamente');
            }
            
            $this->logAction('DISPUTA_ESCROW', "Acción de disputa en transacción ESCROW #{$id}", 'escrow_transacciones', $id);
        }
        
        $this->redirect('escrow/ver/' . $id);
    }
    
    /**
     * Cancelar transacción
     */
    public function cancelar() {
        $this->requireRole(['administrador']);
        
        $id = $this->params['id'] ?? 0;
        
        $transaccion = $this->db->fetch(
            "SELECT * FROM escrow_transacciones WHERE id = :id",
            ['id' => $id]
        );
        
        if (!$transaccion) {
            $this->setFlash('error', 'Transacción no encontrada');
            $this->redirect('escrow');
        }
        
        if (in_array($transaccion['estatus'], ['liberado', 'cancelado', 'reembolsado'])) {
            $this->setFlash('error', 'Esta transacción no puede ser cancelada');
            $this->redirect('escrow/ver/' . $id);
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $motivo = $this->sanitize($_POST['motivo_cancelacion'] ?? '');
            $reembolsar = isset($_POST['reembolsar']);
            
            $nuevoEstatus = $reembolsar ? 'reembolsado' : 'cancelado';
            
            $this->db->beginTransaction();
            
            try {
                if ($reembolsar && $transaccion['monto_liberado'] < $transaccion['monto_total']) {
                    $montoReembolso = $transaccion['monto_total'] - $transaccion['monto_liberado'];
                    $this->db->insert('escrow_movimientos', [
                        'transaccion_id' => $id,
                        'tipo' => 'reembolso',
                        'monto' => $montoReembolso,
                        'concepto' => 'Reembolso por cancelación: ' . $motivo,
                        'usuario_id' => $_SESSION['user_id']
                    ]);
                }
                
                $this->db->update('escrow_transacciones', [
                    'estatus' => $nuevoEstatus,
                    'fecha_cancelacion' => date('Y-m-d H:i:s'),
                    'notas_internas' => $transaccion['notas_internas'] . "\n\nCancelación: " . $motivo
                ], 'id = :id', ['id' => $id]);
                
                $this->db->insert('escrow_historial', [
                    'transaccion_id' => $id,
                    'accion' => $reembolsar ? 'REEMBOLSO' : 'CANCELACION',
                    'descripcion' => "Transacción " . ($reembolsar ? 'reembolsada' : 'cancelada') . ": " . $motivo,
                    'estatus_anterior' => $transaccion['estatus'],
                    'estatus_nuevo' => $nuevoEstatus,
                    'usuario_id' => $_SESSION['user_id'],
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? ''
                ]);
                
                $this->db->commit();
                
                $this->logAction('CANCELAR_ESCROW', "Transacción ESCROW #{$id} cancelada", 'escrow_transacciones', $id);
                $this->setFlash('success', 'Transacción ' . ($reembolsar ? 'reembolsada' : 'cancelada') . ' exitosamente');
                
            } catch (Exception $e) {
                $this->db->rollBack();
                $this->setFlash('error', 'Error al cancelar: ' . $e->getMessage());
            }
        }
        
        $this->redirect('escrow/ver/' . $id);
    }
    
    /**
     * Gestión de hitos
     */
    public function hitos() {
        $this->requireRole(['administrador', 'operativo']);
        
        $id = $this->params['id'] ?? 0;
        
        $transaccion = $this->db->fetch(
            "SELECT * FROM escrow_transacciones WHERE id = :id",
            ['id' => $id]
        );
        
        if (!$transaccion) {
            $this->setFlash('error', 'Transacción no encontrada');
            $this->redirect('escrow');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $accion = $_POST['accion'] ?? '';
            
            if ($accion === 'agregar') {
                $descripcion = $this->sanitize($_POST['descripcion_hito'] ?? '');
                $monto = floatval($_POST['monto_hito'] ?? 0);
                $fechaLimite = $_POST['fecha_limite_hito'] ?? null;
                
                // Obtener siguiente número de hito
                $ultimoHito = $this->db->fetch(
                    "SELECT MAX(numero_hito) as ultimo FROM escrow_hitos WHERE transaccion_id = :id",
                    ['id' => $id]
                );
                $numeroHito = ($ultimoHito['ultimo'] ?? 0) + 1;
                
                $this->db->insert('escrow_hitos', [
                    'transaccion_id' => $id,
                    'numero_hito' => $numeroHito,
                    'descripcion' => $descripcion,
                    'monto' => $monto,
                    'fecha_limite' => $fechaLimite ?: null,
                    'estatus' => 'pendiente'
                ]);
                
                $this->setFlash('success', 'Hito agregado exitosamente');
                
            } elseif ($accion === 'completar') {
                $hitoId = intval($_POST['hito_id'] ?? 0);
                $evidencia = $this->sanitize($_POST['evidencia'] ?? '');
                
                $hito = $this->db->fetch(
                    "SELECT * FROM escrow_hitos WHERE id = :id AND transaccion_id = :tid",
                    ['id' => $hitoId, 'tid' => $id]
                );
                
                if ($hito) {
                    $this->db->update('escrow_hitos', [
                        'estatus' => 'completado',
                        'fecha_completado' => date('Y-m-d H:i:s'),
                        'evidencia' => $evidencia,
                        'confirmado_por' => $_SESSION['user_id']
                    ], 'id = :id', ['id' => $hitoId]);
                    
                    $this->db->insert('escrow_historial', [
                        'transaccion_id' => $id,
                        'accion' => 'HITO_COMPLETADO',
                        'descripcion' => "Hito #{$hito['numero_hito']} completado: {$hito['descripcion']}",
                        'usuario_id' => $_SESSION['user_id'],
                        'ip' => $_SERVER['REMOTE_ADDR'] ?? ''
                    ]);
                    
                    $this->setFlash('success', 'Hito marcado como completado');
                }
            }
            
            $this->logAction('HITOS_ESCROW', "Gestión de hitos en transacción ESCROW #{$id}", 'escrow_transacciones', $id);
        }
        
        $this->redirect('escrow/ver/' . $id);
    }
    
    /**
     * Gestión de documentos
     */
    public function documentos() {
        $this->requireRole(['administrador', 'operativo']);
        
        $id = $this->params['id'] ?? 0;
        
        $transaccion = $this->db->fetch(
            "SELECT * FROM escrow_transacciones WHERE id = :id",
            ['id' => $id]
        );
        
        if (!$transaccion) {
            $this->setFlash('error', 'Transacción no encontrada');
            $this->redirect('escrow');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['documento'])) {
            $this->validateCsrf();
            
            $file = $_FILES['documento'];
            $tipo = $this->sanitize($_POST['tipo_documento'] ?? 'otro');
            $descripcion = $this->sanitize($_POST['descripcion_documento'] ?? '');
            $hitoId = !empty($_POST['hito_id']) ? intval($_POST['hito_id']) : null;
            
            if ($file['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                $maxSize = 10 * 1024 * 1024; // 10MB
                
                $fileType = mime_content_type($file['tmp_name']);
                
                if (!in_array($fileType, $allowedTypes)) {
                    $this->setFlash('error', 'Tipo de archivo no permitido');
                } elseif ($file['size'] > $maxSize) {
                    $this->setFlash('error', 'El archivo es demasiado grande (máximo 10MB)');
                } else {
                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $nombreArchivo = 'escrow_' . $id . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                    
                    $uploadDir = UPLOAD_PATH . '/escrow';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    $ruta = $uploadDir . '/' . $nombreArchivo;
                    
                    if (move_uploaded_file($file['tmp_name'], $ruta)) {
                        $this->db->insert('escrow_documentos', [
                            'transaccion_id' => $id,
                            'hito_id' => $hitoId,
                            'tipo' => $tipo,
                            'nombre_archivo' => $file['name'],
                            'ruta_archivo' => 'escrow/' . $nombreArchivo,
                            'descripcion' => $descripcion,
                            'subido_por' => $_SESSION['user_id']
                        ]);
                        
                        $this->setFlash('success', 'Documento subido exitosamente');
                        $this->logAction('DOCUMENTO_ESCROW', "Documento subido a transacción ESCROW #{$id}", 'escrow_transacciones', $id);
                    } else {
                        $this->setFlash('error', 'Error al guardar el archivo');
                    }
                }
            } else {
                $this->setFlash('error', 'Error al subir el archivo');
            }
        }
        
        $this->redirect('escrow/ver/' . $id);
    }
}
