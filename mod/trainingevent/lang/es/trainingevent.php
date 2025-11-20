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
 * Language definitions for trainingevent activity.
 *
 * @package   mod_trainingevent
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Strings for component 'trainingevent', language 'es'
 *
 * @translator Alonso Arias <soporte@orioncloud.com.co>
 */

$string['action'] = 'Acción';
$string['alertteachers'] = 'Alertar a profesores por correo electrónico';
$string['alertteachers_help'] = 'Si esta opción está marcada, también se enviará un correo electrónico a los profesores si un usuario es agregado o eliminado del evento de capacitación.';
$string['alreadybookedondates'] = 'Ya tiene una reserva para un evento en estas fechas';
$string['alreadyenrolled'] = 'Ya está inscrito en otro evento';
$string['approvaldenied'] = 'Su solicitud anterior fue rechazada';
$string['approvalrequested'] = 'Aprobación pendiente';
$string['approvaltype'] = 'Aprobación requerida';
$string['attend'] = 'Reservar';
$string['attend_successful'] = 'Está reservado en este evento';
$string['attend_waitlist_successful'] = 'Se agregó a la lista de espera para este evento';
$string['attendance_changed'] = 'Asistencia al evento de capacitación cambiada para el usuario';
$string['attendance_requested'] = 'Asistencia al evento de capacitación solicitada para el usuario';
$string['attendance_withdrawn'] = 'Asistencia al evento de capacitación retirada para el usuario';
$string['attending'] = 'Asistiendo';
$string['bookingnotes'] = 'Notas de reserva';
$string['booknotes'] = 'Información adicional';
$string['booknotesdefault'] = 'Texto de información adicional de reserva predeterminado';
$string['booknotesdefault_help'] = 'El texto incluido aquí se usará como el texto predeterminado que se muestra a los usuarios cuando reserven. Puede usar esto para proporcionar información de ejemplo que desea que los usuarios incluyan en su reserva.';
$string['bookuser'] = 'Reservar usuario';
$string['both'] = 'Administrador y gestor de empresa';
$string['calendarend'] = '{$a} cierra';
$string['calendarstart'] = '{$a} abre';
$string['calendartitle'] = 'Asistiendo al evento {$a->eventname} del curso {$a->coursename}';
$string['capacity'] = 'Capacidad';
$string['chosenclassroomunavailable'] = 'La ubicación elegida ya está en uso';
$string['companymanager'] = 'Gestor de empresa';
$string['details'] = '* Ver detalles y reservar usuarios';
$string['emailssent'] = 'Los correos electrónicos han sido enviados';
$string['enddatetime'] = 'Fecha/hora de finalización';
$string['enrolledonly'] = 'Necesita ser inscrito en esto por su gestor';
$string['enrolonly'] = 'Usuario agregado manualmente';
$string['event'] = 'Evento de capacitación';
$string['eventhaspassed'] = 'El horario de inicio para este evento ya ha pasado';
$string['eventislocked'] = 'Este evento de capacitación está bloqueado y no se pueden realizar más cambios';
$string['exclusive'] = 'Inscripción exclusiva';
$string['exclusive_help'] = 'Si esta opción está marcada, los usuarios solo podrán inscribirse en uno de los eventos de capacitación exclusivos dentro del curso.';
$string['exportcalendar'] = 'Exportar enlace del calendario';
$string['fullybooked'] = 'Este evento de capacitación está completamente reservado';
$string['haswaitinglist'] = 'Incluir lista de espera';
$string['haswaitinglist_help'] = 'Si incluye una lista de espera con su evento, los usuarios podrán inscribirse en la lista de espera para ese evento incluso si el evento ya está lleno.';
$string['invalidclassroom'] = 'Por favor seleccione una ubicación válida';
$string['location'] = 'Nombre/número de sala';
$string['lockdays'] = 'Bloquear evento (días) antes';
$string['lockdays_help'] = 'Si esta opción está habilitada, los estudiantes no podrán inscribirse ni eliminarse del evento de capacitación este número de días antes de la fecha de inicio.';
$string['manager'] = 'Administrador de departamento';
$string['maxsize'] = 'Anular tamaño de ubicación de capacitación a';
$string['maxsize_help'] = 'Establecer un valor aquí anulará el tamaño de sala predeterminado para la sala de capacitación que ha seleccionado.';
$string['missingenddatetime'] = 'Falta fecha/hora de finalización';
$string['missingstartdatetime'] = 'Falta fecha/hora de inicio';
$string['mod/trainingevent:invite'] = 'El usuario puede invitar a usuarios del departamento a un evento';
$string['mod/trainingevent:resetattendees'] = 'El usuario puede restablecer la lista de asistentes';
$string['mod/trainingevent:viewattendees'] = 'El usuario puede ver la lista actual de asistentes';
$string['modulename'] = 'Evento de capacitación';
$string['modulename_help'] = 'Un evento de capacitación permite agregar talleres de capacitación presencial a un curso. Utiliza las ubicaciones de capacitación definidas por la empresa y puede tener un método complejo de reserva y aprobación para permitir el acceso a los usuarios.';
$string['modulenameplural'] = 'Eventos de capacitación';
$string['myeventtype'] = 'Evento de capacitación';
$string['none'] = 'No se requiere aprobación';
$string['of'] = ' usado de un disponible ';
$string['onwaitinglist'] = 'En lista de espera';
$string['pluginadministration'] = 'Administración de evento de capacitación';
$string['pluginname'] = 'Evento de capacitación';
$string['privacy:metadata'] = 'La actividad de evento de capacitación IOMAD solo muestra datos almacenados en otras ubicaciones.';
$string['privacy:metadata:trainingevent_users'] = 'Array de usuarios';
$string['privacy:metadata:trainingeventid:id'] = 'ID de la tabla {trainingevent_users}';
$string['privacy:metadata:trainingeventid:trainingeventid'] = 'El ID del evento de capacitación';
$string['privacy:metadata:trainingeventid:userid'] = 'El ID del usuario';
$string['publish'] = 'Agregar evento al calendario del curso';
$string['publishedtitle'] = 'Evento {$a->eventname} del curso {$a->coursename}';
$string['publishwaitlist'] = 'Publicar este evento por correo electrónico solo a la lista de espera';
$string['remove'] = 'Eliminar';
$string['removerequest'] = 'Retirar solicitud de aprobación';
$string['removerequest_successfull'] = 'Ha retirado su solicitud de aprobación de reserva';
$string['request'] = 'Solicitar aprobación para asistir';
$string['request_successful'] = 'Ha solicitado aprobación para asistir';
$string['requestagain'] = 'Volver a solicitar aprobación para asistir';
$string['requestagain_success'] = 'Ha vuelto a solicitar aprobación para asistir';
$string['requirenotes'] = 'Permitir notas de reserva adicionales';
$string['requirenotes_help'] = 'Si esta opción está seleccionada, los usuarios podrán agregar información adicional a su reserva. Esto se puede ver en las páginas de asistentes y se incluye en las descargas.';
$string['resetattending'] = 'Limpiar asistentes';
$string['resetattendingfull'] = 'Esto eliminará a todos los usuarios que están inscritos en este evento (y están en cualquier lista de espera). No hay recuperación de esto.';
$string['selectaroom'] = 'Seleccionar una ubicación de capacitación';
$string['selectdifferentevent'] = 'Elegir alternativa';
$string['selectother'] = 'Reservar otro usuario a este evento';
$string['sendingemails'] = 'Enviando correos electrónicos para anunciar este evento';
$string['sendreminder'] = 'Enviar correo electrónico de recordatorio (días)';
$string['sendreminder_help'] = 'Si esto está habilitado, se enviará un correo electrónico de recordatorio a los participantes este número de días antes del evento.';
$string['sendreminderemails'] = 'Tarea que envía los correos electrónicos de recordatorio';
$string['slotsleft'] = 'Espacios restantes';
$string['startdatetime'] = 'Fecha/hora de inicio';
$string['summary'] = 'Resumen';
$string['trainingevent:add'] = 'Agregar un usuario a un evento de capacitación';
$string['trainingevent:addinstance'] = 'Agregar una actividad de evento de capacitación';
$string['trainingevent:addoverride'] = 'Agregar un usuario a un evento de capacitación - anular otras restricciones.';
$string['trainingevent:grade'] = 'Permitir calificación de eventos de capacitación';
$string['trainingevent:invite'] = 'Permitir invitación de usuarios a evento de capacitación';
$string['trainingevent:resetattendees'] = 'Limpiar asistentes de eventos de capacitación';
$string['trainingevent:viewallattendees'] = 'Ver todos los asistentes de eventos de capacitación independientemente de la empresa';
$string['trainingevent:viewattendees'] = 'Ver asistentes de eventos de capacitación';
$string['trainingevent_reset'] = 'El evento de capacitación fue restablecido por el usuario';
$string['trainingeventintro'] = 'Descripción';
$string['trainingeventname'] = 'Nombre';
$string['unattend'] = 'Eliminar reserva';
$string['unattend_successfull'] = 'Ya no está asistiendo a este evento';
$string['unpublish'] = 'Eliminar evento del calendario del curso';
$string['updateattendance'] = 'Actualizar reserva';
$string['updateattendance_successful'] = 'Reserva actualizada exitosamente';
$string['updatewaitlist'] = 'Actualizar entrada de lista de espera';
$string['user_added'] = 'Usuario agregado al evento de capacitación';
$string['user_attending'] = 'Usuario asistiendo al evento de capacitación';
$string['user_removed'] = 'Usuario eliminado del evento de capacitación';
$string['useraddedsuccessfully'] = 'El usuario fue agregado a este evento';
$string['useraddedsuccessfully_approval'] = 'El usuario fue agregado a este evento y se solicitó aprobación completa';
$string['usermovedsuccessfully'] = 'El usuario fue movido a otro evento';
$string['userremovedsuccessfully'] = 'El usuario fue eliminado de este evento';
$string['usersattended'] = 'Usuarios que asistieron';
$string['usersbooked'] = 'Usuarios reservados';
$string['viewattendees'] = 'Ver la lista de asistentes';
$string['viewwaitlist'] = 'Ver la lista de espera';
$string['waitinglistlength'] = 'Longitud de lista de espera';
$string['waitlist'] = 'Marcar como esperando para asistir';
$string['youareattending'] = 'Está reservado como asistente';
$string['youarewaiting'] = 'Está esperando un espacio disponible';
