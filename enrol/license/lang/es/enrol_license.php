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
 * @package   enrol_license
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['canntenrol'] = 'La inscripción está deshabilitada o inactiva';
$string['customwelcomemessage'] = 'Mensaje de bienvenida personalizado';
$string['defaultrole'] = 'Asignación de rol predeterminado';
$string['defaultrole_desc'] = 'Seleccione el rol que debe asignarse a los usuarios durante la inscripción por licencia';
$string['enrolenddate'] = 'Fecha de finalización';
$string['enrolenddaterror'] = 'La fecha de finalización de inscripción no puede ser anterior a la fecha de inicio';
$string['enrolme'] = 'Haga clic aquí para comenzar este curso';
$string['enrolperiod'] = 'Período de inscripción';
$string['enrolperiod_desc'] = 'Duración predeterminada del período de inscripción (en segundos).';
$string['enrolstartdate'] = 'Fecha de inicio';
$string['groupkey'] = 'Usar claves de inscripción por grupo';
$string['groupkey_desc'] = 'Usar claves de inscripción por grupo de forma predeterminada.';
$string['groupkey_help'] = 'Además de restringir el acceso al curso solo a quienes conocen la clave, el uso de una clave de inscripción por grupo significa que los usuarios se agregan automáticamente al grupo cuando se inscriben en el curso.

Para usar una clave de inscripción por grupo, se debe especificar una clave de inscripción en la configuración del curso, así como la clave de inscripción por grupo en la configuración del grupo.';
$string['licensecrontask'] = 'Tarea programada de inscripción por licencia';
$string['licensenolongervalid'] = 'Su licencia para este curso ya no es válida';
$string['licensenotyetvalid'] = 'Su acceso a este curso estará disponible el {$a}';
$string['license:unenrolself'] = 'El usuario puede darse de baja por sí mismo';
$string['longtimenosee'] = 'Dar de baja inactivos después de';
$string['longtimenosee_help'] = 'Si los usuarios no han accedido a un curso durante mucho tiempo, se les da de baja automáticamente. Este parámetro especifica ese límite de tiempo. Esto es independiente del tiempo de inscripción que establece la licencia misma.';
$string['maxenrolled'] = 'Máximo de usuarios inscritos';
$string['maxenrolled_help'] = 'Especifica el número máximo de usuarios que pueden inscribirse por licencia. 0 significa sin límite.';
$string['maxenrolledreached'] = 'Se ha alcanzado el número máximo de usuarios permitidos para inscribirse por licencia.';
$string['nolicenseinformationfound'] = 'Su cuenta no tiene una licencia válida para acceder a este curso. Si requiere acceso, contacte al administrador de su empresa para obtener una licencia.';
$string['password'] = 'Clave de inscripción';
$string['password_help'] = 'Una clave de inscripción permite restringir el acceso al curso solo a quienes conocen la clave.

Si el campo se deja en blanco, cualquier usuario puede inscribirse en el curso.

Si se especifica una clave de inscripción, cualquier usuario que intente inscribirse en el curso deberá proporcionar la clave. Tenga en cuenta que un usuario solo necesita proporcionar la clave de inscripción UNA VEZ, cuando se inscribe en el curso.';
$string['passwordinvalid'] = 'Clave de inscripción incorrecta, por favor intente nuevamente';
$string['passwordinvalidhint'] = 'La clave de inscripción fue incorrecta, por favor intente nuevamente<br />
(Aquí hay una pista: comienza con \'{$a}\')';
$string['pluginname'] = 'Inscripción por licencia';
$string['pluginname_desc'] = 'El plugin de inscripción por licencia permite a los usuarios obtener acceso a cursos después de que se les asigne una licencia. Internamente, la inscripción se realiza mediante el plugin de inscripción manual, que debe estar habilitado en el mismo curso.';
$string['privacy:metadata'] = 'El plugin de inscripción por licencia solo muestra datos almacenados en otras ubicaciones.';
$string['requirepassword'] = 'Requerir clave de inscripción';
$string['requirepassword_desc'] = 'Requerir clave de inscripción en cursos nuevos e impedir la eliminación de la clave de inscripción de cursos existentes.';
$string['role'] = 'Asignar rol';
$string['license:config'] = 'Configurar instancias de inscripción por licencia';
$string['license:manage'] = 'Gestionar usuarios inscritos';
$string['license:unenrol'] = 'Dar de baja usuarios del curso';
$string['license:unenrollicense'] = 'Dar de baja licencia del curso';
$string['sendcoursewelcomemessage'] = 'Enviar mensaje de bienvenida del curso';
$string['sendcoursewelcomemessage_help'] = 'Si está habilitado, los usuarios reciben un mensaje de bienvenida por correo electrónico cuando se inscriben en un curso por licencia.';
$string['showhint'] = 'Mostrar pista';
$string['showhint_desc'] = 'Mostrar la primera letra de la clave de acceso de invitado.';
$string['status'] = 'Permitir inscripciones por licencia';
$string['status_desc'] = 'Permitir que los usuarios se inscriban por licencia en el curso de forma predeterminada.';
$string['status_help'] = 'Esta configuración determina si un usuario puede inscribirse (y también darse de baja si tiene el permiso apropiado) en el curso.';
$string['unenrollicenseconfirm'] = '¿Realmente desea darse de baja del curso "{$a}"?';
$string['usepasswordpolicy'] = 'Usar política de contraseñas';
$string['usepasswordpolicy_desc'] = 'Usar política de contraseñas estándar para claves de inscripción.';
$string['welcometocourse'] = 'Bienvenido a {$a}';
$string['welcometocoursetext'] = '¡Bienvenido a {$a->coursename}!

Si aún no lo ha hecho, debe editar su página de perfil para que podamos conocerlo mejor:

  {$a->profileurl}';
