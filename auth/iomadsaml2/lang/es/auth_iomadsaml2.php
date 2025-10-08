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
 * Strings for component 'auth_iomadsaml2', language 'es'.
 *
 * @package   auth_iomadsaml2
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 o posterior
 */

defined('MOODLE_INTERNAL') || die();

$string['alterlogout'] = 'URL de cierre de sesión alternativa';
$string['alterlogout_help'] = 'La URL para redirigir a un usuario después de ejecutar todos los mecanismos internos de cierre de sesión.';
$string['anyauth'] = 'Permitido cualquier tipo de autenticación';
$string['anyauth_help'] = 'Sí: ¿Permitir el inicio de sesión SAML para todos los usuarios? No: solo usuarios que tengan iomadsaml2 como tipo.';
$string['anyauthotherdisabled'] = 'Ha iniciado sesión correctamente como \'{$a->username}\' pero su tipo de autenticación de \'{$a->auth}\' está deshabilitado.';
$string['attemptsignout'] = 'Intentar cerrar sesión de IdP';
$string['attemptsignout_help'] = 'Esto intentará comunicarse con el IdP para enviar una solicitud de cierre de sesión.';
$string['auth_iomadsaml2description'] = 'Autenticar con un proveedor de identidad (IdP) SAML2';
$string['auth_iomadsaml2blockredirectdescription'] = 'Redirigir o mostrar mensajes a inicios de sesión SAML2 según las restricciones de grupo configuradas';
$string['autocreate'] = 'Crear usuarios automáticamente';
$string['autocreate_help'] = 'Permitir la creación de usuarios de Moodle bajo demanda';
$string['autologin'] = 'Inicio de sesión automático';
$string['autologin_help'] = 'En las páginas que permiten el acceso de invitados sin iniciar sesión, los usuarios inician sesión automáticamente en Moodle con una cuenta de usuario real si han iniciado sesión en el IdP (usando autenticación pasiva).';
$string['autologinbysession'] = 'Verificar una vez por sesión';
$string['autologinbycookie'] = 'Comprobar cuándo existe o cambia la cookie especificada';
$string['autologincookie'] = 'Cookie de inicio de sesión automático';
$string['autologincookie_help'] = 'Nombre de la cookie utilizada para decidir cuándo intentar el inicio de sesión automático (solo es relevante si la opción de cookie está seleccionada arriba).';
$string['availableidps'] = 'Seleccione los IdP disponibles';
$string['availableidps_help'] = 'Si un xml de metadatos de IdP contiene varias entidades de IdP, deberá seleccionar qué entidades están disponibles
Z para que los usuarios inicien sesión.';
$string['blockredirectheading'] = 'Acciones de bloqueo de cuenta';
$string['attrsimple'] = 'Simplificar atributos';
$string['attrsimple_help'] = 'Varios IdP, como ADFS, utilizan claves de atributos largas, como urnas o nombres de esquemas xml con espacios de nombres. Si se establece en Sí, esto los simplificará, por ejemplo, asignar http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname a dicho \'nombre de pila\'.';
$string['certificatedetails'] = 'Detalles del certificado';
$string['certificatedetailshelp'] = '<h1>Contenido del certificado público generado automáticamente por SAML2</h1><p>La ruta del certificado está aquí:</p>';
$string['checkcertificateexpiry'] = 'Caducidad del certificado SAML';
$string['checkcertificateexpired'] = 'El certificado SAML expiró hace {$a}';
$string['checkcertificatewarn'] = 'El certificado SAML caducará en {$a}';
$string['checkcertificateok'] = 'El certificado SAML caducará en {$a}';
$string['certificate_help'] = 'Regenerar la Clave Privada y el Certificado utilizados por este SP. | <a href=\'{$a}\'>Ver certificado de SP</a>';
$string['certificatelock_help'] = 'Bloquear los certificados evitará que se sobrescriban una vez generados.';
$string['certificatelock'] = 'Certificado de bloqueo';
$string['certificatelock_locked'] = 'El certificado está bloqueado.';
$string['certificatelock_lockedmessage'] = 'Los certificados están actualmente bloqueados.';
$string['certificatelock_unlock'] = 'Desbloquear certificados';
$string['certificatelock_regenerate'] = '¡No se regeneran los certificados porque han sido bloqueados!';
$string['certificatelock_warning'] = 'Advertencia. Está a punto de bloquear los certificados, ¿está seguro de que desea hacerlo? <br> Los certificados no están bloqueados actualmente';
$string['certificate'] = 'Regenerar certificado';
$string['commonname'] = 'Nombre común';
$string['countryname'] = 'País';
$string['debug'] = 'Depuración';
$string['debug_help'] = '<p>Esto agrega depuración adicional al registro normal de Moodle | <a href=\'{$a}\'>Ver configuración de SSP</a></p>';
$string['duallogin'] = 'Inicio de sesión dual';
$string['duallogin_help'] = '
<p>Si está activado, los usuarios verán un botón de inicio de sesión manual y SAML. Si está desactivado, siempre serán llevados directamente a la página de inicio de sesión del IdP.</p>
<p>Si es pasivo, los usuarios que ya están autenticados en el IDP iniciarán sesión automáticamente; de ​​lo contrario, se enviarán a la página de inicio de sesión de Moodle.</p>
<p>Si está desactivado, los administradores aún pueden ver la página de inicio de sesión manual a través de /login/index.php?saml=off</p>
<p>Si está activado, las páginas externas pueden realizar enlaces profundos a Moodle usando saml, por ejemplo, /course/view.php?id=45&saml=on</p>
<p>Si está configurado para probar la conexión IdP, se comprobará la conectividad de la red y, si funciona, se iniciará el inicio de sesión SAML.</p>';
$string['emailtaken'] = 'No se puede crear una cuenta nueva porque la dirección de correo electrónico {$a} ya está registrada';
$string['emailtakenupdate'] = 'Su correo electrónico no fue actualizado porque la dirección de correo electrónico {$a} ya está registrada';
$string['errorinvalidautologin'] = 'Solicitud de inicio de sesión automático no válida';
$string['errorparsingxml'] = 'Error al analizar XML: {$a}';
$string['exception'] = 'Excepción SAML2: {$a}';
$string['expirydays'] = 'Vencimiento en días';
$string['error'] = 'Error de inicio de sesión';
$string['fielddelimiter'] = 'Delimitador de campo';
$string['fielddelimiter_help'] = 'El delimitador que se utilizará cuando un campo reciba una matriz de valores del IdP.';
$string['flaggedresponsetypemessage'] = 'Mostrar mensaje personalizado';
$string['flaggedresponsetyperedirect'] = 'Redirigir a URL externa';
$string['flagredirecturl'] = 'URL de redireccionamiento';
$string['flagredirecturl_help'] = '
<p>La URL para redirigir a un usuario no puede acceder a Moodle según las restricciones de grupo configuradas.</p>
<p>(Solo se utiliza cuando el \'Tipo de respuesta\' es \'Redirigir a URL externa\').</p>';
$string['flagmessage'] = 'Mensaje de respuesta';
$string['flagmessage_help'] = '
<p>El mensaje que se mostrará cuando un usuario no tiene permiso para acceder a Moodle según las restricciones de grupo configuradas.</p>
<p>(Solo se muestra cuando \'Tipo de respuesta\' es \'Mostrar mensaje personalizado\'.)</p>';
$string['flagmessage_default'] = 'Ha iniciado sesión con su proveedor de identidad; sin embargo, esta cuenta tiene acceso limitado a Moodle; comuníquese con su administrador para obtener más detalles.';
$string['flagresponsetype'] = 'Tipo de respuesta de bloqueo de cuenta';
$string['flagresponsetype_help'] = 'Si el acceso está bloqueado según las restricciones de grupo configuradas, ¿cómo debería responder Moodle?';
$string['idpattr_help'] = '¿Qué atributo de IdP debe compararse con un campo de usuario de Moodle?';
$string['idpattr'] = 'IdP de mapeo';
$string['idpmetadata_badurl'] = 'Metadatos no válidos en {$a}';
$string['idpmetadata_help'] = 'Para utilizar varios IdP, ingrese cada URL de metadatos públicos en una nueva línea.<br/>Para anular un nombre, coloque el texto antes de http. p.ej. "Nombre de IdP forzado http://ssp.local/simplesaml/saml2/idp/metadata.php"';
$string['idpmetadata'] = 'XML de metadatos de IdP O URL XML pública';
$string['idpmetadata_invalid'] = 'El XML del IdP no es válido';
$string['idpmetadata_noentityid'] = 'El XML del IdP no tiene ID de entidad';
$string['idpmetadatarefresh_help'] = 'Ejecute una tarea programada para actualizar los metadatos del IdP desde la URL de metadatos del IdP';
$string['idpmetadatarefresh'] = 'Actualización de metadatos de IdP';
$string['idpnamedefault'] = 'Iniciar sesión a través de SAML2';
$string['idpnamedefault_varaible'] = 'Iniciar sesión a través de SAML2 ({$a})';
$string['idpname_help'] = 'por ejemplo, myUNI: esto se detecta a partir de los metadatos y se mostrará en la página de inicio de sesión dual (si está habilitado)';
$string['idpname'] = 'Anulación de etiqueta de IdP';
$string['localityname'] = 'Localidad';
$string['logdirdefault'] = '/tmp/';
$string['logdir_help'] = 'El directorio de registro en el que escribirá SSPHP, el archivo se llamará simplesamlphp.log';
$string['logdir'] = 'Directorio de registros';
$string['logtofile'] = 'Habilitar el registro en el archivo';
$string['logtofile_help'] = 'Al activar esto se redirigirá la salida del registro de SSPHP a un archivo en el directorio de registro.';
$string['manageidpsheading'] = 'Administrar proveedores de identidad (IdP) disponibles';
$string['mdlattr_help'] = '¿Con qué campo de usuario de Moodle debe coincidir el atributo IdP?';
$string['mdlattr'] = 'Mapeo de Moodle';
$string['wantassertionssigned'] = 'Quiere afirmaciones firmadas';
$string['wantassertionssigned_help'] = 'Si las afirmaciones recibidas por este SP deben estar firmadas';
$string['assertionsconsumerservices'] = 'Afirmaciones servicios al consumidor';
$string['assertionsconsumerservices_help'] = 'Lista de enlaces que el SP debería soportar';
$string['spentityid'] = 'ID de entidad';
$string['spentityid_help'] = 'Anule la identificación de entidad del proveedor de servicios. En la mayoría de los casos, déjelo en blanco y en su lugar se utilizará un buen valor predeterminado.';
$string['allowcreate'] = 'Permitir crear';
$string['allowcreate_help'] = 'Permitir la creación de usuarios de IdP a pedido';
$string['authncontext'] = 'Contexto de autenticación';
$string['authncontext_help'] = 'Permite el aumento de afirmaciones. Dejar en blanco a menos que sea necesario';
$string['metadatafetchfailed'] = 'Error al recuperar metadatos: {$a}';
$string['metadatafetchfailedstatus'] = 'Error en la recuperación de metadatos: código de estado {$a}';
$string['metadatafetchfailedunknown'] = 'Error al recuperar metadatos: error de cURL desconocido';
$string['multiidp:label:displayname'] = 'Nombre para mostrar';
$string['multiidp:label:alias'] = 'Alias';
$string['multiidp:label:active'] = 'Activo';
$string['multiidp:label:defaultidp'] = 'Proveedor de identidad predeterminado';
$string['multiidp:label:admin'] = 'Solo para usuarios administradores';
$string['multiidp:label:admin_help'] = 'Cualquier usuario que inicie sesión utilizando este IdP se convertirá automáticamente en administrador del sitio.';
$string['multiidp:label:whitelist'] = 'Direcciones IP redirigidas';
$string['multiidp:label:whitelist_help'] = 'Si se establece, obligará a los clientes a utilizar este IdP. Formato: xxx.xxx.xxx.xxx/máscara de bits. Separe varias subredes en una nueva línea.';
$string['multiidpinfo'] = '
<ul>
<li>Un IdP solo se puede usar si está configurado como Activo</li>
<li>Cuando se ha activado el inicio de sesión dual, todos los IdP activos se mostrarán en la página de inicio de sesión</li>
<li>Cuando un IdP se ha configurado como predeterminado y el inicio de sesión dual no está activado, este IdP se usará automáticamente a menos que ?multiidp=on o saml=off se pasa en /login/index.php</li>
<li>A un IdP se le puede dar un alias, al ir a /login/index.php?idpalias={alias} el alias se puede pasar para usar directamente ese IdP</li>
</ul>';
$string['multiidpbuttons'] = 'Botones con iconos';
$string['multiidpdisplay'] = 'Tipo de visualización de múltiples IdP';
$string['multiidpdisplay_help'] = 'Si un xml de metadatos de IdP contiene varias entidades de IdP, ¿cómo se mostrará cada IdP disponible?';
$string['multiidpdropdown'] = 'Lista desplegable';
$string['nameidasattrib'] = 'Exponer NameID como atributo';
$string['nameidasattrib_help'] = 'La reclamación NameID se expondrá a SSPHP como un atributo denominado nameid';
$string['noattribute'] = 'Ha iniciado sesión exitosamente pero no pudimos encontrar su atributo \'{$a}\' para asociarlo a una cuenta en Moodle.';
$string['noidpfound'] = 'El IdP \'{$a}\' no se encontró como IdP configurado.';
$string['noredirectips'] = 'Restringir noredirect por IP';
$string['noredirectips_help'] = 'Cuando el inicio de sesión dual está desactivado y se configuran las IP, esto restringirá el uso de ?saml=off y ?noredirect=1 durante el inicio de sesión SAML a usuarios con subredes IP coincidentes.';
$string['nouser'] = 'Ha iniciado sesión exitosamente como \'{$a}\' pero no tiene una cuenta en Moodle.';
$string['nullprivatecert'] = 'Error al crear el certificado privado.';
$string['nullpubliccert'] = 'Error al crear el certificado público.';
$string['organizationalunitname'] = 'Unidad organizativa';
$string['organizationname'] = 'Organización';
$string['passivemode'] = 'Modo pasivo';
$string['plugindisabled'] = 'El complemento de autenticación SAML2 está deshabilitado';
$string['pluginname'] = 'SAML2';
$string['privatekeypass'] = 'Contraseña de clave de certificado privado';
$string['privatekeypass_help'] = 'Esto se utiliza para firmar el certificado local de Moodle; cambiarlo invalidará el certificado actual.';
$string['regenerateheading'] = 'Regenerar clave privada y certificado';
$string['regenerate_submit'] = 'Regenerado';
$string['requestedattributes'] = 'Atributos solicitados';
$string['requestedattributes_help'] = 'Algunos IdP necesitan que el SP declare qué atributos se solicitarán o serán necesarios. Agregue cada atributo en una nueva línea y estarán presentes en los metadatos del SP bajo la etiqueta <code>AttributeConsumingService</code>. Si desea que un campo sea obligatorio, coloque un espacio y luego * después de esa línea. {$a->example}';
$string['rememberidp'] = 'Recordar servicio de inicio de sesión';
$string['required'] = 'Este campo es obligatorio';
$string['requireint'] = 'Este campo es obligatorio y debe ser un número entero positivo.';
$string['showidplink'] = 'Mostrar enlace de IdP';
$string['showidplink_help'] = 'Esto mostrará el enlace del IdP cuando el sitio esté configurado.';
$string['source'] = 'Fuente: {$a}';
$string['spmetadata_help'] = '<a href=\'{$a}\'>Ver metadatos del proveedor de servicios</a> | <a href=\'{$a}?download=1\'>Descargar metadatos del SP</a>
<p>Es posible que tengas que entregárselo al administrador del IdP para que te incluya en la lista blanca.</p>';
$string['spmetadatasign_help'] = 'Firme los metadatos del SP.';
$string['spmetadatasign'] = 'Firma de metadatos del SP';
$string['spmetadata'] = 'Metadatos del SP';
$string['tempdirdefault'] = '/tmp/simplesaml';
$string['tempdir_help'] = 'Un directorio donde SimpleSAMLphp puede guardar archivos temporales';
$string['tempdir'] = 'Directorio temporal SimpleSAMLphp';
$string['sspversion'] = 'Versión SimpleSAMLphp';
$string['stateorprovincename'] = 'Estado o Provincia';
$string['status'] = 'Estado';
$string['suspendeduser'] = 'Ha iniciado sesión exitosamente como \'{$a}\' pero su cuenta ha sido suspendida en Moodle.';
$string['taskmetadatarefresh'] = 'Tarea de actualización de metadatos';
$string['test_auth_button_login'] = 'Inicio de sesión de proveedor de identidad';
$string['test_auth_button_logout'] = 'Cerrar sesión de proveedor de identidad';
$string['test_auth_str'] = 'La prueba está autenticada e inicia sesión';
$string['test_endpoint'] = 'URL de prueba de conexión';
$string['test_endpoint_desc'] = 'Ingrese una URL para probar la conexión para la redirección de IdP desde el navegador del cliente. Es posible que algunos usuarios o redes no tengan conectividad con el IdP según los permisos de la cuenta o de la red.';
$string['test_idp_conn'] = 'Probar la conexión de IdP';
$string['test_noticetestrequirements'] = 'Para utilizar esta prueba, el complemento debe estar configurado, habilitado y el modo de depuración debe estar habilitado en la configuración del complemento.';
$string['test_passive_str'] = 'Prueba usando isPassive';
$string['testdebuggingdisabled'] = 'Para utilizar esta página de prueba, la depuración de SAML debe estar activada';
$string['tolower'] = 'Coincidencia de casos';
$string['tolower:exact'] = 'Exacto';
$string['tolower:lowercase'] = 'Minúscula';
$string['tolower:caseandaccentinsensitive'] = 'No distingue entre mayúsculas y minúsculas';
$string['tolower:caseinsensitive'] = 'No distingue entre mayúsculas y minúsculas';
$string['tolower_help'] = '
<p>Exacto: la coincidencia distingue entre mayúsculas y minúsculas (predeterminado).</p>
<p>Minúsculas: aplica minúsculas al atributo de IdP antes de la coincidencia.</p>
<p>No distingue entre mayúsculas y minúsculas: ignora las mayúsculas al realizar la coincidencia.</p>';
$string['wrongauth'] = 'Ha iniciado sesión exitosamente como \'{$a}\' pero no está autorizado a acceder a Moodle.';
$string['auth_data_mapping'] = 'Mapeo de datos';
$string['auth_fieldlockfield'] = 'Valor de bloqueo ({$a})';
$string['auth_fieldmapping'] = 'Mapeo de datos ({$a})';
$string['auth_fieldlock_expl'] = '<p><b>Bloquear valor:</b> Si está habilitado, evitará que los usuarios y administradores de Moodle editen el campo directamente. Utilice esta opción si mantiene estos datos en el sistema de autenticación externo. </p>';
$string['auth_fieldlocks'] = 'Bloquear campos de usuario';
$string['auth_updatelocalfield'] = 'Actualizar local ({$a})';
$string['auth_updateremotefield'] = 'Actualización externa ({$a})';
$string['cannotmapfield'] = 'Colisión de mapeo detectada: dos campos se asignan al mismo elemento de calificación {$a}';
$string['locked'] = 'bloqueado';
$string['unlocked'] = 'desbloqueado';
$string['unlockedifempty'] = 'Desbloqueado si está vacío';
$string['update_never'] = 'Nunca';
$string['update_oncreate'] = 'sobre la creación';
$string['update_onlogin'] = 'En cada inicio de sesión';
$string['update_onupdate'] = 'En actualización';
$string['phone1'] = 'Teléfono';
$string['phone2'] = 'teléfono móvil';
$string['nameidpolicy'] = 'Política de ID de nombre';
$string['nameidpolicy_help'] = '';
$string['grouprules'] = 'Reglas del grupo';
$string['grouprules_help'] = '<p>Una lista de reglas para poder controlar el acceso según el valor del atributo del grupo.</p>
<p>Cada línea debe tener una regla en formato: {permitir o denegar} {atributo de grupos}={valor}.</p>
<p>La regla superior en la lista se aplicará primero.</p>
Ejemplo: <br/>
allow admins=yes<br>
denegar admins=no<br>
allow examrole=supervisor<br>
denegar biblioteca=vencido<br>';
$string['privacy:no_data_reason'] = 'El complemento de autenticación Saml2 no almacena ningún dato personal.';
$string['sha1'] = 'SHA1 heredado (peligroso)';
$string['sha256'] = 'SHA256';
$string['sha384'] = 'SHA384';
$string['sha512'] = 'SHA512';
$string['signaturealgorithm'] = 'Algoritmo de firma';
$string['signaturealgorithm_help'] = 'Este es el algoritmo que se utilizará para firmar solicitudes SAML. Advertencia: El algoritmo SHA1 solo se proporciona para compatibilidad con versiones anteriores, a menos que sea absolutamente necesario usarlo, se recomienda evitarlo y usar al menos SHA256 en su lugar.';
$string['selectloginservice'] = 'Seleccione un servicio de inicio de sesión';
$string['regenerateheader'] = 'Regenerar clave privada y certificado';
$string['regeneratewarning'] = '¡Advertencia! Generar un nuevo certificado sobrescribirá el actual y es posible que tengas que actualizar tu IDP';
$string['regeneratepath'] = 'Ruta de acceso del certificado: {$a}';
$string['regeneratesuccess'] = 'Clave privada y certificado regenerados exitosamente';
