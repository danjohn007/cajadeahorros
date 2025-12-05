-- =====================================================
-- Sistema de Gestión Integral de Caja de Ahorros
-- Script de actualización v1.10 - Módulo de Inversionistas
-- Compatible con MySQL 5.7
-- Ejecutar después de las actualizaciones anteriores
-- =====================================================

-- Add new user roles: cliente and inversionista
ALTER TABLE usuarios MODIFY rol ENUM('administrador', 'operativo', 'consulta', 'cliente', 'inversionista') NOT NULL DEFAULT 'consulta';

-- Add additional fields to usuarios table (MySQL 5.7 compatible)
-- The script checks INFORMATION_SCHEMA and executes the ALTER only if the column doesn't exist.

-- Añadir telefono si no existe
SET @col_exists := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'telefono');
SET @sql_stmt := IF(@col_exists = 0,
                    'ALTER TABLE usuarios ADD COLUMN telefono VARCHAR(20) DEFAULT NULL AFTER email;',
                    'SELECT "telefono already exists";');
PREPARE stmt FROM @sql_stmt; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Añadir celular si no existe
SET @col_exists := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'celular');
SET @sql_stmt := IF(@col_exists = 0,
                    'ALTER TABLE usuarios ADD COLUMN celular VARCHAR(20) DEFAULT NULL AFTER telefono;',
                    'SELECT "celular already exists";');
PREPARE stmt FROM @sql_stmt; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Añadir avatar si no existe
SET @col_exists := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'avatar');
SET @sql_stmt := IF(@col_exists = 0,
                    'ALTER TABLE usuarios ADD COLUMN avatar VARCHAR(255) DEFAULT NULL AFTER celular;',
                    'SELECT "avatar already exists";');
PREPARE stmt FROM @sql_stmt; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Create inversionistas table
CREATE TABLE IF NOT EXISTS inversionistas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_inversionista VARCHAR(20) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido_paterno VARCHAR(100) NOT NULL,
    apellido_materno VARCHAR(100),
    rfc VARCHAR(13),
    curp VARCHAR(18),
    fecha_nacimiento DATE,
    telefono VARCHAR(20),
    celular VARCHAR(20),
    email VARCHAR(100),
    direccion TEXT,
    banco VARCHAR(100),
    cuenta_bancaria VARCHAR(50),
    clabe VARCHAR(18),
    fecha_alta DATE,
    estatus ENUM('activo', 'inactivo') DEFAULT 'activo',
    notas TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_rfc (rfc),
    INDEX idx_estatus (estatus),
    INDEX idx_numero_inversionista (numero_inversionista)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create usuarios_inversionistas link table
CREATE TABLE IF NOT EXISTS usuarios_inversionistas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    inversionista_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (inversionista_id) REFERENCES inversionistas(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_inv (usuario_id, inversionista_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create inversiones table
CREATE TABLE IF NOT EXISTS inversiones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    inversionista_id INT NOT NULL,
    numero_inversion VARCHAR(20) UNIQUE NOT NULL,
    monto DECIMAL(14,2) NOT NULL,
    tasa_rendimiento DECIMAL(8,6) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    plazo_meses INT NOT NULL,
    credito_id INT DEFAULT NULL,
    estatus ENUM('activa', 'liquidada', 'cancelada') DEFAULT 'activa',
    notas TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (inversionista_id) REFERENCES inversionistas(id) ON DELETE CASCADE,
    FOREIGN KEY (credito_id) REFERENCES creditos(id) ON DELETE SET NULL,
    INDEX idx_inversionista (inversionista_id),
    INDEX idx_estatus (estatus),
    INDEX idx_numero_inversion (numero_inversion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create rendimientos_inversiones table
CREATE TABLE IF NOT EXISTS rendimientos_inversiones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    inversion_id INT NOT NULL,
    monto DECIMAL(14,2) NOT NULL,
    fecha_calculo DATE NOT NULL,
    fecha_pago DATE,
    estatus ENUM('pendiente', 'pagado', 'cancelado') DEFAULT 'pendiente',
    referencia VARCHAR(100),
    notas TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (inversion_id) REFERENCES inversiones(id) ON DELETE CASCADE,
    INDEX idx_inversion (inversion_id),
    INDEX idx_estatus (estatus)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add identificacion_oficial column to socios table if not exists (MySQL 5.7 compatible)
SET @col_exists := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'socios' AND COLUMN_NAME = 'identificacion_oficial');
SET @sql_stmt := IF(@col_exists = 0,
                    'ALTER TABLE socios ADD COLUMN identificacion_oficial VARCHAR(255) DEFAULT NULL AFTER observaciones;',
                    'SELECT "identificacion_oficial already exists";');
PREPARE stmt FROM @sql_stmt; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Create solicitudes_actualizacion_perfil table for client profile update requests
CREATE TABLE IF NOT EXISTS solicitudes_actualizacion_perfil (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    socio_id INT DEFAULT NULL,
    cambios_solicitados JSON NOT NULL,
    estatus ENUM('pendiente', 'aprobado', 'rechazado') DEFAULT 'pendiente',
    aprobado_por INT DEFAULT NULL,
    fecha_aprobacion DATETIME DEFAULT NULL,
    motivo_rechazo TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (socio_id) REFERENCES socios(id) ON DELETE SET NULL,
    FOREIGN KEY (aprobado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_usuario (usuario_id),
    INDEX idx_estatus (estatus)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- End of update script v1.10
