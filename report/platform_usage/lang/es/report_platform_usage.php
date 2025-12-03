<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Cadenas de texto en español para el Reporte de Uso de Plataforma.
 *
 * @package   report_platform_usage
 * @copyright 2024 IOMAD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Plugin principal.
$string['pluginname'] = 'Reporte de uso de plataforma';
$string['platform_usage:view'] = 'Ver el reporte de uso de plataforma';
$string['platform_usage:export'] = 'Exportar el reporte de uso de plataforma';
$string['platformusagereport'] = 'Reporte de uso de plataforma';
$string['reporttitle'] = 'Reporte de uso de plataforma';

// Filtros.
$string['company'] = 'Empresa';
$string['allcompanies'] = 'Todas las empresas';
$string['selectcompany'] = 'Seleccionar empresa';
$string['filter'] = 'Aplicar filtros';
$string['filter_company'] = 'Empresa';
$string['filter_daterange'] = 'Rango de fechas';

// Períodos de tiempo.
$string['today'] = 'Hoy';
$string['lastweek'] = 'Últimos 7 días';
$string['lastmonth'] = 'Últimos 30 días';
$string['lastquarter'] = 'Últimos 90 días';
$string['lastyear'] = 'Últimos 365 días';
$string['custom'] = 'Rango personalizado';
$string['datefrom'] = 'Desde';
$string['dateto'] = 'Hasta';
$string['daterange'] = 'Rango de fechas';

// Exportación.
$string['exportexcel'] = 'Descargar Excel';
$string['exportcsv'] = 'Descargar CSV';

// Métricas de ingreso.
$string['loginstoday'] = 'Ingresos hoy';
$string['loginsweek'] = 'Ingresos (7 días)';
$string['loginsmonth'] = 'Ingresos (30 días)';
$string['uniqueuserstoday'] = 'Usuarios únicos hoy';
$string['uniqueusersweek'] = 'Usuarios únicos (7 días)';
$string['uniqueusersmonth'] = 'Usuarios únicos (30 días)';
$string['loginsummary'] = 'Resumen de ingresos';
$string['logintrends'] = 'Tendencia de ingresos';
$string['dailylogins'] = 'Ingresos diarios';
$string['weeklylogins'] = 'Ingresos semanales';
$string['monthlylogins'] = 'Ingresos mensuales';

// Métricas de usuarios.
$string['totalusers'] = 'Total de usuarios';
$string['activeusers'] = 'Usuarios activos';
$string['inactiveusers'] = 'Usuarios inactivos';
$string['usersummary'] = 'Resumen de usuarios';
$string['usersbyactivity'] = 'Usuarios por actividad';
$string['userdetails'] = 'Detalle de usuarios';
$string['usercount'] = 'Usuarios';
$string['dailyusers'] = 'Usuarios únicos diarios';
$string['dailyuserstable'] = 'Usuarios diarios';

// Métricas de cursos.
$string['courses'] = 'Cursos';
$string['coursename'] = 'Curso';
$string['courseaccesses'] = 'Accesos';
$string['courseusage'] = 'Uso de cursos';
$string['topcourses'] = 'Cursos más accedidos';
$string['courseaccesstrends'] = 'Tendencia de accesos';
$string['courseaccessdetails'] = 'Detalle de accesos a cursos';
$string['coursesummary'] = 'Resumen de cursos';
$string['courseaccess'] = 'Acceso a cursos';

// Métricas de actividades.
$string['activities'] = 'Actividades';
$string['activityname'] = 'Actividad';
$string['activitytype'] = 'Tipo';
$string['activityaccesses'] = 'Vistas';
$string['activityusage'] = 'Uso de actividades';
$string['topactivities'] = 'Actividades más accedidas';
$string['activityaccessdetails'] = 'Detalle de actividades';
$string['activityaccess'] = 'Acceso a actividades';

// Métricas de finalización.
$string['completions'] = 'Finalizaciones';
$string['completionstoday'] = 'Hoy';
$string['completionsweek'] = 'Últimos 7 días';
$string['completionsmonth'] = 'Últimos 30 días';
$string['totalcompletions'] = 'Total';
$string['completiontrends'] = 'Tendencia de finalizaciones';

// Métricas de dedicación.
$string['topdedication'] = 'Cursos por dedicación';
$string['dedicationdetails'] = 'Detalle de dedicación';
$string['totaldedication'] = 'Tiempo total';
$string['enrolledusers'] = 'Matriculados';
$string['dedicationpercent'] = 'Porcentaje';

// Métricas de sesión.
$string['avgsessionduration'] = 'Duración promedio de sesión';
$string['totalsessions'] = 'Total de sesiones';
$string['logoutstoday'] = 'Cierres de sesión hoy';
$string['logoutsweek'] = 'Cierres de sesión (7 días)';
$string['logoutsmonth'] = 'Cierres de sesión (30 días)';

// Métricas de panel.
$string['dashboardusers'] = 'Usuarios del panel (30 días)';
$string['dashboardweek'] = 'Usuarios del panel (7 días)';
$string['dashboardtoday'] = 'Usuarios del panel hoy';

// Encabezados de tabla.
$string['date'] = 'Fecha';
$string['logins'] = 'Ingresos';
$string['uniqueusers'] = 'Usuarios únicos';
$string['username'] = 'Usuario';
$string['fullname'] = 'Nombre';
$string['email'] = 'Correo';
$string['lastaccess'] = 'Último acceso';
$string['logincount'] = 'Ingresos';
$string['shortname'] = 'Nombre corto';
$string['avgaccessperuser'] = 'Prom. por usuario';

// Estados.
$string['status'] = 'Estado';
$string['active'] = 'Activo';
$string['inactive'] = 'Inactivo';
$string['total'] = 'Total';
$string['created'] = 'Creado';

// Estadísticas.
$string['summary'] = 'Resumen';
$string['platformsummary'] = 'Vista general de la plataforma';
$string['platformaccess'] = 'Acceso a la plataforma';
$string['average'] = 'Promedio';
$string['maximum'] = 'Máximo';
$string['period'] = 'Período';
$string['accesscount'] = 'Accesos';

// Reporte de curso.
$string['coursereport'] = 'Reporte de uso del curso';
$string['coursereport_desc'] = 'Estadísticas de uso del curso: actividad, participación y dedicación.';
$string['courseenrolledusers'] = 'Usuarios matriculados';
$string['courseactiveusers'] = 'Usuarios activos';
$string['courseinactiveusers'] = 'Usuarios inactivos';
$string['courselogins'] = 'Accesos al curso';
$string['coursecompletions'] = 'Finalizaciones';

// Mensajes.
$string['nodata'] = 'No hay datos disponibles para los filtros seleccionados.';
$string['nodataforperiod'] = 'No hay datos disponibles para este período.';
$string['selectcompanyfirst'] = 'Seleccione una empresa para ver el reporte.';
$string['loadingreport'] = 'Cargando...';

// Navegación.
$string['backtoreport'] = 'Volver al reporte';
$string['viewdetails'] = 'Ver detalles';
$string['generateddate'] = 'Generado';
$string['generated_by'] = 'Generado por';

// Privacidad.
$string['privacy:metadata'] = 'Este plugin no almacena datos personales.';

// Descripciones de secciones para la interfaz.
$string['logintrends_desc'] = 'Actividad de ingresos en los últimos 30 días';
$string['usersbyactivity_desc'] = 'Usuarios activos vs inactivos (umbral de 30 días)';
$string['coursetrends_desc'] = 'Accesos a cursos en el período seleccionado';
$string['dedication_desc'] = 'Tiempo dedicado por curso según actividad de sesión';
$string['topcourses_desc'] = 'Cursos ordenados por cantidad de accesos';
$string['topactivities_desc'] = 'Actividades de aprendizaje más vistas';
$string['completiontrends_desc'] = 'Finalizaciones de cursos en 30 días';

// Descripciones de exportación.
$string['export_report_desc'] = 'Análisis de uso de plataforma con métricas de actividad y participación.';
$string['export_logins_desc'] = 'Total de ingresos y usuarios únicos por período. Cada autenticación cuenta como un ingreso.';
$string['export_users_desc'] = 'Usuarios registrados, activos (acceso en 30 días) e inactivos.';
$string['export_courses_desc'] = 'Cursos ordenados por vistas, usuarios únicos y tiempo de dedicación.';
$string['export_activities_desc'] = 'Actividades más accedidas con conteo de vistas y participación.';
$string['export_daily_desc'] = 'Desglose diario de ingresos para análisis de patrones.';
$string['export_completions_desc'] = 'Finalizaciones de cursos por período. Se registran al cumplir los criterios.';
$string['export_dedication_desc'] = 'Tiempo por curso basado en sesiones. Las sesiones terminan tras 1 hora de inactividad.';
$string['export_dailyusers_desc'] = 'Conteo diario de usuarios únicos para seguimiento de tendencias.';

// Varios.
$string['metric_explanation'] = 'Explicación de la métrica';
$string['data_section'] = 'Sección de datos';
$string['users'] = 'Usuarios';
$string['coursemetrics'] = 'Métricas del curso';
$string['metric'] = 'Métrica';
$string['value'] = 'Valor';
$string['completionrate'] = 'Tasa de finalización';

// Configuración.
$string['settings'] = 'Configuración del reporte';
$string['setting_session_limit'] = 'Tiempo de sesión';
$string['setting_session_limit_desc'] = 'Tiempo máximo entre acciones para considerar que pertenecen a una misma sesión. Se usa para calcular la dedicación.';
$string['setting_ignore_sessions_limit'] = 'Duración mínima de sesión';
$string['setting_ignore_sessions_limit_desc'] = 'Ignorar sesiones más cortas que esta duración. Ayuda a filtrar visitas breves que no representan tiempo real de trabajo.';
$string['setting_default_period'] = 'Período predeterminado';
$string['setting_default_period_desc'] = 'Rango de tiempo predeterminado para los datos del reporte cuando no se seleccionan fechas.';
$string['setting_top_items_limit'] = 'Límite de elementos';
$string['setting_top_items_limit_desc'] = 'Cantidad de elementos a mostrar en las listas de cursos y actividades.';
$string['setting_enable_cache'] = 'Usar caché';
$string['setting_enable_cache_desc'] = 'Almacenar datos del reporte en caché para mejorar el rendimiento. Desactivar solo para depuración.';

// Tooltips para elementos del reporte.
$string['tooltip_platformaccess'] = 'Cantidad de veces que los usuarios iniciaron sesión en la plataforma durante el período seleccionado.';
$string['tooltip_loginstoday'] = 'Total de eventos de inicio de sesión registrados hoy (un usuario puede iniciar sesión varias veces).';
$string['tooltip_loginsweek'] = 'Total de eventos de inicio de sesión en los últimos 7 días.';
$string['tooltip_loginsmonth'] = 'Total de eventos de inicio de sesión en los últimos 30 días.';
$string['tooltip_uniqueusers'] = 'Cantidad de usuarios diferentes que iniciaron sesión al menos una vez durante el período.';
$string['tooltip_usersummary'] = 'Desglose de usuarios según su estado de actividad en la plataforma.';
$string['tooltip_totalusers'] = 'Total de usuarios registrados en el ámbito seleccionado.';
$string['tooltip_activeusers'] = 'Usuarios que accedieron a la plataforma en los últimos 30 días.';
$string['tooltip_inactiveusers'] = 'Usuarios sin acceso a la plataforma en los últimos 30 días.';
$string['tooltip_dashboardusers'] = 'Cantidad de usuarios que accedieron a su panel este mes.';
$string['tooltip_completions'] = 'Cantidad de cursos completados por los usuarios durante el período seleccionado.';
$string['tooltip_completionstoday'] = 'Cursos completados hoy.';
$string['tooltip_completionsweek'] = 'Cursos completados en los últimos 7 días.';
$string['tooltip_completionsmonth'] = 'Cursos completados en los últimos 30 días.';
$string['tooltip_totalcompletions'] = 'Total de finalizaciones de cursos registradas.';
$string['tooltip_dailyusers'] = 'Desglose diario de usuarios únicos que accedieron a la plataforma.';
$string['tooltip_avgdaily'] = 'Promedio de usuarios únicos por día.';
$string['tooltip_maxdaily'] = 'Mayor cantidad de usuarios únicos en un solo día.';
$string['tooltip_dailylogins'] = 'Tendencia de ingresos y usuarios únicos en los últimos 30 días.';
$string['tooltip_courseaccess'] = 'Métricas de acceso y dedicación para el curso seleccionado.';
$string['tooltip_courseaccesses'] = 'Cantidad de veces que se accedió al curso durante el período seleccionado.';
$string['tooltip_enrolledusers'] = 'Total de usuarios matriculados en el curso.';
$string['tooltip_courseactiveusers'] = 'Usuarios matriculados que accedieron a la plataforma en los últimos 30 días.';
$string['tooltip_courseinactiveusers'] = 'Usuarios matriculados sin acceso a la plataforma en los últimos 30 días.';
$string['tooltip_coursecompletions'] = 'Cantidad de usuarios que completaron todos los requisitos del curso.';
$string['tooltip_totaldedication'] = 'Tiempo total estimado dedicado al curso por todos los usuarios.';
$string['tooltip_avgdedication'] = 'Tiempo promedio dedicado por usuario matriculado.';
$string['tooltip_dedication_calc'] = 'El tiempo se calcula según las sesiones de actividad del usuario. Una sesión termina después de {$a} de inactividad.';
$string['tooltip_topcourses'] = 'Cursos más accedidos ordenados por cantidad de visitas.';
$string['tooltip_topactivities'] = 'Actividades más accedidas ordenadas por cantidad de visitas.';
$string['tooltip_completiontrends'] = 'Finalizaciones de cursos diarias en los últimos 30 días.';
$string['tooltip_dailyuserstable'] = 'Conteos recientes de usuarios únicos diarios.';
$string['tooltip_dedicationchart'] = 'Cursos con mayor dedicación ordenados por tiempo total.';

// Tarea programada.
$string['task_collect_dedication'] = 'Recolectar datos de tiempo de dedicación';

// Cadenas contextuales (curso vs plataforma).
$string['courseaccesstrends'] = 'Tendencia de accesos al curso';
$string['courseaccesstrends_desc'] = 'Accesos diarios al curso y usuarios únicos durante el período seleccionado.';
$string['courseenrolledusers_desc'] = 'Distribución de usuarios matriculados según su estado de actividad.';
$string['coursededicationsummary'] = 'Resumen de dedicación del curso';
$string['coursededication_desc'] = 'Resumen de métricas de tiempo de dedicación para este curso.';
$string['avgdedicationperuser'] = 'Promedio por usuario';
$string['topcourseaccess'] = 'Principales accesos al curso';
$string['coursecompletiontrends'] = 'Tendencia de finalizaciones del curso';
$string['coursecompletiontrends_desc'] = 'Finalizaciones diarias de este curso durante el período seleccionado.';
$string['coursedailyusers'] = 'Usuarios diarios del curso';

// Hojas detalladas de exportación.
$string['courseusersdetails'] = 'Detalle de usuarios matriculados';
$string['courseusersdetails_desc'] = 'Desglose detallado de todos los usuarios matriculados incluyendo estado de finalización, tiempo de dedicación y último acceso.';
$string['courseaccesshistory'] = 'Historial de acceso al curso';
$string['courseaccesshistory_desc'] = 'Tendencias de acceso diario para este curso.';
$string['userdedication'] = 'Dedicación del usuario';
$string['lastcourseaccess'] = 'Último acceso al curso';
$string['completionstatus'] = 'Estado de finalización';
$string['notcompleted'] = 'No completado';
$string['completed'] = 'Completado';
$string['never'] = 'Nunca';

// Nombres de hojas Excel (contexto plataforma).
$string['sheet_summary'] = 'Resumen';
$string['sheet_daily_logins'] = 'Ingresos Diarios';
$string['sheet_daily_users'] = 'Usuarios Diarios';
$string['sheet_completions'] = 'Finalizaciones';
$string['sheet_courses'] = 'Cursos';
$string['sheet_activities'] = 'Actividades';
$string['sheet_users'] = 'Usuarios';
$string['sheet_dedication'] = 'Dedicación';

// Nombres de hojas Excel (contexto curso).
$string['sheet_course_summary'] = 'Resumen del Curso';
$string['sheet_enrolled_users'] = 'Usuarios Matriculados';
$string['sheet_access_history'] = 'Historial de Accesos';
$string['sheet_course_activities'] = 'Actividades del Curso';
$string['sheet_course_completions'] = 'Finalizaciones del Curso';
$string['sheet_course_dedication'] = 'Dedicación del Curso';

// Información adicional del Excel.
$string['export_generated_by'] = 'Reporte generado por IOMAD Platform';
$string['export_date_format'] = '%d/%m/%Y %H:%M';
$string['export_period_info'] = 'Período del reporte';
$string['export_total_records'] = 'Total de registros';
$string['export_data_notes'] = 'Notas sobre los datos';
$string['export_login_note'] = 'Cada evento de ingreso se cuenta por separado. Un usuario puede tener múltiples ingresos por día.';
$string['export_dedication_note'] = 'El tiempo se calcula basándose en sesiones de actividad. Las sesiones terminan después de {$a} de inactividad.';
$string['export_completion_note'] = 'Las finalizaciones se registran cuando se cumplen todos los criterios de finalización del curso.';
$string['export_active_note'] = 'Los usuarios activos son aquellos que accedieron a la plataforma en los últimos 30 días.';
$string['percentage'] = 'Porcentaje';
$string['variation'] = 'Variación';
$string['trend'] = 'Tendencia';
$string['increasing'] = 'En aumento';
$string['decreasing'] = 'En descenso';
$string['stable'] = 'Estable';
