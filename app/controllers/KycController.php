<?php
/**
 * Controlador del Sistema KYC (Know Your Customer)
 * Sistema de Gestión Integral de Caja de Ahorros
 */

require_once CORE_PATH . '/Controller.php';

class KycController extends Controller {
    
    public function index() {
        $this->requireRole(['administrador', 'operativo']);
        
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 15;
        $search = $_GET['q'] ?? '';
        $estatus = $_GET['estatus'] ?? '';
        $nivelRiesgo = $_GET['nivel_riesgo'] ?? '';
        
        // Estadísticas
        $stats = $this->getStats();
        
        $conditions = '1=1';
        $params = [];
        
        if ($search) {
            $conditions .= " AND (s.nombre LIKE :search1 OR s.apellido_paterno LIKE :search2 OR s.numero_socio LIKE :search3 OR s.rfc LIKE :search4)";
            $params['search1'] = "%{$search}%";
            $params['search2'] = "%{$search}%";
            $params['search3'] = "%{$search}%";
            $params['search4'] = "%{$search}%";
        }
        if ($estatus) {
            $conditions .= " AND k.estatus = :estatus";
            $params['estatus'] = $estatus;
        }
        if ($nivelRiesgo) {
            $conditions .= " AND k.nivel_riesgo = :nivel_riesgo";
            $params['nivel_riesgo'] = $nivelRiesgo;
        }
        
        $total = $this->db->fetch(
            "SELECT COUNT(*) as total FROM kyc_verificaciones k
             JOIN socios s ON k.socio_id = s.id
             WHERE {$conditions}",
            $params
        )['total'];
        
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        
        $verificaciones = $this->db->fetchAll(
            "SELECT k.*, s.numero_socio, s.nombre, s.apellido_paterno, s.apellido_materno,
                    s.rfc, s.curp, u.nombre as verificado_nombre
             FROM kyc_verificaciones k
             JOIN socios s ON k.socio_id = s.id
             LEFT JOIN usuarios u ON k.verificado_por = u.id
             WHERE {$conditions}
             ORDER BY k.fecha_verificacion DESC, k.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        
        $this->view('kyc/index', [
            'pageTitle' => 'Sistema KYC - Know Your Customer',
            'verificaciones' => $verificaciones,
            'stats' => $stats,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'search' => $search,
            'estatus' => $estatus,
            'nivelRiesgo' => $nivelRiesgo
        ]);
    }
    
    public function crear() {
        $this->requireRole(['administrador', 'operativo']);
        
        $socioId = $_GET['socio'] ?? null;
        $errors = [];
        $data = [];
        
        // Si se proporciona socio_id, obtener información
        $socioPreseleccionado = null;
        if ($socioId) {
            $socioPreseleccionado = $this->db->fetch(
                "SELECT s.*, 
                        (SELECT COUNT(*) FROM kyc_verificaciones WHERE socio_id = s.id) as verificaciones_previas
                 FROM socios s
                 WHERE s.id = :id AND s.estatus = 'activo'",
                ['id' => $socioId]
            );
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $data = $this->sanitize($_POST);
            
            // Validaciones
            if (empty($data['socio_id'])) $errors[] = 'Debe seleccionar un socio';
            if (empty($data['tipo_documento'])) $errors[] = 'Debe especificar el tipo de documento';
            if (empty($data['numero_documento'])) $errors[] = 'Debe ingresar el número de documento';
            
            // Verificar que el socio existe
            if (!empty($data['socio_id'])) {
                $socio = $this->db->fetch(
                    "SELECT * FROM socios WHERE id = :id AND estatus = 'activo'",
                    ['id' => $data['socio_id']]
                );
                if (!$socio) {
                    $errors[] = 'El socio seleccionado no existe o no está activo';
                }
            }
            
            // Verificar si ya existe una verificación pendiente
            if (!empty($data['socio_id'])) {
                $verificacionPendiente = $this->db->fetch(
                    "SELECT * FROM kyc_verificaciones WHERE socio_id = :socio_id AND estatus = 'pendiente'",
                    ['socio_id' => $data['socio_id']]
                );
                if ($verificacionPendiente) {
                    $errors[] = 'Ya existe una verificación pendiente para este socio';
                }
            }
            
            if (empty($errors)) {
                try {
                    // Calcular nivel de riesgo automático
                    $nivelRiesgo = $this->calcularNivelRiesgo($data);
                    
                    $verificacionId = $this->db->insert('kyc_verificaciones', [
                        'socio_id' => $data['socio_id'],
                        'tipo_documento' => $data['tipo_documento'],
                        'numero_documento' => $data['numero_documento'],
                        'fecha_emision' => $data['fecha_emision'] ?: null,
                        'fecha_vencimiento' => $data['fecha_vencimiento'] ?: null,
                        'pais_emision' => $data['pais_emision'] ?? 'México',
                        'documento_verificado' => isset($data['documento_verificado']) ? 1 : 0,
                        'direccion_verificada' => isset($data['direccion_verificada']) ? 1 : 0,
                        'identidad_verificada' => isset($data['identidad_verificada']) ? 1 : 0,
                        'pep' => isset($data['pep']) ? 1 : 0,
                        'fuente_ingresos' => $data['fuente_ingresos'] ?? '',
                        'actividad_economica' => $data['actividad_economica'] ?? '',
                        'nivel_riesgo' => $nivelRiesgo,
                        'estatus' => 'pendiente',
                        'observaciones' => $data['observaciones'] ?? ''
                    ]);
                    
                    $this->logAction('CREAR_KYC', 
                        "Se creó verificación KYC para socio ID {$data['socio_id']}",
                        'kyc_verificaciones',
                        $verificacionId
                    );
                    
                    $this->setFlash('success', 'Verificación KYC creada exitosamente');
                    $this->redirect('kyc/ver/' . $verificacionId);
                    
                } catch (Exception $e) {
                    $errors[] = 'Error al crear la verificación: ' . $e->getMessage();
                }
            }
        }
        
        $this->view('kyc/crear', [
            'pageTitle' => 'Nueva Verificación KYC',
            'socioPreseleccionado' => $socioPreseleccionado,
            'data' => $data,
            'errors' => $errors
        ]);
    }
    
    public function ver() {
        $this->requireRole(['administrador', 'operativo', 'consulta']);
        
        $id = $this->params['id'] ?? 0;
        
        $verificacion = $this->db->fetch(
            "SELECT k.*, s.numero_socio, s.nombre, s.apellido_paterno, s.apellido_materno,
                    s.rfc, s.curp, s.telefono, s.email, s.direccion, s.colonia, s.municipio,
                    s.estado, s.codigo_postal, s.fecha_nacimiento, s.genero,
                    u.nombre as verificado_nombre
             FROM kyc_verificaciones k
             JOIN socios s ON k.socio_id = s.id
             LEFT JOIN usuarios u ON k.verificado_por = u.id
             WHERE k.id = :id",
            ['id' => $id]
        );
        
        if (!$verificacion) {
            $this->setFlash('error', 'Verificación KYC no encontrada');
            $this->redirect('kyc');
        }
        
        // Obtener documentos adjuntos
        $documentos = $this->db->fetchAll(
            "SELECT * FROM kyc_documentos WHERE verificacion_id = :id ORDER BY fecha_subida DESC",
            ['id' => $id]
        );
        
        // Obtener historial de la verificación
        $historial = $this->db->fetchAll(
            "SELECT h.*, u.nombre as usuario_nombre
             FROM kyc_historial h
             LEFT JOIN usuarios u ON h.usuario_id = u.id
             WHERE h.verificacion_id = :id
             ORDER BY h.created_at DESC",
            ['id' => $id]
        );
        
        $this->view('kyc/ver', [
            'pageTitle' => 'Detalle de Verificación KYC',
            'verificacion' => $verificacion,
            'documentos' => $documentos,
            'historial' => $historial
        ]);
    }
    
    public function editar() {
        $this->requireRole(['administrador', 'operativo']);
        
        $id = $this->params['id'] ?? 0;
        $errors = [];
        
        $verificacion = $this->db->fetch(
            "SELECT k.*, s.numero_socio, s.nombre, s.apellido_paterno, s.apellido_materno
             FROM kyc_verificaciones k
             JOIN socios s ON k.socio_id = s.id
             WHERE k.id = :id",
            ['id' => $id]
        );
        
        if (!$verificacion) {
            $this->setFlash('error', 'Verificación KYC no encontrada');
            $this->redirect('kyc');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $data = $this->sanitize($_POST);
            
            // Validaciones
            if (empty($data['tipo_documento'])) $errors[] = 'Debe especificar el tipo de documento';
            if (empty($data['numero_documento'])) $errors[] = 'Debe ingresar el número de documento';
            
            if (empty($errors)) {
                try {
                    // Guardar valores anteriores para historial
                    $cambios = [];
                    if ($verificacion['estatus'] !== $data['estatus']) {
                        $cambios[] = "Estatus: {$verificacion['estatus']} → {$data['estatus']}";
                    }
                    if ($verificacion['nivel_riesgo'] !== $data['nivel_riesgo']) {
                        $cambios[] = "Nivel de riesgo: {$verificacion['nivel_riesgo']} → {$data['nivel_riesgo']}";
                    }
                    
                    $this->db->update('kyc_verificaciones', [
                        'tipo_documento' => $data['tipo_documento'],
                        'numero_documento' => $data['numero_documento'],
                        'fecha_emision' => $data['fecha_emision'] ?: null,
                        'fecha_vencimiento' => $data['fecha_vencimiento'] ?: null,
                        'pais_emision' => $data['pais_emision'] ?? 'México',
                        'documento_verificado' => isset($data['documento_verificado']) ? 1 : 0,
                        'direccion_verificada' => isset($data['direccion_verificada']) ? 1 : 0,
                        'identidad_verificada' => isset($data['identidad_verificada']) ? 1 : 0,
                        'pep' => isset($data['pep']) ? 1 : 0,
                        'fuente_ingresos' => $data['fuente_ingresos'] ?? '',
                        'actividad_economica' => $data['actividad_economica'] ?? '',
                        'nivel_riesgo' => $data['nivel_riesgo'],
                        'estatus' => $data['estatus'],
                        'observaciones' => $data['observaciones'] ?? '',
                        'fecha_verificacion' => $data['estatus'] === 'aprobado' ? date('Y-m-d H:i:s') : $verificacion['fecha_verificacion'],
                        'verificado_por' => $data['estatus'] === 'aprobado' ? $_SESSION['user_id'] : $verificacion['verificado_por']
                    ], 'id = :id', ['id' => $id]);
                    
                    // Registrar en historial si hay cambios
                    if (!empty($cambios)) {
                        $this->db->insert('kyc_historial', [
                            'verificacion_id' => $id,
                            'usuario_id' => $_SESSION['user_id'],
                            'accion' => 'MODIFICACION',
                            'descripcion' => implode('; ', $cambios)
                        ]);
                    }
                    
                    $this->logAction('EDITAR_KYC', 
                        "Se actualizó verificación KYC ID {$id}",
                        'kyc_verificaciones',
                        $id
                    );
                    
                    $this->setFlash('success', 'Verificación KYC actualizada exitosamente');
                    $this->redirect('kyc/ver/' . $id);
                    
                } catch (Exception $e) {
                    $errors[] = 'Error al actualizar: ' . $e->getMessage();
                }
            }
        }
        
        $this->view('kyc/editar', [
            'pageTitle' => 'Editar Verificación KYC',
            'verificacion' => $verificacion,
            'errors' => $errors
        ]);
    }
    
    public function aprobar() {
        $this->requireRole(['administrador']);
        
        $id = $this->params['id'] ?? 0;
        
        $verificacion = $this->db->fetch(
            "SELECT * FROM kyc_verificaciones WHERE id = :id AND estatus = 'pendiente'",
            ['id' => $id]
        );
        
        if (!$verificacion) {
            $this->setFlash('error', 'Verificación no encontrada o no está pendiente');
            $this->redirect('kyc');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $observaciones = $this->sanitize($_POST['observaciones'] ?? '');
            
            try {
                $this->db->update('kyc_verificaciones', [
                    'estatus' => 'aprobado',
                    'fecha_verificacion' => date('Y-m-d H:i:s'),
                    'verificado_por' => $_SESSION['user_id'],
                    'observaciones' => $observaciones
                ], 'id = :id', ['id' => $id]);
                
                // Registrar en historial
                $this->db->insert('kyc_historial', [
                    'verificacion_id' => $id,
                    'usuario_id' => $_SESSION['user_id'],
                    'accion' => 'APROBACION',
                    'descripcion' => 'Verificación KYC aprobada' . ($observaciones ? ": {$observaciones}" : '')
                ]);
                
                $this->logAction('APROBAR_KYC', 
                    "Se aprobó verificación KYC ID {$id}",
                    'kyc_verificaciones',
                    $id
                );
                
                $this->setFlash('success', 'Verificación KYC aprobada exitosamente');
                
            } catch (Exception $e) {
                $this->setFlash('error', 'Error al aprobar: ' . $e->getMessage());
            }
        }
        
        $this->redirect('kyc/ver/' . $id);
    }
    
    public function rechazar() {
        $this->requireRole(['administrador']);
        
        $id = $this->params['id'] ?? 0;
        
        $verificacion = $this->db->fetch(
            "SELECT * FROM kyc_verificaciones WHERE id = :id AND estatus = 'pendiente'",
            ['id' => $id]
        );
        
        if (!$verificacion) {
            $this->setFlash('error', 'Verificación no encontrada o no está pendiente');
            $this->redirect('kyc');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $motivo = $this->sanitize($_POST['motivo'] ?? '');
            
            if (empty($motivo)) {
                $this->setFlash('error', 'Debe proporcionar un motivo de rechazo');
                $this->redirect('kyc/ver/' . $id);
            }
            
            try {
                $this->db->update('kyc_verificaciones', [
                    'estatus' => 'rechazado',
                    'fecha_verificacion' => date('Y-m-d H:i:s'),
                    'verificado_por' => $_SESSION['user_id'],
                    'observaciones' => $motivo
                ], 'id = :id', ['id' => $id]);
                
                // Registrar en historial
                $this->db->insert('kyc_historial', [
                    'verificacion_id' => $id,
                    'usuario_id' => $_SESSION['user_id'],
                    'accion' => 'RECHAZO',
                    'descripcion' => "Verificación KYC rechazada: {$motivo}"
                ]);
                
                $this->logAction('RECHAZAR_KYC', 
                    "Se rechazó verificación KYC ID {$id}",
                    'kyc_verificaciones',
                    $id
                );
                
                $this->setFlash('success', 'Verificación KYC rechazada');
                
            } catch (Exception $e) {
                $this->setFlash('error', 'Error al rechazar: ' . $e->getMessage());
            }
        }
        
        $this->redirect('kyc/ver/' . $id);
    }
    
    public function documentos() {
        $this->requireRole(['administrador', 'operativo']);
        
        $id = $this->params['id'] ?? 0;
        
        // Validate ID is a positive integer to prevent path traversal
        if (!is_numeric($id) || (int)$id <= 0) {
            $this->setFlash('error', 'ID de verificación inválido');
            $this->redirect('kyc');
        }
        $id = (int)$id;
        
        $verificacion = $this->db->fetch(
            "SELECT k.*, s.nombre, s.apellido_paterno
             FROM kyc_verificaciones k
             JOIN socios s ON k.socio_id = s.id
             WHERE k.id = :id",
            ['id' => $id]
        );
        
        if (!$verificacion) {
            $this->setFlash('error', 'Verificación no encontrada');
            $this->redirect('kyc');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            if (!isset($_FILES['documento']) || $_FILES['documento']['error'] !== UPLOAD_ERR_OK) {
                $this->setFlash('error', 'Error al subir el archivo');
                $this->redirect('kyc/ver/' . $id);
            }
            
            $file = $_FILES['documento'];
            $tipoDocumento = $this->sanitize($_POST['tipo_documento'] ?? 'otro');
            
            // Validar tipo de archivo
            $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file['tmp_name']);
            
            if (!in_array($mimeType, $allowedTypes)) {
                $this->setFlash('error', 'Tipo de archivo no permitido. Solo se permiten JPG, PNG y PDF');
                $this->redirect('kyc/ver/' . $id);
            }
            
            // Validar tamaño (máximo 5MB)
            if ($file['size'] > 5 * 1024 * 1024) {
                $this->setFlash('error', 'El archivo es demasiado grande. Máximo 5MB');
                $this->redirect('kyc/ver/' . $id);
            }
            
            // Crear directorio si no existe - use verified ID
            $uploadDir = UPLOADS_PATH . '/kyc/' . $id;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Generar nombre único con extensión correcta basada en mime type
            // Using uniqid with more entropy and timestamp for uniqueness
            $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'application/pdf' => 'pdf'];
            $ext = $extensions[$mimeType] ?? 'bin';
            $fileName = uniqid('kyc_', true) . '_' . time() . '.' . $ext;
            $filePath = $uploadDir . '/' . $fileName;
            
            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                $this->db->insert('kyc_documentos', [
                    'verificacion_id' => $id,
                    'tipo_documento' => $tipoDocumento,
                    'nombre_archivo' => basename($file['name']), // Use basename for security
                    'ruta_archivo' => 'uploads/kyc/' . $id . '/' . $fileName,
                    'usuario_id' => $_SESSION['user_id']
                ]);
                
                $this->logAction('SUBIR_DOC_KYC', 
                    "Se subió documento para verificación KYC ID {$id}",
                    'kyc_verificaciones',
                    $id
                );
                
                $this->setFlash('success', 'Documento subido exitosamente');
            } else {
                $this->setFlash('error', 'Error al guardar el archivo');
            }
        }
        
        $this->redirect('kyc/ver/' . $id);
    }
    
    public function descargar() {
        $this->requireRole(['administrador', 'operativo', 'consulta']);
        
        $docId = $this->params['id'] ?? 0;
        
        // Validate ID is a positive integer
        if (!is_numeric($docId) || (int)$docId <= 0) {
            $this->setFlash('error', 'ID de documento inválido');
            $this->redirect('kyc');
        }
        $docId = (int)$docId;
        
        // Verify document exists and get its info
        $documento = $this->db->fetch(
            "SELECT d.*, k.socio_id 
             FROM kyc_documentos d
             JOIN kyc_verificaciones k ON d.verificacion_id = k.id
             WHERE d.id = :id",
            ['id' => $docId]
        );
        
        if (!$documento) {
            $this->setFlash('error', 'Documento no encontrado');
            $this->redirect('kyc');
        }
        
        // Build secure file path using only the filename from database
        $filePath = ROOT_PATH . '/' . $documento['ruta_archivo'];
        
        // Verify path is within uploads directory (prevent directory traversal)
        $realPath = realpath($filePath);
        $uploadsDir = realpath(UPLOADS_PATH);
        
        if ($realPath === false || strpos($realPath, $uploadsDir) !== 0) {
            $this->setFlash('error', 'Archivo no accesible');
            $this->redirect('kyc/ver/' . $documento['verificacion_id']);
        }
        
        if (!file_exists($realPath)) {
            $this->setFlash('error', 'Archivo no encontrado');
            $this->redirect('kyc/ver/' . $documento['verificacion_id']);
        }
        
        // Determine MIME type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($realPath);
        
        // Only allow safe file types
        $allowedMimes = ['image/jpeg', 'image/png', 'application/pdf'];
        if (!in_array($mimeType, $allowedMimes)) {
            $this->setFlash('error', 'Tipo de archivo no permitido');
            $this->redirect('kyc/ver/' . $documento['verificacion_id']);
        }
        
        // Log the download
        $this->logAction('DESCARGAR_DOC_KYC', 
            "Se descargó documento ID {$docId} de verificación KYC",
            'kyc_documentos',
            $docId
        );
        
        // Serve the file securely
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: inline; filename="' . basename($documento['nombre_archivo']) . '"');
        header('Content-Length: ' . filesize($realPath));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('X-Content-Type-Options: nosniff');
        readfile($realPath);
        exit;
    }
    
    public function reportes() {
        $this->requireRole(['administrador', 'operativo', 'consulta']);
        
        // Estadísticas generales
        $stats = $this->getStats();
        
        // Verificaciones por mes (últimos 6 meses)
        $verificacionesPorMes = $this->db->fetchAll(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') as mes,
                    COUNT(*) as total,
                    SUM(CASE WHEN estatus = 'aprobado' THEN 1 ELSE 0 END) as aprobados,
                    SUM(CASE WHEN estatus = 'rechazado' THEN 1 ELSE 0 END) as rechazados
             FROM kyc_verificaciones
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
             GROUP BY DATE_FORMAT(created_at, '%Y-%m')
             ORDER BY mes ASC"
        );
        
        // Distribución por nivel de riesgo
        $distribucionRiesgo = $this->db->fetchAll(
            "SELECT nivel_riesgo, COUNT(*) as total
             FROM kyc_verificaciones
             GROUP BY nivel_riesgo"
        );
        
        // Verificaciones vencidas (próximas a vencer)
        $proximasVencer = $this->db->fetchAll(
            "SELECT k.*, s.numero_socio, s.nombre, s.apellido_paterno
             FROM kyc_verificaciones k
             JOIN socios s ON k.socio_id = s.id
             WHERE k.fecha_vencimiento IS NOT NULL 
               AND k.fecha_vencimiento <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
               AND k.estatus = 'aprobado'
             ORDER BY k.fecha_vencimiento ASC
             LIMIT 10"
        );
        
        $this->view('kyc/reportes', [
            'pageTitle' => 'Reportes KYC',
            'stats' => $stats,
            'verificacionesPorMes' => $verificacionesPorMes,
            'distribucionRiesgo' => $distribucionRiesgo,
            'proximasVencer' => $proximasVencer
        ]);
    }
    
    private function getStats() {
        $totalVerificaciones = $this->db->fetch(
            "SELECT COUNT(*) as total FROM kyc_verificaciones"
        )['total'];
        
        $pendientes = $this->db->fetch(
            "SELECT COUNT(*) as total FROM kyc_verificaciones WHERE estatus = 'pendiente'"
        )['total'];
        
        $aprobados = $this->db->fetch(
            "SELECT COUNT(*) as total FROM kyc_verificaciones WHERE estatus = 'aprobado'"
        )['total'];
        
        $rechazados = $this->db->fetch(
            "SELECT COUNT(*) as total FROM kyc_verificaciones WHERE estatus = 'rechazado'"
        )['total'];
        
        $altoRiesgo = $this->db->fetch(
            "SELECT COUNT(*) as total FROM kyc_verificaciones WHERE nivel_riesgo = 'alto'"
        )['total'];
        
        $pep = $this->db->fetch(
            "SELECT COUNT(*) as total FROM kyc_verificaciones WHERE pep = 1"
        )['total'];
        
        return [
            'totalVerificaciones' => $totalVerificaciones,
            'pendientes' => $pendientes,
            'aprobados' => $aprobados,
            'rechazados' => $rechazados,
            'altoRiesgo' => $altoRiesgo,
            'pep' => $pep
        ];
    }
    
    private function calcularNivelRiesgo($data) {
        $puntos = 0;
        
        // PEP (Persona Políticamente Expuesta)
        if (isset($data['pep']) && $data['pep']) {
            $puntos += 30;
        }
        
        // Documento verificado
        if (!isset($data['documento_verificado']) || !$data['documento_verificado']) {
            $puntos += 15;
        }
        
        // Dirección verificada
        if (!isset($data['direccion_verificada']) || !$data['direccion_verificada']) {
            $puntos += 10;
        }
        
        // Identidad verificada
        if (!isset($data['identidad_verificada']) || !$data['identidad_verificada']) {
            $puntos += 15;
        }
        
        // Fuente de ingresos no especificada
        if (empty($data['fuente_ingresos'])) {
            $puntos += 10;
        }
        
        // Determinar nivel de riesgo
        if ($puntos >= 40) {
            return 'alto';
        } elseif ($puntos >= 20) {
            return 'medio';
        }
        return 'bajo';
    }
}
