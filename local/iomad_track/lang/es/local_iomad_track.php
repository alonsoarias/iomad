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
 * @package   local_iomad_signup
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Strings for component 'local_iomad_track', language 'es'
 *
 * @translator Alonso Arias <soporte@orioncloud.com.co>
 */

$string['pluginname'] = 'Seguimiento de finalización de IOMAD';
$string['privacy:metadata'] = 'El plugin \'Seguimiento de finalización local de IOMAD\' solo muestra datos almacenados en otras ubicaciones.';
$string['privacy:metadata:local_iomad_track:id'] = 'ID de seguimiento de IOMAD local';
$string['privacy:metadata:local_iomad_track:courseid'] = 'ID de curso';
$string['privacy:metadata:local_iomad_track:coursename'] = 'Nombre del curso.';
$string['privacy:metadata:local_iomad_track:userid'] = 'ID de usuario';
$string['privacy:metadata:local_iomad_track:companyid'] = 'ID de empresa del usuario';
$string['privacy:metadata:local_iomad_track:timecompleted'] = 'Tiempo de finalización del curso';
$string['privacy:metadata:local_iomad_track:timeenrolled'] = 'Tiempo de inscripción al curso';
$string['privacy:metadata:local_iomad_track:timestarted'] = 'Tiempo de inicio del curso';
$string['privacy:metadata:local_iomad_track:finalscore'] = 'Puntuación final del curso';
$string['privacy:metadata:local_iomad_track:licenseid'] = 'ID de licencia';
$string['privacy:metadata:local_iomad_track:licensename'] = 'Nombre de licencia';
$string['privacy:metadata:local_iomad_track:licenseallocated'] = 'Marca de tiempo Unix de cuando se asignó la licencia';
$string['privacy:metadata:local_iomad_track:modifiedtime'] = 'Tiempo de modificación del registro';
$string['privacy:metadata:local_iomad_track'] = 'Información de usuario de seguimiento de IOMAD local';
$string['privacy:metadata:local_iomad_track_certs:id'] = 'ID de registro de certificado de seguimiento de IOMAD local';
$string['privacy:metadata:local_iomad_track_certs:trackid'] = 'ID de seguimiento de certificado';
$string['privacy:metadata:local_iomad_track_certs:filename'] = 'Nombre de archivo de certificado';
$string['privacy:metadata:local_iomad_track_certs'] = 'Información de certificado de seguimiento de iomad local';
$string['fixtracklicensetask'] = 'Tarea ad-hoc de corrección de detalles de seguimiento de licencia de IOMAD track';
$string['iomad_track:importfrommoodle'] = 'Importar información de finalización desde tablas de Moodle';
$string['importcompletionsfrommoodle'] = 'Importar información de finalización almacenada desde tablas de Moodle';
$string['importcompletionsfrommoodlefull'] = 'Esto ejecutará una tarea ad-hoc para importar toda la información de finalización de Moodle a las tablas de informes de IOMAD.';
$string['importcompletionsfrommoodlefullwitherrors'] = 'Esto ejecutará una tarea ad-hoc para importar PARTE de la información de finalización de Moodle a las tablas de informes de IOMAD. No todos los cursos tienen la finalización habilitada o criterios configurados y su información se omitirá. Si desea saber cuáles son estos cursos, use el enlace de verificación en la página anterior.';
$string['importmoodlecompletioninformation'] = 'Tarea ad-hoc para importar información de finalización desde tablas de Moodle';
$string['fixcertificatetask'] = 'Cambiar contexto de certificado a contexto de usuario';
$string['fixenrolleddatetask'] = 'Tarea ad-hoc para actualizar la información de finalización almacenada para usar la marca de tiempo \'timecreated\' de inscripción donde esto no esté ya establecido.';
$string['fixcourseclearedtask'] = 'Tarea ad-hoc para actualizar el campo \'coursecleared\' en los registros de finalización almacenados';
$string['fixtracklicensetask'] = 'Tarea ad-hoc para corregir la información de licencia de registros almacenados';
$string['savecertificatetask'] = 'Tarea ad-hoc para almacenar un certificado para un usuario al finalizar el curso';
$string['importcompletionrecords'] = 'Importar registros de finalización';
$string['uploadcompletionresult'] = 'Resultado de carga de archivo de finalización';
$string['completionimportfromfile'] = 'Importación de finalización desde archivo';
$string['importcompletionsfromfile'] = 'Importar información de finalización desde archivo';
$string['courseswithoutcompletionenabledcouunt'] = 'Número de cursos que no tienen la finalización habilitada = {$a}';
$string['courseswithoutcompletioncriteriacouunt'] ='Número de cursos que no tienen criterios de finalización = {$a}';
$string['checkcoursestatusmoodle'] = 'Verificar configuración de cursos para importación';
