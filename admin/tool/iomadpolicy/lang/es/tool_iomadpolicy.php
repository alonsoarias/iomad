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
 * Strings for component 'tool_iomadpolicy', language 'es'.
 *
 * @package   tool_iomadpolicy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 o posterior
 */

defined('MOODLE_INTERNAL') || die();

$string['acceptanceacknowledgement'] = 'Reconozco que he recibido una solicitud para dar consentimiento en nombre de los usuarios anteriores.';
$string['acceptancenote'] = 'Observaciones';
$string['acceptancepolicies'] = 'Políticas';
$string['acceptancessavedsucessfully'] = 'Los acuerdos se han guardado correctamente.';
$string['acceptancestatusaccepted'] = 'Aceptado';
$string['acceptancestatusacceptedbehalf'] = 'Aceptado en nombre del usuario';
$string['acceptancestatusdeclined'] = 'Rechazado';
$string['acceptancestatusdeclinedbehalf'] = 'Rechazado en nombre del usuario';
$string['acceptancestatusoverall'] = 'En general';
$string['acceptancestatuspartial'] = 'Aceptado parcialmente';
$string['acceptancestatuspending'] = 'Pendiente';
$string['acceptanceusers'] = 'Usuarios';
$string['actions'] = 'Comportamiento';
$string['activate'] = 'Establecer estado en "Activo"';
$string['activating'] = 'Activando una política';
$string['activateconfirm'] = '<p>Estás a punto de activar la política <em>\'{$a->name}\'</em> y hacer que la versión <em>\'{$a->revision}\'</em> sea la actual.</p><p>Todos los usuarios deberán aceptar esta nueva versión de la política para poder utilizar el sitio.</p>';
$string['activateconfirmyes'] = 'Activar';
$string['agreepolicies'] = 'Por favor acepte las siguientes políticas';
$string['backtoprevious'] = 'Volver a la página anterior';
$string['backtotop'] = 'Volver arriba';
$string['cachedef_iomadpolicy_optional'] = 'Caché del indicador opcional/obligatorio para versiones de políticas';
$string['consentbulk'] = 'Consentir';
$string['consentpagetitle'] = 'Consentir';
$string['contactdpo'] = 'Si tiene alguna pregunta sobre las políticas, comuníquese con el oficial de privacidad.';
$string['dataproc'] = 'Tratamiento de datos personales';
$string['declineacknowledgement'] = 'Reconozco que he recibido una solicitud para rechazar el consentimiento en nombre de los usuarios mencionados anteriormente.';
$string['declinetheiomadpolicy'] = 'Rechazar el consentimiento del usuario';
$string['deleting'] = 'Eliminar una versión';
$string['deleteconfirm'] = '<p>¿Está seguro de que desea eliminar la política <em>\'{$a->name}\'</em>?</p><p>Esta operación no se puede deshacer.</p>';
$string['editingiomadpolicydocument'] = 'Política de edición';
$string['erroriomadpolicyversioncompulsory'] = '¡Las pólizas obligatorias no se pueden rechazar!';
$string['erroriomadpolicyversionnotfound'] = 'No existe ninguna versión de política con este identificador.';
$string['errorsaveasdraft'] = 'Los cambios menores no se pueden guardar como borrador.';
$string['errorusercantviewiomadpolicyversion'] = 'El usuario no tiene acceso a esta versión de la política.';
$string['event_acceptance_created'] = 'Acuerdo de política de usuario creado';
$string['event_acceptance_updated'] = 'Acuerdo de política de usuario actualizado';
$string['filtercapabilityno'] = 'Permiso: No puedo estar de acuerdo';
$string['filtercapabilityyes'] = 'Permiso: Puedo aceptar';
$string['filterrevision'] = 'Versión: {$a}';
$string['filterrevisionstatus'] = 'Versión: {$a->name} ({$a->status})';
$string['filterrole'] = 'Rol: {$a}';
$string['filters'] = 'Filtros';
$string['filterstatusdeclined'] = 'Estado: Rechazado';
$string['filterstatuspending'] = 'Estado: Pendiente';
$string['filterstatusyes'] = 'Estado: Acordado';
$string['filterplaceholder'] = 'Buscar palabra clave o seleccionar filtro';
$string['filteriomadpolicy'] = 'Política: {$a}';
$string['guestconsent:continue'] = 'Continuar';
$string['guestconsentmessage'] = 'Si continúa navegando en este sitio web, acepta nuestras políticas:';
$string['iagree'] = 'Acepto el {$a}';
$string['idontagree'] = 'No gracias, rechazo {$a}';
$string['iagreetotheiomadpolicy'] = 'Dar consentimiento';
$string['inactivate'] = 'Establecer estado en "Inactivo"';
$string['inactivating'] = 'Inactivar una política';
$string['inactivatingconfirm'] = '<p>Estás a punto de desactivar la política <em>\'{$a->name}\'</em> versión <em>\'{$a->revision}\'</em>.</p>';
$string['inactivatingconfirmyes'] = 'Inactivar';
$string['invalidversionid'] = '¡No existe ninguna póliza con este identificador!';
$string['irevoketheiomadpolicy'] = 'Retirar el consentimiento del usuario';
$string['listactivepolicies'] = 'Lista de políticas activas';
$string['minorchange'] = 'Cambio menor';
$string['minorchangeinfo'] = 'Un cambio menor no altera el significado de la póliza. No es necesario que los usuarios vuelvan a aceptar la política si la edición se marca como un cambio menor.';
$string['managepolicies'] = 'Administrar políticas';
$string['movedown'] = 'Bajar';
$string['moveup'] = 'Subir';
$string['mustagreetocontinue'] = 'Antes de continuar es necesario reconocer todas estas políticas.';
$string['newiomadpolicy'] = 'Nueva politica';
$string['newversion'] = 'Nueva versión';
$string['noactivepolicies'] = 'No hay políticas con una versión activa.';
$string['nofiltersapplied'] = 'No se aplicaron filtros';
$string['nopermissiontoagreedocs'] = 'No hay permiso para aceptar las políticas.';
$string['nopermissiontoagreedocs_desc'] = 'Lo sentimos, no tienes los permisos necesarios para aceptar las políticas.<br />No podrás utilizar este sitio hasta que se acuerden las siguientes políticas:';
$string['nopermissiontoagreedocsbehalf'] = 'No hay permiso para aceptar las políticas en nombre de este usuario.';
$string['nopermissiontoagreedocsbehalf_desc'] = 'Lo sentimos, no tienes el permiso necesario para aceptar las siguientes políticas en nombre de {$a}:';
$string['nopermissiontoagreedocscontact'] = 'Para obtener más ayuda, comuníquese con';
$string['nopermissiontoviewiomadpolicyversion'] = 'No tiene permisos para ver esta versión de la política.';
$string['nopolicies'] = 'No existen políticas para usuarios registrados con una versión activa.';
$string['selectiomadpolicyandversion'] = 'Utilice el filtro de arriba para seleccionar política y/o versión';
$string['steppolicies'] = 'Política {$a->numiomadpolicy} fuera de {$a->totalpolicies}';
$string['pluginname'] = 'Políticas del IOMAD';
$string['policiesagreements'] = 'Políticas y acuerdos';
$string['importiomadpolicy'] = 'Importar políticas desde tool_policy';
$string['iomadpolicy:accept'] = 'Aceptar las políticas';
$string['iomadpolicy:acceptbehalf'] = 'Dar consentimiento para políticas en nombre de otra persona';
$string['iomadpolicy:managedocs'] = 'Administrar políticas';
$string['iomadpolicy:viewacceptances'] = 'Ver informes de acuerdos de usuario';
$string['iomadpolicydocaudience'] = 'Consentimiento del usuario';
$string['iomadpolicydocaudience0'] = 'Todos los usuarios';
$string['iomadpolicydocaudience1'] = 'Usuarios autenticados';
$string['iomadpolicydocaudience2'] = 'Huéspedes';
$string['iomadpolicydoccontent'] = 'Póliza completa';
$string['iomadpolicydochdriomadpolicy'] = 'Política';
$string['iomadpolicydochdrversion'] = 'Versión del documento';
$string['iomadpolicydocname'] = 'Nombre';
$string['iomadpolicydocoptional'] = 'Acuerdo opcional';
$string['iomadpolicydocoptionalyes'] = 'Opcional';
$string['iomadpolicydocoptionalno'] = 'Obligatorio';
$string['iomadpolicydocrevision'] = 'Versión';
$string['iomadpolicydocsummary'] = 'Resumen';
$string['iomadpolicydocsummary_help'] = 'Este texto debe proporcionar un resumen de la política, potencialmente en una forma simplificada y fácilmente accesible, utilizando un lenguaje claro y sencillo.';
$string['iomadpolicydoctype'] = 'Tipo';
$string['iomadpolicydoctype0'] = 'Política del sitio';
$string['iomadpolicydoctype1'] = 'Política de privacidad';
$string['iomadpolicydoctype2'] = 'Política de terceros';
$string['iomadpolicydoctype99'] = 'Otra póliza';
$string['iomadpolicydocuments'] = 'Documentos de política';
$string['iomadpolicynamedversion'] = 'Política {$a->name} (versión {$a->revision} - {$a->id})';
$string['iomadpolicypriorityagreement'] = 'Mostrar política antes de mostrar otras políticas';
$string['iomadpolicyversionacceptedinbehalf'] = 'El consentimiento para esta política se ha otorgado en su nombre.';
$string['iomadpolicyversionacceptedinotherlang'] = 'El consentimiento para esta versión de la política se ha otorgado en un idioma diferente.';
$string['previousversions'] = '{$a} versiones anteriores';
$string['privacy:metadata:acceptances'] = 'Información sobre acuerdos de póliza realizados por los usuarios.';
$string['privacy:metadata:acceptances:iomadpolicyversionid'] = 'La versión de la póliza para la cual se dio el consentimiento.';
$string['privacy:metadata:acceptances:userid'] = 'El usuario al que se refiere este acuerdo de póliza.';
$string['privacy:metadata:acceptances:status'] = 'El estado del acuerdo.';
$string['privacy:metadata:acceptances:lang'] = 'El idioma utilizado para mostrar la política cuando se otorgó el consentimiento.';
$string['privacy:metadata:acceptances:usermodified'] = 'El usuario que dio su consentimiento para la política, si lo hizo en nombre de otro usuario.';
$string['privacy:metadata:acceptances:timecreated'] = 'El momento en que el usuario aceptó la política.';
$string['privacy:metadata:acceptances:timemodified'] = 'La hora en que el usuario actualizó su acuerdo.';
$string['privacy:metadata:acceptances:note'] = 'Cualquier comentario agregado por un usuario al dar su consentimiento en nombre de otro usuario.';
$string['privacy:metadata:subsystem:corefiles'] = 'La herramienta de políticas almacena archivos incluidos en el resumen y la política completa.';
$string['privacy:metadata:versions'] = 'Información de la versión de la política.';
$string['privacy:metadata:versions:name'] = 'El nombre de la póliza.';
$string['privacy:metadata:versions:type'] = 'Tipo de póliza.';
$string['privacy:metadata:versions:audience'] = 'El tipo de usuarios requeridos para dar su consentimiento.';
$string['privacy:metadata:versions:archived'] = 'El estado de la política (activa o inactiva).';
$string['privacy:metadata:versions:usermodified'] = 'El usuario que modificó la política.';
$string['privacy:metadata:versions:timecreated'] = 'La hora en que se creó esta versión de la política.';
$string['privacy:metadata:versions:timemodified'] = 'La hora a la que se actualizó esta versión de la política.';
$string['privacy:metadata:versions:iomadpolicyid'] = 'La política a la que está asociada esta versión.';
$string['privacy:metadata:versions:revision'] = 'El nombre de la revisión de esta versión de la política.';
$string['privacy:metadata:versions:summary'] = 'El resumen de esta versión de la póliza.';
$string['privacy:metadata:versions:summaryformat'] = 'El formato del campo de resumen.';
$string['privacy:metadata:versions:content'] = 'El contenido de esta versión de la póliza.';
$string['privacy:metadata:versions:contentformat'] = 'El formato del campo de contenido.';
$string['privacysettings'] = 'Configuración de privacidad';
$string['readiomadpolicy'] = 'Por favor lea nuestro {$a}';
$string['refertofulliomadpolicytext'] = 'Consulte el {$a} completo si desea revisar el texto.';
$string['response'] = 'Respuesta';
$string['responseby'] = 'Demandado';
$string['responseon'] = 'Fecha';
$string['revokeacknowledgement'] = 'Reconozco que he recibido una solicitud para retirar el consentimiento en nombre de los usuarios mencionados anteriormente.';
$string['save'] = 'Ahorrar';
$string['saveasdraft'] = 'Guardar como borrador';
$string['selectuser'] = 'Seleccionar usuario {$a}';
$string['selectusersforconsent'] = 'Seleccione los usuarios para dar su consentimiento en nombre de.';
$string['settodraft'] = 'Crear un nuevo borrador';
$string['status'] = 'Estado de la política';
$string['statusformtitleaccept'] = 'Aceptando política';
$string['statusformtitledecline'] = 'Política en declive';
$string['statusformtitlerevoke'] = 'Política de retiro';
$string['statusinfo'] = 'Una política con estado "Activo" requiere que los usuarios den su consentimiento, ya sea cuando inician sesión por primera vez o, en el caso de los usuarios existentes, cuando inician sesión la próxima vez.';
$string['status0'] = 'Borrador';
$string['status1'] = 'Activo';
$string['status2'] = 'Inactivo';
$string['useracceptanceactionaccept'] = 'Aceptar';
$string['useracceptanceactionacceptone'] = 'Aceptar {$a}';
$string['useracceptanceactionacceptpending'] = 'Aceptar políticas pendientes';
$string['useracceptanceactiondecline'] = 'Rechazar';
$string['useracceptanceactiondeclineone'] = 'Rechazar {$a}';
$string['useracceptanceactiondeclinepending'] = 'Rechazar políticas pendientes';
$string['useracceptanceactiondetails'] = 'Detalles';
$string['useracceptanceactionrevoke'] = 'Retirar';
$string['useracceptanceactionrevokeall'] = 'Retirar pólizas aceptadas';
$string['useracceptanceactionrevokeone'] = 'Retirar la aceptación de {$a}';
$string['useracceptancecount'] = '{$a->agreedcount} de {$a->userscount} ({$a->percent}%)';
$string['useracceptancecountna'] = 'N / A';
$string['useracceptances'] = 'Acuerdos de usuario';
$string['useriomadpolicysettings'] = 'Políticas';
$string['usersaccepted'] = 'Acuerdos';
$string['viewarchived'] = 'Ver versiones anteriores';
$string['viewconsentpageforuser'] = 'Viendo esta página en nombre de {$a}';
