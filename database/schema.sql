-- =====================================================
-- Sistema de Gestión Integral de Caja de Ahorros
-- Script de creación de base de datos
-- =====================================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS caja_ahorros CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE caja_ahorros;

-- =====================================================
-- TABLAS DE CONFIGURACIÓN Y SEGURIDAD
-- =====================================================

-- Tabla de configuraciones del sistema
CREATE TABLE IF NOT EXISTS configuraciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(100) NOT NULL UNIQUE,
    valor TEXT,
    tipo VARCHAR(50) DEFAULT 'text',
    descripcion VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de usuarios del sistema
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('administrador', 'operativo', 'consulta') NOT NULL DEFAULT 'consulta',
    activo TINYINT(1) DEFAULT 1,
    ultimo_acceso DATETIME,
    token_recuperacion VARCHAR(100),
    token_expiracion DATETIME,
    requiere_cambio_password TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de bitácora de acciones
CREATE TABLE IF NOT EXISTS bitacora (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    accion VARCHAR(100) NOT NULL,
    descripcion TEXT,
    entidad VARCHAR(50),
    entidad_id INT,
    ip VARCHAR(45),
    user_agent TEXT,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLAS DE GESTIÓN DE SOCIOS
-- =====================================================

-- Tabla de unidades de trabajo
CREATE TABLE IF NOT EXISTS unidades_trabajo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    clave VARCHAR(20),
    direccion TEXT,
    activo TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de socios (padrón maestro)
CREATE TABLE IF NOT EXISTS socios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_socio VARCHAR(20) UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    apellido_paterno VARCHAR(100) NOT NULL,
    apellido_materno VARCHAR(100),
    rfc VARCHAR(13),
    curp VARCHAR(18),
    fecha_nacimiento DATE,
    genero ENUM('M', 'F', 'O'),
    estado_civil ENUM('soltero', 'casado', 'divorciado', 'viudo', 'union_libre'),
    telefono VARCHAR(20),
    celular VARCHAR(20),
    email VARCHAR(100),
    direccion TEXT,
    colonia VARCHAR(100),
    municipio VARCHAR(100),
    estado VARCHAR(100),
    codigo_postal VARCHAR(10),
    unidad_trabajo_id INT,
    puesto VARCHAR(100),
    numero_empleado VARCHAR(50),
    fecha_ingreso_trabajo DATE,
    salario_mensual DECIMAL(12,2),
    fecha_alta DATE,
    fecha_baja DATE,
    motivo_baja TEXT,
    estatus ENUM('activo', 'inactivo', 'suspendido', 'baja') DEFAULT 'activo',
    beneficiario_nombre VARCHAR(200),
    beneficiario_parentesco VARCHAR(50),
    beneficiario_telefono VARCHAR(20),
    observaciones TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (unidad_trabajo_id) REFERENCES unidades_trabajo(id) ON DELETE SET NULL,
    INDEX idx_rfc (rfc),
    INDEX idx_curp (curp),
    INDEX idx_numero_socio (numero_socio),
    INDEX idx_estatus (estatus)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Historial de cambios de socios
CREATE TABLE IF NOT EXISTS socios_historial (
    id INT AUTO_INCREMENT PRIMARY KEY,
    socio_id INT NOT NULL,
    usuario_id INT,
    campo_modificado VARCHAR(100),
    valor_anterior TEXT,
    valor_nuevo TEXT,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (socio_id) REFERENCES socios(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLAS DE GESTIÓN DE AHORRO
-- =====================================================

-- Tabla de cuentas de ahorro
CREATE TABLE IF NOT EXISTS cuentas_ahorro (
    id INT AUTO_INCREMENT PRIMARY KEY,
    socio_id INT NOT NULL,
    numero_cuenta VARCHAR(20) UNIQUE,
    saldo DECIMAL(14,2) DEFAULT 0.00,
    tasa_interes DECIMAL(5,4) DEFAULT 0.0000,
    fecha_apertura DATE,
    estatus ENUM('activa', 'inactiva', 'bloqueada') DEFAULT 'activa',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (socio_id) REFERENCES socios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de movimientos de ahorro
CREATE TABLE IF NOT EXISTS movimientos_ahorro (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cuenta_id INT NOT NULL,
    tipo ENUM('aportacion', 'retiro', 'interes', 'ajuste') NOT NULL,
    monto DECIMAL(14,2) NOT NULL,
    saldo_anterior DECIMAL(14,2),
    saldo_nuevo DECIMAL(14,2),
    concepto VARCHAR(255),
    referencia VARCHAR(100),
    origen ENUM('ventanilla', 'nomina', 'transferencia', 'sistema') DEFAULT 'ventanilla',
    usuario_id INT,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cuenta_id) REFERENCES cuentas_ahorro(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_fecha (fecha),
    INDEX idx_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLAS DE GESTIÓN DE CRÉDITOS
-- =====================================================

-- Tipos de crédito
CREATE TABLE IF NOT EXISTS tipos_credito (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    tasa_interes DECIMAL(5,4) NOT NULL,
    plazo_minimo INT,
    plazo_maximo INT,
    monto_minimo DECIMAL(14,2),
    monto_maximo DECIMAL(14,2),
    requisitos TEXT,
    activo TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de créditos
CREATE TABLE IF NOT EXISTS creditos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_credito VARCHAR(20) UNIQUE,
    socio_id INT NOT NULL,
    tipo_credito_id INT NOT NULL,
    monto_solicitado DECIMAL(14,2) NOT NULL,
    monto_autorizado DECIMAL(14,2),
    tasa_interes DECIMAL(5,4) NOT NULL,
    plazo_meses INT NOT NULL,
    monto_cuota DECIMAL(14,2),
    fecha_solicitud DATE,
    fecha_autorizacion DATE,
    fecha_formalizacion DATE,
    fecha_primer_pago DATE,
    fecha_ultimo_pago DATE,
    saldo_actual DECIMAL(14,2),
    pagos_realizados INT DEFAULT 0,
    pagos_vencidos INT DEFAULT 0,
    estatus ENUM('solicitud', 'en_revision', 'autorizado', 'rechazado', 'formalizado', 'activo', 'liquidado', 'castigado') DEFAULT 'solicitud',
    autorizado_por INT,
    observaciones TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (socio_id) REFERENCES socios(id) ON DELETE CASCADE,
    FOREIGN KEY (tipo_credito_id) REFERENCES tipos_credito(id),
    FOREIGN KEY (autorizado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_estatus (estatus),
    INDEX idx_socio (socio_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de amortización
CREATE TABLE IF NOT EXISTS amortizacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    credito_id INT NOT NULL,
    numero_pago INT NOT NULL,
    fecha_vencimiento DATE NOT NULL,
    monto_capital DECIMAL(14,2) NOT NULL,
    monto_interes DECIMAL(14,2) NOT NULL,
    monto_total DECIMAL(14,2) NOT NULL,
    saldo_restante DECIMAL(14,2),
    fecha_pago DATE,
    monto_pagado DECIMAL(14,2),
    estatus ENUM('pendiente', 'pagado', 'parcial', 'vencido') DEFAULT 'pendiente',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (credito_id) REFERENCES creditos(id) ON DELETE CASCADE,
    INDEX idx_credito_numero (credito_id, numero_pago),
    INDEX idx_estatus (estatus)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pagos de créditos
CREATE TABLE IF NOT EXISTS pagos_credito (
    id INT AUTO_INCREMENT PRIMARY KEY,
    credito_id INT NOT NULL,
    amortizacion_id INT,
    monto DECIMAL(14,2) NOT NULL,
    monto_capital DECIMAL(14,2),
    monto_interes DECIMAL(14,2),
    monto_mora DECIMAL(14,2) DEFAULT 0.00,
    fecha_pago DATETIME DEFAULT CURRENT_TIMESTAMP,
    origen ENUM('ventanilla', 'nomina', 'transferencia') DEFAULT 'ventanilla',
    referencia VARCHAR(100),
    usuario_id INT,
    observaciones TEXT,
    FOREIGN KEY (credito_id) REFERENCES creditos(id) ON DELETE CASCADE,
    FOREIGN KEY (amortizacion_id) REFERENCES amortizacion(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Documentos de créditos
CREATE TABLE IF NOT EXISTS documentos_credito (
    id INT AUTO_INCREMENT PRIMARY KEY,
    credito_id INT NOT NULL,
    tipo VARCHAR(100) NOT NULL,
    nombre_archivo VARCHAR(255),
    ruta_archivo VARCHAR(500),
    fecha_subida DATETIME DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT,
    FOREIGN KEY (credito_id) REFERENCES creditos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLAS DE NÓMINA Y DESCUENTOS
-- =====================================================

-- Archivos de nómina cargados
CREATE TABLE IF NOT EXISTS archivos_nomina (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_archivo VARCHAR(255) NOT NULL,
    ruta_archivo VARCHAR(500),
    periodo VARCHAR(50),
    fecha_nomina DATE,
    total_registros INT DEFAULT 0,
    registros_procesados INT DEFAULT 0,
    registros_pendientes INT DEFAULT 0,
    registros_error INT DEFAULT 0,
    estatus ENUM('cargado', 'procesando', 'pendiente_revision', 'aplicado', 'error') DEFAULT 'cargado',
    usuario_id INT,
    fecha_carga DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_procesamiento DATETIME,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Registros de nómina
CREATE TABLE IF NOT EXISTS registros_nomina (
    id INT AUTO_INCREMENT PRIMARY KEY,
    archivo_id INT NOT NULL,
    rfc VARCHAR(13),
    curp VARCHAR(18),
    nombre_nomina VARCHAR(200),
    numero_empleado VARCHAR(50),
    monto_descuento DECIMAL(14,2),
    concepto VARCHAR(100),
    socio_id INT,
    estatus ENUM('pendiente', 'coincidencia', 'homonimia', 'sin_coincidencia', 'aplicado', 'error') DEFAULT 'pendiente',
    mensaje_error TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (archivo_id) REFERENCES archivos_nomina(id) ON DELETE CASCADE,
    FOREIGN KEY (socio_id) REFERENCES socios(id) ON DELETE SET NULL,
    INDEX idx_rfc (rfc),
    INDEX idx_curp (curp),
    INDEX idx_estatus (estatus)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de equivalencias para resolución de homonimias
CREATE TABLE IF NOT EXISTS equivalencias_nomina (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rfc VARCHAR(13),
    curp VARCHAR(18),
    nombre_nomina VARCHAR(200),
    socio_id INT NOT NULL,
    usuario_id INT,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (socio_id) REFERENCES socios(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_rfc (rfc),
    INDEX idx_curp (curp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- VISTAS ÚTILES
-- =====================================================

-- Vista de saldo global de ahorro
CREATE OR REPLACE VIEW v_saldo_ahorro_global AS
SELECT 
    COUNT(DISTINCT ca.socio_id) as total_socios,
    SUM(ca.saldo) as saldo_total
FROM cuentas_ahorro ca
WHERE ca.estatus = 'activa';

-- Vista de cartera de créditos
CREATE OR REPLACE VIEW v_cartera_creditos AS
SELECT 
    c.id,
    c.numero_credito,
    s.numero_socio,
    CONCAT(s.nombre, ' ', s.apellido_paterno, ' ', COALESCE(s.apellido_materno, '')) as nombre_socio,
    tc.nombre as tipo_credito,
    c.monto_autorizado,
    c.saldo_actual,
    c.plazo_meses,
    c.pagos_realizados,
    c.pagos_vencidos,
    c.estatus,
    c.fecha_formalizacion
FROM creditos c
JOIN socios s ON c.socio_id = s.id
JOIN tipos_credito tc ON c.tipo_credito_id = tc.id
WHERE c.estatus IN ('activo', 'formalizado');

-- Vista de cartera vencida
CREATE OR REPLACE VIEW v_cartera_vencida AS
SELECT 
    c.id as credito_id,
    c.numero_credito,
    s.id as socio_id,
    s.numero_socio,
    CONCAT(s.nombre, ' ', s.apellido_paterno) as nombre_socio,
    s.telefono,
    s.celular,
    s.email,
    a.numero_pago,
    a.fecha_vencimiento,
    a.monto_total as monto_vencido,
    DATEDIFF(CURDATE(), a.fecha_vencimiento) as dias_vencido,
    CASE 
        WHEN DATEDIFF(CURDATE(), a.fecha_vencimiento) <= 30 THEN '1-30 días'
        WHEN DATEDIFF(CURDATE(), a.fecha_vencimiento) <= 60 THEN '31-60 días'
        WHEN DATEDIFF(CURDATE(), a.fecha_vencimiento) <= 90 THEN '61-90 días'
        ELSE 'Más de 90 días'
    END as rango_vencimiento
FROM amortizacion a
JOIN creditos c ON a.credito_id = c.id
JOIN socios s ON c.socio_id = s.id
WHERE a.estatus = 'vencido' OR (a.estatus = 'pendiente' AND a.fecha_vencimiento < CURDATE());

-- =====================================================
-- DATOS INICIALES
-- =====================================================

-- Configuraciones iniciales
INSERT INTO configuraciones (clave, valor, tipo, descripcion) VALUES
('nombre_sitio', 'Caja de Ahorros Sindicato', 'text', 'Nombre del sitio'),
('logo', 'logo.png', 'image', 'Logotipo del sistema'),
('correo_sistema', 'sistema@cajadeahorros.com', 'email', 'Correo principal del sistema'),
('telefono_contacto', '+52 442 123 4567', 'text', 'Teléfono de contacto'),
('horario_atencion', 'Lunes a Viernes 9:00 - 18:00', 'text', 'Horario de atención'),
('color_primario', '#1e40af', 'color', 'Color primario del sistema'),
('color_secundario', '#3b82f6', 'color', 'Color secundario del sistema'),
('paypal_client_id', '', 'text', 'Client ID de PayPal'),
('paypal_secret', '', 'password', 'Secret de PayPal'),
('tasa_interes_ahorro', '0.03', 'number', 'Tasa de interés anual para ahorro'),
('tasa_mora', '0.02', 'number', 'Tasa de mora mensual');

-- Usuario administrador por defecto (password: admin123)
-- NOTA: Estos usuarios requieren cambio de contraseña en el primer inicio de sesión
-- IMPORTANTE: Cambiar las contraseñas inmediatamente después de la instalación
INSERT INTO usuarios (nombre, email, password, rol, activo, requiere_cambio_password) VALUES
('Administrador Sistema', 'admin@cajadeahorros.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador', 1, 1),
('Operador Caja', 'operador@cajadeahorros.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'operativo', 1, 1),
('Consulta General', 'consulta@cajadeahorros.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'consulta', 1, 1);

-- Unidades de trabajo (dependencias del estado de Querétaro)
INSERT INTO unidades_trabajo (nombre, clave, direccion, activo) VALUES
('Secretaría de Gobierno', 'SEGOB', 'Av. 5 de Mayo No. 6, Centro, Querétaro, Qro.', 1),
('Secretaría de Finanzas', 'SEFIN', 'Av. Constituyentes Pte. No. 180, Centro, Querétaro, Qro.', 1),
('Secretaría de Educación', 'SEDEQ', 'Luis Pasteur Sur No. 36, Centro, Querétaro, Qro.', 1),
('Secretaría de Salud', 'SESA', 'Av. Tecnológico No. 600, San Pablo, Querétaro, Qro.', 1),
('Secretaría de Desarrollo Urbano', 'SEDESU', 'Av. 5 de Febrero No. 35, Centro, Querétaro, Qro.', 1),
('DIF Estatal Querétaro', 'DIF', 'Blvd. Bernardo Quintana No. 600, Arboledas, Querétaro, Qro.', 1),
('Universidad Autónoma de Querétaro', 'UAQ', 'Cerro de las Campanas s/n, Centro Universitario, Querétaro, Qro.', 1),
('Instituto de la Vivienda', 'IVEQ', 'Av. Luis Pasteur Sur No. 8, Centro, Querétaro, Qro.', 1);

-- Tipos de crédito
INSERT INTO tipos_credito (nombre, descripcion, tasa_interes, plazo_minimo, plazo_maximo, monto_minimo, monto_maximo, requisitos, activo) VALUES
('Crédito Personal', 'Crédito para gastos personales diversos', 0.0150, 6, 24, 5000.00, 50000.00, 'Antigüedad mínima 6 meses, Ahorro mínimo $1,000', 1),
('Crédito de Emergencia', 'Crédito para emergencias médicas o familiares', 0.0100, 3, 12, 1000.00, 20000.00, 'Antigüedad mínima 3 meses', 1),
('Crédito Hipotecario', 'Crédito para adquisición o mejora de vivienda', 0.0120, 12, 120, 50000.00, 500000.00, 'Antigüedad mínima 2 años, Ahorro mínimo $20,000, Aval', 1),
('Crédito Automotriz', 'Crédito para compra de vehículo', 0.0130, 12, 48, 30000.00, 300000.00, 'Antigüedad mínima 1 año, Ahorro mínimo $10,000', 1),
('Crédito Educativo', 'Crédito para gastos educativos', 0.0080, 6, 36, 5000.00, 100000.00, 'Antigüedad mínima 6 meses', 1);

-- Socios de ejemplo
INSERT INTO socios (numero_socio, nombre, apellido_paterno, apellido_materno, rfc, curp, fecha_nacimiento, genero, estado_civil, telefono, celular, email, direccion, colonia, municipio, estado, codigo_postal, unidad_trabajo_id, puesto, numero_empleado, fecha_ingreso_trabajo, salario_mensual, fecha_alta, estatus) VALUES
('SOC-0001', 'Juan Carlos', 'García', 'López', 'GALJ850315ABC', 'GALJ850315HQTRRN09', '1985-03-15', 'M', 'casado', '4421234567', '4421234568', 'juan.garcia@email.com', 'Calle Hidalgo 123', 'Centro', 'Querétaro', 'Querétaro', '76000', 1, 'Analista Administrativo', 'EMP001', '2015-06-01', 18500.00, '2016-01-15', 'activo'),
('SOC-0002', 'María Elena', 'Hernández', 'Ramírez', 'HERM900520DEF', 'HERM900520MQTRML05', '1990-05-20', 'F', 'soltero', '4422345678', '4422345679', 'maria.hernandez@email.com', 'Av. Universidad 456', 'Juriquilla', 'Querétaro', 'Querétaro', '76230', 3, 'Profesora', 'EMP002', '2018-08-15', 22000.00, '2019-02-20', 'activo'),
('SOC-0003', 'Roberto', 'Martínez', 'Sánchez', 'MASR780810GHI', 'MASR780810HQTRNB08', '1978-08-10', 'M', 'casado', '4423456789', '4423456780', 'roberto.martinez@email.com', 'Privada de las Rosas 78', 'Colinas del Cimatario', 'Querétaro', 'Querétaro', '76090', 2, 'Contador', 'EMP003', '2010-03-01', 28000.00, '2011-05-10', 'activo'),
('SOC-0004', 'Ana Lucía', 'Torres', 'Mendoza', 'TOMA880215JKL', 'TOMA880215MQTRNN08', '1988-02-15', 'F', 'casado', '4424567890', '4424567891', 'ana.torres@email.com', 'Blvd. Constituyentes 890', 'El Marqués', 'Querétaro', 'Querétaro', '76047', 4, 'Enfermera', 'EMP004', '2016-11-01', 16500.00, '2017-03-25', 'activo'),
('SOC-0005', 'Pedro', 'López', 'García', 'LOGP950630MNO', 'LOGP950630HQTPRR05', '1995-06-30', 'M', 'soltero', '4425678901', '4425678902', 'pedro.lopez@email.com', 'Calle Independencia 234', 'Centro', 'San Juan del Río', 'Querétaro', '76800', 5, 'Arquitecto Jr', 'EMP005', '2020-01-15', 15000.00, '2020-06-01', 'activo'),
('SOC-0006', 'Carmen', 'Ruiz', 'Flores', 'RUFC820912PQR', 'RUFC820912MQTLZR09', '1982-09-12', 'F', 'divorciado', '4426789012', '4426789013', 'carmen.ruiz@email.com', 'Calle Corregidora 567', 'Centro', 'Querétaro', 'Querétaro', '76000', 6, 'Trabajadora Social', 'EMP006', '2012-07-01', 19500.00, '2013-01-10', 'activo'),
('SOC-0007', 'Miguel Ángel', 'Sánchez', 'Ortiz', 'SAOM870425STU', 'SAOM870425HQTNNR07', '1987-04-25', 'M', 'casado', '4427890123', '4427890124', 'miguel.sanchez@email.com', 'Av. Tecnológico 1234', 'San Pablo', 'Querétaro', 'Querétaro', '76130', 7, 'Profesor Universitario', 'EMP007', '2014-02-01', 32000.00, '2014-08-15', 'activo'),
('SOC-0008', 'Laura Patricia', 'Morales', 'Jiménez', 'MOJL910318VWX', 'MOJL910318MQTRMR06', '1991-03-18', 'F', 'soltero', '4428901234', '4428901235', 'laura.morales@email.com', 'Privada del Sol 45', 'Lomas de Casa Blanca', 'Querétaro', 'Querétaro', '76080', 8, 'Analista de Proyectos', 'EMP008', '2019-05-01', 21000.00, '2019-10-20', 'activo');

-- Cuentas de ahorro para los socios
INSERT INTO cuentas_ahorro (socio_id, numero_cuenta, saldo, tasa_interes, fecha_apertura, estatus) VALUES
(1, 'AHO-0001', 25000.00, 0.0300, '2016-01-15', 'activa'),
(2, 'AHO-0002', 15500.00, 0.0300, '2019-02-20', 'activa'),
(3, 'AHO-0003', 85000.00, 0.0300, '2011-05-10', 'activa'),
(4, 'AHO-0004', 12000.00, 0.0300, '2017-03-25', 'activa'),
(5, 'AHO-0005', 5500.00, 0.0300, '2020-06-01', 'activa'),
(6, 'AHO-0006', 35000.00, 0.0300, '2013-01-10', 'activa'),
(7, 'AHO-0007', 62000.00, 0.0300, '2014-08-15', 'activa'),
(8, 'AHO-0008', 18000.00, 0.0300, '2019-10-20', 'activa');

-- Movimientos de ahorro de ejemplo
INSERT INTO movimientos_ahorro (cuenta_id, tipo, monto, saldo_anterior, saldo_nuevo, concepto, origen, usuario_id, fecha) VALUES
(1, 'aportacion', 5000.00, 0.00, 5000.00, 'Aportación inicial', 'ventanilla', 1, '2016-01-15 10:00:00'),
(1, 'aportacion', 2000.00, 5000.00, 7000.00, 'Aportación quincenal', 'nomina', 1, '2016-02-01 00:00:00'),
(1, 'aportacion', 2000.00, 7000.00, 9000.00, 'Aportación quincenal', 'nomina', 1, '2016-02-15 00:00:00'),
(2, 'aportacion', 3000.00, 0.00, 3000.00, 'Aportación inicial', 'ventanilla', 1, '2019-02-20 11:30:00'),
(2, 'aportacion', 1500.00, 3000.00, 4500.00, 'Aportación quincenal', 'nomina', 1, '2019-03-01 00:00:00'),
(3, 'aportacion', 10000.00, 0.00, 10000.00, 'Aportación inicial', 'ventanilla', 1, '2011-05-10 09:00:00'),
(3, 'aportacion', 5000.00, 10000.00, 15000.00, 'Aportación quincenal', 'nomina', 1, '2011-06-01 00:00:00'),
(3, 'retiro', 3000.00, 15000.00, 12000.00, 'Retiro personal', 'ventanilla', 1, '2011-07-15 14:00:00');

-- Créditos de ejemplo
INSERT INTO creditos (numero_credito, socio_id, tipo_credito_id, monto_solicitado, monto_autorizado, tasa_interes, plazo_meses, monto_cuota, fecha_solicitud, fecha_autorizacion, fecha_formalizacion, fecha_primer_pago, saldo_actual, pagos_realizados, estatus, autorizado_por) VALUES
('CRE-0001', 1, 1, 30000.00, 30000.00, 0.0150, 12, 2726.71, '2023-01-10', '2023-01-12', '2023-01-15', '2023-02-15', 18500.00, 6, 'activo', 1),
('CRE-0002', 3, 3, 200000.00, 200000.00, 0.0120, 60, 4078.33, '2022-06-01', '2022-06-10', '2022-06-15', '2022-07-15', 145000.00, 18, 'activo', 1),
('CRE-0003', 4, 2, 15000.00, 15000.00, 0.0100, 6, 2563.00, '2023-09-01', '2023-09-02', '2023-09-05', '2023-10-05', 7500.00, 3, 'activo', 1),
('CRE-0004', 7, 4, 150000.00, 150000.00, 0.0130, 36, 4891.00, '2023-03-15', '2023-03-20', '2023-03-25', '2023-04-25', 112000.00, 9, 'activo', 1);

-- Amortización para crédito 1 (ejemplo parcial)
INSERT INTO amortizacion (credito_id, numero_pago, fecha_vencimiento, monto_capital, monto_interes, monto_total, saldo_restante, fecha_pago, monto_pagado, estatus) VALUES
(1, 1, '2023-02-15', 2276.71, 450.00, 2726.71, 27723.29, '2023-02-15', 2726.71, 'pagado'),
(1, 2, '2023-03-15', 2310.86, 415.85, 2726.71, 25412.43, '2023-03-15', 2726.71, 'pagado'),
(1, 3, '2023-04-15', 2345.52, 381.19, 2726.71, 23066.91, '2023-04-15', 2726.71, 'pagado'),
(1, 4, '2023-05-15', 2380.71, 346.00, 2726.71, 20686.20, '2023-05-15', 2726.71, 'pagado'),
(1, 5, '2023-06-15', 2416.42, 310.29, 2726.71, 18269.78, '2023-06-15', 2726.71, 'pagado'),
(1, 6, '2023-07-15', 2452.67, 274.04, 2726.71, 15817.11, '2023-07-15', 2726.71, 'pagado'),
(1, 7, '2023-08-15', 2489.46, 237.26, 2726.72, 13327.65, NULL, NULL, 'pendiente'),
(1, 8, '2023-09-15', 2526.81, 199.91, 2726.72, 10800.84, NULL, NULL, 'vencido'),
(1, 9, '2023-10-15', 2564.71, 162.01, 2726.72, 8236.13, NULL, NULL, 'vencido'),
(1, 10, '2023-11-15', 2603.18, 123.54, 2726.72, 5632.95, NULL, NULL, 'pendiente'),
(1, 11, '2023-12-15', 2642.23, 84.49, 2726.72, 2990.72, NULL, NULL, 'pendiente'),
(1, 12, '2024-01-15', 2990.72, 44.86, 3035.58, 0.00, NULL, NULL, 'pendiente');

-- Registrar acciones en bitácora
INSERT INTO bitacora (usuario_id, accion, descripcion, entidad, entidad_id, ip, fecha) VALUES
(1, 'LOGIN', 'Inicio de sesión exitoso', 'usuarios', 1, '127.0.0.1', NOW()),
(1, 'CREAR_SOCIO', 'Se creó el socio Juan Carlos García López', 'socios', 1, '127.0.0.1', '2016-01-15 10:00:00'),
(1, 'CREAR_CUENTA', 'Se creó cuenta de ahorro AHO-0001', 'cuentas_ahorro', 1, '127.0.0.1', '2016-01-15 10:05:00'),
(1, 'CREAR_CREDITO', 'Se creó solicitud de crédito CRE-0001', 'creditos', 1, '127.0.0.1', '2023-01-10 09:00:00'),
(1, 'AUTORIZAR_CREDITO', 'Se autorizó crédito CRE-0001', 'creditos', 1, '127.0.0.1', '2023-01-12 11:00:00');
