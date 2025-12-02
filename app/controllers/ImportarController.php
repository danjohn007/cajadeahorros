<?php
/**
 * Controlador de Importación de Clientes
 * Sistema de Gestión Integral de Caja de Ahorros
 */

require_once CORE_PATH . '/Controller.php';

class ImportarController extends Controller {
    
    public function index() {
        $this->requireRole(['administrador', 'operativo']);
        
        // Obtener últimas importaciones
        $importaciones = $this->db->fetchAll(
            "SELECT i.*, u.nombre as usuario_nombre
             FROM importaciones i
             LEFT JOIN usuarios u ON i.usuario_id = u.id
             ORDER BY i.fecha_inicio DESC
             LIMIT 10"
        );
        
        $this->view('importar/index', [
            'pageTitle' => 'Importar Clientes',
            'importaciones' => $importaciones
        ]);
    }
    
    public function clientes() {
        $this->requireRole(['administrador', 'operativo']);
        
        $errors = [];
        $success = '';
        $importacionId = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
                $errors[] = 'Error al subir el archivo';
            } else {
                $archivo = $_FILES['archivo'];
                $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
                
                if (!in_array($extension, ['xlsx', 'xls', 'csv'])) {
                    $errors[] = 'Formato de archivo no válido. Use Excel (.xlsx, .xls) o CSV';
                } elseif ($archivo['size'] > 5 * 1024 * 1024) {
                    $errors[] = 'El archivo es demasiado grande. Máximo 5MB';
                } else {
                    // Procesar archivo
                    $resultado = $this->procesarArchivo($archivo);
                    
                    if (isset($resultado['error'])) {
                        $errors[] = $resultado['error'];
                    } else {
                        $importacionId = $resultado['importacion_id'];
                        $success = "Archivo procesado: {$resultado['total']} registros encontrados, {$resultado['exitosos']} importados, {$resultado['errores']} con errores";
                    }
                }
            }
        }
        
        $this->view('importar/clientes', [
            'pageTitle' => 'Importar Clientes desde Excel',
            'errors' => $errors,
            'success' => $success,
            'importacionId' => $importacionId
        ]);
    }
    
    public function historial() {
        $this->requireRole(['administrador', 'operativo']);
        
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 20;
        
        $total = $this->db->fetch("SELECT COUNT(*) as total FROM importaciones")['total'];
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        
        $importaciones = $this->db->fetchAll(
            "SELECT i.*, u.nombre as usuario_nombre
             FROM importaciones i
             LEFT JOIN usuarios u ON i.usuario_id = u.id
             ORDER BY i.fecha_inicio DESC
             LIMIT {$perPage} OFFSET {$offset}"
        );
        
        $this->view('importar/historial', [
            'pageTitle' => 'Historial de Importaciones',
            'importaciones' => $importaciones,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }
    
    public function detalle() {
        $this->requireRole(['administrador', 'operativo']);
        
        $id = (int)($this->params['id'] ?? 0);
        
        $importacion = $this->db->fetch(
            "SELECT i.*, u.nombre as usuario_nombre
             FROM importaciones i
             LEFT JOIN usuarios u ON i.usuario_id = u.id
             WHERE i.id = :id",
            ['id' => $id]
        );
        
        if (!$importacion) {
            $this->setFlash('error', 'Importación no encontrada');
            $this->redirect('importar/historial');
        }
        
        $detalles = $this->db->fetchAll(
            "SELECT * FROM importaciones_detalle WHERE importacion_id = :id ORDER BY fila",
            ['id' => $id]
        );
        
        $this->view('importar/detalle', [
            'pageTitle' => 'Detalle de Importación',
            'importacion' => $importacion,
            'detalles' => $detalles
        ]);
    }
    
    public function plantilla() {
        $this->requireRole(['administrador', 'operativo']);
        
        // Generate CSV template
        $headers = ['nombre', 'apellido_paterno', 'apellido_materno', 'email', 'telefono', 'celular', 'rfc', 'curp'];
        $sampleData = [
            ['Juan', 'García', 'López', 'juan.garcia@email.com', '4421234567', '4421234568', 'GALJ850315ABC', 'GALJ850315HQTRRN09'],
            ['María', 'Hernández', 'Ramírez', 'maria.hernandez@email.com', '4422345678', '4422345679', 'HERM900520DEF', 'HERM900520MQTRML05'],
        ];
        
        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=plantilla_importacion_clientes.csv');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Create output stream
        $output = fopen('php://output', 'w');
        
        // Add UTF-8 BOM for Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Write headers
        fputcsv($output, $headers);
        
        // Write sample data
        foreach ($sampleData as $row) {
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit;
    }
    
    private function procesarArchivo($archivo) {
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        
        // Crear registro de importación
        $this->db->insert('importaciones', [
            'nombre_archivo' => $archivo['name'],
            'tipo' => 'socios',
            'estatus' => 'procesando',
            'usuario_id' => $_SESSION['user_id']
        ]);
        $importacionId = $this->db->lastInsertId();
        
        try {
            if ($extension === 'csv') {
                $datos = $this->leerCSV($archivo['tmp_name']);
            } else {
                // Para Excel, usamos una librería o procesamiento manual simplificado
                $datos = $this->leerExcel($archivo['tmp_name']);
            }
            
            if (empty($datos)) {
                return ['error' => 'El archivo está vacío o no tiene el formato correcto'];
            }
            
            $total = count($datos);
            $exitosos = 0;
            $errores = 0;
            
            foreach ($datos as $fila => $registro) {
                $resultado = $this->procesarRegistro($registro, $importacionId, $fila + 2); // +2 porque empezamos en fila 2 (fila 1 es encabezado)
                
                if ($resultado === true) {
                    $exitosos++;
                } else {
                    $errores++;
                }
            }
            
            // Actualizar importación
            $estatus = ($errores === 0) ? 'completado' : (($exitosos > 0) ? 'parcial' : 'error');
            $this->db->update('importaciones', [
                'total_registros' => $total,
                'registros_exitosos' => $exitosos,
                'registros_error' => $errores,
                'estatus' => $estatus,
                'fecha_fin' => date('Y-m-d H:i:s')
            ], 'id = :id', ['id' => $importacionId]);
            
            $this->logAction('IMPORTAR_CLIENTES', "Importación completada: {$exitosos}/{$total} registros", 'importaciones', $importacionId);
            
            return [
                'importacion_id' => $importacionId,
                'total' => $total,
                'exitosos' => $exitosos,
                'errores' => $errores
            ];
            
        } catch (Exception $e) {
            $this->db->update('importaciones', ['estatus' => 'error', 'notas' => $e->getMessage()], 'id = :id', ['id' => $importacionId]);
            return ['error' => 'Error al procesar el archivo: ' . $e->getMessage()];
        }
    }
    
    private function leerCSV($rutaArchivo) {
        $datos = [];
        $encabezados = null;
        
        if (($handle = fopen($rutaArchivo, 'r')) !== false) {
            while (($fila = fgetcsv($handle, 1000, ',')) !== false) {
                if ($encabezados === null) {
                    $encabezados = array_map('strtolower', array_map('trim', $fila));
                } else {
                    $registro = [];
                    foreach ($encabezados as $i => $campo) {
                        $registro[$campo] = isset($fila[$i]) ? trim($fila[$i]) : '';
                    }
                    $datos[] = $registro;
                }
            }
            fclose($handle);
        }
        
        return $datos;
    }
    
    private function leerExcel($rutaArchivo) {
        // Implementación simplificada - en producción usar PhpSpreadsheet
        // Por ahora, retornar array vacío y documentar que se necesita librería
        return [];
    }
    
    private function procesarRegistro($registro, $importacionId, $fila) {
        // Mapear campos del archivo a campos de la base de datos
        $nombre = $registro['nombre'] ?? '';
        $apellidoPaterno = $registro['apellido_paterno'] ?? $registro['apellidos'] ?? '';
        $apellidoMaterno = $registro['apellido_materno'] ?? '';
        $email = $registro['email'] ?? $registro['correo'] ?? '';
        $telefono = $registro['telefono'] ?? '';
        $celular = $registro['celular'] ?? $registro['movil'] ?? '';
        $rfc = strtoupper($registro['rfc'] ?? '');
        $curp = strtoupper($registro['curp'] ?? '');
        
        // Guardar datos originales
        $datosOriginales = json_encode($registro);
        
        // Validar campos requeridos
        if (empty($nombre) || empty($apellidoPaterno)) {
            $this->db->insert('importaciones_detalle', [
                'importacion_id' => $importacionId,
                'fila' => $fila,
                'datos_originales' => $datosOriginales,
                'estatus' => 'error',
                'mensaje_error' => 'Nombre y apellido paterno son requeridos'
            ]);
            return false;
        }
        
        // Verificar duplicados
        if (!empty($rfc)) {
            $existe = $this->db->fetch("SELECT id FROM socios WHERE rfc = :rfc", ['rfc' => $rfc]);
            if ($existe) {
                $this->db->insert('importaciones_detalle', [
                    'importacion_id' => $importacionId,
                    'fila' => $fila,
                    'datos_originales' => $datosOriginales,
                    'estatus' => 'duplicado',
                    'mensaje_error' => 'Ya existe un socio con este RFC',
                    'entidad_id' => $existe['id']
                ]);
                return false;
            }
        }
        
        if (!empty($curp)) {
            $existe = $this->db->fetch("SELECT id FROM socios WHERE curp = :curp", ['curp' => $curp]);
            if ($existe) {
                $this->db->insert('importaciones_detalle', [
                    'importacion_id' => $importacionId,
                    'fila' => $fila,
                    'datos_originales' => $datosOriginales,
                    'estatus' => 'duplicado',
                    'mensaje_error' => 'Ya existe un socio con esta CURP',
                    'entidad_id' => $existe['id']
                ]);
                return false;
            }
        }
        
        try {
            // Use a transaction and locking to prevent race conditions
            // Generate number from database to ensure uniqueness
            $this->db->beginTransaction();
            
            // Lock and get the maximum socio number atomically
            $ultimoSocio = $this->db->fetch(
                "SELECT MAX(CAST(SUBSTRING(numero_socio, 5) AS UNSIGNED)) as max_num 
                 FROM socios 
                 WHERE numero_socio LIKE 'SOC-%' 
                 FOR UPDATE"
            );
            $nuevoNumero = 'SOC-' . str_pad(($ultimoSocio['max_num'] ?? 0) + 1, 4, '0', STR_PAD_LEFT);
            
            // Insertar socio
            $this->db->insert('socios', [
                'numero_socio' => $nuevoNumero,
                'nombre' => $nombre,
                'apellido_paterno' => $apellidoPaterno,
                'apellido_materno' => $apellidoMaterno,
                'email' => $email,
                'telefono' => $telefono,
                'celular' => $celular,
                'rfc' => $rfc,
                'curp' => $curp,
                'fecha_alta' => date('Y-m-d'),
                'estatus' => 'activo'
            ]);
            
            $socioId = $this->db->lastInsertId();
            
            // Crear cuenta de ahorro
            $numeroCuenta = 'AHO-' . str_pad($socioId, 4, '0', STR_PAD_LEFT);
            $this->db->insert('cuentas_ahorro', [
                'socio_id' => $socioId,
                'numero_cuenta' => $numeroCuenta,
                'saldo' => 0,
                'tasa_interes' => 0.03,
                'fecha_apertura' => date('Y-m-d'),
                'estatus' => 'activa'
            ]);
            
            $this->db->insert('importaciones_detalle', [
                'importacion_id' => $importacionId,
                'fila' => $fila,
                'datos_originales' => $datosOriginales,
                'estatus' => 'exitoso',
                'entidad_id' => $socioId
            ]);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $this->db->insert('importaciones_detalle', [
                'importacion_id' => $importacionId,
                'fila' => $fila,
                'datos_originales' => $datosOriginales,
                'estatus' => 'error',
                'mensaje_error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
