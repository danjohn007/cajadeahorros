<?php
/**
 * Controlador del Dashboard
 * Sistema de Gestión Integral de Caja de Ahorros
 */

require_once CORE_PATH . '/Controller.php';

class DashboardController extends Controller {
    
    public function index() {
        $this->requireAuth();
        
        // Obtener estadísticas generales
        $stats = $this->getStats();
        
        // Obtener últimos movimientos
        $ultimosMovimientos = $this->getUltimosMovimientos();
        
        // Obtener créditos pendientes de revisión
        $creditosPendientes = $this->getCreditosPendientes();
        
        // Obtener socios recientes
        $sociosRecientes = $this->getSociosRecientes();
        
        // Datos para gráficas
        $datosGraficas = $this->getDatosGraficas();
        
        $this->view('dashboard/index', [
            'pageTitle' => 'Dashboard',
            'stats' => $stats,
            'ultimosMovimientos' => $ultimosMovimientos,
            'creditosPendientes' => $creditosPendientes,
            'sociosRecientes' => $sociosRecientes,
            'datosGraficas' => $datosGraficas
        ]);
    }
    
    private function getStats() {
        // Total de socios activos
        $totalSocios = $this->db->fetch(
            "SELECT COUNT(*) as total FROM socios WHERE estatus = 'activo'"
        )['total'];
        
        // Saldo total de ahorro
        $saldoAhorro = $this->db->fetch(
            "SELECT COALESCE(SUM(saldo), 0) as total FROM cuentas_ahorro WHERE estatus = 'activa'"
        )['total'];
        
        // Cartera total de créditos
        $carteraCreditos = $this->db->fetch(
            "SELECT COALESCE(SUM(saldo_actual), 0) as total FROM creditos WHERE estatus IN ('activo', 'formalizado')"
        )['total'];
        
        // Cartera vencida
        $carteraVencida = $this->db->fetch(
            "SELECT COALESCE(SUM(monto_total), 0) as total FROM amortizacion 
             WHERE estatus = 'vencido' OR (estatus = 'pendiente' AND fecha_vencimiento < CURDATE())"
        )['total'];
        
        // Créditos activos
        $creditosActivos = $this->db->fetch(
            "SELECT COUNT(*) as total FROM creditos WHERE estatus IN ('activo', 'formalizado')"
        )['total'];
        
        // Solicitudes pendientes
        $solicitudesPendientes = $this->db->fetch(
            "SELECT COUNT(*) as total FROM creditos WHERE estatus IN ('solicitud', 'en_revision')"
        )['total'];
        
        return [
            'totalSocios' => $totalSocios,
            'saldoAhorro' => $saldoAhorro,
            'carteraCreditos' => $carteraCreditos,
            'carteraVencida' => $carteraVencida,
            'creditosActivos' => $creditosActivos,
            'solicitudesPendientes' => $solicitudesPendientes,
            'porcentajeVencida' => $carteraCreditos > 0 ? round(($carteraVencida / $carteraCreditos) * 100, 2) : 0
        ];
    }
    
    private function getUltimosMovimientos() {
        return $this->db->fetchAll(
            "SELECT ma.*, ca.numero_cuenta, 
                    CONCAT(s.nombre, ' ', s.apellido_paterno) as nombre_socio,
                    u.nombre as usuario_nombre
             FROM movimientos_ahorro ma
             JOIN cuentas_ahorro ca ON ma.cuenta_id = ca.id
             JOIN socios s ON ca.socio_id = s.id
             LEFT JOIN usuarios u ON ma.usuario_id = u.id
             ORDER BY ma.fecha DESC
             LIMIT 10"
        );
    }
    
    private function getCreditosPendientes() {
        return $this->db->fetchAll(
            "SELECT c.*, tc.nombre as tipo_credito,
                    CONCAT(s.nombre, ' ', s.apellido_paterno) as nombre_socio
             FROM creditos c
             JOIN socios s ON c.socio_id = s.id
             JOIN tipos_credito tc ON c.tipo_credito_id = tc.id
             WHERE c.estatus IN ('solicitud', 'en_revision')
             ORDER BY c.fecha_solicitud DESC
             LIMIT 5"
        );
    }
    
    private function getSociosRecientes() {
        return $this->db->fetchAll(
            "SELECT s.*, ut.nombre as unidad_trabajo
             FROM socios s
             LEFT JOIN unidades_trabajo ut ON s.unidad_trabajo_id = ut.id
             ORDER BY s.created_at DESC
             LIMIT 5"
        );
    }
    
    private function getDatosGraficas() {
        // Movimientos de ahorro por mes (últimos 6 meses)
        $movimientosMes = $this->db->fetchAll(
            "SELECT 
                DATE_FORMAT(fecha, '%Y-%m') as mes,
                SUM(CASE WHEN tipo = 'aportacion' THEN monto ELSE 0 END) as aportaciones,
                SUM(CASE WHEN tipo = 'retiro' THEN monto ELSE 0 END) as retiros
             FROM movimientos_ahorro
             WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
             GROUP BY DATE_FORMAT(fecha, '%Y-%m')
             ORDER BY mes"
        );
        
        // Distribución de créditos por tipo
        $creditosPorTipo = $this->db->fetchAll(
            "SELECT tc.nombre, COUNT(*) as cantidad, SUM(c.saldo_actual) as monto
             FROM creditos c
             JOIN tipos_credito tc ON c.tipo_credito_id = tc.id
             WHERE c.estatus IN ('activo', 'formalizado')
             GROUP BY tc.id, tc.nombre"
        );
        
        // Estado de cartera
        $estadoCartera = $this->db->fetchAll(
            "SELECT 
                CASE 
                    WHEN a.estatus = 'pagado' THEN 'Al corriente'
                    WHEN a.estatus = 'vencido' OR (a.estatus = 'pendiente' AND a.fecha_vencimiento < CURDATE()) THEN 'Vencido'
                    ELSE 'Pendiente'
                END as estado,
                COUNT(*) as cantidad
             FROM amortizacion a
             JOIN creditos c ON a.credito_id = c.id
             WHERE c.estatus IN ('activo', 'formalizado')
             GROUP BY estado"
        );
        
        return [
            'movimientosMes' => $movimientosMes,
            'creditosPorTipo' => $creditosPorTipo,
            'estadoCartera' => $estadoCartera
        ];
    }
}
