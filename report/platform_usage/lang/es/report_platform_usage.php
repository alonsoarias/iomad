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

// Configuración.
$string['settings'] = 'Configuración del reporte';
$string['setting_session_limit'] = 'Tiempo de sesión';
$string['setting_session_limit_desc'] = 'Tiempo máximo entre acciones para considerar que pertenecen a una misma sesión. Se usa para calcular la dedicación.';
$string['setting_default_period'] = 'Período predeterminado';
$string['setting_default_period_desc'] = 'Rango de tiempo predeterminado para los datos del reporte cuando no se seleccionan fechas.';
$string['setting_top_items_limit'] = 'Límite de elementos';
$string['setting_top_items_limit_desc'] = 'Cantidad de elementos a mostrar en las listas de cursos y actividades.';
$string['setting_enable_cache'] = 'Usar caché';
$string['setting_enable_cache_desc'] = 'Almacenar datos del reporte en caché para mejorar el rendimiento. Desactivar solo para depuración.';
