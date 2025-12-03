-- =====================================================
-- Sistema de Gestión Integral de Caja de Ahorros
-- Script de actualización v1.3
-- Cardex del Socio, Pagos PayPal, Cliente user level
-- Versión compatible sin uso de INFORMATION_SCHEMA directo ni sintaxis
-- que requiera MySQL 8+ (usa handlers para evitar fallos si ya existen)
-- =====================================================

USE caja_ahorros;

-- =====================================================
-- ACTUALIZACIÓN DE TABLA DE USUARIOS (agregar rol cliente)
-- =====================================================

ALTER TABLE usuarios 
MODIFY COLUMN rol ENUM('administrador', 'operativo', 'consulta', 'cliente') NOT NULL DEFAULT 'consulta';

-- =====================================================
-- TABLA PARA MÉTRICAS CRM (si no existe)
-- =====================================================

CREATE TABLE IF NOT EXISTS metricas_crm (
    id INT AUTO_INCREMENT PRIMARY KEY,
    socio_id INT NOT NULL,
    ltv DECIMAL(12,2) DEFAULT 0,
    frecuencia_transacciones INT DEFAULT 0,
    ultima_transaccion DATE,
    dias_sin_actividad INT DEFAULT 0,
    nivel_riesgo ENUM('bajo', 'medio', 'alto') DEFAULT 'bajo',
    es_vip TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (socio_id) REFERENCES socios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_socio (socio_id),
    INDEX idx_riesgo (nivel_riesgo),
    INDEX idx_vip (es_vip)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA PARA SEGMENTOS DE CLIENTES (si no existe)
-- =====================================================

CREATE TABLE IF NOT EXISTS segmentos_clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    color VARCHAR(7) DEFAULT '#3b82f6',
    activo TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA PARA RELACIÓN SOCIOS-SEGMENTOS (si no existe)
-- =====================================================

CREATE TABLE IF NOT EXISTS socios_segmentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    socio_id INT NOT NULL,
    segmento_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (socio_id) REFERENCES socios(id) ON DELETE CASCADE,
    FOREIGN KEY (segmento_id) REFERENCES segmentos_clientes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_socio_segmento (socio_id, segmento_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA PARA INTERACCIONES CON CLIENTES (si no existe)
-- =====================================================

CREATE TABLE IF NOT EXISTS interacciones_clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    socio_id INT NOT NULL,
    tipo ENUM('llamada', 'email', 'visita', 'whatsapp', 'reunion', 'otro') NOT NULL,
    asunto VARCHAR(255),
    descripcion TEXT NOT NULL,
    resultado TEXT,
    seguimiento_requerido TINYINT(1) DEFAULT 0,
    fecha_seguimiento DATE,
    usuario_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (socio_id) REFERENCES socios(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_tipo (tipo),
    INDEX idx_seguimiento (seguimiento_requerido, fecha_seguimiento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA PARA TOKENS DE PAGO
-- =====================================================

CREATE TABLE IF NOT EXISTS tokens_pago (
    id INT AUTO_INCREMENT PRIMARY KEY,
    credito_id INT NOT NULL,
    amortizacion_id INT,
    token VARCHAR(64) NOT NULL UNIQUE,
    monto DECIMAL(12,2) NOT NULL,
    tipo ENUM('credito_total', 'cuota', 'monto_personalizado') DEFAULT 'cuota',
    fecha_expiracion DATETIME NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    usuario_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (credito_id) REFERENCES creditos(id) ON DELETE CASCADE,
    FOREIGN KEY (amortizacion_id) REFERENCES amortizacion(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_token (token),
    INDEX idx_credito (credito_id),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA PARA PAGOS ONLINE
-- =====================================================

CREATE TABLE IF NOT EXISTS pagos_online (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token_pago_id INT,
    credito_id INT NOT NULL,
    amortizacion_id INT,
    monto DECIMAL(12,2) NOT NULL,
    paypal_order_id VARCHAR(100),
    paypal_payer_id VARCHAR(100),
    estatus ENUM('pendiente', 'completado', 'fallido', 'reembolsado') DEFAULT 'pendiente',
    fecha_pago DATETIME,
    notas TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (token_pago_id) REFERENCES tokens_pago(id) ON DELETE SET NULL,
    FOREIGN KEY (credito_id) REFERENCES creditos(id) ON DELETE CASCADE,
    FOREIGN KEY (amortizacion_id) REFERENCES amortizacion(id) ON DELETE SET NULL,
    INDEX idx_paypal_order (paypal_order_id),
    INDEX idx_estatus (estatus)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- AGREGAR CAMPO DE IDENTIFICACIÓN OFICIAL A SOCIOS
-- (manejo de error si la columna ya existe — sin usar INFORMATION_SCHEMA)
-- =====================================================

DROP PROCEDURE IF EXISTS sp_add_identificacion_oficial;
DELIMITER $$
CREATE PROCEDURE sp_add_identificacion_oficial()
BEGIN
    -- Si ocurre cualquier error (por ejemplo: columna ya existe), continuamos.
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION BEGIN END;
    ALTER TABLE socios ADD COLUMN identificacion_oficial VARCHAR(255) AFTER observaciones;
END$$
DELIMITER ;

CALL sp_add_identificacion_oficial();
DROP PROCEDURE IF EXISTS sp_add_identificacion_oficial;

-- =====================================================
-- AGREGAR CONFIGURACIONES PARA PAYPAL
-- =====================================================

INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES 
('paypal_client_id', '', 'text', 'Client ID de PayPal'),
('paypal_client_secret', '', 'password', 'Client Secret de PayPal'),
('paypal_mode', 'sandbox', 'select', 'Modo de PayPal (sandbox/live)')
ON DUPLICATE KEY UPDATE clave = clave;

-- =====================================================
-- TABLA PARA VINCULAR USUARIOS CON SOCIOS (para rol cliente)
-- =====================================================

CREATE TABLE IF NOT EXISTS usuarios_socios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    socio_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (socio_id) REFERENCES socios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_socio (usuario_id, socio_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INDICES ADICIONALES PARA MEJORAR RENDIMIENTO
-- (creación tolerante a errores: si ya existen, se ignoran)
-- =====================================================

DROP PROCEDURE IF EXISTS sp_add_indices_tolerant;
DELIMITER $$
CREATE PROCEDURE sp_add_indices_tolerant()
BEGIN
    -- Ignora cualquier error (por ejemplo: índice ya existe)
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION BEGIN END;

    -- Índice para búsqueda rápida de movimientos por fecha
    ALTER TABLE movimientos_ahorro ADD INDEX idx_mov_ahorro_fecha (fecha);

    -- Índice para búsqueda rápida de amortizaciones vencidas
    ALTER TABLE amortizacion ADD INDEX idx_amort_vencimiento (fecha_vencimiento, estatus);
END$$
DELIMITER ;

CALL sp_add_indices_tolerant();
DROP PROCEDURE IF EXISTS sp_add_indices_tolerant;

-- =====================================================
-- ACTUALIZACIÓN DE PROCEDIMIENTOS ALMACENADOS (si existen)
-- =====================================================

-- Procedimiento para actualizar métricas CRM (crear/actualizar)
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
    
    -- Insertar o actualizar métricas
    INSERT INTO metricas_crm (socio_id, ltv, frecuencia_transacciones, ultima_transaccion, dias_sin_actividad, nivel_riesgo, es_vip)
    VALUES (p_socio_id, v_ltv, v_frecuencia, v_ultima_transaccion, v_dias_sin_actividad, v_nivel_riesgo, v_es_vip)
    ON DUPLICATE KEY UPDATE
        ltv = v_ltv,
        frecuencia_transacciones = v_frecuencia,
        ultima_transaccion = v_ultima_transaccion,
        dias_sin_actividad = v_dias_sin_actividad,
        nivel_riesgo = v_nivel_riesgo,
        es_vip = v_es_vip,
        updated_at = CURRENT_TIMESTAMP;
END //
DELIMITER ;

-- =====================================================
-- FIN DE ACTUALIZACIÓN
-- =====================================================
