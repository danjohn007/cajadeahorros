-- =====================================================
-- Sistema de Gestión Integral de Caja de Ahorros
-- Script de actualización v1.13 - Métodos de Pago y Mejoras
-- Compatible con MySQL 5.7+
-- Ejecutar después de las actualizaciones anteriores
-- =====================================================

USE caja_ahorros;

-- Agregar columnas de método de pago y comprobante a movimientos_ahorro
ALTER TABLE movimientos_ahorro 
ADD COLUMN metodo_pago VARCHAR(50) DEFAULT 'efectivo' AFTER referencia,
ADD COLUMN comprobante VARCHAR(255) DEFAULT NULL AFTER metodo_pago;

-- Crear índice en metodo_pago para mejorar consultas
CREATE INDEX idx_movimientos_ahorro_metodo_pago ON movimientos_ahorro(metodo_pago);

-- Actualizar registros existentes con método de pago por defecto
UPDATE movimientos_ahorro SET metodo_pago = 'efectivo' WHERE metodo_pago IS NULL;

-- Asegurar que la configuración de módulos deshabilitados existe
INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('modulos_deshabilitados', '[]', 'json', 'Lista de módulos especiales deshabilitados en formato JSON (financiero, membresias, inversionistas, crm, kyc, escrow)')
ON DUPLICATE KEY UPDATE descripcion = 'Lista de módulos especiales deshabilitados en formato JSON (financiero, membresias, inversionistas, crm, kyc, escrow)';

-- Verificar que existe el rol 'programador'
-- Este script asume que update_v1.12.sql ya se ejecutó
-- Si no existe el rol, agregarlo
SET @sql = CONCAT('ALTER TABLE usuarios MODIFY rol ENUM(\'administrador\', \'operativo\', \'consulta\', \'cliente\', \'inversionista\', \'programador\') NOT NULL DEFAULT \'consulta\'');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Comentario informativo
-- Los siguientes elementos ya deben estar presentes si se ejecutó update_v1.12.sql:
-- - Configuración chatbot_whatsapp_numero
-- - Configuración chatbot_url_publica  
-- - Configuración chatbot_mensaje_bienvenida
-- - Configuración chatbot_mensaje_horario
-- - Configuración email_contacto

-- Verificar existencia de configuraciones del chatbot (por si no se ejecutó v1.12)
INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('chatbot_whatsapp_numero', '', 'text', 'Número de WhatsApp del chatbot de soporte'),
('chatbot_url_publica', '', 'text', 'URL pública del chatbot de soporte'),
('chatbot_mensaje_bienvenida', 'Hola, bienvenido a nuestro soporte técnico. ¿En qué podemos ayudarte?', 'text', 'Mensaje de bienvenida del chatbot'),
('chatbot_mensaje_horario', 'Nuestro horario de atención es de Lunes a Viernes de 9:00 a 18:00 hrs.', 'text', 'Mensaje de horario del chatbot'),
('email_contacto', '', 'email', 'Correo electrónico de contacto para los clientes')
ON DUPLICATE KEY UPDATE clave = clave;

-- End of update script v1.13
