-- =====================================================
-- Sistema de Gestión Integral de Caja de Ahorros
-- Script de actualización v1.11 - Nivel Usuario Programador y Módulos Especiales
-- Compatible con MySQL 5.7
-- Ejecutar después de las actualizaciones anteriores
-- =====================================================

-- Add new user role: programador
ALTER TABLE usuarios MODIFY rol ENUM('administrador', 'operativo', 'consulta', 'cliente', 'inversionista', 'programador') NOT NULL DEFAULT 'consulta';

-- Add modulos_deshabilitados configuration for special modules
INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('modulos_deshabilitados', '', 'json', 'Lista de módulos deshabilitados (JSON array)')
ON DUPLICATE KEY UPDATE clave = clave;

-- Add chatbot configuration
INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('chatbot_whatsapp_numero', '', 'text', 'Número de WhatsApp del chatbot'),
('chatbot_url_publica', '', 'text', 'URL pública del chatbot'),
('chatbot_mensaje_bienvenida', 'Hola, bienvenido a nuestro soporte técnico. ¿En qué podemos ayudarte?', 'text', 'Mensaje de bienvenida del chatbot'),
('chatbot_mensaje_horario', 'Nuestro horario de atención es de Lunes a Viernes de 9:00 a 18:00 hrs.', 'text', 'Mensaje de horario del chatbot'),
('chatbot_mensaje_fuera_horario', 'En este momento estamos fuera de horario. Por favor déjanos tu mensaje y te contactaremos a la brevedad.', 'text', 'Mensaje fuera de horario del chatbot')
ON DUPLICATE KEY UPDATE clave = clave;

-- End of update script v1.11
