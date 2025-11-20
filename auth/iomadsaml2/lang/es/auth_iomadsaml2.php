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
 * Anobody can login using saml2
 *
 * @package   auth_iomadsaml2
 * @copyright Brendan Heywood <brendan@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Strings for component 'auth_iomadsaml2', language 'es'
 *
 * @translator Alonso Arias <soporte@orioncloud.com.co>
 */

$string['alterlogout'] = 'URL de cierre de sesión alternativa';
$string['alterlogout_help'] = 'La URL a la que se redirigirá al usuario después de ejecutar todos los mecanismos internos de cierre de sesión';
$string['anyauth'] = 'Permitir cualquier tipo de autenticación';
$string['anyauth_help'] = 'Sí: ¿Permitir inicio de sesión SAML para todos los usuarios? No: Solo usuarios que tengan iomadsaml2 como su tipo.';
$string['anyauthotherdisabled'] = 'Ha iniciado sesión correctamente como \'{$a->username}\' pero su tipo de autenticación \'{$a->auth}\' está deshabilitado.';
$string['attemptsignout'] = 'Intentar cierre de sesión en IdP';
$string['attemptsignout_help'] = 'Esto intentará comunicarse con el IdP para enviar una solicitud de cierre de sesión';
$string['auth_iomadsaml2description'] = 'Autenticarse con un proveedor de identidad (IdP) SAML2';
$string['auth_iomadsaml2blockredirectdescription'] = 'Redirigir o mostrar mensaje a inicios de sesión SAML2 basados en restricciones de grupo configuradas';
$string['autocreate'] = 'Crear usuarios automáticamente';
$string['autocreate_help'] = 'Permitir la creación de usuarios de Moodle bajo demanda';
$string['autologin'] = 'Inicio de sesión automático';
$string['autologin_help'] = 'En páginas que permitan acceso de invitado sin iniciar sesión, iniciar sesión automáticamente a los usuarios en Moodle con una cuenta de usuario real si están autenticados en el IdP (usando autenticación pasiva).';
$string['autologinbysession'] = 'Verificar una vez por sesión';
$string['autologinbycookie'] = 'Verificar cuando la cookie especificada exista o cambie';
$string['autologincookie'] = 'Cookie de inicio de sesión automático';
$string['autologincookie_help'] = 'Nombre de la cookie utilizada para decidir cuándo intentar el inicio de sesión automático (solo relevante si la opción de cookie está seleccionada arriba).';
$string['availableidps'] = 'Seleccionar IdPs disponibles';
$string['availableidps_help'] = 'Si un xml de metadatos IdP contiene múltiples entidades IdP, deberá seleccionar qué entidades están disponibles
para que los usuarios inicien sesión.';
$string['blockredirectheading'] = 'Acciones de bloqueo de cuenta';
$string['attrsimple'] = 'Simplificar atributos';
$string['attrsimple_help'] = 'Varios IdP como ADFS usan claves de atributos largas como urns o nombres de esquemas xml con espacios de nombres. Si se establece en Sí, esto simplificará estos, por ejemplo, mapear http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname a simplemente \'givenname\'.';
$string['certificatedetails'] = 'Detalles del certificado';
$string['certificatedetailshelp'] = '<h1>Contenido del certificado público generado automáticamente por SAML2</h1><p>La ruta del certificado está aquí:</p>';
$string['checkcertificateexpiry'] = 'Vencimiento del certificado SAML';
$string['checkcertificateexpired'] = 'El certificado SAML venció hace {$a}';
$string['checkcertificatewarn'] = 'El certificado SAML vencerá en {$a}';
$string['checkcertificateok'] = 'El certificado SAML vencerá en {$a}';
$string['certificate_help'] = 'Regenerar la clave privada y el certificado utilizados por este SP. | <a href=\'{$a}\'>Ver certificado SP</a>';
$string['certificatelock_help'] = 'Bloquear los certificados evitará que se sobrescriban una vez generados.';
$string['certificatelock'] = 'Bloquear certificado';
$string['certificatelock_locked'] = 'El certificado está bloqueado';
$string['certificatelock_lockedmessage'] = 'Los certificados están actualmente bloqueados.';
$string['certificatelock_unlock'] = 'Desbloquear certificados';
$string['certificatelock_regenerate'] = '¡No se regeneran los certificados porque han sido bloqueados!';
$string['certificatelock_warning'] = 'Advertencia. Está a punto de bloquear los certificados, ¿está seguro de que desea hacer esto? <br> Los certificados no están bloqueados actualmente';
$string['certificate'] = 'Regenerar certificado';
$string['commonname'] = 'Nombre común';
$string['countryname'] = 'País';
$string['debug'] = 'Depuración';
$string['debug_help'] = '<p>Esto añade depuración adicional al registro normal de Moodle | <a href=\'{$a}\'>Ver configuración SSP</a></p>';
$string['duallogin'] = 'Inicio de sesión dual';
$string['duallogin_help'] = '
<p>Si está activado, los usuarios verán botones de inicio de sesión manual y SAML. Si está desactivado, siempre serán llevados directamente a la página de inicio de sesión IdP.</p>
<p>Si está en pasivo, los usuarios que ya estén autenticados en el IdP iniciarán sesión automáticamente, de lo contrario serán enviados a la página de inicio de sesión de Moodle.</p>
<p>Si está desactivado, los administradores aún pueden ver la página de inicio de sesión manual a través de /login/index.php?saml=off</p>
<p>Si está activado, las páginas externas pueden enlazar directamente a Moodle usando SAML, por ejemplo /course/view.php?id=45&saml=on</p>
<p>Si se establece en probar conexión IdP, se verificará la conectividad de red y, si es funcional, se iniciará el inicio de sesión SAML.</p>';
$string['emailtaken'] = 'No se puede crear una nueva cuenta, porque la dirección de correo electrónico {$a} ya está registrada';
$string['emailtakenupdate'] = 'Su correo electrónico no se actualizó, porque la dirección de correo electrónico {$a} ya está registrada';
$string['errorinvalidautologin'] = 'Solicitud de inicio de sesión automático no válida';
$string['errorparsingxml'] = 'Error al analizar XML: {$a}';
$string['exception'] = 'Excepción SAML2: {$a}';
$string['expirydays'] = 'Vencimiento en días';
$string['error'] = 'Error de inicio de sesión';
$string['fielddelimiter'] = 'Delimitador de campo';
$string['fielddelimiter_help'] = 'El delimitador a usar cuando un campo recibe una matriz de valores del IdP.';
$string['flaggedresponsetypemessage'] = 'Mostrar mensaje personalizado';
$string['flaggedresponsetyperedirect'] = 'Redirigir a URL externa';
$string['flagredirecturl'] = 'URL de redirección';
$string['flagredirecturl_help'] = '
<p>La URL a la que se redirigirá a un usuario si no se le permite acceder a Moodle según las restricciones de grupo configuradas.</p>
<p>(Solo se utiliza cuando el \'Tipo de respuesta\' es \'Redirigir a URL externa\'.)</p>';
$string['flagmessage'] = 'Mensaje de respuesta';
$string['flagmessage_help'] = '
<p>El mensaje a mostrar cuando a un usuario no se le permite acceder a Moodle según las restricciones de grupo configuradas.</p>
<p>(Solo se muestra cuando el \'Tipo de respuesta\' es \'Mostrar mensaje personalizado\'.)</p>';
$string['flagmessage_default'] = 'Ha iniciado sesión en su proveedor de identidad, sin embargo, esta cuenta tiene acceso limitado a Moodle, por favor contacte a su administrador para más detalles.';
$string['flagresponsetype'] = 'Tipo de respuesta de bloqueo de cuenta';
$string['flagresponsetype_help'] = 'Si se bloquea el acceso según las restricciones de grupo configuradas, ¿cómo debe responder Moodle?';
$string['idpattr_help'] = '¿Qué atributo IdP debe coincidir con un campo de usuario de Moodle?';
$string['idpattr'] = 'Mapeo IdP';
$string['idpmetadata_badurl'] = 'Metadatos no válidos en {$a}';
$string['idpmetadata_help'] = 'Para usar múltiples IdPs ingrese cada URL de metadatos públicos en una nueva línea.<br/>Para sobrescribir un nombre, coloque texto antes del http. por ejemplo, "Nombre de IdP forzado http://ssp.local/simplesaml/saml2/idp/metadata.php"';
$string['idpmetadata'] = 'XML de metadatos IdP o URL XML pública';
$string['idpmetadata_invalid'] = 'El XML del IdP no es válido';
$string['idpmetadata_noentityid'] = 'El XML del IdP no tiene entityID';
$string['idpmetadatarefresh_help'] = 'Ejecutar una tarea programada para actualizar los metadatos IdP desde la URL de metadatos IdP';
$string['idpmetadatarefresh'] = 'Actualización de metadatos IdP';
$string['idpnamedefault'] = 'Iniciar sesión a través de SAML2';
$string['idpnamedefault_varaible'] = 'Iniciar sesión a través de SAML2 ({$a})';
$string['idpname_help'] = 'por ejemplo miUNI - esto se detecta de los metadatos y se mostrará en la página de inicio de sesión dual (si está habilitada)';
$string['idpname'] = 'Sobrescritura de etiqueta IdP';
$string['localityname'] = 'Localidad';
$string['logdirdefault'] = '/tmp/';
$string['logdir_help'] = 'El directorio de registro en el que SSPHP escribirá, el archivo se llamará simplesamlphp.log';
$string['logdir'] = 'Directorio de registro';
$string['logtofile'] = 'Habilitar registro en archivo';
$string['logtofile_help'] = 'Activar esto redirigirá la salida de registro de SSPHP a un archivo en el directorio de registro';
$string['manageidpsheading'] = 'Administrar proveedores de identidad (IdPs) disponibles';
$string['mdlattr_help'] = '¿A qué campo de usuario de Moodle debe coincidir el atributo IdP?';
$string['mdlattr'] = 'Mapeo Moodle';
$string['wantassertionssigned'] = 'Requiere aserciones firmadas';
$string['wantassertionssigned_help'] = 'Si las aserciones recibidas por este SP deben estar firmadas';
$string['assertionsconsumerservices'] = 'Servicios consumidores de aserciones';
$string['assertionsconsumerservices_help'] = 'Lista de enlaces que el SP debe admitir';
$string['spentityid'] = 'ID de entidad';
$string['spentityid_help'] = 'Sobrescribir el ID de entidad del proveedor de servicios. En la mayoría de los casos deje en blanco y se usará un buen valor predeterminado.';
$string['allowcreate'] = 'Permitir crear';
$string['allowcreate_help'] = 'Permitir la creación de usuarios IdP bajo demanda';
$string['authncontext'] = 'AuthnContext';
$string['authncontext_help'] = 'Permite el aumento de aserciones. Deje en blanco a menos que sea necesario';
$string['metadatafetchfailed'] = 'Error al obtener metadatos: {$a}';
$string['metadatafetchfailedstatus'] = 'Error al obtener metadatos: Código de estado {$a}';
$string['metadatafetchfailedunknown'] = 'Error al obtener metadatos: Error cURL desconocido';
$string['multiidp:label:displayname'] = 'Nombre para mostrar';
$string['multiidp:label:alias'] = 'Alias';
$string['multiidp:label:active'] = 'Activo';
$string['multiidp:label:defaultidp'] = 'IdP predeterminado';
$string['multiidp:label:admin'] = 'Solo para usuarios administradores';
$string['multiidp:label:admin_help'] = 'Cualquier usuario que inicie sesión usando este IdP automáticamente se convertirá en administrador del sitio';
$string['multiidp:label:whitelist'] = 'Direcciones IP redirigidas';
$string['multiidp:label:whitelist_help'] = 'Si se establece, forzará a los clientes a este IdP. Formato: xxx.xxx.xxx.xxx/máscara de bits. Separe múltiples subredes en una nueva línea.';
$string['multiidpinfo'] = '
<ul>
<li>Un IdP solo se puede usar si está establecido como Activo</li>
<li>Cuando se haya activado el inicio de sesión dual, todos los IdPs activos se mostrarán en la página de inicio de sesión</li>
<li>Cuando un IdP se ha establecido como predeterminado y el inicio de sesión dual no está activado, este IdP se usará automáticamente a menos que se pase ?multiidp=on o saml=off en /login/index.php</li>
<li>Se puede dar un alias a un IdP, al ir a /login/index.php?idpalias={alias} el alias se puede pasar para usar directamente ese IdP</li>
</ul>';
$string['multiidpbuttons'] = 'Botones con iconos';
$string['multiidpdisplay'] = 'Tipo de visualización de múltiples IdP';
$string['multiidpdisplay_help'] = 'Si un xml de metadatos IdP contiene múltiples entidades IdP, ¿cómo se mostrará cada IdP disponible?';
$string['multiidpdropdown'] = 'Lista desplegable';
$string['nameidasattrib'] = 'Exponer NameID como atributo';
$string['nameidasattrib_help'] = 'El reclamo NameID se expondrá a SSPHP como un atributo llamado nameid';
$string['noattribute'] = 'Ha iniciado sesión correctamente pero no pudimos encontrar su atributo \'{$a}\' para asociarlo a una cuenta en Moodle.';
$string['noidpfound'] = 'El IdP \'{$a}\' no se encontró como un IdP configurado.';
$string['noredirectips'] = 'Restringir noredirect por IP';
$string['noredirectips_help'] = 'Cuando el inicio de sesión dual está desactivado y se establecen IPs, esto restringirá el uso de ?saml=off y ?noredirect=1 durante el inicio de sesión SAML a usuarios con subredes IP coincidentes.';
$string['nouser'] = 'Ha iniciado sesión correctamente como \'{$a}\' pero no tiene una cuenta en Moodle.';
$string['nullprivatecert'] = 'Error al crear el certificado privado.';
$string['nullpubliccert'] = 'Error al crear el certificado público.';
$string['organizationalunitname'] = 'Unidad organizacional';
$string['organizationname'] = 'Organización';
$string['passivemode'] = 'Modo pasivo';
$string['plugindisabled'] = 'El complemento de autenticación SAML2 está deshabilitado';
$string['pluginname'] = 'SAML2';
$string['privatekeypass'] = 'Contraseña de clave de certificado privado';
$string['privatekeypass_help'] = 'Esto se usa para firmar el certificado local de Moodle, cambiar esto invalidará el certificado actual.';
$string['regenerateheading'] = 'Regenerar clave privada y certificado';
$string['regenerate_submit'] = 'Regenerar';
$string['requestedattributes'] = 'Atributos solicitados';
$string['requestedattributes_help'] = 'Algunos IdP necesitan que el SP declare qué atributos se solicitarán o son requeridos. Agregue cada atributo en una nueva línea y estos estarán presentes en los metadatos del SP bajo la etiqueta <code>AttributeConsumingService</code>. Si desea que un campo sea obligatorio, coloque un espacio y luego * después de esa línea. {$a->example}';
$string['rememberidp'] = 'Recordar servicio de inicio de sesión';
$string['required'] = 'Este campo es obligatorio';
$string['requireint'] = 'Este campo es obligatorio y debe ser un entero positivo';
$string['showidplink'] = 'Mostrar enlace IdP';
$string['showidplink_help'] = 'Esto mostrará el enlace IdP cuando el sitio esté configurado.';
$string['source'] = 'Fuente: {$a}';
$string['spmetadata_help'] = '<a href=\'{$a}\'>Ver metadatos del proveedor de servicios</a> | <a href=\'{$a}?download=1\'>Descargar metadatos SP</a>
<p>Es posible que deba proporcionar esto al administrador del IdP para que lo incluya en la lista blanca.</p>';
$string['spmetadatasign_help'] = 'Firmar los metadatos SP.';
$string['spmetadatasign'] = 'Firma de metadatos SP';
$string['spmetadata'] = 'Metadatos SP';
$string['tempdirdefault'] = '/tmp/simplesaml';
$string['tempdir_help'] = 'Un directorio donde SimpleSAMLphp puede guardar archivos temporales';
$string['tempdir'] = 'Directorio temporal de SimpleSAMLphp';
$string['sspversion'] = 'Versión de SimpleSAMLphp';
$string['stateorprovincename'] = 'Estado o provincia';
$string['status'] = 'Estado';
$string['suspendeduser'] = 'Ha iniciado sesión correctamente como \'{$a}\' pero su cuenta ha sido suspendida en Moodle.';
$string['taskmetadatarefresh'] = 'Tarea de actualización de metadatos';
$string['test_auth_button_login'] = 'Inicio de sesión IdP';
$string['test_auth_button_logout'] = 'Cierre de sesión IdP';
$string['test_auth_str'] = 'Probar isAuthenticated e inicio de sesión';
$string['test_endpoint'] = 'URL de prueba de conexión';
$string['test_endpoint_desc'] = 'Ingrese una URL para probar la conexión para la redirección IdP desde el navegador del cliente. Algunos usuarios o redes pueden no tener conectividad al IdP según los permisos de cuenta o red.';
$string['test_idp_conn'] = 'Probar conexión IdP';
$string['test_noticetestrequirements'] = 'Para usar esta prueba, el complemento debe estar configurado, habilitado y el modo de depuración debe estar habilitado en la configuración del complemento.';
$string['test_passive_str'] = 'Probar usando isPassive';
$string['testdebuggingdisabled'] = 'Para usar esta página de prueba, la depuración SAML debe estar activada';
$string['tolower'] = 'Coincidencia de mayúsculas/minúsculas';
$string['tolower:exact'] = 'Exacto';
$string['tolower:lowercase'] = 'Minúsculas';
$string['tolower:caseandaccentinsensitive'] = 'Insensible a mayúsculas/minúsculas y acentos';
$string['tolower:caseinsensitive'] = 'Insensible a mayúsculas/minúsculas';
$string['tolower_help'] = '
<p>Exacto: la coincidencia distingue entre mayúsculas y minúsculas (predeterminado).</p>
<p>Minúsculas: aplica minúsculas al atributo IdP antes de la coincidencia.</p>
<p>Insensible a mayúsculas/minúsculas: ignora mayúsculas/minúsculas al hacer coincidir.</p>';
$string['wrongauth'] = 'Ha iniciado sesión correctamente como \'{$a}\' pero no está autorizado para acceder a Moodle.';
$string['auth_data_mapping'] = 'Mapeo de datos';
$string['auth_fieldlockfield'] = 'Bloquear valor ({$a})';
$string['auth_fieldmapping'] = 'Mapeo de datos ({$a})';
$string['auth_fieldlock_expl'] = '<p><b>Bloquear valor:</b> Si está habilitado, evitará que los usuarios y administradores de Moodle editen el campo directamente. Use esta opción si está manteniendo estos datos en el sistema de autenticación externo. </p>';
$string['auth_fieldlocks'] = 'Bloquear campos de usuario';
$string['auth_updatelocalfield'] = 'Actualizar local ({$a})';
$string['auth_updateremotefield'] = 'Actualizar externo ({$a})';
$string['cannotmapfield'] = 'Colisión de mapeo detectada - dos campos se asignan al mismo elemento de calificación {$a}';
$string['locked'] = 'Bloqueado';
$string['unlocked'] = 'Desbloqueado';
$string['unlockedifempty'] = 'Desbloqueado si está vacío';
$string['update_never'] = 'Nunca';
$string['update_oncreate'] = 'En la creación';
$string['update_onlogin'] = 'En cada inicio de sesión';
$string['update_onupdate'] = 'En la actualización';
$string['phone1'] = 'Teléfono';
$string['phone2'] = 'Teléfono móvil';
$string['nameidpolicy'] = 'Política NameID';
$string['nameidpolicy_help'] = '';
$string['grouprules'] = 'Reglas de grupo';
$string['grouprules_help'] = '<p>Una lista de reglas para poder controlar el acceso según el valor del atributo de grupo.</p>
<p>Cada línea debe tener una regla en formato: {permitir o denegar} {atributo de grupos}={valor}.</p>
<p>La regla más alta en la lista se aplicará primero.</p>
Ejemplo: <br/>
allow admins=yes<br>
deny admins=no<br>
allow examrole=proctor<br>
deny library=overdue<br>';
/*
 * Privacy provider (GDPR)
 */
$string["privacy:no_data_reason"] = "El complemento de autenticación Saml2 no almacena ningún dato personal.";

/*
 * Signing Algorithm
 */
$string['sha1'] = 'SHA1 heredado (Peligroso)';
$string['sha256'] = 'SHA256';
$string['sha384'] = 'SHA384';
$string['sha512'] = 'SHA512';
$string['signaturealgorithm'] = 'Algoritmo de firma';
$string['signaturealgorithm_help'] = 'Este es el algoritmo que se usará para firmar solicitudes SAML. Advertencia: El algoritmo SHA1 solo se proporciona para compatibilidad con versiones anteriores, a menos que absolutamente deba usarlo, se recomienda evitarlo y usar al menos SHA256 en su lugar.';
$string['selectloginservice'] = 'Seleccione un servicio de inicio de sesión';
$string['regenerateheader'] = 'Regenerar clave privada y certificado';
$string['regeneratewarning'] = '¡Advertencia! Generar un nuevo certificado sobrescribirá el actual y es posible que deba actualizar su IDP';
$string['regeneratepath'] = 'Ruta del certificado: {$a}';
$string['regenerateheader'] = 'Regenerar clave privada y certificado';
$string['regeneratesuccess'] = 'Clave privada y certificado regenerados correctamente';
