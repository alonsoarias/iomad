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
 * @package   block_iomad_microlearning
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['actions'] = 'Acciones';
$string['active'] = 'Activo';
$string['active_help'] = 'Si el hilo no está activo, no se enviarán mensajes a los usuarios y no será visible en su panel';
$string['blocktitle'] = 'Hilos de microaprendizaje';
$string['bulkassigngroups'] = 'Asignar grupos de hilos por CSV';
$string['clonethread'] = 'Clonar hilo';
$string['clonethreadcheckfull'] = '¿Está seguro de que desea clonar el hilo {$a} y todas las píldoras asociadas a un nuevo hilo? Esto no copia ningún usuario actualmente asignado.';
$string['copy'] = ' (copia)';
$string['cmid'] = 'ID del módulo del curso';
$string['cmid_help'] = 'Ingrese el número de ID del módulo del curso en este campo para la sección única del curso. Debe definir este o el valor de ID de Sección.';
$string['cmidalreadyinuse'] = 'El ID del módulo del curso ya está en uso';
$string['company_threads_for'] = 'Hilos de microaprendizaje para la empresa {$a}';
$string['company_users_for'] = 'Usuarios para el hilo de microaprendizaje {$a}';
$string['creategroup'] = 'Crear nuevo grupo';
$string['crontask'] = 'Tarea cron de microaprendizaje IOMAD';
$string['defaultdue'] = 'Vence después de';
$string['defaultdue_help'] = 'Este es el tiempo después del cual la píldora programada vence. Se puede sobrescribir cambiando el programa del hilo.';
$string['deletegroup'] = 'Eliminar grupo {$a}';
$string['deletegroupcheckfull'] = '¿Desea eliminar el grupo {$a}? Esto también eliminará cualquier usuario asociado a este grupo.';
$string['deletenugget'] = 'Eliminar píldora';
$string['deletenuggetcheckfull'] = '¿Está seguro de que desea eliminar la píldora {$a}?';
$string['deletethread'] = 'Eliminar hilo';
$string['deletethreadcheckfull'] = '¿Está seguro de que desea eliminar completamente el hilo {$a} y todas las píldoras y usuarios asociados?';
$string['duedate'] = 'Fecha de vencimiento';
$string['duedatebeforescheduledate'] = 'La fecha de vencimiento es anterior a la fecha programada';
$string['editgroup'] = 'Editar grupo';
$string['editnugget'] = 'Editar píldora';
$string['editthread'] = 'Editar hilo';
$string['erroredgroups'] = 'Asignaciones de grupo con errores';
$string['group'] = 'Grupo de hilo';
$string['group_help'] = 'Este es el grupo dentro del hilo de microaprendizaje al que se asignará el usuario';
$string['groupcreatedok'] = 'El grupo se ha creado correctamente';
$string['groupdeletedok'] = 'El grupo se ha eliminado correctamente';
$string['groupupdatedok'] = 'El grupo se ha actualizado correctamente';
$string['halt_until_fulfilled'] = 'Detener mensajes hasta completar';
$string['halt_until_fulfilled_help'] = 'Establézcalo en verdadero si desea detener el envío de mensajes hasta que se complete la píldora anterior.';
$string['importgroupsfromfile'] = 'Importar asignaciones de grupos de usuarios de hilos';
$string['importthread'] = 'Importar hilo';
$string['importthreadcheckfull'] = '¿Está seguro de que desea importar el hilo {$a} y todas las píldoras asociadas a un nuevo hilo en esta empresa? Esto no copia ningún usuario actualmente asignado.';
$string['importusergroups'] = 'Importar grupos de usuarios de hilos';
$string['incorrecturl'] = 'La URL especificada no está dentro de este sitio';
$string['interval'] = 'Intervalo de liberación';
$string['interval_help'] = 'Este es el intervalo predeterminado entre fechas de programa para cada píldora';
$string['ibnalidthreadid'] = 'El hilo que está buscando no existe.';
$string['iomad_microlearning:addinstance'] = 'Añadir un bloque de microaprendizaje';
$string['iomad_microlearning:assign_threads'] = 'Asignar un usuario a un hilo de microaprendizaje';
$string['iomad_microlearning:importgroupfromcsv'] = 'Asignar grupos de hilos a usuarios mediante CSV';
$string['iomad_microlearning:manage_groups'] = 'Gestionar grupos de hilos';
$string['iomad_microlearning:edit_nuggets'] = 'Editar píldoras de microaprendizaje';
$string['iomad_microlearning:edit_threads'] = 'Editar hilos de microaprendizaje';
$string['iomad_microlearning:import_threads'] = 'Importar hilos de microaprendizaje';
$string['iomad_microlearning:myaddinstance'] = 'Añadir un bloque de microaprendizaje a mi panel';
$string['iomad_microlearning:thread_clone'] = 'Clonar un hilo de microaprendizaje';
$string['iomad_microlearning:thread_delete'] = 'Eliminar un hilo de microaprendizaje';
$string['iomad_microlearning:thread_view'] = 'Ver hilos de microaprendizaje';
$string['iomad_microlearning:view'] = 'Ver microaprendizaje IOMAD';
$string['learninggroups'] = 'Gestionar grupos de hilos';
$string['learningnuggets'] = 'Gestionar píldoras';
$string['learningschedules'] = 'Gestionar programas';
$string['learningusers'] = 'Gestionar usuarios de hilos';
$string['message_preset'] = 'Enviar mensaje después de';
$string['message_preset_help'] = 'Ingrese un retraso de tiempo usando las unidades proporcionadas después del cual enviar el mensaje.';
$string['message_time'] = 'Hora de envío de mensaje';
$string['message_time_help'] = 'Ingrese una hora programada en la que se enviará el mensaje.';
$string['microlearning'] = 'Microaprendizaje';
$string['microlearninglinkexpires'] = 'Número de días después de que expire el enlace de correo electrónico';
$string['microlearninglinkexpires_help'] = 'Este es el número de días después del cual el enlace de microaprendizaje enviado por correo electrónico al usuario expirará y el usuario deberá iniciar sesión usando el proceso normal';
$string['missingname'] = 'Falta el nombre de la píldora';
$string['missingsectionorcmid'] = 'Por favor ingrese una sección de curso o ID de módulo de curso';
$string['namehelp'] = 'nombre del grupo';
$string['namehelp_help'] = 'Este es el nombre del grupo utilizado dentro del hilo de microaprendizaje. Los nombres son únicos dentro de los hilos pero pueden reutilizarse en múltiples hilos';
$string['nameinuse'] = 'El nombre ya está en uso';
$string['nolearningthreads'] = 'No hay hilos de microaprendizaje';
$string['nonuggets'] = 'No hay píldoras creadas para este hilo';
$string['nugget'] = 'Píldora de microaprendizaje';
$string['nuggetcreated'] = 'Píldora de microaprendizaje creada';
$string['nuggetcreatedok'] = 'Píldora creada correctamente';
$string['nuggetcupdatedok'] = 'Píldora actualizada correctamente';
$string['nuggetdeleted'] = 'Píldora de microaprendizaje eliminada';
$string['nuggetmoved'] = 'Orden de píldora de microaprendizaje movida';
$string['nuggetname'] = 'Nombre de la píldora';
$string['nuggetname_help'] = 'Elija un nombre único para la píldora de aprendizaje';
$string['nuggetorder'] = 'Orden';
$string['nuggets'] = 'Píldoras de microaprendizaje';
$string['nuggetupdated'] = 'Píldora de microaprendizaje actualizada';
$string['pluginname'] = 'Hilos de microaprendizaje IOMAD';
$string['privacy:metadata'] = 'El bloque de microaprendizaje IOMAD solo muestra datos almacenados en otras ubicaciones.';
$string['privacy:metadata:microlearning_thread_user'] = 'Información de datos de usuario de hilo de microaprendizaje. No se conservan datos personales.';
$string['privacy:metadata:microlearning_thread_user:id'] = 'Id de registro de hilo de microaprendizaje';
$string['privacy:metadata:microlearning_thread_user:userid'] = 'Id de usuario de hilo de microaprendizaje';
$string['privacy:metadata:microlearning_thread_user:threadid'] = 'Id de hilo de microaprendizaje';
$string['privacy:metadata:microlearning_thread_user:nuggetid'] = 'Id de píldora de hilo de microaprendizaje';
$string['privacy:metadata:microlearning_thread_user:groupid'] = 'Id de grupo de hilo de microaprendizaje';
$string['privacy:metadata:microlearning_thread_user:schedule_date'] = 'Fecha programada de hilo de microaprendizaje';
$string['privacy:metadata:microlearning_thread_user:due_date'] = 'Fecha de vencimiento de hilo de microaprendizaje';
$string['privacy:metadata:microlearning_thread_user:reminder1_date'] = 'Fecha del primer recordatorio de hilo de microaprendizaje';
$string['privacy:metadata:microlearning_thread_user:reminder2_date'] = 'Fecha del segundo recordatorio de hilo de microaprendizaje';
$string['privacy:metadata:microlearning_thread_user:messagetime'] = 'Hora de enviar mensajes después del hilo de microaprendizaje';
$string['privacy:metadata:microlearning_thread_user:message_delivered'] = 'Indicador de mensaje entregado de hilo de microaprendizaje';
$string['privacy:metadata:microlearning_thread_user:reminder1_delivered'] = 'Indicador de primer recordatorio entregado de hilo de microaprendizaje';
$string['privacy:metadata:microlearning_thread_user:reminder2_delivered'] = 'Indicador de segundo recordatorio entregado de hilo de microaprendizaje';
$string['privacy:metadata:microlearning_thread_user:timecompleted'] = 'Tiempo completado de píldora de hilo de microaprendizaje';
$string['privacy:metadata:microlearning_thread_user:accesskey'] = 'Clave de acceso de correo electrónico de hilo de microaprendizaje';
$string['privacy:metadata:microlearning_thread_user:timecreated'] = 'Tiempo creado de hilo de microaprendizaje';
$string['reminder1'] = 'Primer recordatorio';
$string['reminder1_help'] = 'Tiempo después del cual se enviará el primer recordatorio si la píldora no está marcada como completa.';
$string['reminder2'] = 'Segundo recordatorio';
$string['reminder2_help'] = 'Tiempo después del cual se enviará el segundo recordatorio si la píldora no está marcada como completa.';
$string['reminderdatebeforescheduledate'] = 'La fecha del recordatorio es anterior a la fecha programada';
$string['reminderdatesoutoforder'] = 'Las fechas de recordatorio están fuera de orden';
$string['resetschedule'] = 'Restablecer programa';
$string['resetschedulecheckfull'] = '¿Desea restablecer completamente el programa para {$a}?';
$string['scheduledate'] = 'Fecha de programa';
$string['scheduleoutoforder'] = 'Las fechas de programa están fuera de orden';
$string['scheduletype'] = 'Tipo de programa';
$string['scheduletype_help'] = 'Esto controla qué fecha de inicio obtendrá el usuario que se está asignando. Estándar significa que se agregan de acuerdo con el programa definido. Comenzar hoy significa que se programarán para comenzar el hilo hoy. Comenzar en el próximo programado comenzará al usuario en la próxima fecha programada definida por el hilo actual.';
$string['sectionid'] = 'ID de sección del curso';
$string['sectionid_help'] = 'Ingrese el número de ID de la sección del curso en este campo para la sección única del curso. Debe definir este o el valor de CMID.';
$string['sectionidalreadyinuse'] = 'El ID de sección ya está en uso';
$string['selectthread'] = 'Seleccionar hilo de microaprendizaje';
$string['send_message'] = 'Enviar mensaje';
$string['send_message_help'] = 'Establézcalo en verdadero si desea que se envíen correos electrónicos a los usuarios para píldoras que se programen o recordatorios para completar.';
$string['send_reminder'] = 'Enviar recordatorio';
$string['send_reminder_help'] = 'Establézcalo en verdadero si desea enviar correos electrónicos de recordatorio a los usuarios asignados.';
$string['standard'] = 'Estándar';
$string['startdate'] = 'Fecha de inicio';
$string['startdate_help'] = 'La fecha desde la que se programará el hilo de microaprendizaje';
$string['startnextscheduled'] = 'Comenzar próximo día programado';
$string['starttoday'] = 'Comenzar hoy';
$string['threadcreated'] = 'Hilo de microaprendizaje creado';
$string['threadcreatedok'] = 'Hilo creado correctamente';
$string['threaddeleted'] = 'Hilo de microaprendizaje eliminado';
$string['threadname'] = 'Nombre del hilo';
$string['threadname_help'] = 'El nombre del hilo de microaprendizaje';
$string['threads'] = 'Hilos de microaprendizaje';
$string['threadschedule'] = 'Programa del hilo';
$string['threadscheduleresetok'] = 'Programa del hilo restablecido correctamente';
$string['threadscheduleupdatedok'] = 'Programa del hilo actualizado correctamente';
$string['threadscheduleupdated'] = 'Programa de hilo de microaprendizaje actualizado';
$string['threadupdated'] = 'Hilo de microaprendizaje actualizado';
$string['threadupdatedok'] = 'Hilo actualizado correctamente';
$string['timecreated'] = 'Tiempo creado';
$string['updown'] = 'Arriba/Abajo';
$string['uploadgroupresult'] = 'Resultado de carga de grupos';
$string['userassigned'] = 'Usuario asignado';
$string['userunassigned'] = 'Usuario no asignado';
$string['url'] = 'URL';
$string['url_help'] = 'Especifique una URL del sitio en su lugar';
$string['microllinkexpires'] = 'El enlace de correo electrónico de microaprendizaje expira';
$string['microllinkexpires_help'] = 'Esta es la duración de tiempo después de la cual el enlace enviado por correo electrónico no iniciará sesión automáticamente al usuario.';
