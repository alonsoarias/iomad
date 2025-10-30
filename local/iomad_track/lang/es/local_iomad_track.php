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
 * @package   local_iomad_track
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Seguimiento de finalización de IOMAD';
$string['privacy:metadata'] = "El plugin 'Seguimiento de finalización de IOMAD' solo muestra datos almacenados en otras ubicaciones.";
$string['privacy:metadata:local_iomad_track:id'] = 'ID de seguimiento local de IOMAD';
$string['privacy:metadata:local_iomad_track:courseid'] = 'ID del curso';
$string['privacy:metadata:local_iomad_track:coursename'] = 'Nombre del curso';
$string['privacy:metadata:local_iomad_track:userid'] = 'ID del usuario';
$string['privacy:metadata:local_iomad_track:companyid'] = 'ID de la empresa del usuario';
$string['privacy:metadata:local_iomad_track:timecompleted'] = 'Momento de finalización del curso';
$string['privacy:metadata:local_iomad_track:timeenrolled'] = 'Momento de matriculación en el curso';
$string['privacy:metadata:local_iomad_track:timestarted'] = 'Momento de inicio del curso';
$string['privacy:metadata:local_iomad_track:finalscore'] = 'Puntuación final del curso';
$string['privacy:metadata:local_iomad_track:licenseid'] = 'ID de la licencia';
$string['privacy:metadata:local_iomad_track:licensename'] = 'Nombre de la licencia';
$string['privacy:metadata:local_iomad_track:licenseallocated'] = 'Marca de tiempo Unix en la que se asignó la licencia';
$string['privacy:metadata:local_iomad_track:modifiedtime'] = 'Momento de modificación del registro';
$string['privacy:metadata:local_iomad_track'] = 'Información de seguimiento de usuarios de IOMAD';
$string['privacy:metadata:local_iomad_track_certs:id'] = 'ID del registro del certificado de seguimiento local de IOMAD';
$string['privacy:metadata:local_iomad_track_certs:trackid'] = 'ID de seguimiento del certificado';
$string['privacy:metadata:local_iomad_track_certs:filename'] = 'Nombre de archivo del certificado';
$string['privacy:metadata:local_iomad_track_certs'] = 'Información del certificado de seguimiento local de IOMAD';
$string['fixtracklicensetask'] = 'Tarea ad hoc para corregir los detalles de licencias del seguimiento de IOMAD';
$string['iomad_track:importfrommoodle'] = 'Importar información de finalización desde las tablas de Moodle';
$string['importcompletionsfrommoodle'] = 'Importar la información de finalización almacenada desde las tablas de Moodle';
$string['importcompletionsfrommoodlefull'] = 'Esto ejecutará una tarea ad hoc para importar toda la información de finalización desde Moodle a las tablas de informes de IOMAD.';
$string['importcompletionsfrommoodlefullwitherrors'] = 'Esto ejecutará una tarea ad hoc para importar PARTE de la información de finalización desde Moodle a las tablas de informes de IOMAD. No todos los cursos tienen la finalización habilitada o criterios configurados y se omitirá su información. Si desea saber cuáles son esos cursos, utilice el enlace de comprobación en la página anterior.';
$string['importmoodlecompletioninformation'] = 'Tarea ad hoc para importar la información de finalización desde las tablas de Moodle';
$string['fixcertificatetask'] = 'Cambiar el contexto del certificado al contexto del usuario';
$string['fixenrolleddatetask'] = "Tarea ad hoc para actualizar la información de finalización almacenada con la marca de tiempo 'timecreated' de la matrícula cuando no se haya establecido.";
$string['fixcourseclearedtask'] = "Tarea ad hoc para actualizar el campo 'coursecleared' en los registros de finalización almacenados";
$string['fixtracklicensetask'] = 'Tarea ad hoc para corregir la información de licencia en los registros almacenados';
$string['savecertificatetask'] = 'Tarea ad hoc para guardar un certificado de un usuario al completar un curso';
$string['importcompletionrecords'] = 'Importar registros de finalización';
$string['uploadcompletionresult'] = 'Resultado de la carga del archivo de finalización';
$string['completionimportfromfile'] = 'Importación de finalización desde un archivo';
$string['importcompletionsfromfile'] = 'Importar información de finalización desde un archivo';
$string['courseswithoutcompletionenabledcouunt'] = 'Número de cursos que no tienen habilitada la finalización = {$a}';
$string['courseswithoutcompletioncriteriacouunt'] = 'Número de cursos que no tienen criterios de finalización = {$a}';
$string['checkcoursestatusmoodle'] = 'Comprobar la configuración del curso para la importación';
