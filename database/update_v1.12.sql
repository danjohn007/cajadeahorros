-- =====================================================
-- Sistema de Gestión Integral de Caja de Ahorros
-- Script de actualización v1.12 - Módulos Especiales y Soporte
-- Compatible con MySQL 5.7
-- Ejecutar después de las actualizaciones anteriores
-- =====================================================

-- Ensure modulos_deshabilitados configuration exists
INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('modulos_deshabilitados', '[]', 'json', 'Lista de módulos especiales deshabilitados en formato JSON (financiero, membresias, inversionistas, crm, kyc, escrow)')
ON DUPLICATE KEY UPDATE descripcion = 'Lista de módulos especiales deshabilitados en formato JSON (financiero, membresias, inversionistas, crm, kyc, escrow)';

-- Ensure chatbot/soporte configuration exists
INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('chatbot_whatsapp_numero', '', 'text', 'Número de WhatsApp del chatbot de soporte'),
('chatbot_url_publica', '', 'text', 'URL pública del chatbot de soporte'),
('chatbot_mensaje_bienvenida', 'Hola, bienvenido a nuestro soporte técnico. ¿En qué podemos ayudarte?', 'text', 'Mensaje de bienvenida del chatbot'),
('chatbot_mensaje_horario', 'Nuestro horario de atención es de Lunes a Viernes de 9:00 a 18:00 hrs.', 'text', 'Mensaje de horario del chatbot'),
('chatbot_mensaje_fuera_horario', 'En este momento estamos fuera de horario. Por favor déjanos tu mensaje y te contactaremos a la brevedad.', 'text', 'Mensaje fuera de horario del chatbot')
ON DUPLICATE KEY UPDATE clave = clave;

-- Ensure email contact configuration exists (used in styled emails)
INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('email_contacto', '', 'email', 'Correo electrónico de contacto para los clientes')
ON DUPLICATE KEY UPDATE clave = clave;

-- Add programador role if not exists (from v1.11, ensuring compatibility)
-- Note: This ALTER may fail if the role already exists, which is fine
ALTER TABLE usuarios MODIFY rol ENUM('administrador', 'operativo', 'consulta', 'cliente', 'inversionista', 'programador') NOT NULL DEFAULT 'consulta';

-- End of update script v1.12
