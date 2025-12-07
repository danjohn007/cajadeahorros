<?php
/**
 * Controlador de Tesorería
 * Gestiona proyecciones financieras y flujos de efectivo
 */

class TesoreriaController extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->requireAuth();
    }

    /**
     * Muestra la vista principal de tesorería
     */
    public function index() {
        $data = [
            'titulo' => 'Tesorería',
            'usuario' => $_SESSION['usuario'] ?? null
        ];
        $this->view('tesoreria/index', $data);
    }

    /**
     * Calcula proyecciones de flujo de efectivo basadas en amortizaciones
     * 
     * @param string $fecha_inicio
     * @param string $fecha_fin
     * @param int $empresa_id (opcional)
     */
    public function obtenerProyecciones() {
        try {
            $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-d');
            $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d', strtotime('+6 months'));
            $empresa_id = $_GET['empresa_id'] ?? null;

            // Obtener proyecciones desde la tabla de amortización
            $sql = "
                SELECT 
                    a.fecha_vencimiento AS fecha,
                    'ingreso' AS tipo,
                    'Cobro de Créditos' AS concepto,
                    DATE_FORMAT(a.fecha_vencimiento, '%Y-%m') AS periodo,
                    SUM(a.monto_capital) AS capital_proyectado,
                    SUM(a.monto_interes) AS interes_proyectado,
                    SUM(a.monto_total) AS monto_proyectado,
                    COUNT(*) AS numero_pagos,
                    c.empresa_id
                FROM amortizacion a
                INNER JOIN creditos c ON a.credito_id = c.id
                WHERE a.estatus = 'pendiente'
                AND c.estatus IN ('activo', 'formalizado')
                AND a.fecha_vencimiento BETWEEN ? AND ?
            ";

            $params = [$fecha_inicio, $fecha_fin];

            if ($empresa_id) {
                $sql .= " AND c.empresa_id = ?";
                $params[] = $empresa_id;
            }

            $sql .= " GROUP BY a.fecha_vencimiento, c.empresa_id
                      ORDER BY a.fecha_vencimiento";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $proyecciones_diarias = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Agrupar por mes
            $sql_mensual = "
                SELECT 
                    DATE_FORMAT(a.fecha_vencimiento, '%Y-%m') AS periodo,
                    DATE_FORMAT(a.fecha_vencimiento, '%Y-%m-01') AS fecha_inicio_mes,
                    SUM(a.monto_capital) AS capital_proyectado,
                    SUM(a.monto_interes) AS interes_proyectado,
                    SUM(a.monto_total) AS monto_total_proyectado,
                    COUNT(DISTINCT a.credito_id) AS creditos_involucrados,
                    COUNT(*) AS numero_pagos
                FROM amortizacion a
                INNER JOIN creditos c ON a.credito_id = c.id
                WHERE a.estatus = 'pendiente'
                AND c.estatus IN ('activo', 'formalizado')
                AND a.fecha_vencimiento BETWEEN ? AND ?
            ";

            if ($empresa_id) {
                $sql_mensual .= " AND c.empresa_id = ?";
            }

            $sql_mensual .= " GROUP BY DATE_FORMAT(a.fecha_vencimiento, '%Y-%m')
                             ORDER BY periodo";

            $stmt = $this->db->prepare($sql_mensual);
            $stmt->execute($params);
            $proyecciones_mensuales = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calcular flujo acumulado
            $acumulado_capital = 0;
            $acumulado_interes = 0;
            $acumulado_total = 0;

            foreach ($proyecciones_mensuales as &$proyeccion) {
                $acumulado_capital += $proyeccion['capital_proyectado'];
                $acumulado_interes += $proyeccion['interes_proyectado'];
                $acumulado_total += $proyeccion['monto_total_proyectado'];

                $proyeccion['acumulado_capital'] = $acumulado_capital;
                $proyeccion['acumulado_interes'] = $acumulado_interes;
                $proyeccion['acumulado_total'] = $acumulado_total;
            }

            $this->jsonResponse([
                'success' => true,
                'proyecciones_diarias' => $proyecciones_diarias,
                'proyecciones_mensuales' => $proyecciones_mensuales,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin
            ]);

        } catch (Exception $e) {
            error_log("Error en obtenerProyecciones: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al obtener proyecciones'
            ], 500);
        }
    }

    /**
     * Obtiene el flujo de efectivo real vs proyectado
     */
    public function obtenerFlujosEfectivo() {
        try {
            $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-d', strtotime('-6 months'));
            $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
            $empresa_id = $_GET['empresa_id'] ?? null;

            // Obtener flujos reales (pagos efectuados)
            $sql = "
                SELECT 
                    DATE_FORMAT(a.fecha_pago, '%Y-%m') AS periodo,
                    'ingreso' AS tipo,
                    'Cobro de Créditos - Real' AS concepto,
                    SUM(a.monto_pagado) AS monto_real,
                    COUNT(*) AS numero_pagos
                FROM amortizacion a
                INNER JOIN creditos c ON a.credito_id = c.id
                WHERE a.estatus = 'pagado'
                AND a.fecha_pago BETWEEN ? AND ?
            ";

            $params = [$fecha_inicio, $fecha_fin];

            if ($empresa_id) {
                $sql .= " AND c.empresa_id = ?";
                $params[] = $empresa_id;
            }

            $sql .= " GROUP BY DATE_FORMAT(a.fecha_pago, '%Y-%m')
                      ORDER BY periodo";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $flujos_reales = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Obtener flujos proyectados para el mismo período
            $sql_proyectado = "
                SELECT 
                    DATE_FORMAT(a.fecha_vencimiento, '%Y-%m') AS periodo,
                    'ingreso' AS tipo,
                    'Cobro de Créditos - Proyectado' AS concepto,
                    SUM(a.monto_total) AS monto_proyectado,
                    COUNT(*) AS numero_pagos
                FROM amortizacion a
                INNER JOIN creditos c ON a.credito_id = c.id
                WHERE a.fecha_vencimiento BETWEEN ? AND ?
            ";

            if ($empresa_id) {
                $sql_proyectado .= " AND c.empresa_id = ?";
            }

            $sql_proyectado .= " GROUP BY DATE_FORMAT(a.fecha_vencimiento, '%Y-%m')
                                ORDER BY periodo";

            $stmt = $this->db->prepare($sql_proyectado);
            $stmt->execute($params);
            $flujos_proyectados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Combinar real vs proyectado
            $comparacion = [];
            foreach ($flujos_proyectados as $proyectado) {
                $periodo = $proyectado['periodo'];
                $comparacion[$periodo] = [
                    'periodo' => $periodo,
                    'monto_proyectado' => $proyectado['monto_proyectado'],
                    'monto_real' => 0,
                    'variacion' => 0,
                    'porcentaje_cumplimiento' => 0
                ];
            }

            foreach ($flujos_reales as $real) {
                $periodo = $real['periodo'];
                if (isset($comparacion[$periodo])) {
                    $comparacion[$periodo]['monto_real'] = $real['monto_real'];
                    $comparacion[$periodo]['variacion'] = $real['monto_real'] - $comparacion[$periodo]['monto_proyectado'];
                    if ($comparacion[$periodo]['monto_proyectado'] > 0) {
                        $comparacion[$periodo]['porcentaje_cumplimiento'] = 
                            round(($real['monto_real'] / $comparacion[$periodo]['monto_proyectado']) * 100, 2);
                    }
                } else {
                    $comparacion[$periodo] = [
                        'periodo' => $periodo,
                        'monto_proyectado' => 0,
                        'monto_real' => $real['monto_real'],
                        'variacion' => $real['monto_real'],
                        'porcentaje_cumplimiento' => 100
                    ];
                }
            }

            $this->jsonResponse([
                'success' => true,
                'comparacion' => array_values($comparacion),
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin
            ]);

        } catch (Exception $e) {
            error_log("Error en obtenerFlujosEfectivo: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al obtener flujos de efectivo'
            ], 500);
        }
    }

    /**
     * Registra una proyección financiera manual
     */
    public function registrarProyeccion() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
                return;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            
            $fecha_proyeccion = $data['fecha_proyeccion'] ?? null;
            $tipo = $data['tipo'] ?? null; // ingreso, egreso
            $concepto = $data['concepto'] ?? null;
            $monto_proyectado = $data['monto_proyectado'] ?? 0;
            $empresa_id = $data['empresa_id'] ?? null;

            if (!$fecha_proyeccion || !$tipo || !$concepto) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Parámetros incompletos'
                ], 400);
                return;
            }

            $usuario_id = $_SESSION['usuario_id'] ?? null;

            $stmt = $this->db->prepare("
                INSERT INTO proyecciones_financieras 
                (fecha_proyeccion, tipo, concepto, monto_proyectado, empresa_id, usuario_id)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $fecha_proyeccion,
                $tipo,
                $concepto,
                $monto_proyectado,
                $empresa_id,
                $usuario_id
            ]);

            $id = $this->db->lastInsertId();

            // Registrar en bitácora
            $this->registrarBitacora(
                $usuario_id,
                'registrar_proyeccion',
                "Proyección de $tipo: $concepto por $" . number_format($monto_proyectado, 2),
                'proyecciones_financieras',
                $id
            );

            $this->jsonResponse([
                'success' => true,
                'message' => 'Proyección registrada correctamente',
                'id' => $id
            ]);

        } catch (Exception $e) {
            error_log("Error en registrarProyeccion: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al registrar proyección'
            ], 500);
        }
    }

    /**
     * Obtiene el resumen de cartera para tesorería
     */
    public function obtenerResumenCartera() {
        try {
            $empresa_id = $_GET['empresa_id'] ?? null;

            $sql = "
                SELECT 
                    COUNT(*) AS total_creditos,
                    SUM(CASE WHEN tipo_cartera = 'vigente' THEN 1 ELSE 0 END) AS creditos_vigentes,
                    SUM(CASE WHEN tipo_cartera = 'vencida' THEN 1 ELSE 0 END) AS creditos_vencidos,
                    SUM(saldo_actual) AS saldo_total,
                    SUM(CASE WHEN tipo_cartera = 'vigente' THEN saldo_actual ELSE 0 END) AS saldo_vigente,
                    SUM(CASE WHEN tipo_cartera = 'vencida' THEN saldo_actual ELSE 0 END) AS saldo_vencido,
                    AVG(dias_mora) AS dias_mora_promedio,
                    SUM(CASE WHEN dias_mora > 0 THEN 1 ELSE 0 END) AS creditos_con_mora
                FROM creditos
                WHERE estatus IN ('activo', 'formalizado')
            ";

            $params = [];
            if ($empresa_id) {
                $sql .= " AND empresa_id = ?";
                $params[] = $empresa_id;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $resumen = $stmt->fetch(PDO::FETCH_ASSOC);

            // Calcular indicadores
            if ($resumen['saldo_total'] > 0) {
                $resumen['porcentaje_cartera_vigente'] = round(($resumen['saldo_vigente'] / $resumen['saldo_total']) * 100, 2);
                $resumen['porcentaje_cartera_vencida'] = round(($resumen['saldo_vencido'] / $resumen['saldo_total']) * 100, 2);
            } else {
                $resumen['porcentaje_cartera_vigente'] = 0;
                $resumen['porcentaje_cartera_vencida'] = 0;
            }

            $this->jsonResponse([
                'success' => true,
                'resumen' => $resumen
            ]);

        } catch (Exception $e) {
            error_log("Error en obtenerResumenCartera: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al obtener resumen de cartera'
            ], 500);
        }
    }
}
