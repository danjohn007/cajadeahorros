<?php
/**
 * Controlador de Políticas de Crédito
 * Gestiona validaciones de políticas y reglas de negocio para créditos
 */

class PoliticasCreditoController extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->requireAuth();
    }

    /**
     * Valida si un solicitante cumple con las políticas de edad para el plazo solicitado
     * 
     * @param int $socio_id ID del socio
     * @param int $plazo_meses Plazo solicitado en meses
     * @return array ['valido' => bool, 'mensaje' => string, 'plazo_maximo' => int]
     */
    public function validarEdadPlazo($socio_id = null, $plazo_meses = null) {
        try {
            // Si se recibe por POST (llamada AJAX)
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                $socio_id = $data['socio_id'] ?? null;
                $plazo_meses = $data['plazo_meses'] ?? null;
            }

            if (!$socio_id || !$plazo_meses) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Parámetros incompletos'
                ], 400);
                return;
            }

            // Obtener edad del socio
            $stmt = $this->db->prepare("
                SELECT 
                    id,
                    nombre,
                    apellido_paterno,
                    apellido_materno,
                    fecha_nacimiento,
                    TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad
                FROM socios 
                WHERE id = ?
            ");
            $stmt->execute([$socio_id]);
            $socio = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$socio) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Socio no encontrado'
                ], 404);
                return;
            }

            if (!$socio['fecha_nacimiento']) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'El socio no tiene registrada su fecha de nacimiento',
                    'require_birth_date' => true
                ], 400);
                return;
            }

            $edad = $socio['edad'];
            $nombre_completo = trim("{$socio['nombre']} {$socio['apellido_paterno']} {$socio['apellido_materno']}");

            // Aplicar regla de negocio: edad > 69 años → plazo máximo 12 meses
            $plazo_maximo = $edad > 69 ? 12 : 360; // 360 meses = 30 años (plazo máximo general)

            $valido = $plazo_meses <= $plazo_maximo;

            $mensaje = '';
            if (!$valido) {
                $mensaje = "El solicitante {$nombre_completo} tiene {$edad} años. ";
                $mensaje .= "Por políticas de la institución, solicitantes mayores de 69 años ";
                $mensaje .= "solo pueden acceder a créditos con plazo máximo de 12 meses. ";
                $mensaje .= "El plazo solicitado es de {$plazo_meses} meses.";
            }

            $this->jsonResponse([
                'success' => true,
                'valido' => $valido,
                'mensaje' => $mensaje,
                'edad' => $edad,
                'plazo_solicitado' => $plazo_meses,
                'plazo_maximo' => $plazo_maximo,
                'nombre_completo' => $nombre_completo
            ]);

        } catch (Exception $e) {
            error_log("Error en validarEdadPlazo: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al validar edad y plazo'
            ], 500);
        }
    }

    /**
     * Valida si un crédito requiere aval según el monto solicitado
     * 
     * @param int $producto_id ID del producto financiero
     * @param float $monto Monto solicitado
     * @return array ['requiere_aval' => bool, 'mensaje' => string]
     */
    public function validarRequiereAval() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
                return;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $producto_id = $data['producto_id'] ?? null;
            $monto = $data['monto'] ?? 0;

            if (!$producto_id) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Producto no especificado'
                ], 400);
                return;
            }

            // Obtener umbral del producto
            $stmt = $this->db->prepare("
                SELECT 
                    id,
                    nombre,
                    requiere_aval,
                    monto_requiere_aval
                FROM productos_financieros 
                WHERE id = ? AND activo = 1
            ");
            $stmt->execute([$producto_id]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$producto) {
                // Si no existe en productos_financieros, buscar en tipos_credito (compatibilidad)
                $stmt = $this->db->prepare("
                    SELECT 
                        id,
                        nombre,
                        monto_maximo
                    FROM tipos_credito 
                    WHERE id = ?
                ");
                $stmt->execute([$producto_id]);
                $tipo_credito = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$tipo_credito) {
                    $this->jsonResponse([
                        'success' => false,
                        'message' => 'Producto no encontrado'
                    ], 404);
                    return;
                }

                // Umbral por defecto: 100,000
                $umbral = 100000;
                $requiere_aval = $monto > $umbral;

                $this->jsonResponse([
                    'success' => true,
                    'requiere_aval' => $requiere_aval,
                    'mensaje' => $requiere_aval ? 
                        "El monto solicitado ($" . number_format($monto, 2) . ") excede el umbral de $" . number_format($umbral, 2) . ". Se requiere aval u obligado solidario." : 
                        "El monto no requiere aval",
                    'umbral' => $umbral
                ]);
                return;
            }

            $requiere_aval = false;
            $mensaje = "El monto no requiere aval";

            if ($producto['requiere_aval'] && $producto['monto_requiere_aval']) {
                if ($monto > $producto['monto_requiere_aval']) {
                    $requiere_aval = true;
                    $mensaje = "El monto solicitado ($" . number_format($monto, 2) . ") excede el umbral de $" . 
                               number_format($producto['monto_requiere_aval'], 2) . 
                               " del producto {$producto['nombre']}. Se requiere aval u obligado solidario.";
                }
            }

            $this->jsonResponse([
                'success' => true,
                'requiere_aval' => $requiere_aval,
                'mensaje' => $mensaje,
                'umbral' => $producto['monto_requiere_aval'],
                'producto' => $producto['nombre']
            ]);

        } catch (Exception $e) {
            error_log("Error en validarRequiereAval: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al validar requerimiento de aval'
            ], 500);
        }
    }

    /**
     * Obtiene el checklist aplicable para un tipo de operación
     * 
     * @param string $tipo_operacion apertura, renovacion, reestructura
     * @param int $producto_id (opcional)
     */
    public function obtenerChecklist() {
        try {
            $tipo_operacion = $_GET['tipo_operacion'] ?? 'apertura';
            $producto_id = $_GET['producto_id'] ?? null;

            $sql = "
                SELECT 
                    c.id,
                    c.nombre,
                    c.descripcion,
                    c.tipo_operacion,
                    ci.id AS item_id,
                    ci.descripcion AS item_descripcion,
                    ci.tipo AS item_tipo,
                    ci.requerido AS item_requerido,
                    ci.orden AS item_orden
                FROM checklists_credito c
                INNER JOIN checklist_items ci ON c.id = ci.checklist_id
                WHERE c.activo = 1 
                AND c.tipo_operacion = ?
            ";

            $params = [$tipo_operacion];

            if ($producto_id) {
                $sql .= " AND (c.producto_id = ? OR c.producto_id IS NULL)";
                $params[] = $producto_id;
            } else {
                $sql .= " AND c.producto_id IS NULL";
            }

            $sql .= " ORDER BY c.orden, ci.orden";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Agrupar por checklist
            $checklists = [];
            foreach ($results as $row) {
                $checklist_id = $row['id'];
                if (!isset($checklists[$checklist_id])) {
                    $checklists[$checklist_id] = [
                        'id' => $row['id'],
                        'nombre' => $row['nombre'],
                        'descripcion' => $row['descripcion'],
                        'tipo_operacion' => $row['tipo_operacion'],
                        'items' => []
                    ];
                }
                $checklists[$checklist_id]['items'][] = [
                    'id' => $row['item_id'],
                    'descripcion' => $row['item_descripcion'],
                    'tipo' => $row['item_tipo'],
                    'requerido' => (bool)$row['item_requerido'],
                    'orden' => $row['item_orden']
                ];
            }

            $this->jsonResponse([
                'success' => true,
                'checklists' => array_values($checklists)
            ]);

        } catch (Exception $e) {
            error_log("Error en obtenerChecklist: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al obtener checklist'
            ], 500);
        }
    }

    /**
     * Valida el checklist completo de un crédito
     * 
     * @param int $credito_id
     */
    public function validarChecklistCredito() {
        try {
            $credito_id = $_GET['credito_id'] ?? null;

            if (!$credito_id) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Crédito no especificado'
                ], 400);
                return;
            }

            // Obtener validaciones del crédito
            $stmt = $this->db->prepare("
                SELECT 
                    cv.id,
                    cv.completado,
                    cv.fecha_completado,
                    ci.descripcion,
                    ci.tipo,
                    ci.requerido,
                    u.nombre AS validado_por_nombre
                FROM checklist_validaciones cv
                INNER JOIN checklist_items ci ON cv.checklist_item_id = ci.id
                LEFT JOIN usuarios u ON cv.validado_por = u.id
                WHERE cv.credito_id = ?
                ORDER BY ci.orden
            ");
            $stmt->execute([$credito_id]);
            $validaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $total_items = count($validaciones);
            $items_completados = 0;
            $items_pendientes = [];

            foreach ($validaciones as $val) {
                if ($val['completado']) {
                    $items_completados++;
                } else if ($val['requerido']) {
                    $items_pendientes[] = $val['descripcion'];
                }
            }

            $checklist_completo = (count($items_pendientes) === 0);
            $porcentaje_completado = $total_items > 0 ? round(($items_completados / $total_items) * 100, 2) : 0;

            $this->jsonResponse([
                'success' => true,
                'checklist_completo' => $checklist_completo,
                'total_items' => $total_items,
                'items_completados' => $items_completados,
                'items_pendientes' => $items_pendientes,
                'porcentaje_completado' => $porcentaje_completado,
                'validaciones' => $validaciones
            ]);

        } catch (Exception $e) {
            error_log("Error en validarChecklistCredito: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al validar checklist'
            ], 500);
        }
    }

    /**
     * Marca un item del checklist como completado
     */
    public function marcarItemCompletado() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
                return;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $credito_id = $data['credito_id'] ?? null;
            $checklist_item_id = $data['checklist_item_id'] ?? null;
            $completado = $data['completado'] ?? true;
            $observaciones = $data['observaciones'] ?? null;

            if (!$credito_id || !$checklist_item_id) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Parámetros incompletos'
                ], 400);
                return;
            }

            // Verificar si ya existe el registro
            $stmt = $this->db->prepare("
                SELECT id FROM checklist_validaciones 
                WHERE credito_id = ? AND checklist_item_id = ?
            ");
            $stmt->execute([$credito_id, $checklist_item_id]);
            $existe = $stmt->fetch(PDO::FETCH_ASSOC);

            $usuario_id = $_SESSION['usuario_id'] ?? null;

            if ($existe) {
                // Actualizar
                $stmt = $this->db->prepare("
                    UPDATE checklist_validaciones 
                    SET completado = ?,
                        fecha_completado = ?,
                        validado_por = ?,
                        observaciones = ?,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?
                ");
                $stmt->execute([
                    $completado,
                    $completado ? date('Y-m-d H:i:s') : null,
                    $usuario_id,
                    $observaciones,
                    $existe['id']
                ]);
            } else {
                // Insertar
                $stmt = $this->db->prepare("
                    INSERT INTO checklist_validaciones 
                    (credito_id, checklist_item_id, completado, fecha_completado, validado_por, observaciones)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $credito_id,
                    $checklist_item_id,
                    $completado,
                    $completado ? date('Y-m-d H:i:s') : null,
                    $usuario_id,
                    $observaciones
                ]);
            }

            // Registrar en bitácora
            $this->registrarBitacora(
                $usuario_id,
                'marcar_checklist_item',
                "Item de checklist " . ($completado ? 'completado' : 'pendiente'),
                'creditos',
                $credito_id
            );

            $this->jsonResponse([
                'success' => true,
                'message' => 'Item actualizado correctamente'
            ]);

        } catch (Exception $e) {
            error_log("Error en marcarItemCompletado: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al actualizar item del checklist'
            ], 500);
        }
    }
}
