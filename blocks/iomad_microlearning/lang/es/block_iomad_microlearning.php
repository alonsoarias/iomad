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
 * Strings for component 'block_iomad_microlearning', language 'es'.
 *
 * @package   block_iomad_microlearning
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 o posterior
 */

defined('MOODLE_INTERNAL') || die();

$string['actions'] = 'Comportamiento';
$string['active'] = 'Activo';
$string['active_help'] = 'Si el hilo no está activo, no se enviarán mensajes a los usuarios y no será visible en su panel.';
$string['blocktitle'] = 'Hilos de microaprendizaje';
$string['bulkassigngroups'] = 'Asignar grupos de hilos por CSV';
$string['clonethread'] = 'Clonar hilo';
$string['clonethreadcheckfull'] = '¿Está seguro de que desea clonar el hilo {$a} y todos los nuggets asociados en un hilo nuevo?  Esto no copia a ningún usuario asignado actualmente.';
$string['copy'] = '(Copiar)';
$string['cmid'] = 'ID del módulo del curso';
$string['cmid_help'] = 'Número de identificación del módulo del curso en este campo para la sección única del curso.  Debe definir esto o el valor de ID de sección.';
$string['cmidalreadyinuse'] = 'El ID del módulo del curso ya está en uso';
$string['company_threads_for'] = 'Hilos de microaprendizaje para empresa {$a}';
$string['company_users_for'] = 'Usuarios del hilo de microaprendizaje {$a}';
$string['creategroup'] = 'Crear nuevo grupo';
$string['crontask'] = 'Cron de microaprendizaje IOMAD';
$string['defaultdue'] = 'Vencimiento después';
$string['defaultdue_help'] = 'Este es el momento después del cual vence la pepita programada.  Se puede sobrescribir cambiando el cronograma del hilo.';
$string['deletegroup'] = 'Eliminar grupo {$a}';
$string['deletegroupcheckfull'] = '¿Quieres eliminar el grupo {$a}?  Esto también eliminará a los usuarios asociados a este grupo.';
$string['deletenugget'] = 'eliminar pepita';
$string['deletenuggetcheckfull'] = '¿Está seguro de que desea eliminar la pepita {$a}?';
$string['deletethread'] = 'Eliminar hilo';
$string['deletethreadcheckfull'] = '¿Está seguro de que desea eliminar por completo el hilo {$a} y todos los nuggets y usuarios asociados?';
$string['duedate'] = 'Fecha de vencimiento';
$string['duedatebeforescheduledate'] = 'La fecha de vencimiento es anterior a la fecha programada.';
$string['editgroup'] = 'Editar grupo';
$string['editnugget'] = 'Editar pepita';
$string['editthread'] = 'Editar hilo';
$string['erroredgroups'] = 'Asignaciones de grupo con errores';
$string['group'] = 'Grupo de hilos';
$string['group_help'] = 'Este es el grupo dentro del hilo de microaprendizaje al que se asignará el usuario.';
$string['groupcreatedok'] = 'El grupo ha sido creado exitosamente.';
$string['groupdeletedok'] = 'El grupo se ha eliminado correctamente.';
$string['groupupdatedok'] = 'El grupo se ha actualizado correctamente.';
$string['halt_until_fulfilled'] = 'Detener mensajes hasta que se completen';
$string['halt_until_fulfilled_help'] = 'Establezca esto en verdadero si desea dejar de enviar mensajes hasta que se complete el pepita anterior.';
$string['importgroupsfromfile'] = 'Importar asignaciones de grupos de usuarios de subprocesos';
$string['importthread'] = 'Importar hilo';
$string['importthreadcheckfull'] = '¿Está seguro de que desea importar el hilo {$a} y todos los nuggets asociados a un nuevo hilo en esta empresa?  Esto no copia a ningún usuario asignado actualmente.';
$string['importusergroups'] = 'Importar grupos de usuarios de hilos';
$string['incorrecturl'] = 'La URL especificada no está dentro de este sitio.';
$string['interval'] = 'Intervalo de liberación';
$string['interval_help'] = 'Este es el intervalo predeterminado entre las fechas programadas para cada pepita';
$string['ibnalidthreadid'] = 'El hilo que buscas no existe.';
$string['iomad_microlearning:addinstance'] = 'Agregar un bloque de microaprendizaje';
$string['iomad_microlearning:assign_threads'] = 'Asignar un usuario a un hilo de microaprendizaje';
$string['iomad_microlearning:importgroupfromcsv'] = 'Asignar grupos de hilos a usuarios mediante CSV';
$string['iomad_microlearning:manage_groups'] = 'Administrar grupos de hilos';
$string['iomad_microlearning:edit_nuggets'] = 'Editar pepitas de microaprendizaje';
$string['iomad_microlearning:edit_threads'] = 'Editar hilos de microaprendizaje';
$string['iomad_microlearning:import_threads'] = 'Importar hilos de microaprendizaje';
$string['iomad_microlearning:myaddinstance'] = 'Agregar un bloque de microaprendizaje a mi panel';
$string['iomad_microlearning:thread_clone'] = 'Clonar un hilo de microaprendizaje';
$string['iomad_microlearning:thread_delete'] = 'Eliminar un hilo de microaprendizaje';
$string['iomad_microlearning:thread_view'] = 'Ver hilos de microaprendizaje';
$string['iomad_microlearning:view'] = 'Ver microaprendizaje IOMAD';
$string['learninggroups'] = 'Administrar grupos de hilos';
$string['learningnuggets'] = 'Administrar pepitas';
$string['learningschedules'] = 'Gestionar horarios';
$string['learningusers'] = 'Administrar usuarios del hilo';
$string['message_preset'] = 'Enviar mensaje después';
$string['message_preset_help'] = 'Introduzca un retraso de tiempo utilizando las unidades proporcionadas después del cual enviar el mensaje.';
$string['message_time'] = 'enviar mensaje hora';
$string['message_time_help'] = 'Ingrese una hora programada a la que se enviará el mensaje.';
$string['microlearning'] = 'Microaprendizaje';
$string['microlearninglinkexpires'] = 'Número de días después de que expire el enlace del correo electrónico';
$string['microlearninglinkexpires_help'] = 'Esta es la cantidad de días después de los cuales el enlace de microaprendizaje enviado por correo electrónico al usuario caducará y el usuario deberá iniciar sesión mediante el proceso normal.';
$string['missingname'] = 'Falta el nombre de la pepita';
$string['missingsectionorcmid'] = 'Ingrese una sección del curso o un ID de módulo del curso';
$string['namehelp'] = 'nombre del grupo';
$string['namehelp_help'] = 'Este es el nombre del grupo utilizado en el hilo de microaprendizaje.  Los nombres son únicos dentro de los hilos, pero se pueden reutilizar en varios hilos.';
$string['nameinuse'] = 'El nombre ya está en uso';
$string['nolearningthreads'] = 'No hay hilos de microaprendizaje.';
$string['nonuggets'] = 'No hay pepitas creadas para este hilo.';
$string['nugget'] = 'pepita de microaprendizaje';
$string['nuggetcreated'] = 'Se creó una pepita de microaprendizaje';
$string['nuggetcreatedok'] = 'Nugget creado correctamente';
$string['nuggetcupdatedok'] = 'Nugget actualizado correctamente';
$string['nuggetdeleted'] = 'Se eliminó la pepita de microaprendizaje';
$string['nuggetmoved'] = 'Se movió el pedido de pepitas de microaprendizaje';
$string['nuggetname'] = 'Nombre de la pepita';
$string['nuggetname_help'] = 'Elija un nombre único para la pepita de aprendizaje';
$string['nuggetorder'] = 'Orden';
$string['nuggets'] = 'Pepitas de microaprendizaje';
$string['nuggetupdated'] = 'Nugget de microaprendizaje actualizado';
$string['pluginname'] = 'Hilos de microaprendizaje de IOMAD';
$string['privacy:metadata'] = 'El bloque IOMAD Microlearning solo muestra datos almacenados en otras ubicaciones.';
$string['privacy:metadata:microlearning_thread_user'] = 'Información de datos de usuario del hilo de microaprendizaje. No se guardan datos personales.';
$string['privacy:metadata:microlearning_thread_user:id'] = 'ID de registro del hilo de microaprendizaje';
$string['privacy:metadata:microlearning_thread_user:userid'] = 'ID de usuario del hilo de microaprendizaje';
$string['privacy:metadata:microlearning_thread_user:threadid'] = 'ID del hilo de microaprendizaje';
$string['privacy:metadata:microlearning_thread_user:nuggetid'] = 'ID de pepita de hilo de microaprendizaje';
$string['privacy:metadata:microlearning_thread_user:groupid'] = 'ID del grupo de hilos de microaprendizaje';
$string['privacy:metadata:microlearning_thread_user:schedule_date'] = 'Fecha programada del hilo de microaprendizaje';
$string['privacy:metadata:microlearning_thread_user:due_date'] = 'Fecha de vencimiento del hilo de microaprendizaje';
$string['privacy:metadata:microlearning_thread_user:reminder1_date'] = 'Fecha del primer recordatorio del hilo de microaprendizaje';
$string['privacy:metadata:microlearning_thread_user:reminder2_date'] = 'Segunda fecha de recordatorio del hilo de microaprendizaje';
$string['privacy:metadata:microlearning_thread_user:messagetime'] = 'Tiempo del hilo de microaprendizaje para enviar mensajes después';
$string['privacy:metadata:microlearning_thread_user:message_delivered'] = 'Indicador de mensaje entregado del hilo de microaprendizaje';
$string['privacy:metadata:microlearning_thread_user:reminder1_delivered'] = 'Indicador de primer recordatorio entregado del hilo de microaprendizaje';
$string['privacy:metadata:microlearning_thread_user:reminder2_delivered'] = 'Indicador de segundo recordatorio entregado del hilo de microaprendizaje';
$string['privacy:metadata:microlearning_thread_user:timecompleted'] = 'Tiempo de pepita de hilo de microaprendizaje completado';
$string['privacy:metadata:microlearning_thread_user:accesskey'] = 'Clave de acceso al correo electrónico del hilo de microaprendizaje';
$string['privacy:metadata:microlearning_thread_user:timecreated'] = 'Tiempo de hilo de microaprendizaje creado';
$string['reminder1'] = 'Primer recordatorio';
$string['reminder1_help'] = 'Tiempo después del cual se enviará el primer recordatorio si la pepita no está marcada como completa.';
$string['reminder2'] = 'Segundo recordatorio';
$string['reminder2_help'] = 'Tiempo después del cual se enviará el segundo recordatorio si la pepita no está marcada como completa.';
$string['reminderdatebeforescheduledate'] = 'La fecha del recordatorio es anterior a la fecha programada.';
$string['reminderdatesoutoforder'] = 'Las fechas de recordatorio están desordenadas.';
$string['resetschedule'] = 'Restablecer horario';
$string['resetschedulecheckfull'] = '¿Quieres restablecer completamente el horario de {$a}?';
$string['scheduledate'] = 'Fecha programada';
$string['scheduleoutoforder'] = 'Las fechas programadas están fuera de orden.';
$string['scheduletype'] = 'Tipo de horario';
$string['scheduletype_help'] = 'Esto controla la fecha de inicio que obtendrá el usuario asignado.  Estándar significa que se agregan según el cronograma definido. Comenzar hoy significa que estarán programados para iniciar el hilo hoy.  Iniciar en la siguiente fecha programada iniciará al usuario en la siguiente fecha programada definida por el hilo actual.';
$string['sectionid'] = 'ID de la sección del curso';
$string['sectionid_help'] = 'Ingrese el número de identificación de la sección del curso en este campo para la sección única del curso.  Debe definir esto o el valor CMID.';
$string['sectionidalreadyinuse'] = 'El ID de sección ya está en uso';
$string['selectthread'] = 'Seleccionar hilo de microaprendizaje';
$string['send_message'] = 'enviar mensaje';
$string['send_message_help'] = 'Configúrelo en verdadero si desea que se envíen correos electrónicos a los usuarios para programar nuggets o recordatorios para completar.';
$string['send_reminder'] = 'Enviar recordatorio';
$string['send_reminder_help'] = 'Establezca esto en verdadero si desea enviar correos electrónicos de recordatorio a los usuarios asignados.';
$string['standard'] = 'Estándar';
$string['startdate'] = 'Fecha de inicio';
$string['startdate_help'] = 'La fecha en la que se programará el hilo de microaprendizaje';
$string['startnextscheduled'] = 'Iniciar el siguiente día programado';
$string['starttoday'] = 'Empieza hoy';
$string['threadcreated'] = 'Hilo de microaprendizaje creado';
$string['threadcreatedok'] = 'Hilo creado correctamente.';
$string['threaddeleted'] = 'Hilo de microaprendizaje eliminado';
$string['threadname'] = 'Nombre del hilo';
$string['threadname_help'] = 'El nombre del hilo de microaprendizaje.';
$string['threads'] = 'Hilos de microaprendizaje';
$string['threadschedule'] = 'Horario del hilo';
$string['threadscheduleresetok'] = 'Restablecer el cronograma del hilo OK';
$string['threadscheduleupdatedok'] = 'Calendario de hilos actualizado OK';
$string['threadscheduleupdated'] = 'Calendario de hilos de microaprendizaje actualizado';
$string['threadupdated'] = 'Hilo de microaprendizaje actualizado';
$string['threadupdatedok'] = 'Hilo actualizado OK';
$string['timecreated'] = 'tiempo creado';
$string['updown'] = 'Arriba/Abajo';
$string['uploadgroupresult'] = 'Subir resultado de grupos';
$string['userassigned'] = 'Usuario asignado';
$string['userunassigned'] = 'Usuario no asignado';
$string['url'] = 'URL';
$string['url_help'] = 'En su lugar, especifique la URL de un sitio';
$string['microllinkexpires'] = 'El enlace de correo electrónico de microaprendizaje caduca';
$string['microllinkexpires_help'] = 'Este es el período de tiempo después del cual el enlace enviado por correo electrónico no iniciará la sesión del usuario automáticamente.';
