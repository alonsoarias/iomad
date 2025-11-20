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
 * Plugin strings are defined here.
 *
 * @package     tool_iomadpolicy
 * @category    string
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Strings for component 'tool_iomadpolicy', language 'es'
 *
 * @translator Alonso Arias <soporte@orioncloud.com.co>
 */

defined('MOODLE_INTERNAL') || die();

$string['acceptanceacknowledgement'] = 'Reconozco que he recibido una solicitud para dar consentimiento en nombre del/de los usuario(s) mencionado(s).';
$string['acceptancenote'] = 'Observaciones';
$string['acceptancepolicies'] = 'Políticas';
$string['acceptancessavedsucessfully'] = 'Los acuerdos se han guardado exitosamente.';
$string['acceptancestatusaccepted'] = 'Aceptado';
$string['acceptancestatusacceptedbehalf'] = 'Aceptado en nombre del usuario';
$string['acceptancestatusdeclined'] = 'Rechazado';
$string['acceptancestatusdeclinedbehalf'] = 'Rechazado en nombre del usuario';
$string['acceptancestatusoverall'] = 'General';
$string['acceptancestatuspartial'] = 'Parcialmente aceptado';
$string['acceptancestatuspending'] = 'Pendiente';
$string['acceptanceusers'] = 'Usuarios';
$string['actions'] = 'Acciones';
$string['activate'] = 'Establecer estado como "Activo"';
$string['activating'] = 'Activando una política';
$string['activateconfirm'] = '<p>Está a punto de activar la política <em>\'{$a->name}\'</em> y hacer que la versión <em>\'{$a->revision}\'</em> sea la actual.</p><p>Todos los usuarios deberán aceptar esta nueva versión de la política para poder usar el sitio.</p>';
$string['activateconfirmyes'] = 'Activar';
$string['agreepolicies'] = 'Por favor acepte las siguientes políticas';
$string['backtoprevious'] = 'Volver a la página anterior';
$string['backtotop'] = 'Volver arriba';
$string['cachedef_iomadpolicy_optional'] = 'Caché de la bandera opcional/obligatoria para versiones de política';
$string['consentbulk'] = 'Consentimiento';
$string['consentpagetitle'] = 'Consentimiento';
$string['contactdpo'] = 'Para cualquier pregunta sobre las políticas, contacte al oficial de privacidad.';
$string['dataproc'] = 'Procesamiento de datos personales';
$string['declineacknowledgement'] = 'Reconozco que he recibido una solicitud para rechazar el consentimiento en nombre del/de los usuario(s) mencionado(s).';
$string['declinetheiomadpolicy'] = 'Rechazar consentimiento del usuario';
$string['deleting'] = 'Eliminando una versión';
$string['deleteconfirm'] = '<p>¿Está seguro de que desea eliminar la política <em>\'{$a->name}\'</em>?</p><p>Esta operación no se puede deshacer.</p>';
$string['editingiomadpolicydocument'] = 'Editando política';
$string['erroriomadpolicyversioncompulsory'] = '¡Las políticas obligatorias no pueden ser rechazadas!';
$string['erroriomadpolicyversionnotfound'] = 'No hay ninguna versión de política con este identificador.';
$string['errorsaveasdraft'] = 'El cambio menor no se puede guardar como borrador';
$string['errorusercantviewiomadpolicyversion'] = 'El usuario no tiene acceso a esta versión de política.';
$string['event_acceptance_created'] = 'Acuerdo de política de usuario creado';
$string['event_acceptance_updated'] = 'Acuerdo de política de usuario actualizado';
$string['filtercapabilityno'] = 'Permiso: No puede aceptar';
$string['filtercapabilityyes'] = 'Permiso: Puede aceptar';
$string['filterrevision'] = 'Versión: {$a}';
$string['filterrevisionstatus'] = 'Versión: {$a->name} ({$a->status})';
$string['filterrole'] = 'Rol: {$a}';
$string['filters'] = 'Filtros';
$string['filterstatusdeclined'] = 'Estado: Rechazado';
$string['filterstatuspending'] = 'Estado: Pendiente';
$string['filterstatusyes'] = 'Estado: Aceptado';
$string['filterplaceholder'] = 'Buscar palabra clave o seleccionar filtro';
$string['filteriomadpolicy'] = 'Política: {$a}';
$string['guestconsent:continue'] = 'Continuar';
$string['guestconsentmessage'] = 'Si continúa navegando en este sitio web, acepta nuestras políticas:';
$string['iagree'] = 'Acepto {$a}';
$string['idontagree'] = 'No, gracias, rechazo {$a}';
$string['iagreetotheiomadpolicy'] = 'Dar consentimiento';
$string['inactivate'] = 'Establecer estado como "Inactivo"';
$string['inactivating'] = 'Inactivando una política';
$string['inactivatingconfirm'] = '<p>Está a punto de inactivar la política <em>\'{$a->name}\'</em> versión <em>\'{$a->revision}\'</em>.</p>';
$string['inactivatingconfirmyes'] = 'Inactivar';
$string['invalidversionid'] = '¡No hay ninguna política con este identificador!';
$string['irevoketheiomadpolicy'] = 'Retirar consentimiento del usuario';
$string['listactivepolicies'] = 'Lista de políticas activas';
$string['minorchange'] = 'Cambio menor';
$string['minorchangeinfo'] = 'Un cambio menor no altera el significado de la política. Los usuarios no están obligados a aceptar la política nuevamente si la edición se marca como un cambio menor.';
$string['managepolicies'] = 'Gestionar políticas';
$string['movedown'] = 'Mover abajo';
$string['moveup'] = 'Mover arriba';
$string['mustagreetocontinue'] = 'Antes de continuar debe reconocer todas estas políticas.';
$string['newiomadpolicy'] = 'Nueva política';
$string['newversion'] = 'Nueva versión';
$string['noactivepolicies'] = 'No hay políticas con una versión activa.';
$string['nofiltersapplied'] = 'No se aplicaron filtros';
$string['nopermissiontoagreedocs'] = 'Sin permiso para aceptar las políticas';
$string['nopermissiontoagreedocs_desc'] = 'Lo sentimos, no tiene los permisos requeridos para aceptar las políticas.<br />No podrá usar este sitio hasta que se acepten las siguientes políticas:';
$string['nopermissiontoagreedocsbehalf'] = 'Sin permiso para aceptar las políticas en nombre de este usuario';
$string['nopermissiontoagreedocsbehalf_desc'] = 'Lo sentimos, no tiene el permiso requerido para aceptar las siguientes políticas en nombre de {$a}:';
$string['nopermissiontoagreedocscontact'] = 'Para más ayuda, por favor contacte';
$string['nopermissiontoviewiomadpolicyversion'] = 'No tiene permisos para ver esta versión de política.';
$string['nopolicies'] = 'No hay políticas para usuarios registrados con una versión activa.';
$string['selectiomadpolicyandversion'] = 'Use el filtro anterior para seleccionar política y/o versión';
$string['steppolicies'] = 'Política {$a->numiomadpolicy} de {$a->totalpolicies}';
$string['pluginname'] = 'Políticas IOMAD';
$string['policiesagreements'] = 'Políticas y acuerdos';
$string['importiomadpolicy'] = 'Importar políticas desde tool_policy';
$string['iomadpolicy:accept'] = 'Aceptar las políticas';
$string['iomadpolicy:acceptbehalf'] = 'Dar consentimiento para políticas en nombre de otra persona';
$string['iomadpolicy:managedocs'] = 'Gestionar políticas';
$string['iomadpolicy:viewacceptances'] = 'Ver reportes de acuerdos de usuario';
$string['iomadpolicydocaudience'] = 'Consentimiento del usuario';
$string['iomadpolicydocaudience0'] = 'Todos los usuarios';
$string['iomadpolicydocaudience1'] = 'Usuarios autenticados';
$string['iomadpolicydocaudience2'] = 'Invitados';
$string['iomadpolicydoccontent'] = 'Política completa';
$string['iomadpolicydochdriomadpolicy'] = 'Política';
$string['iomadpolicydochdrversion'] = 'Versión del documento';
$string['iomadpolicydocname'] = 'Nombre';
$string['iomadpolicydocoptional'] = 'Acuerdo opcional';
$string['iomadpolicydocoptionalyes'] = 'Opcional';
$string['iomadpolicydocoptionalno'] = 'Obligatorio';
$string['iomadpolicydocrevision'] = 'Versión';
$string['iomadpolicydocsummary'] = 'Resumen';
$string['iomadpolicydocsummary_help'] = 'Este texto debe proporcionar un resumen de la política, potencialmente en una forma simplificada y fácilmente accesible, usando lenguaje claro y sencillo.';
$string['iomadpolicydoctype'] = 'Tipo';
$string['iomadpolicydoctype0'] = 'Política del sitio';
$string['iomadpolicydoctype1'] = 'Política de privacidad';
$string['iomadpolicydoctype2'] = 'Política de terceros';
$string['iomadpolicydoctype99'] = 'Otra política';
$string['iomadpolicydocuments'] = 'Documentos de política';
$string['iomadpolicynamedversion'] = 'Política {$a->name} (versión {$a->revision} - {$a->id})';
$string['iomadpolicypriorityagreement'] = 'Mostrar política antes de mostrar otras políticas';
$string['iomadpolicyversionacceptedinbehalf'] = 'Se ha dado consentimiento para esta política en su nombre.';
$string['iomadpolicyversionacceptedinotherlang'] = 'Se ha dado consentimiento para esta versión de política en un idioma diferente.';
$string['previousversions'] = '{$a} versiones anteriores';
$string['privacy:metadata:acceptances'] = 'Información sobre acuerdos de política realizados por usuarios.';
$string['privacy:metadata:acceptances:iomadpolicyversionid'] = 'La versión de la política para la cual se dio consentimiento.';
$string['privacy:metadata:acceptances:userid'] = 'El usuario al que se relaciona este acuerdo de política.';
$string['privacy:metadata:acceptances:status'] = 'El estado del acuerdo.';
$string['privacy:metadata:acceptances:lang'] = 'El idioma usado para mostrar la política cuando se dio el consentimiento.';
$string['privacy:metadata:acceptances:usermodified'] = 'El usuario que dio consentimiento para la política, si se hizo en nombre de otro usuario.';
$string['privacy:metadata:acceptances:timecreated'] = 'El momento en que el usuario aceptó la política.';
$string['privacy:metadata:acceptances:timemodified'] = 'El momento en que el usuario actualizó su acuerdo.';
$string['privacy:metadata:acceptances:note'] = 'Cualquier comentario agregado por un usuario al dar consentimiento en nombre de otro usuario.';
$string['privacy:metadata:subsystem:corefiles'] = 'La herramienta de política almacena archivos incluidos en el resumen y la política completa.';
$string['privacy:metadata:versions'] = 'Información de versión de política.';
$string['privacy:metadata:versions:name'] = 'El nombre de la política.';
$string['privacy:metadata:versions:type'] = 'Tipo de política.';
$string['privacy:metadata:versions:audience'] = 'El tipo de usuarios que deben dar su consentimiento.';
$string['privacy:metadata:versions:archived'] = 'El estado de la política (activa o inactiva).';
$string['privacy:metadata:versions:usermodified'] = 'El usuario que modificó la política.';
$string['privacy:metadata:versions:timecreated'] = 'El momento en que se creó esta versión de la política.';
$string['privacy:metadata:versions:timemodified'] = 'El momento en que se actualizó esta versión de la política.';
$string['privacy:metadata:versions:iomadpolicyid'] = 'La política con la que está asociada esta versión.';
$string['privacy:metadata:versions:revision'] = 'El nombre de revisión de esta versión de la política.';
$string['privacy:metadata:versions:summary'] = 'El resumen de esta versión de la política.';
$string['privacy:metadata:versions:summaryformat'] = 'El formato del campo resumen.';
$string['privacy:metadata:versions:content'] = 'El contenido de esta versión de la política.';
$string['privacy:metadata:versions:contentformat'] = 'El formato del campo contenido.';
$string['privacysettings'] = 'Configuración de privacidad';
$string['readiomadpolicy'] = 'Por favor lea {$a}';
$string['refertofulliomadpolicytext'] = 'Por favor consulte {$a} completo si desea revisar el texto.';
$string['response'] = 'Respuesta';
$string['responseby'] = 'Encuestado';
$string['responseon'] = 'Fecha';
$string['revokeacknowledgement'] = 'Reconozco que he recibido una solicitud para retirar el consentimiento en nombre del/de los usuario(s) mencionado(s).';
$string['save'] = 'Guardar';
$string['saveasdraft'] = 'Guardar como borrador';
$string['selectuser'] = 'Seleccionar usuario {$a}';
$string['selectusersforconsent'] = 'Seleccione usuarios para dar consentimiento en su nombre.';
$string['settodraft'] = 'Crear un nuevo borrador';
$string['status'] = 'Estado de la política';
$string['statusformtitleaccept'] = 'Aceptando política';
$string['statusformtitledecline'] = 'Rechazando política';
$string['statusformtitlerevoke'] = 'Retirando política';
$string['statusinfo'] = 'Una política con estado \'Activo\' requiere que los usuarios den su consentimiento, ya sea cuando inicien sesión por primera vez o, en el caso de usuarios existentes, cuando inicien sesión la próxima vez.';
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
$string['useracceptanceactionrevokeall'] = 'Retirar políticas aceptadas';
$string['useracceptanceactionrevokeone'] = 'Retirar aceptación de {$a}';
$string['useracceptancecount'] = '{$a->agreedcount} de {$a->userscount} ({$a->percent}%)';
$string['useracceptancecountna'] = 'N/A';
$string['useracceptances'] = 'Acuerdos de usuario';
$string['useriomadpolicysettings'] = 'Políticas';
$string['usersaccepted'] = 'Acuerdos';
$string['viewarchived'] = 'Ver versiones anteriores';
$string['viewconsentpageforuser'] = 'Viendo esta página en nombre de {$a}';
