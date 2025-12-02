-- =====================================================
-- Sistema de Gestión Integral de Caja de Ahorros
-- Script de actualización de base de datos v1.1
-- =====================================================
-- Este script añade nuevos módulos al sistema:
-- - Configuración General extendida (SMTP, estilos, PayPal, contacto)
-- - Membresías
-- - Módulo Financiero
-- - Importación de Clientes desde Excel
-- - Auditoría mejorada
-- - Informe CRM
-- - Dispositivos IoT (Shelly Cloud y HikVision)
-- =====================================================

USE caja_ahorros;

-- =====================================================
-- ACTUALIZACIÓN DE TABLA DE CONFIGURACIONES
-- Añadir nuevos campos para SMTP, estilos y contacto
-- =====================================================

-- Configuración SMTP de envío de correos
INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('smtp_host', '', 'text', 'Servidor SMTP'),
('smtp_port', '587', 'number', 'Puerto SMTP'),
('smtp_user', '', 'text', 'Usuario SMTP'),
('smtp_password', '', 'password', 'Contraseña SMTP'),
('smtp_from_name', '', 'text', 'Nombre del remitente'),
('smtp_encryption', 'tls', 'text', 'Tipo de encriptación (tls/ssl)')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- Configuración de estilos extendida
INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('color_acento', '#89ab37', 'color', 'Color de acento del sistema'),
('texto_copyright', '© 2025 Sistema de Gestión. Todos los derechos reservados.', 'text', 'Texto de copyright')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- Configuración de contacto
INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('email_contacto', '', 'email', 'Email de contacto'),
('cuota_mantenimiento', '1500', 'number', 'Cuota de mantenimiento por defecto')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- Configuración de PayPal extendida
INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('paypal_enabled', '0', 'boolean', 'Habilitar pagos con PayPal'),
('paypal_mode', 'sandbox', 'text', 'Modo de PayPal (sandbox/live)')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- =====================================================
-- TABLAS PARA MÓDULO DE MEMBRESÍAS
-- =====================================================

-- Tipos de membresías
CREATE TABLE IF NOT EXISTS tipos_membresia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(14,2) NOT NULL,
    duracion_dias INT NOT NULL DEFAULT 30,
    beneficios TEXT,
    activo TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membresías de socios
CREATE TABLE IF NOT EXISTS membresias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    socio_id INT NOT NULL,
    tipo_membresia_id INT NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    monto_pagado DECIMAL(14,2),
    estatus ENUM('activa', 'vencida', 'cancelada', 'pendiente') DEFAULT 'pendiente',
    metodo_pago ENUM('efectivo', 'transferencia', 'paypal', 'tarjeta') DEFAULT 'efectivo',
    referencia_pago VARCHAR(100),
    notas TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (socio_id) REFERENCES socios(id) ON DELETE CASCADE,
    FOREIGN KEY (tipo_membresia_id) REFERENCES tipos_membresia(id),
    INDEX idx_socio (socio_id),
    INDEX idx_estatus (estatus),
    INDEX idx_fechas (fecha_inicio, fecha_fin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Historial de pagos de membresía
CREATE TABLE IF NOT EXISTS pagos_membresia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    membresia_id INT NOT NULL,
    monto DECIMAL(14,2) NOT NULL,
    fecha_pago DATETIME DEFAULT CURRENT_TIMESTAMP,
    metodo_pago ENUM('efectivo', 'transferencia', 'paypal', 'tarjeta') DEFAULT 'efectivo',
    referencia VARCHAR(100),
    usuario_id INT,
    notas TEXT,
    FOREIGN KEY (membresia_id) REFERENCES membresias(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLAS PARA MÓDULO FINANCIERO
-- =====================================================

-- Categorías financieras
CREATE TABLE IF NOT EXISTS categorias_financieras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo ENUM('ingreso', 'egreso') NOT NULL,
    descripcion TEXT,
    color VARCHAR(7) DEFAULT '#000000',
    icono VARCHAR(50) DEFAULT 'fas fa-tag',
    activo TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Transacciones financieras
CREATE TABLE IF NOT EXISTS transacciones_financieras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('ingreso', 'egreso') NOT NULL,
    categoria_id INT,
    concepto VARCHAR(255) NOT NULL,
    monto DECIMAL(14,2) NOT NULL,
    fecha DATE NOT NULL,
    metodo_pago ENUM('efectivo', 'transferencia', 'cheque', 'tarjeta', 'paypal') DEFAULT 'efectivo',
    referencia VARCHAR(100),
    comprobante VARCHAR(255),
    socio_id INT,
    proveedor VARCHAR(200),
    notas TEXT,
    usuario_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias_financieras(id) ON DELETE SET NULL,
    FOREIGN KEY (socio_id) REFERENCES socios(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_fecha (fecha),
    INDEX idx_tipo (tipo),
    INDEX idx_categoria (categoria_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Presupuestos
CREATE TABLE IF NOT EXISTS presupuestos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    año INT NOT NULL,
    mes INT NOT NULL,
    monto_presupuestado DECIMAL(14,2) NOT NULL,
    monto_ejecutado DECIMAL(14,2) DEFAULT 0.00,
    notas TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias_financieras(id) ON DELETE CASCADE,
    UNIQUE KEY unique_presupuesto (categoria_id, año, mes)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cuentas bancarias
CREATE TABLE IF NOT EXISTS cuentas_bancarias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    banco VARCHAR(100) NOT NULL,
    numero_cuenta VARCHAR(50) NOT NULL,
    clabe VARCHAR(18),
    titular VARCHAR(200),
    tipo ENUM('cheques', 'ahorro', 'inversión') DEFAULT 'cheques',
    saldo_actual DECIMAL(14,2) DEFAULT 0.00,
    activa TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLAS PARA IMPORTACIÓN DE CLIENTES
-- =====================================================

-- Lotes de importación
CREATE TABLE IF NOT EXISTS importaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_archivo VARCHAR(255) NOT NULL,
    tipo ENUM('socios', 'clientes', 'pagos') NOT NULL DEFAULT 'socios',
    total_registros INT DEFAULT 0,
    registros_exitosos INT DEFAULT 0,
    registros_error INT DEFAULT 0,
    estatus ENUM('procesando', 'completado', 'error', 'parcial') DEFAULT 'procesando',
    usuario_id INT,
    fecha_inicio DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_fin DATETIME,
    notas TEXT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Registros de importación (detalle)
CREATE TABLE IF NOT EXISTS importaciones_detalle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    importacion_id INT NOT NULL,
    fila INT NOT NULL,
    datos_originales TEXT,
    estatus ENUM('exitoso', 'error', 'duplicado', 'pendiente') DEFAULT 'pendiente',
    mensaje_error TEXT,
    entidad_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (importacion_id) REFERENCES importaciones(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLAS PARA AUDITORÍA EXTENDIDA
-- =====================================================

-- Logs de sistema (complementa la tabla bitacora existente)
CREATE TABLE IF NOT EXISTS logs_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nivel ENUM('debug', 'info', 'warning', 'error', 'critical') NOT NULL DEFAULT 'info',
    modulo VARCHAR(100),
    mensaje TEXT NOT NULL,
    contexto JSON,
    usuario_id INT,
    ip VARCHAR(45),
    user_agent TEXT,
    url VARCHAR(500),
    metodo VARCHAR(10),
    tiempo_respuesta DECIMAL(10,4),
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_fecha (fecha),
    INDEX idx_nivel (nivel),
    INDEX idx_modulo (modulo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sesiones de usuario
CREATE TABLE IF NOT EXISTS sesiones_usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    ip VARCHAR(45),
    user_agent TEXT,
    fecha_inicio DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_ultimo_acceso DATETIME DEFAULT CURRENT_TIMESTAMP,
    activa TINYINT(1) DEFAULT 1,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cambios en registros (tracking detallado)
CREATE TABLE IF NOT EXISTS cambios_registro (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tabla VARCHAR(100) NOT NULL,
    registro_id INT NOT NULL,
    operacion ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
    datos_anteriores JSON,
    datos_nuevos JSON,
    usuario_id INT,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_tabla_registro (tabla, registro_id),
    INDEX idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLAS PARA INFORME CRM
-- =====================================================

-- Segmentos de clientes
CREATE TABLE IF NOT EXISTS segmentos_clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    criterios JSON,
    color VARCHAR(7) DEFAULT '#3b82f6',
    activo TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Clasificación de socios en segmentos
CREATE TABLE IF NOT EXISTS socios_segmentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    socio_id INT NOT NULL,
    segmento_id INT NOT NULL,
    fecha_asignacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (socio_id) REFERENCES socios(id) ON DELETE CASCADE,
    FOREIGN KEY (segmento_id) REFERENCES segmentos_clientes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_socio_segmento (socio_id, segmento_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Métricas CRM de socios
CREATE TABLE IF NOT EXISTS metricas_crm (
    id INT AUTO_INCREMENT PRIMARY KEY,
    socio_id INT NOT NULL,
    ltv DECIMAL(14,2) DEFAULT 0.00 COMMENT 'Lifetime Value',
    frecuencia_transacciones INT DEFAULT 0,
    promedio_transaccion DECIMAL(14,2) DEFAULT 0.00,
    ultima_transaccion DATE,
    dias_sin_actividad INT DEFAULT 0,
    nivel_riesgo ENUM('bajo', 'medio', 'alto') DEFAULT 'bajo',
    es_vip TINYINT(1) DEFAULT 0,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (socio_id) REFERENCES socios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_socio (socio_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notas/Interacciones con clientes
CREATE TABLE IF NOT EXISTS interacciones_clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    socio_id INT NOT NULL,
    tipo ENUM('llamada', 'email', 'visita', 'nota', 'queja', 'seguimiento') NOT NULL,
    asunto VARCHAR(255),
    descripcion TEXT,
    resultado VARCHAR(255),
    seguimiento_requerido TINYINT(1) DEFAULT 0,
    fecha_seguimiento DATE,
    usuario_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (socio_id) REFERENCES socios(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_socio (socio_id),
    INDEX idx_fecha (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLAS PARA DISPOSITIVOS IoT
-- (Shelly Cloud y HikVision)
-- =====================================================

-- Dispositivos IoT genérico
CREATE TABLE IF NOT EXISTS dispositivos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo ENUM('shelly', 'hikvision', 'otro') NOT NULL,
    modelo VARCHAR(100),
    ubicacion VARCHAR(255),
    ip_address VARCHAR(45),
    mac_address VARCHAR(17),
    descripcion TEXT,
    activo TINYINT(1) DEFAULT 1,
    ultimo_estado VARCHAR(50),
    ultima_conexion DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Configuración Shelly Cloud
CREATE TABLE IF NOT EXISTS shelly_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dispositivo_id INT NOT NULL,
    cloud_key VARCHAR(255),
    cloud_server VARCHAR(255),
    auth_token VARCHAR(255),
    channel INT DEFAULT 0,
    estado_actual ENUM('on', 'off', 'unknown') DEFAULT 'unknown',
    potencia_actual DECIMAL(10,2) DEFAULT 0.00,
    energia_consumida DECIMAL(14,2) DEFAULT 0.00,
    temperatura DECIMAL(5,2),
    ultima_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (dispositivo_id) REFERENCES dispositivos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Configuración HikVision
CREATE TABLE IF NOT EXISTS hikvision_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dispositivo_id INT NOT NULL,
    usuario VARCHAR(100),
    password_encrypted VARCHAR(255),
    puerto_http INT DEFAULT 80,
    puerto_rtsp INT DEFAULT 554,
    canales INT DEFAULT 1,
    grabacion_activa TINYINT(1) DEFAULT 0,
    deteccion_movimiento TINYINT(1) DEFAULT 0,
    ultima_captura VARCHAR(255),
    ultima_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (dispositivo_id) REFERENCES dispositivos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Eventos de dispositivos
CREATE TABLE IF NOT EXISTS eventos_dispositivos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dispositivo_id INT NOT NULL,
    tipo_evento VARCHAR(100) NOT NULL,
    descripcion TEXT,
    datos JSON,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (dispositivo_id) REFERENCES dispositivos(id) ON DELETE CASCADE,
    INDEX idx_dispositivo (dispositivo_id),
    INDEX idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Programación de dispositivos
CREATE TABLE IF NOT EXISTS programacion_dispositivos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dispositivo_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    accion ENUM('encender', 'apagar', 'toggle', 'capturar') NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME,
    dias_semana VARCHAR(20) DEFAULT '1,2,3,4,5,6,7',
    activo TINYINT(1) DEFAULT 1,
    ultima_ejecucion DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (dispositivo_id) REFERENCES dispositivos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DATOS INICIALES PARA NUEVOS MÓDULOS
-- =====================================================

-- Tipos de membresía por defecto
INSERT INTO tipos_membresia (nombre, descripcion, precio, duracion_dias, beneficios, activo) VALUES
('Básica', 'Membresía básica con acceso a servicios estándar', 500.00, 30, 'Acceso a áreas comunes,Participación en eventos', 1),
('Premium', 'Membresía premium con beneficios adicionales', 1000.00, 30, 'Acceso a todas las áreas,Descuentos en servicios,Prioridad en reservas', 1),
('VIP', 'Membresía VIP con todos los beneficios', 2000.00, 30, 'Acceso ilimitado,Servicios exclusivos,Descuentos especiales,Invitaciones a eventos privados', 1),
('Anual', 'Membresía anual con descuento', 10000.00, 365, 'Todos los beneficios VIP,Descuento por pago anual', 1)
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- Categorías financieras por defecto
INSERT INTO categorias_financieras (nombre, tipo, descripcion, color, icono, activo) VALUES
('Cuotas de Mantenimiento', 'ingreso', 'Ingresos por cuotas de mantenimiento', '#22c55e', 'fas fa-home', 1),
('Membresías', 'ingreso', 'Ingresos por membresías', '#3b82f6', 'fas fa-id-card', 1),
('Eventos', 'ingreso', 'Ingresos por eventos y reservas', '#8b5cf6', 'fas fa-calendar', 1),
('Multas', 'ingreso', 'Ingresos por multas y penalizaciones', '#f59e0b', 'fas fa-gavel', 1),
('Otros Ingresos', 'ingreso', 'Otros ingresos diversos', '#6b7280', 'fas fa-plus-circle', 1),
('Servicios', 'egreso', 'Pago de servicios (agua, luz, gas)', '#ef4444', 'fas fa-bolt', 1),
('Mantenimiento', 'egreso', 'Gastos de mantenimiento y reparaciones', '#f97316', 'fas fa-tools', 1),
('Nómina', 'egreso', 'Pago de sueldos y salarios', '#ec4899', 'fas fa-users', 1),
('Seguridad', 'egreso', 'Gastos de seguridad', '#14b8a6', 'fas fa-shield-alt', 1),
('Administración', 'egreso', 'Gastos administrativos', '#64748b', 'fas fa-building', 1),
('Otros Egresos', 'egreso', 'Otros gastos diversos', '#6b7280', 'fas fa-minus-circle', 1)
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- Segmentos de clientes por defecto
INSERT INTO segmentos_clientes (nombre, descripcion, color, activo) VALUES
('Sin compras', 'Clientes sin transacciones registradas', '#6b7280', 1),
('Ocasional', 'Clientes con baja frecuencia de transacciones', '#f59e0b', 1),
('Regular', 'Clientes con transacciones frecuentes', '#3b82f6', 1),
('VIP', 'Clientes de alto valor', '#22c55e', 1),
('En Riesgo', 'Clientes con mora o sin actividad reciente', '#ef4444', 1)
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- =====================================================
-- VISTAS PARA REPORTES
-- =====================================================

-- Vista de resumen de membresías activas
CREATE OR REPLACE VIEW v_membresias_activas AS
SELECT 
    m.id,
    m.socio_id,
    CONCAT(s.nombre, ' ', s.apellido_paterno, ' ', COALESCE(s.apellido_materno, '')) as nombre_socio,
    s.email,
    s.celular,
    tm.nombre as tipo_membresia,
    m.fecha_inicio,
    m.fecha_fin,
    DATEDIFF(m.fecha_fin, CURDATE()) as dias_restantes,
    m.monto_pagado,
    m.estatus
FROM membresias m
JOIN socios s ON m.socio_id = s.id
JOIN tipos_membresia tm ON m.tipo_membresia_id = tm.id
WHERE m.estatus = 'activa';

-- Vista de resumen financiero mensual
CREATE OR REPLACE VIEW v_resumen_financiero AS
SELECT 
    DATE_FORMAT(t.fecha, '%Y-%m') as periodo,
    t.tipo,
    cf.nombre as categoria,
    COUNT(*) as num_transacciones,
    SUM(t.monto) as monto_total
FROM transacciones_financieras t
LEFT JOIN categorias_financieras cf ON t.categoria_id = cf.id
GROUP BY DATE_FORMAT(t.fecha, '%Y-%m'), t.tipo, cf.nombre
ORDER BY periodo DESC, t.tipo;

-- Vista CRM de clientes
CREATE OR REPLACE VIEW v_crm_clientes AS
SELECT 
    s.id,
    s.numero_socio,
    CONCAT(s.nombre, ' ', s.apellido_paterno, ' ', COALESCE(s.apellido_materno, '')) as nombre_completo,
    s.email,
    s.celular,
    s.estatus,
    COALESCE(m.ltv, 0) as ltv,
    COALESCE(m.frecuencia_transacciones, 0) as frecuencia,
    m.ultima_transaccion,
    COALESCE(m.dias_sin_actividad, DATEDIFF(CURDATE(), s.created_at)) as dias_inactivo,
    COALESCE(m.nivel_riesgo, 'bajo') as nivel_riesgo,
    COALESCE(m.es_vip, 0) as es_vip,
    ca.saldo as saldo_ahorro,
    (SELECT COUNT(*) FROM creditos c WHERE c.socio_id = s.id AND c.estatus IN ('activo', 'formalizado')) as creditos_activos
FROM socios s
LEFT JOIN metricas_crm m ON s.id = m.socio_id
LEFT JOIN cuentas_ahorro ca ON s.id = ca.socio_id AND ca.estatus = 'activa';

-- Vista de dispositivos con estado
CREATE OR REPLACE VIEW v_dispositivos_estado AS
SELECT 
    d.id,
    d.nombre,
    d.tipo,
    d.modelo,
    d.ubicacion,
    d.ip_address,
    d.activo,
    d.ultimo_estado,
    d.ultima_conexion,
    CASE 
        WHEN d.tipo = 'shelly' THEN sc.estado_actual
        WHEN d.tipo = 'hikvision' THEN IF(hc.grabacion_activa, 'grabando', 'inactivo')
        ELSE 'desconocido'
    END as estado_detalle,
    CASE 
        WHEN d.tipo = 'shelly' THEN sc.potencia_actual
        ELSE NULL
    END as potencia
FROM dispositivos d
LEFT JOIN shelly_config sc ON d.id = sc.dispositivo_id
LEFT JOIN hikvision_config hc ON d.id = hc.dispositivo_id;

-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS
-- =====================================================

-- Procedimiento para actualizar métricas CRM de un socio
DROP PROCEDURE IF EXISTS sp_actualizar_metricas_crm;
DELIMITER //
CREATE PROCEDURE sp_actualizar_metricas_crm(IN p_socio_id INT)
BEGIN
    DECLARE v_ltv DECIMAL(14,2);
    DECLARE v_frecuencia INT;
    DECLARE v_promedio DECIMAL(14,2);
    DECLARE v_ultima_transaccion DATE;
    DECLARE v_dias_sin_actividad INT;
    
    -- Calcular métricas basadas en movimientos de ahorro y pagos
    SELECT 
        COALESCE(SUM(monto), 0),
        COUNT(*),
        COALESCE(AVG(monto), 0),
        MAX(fecha)
    INTO v_ltv, v_frecuencia, v_promedio, v_ultima_transaccion
    FROM (
        SELECT monto, fecha FROM movimientos_ahorro ma
        JOIN cuentas_ahorro ca ON ma.cuenta_id = ca.id
        WHERE ca.socio_id = p_socio_id AND ma.tipo = 'aportacion'
        UNION ALL
        SELECT monto, fecha_pago as fecha FROM pagos_credito pc
        JOIN creditos c ON pc.credito_id = c.id
        WHERE c.socio_id = p_socio_id
    ) as transacciones;
    
    SET v_dias_sin_actividad = COALESCE(DATEDIFF(CURDATE(), v_ultima_transaccion), 365);
    
    INSERT INTO metricas_crm (socio_id, ltv, frecuencia_transacciones, promedio_transaccion, 
                              ultima_transaccion, dias_sin_actividad, nivel_riesgo, es_vip)
    VALUES (p_socio_id, v_ltv, v_frecuencia, v_promedio, v_ultima_transaccion, v_dias_sin_actividad,
            CASE 
                WHEN v_dias_sin_actividad > 90 THEN 'alto'
                WHEN v_dias_sin_actividad > 30 THEN 'medio'
                ELSE 'bajo'
            END,
            IF(v_ltv > 100000, 1, 0))
    ON DUPLICATE KEY UPDATE
        ltv = v_ltv,
        frecuencia_transacciones = v_frecuencia,
        promedio_transaccion = v_promedio,
        ultima_transaccion = v_ultima_transaccion,
        dias_sin_actividad = v_dias_sin_actividad,
        nivel_riesgo = CASE 
            WHEN v_dias_sin_actividad > 90 THEN 'alto'
            WHEN v_dias_sin_actividad > 30 THEN 'medio'
            ELSE 'bajo'
        END,
        es_vip = IF(v_ltv > 100000, 1, 0);
END //
DELIMITER ;

-- Procedimiento para verificar y actualizar membresías vencidas
DROP PROCEDURE IF EXISTS sp_actualizar_membresias_vencidas;
DELIMITER //
CREATE PROCEDURE sp_actualizar_membresias_vencidas()
BEGIN
    UPDATE membresias 
    SET estatus = 'vencida'
    WHERE estatus = 'activa' AND fecha_fin < CURDATE();
END //
DELIMITER ;

-- =====================================================
-- INDICES ADICIONALES PARA RENDIMIENTO
-- =====================================================

-- Índices en tabla bitacora para auditoría mejorada
CREATE INDEX idx_bitacora_fecha_usuario ON bitacora(fecha, usuario_id);

-- Índices en tabla socios para CRM
CREATE INDEX idx_socios_email ON socios(email);
CREATE INDEX idx_socios_celular ON socios(celular);

-- =====================================================
-- ACTUALIZACIÓN DE VERSIÓN DEL SISTEMA
-- =====================================================

INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('version_sistema', '1.1.0', 'text', 'Versión actual del sistema')
ON DUPLICATE KEY UPDATE valor = '1.1.0';

INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('fecha_actualizacion', NOW(), 'datetime', 'Fecha de última actualización')
ON DUPLICATE KEY UPDATE valor = NOW();
