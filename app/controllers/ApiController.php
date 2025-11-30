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
    
    public function qr() {
        // API para generar códigos QR
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        $contenido = $input['contenido'] ?? '';
        $tamano = min(max((int)($input['tamano'] ?? 200), 50), 500);
        
        if (empty($contenido)) {
            http_response_code(400);
            echo json_encode(['error' => 'El contenido es requerido']);
            exit;
        }
        
        // Generar URL del QR usando servicio externo
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=' . $tamano . 'x' . $tamano . '&data=' . urlencode($contenido);
        
        echo json_encode([
            'success' => true,
            'qr_url' => $qrUrl,
            'contenido' => $contenido,
            'tamano' => $tamano
        ]);
        exit;
    }
    
    public function generarQRMasivo() {
        $this->requireAuth();
        $this->requireRole(['administrador', 'operativo']);
        
        header('Content-Type: application/json');
        
        $tipo = $_POST['tipo'] ?? 'socios';
        $qrs = [];
        
        switch ($tipo) {
            case 'socios':
                $registros = $this->db->fetchAll(
                    "SELECT id, numero_socio, nombre, apellido_paterno FROM socios WHERE estatus = 'activo' LIMIT 100"
                );
                foreach ($registros as $r) {
                    $data = "SOCIO|{$r['numero_socio']}|{$r['nombre']} {$r['apellido_paterno']}";
                    $qrs[] = [
                        'id' => $r['id'],
                        'label' => $r['numero_socio'],
                        'url' => 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($data)
                    ];
                }
                break;
                
            case 'creditos':
                $registros = $this->db->fetchAll(
                    "SELECT c.id, c.numero_credito, s.nombre, s.apellido_paterno 
                     FROM creditos c 
                     JOIN socios s ON c.socio_id = s.id 
                     WHERE c.estatus IN ('activo', 'formalizado') 
                     LIMIT 100"
                );
                foreach ($registros as $r) {
                    $data = "CREDITO|{$r['numero_credito']}|{$r['nombre']} {$r['apellido_paterno']}";
                    $qrs[] = [
                        'id' => $r['id'],
                        'label' => $r['numero_credito'],
                        'url' => 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($data)
                    ];
                }
                break;
                
            case 'cuentas':
                $registros = $this->db->fetchAll(
                    "SELECT ca.id, ca.numero_cuenta, s.nombre, s.apellido_paterno 
                     FROM cuentas_ahorro ca 
                     JOIN socios s ON ca.socio_id = s.id 
                     WHERE ca.estatus = 'activa' 
                     LIMIT 100"
                );
                foreach ($registros as $r) {
                    $data = "CUENTA|{$r['numero_cuenta']}|{$r['nombre']} {$r['apellido_paterno']}";
                    $qrs[] = [
                        'id' => $r['id'],
                        'label' => $r['numero_cuenta'],
                        'url' => 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($data)
                    ];
                }
                break;
        }
        
        echo json_encode([
            'success' => true,
            'tipo' => $tipo,
            'total' => count($qrs),
            'qrs' => $qrs
        ]);
        exit;
    }
}
