-- =====================================================
-- ID FINANCIERO - Sistema de Gestión de Crédito Multiempresa
-- Script de actualización de base de datos
-- Versión: 2.0 (lista para importar, compatible con MySQL/MariaDB sin ALTER ... IF NOT EXISTS)
-- Fecha: 2025-12-07
-- =====================================================
--
-- Instrucciones:
-- 1) Ejecutar este script en un entorno de pruebas primero.
-- 2) Requiere permisos para crear tablas, alterar tablas y crear índices/constraints.
-- 3) Este script evita la sintaxis ALTER ... ADD COLUMN IF NOT EXISTS / ADD INDEX IF NOT EXISTS
--    y en su lugar comprueba INFORMATION_SCHEMA y usa PREPARE/EXECUTE dinámico para aplicar cambios
--    en el orden correcto (columnas -> índices -> constraints).
--
-- Nota importante:
-- - Este script asume que la tabla `creditos` ya existe. Si no existe, creela primero.
-- - Antes de agregar FKs (especialmente producto_financiero_id) confirme que los valores actuales
--   en creditos son NULL o coinciden con las tablas de referencia para evitar errores de integridad.
--
-- =====================================================
-- 1. ARQUITECTURA MULTIEMPRESA (crear tablas nuevas si no existen)
-- =====================================================

CREATE TABLE IF NOT EXISTS empresas_grupo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    nombre_corto VARCHAR(50),
    rfc VARCHAR(13) UNIQUE,
    razon_social VARCHAR(250),
    direccion TEXT,
    telefono VARCHAR(20),
    email VARCHAR(100),
    sitio_web VARCHAR(200),
    logo VARCHAR(255),
    activo TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS unidades_negocio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    nombre VARCHAR(200) NOT NULL,
    clave VARCHAR(50),
    tipo VARCHAR(50),
    direccion TEXT,
    telefono VARCHAR(20),
    responsable VARCHAR(200),
    activo TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (empresa_id) REFERENCES empresas_grupo(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS productos_financieros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    nombre VARCHAR(200) NOT NULL,
    tipo VARCHAR(50) NOT NULL COMMENT 'credito, ahorro, inversion, etc',
    descripcion TEXT,
    tasa_interes_min DECIMAL(5,4),
    tasa_interes_max DECIMAL(5,4),
    plazo_min_meses INT,
    plazo_max_meses INT,
    monto_min DECIMAL(14,2),
    monto_max DECIMAL(14,2),
    requiere_aval TINYINT(1) DEFAULT 0,
    monto_requiere_aval DECIMAL(14,2),
    comision_apertura DECIMAL(5,4),
    activo TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (empresa_id) REFERENCES empresas_grupo(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS fuerza_ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    unidad_negocio_id INT NOT NULL,
    nombre VARCHAR(200) NOT NULL,
    apellido_paterno VARCHAR(100) NOT NULL,
    apellido_materno VARCHAR(100),
    rfc VARCHAR(13),
    curp VARCHAR(18),
    email VARCHAR(100),
    telefono VARCHAR(20),
    puesto VARCHAR(100),
    tipo VARCHAR(50) COMMENT 'promotor, asesor, gerente',
    fecha_alta DATE,
    fecha_baja DATE,
    activo TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (unidad_negocio_id) REFERENCES unidades_negocio(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS poblaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    municipio VARCHAR(200),
    estado VARCHAR(100) NOT NULL,
    codigo_postal VARCHAR(10),
    tipo VARCHAR(50) COMMENT 'ciudad, pueblo, colonia',
    activo TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_estado (estado),
    INDEX idx_municipio (municipio),
    INDEX idx_cp (codigo_postal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. POLÍTICAS DE CRÉDITO Y CHECKLISTS
-- =====================================================

CREATE TABLE IF NOT EXISTS politicas_credito (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    tipo VARCHAR(50) COMMENT 'edad, monto, garantia, documentacion',
    condicion TEXT COMMENT 'JSON con la definición de la condición',
    valor_min DECIMAL(14,2),
    valor_max DECIMAL(14,2),
    activo TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos_financieros(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS checklists_credito (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    tipo_operacion VARCHAR(50) NOT NULL COMMENT 'apertura, renovacion, reestructura',
    producto_id INT,
    descripcion TEXT,
    orden INT DEFAULT 0,
    activo TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos_financieros(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS checklist_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    checklist_id INT NOT NULL,
    descripcion TEXT NOT NULL,
    tipo VARCHAR(50) COMMENT 'documento, validacion, aprobacion',
    requerido TINYINT(1) DEFAULT 1,
    orden INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (checklist_id) REFERENCES checklists_credito(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- checklist_validaciones requiere la tabla creditos; se crea solo si creditos existe
CREATE TABLE IF NOT EXISTS checklist_validaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    credito_id INT NOT NULL,
    checklist_item_id INT NOT NULL,
    completado TINYINT(1) DEFAULT 0,
    fecha_completado DATETIME,
    validado_por INT,
    observaciones TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (credito_id) REFERENCES creditos(id) ON DELETE CASCADE,
    FOREIGN KEY (checklist_item_id) REFERENCES checklist_items(id) ON DELETE CASCADE,
    FOREIGN KEY (validado_por) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. GESTIÓN DE GARANTÍAS Y AVALES
-- =====================================================

CREATE TABLE IF NOT EXISTS avales_obligados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    credito_id INT NOT NULL,
    tipo VARCHAR(50) NOT NULL COMMENT 'aval, obligado_solidario, garante',
    nombre VARCHAR(200) NOT NULL,
    apellido_paterno VARCHAR(100) NOT NULL,
    apellido_materno VARCHAR(100),
    rfc VARCHAR(13),
    curp VARCHAR(18),
    fecha_nacimiento DATE,
    telefono VARCHAR(20),
    celular VARCHAR(20),
    email VARCHAR(100),
    direccion TEXT,
    relacion_solicitante VARCHAR(100),
    capacidad_pago DECIMAL(14,2),
    activo TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (credito_id) REFERENCES creditos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS garantias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    credito_id INT NOT NULL,
    tipo VARCHAR(100) NOT NULL COMMENT 'hipotecaria, prendaria, liquida',
    descripcion TEXT,
    valor_estimado DECIMAL(14,2),
    documento VARCHAR(255),
    fecha_valuacion DATE,
    activo TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (credito_id) REFERENCES creditos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. MÓDULO DE TESORERÍA
-- =====================================================

CREATE TABLE IF NOT EXISTS proyecciones_financieras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha_proyeccion DATE NOT NULL,
    tipo VARCHAR(50) COMMENT 'ingreso, egreso',
    concepto VARCHAR(200),
    monto_proyectado DECIMAL(14,2),
    monto_real DECIMAL(14,2),
    empresa_id INT,
    usuario_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (empresa_id) REFERENCES empresas_grupo(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_fecha (fecha_proyeccion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS flujos_efectivo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    tipo VARCHAR(50) NOT NULL COMMENT 'entrada, salida',
    concepto VARCHAR(200),
    categoria VARCHAR(100),
    monto DECIMAL(14,2) NOT NULL,
    empresa_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (empresa_id) REFERENCES empresas_grupo(id) ON DELETE CASCADE,
    INDEX idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. GESTIÓN DE CARTERA
-- =====================================================

CREATE TABLE IF NOT EXISTS traspasos_cartera (
    id INT AUTO_INCREMENT PRIMARY KEY,
    credito_id INT NOT NULL,
    tipo_traspaso VARCHAR(50) NOT NULL COMMENT 'vigente_a_vencida, vencida_a_vigente',
    fecha_traspaso DATE NOT NULL,
    dias_mora INT,
    saldo_vencido DECIMAL(14,2),
    motivo TEXT,
    usuario_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (credito_id) REFERENCES creditos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS convenios_pago (
    id INT AUTO_INCREMENT PRIMARY KEY,
    credito_id INT NOT NULL,
    fecha_convenio DATE NOT NULL,
    monto_total DECIMAL(14,2) NOT NULL,
    numero_cuotas INT NOT NULL,
    monto_cuota DECIMAL(14,2) NOT NULL,
    fecha_primer_pago DATE,
    estatus VARCHAR(50) DEFAULT 'activo' COMMENT 'activo, cumplido, incumplido',
    usuario_id INT,
    observaciones TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (credito_id) REFERENCES creditos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS liquidaciones_credito (
    id INT AUTO_INCREMENT PRIMARY KEY,
    credito_id INT NOT NULL,
    tipo VARCHAR(50) NOT NULL COMMENT 'total, parcial, anticipada',
    fecha_liquidacion DATE NOT NULL,
    saldo_capital DECIMAL(14,2) NOT NULL,
    intereses_pendientes DECIMAL(14,2),
    total_liquidado DECIMAL(14,2) NOT NULL,
    descuento DECIMAL(14,2) DEFAULT 0,
    valor_cartera DECIMAL(14,2),
    usuario_id INT,
    observaciones TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (credito_id) REFERENCES creditos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 6. REPORTES REGULATORIOS CNBV
-- =====================================================

CREATE TABLE IF NOT EXISTS reportes_cnbv (
    id INT AUTO_INCREMENT PRIMARY KEY,
    periodo VARCHAR(20) NOT NULL COMMENT 'YYYY-MM',
    tipo_reporte VARCHAR(100) NOT NULL,
    fecha_generacion DATETIME NOT NULL,
    archivo VARCHAR(255),
    formato VARCHAR(20) COMMENT 'XML, EXCEL',
    estatus VARCHAR(50) DEFAULT 'generado' COMMENT 'generado, enviado, aceptado, rechazado',
    fecha_envio DATETIME,
    usuario_id INT,
    observaciones TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_periodo (periodo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS reportes_cnbv_detalle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reporte_id INT NOT NULL,
    concepto VARCHAR(200),
    valor TEXT,
    orden INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reporte_id) REFERENCES reportes_cnbv(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 7. ACTUALIZACIONES A TABLAS EXISTENTES (columnas, índices, FKs)
-- =====================================================
-- Nota: Se comprueba existence en INFORMATION_SCHEMA y se aplica en el orden correcto.
--       Esto evita errores como "La columna clave 'empresa_id' no existe en la tabla".

-- Variables auxiliares: se reutilizan @cnt, @col_exists, @idx_exists, @sql, @empresa_existe, @fk_exists
-- 7.1 Agregar columnas a la tabla creditos si no existen
SELECT COUNT(*) INTO @cnt FROM information_schema.columns
 WHERE table_schema = DATABASE() AND table_name = 'creditos' AND column_name = 'empresa_id';
SET @sql = IF(@cnt = 0,
    'ALTER TABLE `creditos` ADD COLUMN `empresa_id` INT AFTER `id`',
    'SELECT \"Column empresa_id already exists\" AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @cnt FROM information_schema.columns
 WHERE table_schema = DATABASE() AND table_name = 'creditos' AND column_name = 'producto_financiero_id';
SET @sql = IF(@cnt = 0,
    'ALTER TABLE `creditos` ADD COLUMN `producto_financiero_id` INT AFTER `tipo_credito_id`',
    'SELECT \"Column producto_financiero_id already exists\" AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @cnt FROM information_schema.columns
 WHERE table_schema = DATABASE() AND table_name = 'creditos' AND column_name = 'origen_procedencia';
SET @sql = IF(@cnt = 0,
    'ALTER TABLE `creditos` ADD COLUMN `origen_procedencia` VARCHAR(100) AFTER `observaciones`',
    'SELECT \"Column origen_procedencia already exists\" AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @cnt FROM information_schema.columns
 WHERE table_schema = DATABASE() AND table_name = 'creditos' AND column_name = 'tipo_origen';
SET @sql = IF(@cnt = 0,
    'ALTER TABLE `creditos` ADD COLUMN `tipo_origen` VARCHAR(50) AFTER `origen_procedencia`',
    'SELECT \"Column tipo_origen already exists\" AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @cnt FROM information_schema.columns
 WHERE table_schema = DATABASE() AND table_name = 'creditos' AND column_name = 'promotor_id';
SET @sql = IF(@cnt = 0,
    'ALTER TABLE `creditos` ADD COLUMN `promotor_id` INT AFTER `tipo_origen`',
    'SELECT \"Column promotor_id already exists\" AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @cnt FROM information_schema.columns
 WHERE table_schema = DATABASE() AND table_name = 'creditos' AND column_name = 'requiere_aval';
SET @sql = IF(@cnt = 0,
    'ALTER TABLE `creditos` ADD COLUMN `requiere_aval` TINYINT(1) DEFAULT 0 AFTER `promotor_id`',
    'SELECT \"Column requiere_aval already exists\" AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @cnt FROM information_schema.columns
 WHERE table_schema = DATABASE() AND table_name = 'creditos' AND column_name = 'motivo_rechazo';
SET @sql = IF(@cnt = 0,
    'ALTER TABLE `creditos` ADD COLUMN `motivo_rechazo` TEXT AFTER `observaciones`',
    'SELECT \"Column motivo_rechazo already exists\" AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @cnt FROM information_schema.columns
 WHERE table_schema = DATABASE() AND table_name = 'creditos' AND column_name = 'dias_mora';
SET @sql = IF(@cnt = 0,
    'ALTER TABLE `creditos` ADD COLUMN `dias_mora` INT DEFAULT 0 AFTER `pagos_vencidos`',
    'SELECT \"Column dias_mora already exists\" AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @cnt FROM information_schema.columns
 WHERE table_schema = DATABASE() AND table_name = 'creditos' AND column_name = 'tipo_cartera';
-- NOTE: doble comilla simple dentro de la cadena SQL duplicada para representar 'vigente'
SET @sql = IF(@cnt = 0,
    'ALTER TABLE `creditos` ADD COLUMN `tipo_cartera` VARCHAR(50) DEFAULT ''vigente'' COMMENT ''vigente, vencida'' AFTER `dias_mora`',
    'SELECT \"Column tipo_cartera already exists\" AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 7.2 Agregar índices en creditos (solo si la columna existe y el índice no existe)
SELECT COUNT(*) INTO @col_exists FROM information_schema.columns
 WHERE table_schema = DATABASE() AND table_name = 'creditos' AND column_name = 'empresa_id';
SELECT COUNT(*) INTO @idx_exists FROM information_schema.statistics
 WHERE table_schema = DATABASE() AND table_name = 'creditos' AND index_name = 'idx_empresa';
SET @sql = IF(@col_exists > 0 AND @idx_exists = 0,
    'ALTER TABLE `creditos` ADD INDEX `idx_empresa` (`empresa_id`)',
    IF(@idx_exists > 0, 'SELECT \"Index idx_empresa already exists\" AS msg', 'SELECT \"Column empresa_id missing, index not created\" AS msg'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @col_exists FROM information_schema.columns
 WHERE table_schema = DATABASE() AND table_name = 'creditos' AND column_name = 'producto_financiero_id';
SELECT COUNT(*) INTO @idx_exists FROM information_schema.statistics
 WHERE table_schema = DATABASE() AND table_name = 'creditos' AND index_name = 'idx_producto';
SET @sql = IF(@col_exists > 0 AND @idx_exists = 0,
    'ALTER TABLE `creditos` ADD INDEX `idx_producto` (`producto_financiero_id`)',
    IF(@idx_exists > 0, 'SELECT \"Index idx_producto already exists\" AS msg', 'SELECT \"Column producto_financiero_id missing, index not created\" AS msg'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @col_exists FROM information_schema.columns
 WHERE table_schema = DATABASE() AND table_name = 'creditos' AND column_name = 'promotor_id';
SELECT COUNT(*) INTO @idx_exists FROM information_schema.statistics
 WHERE table_schema = DATABASE() AND table_name = 'creditos' AND index_name = 'idx_promotor';
SET @sql = IF(@col_exists > 0 AND @idx_exists = 0,
    'ALTER TABLE `creditos` ADD INDEX `idx_promotor` (`promotor_id`)',
    IF(@idx_exists > 0, 'SELECT \"Index idx_promotor already exists\" AS msg', 'SELECT \"Column promotor_id missing, index not created\" AS msg'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @col_exists FROM information_schema.columns
 WHERE table_schema = DATABASE() AND table_name = 'creditos' AND column_name = 'estatus';
SELECT COUNT(*) INTO @idx_exists FROM information_schema.statistics
 WHERE table_schema = DATABASE() AND table_name = 'creditos' AND index_name = 'idx_estatus';
SET @sql = IF(@col_exists > 0 AND @idx_exists = 0,
    'ALTER TABLE `creditos` ADD INDEX `idx_estatus` (`estatus`)',
    IF(@idx_exists > 0, 'SELECT \"Index idx_estatus already exists\" AS msg', 'SELECT \"Column estatus missing, index not created\" AS msg'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @col_exists FROM information_schema.columns
 WHERE table_schema = DATABASE() AND table_name = 'creditos' AND column_name = 'tipo_cartera';
SELECT COUNT(*) INTO @idx_exists FROM information_schema.statistics
 WHERE table_schema = DATABASE() AND table_name = 'creditos' AND index_name = 'idx_tipo_cartera';
SET @sql = IF(@col_exists > 0 AND @idx_exists = 0,
    'ALTER TABLE `creditos` ADD INDEX `idx_tipo_cartera` (`tipo_cartera`)',
    IF(@idx_exists > 0, 'SELECT \"Index idx_tipo_cartera already exists\" AS msg', 'SELECT \"Column tipo_cartera missing, index not created\" AS msg'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 7.3 Agregar FK para empresa_id si existe la empresa por defecto (id = 1) y la FK no existe
SELECT COUNT(*) INTO @empresa_existe FROM information_schema.tables
 WHERE table_schema = DATABASE() AND table_name = 'empresas_grupo';
-- verificar existencia de empresa id=1 solo si la tabla existe
SET @empresa_existe = IF(@empresa_existe > 0, (SELECT COUNT(*) FROM empresas_grupo WHERE id = 1), 0);

SELECT COUNT(*) INTO @fk_exists FROM information_schema.table_constraints
 WHERE table_schema = DATABASE() AND table_name = 'creditos' AND constraint_type = 'FOREIGN KEY' AND constraint_name = 'fk_creditos_empresa';

SET @sql = IF(@empresa_existe > 0 AND @fk_exists = 0,
    'ALTER TABLE `creditos` ADD CONSTRAINT `fk_creditos_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas_grupo`(`id`) ON DELETE SET NULL',
    IF(@fk_exists > 0,
       'SELECT \"FK fk_creditos_empresa already exists\" AS msg',
       'SELECT \"Empresa por defecto no existe o empresas_grupo ausente, FK no agregada\" AS msg'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Nota: FK para producto_financiero_id NO se agrega automáticamente aquí; antes de agregarla,
-- verifique que todos los valores en creditos.producto_financiero_id sean NULL o existan en productos_financieros.id.

-- 7.4 Actualizar documentos_credito (columnas revisado, fecha_revision, revisado_por)
SELECT COUNT(*) INTO @cnt FROM information_schema.columns
 WHERE table_schema = DATABASE() AND table_name = 'documentos_credito' AND column_name = 'revisado';
SET @sql = IF(@cnt = 0,
    'ALTER TABLE `documentos_credito` ADD COLUMN `revisado` TINYINT(1) DEFAULT 0 AFTER `ruta_archivo`',
    'SELECT \"Column revisado already exists\" AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @cnt FROM information_schema.columns
 WHERE table_schema = DATABASE() AND table_name = 'documentos_credito' AND column_name = 'fecha_revision';
SET @sql = IF(@cnt = 0,
    'ALTER TABLE `documentos_credito` ADD COLUMN `fecha_revision` DATETIME AFTER `revisado`',
    'SELECT \"Column fecha_revision already exists\" AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @cnt FROM information_schema.columns
 WHERE table_schema = DATABASE() AND table_name = 'documentos_credito' AND column_name = 'revisado_por';
SET @sql = IF(@cnt = 0,
    'ALTER TABLE `documentos_credito` ADD COLUMN `revisado_por` INT AFTER `fecha_revision`',
    'SELECT \"Column revisado_por already exists\" AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =====================================================
-- 8. DATOS INICIALES
-- =====================================================

-- Insertar empresa por defecto si no existe
INSERT IGNORE INTO empresas_grupo (id, nombre, nombre_corto, rfc, activo)
VALUES (1, 'Caja de Ahorros Principal', 'CAP', 'CAP000000XXX', 1);

-- Insertar unidad de negocio por defecto
INSERT IGNORE INTO unidades_negocio (id, empresa_id, nombre, clave, activo)
VALUES (1, 1, 'Oficina Central', 'UC-001', 1);

-- Insertar productos financieros básicos
INSERT IGNORE INTO productos_financieros (id, empresa_id, nombre, tipo, tasa_interes_min, tasa_interes_max, plazo_min_meses, plazo_max_meses, monto_min, monto_max, activo)
VALUES 
(1, 1, 'Crédito Personal', 'credito', 0.0100, 0.0200, 6, 60, 5000.00, 500000.00, 1),
(2, 1, 'Crédito Hipotecario', 'credito', 0.0080, 0.0150, 12, 240, 100000.00, 5000000.00, 1),
(3, 1, 'Crédito Auto', 'credito', 0.0100, 0.0180, 12, 84, 50000.00, 1000000.00, 1);

-- Insertar política de edad por defecto
INSERT IGNORE INTO politicas_credito (producto_id, nombre, descripcion, tipo, valor_min, valor_max, activo)
VALUES 
(NULL, 'Restricción de Plazo por Edad', 'Solicitantes mayores de 69 años solo pueden acceder a créditos de máximo 12 meses', 'edad', 69, 12, 1);

-- Insertar checklist básicos
INSERT IGNORE INTO checklists_credito (nombre, tipo_operacion, descripcion, orden, activo)
VALUES 
('Checklist Apertura de Crédito', 'apertura', 'Validación obligatoria para apertura de nuevos créditos', 1, 1),
('Checklist Renovación de Crédito', 'renovacion', 'Validación para renovación de créditos existentes', 2, 1),
('Checklist Reestructura de Crédito', 'reestructura', 'Validación para reestructuración de créditos', 3, 1);

-- Insertar items de checklist para apertura
INSERT IGNORE INTO checklist_items (checklist_id, descripcion, tipo, requerido, orden)
VALUES 
(1, 'Identificación oficial vigente', 'documento', 1, 1),
(1, 'Comprobante de domicilio (no mayor a 3 meses)', 'documento', 1, 2),
(1, 'Comprobante de ingresos', 'documento', 1, 3),
(1, 'Validación de edad y plazo máximo', 'validacion', 1, 4),
(1, 'Verificación de capacidad de pago', 'validacion', 1, 5),
(1, 'Consulta de buró de crédito', 'validacion', 1, 6),
(1, 'Aprobación del comité de crédito', 'aprobacion', 1, 7);

-- =====================================================
-- 9. VISTAS PARA REPORTES
-- =====================================================
-- Asegurarse que las columnas referenciadas en las vistas existen (empresa_id, tipo_cartera, dias_mora).
-- Si las columnas no existen, las vistas pueden fallar; las ALTER previos intentan crearlas.

CREATE OR REPLACE VIEW v_resumen_cartera AS
SELECT 
    c.empresa_id,
    e.nombre AS empresa_nombre,
    c.tipo_cartera,
    c.estatus,
    COUNT(*) AS total_creditos,
    SUM(c.saldo_actual) AS saldo_total,
    AVG(c.dias_mora) AS dias_mora_promedio,
    SUM(CASE WHEN c.dias_mora > 0 THEN 1 ELSE 0 END) AS creditos_con_mora
FROM creditos c
LEFT JOIN empresas_grupo e ON c.empresa_id = e.id
WHERE c.estatus IN ('activo', 'formalizado')
GROUP BY c.empresa_id, e.nombre, c.tipo_cartera, c.estatus;

CREATE OR REPLACE VIEW v_operaciones_diarias AS
SELECT 
    DATE(c.fecha_formalizacion) AS fecha,
    c.empresa_id,
    COUNT(*) AS total_operaciones,
    SUM(c.monto_autorizado) AS monto_total,
    AVG(c.monto_autorizado) AS monto_promedio,
    SUM(CASE WHEN c.estatus = 'activo' THEN 1 ELSE 0 END) AS creditos_activos,
    SUM(CASE WHEN c.estatus = 'rechazado' THEN 1 ELSE 0 END) AS creditos_rechazados
FROM creditos c
WHERE c.fecha_formalizacion IS NOT NULL
GROUP BY DATE(c.fecha_formalizacion), c.empresa_id;

CREATE OR REPLACE VIEW v_proyecciones_tesoreria AS
SELECT 
    a.fecha_vencimiento,
    a.credito_id,
    c.empresa_id,
    c.numero_credito,
    a.monto_capital,
    a.monto_interes,
    a.monto_total,
    a.estatus,
    CASE 
        WHEN a.estatus = 'pendiente' THEN a.monto_total
        ELSE 0
    END AS monto_esperado
FROM amortizacion a
INNER JOIN creditos c ON a.credito_id = c.id
WHERE c.estatus IN ('activo', 'formalizado')
ORDER BY a.fecha_vencimiento;

-- =====================================================
-- 10. ÍNDICES ADICIONALES PARA OPTIMIZACIÓN (amortizacion, socios)
-- =====================================================

SELECT COUNT(*) INTO @cnt FROM information_schema.statistics
 WHERE table_schema = DATABASE() AND table_name = 'amortizacion' AND index_name = 'idx_fecha_vencimiento';
SET @sql = IF(@cnt = 0,
    'ALTER TABLE `amortizacion` ADD INDEX `idx_fecha_vencimiento` (`fecha_vencimiento`)',
    'SELECT \"Index idx_fecha_vencimiento already exists or amortizacion missing\" AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @cnt FROM information_schema.statistics
 WHERE table_schema = DATABASE() AND table_name = 'amortizacion' AND index_name = 'idx_estatus';
SET @sql = IF(@cnt = 0,
    'ALTER TABLE `amortizacion` ADD INDEX `idx_estatus` (`estatus`)',
    'SELECT \"Index idx_estatus already exists or amortizacion missing\" AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @cnt FROM information_schema.statistics
 WHERE table_schema = DATABASE() AND table_name = 'socios' AND index_name = 'idx_fecha_nacimiento';
SET @sql = IF(@cnt = 0,
    'ALTER TABLE `socios` ADD INDEX `idx_fecha_nacimiento` (`fecha_nacimiento`)',
    'SELECT \"Index idx_fecha_nacimiento already exists or socios missing\" AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @cnt FROM information_schema.statistics
 WHERE table_schema = DATABASE() AND table_name = 'socios' AND index_name = 'idx_estatus';
SET @sql = IF(@cnt = 0,
    'ALTER TABLE `socios` ADD INDEX `idx_estatus` (`estatus`)',
    'SELECT \"Index idx_estatus already exists or socios missing\" AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =====================================================
-- FINALIZACIÓN
-- =====================================================
COMMIT;

-- =====================================================
-- RECOMENDACIONES POST-IMPORTACIÓN
-- =====================================================
-- 1) Verifique que las columnas nuevas aparecen en DESCRIBE creditos;
-- 2) Valide datos inconsistentes antes de agregar FK producto_financiero_id:
--    SELECT DISTINCT producto_financiero_id FROM creditos WHERE producto_financiero_id IS NOT NULL AND producto_financiero_id NOT IN (SELECT id FROM productos_financieros);
-- 3) Si necesita que agregue automáticamente la FK producto->creditos, solicite que revise los resultados del paso 2; puedo generar el ALTER para agregarla.
-- 4) Pruebe las vistas y los índices en entorno de staging antes de producción.
