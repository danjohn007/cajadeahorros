-- =====================================================
-- Sistema de Gestión Integral de Caja de Ahorros
-- Script de actualización v1.7
-- Módulo Sistema KYC (Know Your Customer)
-- =====================================================

USE caja_ahorros;

-- =====================================================
-- TABLA PRINCIPAL DE VERIFICACIONES KYC
-- =====================================================

CREATE TABLE IF NOT EXISTS kyc_verificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    socio_id INT NOT NULL,
    tipo_documento VARCHAR(100) NOT NULL COMMENT 'Tipo de documento de identificación',
    numero_documento VARCHAR(50) NOT NULL COMMENT 'Número del documento',
    fecha_emision DATE COMMENT 'Fecha de emisión del documento',
    fecha_vencimiento DATE COMMENT 'Fecha de vencimiento del documento',
    pais_emision VARCHAR(100) DEFAULT 'México' COMMENT 'País emisor del documento',
    documento_verificado TINYINT(1) DEFAULT 0 COMMENT 'Indica si el documento fue verificado',
    direccion_verificada TINYINT(1) DEFAULT 0 COMMENT 'Indica si la dirección fue verificada',
    identidad_verificada TINYINT(1) DEFAULT 0 COMMENT 'Indica si la identidad fue verificada biométricamente',
    pep TINYINT(1) DEFAULT 0 COMMENT 'Persona Políticamente Expuesta',
    fuente_ingresos VARCHAR(100) COMMENT 'Fuente principal de ingresos',
    actividad_economica VARCHAR(255) COMMENT 'Actividad económica o giro',
    nivel_riesgo ENUM('bajo', 'medio', 'alto') DEFAULT 'bajo' COMMENT 'Nivel de riesgo KYC',
    estatus ENUM('pendiente', 'aprobado', 'rechazado', 'vencido') DEFAULT 'pendiente',
    fecha_verificacion DATETIME COMMENT 'Fecha en que se completó la verificación',
    verificado_por INT COMMENT 'Usuario que realizó la verificación',
    observaciones TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (socio_id) REFERENCES socios(id) ON DELETE CASCADE,
    FOREIGN KEY (verificado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_socio (socio_id),
    INDEX idx_estatus (estatus),
    INDEX idx_nivel_riesgo (nivel_riesgo),
    INDEX idx_pep (pep),
    INDEX idx_fecha_vencimiento (fecha_vencimiento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA DE DOCUMENTOS ADJUNTOS KYC
-- =====================================================

CREATE TABLE IF NOT EXISTS kyc_documentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    verificacion_id INT NOT NULL,
    tipo_documento VARCHAR(100) NOT NULL COMMENT 'Tipo de documento: identificacion, comprobante_domicilio, etc.',
    nombre_archivo VARCHAR(255) NOT NULL,
    ruta_archivo VARCHAR(500) NOT NULL,
    fecha_subida DATETIME DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT,
    FOREIGN KEY (verificacion_id) REFERENCES kyc_verificaciones(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_verificacion (verificacion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA DE HISTORIAL DE CAMBIOS KYC
-- =====================================================

CREATE TABLE IF NOT EXISTS kyc_historial (
    id INT AUTO_INCREMENT PRIMARY KEY,
    verificacion_id INT NOT NULL,
    usuario_id INT,
    accion VARCHAR(100) NOT NULL COMMENT 'Tipo de acción realizada',
    descripcion TEXT COMMENT 'Descripción de los cambios',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (verificacion_id) REFERENCES kyc_verificaciones(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_verificacion (verificacion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- VISTA PARA REPORTE DE KYC
-- =====================================================

CREATE OR REPLACE VIEW v_kyc_resumen AS
SELECT 
    k.id,
    k.socio_id,
    s.numero_socio,
    CONCAT(s.nombre, ' ', s.apellido_paterno, ' ', COALESCE(s.apellido_materno, '')) as nombre_socio,
    s.rfc,
    s.curp,
    k.tipo_documento,
    k.numero_documento,
    k.nivel_riesgo,
    k.estatus,
    k.pep,
    k.documento_verificado,
    k.direccion_verificada,
    k.identidad_verificada,
    k.fecha_vencimiento,
    k.fecha_verificacion,
    k.created_at,
    CASE 
        WHEN k.fecha_vencimiento IS NULL THEN 'Sin vencimiento'
        WHEN k.fecha_vencimiento < CURDATE() THEN 'Vencido'
        WHEN k.fecha_vencimiento <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 'Por vencer'
        ELSE 'Vigente'
    END as estado_documento
FROM kyc_verificaciones k
JOIN socios s ON k.socio_id = s.id;

-- =====================================================
-- TRIGGER PARA NOTIFICAR NUEVO KYC ALTO RIESGO
-- =====================================================

DROP TRIGGER IF EXISTS tr_notificar_kyc_alto_riesgo;
DELIMITER $$
CREATE TRIGGER tr_notificar_kyc_alto_riesgo
AFTER INSERT ON kyc_verificaciones
FOR EACH ROW
BEGIN
    -- Notificar a administradores si es alto riesgo o PEP
    IF NEW.nivel_riesgo = 'alto' OR NEW.pep = 1 THEN
        INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, url)
        SELECT u.id, 'warning', 
               CONCAT('Verificación KYC ', IF(NEW.pep = 1, 'PEP', 'Alto Riesgo')),
               CONCAT('Nueva verificación KYC con nivel de riesgo ', NEW.nivel_riesgo, IF(NEW.pep = 1, ' - PEP', '')),
               CONCAT('kyc/ver/', NEW.id)
        FROM usuarios u
        WHERE u.rol = 'administrador' AND u.activo = 1;
    END IF;
END$$
DELIMITER ;

-- =====================================================
-- TRIGGER PARA MARCAR KYC COMO VENCIDO
-- (Se puede ejecutar con un evento programado)
-- =====================================================

DROP EVENT IF EXISTS ev_actualizar_kyc_vencido;
DELIMITER $$
CREATE EVENT IF NOT EXISTS ev_actualizar_kyc_vencido
ON SCHEDULE EVERY 1 DAY
DO
BEGIN
    UPDATE kyc_verificaciones 
    SET estatus = 'vencido' 
    WHERE fecha_vencimiento < CURDATE() 
      AND estatus = 'aprobado';
END$$
DELIMITER ;

-- =====================================================
-- DATOS DE EJEMPLO PARA KYC
-- =====================================================

-- Insertar verificaciones KYC para socios existentes
INSERT INTO kyc_verificaciones (
    socio_id, tipo_documento, numero_documento, fecha_emision, fecha_vencimiento,
    documento_verificado, direccion_verificada, identidad_verificada, pep,
    fuente_ingresos, actividad_economica, nivel_riesgo, estatus, fecha_verificacion, verificado_por
) VALUES
(1, 'INE', 'GALJ850315HQTRRN09001', '2020-03-15', '2030-03-15', 1, 1, 1, 0, 'Empleo', 'Gobierno', 'bajo', 'aprobado', NOW(), 1),
(2, 'INE', 'HERM900520MQTRML05002', '2019-05-20', '2029-05-20', 1, 1, 0, 0, 'Empleo', 'Educación', 'bajo', 'aprobado', NOW(), 1),
(3, 'Pasaporte', 'G12345678', '2021-08-10', '2031-08-10', 1, 1, 1, 0, 'Empleo', 'Contabilidad', 'bajo', 'aprobado', NOW(), 1),
(4, 'INE', 'TOMA880215MQTRNN08003', '2018-02-15', '2028-02-15', 1, 0, 0, 0, 'Empleo', 'Salud', 'medio', 'pendiente', NULL, NULL),
(5, 'INE', 'LOGP950630HQTPRR05004', '2022-06-30', '2032-06-30', 0, 0, 0, 0, 'Empleo', 'Arquitectura', 'medio', 'pendiente', NULL, NULL);

-- Registrar historial inicial
INSERT INTO kyc_historial (verificacion_id, usuario_id, accion, descripcion) VALUES
(1, 1, 'CREACION', 'Verificación KYC inicial creada'),
(1, 1, 'APROBACION', 'Verificación aprobada - documentos completos'),
(2, 1, 'CREACION', 'Verificación KYC inicial creada'),
(2, 1, 'APROBACION', 'Verificación aprobada'),
(3, 1, 'CREACION', 'Verificación KYC inicial creada'),
(3, 1, 'APROBACION', 'Verificación aprobada con pasaporte'),
(4, 1, 'CREACION', 'Verificación KYC pendiente - falta comprobante domicilio'),
(5, 1, 'CREACION', 'Verificación KYC pendiente - documentos por verificar');

-- =====================================================
-- ACTUALIZACIÓN DE VERSIÓN DEL SISTEMA
-- =====================================================

INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('version_sistema', '1.7.0', 'text', 'Versión actual del sistema')
ON DUPLICATE KEY UPDATE valor = '1.7.0';

INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('fecha_actualizacion', NOW(), 'datetime', 'Fecha de última actualización')
ON DUPLICATE KEY UPDATE valor = NOW();

-- =====================================================
-- FIN DE ACTUALIZACIÓN v1.7 - SISTEMA KYC
-- =====================================================
