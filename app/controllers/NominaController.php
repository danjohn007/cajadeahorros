<?php
/**
 * Controlador de Nómina
 * Sistema de Gestión Integral de Caja de Ahorros
 */

require_once CORE_PATH . '/Controller.php';

class NominaController extends Controller {
    
    public function index() {
        $this->requireAuth();
        
        // Obtener archivos de nómina
        $archivos = $this->db->fetchAll(
            "SELECT an.*, u.nombre as usuario_nombre
             FROM archivos_nomina an
             LEFT JOIN usuarios u ON an.usuario_id = u.id
             ORDER BY an.fecha_carga DESC
             LIMIT 50"
        );
        
        // Stats
        $stats = [
            'totalArchivos' => count($archivos),
            'pendientes' => $this->db->fetch(
                "SELECT COUNT(*) as total FROM archivos_nomina WHERE estatus IN ('cargado', 'pendiente_revision')"
            )['total'],
            'procesados' => $this->db->fetch(
                "SELECT COUNT(*) as total FROM archivos_nomina WHERE estatus = 'aplicado'"
            )['total']
        ];
        
        $this->view('nomina/index', [
            'pageTitle' => 'Procesamiento de Nómina',
            'archivos' => $archivos,
            'stats' => $stats
        ]);
    }
    
    public function cargar() {
        $this->requireRole(['administrador', 'operativo']);
        
        $errors = [];
        $preview = null;
        $previewMode = isset($_POST['preview']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
                $archivo = $_FILES['archivo'];
                $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
                
                if (!in_array($ext, ['csv', 'xlsx', 'xls'])) {
                    $errors[] = 'Formato de archivo no válido. Use CSV o Excel.';
                } else {
                    $periodo = $this->sanitize($_POST['periodo'] ?? '');
                    $fechaNomina = $_POST['fecha_nomina'] ?? date('Y-m-d');
                    
                    // Guardar archivo temporalmente para preview
                    $nombreArchivo = 'nomina_' . date('Y-m-d_His') . '.' . $ext;
                    $rutaArchivo = UPLOADS_PATH . '/' . $nombreArchivo;
                    
                    if (move_uploaded_file($archivo['tmp_name'], $rutaArchivo)) {
                        // Procesar archivo
                        $registros = $this->procesarArchivo($rutaArchivo, $ext);
                        
                        if ($previewMode) {
                            // Mode preview - mostrar registros pero no guardar
                            $preview = [
                                'nombre_archivo' => $archivo['name'],
                                'ruta_archivo' => $rutaArchivo,
                                'periodo' => $periodo,
                                'fecha_nomina' => $fechaNomina,
                                'registros' => array_slice($registros, 0, 50), // Limitar a 50 para preview
                                'total_registros' => count($registros)
                            ];
                            
                            // Store in session for final import
                            $_SESSION['nomina_preview'] = [
                                'ruta_archivo' => $rutaArchivo,
                                'nombre_archivo' => $archivo['name'],
                                'periodo' => $periodo,
                                'fecha_nomina' => $fechaNomina,
                                'registros' => $registros
                            ];
                        } else {
                            // Mode final import - check if we have preview data
                            if (isset($_POST['confirm_import']) && isset($_SESSION['nomina_preview'])) {
                                $previewData = $_SESSION['nomina_preview'];
                                $registros = $previewData['registros'];
                                $rutaArchivo = $previewData['ruta_archivo'];
                                $archivo = ['name' => $previewData['nombre_archivo']];
                                $periodo = $previewData['periodo'];
                                $fechaNomina = $previewData['fecha_nomina'];
                                unset($_SESSION['nomina_preview']);
                                
                                // Delete temp file from new upload if exists
                                if (file_exists(UPLOADS_PATH . '/' . $nombreArchivo)) {
                                    @unlink(UPLOADS_PATH . '/' . $nombreArchivo);
                                }
                            }
                            
                            // Guardar en BD
                            $archivoId = $this->db->insert('archivos_nomina', [
                                'nombre_archivo' => $archivo['name'],
                                'ruta_archivo' => $rutaArchivo,
                                'periodo' => $periodo,
                                'fecha_nomina' => $fechaNomina,
                                'total_registros' => count($registros),
                                'estatus' => 'cargado',
                                'usuario_id' => $_SESSION['user_id'],
                                'fecha_carga' => date('Y-m-d H:i:s')
                            ]);
                            
                            // Guardar registros
                            foreach ($registros as $reg) {
                                $this->db->insert('registros_nomina', [
                                    'archivo_id' => $archivoId,
                                    'rfc' => $reg['rfc'] ?? '',
                                    'curp' => $reg['curp'] ?? '',
                                    'nombre_nomina' => $reg['nombre'] ?? '',
                                    'numero_empleado' => $reg['numero_empleado'] ?? '',
                                    'monto_descuento' => $reg['monto'] ?? 0,
                                    'concepto' => $reg['concepto'] ?? '',
                                    'estatus' => 'pendiente'
                                ]);
                            }
                            
                            $this->logAction('CARGAR_NOMINA', 
                                "Se cargó archivo de nómina: {$archivo['name']} con " . count($registros) . " registros",
                                'archivos_nomina',
                                $archivoId
                            );
                            
                            $this->setFlash('success', 'Archivo cargado exitosamente con ' . count($registros) . ' registros');
                            $this->redirect('nomina/procesar/' . $archivoId);
                        }
                    } else {
                        $errors[] = 'Error al guardar el archivo';
                    }
                }
            } elseif (isset($_POST['confirm_import']) && isset($_SESSION['nomina_preview'])) {
                // Import from preview without new file upload
                $previewData = $_SESSION['nomina_preview'];
                $registros = $previewData['registros'];
                $rutaArchivo = $previewData['ruta_archivo'];
                $periodo = $previewData['periodo'];
                $fechaNomina = $previewData['fecha_nomina'];
                $nombreArchivo = $previewData['nombre_archivo'];
                unset($_SESSION['nomina_preview']);
                
                // Guardar en BD
                $archivoId = $this->db->insert('archivos_nomina', [
                    'nombre_archivo' => $nombreArchivo,
                    'ruta_archivo' => $rutaArchivo,
                    'periodo' => $periodo,
                    'fecha_nomina' => $fechaNomina,
                    'total_registros' => count($registros),
                    'estatus' => 'cargado',
                    'usuario_id' => $_SESSION['user_id'],
                    'fecha_carga' => date('Y-m-d H:i:s')
                ]);
                
                // Guardar registros
                foreach ($registros as $reg) {
                    $this->db->insert('registros_nomina', [
                        'archivo_id' => $archivoId,
                        'rfc' => $reg['rfc'] ?? '',
                        'curp' => $reg['curp'] ?? '',
                        'nombre_nomina' => $reg['nombre'] ?? '',
                        'numero_empleado' => $reg['numero_empleado'] ?? '',
                        'monto_descuento' => $reg['monto'] ?? 0,
                        'concepto' => $reg['concepto'] ?? '',
                        'estatus' => 'pendiente'
                    ]);
                }
                
                $this->logAction('CARGAR_NOMINA', 
                    "Se cargó archivo de nómina: {$nombreArchivo} con " . count($registros) . " registros",
                    'archivos_nomina',
                    $archivoId
                );
                
                $this->setFlash('success', 'Archivo cargado exitosamente con ' . count($registros) . ' registros');
                $this->redirect('nomina/procesar/' . $archivoId);
            } else {
                $errors[] = 'Debe seleccionar un archivo';
            }
        }
        
        $this->view('nomina/cargar', [
            'pageTitle' => 'Cargar Archivo de Nómina',
            'errors' => $errors,
            'preview' => $preview
        ]);
    }
    
    public function procesar() {
        $this->requireRole(['administrador', 'operativo']);
        
        $id = $this->params['id'] ?? 0;
        
        $archivo = $this->db->fetch(
            "SELECT * FROM archivos_nomina WHERE id = :id",
            ['id' => $id]
        );
        
        if (!$archivo) {
            $this->setFlash('error', 'Archivo no encontrado');
            $this->redirect('nomina');
        }
        
        // Si es POST, procesar matching
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            $this->ejecutarMatching($id);
            $this->setFlash('success', 'Proceso de matching completado');
        }
        
        // Obtener registros
        $registros = $this->db->fetchAll(
            "SELECT rn.*, s.nombre as socio_nombre, s.apellido_paterno as socio_apellido,
                    s.numero_socio
             FROM registros_nomina rn
             LEFT JOIN socios s ON rn.socio_id = s.id
             WHERE rn.archivo_id = :id
             ORDER BY rn.estatus, rn.id",
            ['id' => $id]
        );
        
        // Estadísticas
        $stats = [
            'total' => count($registros),
            'coincidencia' => 0,
            'homonimia' => 0,
            'sin_coincidencia' => 0,
            'aplicado' => 0,
            'pendiente' => 0
        ];
        
        foreach ($registros as $reg) {
            if (isset($stats[$reg['estatus']])) {
                $stats[$reg['estatus']]++;
            }
        }
        
        $this->view('nomina/procesar', [
            'pageTitle' => 'Procesar Nómina',
            'archivo' => $archivo,
            'registros' => $registros,
            'stats' => $stats
        ]);
    }
    
    public function homonimias() {
        $this->requireRole(['administrador', 'operativo']);
        
        // Obtener registros con homonimia
        $registros = $this->db->fetchAll(
            "SELECT rn.*, an.nombre_archivo, an.periodo
             FROM registros_nomina rn
             JOIN archivos_nomina an ON rn.archivo_id = an.id
             WHERE rn.estatus = 'homonimia'
             ORDER BY an.fecha_carga DESC, rn.id"
        );
        
        $this->view('nomina/homonimias', [
            'pageTitle' => 'Resolución de Homonimias',
            'registros' => $registros
        ]);
    }
    
    public function resolver() {
        $this->requireRole(['administrador', 'operativo']);
        
        $id = $this->params['id'] ?? 0;
        
        $registro = $this->db->fetch(
            "SELECT rn.*, an.nombre_archivo
             FROM registros_nomina rn
             JOIN archivos_nomina an ON rn.archivo_id = an.id
             WHERE rn.id = :id",
            ['id' => $id]
        );
        
        if (!$registro) {
            $this->setFlash('error', 'Registro no encontrado');
            $this->redirect('nomina/homonimias');
        }
        
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $socioId = (int)$_POST['socio_id'];
            
            if ($socioId) {
                // Actualizar registro
                $this->db->update('registros_nomina', [
                    'socio_id' => $socioId,
                    'estatus' => 'coincidencia'
                ], 'id = :id', ['id' => $id]);
                
                // Guardar equivalencia para futuras coincidencias
                $this->db->insert('equivalencias_nomina', [
                    'rfc' => $registro['rfc'],
                    'curp' => $registro['curp'],
                    'nombre_nomina' => $registro['nombre_nomina'],
                    'socio_id' => $socioId,
                    'usuario_id' => $_SESSION['user_id']
                ]);
                
                $this->logAction('RESOLVER_HOMONIMIA', 
                    "Se resolvió homonimia para: {$registro['nombre_nomina']}",
                    'registros_nomina',
                    $id
                );
                
                $this->setFlash('success', 'Homonimia resuelta exitosamente');
                $this->redirect('nomina/homonimias');
            } else {
                $errors[] = 'Debe seleccionar un socio';
            }
        }
        
        // Buscar posibles coincidencias
        $posiblesCoincidencias = $this->buscarCoincidencias($registro);
        
        $this->view('nomina/resolver', [
            'pageTitle' => 'Resolver Homonimia',
            'registro' => $registro,
            'posiblesCoincidencias' => $posiblesCoincidencias,
            'errors' => $errors
        ]);
    }
    
    public function aplicar() {
        $this->requireRole(['administrador']);
        
        $id = $this->params['id'] ?? 0;
        
        $archivo = $this->db->fetch(
            "SELECT * FROM archivos_nomina WHERE id = :id",
            ['id' => $id]
        );
        
        if (!$archivo) {
            $this->setFlash('error', 'Archivo no encontrado');
            $this->redirect('nomina');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            try {
                $this->db->beginTransaction();
                
                // Obtener registros con coincidencia
                $registros = $this->db->fetchAll(
                    "SELECT rn.*, s.id as socio_id
                     FROM registros_nomina rn
                     JOIN socios s ON rn.socio_id = s.id
                     WHERE rn.archivo_id = :id AND rn.estatus = 'coincidencia'",
                    ['id' => $id]
                );
                
                $aplicados = 0;
                
                foreach ($registros as $reg) {
                    // Obtener cuenta de ahorro
                    $cuenta = $this->db->fetch(
                        "SELECT * FROM cuentas_ahorro WHERE socio_id = :socio_id AND estatus = 'activa'",
                        ['socio_id' => $reg['socio_id']]
                    );
                    
                    if ($cuenta && $reg['monto_descuento'] > 0) {
                        // Registrar aportación
                        $saldoNuevo = $cuenta['saldo'] + $reg['monto_descuento'];
                        
                        $this->db->insert('movimientos_ahorro', [
                            'cuenta_id' => $cuenta['id'],
                            'tipo' => 'aportacion',
                            'monto' => $reg['monto_descuento'],
                            'saldo_anterior' => $cuenta['saldo'],
                            'saldo_nuevo' => $saldoNuevo,
                            'concepto' => 'Descuento nómina - ' . $archivo['periodo'],
                            'referencia' => $archivo['nombre_archivo'],
                            'origen' => 'nomina',
                            'usuario_id' => $_SESSION['user_id'],
                            'fecha' => date('Y-m-d H:i:s')
                        ]);
                        
                        // Actualizar saldo
                        $this->db->update('cuentas_ahorro',
                            ['saldo' => $saldoNuevo],
                            'id = :id',
                            ['id' => $cuenta['id']]
                        );
                        
                        // Marcar registro como aplicado
                        $this->db->update('registros_nomina',
                            ['estatus' => 'aplicado'],
                            'id = :id',
                            ['id' => $reg['id']]
                        );
                        
                        $aplicados++;
                    }
                }
                
                // Actualizar archivo
                $this->db->update('archivos_nomina', [
                    'registros_procesados' => $aplicados,
                    'estatus' => 'aplicado',
                    'fecha_procesamiento' => date('Y-m-d H:i:s')
                ], 'id = :id', ['id' => $id]);
                
                $this->db->commit();
                
                $this->logAction('APLICAR_NOMINA', 
                    "Se aplicó nómina: {$archivo['nombre_archivo']} con {$aplicados} registros",
                    'archivos_nomina',
                    $id
                );
                
                $this->setFlash('success', "Nómina aplicada exitosamente. {$aplicados} movimientos registrados.");
                $this->redirect('nomina');
                
            } catch (Exception $e) {
                $this->db->rollBack();
                $this->setFlash('error', 'Error al aplicar nómina: ' . $e->getMessage());
            }
        }
        
        $this->redirect('nomina/procesar/' . $id);
    }
    
    private function procesarArchivo($ruta, $extension) {
        $registros = [];
        
        if ($extension === 'csv') {
            if (($handle = fopen($ruta, 'r')) !== false) {
                $headers = fgetcsv($handle, 1000, ',');
                
                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    $registros[] = [
                        'rfc' => $data[0] ?? '',
                        'curp' => $data[1] ?? '',
                        'nombre' => $data[2] ?? '',
                        'numero_empleado' => $data[3] ?? '',
                        'monto' => floatval(str_replace(['$', ','], '', $data[4] ?? '0')),
                        'concepto' => $data[5] ?? 'Ahorro'
                    ];
                }
                fclose($handle);
            }
        }
        // Para Excel se necesitaría una librería como PhpSpreadsheet
        
        return $registros;
    }
    
    private function ejecutarMatching($archivoId) {
        $registros = $this->db->fetchAll(
            "SELECT * FROM registros_nomina WHERE archivo_id = :id AND estatus = 'pendiente'",
            ['id' => $archivoId]
        );
        
        foreach ($registros as $reg) {
            // Buscar por equivalencia previa
            $equivalencia = $this->db->fetch(
                "SELECT socio_id FROM equivalencias_nomina 
                 WHERE (rfc = :rfc AND rfc != '') OR (curp = :curp AND curp != '')
                 ORDER BY id DESC LIMIT 1",
                ['rfc' => $reg['rfc'], 'curp' => $reg['curp']]
            );
            
            if ($equivalencia) {
                $this->db->update('registros_nomina', [
                    'socio_id' => $equivalencia['socio_id'],
                    'estatus' => 'coincidencia'
                ], 'id = :id', ['id' => $reg['id']]);
                continue;
            }
            
            // Buscar por CURP exacto
            if (!empty($reg['curp'])) {
                $socio = $this->db->fetch(
                    "SELECT id FROM socios WHERE curp = :curp AND estatus = 'activo'",
                    ['curp' => $reg['curp']]
                );
                if ($socio) {
                    $this->db->update('registros_nomina', [
                        'socio_id' => $socio['id'],
                        'estatus' => 'coincidencia'
                    ], 'id = :id', ['id' => $reg['id']]);
                    continue;
                }
            }
            
            // Buscar por RFC exacto
            if (!empty($reg['rfc'])) {
                $socios = $this->db->fetchAll(
                    "SELECT id FROM socios WHERE rfc = :rfc AND estatus = 'activo'",
                    ['rfc' => $reg['rfc']]
                );
                
                if (count($socios) === 1) {
                    $this->db->update('registros_nomina', [
                        'socio_id' => $socios[0]['id'],
                        'estatus' => 'coincidencia'
                    ], 'id = :id', ['id' => $reg['id']]);
                    continue;
                } elseif (count($socios) > 1) {
                    $this->db->update('registros_nomina', [
                        'estatus' => 'homonimia'
                    ], 'id = :id', ['id' => $reg['id']]);
                    continue;
                }
            }
            
            // Sin coincidencia
            $this->db->update('registros_nomina', [
                'estatus' => 'sin_coincidencia'
            ], 'id = :id', ['id' => $reg['id']]);
        }
    }
    
    private function buscarCoincidencias($registro) {
        $condiciones = [];
        $params = [];
        
        if (!empty($registro['rfc'])) {
            $condiciones[] = "rfc LIKE :rfc";
            $params['rfc'] = substr($registro['rfc'], 0, 10) . '%';
        }
        if (!empty($registro['curp'])) {
            $condiciones[] = "curp LIKE :curp";
            $params['curp'] = substr($registro['curp'], 0, 16) . '%';
        }
        if (!empty($registro['nombre_nomina'])) {
            $condiciones[] = "(nombre LIKE :nombre OR apellido_paterno LIKE :apellido)";
            $params['nombre'] = '%' . explode(' ', $registro['nombre_nomina'])[0] . '%';
            $params['apellido'] = '%' . (explode(' ', $registro['nombre_nomina'])[1] ?? '') . '%';
        }
        
        if (empty($condiciones)) {
            return [];
        }
        
        return $this->db->fetchAll(
            "SELECT * FROM socios WHERE estatus = 'activo' AND (" . implode(' OR ', $condiciones) . ")
             ORDER BY nombre, apellido_paterno LIMIT 20",
            $params
        );
    }
}
