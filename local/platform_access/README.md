# Local Platform Access - Analisis Exhaustivo v2.0

## Informacion General

| Atributo | Valor |
|----------|-------|
| **Componente** | local_platform_access |
| **Version** | 1.2.0 (Build: 20251129) |
| **Requiere Moodle** | 2024100700+ |
| **Tipo** | Plugin local |
| **Proposito** | Generador de eventos simulados para testing |

---

## 1. QUE HACE EL PLUGIN ACTUALMENTE

### Proposito Principal
Este plugin es un **generador de datos de prueba** que simula eventos de acceso a la plataforma. Su funcion principal es insertar registros directamente en la tabla `logstore_standard_log` para simular actividad de usuarios cuando no hay datos reales disponibles.

### Funcionalidades Implementadas (Codigo Actual)

Segun analisis de `classes/generator.php`:

1. **Generacion de eventos de login** (`\core\event\user_loggedin`)
   - Simula inicios de sesion de usuarios (lineas 95-150)
   - Distribuye logins a lo largo de un periodo de dias configurable
   - Solo genera para usuarios matriculados en cursos de su empresa

2. **Generacion de eventos de dashboard** (`\core\event\dashboard_viewed`)
   - Simula visualizaciones del dashboard personal (lineas 152-170)
   - Se genera despues de cada login simulado

3. **Generacion de eventos de cursos** (`\core\event\course_viewed`)
   - Simula acceso a cursos especificos (lineas 180-230)
   - Respeta la matriculacion via `company_course`

4. **Generacion de eventos de actividades**
   - Simula visualizacion de modulos de curso (lineas 240-290)
   - Usa `action = 'viewed'` y `target = 'course_module'`
   - Componente dinamico segun tipo de modulo

5. **Generacion de eventos de logout** (`\core\event\user_loggedout`)
   - Simula cierres de sesion (lineas 295-330)
   - Probabilidad del 50% despues de actividad

### Archivos del Plugin

```
local/platform_access/
|-- index.php              # Pagina principal con formulario
|-- generate.php           # Endpoint de procesamiento
|-- version.php            # Metadatos v1.2.0
|-- settings.php           # Configuracion admin
|-- classes/
|   |-- generator.php      # Clase principal (895 lineas)
|   |-- form/
|   |   +-- generate_form.php  # Formulario Moodleform
|   +-- privacy/
|       +-- provider.php   # Null provider (GDPR)
|-- db/
|   +-- access.php         # Capacidades: generate, view
+-- lang/
    |-- en/local_platform_access.php (52 strings)
    +-- es/local_platform_access.php (52 strings)
```

---

## 2. EVENTOS QUE GENERA (NO CAPTURA)

### IMPORTANTE: Este plugin NO es un Observer

Este plugin **NO captura eventos** de Moodle. No existe `db/events.php` ni clases observer.

En su lugar, **GENERA** registros insertandolos directamente en `logstore_standard_log`.

### Eventos Generados Actualmente

| Evento | eventname | component | action | target |
|--------|-----------|-----------|--------|--------|
| Login | `\core\event\user_loggedin` | core | loggedin | user |
| Dashboard | `\core\event\dashboard_viewed` | core | viewed | dashboard |
| Curso | `\core\event\course_viewed` | core | viewed | course |
| Actividad | Varia segun modulo | mod_* | viewed | course_module |
| Logout | `\core\event\user_loggedout` | core | loggedout | user |

### Analisis del Metodo `generate()` (generator.php)

```php
// Linea 72-85: Distribucion de logins por dia
$loginsPerDay = ceil($numLogins / $days);
for ($day = 0; $day < $days; $day++) {
    $dayTimestamp = $startTime + ($day * 86400);

    // Hora aleatoria entre 6:00 y 23:00
    $hour = rand(6, 23);
    $minute = rand(0, 59);
    $loginTime = strtotime(date('Y-m-d', $dayTimestamp) . " $hour:$minute:00");
}
```

```php
// Linea 95-130: Insercion de login
$loginRecord = new \stdClass();
$loginRecord->eventname = '\\core\\event\\user_loggedin';
$loginRecord->component = 'core';
$loginRecord->action = 'loggedin';
$loginRecord->target = 'user';
$loginRecord->objecttable = 'user';
$loginRecord->objectid = $userid;
$loginRecord->crud = 'r';
$loginRecord->edulevel = 0;  // LEVEL_OTHER
$loginRecord->contextid = 1;  // Context sistema
$loginRecord->contextlevel = CONTEXT_SYSTEM; // 10
$loginRecord->contextinstanceid = 0;
$loginRecord->userid = $userid;
$loginRecord->timecreated = $loginTime;
$loginRecord->origin = 'web';
$loginRecord->ip = $this->generate_random_ip();
$loginRecord->other = null;

$DB->insert_record('logstore_standard_log', $loginRecord);
```

### Filtrado IOMAD

El generador filtra usuarios por empresa usando:

```php
// Lineas 45-60 en get_company_users()
$sql = "SELECT DISTINCT cu.userid
        FROM {company_users} cu
        JOIN {user_enrolments} ue ON cu.userid = ue.userid
        JOIN {enrol} e ON ue.enrolid = e.id
        JOIN {company_course} cc ON e.courseid = cc.courseid
        WHERE cu.companyid = :companyid
        AND cc.companyid = :companyid2";
```

---

## 3. SISTEMA DE EVENTOS DE MOODLE

### Arquitectura de Eventos

Moodle utiliza un sistema de eventos basado en el patron Observer. Los eventos se definen en `lib/classes/event/`.

#### Clase Base: `\core\event\base` (lib/classes/event/base.php)

```php
abstract class base implements \IteratorAggregate {
    protected $data = array(
        'eventname' => null,    // Ej: \core\event\user_loggedin
        'component' => null,    // Ej: core, mod_forum
        'action' => null,       // Ej: loggedin, viewed, created
        'target' => null,       // Ej: user, course, course_module
        'objecttable' => null,  // Tabla del objeto
        'objectid' => null,     // ID del objeto
        'crud' => null,         // c=create, r=read, u=update, d=delete
        'edulevel' => null,     // 0=OTHER, 1=TEACHING, 2=PARTICIPATING
        'contextid' => null,
        'contextlevel' => null, // 10=SYSTEM, 40=CATEGORY, 50=COURSE, 70=MODULE
        'contextinstanceid' => null,
        'userid' => null,
        'courseid' => null,
        'relateduserid' => null,
        'anonymous' => 0,
        'other' => null,        // Datos adicionales serializados
        'timecreated' => null,
        'origin' => null,       // web, cli, cron, ws
        'ip' => null,
        'realuserid' => null    // Para login-as
    );
}
```

### Tabla logstore_standard_log

Estructura XML (`admin/tool/log/store/standard/db/install.xml`):

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| id | INT(10) | PK autoincrement |
| eventname | VARCHAR(255) | Nombre completo del evento |
| component | VARCHAR(100) | Componente origen |
| action | VARCHAR(100) | Accion realizada |
| target | VARCHAR(100) | Objeto afectado |
| objecttable | VARCHAR(50) | Tabla del objeto |
| objectid | INT(10) | ID del objeto |
| crud | CHAR(1) | c/r/u/d |
| edulevel | TINYINT(1) | Nivel educativo |
| contextid | INT(10) | FK -> context.id |
| contextlevel | INT(10) | Nivel de contexto |
| contextinstanceid | INT(10) | ID de instancia |
| userid | INT(10) | FK -> user.id |
| courseid | INT(10) | FK -> course.id |
| relateduserid | INT(10) | Usuario relacionado |
| anonymous | TINYINT(1) | Es anonimo |
| other | TEXT | JSON con datos extra |
| timecreated | INT(10) | Timestamp Unix |
| origin | VARCHAR(10) | web/cli/cron/ws |
| ip | VARCHAR(45) | IP del usuario |
| realuserid | INT(10) | Usuario real (login-as) |

### Indices Existentes

```sql
INDEX timecreated (timecreated)
INDEX course-time (courseid, anonymous, timecreated)
INDEX user-module (userid, contextlevel, contextinstanceid, crud, edulevel, timecreated)
```

### Eventos del Core (lib/classes/event/)

| Archivo | Clase | Descripcion |
|---------|-------|-------------|
| user_loggedin.php | user_loggedin | Login exitoso |
| user_loggedout.php | user_loggedout | Logout |
| user_login_failed.php | user_login_failed | Login fallido |
| dashboard_viewed.php | dashboard_viewed | Vista de dashboard |
| course_viewed.php | course_viewed | Vista de curso |
| course_completed.php | course_completed | Curso completado |
| course_module_viewed.php | course_module_viewed (abstract) | Vista de modulo |
| user_created.php | user_created | Usuario creado |
| user_updated.php | user_updated | Usuario modificado |
| user_graded.php | user_graded | Usuario calificado |
| user_enrolment_created.php | user_enrolment_created | Matricula creada |

### Eventos de Modulos (ejemplo mod_forum)

```php
// mod/forum/classes/event/course_module_viewed.php
namespace mod_forum\event;

class course_module_viewed extends \core\event\course_module_viewed {
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'forum';
    }
}
```

---

## 4. MEJORAS PROPUESTAS

### 4.1 CRITICO: Implementar Observer Real de Eventos

**Problema**: El plugin solo genera datos simulados, no observa eventos reales.

**Solucion**: Crear sistema de observers para captura real de eventos.

#### Paso 1: Crear db/events.php

```php
<?php
defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => '\core\event\user_loggedin',
        'callback' => '\local_platform_access\observer::user_loggedin',
        'internal' => true,
        'priority' => 0,
    ],
    [
        'eventname' => '\core\event\user_loggedout',
        'callback' => '\local_platform_access\observer::user_loggedout',
    ],
    [
        'eventname' => '\core\event\dashboard_viewed',
        'callback' => '\local_platform_access\observer::dashboard_viewed',
    ],
    [
        'eventname' => '\core\event\course_viewed',
        'callback' => '\local_platform_access\observer::course_viewed',
    ],
    [
        'eventname' => '\core\event\course_completed',
        'callback' => '\local_platform_access\observer::course_completed',
    ],
    [
        'eventname' => '*',
        'callback' => '\local_platform_access\observer::all_events',
        'includefile' => '/local/platform_access/classes/observer.php',
    ],
];
```

#### Paso 2: Crear classes/observer.php

```php
<?php
namespace local_platform_access;

defined('MOODLE_INTERNAL') || die();

class observer {

    public static function user_loggedin(\core\event\user_loggedin $event) {
        global $DB;

        $companyid = self::get_user_company($event->userid);
        if (!$companyid) {
            return;
        }

        $record = new \stdClass();
        $record->userid = $event->userid;
        $record->companyid = $companyid;
        $record->eventtype = 'login';
        $record->sessionid = session_id();
        $record->ip = $event->other['ip'] ?? getremoteaddr();
        $record->timecreated = $event->timecreated;

        $DB->insert_record('local_platform_access_log', $record);
    }

    public static function course_viewed(\core\event\course_viewed $event) {
        global $DB;

        $companyid = self::get_user_company($event->userid);
        if (!$companyid) {
            return;
        }

        $record = new \stdClass();
        $record->userid = $event->userid;
        $record->companyid = $companyid;
        $record->courseid = $event->courseid;
        $record->eventtype = 'course_view';
        $record->timecreated = $event->timecreated;

        $DB->insert_record('local_platform_access_log', $record);
    }

    protected static function get_user_company($userid) {
        global $DB;
        return $DB->get_field('company_users', 'companyid',
            ['userid' => $userid], IGNORE_MULTIPLE);
    }
}
```

### 4.2 Crear Tabla Propia de Accesos

#### db/install.xml

```xml
<?xml version="1.0" encoding="UTF-8" ?>
<XMLDB PATH="local/platform_access/db" VERSION="20251129">
  <TABLES>
    <TABLE NAME="local_platform_access_log" COMMENT="Platform access tracking">
      <FIELDS>
        <FIELD NAME="id" TYPE="int" LENGTH="10" NOTNULL="true" SEQUENCE="true"/>
        <FIELD NAME="userid" TYPE="int" LENGTH="10" NOTNULL="true"/>
        <FIELD NAME="companyid" TYPE="int" LENGTH="10" NOTNULL="true"/>
        <FIELD NAME="eventtype" TYPE="char" LENGTH="50" NOTNULL="true"/>
        <FIELD NAME="courseid" TYPE="int" LENGTH="10"/>
        <FIELD NAME="cmid" TYPE="int" LENGTH="10"/>
        <FIELD NAME="sessionid" TYPE="char" LENGTH="128"/>
        <FIELD NAME="ip" TYPE="char" LENGTH="45"/>
        <FIELD NAME="duration" TYPE="int" LENGTH="10"/>
        <FIELD NAME="timecreated" TYPE="int" LENGTH="10" NOTNULL="true"/>
      </FIELDS>
      <KEYS>
        <KEY NAME="primary" TYPE="primary" FIELDS="id"/>
        <KEY NAME="userid" TYPE="foreign" FIELDS="userid" REFTABLE="user" REFFIELDS="id"/>
        <KEY NAME="companyid" TYPE="foreign" FIELDS="companyid" REFTABLE="company" REFFIELDS="id"/>
      </KEYS>
      <INDEXES>
        <INDEX NAME="userid_time" UNIQUE="false" FIELDS="userid, timecreated"/>
        <INDEX NAME="companyid_time" UNIQUE="false" FIELDS="companyid, timecreated"/>
        <INDEX NAME="eventtype_time" UNIQUE="false" FIELDS="eventtype, timecreated"/>
      </INDEXES>
    </TABLE>
  </TABLES>
</XMLDB>
```

### 4.3 Eventos Adicionales a Capturar

| Evento | Clase | Prioridad | Justificacion |
|--------|-------|-----------|---------------|
| user_graded | `\core\event\user_graded` | Alta | Calificaciones |
| course_module_completion_updated | `\core\event\course_module_completion_updated` | Alta | Progreso |
| attempt_submitted | `\mod_quiz\event\attempt_submitted` | Alta | Examenes |
| assessable_submitted | `\mod_assign\event\assessable_submitted` | Alta | Tareas |
| discussion_viewed | `\mod_forum\event\discussion_viewed` | Media | Foros |
| post_created | `\mod_forum\event\post_created` | Media | Participacion |
| message_sent | `\core\event\message_sent` | Baja | Comunicacion |

### 4.4 Metricas de Engagement

#### Duracion de Sesion

```php
public function calculate_session_duration($userid, $logintime) {
    global $DB;

    $sql = "SELECT MIN(timecreated) as endtime
            FROM {logstore_standard_log}
            WHERE userid = :userid
            AND timecreated > :logintime
            AND eventname IN (:logout, :nextlogin)";

    $result = $DB->get_record_sql($sql, [
        'userid' => $userid,
        'logintime' => $logintime,
        'logout' => '\core\event\user_loggedout',
        'nextlogin' => '\core\event\user_loggedin'
    ]);

    if ($result && $result->endtime) {
        return min($result->endtime - $logintime, 7200); // Max 2 horas
    }
    return 1800; // Default 30 min
}
```

#### Tasa de Rebote

```php
public function calculate_bounce_rate($companyid, $datefrom, $dateto) {
    global $DB;

    $sql = "SELECT
                COUNT(DISTINCT CASE WHEN event_count = 1 THEN session_id END) as bounces,
                COUNT(DISTINCT session_id) as total
            FROM (
                SELECT userid,
                       CONCAT(userid, '_', DATE(FROM_UNIXTIME(timecreated))) as session_id,
                       COUNT(*) as event_count
                FROM {logstore_standard_log}
                WHERE timecreated BETWEEN :datefrom AND :dateto
                GROUP BY userid, DATE(FROM_UNIXTIME(timecreated))
            ) sessions";

    $result = $DB->get_record_sql($sql, [
        'datefrom' => $datefrom,
        'dateto' => $dateto
    ]);

    return $result->total > 0
        ? round(($result->bounces / $result->total) * 100, 2)
        : 0;
}
```

### 4.5 Optimizaciones de Rendimiento

#### Insercion Batch

```php
public function generate_batch($records, $batchSize = 500) {
    global $DB;

    $transaction = $DB->start_delegated_transaction();

    try {
        $batches = array_chunk($records, $batchSize);
        foreach ($batches as $batch) {
            $DB->insert_records('logstore_standard_log', $batch);
        }
        $transaction->allow_commit();
    } catch (\Exception $e) {
        $transaction->rollback($e);
        throw $e;
    }
}
```

---

## 5. INSTRUCCIONES DE IMPLEMENTACION

### Paso 1: Crear archivos de observer

1. Crear `db/events.php` con la definicion de observers
2. Crear `classes/observer.php` con los callbacks
3. Incrementar version en `version.php`:
   ```php
   $plugin->version = 2025112901;
   ```

### Paso 2: Crear tabla personalizada

1. Crear `db/install.xml` con la estructura
2. Crear `db/upgrade.php`:

```php
<?php
function xmldb_local_platform_access_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2025112901) {
        // Definir tabla
        $table = new xmldb_table('local_platform_access_log');

        // Agregar campos
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null,
            XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null,
            XMLDB_NOTNULL, null, null);
        $table->add_field('companyid', XMLDB_TYPE_INTEGER, '10', null,
            XMLDB_NOTNULL, null, null);
        $table->add_field('eventtype', XMLDB_TYPE_CHAR, '50', null,
            XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null,
            null, null, null);
        $table->add_field('cmid', XMLDB_TYPE_INTEGER, '10', null,
            null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null,
            XMLDB_NOTNULL, null, null);

        // Agregar keys e indices
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_index('userid_time', XMLDB_INDEX_NOTUNIQUE,
            ['userid', 'timecreated']);
        $table->add_index('companyid_time', XMLDB_INDEX_NOTUNIQUE,
            ['companyid', 'timecreated']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2025112901, 'local', 'platform_access');
    }

    return true;
}
```

### Paso 3: Actualizar y purgar

```bash
php admin/cli/upgrade.php
php admin/cli/purge_caches.php
```

---

## 6. RESUMEN EJECUTIVO

### Estado Actual
| Aspecto | Estado |
|---------|--------|
| Generacion de logins | Funcional |
| Generacion de dashboard views | Funcional |
| Generacion de course views | Funcional |
| Generacion de activity views | Funcional |
| Generacion de logouts | Funcional |
| Observacion de eventos reales | NO IMPLEMENTADO |
| Tabla propia de datos | NO EXISTE |
| Metricas de engagement | NO IMPLEMENTADO |

### Prioridades de Mejora

| # | Mejora | Prioridad | Complejidad | Archivos |
|---|--------|-----------|-------------|----------|
| 1 | Implementar observers | ALTA | Media | 2 nuevos |
| 2 | Crear tabla propia | ALTA | Media | 3 archivos |
| 3 | Agregar metricas engagement | MEDIA | Alta | 2-3 archivos |
| 4 | Optimizar batch inserts | BAJA | Baja | 1 archivo |

### Conclusion

El plugin `local_platform_access` cumple su funcion como generador de datos de prueba para testing y demos. Sin embargo, para convertirlo en una herramienta de analytics real, necesita:

1. **Sistema de observers** para capturar eventos reales
2. **Tabla propia** para almacenar datos personalizados por empresa
3. **Metricas de engagement** como duracion de sesion y tasa de rebote

Estas mejoras transformarian el plugin de un simple generador de datos falsos a una verdadera herramienta de analytics de acceso a la plataforma.
