<?php
/**
 * Controlador de Reportes
 * Sistema de Gestión Integral de Caja de Ahorros
 */

require_once CORE_PATH . '/Controller.php';

class ReportesController extends Controller {
    
    public function index() {
        $this->requireAuth();
        
        // Indicadores generales para el dashboard
        $indicadores = $this->getIndicadores();
        
        $this->view('reportes/index', [
            'pageTitle' => 'Reportes y Tableros',
            'indicadores' => $indicadores
        ]);
    }
    
    public function socios() {
        $this->requireAuth();
        
        $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-01-01');
        $fechaFin = $_GET['fecha_fin'] ?? date('Y-m-d');
        
        // Altas y bajas por mes
        $altasBajas = $this->db->fetchAll(
            "SELECT 
                DATE_FORMAT(fecha_alta, '%Y-%m') as mes,
                COUNT(CASE WHEN estatus = 'activo' THEN 1 END) as altas,
                COUNT(CASE WHEN estatus = 'baja' THEN 1 END) as bajas
             FROM socios
             WHERE fecha_alta BETWEEN :inicio AND :fin
             GROUP BY DATE_FORMAT(fecha_alta, '%Y-%m')
             ORDER BY mes",
            ['inicio' => $fechaInicio, 'fin' => $fechaFin]
        );
        
        // Por unidad de trabajo
        $porUnidad = $this->db->fetchAll(
            "SELECT ut.nombre as unidad, COUNT(s.id) as total,
                    COALESCE(SUM(ca.saldo), 0) as saldo_total
             FROM unidades_trabajo ut
             LEFT JOIN socios s ON ut.id = s.unidad_trabajo_id AND s.estatus = 'activo'
             LEFT JOIN cuentas_ahorro ca ON s.id = ca.socio_id
             GROUP BY ut.id
             ORDER BY total DESC"
        );
        
        // Totales
        $totales = $this->db->fetch(
            "SELECT 
                COUNT(CASE WHEN estatus = 'activo' THEN 1 END) as activos,
                COUNT(CASE WHEN estatus = 'inactivo' THEN 1 END) as inactivos,
                COUNT(CASE WHEN estatus = 'baja' THEN 1 END) as bajas,
                COUNT(*) as total
             FROM socios"
        );
        
        $this->view('reportes/socios', [
            'pageTitle' => 'Reporte de Socios',
            'altasBajas' => $altasBajas,
            'porUnidad' => $porUnidad,
            'totales' => $totales,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin
        ]);
    }
    
    public function ahorro() {
        $this->requireAuth();
        
        $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-01-01');
        $fechaFin = $_GET['fecha_fin'] ?? date('Y-m-d');
        
        // Movimientos por mes
        $movimientosMes = $this->db->fetchAll(
            "SELECT 
                DATE_FORMAT(fecha, '%Y-%m') as mes,
                SUM(CASE WHEN tipo = 'aportacion' THEN monto ELSE 0 END) as aportaciones,
                SUM(CASE WHEN tipo = 'retiro' THEN monto ELSE 0 END) as retiros,
                SUM(CASE WHEN tipo = 'interes' THEN monto ELSE 0 END) as intereses
             FROM movimientos_ahorro
             WHERE DATE(fecha) BETWEEN :inicio AND :fin
             GROUP BY DATE_FORMAT(fecha, '%Y-%m')
             ORDER BY mes",
            ['inicio' => $fechaInicio, 'fin' => $fechaFin]
        );
        
        // Top ahorradores
        $topAhorradores = $this->db->fetchAll(
            "SELECT s.numero_socio, CONCAT(s.nombre, ' ', s.apellido_paterno) as nombre,
                    ca.saldo, ca.numero_cuenta
             FROM cuentas_ahorro ca
             JOIN socios s ON ca.socio_id = s.id
             WHERE ca.estatus = 'activa'
             ORDER BY ca.saldo DESC
             LIMIT 10"
        );
        
        // Totales
        $totales = $this->db->fetch(
            "SELECT 
                COALESCE(SUM(saldo), 0) as saldo_total,
                COUNT(*) as cuentas_activas,
                AVG(saldo) as promedio
             FROM cuentas_ahorro WHERE estatus = 'activa'"
        );
        
        $this->view('reportes/ahorro', [
            'pageTitle' => 'Reporte de Ahorro',
            'movimientosMes' => $movimientosMes,
            'topAhorradores' => $topAhorradores,
            'totales' => $totales,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin
        ]);
    }
    
    public function creditos() {
        $this->requireAuth();
        
        $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-01-01');
        $fechaFin = $_GET['fecha_fin'] ?? date('Y-m-d');
        
        // Créditos otorgados por mes
        $creditosMes = $this->db->fetchAll(
            "SELECT 
                DATE_FORMAT(fecha_autorizacion, '%Y-%m') as mes,
                COUNT(*) as cantidad,
                SUM(monto_autorizado) as monto_total
             FROM creditos
             WHERE fecha_autorizacion BETWEEN :inicio AND :fin
               AND estatus IN ('activo', 'formalizado', 'liquidado')
             GROUP BY DATE_FORMAT(fecha_autorizacion, '%Y-%m')
             ORDER BY mes",
            ['inicio' => $fechaInicio, 'fin' => $fechaFin]
        );
        
        // Por tipo de crédito
        $porTipo = $this->db->fetchAll(
            "SELECT tc.nombre, COUNT(c.id) as cantidad, 
                    COALESCE(SUM(c.monto_autorizado), 0) as monto_otorgado,
                    COALESCE(SUM(c.saldo_actual), 0) as saldo_actual
             FROM tipos_credito tc
             LEFT JOIN creditos c ON tc.id = c.tipo_credito_id AND c.estatus IN ('activo', 'formalizado')
             GROUP BY tc.id
             ORDER BY saldo_actual DESC"
        );
        
        // Resumen
        $resumen = $this->db->fetch(
            "SELECT 
                COUNT(*) as total_activos,
                SUM(monto_autorizado) as total_otorgado,
                SUM(saldo_actual) as cartera_total,
                AVG(monto_autorizado) as promedio_monto
             FROM creditos WHERE estatus IN ('activo', 'formalizado')"
        );
        
        $this->view('reportes/creditos', [
            'pageTitle' => 'Reporte de Créditos',
            'creditosMes' => $creditosMes,
            'porTipo' => $porTipo,
            'resumen' => $resumen,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin
        ]);
    }
    
    public function cartera() {
        $this->requireAuth();
        
        // Análisis de cartera
        $analisis = $this->db->fetch(
            "SELECT 
                COALESCE(SUM(saldo_actual), 0) as cartera_total,
                COUNT(*) as creditos_activos,
                AVG(saldo_actual) as promedio_saldo
             FROM creditos WHERE estatus IN ('activo', 'formalizado')"
        );
        
        // Cartera por antigüedad
        $porAntiguedad = $this->db->fetchAll(
            "SELECT 
                CASE 
                    WHEN DATEDIFF(CURDATE(), a.fecha_vencimiento) <= 0 THEN 'Al corriente'
                    WHEN DATEDIFF(CURDATE(), a.fecha_vencimiento) BETWEEN 1 AND 30 THEN '1-30 días'
                    WHEN DATEDIFF(CURDATE(), a.fecha_vencimiento) BETWEEN 31 AND 60 THEN '31-60 días'
                    WHEN DATEDIFF(CURDATE(), a.fecha_vencimiento) BETWEEN 61 AND 90 THEN '61-90 días'
                    ELSE 'Más de 90 días'
                END as rango,
                COUNT(*) as pagos,
                SUM(a.monto_total) as monto
             FROM amortizacion a
             JOIN creditos c ON a.credito_id = c.id
             WHERE c.estatus IN ('activo', 'formalizado') AND a.estatus != 'pagado'
             GROUP BY rango
             ORDER BY FIELD(rango, 'Al corriente', '1-30 días', '31-60 días', '61-90 días', 'Más de 90 días')"
        );
        
        $this->view('reportes/cartera', [
            'pageTitle' => 'Reporte de Cartera',
            'analisis' => $analisis,
            'porAntiguedad' => $porAntiguedad
        ]);
    }
    
    public function nomina() {
        $this->requireAuth();
        
        // Historial de procesamiento de nóminas
        $historial = $this->db->fetchAll(
            "SELECT an.*, u.nombre as usuario_nombre,
                    (SELECT SUM(monto_descuento) FROM registros_nomina WHERE archivo_id = an.id AND estatus = 'aplicado') as monto_aplicado
             FROM archivos_nomina an
             LEFT JOIN usuarios u ON an.usuario_id = u.id
             ORDER BY an.fecha_carga DESC
             LIMIT 20"
        );
        
        // Resumen
        $resumen = $this->db->fetch(
            "SELECT 
                COUNT(DISTINCT an.id) as archivos_procesados,
                COALESCE(SUM(rn.monto_descuento), 0) as monto_total_aplicado,
                COUNT(DISTINCT rn.socio_id) as socios_beneficiados
             FROM archivos_nomina an
             JOIN registros_nomina rn ON an.id = rn.archivo_id
             WHERE an.estatus = 'aplicado' AND rn.estatus = 'aplicado'"
        );
        
        $this->view('reportes/nomina', [
            'pageTitle' => 'Reporte de Nómina',
            'historial' => $historial,
            'resumen' => $resumen
        ]);
    }
    
    public function exportar() {
        $this->requireAuth();
        
        $tipo = $this->params['tipo'] ?? 'socios';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=reporte_' . $tipo . '_' . date('Y-m-d') . '.csv');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        switch ($tipo) {
            case 'socios':
                fputcsv($output, ['No. Socio', 'Nombre', 'RFC', 'CURP', 'Unidad', 'Estatus', 'Fecha Alta']);
                $data = $this->db->fetchAll(
                    "SELECT s.numero_socio, CONCAT(s.nombre, ' ', s.apellido_paterno, ' ', COALESCE(s.apellido_materno, '')) as nombre,
                            s.rfc, s.curp, ut.nombre as unidad, s.estatus, s.fecha_alta
                     FROM socios s
                     LEFT JOIN unidades_trabajo ut ON s.unidad_trabajo_id = ut.id
                     ORDER BY s.apellido_paterno, s.nombre"
                );
                foreach ($data as $row) {
                    fputcsv($output, array_values($row));
                }
                break;
                
            case 'ahorro':
                fputcsv($output, ['No. Cuenta', 'Socio', 'Saldo', 'Última Aportación']);
                $data = $this->db->fetchAll(
                    "SELECT ca.numero_cuenta, 
                            CONCAT(s.nombre, ' ', s.apellido_paterno) as nombre,
                            ca.saldo,
                            (SELECT MAX(fecha) FROM movimientos_ahorro WHERE cuenta_id = ca.id) as ultimo_mov
                     FROM cuentas_ahorro ca
                     JOIN socios s ON ca.socio_id = s.id
                     WHERE ca.estatus = 'activa'
                     ORDER BY ca.saldo DESC"
                );
                foreach ($data as $row) {
                    fputcsv($output, array_values($row));
                }
                break;
        }
        
        fclose($output);
        exit;
    }
    
    private function getIndicadores() {
        return [
            'socios' => [
                'total' => $this->db->fetch("SELECT COUNT(*) as t FROM socios WHERE estatus = 'activo'")['t'],
                'nuevos_mes' => $this->db->fetch("SELECT COUNT(*) as t FROM socios WHERE estatus = 'activo' AND MONTH(fecha_alta) = MONTH(CURDATE())")['t']
            ],
            'ahorro' => [
                'saldo_total' => $this->db->fetch("SELECT COALESCE(SUM(saldo), 0) as t FROM cuentas_ahorro WHERE estatus = 'activa'")['t'],
                'aportaciones_mes' => $this->db->fetch("SELECT COALESCE(SUM(monto), 0) as t FROM movimientos_ahorro WHERE tipo = 'aportacion' AND MONTH(fecha) = MONTH(CURDATE())")['t']
            ],
            'creditos' => [
                'cartera_total' => $this->db->fetch("SELECT COALESCE(SUM(saldo_actual), 0) as t FROM creditos WHERE estatus IN ('activo', 'formalizado')")['t'],
                'creditos_activos' => $this->db->fetch("SELECT COUNT(*) as t FROM creditos WHERE estatus IN ('activo', 'formalizado')")['t']
            ],
            'cartera_vencida' => [
                'monto' => $this->db->fetch("SELECT COALESCE(SUM(a.monto_total), 0) as t FROM amortizacion a JOIN creditos c ON a.credito_id = c.id WHERE (a.estatus = 'vencido' OR (a.estatus = 'pendiente' AND a.fecha_vencimiento < CURDATE())) AND c.estatus IN ('activo', 'formalizado')")['t'],
                'socios_mora' => $this->db->fetch("SELECT COUNT(DISTINCT c.socio_id) as t FROM amortizacion a JOIN creditos c ON a.credito_id = c.id WHERE (a.estatus = 'vencido' OR (a.estatus = 'pendiente' AND a.fecha_vencimiento < CURDATE())) AND c.estatus IN ('activo', 'formalizado')")['t']
            ]
        ];
    }
}
