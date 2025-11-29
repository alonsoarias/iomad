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
$string['activeusers'] = 'Usuarios activos';
$string['inactiveusers'] = 'Usuarios inactivos';

// Charts titles.
$string['logintrends'] = 'Tendencias de Ingreso';
$string['dailylogins'] = 'Ingresos Diarios';
$string['weeklylogins'] = 'Ingresos Semanales';
$string['monthlylogins'] = 'Ingresos Mensuales';
$string['usersbyactivity'] = 'Usuarios por Estado de Actividad';
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

// Metricas de engagement.
$string['completions'] = 'Finalizaciones';
$string['completionsmonth'] = 'Finalizaciones (30 dias)';
$string['completionsweek'] = 'Finalizaciones (7 dias)';
$string['completionstoday'] = 'Finalizaciones hoy';
$string['totalcompletions'] = 'Total de finalizaciones';
$string['completiontrends'] = 'Tendencias de Finalizacion de Cursos';

// Metricas de acceso al tablero.
$string['dashboardusers'] = 'Usuarios del tablero (30 dias)';
$string['dashboardweek'] = 'Usuarios del tablero (7 dias)';
$string['dashboardtoday'] = 'Hoy';

// Metricas de sesion.
$string['avgsessionduration'] = 'Duracion promedio de sesion';
$string['totalsessions'] = 'Total de sesiones';

// Metricas de cierre de sesion.
$string['logoutsmonth'] = 'Cierres de sesion (30 dias)';
$string['logoutsweek'] = 'Cierres de sesion (7 dias)';
$string['logoutstoday'] = 'Cierres de sesion hoy';

// Metricas de duracion de sesion (ALTA PRIORIDAD).
$string['sessionduration'] = 'Duracion de Sesion';
$string['avgsessionminutes'] = 'Sesion promedio (minutos)';
$string['sessionswithlogout'] = 'Sesiones con cierre';
$string['estimatedsessions'] = 'Sesiones estimadas';
$string['sessiontracking'] = 'Seguimiento de Sesiones';

// Metricas de seguridad - Logins fallidos (PRIORIDAD MEDIA).
$string['securitymetrics'] = 'Metricas de Seguridad';
$string['failedlogins'] = 'Intentos de Inicio de Sesion Fallidos';
$string['failedloginstoday'] = 'Inicios fallidos hoy';
$string['failedloginsweek'] = 'Inicios fallidos (7 dias)';
$string['failedloginsmonth'] = 'Inicios fallidos (30 dias)';
$string['failedloginsbyreason'] = 'Inicios fallidos por razon';

// Razones de inicio de sesion fallido.
$string['failedloginreason1'] = 'El usuario no existe';
$string['failedloginreason2'] = 'Usuario suspendido';
$string['failedloginreason3'] = 'Contrasena incorrecta';
$string['failedloginreason4'] = 'Usuario bloqueado';
$string['failedloginreason5'] = 'Usuario no autorizado';

// Sesiones diarias.
$string['dailysessions'] = 'Sesiones Diarias';
$string['sessions'] = 'Sesiones';
$string['logouts'] = 'Cierres de sesion';
