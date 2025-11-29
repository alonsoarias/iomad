# Local Platform Access - Documentacion Exhaustiva

## Resumen del Plugin

**Nombre:** `local_platform_access`
**Version:** 1.3.0 (Build: 20251129)
**Tipo:** Plugin local de generacion de datos
**Ubicacion:** `/local/platform_access/`

### Descripcion

Este plugin genera registros de acceso a la plataforma Moodle/IOMAD para usuarios. Permite crear datos historicos de logins, accesos a cursos, actividades, dashboard, logouts, completados de cursos, **logins fallidos** y **sesiones con duracion**.

---

## Sistema de Eventos de Moodle

### Arquitectura de Eventos

Los eventos de Moodle se basan en la clase abstracta `\core\event\base` ubicada en `/lib/classes/event/base.php`. Cada evento hereda de esta clase e implementa metodos especificos.

### Propiedades Clave de Eventos

| Propiedad | Tipo | Descripcion |
|-----------|------|-------------|
| `eventname` | string | Nombre completo del evento (ej: `\core\event\user_loggedin`) |
| `component` | string | Componente que dispara el evento |
| `action` | string | Accion realizada (ej: `loggedin`, `viewed`) |
| `target` | string | Objetivo de la accion |
| `objecttable` | string | Tabla donde se almacena el objeto |
| `objectid` | int | ID del objeto relacionado |
| `crud` | string | Tipo de operacion: c=create, r=read, u=update, d=delete |
| `edulevel` | int | Nivel educativo: 0=other, 1=teaching, 2=participating |
| `userid` | int | ID del usuario que realiza la accion |
| `timecreated` | int | Timestamp del evento |

### Eventos Generados por el Plugin

| Evento | Clase | Descripcion | Cuando se Dispara |
|--------|-------|-------------|-------------------|
| Login | `\core\event\user_loggedin` | Usuario inicia sesion | Al hacer login |
| Logout | `\core\event\user_loggedout` | Usuario cierra sesion | Al hacer logout |
| Login Fallido | `\core\event\user_login_failed` | Intento de login fallido | Al fallar autenticacion |
| Dashboard | `\core\event\dashboard_viewed` | Usuario ve su dashboard | Al acceder a area personal |
| Curso Visto | `\core\event\course_viewed` | Usuario accede a un curso | Al entrar a un curso |
| Actividad Vista | `\mod_*\event\course_module_viewed` | Usuario ve una actividad | Al acceder a cualquier actividad |
| Curso Completado | `\core\event\course_completed` | Usuario completa un curso | Al cumplir criterios de completado |

### Almacenamiento de Eventos

Los eventos se almacenan en la tabla `{logstore_standard_log}` con los siguientes campos relevantes:

```sql
CREATE TABLE logstore_standard_log (
    id BIGINT,
    eventname VARCHAR(255),   -- Nombre del evento
    component VARCHAR(100),   -- Componente
    action VARCHAR(100),      -- Accion
    target VARCHAR(100),      -- Objetivo
    objecttable VARCHAR(50),  -- Tabla del objeto
    objectid BIGINT,          -- ID del objeto
    crud CHAR(1),             -- Tipo operacion
    edulevel TINYINT,         -- Nivel educativo
    contextid BIGINT,         -- ID del contexto
    contextlevel BIGINT,      -- Nivel del contexto
    contextinstanceid BIGINT, -- ID instancia del contexto
    userid BIGINT,            -- Usuario que realiza accion
    courseid BIGINT,          -- Curso relacionado
    relateduserid BIGINT,     -- Usuario relacionado
    anonymous TINYINT,        -- Es anonimo
    other TEXT,               -- Datos adicionales serializados
    timecreated BIGINT,       -- Timestamp
    origin VARCHAR(10),       -- Origen: web, cli, ws
    ip VARCHAR(45),           -- Direccion IP
    realuserid BIGINT         -- Usuario real (si hay suplantacion)
);
```

---

## Estructura del Plugin

```
local/platform_access/
+-- classes/
|   +-- generator.php           # Clase principal de generacion
|   +-- form/
|   |   +-- generate_form.php   # Formulario de configuracion
|   +-- privacy/
|       +-- provider.php        # Proveedor de privacidad GDPR
+-- db/
|   +-- access.php              # Capacidades del plugin
+-- lang/
|   +-- en/
|   |   +-- local_platform_access.php  # Strings ingles
|   +-- es/
|       +-- local_platform_access.php  # Strings espanol
+-- generate.php                # Pagina de generacion
+-- index.php                   # Pagina principal
+-- settings.php                # Configuracion del plugin
+-- styles.css                  # Estilos CSS
+-- version.php                 # Informacion de version
```

---

## Funcionalidades Implementadas

### 1. Generacion de Logins
- Crea eventos `user_loggedin` en logstore
- Actualiza campos de usuario: `firstaccess`, `lastaccess`, `lastlogin`, `currentlogin`, `lastip`
- Actualiza tabla `local_report_user_logins` si existe

### 2. Generacion de Acceso a Cursos
- Crea eventos `course_viewed`
- Solo para cursos donde el usuario esta inscrito
- Actualiza tabla `user_lastaccess`

### 3. Generacion de Acceso a Actividades
- Crea eventos `course_module_viewed` usando clases nativas de cada modulo
- Fallback para modulos sin clase de evento propia
- Actualiza tablas `course_modules_completion` y `course_modules_viewed`

### 4. Generacion de Dashboard Access
- Crea eventos `dashboard_viewed`
- 70-80% de probabilidad despues de cada login

### 5. Generacion de Logouts
- Crea eventos `user_loggedout`
- Permite calcular duracion de sesiones

### 6. Generacion de Completados de Curso
- Crea registros en `course_completions`
- Crea eventos `course_completed`
- Porcentaje configurable de completados

### 7. **NUEVO: Duracion de Sesion (ALTA PRIORIDAD)**
- Genera sesiones completas login-logout con duracion realista
- Duracion configurable (min/max en minutos)
- Tracking de estadisticas de sesion:
  - Total de sesiones con duracion
  - Duracion promedio
  - Total de minutos de sesion
  - Estadisticas min/max/mediana

### 8. **NUEVO: Logins Fallidos (PRIORIDAD MEDIA)**
- Genera eventos `user_login_failed` para monitoreo de seguridad
- Razones de fallo configurables:
  - 1: Usuario no existe
  - 2: Usuario suspendido
  - 3: Contrasena incorrecta (80% de casos)
  - 4: Usuario bloqueado
  - 5: Usuario no autorizado

---

## Configuracion del Generador

### Opciones Basicas

| Opcion | Tipo | Default | Descripcion |
|--------|------|---------|-------------|
| `companyid` | int | 0 | ID de compania (0 = todas) |
| `datefrom` | timestamp | Nov 15 2025 | Fecha inicio de generacion |
| `dateto` | timestamp | now() | Fecha fin de generacion |
| `accesstype` | string | 'all' | Tipo: login, course, activity, all |
| `cleanbeforegenerate` | bool | true | TRUNCAR datos antes de generar |
| `randomize` | bool | true | Aleatorizar timestamps |

### Opciones de Usuario

| Opcion | Tipo | Default | Descripcion |
|--------|------|---------|-------------|
| `onlyactive` | bool | true | Solo usuarios activos |
| `includeadmins` | bool | false | Incluir administradores |
| `updateusercreated` | bool | true | Actualizar fecha creacion |
| `usercreateddate` | timestamp | Nov 15 2025 | Nueva fecha de creacion |

### Rangos de Acceso

| Opcion | Min | Max | Descripcion |
|--------|-----|-----|-------------|
| `loginsmin/max` | 1 | 5 | Logins por usuario |
| `courseaccessmin/max` | 1 | 3 | Accesos a curso por usuario |
| `activityaccessmin/max` | 1 | 2 | Accesos a actividad por curso |

### Eventos Avanzados

| Opcion | Default | Descripcion |
|--------|---------|-------------|
| `generatedashboard` | true | Generar acceso a dashboard |
| `generatelogouts` | false | Generar eventos de logout |
| `generatecompletions` | false | Generar completados |
| `completionpercentmin/max` | 50-100 | % de completados |

### **NUEVA: Duracion de Sesion**

| Opcion | Default | Descripcion |
|--------|---------|-------------|
| `calculatesessionduration` | true | Habilitar tracking de duracion |
| `sessiondurationmin` | 10 | Duracion minima (minutos) |
| `sessiondurationmax` | 120 | Duracion maxima (minutos) |

### **NUEVA: Seguridad**

| Opcion | Default | Descripcion |
|--------|---------|-------------|
| `generatefailedlogins` | true | Generar logins fallidos |
| `failedloginsmin` | 0 | Minimo por usuario |
| `failedloginsmax` | 3 | Maximo por usuario |

---

## Estadisticas Generadas

El metodo `run()` retorna un array con las siguientes estadisticas:

```php
$stats = [
    'users_processed' => 150,          // Usuarios procesados
    'users_updated' => 145,            // Fechas de creacion actualizadas
    'users_without_enrollments' => 5,  // Sin matriculas
    'logins_generated' => 450,         // Eventos de login
    'course_access_generated' => 800,  // Accesos a cursos
    'activity_access_generated' => 1200, // Accesos a actividades
    'dashboard_access_generated' => 350, // Accesos a dashboard
    'logouts_generated' => 400,        // Eventos de logout
    'completions_generated' => 120,    // Completados de curso
    'lastaccess_updated' => 145,       // Lastaccess actualizados
    'records_deleted' => 5000,         // Registros eliminados
    'failed_logins_generated' => 75,   // NUEVO: Logins fallidos
    'sessions_with_duration' => 400,   // NUEVO: Sesiones con duracion
    'total_session_minutes' => 24000,  // NUEVO: Total minutos sesion
    'avg_session_minutes' => 60.0,     // NUEVO: Promedio duracion
    'time_elapsed' => 45,              // Segundos de ejecucion
];
```

---

## Limpieza de Datos (TRUNCAR)

Cuando `cleanbeforegenerate = true`, el plugin elimina:

1. Eventos de logstore_standard_log:
   - `user_loggedin`
   - `user_loggedout`
   - `user_login_failed` (NUEVO)
   - `course_viewed`
   - `course_module_viewed` (todos los modulos)
   - `dashboard_viewed`
   - `course_completed`

2. Tablas de tracking:
   - `user_lastaccess`
   - `local_report_user_logins`
   - `course_modules_completion`
   - `course_completions`
   - `course_modules_viewed`

3. Campos de usuario:
   - `firstaccess = 0`
   - `lastaccess = 0`
   - `lastlogin = 0`
   - `currentlogin = 0`
   - `lastip = ''`

---

## Uso Programatico

```php
// Crear instancia del generador.
$generator = new \local_platform_access\generator([
    'companyid' => 1,
    'datefrom' => strtotime('2025-11-15'),
    'dateto' => time(),
    'loginsmin' => 2,
    'loginsmax' => 10,
    'cleanbeforegenerate' => true,
    'generatedashboard' => true,
    'generatelogouts' => true,
    'generatecompletions' => true,
    // Nuevas opciones de sesion.
    'calculatesessionduration' => true,
    'sessiondurationmin' => 15,
    'sessiondurationmax' => 90,
    // Nuevas opciones de seguridad.
    'generatefailedlogins' => true,
    'failedloginsmin' => 0,
    'failedloginsmax' => 2,
]);

// Ejecutar generacion.
$stats = $generator->run(function($user, $stats) {
    echo "Procesando: {$user->username}\n";
});

// Obtener estadisticas de sesion.
$sessionStats = $generator->get_session_duration_stats();
// Retorna: ['min' => 15, 'max' => 88, 'avg' => 45.3, 'median' => 42, 'total_sessions' => 400]
```

---

## Capacidades

| Capacidad | Descripcion | Rol Default |
|-----------|-------------|-------------|
| `local/platform_access:generate` | Generar registros de acceso | manager |
| `local/platform_access:view` | Ver el generador | manager |

---

## Compatibilidad

### IOMAD
- Detecta automaticamente tablas `company_users` y `company_course`
- Filtra usuarios y cursos por compania
- Solo genera accesos a cursos donde el usuario esta inscrito Y pertenecen a la compania

### Moodle Estandar
- Funciona sin las tablas de IOMAD
- Procesa todos los usuarios activos
- Genera accesos a todos los cursos donde el usuario esta inscrito

---

## Consideraciones de Rendimiento

1. **Procesamiento por lotes**: Procesa un usuario a la vez para evitar memory overflow
2. **Pre-fetch de datos**: Carga cursos y modulos al inicio
3. **Transacciones**: Inserta registros individualmente (sin batch insert)
4. **Tiempo estimado**: ~0.3s por usuario con configuracion default

### Recomendaciones

- Para > 1000 usuarios, ejecutar en CLI o aumentar `max_execution_time`
- Usar `cleanbeforegenerate = true` para evitar duplicados
- Ajustar rangos de acceso segun tamano de la BD

---

## Generacion Incremental

El plugin soporta generacion incremental:

1. **Fecha inicio**: Especificar `datefrom` (ej: 15 noviembre)
2. **Fecha fin**: Especificar `dateto` hasta el dia actual
3. **Incremento gradual**: Los timestamps se distribuyen uniformemente
4. **Tendencia realista**: Valores dentro de rangos configurables

---

## Mejoras Implementadas (v1.3.0)

### ALTA PRIORIDAD: Duracion de Sesion
- Tracking completo de duracion de sesion
- Generacion de pares login-logout realistas
- Estadisticas de sesion (min, max, avg, mediana)
- Metodo `get_session_duration_stats()`

### PRIORIDAD MEDIA: Logins Fallidos
- Eventos `user_login_failed` para seguridad
- 5 tipos de razones de fallo
- Distribucion realista (80% password incorrecto)
- Limpieza automatica en truncar

---

## Changelog

### v1.3.0 (2025-11-29)
- Agregado: Tracking de duracion de sesion por usuario
- Agregado: Generacion de logins fallidos (seguridad)
- Agregado: Metodo `get_session_duration_stats()`
- Agregado: Metodo `generate_session_with_duration()`
- Agregado: Metodo `generate_failed_login_record()`
- Agregado: Limpieza de eventos `user_login_failed`
- Mejorado: Formulario con secciones expandibles
- Mejorado: Strings en ingles y espanol

### v1.2.0 (2025-11-28)
- Agregado: Generacion de eventos de dashboard, logout y completions
- Agregado: Opcion para truncar tablas antes de generar
- Mejorado: Solo genera accesos a cursos donde usuario esta inscrito

### v1.0.0 (2025-11-27)
- Version inicial con generacion de logins, cursos y actividades
