<?php
/**
 * Controlador de Consulta Pública al Buró de Crédito
 * Sistema de Gestión Integral de Caja de Ahorros
 */

require_once CORE_PATH . '/Controller.php';

class BuroController extends Controller {
    
    /**
     * Vista pública de consulta al Buró de Crédito
     */
    public function consulta() {
        $buroEnabled = getConfig('buro_api_enabled', '0') === '1';
        $costoConsulta = floatval(getConfig('buro_costo_consulta', '50'));
        $paypalClientId = getConfig('paypal_client_id', '');
        $paypalEnabled = getConfig('paypal_enabled', '0') === '1';
        
        $errors = [];
        $token = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipoConsulta = $_POST['tipo_consulta'] ?? 'rfc';
            $identificador = strtoupper(trim($this->sanitize($_POST['identificador'] ?? '')));
            $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
            
            // Validaciones
            if (!in_array($tipoConsulta, ['rfc', 'curp'])) {
                $errors[] = 'Tipo de consulta inválido';
            }
            
            if (empty($identificador)) {
                $errors[] = 'El identificador (RFC o CURP) es requerido';
            } else {
                if ($tipoConsulta === 'rfc' && !$this->validarRfc($identificador)) {
                    $errors[] = 'El RFC ingresado no tiene un formato válido';
                }
                if ($tipoConsulta === 'curp' && !$this->validarCurp($identificador)) {
                    $errors[] = 'El CURP ingresado no tiene un formato válido';
                }
            }
            
            if (!$email) {
                $errors[] = 'El correo electrónico es requerido y debe ser válido';
            }
            
            if (!$buroEnabled) {
                $errors[] = 'El servicio de consulta al Buró de Crédito no está disponible';
            }
            
            if (!$paypalEnabled || empty($paypalClientId)) {
                $errors[] = 'El sistema de pagos no está configurado';
            }
            
            if (empty($errors)) {
                // Crear token único para la consulta
                $token = bin2hex(random_bytes(32));
                
                // Registrar la consulta pendiente de pago
                $consultaId = $this->db->insert('consultas_buro', [
                    'tipo_consulta' => $tipoConsulta,
                    'identificador' => $identificador,
                    'costo' => $costoConsulta,
                    'token_consulta' => $token,
                    'email_solicitante' => $email,
                    'ip_solicitante' => $_SERVER['REMOTE_ADDR'] ?? '',
                    'estatus' => 'pendiente_pago'
                ]);
                
                // Redirigir a la página de pago
                $this->redirect('buro/pagar?token=' . $token);
            }
        }
        
        $this->viewPublico('buro/consulta', [
            'pageTitle' => 'Consulta Buró de Crédito',
            'buroEnabled' => $buroEnabled,
            'costoConsulta' => $costoConsulta,
            'paypalEnabled' => $paypalEnabled,
            'errors' => $errors
        ]);
    }
    
    /**
     * Página de pago para la consulta
     */
    public function pagar() {
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            $this->redirect('buro/consulta');
        }
        
        $consulta = $this->db->fetch(
            "SELECT * FROM consultas_buro WHERE token_consulta = :token AND estatus = 'pendiente_pago'",
            ['token' => $token]
        );
        
        if (!$consulta) {
            $this->viewPublico('buro/error', [
                'pageTitle' => 'Error',
                'mensaje' => 'La consulta no existe o ya fue procesada.'
            ]);
            return;
        }
        
        $paypalClientId = getConfig('paypal_client_id', '');
        $paypalMode = getConfig('paypal_mode', 'sandbox');
        
        $this->viewPublico('buro/pagar', [
            'pageTitle' => 'Pago Consulta Buró de Crédito',
            'consulta' => $consulta,
            'paypalClientId' => $paypalClientId,
            'paypalMode' => $paypalMode
        ]);
    }
    
    /**
     * Procesar pago y realizar consulta al Buró
     */
    public function procesar() {
        header('Content-Type: application/json');
        
        $data = json_decode(file_get_contents('php://input'), true);
        $token = $data['token'] ?? '';
        $paypalOrderId = $data['orderID'] ?? '';
        
        if (empty($token) || empty($paypalOrderId)) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit;
        }
        
        $consulta = $this->db->fetch(
            "SELECT * FROM consultas_buro WHERE token_consulta = :token AND estatus = 'pendiente_pago'",
            ['token' => $token]
        );
        
        if (!$consulta) {
            echo json_encode(['success' => false, 'message' => 'Consulta no encontrada o ya procesada']);
            exit;
        }
        
        $this->db->beginTransaction();
        
        try {
            // Actualizar estado a pagado
            $this->db->update('consultas_buro', [
                'paypal_order_id' => $paypalOrderId,
                'estatus' => 'pagado',
                'fecha_pago' => date('Y-m-d H:i:s')
            ], 'id = :id', ['id' => $consulta['id']]);
            
            // Realizar consulta al Buró de Crédito
            $resultadoBuro = $this->consultarBuro(
                $consulta['tipo_consulta'],
                $consulta['identificador']
            );
            
            if ($resultadoBuro['success']) {
                $this->db->update('consultas_buro', [
                    'nombre_consultado' => $resultadoBuro['nombre'] ?? null,
                    'resultado_score' => $resultadoBuro['score'] ?? null,
                    'resultado_json' => json_encode($resultadoBuro['data']),
                    'estatus' => 'consultado',
                    'fecha_consulta' => date('Y-m-d H:i:s')
                ], 'id = :id', ['id' => $consulta['id']]);
                
                $this->db->commit();
                
                // Enviar email con resultados (opcional)
                // $this->enviarResultadosEmail($consulta['email_solicitante'], $resultadoBuro);
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Consulta realizada exitosamente',
                    'redirect' => BASE_URL . '/buro/resultado/' . $token
                ]);
            } else {
                $this->db->update('consultas_buro', [
                    'estatus' => 'error',
                    'error_mensaje' => $resultadoBuro['message']
                ], 'id = :id', ['id' => $consulta['id']]);
                
                $this->db->commit();
                
                echo json_encode([
                    'success' => false, 
                    'message' => 'Error al consultar el Buró: ' . $resultadoBuro['message']
                ]);
            }
            
        } catch (Exception $e) {
            $this->db->rollBack();
            echo json_encode(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
        }
        
        exit;
    }
    
    /**
     * Mostrar resultados de la consulta
     */
    public function resultado() {
        $token = $this->params['token'] ?? '';
        
        if (empty($token)) {
            $this->redirect('buro/consulta');
        }
        
        $consulta = $this->db->fetch(
            "SELECT * FROM consultas_buro WHERE token_consulta = :token",
            ['token' => $token]
        );
        
        if (!$consulta) {
            $this->viewPublico('buro/error', [
                'pageTitle' => 'Error',
                'mensaje' => 'Consulta no encontrada.'
            ]);
            return;
        }
        
        if ($consulta['estatus'] === 'pendiente_pago') {
            $this->redirect('buro/pagar?token=' . $token);
        }
        
        $resultado = null;
        if ($consulta['resultado_json']) {
            $resultado = json_decode($consulta['resultado_json'], true);
        }
        
        $this->viewPublico('buro/resultado', [
            'pageTitle' => 'Resultado Consulta Buró de Crédito',
            'consulta' => $consulta,
            'resultado' => $resultado
        ]);
    }
    
    /**
     * Realizar consulta al API del Buró de Crédito
     */
    private function consultarBuro($tipoConsulta, $identificador) {
        $apiUrl = getConfig('buro_api_url', 'https://apif.burodecredito.com.mx');
        $apiUsername = getConfig('buro_api_username', '');
        $apiPassword = getConfig('buro_api_password', '');
        $apiKey = getConfig('buro_api_key', '');
        
        // Si no hay credenciales configuradas, simular respuesta
        if (empty($apiUsername) || empty($apiPassword)) {
            // Modo demo/simulación
            return $this->simularConsultaBuro($tipoConsulta, $identificador);
        }
        
        try {
            // Construir petición al API real del Buró
            $endpoint = $apiUrl . '/api/v1/consulta';
            
            $postData = [
                'tipo' => $tipoConsulta,
                'identificador' => $identificador
            ];
            
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $endpoint,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($postData),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Basic ' . base64_encode($apiUsername . ':' . $apiPassword),
                    'X-API-Key: ' . $apiKey
                ],
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => true
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($error) {
                return ['success' => false, 'message' => 'Error de conexión: ' . $error];
            }
            
            if ($httpCode !== 200) {
                return ['success' => false, 'message' => 'Error del servidor: HTTP ' . $httpCode];
            }
            
            $data = json_decode($response, true);
            
            if (!$data || isset($data['error'])) {
                return ['success' => false, 'message' => $data['error'] ?? 'Respuesta inválida del servidor'];
            }
            
            return [
                'success' => true,
                'nombre' => $data['nombre'] ?? null,
                'score' => $data['score'] ?? null,
                'data' => $data
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al consultar: ' . $e->getMessage()];
        }
    }
    
    /**
     * Simular respuesta del Buró para modo demo
     */
    private function simularConsultaBuro($tipoConsulta, $identificador) {
        // Generar datos simulados para demostración
        $scores = [580, 620, 680, 720, 750, 800];
        $score = $scores[array_rand($scores)];
        
        $nombres = [
            'JUAN CARLOS GARCIA LOPEZ',
            'MARIA FERNANDA MARTINEZ RUIZ',
            'ROBERTO HERNANDEZ SANCHEZ',
            'ANA PATRICIA TORRES MENDEZ'
        ];
        $nombre = $nombres[array_rand($nombres)];
        
        $clasificaciones = [
            ['bajo' => 580, 'medio' => 650, 'alto' => 720],
        ];
        
        $nivelRiesgo = 'bajo';
        if ($score < 650) $nivelRiesgo = 'alto';
        elseif ($score < 720) $nivelRiesgo = 'medio';
        
        return [
            'success' => true,
            'nombre' => $nombre,
            'score' => $score,
            'data' => [
                'identificador' => $identificador,
                'tipo_consulta' => $tipoConsulta,
                'nombre_completo' => $nombre,
                'score_crediticio' => $score,
                'nivel_riesgo' => $nivelRiesgo,
                'cuentas_activas' => rand(1, 5),
                'cuentas_cerradas' => rand(0, 3),
                'creditos_vigentes' => rand(0, 3),
                'monto_total_deuda' => rand(5000, 150000),
                'pagos_puntuales' => rand(85, 100) . '%',
                'historial_meses' => rand(12, 84),
                'fecha_consulta' => date('Y-m-d H:i:s'),
                'consultas_recientes' => rand(1, 5),
                'alertas' => [],
                'mensaje' => 'Consulta realizada en modo demostración. Configure las credenciales del API para consultas reales.'
            ]
        ];
    }
    
    /**
     * Validar formato de RFC
     */
    private function validarRfc($rfc) {
        // RFC persona física: 13 caracteres
        // RFC persona moral: 12 caracteres
        $pattern = '/^[A-ZÑ&]{3,4}[0-9]{6}[A-Z0-9]{3}$/i';
        return preg_match($pattern, $rfc);
    }
    
    /**
     * Validar formato de CURP
     */
    private function validarCurp($curp) {
        // CURP: 18 caracteres
        $pattern = '/^[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9A-Z]{2}$/i';
        return preg_match($pattern, $curp);
    }
    
    /**
     * Renderizar vista pública (sin layout administrativo)
     */
    protected function viewPublico($view, $data = []) {
        extract($data);
        $viewFile = APP_PATH . '/views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            throw new Exception("Vista {$view} no encontrada");
        }
        exit;
    }
}
