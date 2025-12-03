-- =====================================================
-- Sistema de Gestión Integral de Caja de Ahorros
-- Script de actualización v1.4
-- Registro público de cliente y correcciones de errores
-- =====================================================

USE caja_ahorros;

-- =====================================================
-- TABLA PARA LOGS DEL SISTEMA (si no existe)
-- =====================================================

CREATE TABLE IF NOT EXISTS logs_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nivel ENUM('info', 'warning', 'error', 'critical') DEFAULT 'info',
    modulo VARCHAR(100),
    mensaje TEXT,
    contexto JSON,
    usuario_id INT,
    ip VARCHAR(45),
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_nivel (nivel),
    INDEX idx_modulo (modulo),
    INDEX idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA PARA SESIONES DE USUARIO (si no existe)
-- =====================================================

CREATE TABLE IF NOT EXISTS sesiones_usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    session_id VARCHAR(128),
    ip VARCHAR(45),
    user_agent TEXT,
    fecha_inicio DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_ultimo_acceso DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    activa TINYINT(1) DEFAULT 1,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id),
    INDEX idx_activa (activa),
    INDEX idx_session (session_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA PARA REGISTRO DE CAMBIOS (si no existe)
-- =====================================================

CREATE TABLE IF NOT EXISTS cambios_registro (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tabla VARCHAR(100) NOT NULL,
    registro_id INT,
    operacion ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
    datos_anteriores JSON,
    datos_nuevos JSON,
    usuario_id INT,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_tabla (tabla),
    INDEX idx_operacion (operacion),
    INDEX idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA PARA CATEGORÍAS FINANCIERAS (si no existe)
-- =====================================================

CREATE TABLE IF NOT EXISTS categorias_financieras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo ENUM('ingreso', 'egreso') NOT NULL,
    descripcion TEXT,
    color VARCHAR(7) DEFAULT '#3b82f6',
    icono VARCHAR(50) DEFAULT 'fas fa-tag',
    activo TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA PARA TRANSACCIONES FINANCIERAS (si no existe)
-- =====================================================

CREATE TABLE IF NOT EXISTS transacciones_financieras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('ingreso', 'egreso') NOT NULL,
    categoria_id INT,
    concepto VARCHAR(255) NOT NULL,
    monto DECIMAL(12,2) NOT NULL,
    fecha DATE NOT NULL,
    metodo_pago ENUM('efectivo', 'transferencia', 'cheque', 'tarjeta') DEFAULT 'efectivo',
    referencia VARCHAR(100),
    proveedor VARCHAR(200),
    notas TEXT,
    usuario_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias_financieras(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_tipo (tipo),
    INDEX idx_fecha (fecha),
    INDEX idx_categoria (categoria_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA PARA PRESUPUESTOS (si no existe)
-- =====================================================

CREATE TABLE IF NOT EXISTS presupuestos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    año INT NOT NULL,
    mes INT NOT NULL,
    monto_presupuestado DECIMAL(12,2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias_financieras(id) ON DELETE CASCADE,
    UNIQUE KEY unique_presupuesto (categoria_id, año, mes)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ACTUALIZACIÓN DE TABLA DE USUARIOS (agregar rol cliente)
-- =====================================================

-- Solo ejecutar si la columna no tiene ya el valor 'cliente'
-- Nota: Esta alteración puede fallar si el ENUM ya incluye 'cliente',
-- pero es seguro ignorar el error

ALTER TABLE usuarios 
MODIFY COLUMN rol ENUM('administrador', 'operativo', 'consulta', 'cliente') NOT NULL DEFAULT 'consulta';

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
-- AGREGAR CAMPOS DE TELÉFONO Y CELULAR A USUARIOS (si no existen)
-- =====================================================

DROP PROCEDURE IF EXISTS sp_add_campos_usuario;
DELIMITER $$
CREATE PROCEDURE sp_add_campos_usuario()
BEGIN
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION BEGIN END;
    ALTER TABLE usuarios ADD COLUMN telefono VARCHAR(20) AFTER email;
    ALTER TABLE usuarios ADD COLUMN celular VARCHAR(20) AFTER telefono;
END$$
DELIMITER ;

CALL sp_add_campos_usuario();
DROP PROCEDURE IF EXISTS sp_add_campos_usuario;

-- =====================================================
-- INSERTAR CATEGORÍAS FINANCIERAS POR DEFECTO
-- =====================================================

INSERT INTO categorias_financieras (nombre, tipo, descripcion, color, icono) VALUES 
('Aportaciones de Socios', 'ingreso', 'Aportaciones regulares de los socios', '#22c55e', 'fas fa-hand-holding-usd'),
('Intereses Cobrados', 'ingreso', 'Intereses cobrados por créditos', '#3b82f6', 'fas fa-percentage'),
('Cuotas de Membresía', 'ingreso', 'Cuotas por membresía anual', '#8b5cf6', 'fas fa-id-card'),
('Otros Ingresos', 'ingreso', 'Otros ingresos varios', '#6b7280', 'fas fa-plus-circle'),
('Gastos Operativos', 'egreso', 'Gastos de operación general', '#ef4444', 'fas fa-cogs'),
('Nómina', 'egreso', 'Pago de salarios y prestaciones', '#f97316', 'fas fa-users'),
('Servicios', 'egreso', 'Luz, agua, teléfono, internet', '#eab308', 'fas fa-file-invoice'),
('Papelería', 'egreso', 'Material de oficina', '#06b6d4', 'fas fa-paperclip'),
('Mantenimiento', 'egreso', 'Mantenimiento de equipo e instalaciones', '#84cc16', 'fas fa-tools'),
('Otros Egresos', 'egreso', 'Otros gastos varios', '#6b7280', 'fas fa-minus-circle')
ON DUPLICATE KEY UPDATE nombre = nombre;

-- =====================================================
-- FIN DE ACTUALIZACIÓN
-- =====================================================
