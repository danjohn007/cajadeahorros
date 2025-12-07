# Sistema de Gestión Integral de Caja de Ahorros

Plataforma web integral para la gestión de socios, ahorros, créditos, descuentos vía nómina, cartera y reportes de la Caja de Ahorros.

## 📋 Características

### Módulos Implementados

- **🏠 Dashboard**: Panel de control con indicadores clave y resumen ejecutivo
- **👥 Gestión de Socios**: Padrón maestro con ID único, altas/bajas/modificaciones, historial de cambios
- **💰 Gestión de Ahorro**: Movimientos, saldos, historial por socio
- **💳 Gestión de Créditos**: Solicitudes, autorización, tablas de amortización, seguimiento
- **📄 Nómina y Descuentos**: Carga de archivos, matching automático, resolución de homonimias
- **📊 Cartera y Cobranza**: Cartera vencida, listados de mora, exportación
- **📈 Reportes y Tableros**: Reportes operativos y ejecutivos con gráficas
- **⚙️ Configuraciones**: Personalización del sistema, estilos, correo, PayPal, QR
- **👤 Gestión de Usuarios**: Control de acceso por roles (administrador, operativo, consulta)
- **📝 Bitácora**: Registro de todas las acciones del sistema

### 🆕 ID FINANCIERO - Nuevas Funcionalidades (v2.0)

- **🏢 Arquitectura Multiempresa**: Gestión de múltiples entidades del grupo con productos y unidades de negocio segregadas
- **📋 Políticas de Crédito**: Motor de reglas con validaciones automáticas de edad, montos y garantías
- **✅ Sistema de Checklists**: Validación obligatoria de documentos y requisitos por tipo de operación
- **💼 Módulo de Tesorería**: Proyecciones financieras y flujos de efectivo en tiempo real
- **📊 Reportes CNBV**: Generación automática de reportes regulatorios en XML/Excel
- **🤝 Gestión de Garantías**: Control de avales, obligados solidarios y garantías
- **📉 Gestión de Cartera Avanzada**: Traspasos automáticos, convenios de pago, liquidaciones

**Ver documentación completa**: [docs/ID_FINANCIERO.md](docs/ID_FINANCIERO.md)

### Tecnologías Utilizadas

- **Backend**: PHP Puro (sin framework) - Arquitectura MVC
- **Base de Datos**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Estilos**: Tailwind CSS
- **Gráficas**: Chart.js
- **Iconos**: Font Awesome

## 🚀 Requisitos del Sistema

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor Apache con mod_rewrite habilitado
- Extensiones PHP: PDO, PDO_MySQL, mbstring, json

## 📦 Instalación

### 1. Clonar o copiar el proyecto

```bash
git clone https://github.com/danjohn007/cajadeahorros.git
```

O descomprimir el archivo en el directorio de tu servidor web.

### 2. Configurar la base de datos

Crear la base de datos en MySQL:

```sql
CREATE DATABASE cajadeahorros CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Importar el esquema y datos de ejemplo:

```bash
mysql -u root -p cajadeahorros < database/schema.sql
```

### 3. Configurar credenciales de base de datos

Editar el archivo `config/config.php`:

```php
// Credenciales de Base de Datos
define('DB_HOST', 'localhost');     // Host de la base de datos
define('DB_NAME', 'cajadeahorros'); // Nombre de la base de datos
define('DB_USER', 'root');          // Usuario de MySQL
define('DB_PASS', '');              // Contraseña de MySQL
define('DB_CHARSET', 'utf8mb4');
```

### 4. Configurar Apache

Asegúrate de que mod_rewrite esté habilitado:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

El archivo `.htaccess` ya está incluido y configurado.

### 5. Permisos de directorios

```bash
chmod 755 -R /ruta/al/proyecto
chmod 777 -R uploads/
```

### 6. Verificar instalación

Acceder a la URL de prueba de conexión:

```
http://tu-servidor/cajadeahorros/test.php
```

## 🔐 Credenciales por Defecto

| Usuario | Contraseña | Rol |
|---------|------------|-----|
| admin@cajadeahorros.com | admin123 | Administrador |
| operador@cajadeahorros.com | operador123 | Operativo |
| consulta@cajadeahorros.com | consulta123 | Consulta |

**⚠️ IMPORTANTE**: Cambiar estas contraseñas después del primer inicio de sesión.

## 📁 Estructura del Proyecto

```
cajadeahorros/
├── app/
│   ├── controllers/     # Controladores MVC
│   ├── models/          # Modelos (pendiente)
│   └── views/           # Vistas organizadas por módulo
├── config/
│   ├── config.php       # Configuración general
│   └── routes.php       # Definición de rutas amigables
├── core/
│   ├── Controller.php   # Controlador base
│   ├── Database.php     # Clase de conexión PDO
│   ├── Model.php        # Modelo base
│   └── Router.php       # Enrutador de URLs amigables
├── database/
│   └── schema.sql       # Esquema y datos de ejemplo
├── public/
│   ├── css/             # Estilos personalizados
│   ├── js/              # JavaScript
│   └── images/          # Imágenes y logos
├── uploads/             # Archivos subidos
├── .htaccess            # Configuración Apache
├── index.php            # Punto de entrada
├── test.php             # Prueba de conexión
└── README.md            # Este archivo
```

## 🔗 URLs Amigables

El sistema implementa URLs amigables. Ejemplos:

- `/dashboard` - Panel principal
- `/socios` - Lista de socios
- `/socios/crear` - Nuevo socio
- `/socios/ver/1` - Ver socio #1
- `/creditos/solicitud` - Nueva solicitud de crédito
- `/nomina/cargar` - Cargar archivo de nómina
- `/reportes` - Dashboard de reportes
- `/configuraciones` - Configuraciones del sistema

## 🛡️ Seguridad

- Autenticación con sesiones PHP
- Contraseñas hasheadas con `password_hash()` (bcrypt)
- Protección CSRF en formularios
- Consultas preparadas (PDO) para prevenir SQL Injection
- Escape de salida con `htmlspecialchars()`
- Control de acceso basado en roles
- Bitácora de acciones

## 📊 Datos de Ejemplo

El archivo `database/schema.sql` incluye datos de ejemplo del estado de Querétaro:

- 3 usuarios del sistema
- 10 socios de ejemplo
- 5 unidades de trabajo
- 3 tipos de crédito
- Movimientos de ahorro
- Créditos activos
- Tabla de amortización

## 📝 Roles y Permisos

| Función | Administrador | Operativo | Consulta |
|---------|:-------------:|:---------:|:--------:|
| Ver dashboard | ✅ | ✅ | ✅ |
| Gestionar socios | ✅ | ✅ | ❌ |
| Ver socios | ✅ | ✅ | ✅ |
| Gestionar ahorros | ✅ | ✅ | ❌ |
| Gestionar créditos | ✅ | ✅ | ❌ |
| Autorizar créditos | ✅ | ❌ | ❌ |
| Cargar nómina | ✅ | ✅ | ❌ |
| Ver reportes | ✅ | ✅ | ✅ |
| Configuraciones | ✅ | ❌ | ❌ |
| Gestionar usuarios | ✅ | ❌ | ❌ |
| Ver bitácora | ✅ | ❌ | ❌ |

## 🔧 Configuraciones Globales

Desde el módulo de Configuraciones se puede:

- ✅ Cambiar nombre del sitio y logotipo
- ✅ Configurar correo del sistema
- ✅ Definir teléfonos y horarios de atención
- ✅ Cambiar colores principales del sistema
- ✅ Configurar cuenta de PayPal
- ✅ Generar códigos QR (individual y masivo)

## 📄 Licencia

Este proyecto es software propietario desarrollado para uso exclusivo de la Caja de Ahorros.

## 👨‍💻 Soporte

Para soporte técnico, contactar al administrador del sistema.
