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
 * @package   local_platform_access
 * @copyright 2024 IOMAD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Generador de Accesos a Plataforma';
$string['platform_access:generate'] = 'Generar registros de acceso a plataforma';
$string['platform_access:view'] = 'Ver generador de accesos a plataforma';
$string['generateaccess'] = 'Generar Registros de Acceso';
$string['generateaccessdesc'] = 'Esta herramienta genera registros de inicio de sesion, acceso a cursos y actividades para los usuarios de la plataforma. Seleccione una compania para generar registros de acceso para todos sus usuarios y los cursos en los que estan inscritos.';
$string['generatelogins'] = 'Generar registros de inicio de sesion';
$string['generatecourseaccess'] = 'Generar registros de acceso a cursos';
$string['datefrom'] = 'Fecha desde';
$string['dateto'] = 'Fecha hasta';
$string['generatebutton'] = 'Generar Registros de Acceso';
$string['generating'] = 'Generando registros de acceso...';
$string['success'] = 'Registros de acceso generados exitosamente';
$string['error'] = 'Error al generar registros de acceso';
$string['totalusers'] = 'Total de usuarios procesados';
$string['totallogins'] = 'Total de registros de inicio de sesion generados';
$string['totalcourseaccess'] = 'Total de registros de acceso a cursos generados';
$string['confirmgenerate'] = 'Esta seguro de que desea generar registros de acceso? Esto insertara registros en la base de datos.';
$string['loginsperuser'] = 'Inicios de sesion por usuario';
$string['courseaccessperuser'] = 'Accesos a cursos por usuario';
$string['activityaccesspercourse'] = 'Accesos a actividades por curso';
$string['randomize'] = 'Aleatorizar marcas de tiempo';
$string['randomizedesc'] = 'Si esta habilitado, las marcas de tiempo se distribuiran aleatoriamente dentro del rango de fechas especificado.';
$string['includeadmins'] = 'Incluir usuarios administradores';
$string['includeguests'] = 'Incluir usuarios invitados';
$string['onlyactiveusers'] = 'Solo usuarios activos (no suspendidos/eliminados)';
$string['processinguser'] = 'Procesando usuario: {$a}';
$string['nousers'] = 'No se encontraron usuarios que coincidan con los criterios. Asegurese de que la compania seleccionada tenga usuarios asignados.';
$string['nocourses'] = 'No se encontraron cursos para la compania seleccionada';
$string['summary'] = 'Resumen de Generacion';
$string['usersprocessed'] = 'Usuarios procesados';
$string['loginsgenerated'] = 'Registros de inicio de sesion generados';
$string['courseaccessgenerated'] = 'Registros de acceso a cursos generados';
$string['activityaccessgenerated'] = 'Registros de acceso a actividades generados';
$string['lastaccessupdated'] = 'Ultimo acceso de usuario actualizado';
$string['timecompleted'] = 'Tiempo completado (segundos)';
$string['specifycourses'] = 'Cursos especificos (dejar vacio para todos)';
$string['specifyusers'] = 'Usuarios especificos (dejar vacio para todos)';
$string['accesstype'] = 'Tipo de acceso a generar';
$string['loginonly'] = 'Solo registros de inicio de sesion';
$string['courseonly'] = 'Solo registros de acceso a cursos';
$string['activityonly'] = 'Solo registros de acceso a actividades';
$string['both'] = 'Inicio de sesion y acceso a cursos';
$string['all'] = 'Todas las companias';
$string['all_access'] = 'Todo (inicio de sesion, cursos y actividades)';
$string['company'] = 'Compania';
$string['company_help'] = 'Seleccione una compania para generar registros de acceso para todos sus usuarios. Si selecciona "Todas las companias", se generaran registros para usuarios de todas las companias del sistema.';
$string['privacy:metadata'] = 'El plugin Generador de Accesos a Plataforma no almacena ningun dato personal.';
$string['randomize_help'] = 'Cuando esta habilitado, las marcas de tiempo para los registros de inicio de sesion y acceso a cursos se distribuiran aleatoriamente dentro del rango de fechas especificado. Cuando esta deshabilitado, todos los registros usaran la fecha final.';
$string['usersettings'] = 'Configuracion de Usuario';
$string['updateusercreated'] = 'Actualizar fecha de creacion de usuario';
$string['updateusercreated_help'] = 'Si esta habilitado, la fecha de creacion del usuario (timecreated) se actualizara a la fecha especificada. Esto afecta la tabla user y la tabla local_report_user_logins si existe.';
$string['usercreateddate'] = 'Fecha de creacion de usuario';
$string['daterange'] = 'Rango de Fechas';
$string['accesscounts'] = 'Cantidad de Accesos (Rangos Aleatorios)';
$string['minmaxerror'] = 'El valor minimo debe ser menor o igual al valor maximo';
$string['usersupdated'] = 'Fecha de creacion de usuarios actualizada';
$string['cleanbeforegenerate'] = 'Limpiar registros existentes antes de generar';
$string['cleanbeforegenerate_help'] = 'Si esta habilitado, todos los registros de acceso existentes (inicios de sesion, vistas de cursos, vistas de actividades) para los usuarios seleccionados seran eliminados antes de generar nuevos registros. Esto incluye restablecer los campos de acceso del usuario (firstaccess, lastaccess, lastlogin, currentlogin).';
$string['recordsdeleted'] = 'Registros existentes eliminados';
$string['userswithoutenrollments'] = 'Usuarios sin matriculas en cursos';

// Advanced events.
$string['advancedevents'] = 'Eventos Avanzados';
$string['generatedashboard'] = 'Generar registros de acceso al panel';
$string['generatedashboard_help'] = 'Si esta habilitado, se generaran eventos de acceso al panel/area personal despues de cada inicio de sesion (con 70% de probabilidad).';
$string['generatelogouts'] = 'Generar registros de cierre de sesion';
$string['generatelogouts_help'] = 'Si esta habilitado, se generaran eventos de cierre de sesion para el 50% de las sesiones de inicio con una duracion realista (5 min - 2 horas).';
$string['generatecompletions'] = 'Generar registros de finalizacion de cursos';
$string['generatecompletions_help'] = 'Si esta habilitado, se generaran registros de finalizacion de cursos para un porcentaje de cursos basado en el rango de porcentaje de finalizacion especificado a continuacion.';
$string['completionpercent'] = 'Porcentaje de finalizacion';
$string['percentageerror'] = 'El porcentaje debe estar entre 0 y 100';
$string['dashboardaccessgenerated'] = 'Registros de acceso al panel generados';
$string['logoutsgenerated'] = 'Registros de cierre de sesion generados';
$string['completionsgenerated'] = 'Finalizaciones de cursos generadas';
