-- =====================================================
-- Sistema de Gestión Integral de Caja de Ahorros
-- Script de actualización v1.9
-- Mejoras en Pagos: Métodos de Pago y Comprobantes
-- =====================================================

USE caja_ahorros;

-- =====================================================
-- ACTUALIZAR TABLA pagos_credito
-- Agregar campos para método de pago y observaciones
-- =====================================================

-- Modificar la columna 'origen' para soportar más métodos de pago
ALTER TABLE pagos_credito 
MODIFY COLUMN origen ENUM('efectivo', 'transferencia', 'cheque', 'tarjeta_debito', 'tarjeta_credito', 'deposito', 'nomina', 'oxxo', 'spei', 'paypal', 'ventanilla', 'otro') DEFAULT 'efectivo';

-- La columna observaciones ya existe en pagos_credito, no es necesario agregarla.

-- =====================================================
-- TABLA DE CATÁLOGO DE MÉTODOS DE PAGO
-- Esta tabla almacena los diferentes métodos de pago disponibles
-- para registrar pagos de créditos. Se relaciona con la tabla
-- pagos_credito a través del campo 'origen' que debe coincidir
-- con el campo 'codigo' de esta tabla.
-- =====================================================

CREATE TABLE IF NOT EXISTS metodos_pago (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    requiere_referencia TINYINT(1) DEFAULT 0,
    requiere_comprobante TINYINT(1) DEFAULT 0,
    activo TINYINT(1) DEFAULT 1,
    orden INT DEFAULT 0,
    icono VARCHAR(50) DEFAULT 'fas fa-money-bill',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar métodos de pago predeterminados
INSERT INTO metodos_pago (codigo, nombre, descripcion, requiere_referencia, requiere_comprobante, activo, orden, icono) VALUES
('efectivo', 'Efectivo', 'Pago en efectivo en ventanilla', 0, 0, 1, 1, 'fas fa-money-bill-wave'),
('transferencia', 'Transferencia Bancaria', 'Transferencia electrónica entre cuentas bancarias', 1, 1, 1, 2, 'fas fa-exchange-alt'),
('cheque', 'Cheque', 'Pago mediante cheque bancario', 1, 1, 1, 3, 'fas fa-money-check'),
('tarjeta_debito', 'Tarjeta de Débito', 'Pago con tarjeta de débito en terminal', 1, 0, 1, 4, 'fas fa-credit-card'),
('tarjeta_credito', 'Tarjeta de Crédito', 'Pago con tarjeta de crédito en terminal', 1, 0, 1, 5, 'fas fa-credit-card'),
('deposito', 'Depósito en Cuenta', 'Depósito directo en cuenta bancaria de la caja', 1, 1, 1, 6, 'fas fa-piggy-bank'),
('nomina', 'Descuento Vía Nómina', 'Descuento automático de nómina', 0, 0, 1, 7, 'fas fa-file-invoice-dollar'),
('oxxo', 'Pago en OXXO', 'Pago realizado en tienda OXXO', 1, 1, 1, 8, 'fas fa-store'),
('spei', 'SPEI', 'Sistema de Pagos Electrónicos Interbancarios', 1, 1, 1, 9, 'fas fa-bolt'),
('paypal', 'PayPal', 'Pago realizado a través de PayPal', 1, 0, 1, 10, 'fab fa-paypal'),
('otro', 'Otro', 'Otro método de pago', 0, 0, 1, 99, 'fas fa-ellipsis-h')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), descripcion = VALUES(descripcion);

-- =====================================================
-- TABLA DE PAGOS ONLINE (si no existe)
-- =====================================================

CREATE TABLE IF NOT EXISTS pagos_online (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token_pago_id INT,
    credito_id INT NOT NULL,
    amortizacion_id INT,
    monto DECIMAL(14,2) NOT NULL,
    paypal_order_id VARCHAR(100),
    paypal_transaction_id VARCHAR(100),
    payer_email VARCHAR(100),
    estatus ENUM('pendiente', 'completado', 'fallido', 'reembolsado') DEFAULT 'pendiente',
    fecha_pago DATETIME DEFAULT CURRENT_TIMESTAMP,
    datos_respuesta JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (credito_id) REFERENCES creditos(id) ON DELETE CASCADE,
    FOREIGN KEY (amortizacion_id) REFERENCES amortizacion(id) ON DELETE SET NULL,
    INDEX idx_credito (credito_id),
    INDEX idx_paypal_order (paypal_order_id),
    INDEX idx_estatus (estatus)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CONFIGURACIONES ADICIONALES
-- =====================================================

INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('paypal_enabled', '0', 'boolean', 'Habilitar pagos con PayPal'),
('paypal_mode', 'sandbox', 'text', 'Modo de PayPal (sandbox o live)')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- =====================================================
-- ACTUALIZACIÓN DE VERSIÓN DEL SISTEMA
-- =====================================================

UPDATE configuraciones SET valor = '1.9.0' WHERE clave = 'version_sistema';
UPDATE configuraciones SET valor = NOW() WHERE clave = 'fecha_actualizacion';

-- Si no existen las configuraciones de versión, crearlas
INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('version_sistema', '1.9.0', 'text', 'Versión actual del sistema')
ON DUPLICATE KEY UPDATE valor = '1.9.0';

INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('fecha_actualizacion', NOW(), 'datetime', 'Fecha de última actualización')
ON DUPLICATE KEY UPDATE valor = NOW();

-- =====================================================
-- FIN DE ACTUALIZACIÓN v1.9
-- =====================================================
