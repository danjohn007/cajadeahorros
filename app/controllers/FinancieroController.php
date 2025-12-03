<?php
/**
 * Controlador del Módulo Financiero
 * Sistema de Gestión Integral de Caja de Ahorros
 */

require_once CORE_PATH . '/Controller.php';

class FinancieroController extends Controller {
    
    public function index() {
        $this->requireAuth();
        
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 20;
        $tipo = $_GET['tipo'] ?? '';
        $categoria = $_GET['categoria'] ?? '';
        $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fechaFin = $_GET['fecha_fin'] ?? date('Y-m-d');
        
        $conditions = '1=1';
        $params = [];
        
        if ($tipo) {
            $conditions .= ' AND t.tipo = :tipo';
            $params['tipo'] = $tipo;
        }
        if ($categoria) {
            $conditions .= ' AND t.categoria_id = :categoria';
            $params['categoria'] = $categoria;
        }
        if ($fechaInicio) {
            $conditions .= ' AND t.fecha >= :fecha_inicio';
            $params['fecha_inicio'] = $fechaInicio;
        }
        if ($fechaFin) {
            $conditions .= ' AND t.fecha <= :fecha_fin';
            $params['fecha_fin'] = $fechaFin;
        }
        
        $total = $this->db->fetch(
            "SELECT COUNT(*) as total FROM transacciones_financieras t WHERE {$conditions}",
            $params
        )['total'];
        
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        
        $transacciones = $this->db->fetchAll(
            "SELECT t.*, cf.nombre as categoria_nombre, cf.color as categoria_color,
                    u.nombre as usuario_nombre
             FROM transacciones_financieras t
             LEFT JOIN categorias_financieras cf ON t.categoria_id = cf.id
             LEFT JOIN usuarios u ON t.usuario_id = u.id
             WHERE {$conditions}
             ORDER BY t.fecha DESC, t.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        
        $categorias = $this->db->fetchAll(
            "SELECT * FROM categorias_financieras WHERE activo = 1 ORDER BY tipo, nombre"
        );
        
        // Resumen financiero
        $resumen = $this->getResumen($fechaInicio, $fechaFin);
        
        $this->view('financiero/index', [
            'pageTitle' => 'Módulo Financiero',
            'transacciones' => $transacciones,
            'categorias' => $categorias,
            'resumen' => $resumen,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'tipo' => $tipo,
            'categoriaFilter' => $categoria,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin
        ]);
    }
    
    public function transaccion() {
        $this->requireRole(['administrador', 'operativo']);
        
        $id = isset($this->params['id']) ? (int)$this->params['id'] : 0;
        $transaccion = null;
        $errors = [];
        
        if ($id) {
            $transaccion = $this->db->fetch(
                "SELECT * FROM transacciones_financieras WHERE id = :id",
                ['id' => $id]
            );
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $tipo = $this->sanitize($_POST['tipo'] ?? '');
            $categoriaId = (int)($_POST['categoria_id'] ?? 0);
            $concepto = $this->sanitize($_POST['concepto'] ?? '');
            $monto = (float)($_POST['monto'] ?? 0);
            $fecha = $this->sanitize($_POST['fecha'] ?? date('Y-m-d'));
            $metodoPago = $this->sanitize($_POST['metodo_pago'] ?? 'efectivo');
            $referencia = $this->sanitize($_POST['referencia'] ?? '');
            $proveedor = $this->sanitize($_POST['proveedor'] ?? '');
            $notas = $this->sanitize($_POST['notas'] ?? '');
            
            // Validaciones
            if (!in_array($tipo, ['ingreso', 'egreso'])) {
                $errors[] = 'Tipo de transacción inválido';
            }
            if (empty($concepto)) {
                $errors[] = 'El concepto es requerido';
            }
            if ($monto <= 0) {
                $errors[] = 'El monto debe ser mayor a cero';
            }
            
            if (empty($errors)) {
                $data = [
                    'tipo' => $tipo,
                    'categoria_id' => $categoriaId ?: null,
                    'concepto' => $concepto,
                    'monto' => $monto,
                    'fecha' => $fecha,
                    'metodo_pago' => $metodoPago,
                    'referencia' => $referencia,
                    'proveedor' => $proveedor,
                    'notas' => $notas,
                    'usuario_id' => $_SESSION['user_id']
                ];
                
                if ($id) {
                    $this->db->update('transacciones_financieras', $data, 'id = :id', ['id' => $id]);
                    $this->logAction('EDITAR_TRANSACCION', "Se editó transacción ID: {$id}", 'transacciones_financieras', $id);
                    $this->setFlash('success', 'Transacción actualizada exitosamente');
                } else {
                    $this->db->insert('transacciones_financieras', $data);
                    $newId = $this->db->lastInsertId();
                    $this->logAction('CREAR_TRANSACCION', "Se creó transacción: {$concepto}", 'transacciones_financieras', $newId);
                    $this->setFlash('success', 'Transacción registrada exitosamente');
                }
                
                $this->redirect('financiero');
            }
        }
        
        $categorias = $this->db->fetchAll(
            "SELECT * FROM categorias_financieras WHERE activo = 1 ORDER BY tipo, nombre"
        );
        
        $this->view('financiero/transaccion', [
            'pageTitle' => $id ? 'Editar Transacción' : 'Nueva Transacción',
            'transaccion' => $transaccion,
            'categorias' => $categorias,
            'errors' => $errors
        ]);
    }
    
    public function categorias() {
        $this->requireRole(['administrador']);
        
        $errors = [];
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $action = $_POST['action'] ?? '';
            
            if ($action === 'crear') {
                $nombre = $this->sanitize($_POST['nombre'] ?? '');
                $tipo = $this->sanitize($_POST['tipo'] ?? '');
                $descripcion = $this->sanitize($_POST['descripcion'] ?? '');
                $color = $this->sanitize($_POST['color'] ?? '#000000');
                $icono = $this->sanitize($_POST['icono'] ?? 'fas fa-tag');
                
                if (empty($nombre)) {
                    $errors[] = 'El nombre es requerido';
                } elseif (!in_array($tipo, ['ingreso', 'egreso'])) {
                    $errors[] = 'Tipo de categoría inválido';
                } else {
                    $this->db->insert('categorias_financieras', [
                        'nombre' => $nombre,
                        'tipo' => $tipo,
                        'descripcion' => $descripcion,
                        'color' => $color,
                        'icono' => $icono,
                        'activo' => 1
                    ]);
                    
                    $this->logAction('CREAR_CATEGORIA', "Se creó categoría financiera: {$nombre}", 'categorias_financieras', $this->db->lastInsertId());
                    $success = 'Categoría creada exitosamente';
                }
            } elseif ($action === 'toggle') {
                $id = (int)($_POST['id'] ?? 0);
                $categoria = $this->db->fetch("SELECT activo FROM categorias_financieras WHERE id = :id", ['id' => $id]);
                if ($categoria) {
                    $nuevoEstado = $categoria['activo'] ? 0 : 1;
                    $this->db->update('categorias_financieras', ['activo' => $nuevoEstado], 'id = :id', ['id' => $id]);
                    $success = $nuevoEstado ? 'Categoría activada' : 'Categoría desactivada';
                }
            }
        }
        
        $categorias = $this->db->fetchAll(
            "SELECT cf.*, 
                    (SELECT COUNT(*) FROM transacciones_financieras t WHERE t.categoria_id = cf.id) as num_transacciones
             FROM categorias_financieras cf 
             ORDER BY cf.tipo, cf.nombre"
        );
        
        $this->view('financiero/categorias', [
            'pageTitle' => 'Categorías Financieras',
            'categorias' => $categorias,
            'errors' => $errors,
            'success' => $success
        ]);
    }
    
    public function reportes() {
        $this->requireRole(['administrador', 'operativo']);
        
        $anio = (int)($_GET['año'] ?? date('Y'));
        $mes = (int)($_GET['mes'] ?? 0);
        
        // Resumen por categoría
        $conditions = 'YEAR(t.fecha) = :anio';
        $params = ['anio' => $anio];
        
        if ($mes > 0) {
            $conditions .= ' AND MONTH(t.fecha) = :mes';
            $params['mes'] = $mes;
        }
        
        $resumenCategoria = $this->db->fetchAll(
            "SELECT cf.nombre, cf.tipo, cf.color, SUM(t.monto) as total
             FROM transacciones_financieras t
             JOIN categorias_financieras cf ON t.categoria_id = cf.id
             WHERE {$conditions}
             GROUP BY cf.id, cf.nombre, cf.tipo, cf.color
             ORDER BY cf.tipo, total DESC",
            $params
        );
        
        // Totales por tipo
        $totales = $this->db->fetch(
            "SELECT 
                COALESCE(SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END), 0) as ingresos,
                COALESCE(SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END), 0) as egresos
             FROM transacciones_financieras
             WHERE {$conditions}",
            $params
        );
        
        // Evolución mensual
        $evolucionMensual = $this->db->fetchAll(
            "SELECT 
                DATE_FORMAT(fecha, '%Y-%m') as periodo,
                SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) as ingresos,
                SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END) as egresos
             FROM transacciones_financieras
             WHERE YEAR(fecha) = :anio
             GROUP BY DATE_FORMAT(fecha, '%Y-%m')
             ORDER BY periodo",
            ['anio' => $anio]
        );
        
        $this->view('financiero/reportes', [
            'pageTitle' => 'Reportes Financieros',
            'resumenCategoria' => $resumenCategoria,
            'totales' => $totales,
            'evolucionMensual' => $evolucionMensual,
            'año' => $anio,
            'mes' => $mes
        ]);
    }
    
    public function presupuestos() {
        $this->requireRole(['administrador']);
        
        $anio = (int)($_GET['año'] ?? date('Y'));
        $errors = [];
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $categoriaId = (int)($_POST['categoria_id'] ?? 0);
            $mesPresupuesto = (int)($_POST['mes'] ?? 0);
            $montoPresupuestado = (float)($_POST['monto_presupuestado'] ?? 0);
            
            if ($categoriaId && $mesPresupuesto && $montoPresupuestado > 0) {
                // Insertar o actualizar
                $existe = $this->db->fetch(
                    "SELECT id FROM presupuestos WHERE categoria_id = :cat AND año = :anio AND mes = :mes",
                    ['cat' => $categoriaId, 'anio' => $anio, 'mes' => $mesPresupuesto]
                );
                
                if ($existe) {
                    $this->db->update('presupuestos', 
                        ['monto_presupuestado' => $montoPresupuestado],
                        'id = :id',
                        ['id' => $existe['id']]
                    );
                } else {
                    $this->db->insert('presupuestos', [
                        'categoria_id' => $categoriaId,
                        'año' => $anio,
                        'mes' => $mesPresupuesto,
                        'monto_presupuestado' => $montoPresupuestado
                    ]);
                }
                
                $success = 'Presupuesto guardado exitosamente';
            } else {
                $errors[] = 'Datos incompletos para el presupuesto';
            }
        }
        
        $presupuestos = $this->db->fetchAll(
            "SELECT p.*, cf.nombre as categoria_nombre, cf.tipo, cf.color,
                    (SELECT COALESCE(SUM(monto), 0) FROM transacciones_financieras 
                     WHERE categoria_id = p.categoria_id 
                     AND YEAR(fecha) = p.año AND MONTH(fecha) = p.mes) as ejecutado
             FROM presupuestos p
             JOIN categorias_financieras cf ON p.categoria_id = cf.id
             WHERE p.año = :anio
             ORDER BY p.mes, cf.nombre",
            ['anio' => $anio]
        );
        
        $categorias = $this->db->fetchAll(
            "SELECT * FROM categorias_financieras WHERE activo = 1 ORDER BY tipo, nombre"
        );
        
        $this->view('financiero/presupuestos', [
            'pageTitle' => 'Presupuestos',
            'presupuestos' => $presupuestos,
            'categorias' => $categorias,
            'año' => $anio,
            'errors' => $errors,
            'success' => $success
        ]);
    }
    
    private function getResumen($fechaInicio, $fechaFin) {
        $params = ['fi' => $fechaInicio, 'ff' => $fechaFin];
        
        $ingresos = $this->db->fetch(
            "SELECT COALESCE(SUM(monto), 0) as total FROM transacciones_financieras 
             WHERE tipo = 'ingreso' AND fecha BETWEEN :fi AND :ff",
            $params
        )['total'];
        
        $egresos = $this->db->fetch(
            "SELECT COALESCE(SUM(monto), 0) as total FROM transacciones_financieras 
             WHERE tipo = 'egreso' AND fecha BETWEEN :fi AND :ff",
            $params
        )['total'];
        
        return [
            'ingresos' => $ingresos,
            'egresos' => $egresos,
            'balance' => $ingresos - $egresos
        ];
    }
}
