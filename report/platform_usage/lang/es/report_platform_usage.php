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

// Información del plugin.
$string['pluginname'] = 'Reporte de Uso de Plataforma';
$string['platform_usage:view'] = 'Ver reporte de uso de plataforma';
$string['platform_usage:export'] = 'Exportar reporte de uso de plataforma';
$string['platformusagereport'] = 'Reporte de Uso de Plataforma';

// Filtro de compañía.
$string['company'] = 'Compañía';
$string['allcompanies'] = 'Todas las compañías';
$string['selectcompany'] = 'Seleccione una compañía';
$string['filter'] = 'Aplicar filtro';

// Opciones de exportación.
$string['exportexcel'] = 'Exportar a Excel';
$string['exportcsv'] = 'Exportar a CSV';

// Períodos de tiempo.
$string['today'] = 'Hoy';
$string['lastweek'] = 'Últimos 7 días';
$string['lastmonth'] = 'Últimos 30 días';
$string['lastquarter'] = 'Últimos 90 días';
$string['lastyear'] = 'Último año';
$string['custom'] = 'Rango de fechas personalizado';
$string['datefrom'] = 'Desde';
$string['dateto'] = 'Hasta';

// Estadísticas de inicio de sesión.
$string['loginstoday'] = 'Ingresos hoy';
$string['loginsweek'] = 'Ingresos en los últimos 7 días';
$string['loginsmonth'] = 'Ingresos en los últimos 30 días';
$string['uniqueuserstoday'] = 'Usuarios únicos hoy';
$string['uniqueusersweek'] = 'Usuarios únicos en los últimos 7 días';
$string['uniqueusersmonth'] = 'Usuarios únicos en los últimos 30 días';

// Estadísticas de usuarios.
$string['totalusers'] = 'Total de usuarios registrados';
$string['activeusers'] = 'Usuarios activos (acceso en los últimos 30 días)';
$string['inactiveusers'] = 'Usuarios inactivos (sin acceso en los últimos 30 días)';

// Títulos de gráficos.
$string['logintrends'] = 'Tendencias de Ingreso';
$string['dailylogins'] = 'Ingresos Diarios';
$string['weeklylogins'] = 'Ingresos Semanales';
$string['monthlylogins'] = 'Ingresos Mensuales';
$string['usersbyactivity'] = 'Usuarios por Estado de Actividad';
$string['courseusage'] = 'Uso de Cursos';
$string['topcourses'] = 'Cursos Más Accedidos';
$string['courseaccesstrends'] = 'Tendencias de Acceso a Cursos';
$string['activityusage'] = 'Uso de Actividades';
$string['topactivities'] = 'Actividades Más Accedidas';

// Encabezados de tabla.
$string['date'] = 'Fecha';
$string['logins'] = 'Ingresos';
$string['uniqueusers'] = 'Usuarios únicos';
$string['coursename'] = 'Nombre del curso';
$string['courseaccesses'] = 'Accesos al curso';
$string['activityname'] = 'Nombre de la actividad';
$string['activitytype'] = 'Tipo de actividad';
$string['activityaccesses'] = 'Accesos a la actividad';
$string['username'] = 'Nombre de usuario';
$string['fullname'] = 'Nombre completo';
$string['email'] = 'Correo electrónico';
$string['lastaccess'] = 'Último acceso';
$string['logincount'] = 'Cantidad de ingresos';

// Secciones de resumen.
$string['summary'] = 'Resumen';
$string['platformsummary'] = 'Resumen de Plataforma';
$string['coursesummary'] = 'Resumen de Cursos';
$string['usersummary'] = 'Resumen de Usuarios';

// Secciones del reporte.
$string['platformaccess'] = 'Acceso a la Plataforma';
$string['courseaccess'] = 'Acceso a Cursos';
$string['activityaccess'] = 'Acceso a Actividades';
$string['userdetails'] = 'Detalles de Usuarios';

// Mensajes y notificaciones.
$string['nodata'] = 'No hay datos disponibles para los filtros seleccionados';
$string['nodataforperiod'] = 'No hay datos disponibles para el período seleccionado';
$string['selectcompanyfirst'] = 'Por favor, seleccione una compañía para ver el reporte';
$string['loadingreport'] = 'Cargando datos del reporte...';

// Privacidad.
$string['privacy:metadata'] = 'El plugin Reporte de Uso de Plataforma no almacena ningún dato personal.';

// Navegación.
$string['backtoreport'] = 'Volver al reporte';
$string['viewdetails'] = 'Ver detalles';

// Etiquetas de gráficos.
$string['period'] = 'Período';
$string['accesscount'] = 'Cantidad de accesos';
$string['usercount'] = 'Cantidad de usuarios';

// Exportación a Excel.
$string['reporttitle'] = 'Reporte de Uso de Plataforma';
$string['daterange'] = 'Rango de fechas';
$string['generateddate'] = 'Generado el';
$string['loginsummary'] = 'Resumen de Ingresos';
$string['shortname'] = 'Nombre corto';
$string['courses'] = 'Cursos';
$string['activities'] = 'Actividades';
$string['users'] = 'Usuarios';
$string['courseaccessdetails'] = 'Detalles de Acceso a Cursos';
$string['activityaccessdetails'] = 'Detalles de Acceso a Actividades';
$string['avgaccessperuser'] = 'Promedio de accesos por usuario';
$string['total'] = 'Total';
$string['created'] = 'Fecha de creación';
$string['status'] = 'Estado';
$string['active'] = 'Activo';
$string['inactive'] = 'Inactivo';

// Finalizaciones de cursos.
$string['completions'] = 'Finalizaciones';
$string['completionsmonth'] = 'Finalizaciones (últimos 30 días)';
$string['completionsweek'] = 'Finalizaciones (últimos 7 días)';
$string['completionstoday'] = 'Finalizaciones hoy';
$string['totalcompletions'] = 'Total de finalizaciones';
$string['completiontrends'] = 'Tendencias de Finalización';

// Acceso al panel de control.
$string['dashboardusers'] = 'Usuarios del panel (últimos 30 días)';
$string['dashboardweek'] = 'Usuarios del panel (últimos 7 días)';
$string['dashboardtoday'] = 'Hoy';

// Métricas de sesión.
$string['avgsessionduration'] = 'Duración promedio de sesión';
$string['totalsessions'] = 'Total de sesiones';

// Métricas de cierre de sesión.
$string['logoutsmonth'] = 'Cierres de sesión (últimos 30 días)';
$string['logoutsweek'] = 'Cierres de sesión (últimos 7 días)';
$string['logoutstoday'] = 'Cierres de sesión hoy';

// Usuarios diarios.
$string['dailyusers'] = 'Usuarios Únicos Diarios';
$string['dailyuserstable'] = 'Historial de Usuarios Diarios';

// Métricas de dedicación.
$string['topdedication'] = 'Cursos con Mayor Tiempo de Dedicación';
$string['dedicationdetails'] = 'Detalles de Dedicación';
$string['totaldedication'] = 'Tiempo total de dedicación';
$string['enrolledusers'] = 'Usuarios matriculados';
$string['dedicationpercent'] = 'Porcentaje de dedicación';

// Reporte de curso.
$string['coursereport'] = 'Reporte de Uso del Curso';
$string['coursereport_desc'] = 'Estadísticas detalladas de uso de este curso, incluyendo actividad de usuarios, métricas de participación y análisis del tiempo de dedicación.';
$string['courseenrolledusers'] = 'Usuarios matriculados';
$string['courseactiveusers'] = 'Usuarios activos en el curso';
$string['courseinactiveusers'] = 'Usuarios inactivos en el curso';
$string['courselogins'] = 'Accesos al curso';
$string['coursecompletions'] = 'Finalizaciones del curso';

// Descripciones de secciones.
$string['logintrends_desc'] = 'Tendencias de ingresos diarios y conteos de usuarios únicos durante los últimos 30 días.';
$string['usersbyactivity_desc'] = 'Distribución de usuarios por estado de actividad (activos vs. inactivos según umbral de 30 días).';
$string['coursetrends_desc'] = 'Tendencias de acceso a cursos durante el período seleccionado.';
$string['dedication_desc'] = 'Tiempo dedicado por estudiantes en cada curso, calculado a partir de los registros de actividad de sesión.';
$string['topcourses_desc'] = 'Cursos ordenados por cantidad de accesos, con métricas de tiempo de dedicación.';
$string['topactivities_desc'] = 'Actividades de aprendizaje más accedidas en todos los cursos.';
$string['completiontrends_desc'] = 'Tendencias de finalización de cursos durante los últimos 30 días.';

// Estadísticas.
$string['average'] = 'Promedio';
$string['maximum'] = 'Máximo';

// Descripciones de exportación.
$string['export_report_desc'] = 'Análisis integral del uso de la plataforma con métricas detalladas de actividad y datos de participación de usuarios.';
$string['export_logins_desc'] = 'Resumen de Ingresos: Total de ingresos y usuarios únicos para hoy, los últimos 7 días y los últimos 30 días. Cada autenticación exitosa a la plataforma cuenta como un ingreso.';
$string['export_users_desc'] = 'Resumen de Actividad de Usuarios: Total de usuarios registrados, usuarios activos (ingresaron en los últimos 30 días) e inactivos. Los usuarios activos son aquellos que accedieron a la plataforma en los últimos 30 días.';
$string['export_courses_desc'] = 'Detalles de Acceso a Cursos: Cursos ordenados por vistas totales, usuarios únicos y tiempo de dedicación. El conteo de accesos representa las veces que los usuarios vieron cada página del curso.';
$string['export_activities_desc'] = 'Detalles de Acceso a Actividades: Actividades de aprendizaje más accedidas (tareas, foros, cuestionarios, etc.) con conteos de vistas y participación de usuarios únicos. Identifica qué materiales de aprendizaje son más utilizados.';
$string['export_daily_desc'] = 'Historial de Ingresos Diarios: Desglose día por día de ingresos a la plataforma y usuarios únicos. Útil para identificar patrones de uso y períodos de mayor actividad.';
$string['export_completions_desc'] = 'Resumen de Finalización de Cursos: Cantidad de cursos finalizados en diferentes períodos. Una finalización se registra cuando un usuario cumple todos los criterios de finalización del curso.';
$string['export_dedication_desc'] = 'Análisis de Dedicación a Cursos: Tiempo dedicado por estudiantes en cada curso según la actividad de sesión. Las sesiones terminan después de 1 hora de inactividad. Muestra el tiempo total de dedicación y la distribución porcentual entre cursos.';
$string['export_dailyusers_desc'] = 'Usuarios Únicos Diarios: Registro histórico de conteos de usuarios únicos por día. Permite seguir las tendencias de participación y el crecimiento de la plataforma a lo largo del tiempo.';

// Etiquetas de filtros.
$string['generated_by'] = 'Generado por';
$string['filter_company'] = 'Filtro de compañía';
$string['filter_daterange'] = 'Filtro de rango de fechas';
$string['metric_explanation'] = 'Explicación de la métrica';
$string['data_section'] = 'Sección de datos';
