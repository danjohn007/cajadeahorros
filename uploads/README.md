# Uploads Directory
Este directorio contiene los archivos subidos al sistema.

## Subdirectorios

- **nomina/** - Archivos de nómina cargados (Excel, CSV)
- **documentos/** - Documentos de créditos y socios

## Permisos

Este directorio debe tener permisos de escritura:

```bash
chmod 777 -R uploads/
```

## Seguridad

Los archivos en este directorio están protegidos contra ejecución directa mediante .htaccess
