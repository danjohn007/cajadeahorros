-- =====================================================
-- Sistema de Gestión Integral de Caja de Ahorros
-- Script de actualización v1.8
-- Módulo Sistema ESCROW y API Buró de Crédito
-- =====================================================

USE caja_ahorros;

-- =====================================================
-- CONFIGURACIONES PARA API BURÓ DE CRÉDITO
-- =====================================================

INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('buro_api_enabled', '0', 'boolean', 'Habilitar API Buró de Crédito'),
('buro_api_url', 'https://apif.burodecredito.com.mx', 'text', 'URL base de la API Buró de Crédito'),
('buro_api_username', '', 'text', 'Usuario de la API Buró de Crédito'),
('buro_api_password', '', 'password', 'Contraseña de la API Buró de Crédito'),
('buro_api_key', '', 'text', 'API Key de Buró de Crédito'),
('buro_costo_consulta', '50.00', 'number', 'Costo por consulta al Buró de Crédito (MXN)')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- =====================================================
-- TABLA DE CONSULTAS AL BURÓ DE CRÉDITO
-- =====================================================

CREATE TABLE IF NOT EXISTS consultas_buro (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_consulta ENUM('rfc', 'curp') NOT NULL COMMENT 'Tipo de identificador usado',
    identificador VARCHAR(20) NOT NULL COMMENT 'RFC o CURP consultado',
    nombre_consultado VARCHAR(200) COMMENT 'Nombre devuelto por el Buró',
    resultado_score INT COMMENT 'Score crediticio',
    resultado_json LONGTEXT COMMENT 'Respuesta completa del Buró en JSON',
    costo DECIMAL(10,2) NOT NULL COMMENT 'Costo de la consulta',
    paypal_order_id VARCHAR(100) COMMENT 'ID de orden de PayPal',
    estatus ENUM('pendiente_pago', 'pagado', 'consultado', 'error') DEFAULT 'pendiente_pago',
    token_consulta VARCHAR(64) UNIQUE COMMENT 'Token único para la consulta',
    ip_solicitante VARCHAR(45) COMMENT 'IP del solicitante',
    email_solicitante VARCHAR(100) COMMENT 'Email para enviar resultados',
    usuario_id INT COMMENT 'Usuario interno si aplica',
    error_mensaje TEXT COMMENT 'Mensaje de error si falló',
    fecha_pago DATETIME COMMENT 'Fecha del pago',
    fecha_consulta DATETIME COMMENT 'Fecha de la consulta al Buró',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_identificador (identificador),
    INDEX idx_token (token_consulta),
    INDEX idx_estatus (estatus),
    INDEX idx_fecha (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA PRINCIPAL DE TRANSACCIONES ESCROW
-- =====================================================

CREATE TABLE IF NOT EXISTS escrow_transacciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_transaccion VARCHAR(30) UNIQUE NOT NULL COMMENT 'Número único de transacción ESCROW',
    tipo ENUM('compraventa', 'servicio', 'proyecto', 'otro') NOT NULL DEFAULT 'compraventa',
    titulo VARCHAR(200) NOT NULL COMMENT 'Título descriptivo de la transacción',
    descripcion TEXT COMMENT 'Descripción detallada',
    
    -- Participantes
    comprador_id INT COMMENT 'ID del socio comprador',
    comprador_nombre VARCHAR(200) COMMENT 'Nombre del comprador (si no es socio)',
    comprador_email VARCHAR(100) COMMENT 'Email del comprador',
    comprador_telefono VARCHAR(20) COMMENT 'Teléfono del comprador',
    
    vendedor_id INT COMMENT 'ID del socio vendedor',
    vendedor_nombre VARCHAR(200) COMMENT 'Nombre del vendedor (si no es socio)',
    vendedor_email VARCHAR(100) COMMENT 'Email del vendedor',
    vendedor_telefono VARCHAR(20) COMMENT 'Teléfono del vendedor',
    
    -- Montos
    monto_total DECIMAL(14,2) NOT NULL COMMENT 'Monto total de la transacción',
    comision_porcentaje DECIMAL(5,2) DEFAULT 2.50 COMMENT 'Porcentaje de comisión ESCROW',
    comision_monto DECIMAL(14,2) COMMENT 'Monto de comisión calculado',
    monto_liberado DECIMAL(14,2) DEFAULT 0.00 COMMENT 'Monto ya liberado al vendedor',
    
    -- Fechas
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_deposito DATETIME COMMENT 'Fecha en que el comprador depositó',
    fecha_limite DATE COMMENT 'Fecha límite para completar',
    fecha_liberacion DATETIME COMMENT 'Fecha de liberación de fondos',
    fecha_cancelacion DATETIME COMMENT 'Fecha de cancelación si aplica',
    
    -- Estado
    estatus ENUM('borrador', 'pendiente_deposito', 'fondos_depositados', 'en_proceso', 
                 'entrega_confirmada', 'liberado', 'disputa', 'cancelado', 'reembolsado') 
            DEFAULT 'borrador',
    
    -- Trazabilidad
    usuario_creador INT COMMENT 'Usuario que creó la transacción',
    notas_internas TEXT COMMENT 'Notas para uso interno',
    
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (comprador_id) REFERENCES socios(id) ON DELETE SET NULL,
    FOREIGN KEY (vendedor_id) REFERENCES socios(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_creador) REFERENCES usuarios(id) ON DELETE SET NULL,
    
    INDEX idx_numero (numero_transaccion),
    INDEX idx_estatus (estatus),
    INDEX idx_comprador (comprador_id),
    INDEX idx_vendedor (vendedor_id),
    INDEX idx_fecha (fecha_creacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA DE HITOS/MILESTONES DE ESCROW
-- =====================================================

CREATE TABLE IF NOT EXISTS escrow_hitos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaccion_id INT NOT NULL,
    numero_hito INT NOT NULL COMMENT 'Número secuencial del hito',
    descripcion VARCHAR(255) NOT NULL COMMENT 'Descripción del hito',
    monto DECIMAL(14,2) NOT NULL COMMENT 'Monto a liberar al completar',
    fecha_limite DATE COMMENT 'Fecha límite para completar',
    fecha_completado DATETIME COMMENT 'Fecha en que se completó',
    estatus ENUM('pendiente', 'completado', 'cancelado') DEFAULT 'pendiente',
    evidencia TEXT COMMENT 'Descripción de evidencia entregada',
    confirmado_por INT COMMENT 'Usuario que confirmó el hito',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (transaccion_id) REFERENCES escrow_transacciones(id) ON DELETE CASCADE,
    FOREIGN KEY (confirmado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_transaccion (transaccion_id),
    INDEX idx_estatus (estatus)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA DE DOCUMENTOS ESCROW
-- =====================================================

CREATE TABLE IF NOT EXISTS escrow_documentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaccion_id INT NOT NULL,
    hito_id INT COMMENT 'Opcional: asociado a un hito específico',
    tipo VARCHAR(100) NOT NULL COMMENT 'Tipo de documento: contrato, evidencia, etc.',
    nombre_archivo VARCHAR(255) NOT NULL,
    ruta_archivo VARCHAR(500) NOT NULL,
    descripcion TEXT,
    subido_por INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaccion_id) REFERENCES escrow_transacciones(id) ON DELETE CASCADE,
    FOREIGN KEY (hito_id) REFERENCES escrow_hitos(id) ON DELETE SET NULL,
    FOREIGN KEY (subido_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_transaccion (transaccion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA DE MOVIMIENTOS/PAGOS ESCROW
-- =====================================================

CREATE TABLE IF NOT EXISTS escrow_movimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaccion_id INT NOT NULL,
    tipo ENUM('deposito', 'liberacion', 'comision', 'reembolso', 'ajuste') NOT NULL,
    monto DECIMAL(14,2) NOT NULL,
    concepto VARCHAR(255) NOT NULL,
    metodo_pago VARCHAR(50) COMMENT 'paypal, transferencia, efectivo, etc.',
    referencia_pago VARCHAR(100) COMMENT 'Referencia del pago',
    usuario_id INT COMMENT 'Usuario que registró el movimiento',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaccion_id) REFERENCES escrow_transacciones(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_transaccion (transaccion_id),
    INDEX idx_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA DE HISTORIAL/BITÁCORA ESCROW
-- =====================================================

CREATE TABLE IF NOT EXISTS escrow_historial (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaccion_id INT NOT NULL,
    accion VARCHAR(100) NOT NULL COMMENT 'Tipo de acción realizada',
    descripcion TEXT COMMENT 'Descripción detallada del cambio',
    estatus_anterior VARCHAR(50),
    estatus_nuevo VARCHAR(50),
    usuario_id INT,
    ip VARCHAR(45),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaccion_id) REFERENCES escrow_transacciones(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_transaccion (transaccion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA DE DISPUTAS ESCROW
-- =====================================================

CREATE TABLE IF NOT EXISTS escrow_disputas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaccion_id INT NOT NULL,
    iniciado_por ENUM('comprador', 'vendedor') NOT NULL,
    motivo VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    evidencia TEXT COMMENT 'Descripción de evidencia presentada',
    resolucion TEXT COMMENT 'Resolución de la disputa',
    resuelto_por INT COMMENT 'Usuario que resolvió',
    estatus ENUM('abierta', 'en_revision', 'resuelta_comprador', 'resuelta_vendedor', 'resuelta_parcial') DEFAULT 'abierta',
    fecha_resolucion DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (transaccion_id) REFERENCES escrow_transacciones(id) ON DELETE CASCADE,
    FOREIGN KEY (resuelto_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_transaccion (transaccion_id),
    INDEX idx_estatus (estatus)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CONFIGURACIONES ESCROW
-- =====================================================

INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('escrow_enabled', '1', 'boolean', 'Habilitar módulo ESCROW'),
('escrow_comision_porcentaje', '2.50', 'number', 'Porcentaje de comisión ESCROW por defecto'),
('escrow_monto_minimo', '100.00', 'number', 'Monto mínimo para transacciones ESCROW'),
('escrow_dias_limite_defecto', '30', 'number', 'Días límite por defecto para transacciones'),
('escrow_cuenta_retencion', '', 'text', 'Número de cuenta para retención de fondos')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- =====================================================
-- VISTA RESUMEN ESCROW
-- =====================================================

CREATE OR REPLACE VIEW v_escrow_resumen AS
SELECT 
    et.id,
    et.numero_transaccion,
    et.tipo,
    et.titulo,
    COALESCE(sc.numero_socio, 'Externo') as comprador_socio,
    COALESCE(CONCAT(sc.nombre, ' ', sc.apellido_paterno), et.comprador_nombre) as comprador_nombre,
    COALESCE(sv.numero_socio, 'Externo') as vendedor_socio,
    COALESCE(CONCAT(sv.nombre, ' ', sv.apellido_paterno), et.vendedor_nombre) as vendedor_nombre,
    et.monto_total,
    et.comision_monto,
    et.monto_liberado,
    (et.monto_total - et.monto_liberado) as saldo_retenido,
    et.estatus,
    et.fecha_creacion,
    et.fecha_limite,
    COUNT(DISTINCT eh.id) as total_hitos,
    SUM(CASE WHEN eh.estatus = 'completado' THEN 1 ELSE 0 END) as hitos_completados
FROM escrow_transacciones et
LEFT JOIN socios sc ON et.comprador_id = sc.id
LEFT JOIN socios sv ON et.vendedor_id = sv.id
LEFT JOIN escrow_hitos eh ON et.id = eh.transaccion_id
GROUP BY et.id;

-- =====================================================
-- TRIGGER PARA GENERAR NÚMERO DE TRANSACCIÓN ESCROW
-- =====================================================

DROP TRIGGER IF EXISTS tr_escrow_numero_transaccion;
DELIMITER $$
CREATE TRIGGER tr_escrow_numero_transaccion
BEFORE INSERT ON escrow_transacciones
FOR EACH ROW
BEGIN
    DECLARE next_num INT;
    
    IF NEW.numero_transaccion IS NULL OR NEW.numero_transaccion = '' THEN
        SELECT COALESCE(MAX(CAST(SUBSTRING(numero_transaccion, 5) AS UNSIGNED)), 0) + 1 
        INTO next_num
        FROM escrow_transacciones;
        
        SET NEW.numero_transaccion = CONCAT('ESC-', LPAD(next_num, 6, '0'));
    END IF;
    
    -- Calcular comisión
    IF NEW.comision_monto IS NULL THEN
        SET NEW.comision_monto = ROUND(NEW.monto_total * (NEW.comision_porcentaje / 100), 2);
    END IF;
END$$
DELIMITER ;

-- =====================================================
-- TRIGGER PARA HISTORIAL ESCROW
-- =====================================================

DROP TRIGGER IF EXISTS tr_escrow_historial_update;
DELIMITER $$
CREATE TRIGGER tr_escrow_historial_update
AFTER UPDATE ON escrow_transacciones
FOR EACH ROW
BEGIN
    IF OLD.estatus != NEW.estatus THEN
        INSERT INTO escrow_historial (transaccion_id, accion, descripcion, estatus_anterior, estatus_nuevo)
        VALUES (NEW.id, 'CAMBIO_ESTATUS', 
                CONCAT('Estado cambiado de ', OLD.estatus, ' a ', NEW.estatus),
                OLD.estatus, NEW.estatus);
    END IF;
END$$
DELIMITER ;

-- =====================================================
-- TRIGGER PARA NOTIFICACIONES ESCROW
-- =====================================================

DROP TRIGGER IF EXISTS tr_escrow_notificar_nuevo;
DELIMITER $$
CREATE TRIGGER tr_escrow_notificar_nuevo
AFTER INSERT ON escrow_transacciones
FOR EACH ROW
BEGIN
    -- Notificar a administradores sobre nueva transacción ESCROW
    INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, url)
    SELECT u.id, 'info', 
           CONCAT('Nueva transacción ESCROW: ', NEW.numero_transaccion),
           CONCAT('Transacción por $', FORMAT(NEW.monto_total, 2), ' - ', NEW.titulo),
           CONCAT('escrow/ver/', NEW.id)
    FROM usuarios u
    WHERE u.rol = 'administrador' AND u.activo = 1;
END$$
DELIMITER ;

-- =====================================================
-- ACTUALIZACIÓN DE VERSIÓN DEL SISTEMA
-- =====================================================

INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('version_sistema', '1.8.0', 'text', 'Versión actual del sistema')
ON DUPLICATE KEY UPDATE valor = '1.8.0';

INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('fecha_actualizacion', NOW(), 'datetime', 'Fecha de última actualización')
ON DUPLICATE KEY UPDATE valor = NOW();

-- =====================================================
-- FIN DE ACTUALIZACIÓN v1.8 - ESCROW Y BURÓ DE CRÉDITO
-- =====================================================
