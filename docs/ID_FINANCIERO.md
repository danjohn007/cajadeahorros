# ID FINANCIERO - Sistema de Gestión de Crédito Multiempresa

## Descripción

ID FINANCIERO es una plataforma integral para la administración de productos financieros, gestión de créditos, tesorería y cumplimiento regulatorio (CNBV). El sistema opera bajo una arquitectura multiempresa, permitiendo que distintas entidades del grupo gestionen sus propios productos, unidades de negocio y fuerzas de ventas de manera centralizada pero segregada.

## Nuevas Funcionalidades Implementadas

### 1. Arquitectura Multiempresa

El sistema ahora soporta múltiples empresas dentro de un mismo grupo financiero:

#### Tablas Principales

- **empresas_grupo**: Entidades legales que poseen los productos y emiten los créditos
- **unidades_negocio**: Subdivisiones operativas de cada empresa
- **productos_financieros**: Catálogo de productos vinculados a empresas específicas
- **fuerza_ventas**: Promotores y asesores asociados a unidades de negocio
- **poblaciones**: Catálogo geográfico normalizado para direcciones

### 2. Módulo de Políticas de Crédito

Sistema de reglas de negocio configurables con validaciones automáticas.

#### Controlador: `PoliticasCreditoController`

**Reglas de Negocio Implementadas:**

1. **Restricción por Edad**: Solicitantes mayores de 69 años solo pueden acceder a créditos con plazo máximo de 12 meses
2. **Requerimiento de Aval**: Montos superiores al umbral configurado requieren aval u obligado solidario
3. **Checklists Obligatorios**: Validación de documentos y requisitos antes de cambiar el estado de un expediente

### 3. Sistema de Checklists

Validación estructurada para diferentes tipos de operaciones crediticias (apertura, renovación, reestructura).

### 4. Módulo de Tesorería

Gestión de flujos de efectivo y proyecciones financieras basadas en tablas de amortización.

### 5. Reportes Regulatorios CNBV

Generación de reportes en formato XML/Excel según normativa de la Comisión Nacional Bancaria y de Valores.

### 6. Gestión de Garantías y Avales

Registro y control de avales, obligados solidarios y garantías.

### 7. Gestión de Cartera

Control de traspasos entre cartera vigente y vencida, convenios de pago y liquidaciones.

## Instalación y Configuración

### 1. Ejecutar Migración de Base de Datos

```bash
mysql -u root -p cajadeahorros < database/update_id_financiero.sql
```

Este script creará:
- 23 nuevas tablas
- 3 vistas predefinidas
- Datos iniciales de ejemplo
- Índices optimizados

### 2. Incluir JavaScript en Formularios

Agregar en las vistas de formularios de crédito:

```html
<script src="/public/js/politicas-credito.js"></script>
```

## Endpoints API Disponibles

### Políticas de Crédito

- `POST /politicas/validar-edad-plazo` - Validar edad y plazo de solicitante
- `POST /politicas/validar-aval` - Validar si requiere aval por monto
- `GET /politicas/checklist` - Obtener checklist por tipo de operación
- `GET /politicas/checklist/validar` - Validar checklist de crédito
- `POST /politicas/checklist/marcar` - Marcar item de checklist

### Tesorería

- `GET /tesoreria` - Vista principal
- `GET /tesoreria/proyecciones` - Proyecciones financieras
- `GET /tesoreria/flujos` - Flujos de efectivo real vs proyectado
- `POST /tesoreria/proyeccion` - Registrar proyección manual
- `GET /tesoreria/resumen-cartera` - Resumen de cartera

### CNBV

- `GET /cnbv` - Vista principal
- `GET /cnbv/reportes` - Listar reportes generados
- `POST /cnbv/generar-situacion-financiera` - Generar reporte de situación financiera
- `POST /cnbv/generar-cartera` - Generar reporte de cartera crediticia

## Referencia Técnica

- **Guías CNBV**: https://www.gob.mx/cnbv/documentos/guias-de-apoyo-reportes-regulatorios-de-situacion-financiera
- **Estándar PSR**: https://www.php-fig.org/psr/
- **Documentación del Sistema**: Ver README.md principal

---

*Documento generado el 2025-12-07 para ID FINANCIERO v2.0*
