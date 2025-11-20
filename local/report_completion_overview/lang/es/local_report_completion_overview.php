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
 * Strings for component 'local_report_completion_overview', language 'es'
 *
 * @translator Alonso Arias <soporte@orioncloud.com.co>
 */

$string['pluginname'] = 'Informe de Resumen de Finalización';
$string['privacy:metadata:local_report_user_lic_allocs:id'] = 'ID de registro de asignación de licencia de usuario del informe local';
$string['privacy:metadata:local_report_user_lic_allocs:courseid'] = 'ID del curso';
$string['privacy:metadata:local_report_user_lic_allocs:action'] = 'Acción de asignación';
$string['privacy:metadata:local_report_user_lic_allocs:userid'] = 'ID de usuario';
$string['privacy:metadata:local_report_user_lic_allocs:licenseid'] = 'ID de licencia';
$string['privacy:metadata:local_report_user_lic_allocs:issuedate'] = 'Marca de tiempo Unix de emisión de licencia';
$string['privacy:metadata:local_report_user_lic_allocs'] = 'Información de usuario de asignación de licencia de usuario del informe local';
$string['hideenrolledonly'] = 'Destacar disponible';
$string['hideexpiry'] = 'Destacar expiración';
$string['report_completion_overview:view'] = 'Ver informe de resumen de finalización de curso';
$string['showenrolled'] = 'Destacar solo con matrículas';
$string['showenrolledonly'] = 'Destacar cursos con matrículas registradas';
$string['showenrolledonly_help'] = 'Si esta opción está marcada, solo se mostrarán los cursos que tienen o han tenido matrículas registradas.';
$string['showexpiry'] = 'Destacar todo';
$string['showexpiryonly'] = 'Destacar solo cursos con duración válida';
$string['showexpiryonly_help'] = 'Si esta opción está marcada, los cursos que no tengan una duración válida no se mostrarán en color en el resumen gráfico por defecto.';
$string['showfulldetail'] = 'Mostrar detalles completos de finalización';
$string['showfulldetail_help'] = 'Si esta opción está marcada, se mostrará toda la información de finalización, de lo contrario solo se mostrarán las fechas de finalización y expiración.';
$string['warningduration'] = 'Límite de advertencia de expiración';
$string['warningdurationcompany'] = 'Límite de advertencia de expiración específico de la empresa';
$string['warningduration_help'] = 'Este es el valor de tiempo antes de que un curso expire donde el informe mostrará los colores de advertencia de expiración en lugar de los colores OK.';
$string['coursesummary'] = 'Matriculado: {$a->enrolled}
Iniciado: {$a->timestarted}
Completado: {$a->timecompleted}
Expira: {$a->timeexpires}
Calificación: {$a->finalscore}';
$string['coursesummary_extra_indate'] = 'Matriculado: {$a->enrolled}
Iniciado: {$a->timestarted}
Completado: {$a->timecompleted}
Expira: {$a->timeexpires}
Calificación: {$a->finalscore}
Última finalización: {$a->lastcompleted}
Expira: {$a->timeexpired}';
$string['coursesummary_extra_outdate'] = 'Matriculado: {$a->enrolled}
Iniciado: {$a->timestarted}
Completado: {$a->timecompleted}
Expira: {$a->timeexpires}
Calificación: {$a->finalscore}
Última finalización: {$a->lastcompleted}
Expirado: {$a->timeexpired}';
$string['coursesummary_expired'] = 'Matriculado: {$a->enrolled}
Iniciado: {$a->timestarted}
Expirado: {$a->timeexpires}
Calificación: {$a->finalscore}';
$string['coursesummary_noexpiry'] = 'Matriculado: {$a->enrolled}
Iniciado: {$a->timestarted}
Completado: {$a->timecompleted}
Calificación: {$a->finalscore}';
$string['coursesummary_nograde'] = 'Matriculado: {$a->enrolled}
Iniciado: {$a->timestarted}
Completado: {$a->timecompleted}
Expira: {$a->timeexpires}
Resultado: Aprobado';
$string['coursesummary_nograde_noexpiry'] = 'Matriculado: {$a->enrolled}
Iniciado: {$a->timestarted}
Completado: {$a->timecompleted}
Resultado: Aprobado';
$string['coursesummary_partial'] = 'Completado: {$a->timecompleted}
Expira: {$a->timeexpires}';
$string['coursesummary_partial_extra_indate'] = 'Completado: {$a->timecompleted}
Expira: {$a->timeexpires}
Última finalización: {$a->lastcompleted}
Expira: {$a->timeexpired}';
$string['coursesummary_partial_extra_outdate'] = 'Completado: {$a->timecompleted}
Expira: {$a->timeexpires}
Última finalización: {$a->lastcompleted}
Expirado: {$a->timeexpired}';
$string['report_completion_overview_title'] = 'Informe de resumen de finalización';
$string['notcompleted'] = 'En progreso';
$string['notcompleted-expiring'] = 'En progreso (Por vencer)';
$string['notcompleted-indate'] = 'En progreso (OK)';
$string['notcompleted-outdate'] = 'En progreso (Expirado)';
$string['notenrolled']  = 'No matriculado';
$string['notenrolled-expiring']  = 'No matriculado (Por vencer)';
$string['notenrolled-indate']  = 'No matriculado (OK)';
$string['notenrolled-outdate']  = 'No matriculado (Expirado)';
$string['indate'] = 'OK';
$string['expiring'] = 'Por vencer';
$string['expired'] = 'Expirado';
$string['coursestatus'] = 'Estado de {$a}';
$string['coursecompletion'] = 'Finalización de {$a}';
$string['courseexpiry'] = 'Expiración de {$a}';
$string['bycourses'] = 'Ver por curso';
$string['byusers'] = 'Ver por usuario';
