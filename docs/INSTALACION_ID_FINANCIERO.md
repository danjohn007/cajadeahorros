# Guía de Instalación - ID FINANCIERO v2.0

## Requisitos Previos

- Sistema base de Caja de Ahorros funcional
- MySQL 5.7 o superior
- PHP 7.4 o superior
- Acceso de administrador a la base de datos

## Pasos de Instalación

### 1. Respaldo de Base de Datos

**IMPORTANTE**: Antes de ejecutar cualquier cambio, realice un respaldo completo de la base de datos.

```bash
mysqldump -u root -p cajadeahorros > backup_cajadeahorros_$(date +%Y%m%d_%H%M%S).sql
```

### 2. Ejecutar Migración SQL

Ejecute el script de actualización:

```bash
mysql -u root -p cajadeahorros < database/update_id_financiero.sql
```

O desde MySQL:

```sql
SOURCE /ruta/completa/database/update_id_financiero.sql;
```

### 3. Verificar Tablas Creadas

Ejecute el siguiente comando para verificar que las tablas se crearon correctamente:

```sql
USE cajadeahorros;

-- Verificar tablas de arquitectura multiempresa
SHOW TABLES LIKE 'empresas_grupo';
SHOW TABLES LIKE 'unidades_negocio';
SHOW TABLES LIKE 'productos_financieros';
SHOW TABLES LIKE 'fuerza_ventas';
SHOW TABLES LIKE 'poblaciones';

-- Verificar tablas de políticas
SHOW TABLES LIKE 'politicas_credito';
SHOW TABLES LIKE 'checklists_credito';
SHOW TABLES LIKE 'checklist_items';
SHOW TABLES LIKE 'checklist_validaciones';

-- Verificar tablas de garantías
SHOW TABLES LIKE 'avales_obligados';
SHOW TABLES LIKE 'garantias';

-- Verificar tablas de tesorería
SHOW TABLES LIKE 'proyecciones_financieras';
SHOW TABLES LIKE 'flujos_efectivo';

-- Verificar tablas de cartera
SHOW TABLES LIKE 'traspasos_cartera';
SHOW TABLES LIKE 'convenios_pago';
SHOW TABLES LIKE 'liquidaciones_credito';

-- Verificar tablas CNBV
SHOW TABLES LIKE 'reportes_cnbv';
SHOW TABLES LIKE 'reportes_cnbv_detalle';
```

### 4. Verificar Datos Iniciales

Verifique que se insertaron los datos de ejemplo:

```sql
-- Ver empresa por defecto
SELECT * FROM empresas_grupo;

-- Ver unidad de negocio
SELECT * FROM unidades_negocio;

-- Ver productos financieros
SELECT * FROM productos_financieros;

-- Ver políticas de crédito
SELECT * FROM politicas_credito;

-- Ver checklists
SELECT c.nombre, c.tipo_operacion, COUNT(ci.id) as items
FROM checklists_credito c
LEFT JOIN checklist_items ci ON c.id = ci.checklist_id
GROUP BY c.id;
```

### 5. Verificar Campos Agregados a Tabla Créditos

```sql
DESCRIBE creditos;
```

Debe ver los siguientes campos nuevos:
- empresa_id
- producto_financiero_id
- origen_procedencia
- tipo_origen
- promotor_id
- requiere_aval
- motivo_rechazo
- dias_mora
- tipo_cartera

### 6. Verificar Vistas Creadas

```sql
-- Ver vista de resumen de cartera
SELECT * FROM v_resumen_cartera LIMIT 5;

-- Ver vista de operaciones diarias
SELECT * FROM v_operaciones_diarias LIMIT 5;

-- Ver vista de proyecciones de tesorería
SELECT * FROM v_proyecciones_tesoreria LIMIT 5;
```

### 7. Verificar Permisos de Archivos

Crear directorio para reportes CNBV:

```bash
mkdir -p uploads/reportes_cnbv
chmod 755 uploads/reportes_cnbv
```

### 8. Probar Endpoints de API

Puede probar los endpoints con curl o desde el navegador (si tiene sesión iniciada):

```bash
# Ejemplo: Validar edad y plazo (debe estar autenticado)
curl -X POST http://localhost/cajadeahorros/politicas/validar-edad-plazo \
  -H "Content-Type: application/json" \
  -d '{"socio_id": 1, "plazo_meses": 24}'
```

## Solución de Problemas

### Error: "Table already exists"

Si el script se ejecutó parcialmente, puede continuar desde donde falló. El script usa `CREATE TABLE IF NOT EXISTS` y `INSERT IGNORE`, por lo que es seguro ejecutarlo múltiples veces.

### Error: "Foreign key constraint fails"

Verifique que:
1. Las tablas referenciadas existan
2. Los datos de referencia existan antes de insertar datos dependientes

### Error: "Access denied"

Asegúrese de que el usuario de MySQL tiene permisos:

```sql
GRANT ALL PRIVILEGES ON cajadeahorros.* TO 'usuario_app'@'localhost';
FLUSH PRIVILEGES;
```

### Error: "Unknown column in field list"

Si está usando una versión anterior de MySQL, algunos campos pueden no ser compatibles. Revise la compatibilidad con su versión de MySQL.

## Rollback (Restaurar Base de Datos)

Si necesita revertir los cambios:

```bash
# Restaurar desde el backup
mysql -u root -p cajadeahorros < backup_cajadeahorros_YYYYMMDD_HHMMSS.sql
```

## Verificación Final

Ejecute el siguiente script de verificación:

```sql
-- Contar tablas nuevas
SELECT 
    COUNT(*) as total_tablas_sistema,
    SUM(CASE WHEN TABLE_NAME LIKE 'empresas%' OR TABLE_NAME LIKE 'unidades%' 
        OR TABLE_NAME LIKE 'productos%' OR TABLE_NAME LIKE 'fuerza%' 
        OR TABLE_NAME LIKE 'politicas%' OR TABLE_NAME LIKE 'checklists%'
        OR TABLE_NAME LIKE 'avales%' OR TABLE_NAME LIKE 'garantias%'
        OR TABLE_NAME LIKE 'proyecciones%' OR TABLE_NAME LIKE 'flujos%'
        OR TABLE_NAME LIKE 'traspasos%' OR TABLE_NAME LIKE 'convenios%'
        OR TABLE_NAME LIKE 'liquidaciones%' OR TABLE_NAME LIKE 'reportes_cnbv%'
        OR TABLE_NAME LIKE 'poblaciones%' THEN 1 ELSE 0 END) as tablas_id_financiero
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'cajadeahorros';
```

Debería mostrar al menos 14 tablas nuevas del sistema ID FINANCIERO.

## Siguientes Pasos

1. Revisar la documentación completa en `docs/ID_FINANCIERO.md`
2. Configurar datos de su empresa en la tabla `empresas_grupo`
3. Crear unidades de negocio según su estructura organizacional
4. Definir productos financieros con sus políticas
5. Configurar checklists personalizados si es necesario
6. Capacitar al personal en las nuevas funcionalidades

## Soporte

Para soporte adicional:
- Revise la documentación en `docs/ID_FINANCIERO.md`
- Consulte el README principal del proyecto
- Revise los logs en caso de errores

---

*Versión 1.0 - Diciembre 2025*
