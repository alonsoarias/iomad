# Report Platform Usage - Analisis Exhaustivo v2.0

## Informacion General

| Atributo | Valor |
|----------|-------|
| **Componente** | report_platform_usage |
| **Version** | 1.3.0 (Build: 20251129) |
| **Requiere Moodle** | 2024100700+ |
| **Tipo** | Plugin de reportes |
| **Proposito** | Dashboard de metricas de uso de plataforma |

---

## 1. QUE HACE EL PLUGIN ACTUALMENTE

### Proposito Principal
Este plugin proporciona un **dashboard de reportes en tiempo real** sobre el uso de la plataforma Moodle/IOMAD. Consulta datos de `logstore_standard_log` y otras tablas para generar metricas y visualizaciones de actividad de usuarios.

### Funcionalidades Implementadas (Codigo Actual)

Segun analisis de `classes/report.php` (1017 lineas):

1. **Resumen de Logins** (`get_login_summary()` - lineas 196-272)
   - Logins totales y usuarios unicos por periodo (hoy, 7 dias, 30 dias)
   - Query optimizada con una sola consulta SQL

2. **Logins Diarios** (`get_daily_logins()` - lineas 280-343)
   - Serie temporal de logins por dia
   - Datos para grafico Chart.js

3. **Actividad de Usuarios** (`get_user_activity_summary()` - lineas 350-385)
   - Total usuarios, activos, inactivos
   - Basado en campo `lastaccess` de tabla `user`

4. **Top Cursos** (`get_top_courses()` - lineas 393-436)
   - Cursos mas accedidos
   - Cuenta de accesos y usuarios unicos

5. **Tendencias de Acceso a Cursos** (`get_course_access_trends()` - lineas 444-504)
   - Serie temporal de accesos a cursos

6. **Top Actividades** (`get_top_activities()` - lineas 512-577)
   - Actividades mas vistas por tipo
   - Nombre legible del modulo

7. **Completados de Cursos** (`get_course_completions_summary()` - lineas 740-793)
   - Completados por periodo (hoy, 7 dias, 30 dias, total)
   - Consulta tabla `course_completions`

8. **Acceso a Dashboard** (`get_dashboard_access()` - lineas 800-852)
   - Usuarios que accedieron al dashboard por periodo
   - Evento `\core\event\dashboard_viewed`

9. **Tendencias de Completado** (`get_completion_trends()` - lineas 860-915)
   - Serie temporal de completados de curso

10. **Resumen de Logouts** (`get_logout_summary()` - lineas 922-974)
    - Logouts por periodo (hoy, 7 dias, 30 dias)
    - Evento `\core\event\user_loggedout`

### Archivos del Plugin

```
report/platform_usage/
|-- index.php              # Dashboard principal (HTML + JS)
|-- ajax.php               # Endpoint AJAX para datos dinamicos
|-- export.php             # Handler de exportacion
|-- version.php            # Metadatos v1.3.0
|-- settings.php           # Configuracion admin
|-- classes/
|   |-- report.php         # Clase principal de datos (1017 lineas)
|   |-- exporter.php       # Exportador CSV (267 lineas)
|   |-- excel_exporter.php # Exportador Excel con graficos (751 lineas)
|   +-- privacy/
|       +-- provider.php   # Null provider (GDPR)
|-- db/
|   |-- access.php         # Capacidades: view, export
|   +-- caches.php         # Definiciones de cache MUC
+-- lang/
    |-- en/report_platform_usage.php (72 strings)
    +-- es/report_platform_usage.php (64 strings)
```

---

## 2. EVENTOS QUE CONSULTA ACTUALMENTE

### Eventos de logstore_standard_log

| Evento | eventname | Metodo | Metricas |
|--------|-----------|--------|----------|
| Login | `\core\event\user_loggedin` | `get_login_summary()` | Logins totales, unicos |
| Logout | `\core\event\user_loggedout` | `get_logout_summary()` | Logouts por periodo |
| Dashboard | `\core\event\dashboard_viewed` | `get_dashboard_access()` | Usuarios dashboard |
| Curso Visto | `\core\event\course_viewed` | `get_top_courses()` | Top cursos |
| Actividad Vista | `action='viewed' + target='course_module'` | `get_top_activities()` | Top actividades |

### Tablas Consultadas

| Tabla | Datos Obtenidos | Metodo |
|-------|-----------------|--------|
| `logstore_standard_log` | Eventos de sistema | Multiples |
| `user` | lastaccess, datos de usuario | `get_user_activity_summary()` |
| `course_completions` | Completados de cursos | `get_course_completions_summary()` |
| `company_users` | Filtro por empresa IOMAD | `get_company_userids()` |
| `course` | Nombres de cursos | `get_top_courses()` |
| `course_modules` | Info de modulos | `get_top_activities()` |

### Queries SQL Principales

#### Login Summary (lineas 241-271)

```sql
SELECT
    SUM(CASE WHEN timecreated >= :today THEN 1 ELSE 0 END) as logins_today,
    COUNT(DISTINCT CASE WHEN timecreated >= :today2 THEN userid END) as unique_today,
    SUM(CASE WHEN timecreated >= :week THEN 1 ELSE 0 END) as logins_week,
    COUNT(DISTINCT CASE WHEN timecreated >= :week2 THEN userid END) as unique_week,
    SUM(CASE WHEN timecreated >= :month THEN 1 ELSE 0 END) as logins_month,
    COUNT(DISTINCT CASE WHEN timecreated >= :month2 THEN userid END) as unique_month
FROM {logstore_standard_log}
WHERE eventname = '\\core\\event\\user_loggedin'
  AND userid IN (SELECT userid FROM company_users WHERE companyid = :companyid)
```

#### Top Courses (lineas 415-435)

```sql
SELECT c.id, c.shortname, c.fullname,
       COUNT(*) as access_count,
       COUNT(DISTINCT l.userid) as unique_users
FROM {logstore_standard_log} l
JOIN {course} c ON l.courseid = c.id
WHERE l.eventname = '\\core\\event\\course_viewed'
  AND l.timecreated BETWEEN :datefrom AND :dateto
  AND l.courseid > 1
  AND l.userid IN (:userids)
GROUP BY c.id, c.shortname, c.fullname
ORDER BY access_count DESC
LIMIT 10
```

#### Top Activities (lineas 534-551)

```sql
SELECT l.contextinstanceid, l.component, l.courseid,
       COUNT(*) as access_count,
       COUNT(DISTINCT l.userid) as unique_users
FROM {logstore_standard_log} l
WHERE l.action = 'viewed'
  AND l.target = 'course_module'
  AND l.timecreated BETWEEN :datefrom AND :dateto
  AND l.userid IN (:userids)
GROUP BY l.contextinstanceid, l.component, l.courseid
ORDER BY access_count DESC
LIMIT 10
```

---

## 3. SISTEMA DE CACHE MUC

### Configuracion (db/caches.php)

```php
$definitions = [
    'reportdata' => [
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'simpledata' => false,
        'ttl' => 300,  // 5 minutos
        'staticacceleration' => true,
        'staticaccelerationsize' => 10,
    ],
    'companyusers' => [
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'simpledata' => true,
        'ttl' => 600,  // 10 minutos
        'staticacceleration' => true,
        'staticaccelerationsize' => 50,
    ],
];
```

### Uso del Cache (lineas 96-110)

```php
protected function get_cached_data(string $type, callable $callback) {
    if (!$this->usecache) {
        return $callback();
    }

    $key = $this->get_cache_key($type);
    $data = $this->cache->get($key);

    if ($data === false) {
        $data = $callback();
        $this->cache->set($key, $data);
    }

    return $data;
}
```

---

## 4. EXPORTADORES

### CSV Exporter (exporter.php)

Tipos de exportacion:
- `logins` - Resumen de logins
- `courses` - Acceso a cursos
- `users` - Detalles de usuarios
- `daily` - Datos diarios
- `summary` - Resumen completo

### Excel Exporter (excel_exporter.php)

Usa PHPSpreadsheet para generar Excel con graficos embebidos:

**Hojas generadas:**
1. **Summary** - Resumen ejecutivo con graficos de barras y pie
2. **Daily Logins** - Serie temporal con grafico de lineas
3. **Courses** - Detalle de acceso a cursos con grafico de barras
4. **Activities** - Top actividades
5. **Users** - Detalle de usuarios

**Graficos incluidos:**
- Login Summary (Bar Chart)
- User Activity (Pie Chart)
- Daily Logins (Line Chart)
- Top Courses (Horizontal Bar Chart)

---

## 5. METRICAS ACTUALES DEL DASHBOARD

### Tarjetas de Resumen

| Metrica | Periodos | Fuente |
|---------|----------|--------|
| Logins | Hoy, 7d, 30d | `logstore_standard_log` |
| Logouts | Hoy, 7d, 30d | `logstore_standard_log` |
| Dashboard Users | Hoy, 7d, 30d | `logstore_standard_log` |
| Completados | Hoy, 7d, 30d, Total | `course_completions` |
| Usuarios Activos | Total, Activos, Inactivos | `user.lastaccess` |

### Graficos

| Grafico | Tipo | Datos |
|---------|------|-------|
| Daily Logins | Line | Logins + Usuarios unicos por dia |
| User Activity | Doughnut | Activos vs Inactivos |
| Course Access Trends | Line | Accesos a cursos por dia |
| Completion Trends | Line | Completados por dia |
| Top Courses | Bar | Top 10 cursos mas accedidos |

### Tablas

| Tabla | Columnas |
|-------|----------|
| Course Usage | Curso, Accesos, Usuarios Unicos |
| Top Activities | Nombre, Tipo, Curso, Accesos, Usuarios |

---

## 6. EVENTOS NO CONSULTADOS (Oportunidades)

### Eventos de Alto Valor Disponibles

| Evento | Clase | Valor Potencial |
|--------|-------|-----------------|
| Login fallido | `\core\event\user_login_failed` | Seguridad |
| Usuario calificado | `\core\event\user_graded` | Evaluacion |
| Modulo completado | `\core\event\course_module_completion_updated` | Progreso granular |
| Quiz iniciado | `\mod_quiz\event\attempt_started` | Examenes |
| Quiz enviado | `\mod_quiz\event\attempt_submitted` | Completado examenes |
| Tarea enviada | `\mod_assign\event\submission_created` | Entregas |
| Post de foro | `\mod_forum\event\post_created` | Participacion |
| Mensaje enviado | `\core\event\message_sent` | Comunicacion |

### Tablas No Utilizadas

| Tabla | Informacion Disponible |
|-------|------------------------|
| `grade_grades` | Calificaciones detalladas |
| `quiz_attempts` | Intentos de quiz |
| `assign_submission` | Entregas de tareas |
| `forum_posts` | Posts en foros |
| `course_modules_completion` | Completado de actividades |

---

## 7. MEJORAS PROPUESTAS

### 7.1 ALTA PRIORIDAD: Metricas de Calificaciones

**Archivo:** `classes/report.php`

```php
/**
 * Get grading activity summary.
 *
 * @return array Grading statistics
 */
public function get_grading_activity(): array {
    return $this->get_cached_data('grading_activity', function() {
        return $this->compute_grading_activity();
    });
}

protected function compute_grading_activity(): array {
    global $DB;

    $monthstart = strtotime('-30 days midnight');
    $userids = $this->get_company_userids();

    if (empty($userids)) {
        return ['grades_given' => 0, 'users_graded' => 0, 'avg_grade' => 0];
    }

    list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

    $sql = "SELECT
                COUNT(*) as grades_given,
                COUNT(DISTINCT gg.userid) as users_graded,
                AVG(gg.finalgrade) as avg_grade
            FROM {grade_grades} gg
            JOIN {grade_items} gi ON gg.itemid = gi.id
            WHERE gg.userid $usersql
              AND gg.finalgrade IS NOT NULL
              AND gg.timemodified >= :monthstart
              AND gi.itemtype != 'course'";

    $params = array_merge(['monthstart' => $monthstart], $userparams);
    $result = $DB->get_record_sql($sql, $params);

    return [
        'grades_given' => (int) ($result->grades_given ?? 0),
        'users_graded' => (int) ($result->users_graded ?? 0),
        'avg_grade' => round((float) ($result->avg_grade ?? 0), 2),
    ];
}
```

### 7.2 ALTA PRIORIDAD: Duracion Estimada de Sesion

```php
/**
 * Get session duration statistics.
 *
 * @return array Session stats
 */
public function get_session_duration_stats(): array {
    return $this->get_cached_data('session_duration', function() {
        return $this->compute_session_duration();
    });
}

protected function compute_session_duration(): array {
    global $DB;

    $dbman = $DB->get_manager();
    if (!$dbman->table_exists('logstore_standard_log')) {
        return ['avg_minutes' => 0, 'total_sessions' => 0];
    }

    $monthstart = strtotime('-30 days midnight');
    $userids = $this->get_company_userids();

    if (empty($userids)) {
        return ['avg_minutes' => 0, 'total_sessions' => 0];
    }

    list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

    // Calcular duracion promedio basada en login/logout
    $sql = "SELECT
                COUNT(DISTINCT CONCAT(userid, '_', DATE(FROM_UNIXTIME(timecreated)))) as sessions,
                AVG(session_events) * 2 as avg_minutes  -- Estimar 2 min por evento
            FROM (
                SELECT userid, DATE(FROM_UNIXTIME(timecreated)) as session_date,
                       COUNT(*) as session_events
                FROM {logstore_standard_log}
                WHERE userid $usersql
                  AND timecreated >= :monthstart
                GROUP BY userid, session_date
            ) session_data";

    $params = array_merge(['monthstart' => $monthstart], $userparams);
    $result = $DB->get_record_sql($sql, $params);

    return [
        'avg_minutes' => round((float) ($result->avg_minutes ?? 15), 1),
        'total_sessions' => (int) ($result->sessions ?? 0),
    ];
}
```

### 7.3 MEDIA PRIORIDAD: Logins Fallidos (Seguridad)

```php
/**
 * Get failed login attempts.
 *
 * @return array Failed login stats
 */
public function get_failed_logins(): array {
    return $this->get_cached_data('failed_logins', function() {
        return $this->compute_failed_logins();
    });
}

protected function compute_failed_logins(): array {
    global $DB;

    $dbman = $DB->get_manager();
    if (!$dbman->table_exists('logstore_standard_log')) {
        return ['last_24h' => 0, 'last_week' => 0, 'top_usernames' => []];
    }

    $dayago = strtotime('-24 hours');
    $weekago = strtotime('-7 days');

    $sql = "SELECT
                SUM(CASE WHEN timecreated >= :dayago THEN 1 ELSE 0 END) as last_24h,
                SUM(CASE WHEN timecreated >= :weekago THEN 1 ELSE 0 END) as last_week
            FROM {logstore_standard_log}
            WHERE eventname = :event";

    $params = [
        'event' => '\\core\\event\\user_login_failed',
        'dayago' => $dayago,
        'weekago' => $weekago,
    ];

    $result = $DB->get_record_sql($sql, $params);

    // Top usernames con intentos fallidos
    $sql2 = "SELECT other, COUNT(*) as attempts
             FROM {logstore_standard_log}
             WHERE eventname = :event
               AND timecreated >= :weekago
             GROUP BY other
             ORDER BY attempts DESC
             LIMIT 5";

    $topUsers = $DB->get_records_sql($sql2, [
        'event' => '\\core\\event\\user_login_failed',
        'weekago' => $weekago
    ]);

    return [
        'last_24h' => (int) ($result->last_24h ?? 0),
        'last_week' => (int) ($result->last_week ?? 0),
        'top_usernames' => $topUsers,
    ];
}
```

### 7.4 MEDIA PRIORIDAD: Actividad de Quiz

```php
/**
 * Get quiz activity summary.
 *
 * @return array Quiz statistics
 */
public function get_quiz_activity(): array {
    return $this->get_cached_data('quiz_activity', function() {
        return $this->compute_quiz_activity();
    });
}

protected function compute_quiz_activity(): array {
    global $DB;

    $monthstart = strtotime('-30 days midnight');
    $userids = $this->get_company_userids();

    if (empty($userids)) {
        return [
            'attempts_started' => 0,
            'attempts_submitted' => 0,
            'avg_grade' => 0,
            'completion_rate' => 0
        ];
    }

    list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

    // Consultar intentos de quiz
    $sql = "SELECT
                COUNT(CASE WHEN state = 'inprogress' OR state = 'finished' THEN 1 END) as started,
                COUNT(CASE WHEN state = 'finished' THEN 1 END) as submitted,
                AVG(CASE WHEN state = 'finished' THEN sumgrades END) as avg_score
            FROM {quiz_attempts}
            WHERE userid $usersql
              AND timestart >= :monthstart";

    $params = array_merge(['monthstart' => $monthstart], $userparams);
    $result = $DB->get_record_sql($sql, $params);

    $started = (int) ($result->started ?? 0);
    $submitted = (int) ($result->submitted ?? 0);
    $completionRate = $started > 0 ? round(($submitted / $started) * 100, 1) : 0;

    return [
        'attempts_started' => $started,
        'attempts_submitted' => $submitted,
        'avg_grade' => round((float) ($result->avg_score ?? 0), 2),
        'completion_rate' => $completionRate,
    ];
}
```

### 7.5 BAJA PRIORIDAD: Actividad de Foros

```php
/**
 * Get forum activity summary.
 *
 * @return array Forum statistics
 */
public function get_forum_activity(): array {
    return $this->get_cached_data('forum_activity', function() {
        return $this->compute_forum_activity();
    });
}

protected function compute_forum_activity(): array {
    global $DB;

    $monthstart = strtotime('-30 days midnight');
    $userids = $this->get_company_userids();

    if (empty($userids)) {
        return ['posts_created' => 0, 'discussions_started' => 0, 'active_users' => 0];
    }

    list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

    $sql = "SELECT
                COUNT(*) as posts_created,
                COUNT(DISTINCT parent) as discussions,
                COUNT(DISTINCT userid) as active_users
            FROM {forum_posts}
            WHERE userid $usersql
              AND created >= :monthstart";

    $params = array_merge(['monthstart' => $monthstart], $userparams);
    $result = $DB->get_record_sql($sql, $params);

    return [
        'posts_created' => (int) ($result->posts_created ?? 0),
        'discussions_started' => (int) ($result->discussions ?? 0),
        'active_users' => (int) ($result->active_users ?? 0),
    ];
}
```

---

## 8. OPTIMIZACIONES PROPUESTAS

### 8.1 Indice Adicional en logstore_standard_log

```sql
CREATE INDEX idx_eventname_userid_time
ON logstore_standard_log (eventname, userid, timecreated);
```

### 8.2 Query Combinada para Dashboard

Para reducir round-trips a la base de datos:

```php
/**
 * Get all summary data in single query.
 */
public function get_combined_summary(): array {
    return $this->get_cached_data('combined_summary', function() {
        global $DB;

        $userids = $this->get_company_userids();
        if (empty($userids)) {
            return $this->get_empty_combined_summary();
        }

        $todaystart = strtotime('today midnight');
        $weekstart = strtotime('-7 days midnight');
        $monthstart = strtotime('-30 days midnight');

        list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

        // Una sola query para todos los eventos
        $sql = "SELECT
                    -- Logins
                    SUM(CASE WHEN eventname = :ev_login AND timecreated >= :t1 THEN 1 ELSE 0 END) as logins_today,
                    COUNT(DISTINCT CASE WHEN eventname = :ev_login2 AND timecreated >= :t2 THEN userid END) as unique_today,
                    SUM(CASE WHEN eventname = :ev_login3 AND timecreated >= :w1 THEN 1 ELSE 0 END) as logins_week,
                    SUM(CASE WHEN eventname = :ev_login4 AND timecreated >= :m1 THEN 1 ELSE 0 END) as logins_month,
                    -- Logouts
                    SUM(CASE WHEN eventname = :ev_logout AND timecreated >= :m2 THEN 1 ELSE 0 END) as logouts_month,
                    -- Dashboard
                    COUNT(DISTINCT CASE WHEN eventname = :ev_dash AND timecreated >= :m3 THEN userid END) as dashboard_month,
                    -- Course views
                    SUM(CASE WHEN eventname = :ev_course AND timecreated >= :m4 THEN 1 ELSE 0 END) as course_views
                FROM {logstore_standard_log}
                WHERE userid $usersql";

        $params = array_merge([
            'ev_login' => '\\core\\event\\user_loggedin',
            'ev_login2' => '\\core\\event\\user_loggedin',
            'ev_login3' => '\\core\\event\\user_loggedin',
            'ev_login4' => '\\core\\event\\user_loggedin',
            'ev_logout' => '\\core\\event\\user_loggedout',
            'ev_dash' => '\\core\\event\\dashboard_viewed',
            'ev_course' => '\\core\\event\\course_viewed',
            't1' => $todaystart, 't2' => $todaystart,
            'w1' => $weekstart,
            'm1' => $monthstart, 'm2' => $monthstart,
            'm3' => $monthstart, 'm4' => $monthstart,
        ], $userparams);

        return $DB->get_record_sql($sql, $params);
    });
}
```

### 8.3 Cache Warming via Cron

```php
// classes/task/cache_warm.php
namespace report_platform_usage\task;

class cache_warm extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('cachetask', 'report_platform_usage');
    }

    public function execute() {
        global $DB;

        // Obtener todas las empresas
        $companies = $DB->get_records('company', null, '', 'id');

        foreach ($companies as $company) {
            $report = new \report_platform_usage\report($company->id, 0, 0, true);

            // Pre-calentar datos comunes
            $report->get_login_summary();
            $report->get_user_activity_summary();
            $report->get_top_courses(10);

            mtrace("Cache warmed for company {$company->id}");
        }
    }
}
```

---

## 9. STRINGS ADICIONALES PROPUESTOS

**Archivo:** `lang/en/report_platform_usage.php`

```php
// Nuevas metricas
$string['gradingactivity'] = 'Grading Activity';
$string['usersgraded'] = 'Users Graded';
$string['avggrade'] = 'Average Grade';

$string['quizactivity'] = 'Quiz Activity';
$string['quizstarted'] = 'Quizzes Started';
$string['quizsubmitted'] = 'Quizzes Submitted';
$string['quizcompletionrate'] = 'Quiz Completion Rate';

$string['forumactivity'] = 'Forum Activity';
$string['forumposts'] = 'Forum Posts';
$string['forumdiscussions'] = 'Discussions Started';

$string['sessionduration'] = 'Session Duration';
$string['avgsession'] = 'Average Session';
$string['totalsessions'] = 'Total Sessions';

// Seguridad
$string['securitydashboard'] = 'Security Dashboard';
$string['failedlogins'] = 'Failed Login Attempts';
$string['last24hours'] = 'Last 24 hours';
$string['suspiciousactivity'] = 'Suspicious Activity';

// Cache
$string['cachetask'] = 'Warm report cache';
```

---

## 10. ARQUITECTURA

```
+------------------------------------------------------------------+
|                        index.php (Dashboard)                      |
|  +---------+  +---------+  +---------+  +---------+  +---------+ |
|  | Logins  |  | Users   |  | Courses |  |Complete |  | Logout  | |
|  | Cards   |  | Cards   |  | Cards   |  | Cards   |  | Cards   | |
|  +---------+  +---------+  +---------+  +---------+  +---------+ |
|                                                                   |
|  +-------------------------+  +----------------------------------+|
|  |    Charts (Chart.js)    |  |        Tables                    ||
|  | - Daily Logins          |  | - Course Usage                   ||
|  | - User Activity Pie     |  | - Top Activities                 ||
|  | - Course Trends         |  |                                  ||
|  | - Completion Trends     |  |                                  ||
|  +-------------------------+  +----------------------------------+|
+------------------------------------------------------------------+
                              |
                              v
+------------------------------------------------------------------+
|                    ajax.php / export.php                          |
+------------------------------------------------------------------+
                              |
                              v
+------------------------------------------------------------------+
|                    classes/report.php                             |
|  +------------+  +------------+  +------------+  +------------+  |
|  | get_login  |  | get_top    |  | get_course |  | get_dash   |  |
|  | _summary() |  | _courses() |  | _complete  |  | _access()  |  |
|  +------------+  +------------+  +------------+  +------------+  |
+------------------------------------------------------------------+
          |              |              |              |
          v              v              v              v
+------------------------------------------------------------------+
|                      MUC Cache Layer                              |
|          'reportdata' (TTL 5min)  'companyusers' (TTL 10min)     |
+------------------------------------------------------------------+
          |              |              |              |
          v              v              v              v
+------------------------------------------------------------------+
|                        Database                                   |
|  +-----------------+  +-----------------+  +-----------------+   |
|  | logstore_log    |  | course_complete |  | company_users   |   |
|  +-----------------+  +-----------------+  +-----------------+   |
+------------------------------------------------------------------+
```

---

## 11. RESUMEN EJECUTIVO

### Estado Actual del Plugin

| Funcionalidad | Estado | Ubicacion |
|---------------|--------|-----------|
| Login summary (hoy/7d/30d) | IMPLEMENTADO | report.php:196 |
| Logout summary | IMPLEMENTADO | report.php:922 |
| Dashboard access | IMPLEMENTADO | report.php:800 |
| Daily logins chart | IMPLEMENTADO | report.php:280 |
| User activity summary | IMPLEMENTADO | report.php:350 |
| Top courses | IMPLEMENTADO | report.php:393 |
| Top activities | IMPLEMENTADO | report.php:512 |
| Course completions | IMPLEMENTADO | report.php:740 |
| Completion trends | IMPLEMENTADO | report.php:860 |
| Cache MUC | IMPLEMENTADO | caches.php |
| Export CSV | IMPLEMENTADO | exporter.php |
| Export Excel con graficos | IMPLEMENTADO | excel_exporter.php |
| Filtro por empresa IOMAD | IMPLEMENTADO | report.php:127 |
| AJAX dinamico | IMPLEMENTADO | ajax.php |

### Funcionalidades Faltantes

| Funcionalidad | Prioridad | Complejidad |
|---------------|-----------|-------------|
| Calificaciones (grades) | ALTA | Media |
| Duracion de sesion | ALTA | Alta |
| Logins fallidos | MEDIA | Baja |
| Actividad de quiz | MEDIA | Media |
| Actividad de foros | BAJA | Baja |
| Query combinada optimizada | ALTA | Media |
| Cache warming cron | BAJA | Baja |

### Conclusion

El plugin `report_platform_usage` es un dashboard **robusto y funcional** que proporciona metricas clave de uso de plataforma. Ya incluye:

- Metricas de login/logout/dashboard
- Completados de cursos y tendencias
- Sistema de cache MUC para rendimiento
- Exportacion a CSV y Excel con graficos

Las mejoras propuestas expandirian el valor analitico agregando:

1. **Metricas de calificaciones** - Visibilidad del proceso de evaluacion
2. **Duracion de sesiones** - Indicador clave de engagement
3. **Seguridad** - Monitoreo de intentos de acceso fallidos
4. **Actividad especifica** - Quiz, foros, tareas

Estas mejoras transformarian el reporte de un panel de acceso basico a una herramienta completa de **Learning Analytics** para IOMAD.
