# Local Platform Access - Análisis y Documentación

## Resumen del Plugin

**Nombre:** `local_platform_access`
**Versión:** 1.6.0 (Build: 20241128)
**Tipo:** Plugin local de generación de datos de prueba
**Ubicación:** `/local/platform_access/`

### Descripción

Este plugin es un **generador de datos de acceso** para propósitos de prueba y demostración. **NO es un observador de eventos** - en su lugar, genera registros sintéticos directamente en las tablas de Moodle para simular actividad de usuarios.

### Funcionalidad Actual

El plugin permite a administradores generar registros de:
- **Logins de usuarios** (`\core\event\user_loggedin`)
- **Accesos a cursos** (`\core\event\course_viewed`)
- **Accesos a actividades** (`\mod_*\event\course_module_viewed`)

---

## Arquitectura del Sistema de Eventos de Moodle

### Clase Base de Eventos
Todos los eventos de Moodle heredan de `\core\event\base` (`lib/classes/event/base.php`).

**Propiedades principales:**
- `eventname`: Nombre completo del evento (ej: `\core\event\user_loggedin`)
- `component`: Componente que genera el evento (ej: `core`, `mod_quiz`)
- `action`: Acción realizada (ej: `loggedin`, `viewed`)
- `target`: Objeto afectado (ej: `user`, `course_module`)
- `crud`: Tipo de operación (`c`=create, `r`=read, `u`=update, `d`=delete)
- `edulevel`: Nivel educativo (0=OTHER, 1=TEACHING, 2=PARTICIPATING)
- `userid`: ID del usuario que realizó la acción
- `courseid`: ID del curso relacionado
- `contextid`: ID del contexto
- `timecreated`: Timestamp del evento

### Tabla de Almacenamiento
Los eventos se almacenan en `logstore_standard_log` con la siguiente estructura:

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT | ID único |
| eventname | VARCHAR(255) | Nombre completo del evento |
| component | VARCHAR(100) | Componente origen |
| action | VARCHAR(100) | Acción realizada |
| target | VARCHAR(100) | Objeto afectado |
| objecttable | VARCHAR(50) | Tabla del objeto |
| objectid | INT | ID del objeto |
| crud | CHAR(1) | Tipo de operación |
| edulevel | TINYINT | Nivel educativo |
| contextid | INT | ID del contexto |
| contextlevel | INT | Nivel de contexto |
| contextinstanceid | INT | ID de instancia del contexto |
| userid | INT | ID del usuario |
| courseid | INT | ID del curso |
| relateduserid | INT | ID de usuario relacionado |
| anonymous | TINYINT | Si el evento es anónimo |
| other | TEXT | Datos adicionales serializados |
| timecreated | INT | Timestamp de creación |
| origin | VARCHAR(10) | Origen (web, cli, cron, ws) |
| ip | VARCHAR(45) | Dirección IP |
| realuserid | INT | ID real cuando login-as |

---

## Análisis del Código Actual

### Estructura de Archivos

```
local/platform_access/
├── classes/
│   ├── form/
│   │   └── generate_form.php     # Formulario de configuración
│   ├── generator.php             # Clase principal de generación
│   └── privacy/
│       └── provider.php          # Proveedor de privacidad
├── db/
│   └── access.php                # Capacidades del plugin
├── lang/
│   ├── en/
│   │   └── local_platform_access.php  # Strings en inglés
│   └── es/
│       └── local_platform_access.php  # Strings en español
├── generate.php                  # Página de procesamiento
├── index.php                     # Página principal
├── settings.php                  # Configuración del plugin
├── styles.css                    # Estilos CSS
└── version.php                   # Información de versión
```

### Clase `generator.php` - Análisis Detallado

#### Propiedades de Configuración
```php
protected $datefrom;              // Timestamp inicio
protected $dateto;                // Timestamp fin
protected $loginsmin;             // Logins mínimos por usuario
protected $loginsmax;             // Logins máximos por usuario
protected $courseaccessmin;       // Accesos mínimos a curso
protected $courseaccessmax;       // Accesos máximos a curso
protected $activityaccessmin;     // Accesos mínimos a actividad
protected $activityaccessmax;     // Accesos máximos a actividad
protected $randomize;             // Aleatorizar timestamps
protected $includeadmins;         // Incluir administradores
protected $onlyactive;            // Solo usuarios activos
protected $accesstype;            // Tipo: login/course/activity/all
protected $companyid;             // ID de compañía (IOMAD)
protected $updateusercreated;     // Actualizar fecha creación
protected $cleanbeforegenerate;   // Limpiar antes de generar
```

#### Métodos Principales

| Método | Línea | Descripción |
|--------|-------|-------------|
| `__construct()` | 99 | Inicializa opciones de generación |
| `get_users()` | 257 | Obtiene usuarios filtrados por compañía |
| `get_user_courses()` | 395 | Obtiene cursos donde el usuario está matriculado |
| `get_course_modules()` | 439 | Obtiene módulos de un curso |
| `generate_login_record()` | 496 | Genera registro de login |
| `generate_course_access_record()` | 627 | Genera registro de acceso a curso |
| `generate_activity_access_record()` | 675 | Genera registro de acceso a actividad |
| `clean_existing_records()` | 160 | Limpia registros existentes |
| `run()` | 837 | Ejecuta el proceso de generación |

### Eventos Generados Actualmente

| Evento | Clase | Descripción |
|--------|-------|-------------|
| Login | `\core\event\user_loggedin` | Usuario inicia sesión |
| Curso visto | `\core\event\course_viewed` | Usuario ve un curso |
| Actividad vista | `\mod_*\event\course_module_viewed` | Usuario ve una actividad |

### Tablas Afectadas

1. **`logstore_standard_log`** - Registros de eventos
2. **`user`** - Campos: firstaccess, lastaccess, lastlogin, currentlogin, lastip
3. **`user_lastaccess`** - Último acceso por usuario/curso
4. **`local_report_user_logins`** - Estadísticas de login (si existe)
5. **`course_modules_completion`** - Tracking de completado
6. **`course_modules_viewed`** - Tracking de visualización

---

## Eventos de Moodle NO Capturados/Generados

### Eventos de Navegación
| Evento | Clase | Uso |
|--------|-------|-----|
| Dashboard visto | `\core\event\dashboard_viewed` | Usuario accede a "Área personal" |
| Perfil visto | `\core\event\user_profile_viewed` | Usuario ve un perfil |
| Lista de cursos | `\core\event\course_category_viewed` | Usuario ve categoría |

### Eventos de Interacción
| Evento | Clase | Uso |
|--------|-------|-----|
| Usuario calificado | `\core\event\user_graded` | Calificación asignada |
| Curso completado | `\core\event\course_completed` | Completado de curso |
| Módulo completado | `\core\event\course_module_completion_updated` | Completado de actividad |
| Intento de quiz | `\mod_quiz\event\attempt_started` | Inicio de intento |
| Quiz enviado | `\mod_quiz\event\attempt_submitted` | Envío de intento |
| Foro visto | `\mod_forum\event\discussion_viewed` | Ver discusión |
| Post creado | `\mod_forum\event\post_created` | Crear mensaje |
| Tarea enviada | `\mod_assign\event\submission_created` | Enviar tarea |
| Archivo subido | `\core\event\assessable_uploaded` | Subir archivo |

### Eventos de Autenticación
| Evento | Clase | Uso |
|--------|-------|-----|
| Login fallido | `\core\event\user_login_failed` | Intento fallido |
| Logout | `\core\event\user_loggedout` | Cierre de sesión |
| Login como | `\core\event\user_loggedinas` | Admin como usuario |

---

## Mejoras Propuestas

### 1. Agregar Generación de Evento Dashboard Viewed

**Archivo:** `classes/generator.php`
**Ubicación:** Después de generar logins (línea ~880)

```php
/**
 * Generate dashboard access record.
 *
 * @param object $user User object
 * @param int $timestamp Timestamp for the access
 * @return bool Success
 */
public function generate_dashboard_access_record($user, int $timestamp): bool {
    global $DB;

    if (!$this->logstore_exists()) {
        return false;
    }

    $ip = $this->get_random_ip();

    try {
        $event = \core\event\dashboard_viewed::create([
            'userid' => $user->id,
            'context' => \context_user::instance($user->id),
        ]);

        $entry = $this->event_to_log_entry($event, $timestamp, $ip);
        $DB->insert_record('logstore_standard_log', (object)$entry);
        $this->stats['dashboard_access_generated']++;

        return true;
    } catch (\Exception $e) {
        debugging('Error generating dashboard access: ' . $e->getMessage(), DEBUG_DEVELOPER);
        return false;
    }
}
```

### 2. Agregar Generación de Logout

**Archivo:** `classes/generator.php`

```php
/**
 * Generate logout record.
 *
 * @param object $user User object
 * @param int $timestamp Timestamp (should be after last login)
 * @return bool Success
 */
public function generate_logout_record($user, int $timestamp): bool {
    global $DB;

    if (!$this->logstore_exists()) {
        return false;
    }

    $ip = $this->get_random_ip();

    try {
        $event = \core\event\user_loggedout::create([
            'userid' => $user->id,
            'objectid' => $user->id,
            'other' => ['sessionid' => sesskey()],
        ]);

        $entry = $this->event_to_log_entry($event, $timestamp, $ip);
        $DB->insert_record('logstore_standard_log', (object)$entry);
        $this->stats['logouts_generated']++;

        return true;
    } catch (\Exception $e) {
        return false;
    }
}
```

### 3. Agregar Generación de Course Completed

**Archivo:** `classes/generator.php`

```php
/**
 * Generate course completion record.
 *
 * @param object $user User object
 * @param object $course Course object
 * @param int $timestamp Timestamp
 * @return bool Success
 */
public function generate_course_completion($user, $course, int $timestamp): bool {
    global $DB;

    // Verificar que el curso tiene completion habilitado
    if (!$course->enablecompletion) {
        return false;
    }

    try {
        // Crear registro de completado
        $completion = new \stdClass();
        $completion->userid = $user->id;
        $completion->course = $course->id;
        $completion->timeenrolled = $timestamp - 86400; // Un día antes
        $completion->timestarted = $timestamp - 3600;   // Una hora antes
        $completion->timecompleted = $timestamp;

        $existing = $DB->get_record('course_completions', [
            'userid' => $user->id,
            'course' => $course->id,
        ]);

        if ($existing) {
            $completion->id = $existing->id;
            $DB->update_record('course_completions', $completion);
        } else {
            $completion->id = $DB->insert_record('course_completions', $completion);
        }

        // Disparar evento
        $event = \core\event\course_completed::create_from_completion($completion);
        $entry = $this->event_to_log_entry($event, $timestamp, $this->get_random_ip());
        $DB->insert_record('logstore_standard_log', (object)$entry);

        $this->stats['completions_generated']++;
        return true;
    } catch (\Exception $e) {
        return false;
    }
}
```

### 4. Agregar Opciones en el Formulario

**Archivo:** `classes/form/generate_form.php`
**Ubicación:** Después de línea 110

```php
// Header: Advanced events
$mform->addElement('header', 'advancedevents', get_string('advancedevents', 'local_platform_access'));

// Generate dashboard access
$mform->addElement('advcheckbox', 'generatedashboard',
    get_string('generatedashboard', 'local_platform_access'));
$mform->setDefault('generatedashboard', 1);

// Generate logouts
$mform->addElement('advcheckbox', 'generatelogouts',
    get_string('generatelogouts', 'local_platform_access'));
$mform->setDefault('generatelogouts', 0);

// Generate course completions
$mform->addElement('advcheckbox', 'generatecompletions',
    get_string('generatecompletions', 'local_platform_access'));
$mform->setDefault('generatecompletions', 0);
$mform->addHelpButton('generatecompletions', 'generatecompletions', 'local_platform_access');

// Completion percentage (min-max)
$mform->addElement('text', 'completionpercentmin',
    get_string('completionpercent', 'local_platform_access') . ' (min)', ['size' => 5]);
$mform->setType('completionpercentmin', PARAM_INT);
$mform->setDefault('completionpercentmin', 50);
$mform->disabledIf('completionpercentmin', 'generatecompletions', 'notchecked');

$mform->addElement('text', 'completionpercentmax',
    get_string('completionpercent', 'local_platform_access') . ' (max)', ['size' => 5]);
$mform->setType('completionpercentmax', PARAM_INT);
$mform->setDefault('completionpercentmax', 100);
$mform->disabledIf('completionpercentmax', 'generatecompletions', 'notchecked');
```

### 5. Strings de Idioma Adicionales

**Archivo:** `lang/en/local_platform_access.php`

```php
$string['advancedevents'] = 'Advanced Events';
$string['generatedashboard'] = 'Generate dashboard access records';
$string['generatelogouts'] = 'Generate logout records';
$string['generatecompletions'] = 'Generate course completion records';
$string['generatecompletions_help'] = 'If enabled, random course completions will be generated for users based on the percentage range specified.';
$string['completionpercent'] = 'Completion percentage';
$string['dashboardaccessgenerated'] = 'Dashboard access records generated';
$string['logoutsgenerated'] = 'Logout records generated';
$string['completionsgenerated'] = 'Course completions generated';
```

### 6. Mejora: Distribución Temporal Realista

**Problema:** Actualmente los timestamps son completamente aleatorios.

**Solución:** Crear sesiones realistas con secuencia temporal lógica.

```php
/**
 * Generate a realistic session for a user.
 *
 * @param object $user User object
 * @param int $sessionstart Session start timestamp
 * @return array Array of generated records
 */
public function generate_realistic_session($user, int $sessionstart): array {
    $records = [];
    $currenttime = $sessionstart;

    // 1. Login
    $this->generate_login_record($user, $currenttime);
    $currenttime += rand(5, 30); // 5-30 segundos para cargar

    // 2. Dashboard (70% probabilidad)
    if (rand(1, 100) <= 70) {
        $this->generate_dashboard_access_record($user, $currenttime);
        $currenttime += rand(30, 300); // 30 segundos a 5 minutos
    }

    // 3. Acceder a cursos
    $usercourses = $this->get_user_courses($user->id);
    $coursesToAccess = rand(1, min(3, count($usercourses)));
    $selectedCourses = array_rand($usercourses, min($coursesToAccess, count($usercourses)));

    if (!is_array($selectedCourses)) {
        $selectedCourses = [$selectedCourses];
    }

    foreach ($selectedCourses as $courseKey) {
        $course = $usercourses[$courseKey];
        $currenttime += rand(60, 600); // 1-10 minutos entre cursos

        $this->generate_course_access_record($user, $course, $currenttime);

        // Acceder a actividades del curso
        $modules = $this->get_course_modules($course->id);
        $activitiesToAccess = rand(1, min(5, count($modules)));

        for ($i = 0; $i < $activitiesToAccess && !empty($modules); $i++) {
            $cmKey = array_rand($modules);
            $cm = $modules[$cmKey];
            unset($modules[$cmKey]);

            $currenttime += rand(120, 1800); // 2-30 minutos por actividad
            $this->generate_activity_access_record($user, $course, $cm, $currenttime);
        }
    }

    // 4. Logout (50% probabilidad)
    if (rand(1, 100) <= 50) {
        $currenttime += rand(60, 300);
        $this->generate_logout_record($user, $currenttime);
    }

    return $records;
}
```

---

## Capacidades Definidas

| Capacidad | Riesgo | Tipo | Descripción |
|-----------|--------|------|-------------|
| `local/platform_access:generate` | CONFIG, DATALOSS | write | Generar registros |
| `local/platform_access:view` | PERSONAL | read | Ver generador |

---

## Diagrama de Flujo

```
┌─────────────────┐
│   index.php     │ ◄── Usuario accede
│  (Formulario)   │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  generate.php   │ ◄── Procesa formulario
│  (Ejecución)    │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   generator     │
│     class       │
└────────┬────────┘
         │
    ┌────┴────┐
    │         │
    ▼         ▼
┌───────┐ ┌───────┐
│ clean │ │ get   │
│records│ │ users │
└───┬───┘ └───┬───┘
    │         │
    ▼         ▼
┌───────────────────────┐
│   Por cada usuario:   │
│  ├─ generate_login    │
│  ├─ generate_course   │
│  └─ generate_activity │
└───────────┬───────────┘
            │
            ▼
┌───────────────────────┐
│ Actualizar tablas:    │
│  ├─ logstore_log      │
│  ├─ user              │
│  ├─ user_lastaccess   │
│  └─ course_modules_*  │
└───────────────────────┘
```

---

## Consideraciones de Rendimiento

1. **Incrementa límites:** El plugin usa `core_php_time_limit::raise(0)` y `raise_memory_limit(MEMORY_HUGE)`
2. **Procesamiento por lotes:** Se procesa usuario por usuario con callback de progreso
3. **Índices en logstore:** Asegúrate de que los índices de `logstore_standard_log` estén optimizados

---

## Notas de Seguridad

- El plugin requiere capacidad `local/platform_access:generate` que tiene riesgo `RISK_CONFIG | RISK_DATALOSS`
- Solo accesible para roles con capacidad de administrador
- Requiere sesskey para prevenir CSRF
- Los registros generados son idénticos a los reales (sin marca especial)

---

## Conclusión

El plugin `local_platform_access` cumple su función como generador de datos de prueba, pero podría mejorarse significativamente:

1. **Agregar más tipos de eventos** (dashboard, logout, completions)
2. **Mejorar realismo temporal** (sesiones secuenciales)
3. **Agregar opciones granulares** en el formulario
4. **Incluir métricas de calificaciones** generadas

Estas mejoras harían que los datos de prueba sean más representativos de uso real de la plataforma.
