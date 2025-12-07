<?php
/**
 * Controlador de Entidades (Empresas del Grupo)
 * Gestión de estructura organizacional multiempresa
 */

require_once CORE_PATH . '/Controller.php';

class EntidadesController extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->requireAuth();
    }

    /**
     * Vista principal de entidades
     */
    public function index() {
        $empresas = $this->db->fetchAll(
            "SELECT e.*, 
                    COUNT(DISTINCT un.id) as total_unidades,
                    COUNT(DISTINCT c.id) as total_creditos
             FROM empresas_grupo e
             LEFT JOIN unidades_negocio un ON e.id = un.empresa_id
             LEFT JOIN creditos c ON e.id = c.empresa_id
             GROUP BY e.id
             ORDER BY e.activo DESC, e.nombre ASC"
        );
        
        $this->view('entidades/index', [
            'pageTitle' => 'Empresas del Grupo',
            'empresas' => $empresas
        ]);
    }

    /**
     * Administración de empresas del grupo
     */
    public function empresas() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre = $_POST['nombre'] ?? '';
                $nombre_corto = $_POST['nombre_corto'] ?? '';
                $rfc = $_POST['rfc'] ?? '';
                $razon_social = $_POST['razon_social'] ?? '';
                $direccion = $_POST['direccion'] ?? '';
                $telefono = $_POST['telefono'] ?? '';
                $email = $_POST['email'] ?? '';
                $sitio_web = $_POST['sitio_web'] ?? '';
                
                if (!$nombre) {
                    throw new Exception('El nombre de la empresa es requerido');
                }
                
                $id = $this->db->insert('empresas_grupo', [
                    'nombre' => $nombre,
                    'nombre_corto' => $nombre_corto,
                    'rfc' => $rfc,
                    'razon_social' => $razon_social,
                    'direccion' => $direccion,
                    'telefono' => $telefono,
                    'email' => $email,
                    'sitio_web' => $sitio_web,
                    'activo' => 1
                ]);
                
                $this->logAction(
                    $_SESSION['user_id'],
                    'crear_empresa',
                    "Empresa creada: $nombre",
                    'empresas_grupo',
                    $id
                );
                
                $this->setFlashMessage('Empresa creada correctamente', 'success');
                $this->redirect('/entidades/empresas');
            } catch (Exception $e) {
                $this->setFlashMessage('Error al crear empresa: ' . $e->getMessage(), 'error');
            }
        }
        
        $empresas = $this->db->fetchAll(
            "SELECT * FROM empresas_grupo ORDER BY activo DESC, nombre ASC"
        );
        
        $this->view('entidades/empresas', [
            'pageTitle' => 'Administración de Empresas',
            'empresas' => $empresas
        ]);
    }

    /**
     * Gestión de unidades de negocio
     */
    public function unidades() {
        $empresa_id = $_GET['empresa_id'] ?? null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $empresa_id = $_POST['empresa_id'] ?? null;
                $nombre = $_POST['nombre'] ?? '';
                $clave = $_POST['clave'] ?? '';
                $tipo = $_POST['tipo'] ?? '';
                $direccion = $_POST['direccion'] ?? '';
                $telefono = $_POST['telefono'] ?? '';
                $responsable = $_POST['responsable'] ?? '';
                
                if (!$empresa_id || !$nombre) {
                    throw new Exception('Datos incompletos');
                }
                
                $id = $this->db->insert('unidades_negocio', [
                    'empresa_id' => $empresa_id,
                    'nombre' => $nombre,
                    'clave' => $clave,
                    'tipo' => $tipo,
                    'direccion' => $direccion,
                    'telefono' => $telefono,
                    'responsable' => $responsable,
                    'activo' => 1
                ]);
                
                $this->logAction(
                    $_SESSION['user_id'],
                    'crear_unidad_negocio',
                    "Unidad de negocio creada: $nombre",
                    'unidades_negocio',
                    $id
                );
                
                $this->setFlashMessage('Unidad de negocio creada correctamente', 'success');
                $this->redirect('/entidades/unidades');
            } catch (Exception $e) {
                $this->setFlashMessage('Error al crear unidad de negocio', 'error');
            }
        }
        
        $sql = "SELECT un.*, e.nombre as empresa_nombre
                FROM unidades_negocio un
                JOIN empresas_grupo e ON un.empresa_id = e.id";
        
        $params = [];
        if ($empresa_id) {
            $sql .= " WHERE un.empresa_id = ?";
            $params[] = $empresa_id;
        }
        
        $sql .= " ORDER BY un.activo DESC, un.nombre ASC";
        
        $unidades = $this->db->fetchAll($sql, $params);
        
        $empresas = $this->db->fetchAll(
            "SELECT id, nombre FROM empresas_grupo WHERE activo = 1 ORDER BY nombre"
        );
        
        $this->view('entidades/unidades', [
            'pageTitle' => 'Unidades de Negocio',
            'unidades' => $unidades,
            'empresas' => $empresas
        ]);
    }

    /**
     * Configuración de catálogos corporativos
     */
    public function catalogos() {
        // Obtener productos financieros por empresa
        $productos = $this->db->fetchAll(
            "SELECT pf.*, e.nombre as empresa_nombre
             FROM productos_financieros pf
             JOIN empresas_grupo e ON pf.empresa_id = e.id
             ORDER BY e.nombre, pf.nombre"
        );
        
        // Obtener políticas de crédito
        $politicas = $this->db->fetchAll(
            "SELECT pc.*, pf.nombre as producto_nombre
             FROM politicas_credito pc
             LEFT JOIN productos_financieros pf ON pc.producto_id = pf.id
             ORDER BY pc.activo DESC, pc.nombre"
        );
        
        $this->view('entidades/catalogos', [
            'pageTitle' => 'Catálogos Corporativos',
            'productos' => $productos,
            'politicas' => $politicas
        ]);
    }

    /**
     * Gestión de políticas institucionales
     */
    public function politicas() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $producto_id = $_POST['producto_id'] ?? null;
                $nombre = $_POST['nombre'] ?? '';
                $descripcion = $_POST['descripcion'] ?? '';
                $tipo = $_POST['tipo'] ?? '';
                $valor_min = $_POST['valor_min'] ?? null;
                $valor_max = $_POST['valor_max'] ?? null;
                
                if (!$nombre || !$tipo) {
                    throw new Exception('Datos incompletos');
                }
                
                $id = $this->db->insert('politicas_credito', [
                    'producto_id' => $producto_id,
                    'nombre' => $nombre,
                    'descripcion' => $descripcion,
                    'tipo' => $tipo,
                    'valor_min' => $valor_min,
                    'valor_max' => $valor_max,
                    'activo' => 1
                ]);
                
                $this->logAction(
                    $_SESSION['user_id'],
                    'crear_politica',
                    "Política creada: $nombre",
                    'politicas_credito',
                    $id
                );
                
                $this->setFlashMessage('Política creada correctamente', 'success');
                $this->redirect('/entidades/politicas');
            } catch (Exception $e) {
                $this->setFlashMessage('Error al crear política', 'error');
            }
        }
        
        $politicas = $this->db->fetchAll(
            "SELECT pc.*, pf.nombre as producto_nombre
             FROM politicas_credito pc
             LEFT JOIN productos_financieros pf ON pc.producto_id = pf.id
             ORDER BY pc.activo DESC, pc.nombre"
        );
        
        $productos = $this->db->fetchAll(
            "SELECT id, nombre FROM productos_financieros WHERE activo = 1"
        );
        
        $this->view('entidades/politicas', [
            'pageTitle' => 'Políticas Institucionales',
            'politicas' => $politicas,
            'productos' => $productos
        ]);
    }

    /**
     * Reportes de estructura organizacional
     */
    public function reportes() {
        // Resumen por empresa
        $resumenEmpresas = $this->db->fetchAll(
            "SELECT e.nombre,
                    COUNT(DISTINCT un.id) as unidades_negocio,
                    COUNT(DISTINCT pf.id) as productos_financieros,
                    COUNT(DISTINCT c.id) as total_creditos,
                    COALESCE(SUM(c.monto_autorizado), 0) as monto_total_creditos,
                    COALESCE(SUM(c.saldo_actual), 0) as saldo_actual_total
             FROM empresas_grupo e
             LEFT JOIN unidades_negocio un ON e.id = un.empresa_id
             LEFT JOIN productos_financieros pf ON e.id = pf.empresa_id
             LEFT JOIN creditos c ON e.id = c.empresa_id AND c.estatus IN ('activo', 'formalizado')
             WHERE e.activo = 1
             GROUP BY e.id, e.nombre"
        );
        
        // Resumen por unidad de negocio
        $resumenUnidades = $this->db->fetchAll(
            "SELECT e.nombre as empresa_nombre, un.nombre as unidad_nombre,
                    COUNT(fv.id) as total_promotores,
                    COUNT(DISTINCT c.id) as total_creditos
             FROM empresas_grupo e
             JOIN unidades_negocio un ON e.id = un.empresa_id
             LEFT JOIN fuerza_ventas fv ON un.id = fv.unidad_negocio_id
             LEFT JOIN creditos c ON fv.id = c.promotor_id
             WHERE un.activo = 1
             GROUP BY e.id, e.nombre, un.id, un.nombre"
        );
        
        $this->view('entidades/reportes', [
            'pageTitle' => 'Reportes de Estructura Organizacional',
            'resumenEmpresas' => $resumenEmpresas,
            'resumenUnidades' => $resumenUnidades
        ]);
    }
}
