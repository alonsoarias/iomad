# Report Platform Usage - Análisis y Documentación

## Resumen del Plugin

**Nombre:** `report_platform_usage`
**Versión:** 1.2.0 (Build: 20241128)
**Tipo:** Plugin de reportes
**Ubicación:** `/report/platform_usage/`

### Descripción

Este plugin proporciona un **dashboard de reportes en tiempo real** sobre el uso de la plataforma Moodle/IOMAD. Consulta datos de la tabla `logstore_standard_log` para generar métricas y visualizaciones de actividad de usuarios.

---

## Arquitectura del Plugin

### Estructura de Archivos

```
report/platform_usage/
├── classes/
│   ├── excel_exporter.php        # Exportador Excel con gráficos
│   ├── exporter.php              # Exportador CSV básico
│   ├── report.php                # Clase principal de reportes
│   └── privacy/
│       └── provider.php          # Proveedor de privacidad
├── db/
│   ├── access.php                # Capacidades del plugin
│   └── caches.php                # Definiciones de caché MUC
├── lang/
│   ├── en/
│   │   └── report_platform_usage.php  # Strings en inglés
│   └── es/
│       └── report_platform_usage.php  # Strings en español
├── ajax.php                      # Endpoint AJAX
├── export.php                    # Handler de exportación
├── index.php                     # Página principal con dashboard
├── settings.php                  # Configuración del plugin
└── version.php                   # Información de versión
```

---

## Análisis del Código Actual

### Clase `report.php` - Motor de Datos

#### Propiedades
```php
protected $companyid;    // Filtro por compañía
protected $datefrom;     // Fecha inicio
protected $dateto;       // Fecha fin
protected $userids;      // IDs de usuarios cacheados
protected $cache;        // Caché MUC para datos
protected $usercache;    // Caché MUC para usuarios
protected $usecache;     // Flag de uso de caché
```

#### Métodos de Obtención de Datos

| Método | Línea | Descripción | Evento Consultado |
|--------|-------|-------------|-------------------|
| `get_login_summary()` | 196 | Resumen de logins | `\core\event\user_loggedin` |
| `get_daily_logins()` | 280 | Logins diarios | `\core\event\user_loggedin` |
| `get_user_activity_summary()` | 350 | Usuarios activos/inactivos | Campo `lastaccess` de `user` |
| `get_top_courses()` | 393 | Top cursos por acceso | `\core\event\course_viewed` |
| `get_course_access_trends()` | 444 | Tendencia de accesos | `\core\event\course_viewed` |
| `get_top_activities()` | 512 | Top actividades | `action='viewed'` + `target='course_module'` |
| `get_user_login_details()` | 614 | Detalles por usuario | `\core\event\user_loggedin` |
| `get_course_access_details()` | 664 | Detalles por curso | `\core\event\course_viewed` |
| `get_daily_data_for_export()` | 702 | Datos diarios para exportar | `\core\event\user_loggedin` |

### Eventos Consultados Actualmente

| Evento | Clase | Métricas Generadas |
|--------|-------|-------------------|
| Login | `\core\event\user_loggedin` | Logins totales, usuarios únicos, tendencias |
| Curso visto | `\core\event\course_viewed` | Top cursos, tendencias de acceso |
| Actividad vista | `action='viewed' + target='course_module'` | Top actividades |

---

## Métricas Actuales del Dashboard

### 1. Tarjetas de Resumen
- **Logins hoy:** Total y usuarios únicos
- **Logins últimos 7 días:** Total y usuarios únicos
- **Logins últimos 30 días:** Total y usuarios únicos

### 2. Gráficos
- **Daily Logins (Line Chart):** Logins y usuarios únicos por día
- **User Activity (Doughnut):** Usuarios activos vs inactivos
- **Course Access Trends (Line Chart):** Tendencia de accesos a cursos
- **Top Courses (Bar Chart):** Cursos más accedidos

### 3. Tablas
- **Course Usage:** Nombre, accesos, usuarios únicos
- **Top Activities:** Nombre, curso, tipo, accesos, usuarios únicos

---

## Eventos de Moodle NO Consultados

### Eventos de Alto Valor NO Incluidos

| Evento | Clase | Valor para Reportes |
|--------|-------|---------------------|
| Dashboard visto | `\core\event\dashboard_viewed` | Medir acceso a área personal |
| Curso completado | `\core\event\course_completed` | Métricas de completado |
| Usuario calificado | `\core\event\user_graded` | Actividad de calificación |
| Módulo completado | `\core\event\course_module_completion_updated` | Progreso granular |
| Login fallido | `\core\event\user_login_failed` | Seguridad |
| Logout | `\core\event\user_loggedout` | Duración de sesiones |
| Quiz iniciado | `\mod_quiz\event\attempt_started` | Actividad en evaluaciones |
| Quiz enviado | `\mod_quiz\event\attempt_submitted` | Completado de evaluaciones |
| Tarea enviada | `\mod_assign\event\submission_created` | Entregas |
| Foro post | `\mod_forum\event\post_created` | Participación en foros |
| Mensaje enviado | `\core\event\message_sent` | Comunicación |
| Archivo descargado | `\mod_resource\event\course_module_viewed` | Uso de recursos |

### Datos NO Utilizados

| Tabla/Campo | Información Disponible |
|-------------|------------------------|
| `course_completions` | Completados de cursos |
| `course_modules_completion` | Completados de actividades |
| `grade_grades` | Calificaciones |
| `quiz_attempts` | Intentos de quiz |
| `assign_submission` | Entregas de tareas |
| `forum_posts` | Posts en foros |
| `user.lastip` | Última IP |
| `logstore.origin` | Origen (web/cli/ws) |

---

## Mejoras Propuestas

### 1. Nueva Métrica: Completados de Curso

**Archivo:** `classes/report.php`

```php
/**
 * Get course completions summary.
 *
 * @return array Completion statistics
 */
public function get_course_completions_summary(): array {
    return $this->get_cached_data('completions_summary', function() {
        return $this->compute_course_completions_summary();
    });
}

/**
 * Compute course completions summary.
 *
 * @return array
 */
protected function compute_course_completions_summary(): array {
    global $DB;

    $userids = $this->get_company_userids();
    if (empty($userids)) {
        return [
            'completions_today' => 0,
            'completions_week' => 0,
            'completions_month' => 0,
            'total_completions' => 0,
        ];
    }

    $todaystart = strtotime('today midnight');
    $weekstart = strtotime('-7 days midnight');
    $monthstart = strtotime('-30 days midnight');

    list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'user');

    $sql = "SELECT
                SUM(CASE WHEN timecompleted >= :today THEN 1 ELSE 0 END) as completions_today,
                SUM(CASE WHEN timecompleted >= :week THEN 1 ELSE 0 END) as completions_week,
                SUM(CASE WHEN timecompleted >= :month THEN 1 ELSE 0 END) as completions_month,
                COUNT(*) as total_completions
            FROM {course_completions}
            WHERE userid $usersql
              AND timecompleted IS NOT NULL";

    $params = array_merge([
        'today' => $todaystart,
        'week' => $weekstart,
        'month' => $monthstart,
    ], $userparams);

    $result = $DB->get_record_sql($sql, $params);

    return [
        'completions_today' => (int) ($result->completions_today ?? 0),
        'completions_week' => (int) ($result->completions_week ?? 0),
        'completions_month' => (int) ($result->completions_month ?? 0),
        'total_completions' => (int) ($result->total_completions ?? 0),
    ];
}
```

### 2. Nueva Métrica: Dashboard Access

**Archivo:** `classes/report.php`

```php
/**
 * Get dashboard access statistics.
 *
 * @return array Dashboard access stats
 */
public function get_dashboard_access(): array {
    return $this->get_cached_data('dashboard_access', function() {
        return $this->compute_dashboard_access();
    });
}

/**
 * Compute dashboard access.
 *
 * @return array
 */
protected function compute_dashboard_access(): array {
    global $DB;

    $dbman = $DB->get_manager();
    if (!$dbman->table_exists('logstore_standard_log')) {
        return ['today' => 0, 'week' => 0, 'month' => 0];
    }

    $todaystart = strtotime('today midnight');
    $weekstart = strtotime('-7 days midnight');
    $monthstart = strtotime('-30 days midnight');

    $userids = $this->get_company_userids();
    if (empty($userids)) {
        return ['today' => 0, 'week' => 0, 'month' => 0];
    }

    list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'user');

    $sql = "SELECT
                COUNT(DISTINCT CASE WHEN timecreated >= :today THEN userid END) as today,
                COUNT(DISTINCT CASE WHEN timecreated >= :week THEN userid END) as week,
                COUNT(DISTINCT CASE WHEN timecreated >= :month THEN userid END) as month
            FROM {logstore_standard_log}
            WHERE eventname = :event
              AND userid $usersql";

    $params = array_merge([
        'event' => '\\core\\event\\dashboard_viewed',
        'today' => $todaystart,
        'week' => $weekstart,
        'month' => $monthstart,
    ], $userparams);

    $result = $DB->get_record_sql($sql, $params);

    return [
        'today' => (int) ($result->today ?? 0),
        'week' => (int) ($result->week ?? 0),
        'month' => (int) ($result->month ?? 0),
    ];
}
```

### 3. Nueva Métrica: Duración de Sesión

**Archivo:** `classes/report.php`

```php
/**
 * Get average session duration estimate.
 * Calculates based on login/logout events or last activity.
 *
 * @return array Session duration stats
 */
public function get_session_duration_stats(): array {
    return $this->get_cached_data('session_duration', function() {
        return $this->compute_session_duration();
    });
}

/**
 * Compute session duration.
 *
 * @return array
 */
protected function compute_session_duration(): array {
    global $DB;

    $dbman = $DB->get_manager();
    if (!$dbman->table_exists('logstore_standard_log')) {
        return ['avg_minutes' => 0, 'total_sessions' => 0];
    }

    $monthstart = strtotime('-30 days midnight');
    list($usersql, $userparams) = $this->get_user_sql('l1.userid');

    // Estimate session duration by finding time between login and next login or logout
    $sql = "SELECT
                AVG(
                    CASE
                        WHEN l2.timecreated IS NOT NULL
                        THEN LEAST(l2.timecreated - l1.timecreated, 7200) -- Max 2 hours
                        ELSE 1800 -- Default 30 min if no logout found
                    END
                ) / 60 as avg_minutes,
                COUNT(DISTINCT l1.id) as total_sessions
            FROM {logstore_standard_log} l1
            LEFT JOIN {logstore_standard_log} l2
                ON l1.userid = l2.userid
                AND l2.eventname IN (
                    '\\\\core\\\\event\\\\user_loggedout',
                    '\\\\core\\\\event\\\\user_loggedin'
                )
                AND l2.timecreated > l1.timecreated
                AND l2.timecreated < l1.timecreated + 86400
                AND l2.id = (
                    SELECT MIN(id) FROM {logstore_standard_log}
                    WHERE userid = l1.userid
                    AND timecreated > l1.timecreated
                    AND eventname IN (
                        '\\\\core\\\\event\\\\user_loggedout',
                        '\\\\core\\\\event\\\\user_loggedin'
                    )
                )
            WHERE l1.eventname = :event
              AND l1.timecreated >= :monthstart
              AND $usersql";

    $params = array_merge([
        'event' => '\\core\\event\\user_loggedin',
        'monthstart' => $monthstart,
    ], $userparams);

    $result = $DB->get_record_sql($sql, $params);

    return [
        'avg_minutes' => round((float) ($result->avg_minutes ?? 30), 1),
        'total_sessions' => (int) ($result->total_sessions ?? 0),
    ];
}
```

### 4. Nueva Métrica: Actividad de Calificaciones

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

/**
 * Compute grading activity.
 *
 * @return array
 */
protected function compute_grading_activity(): array {
    global $DB;

    $monthstart = strtotime('-30 days midnight');
    $userids = $this->get_company_userids();

    if (empty($userids)) {
        return [
            'grades_given' => 0,
            'users_graded' => 0,
            'avg_grade' => 0,
        ];
    }

    list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'user');

    // Count grades given to company users
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

### 5. Nueva Sección en Dashboard: Engagement Score

**Archivo:** `index.php` (agregar después de línea 156)

```php
// Engagement metrics row
echo '<div class="row mb-4">';

// Course Completions card
$completions = $report->get_course_completions_summary();
echo '<div class="col-md-3 col-sm-6 mb-3">';
echo '<div class="card h-100 border-info">';
echo '<div class="card-body text-center">';
echo '<h5 class="card-title text-info">' . get_string('completionsmonth', 'report_platform_usage') . '</h5>';
echo '<h2 class="display-4" id="completions-month">' . number_format($completions['completions_month']) . '</h2>';
echo '<p class="text-muted">' . get_string('totalcompletions', 'report_platform_usage') . ': <strong>' .
    number_format($completions['total_completions']) . '</strong></p>';
echo '</div>';
echo '</div>';
echo '</div>';

// Dashboard Access card
$dashboardAccess = $report->get_dashboard_access();
echo '<div class="col-md-3 col-sm-6 mb-3">';
echo '<div class="card h-100 border-secondary">';
echo '<div class="card-body text-center">';
echo '<h5 class="card-title text-secondary">' . get_string('dashboardusers', 'report_platform_usage') . '</h5>';
echo '<h2 class="display-4" id="dashboard-month">' . number_format($dashboardAccess['month']) . '</h2>';
echo '<p class="text-muted">' . get_string('dashboardtoday', 'report_platform_usage') . ': <strong>' .
    number_format($dashboardAccess['today']) . '</strong></p>';
echo '</div>';
echo '</div>';
echo '</div>';

// Average Session Duration card
$sessionStats = $report->get_session_duration_stats();
echo '<div class="col-md-3 col-sm-6 mb-3">';
echo '<div class="card h-100 border-dark">';
echo '<div class="card-body text-center">';
echo '<h5 class="card-title text-dark">' . get_string('avgsessionduration', 'report_platform_usage') . '</h5>';
echo '<h2 class="display-4" id="avg-session">' . $sessionStats['avg_minutes'] . ' <small>min</small></h2>';
echo '<p class="text-muted">' . get_string('totalsessions', 'report_platform_usage') . ': <strong>' .
    number_format($sessionStats['total_sessions']) . '</strong></p>';
echo '</div>';
echo '</div>';
echo '</div>';

// Grading Activity card
$gradingActivity = $report->get_grading_activity();
echo '<div class="col-md-3 col-sm-6 mb-3">';
echo '<div class="card h-100 border-danger">';
echo '<div class="card-body text-center">';
echo '<h5 class="card-title text-danger">' . get_string('gradingactivity', 'report_platform_usage') . '</h5>';
echo '<h2 class="display-4" id="grades-given">' . number_format($gradingActivity['grades_given']) . '</h2>';
echo '<p class="text-muted">' . get_string('usersgraded', 'report_platform_usage') . ': <strong>' .
    number_format($gradingActivity['users_graded']) . '</strong></p>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '</div>';
```

### 6. Nuevo Gráfico: Completion Trends

**Archivo:** `index.php` (agregar nuevo gráfico)

```php
// Completion Trends Chart
echo '<div class="row mb-4">';
echo '<div class="col-lg-6 mb-3">';
echo '<div class="card h-100">';
echo '<div class="card-header"><h5 class="mb-0">' . get_string('completiontrends', 'report_platform_usage') . '</h5></div>';
echo '<div class="card-body">';
echo '<canvas id="completionTrendsChart" height="300"></canvas>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';
```

### 7. Método para Tendencias de Completado

**Archivo:** `classes/report.php`

```php
/**
 * Get completion trends over time.
 *
 * @param int $days Number of days
 * @return array [labels, data]
 */
public function get_completion_trends(int $days = 30): array {
    return $this->get_cached_data("completion_trends_{$days}", function() use ($days) {
        return $this->compute_completion_trends($days);
    });
}

/**
 * Compute completion trends.
 *
 * @param int $days
 * @return array
 */
protected function compute_completion_trends(int $days): array {
    global $DB;

    $starttime = strtotime("-{$days} days midnight");
    $userids = $this->get_company_userids();

    if (empty($userids)) {
        return ['labels' => [], 'data' => []];
    }

    list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'user');

    $sql = "SELECT DATE(FROM_UNIXTIME(timecompleted)) as completion_date,
                   COUNT(*) as completion_count
            FROM {course_completions}
            WHERE userid $usersql
              AND timecompleted >= :starttime
              AND timecompleted IS NOT NULL
            GROUP BY DATE(FROM_UNIXTIME(timecompleted))
            ORDER BY completion_date ASC";

    $params = array_merge(['starttime' => $starttime], $userparams);
    $records = $DB->get_records_sql($sql, $params);

    $labels = [];
    $data = [];

    for ($i = $days; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-{$i} days"));
        $labels[] = date('d M', strtotime($date));
        $data[$date] = 0;
    }

    foreach ($records as $record) {
        if (isset($data[$record->completion_date])) {
            $data[$record->completion_date] = (int) $record->completion_count;
        }
    }

    return [
        'labels' => $labels,
        'data' => array_values($data),
    ];
}
```

### 8. Strings de Idioma Adicionales

**Archivo:** `lang/en/report_platform_usage.php`

```php
// New metrics
$string['completionsmonth'] = 'Completions (30 days)';
$string['totalcompletions'] = 'Total completions';
$string['completiontrends'] = 'Completion Trends';
$string['dashboardusers'] = 'Dashboard Users (30 days)';
$string['dashboardtoday'] = 'Today';
$string['avgsessionduration'] = 'Avg Session Duration';
$string['totalsessions'] = 'Total sessions';
$string['gradingactivity'] = 'Grades Given (30 days)';
$string['usersgraded'] = 'Users graded';

// Engagement section
$string['engagement'] = 'Engagement Metrics';
$string['engagementscore'] = 'Engagement Score';
$string['participationrate'] = 'Participation Rate';

// Security metrics
$string['failedlogins'] = 'Failed Login Attempts';
$string['suspiciousactivity'] = 'Suspicious Activity';

// Content metrics
$string['filesdownloaded'] = 'Files Downloaded';
$string['forumposts'] = 'Forum Posts';
$string['assignsubmissions'] = 'Assignment Submissions';
$string['quizattempts'] = 'Quiz Attempts';
```

### 9. Optimización de Consultas

**Problema:** Múltiples consultas independientes ejecutadas secuencialmente.

**Solución:** Agregar consulta combinada para reducir round-trips a la BD.

**Archivo:** `classes/report.php`

```php
/**
 * Get all summary data in a single optimized query.
 * Reduces database round-trips significantly.
 *
 * @return array Combined summary data
 */
public function get_combined_summary(): array {
    return $this->get_cached_data('combined_summary', function() {
        return $this->compute_combined_summary();
    });
}

/**
 * Compute combined summary with single query for each table.
 *
 * @return array
 */
protected function compute_combined_summary(): array {
    global $DB;

    $userids = $this->get_company_userids();
    if (empty($userids)) {
        return $this->get_empty_summary();
    }

    $todaystart = strtotime('today midnight');
    $weekstart = strtotime('-7 days midnight');
    $monthstart = strtotime('-30 days midnight');

    list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'user');

    // Single query for logstore data
    $sql = "SELECT
                -- Logins
                SUM(CASE WHEN eventname = :ev_login AND timecreated >= :t1 THEN 1 ELSE 0 END) as logins_today,
                COUNT(DISTINCT CASE WHEN eventname = :ev_login2 AND timecreated >= :t2 THEN userid END) as unique_today,
                SUM(CASE WHEN eventname = :ev_login3 AND timecreated >= :w1 THEN 1 ELSE 0 END) as logins_week,
                COUNT(DISTINCT CASE WHEN eventname = :ev_login4 AND timecreated >= :w2 THEN userid END) as unique_week,
                SUM(CASE WHEN eventname = :ev_login5 AND timecreated >= :m1 THEN 1 ELSE 0 END) as logins_month,
                COUNT(DISTINCT CASE WHEN eventname = :ev_login6 AND timecreated >= :m2 THEN userid END) as unique_month,
                -- Course views
                SUM(CASE WHEN eventname = :ev_course AND timecreated >= :m3 THEN 1 ELSE 0 END) as course_views_month,
                COUNT(DISTINCT CASE WHEN eventname = :ev_course2 AND timecreated >= :m4 THEN courseid END) as courses_accessed,
                -- Dashboard
                COUNT(DISTINCT CASE WHEN eventname = :ev_dash AND timecreated >= :m5 THEN userid END) as dashboard_users
            FROM {logstore_standard_log}
            WHERE userid $usersql";

    $params = array_merge([
        'ev_login' => '\\core\\event\\user_loggedin',
        'ev_login2' => '\\core\\event\\user_loggedin',
        'ev_login3' => '\\core\\event\\user_loggedin',
        'ev_login4' => '\\core\\event\\user_loggedin',
        'ev_login5' => '\\core\\event\\user_loggedin',
        'ev_login6' => '\\core\\event\\user_loggedin',
        'ev_course' => '\\core\\event\\course_viewed',
        'ev_course2' => '\\core\\event\\course_viewed',
        'ev_dash' => '\\core\\event\\dashboard_viewed',
        't1' => $todaystart, 't2' => $todaystart,
        'w1' => $weekstart, 'w2' => $weekstart,
        'm1' => $monthstart, 'm2' => $monthstart,
        'm3' => $monthstart, 'm4' => $monthstart,
        'm5' => $monthstart,
    ], $userparams);

    $result = $DB->get_record_sql($sql, $params);

    return [
        'logins' => [
            'today' => (int) ($result->logins_today ?? 0),
            'unique_today' => (int) ($result->unique_today ?? 0),
            'week' => (int) ($result->logins_week ?? 0),
            'unique_week' => (int) ($result->unique_week ?? 0),
            'month' => (int) ($result->logins_month ?? 0),
            'unique_month' => (int) ($result->unique_month ?? 0),
        ],
        'courses' => [
            'views_month' => (int) ($result->course_views_month ?? 0),
            'accessed' => (int) ($result->courses_accessed ?? 0),
        ],
        'dashboard_users' => (int) ($result->dashboard_users ?? 0),
    ];
}
```

### 10. Nueva Vista: Security Dashboard

**Archivo nuevo:** `security.php`

```php
<?php
// Security metrics dashboard for platform usage

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();
$context = context_system::instance();
require_capability('report/platform_usage:view', $context);

$companyid = optional_param('companyid', 0, PARAM_INT);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/report/platform_usage/security.php'));
$PAGE->set_pagelayout('report');
$PAGE->set_title(get_string('securitydashboard', 'report_platform_usage'));

$report = new \report_platform_usage\report($companyid);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('securitydashboard', 'report_platform_usage'));

// Failed logins
$failedLogins = $report->get_failed_logins();

// Display security metrics...
echo '<div class="row">';
echo '<div class="col-md-4">';
echo '<div class="card border-danger">';
echo '<div class="card-body text-center">';
echo '<h5 class="text-danger">Failed Logins (24h)</h5>';
echo '<h2 class="display-4">' . number_format($failedLogins['last_24h']) . '</h2>';
echo '</div></div></div>';
// ... more security cards
echo '</div>';

echo $OUTPUT->footer();
```

---

## Definiciones de Caché

**Archivo:** `db/caches.php`

| Caché | Modo | TTL | Descripción |
|-------|------|-----|-------------|
| `reportdata` | APPLICATION | 300s (5 min) | Datos de reporte |
| `companyusers` | APPLICATION | 600s (10 min) | Listas de usuarios por compañía |

### Propuesta: Caché adicional

```php
// Cache for heavy computations
'heavycompute' => [
    'mode' => cache_store::MODE_APPLICATION,
    'simplekeys' => true,
    'simpledata' => false,
    'ttl' => 900, // 15 minutes
    'staticacceleration' => true,
    'staticaccelerationsize' => 5,
],
```

---

## Capacidades Definidas

| Capacidad | Tipo | Descripción |
|-----------|------|-------------|
| `report/platform_usage:view` | read | Ver reporte |
| `report/platform_usage:export` | read | Exportar reporte |

### Propuesta: Capacidad adicional

```php
'report/platform_usage:viewsecurity' => [
    'captype' => 'read',
    'contextlevel' => CONTEXT_SYSTEM,
    'archetypes' => [
        'manager' => CAP_ALLOW,
    ],
],
```

---

## Diagrama de Arquitectura

```
┌─────────────────────────────────────────────────────────┐
│                      index.php                          │
│              (Dashboard Principal)                      │
└───────────────────────┬─────────────────────────────────┘
                        │
          ┌─────────────┼─────────────┐
          │             │             │
          ▼             ▼             ▼
┌─────────────┐  ┌─────────────┐  ┌─────────────┐
│  ajax.php   │  │  export.php │  │security.php │
│   (AJAX)    │  │  (Export)   │  │ (Security)  │
└──────┬──────┘  └──────┬──────┘  └──────┬──────┘
       │                │                │
       └────────────────┼────────────────┘
                        │
                        ▼
              ┌─────────────────┐
              │   report.php    │
              │  (Clase Report) │
              └────────┬────────┘
                       │
         ┌─────────────┼─────────────┐
         │             │             │
         ▼             ▼             ▼
┌─────────────┐  ┌─────────────┐  ┌─────────────┐
│  MUC Cache  │  │   Database  │  │   Charts    │
│ reportdata  │  │logstore_log │  │  Chart.js   │
│companyusers │  │completions  │  │             │
└─────────────┘  │grade_grades │  └─────────────┘
                 └─────────────┘
```

---

## Exportadores

### CSV Exporter (`exporter.php`)
- Exportación básica a CSV
- Tipos: logins, courses, users, daily, summary

### Excel Exporter (`excel_exporter.php`)
- Exportación avanzada con PHPSpreadsheet
- Incluye gráficos embebidos (barras, líneas, pie)
- 5 hojas: Summary, Daily Logins, Courses, Activities, Users

---

## Consideraciones de Rendimiento

### Actuales
1. Sistema de caché MUC con TTL de 5-10 minutos
2. Consultas optimizadas con índices
3. AJAX para actualización dinámica sin recarga

### Propuestas
1. Implementar query combinada para reducir round-trips
2. Agregar índices adicionales en `logstore_standard_log`:
   ```sql
   CREATE INDEX idx_event_user_time ON logstore_standard_log (eventname, userid, timecreated);
   ```
3. Considerar materialización de vistas para reportes frecuentes
4. Implementar cache warming en cron

---

## Resumen de Mejoras Propuestas

| Mejora | Prioridad | Complejidad | Impacto |
|--------|-----------|-------------|---------|
| Completados de curso | Alta | Media | Alto |
| Dashboard access | Alta | Baja | Medio |
| Duración de sesión | Media | Alta | Alto |
| Actividad de calificaciones | Media | Media | Alto |
| Security dashboard | Baja | Alta | Medio |
| Query combinada optimizada | Alta | Media | Alto |
| Completion trends chart | Media | Baja | Medio |

---

## Conclusión

El plugin `report_platform_usage` proporciona un dashboard funcional pero limitado. Las mejoras propuestas expandirían significativamente su valor:

1. **Métricas de Completado**: Crítico para medir éxito educativo
2. **Duración de Sesión**: Indicador clave de engagement
3. **Actividad de Calificaciones**: Visibilidad del proceso de evaluación
4. **Dashboard Security**: Monitoreo de intentos de acceso
5. **Optimizaciones**: Mejor rendimiento con grandes volúmenes

La implementación de estas mejoras transformaría el reporte de un simple contador de logins a un verdadero panel de análisis de aprendizaje.
