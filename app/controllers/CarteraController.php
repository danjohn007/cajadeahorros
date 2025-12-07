<?php
/**
 * Controlador de Cartera y Cobranza
 * Sistema de Gestión Integral de Caja de Ahorros
 */

require_once CORE_PATH . '/Controller.php';

class CarteraController extends Controller {
    
    public function index() {
        $this->requireAuth();
        
        // Estadísticas de cartera
        $stats = $this->getStats();
        
        // Cartera general
        $cartera = $this->db->fetchAll(
            "SELECT c.*, tc.nombre as tipo_credito,
                    s.numero_socio, s.nombre, s.apellido_paterno, s.apellido_materno,
                    (SELECT COUNT(*) FROM amortizacion WHERE credito_id = c.id AND 
                        (estatus = 'vencido' OR (estatus = 'pendiente' AND fecha_vencimiento < CURDATE()))) as pagos_vencidos
             FROM creditos c
             JOIN socios s ON c.socio_id = s.id
             JOIN tipos_credito tc ON c.tipo_credito_id = tc.id
             WHERE c.estatus IN ('activo', 'formalizado')
             ORDER BY pagos_vencidos DESC, c.saldo_actual DESC
             LIMIT 50"
        );
        
        $this->view('cartera/index', [
            'pageTitle' => 'Cartera de Créditos',
            'cartera' => $cartera,
            'stats' => $stats
        ]);
    }
    
    public function vencida() {
        $this->requireAuth();
        
        $rangoFilter = $_GET['rango'] ?? '';
        
        $conditions = "(a.estatus = 'vencido' OR (a.estatus = 'pendiente' AND a.fecha_vencimiento < CURDATE()))";
        
        if ($rangoFilter === '1-30') {
            $conditions .= " AND DATEDIFF(CURDATE(), a.fecha_vencimiento) BETWEEN 1 AND 30";
        } elseif ($rangoFilter === '31-60') {
            $conditions .= " AND DATEDIFF(CURDATE(), a.fecha_vencimiento) BETWEEN 31 AND 60";
        } elseif ($rangoFilter === '61-90') {
            $conditions .= " AND DATEDIFF(CURDATE(), a.fecha_vencimiento) BETWEEN 61 AND 90";
        } elseif ($rangoFilter === '90+') {
            $conditions .= " AND DATEDIFF(CURDATE(), a.fecha_vencimiento) > 90";
        }
        
        $vencida = $this->db->fetchAll(
            "SELECT c.id as credito_id, c.numero_credito, c.saldo_actual,
                    s.id as socio_id, s.numero_socio, s.nombre, s.apellido_paterno, 
                    s.telefono, s.celular, s.email,
                    a.numero_pago, a.fecha_vencimiento, a.monto_total,
                    DATEDIFF(CURDATE(), a.fecha_vencimiento) as dias_vencido,
                    tc.nombre as tipo_credito
             FROM amortizacion a
             JOIN creditos c ON a.credito_id = c.id
             JOIN socios s ON c.socio_id = s.id
             JOIN tipos_credito tc ON c.tipo_credito_id = tc.id
             WHERE {$conditions} AND c.estatus IN ('activo', 'formalizado')
             ORDER BY dias_vencido DESC"
        );
        
        // Agrupar por rango
        $porRango = [
            '1-30' => 0,
            '31-60' => 0,
            '61-90' => 0,
            '90+' => 0
        ];
        $montosPorRango = [
            '1-30' => 0,
            '31-60' => 0,
            '61-90' => 0,
            '90+' => 0
        ];
        
        foreach ($vencida as $item) {
            $dias = $item['dias_vencido'];
            if ($dias <= 30) {
                $porRango['1-30']++;
                $montosPorRango['1-30'] += $item['monto_total'];
            } elseif ($dias <= 60) {
                $porRango['31-60']++;
                $montosPorRango['31-60'] += $item['monto_total'];
            } elseif ($dias <= 90) {
                $porRango['61-90']++;
                $montosPorRango['61-90'] += $item['monto_total'];
            } else {
                $porRango['90+']++;
                $montosPorRango['90+'] += $item['monto_total'];
            }
        }
        
        $this->view('cartera/vencida', [
            'pageTitle' => 'Cartera Vencida',
            'vencida' => $vencida,
            'porRango' => $porRango,
            'montosPorRango' => $montosPorRango,
            'rangoFilter' => $rangoFilter
        ]);
    }
    
    public function mora() {
        $this->requireAuth();
        
        // Socios en mora con datos de contacto
        $mora = $this->db->fetchAll(
            "SELECT s.id, s.numero_socio, s.nombre, s.apellido_paterno, s.apellido_materno,
                    s.telefono, s.celular, s.email, s.direccion,
                    COUNT(DISTINCT c.id) as creditos_mora,
                    SUM(a.monto_total) as monto_adeudado,
                    MAX(DATEDIFF(CURDATE(), a.fecha_vencimiento)) as max_dias_vencido
             FROM socios s
             JOIN creditos c ON s.id = c.socio_id
             JOIN amortizacion a ON c.id = a.credito_id
             WHERE c.estatus IN ('activo', 'formalizado')
               AND (a.estatus = 'vencido' OR (a.estatus = 'pendiente' AND a.fecha_vencimiento < CURDATE()))
             GROUP BY s.id
             ORDER BY max_dias_vencido DESC, monto_adeudado DESC"
        );
        
        $this->view('cartera/mora', [
            'pageTitle' => 'Socios en Mora',
            'mora' => $mora
        ]);
    }
    
    public function exportar() {
        $this->requireAuth();
        
        $tipo = $_GET['tipo'] ?? 'cartera';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $tipo . '_' . date('Y-m-d') . '.csv');
        
        $output = fopen('php://output', 'w');
        
        // BOM para Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        if ($tipo === 'vencida') {
            fputcsv($output, ['No. Crédito', 'Socio', 'Teléfono', 'Celular', 'Email', 'Pago #', 'Vencimiento', 'Monto', 'Días Vencido']);
            
            $vencida = $this->db->fetchAll(
                "SELECT c.numero_credito, CONCAT(s.nombre, ' ', s.apellido_paterno) as socio,
                        s.telefono, s.celular, s.email,
                        a.numero_pago, a.fecha_vencimiento, a.monto_total,
                        DATEDIFF(CURDATE(), a.fecha_vencimiento) as dias_vencido
                 FROM amortizacion a
                 JOIN creditos c ON a.credito_id = c.id
                 JOIN socios s ON c.socio_id = s.id
                 WHERE (a.estatus = 'vencido' OR (a.estatus = 'pendiente' AND a.fecha_vencimiento < CURDATE()))
                   AND c.estatus IN ('activo', 'formalizado')
                 ORDER BY dias_vencido DESC"
            );
            
            foreach ($vencida as $row) {
                fputcsv($output, [
                    $row['numero_credito'],
                    $row['socio'],
                    $row['telefono'],
                    $row['celular'],
                    $row['email'],
                    $row['numero_pago'],
                    $row['fecha_vencimiento'],
                    $row['monto_total'],
                    $row['dias_vencido']
                ]);
            }
        } else {
            fputcsv($output, ['No. Crédito', 'Socio', 'Tipo', 'Monto Autorizado', 'Saldo Actual', 'Pagos Realizados', 'Estatus']);
            
            $cartera = $this->db->fetchAll(
                "SELECT c.numero_credito, CONCAT(s.nombre, ' ', s.apellido_paterno) as socio,
                        tc.nombre as tipo, c.monto_autorizado, c.saldo_actual, 
                        c.pagos_realizados, c.plazo_meses, c.estatus
                 FROM creditos c
                 JOIN socios s ON c.socio_id = s.id
                 JOIN tipos_credito tc ON c.tipo_credito_id = tc.id
                 WHERE c.estatus IN ('activo', 'formalizado')
                 ORDER BY c.saldo_actual DESC"
            );
            
            foreach ($cartera as $row) {
                fputcsv($output, [
                    $row['numero_credito'],
                    $row['socio'],
                    $row['tipo'],
                    $row['monto_autorizado'],
                    $row['saldo_actual'],
                    $row['pagos_realizados'] . '/' . $row['plazo_meses'],
                    ucfirst($row['estatus'])
                ]);
            }
        }
        
        fclose($output);
        exit;
    }
    
    private function getStats() {
        $carteraTotal = $this->db->fetch(
            "SELECT COALESCE(SUM(saldo_actual), 0) as total FROM creditos WHERE estatus IN ('activo', 'formalizado')"
        )['total'];
        
        $carteraVencida = $this->db->fetch(
            "SELECT COALESCE(SUM(a.monto_total), 0) as total 
             FROM amortizacion a
             JOIN creditos c ON a.credito_id = c.id
             WHERE (a.estatus = 'vencido' OR (a.estatus = 'pendiente' AND a.fecha_vencimiento < CURDATE()))
               AND c.estatus IN ('activo', 'formalizado')"
        )['total'];
        
        $creditosActivos = $this->db->fetch(
            "SELECT COUNT(*) as total FROM creditos WHERE estatus IN ('activo', 'formalizado')"
        )['total'];
        
        $sociosMora = $this->db->fetch(
            "SELECT COUNT(DISTINCT s.id) as total
             FROM socios s
             JOIN creditos c ON s.id = c.socio_id
             JOIN amortizacion a ON c.id = a.credito_id
             WHERE c.estatus IN ('activo', 'formalizado')
               AND (a.estatus = 'vencido' OR (a.estatus = 'pendiente' AND a.fecha_vencimiento < CURDATE()))"
        )['total'];
        
        return [
            'carteraTotal' => $carteraTotal,
            'carteraVencida' => $carteraVencida,
            'creditosActivos' => $creditosActivos,
            'sociosMora' => $sociosMora,
            'porcentajeVencida' => $carteraTotal > 0 ? round(($carteraVencida / $carteraTotal) * 100, 2) : 0
        ];
    }
    
    /**
     * Aplicación de pagos y abonos
     */
    public function aplicarPago() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $credito_id = $_POST['credito_id'] ?? null;
                $monto = $_POST['monto'] ?? 0;
                $fecha_pago = $_POST['fecha_pago'] ?? date('Y-m-d');
                $tipo_pago = $_POST['tipo_pago'] ?? 'efectivo';
                
                if (!$credito_id || $monto <= 0) {
                    throw new Exception('Datos incompletos o inválidos');
                }
                
                // Aplicar pago a las amortizaciones pendientes
                $amortizaciones = $this->db->fetchAll(
                    "SELECT * FROM amortizacion 
                     WHERE credito_id = ? AND estatus IN ('pendiente', 'vencido')
                     ORDER BY fecha_vencimiento ASC",
                    [$credito_id]
                );
                
                $monto_restante = $monto;
                foreach ($amortizaciones as $amortizacion) {
                    if ($monto_restante <= 0) break;
                    
                    $monto_aplicar = min($monto_restante, $amortizacion['monto_total']);
                    
                    $this->db->update('amortizacion', $amortizacion['id'], [
                        'monto_pagado' => $amortizacion['monto_pagado'] + $monto_aplicar,
                        'fecha_pago' => $fecha_pago,
                        'estatus' => ($amortizacion['monto_pagado'] + $monto_aplicar >= $amortizacion['monto_total']) ? 'pagado' : 'parcial'
                    ]);
                    
                    $monto_restante -= $monto_aplicar;
                }
                
                // Actualizar saldo del crédito
                $this->db->query(
                    "UPDATE creditos SET saldo_actual = saldo_actual - ? WHERE id = ?",
                    [$monto - $monto_restante, $credito_id]
                );
                
                $this->logAction(
                    $_SESSION['user_id'],
                    'aplicar_pago',
                    "Pago aplicado al crédito #$credito_id por $" . number_format($monto, 2),
                    'creditos',
                    $credito_id
                );
                
                $this->jsonResponse(['success' => true, 'message' => 'Pago aplicado correctamente']);
            } catch (Exception $e) {
                $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
            }
        }
    }
    
    /**
     * Control de carteras vigentes
     */
    public function vigente() {
        $this->requireAuth();
        
        $vigente = $this->db->fetchAll(
            "SELECT c.*, tc.nombre as tipo_credito,
                    s.numero_socio, s.nombre, s.apellido_paterno, s.apellido_materno,
                    (SELECT COUNT(*) FROM amortizacion WHERE credito_id = c.id AND estatus = 'pagado') as pagos_realizados,
                    (SELECT COUNT(*) FROM amortizacion WHERE credito_id = c.id) as total_pagos
             FROM creditos c
             JOIN socios s ON c.socio_id = s.id
             JOIN tipos_credito tc ON c.tipo_credito_id = tc.id
             WHERE c.estatus IN ('activo', 'formalizado')
             AND c.tipo_cartera = 'vigente'
             ORDER BY c.fecha_formalizacion DESC
             LIMIT 100"
        );
        
        $this->view('cartera/vigente', [
            'pageTitle' => 'Cartera Vigente',
            'vigente' => $vigente
        ]);
    }
    
    /**
     * Generación de estados de cuenta y recibos
     */
    public function estadosCuenta($credito_id = null) {
        if ($credito_id) {
            $credito = $this->db->fetch(
                "SELECT c.*, s.nombre, s.apellido_paterno, s.apellido_materno, s.direccion
                 FROM creditos c
                 JOIN socios s ON c.socio_id = s.id
                 WHERE c.id = ?",
                [$credito_id]
            );
            
            if (!$credito) {
                $this->redirect('/cartera');
                return;
            }
            
            $amortizaciones = $this->db->fetchAll(
                "SELECT * FROM amortizacion WHERE credito_id = ? ORDER BY numero_pago",
                [$credito_id]
            );
            
            $this->view('cartera/estado_cuenta', [
                'pageTitle' => 'Estado de Cuenta',
                'credito' => $credito,
                'amortizaciones' => $amortizaciones
            ]);
        } else {
            $this->redirect('/cartera');
        }
    }
    
    /**
     * Gestión de cartera vencida
     */
    public function gestionVencida() {
        $carteraVencida = $this->db->fetchAll(
            "SELECT c.*, s.nombre, s.apellido_paterno, s.telefono, s.celular,
                    COUNT(a.id) as pagos_vencidos,
                    SUM(a.monto_total) as monto_vencido,
                    MAX(DATEDIFF(CURDATE(), a.fecha_vencimiento)) as dias_mora_max
             FROM creditos c
             JOIN socios s ON c.socio_id = s.id
             JOIN amortizacion a ON c.id = a.credito_id
             WHERE c.estatus IN ('activo', 'formalizado')
             AND (a.estatus = 'vencido' OR (a.estatus = 'pendiente' AND a.fecha_vencimiento < CURDATE()))
             GROUP BY c.id
             ORDER BY dias_mora_max DESC"
        );
        
        $this->view('cartera/gestion_vencida', [
            'pageTitle' => 'Gestión de Cartera Vencida',
            'carteraVencida' => $carteraVencida
        ]);
    }
    
    /**
     * Gestión de prepagos y liquidaciones anticipadas
     */
    public function prepagos() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $credito_id = $_POST['credito_id'] ?? null;
                $monto_prepago = $_POST['monto_prepago'] ?? 0;
                $tipo = $_POST['tipo'] ?? 'parcial';
                
                if (!$credito_id || $monto_prepago <= 0) {
                    throw new Exception('Datos incompletos');
                }
                
                // Registrar liquidación
                $this->db->insert('liquidaciones_credito', [
                    'credito_id' => $credito_id,
                    'tipo' => $tipo,
                    'fecha_liquidacion' => date('Y-m-d'),
                    'saldo_capital' => $monto_prepago,
                    'total_liquidado' => $monto_prepago,
                    'usuario_id' => $_SESSION['user_id']
                ]);
                
                if ($tipo === 'total') {
                    $this->db->update('creditos', $credito_id, [
                        'estatus' => 'liquidado',
                        'saldo_actual' => 0
                    ]);
                }
                
                $this->setFlash('success', 'Prepago registrado correctamente');
                $this->redirect('/cartera/prepagos');
            } catch (Exception $e) {
                $this->setFlash('error', 'Error al registrar prepago');
            }
        }
        
        $prepagos = $this->db->fetchAll(
            "SELECT l.*, c.numero_credito, s.nombre, s.apellido_paterno
             FROM liquidaciones_credito l
             JOIN creditos c ON l.credito_id = c.id
             JOIN socios s ON c.socio_id = s.id
             ORDER BY l.fecha_liquidacion DESC
             LIMIT 100"
        );
        
        $this->view('cartera/prepagos', [
            'pageTitle' => 'Prepagos y Liquidaciones Anticipadas',
            'prepagos' => $prepagos
        ]);
    }
    
    /**
     * Procesamiento de traspasos de cartera
     */
    public function traspasos() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $credito_id = $_POST['credito_id'] ?? null;
                $tipo_traspaso = $_POST['tipo_traspaso'] ?? '';
                $motivo = $_POST['motivo'] ?? '';
                
                if (!$credito_id || !$tipo_traspaso) {
                    throw new Exception('Datos incompletos');
                }
                
                $credito = $this->db->fetch("SELECT * FROM creditos WHERE id = ?", [$credito_id]);
                
                // Registrar traspaso
                $this->db->insert('traspasos_cartera', [
                    'credito_id' => $credito_id,
                    'tipo_traspaso' => $tipo_traspaso,
                    'fecha_traspaso' => date('Y-m-d'),
                    'dias_mora' => $credito['dias_mora'] ?? 0,
                    'saldo_vencido' => $credito['saldo_actual'],
                    'motivo' => $motivo,
                    'usuario_id' => $_SESSION['user_id']
                ]);
                
                // Actualizar tipo de cartera
                $nuevo_tipo = ($tipo_traspaso === 'vigente_a_vencida') ? 'vencida' : 'vigente';
                $this->db->update('creditos', $credito_id, [
                    'tipo_cartera' => $nuevo_tipo
                ]);
                
                $this->logAction(
                    $_SESSION['user_id'],
                    'traspaso_cartera',
                    "Traspaso de cartera: $tipo_traspaso para crédito #$credito_id",
                    'traspasos_cartera',
                    $credito_id
                );
                
                $this->setFlash('success', 'Traspaso registrado correctamente');
                $this->redirect('/cartera/traspasos');
            } catch (Exception $e) {
                $this->setFlash('error', 'Error al registrar traspaso');
            }
        }
        
        $traspasos = $this->db->fetchAll(
            "SELECT t.*, c.numero_credito, s.nombre, s.apellido_paterno
             FROM traspasos_cartera t
             JOIN creditos c ON t.credito_id = c.id
             JOIN socios s ON c.socio_id = s.id
             ORDER BY t.fecha_traspaso DESC
             LIMIT 100"
        );
        
        $this->view('cartera/traspasos', [
            'pageTitle' => 'Traspasos de Cartera',
            'traspasos' => $traspasos
        ]);
    }
}
