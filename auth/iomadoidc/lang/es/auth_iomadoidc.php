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
 * Spanish language strings.
 *
 * @package auth_iomadoidc
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'OpenID Connect';
$string['auth_iomadoidcdescription'] = 'El complemento de autenticación OpenID Connect proporciona funcionalidad de inicio de sesión único utilizando un IdP configurable.';

// Configuration pages.
$string['settings_page_other_settings'] = 'Otras opciones';
$string['settings_page_application'] = 'IdP y autenticación';
$string['settings_page_cleanup_iomadoidc_tokens'] = 'Limpiar tokens OpenID Connect';
$string['settings_page_field_mapping'] = 'Mapeos de campos';
$string['heading_basic'] = 'Configuración básica';
$string['heading_basic_desc'] = '';
$string['heading_additional_options'] = 'Opciones adicionales';
$string['heading_additional_options_desc'] = '';
$string['heading_user_restrictions'] = 'Restricciones de usuario';
$string['heading_user_restrictions_desc'] = '';
$string['heading_sign_out'] = 'Integración de cierre de sesión';
$string['heading_sign_out_desc'] = '';
$string['heading_display'] = 'Visualización';
$string['heading_display_desc'] = '';
$string['heading_debugging'] = 'Depuración';
$string['heading_debugging_desc'] = '';
$string['idptype'] = 'Tipo de Proveedor de Identidad (IdP)';
$string['idptype_help'] = 'Actualmente se admiten tres tipos de IdP:
<ul>
<li><b>Microsoft Entra ID (v1.0)</b>: Microsoft Entra ID con puntos de acceso oauth2 v1.0, por ejemplo, https://login.microsoftonline.com/common/oauth2/authorize.</li>
<li><b>Microsoft identity platform (v2.0)</b>: Microsoft Entra ID con puntos de acceso oauth2 v2.0, por ejemplo, https://login.microsoftonline.com/common/oauth2/v2.0/authorize.</li>
<li><b>Otro</b>: cualquier IdP que no sea de Microsoft.</li>
</ul>
Las diferencias entre las opciones <b>Microsoft Entra ID (v1.0)</b> y <b>Microsoft identity platform (v2.0)</b> se pueden encontrar en <a href="https://docs.microsoft.com/en-us/azure/active-directory/azuread-dev/azure-ad-endpoint-comparison">https://docs.microsoft.com/en-us/azure/active-directory/azuread-dev/azure-ad-endpoint-comparison</a>.<br/>
En particular, la aplicación configurada puede usar <b>certificado</b> además de <b>secreto</b> para autenticación cuando se usa el IdP <b>Microsoft identity platform (v2.0)</b>.<br/>
Los puntos de acceso de autorización y token deben configurarse de acuerdo con el tipo de IdP configurado.';
$string['idp_type_microsoft_entra_id'] = 'Microsoft Entra ID (v1.0)';
$string['idp_type_microsoft_identity_platform'] = 'Microsoft identity platform (v2.0)';
$string['idp_type_other'] = 'Otro';
$string['cfg_authenticationlink_desc'] = '<a href="{$a}" target="_blank">Enlace a configuración de IdP y autenticación</a>';
$string['authendpoint'] = 'Punto de acceso de autorización';
$string['authendpoint_help'] = 'La URI del punto de acceso de autorización de su IdP a utilizar.<br/>
Tenga en cuenta que si el sitio se va a configurar para permitir que usuarios de otros inquilinos accedan, no se puede usar un punto de acceso de autorización específico del inquilino.';
$string['cfg_autoappend_key'] = 'Anexar automáticamente';
$string['cfg_autoappend_desc'] = 'Anexar automáticamente esta cadena al iniciar sesión usuarios mediante el método de autenticación "Credenciales de contraseña del propietario del recurso". Esto es útil cuando su IdP requiere un dominio común, pero no desea exigir a los usuarios que lo escriban al iniciar sesión. Por ejemplo, si el usuario completo de OpenID Connect es "james@example.com" y usted ingresa "@example.com" aquí, el usuario solo tendrá que ingresar "james" como su nombre de usuario. <br /><b>Nota:</b> En el caso de que existan nombres de usuario en conflicto, es decir, existe un usuario de Moodle con el mismo nombre, se utiliza la prioridad del complemento de autenticación para determinar qué usuario prevalece.';
$string['clientid'] = 'ID de aplicación';
$string['clientid_help'] = 'El ID de aplicación / cliente registrado en el IdP.';
$string['clientauthmethod'] = 'Método de autenticación del cliente';
$string['clientauthmethod_help'] = '<ul>
<li>Los IdP de todos los tipos pueden usar el método de autenticación "<b>Secreto</b>".</li>
<li>Los IdP del tipo <b>Microsoft identity platform (v2.0)</b> pueden usar adicionalmente el método de autenticación <b>Certificado</b>.</li>
</ul>';
$string['auth_method_secret'] = 'Secreto';
$string['auth_method_certificate'] = 'Certificado';
$string['clientsecret'] = 'Secreto del cliente';
$string['clientsecret_help'] = 'Al usar el método de autenticación <b>secreto</b>, este es el secreto del cliente en el IdP. En algunos proveedores, también se le conoce como clave.';
$string['clientprivatekey'] = 'Clave privada del certificado de cliente';
$string['clientprivatekey_help'] = 'Al usar el método de autenticación <b>certificado</b> y la fuente de certificado <b>Texto plano</b>, esta es la clave privada del certificado utilizado para autenticarse con el IdP.';
$string['clientcert'] = 'Clave pública del certificado de cliente';
$string['clientcert_help'] = 'Al usar el método de autenticación <b>certificado</b> y la fuente de certificado <b>Texto plano</b>, esta es la clave pública, o certificado, utilizado para autenticarse con el IdP.';
$string['clientcertsource'] = 'Fuente del certificado';
$string['clientcertsource_help'] = 'Al usar el método de autenticación <b>certificado</b>, esto se usa para definir de dónde recuperar el certificado.
<ul>
<li>La fuente <b>Texto plano</b> requiere que el contenido del archivo de certificado/clave privada se configure en las configuraciones de área de texto posteriores.</li>
<li>La fuente <b>Nombre de archivo</b> requiere que los archivos de certificado/clave privada existan en una carpeta <b>microsoft_certs</b> en la carpeta de datos de Moodle.</li>
</ul>';
$string['cert_source_text'] = 'Texto plano';
$string['cert_source_path'] = 'Nombre de archivo';
$string['clientprivatekeyfile'] = 'Nombre de archivo de la clave privada del certificado de cliente';
$string['clientprivatekeyfile_help'] = 'Al usar el método de autenticación <b>certificado</b> y la fuente de certificado <b>Nombre de archivo</b>, este es el nombre del archivo de la clave privada utilizada para autenticarse con el IdP. El archivo debe estar presente en una carpeta <b>microsoft_certs</b> en la carpeta de datos de Moodle.';
$string['clientcertfile'] = 'Nombre de archivo de la clave pública del certificado de cliente';
$string['clientcertfile_help'] = 'Al usar el método de autenticación <b>certificado</b> y la fuente de certificado <b>Nombre de archivo</b>, este es el nombre del archivo de la clave pública, o certificado, utilizado para autenticarse con el IdP. El archivo debe estar presente en una carpeta <b>microsoft_certs</b> en la carpeta de datos de Moodle.';
$string['clientcertpassphrase'] = 'Frase de contraseña del certificado de cliente';
$string['clientcertpassphrase_help'] = 'Si la clave privada del certificado de cliente está cifrada, esta es la frase de contraseña para descifrarla.';
$string['cfg_domainhint_key'] = 'Sugerencia de dominio';
$string['cfg_domainhint_desc'] = 'Al usar el flujo de inicio de sesión <b>Código de autorización</b>, pasar este valor como el parámetro "domain_hint". "domain_hint" es utilizado por algunos IdP de OpenID Connect para facilitar el proceso de inicio de sesión a los usuarios. Consulte con su proveedor para ver si admiten este parámetro.';
$string['cfg_err_invalidauthendpoint'] = 'Punto de acceso de autorización no válido';
$string['cfg_err_invalidtokenendpoint'] = 'Punto de acceso de token no válido';
$string['cfg_err_invalidclientid'] = 'ID de cliente no válido';
$string['cfg_err_invalidclientsecret'] = 'Secreto de cliente no válido';
$string['cfg_forceredirect_key'] = 'Forzar redireccionamiento';
$string['cfg_forceredirect_desc'] = 'Si está habilitado, omitirá la página de índice de inicio de sesión y redirigirá a la página de OpenID Connect. Se puede omitir con el parámetro de URL ?noredirect=1';
$string['cfg_icon_key'] = 'Icono';
$string['cfg_icon_desc'] = 'Un icono para mostrar junto al nombre del proveedor en la página de inicio de sesión.';
$string['cfg_iconalt_o365'] = 'Icono de Microsoft 365';
$string['cfg_iconalt_locked'] = 'Icono de bloqueado';
$string['cfg_iconalt_lock'] = 'Icono de candado';
$string['cfg_iconalt_go'] = 'Círculo verde';
$string['cfg_iconalt_stop'] = 'Círculo rojo';
$string['cfg_iconalt_user'] = 'Icono de usuario';
$string['cfg_iconalt_user2'] = 'Icono de usuario alternativo';
$string['cfg_iconalt_key'] = 'Icono de llave';
$string['cfg_iconalt_group'] = 'Icono de grupo';
$string['cfg_iconalt_group2'] = 'Icono de grupo alternativo';
$string['cfg_iconalt_mnet'] = 'Icono de MNET';
$string['cfg_iconalt_userlock'] = 'Icono de usuario con candado';
$string['cfg_iconalt_plus'] = 'Icono de más';
$string['cfg_iconalt_check'] = 'Icono de marca de verificación';
$string['cfg_iconalt_rightarrow'] = 'Icono de flecha hacia la derecha';
$string['cfg_customicon_key'] = 'Icono personalizado';
$string['cfg_customicon_desc'] = 'Si desea usar su propio icono, súbalo aquí. Esto anula cualquier icono elegido anteriormente. <br /><br /><b>Notas sobre el uso de iconos personalizados:</b><ul><li>Esta imagen <b>no</b> se redimensionará en la página de inicio de sesión, por lo que recomendamos subir una imagen no mayor de 35x35 píxeles.</li><li>Si ha subido un icono personalizado y desea volver a uno de los iconos predeterminados, haga clic en el icono personalizado en el cuadro de arriba, luego haga clic en "Eliminar", luego en "Aceptar" y luego en "Guardar cambios" en la parte inferior de este formulario. El icono predeterminado seleccionado ahora aparecerá en la página de inicio de sesión de Moodle.</li></ul>';
$string['cfg_debugmode_key'] = 'Registrar mensajes de depuración';
$string['cfg_debugmode_desc'] = 'Si está habilitado, se registrará información en el registro de Moodle que puede ayudar a identificar problemas.';
$string['cfg_loginflow_key'] = 'Flujo de inicio de sesión';
$string['cfg_loginflow_authcode'] = 'Flujo de código de autorización <b>(recomendado)</b>';
$string['cfg_loginflow_authcode_desc'] = 'Usando este flujo, el usuario hace clic en el nombre del IdP (consulte "Nombre para mostrar del proveedor" arriba) en la página de inicio de sesión de Moodle y es redirigido al proveedor para iniciar sesión. Una vez iniciado sesión exitosamente, el usuario es redirigido de vuelta a Moodle donde el inicio de sesión de Moodle se realiza de manera transparente. Esta es la forma más estandarizada y segura para que el usuario inicie sesión.';
$string['cfg_loginflow_rocreds'] = 'Concesión de credenciales de contraseña del propietario del recurso <b>(obsoleto)</b>';
$string['cfg_loginflow_rocreds_desc'] = '<b>Este flujo de inicio de sesión está obsoleto y se eliminará del complemento pronto.</b><br/>Usando este flujo, el usuario ingresa su nombre de usuario y contraseña en el formulario de inicio de sesión de Moodle como lo haría con un inicio de sesión manual. Esto autorizará al usuario con el IdP, pero no creará una sesión en el sitio del IdP. Por ejemplo, si usa Microsoft 365 con OpenID Connect, el usuario iniciará sesión en Moodle pero no en las aplicaciones web de Microsoft 365. Se recomienda usar la solicitud de autorización si desea que los usuarios inicien sesión tanto en Moodle como en el IdP. Tenga en cuenta que no todos los IdP admiten este flujo. Esta opción solo debe usarse cuando otros tipos de concesión de autorización no estén disponibles.';
$string['cfg_silentloginmode_key'] = 'Modo de inicio de sesión silencioso';
$string['cfg_silentloginmode_desc'] = 'Si está habilitado, Moodle intentará usar la sesión activa de un usuario autenticado en el punto de acceso de autorización configurado para iniciar sesión al usuario.<br/>
Para usar esta característica, se requieren las siguientes configuraciones:
<ul>
<li><b>Forzar a los usuarios a iniciar sesión</b> (forcelogin) en la <a href="{$a}" target="_blank">sección de Políticas del sitio</a> está habilitado.</li>
<li>La configuración <b>Forzar redireccionamiento</b> (auth_iomadoidc/forceredirect) arriba está habilitada.</li>
</ul>
Para evitar que Moodle intente usar cuentas personales o cuentas de otros inquilinos para iniciar sesión, también se recomienda usar puntos de acceso específicos del inquilino, en lugar de genéricos que usan rutas "common" u "organization", etc.<br/>
<br/>
Para IdP de Microsoft, la experiencia del usuario es la siguiente:
<ul>
<li>Si no se encuentra ninguna sesión de usuario activa, se mostrará la página de inicio de sesión de Moodle.</li>
<li>Si solo se encuentra una sesión de usuario activa, y el usuario tiene acceso a la aplicación de Entra ID (es decir, el usuario es del mismo inquilino o es un usuario invitado del inquilino), el usuario iniciará sesión en Moodle automáticamente usando SSO.</li>
<li>Si solo se encuentra una sesión de usuario activa, pero el usuario no tiene acceso a la aplicación de Entra ID (por ejemplo, el usuario es de un inquilino diferente, o la aplicación requiere asignación de usuario y el usuario no está asignado), se mostrará la página de inicio de sesión de Moodle.</li>
<li>Si hay múltiples sesiones de usuario activas que tienen acceso a la aplicación de Entra ID, se mostrará una página para permitir que el usuario seleccione la cuenta con la que iniciar sesión.</li>
</ul>';
$string['iomadoidcresource'] = 'Recurso';
$string['iomadoidcresource_help'] = 'El recurso de OpenID Connect para el cual enviar la solicitud.<br/>
<b>Nota</b> este parámetro no es compatible con el tipo de IdP <b>Microsoft identity platform (v2.0)</b>.';
$string['iomadoidcscope'] = 'Ámbito';
$string['iomadoidcscope_help'] = 'El ámbito OIDC a utilizar.';
$string['secretexpiryrecipients'] = 'Destinatarios de notificación de caducidad del secreto';
$string['secretexpiryrecipients_help'] = 'Una lista separada por comas de direcciones de correo electrónico a las que enviar notificaciones de caducidad del secreto.<br/>
Si no se ingresa ninguna dirección de correo electrónico, se notificará al administrador principal del sitio.';
$string['cfg_opname_key'] = 'Nombre para mostrar del proveedor';
$string['cfg_opname_desc'] = 'Esta es una etiqueta dirigida al usuario final que identifica el tipo de credenciales que el usuario debe usar para iniciar sesión. Esta etiqueta se usa en todas las partes de este complemento dirigidas al usuario para identificar a su proveedor.';
$string['cfg_redirecturi_key'] = 'URI de redireccionamiento';
$string['cfg_redirecturi_desc'] = 'Esta es la URI para registrar como "URI de redireccionamiento". Su IdP de OpenID Connect debe solicitar esto al registrar Moodle como cliente. <br /><b>NOTA:</b> Debe ingresar esto en su IdP de OpenID Connect *exactamente* como aparece aquí. Cualquier diferencia impedirá los inicios de sesión usando OpenID Connect.';
$string['tokenendpoint'] = 'Punto de acceso de token';
$string['tokenendpoint_help'] = 'La URI del punto de acceso de token de su IdP a utilizar.<br/>
Tenga en cuenta que si el sitio se va a configurar para permitir que usuarios de otros inquilinos accedan, no se puede usar un punto de acceso de token específico del inquilino.';
$string['cfg_userrestrictions_key'] = 'Restricciones de usuario';
$string['cfg_userrestrictions_desc'] = 'Solo permitir que inicien sesión usuarios que cumplan ciertas restricciones. <br /><b>Cómo usar las restricciones de usuario: </b> <ul><li>Ingrese un patrón de <a href="https://en.wikipedia.org/wiki/Regular_expression">expresión regular</a> que coincida con los nombres de usuario de los usuarios que desea permitir.</li><li>Ingrese un patrón por línea</li><li>Si ingresa múltiples patrones, se permitirá a un usuario si coincide con CUALQUIERA de los patrones.</li><li>El carácter "/" debe escaparse con "\".</li><li>Si no ingresa ninguna restricción arriba, todos los usuarios que puedan iniciar sesión en el IdP de OpenID Connect serán aceptados por Moodle.</li><li>A cualquier usuario que no coincida con ningún patrón ingresado se le impedirá iniciar sesión usando OpenID Connect.</li></ul>';
$string['cfg_userrestrictionscasesensitive_key'] = 'Restricciones de usuario sensibles a mayúsculas';
$string['cfg_userrestrictionscasesensitive_desc'] = 'Esto controla si se usa la opción "/i" en expresión regular en la coincidencia de restricción de usuario.<br/>Si está habilitado, todas las comprobaciones de restricción de usuario se realizarán con sensibilidad a mayúsculas. Tenga en cuenta que si esto está deshabilitado, cualquier patrón sobre casos de letras será ignorado.';
$string['cfg_signoffintegration_key'] = 'Cierre de sesión único (de Moodle a IdP)';
$string['cfg_signoffintegration_desc'] = 'Si la opción está habilitada, cuando un usuario de Moodle conectado al IdP configurado cierre sesión en Moodle, la integración activará una solicitud en el punto de acceso de cierre de sesión a continuación, intentando cerrar la sesión del usuario del IdP también.<br/>
Tenga en cuenta que para la integración con Microsoft Entra ID, la URL del sitio de Moodle ({$a}) debe agregarse como una URI de redireccionamiento en la aplicación de Azure creada para la integración de Moodle y Microsoft 365.';
$string['cfg_logoutendpoint_key'] = 'Punto de acceso de cierre de sesión del IdP';
$string['cfg_logoutendpoint_desc'] = 'La URI del punto de acceso de cierre de sesión de su IdP a utilizar.';
$string['cfg_frontchannellogouturl_key'] = 'URL de cierre de sesión de canal frontal';
$string['cfg_frontchannellogouturl_desc'] = 'Esta es la URL que su IdP debe activar cuando intenta cerrar la sesión de los usuarios de Moodle.<br/>
Para Microsoft Entra ID / Microsoft identity platform, la configuración se llama "URL de cierre de sesión de canal frontal" y es configurable en la aplicación de Azure.';
$string['cfg_field_mapping_desc'] = 'Los datos del perfil de usuario se pueden mapear desde el IdP de OpenID Connect a Moodle. Los campos remotos disponibles para mapear dependen en gran medida del tipo de IdP.<br/>
<ul>
<li>Algunos campos de perfil básicos están disponibles desde los claims del token de acceso y el token de ID de todos los tipos de IdP.</li>
<li>Si se configura el tipo de IdP de Microsoft (ya sea v1.0 o v2.0), los datos de perfil adicionales se pueden poner a disposición a través de llamadas a Graph API instalando y configurando el <a href="https://moodle.org/plugins/local_o365">complemento de integración de Microsoft 365 (local_o365)</a>.</li>
<li>Si la característica de sincronización de perfil de SDS está habilitada en el complemento local_o365, ciertos campos de perfil se pueden sincronizar desde SDS a Moodle cuando se ejecuta la tarea programada "Sincronizar con SDS", y no sucederá cuando se ejecute la tarea programada "Sincronizar usuarios con Microsoft Entra ID", ni cuando el usuario inicie sesión.</li>
</ul>

Los claims disponibles de los tokens de ID y acceso varían según el tipo de IdP, pero la mayoría de los IdP permiten cierto nivel de personalización de los claims. La documentación sobre IdP de Microsoft se encuentra vinculada a continuación:
<ul>
<li><a target="_blank" href="https://learn.microsoft.com/en-us/entra/identity-platform/access-token-claims-reference">Claims de token de acceso</a></li>
<li><a target="_blank" href="https://learn.microsoft.com/en-us/entra/identity-platform/id-token-claims-reference">Claims de token de ID</a></li>
<li><a target="_blank" href="https://learn.microsoft.com/en-us/entra/identity-platform/optional-claims-reference">Configuración de claims opcionales</a>: Tenga en cuenta que "Email" es un claim opcional en el tipo de IdP Microsoft Entra ID (v1.0).</li>
</ul>';

$string['cfg_cleanupiomadoidctokens_key'] = 'Limpiar tokens OpenID Connect';
$string['cfg_cleanupiomadoidctokens_desc'] = 'Si sus usuarios experimentan problemas al iniciar sesión con su cuenta de Microsoft 365, intente limpiar los tokens de OpenID Connect. Esto elimina tokens extraviados e incompletos que pueden causar errores. ADVERTENCIA: Esto puede interrumpir inicios de sesión en proceso, por lo que es mejor hacerlo durante el tiempo de inactividad.';
$string['settings_section_basic'] = 'Configuración básica';
$string['settings_section_authentication'] = 'Autenticación';
$string['settings_section_endpoints'] = 'Puntos de acceso';
$string['settings_section_other_params'] = 'Otros parámetros';
$string['settings_section_secret_expiry_notification'] = 'Notificación de caducidad del secreto';
$string['authentication_and_endpoints_saved'] = 'Configuración de autenticación y puntos de acceso actualizada.';
$string['application_updated'] = 'La configuración de la aplicación OpenID Connect se ha actualizado.';
$string['application_updated_microsoft'] = 'La configuración de la aplicación OpenID Connect se actualizó.<br/>
<span class="warning" style="color: red;">El administrador de Azure deberá <b>Proporcionar consentimiento de administrador</b> y <b>Verificar configuración</b> nuevamente en la <a href="{$a}" target="_blank">página de configuración de integración de Microsoft 365</a> si se actualizan las configuraciones de "Tipo de Proveedor de Identidad (IdP)" o "Método de autenticación del cliente".</span>';
$string['application_not_changed'] = 'La configuración de la aplicación OpenID Connect no se modificó.';

$string['event_debug'] = 'Mensaje de depuración';

$string['task_cleanup_iomadoidc_state_and_token'] = 'Limpiar estado OIDC y token no válido';

$string['errorauthdisconnectemptypassword'] = 'La contraseña no puede estar vacía';
$string['errorauthdisconnectemptyusername'] = 'El nombre de usuario no puede estar vacío';
$string['errorauthdisconnectusernameexists'] = 'Ese nombre de usuario ya está en uso. Por favor, elija uno diferente.';
$string['errorauthdisconnectnewmethod'] = 'Usar método de inicio de sesión';
$string['errorauthdisconnectinvalidmethod'] = 'Se recibió un método de inicio de sesión no válido.';
$string['errorauthdisconnectifmanual'] = 'Si usa el método de inicio de sesión manual, ingrese las credenciales a continuación.';
$string['errorauthgeneral'] = 'Hubo un problema al iniciar sesión. Póngase en contacto con su administrador para obtener ayuda.';
$string['errorauthinvalididtoken'] = 'Se recibió un id_token no válido.';
$string['errorauthloginfailednouser'] = 'Inicio de sesión no válido: Usuario no encontrado en Moodle. Si este sitio tiene habilitada la configuración "authpreventaccountcreation", esto puede significar que necesita que un administrador cree una cuenta para usted primero.';
$string['errorauthloginfaileddupemail'] = 'Inicio de sesión no válido: Existe una cuenta en este Moodle con la misma dirección de correo electrónico que la cuenta que intenta crear, y la configuración "Permitir cuentas con el mismo correo electrónico" (allowaccountssameemail) está deshabilitada.';
$string['errorauthnoauthcode'] = 'No se recibió ningún código de autorización del servidor de identidad. Los registros de error pueden tener más información.';
$string['errorauthnocredsandendpoints'] = 'Por favor, configure las credenciales del cliente y los puntos de acceso de OpenID Connect.';
$string['errorauthnohttpclient'] = 'Por favor, configure un cliente HTTP.';
$string['errorauthnoidtoken'] = 'No se recibió el id_token de OpenID Connect.';
$string['errorauthnoaccesstoken'] = 'No se recibió el token de acceso.';
$string['errorauthunknownstate'] = 'Estado desconocido.';
$string['errorauthuseralreadyconnected'] = 'Ya está conectado a un usuario diferente de OpenID Connect.';
$string['errorauthuserconnectedtodifferent'] = 'El usuario de OpenID Connect que se autenticó ya está conectado a un usuario de Moodle.';
$string['errorbadloginflow'] = 'Se especificó un tipo de autenticación no válido. Nota: Si está recibiendo esto después de una instalación o actualización reciente, por favor limpie su caché de Moodle.';
$string['errorjwtbadpayload'] = 'No se pudo leer la carga útil JWT.';
$string['errorjwtcouldnotreadheader'] = 'No se pudo leer el encabezado JWT';
$string['errorjwtempty'] = 'Se recibió un JWT vacío o que no es cadena.';
$string['errorjwtinvalidheader'] = 'Encabezado JWT no válido';
$string['errorjwtmalformed'] = 'Se recibió un JWT con formato incorrecto.';
$string['errorjwtunsupportedalg'] = 'JWS Alg o JWE no compatible';
$string['errorlogintoconnectedaccount'] = 'Este usuario de Microsoft 365 está conectado a una cuenta de Moodle, pero el inicio de sesión de OpenID Connect no está habilitado para esta cuenta de Moodle. Por favor, inicie sesión en la cuenta de Moodle usando el método de autenticación definido de la cuenta para usar las características de Microsoft 365';
$string['erroriomadoidcnotenabled'] = 'El complemento de autenticación OpenID Connect no está habilitado.';
$string['errornodisconnectionauthmethod'] = 'No se puede desconectar porque no hay un complemento de autenticación habilitado al que recurrir (ya sea el método de inicio de sesión anterior del usuario o el método de inicio de sesión manual).';
$string['erroriomadoidcclientinvalidendpoint'] = 'Se recibió una URI de punto de acceso no válida.';
$string['erroriomadoidcclientnocreds'] = 'Por favor, configure las credenciales del cliente con setcreds';
$string['erroriomadoidcclientnoauthendpoint'] = 'No se estableció ningún punto de acceso de autorización. Por favor, configure con $this->setendpoints';
$string['erroriomadoidcclientnotokenendpoint'] = 'No se estableció ningún punto de acceso de token. Por favor, configure con $this->setendpoints';
$string['erroriomadoidcclientinsecuretokenendpoint'] = 'El punto de acceso de token debe usar SSL/TLS para esto.';
$string['errorrestricted'] = 'Este sitio tiene restricciones sobre los usuarios que pueden iniciar sesión con OpenID Connect. Estas restricciones actualmente le impiden completar este intento de inicio de sesión.';
$string['errorucpinvalidaction'] = 'Se recibió una acción no válida.';
$string['erroriomadoidccall'] = 'Error en OpenID Connect. Por favor, revise los registros para obtener más información.';
$string['erroriomadoidccall_message'] = 'Error en OpenID Connect: {$a}';
$string['errorinvalidredirect_message'] = 'La URL a la que está intentando redirigir no existe.';
$string['errorinvalidcertificatesource'] = 'Fuente de certificado no válida';
$string['error_empty_tenantnameorguid'] = 'El nombre o GUID del inquilino no puede estar vacío al usar IdP de Microsoft Entra ID (v1.0) o Microsoft identity platform (v2.0).';
$string['error_invalid_client_authentication_method'] = 'Método de autenticación del cliente no válido';
$string['error_empty_client_secret'] = 'El secreto del cliente no puede estar vacío al usar el método de autenticación "secreto"';
$string['error_empty_client_private_key'] = 'La clave privada del certificado de cliente no puede estar vacía al usar el método de autenticación "certificado"';
$string['error_empty_client_cert'] = 'La clave pública del certificado de cliente no puede estar vacía al usar el método de autenticación "certificado"';
$string['error_empty_client_private_key_file'] = 'El archivo de clave privada del certificado de cliente no puede estar vacío al usar el método de autenticación "certificado"';
$string['error_empty_client_cert_file'] = 'El archivo de clave pública del certificado de cliente no puede estar vacío al usar el método de autenticación "certificado"';
$string['error_empty_tenantname_or_guid'] = 'El nombre o GUID del inquilino no puede estar vacío al usar el método de autenticación "certificado"';
$string['error_endpoint_mismatch_auth_endpoint'] = 'El punto de acceso de autorización configurado no coincide con el tipo de IdP configurado.<br/>
<ul>
<li>Al usar el tipo de IdP "Microsoft Entra ID (v1.0)", use el punto de acceso v1.0, por ejemplo, https://login.microsoftonline.com/common/oauth2/authorize</li>
<li>Al usar el tipo de IdP "Microsoft identity platform (v2.0)", use el punto de acceso v2.0, por ejemplo, https://login.microsoftonline.com/common/oauth2/v2.0/authorize</li>
</ul>';
$string['error_endpoint_mismatch_token_endpoint'] = 'El punto de acceso de token configurado no coincide con el tipo de IdP configurado.<br/>
<ul>
<li>Al usar el tipo de IdP "Microsoft Entra ID (v1.0)", use el punto de acceso v1.0, por ejemplo, https://login.microsoftonline.com/common/oauth2/token</li>
<li>Al usar el tipo de IdP "Microsoft identity platform (v2.0)", use el punto de acceso v2.0, por ejemplo, https://login.microsoftonline.com/common/oauth2/v2.0/authorize</li>
</ul>';
$string['error_tenant_specific_endpoint_required'] = 'Al usar el tipo de IdP "Microsoft identity platform (v2.0)" y el método de autenticación "Certificado", se requiere un punto de acceso específico del inquilino (es decir, no common/organizations/consumers).';
$string['error_empty_iomadoidcresource'] = 'El recurso no puede estar vacío al usar Microsoft Entra ID (v1.0) u otros tipos de IdP.';
$string['erroruserwithusernamealreadyexists'] = 'Ocurrió un error al intentar cambiar el nombre de su cuenta de Moodle. Ya existe un usuario de Moodle con el nuevo nombre de usuario. Solicite a su administrador del sitio que resuelva esto primero.';
$string['error_no_response_available'] = 'No hay respuestas disponibles.';

$string['eventuserauthed'] = 'Usuario autorizado con OpenID Connect';
$string['eventusercreated'] = 'Usuario creado con OpenID Connect';
$string['eventuserconnected'] = 'Usuario conectado a OpenID Connect';
$string['eventuserloggedin'] = 'Usuario inició sesión con OpenID Connect';
$string['eventuserdisconnected'] = 'Usuario desconectado de OpenID Connect';
$string['eventuserrenameattempt'] = 'El complemento auth_iomadoidc intentó renombrar a un usuario';

$string['iomadoidc:manageconnection'] = 'Permitir conexión y desconexión de OpenID';
$string['iomadoidc:manageconnectionconnect'] = 'Permitir conexión de OpenID';
$string['iomadoidc:manageconnectiondisconnect'] = 'Permitir desconexión de OpenID';

$string['privacy:metadata:auth_iomadoidc'] = 'Autenticación OpenID Connect';
$string['privacy:metadata:auth_iomadoidc_prevlogin'] = 'Métodos de inicio de sesión anteriores para deshacer conexiones de Microsoft 365';
$string['privacy:metadata:auth_iomadoidc_prevlogin:userid'] = 'El ID del usuario de Moodle';
$string['privacy:metadata:auth_iomadoidc_prevlogin:method'] = 'El método de inicio de sesión anterior';
$string['privacy:metadata:auth_iomadoidc_prevlogin:password'] = 'El campo de contraseña de usuario anterior (cifrado).';
$string['privacy:metadata:auth_iomadoidc_token'] = 'Tokens OpenID Connect';
$string['privacy:metadata:auth_iomadoidc_token:iomadoidcuniqid'] = 'El identificador único de usuario OIDC.';
$string['privacy:metadata:auth_iomadoidc_token:username'] = 'El nombre de usuario del usuario de Moodle';
$string['privacy:metadata:auth_iomadoidc_token:userid'] = 'El ID de usuario del usuario de Moodle';
$string['privacy:metadata:auth_iomadoidc_token:iomadoidcusername'] = 'El nombre de usuario del usuario OIDC';
$string['privacy:metadata:auth_iomadoidc_token:scope'] = 'El ámbito del token';
$string['privacy:metadata:auth_iomadoidc_token:tokenresource'] = 'El recurso del token';
$string['privacy:metadata:auth_iomadoidc_token:authcode'] = 'El código de autenticación del token';
$string['privacy:metadata:auth_iomadoidc_token:token'] = 'El token';
$string['privacy:metadata:auth_iomadoidc_token:expiry'] = 'La caducidad del token';
$string['privacy:metadata:auth_iomadoidc_token:refreshtoken'] = 'El token de actualización';
$string['privacy:metadata:auth_iomadoidc_token:idtoken'] = 'El token de ID';

// In the following strings, $a refers to a customizable name for the identity manager. For example, this could be
// "Microsoft 365", "OpenID Connect", etc.
$string['ucp_general_intro'] = 'Aquí puede administrar su conexión a {$a}. Si está habilitada, podrá usar su cuenta de {$a} para iniciar sesión en Moodle en lugar de un nombre de usuario y contraseña separados. Una vez conectado, ya no tendrá que recordar un nombre de usuario y contraseña para Moodle, todos los inicios de sesión serán gestionados por {$a}.';
$string['ucp_login_start'] = 'Comenzar a usar {$a} para iniciar sesión en Moodle';
$string['ucp_login_start_desc'] = 'Esto cambiará su cuenta para usar {$a} para iniciar sesión en Moodle. Una vez habilitado, iniciará sesión usando sus credenciales de {$a} - su nombre de usuario y contraseña actual de Moodle no funcionarán. Puede desconectar su cuenta en cualquier momento y volver a iniciar sesión normalmente.';
$string['ucp_login_stop'] = 'Dejar de usar {$a} para iniciar sesión en Moodle';
$string['ucp_login_stop_desc'] = 'Actualmente está usando {$a} para iniciar sesión en Moodle. Al hacer clic en "Dejar de usar inicio de sesión de {$a}" se desconectará su cuenta de Moodle de {$a}. Ya no podrá iniciar sesión en Moodle con su cuenta de {$a}. Se le pedirá que cree un nombre de usuario y contraseña, y a partir de entonces podrá iniciar sesión en Moodle directamente.';
$string['ucp_login_status'] = 'El inicio de sesión de {$a} está:';
$string['ucp_status_enabled'] = 'Habilitado';
$string['ucp_status_disabled'] = 'Deshabilitado';
$string['ucp_disconnect_title'] = 'Desconexión de {$a}';
$string['ucp_disconnect_details'] = 'Esto desconectará su cuenta de Moodle de {$a}. Necesitará crear un nombre de usuario y contraseña para iniciar sesión en Moodle.';
$string['ucp_title'] = 'Administración de {$a}';
$string['ucp_o365accountconnected'] = 'Esta cuenta de Microsoft 365 ya está conectada con otra cuenta de Moodle.';

// Clean up OIDC tokens.
$string['cleanup_iomadoidc_tokens'] = 'Limpiar tokens OpenID Connect';
$string['unmatched'] = 'Sin coincidencia';
$string['delete_token'] = 'Eliminar token';
$string['mismatched'] = 'No coincide';
$string['na'] = 'n/d';
$string['mismatched_details'] = 'El registro de token contiene el nombre de usuario "{$a->tokenusername}"; el usuario de Moodle coincidente tiene el nombre de usuario "{$a->moodleusername}".';
$string['delete_token_and_reference'] = 'Eliminar token y referencia';
$string['table_token_id'] = 'ID de registro de token';
$string['table_iomadoidc_username'] = 'Nombre de usuario OIDC';
$string['table_token_unique_id'] = 'ID único OIDC';
$string['table_matching_status'] = 'Estado de coincidencia';
$string['table_matching_details'] = 'Detalles';
$string['table_action'] = 'Acción';
$string['token_deleted'] = 'El token se eliminó exitosamente';
$string['no_token_to_cleanup'] = 'No hay tokens OIDC para limpiar.';

$string['errorusermatched'] = 'La cuenta de Microsoft 365 "{$a->entraidupn}" ya está asociada con el usuario de Moodle "{$a->username}". Para completar la conexión, por favor inicie sesión como ese usuario de Moodle primero y siga las instrucciones en el bloque de Microsoft.';

// User mapping options.
$string['update_oncreate_and_onlogin'] = 'En la creación y en cada inicio de sesión';
$string['update_oncreate_and_onlogin_and_usersync'] = 'En la creación, en cada inicio de sesión y en cada ejecución de tarea de sincronización de usuarios';
$string['update_onlogin_and_usersync'] = 'En cada inicio de sesión y en cada ejecución de tarea de sincronización de usuarios';

// Remote fields.
$string['settings_fieldmap_feild_not_mapped'] = '(no mapeado)';
$string['settings_fieldmap_field_city'] = 'Ciudad';
$string['settings_fieldmap_field_companyName'] = 'Nombre de la empresa';
$string['settings_fieldmap_field_objectId'] = 'ID de objeto';
$string['settings_fieldmap_field_country'] = 'País';
$string['settings_fieldmap_field_department'] = 'Departamento';
$string['settings_fieldmap_field_displayName'] = 'Nombre para mostrar';
$string['settings_fieldmap_field_surname'] = 'Apellido';
$string['settings_fieldmap_field_faxNumber'] = 'Número de fax';
$string['settings_fieldmap_field_telephoneNumber'] = 'Número de teléfono';
$string['settings_fieldmap_field_givenName'] = 'Nombre de pila';
$string['settings_fieldmap_field_jobTitle'] = 'Cargo';
$string['settings_fieldmap_field_mail'] = 'Correo electrónico';
$string['settings_fieldmap_field_mobile'] = 'Móvil';
$string['settings_fieldmap_field_postalCode'] = 'Código postal';
$string['settings_fieldmap_field_preferredLanguage'] = 'Idioma';
$string['settings_fieldmap_field_state'] = 'Estado';
$string['settings_fieldmap_field_streetAddress'] = 'Dirección';
$string['settings_fieldmap_field_userPrincipalName'] = 'Nombre de usuario (UPN)';
$string['settings_fieldmap_field_employeeId'] = 'ID de empleado';
$string['settings_fieldmap_field_businessPhones'] = 'Teléfono de oficina';
$string['settings_fieldmap_field_mobilePhone'] = 'Teléfono móvil';
$string['settings_fieldmap_field_officeLocation'] = 'Oficina';
$string['settings_fieldmap_field_preferredName'] = 'Nombre preferido';
$string['settings_fieldmap_field_manager'] = 'Nombre del gerente';
$string['settings_fieldmap_field_manager_email'] = 'Correo electrónico del gerente';
$string['settings_fieldmap_field_teams'] = 'Equipos';
$string['settings_fieldmap_field_groups'] = 'Grupos';
$string['settings_fieldmap_field_roles'] = 'Roles';
$string['settings_fieldmap_field_onPremisesSamAccountName'] = 'Nombre de cuenta SAM local';
$string['settings_fieldmap_field_extensionattribute'] = 'Atributo de extensión {$a}';
$string['settings_fieldmap_field_sds_school_id'] = 'ID de escuela SDS ({$a})';
$string['settings_fieldmap_field_sds_school_name'] = 'Nombre de escuela SDS ({$a})';
$string['settings_fieldmap_field_sds_school_role'] = 'Rol de escuela SDS ("Student" o "Teacher")';
$string['settings_fieldmap_field_sds_student_externalId'] = 'ID externo de estudiante SDS';
$string['settings_fieldmap_field_sds_student_birthDate'] = 'Fecha de nacimiento de estudiante SDS';
$string['settings_fieldmap_field_sds_student_grade'] = 'Grado de estudiante SDS';
$string['settings_fieldmap_field_sds_student_graduationYear'] = 'Año de graduación de estudiante SDS';
$string['settings_fieldmap_field_sds_student_studentNumber'] = 'Número de estudiante SDS';
$string['settings_fieldmap_field_sds_teacher_externalId'] = 'ID externo de profesor SDS';
$string['settings_fieldmap_field_sds_teacher_teacherNumber'] = 'Número de profesor SDS';
