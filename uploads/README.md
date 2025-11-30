# Uploads Directory
Este directorio contiene los archivos subidos al sistema.

## Subdirectorios

- **nomina/** - Archivos de nómina cargados (Excel, CSV)
- **documentos/** - Documentos de créditos y socios

## Permisos

Este directorio debe tener permisos de escritura apropiados:

```bash
# Opción recomendada (el usuario del servidor web debe ser propietario)
chown -R www-data:www-data uploads/
chmod 755 -R uploads/

# O usar el grupo del servidor web
chgrp -R www-data uploads/
chmod 775 -R uploads/
```

## Seguridad

Los archivos en este directorio están protegidos contra ejecución directa mediante .htaccess.
Solo se permite acceso a tipos de archivo específicos.
