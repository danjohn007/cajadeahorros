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
}
