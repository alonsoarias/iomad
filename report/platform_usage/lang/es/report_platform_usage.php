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
 * Spanish language strings.
 *
 * @package   report_platform_usage
 * @copyright 2024 IOMAD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Reporte de Uso de Plataforma';
$string['platform_usage:view'] = 'Ver reporte de uso de plataforma';
$string['platform_usage:export'] = 'Exportar reporte de uso de plataforma';
$string['platformusagereport'] = 'Reporte de Uso de Plataforma';
$string['company'] = 'Compania';
$string['allcompanies'] = 'Todas las companias';
$string['selectcompany'] = 'Seleccione una compania';
$string['filter'] = 'Filtrar';
$string['exportexcel'] = 'Exportar a Excel';
$string['exportcsv'] = 'Exportar a CSV';

// Time periods.
$string['today'] = 'Hoy';
$string['lastweek'] = 'Ultimos 7 dias';
$string['lastmonth'] = 'Ultimos 30 dias';
$string['lastquarter'] = 'Ultimos 90 dias';
$string['lastyear'] = 'Ultimo ano';
$string['custom'] = 'Rango personalizado';
$string['datefrom'] = 'Fecha desde';
$string['dateto'] = 'Fecha hasta';

// Statistics labels.
$string['loginstoday'] = 'Ingresos hoy';
$string['loginsweek'] = 'Ingresos ultimos 7 dias';
$string['loginsmonth'] = 'Ingresos ultimos 30 dias';
$string['uniqueuserstoday'] = 'Usuarios unicos hoy';
$string['uniqueusersweek'] = 'Usuarios unicos ultimos 7 dias';
$string['uniqueusersmonth'] = 'Usuarios unicos ultimos 30 dias';
$string['totalusers'] = 'Total de usuarios';
$string['activeusers'] = 'Usuarios activos (ingreso en ultimos 30 dias)';
$string['inactiveusers'] = 'Usuarios sin actividad reciente (sin ingreso en 30 dias)';

// Charts titles.
$string['logintrends'] = 'Tendencias de Ingreso';
$string['dailylogins'] = 'Ingresos Diarios';
$string['weeklylogins'] = 'Ingresos Semanales';
$string['monthlylogins'] = 'Ingresos Mensuales';
$string['usersbyactivity'] = 'Usuarios por Actividad de Ingreso Reciente';
$string['courseusage'] = 'Uso de Cursos';
$string['topcourses'] = 'Cursos mas Accedidos';
$string['courseaccesstrends'] = 'Tendencias de Acceso a Cursos';
$string['activityusage'] = 'Uso de Actividades';
$string['topactivities'] = 'Actividades mas Accedidas';

// Table headers.
$string['date'] = 'Fecha';
$string['logins'] = 'Ingresos';
$string['uniqueusers'] = 'Usuarios Unicos';
$string['coursename'] = 'Nombre del Curso';
$string['courseaccesses'] = 'Accesos al Curso';
$string['activityname'] = 'Nombre de Actividad';
$string['activitytype'] = 'Tipo de Actividad';
$string['activityaccesses'] = 'Accesos a Actividad';
$string['username'] = 'Nombre de usuario';
$string['fullname'] = 'Nombre Completo';
$string['email'] = 'Correo electronico';
$string['lastaccess'] = 'Ultimo Acceso';
$string['logincount'] = 'Cantidad de Ingresos';

// Summary section.
$string['summary'] = 'Resumen';
$string['platformsummary'] = 'Resumen de Plataforma';
$string['coursesummary'] = 'Resumen de Cursos';
$string['usersummary'] = 'Resumen de Usuarios';

// Report sections.
$string['platformaccess'] = 'Acceso a Plataforma';
$string['courseaccess'] = 'Acceso a Cursos';
$string['activityaccess'] = 'Acceso a Actividades';
$string['userdetails'] = 'Detalles de Usuario';

// Messages.
$string['nodata'] = 'No hay datos disponibles para los criterios seleccionados';
$string['nodataforperiod'] = 'No hay datos disponibles para el periodo seleccionado';
$string['selectcompanyfirst'] = 'Por favor seleccione una compania para ver el reporte';
$string['loadingreport'] = 'Cargando datos del reporte...';

// Privacy.
$string['privacy:metadata'] = 'El plugin Reporte de Uso de Plataforma no almacena ningun dato personal.';

// Navigation.
$string['backtoreport'] = 'Volver al reporte';
$string['viewdetails'] = 'Ver detalles';

// Period labels for charts.
$string['period'] = 'Periodo';
$string['accesscount'] = 'Cantidad de Accesos';
$string['usercount'] = 'Cantidad de Usuarios';

// Excel export strings.
$string['reporttitle'] = 'Reporte de Uso de Plataforma';
$string['daterange'] = 'Rango de Fechas';
$string['generateddate'] = 'Fecha de Generacion';
$string['loginsummary'] = 'Resumen de Ingresos';
$string['shortname'] = 'Nombre Corto';
$string['courses'] = 'Cursos';
$string['activities'] = 'Actividades';
$string['users'] = 'Usuarios';
$string['courseaccessdetails'] = 'Detalles de Acceso a Cursos';
$string['activityaccessdetails'] = 'Detalles de Acceso a Actividades';
$string['avgaccessperuser'] = 'Prom. Accesos/Usuario';
$string['total'] = 'Total';
$string['created'] = 'Creado';
$string['status'] = 'Estado';
$string['active'] = 'Activo';
$string['inactive'] = 'Inactivo';

// Engagement metrics.
$string['completions'] = 'Completados';
$string['completionsmonth'] = 'Completados (30 dias)';
$string['completionsweek'] = 'Completados (7 dias)';
$string['completionstoday'] = 'Completados hoy';
$string['totalcompletions'] = 'Total completados';
$string['completiontrends'] = 'Tendencias de Finalizacion de Cursos';

// Dashboard access metrics.
$string['dashboardusers'] = 'Usuarios del panel (30 dias)';
$string['dashboardweek'] = 'Usuarios del panel (7 dias)';
$string['dashboardtoday'] = 'Hoy';

// Session metrics.
$string['avgsessionduration'] = 'Duracion promedio de sesion';
$string['totalsessions'] = 'Total de sesiones';

// Logout metrics.
$string['logoutsmonth'] = 'Cierres de sesion (30 dias)';
$string['logoutsweek'] = 'Cierres de sesion (7 dias)';
$string['logoutstoday'] = 'Cierres de sesion hoy';

// Daily users.
$string['dailyusers'] = 'Usuarios Diarios (ultimos 10 dias)';
$string['dailyuserstable'] = 'Historial de Usuarios Diarios';

// Dedication metrics.
$string['topdedication'] = 'Cursos con Mayor Dedicacion';
$string['dedicationdetails'] = 'Detalles de Dedicacion';
$string['totaldedication'] = 'Dedicacion Total';
$string['enrolledusers'] = 'Usuarios Matriculados';
$string['dedicationpercent'] = '% de Dedicacion';

// Course report strings.
$string['coursereport'] = 'Reporte de Uso del Curso';
$string['coursereport_desc'] = 'Estadisticas detalladas de uso de este curso, incluyendo actividad de usuarios, participacion y metricas de dedicacion.';
$string['courseenrolledusers'] = 'Usuarios matriculados';
$string['courseactiveusers'] = 'Usuarios activos (curso)';
$string['courseinactiveusers'] = 'Usuarios inactivos (curso)';
$string['courselogins'] = 'Accesos al curso';
$string['coursecompletions'] = 'Finalizaciones del curso';

// Section descriptions.
$string['logintrends_desc'] = 'Tendencias de ingresos diarios y usuarios unicos de los ultimos 30 dias';
$string['usersbyactivity_desc'] = 'Distribucion de usuarios activos vs inactivos (umbral de 30 dias)';
$string['coursetrends_desc'] = 'Tendencias de acceso a cursos durante el periodo seleccionado';
$string['dedication_desc'] = 'Tiempo dedicado por estudiantes en cada curso basado en actividad de sesion';
$string['topcourses_desc'] = 'Cursos ordenados por cantidad de accesos con metricas de dedicacion';
$string['topactivities_desc'] = 'Actividades mas accedidas en todos los cursos';
$string['completiontrends_desc'] = 'Tendencias de finalizacion de cursos de los ultimos 30 dias';

// Daily metrics.
$string['average'] = 'Promedio';
$string['maximum'] = 'Maximo';

// Export descriptions.
$string['export_report_desc'] = 'Reporte de Uso de Plataforma - Analisis integral de la actividad de la plataforma';
$string['export_logins_desc'] = 'Resumen de Ingresos: Total de ingresos y usuarios unicos para hoy, ultimos 7 dias y ultimos 30 dias. Un ingreso se cuenta cada vez que un usuario se autentica exitosamente en la plataforma.';
$string['export_users_desc'] = 'Resumen de Actividad de Usuarios: Total de usuarios registrados, usuarios activos (ingresaron en los ultimos 30 dias) e inactivos. Un usuario activo es aquel que ha accedido a la plataforma en los ultimos 30 dias.';
$string['export_courses_desc'] = 'Detalles de Acceso a Cursos: Cursos ordenados por vistas totales, usuarios unicos que accedieron y tiempo de dedicacion. El conteo de accesos representa las veces que los usuarios vieron la pagina del curso.';
$string['export_activities_desc'] = 'Detalles de Acceso a Actividades: Actividades mas accedidas (tareas, foros, cuestionarios, etc.) con conteos de vistas y participacion de usuarios. Ayuda a identificar que materiales de aprendizaje son mas utilizados.';
$string['export_daily_desc'] = 'Historial de Ingresos Diarios: Desglose dia por dia de ingresos y usuarios unicos. Util para identificar patrones de uso y periodos de mayor actividad.';
$string['export_completions_desc'] = 'Resumen de Finalizacion de Cursos: Cantidad de cursos completados en diferentes periodos. Una finalizacion se registra cuando un usuario cumple todos los criterios de finalizacion del curso.';
$string['export_dedication_desc'] = 'Analisis de Dedicacion a Cursos: Tiempo dedicado por estudiantes en cada curso basado en analisis de sesiones. Una sesion termina cuando el usuario esta inactivo por mas de 1 hora. Muestra tiempo total de dedicacion y distribucion porcentual entre cursos.';
$string['export_dailyusers_desc'] = 'Usuarios Unicos Diarios: Registro historico de conteos de usuarios unicos por dia. Ayuda a rastrear tendencias de participacion y crecimiento de la plataforma.';
$string['generated_by'] = 'Generado por';
$string['filter_company'] = 'Filtro - Compania';
$string['filter_daterange'] = 'Filtro - Rango de Fechas';
$string['metric_explanation'] = 'Explicacion de Metrica';
$string['data_section'] = 'Seccion de Datos';
