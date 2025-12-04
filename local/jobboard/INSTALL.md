# Guía de Instalación - Plugin Bolsa de Empleo

## Requisitos del Sistema

### Software Requerido
- **Moodle**: 4.1 LTS o superior
- **PHP**: 7.4 o superior (8.0+ recomendado para Moodle 4.3+)
- **Base de datos**: PostgreSQL 12+ o MySQL 8.0+

### Requisitos Opcionales
- **Iomad**: Para soporte multi-tenant (múltiples empresas/sedes)

## Instalación

### Método 1: Instalación Manual

1. **Descargar el plugin**
   ```bash
   cd /path/to/moodle/local
   git clone [repository-url] jobboard
   # O extraer el archivo ZIP descargado
   unzip jobboard.zip
   ```

2. **Verificar estructura de directorios**
   ```
   /path/to/moodle/local/jobboard/
   ├── classes/
   ├── db/
   ├── lang/
   ├── lib.php
   ├── version.php
   └── ...
   ```

3. **Permisos de archivos**
   ```bash
   chmod -R 755 /path/to/moodle/local/jobboard
   chown -R www-data:www-data /path/to/moodle/local/jobboard
   ```

4. **Ejecutar actualización de Moodle**
   - Acceder a Moodle como administrador
   - Ir a: Administración del sitio → Notificaciones
   - Seguir el proceso de instalación del plugin
   - Revisar y confirmar la instalación

### Método 2: Instalación vía CLI

```bash
cd /path/to/moodle
php admin/cli/upgrade.php
```

## Configuración Post-Instalación

### 1. Asignar Permisos

Por defecto, el plugin asigna capabilities a los siguientes roles:

| Rol | Capabilities |
|-----|-------------|
| Manager | Todas las capabilities de gestión |
| Teacher | Postularse a vacantes |
| User | Postularse a vacantes |

Para personalizar permisos:
1. Ir a: Administración del sitio → Usuarios → Permisos → Definir roles
2. Seleccionar el rol a modificar
3. Buscar capabilities que comienzan con `local/jobboard:`
4. Asignar según necesidades

### 2. Configuración General

Acceder a: Administración del sitio → Bolsa de Empleo → Configuración General

Configurar:
- **Nombre de la institución**: Para reportes y notificaciones
- **Email de contacto**: Para comunicaciones
- **Logo institucional**: Para reportes PDF

### 3. Configuración de Documentos

Acceder a: Administración del sitio → Bolsa de Empleo → Tipos de Documentos

- Revisar tipos de documentos predefinidos
- Habilitar/deshabilitar según necesidades
- Configurar días máximos de antigüedad

### 4. Plantillas de Correo

Acceder a: Administración del sitio → Bolsa de Empleo → Plantillas de Correo

- Personalizar textos de notificaciones
- Usar placeholders disponibles:
  - `{USER_FULLNAME}` - Nombre completo del usuario
  - `{VACANCY_CODE}` - Código de vacante
  - `{VACANCY_TITLE}` - Título de vacante
  - `{APPLICATION_DATE}` - Fecha de postulación
  - `{CURRENT_STATUS}` - Estado actual

### 5. Configuración Multi-tenant (Iomad)

Si usa Iomad:
1. Ir a: Administración del sitio → Bolsa de Empleo → Configuración Multi-tenant
2. Configurar:
   - Permitir postulación entre tenants
   - Visibilidad de revisores cross-tenant

## Verificación de Instalación

### Verificar Base de Datos

Ejecutar en consola de base de datos:

```sql
-- PostgreSQL
SELECT table_name FROM information_schema.tables
WHERE table_name LIKE 'mdl_local_jobboard%';

-- MySQL
SHOW TABLES LIKE 'mdl_local_jobboard%';
```

Deben existir las siguientes tablas:
- `local_jobboard_vacancy`
- `local_jobboard_vacancy_field`
- `local_jobboard_application`
- `local_jobboard_document`
- `local_jobboard_doc_validation`
- `local_jobboard_doc_requirement`
- `local_jobboard_workflow_log`
- `local_jobboard_audit`
- `local_jobboard_exemption`
- `local_jobboard_notification`
- `local_jobboard_config`
- `local_jobboard_api_token`
- `local_jobboard_doctype`
- `local_jobboard_email_template`

### Verificar Capabilities

```bash
cd /path/to/moodle
php admin/cli/check_database_schema.php
```

### Verificar Funcionalidad

1. Acceder como administrador
2. Ir a: Bolsa de Empleo → Panel de Control
3. Verificar que se muestran las opciones de navegación
4. Crear una vacante de prueba

## Tareas Programadas

El plugin registra las siguientes tareas:

| Tarea | Descripción | Frecuencia Sugerida |
|-------|-------------|---------------------|
| Enviar notificaciones | Procesa cola de notificaciones | Cada 5 minutos |
| Limpieza de datos | Elimina datos antiguos según política | Diaria |
| Actualizar métricas | Recalcula métricas del dashboard | Cada hora |

Configurar en: Administración del sitio → Servidor → Tareas programadas

## Solución de Problemas

### Error: Tabla no encontrada

```
Error: Table 'mdl_local_jobboard_vacancy' doesn't exist
```

**Solución**: Ejecutar actualización de base de datos:
```bash
php admin/cli/upgrade.php
```

### Error: Permiso denegado

```
Error: You do not have permission to perform this action
```

**Solución**: Verificar que el usuario tiene las capabilities necesarias asignadas.

### Error: Archivo no se puede subir

```
Error: File exceeds maximum allowed size
```

**Solución**: Verificar configuración de tamaño máximo en:
- PHP: `upload_max_filesize`, `post_max_size`
- Moodle: Límites de carga de archivos
- Plugin: Configuración de tamaño máximo

### Logs de Depuración

Habilitar depuración en Moodle:
1. Ir a: Administración del sitio → Desarrollo → Depuración
2. Establecer "Mensajes de depuración" en "DEVELOPER"
3. Revisar logs en `/path/to/moodledata/logs/`

## Actualización

### Proceso de Actualización

1. Hacer backup de la base de datos
2. Descargar nueva versión del plugin
3. Reemplazar archivos en `/local/jobboard`
4. Ejecutar actualización de Moodle

```bash
php admin/cli/upgrade.php
```

### Consideraciones

- Revisar CHANGELOG.md para cambios importantes
- Verificar compatibilidad con versión de Moodle
- Probar en entorno de desarrollo primero

## Desinstalación

### Advertencia

La desinstalación elimina TODOS los datos del plugin:
- Vacantes
- Postulaciones
- Documentos subidos
- Configuraciones

### Proceso

1. Ir a: Administración del sitio → Plugins → Visión general de plugins
2. Buscar "Bolsa de Empleo" (local_jobboard)
3. Hacer clic en "Desinstalar"
4. Confirmar eliminación

### Alternativa CLI

```bash
php admin/cli/uninstall_plugins.php --plugins=local_jobboard --run
```

## Soporte

Para obtener ayuda:

1. Revisar documentación incluida
2. Consultar FAQ en README.md
3. Reportar issues en el repositorio
