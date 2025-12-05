<?php
/**
 * Controlador de Socios
 * Sistema de Gestión Integral de Caja de Ahorros
 */

require_once CORE_PATH . '/Controller.php';

class SociosController extends Controller {
    
    public function index() {
        $this->requireAuth();
        
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 15;
        $search = $_GET['q'] ?? '';
        $estatus = $_GET['estatus'] ?? '';
        
        $conditions = '1=1';
        $params = [];
        
        if ($search) {
            $conditions .= " AND (s.nombre LIKE :search1 OR s.apellido_paterno LIKE :search2 OR s.rfc LIKE :search3 OR s.curp LIKE :search4 OR s.numero_socio LIKE :search5)";
            $searchTerm = "%{$search}%";
            $params['search1'] = $searchTerm;
            $params['search2'] = $searchTerm;
            $params['search3'] = $searchTerm;
            $params['search4'] = $searchTerm;
            $params['search5'] = $searchTerm;
        }
        
        if ($estatus) {
            $conditions .= " AND s.estatus = :estatus";
            $params['estatus'] = $estatus;
        }
        
        // Count total
        $total = $this->db->fetch(
            "SELECT COUNT(*) as total FROM socios s WHERE {$conditions}",
            $params
        )['total'];
        
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        
        // Get socios
        $socios = $this->db->fetchAll(
            "SELECT s.*, ut.nombre as unidad_trabajo,
                    (SELECT saldo FROM cuentas_ahorro WHERE socio_id = s.id AND estatus = 'activa' LIMIT 1) as saldo_ahorro,
                    (SELECT COUNT(*) FROM creditos WHERE socio_id = s.id AND estatus IN ('activo', 'formalizado')) as creditos_activos
             FROM socios s
             LEFT JOIN unidades_trabajo ut ON s.unidad_trabajo_id = ut.id
             WHERE {$conditions}
             ORDER BY s.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        
        $this->view('socios/index', [
            'pageTitle' => 'Gestión de Socios',
            'socios' => $socios,
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
        
        // Get unidades de trabajo
        $unidades = $this->db->fetchAll("SELECT * FROM unidades_trabajo WHERE activo = 1 ORDER BY nombre");
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $data = $this->sanitize($_POST);
            
            // Validaciones
            if (empty($data['nombre'])) $errors[] = 'El nombre es requerido';
            if (empty($data['apellido_paterno'])) $errors[] = 'El apellido paterno es requerido';
            
            // Validar RFC único
            if (!empty($data['rfc'])) {
                $exists = $this->db->fetch(
                    "SELECT id FROM socios WHERE rfc = :rfc",
                    ['rfc' => $data['rfc']]
                );
                if ($exists) $errors[] = 'El RFC ya está registrado';
            }
            
            // Validar CURP único
            if (!empty($data['curp'])) {
                $exists = $this->db->fetch(
                    "SELECT id FROM socios WHERE curp = :curp",
                    ['curp' => $data['curp']]
                );
                if ($exists) $errors[] = 'El CURP ya está registrado';
            }
            
            if (empty($errors)) {
                try {
                    $this->db->beginTransaction();
                    
                    // Generar número de socio
                    $lastSocio = $this->db->fetch("SELECT numero_socio FROM socios ORDER BY id DESC LIMIT 1");
                    $nextNum = 1;
                    if ($lastSocio && preg_match('/SOC-(\d+)/', $lastSocio['numero_socio'], $matches)) {
                        $nextNum = (int)$matches[1] + 1;
                    }
                    $numeroSocio = 'SOC-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
                    
                    $socioId = $this->db->insert('socios', [
                        'numero_socio' => $numeroSocio,
                        'nombre' => $data['nombre'],
                        'apellido_paterno' => $data['apellido_paterno'],
                        'apellido_materno' => $data['apellido_materno'] ?? '',
                        'rfc' => $data['rfc'] ?? '',
                        'curp' => $data['curp'] ?? '',
                        'fecha_nacimiento' => $data['fecha_nacimiento'] ?: null,
                        'genero' => $data['genero'] ?? null,
                        'estado_civil' => $data['estado_civil'] ?? null,
                        'telefono' => $data['telefono'] ?? '',
                        'celular' => $data['celular'] ?? '',
                        'email' => $data['email'] ?? '',
                        'direccion' => $data['direccion'] ?? '',
                        'colonia' => $data['colonia'] ?? '',
                        'municipio' => $data['municipio'] ?? '',
                        'estado' => $data['estado'] ?? 'Querétaro',
                        'codigo_postal' => $data['codigo_postal'] ?? '',
                        'unidad_trabajo_id' => $data['unidad_trabajo_id'] ?: null,
                        'puesto' => $data['puesto'] ?? '',
                        'numero_empleado' => $data['numero_empleado'] ?? '',
                        'fecha_ingreso_trabajo' => $data['fecha_ingreso_trabajo'] ?: null,
                        'salario_mensual' => $data['salario_mensual'] ?: null,
                        'fecha_alta' => date('Y-m-d'),
                        'estatus' => 'activo',
                        'beneficiario_nombre' => $data['beneficiario_nombre'] ?? '',
                        'beneficiario_parentesco' => $data['beneficiario_parentesco'] ?? '',
                        'beneficiario_telefono' => $data['beneficiario_telefono'] ?? '',
                        'observaciones' => $data['observaciones'] ?? ''
                    ]);
                    
                    // Handle file upload for identificacion_oficial
                    if (isset($_FILES['identificacion_oficial']) && $_FILES['identificacion_oficial']['error'] === UPLOAD_ERR_OK) {
                        $file = $_FILES['identificacion_oficial'];
                        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                        $maxSize = 5 * 1024 * 1024; // 5MB
                        
                        $fileType = mime_content_type($file['tmp_name']);
                        if (in_array($fileType, $allowedTypes) && $file['size'] <= $maxSize) {
                            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                            $nombreArchivo = 'id_' . $socioId . '_' . time() . '.' . $ext;
                            
                            $uploadDir = UPLOADS_PATH . '/identificaciones';
                            if (!is_dir($uploadDir)) {
                                mkdir($uploadDir, 0755, true);
                            }
                            
                            $ruta = $uploadDir . '/' . $nombreArchivo;
                            if (move_uploaded_file($file['tmp_name'], $ruta)) {
                                $this->db->update('socios', [
                                    'identificacion_oficial' => $nombreArchivo
                                ], 'id = :id', ['id' => $socioId]);
                            }
                        }
                    }
                    
                    // Crear cuenta de ahorro automáticamente
                    $lastCuenta = $this->db->fetch("SELECT numero_cuenta FROM cuentas_ahorro ORDER BY id DESC LIMIT 1");
                    $nextCuentaNum = 1;
                    if ($lastCuenta && preg_match('/AHO-(\d+)/', $lastCuenta['numero_cuenta'], $matches)) {
                        $nextCuentaNum = (int)$matches[1] + 1;
                    }
                    $numeroCuenta = 'AHO-' . str_pad($nextCuentaNum, 4, '0', STR_PAD_LEFT);
                    
                    $this->db->insert('cuentas_ahorro', [
                        'socio_id' => $socioId,
                        'numero_cuenta' => $numeroCuenta,
                        'saldo' => 0,
                        'tasa_interes' => 0.03,
                        'fecha_apertura' => date('Y-m-d'),
                        'estatus' => 'activa'
                    ]);
                    
                    $this->db->commit();
                    
                    $this->logAction('CREAR_SOCIO', "Se creó el socio {$data['nombre']} {$data['apellido_paterno']}", 'socios', $socioId);
                    
                    $this->setFlash('success', 'Socio creado exitosamente');
                    $this->redirect('socios/ver/' . $socioId);
                    
                } catch (Exception $e) {
                    $this->db->rollBack();
                    $errors[] = 'Error al crear el socio: ' . $e->getMessage();
                }
            }
        }
        
        $this->view('socios/form', [
            'pageTitle' => 'Nuevo Socio',
            'action' => 'crear',
            'socio' => $data,
            'unidades' => $unidades,
            'errors' => $errors
        ]);
    }
    
    public function editar() {
        $this->requireRole(['administrador', 'operativo']);
        
        $id = $this->params['id'] ?? 0;
        $socio = $this->db->fetch("SELECT * FROM socios WHERE id = :id", ['id' => $id]);
        
        if (!$socio) {
            $this->setFlash('error', 'Socio no encontrado');
            $this->redirect('socios');
        }
        
        $errors = [];
        $unidades = $this->db->fetchAll("SELECT * FROM unidades_trabajo WHERE activo = 1 ORDER BY nombre");
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $data = $this->sanitize($_POST);
            
            // Validaciones
            if (empty($data['nombre'])) $errors[] = 'El nombre es requerido';
            if (empty($data['apellido_paterno'])) $errors[] = 'El apellido paterno es requerido';
            
            // Validar RFC único (excluyendo el actual)
            if (!empty($data['rfc'])) {
                $exists = $this->db->fetch(
                    "SELECT id FROM socios WHERE rfc = :rfc AND id != :id",
                    ['rfc' => $data['rfc'], 'id' => $id]
                );
                if ($exists) $errors[] = 'El RFC ya está registrado';
            }
            
            // Validar CURP único
            if (!empty($data['curp'])) {
                $exists = $this->db->fetch(
                    "SELECT id FROM socios WHERE curp = :curp AND id != :id",
                    ['curp' => $data['curp'], 'id' => $id]
                );
                if ($exists) $errors[] = 'El CURP ya está registrado';
            }
            
            if (empty($errors)) {
                // Registrar cambios en historial
                $campos = ['nombre', 'apellido_paterno', 'apellido_materno', 'rfc', 'curp', 'telefono', 'email', 'direccion', 'estatus', 'observaciones'];
                foreach ($campos as $campo) {
                    if (isset($data[$campo]) && $data[$campo] !== ($socio[$campo] ?? '')) {
                        $this->db->insert('socios_historial', [
                            'socio_id' => $id,
                            'usuario_id' => $_SESSION['user_id'],
                            'campo_modificado' => $campo,
                            'valor_anterior' => $socio[$campo] ?? '',
                            'valor_nuevo' => $data[$campo]
                        ]);
                    }
                }
                
                // Handle file upload for identificacion_oficial
                $identificacionOficial = $socio['identificacion_oficial'] ?? '';
                if (isset($_FILES['identificacion_oficial']) && $_FILES['identificacion_oficial']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES['identificacion_oficial'];
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                    $maxSize = 5 * 1024 * 1024; // 5MB
                    
                    $fileType = mime_content_type($file['tmp_name']);
                    if (in_array($fileType, $allowedTypes) && $file['size'] <= $maxSize) {
                        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                        $nombreArchivo = 'id_' . $id . '_' . time() . '.' . $ext;
                        
                        $uploadDir = UPLOADS_PATH . '/identificaciones';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }
                        
                        $ruta = $uploadDir . '/' . $nombreArchivo;
                        if (move_uploaded_file($file['tmp_name'], $ruta)) {
                            // Delete old file if exists
                            if ($identificacionOficial && file_exists(UPLOADS_PATH . '/identificaciones/' . $identificacionOficial)) {
                                unlink(UPLOADS_PATH . '/identificaciones/' . $identificacionOficial);
                            }
                            $identificacionOficial = $nombreArchivo;
                        }
                    }
                }
                
                $this->db->update('socios', [
                    'nombre' => $data['nombre'],
                    'apellido_paterno' => $data['apellido_paterno'],
                    'apellido_materno' => $data['apellido_materno'] ?? '',
                    'rfc' => $data['rfc'] ?? '',
                    'curp' => $data['curp'] ?? '',
                    'fecha_nacimiento' => $data['fecha_nacimiento'] ?: null,
                    'genero' => $data['genero'] ?? null,
                    'estado_civil' => $data['estado_civil'] ?? null,
                    'telefono' => $data['telefono'] ?? '',
                    'celular' => $data['celular'] ?? '',
                    'email' => $data['email'] ?? '',
                    'direccion' => $data['direccion'] ?? '',
                    'colonia' => $data['colonia'] ?? '',
                    'municipio' => $data['municipio'] ?? '',
                    'estado' => $data['estado'] ?? 'Querétaro',
                    'codigo_postal' => $data['codigo_postal'] ?? '',
                    'unidad_trabajo_id' => $data['unidad_trabajo_id'] ?: null,
                    'puesto' => $data['puesto'] ?? '',
                    'numero_empleado' => $data['numero_empleado'] ?? '',
                    'fecha_ingreso_trabajo' => $data['fecha_ingreso_trabajo'] ?: null,
                    'salario_mensual' => $data['salario_mensual'] ?: null,
                    'estatus' => $data['estatus'] ?? 'activo',
                    'beneficiario_nombre' => $data['beneficiario_nombre'] ?? '',
                    'beneficiario_parentesco' => $data['beneficiario_parentesco'] ?? '',
                    'beneficiario_telefono' => $data['beneficiario_telefono'] ?? '',
                    'observaciones' => $data['observaciones'] ?? '',
                    'identificacion_oficial' => $identificacionOficial
                ], 'id = :id', ['id' => $id]);
                
                $this->logAction('EDITAR_SOCIO', "Se editó el socio {$data['nombre']} {$data['apellido_paterno']}", 'socios', $id);
                
                $this->setFlash('success', 'Socio actualizado exitosamente');
                $this->redirect('socios/ver/' . $id);
            }
            
            $socio = array_merge($socio, $data);
        }
        
        $this->view('socios/form', [
            'pageTitle' => 'Editar Socio',
            'action' => 'editar',
            'socio' => $socio,
            'unidades' => $unidades,
            'errors' => $errors
        ]);
    }
    
    public function ver() {
        $this->requireAuth();
        
        $id = $this->params['id'] ?? 0;
        
        $socio = $this->db->fetch(
            "SELECT s.*, ut.nombre as unidad_trabajo
             FROM socios s
             LEFT JOIN unidades_trabajo ut ON s.unidad_trabajo_id = ut.id
             WHERE s.id = :id",
            ['id' => $id]
        );
        
        if (!$socio) {
            $this->setFlash('error', 'Socio no encontrado');
            $this->redirect('socios');
        }
        
        // Obtener cuenta de ahorro
        $cuentaAhorro = $this->db->fetch(
            "SELECT * FROM cuentas_ahorro WHERE socio_id = :id",
            ['id' => $id]
        );
        
        // Obtener últimos movimientos de ahorro
        $movimientosAhorro = [];
        if ($cuentaAhorro) {
            $movimientosAhorro = $this->db->fetchAll(
                "SELECT * FROM movimientos_ahorro WHERE cuenta_id = :cuenta_id ORDER BY fecha DESC LIMIT 10",
                ['cuenta_id' => $cuentaAhorro['id']]
            );
        }
        
        // Obtener créditos
        $creditos = $this->db->fetchAll(
            "SELECT c.*, tc.nombre as tipo_credito
             FROM creditos c
             JOIN tipos_credito tc ON c.tipo_credito_id = tc.id
             WHERE c.socio_id = :id
             ORDER BY c.created_at DESC",
            ['id' => $id]
        );
        
        $this->view('socios/ver', [
            'pageTitle' => 'Detalle de Socio',
            'socio' => $socio,
            'cuentaAhorro' => $cuentaAhorro,
            'movimientosAhorro' => $movimientosAhorro,
            'creditos' => $creditos
        ]);
    }
    
    public function eliminar() {
        $this->requireRole(['administrador']);
        
        $id = $this->params['id'] ?? 0;
        
        $socio = $this->db->fetch("SELECT * FROM socios WHERE id = :id", ['id' => $id]);
        
        if (!$socio) {
            $this->setFlash('error', 'Socio no encontrado');
            $this->redirect('socios');
        }
        
        // Verificar si tiene créditos activos
        $creditosActivos = $this->db->fetch(
            "SELECT COUNT(*) as total FROM creditos WHERE socio_id = :id AND estatus IN ('activo', 'formalizado')",
            ['id' => $id]
        )['total'];
        
        if ($creditosActivos > 0) {
            $this->setFlash('error', 'No se puede eliminar un socio con créditos activos');
            $this->redirect('socios/ver/' . $id);
        }
        
        // Cambiar estatus a baja en lugar de eliminar
        $this->db->update('socios', [
            'estatus' => 'baja',
            'fecha_baja' => date('Y-m-d'),
            'motivo_baja' => 'Eliminado por administrador'
        ], 'id = :id', ['id' => $id]);
        
        $this->logAction('BAJA_SOCIO', "Se dio de baja al socio {$socio['nombre']} {$socio['apellido_paterno']}", 'socios', $id);
        
        $this->setFlash('success', 'Socio dado de baja exitosamente');
        $this->redirect('socios');
    }
    
    public function historial() {
        $this->requireAuth();
        
        $id = $this->params['id'] ?? 0;
        
        $socio = $this->db->fetch("SELECT * FROM socios WHERE id = :id", ['id' => $id]);
        
        if (!$socio) {
            $this->setFlash('error', 'Socio no encontrado');
            $this->redirect('socios');
        }
        
        $historial = $this->db->fetchAll(
            "SELECT h.*, u.nombre as usuario_nombre
             FROM socios_historial h
             LEFT JOIN usuarios u ON h.usuario_id = u.id
             WHERE h.socio_id = :id
             ORDER BY h.fecha DESC",
            ['id' => $id]
        );
        
        $this->view('socios/historial', [
            'pageTitle' => 'Historial de Cambios',
            'socio' => $socio,
            'historial' => $historial
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
            "SELECT id, numero_socio, nombre, apellido_paterno, apellido_materno, rfc, curp
             FROM socios
             WHERE estatus = 'activo' AND (
                nombre LIKE :q1 OR 
                apellido_paterno LIKE :q2 OR 
                rfc LIKE :q3 OR 
                curp LIKE :q4 OR 
                numero_socio LIKE :q5
             )
             LIMIT 10",
            ['q1' => $searchTerm, 'q2' => $searchTerm, 'q3' => $searchTerm, 'q4' => $searchTerm, 'q5' => $searchTerm]
        );
        
        $this->json(['results' => $results]);
    }
    
    public function estadoCuenta() {
        $this->requireAuth();
        
        $id = $this->params['id'] ?? 0;
        
        // Get month and year from query params or use current
        $mes = isset($_GET['mes']) ? (int)$_GET['mes'] : (int)date('m');
        $anio = isset($_GET['anio']) ? (int)$_GET['anio'] : (int)date('Y');
        
        // Validate ranges
        if ($mes < 1 || $mes > 12) $mes = (int)date('m');
        if ($anio < 2000 || $anio > (int)date('Y') + 1) $anio = (int)date('Y');
        
        $socio = $this->db->fetch(
            "SELECT s.*, ut.nombre as unidad_trabajo
             FROM socios s
             LEFT JOIN unidades_trabajo ut ON s.unidad_trabajo_id = ut.id
             WHERE s.id = :id",
            ['id' => $id]
        );
        
        if (!$socio) {
            $this->setFlash('error', 'Socio no encontrado');
            $this->redirect('socios');
        }
        
        // Get savings account
        $cuentaAhorro = $this->db->fetch(
            "SELECT * FROM cuentas_ahorro WHERE socio_id = :id",
            ['id' => $id]
        );
        
        // Get movements for the selected month
        $movimientosAhorro = [];
        if ($cuentaAhorro) {
            $movimientosAhorro = $this->db->fetchAll(
                "SELECT * FROM movimientos_ahorro 
                 WHERE cuenta_id = :cuenta_id 
                 AND MONTH(fecha) = :mes AND YEAR(fecha) = :anio
                 ORDER BY fecha ASC",
                ['cuenta_id' => $cuentaAhorro['id'], 'mes' => $mes, 'anio' => $anio]
            );
        }
        
        // Get credits
        $creditos = $this->db->fetchAll(
            "SELECT c.*, tc.nombre as tipo_credito
             FROM creditos c
             JOIN tipos_credito tc ON c.tipo_credito_id = tc.id
             WHERE c.socio_id = :id AND c.estatus IN ('activo', 'formalizado')
             ORDER BY c.created_at DESC",
            ['id' => $id]
        );
        
        // Get credit payments for the month
        $pagosCredito = [];
        foreach ($creditos as $credito) {
            $pagos = $this->db->fetchAll(
                "SELECT pc.*, a.numero_pago 
                 FROM pagos_credito pc
                 LEFT JOIN amortizacion a ON pc.amortizacion_id = a.id
                 WHERE pc.credito_id = :credito_id
                 AND MONTH(pc.fecha_pago) = :mes AND YEAR(pc.fecha_pago) = :anio
                 ORDER BY pc.fecha_pago ASC",
                ['credito_id' => $credito['id'], 'mes' => $mes, 'anio' => $anio]
            );
            if (!empty($pagos)) {
                $pagosCredito[$credito['id']] = $pagos;
            }
        }
        
        // Get amortization pending for credits
        $amortizacionPendiente = [];
        foreach ($creditos as $credito) {
            $pendientes = $this->db->fetchAll(
                "SELECT * FROM amortizacion 
                 WHERE credito_id = :credito_id 
                 AND estatus IN ('pendiente', 'vencido')
                 ORDER BY numero_pago LIMIT 3",
                ['credito_id' => $credito['id']]
            );
            if (!empty($pendientes)) {
                $amortizacionPendiente[$credito['id']] = $pendientes;
            }
        }
        
        $this->view('socios/estado_cuenta', [
            'pageTitle' => 'Estado de Cuenta',
            'socio' => $socio,
            'cuentaAhorro' => $cuentaAhorro,
            'movimientosAhorro' => $movimientosAhorro,
            'creditos' => $creditos,
            'pagosCredito' => $pagosCredito,
            'amortizacionPendiente' => $amortizacionPendiente,
            'mes' => $mes,
            'anio' => $anio
        ]);
    }
}
