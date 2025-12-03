-- =====================================================
-- Sistema de Gestión Integral de Caja de Ahorros
-- Script de actualización v1.6
-- Proveedores, mejoras en transacciones financieras
-- y notificaciones del sistema
-- =====================================================

USE caja_ahorros;

-- =====================================================
-- TABLA PARA PROVEEDORES
-- =====================================================

CREATE TABLE IF NOT EXISTS proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    rfc VARCHAR(13),
    contacto VARCHAR(200),
    telefono VARCHAR(20),
    email VARCHAR(100),
    direccion TEXT,
    notas TEXT,
    activo TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre),
    INDEX idx_rfc (rfc),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- AGREGAR CAMPOS A TRANSACCIONES_FINANCIERAS
-- =====================================================

DROP PROCEDURE IF EXISTS sp_add_transaccion_fields;
DELIMITER $$
CREATE PROCEDURE sp_add_transaccion_fields()
BEGIN
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION BEGIN END;
    
    -- Agregar campo proveedor_id
    ALTER TABLE transacciones_financieras ADD COLUMN proveedor_id INT AFTER proveedor;
    
    -- Agregar campo socio_id  
    ALTER TABLE transacciones_financieras ADD COLUMN socio_id INT AFTER proveedor_id;
    
    -- Agregar índices
    ALTER TABLE transacciones_financieras ADD INDEX idx_proveedor_id (proveedor_id);
    ALTER TABLE transacciones_financieras ADD INDEX idx_socio_id (socio_id);
    
    -- Agregar foreign keys (opcional, puede fallar si hay datos inconsistentes)
    ALTER TABLE transacciones_financieras 
    ADD CONSTRAINT fk_transaccion_proveedor 
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id) ON DELETE SET NULL;
    
    ALTER TABLE transacciones_financieras 
    ADD CONSTRAINT fk_transaccion_socio 
    FOREIGN KEY (socio_id) REFERENCES socios(id) ON DELETE SET NULL;
END$$
DELIMITER ;

CALL sp_add_transaccion_fields();
DROP PROCEDURE IF EXISTS sp_add_transaccion_fields;

-- =====================================================
-- ASEGURAR QUE EXISTE LA TABLA NOTIFICACIONES
-- (por si no se ejecutó update_v1.5)
-- =====================================================

CREATE TABLE IF NOT EXISTS notificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    tipo ENUM('info', 'warning', 'success', 'error', 'vinculacion', 'pago', 'credito', 'sistema') DEFAULT 'info',
    titulo VARCHAR(200) NOT NULL,
    mensaje TEXT NOT NULL,
    url VARCHAR(500),
    leida TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_leida (usuario_id, leida),
    INDEX idx_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ASEGURAR QUE EXISTE LA TABLA SOLICITUDES_VINCULACION
-- (por si no se ejecutó update_v1.5)
-- =====================================================

CREATE TABLE IF NOT EXISTS solicitudes_vinculacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nombre VARCHAR(200) NOT NULL,
    email VARCHAR(100),
    telefono VARCHAR(20),
    celular VARCHAR(20),
    whatsapp VARCHAR(20),
    mensaje TEXT,
    estatus ENUM('pendiente', 'en_revision', 'aprobada', 'rechazada') DEFAULT 'pendiente',
    revisado_por INT,
    fecha_revision DATETIME,
    notas_revision TEXT,
    socio_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (revisado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    FOREIGN KEY (socio_id) REFERENCES socios(id) ON DELETE SET NULL,
    INDEX idx_estatus (estatus),
    INDEX idx_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INSERTAR PROVEEDORES DE EJEMPLO
-- =====================================================

INSERT IGNORE INTO proveedores (nombre, rfc, contacto, telefono, email, notas) VALUES
('Papelería y Útiles S.A.', 'PAP010101ABC', 'Juan García', '5551234567', 'ventas@papeleria.com', 'Proveedor de papelería y artículos de oficina'),
('Servicios de Limpieza Pro', 'SER020202DEF', 'María López', '5559876543', 'contacto@limpiezapro.com', 'Servicio de limpieza mensual'),
('Mantenimiento Integral', 'MAN030303GHI', 'Pedro Martínez', '5555551234', 'info@mantenimiento.com', 'Mantenimiento general de oficinas'),
('Tecnología y Sistemas', 'TEC040404JKL', 'Ana Hernández', '5552223344', 'soporte@tecsistemas.com', 'Proveedor de equipo de cómputo y soporte'),
('Publicidad Efectiva', 'PUB050505MNO', 'Carlos Ruiz', '5553334455', 'marketing@pubefectiva.com', 'Servicios de publicidad y marketing');

-- =====================================================
-- CREAR TRIGGER PARA NOTIFICACIONES DE VINCULACIÓN
-- =====================================================

DROP TRIGGER IF EXISTS tr_notificar_solicitud_vinculacion;
DELIMITER $$
CREATE TRIGGER tr_notificar_solicitud_vinculacion
AFTER INSERT ON solicitudes_vinculacion
FOR EACH ROW
BEGIN
    -- Notificar a todos los administradores
    INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, url)
    SELECT id, 'vinculacion', 'Nueva solicitud de vinculación',
           CONCAT('El usuario ', NEW.nombre, ' ha solicitado vincular su cuenta'),
           'crm/customerjourney'
    FROM usuarios 
    WHERE rol = 'administrador' AND activo = 1;
END$$
DELIMITER ;

-- =====================================================
-- CREAR TRIGGER PARA NOTIFICACIONES DE CRÉDITO
-- =====================================================

DROP TRIGGER IF EXISTS tr_notificar_credito_nuevo;
DELIMITER $$
CREATE TRIGGER tr_notificar_credito_nuevo
AFTER INSERT ON creditos
FOR EACH ROW
BEGIN
    -- Notificar a todos los administradores sobre nueva solicitud de crédito
    IF NEW.estatus = 'solicitud' THEN
        INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, url)
        SELECT u.id, 'credito', 'Nueva solicitud de crédito',
               CONCAT('Solicitud de crédito ', NEW.numero_credito, ' por $', FORMAT(NEW.monto_solicitado, 2)),
               CONCAT('creditos/ver/', NEW.id)
        FROM usuarios u
        WHERE u.rol = 'administrador' AND u.activo = 1;
    END IF;
END$$
DELIMITER ;

-- =====================================================
-- ACTUALIZACIÓN DE VERSIÓN DEL SISTEMA
-- =====================================================

INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('version_sistema', '1.6.0', 'text', 'Versión actual del sistema')
ON DUPLICATE KEY UPDATE valor = '1.6.0';

INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('fecha_actualizacion', NOW(), 'datetime', 'Fecha de última actualización')
ON DUPLICATE KEY UPDATE valor = NOW();

-- =====================================================
-- FIN DE ACTUALIZACIÓN v1.6
-- =====================================================
