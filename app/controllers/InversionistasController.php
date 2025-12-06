<?php
/**
 * Controlador de Inversionistas
 * Sistema de Gestión Integral de Caja de Ahorros
 */

require_once CORE_PATH . '/Controller.php';

class InversionistasController extends Controller {
    
    public function index() {
        $this->requireRole(['administrador', 'operativo']);
        
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 15;
        $search = $_GET['q'] ?? '';
        $estatus = $_GET['estatus'] ?? '';
        
        $conditions = '1=1';
        $params = [];
        
        if ($search) {
            $conditions .= " AND (i.nombre LIKE :search1 OR i.apellido_paterno LIKE :search2 OR i.rfc LIKE :search3 OR i.email LIKE :search4)";
            $searchTerm = "%{$search}%";
            $params['search1'] = $searchTerm;
            $params['search2'] = $searchTerm;
            $params['search3'] = $searchTerm;
            $params['search4'] = $searchTerm;
        }
        
        if ($estatus) {
            $conditions .= " AND i.estatus = :estatus";
            $params['estatus'] = $estatus;
        }
        
        // Count total
        $total = $this->db->fetch(
            "SELECT COUNT(*) as total FROM inversionistas i WHERE {$conditions}",
            $params
        )['total'];
        
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        
        // Get inversionistas
        $inversionistas = $this->db->fetchAll(
            "SELECT i.*,
                    (SELECT SUM(monto) FROM inversiones WHERE inversionista_id = i.id AND estatus = 'activa') as total_invertido,
                    (SELECT COUNT(*) FROM inversiones WHERE inversionista_id = i.id AND estatus = 'activa') as inversiones_activas
             FROM inversionistas i
             WHERE {$conditions}
             ORDER BY i.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        
        // Get summary stats
        $stats = $this->db->fetch(
            "SELECT 
                COUNT(*) as total_inversionistas,
                (SELECT SUM(monto) FROM inversiones WHERE estatus = 'activa') as total_capital,
                (SELECT COUNT(*) FROM inversiones WHERE estatus = 'activa') as inversiones_activas
             FROM inversionistas WHERE estatus = 'activo'"
        );
        
        $this->view('inversionistas/index', [
            'pageTitle' => 'Gestión de Inversionistas',
            'inversionistas' => $inversionistas,
            'stats' => $stats,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'search' => $search,
            'estatus' => $estatus
        ]);
    }
    
    public function crear() {
        $this->requireRole(['administrador', 'operativo']);
        
        $errors = [];
        $data = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $data = $this->sanitize($_POST);
            
            // Validaciones
            if (empty($data['nombre'])) $errors[] = 'El nombre es requerido';
            if (empty($data['apellido_paterno'])) $errors[] = 'El apellido paterno es requerido';
            if (empty($data['email'])) $errors[] = 'El correo electrónico es requerido';
            
            // Validar RFC único
            if (!empty($data['rfc'])) {
                $exists = $this->db->fetch(
                    "SELECT id FROM inversionistas WHERE rfc = :rfc",
                    ['rfc' => $data['rfc']]
                );
                if ($exists) $errors[] = 'El RFC ya está registrado';
            }
            
            // Validar email único
            if (!empty($data['email'])) {
                $exists = $this->db->fetch(
                    "SELECT id FROM inversionistas WHERE email = :email",
                    ['email' => $data['email']]
                );
                if ($exists) $errors[] = 'El email ya está registrado';
            }
            
            if (empty($errors)) {
                try {
                    // Generate unique investor number
                    $lastInv = $this->db->fetch("SELECT numero_inversionista FROM inversionistas ORDER BY id DESC LIMIT 1");
                    $nextNum = 1;
                    if ($lastInv && preg_match('/INV-(\d+)/', $lastInv['numero_inversionista'], $matches)) {
                        $nextNum = (int)$matches[1] + 1;
                    }
                    $numeroInversionista = 'INV-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
                    
                    $inversionistaId = $this->db->insert('inversionistas', [
                        'numero_inversionista' => $numeroInversionista,
                        'nombre' => $data['nombre'],
                        'apellido_paterno' => $data['apellido_paterno'],
                        'apellido_materno' => $data['apellido_materno'] ?? '',
                        'rfc' => $data['rfc'] ?? '',
                        'curp' => $data['curp'] ?? '',
                        'fecha_nacimiento' => $data['fecha_nacimiento'] ?: null,
                        'telefono' => $data['telefono'] ?? '',
                        'celular' => $data['celular'] ?? '',
                        'email' => $data['email'],
                        'direccion' => $data['direccion'] ?? '',
                        'banco' => $data['banco'] ?? '',
                        'cuenta_bancaria' => $data['cuenta_bancaria'] ?? '',
                        'clabe' => $data['clabe'] ?? '',
                        'fecha_alta' => date('Y-m-d'),
                        'estatus' => 'activo',
                        'notas' => $data['notas'] ?? ''
                    ]);
                    
                    // Create user account for investor if requested
                    if (!empty($data['crear_usuario'])) {
                        $tempPassword = bin2hex(random_bytes(6)); // 12 characters
                        $this->db->insert('usuarios', [
                            'nombre' => $data['nombre'] . ' ' . $data['apellido_paterno'],
                            'email' => $data['email'],
                            'password' => password_hash($tempPassword, PASSWORD_DEFAULT),
                            'rol' => 'inversionista',
                            'activo' => 1,
                            'requiere_cambio_password' => 1
                        ]);
                        
                        $userId = $this->db->lastInsertId();
                        
                        // Link user to investor
                        $this->db->insert('usuarios_inversionistas', [
                            'usuario_id' => $userId,
                            'inversionista_id' => $inversionistaId
                        ]);
                        
                        // Send styled email with credentials
                        $siteName = getSiteName();
                        $loginUrl = BASE_URL . '/auth/login';
                        
                        $emailContent = "<p style='font-size: 16px;'>Hola <strong>{$data['nombre']}</strong>,</p>";
                        $emailContent .= "<p>¡Te damos la bienvenida a <strong>{$siteName}</strong>!</p>";
                        $emailContent .= "<p>Se ha creado una cuenta de inversionista para ti. A continuación encontrarás tus credenciales de acceso:</p>";
                        $emailContent .= "<div style='background-color: #f3f4f6; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
                        $emailContent .= "<p style='margin: 5px 0;'><strong>📧 Correo:</strong> {$data['email']}</p>";
                        $emailContent .= "<p style='margin: 5px 0;'><strong>🔑 Contraseña temporal:</strong> <code style='background-color: #e5e7eb; padding: 2px 8px; border-radius: 4px;'>{$tempPassword}</code></p>";
                        $emailContent .= "</div>";
                        $emailContent .= "<p style='background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px; margin: 20px 0; border-radius: 4px;'>";
                        $emailContent .= "<strong>⚠️ Importante:</strong> Por seguridad, deberás cambiar tu contraseña en el primer inicio de sesión.";
                        $emailContent .= "</p>";
                        $emailContent .= "<p>Haz clic en el botón de abajo para acceder al sistema:</p>";
                        
                        $emailResult = sendStyledEmail(
                            $data['email'],
                            "Bienvenido a {$siteName} - Credenciales de Acceso",
                            "¡Bienvenido Inversionista!",
                            $emailContent,
                            "Iniciar Sesión",
                            $loginUrl
                        );
                        
                        if ($emailResult !== true) {
                            // Log error but continue
                            $this->db->insert('bitacora', [
                                'usuario_id' => $_SESSION['user_id'],
                                'accion' => 'EMAIL_ERROR',
                                'descripcion' => 'Error al enviar credenciales a inversionista: ' . $emailResult,
                                'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
                                'fecha' => date('Y-m-d H:i:s')
                            ]);
                        }
                    }
                    
                    $this->logAction('CREAR_INVERSIONISTA', "Se creó el inversionista {$data['nombre']} {$data['apellido_paterno']}", 'inversionistas', $inversionistaId);
                    
                    $this->setFlash('success', 'Inversionista creado exitosamente');
                    $this->redirect('inversionistas/ver/' . $inversionistaId);
                    
                } catch (Exception $e) {
                    $errors[] = 'Error al crear el inversionista: ' . $e->getMessage();
                }
            }
        }
        
        $this->view('inversionistas/form', [
            'pageTitle' => 'Nuevo Inversionista',
            'action' => 'crear',
            'inversionista' => $data,
            'errors' => $errors
        ]);
    }
    
    public function editar() {
        $this->requireRole(['administrador', 'operativo']);
        
        $id = $this->params['id'] ?? 0;
        $inversionista = $this->db->fetch("SELECT * FROM inversionistas WHERE id = :id", ['id' => $id]);
        
        if (!$inversionista) {
            $this->setFlash('error', 'Inversionista no encontrado');
            $this->redirect('inversionistas');
        }
        
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $data = $this->sanitize($_POST);
            
            // Validaciones
            if (empty($data['nombre'])) $errors[] = 'El nombre es requerido';
            if (empty($data['apellido_paterno'])) $errors[] = 'El apellido paterno es requerido';
            
            // Validar RFC único (excluyendo el actual)
            if (!empty($data['rfc'])) {
                $exists = $this->db->fetch(
                    "SELECT id FROM inversionistas WHERE rfc = :rfc AND id != :id",
                    ['rfc' => $data['rfc'], 'id' => $id]
                );
                if ($exists) $errors[] = 'El RFC ya está registrado';
            }
            
            if (empty($errors)) {
                $this->db->update('inversionistas', [
                    'nombre' => $data['nombre'],
                    'apellido_paterno' => $data['apellido_paterno'],
                    'apellido_materno' => $data['apellido_materno'] ?? '',
                    'rfc' => $data['rfc'] ?? '',
                    'curp' => $data['curp'] ?? '',
                    'fecha_nacimiento' => $data['fecha_nacimiento'] ?: null,
                    'telefono' => $data['telefono'] ?? '',
                    'celular' => $data['celular'] ?? '',
                    'email' => $data['email'] ?? '',
                    'direccion' => $data['direccion'] ?? '',
                    'banco' => $data['banco'] ?? '',
                    'cuenta_bancaria' => $data['cuenta_bancaria'] ?? '',
                    'clabe' => $data['clabe'] ?? '',
                    'estatus' => $data['estatus'] ?? 'activo',
                    'notas' => $data['notas'] ?? ''
                ], 'id = :id', ['id' => $id]);
                
                $this->logAction('EDITAR_INVERSIONISTA', "Se editó el inversionista {$data['nombre']} {$data['apellido_paterno']}", 'inversionistas', $id);
                
                $this->setFlash('success', 'Inversionista actualizado exitosamente');
                $this->redirect('inversionistas/ver/' . $id);
            }
            
            $inversionista = array_merge($inversionista, $data);
        }
        
        $this->view('inversionistas/form', [
            'pageTitle' => 'Editar Inversionista',
            'action' => 'editar',
            'inversionista' => $inversionista,
            'errors' => $errors
        ]);
    }
    
    public function ver() {
        $this->requireAuth();
        
        $id = $this->params['id'] ?? 0;
        
        // If user is inversionista, can only view their own profile
        if ($_SESSION['user_role'] === 'inversionista') {
            $vinculo = $this->db->fetch(
                "SELECT inversionista_id FROM usuarios_inversionistas WHERE usuario_id = :user_id",
                ['user_id' => $_SESSION['user_id']]
            );
            if (!$vinculo || $vinculo['inversionista_id'] != $id) {
                $this->setFlash('error', 'No tiene permiso para ver este perfil');
                $this->redirect('dashboard');
            }
        }
        
        $inversionista = $this->db->fetch(
            "SELECT * FROM inversionistas WHERE id = :id",
            ['id' => $id]
        );
        
        if (!$inversionista) {
            $this->setFlash('error', 'Inversionista no encontrado');
            $this->redirect('inversionistas');
        }
        
        // Get active investments
        $inversiones = $this->db->fetchAll(
            "SELECT inv.*, tc.nombre as tipo_credito, c.numero_credito, s.nombre as socio_nombre, s.apellido_paterno
             FROM inversiones inv
             LEFT JOIN creditos c ON inv.credito_id = c.id
             LEFT JOIN tipos_credito tc ON c.tipo_credito_id = tc.id
             LEFT JOIN socios s ON c.socio_id = s.id
             WHERE inv.inversionista_id = :id
             ORDER BY inv.fecha_inicio DESC",
            ['id' => $id]
        );
        
        // Get returns/payments
        $rendimientos = $this->db->fetchAll(
            "SELECT r.*, inv.numero_inversion
             FROM rendimientos_inversiones r
             JOIN inversiones inv ON r.inversion_id = inv.id
             WHERE inv.inversionista_id = :id
             ORDER BY r.fecha_pago DESC
             LIMIT 20",
            ['id' => $id]
        );
        
        // Calculate totals
        $totalInvertido = 0;
        $totalRendimientos = 0;
        foreach ($inversiones as $inv) {
            if ($inv['estatus'] === 'activa') {
                $totalInvertido += $inv['monto'];
            }
        }
        foreach ($rendimientos as $rend) {
            if ($rend['estatus'] === 'pagado') {
                $totalRendimientos += $rend['monto'];
            }
        }
        
        $this->view('inversionistas/ver', [
            'pageTitle' => 'Detalle de Inversionista',
            'inversionista' => $inversionista,
            'inversiones' => $inversiones,
            'rendimientos' => $rendimientos,
            'totalInvertido' => $totalInvertido,
            'totalRendimientos' => $totalRendimientos
        ]);
    }
    
    public function inversion() {
        $this->requireRole(['administrador', 'operativo']);
        
        $inversionistaId = $this->params['id'] ?? 0;
        $inversionId = $this->params['inversion_id'] ?? 0;
        
        $inversionista = $this->db->fetch("SELECT * FROM inversionistas WHERE id = :id", ['id' => $inversionistaId]);
        
        if (!$inversionista) {
            $this->setFlash('error', 'Inversionista no encontrado');
            $this->redirect('inversionistas');
        }
        
        $inversion = null;
        if ($inversionId) {
            $inversion = $this->db->fetch("SELECT * FROM inversiones WHERE id = :id", ['id' => $inversionId]);
        }
        
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $data = $this->sanitize($_POST);
            
            $monto = (float)($data['monto'] ?? 0);
            $tasaRendimiento = (float)($data['tasa_rendimiento'] ?? 0);
            $fechaInicio = $data['fecha_inicio'] ?? date('Y-m-d');
            $plazoMeses = (int)($data['plazo_meses'] ?? 12);
            $creditoId = (int)($data['credito_id'] ?? 0) ?: null;
            
            if ($monto <= 0) $errors[] = 'El monto debe ser mayor a cero';
            if ($tasaRendimiento <= 0 || $tasaRendimiento > 100) $errors[] = 'La tasa de rendimiento debe ser entre 0 y 100%';
            if ($plazoMeses <= 0) $errors[] = 'El plazo debe ser mayor a cero';
            
            if (empty($errors)) {
                // Generate investment number
                $lastInv = $this->db->fetch("SELECT numero_inversion FROM inversiones ORDER BY id DESC LIMIT 1");
                $nextNum = 1;
                if ($lastInv && preg_match('/INVE-(\d+)/', $lastInv['numero_inversion'], $matches)) {
                    $nextNum = (int)$matches[1] + 1;
                }
                $numeroInversion = 'INVE-' . str_pad($nextNum, 6, '0', STR_PAD_LEFT);
                
                $fechaFin = date('Y-m-d', strtotime($fechaInicio . " + {$plazoMeses} months"));
                
                $inversionData = [
                    'inversionista_id' => $inversionistaId,
                    'numero_inversion' => $numeroInversion,
                    'monto' => $monto,
                    'tasa_rendimiento' => $tasaRendimiento / 100, // Convert to decimal
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin' => $fechaFin,
                    'plazo_meses' => $plazoMeses,
                    'credito_id' => $creditoId,
                    'estatus' => 'activa',
                    'notas' => $data['notas'] ?? ''
                ];
                
                if ($inversionId) {
                    $this->db->update('inversiones', $inversionData, 'id = :id', ['id' => $inversionId]);
                    $this->logAction('EDITAR_INVERSION', "Se editó inversión {$numeroInversion}", 'inversiones', $inversionId);
                    $this->setFlash('success', 'Inversión actualizada exitosamente');
                } else {
                    $newId = $this->db->insert('inversiones', $inversionData);
                    $this->logAction('CREAR_INVERSION', "Se creó inversión {$numeroInversion} por \${$monto}", 'inversiones', $newId);
                    $this->setFlash('success', 'Inversión registrada exitosamente');
                }
                
                $this->redirect('inversionistas/ver/' . $inversionistaId);
            }
        }
        
        // Get available credits for linking
        $creditos = $this->db->fetchAll(
            "SELECT c.id, c.numero_credito, c.monto_autorizado, tc.nombre as tipo, 
                    CONCAT(s.nombre, ' ', s.apellido_paterno) as socio
             FROM creditos c
             JOIN tipos_credito tc ON c.tipo_credito_id = tc.id
             JOIN socios s ON c.socio_id = s.id
             WHERE c.estatus IN ('activo', 'formalizado')
             ORDER BY c.numero_credito DESC
             LIMIT 100"
        );
        
        $this->view('inversionistas/inversion', [
            'pageTitle' => $inversionId ? 'Editar Inversión' : 'Nueva Inversión',
            'inversionista' => $inversionista,
            'inversion' => $inversion,
            'creditos' => $creditos,
            'errors' => $errors
        ]);
    }
    
    public function buscar() {
        $this->requireAuth();
        
        $q = $_GET['q'] ?? '';
        
        if (strlen($q) < 2) {
            $this->json(['results' => []]);
        }
        
        $searchTerm = "%{$q}%";
        $results = $this->db->fetchAll(
            "SELECT id, numero_inversionista, nombre, apellido_paterno, apellido_materno, rfc, email
             FROM inversionistas
             WHERE estatus = 'activo' AND (
                nombre LIKE :q1 OR 
                apellido_paterno LIKE :q2 OR 
                rfc LIKE :q3 OR 
                email LIKE :q4 OR
                numero_inversionista LIKE :q5
             )
             LIMIT 10",
            ['q1' => $searchTerm, 'q2' => $searchTerm, 'q3' => $searchTerm, 'q4' => $searchTerm, 'q5' => $searchTerm]
        );
        
        $this->json(['results' => $results]);
    }
}
