-- =====================================================
-- Sistema de Gestión Integral de Caja de Ahorros
-- Script de actualización de base de datos v1.2
-- =====================================================
-- Este script contiene actualizaciones relacionadas con:
-- - Corrección de errores de vistas faltantes
-- - Mejoras en auditoría del sistema
-- - Funcionalidad de impresión de cardex
-- - Configuración de correo SMTP
-- =====================================================

USE caja_ahorros;

-- =====================================================
-- VERIFICAR TABLAS EXISTENTES Y CREAR SI NO EXISTEN
-- =====================================================

-- Tabla de logs del sistema (si no existe)
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

-- Tabla de sesiones de usuario (si no existe)
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

-- Tabla de cambios en registros (si no existe)
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

-- Tabla de tipos de membresía (si no existe)
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

-- Tabla de membresías (si no existe)
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

-- Tabla de pagos de membresía (si no existe)
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

-- Tabla de importaciones (si no existe)
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

-- Tabla de detalles de importación (si no existe)
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

-- Tabla de dispositivos IoT (si no existe)
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

-- Tabla de configuración Shelly (si no existe)
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

-- Tabla de configuración HikVision (si no existe)
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

-- Tabla de eventos de dispositivos (si no existe)
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

-- Tabla de programación de dispositivos (si no existe)
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
-- AGREGAR CONFIGURACIONES DE CORREO SMTP SI NO EXISTEN
-- =====================================================

INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('smtp_host', '', 'text', 'Servidor SMTP'),
('smtp_port', '465', 'number', 'Puerto SMTP (465 para SSL, 587 para TLS)'),
('smtp_user', '', 'text', 'Usuario SMTP'),
('smtp_password', '', 'password', 'Contraseña SMTP'),
('smtp_from_name', '', 'text', 'Nombre del remitente'),
('smtp_encryption', 'ssl', 'text', 'Tipo de encriptación (tls/ssl)')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- =====================================================
-- DATOS INICIALES PARA TIPOS DE MEMBRESÍA
-- =====================================================

INSERT INTO tipos_membresia (nombre, descripcion, precio, duracion_dias, beneficios, activo) VALUES
('Básica', 'Membresía básica con acceso a servicios estándar', 500.00, 30, 'Acceso a áreas comunes,Participación en eventos', 1),
('Premium', 'Membresía premium con beneficios adicionales', 1000.00, 30, 'Acceso a todas las áreas,Descuentos en servicios,Prioridad en reservas', 1),
('VIP', 'Membresía VIP con todos los beneficios', 2000.00, 30, 'Acceso ilimitado,Servicios exclusivos,Descuentos especiales,Invitaciones a eventos privados', 1),
('Anual', 'Membresía anual con descuento', 10000.00, 365, 'Todos los beneficios VIP,Descuento por pago anual', 1)
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- =====================================================
-- ACTUALIZACIÓN DE VERSIÓN DEL SISTEMA
-- =====================================================

INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('version_sistema', '1.2.0', 'text', 'Versión actual del sistema')
ON DUPLICATE KEY UPDATE valor = '1.2.0';

INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('fecha_actualizacion', NOW(), 'datetime', 'Fecha de última actualización')
ON DUPLICATE KEY UPDATE valor = NOW();

-- =====================================================
-- FIN DEL SCRIPT DE ACTUALIZACIÓN
-- =====================================================
