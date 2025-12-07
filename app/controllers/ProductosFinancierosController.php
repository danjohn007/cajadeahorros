<?php
/**
 * Controlador de Productos Financieros
 * Gestión de productos, tasas, comisiones y esquemas de amortización
 */

require_once CORE_PATH . '/Controller.php';

class ProductosFinancierosController extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->requireAuth();
    }

    /**
     * Vista principal de productos financieros
     */
    public function index() {
        $productos = $this->db->fetchAll(
            "SELECT pf.*, e.nombre as empresa_nombre,
                    COUNT(DISTINCT c.id) as total_creditos,
                    COALESCE(SUM(c.monto_autorizado), 0) as monto_total
             FROM productos_financieros pf
             JOIN empresas_grupo e ON pf.empresa_id = e.id
             LEFT JOIN creditos c ON pf.id = c.producto_financiero_id
             GROUP BY pf.id
             ORDER BY pf.activo DESC, pf.nombre ASC"
        );
        
        $this->view('productos_financieros/index', [
            'pageTitle' => 'Productos Financieros',
            'productos' => $productos
        ]);
    }

    /**
     * Configuración de créditos
     */
    public function creditos() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $empresa_id = $_POST['empresa_id'] ?? null;
                $nombre = $_POST['nombre'] ?? '';
                $tipo = $_POST['tipo'] ?? 'credito';
                $descripcion = $_POST['descripcion'] ?? '';
                $tasa_interes_min = $_POST['tasa_interes_min'] ?? 0;
                $tasa_interes_max = $_POST['tasa_interes_max'] ?? 0;
                $plazo_min_meses = $_POST['plazo_min_meses'] ?? 1;
                $plazo_max_meses = $_POST['plazo_max_meses'] ?? 12;
                $monto_min = $_POST['monto_min'] ?? 0;
                $monto_max = $_POST['monto_max'] ?? 0;
                $requiere_aval = $_POST['requiere_aval'] ?? 0;
                $monto_requiere_aval = $_POST['monto_requiere_aval'] ?? 0;
                $comision_apertura = $_POST['comision_apertura'] ?? 0;
                
                if (!$empresa_id || !$nombre) {
                    throw new Exception('Datos incompletos');
                }
                
                $id = $this->db->insert('productos_financieros', [
                    'empresa_id' => $empresa_id,
                    'nombre' => $nombre,
                    'tipo' => $tipo,
                    'descripcion' => $descripcion,
                    'tasa_interes_min' => $tasa_interes_min,
                    'tasa_interes_max' => $tasa_interes_max,
                    'plazo_min_meses' => $plazo_min_meses,
                    'plazo_max_meses' => $plazo_max_meses,
                    'monto_min' => $monto_min,
                    'monto_max' => $monto_max,
                    'requiere_aval' => $requiere_aval,
                    'monto_requiere_aval' => $monto_requiere_aval,
                    'comision_apertura' => $comision_apertura,
                    'activo' => 1
                ]);
                
                $this->logAction(
                    $_SESSION['user_id'],
                    'crear_producto',
                    "Producto financiero creado: $nombre",
                    'productos_financieros',
                    $id
                );
                
                $this->setFlash('success', 'Producto creado correctamente');
                $this->redirect('/productos-financieros/creditos');
            } catch (Exception $e) {
                $this->setFlash('error', 'Error al crear producto: ' . $e->getMessage());
            }
        }
        
        $productos = $this->db->fetchAll(
            "SELECT pf.*, e.nombre as empresa_nombre
             FROM productos_financieros pf
             JOIN empresas_grupo e ON pf.empresa_id = e.id
             WHERE pf.tipo = 'credito'
             ORDER BY pf.activo DESC, pf.nombre"
        );
        
        $empresas = $this->db->fetchAll(
            "SELECT id, nombre FROM empresas_grupo WHERE activo = 1"
        );
        
        $this->view('productos_financieros/creditos', [
            'pageTitle' => 'Configuración de Créditos',
            'productos' => $productos,
            'empresas' => $empresas
        ]);
    }

    /**
     * Gestión de tasas de interés y comisiones
     */
    public function tasas($id = null) {
        $producto = null;
        if ($id) {
            $producto = $this->db->fetch(
                "SELECT pf.*, e.nombre as empresa_nombre
                 FROM productos_financieros pf
                 JOIN empresas_grupo e ON pf.empresa_id = e.id
                 WHERE pf.id = ?",
                [$id]
            );
            
            if (!$producto) {
                $this->redirect('/productos-financieros');
                return;
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $producto_id = $_POST['producto_id'] ?? null;
                $tasa_interes_min = $_POST['tasa_interes_min'] ?? 0;
                $tasa_interes_max = $_POST['tasa_interes_max'] ?? 0;
                $comision_apertura = $_POST['comision_apertura'] ?? 0;
                
                if (!$producto_id) {
                    throw new Exception('Producto no especificado');
                }
                
                $this->db->update('productos_financieros', $producto_id, [
                    'tasa_interes_min' => $tasa_interes_min,
                    'tasa_interes_max' => $tasa_interes_max,
                    'comision_apertura' => $comision_apertura
                ]);
                
                $this->logAction(
                    $_SESSION['user_id'],
                    'actualizar_tasas',
                    "Tasas actualizadas para producto #$producto_id",
                    'productos_financieros',
                    $producto_id
                );
                
                $this->setFlash('success', 'Tasas actualizadas correctamente');
                $this->redirect('/productos-financieros/tasas/' . $producto_id);
            } catch (Exception $e) {
                $this->setFlash('error', 'Error al actualizar tasas');
            }
        }
        
        $productos = $this->db->fetchAll(
            "SELECT pf.id, pf.nombre, e.nombre as empresa_nombre
             FROM productos_financieros pf
             JOIN empresas_grupo e ON pf.empresa_id = e.id
             WHERE pf.activo = 1
             ORDER BY e.nombre, pf.nombre"
        );
        
        $this->view('productos_financieros/tasas', [
            'pageTitle' => 'Tasas de Interés y Comisiones',
            'producto' => $producto,
            'productos' => $productos
        ]);
    }

    /**
     * Configuración de plazos y condiciones
     */
    public function plazos($id = null) {
        $producto = null;
        if ($id) {
            $producto = $this->db->fetch(
                "SELECT * FROM productos_financieros WHERE id = ?",
                [$id]
            );
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $producto_id = $_POST['producto_id'] ?? null;
                $plazo_min_meses = $_POST['plazo_min_meses'] ?? 1;
                $plazo_max_meses = $_POST['plazo_max_meses'] ?? 12;
                $monto_min = $_POST['monto_min'] ?? 0;
                $monto_max = $_POST['monto_max'] ?? 0;
                $requiere_aval = $_POST['requiere_aval'] ?? 0;
                $monto_requiere_aval = $_POST['monto_requiere_aval'] ?? 0;
                
                if (!$producto_id) {
                    throw new Exception('Producto no especificado');
                }
                
                $this->db->update('productos_financieros', $producto_id, [
                    'plazo_min_meses' => $plazo_min_meses,
                    'plazo_max_meses' => $plazo_max_meses,
                    'monto_min' => $monto_min,
                    'monto_max' => $monto_max,
                    'requiere_aval' => $requiere_aval,
                    'monto_requiere_aval' => $monto_requiere_aval
                ]);
                
                $this->logAction(
                    $_SESSION['user_id'],
                    'actualizar_plazos',
                    "Plazos y condiciones actualizados para producto #$producto_id",
                    'productos_financieros',
                    $producto_id
                );
                
                $this->setFlash('success', 'Plazos actualizados correctamente');
                $this->redirect('/productos-financieros/plazos/' . $producto_id);
            } catch (Exception $e) {
                $this->setFlash('error', 'Error al actualizar plazos');
            }
        }
        
        $productos = $this->db->fetchAll(
            "SELECT id, nombre FROM productos_financieros WHERE activo = 1"
        );
        
        $this->view('productos_financieros/plazos', [
            'pageTitle' => 'Plazos y Condiciones',
            'producto' => $producto,
            'productos' => $productos
        ]);
    }

    /**
     * Diseño de esquemas de amortización
     */
    public function amortizacion() {
        $this->view('productos_financieros/amortizacion', [
            'pageTitle' => 'Esquemas de Amortización'
        ]);
    }

    /**
     * Administración de beneficios y promociones
     */
    public function beneficios() {
        $this->view('productos_financieros/beneficios', [
            'pageTitle' => 'Beneficios y Promociones'
        ]);
    }
}
