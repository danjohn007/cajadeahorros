-- =====================================================
-- Sistema de Gestión Integral de Caja de Ahorros
-- Script de actualización v1.5
-- Correcciones de errores SQL y nuevas funcionalidades
-- =====================================================

USE caja_ahorros;

-- =====================================================
-- CORRECCIÓN DE PROCEDIMIENTO sp_actualizar_metricas_crm
-- Eliminar referencia a columna updated_at que no existe
-- =====================================================

DROP PROCEDURE IF EXISTS sp_actualizar_metricas_crm;
DELIMITER //
CREATE PROCEDURE sp_actualizar_metricas_crm(IN p_socio_id INT)
BEGIN
    DECLARE v_ltv DECIMAL(12,2) DEFAULT 0;
    DECLARE v_frecuencia INT DEFAULT 0;
    DECLARE v_ultima_transaccion DATE;
    DECLARE v_dias_sin_actividad INT DEFAULT 0;
    DECLARE v_nivel_riesgo VARCHAR(10) DEFAULT 'bajo';
    DECLARE v_es_vip TINYINT DEFAULT 0;
    
    -- Calcular LTV (suma de todos los pagos realizados)
    SELECT COALESCE(SUM(monto), 0) INTO v_ltv
    FROM movimientos_ahorro ma
    JOIN cuentas_ahorro ca ON ma.cuenta_id = ca.id
    WHERE ca.socio_id = p_socio_id AND ma.tipo = 'aportacion';
    
    -- Sumar pagos de créditos
    SELECT v_ltv + COALESCE(SUM(monto), 0) INTO v_ltv
    FROM pagos_credito pc
    JOIN creditos c ON pc.credito_id = c.id
    WHERE c.socio_id = p_socio_id;
    
    -- Calcular frecuencia de transacciones (últimos 12 meses)
    SELECT COUNT(*) INTO v_frecuencia
    FROM (
        SELECT fecha FROM movimientos_ahorro ma
        JOIN cuentas_ahorro ca ON ma.cuenta_id = ca.id
        WHERE ca.socio_id = p_socio_id AND ma.fecha >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        UNION ALL
        SELECT fecha_pago as fecha FROM pagos_credito pc
        JOIN creditos c ON pc.credito_id = c.id
        WHERE c.socio_id = p_socio_id AND pc.fecha_pago >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    ) transacciones;
    
    -- Última transacción
    SELECT MAX(ultima) INTO v_ultima_transaccion
    FROM (
        SELECT MAX(ma.fecha) as ultima FROM movimientos_ahorro ma
        JOIN cuentas_ahorro ca ON ma.cuenta_id = ca.id
        WHERE ca.socio_id = p_socio_id
        UNION ALL
        SELECT MAX(pc.fecha_pago) FROM pagos_credito pc
        JOIN creditos c ON pc.credito_id = c.id
        WHERE c.socio_id = p_socio_id
    ) ultimas;
    
    -- Días sin actividad
    IF v_ultima_transaccion IS NOT NULL THEN
        SET v_dias_sin_actividad = DATEDIFF(CURDATE(), v_ultima_transaccion);
    ELSE
        SET v_dias_sin_actividad = 999;
    END IF;
    
    -- Nivel de riesgo
    IF v_dias_sin_actividad > 180 THEN
        SET v_nivel_riesgo = 'alto';
    ELSEIF v_dias_sin_actividad > 90 THEN
        SET v_nivel_riesgo = 'medio';
    ELSE
        SET v_nivel_riesgo = 'bajo';
    END IF;
    
    -- Es VIP (LTV alto y frecuencia alta)
    IF v_ltv > 50000 AND v_frecuencia > 12 THEN
        SET v_es_vip = 1;
    END IF;
    
    -- Insertar o actualizar métricas (sin updated_at ya que se actualiza automáticamente)
    INSERT INTO metricas_crm (socio_id, ltv, frecuencia_transacciones, ultima_transaccion, dias_sin_actividad, nivel_riesgo, es_vip)
    VALUES (p_socio_id, v_ltv, v_frecuencia, v_ultima_transaccion, v_dias_sin_actividad, v_nivel_riesgo, v_es_vip)
    ON DUPLICATE KEY UPDATE
        ltv = v_ltv,
        frecuencia_transacciones = v_frecuencia,
        ultima_transaccion = v_ultima_transaccion,
        dias_sin_actividad = v_dias_sin_actividad,
        nivel_riesgo = v_nivel_riesgo,
        es_vip = v_es_vip;
END //
DELIMITER ;

-- =====================================================
-- TABLA PARA SOLICITUDES DE VINCULACIÓN (Customer Journey)
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
-- TABLA PARA NOTIFICACIONES DEL SISTEMA
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
-- TABLA PARA PROSPECTOS (Customer Journey)
-- =====================================================

CREATE TABLE IF NOT EXISTS prospectos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    nombre VARCHAR(200) NOT NULL,
    apellido_paterno VARCHAR(100),
    apellido_materno VARCHAR(100),
    email VARCHAR(100),
    telefono VARCHAR(20),
    celular VARCHAR(20),
    whatsapp VARCHAR(20),
    origen ENUM('registro_web', 'importacion', 'referido', 'otro') DEFAULT 'registro_web',
    estatus ENUM('nuevo', 'contactado', 'interesado', 'no_interesado', 'convertido', 'descartado') DEFAULT 'nuevo',
    fecha_ultimo_contacto DATETIME,
    notas TEXT,
    asignado_a INT,
    socio_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    FOREIGN KEY (asignado_a) REFERENCES usuarios(id) ON DELETE SET NULL,
    FOREIGN KEY (socio_id) REFERENCES socios(id) ON DELETE SET NULL,
    INDEX idx_estatus (estatus),
    INDEX idx_email (email),
    INDEX idx_telefono (telefono),
    INDEX idx_celular (celular)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- AGREGAR CAMPO email_verificado A USUARIOS
-- =====================================================

DROP PROCEDURE IF EXISTS sp_add_email_verificado;
DELIMITER $$
CREATE PROCEDURE sp_add_email_verificado()
BEGIN
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION BEGIN END;
    ALTER TABLE usuarios ADD COLUMN email_verificado TINYINT(1) DEFAULT 0 AFTER activo;
    ALTER TABLE usuarios ADD COLUMN token_verificacion VARCHAR(100) AFTER email_verificado;
    ALTER TABLE usuarios ADD COLUMN token_verificacion_expira DATETIME AFTER token_verificacion;
END$$
DELIMITER ;

CALL sp_add_email_verificado();
DROP PROCEDURE IF EXISTS sp_add_email_verificado;

-- =====================================================
-- ACTUALIZACIÓN DE VERSIÓN DEL SISTEMA
-- =====================================================

INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('version_sistema', '1.5.0', 'text', 'Versión actual del sistema')
ON DUPLICATE KEY UPDATE valor = '1.5.0';

INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('fecha_actualizacion', NOW(), 'datetime', 'Fecha de última actualización')
ON DUPLICATE KEY UPDATE valor = NOW();

-- =====================================================
-- FIN DE ACTUALIZACIÓN
-- =====================================================
