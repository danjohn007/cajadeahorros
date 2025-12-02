<?php
/**
 * Controlador de Pagos Online (PayPal)
 * Sistema de Gestión Integral de Caja de Ahorros
 */

require_once CORE_PATH . '/Controller.php';

class PagoController extends Controller {
    
    /**
     * Generates a payment link for a credit
     */
    public function enlace() {
        $this->requireAuth();
        
        $creditoId = $this->params['id'] ?? 0;
        
        $credito = $this->db->fetch(
            "SELECT c.*, s.numero_socio, s.nombre, s.apellido_paterno, s.email, s.celular
             FROM creditos c
             JOIN socios s ON c.socio_id = s.id
             WHERE c.id = :id AND c.estatus IN ('activo', 'formalizado')",
            ['id' => $creditoId]
        );
        
        if (!$credito) {
            $this->setFlash('error', 'Crédito no encontrado o no está activo');
            $this->redirect('cartera');
        }
        
        // Generate or get existing payment token
        $existingToken = $this->db->fetch(
            "SELECT * FROM tokens_pago WHERE credito_id = :id AND activo = 1 AND fecha_expiracion > NOW()",
            ['id' => $creditoId]
        );
        
        if ($existingToken) {
            $token = $existingToken['token'];
        } else {
            // Deactivate old tokens
            $this->db->execute(
                "UPDATE tokens_pago SET activo = 0 WHERE credito_id = :id",
                ['id' => $creditoId]
            );
            
            // Generate new token
            $token = bin2hex(random_bytes(32));
            $this->db->insert('tokens_pago', [
                'credito_id' => $creditoId,
                'token' => $token,
                'monto' => $credito['saldo_actual'],
                'tipo' => 'credito_total',
                'fecha_expiracion' => date('Y-m-d H:i:s', strtotime('+7 days')),
                'activo' => 1,
                'usuario_id' => $_SESSION['user_id']
            ]);
        }
        
        $enlacePago = BASE_URL . '/pago/publico/' . $token;
        
        $this->logAction('GENERAR_ENLACE_PAGO', "Enlace de pago generado para crédito {$credito['numero_credito']}", 'creditos', $creditoId);
        
        $this->view('pago/enlace', [
            'pageTitle' => 'Enlace de Pago',
            'credito' => $credito,
            'enlacePago' => $enlacePago,
            'token' => $token
        ]);
    }
    
    /**
     * Generates payment link for a specific installment
     */
    public function cuota() {
        $this->requireAuth();
        
        $amortizacionId = $this->params['id'] ?? 0;
        
        $amortizacion = $this->db->fetch(
            "SELECT a.*, c.numero_credito, c.id as credito_id, c.saldo_actual,
                    s.numero_socio, s.nombre, s.apellido_paterno, s.email
             FROM amortizacion a
             JOIN creditos c ON a.credito_id = c.id
             JOIN socios s ON c.socio_id = s.id
             WHERE a.id = :id AND a.estatus IN ('pendiente', 'vencido')",
            ['id' => $amortizacionId]
        );
        
        if (!$amortizacion) {
            $this->setFlash('error', 'Cuota no encontrada o ya fue pagada');
            $this->redirect('creditos');
        }
        
        // Generate token for this specific payment
        $token = bin2hex(random_bytes(32));
        $this->db->insert('tokens_pago', [
            'credito_id' => $amortizacion['credito_id'],
            'amortizacion_id' => $amortizacionId,
            'token' => $token,
            'monto' => $amortizacion['monto_total'],
            'tipo' => 'cuota',
            'fecha_expiracion' => date('Y-m-d H:i:s', strtotime('+24 hours')),
            'activo' => 1,
            'usuario_id' => $_SESSION['user_id']
        ]);
        
        $enlacePago = BASE_URL . '/pago/publico/' . $token;
        
        $this->view('pago/cuota', [
            'pageTitle' => 'Pagar Cuota',
            'amortizacion' => $amortizacion,
            'enlacePago' => $enlacePago,
            'token' => $token
        ]);
    }
    
    /**
     * Public payment page (no authentication required)
     */
    public function publico() {
        $token = $this->params['token'] ?? '';
        
        $tokenData = $this->db->fetch(
            "SELECT tp.*, c.numero_credito, c.saldo_actual,
                    s.numero_socio, s.nombre, s.apellido_paterno
             FROM tokens_pago tp
             JOIN creditos c ON tp.credito_id = c.id
             JOIN socios s ON c.socio_id = s.id
             WHERE tp.token = :token AND tp.activo = 1 AND tp.fecha_expiracion > NOW()",
            ['token' => $token]
        );
        
        if (!$tokenData) {
            $this->view('pago/error', [
                'pageTitle' => 'Enlace Inválido',
                'mensaje' => 'El enlace de pago ha expirado o no es válido.'
            ]);
            return;
        }
        
        // Get PayPal configuration
        $paypalClientId = getConfig('paypal_client_id', '');
        $paypalMode = getConfig('paypal_mode', 'sandbox');
        
        $this->view('pago/publico', [
            'pageTitle' => 'Realizar Pago',
            'tokenData' => $tokenData,
            'paypalClientId' => $paypalClientId,
            'paypalMode' => $paypalMode
        ]);
    }
    
    /**
     * Process PayPal payment
     */
    public function procesar() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $token = $data['token'] ?? '';
        $paypalOrderId = $data['orderID'] ?? '';
        
        $tokenData = $this->db->fetch(
            "SELECT * FROM tokens_pago WHERE token = :token AND activo = 1",
            ['token' => $token]
        );
        
        if (!$tokenData) {
            $this->json(['success' => false, 'message' => 'Token inválido'], 400);
            return;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Register payment
            $pagoId = $this->db->insert('pagos_online', [
                'token_pago_id' => $tokenData['id'],
                'credito_id' => $tokenData['credito_id'],
                'amortizacion_id' => $tokenData['amortizacion_id'],
                'monto' => $tokenData['monto'],
                'paypal_order_id' => $paypalOrderId,
                'estatus' => 'completado',
                'fecha_pago' => date('Y-m-d H:i:s')
            ]);
            
            // If it's a specific installment payment
            if ($tokenData['amortizacion_id']) {
                $amortizacion = $this->db->fetch(
                    "SELECT * FROM amortizacion WHERE id = :id",
                    ['id' => $tokenData['amortizacion_id']]
                );
                
                // Register credit payment
                $this->db->insert('pagos_credito', [
                    'credito_id' => $tokenData['credito_id'],
                    'amortizacion_id' => $tokenData['amortizacion_id'],
                    'monto' => $tokenData['monto'],
                    'monto_capital' => $amortizacion['monto_capital'],
                    'monto_interes' => $amortizacion['monto_interes'],
                    'fecha_pago' => date('Y-m-d H:i:s'),
                    'origen' => 'paypal',
                    'referencia' => $paypalOrderId
                ]);
                
                // Update amortization
                $this->db->update('amortizacion', [
                    'fecha_pago' => date('Y-m-d'),
                    'monto_pagado' => $tokenData['monto'],
                    'estatus' => 'pagado'
                ], 'id = :id', ['id' => $tokenData['amortizacion_id']]);
                
                // Update credit balance
                $credito = $this->db->fetch(
                    "SELECT * FROM creditos WHERE id = :id",
                    ['id' => $tokenData['credito_id']]
                );
                
                $nuevoSaldo = $credito['saldo_actual'] - $amortizacion['monto_capital'];
                $pagosRealizados = $credito['pagos_realizados'] + 1;
                $estatusCredito = $nuevoSaldo <= 0 ? 'liquidado' : 'activo';
                
                $this->db->update('creditos', [
                    'saldo_actual' => max(0, $nuevoSaldo),
                    'pagos_realizados' => $pagosRealizados,
                    'estatus' => $estatusCredito
                ], 'id = :id', ['id' => $tokenData['credito_id']]);
            }
            
            // Deactivate token
            $this->db->update('tokens_pago', ['activo' => 0], 'id = :id', ['id' => $tokenData['id']]);
            
            $this->db->commit();
            
            $this->json(['success' => true, 'message' => 'Pago procesado exitosamente']);
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $this->json(['success' => false, 'message' => 'Error al procesar el pago: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Payment success page
     */
    public function exito() {
        $this->view('pago/exito', [
            'pageTitle' => 'Pago Exitoso'
        ]);
    }
    
    /**
     * Payment cancelled page
     */
    public function cancelado() {
        $this->view('pago/cancelado', [
            'pageTitle' => 'Pago Cancelado'
        ]);
    }
}
