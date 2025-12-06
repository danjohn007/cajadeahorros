<?php
/**
 * Controlador de Soporte Técnico Público
 * Sistema de Gestión Integral de Caja de Ahorros
 */

require_once CORE_PATH . '/Controller.php';

class SoporteController extends Controller {
    
    public function index() {
        // Public page - no authentication required
        
        // Get system configuration
        $config = $this->getConfiguraciones([
            'nombre_sitio', 'logo', 'telefono_contacto', 'email_contacto', 
            'horario_atencion', 'texto_copyright', 'color_primario', 
            'color_secundario', 'color_acento',
            'chatbot_whatsapp_numero', 'chatbot_url_publica',
            'chatbot_mensaje_bienvenida', 'chatbot_mensaje_horario'
        ]);
        
        $this->viewPartial('soporte/index', [
            'pageTitle' => 'Soporte Técnico',
            'config' => $config
        ]);
    }
    
    private function getConfiguraciones($claves) {
        $config = [];
        foreach ($claves as $clave) {
            $row = $this->db->fetch(
                "SELECT valor FROM configuraciones WHERE clave = :clave",
                ['clave' => $clave]
            );
            $config[$clave] = $row['valor'] ?? '';
        }
        return $config;
    }
}
