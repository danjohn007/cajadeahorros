-- =====================================================
-- Sistema de Gestión Integral de Caja de Ahorros
-- Script de actualización v1.15
-- Compatible con MySQL 5.7+
-- Ejecutar después de update_v1.14.sql
-- =====================================================

-- 1. Agregar rol 'programador' al ENUM de usuarios
-- Primero, verificar si 'programador' ya existe en el ENUM
SET @column_type := (SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS
                     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'rol');

-- Solo agregar 'programador' si no existe en el ENUM
SET @has_programador := LOCATE('programador', @column_type);
SET @sql_stmt := IF(@has_programador = 0,
                    "ALTER TABLE usuarios MODIFY COLUMN rol ENUM('administrador', 'operativo', 'consulta', 'programador', 'cliente') NOT NULL DEFAULT 'consulta';",
                    'SELECT "programador role already exists";');
PREPARE stmt FROM @sql_stmt; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2. Agregar rol 'cliente' si no existe (para usuarios del portal)
-- Ya se agregó en la modificación anterior

-- 3. Actualizar comentarios en configuraciones
UPDATE configuraciones 
SET descripcion = 'Rol de usuario: administrador (todos los permisos), operativo (operaciones diarias), consulta (solo lectura), programador (administrador + módulos especiales), cliente (portal del cliente)'
WHERE clave = 'roles_sistema';

-- 4. Insertar configuración si no existe
INSERT IGNORE INTO configuraciones (clave, valor, tipo, descripcion)
VALUES ('mostrar_asesor_cardex', '1', 'boolean', 'Mostrar nombre del asesor en cardex y estados de cuenta');

-- 5. Insertar configuración para logo si no existe
INSERT IGNORE INTO configuraciones (clave, valor, tipo, descripcion)
VALUES ('mostrar_logo_impresion', '1', 'boolean', 'Mostrar logo en documentos impresos (cardex y estados de cuenta)');

-- Fin del script de actualización v1.15
-- Nota: Los cambios de interfaz (vistas PHP) se aplican directamente en los archivos correspondientes
-- Cambios principales:
-- - Agregado rol 'programador' con acceso a módulos especiales
-- - Mejoras en impresión de cardex y estados de cuenta
-- - Corrección de botón WhatsApp duplicado
-- - Corrección de ruta 404 en solicitudes de actualización
