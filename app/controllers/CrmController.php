<?php
/**
 * Controlador de Informe CRM
 * Sistema de Gestión Integral de Caja de Ahorros
 */

require_once CORE_PATH . '/Controller.php';

class CrmController extends Controller {
    
    public function index() {
        $this->requireAuth();
        
        // Estadísticas principales
        $stats = $this->getStats();
        
        // Distribución por segmento
        $segmentacion = $this->db->fetchAll(
            "SELECT sc.nombre, sc.color, COUNT(ss.socio_id) as cantidad
             FROM segmentos_clientes sc
             LEFT JOIN socios_segmentos ss ON sc.id = ss.segmento_id
             WHERE sc.activo = 1
             GROUP BY sc.id, sc.nombre, sc.color
             ORDER BY cantidad DESC"
        );
        
        // Agregar clientes sin segmento
        $sinSegmento = $this->db->fetch(
            "SELECT COUNT(*) as total FROM socios s 
             WHERE s.estatus = 'activo' 
             AND s.id NOT IN (SELECT DISTINCT socio_id FROM socios_segmentos)"
        )['total'];
        
        if ($sinSegmento > 0) {
            array_unshift($segmentacion, [
                'nombre' => 'Sin compras',
                'color' => '#6b7280',
                'cantidad' => $sinSegmento
            ]);
        }
        
        // Rendimiento por segmento
        $rendimientoPorSegmento = $this->db->fetchAll(
            "SELECT sc.nombre, 
                    COALESCE(SUM(mc.ltv), 0) as ingresos_totales,
                    COALESCE(AVG(mc.ltv), 0) as promedio
             FROM segmentos_clientes sc
             LEFT JOIN socios_segmentos ss ON sc.id = ss.segmento_id
             LEFT JOIN metricas_crm mc ON ss.socio_id = mc.socio_id
             WHERE sc.activo = 1
             GROUP BY sc.id, sc.nombre
             ORDER BY ingresos_totales DESC"
        );
        
        // Clientes en riesgo
        $clientesRiesgo = $this->db->fetchAll(
            "SELECT s.id, s.numero_socio, 
                    CONCAT(s.nombre, ' ', s.apellido_paterno) as nombre,
                    s.email, s.celular,
                    mc.ltv, mc.dias_sin_actividad, mc.ultima_transaccion
             FROM socios s
             JOIN metricas_crm mc ON s.id = mc.socio_id
             WHERE mc.nivel_riesgo = 'alto' AND s.estatus = 'activo'
             ORDER BY mc.dias_sin_actividad DESC
             LIMIT 10"
        );
        
        // Clientes VIP
        $clientesVip = $this->db->fetchAll(
            "SELECT s.id, s.numero_socio,
                    CONCAT(s.nombre, ' ', s.apellido_paterno) as nombre,
                    mc.ltv, mc.frecuencia_transacciones
             FROM socios s
             JOIN metricas_crm mc ON s.id = mc.socio_id
             WHERE mc.es_vip = 1 AND s.estatus = 'activo'
             ORDER BY mc.ltv DESC
             LIMIT 10"
        );
        
        $this->view('crm/index', [
            'pageTitle' => 'Informe CRM',
            'stats' => $stats,
            'segmentacion' => $segmentacion,
            'rendimientoPorSegmento' => $rendimientoPorSegmento,
            'clientesRiesgo' => $clientesRiesgo,
            'clientesVip' => $clientesVip,
            'actividadReciente' => $this->getActividadReciente(),
            'analisisVentas' => $this->getAnalisisVentas(),
            'embudoConversion' => $this->getEmbudoConversion(),
            'analisisRetencion' => $this->getAnalisisRetencion()
        ]);
    }
    
    /**
     * Obtener actividad reciente de clientes
     */
    private function getActividadReciente() {
        // Actividad por tipo de transacción en los últimos 30 días
        $actividadPorTipo = $this->db->fetchAll(
            "SELECT 
                'Aportaciones' as tipo, COUNT(*) as cantidad, COALESCE(SUM(monto), 0) as total
             FROM movimientos_ahorro ma
             JOIN cuentas_ahorro ca ON ma.cuenta_id = ca.id
             WHERE ma.tipo = 'aportacion' AND ma.fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             UNION ALL
             SELECT 
                'Retiros' as tipo, COUNT(*) as cantidad, COALESCE(SUM(monto), 0) as total
             FROM movimientos_ahorro ma
             JOIN cuentas_ahorro ca ON ma.cuenta_id = ca.id
             WHERE ma.tipo = 'retiro' AND ma.fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             UNION ALL
             SELECT 
                'Pagos de Crédito' as tipo, COUNT(*) as cantidad, COALESCE(SUM(monto), 0) as total
             FROM pagos_credito
             WHERE fecha_pago >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)"
        );
        
        // Evolución diaria de la última semana
        $evolucionDiaria = $this->db->fetchAll(
            "SELECT 
                DATE(fecha) as dia,
                COUNT(*) as transacciones,
                SUM(monto) as total
             FROM (
                SELECT fecha, monto FROM movimientos_ahorro ma
                JOIN cuentas_ahorro ca ON ma.cuenta_id = ca.id
                WHERE ma.fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                UNION ALL
                SELECT fecha_pago as fecha, monto FROM pagos_credito
                WHERE fecha_pago >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
             ) t
             GROUP BY DATE(fecha)
             ORDER BY dia"
        );
        
        // Clientes nuevos vs recurrentes este mes
        $clientesNuevos = $this->db->fetch(
            "SELECT COUNT(*) as total FROM socios WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())"
        )['total'];
        
        return [
            'por_tipo' => $actividadPorTipo,
            'evolucion_diaria' => $evolucionDiaria,
            'clientes_nuevos_mes' => $clientesNuevos
        ];
    }
    
    /**
     * Obtener análisis de ventas
     */
    private function getAnalisisVentas() {
        // Top tipos de crédito por monto autorizado
        $topTiposCredito = $this->db->fetchAll(
            "SELECT tc.nombre, COUNT(c.id) as cantidad, COALESCE(SUM(c.monto_autorizado), 0) as total
             FROM tipos_credito tc
             LEFT JOIN creditos c ON tc.id = c.tipo_credito_id AND c.estatus IN ('activo', 'formalizado', 'liquidado')
             GROUP BY tc.id, tc.nombre
             ORDER BY total DESC
             LIMIT 5"
        );
        
        // Créditos por mes (últimos 6 meses)
        $creditosPorMes = $this->db->fetchAll(
            "SELECT 
                DATE_FORMAT(fecha_formalizacion, '%Y-%m') as mes,
                COUNT(*) as cantidad,
                SUM(monto_autorizado) as total
             FROM creditos
             WHERE fecha_formalizacion >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                   AND estatus IN ('activo', 'formalizado', 'liquidado')
             GROUP BY DATE_FORMAT(fecha_formalizacion, '%Y-%m')
             ORDER BY mes"
        );
        
        // Ticket promedio de crédito
        $ticketPromedio = $this->db->fetch(
            "SELECT COALESCE(AVG(monto_autorizado), 0) as promedio FROM creditos WHERE estatus IN ('activo', 'formalizado', 'liquidado')"
        )['promedio'];
        
        return [
            'top_tipos_credito' => $topTiposCredito,
            'creditos_por_mes' => $creditosPorMes,
            'ticket_promedio' => $ticketPromedio
        ];
    }
    
    /**
     * Obtener análisis del embudo de conversión
     */
    private function getEmbudoConversion() {
        // Embudo de créditos
        $embudo = [
            'solicitudes' => $this->db->fetch(
                "SELECT COUNT(*) as total FROM creditos WHERE estatus IN ('solicitud', 'en_revision')"
            )['total'],
            'autorizados' => $this->db->fetch(
                "SELECT COUNT(*) as total FROM creditos WHERE estatus = 'autorizado'"
            )['total'],
            'formalizados' => $this->db->fetch(
                "SELECT COUNT(*) as total FROM creditos WHERE estatus = 'formalizado'"
            )['total'],
            'activos' => $this->db->fetch(
                "SELECT COUNT(*) as total FROM creditos WHERE estatus = 'activo'"
            )['total'],
            'liquidados' => $this->db->fetch(
                "SELECT COUNT(*) as total FROM creditos WHERE estatus = 'liquidado'"
            )['total'],
            'rechazados' => $this->db->fetch(
                "SELECT COUNT(*) as total FROM creditos WHERE estatus = 'rechazado'"
            )['total']
        ];
        
        // Tasa de conversión
        $totalSolicitudes = $this->db->fetch(
            "SELECT COUNT(*) as total FROM creditos"
        )['total'];
        
        $tasaConversion = $totalSolicitudes > 0 
            ? round(($embudo['activos'] + $embudo['liquidados'] + $embudo['formalizados']) / $totalSolicitudes * 100, 1)
            : 0;
        
        return [
            'embudo' => $embudo,
            'tasa_conversion' => $tasaConversion
        ];
    }
    
    /**
     * Obtener análisis de retención
     */
    private function getAnalisisRetencion() {
        // Distribución por nivel de riesgo
        $distribucionRiesgo = $this->db->fetchAll(
            "SELECT nivel_riesgo, COUNT(*) as cantidad
             FROM metricas_crm
             GROUP BY nivel_riesgo
             ORDER BY FIELD(nivel_riesgo, 'bajo', 'medio', 'alto')"
        );
        
        // Socios activos por antigüedad
        $porAntiguedad = $this->db->fetchAll(
            "SELECT 
                CASE 
                    WHEN DATEDIFF(CURDATE(), fecha_alta) <= 365 THEN '0-1 año'
                    WHEN DATEDIFF(CURDATE(), fecha_alta) <= 730 THEN '1-2 años'
                    WHEN DATEDIFF(CURDATE(), fecha_alta) <= 1095 THEN '2-3 años'
                    ELSE '3+ años'
                END as antiguedad,
                COUNT(*) as cantidad
             FROM socios
             WHERE estatus = 'activo' AND fecha_alta IS NOT NULL
             GROUP BY antiguedad
             ORDER BY FIELD(antiguedad, '0-1 año', '1-2 años', '2-3 años', '3+ años')"
        );
        
        // Tasa de retención (socios activos / total socios)
        $totalSocios = $this->db->fetch("SELECT COUNT(*) as total FROM socios")['total'];
        $sociosActivos = $this->db->fetch("SELECT COUNT(*) as total FROM socios WHERE estatus = 'activo'")['total'];
        $tasaRetencion = $totalSocios > 0 ? round($sociosActivos / $totalSocios * 100, 1) : 0;
        
        // Socios que dieron de baja este mes
        $bajasEsteMes = $this->db->fetch(
            "SELECT COUNT(*) as total FROM socios 
             WHERE estatus = 'baja' AND MONTH(fecha_baja) = MONTH(CURDATE()) AND YEAR(fecha_baja) = YEAR(CURDATE())"
        )['total'];
        
        return [
            'distribucion_riesgo' => $distribucionRiesgo,
            'por_antiguedad' => $porAntiguedad,
            'tasa_retencion' => $tasaRetencion,
            'bajas_mes' => $bajasEsteMes
        ];
    }
    
    public function segmentos() {
        $this->requireRole(['administrador']);
        
        $errors = [];
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $action = $_POST['action'] ?? '';
            
            if ($action === 'crear') {
                $nombre = $this->sanitize($_POST['nombre'] ?? '');
                $descripcion = $this->sanitize($_POST['descripcion'] ?? '');
                $color = $this->sanitize($_POST['color'] ?? '#3b82f6');
                
                if (empty($nombre)) {
                    $errors[] = 'El nombre es requerido';
                } else {
                    $this->db->insert('segmentos_clientes', [
                        'nombre' => $nombre,
                        'descripcion' => $descripcion,
                        'color' => $color,
                        'activo' => 1
                    ]);
                    
                    $this->logAction('CREAR_SEGMENTO', "Se creó segmento: {$nombre}", 'segmentos_clientes', $this->db->lastInsertId());
                    $success = 'Segmento creado exitosamente';
                }
            } elseif ($action === 'actualizar') {
                $id = (int)($_POST['id'] ?? 0);
                $nombre = $this->sanitize($_POST['nombre'] ?? '');
                $descripcion = $this->sanitize($_POST['descripcion'] ?? '');
                $color = $this->sanitize($_POST['color'] ?? '#3b82f6');
                
                if (!$id) {
                    $errors[] = 'ID de segmento inválido';
                } elseif (empty($nombre)) {
                    $errors[] = 'El nombre es requerido';
                } else {
                    $this->db->update('segmentos_clientes', [
                        'nombre' => $nombre,
                        'descripcion' => $descripcion,
                        'color' => $color
                    ], 'id = :id', ['id' => $id]);
                    
                    $this->logAction('EDITAR_SEGMENTO', "Se editó segmento ID: {$id}", 'segmentos_clientes', $id);
                    $success = 'Segmento actualizado exitosamente';
                }
            } elseif ($action === 'toggle') {
                $id = (int)($_POST['id'] ?? 0);
                $segmento = $this->db->fetch("SELECT activo FROM segmentos_clientes WHERE id = :id", ['id' => $id]);
                if ($segmento) {
                    $nuevoEstado = $segmento['activo'] ? 0 : 1;
                    $this->db->update('segmentos_clientes', ['activo' => $nuevoEstado], 'id = :id', ['id' => $id]);
                    $this->logAction('TOGGLE_SEGMENTO', "Se cambió estado de segmento ID: {$id}", 'segmentos_clientes', $id);
                    $success = $nuevoEstado ? 'Segmento activado' : 'Segmento desactivado';
                }
            } elseif ($action === 'eliminar') {
                $id = (int)($_POST['id'] ?? 0);
                // Verificar que no tenga clientes asignados
                $clientesAsignados = $this->db->fetch(
                    "SELECT COUNT(*) as total FROM socios_segmentos WHERE segmento_id = :id",
                    ['id' => $id]
                )['total'];
                
                if ($clientesAsignados > 0) {
                    $errors[] = "No se puede eliminar el segmento porque tiene {$clientesAsignados} cliente(s) asignado(s)";
                } else {
                    $this->db->delete('segmentos_clientes', 'id = :id', ['id' => $id]);
                    $this->logAction('ELIMINAR_SEGMENTO', "Se eliminó segmento ID: {$id}", 'segmentos_clientes', $id);
                    $success = 'Segmento eliminado exitosamente';
                }
            } elseif ($action === 'actualizar_clientes') {
                // Actualizar métricas CRM para todos los socios activos
                $socios = $this->db->fetchAll("SELECT id FROM socios WHERE estatus = 'activo'");
                $actualizados = 0;
                foreach ($socios as $socio) {
                    try {
                        $this->db->execute("CALL sp_actualizar_metricas_crm(:id)", ['id' => $socio['id']]);
                        $actualizados++;
                    } catch (Exception $e) {
                        // Log error but continue
                        error_log("Error actualizando métricas para socio {$socio['id']}: " . $e->getMessage());
                    }
                }
                $success = "Métricas CRM actualizadas para {$actualizados} de " . count($socios) . ' clientes';
            }
        }
        
        $segmentos = $this->db->fetchAll(
            "SELECT sc.*, 
                    (SELECT COUNT(*) FROM socios_segmentos ss WHERE ss.segmento_id = sc.id) as num_clientes
             FROM segmentos_clientes sc
             ORDER BY sc.nombre"
        );
        
        $this->view('crm/segmentos', [
            'pageTitle' => 'Segmentos de Clientes',
            'segmentos' => $segmentos,
            'errors' => $errors,
            'success' => $success
        ]);
    }
    
    public function metricas() {
        $this->requireAuth();
        
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 20;
        $riesgo = $_GET['riesgo'] ?? '';
        $vip = isset($_GET['vip']) ? $_GET['vip'] : '';
        
        $conditions = 's.estatus = "activo"';
        $params = [];
        
        if ($riesgo) {
            $conditions .= ' AND mc.nivel_riesgo = :riesgo';
            $params['riesgo'] = $riesgo;
        }
        if ($vip === '1') {
            $conditions .= ' AND mc.es_vip = 1';
        }
        
        $total = $this->db->fetch(
            "SELECT COUNT(*) as total FROM socios s
             LEFT JOIN metricas_crm mc ON s.id = mc.socio_id
             WHERE {$conditions}",
            $params
        )['total'];
        
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        
        $clientes = $this->db->fetchAll(
            "SELECT s.id, s.numero_socio, 
                    CONCAT(s.nombre, ' ', s.apellido_paterno) as nombre,
                    s.email, s.celular,
                    COALESCE(mc.ltv, 0) as ltv,
                    COALESCE(mc.frecuencia_transacciones, 0) as frecuencia,
                    mc.ultima_transaccion,
                    COALESCE(mc.dias_sin_actividad, 0) as dias_inactivo,
                    COALESCE(mc.nivel_riesgo, 'bajo') as nivel_riesgo,
                    COALESCE(mc.es_vip, 0) as es_vip
             FROM socios s
             LEFT JOIN metricas_crm mc ON s.id = mc.socio_id
             WHERE {$conditions}
             ORDER BY mc.ltv DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        
        $this->view('crm/metricas', [
            'pageTitle' => 'Métricas de Clientes',
            'clientes' => $clientes,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'riesgo' => $riesgo,
            'vip' => $vip
        ]);
    }
    
    public function interacciones() {
        $this->requireAuth();
        
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 20;
        $tipo = $_GET['tipo'] ?? '';
        
        $conditions = '1=1';
        $params = [];
        
        if ($tipo) {
            $conditions .= ' AND i.tipo = :tipo';
            $params['tipo'] = $tipo;
        }
        
        $total = $this->db->fetch(
            "SELECT COUNT(*) as total FROM interacciones_clientes i WHERE {$conditions}",
            $params
        )['total'];
        
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        
        $interacciones = $this->db->fetchAll(
            "SELECT i.*, 
                    CONCAT(s.nombre, ' ', s.apellido_paterno) as cliente_nombre,
                    s.numero_socio,
                    u.nombre as usuario_nombre
             FROM interacciones_clientes i
             JOIN socios s ON i.socio_id = s.id
             LEFT JOIN usuarios u ON i.usuario_id = u.id
             WHERE {$conditions}
             ORDER BY i.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        
        $this->view('crm/interacciones', [
            'pageTitle' => 'Interacciones con Clientes',
            'interacciones' => $interacciones,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'tipoFilter' => $tipo
        ]);
    }
    
    public function interaccion() {
        $this->requireRole(['administrador', 'operativo']);
        
        $socioId = isset($this->params['id']) ? (int)$this->params['id'] : 0;
        
        if (!$socioId) {
            $socioId = (int)($_GET['socio_id'] ?? 0);
        }
        
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $socioIdPost = (int)($_POST['socio_id'] ?? 0);
            $tipo = $this->sanitize($_POST['tipo'] ?? '');
            $asunto = $this->sanitize($_POST['asunto'] ?? '');
            $descripcion = $this->sanitize($_POST['descripcion'] ?? '');
            $resultado = $this->sanitize($_POST['resultado'] ?? '');
            $seguimientoRequerido = isset($_POST['seguimiento_requerido']) ? 1 : 0;
            $fechaSeguimiento = $this->sanitize($_POST['fecha_seguimiento'] ?? '');
            
            if (!$socioIdPost) {
                $errors[] = 'Debe seleccionar un cliente';
            }
            if (!$tipo) {
                $errors[] = 'El tipo de interacción es requerido';
            }
            if (empty($descripcion)) {
                $errors[] = 'La descripción es requerida';
            }
            
            if (empty($errors)) {
                $this->db->insert('interacciones_clientes', [
                    'socio_id' => $socioIdPost,
                    'tipo' => $tipo,
                    'asunto' => $asunto,
                    'descripcion' => $descripcion,
                    'resultado' => $resultado,
                    'seguimiento_requerido' => $seguimientoRequerido,
                    'fecha_seguimiento' => $seguimientoRequerido && $fechaSeguimiento ? $fechaSeguimiento : null,
                    'usuario_id' => $_SESSION['user_id']
                ]);
                
                $this->logAction('CREAR_INTERACCION', "Interacción registrada para socio ID: {$socioIdPost}", 'interacciones_clientes', $this->db->lastInsertId());
                $this->setFlash('success', 'Interacción registrada exitosamente');
                $this->redirect('crm/interacciones');
            }
        }
        
        $socio = null;
        if ($socioId) {
            $socio = $this->db->fetch(
                "SELECT id, numero_socio, CONCAT(nombre, ' ', apellido_paterno) as nombre 
                 FROM socios WHERE id = :id",
                ['id' => $socioId]
            );
        }
        
        $socios = $this->db->fetchAll(
            "SELECT id, numero_socio, CONCAT(nombre, ' ', apellido_paterno) as nombre 
             FROM socios WHERE estatus = 'activo' ORDER BY nombre"
        );
        
        $this->view('crm/interaccion', [
            'pageTitle' => 'Registrar Interacción',
            'socio' => $socio,
            'socios' => $socios,
            'errors' => $errors
        ]);
    }
    
    private function getStats() {
        return [
            'total_clientes' => $this->db->fetch(
                "SELECT COUNT(*) as total FROM socios WHERE estatus = 'activo'"
            )['total'],
            'clientes_activos' => $this->db->fetch(
                "SELECT COUNT(*) as total FROM metricas_crm WHERE dias_sin_actividad <= 30"
            )['total'] ?? 0,
            'ltv_promedio' => $this->db->fetch(
                "SELECT COALESCE(AVG(ltv), 0) as promedio FROM metricas_crm"
            )['promedio'] ?? 0,
            'en_riesgo' => $this->db->fetch(
                "SELECT COUNT(*) as total FROM metricas_crm WHERE nivel_riesgo = 'alto'"
            )['total'] ?? 0,
            'vip' => $this->db->fetch(
                "SELECT COUNT(*) as total FROM metricas_crm WHERE es_vip = 1"
            )['total'] ?? 0
        ];
    }
    
    /**
     * Customer Journey - Gestión de prospectos y solicitudes de vinculación
     */
    public function customerjourney() {
        $this->requireRole(['administrador', 'operativo']);
        
        $errors = [];
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $action = $_POST['action'] ?? '';
            $solicitudId = (int)($_POST['solicitud_id'] ?? 0);
            
            if ($action === 'aprobar' && $solicitudId) {
                $socioId = (int)($_POST['socio_id'] ?? 0);
                
                if (!$socioId) {
                    $errors[] = 'Debe seleccionar un socio para vincular';
                } else {
                    $solicitud = $this->db->fetch(
                        "SELECT * FROM solicitudes_vinculacion WHERE id = :id",
                        ['id' => $solicitudId]
                    );
                    
                    if ($solicitud && $solicitud['estatus'] === 'pendiente') {
                        // Vincular usuario con socio
                        $this->db->insert('usuarios_socios', [
                            'usuario_id' => $solicitud['usuario_id'],
                            'socio_id' => $socioId
                        ]);
                        
                        // Actualizar solicitud
                        $this->db->update('solicitudes_vinculacion', [
                            'estatus' => 'aprobada',
                            'revisado_por' => $_SESSION['user_id'],
                            'fecha_revision' => date('Y-m-d H:i:s'),
                            'socio_id' => $socioId
                        ], 'id = :id', ['id' => $solicitudId]);
                        
                        // Notificar al usuario
                        $this->db->insert('notificaciones', [
                            'usuario_id' => $solicitud['usuario_id'],
                            'tipo' => 'success',
                            'titulo' => 'Cuenta vinculada exitosamente',
                            'mensaje' => 'Tu solicitud de vinculación ha sido aprobada. Ya puedes acceder a tu portal de socio.',
                            'url' => 'cliente'
                        ]);
                        
                        $this->logAction('APROBAR_VINCULACION', 
                            "Se aprobó vinculación de usuario {$solicitud['usuario_id']} con socio {$socioId}",
                            'solicitudes_vinculacion',
                            $solicitudId
                        );
                        
                        $success = 'Solicitud aprobada y cuenta vinculada exitosamente';
                    }
                }
            } elseif ($action === 'rechazar' && $solicitudId) {
                $notas = $this->sanitize($_POST['notas_revision'] ?? '');
                
                $this->db->update('solicitudes_vinculacion', [
                    'estatus' => 'rechazada',
                    'revisado_por' => $_SESSION['user_id'],
                    'fecha_revision' => date('Y-m-d H:i:s'),
                    'notas_revision' => $notas
                ], 'id = :id', ['id' => $solicitudId]);
                
                // Notificar al usuario
                $solicitud = $this->db->fetch(
                    "SELECT usuario_id FROM solicitudes_vinculacion WHERE id = :id",
                    ['id' => $solicitudId]
                );
                
                if ($solicitud) {
                    $this->db->insert('notificaciones', [
                        'usuario_id' => $solicitud['usuario_id'],
                        'tipo' => 'warning',
                        'titulo' => 'Solicitud de vinculación rechazada',
                        'mensaje' => 'Tu solicitud de vinculación ha sido rechazada. Contacta a la oficina para más información.',
                        'url' => 'cliente'
                    ]);
                }
                
                $this->logAction('RECHAZAR_VINCULACION', 
                    "Se rechazó vinculación de solicitud {$solicitudId}",
                    'solicitudes_vinculacion',
                    $solicitudId
                );
                
                $success = 'Solicitud rechazada';
            }
        }
        
        // Obtener solicitudes de vinculación pendientes
        $solicitudesPendientes = $this->db->fetchAll(
            "SELECT sv.*, u.email as usuario_email
             FROM solicitudes_vinculacion sv
             JOIN usuarios u ON sv.usuario_id = u.id
             WHERE sv.estatus IN ('pendiente', 'en_revision')
             ORDER BY sv.created_at DESC"
        );
        
        // Obtener solicitudes de actualización de perfil pendientes
        $solicitudesActualizacion = $this->db->fetchAll(
            "SELECT sap.*, u.email as usuario_email, u.nombre as usuario_nombre,
                    s.numero_socio, s.nombre as socio_nombre, 
                    s.apellido_paterno, s.apellido_materno
             FROM solicitudes_actualizacion_perfil sap
             JOIN usuarios u ON sap.usuario_id = u.id
             LEFT JOIN socios s ON sap.socio_id = s.id
             WHERE sap.estatus = 'pendiente'
             ORDER BY sap.created_at DESC"
        );
        
        // Obtener historial de solicitudes
        $historialSolicitudes = $this->db->fetchAll(
            "SELECT sv.*, u.email as usuario_email,
                    ur.nombre as revisado_por_nombre,
                    s.numero_socio
             FROM solicitudes_vinculacion sv
             JOIN usuarios u ON sv.usuario_id = u.id
             LEFT JOIN usuarios ur ON sv.revisado_por = ur.id
             LEFT JOIN socios s ON sv.socio_id = s.id
             WHERE sv.estatus IN ('aprobada', 'rechazada')
             ORDER BY sv.fecha_revision DESC
             LIMIT 20"
        );
        
        // Estadísticas
        $stats = [
            'pendientes' => count($solicitudesPendientes),
            'aprobadas_mes' => $this->db->fetch(
                "SELECT COUNT(*) as total FROM solicitudes_vinculacion 
                 WHERE estatus = 'aprobada' AND MONTH(fecha_revision) = MONTH(CURDATE())"
            )['total'],
            'rechazadas_mes' => $this->db->fetch(
                "SELECT COUNT(*) as total FROM solicitudes_vinculacion 
                 WHERE estatus = 'rechazada' AND MONTH(fecha_revision) = MONTH(CURDATE())"
            )['total'],
            'usuarios_sin_vincular' => $this->db->fetch(
                "SELECT COUNT(*) as total FROM usuarios u
                 WHERE u.rol = 'cliente' AND u.activo = 1
                 AND u.id NOT IN (SELECT usuario_id FROM usuarios_socios)"
            )['total'],
            'actualizaciones_pendientes' => count($solicitudesActualizacion)
        ];
        
        $this->view('crm/customerjourney', [
            'pageTitle' => 'Customer Journey',
            'solicitudesPendientes' => $solicitudesPendientes,
            'solicitudesActualizacion' => $solicitudesActualizacion,
            'historialSolicitudes' => $historialSolicitudes,
            'stats' => $stats,
            'errors' => $errors,
            'success' => $success
        ]);
    }
}
