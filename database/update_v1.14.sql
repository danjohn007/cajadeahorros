-- =====================================================
-- Sistema de Gestión Integral de Caja de Ahorros
-- Script de actualización v1.14
-- Compatible con MySQL 5.7+
-- Ejecutar después de las actualizaciones anteriores
-- =====================================================

-- Agregar campo created_by (asesor que dio de alta) a la tabla socios
SET @col_exists := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'socios' AND COLUMN_NAME = 'created_by');
SET @sql_stmt := IF(@col_exists = 0,
                    'ALTER TABLE socios ADD COLUMN created_by INT DEFAULT NULL AFTER estatus;',
                    'SELECT "created_by already exists";');
PREPARE stmt FROM @sql_stmt; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Agregar foreign key para created_by si no existe
SET @fk_exists := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                   WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'socios' 
                   AND COLUMN_NAME = 'created_by' AND REFERENCED_TABLE_NAME = 'usuarios');
SET @sql_stmt := IF(@fk_exists = 0 AND @col_exists > 0,
                    'ALTER TABLE socios ADD CONSTRAINT fk_socios_created_by FOREIGN KEY (created_by) REFERENCES usuarios(id) ON DELETE SET NULL;',
                    'SELECT "foreign key already exists or column does not exist";');
PREPARE stmt FROM @sql_stmt; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Agregar índice para mejorar el rendimiento en consultas con created_by
SET @idx_exists := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
                    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'socios' AND INDEX_NAME = 'idx_created_by');
SET @sql_stmt := IF(@idx_exists = 0,
                    'ALTER TABLE socios ADD INDEX idx_created_by (created_by);',
                    'SELECT "idx_created_by already exists";');
PREPARE stmt FROM @sql_stmt; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- End of update script v1.14
