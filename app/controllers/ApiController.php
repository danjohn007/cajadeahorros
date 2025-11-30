<?php
/**
 * Controlador de API
 * Sistema de Gestión Integral de Caja de Ahorros
 */

require_once CORE_PATH . '/Controller.php';

class ApiController extends Controller {
    
    public function buscarSocios() {
        $this->requireAuth();
        
        $q = $_GET['q'] ?? '';
        
        if (strlen($q) < 2) {
            $this->json(['results' => []]);
        }
        
        $results = $this->db->fetchAll(
            "SELECT id, numero_socio, nombre, apellido_paterno, apellido_materno, rfc
             FROM socios
             WHERE estatus = 'activo' AND (
                nombre LIKE :q OR 
                apellido_paterno LIKE :q OR 
                rfc LIKE :q OR 
                numero_socio LIKE :q
             )
             LIMIT 10",
            ['q' => "%{$q}%"]
        );
        
        $this->json(['results' => $results]);
    }
    
    public function dashboardStats() {
        $this->requireAuth();
        
        $stats = [
            'socios' => $this->db->fetch("SELECT COUNT(*) as t FROM socios WHERE estatus = 'activo'")['t'],
            'saldoAhorro' => $this->db->fetch("SELECT COALESCE(SUM(saldo), 0) as t FROM cuentas_ahorro WHERE estatus = 'activa'")['t'],
            'carteraCreditos' => $this->db->fetch("SELECT COALESCE(SUM(saldo_actual), 0) as t FROM creditos WHERE estatus IN ('activo', 'formalizado')")['t'],
            'carteraVencida' => $this->db->fetch("SELECT COALESCE(SUM(monto_total), 0) as t FROM amortizacion WHERE estatus = 'vencido' OR (estatus = 'pendiente' AND fecha_vencimiento < CURDATE())")['t']
        ];
        
        $this->json($stats);
    }
}
