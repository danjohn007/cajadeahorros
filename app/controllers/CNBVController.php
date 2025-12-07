<?php
/**
 * Controlador de Reportes Regulatorios CNBV
 * Gestiona la generación de reportes para la Comisión Nacional Bancaria y de Valores
 */

class CNBVController extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        
        // Solo administradores pueden acceder a reportes regulatorios
        if (!isset($_SESSION['usuario']) || !is_array($_SESSION['usuario'])) {
            http_response_code(401);
            die('Sesión inválida');
        }
        
        if (($_SESSION['usuario']['rol'] ?? '') !== 'administrador') {
            http_response_code(403);
            die('Acceso denegado');
        }
    }

    /**
     * Muestra la vista principal de reportes CNBV
     */
    public function index() {
        $data = [
            'titulo' => 'Reportes Regulatorios CNBV',
            'usuario' => $_SESSION['usuario'] ?? null
        ];
        $this->view('cnbv/index', $data);
    }

    /**
     * Lista los reportes CNBV generados
     */
    public function listarReportes() {
        try {
            $periodo = $_GET['periodo'] ?? null;
            $tipo_reporte = $_GET['tipo_reporte'] ?? null;
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 50;
            $offset = ($page - 1) * $limit;

            $sql = "
                SELECT 
                    r.id,
                    r.periodo,
                    r.tipo_reporte,
                    r.fecha_generacion,
                    r.archivo,
                    r.formato,
                    r.estatus,
                    r.fecha_envio,
                    u.nombre AS generado_por,
                    r.observaciones
                FROM reportes_cnbv r
                LEFT JOIN usuarios u ON r.usuario_id = u.id
                WHERE 1=1
            ";

            $params = [];

            if ($periodo) {
                $sql .= " AND r.periodo = ?";
                $params[] = $periodo;
            }

            if ($tipo_reporte) {
                $sql .= " AND r.tipo_reporte = ?";
                $params[] = $tipo_reporte;
            }

            $sql .= " ORDER BY r.fecha_generacion DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $reportes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Contar total
            $sqlCount = "SELECT COUNT(*) as total FROM reportes_cnbv WHERE 1=1";
            $paramsCount = [];
            
            if ($periodo) {
                $sqlCount .= " AND periodo = ?";
                $paramsCount[] = $periodo;
            }
            if ($tipo_reporte) {
                $sqlCount .= " AND tipo_reporte = ?";
                $paramsCount[] = $tipo_reporte;
            }

            $stmt = $this->db->prepare($sqlCount);
            $stmt->execute($paramsCount);
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            $this->jsonResponse([
                'success' => true,
                'reportes' => $reportes,
                'total' => $total,
                'page' => $page,
                'limit' => $limit
            ]);

        } catch (Exception $e) {
            error_log("Error en listarReportes: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al listar reportes'
            ], 500);
        }
    }

    /**
     * Genera reporte de situación financiera
     */
    public function generarReporteSituacionFinanciera() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
                return;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $periodo = $data['periodo'] ?? date('Y-m');
            $formato = $data['formato'] ?? 'EXCEL';

            // Obtener datos para el reporte
            $datos_reporte = $this->obtenerDatosSituacionFinanciera($periodo);

            // Generar ID del reporte
            $usuario_id = $_SESSION['usuario_id'] ?? null;

            // Insertar registro del reporte
            $stmt = $this->db->prepare("
                INSERT INTO reportes_cnbv 
                (periodo, tipo_reporte, fecha_generacion, formato, estatus, usuario_id)
                VALUES (?, ?, NOW(), ?, 'generado', ?)
            ");

            $stmt->execute([
                $periodo,
                'Situación Financiera',
                $formato,
                $usuario_id
            ]);

            $reporte_id = $this->db->lastInsertId();

            // Guardar detalle del reporte
            $this->guardarDetalleReporte($reporte_id, $datos_reporte);

            // Generar archivo según formato
            $archivo = $this->generarArchivoReporte($reporte_id, $periodo, $formato, $datos_reporte);

            // Actualizar con nombre de archivo
            $stmt = $this->db->prepare("
                UPDATE reportes_cnbv 
                SET archivo = ?
                WHERE id = ?
            ");
            $stmt->execute([$archivo, $reporte_id]);

            // Registrar en bitácora
            $this->registrarBitacora(
                $usuario_id,
                'generar_reporte_cnbv',
                "Reporte CNBV de Situación Financiera - Período: $periodo",
                'reportes_cnbv',
                $reporte_id
            );

            $this->jsonResponse([
                'success' => true,
                'message' => 'Reporte generado correctamente',
                'reporte_id' => $reporte_id,
                'archivo' => $archivo
            ]);

        } catch (Exception $e) {
            error_log("Error en generarReporteSituacionFinanciera: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al generar reporte: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Genera reporte de cartera crediticia
     */
    public function generarReporteCartera() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
                return;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $periodo = $data['periodo'] ?? date('Y-m');
            $formato = $data['formato'] ?? 'EXCEL';

            // Obtener datos para el reporte
            $datos_reporte = $this->obtenerDatosCartera($periodo);

            $usuario_id = $_SESSION['usuario_id'] ?? null;

            // Insertar registro del reporte
            $stmt = $this->db->prepare("
                INSERT INTO reportes_cnbv 
                (periodo, tipo_reporte, fecha_generacion, formato, estatus, usuario_id)
                VALUES (?, ?, NOW(), ?, 'generado', ?)
            ");

            $stmt->execute([
                $periodo,
                'Cartera Crediticia',
                $formato,
                $usuario_id
            ]);

            $reporte_id = $this->db->lastInsertId();

            // Guardar detalle del reporte
            $this->guardarDetalleReporte($reporte_id, $datos_reporte);

            // Generar archivo según formato
            $archivo = $this->generarArchivoReporte($reporte_id, $periodo, $formato, $datos_reporte);

            // Actualizar con nombre de archivo
            $stmt = $this->db->prepare("
                UPDATE reportes_cnbv 
                SET archivo = ?
                WHERE id = ?
            ");
            $stmt->execute([$archivo, $reporte_id]);

            // Registrar en bitácora
            $this->registrarBitacora(
                $usuario_id,
                'generar_reporte_cnbv',
                "Reporte CNBV de Cartera Crediticia - Período: $periodo",
                'reportes_cnbv',
                $reporte_id
            );

            $this->jsonResponse([
                'success' => true,
                'message' => 'Reporte de cartera generado correctamente',
                'reporte_id' => $reporte_id,
                'archivo' => $archivo
            ]);

        } catch (Exception $e) {
            error_log("Error en generarReporteCartera: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al generar reporte: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene datos de situación financiera para el período
     */
    private function obtenerDatosSituacionFinanciera($periodo) {
        // Extraer año y mes del periodo (formato YYYY-MM)
        list($anio, $mes) = explode('-', $periodo);

        $datos = [];

        // 1. Total de activos (cartera vigente + vencida)
        $stmt = $this->db->prepare("
            SELECT 
                SUM(saldo_actual) AS total_cartera,
                SUM(CASE WHEN tipo_cartera = 'vigente' THEN saldo_actual ELSE 0 END) AS cartera_vigente,
                SUM(CASE WHEN tipo_cartera = 'vencida' THEN saldo_actual ELSE 0 END) AS cartera_vencida
            FROM creditos
            WHERE estatus IN ('activo', 'formalizado')
            AND YEAR(fecha_formalizacion) <= ? AND MONTH(fecha_formalizacion) <= ?
        ");
        $stmt->execute([$anio, $mes]);
        $cartera = $stmt->fetch(PDO::FETCH_ASSOC);

        $datos['total_activos'] = $cartera['total_cartera'] ?? 0;
        $datos['cartera_vigente'] = $cartera['cartera_vigente'] ?? 0;
        $datos['cartera_vencida'] = $cartera['cartera_vencida'] ?? 0;

        // 2. Número de clientes activos
        $stmt = $this->db->prepare("
            SELECT COUNT(DISTINCT socio_id) AS clientes_activos
            FROM creditos
            WHERE estatus IN ('activo', 'formalizado')
            AND YEAR(fecha_formalizacion) <= ? AND MONTH(fecha_formalizacion) <= ?
        ");
        $stmt->execute([$anio, $mes]);
        $datos['clientes_activos'] = $stmt->fetch(PDO::FETCH_ASSOC)['clientes_activos'] ?? 0;

        // 3. Número de créditos activos
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS creditos_activos
            FROM creditos
            WHERE estatus IN ('activo', 'formalizado')
            AND YEAR(fecha_formalizacion) <= ? AND MONTH(fecha_formalizacion) <= ?
        ");
        $stmt->execute([$anio, $mes]);
        $datos['creditos_activos'] = $stmt->fetch(PDO::FETCH_ASSOC)['creditos_activos'] ?? 0;

        // 4. Créditos otorgados en el período
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) AS creditos_otorgados,
                SUM(monto_autorizado) AS monto_otorgado
            FROM creditos
            WHERE YEAR(fecha_formalizacion) = ? AND MONTH(fecha_formalizacion) = ?
        ");
        $stmt->execute([$anio, $mes]);
        $otorgados = $stmt->fetch(PDO::FETCH_ASSOC);
        $datos['creditos_otorgados_periodo'] = $otorgados['creditos_otorgados'] ?? 0;
        $datos['monto_otorgado_periodo'] = $otorgados['monto_otorgado'] ?? 0;

        return $datos;
    }

    /**
     * Obtiene datos de cartera para el período
     */
    private function obtenerDatosCartera($periodo) {
        list($anio, $mes) = explode('-', $periodo);

        $stmt = $this->db->prepare("
            SELECT 
                c.numero_credito,
                c.fecha_formalizacion,
                c.monto_autorizado,
                c.saldo_actual,
                c.tasa_interes,
                c.plazo_meses,
                c.tipo_cartera,
                c.dias_mora,
                s.nombre,
                s.apellido_paterno,
                s.apellido_materno,
                s.rfc,
                tc.nombre AS tipo_credito
            FROM creditos c
            INNER JOIN socios s ON c.socio_id = s.id
            LEFT JOIN tipos_credito tc ON c.tipo_credito_id = tc.id
            WHERE c.estatus IN ('activo', 'formalizado')
            AND YEAR(c.fecha_formalizacion) <= ? AND MONTH(c.fecha_formalizacion) <= ?
            ORDER BY c.fecha_formalizacion DESC
        ");
        $stmt->execute([$anio, $mes]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Guarda el detalle del reporte
     */
    private function guardarDetalleReporte($reporte_id, $datos) {
        $stmt = $this->db->prepare("
            INSERT INTO reportes_cnbv_detalle (reporte_id, concepto, valor, orden)
            VALUES (?, ?, ?, ?)
        ");

        $orden = 1;
        foreach ($datos as $concepto => $valor) {
            if (is_array($valor)) {
                $valor = json_encode($valor);
            }
            $stmt->execute([$reporte_id, $concepto, $valor, $orden]);
            $orden++;
        }
    }

    /**
     * Genera el archivo del reporte en el formato especificado
     */
    private function generarArchivoReporte($reporte_id, $periodo, $formato, $datos) {
        $directorio = __DIR__ . '/../../uploads/reportes_cnbv/';
        if (!file_exists($directorio)) {
            mkdir($directorio, 0755, true);
        }

        $nombre_archivo = "CNBV_{$reporte_id}_{$periodo}." . strtolower($formato);
        $ruta_completa = $directorio . $nombre_archivo;

        if ($formato === 'EXCEL') {
            $this->generarExcel($ruta_completa, $datos);
        } else if ($formato === 'XML') {
            $this->generarXML($ruta_completa, $datos);
        }

        return $nombre_archivo;
    }

    /**
     * Genera archivo Excel
     */
    private function generarExcel($ruta, $datos) {
        // Crear archivo CSV como alternativa simple a Excel
        $fp = fopen($ruta, 'w');
        
        // Encabezados
        fputcsv($fp, ['Concepto', 'Valor']);
        
        // Datos
        foreach ($datos as $concepto => $valor) {
            if (is_array($valor)) {
                continue; // Saltear arrays complejos en CSV simple
            }
            fputcsv($fp, [$concepto, $valor]);
        }
        
        fclose($fp);
    }

    /**
     * Genera archivo XML
     */
    private function generarXML($ruta, $datos) {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><ReporteCNBV></ReporteCNBV>');
        
        foreach ($datos as $concepto => $valor) {
            if (is_array($valor)) {
                $elemento = $xml->addChild($concepto);
                foreach ($valor as $k => $v) {
                    $elemento->addChild($k, htmlspecialchars($v));
                }
            } else {
                $xml->addChild($concepto, htmlspecialchars($valor));
            }
        }
        
        $xml->asXML($ruta);
    }
}
