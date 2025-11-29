# Report Platform Usage - Documentacion Exhaustiva

## Resumen del Plugin

**Nombre:** `report_platform_usage`
**Version:** 1.3.0 (Build: 20251129)
**Tipo:** Plugin de reportes
**Ubicacion:** `/report/platform_usage/`

### Descripcion

Este plugin proporciona un **dashboard de reportes en tiempo real** sobre el uso de la plataforma Moodle/IOMAD. Consulta datos de la tabla `logstore_standard_log` para generar metricas y visualizaciones de actividad de usuarios, incluyendo **metricas de seguridad** y **duracion de sesiones**.

---

## Arquitectura del Plugin

### Estructura de Archivos

```
report/platform_usage/
+-- classes/
|   +-- excel_exporter.php        # Exportador Excel con graficos
|   +-- exporter.php              # Exportador CSV basico
|   +-- report.php                # Clase principal de reportes
|   +-- privacy/
|       +-- provider.php          # Proveedor de privacidad
+-- db/
|   +-- access.php                # Capacidades del plugin
|   +-- caches.php                # Definiciones de cache MUC
+-- lang/
|   +-- en/
|   |   +-- report_platform_usage.php  # Strings en ingles
|   +-- es/
|       +-- report_platform_usage.php  # Strings en espanol
+-- ajax.php                      # Endpoint AJAX
+-- export.php                    # Handler de exportacion
+-- index.php                     # Pagina principal con dashboard
+-- settings.php                  # Configuracion del plugin
+-- version.php                   # Informacion de version
```

---

## Metricas del Dashboard

### 1. Tarjetas de Resumen (Login)
- **Logins hoy:** Total y usuarios unicos
- **Logins ultimos 7 dias:** Total y usuarios unicos
- **Logins ultimos 30 dias:** Total y usuarios unicos

### 2. Tarjetas de Engagement
- **Completados de curso:** Hoy, semana, mes, total
- **Accesos a dashboard:** Usuarios que accedieron al area personal
- **Duracion promedio de sesion:** Minutos estimados por sesion

### 3. Tarjetas de Seguridad (NUEVO)
- **Logins fallidos:** Hoy, semana, mes
- **Desglose por razon:** Usuario no existe, suspendido, contrasena incorrecta, etc.

### 4. Graficos
- **Daily Logins (Line Chart):** Logins y usuarios unicos por dia
- **User Activity (Doughnut):** Usuarios activos vs inactivos
- **Course Access Trends (Line Chart):** Tendencia de accesos a cursos
- **Top Courses (Bar Chart):** Cursos mas accedidos
- **Completion Trends (Line Chart):** Tendencia de completados
- **Daily Sessions (Line Chart):** Sesiones y logouts por dia (NUEVO)

### 5. Tablas
- **Course Usage:** Nombre, accesos, usuarios unicos
- **Top Activities:** Nombre, curso, tipo, accesos, usuarios unicos

---

## Eventos de Moodle Consultados

| Evento | Clase | Metricas Generadas |
|--------|-------|-------------------|
| Login | `\core\event\user_loggedin` | Logins totales, usuarios unicos, tendencias |
| Logout | `\core\event\user_loggedout` | Logouts, duracion de sesion |
| Login Fallido | `\core\event\user_login_failed` | Seguridad, intentos fallidos (NUEVO) |
| Curso visto | `\core\event\course_viewed` | Top cursos, tendencias de acceso |
| Dashboard visto | `\core\event\dashboard_viewed` | Accesos al area personal |
| Actividad vista | `action='viewed' + target='course_module'` | Top actividades |
| Curso completado | tabla `course_completions` | Completados, tendencias |

---

## Clase `report.php` - Motor de Datos

### Propiedades
```php
protected $companyid;    // Filtro por compania
protected $datefrom;     // Fecha inicio
protected $dateto;       // Fecha fin
protected $userids;      // IDs de usuarios cacheados
protected $cache;        // Cache MUC para datos
protected $usercache;    // Cache MUC para usuarios
protected $usecache;     // Flag de uso de cache
```

### Metodos Principales

| Metodo | Descripcion | Cache |
|--------|-------------|-------|
| `get_login_summary()` | Resumen de logins (hoy/semana/mes) | Si |
| `get_daily_logins()` | Logins diarios para grafico | Si |
| `get_user_activity_summary()` | Usuarios activos/inactivos | Si |
| `get_top_courses()` | Top cursos por acceso | Si |
| `get_course_access_trends()` | Tendencia de accesos a cursos | Si |
| `get_top_activities()` | Top actividades | Si |
| `get_course_completions_summary()` | Resumen de completados | Si |
| `get_dashboard_access()` | Accesos al dashboard | Si |
| `get_completion_trends()` | Tendencia de completados | Si |
| `get_logout_summary()` | Resumen de logouts | Si |
| `get_session_duration_stats()` | Duracion de sesiones (NUEVO) | Si |
| `get_failed_login_summary()` | Logins fallidos (NUEVO) | Si |
| `get_daily_sessions()` | Sesiones diarias (NUEVO) | Si |

### Metodos de Exportacion

| Metodo | Descripcion |
|--------|-------------|
| `get_user_login_details()` | Detalle de logins por usuario |
| `get_course_access_details()` | Detalle de accesos por curso |
| `get_daily_data_for_export()` | Datos diarios para export |
| `get_all_data()` | Todos los datos para AJAX |

---

## Nuevas Metricas Implementadas (v1.3.0)

### 1. Duracion de Sesion (ALTA PRIORIDAD)

```php
public function get_session_duration_stats(): array {
    // Retorna:
    // - avg_minutes: Duracion promedio en minutos
    // - total_sessions: Total de sesiones (logins)
    // - sessions_with_logout: Sesiones con logout registrado
    // - estimated_sessions: Sesiones sin logout (estimadas)
}
```

**Calculo:** Se emparejan eventos de login con logout del mismo usuario, calculando la diferencia de tiempo. Se aplica un maximo de 4 horas para evitar sesiones abandonadas.

### 2. Logins Fallidos (PRIORIDAD MEDIA - SEGURIDAD)

```php
public function get_failed_login_summary(): array {
    // Retorna:
    // - failed_today: Intentos fallidos hoy
    // - failed_week: Intentos fallidos ultimos 7 dias
    // - failed_month: Intentos fallidos ultimos 30 dias
    // - by_reason: Desglose por tipo de fallo
}
```

**Razones de fallo:**
- 1: Usuario no existe
- 2: Usuario suspendido
- 3: Contrasena incorrecta
- 4: Usuario bloqueado
- 5: Usuario no autorizado

### 3. Sesiones Diarias

```php
public function get_daily_sessions(int $days = 30): array {
    // Retorna:
    // - labels: Etiquetas de fecha
    // - sessions: Numero de logins por dia
    // - logouts: Numero de logouts por dia
}
```

---

## Sistema de Cache (MUC)

### Definiciones en `db/caches.php`

| Cache | Modo | TTL | Descripcion |
|-------|------|-----|-------------|
| `reportdata` | APPLICATION | 300s (5 min) | Datos de reporte calculados |
| `companyusers` | APPLICATION | 600s (10 min) | Listas de usuarios por compania |

### Uso del Cache

```php
// Ejemplo de uso en metodo
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

// Purgar cache
$report->purge_cache();
```

---

## Exportacion

### Exportador CSV (`exporter.php`)
- Exportacion basica a CSV
- Tipos: logins, courses, users, daily, summary

### Exportador Excel (`excel_exporter.php`)
- Usa PHPSpreadsheet
- Incluye graficos embebidos (barras, lineas, pie)
- 5 hojas:
  - Summary: Resumen general con graficos
  - Daily Logins: Datos diarios
  - Courses: Accesos a cursos
  - Activities: Accesos a actividades
  - Users: Detalle por usuario

### Formato Profesional del Excel
- Encabezados con formato
- Columnas auto-ajustadas
- Graficos embebidos
- Totales calculados

---

## Compatibilidad

### IOMAD
- Detecta automaticamente tabla `company_users`
- Filtra usuarios y datos por compania
- Selector de companias en el dashboard
- Funcion `get_companies()` para listar companias

### Moodle Estandar
- Funciona completamente sin IOMAD
- Procesa todos los usuarios del sistema
- Detecta ausencia de tablas de compania

### Deteccion Automatica
```php
$dbman = $DB->get_manager();
if ($dbman->table_exists('company_users')) {
    // Modo IOMAD
} else {
    // Modo Moodle estandar
}
```

---

## Consideraciones de Rendimiento

### Optimizaciones Implementadas

1. **Sistema de Cache MUC**: 5-10 minutos de TTL
2. **Consultas Combinadas**: Multiples metricas en una sola query
3. **AJAX para actualizacion**: Sin recarga de pagina
4. **Pre-carga de usuarios**: Lista de userids cacheada

### Indices Recomendados

```sql
-- Indices para logstore_standard_log
CREATE INDEX idx_event_user_time ON mdl_logstore_standard_log
    (eventname, userid, timecreated);

CREATE INDEX idx_event_time ON mdl_logstore_standard_log
    (eventname, timecreated);

-- Indices para course_completions
CREATE INDEX idx_user_completed ON mdl_course_completions
    (userid, timecompleted);
```

### Queries Optimizadas

Las consultas usan:
- `COUNT(DISTINCT CASE WHEN ... END)` para evitar subconsultas
- Agrupacion en una sola query para reducir round-trips
- Limites de tiempo para evitar escaneos completos

---

## Capacidades

| Capacidad | Tipo | Descripcion |
|-----------|------|-------------|
| `report/platform_usage:view` | read | Ver el reporte |
| `report/platform_usage:export` | read | Exportar datos |

---

## Endpoint AJAX

**Archivo:** `ajax.php`

```php
// Parametros
$action = required_param('action', PARAM_ALPHA); // 'getdata'
$companyid = optional_param('companyid', 0, PARAM_INT);
$datefrom = optional_param('datefrom', 0, PARAM_INT);
$dateto = optional_param('dateto', 0, PARAM_INT);

// Respuesta JSON con todos los datos del reporte
```

---

## Uso Programatico

```php
// Crear instancia del reporte
$report = new \report_platform_usage\report(
    $companyid = 1,
    $datefrom = strtotime('-30 days'),
    $dateto = time(),
    $usecache = true
);

// Obtener metricas individuales
$logins = $report->get_login_summary();
$completions = $report->get_course_completions_summary();
$sessions = $report->get_session_duration_stats();
$failedLogins = $report->get_failed_login_summary();

// Obtener todos los datos (para AJAX)
$allData = $report->get_all_data();

// Exportar a Excel
$exporter = new \report_platform_usage\excel_exporter($report);
$exporter->export_to_file('/tmp/report.xlsx');
```

---

## Datos Retornados por get_all_data()

```php
[
    'login_summary' => [
        'logins_today' => 45,
        'logins_week' => 320,
        'logins_month' => 1250,
        'unique_users_today' => 30,
        'unique_users_week' => 180,
        'unique_users_month' => 450,
    ],
    'user_summary' => [
        'total' => 500,
        'active' => 380,
        'inactive' => 120,
    ],
    'daily_logins' => [
        'labels' => ['01 Nov', '02 Nov', ...],
        'logins' => [45, 52, ...],
        'unique_users' => [30, 35, ...],
    ],
    'course_access_trends' => [...],
    'top_courses' => [...],
    'top_activities' => [...],
    'completions_summary' => [
        'completions_today' => 5,
        'completions_week' => 35,
        'completions_month' => 120,
        'total_completions' => 850,
    ],
    'dashboard_access' => [
        'today' => 25,
        'week' => 150,
        'month' => 400,
    ],
    'completion_trends' => [...],
    'logout_summary' => [...],
    // NUEVOS:
    'session_duration' => [
        'avg_minutes' => 45.3,
        'total_sessions' => 1250,
        'sessions_with_logout' => 980,
        'estimated_sessions' => 270,
    ],
    'failed_logins' => [
        'failed_today' => 3,
        'failed_week' => 15,
        'failed_month' => 42,
        'by_reason' => [
            1 => ['label' => 'User does not exist', 'count' => 5],
            2 => ['label' => 'User is suspended', 'count' => 2],
            3 => ['label' => 'Wrong password', 'count' => 30],
            4 => ['label' => 'User is locked out', 'count' => 3],
            5 => ['label' => 'User is not authorized', 'count' => 2],
        ],
    ],
    'daily_sessions' => [
        'labels' => [...],
        'sessions' => [...],
        'logouts' => [...],
    ],
]
```

---

## Mejoras Implementadas (v1.3.0)

### ALTA PRIORIDAD: Duracion de Sesion
- Calculo de duracion promedio de sesion
- Tracking de sesiones con/sin logout
- Estadisticas de sesiones diarias
- Grafico de sesiones vs logouts

### PRIORIDAD MEDIA: Metricas de Seguridad
- Monitoreo de logins fallidos
- Desglose por tipo de fallo
- Tendencias de intentos fallidos
- Alerta temprana de problemas de seguridad

### Optimizaciones de Performance
- Consultas combinadas
- Cache mejorado
- Indices recomendados

---

## Changelog

### v1.3.0 (2025-11-29)
- Agregado: Metricas de duracion de sesion
- Agregado: Metricas de logins fallidos (seguridad)
- Agregado: Metodo `get_session_duration_stats()`
- Agregado: Metodo `get_failed_login_summary()`
- Agregado: Metodo `get_daily_sessions()`
- Mejorado: `get_all_data()` incluye nuevas metricas
- Mejorado: Strings en ingles y espanol

### v1.2.0 (2025-11-28)
- Agregado: Metricas de completados de curso
- Agregado: Metricas de acceso a dashboard
- Agregado: Tendencias de completados
- Agregado: Exportador Excel con graficos

### v1.0.0 (2025-11-27)
- Version inicial con metricas basicas de login y cursos
