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
 * Strings for component 'auth_iomadoidc', language 'es'.
 *
 * @package   auth_iomadoidc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 o posterior
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Conexión OpenID';
$string['auth_iomadoidcdescription'] = 'El complemento de autenticación OpenID Connect proporciona funcionalidad de inicio de sesión único mediante un IdP configurable.';
$string['settings_page_other_settings'] = 'Otras opciones';
$string['settings_page_application'] = 'IdP y autenticación';
$string['settings_page_cleanup_iomadoidc_tokens'] = 'Limpieza de tokens OpenID Connect';
$string['settings_page_field_mapping'] = 'Mapeos de campo';
$string['heading_basic'] = 'Configuraciones básicas';
$string['heading_basic_desc'] = '';
$string['heading_additional_options'] = 'Opciones adicionales';
$string['heading_additional_options_desc'] = '';
$string['heading_user_restrictions'] = 'Restricciones de usuario';
$string['heading_user_restrictions_desc'] = '';
$string['heading_sign_out'] = 'Cerrar sesión de integración';
$string['heading_sign_out_desc'] = '';
$string['heading_display'] = 'Mostrar';
$string['heading_display_desc'] = '';
$string['heading_debugging'] = 'Depuración';
$string['heading_debugging_desc'] = '';
$string['idptype'] = 'Tipo de proveedor de identidad (IdP)';
$string['idptype_help'] = 'Actualmente se admiten tres tipos de IdP: 
<ul>
<li><b>Microsoft Entra ID (v1.0)</b>: Microsoft Entra ID con puntos finales oauth2 v1.0, p. https://login.microsoftonline.com/common/oauth2/authorize.</li>
<li><b>Plataforma de identidad de Microsoft (v2.0)</b>: Microsoft Entra ID con puntos finales oath2 v2.0, p. https://login.microsoftonline.com/common/oauth2/v2.0/authorize.</li>
<li><b>Otro</b>: cualquier IdP que no sea de Microsoft.</li>
</ul>
Las diferencias entre <b>Microsoft Entra ID (v1.0)</b> y <b>Microsoft Las opciones de plataforma de identidad (v2.0)</b> se pueden encontrar en <a href="https://docs.microsoft.com/en-us/azure/active-directory/azuread-dev/azure-ad-endpoint-comparison">https://d ocs.microsoft.com/en-us/azure/active-directory/azuread-dev/azure-ad-endpoint-comparison</a>.<br/>
ZEn particular, la aplicación configurada puede usar un <b>certificado</b> además del <b>secreto</b> para la autenticación cuando se utiliza el IdP de la <b>plataforma de identidad de Microsoft (v2.0)</b>.<br/>
A Los puntos finales de autorización y token deben configurarse de acuerdo con el tipo de IdP configurado.';
$string['idp_type_microsoft_entra_id'] = 'Identificación de Microsoft Entra (v1.0)';
$string['idp_type_microsoft_identity_platform'] = 'Plataforma de identidad de Microsoft (v2.0)';
$string['idp_type_other'] = 'Otro';
$string['cfg_authenticationlink_desc'] = '<a href="{$a}" target="_blank">Enlace al IdP y configuración de autenticación</a>';
$string['authendpoint'] = 'Punto final de autorización';
$string['authendpoint_help'] = 'El URI del punto final de autorización de su IdP para usar.<br/>
Tenga en cuenta que si el sitio se va a configurar para permitir el acceso a usuarios de otros inquilinos, no se puede usar el punto final de autorización específico del inquilino.';
$string['cfg_autoappend_key'] = 'Agregar automáticamente';
$string['cfg_autoappend_desc'] = 'Agregue automáticamente esta cadena cuando los usuarios inicien sesión utilizando el método de autenticación "Credenciales de contraseña del propietario del recurso". Esto es útil cuando su IdP requiere un dominio común, pero no desea que los usuarios lo escriban al iniciar sesión. Por ejemplo, si el usuario completo de OpenID Connect es "james@example.com" e ingresa "@example.com" aquí, el usuario solo tendrá que ingresar "james" como su nombre de usuario. <br /><b>Nota:</b> En el caso de que existan nombres de usuario en conflicto, es decir, exista un usuario de Moodle con el mismo nombre, la prioridad del complemento de autenticación se utiliza para determinar qué usuario gana.';
$string['clientid'] = 'ID de la aplicación';
$string['clientid_help'] = 'La ID de aplicación/cliente registrada en el IdP.';
$string['clientauthmethod'] = 'Método de autenticación del cliente';
$string['clientauthmethod_help'] = '<ul>
<li>El IdP de todos los tipos puede utilizar el método de autenticación "<b>Secreto</b>".</li>
<li>El IdP del tipo <b>Plataforma de identidad de Microsoft (v2.0)</b> puede utilizar adicionalmente el método de autenticación <b>Certificado</b>.</li>
</ul>';
$string['auth_method_secret'] = 'Secreto';
$string['auth_method_certificate'] = 'Certificado';
$string['clientsecret'] = 'Secreto del cliente';
$string['clientsecret_help'] = 'Cuando se utiliza el método de autenticación <b>secret</b>, este es el secreto del cliente en el IdP. En algunos proveedores, también se la denomina clave.';
$string['clientprivatekey'] = 'Clave privada del certificado de cliente';
$string['clientprivatekey_help'] = 'Cuando se utiliza el método de autenticación <b>certificado</b> y el origen del certificado de <b>texto sin formato</b>, esta es la clave privada del certificado utilizado para autenticarse con el IdP.';
$string['clientcert'] = 'Clave pública del certificado de cliente';
$string['clientcert_help'] = 'Cuando se utiliza el método de autenticación <b>certificado</b> y el origen del certificado de <b>texto sin formato</b>, esta es la clave pública o certificado que se utiliza para autenticarse con el IdP.';
$string['clientcertsource'] = 'Fuente del certificado';
$string['clientcertsource_help'] = 'Cuando se utiliza el método de autenticación de <b>certificado</b>, esto se utiliza para definir de dónde recuperar el certificado. 
<ul>
<li><b>La fuente de texto sin formato</b> requiere que el contenido del archivo de certificado/clave privada se configure en la configuración del área de texto posterior.</li>
<li><b>Nombre de archivo</b> la fuente requiere que los archivos de certificado/clave privada existan en un carpeta <b>microsoft_certs</b> en la carpeta de datos de Moodle.</li>
</ul>';
$string['cert_source_text'] = 'Texto sin formato';
$string['cert_source_path'] = 'Nombre del archivo';
$string['clientprivatekeyfile'] = 'Nombre de archivo de la clave privada del certificado del cliente';
$string['clientprivatekeyfile_help'] = 'Cuando se utiliza el método de autenticación <b>certificado</b> y el origen del certificado <b>nombre de archivo</b>, este es el nombre de archivo de la clave privada utilizada para autenticarse con el IdP. El archivo debe presentarse en una carpeta <b>microsoft_certs</b> en la carpeta de datos de Moodle.';
$string['clientcertfile'] = 'Nombre de archivo de la clave pública del certificado de cliente';
$string['clientcertfile_help'] = 'Cuando se utiliza el método de autenticación <b>certificado</b> y el origen del certificado <b>nombre de archivo</b>, este es el nombre de archivo de la clave pública o certificado utilizado para autenticarse con el IdP. El archivo debe presentarse en una carpeta <b>microsoft_certs</b> en la carpeta de datos de Moodle.';
$string['clientcertpassphrase'] = 'Frase de contraseña del certificado de cliente';
$string['clientcertpassphrase_help'] = 'Si la clave privada del certificado del cliente está cifrada, esta es la frase de contraseña para descifrarla.';
$string['cfg_domainhint_key'] = 'Sugerencia de dominio';
$string['cfg_domainhint_desc'] = 'Cuando utilice el flujo de inicio de sesión del <b>Código de autorización</b>, pase este valor como parámetro "domain_hint". Algunos IdP de OpenID Connect utilizan "domain_hint" para facilitar el proceso de inicio de sesión a los usuarios. Consulte con su proveedor para ver si admiten este parámetro.';
$string['cfg_err_invalidauthendpoint'] = 'Punto final de autorización no válido';
$string['cfg_err_invalidtokenendpoint'] = 'Punto final de token no válido';
$string['cfg_err_invalidclientid'] = 'ID de cliente no válido';
$string['cfg_err_invalidclientsecret'] = 'Secreto de cliente no válido';
$string['cfg_forceredirect_key'] = 'Forzar redirección';
$string['cfg_forceredirect_desc'] = 'Si está habilitado, omitirá la página de índice de inicio de sesión y lo redirigirá a la página de OpenID Connect. Se puede omitir con el parámetro de URL ?noredirect=1';
$string['cfg_icon_key'] = 'Icono';
$string['cfg_icon_desc'] = 'Un icono que se mostrará junto al nombre del proveedor en la página de inicio de sesión.';
$string['cfg_iconalt_o365'] = 'Icono de Microsoft 365';
$string['cfg_iconalt_locked'] = 'Icono bloqueado';
$string['cfg_iconalt_lock'] = 'Icono de candado';
$string['cfg_iconalt_go'] = 'circulo verde';
$string['cfg_iconalt_stop'] = 'circulo rojo';
$string['cfg_iconalt_user'] = 'Icono de usuario';
$string['cfg_iconalt_user2'] = 'Icono de usuario alternativo';
$string['cfg_iconalt_key'] = 'Icono de llave';
$string['cfg_iconalt_group'] = 'Icono de grupo';
$string['cfg_iconalt_group2'] = 'Icono de grupo alternativo';
$string['cfg_iconalt_mnet'] = 'icono MNET';
$string['cfg_iconalt_userlock'] = 'Usuario con icono de candado';
$string['cfg_iconalt_plus'] = 'Icono más';
$string['cfg_iconalt_check'] = 'icono de marca de verificación';
$string['cfg_iconalt_rightarrow'] = 'Icono de flecha hacia la derecha';
$string['cfg_customicon_key'] = 'Icono personalizado';
$string['cfg_customicon_desc'] = 'Si desea utilizar su propio icono, cárguelo aquí. Esto anula cualquier icono elegido anteriormente. <br /><br /><b>Notas sobre el uso de íconos personalizados:</b><ul><li>Esta imagen <b>no</b> cambiará de tamaño en la página de inicio de sesión, por lo que recomendamos cargar una imagen que no supere los 35 x 35 píxeles.</li><li>Si ha cargado un ícono personalizado y desea volver a uno de los íconos estándar, haga clic en el ícono personalizado en el cuadro de arriba, luego haga clic en "Eliminar" y luego haga clic en "Aceptar", luego haga clic en "Guardar cambios" en la parte inferior de este formulario. El icono de stock seleccionado aparecerá ahora en la página de inicio de sesión de Moodle.</li></ul>';
$string['cfg_debugmode_key'] = 'Grabar mensajes de depuración';
$string['cfg_debugmode_desc'] = 'Si está habilitado, se registrará información en el registro de Moodle que puede ayudar a identificar problemas.';
$string['cfg_loginflow_key'] = 'Flujo de inicio de sesión';
$string['cfg_loginflow_authcode'] = 'Flujo de código de autorización <b>(recomendado)</b>';
$string['cfg_loginflow_authcode_desc'] = 'Usando este flujo, el usuario hace clic en el nombre del IdP (consulte "Nombre para mostrar del proveedor" arriba) en la página de inicio de sesión de Moodle y es redirigido al proveedor para iniciar sesión. Una vez que ha iniciado sesión exitosamente, el usuario es redirigido nuevamente a Moodle, donde el inicio de sesión de Moodle se realiza de forma transparente. Esta es la forma más estandarizada y segura para que el usuario inicie sesión.';
$string['cfg_loginflow_rocreds'] = 'Concesión de credenciales de contraseña de propietario de recurso <b>(obsoleto)</b>';
$string['cfg_loginflow_rocreds_desc'] = '<b>Este flujo de inicio de sesión está obsoleto y pronto se eliminará del complemento.</b><br/>Al utilizar este flujo, el usuario ingresa su nombre de usuario y contraseña en el formulario de inicio de sesión de Moodle como lo haría con un inicio de sesión manual. Esto autorizará al usuario con el IdP, pero no creará una sesión en el sitio del IdP. Por ejemplo, si usa Microsoft 365 con OpenID Connect, el usuario iniciará sesión en Moodle pero no en las aplicaciones web de Microsoft 365. Se recomienda utilizar la solicitud de autorización si desea que los usuarios inicien sesión tanto en Moodle como en el IdP. Tenga en cuenta que no todos los IdP admiten este flujo. Esta opción solo debe usarse cuando otros tipos de concesión de autorización no están disponibles.';
$string['cfg_silentloginmode_key'] = 'Modo de inicio de sesión silencioso';
$string['cfg_silentloginmode_desc'] = 'Si está habilitado, Moodle intentará usar la sesión activa de un usuario autenticado en el punto final de autorización configurado para iniciar la sesión del usuario.<br/>
Para usar esta función, se requieren las siguientes configuraciones:
<ul>
<li><b>Forzar a los usuarios a iniciar sesión</b> (forcelogin) en el <a href="{$a}" target="_blank">Sitio sección de políticas</a> está habilitada.</li>
<li><b>Forzar redirección</b> (auth_iomadoidc/forceredirect) la configuración anterior está habilitada.</li>
</ul>
Para evitar que Moodle intente usar cuentas personales o cuentas de otros inquilinos para iniciar sesión, también se recomienda usar puntos finales específicos de inquilinos, en lugar de genéricos. aquellos que usan rutas "comunes" u "organizativas", etc. (es decir, el usuario es del mismo inquilino o es un usuario invitado del inquilino), el usuario iniciará sesión en Moodle automáticamente usando SSO.</li>
<li>Si solo se encuentra una sesión de usuario activa, pero el usuario no tiene acceso a la aplicación Entra ID (por ejemplo, el usuario es de un inquilino diferente, o la aplicación requiere asignación de usuario y el usuario no está asignado), la página de inicio de sesión de Moodle show.</li>
<li>Si hay varias sesiones de usuario activas que tienen acceso a la aplicación Entra ID, se mostrará una página que permitirá al usuario seleccionar la cuenta con la que iniciar sesión.</li>
</ul>';
$string['iomadoidcresource'] = 'Recurso';
$string['iomadoidcresource_help'] = 'El recurso OpenID Connect para el cual enviar la solicitud.<br/>
<b>Nota</b> este parámetro no es compatible con el tipo de IdP <b>Microsoft Identity Platform (v2.0)</b>.';
$string['iomadoidcscope'] = 'Alcance';
$string['iomadoidcscope_help'] = 'El alcance de OIDC que se utilizará.';
$string['secretexpiryrecipients'] = 'Destinatarios de notificaciones secretas de vencimiento';
$string['secretexpiryrecipients_help'] = 'Una lista separada por comas de direcciones de correo electrónico a las que enviar notificaciones secretas de vencimiento.<br/>
Si no se ingresa ninguna dirección de correo electrónico, se notificará al administrador principal del sitio.';
$string['cfg_opname_key'] = 'Nombre para mostrar del proveedor';
$string['cfg_opname_desc'] = 'Esta es una etiqueta orientada al usuario final que identifica el tipo de credenciales que el usuario debe utilizar para iniciar sesión. Esta etiqueta se utiliza en todas las partes de este complemento orientadas al usuario para identificar a su proveedor.';
$string['cfg_redirecturi_key'] = 'URI de redireccionamiento';
$string['cfg_redirecturi_desc'] = 'Este es el URI para registrarse como "URI de redireccionamiento". Su IdP de OpenID Connect debería solicitarlo al registrar Moodle como cliente. <br /><b>NOTA:</b> Debe ingresar esto en su IdP de OpenID Connect *exactamente* como aparece aquí. Cualquier diferencia impedirá el inicio de sesión mediante OpenID Connect.';
$string['tokenendpoint'] = 'Punto final del token';
$string['tokenendpoint_help'] = 'El URI del punto final del token de su IdP que se utilizará.<br/>
Tenga en cuenta que si el sitio se va a configurar para permitir el acceso a usuarios de otros inquilinos, no se puede utilizar el punto final del token específico del inquilino.';
$string['cfg_userrestrictions_key'] = 'Restricciones de usuario';
$string['cfg_userrestrictions_desc'] = 'Sólo permita el inicio de sesión a usuarios que cumplan con ciertas restricciones. <br /><b>Cómo usar restricciones de usuario: </b> <ul><li>Ingrese un patrón de <a href="https://en.wikipedia.org/wiki/Regular_expression">expresión regular</a> que coincida con los nombres de usuario de los usuarios que desea permitir.</li><li>Ingrese un patrón por línea</li><li>Si ingresa varios patrones, se permitirá un usuario si coinciden CUALQUIERA de los patrones.</li><li>El carácter "/" debe tener como escape "\".</li><li>Si no ingresa ninguna restricción arriba, Moodle aceptará a todos los usuarios que puedan iniciar sesión en el IdP de OpenID Connect.</li><li>Cualquier usuario que no coincida con ningún patrón ingresado no podrá iniciar sesión usando OpenID Connect.</li></ul>';
$string['cfg_userrestrictionscasesensitive_key'] = 'Restricciones de usuario Distinguen entre mayúsculas y minúsculas';
$string['cfg_userrestrictionscasesensitive_desc'] = 'Esto controla si la opción "/i" en la expresión regular se usa en la coincidencia de restricción de usuario.<br/>Si está habilitada, todas las comprobaciones de restricción de usuario se realizarán distinguiendo entre mayúsculas y minúsculas. Tenga en cuenta que si esto está deshabilitado, se ignorará cualquier patrón en mayúsculas y minúsculas.';
$string['cfg_signoffintegration_key'] = 'Cierre de sesión único (de Moodle a IdP)';
$string['cfg_signoffintegration_desc'] = 'Si la opción está habilitada, cuando un usuario de Moodle conectado al IdP configurado cierra la sesión de Moodle, la integración activará una solicitud en el extremo de cierre de sesión a continuación, intentando cerrar la sesión del usuario también del IdP.<br/>
Nota para la integración con Microsoft Entra ID, la URL del sitio de Moodle ({$a}) debe agregarse como un URI de redireccionamiento en la aplicación de Azure creada para Moodle y Integración con Microsoft 365.';
$string['cfg_logoutendpoint_key'] = 'Punto final de cierre de sesión de IdP';
$string['cfg_logoutendpoint_desc'] = 'El URI del punto final de cierre de sesión de su IdP que se utilizará.';
$string['cfg_frontchannellogouturl_key'] = 'URL de cierre de sesión del canal frontal';
$string['cfg_frontchannellogouturl_desc'] = 'Esta es la URL que su IdP debe activar cuando intenta cerrar la sesión de los usuarios en Moodle.<br/>
Para Microsoft Entra ID/plataforma de identidad de Microsoft, la configuración se llama "URL de cierre de sesión del canal frontal" y se puede configurar en la aplicación de Azure.';
$string['cfg_field_mapping_desc'] = 'Los datos del perfil de usuario se pueden asignar desde Open ID Connect IdP a Moodle. Los campos remotos disponibles para asignar dependen en gran medida del tipo de IdP.<br/>
<ul>
<li>Algunos campos de perfil básicos están disponibles en las notificaciones de token de acceso y token de ID de todos los tipos de IdP.</li>
<li>Si el tipo de IdP de Microsoft está configurado (ya sea v1.0 o v2.0), se pueden poner a disposición datos de perfil adicionales a través de llamadas a Graph API mediante la instalación y configurando el <a href="https://moodle.org/plugins/local_o365">complemento de integración de Microsoft 365 (local_o365)</a>.</li>
<li>Si la función de sincronización de perfil SDS está habilitada en el complemento local_o365, ciertos campos del perfil se pueden sincronizar desde SDS a Moodle. cuando se ejecuta la tarea programada "Sincronizar con SDS", y no sucederá cuando se ejecuta la tarea programada "Sincronizar usuarios con Microsoft Entra ID", ni cuando el usuario inicia sesión.</li>
</ul>

ZLas reclamaciones disponibles desde el ID y los tokens de acceso varían según el tipo de IdP, pero la mayoría de los IdP permiten cierto nivel de personalización de las reclamaciones. La documentación sobre los IdP de Microsoft está vinculada a continuación:
<ul>
<li><a target="_blank" href="https://learn.microsoft.com/en-us/entra/identity-platform/access-token-claims-reference">Reclamaciones de token de acceso</a></li>
<li><a target="_blank" href="https://learn.microsoft.com/en-us/entra/identity-platform/id-token-claims-reference">Reclamaciones de tokens de identificación</a></li>
<li><a target="_blank" href="https://learn.microsoft.com/en-us/entra/identity-platform/optional-claims-reference">Configuración de reclamo opcional</a>: Nota "Correo electrónico" es un reclamo opcional en el tipo de IdP de Microsoft Entra ID (v1.0).</li>
</ul>';
$string['cfg_cleanupiomadoidctokens_key'] = 'Limpieza de tokens OpenID Connect';
$string['cfg_cleanupiomadoidctokens_desc'] = 'Si sus usuarios tienen problemas para iniciar sesión con su cuenta de Microsoft 365, intente limpiar los tokens de OpenID Connect. Esto elimina los tokens perdidos e incompletos que pueden provocar errores. ADVERTENCIA: Esto puede interrumpir los inicios de sesión en proceso, por lo que es mejor hacerlo durante el tiempo de inactividad.';
$string['settings_section_basic'] = 'Configuraciones básicas';
$string['settings_section_authentication'] = 'Autenticación';
$string['settings_section_endpoints'] = 'Puntos finales';
$string['settings_section_other_params'] = 'Otros parámetros';
$string['settings_section_secret_expiry_notification'] = 'Notificación secreta de vencimiento';
$string['authentication_and_endpoints_saved'] = 'Se actualizaron las configuraciones de autenticación y punto final.';
$string['application_updated'] = 'La configuración de la aplicación OpenID Connect se ha actualizado.';
$string['application_updated_microsoft'] = 'Se actualizó la configuración de la aplicación OpenID Connect.<br/>
<span class="warning" style="color: red;">El administrador de Azure deberá <b>proporcionar consentimiento del administrador</b> y <b>verificar la configuración</b> nuevamente en la <a href="{$a}" target="_blank">página de configuración de integración de Microsoft 365</a> si es "Tipo de proveedor de identidad (IdP)" o se actualiza la configuración del "Método de autenticación del cliente".</span>';
$string['application_not_changed'] = 'La configuración de la aplicación OpenID Connect no se cambió.';
$string['event_debug'] = 'mensaje de depuración';
$string['task_cleanup_iomadoidc_state_and_token'] = 'Limpiar el estado OIDC y el token no válido';
$string['errorauthdisconnectemptypassword'] = 'La contraseña no puede estar vacía';
$string['errorauthdisconnectemptyusername'] = 'El nombre de usuario no puede estar vacío';
$string['errorauthdisconnectusernameexists'] = 'Ese nombre de usuario ya está en uso. Por favor elige uno diferente.';
$string['errorauthdisconnectnewmethod'] = 'Usar método de inicio de sesión';
$string['errorauthdisconnectinvalidmethod'] = 'Se recibió un método de inicio de sesión no válido.';
$string['errorauthdisconnectifmanual'] = 'Si utiliza el método de inicio de sesión manual, ingrese las credenciales a continuación.';
$string['errorauthgeneral'] = 'Hubo un problema al iniciar sesión. Comuníquese con su administrador para obtener ayuda.';
$string['errorauthinvalididtoken'] = 'Se recibió un id_token no válido.';
$string['errorauthloginfailednouser'] = 'Inicio de sesión no válido: Usuario no encontrado en Moodle. Si este sitio tiene habilitada la configuración "authpreventaccountcreation", esto puede significar que primero necesita que un administrador le cree una cuenta.';
$string['errorauthloginfaileddupemail'] = 'Inicio de sesión no válido: una cuenta existente en este Moodle tiene la misma dirección de correo electrónico que la cuenta que intenta crear y la configuración "Permitir cuentas con el mismo correo electrónico" (allowaccountssameemail) está deshabilitada.';
$string['errorauthnoauthcode'] = 'No se recibió ningún código de autorización del servidor de identidad. Los registros de errores pueden tener más información.';
$string['errorauthnocredsandendpoints'] = 'Configure los puntos finales y las credenciales del cliente de OpenID Connect.';
$string['errorauthnohttpclient'] = 'Configure un cliente HTTP.';
$string['errorauthnoidtoken'] = 'OpenID Connect id_token no recibido.';
$string['errorauthnoaccesstoken'] = 'Token de acceso no recibido.';
$string['errorauthunknownstate'] = 'Estado desconocido.';
$string['errorauthuseralreadyconnected'] = 'Ya estás conectado con un usuario de OpenID Connect diferente.';
$string['errorauthuserconnectedtodifferent'] = 'El usuario de OpenID Connect que se autenticó ya está conectado a un usuario de Moodle.';
$string['errorbadloginflow'] = 'Se ha especificado un tipo de autenticación no válido. Nota: Si recibe esto después de una instalación o actualización reciente, borre su caché de Moodle.';
$string['errorjwtbadpayload'] = 'No se pudo leer la carga útil de JWT.';
$string['errorjwtcouldnotreadheader'] = 'No se pudo leer el encabezado JWT';
$string['errorjwtempty'] = 'Se recibió JWT vacío o sin cadena.';
$string['errorjwtinvalidheader'] = 'Encabezado JWT no válido';
$string['errorjwtmalformed'] = 'Se recibió JWT con formato incorrecto.';
$string['errorjwtunsupportedalg'] = 'JWS Alg o JWE no son compatibles';
$string['errorlogintoconnectedaccount'] = 'Este usuario de Microsoft 365 está conectado a una cuenta de Moodle, pero el inicio de sesión de OpenID Connect no está habilitado para esta cuenta de Moodle. Inicie sesión en la cuenta de Moodle utilizando el método de autenticación definido de la cuenta para utilizar las funciones de Microsoft 365.';
$string['erroriomadoidcnotenabled'] = 'El complemento de autenticación OpenID Connect no está habilitado.';
$string['errornodisconnectionauthmethod'] = 'No se puede desconectar porque no hay ningún complemento de autenticación habilitado al que recurrir. (ya sea el método de inicio de sesión anterior del usuario o el método de inicio de sesión manual).';
$string['erroriomadoidcclientinvalidendpoint'] = 'Se recibió un URI de punto final no válido.';
$string['erroriomadoidcclientnocreds'] = 'Configure las credenciales del cliente con setcreds';
$string['erroriomadoidcclientnoauthendpoint'] = 'No se ha establecido ningún punto final de autorización. Establezca con $this->setendpoints';
$string['erroriomadoidcclientnotokenendpoint'] = 'No se ha establecido ningún punto final de token. Establezca con $this->setendpoints';
$string['erroriomadoidcclientinsecuretokenendpoint'] = 'El punto final del token debe utilizar SSL/TLS para esto.';
$string['errorrestricted'] = 'Este sitio tiene restricciones sobre los usuarios que pueden iniciar sesión con OpenID Connect. Actualmente, estas restricciones le impiden completar este intento de inicio de sesión.';
$string['errorucpinvalidaction'] = 'Acción no válida recibida.';
$string['erroriomadoidccall'] = 'Error en OpenID Connect. Consulte los registros para obtener más información.';
$string['erroriomadoidccall_message'] = 'Error en OpenID Connect: {$a}';
$string['errorinvalidredirect_message'] = 'La URL a la que intentas redirigir no existe.';
$string['errorinvalidcertificatesource'] = 'Fuente de certificado no válida';
$string['error_empty_tenantnameorguid'] = 'El nombre del inquilino o el GUID no pueden estar vacíos cuando se utilizan IdP de Microsoft Entra ID (v1.0) o de la plataforma de identidad de Microsoft (v2.0).';
$string['error_invalid_client_authentication_method'] = 'Método de autenticación de cliente no válido';
$string['error_empty_client_secret'] = 'El secreto del cliente no puede estar vacío cuando se utiliza el método de autenticación "secreto"';
$string['error_empty_client_private_key'] = 'La clave privada del certificado del cliente no puede estar vacía cuando se utiliza el método de autenticación "certificado"';
$string['error_empty_client_cert'] = 'La clave pública del certificado de cliente no puede estar vacía cuando se utiliza el método de autenticación "certificado"';
$string['error_empty_client_private_key_file'] = 'El archivo de clave privada del certificado de cliente no puede estar vacío cuando se utiliza el método de autenticación "certificado"';
$string['error_empty_client_cert_file'] = 'El archivo de clave pública del certificado de cliente no puede estar vacío cuando se utiliza el método de autenticación "certificado"';
$string['error_empty_tenantname_or_guid'] = 'El nombre del inquilino o el GUID no pueden estar vacíos cuando se utiliza el método de autenticación "certificado"';
$string['error_endpoint_mismatch_auth_endpoint'] = 'El punto final de autorización configurado no coincide con el tipo de IdP configurado.<br/>
<ul>
<li>Cuando utilice el tipo de IdP "Microsoft Entra ID (v1.0)", utilice el punto final v1.0, p. https://login.microsoftonline.com/common/oauth2/authorize</li>
<li>Cuando utilice el tipo de IdP "Plataforma de identidad de Microsoft (v2.0)", utilice el punto final v2.0, p. https://login.microsoftonline.com/common/oauth2/v2.0/authorize</li>
</ul>';
$string['error_endpoint_mismatch_token_endpoint'] = 'El punto final del token configurado no coincide con el tipo de IdP configurado.<br/>
<ul>
<li>Cuando utilice el tipo de IdP "Microsoft Entra ID (v1.0)", utilice el punto final v1.0, p. https://login.microsoftonline.com/common/oauth2/token</li>
<li>Cuando utilice el tipo de IdP "Plataforma de identidad de Microsoft (v2.0)", utilice el punto final v2.0, p. https://login.microsoftonline.com/common/oauth2/v2.0/authorize</li>
</ul>';
$string['error_tenant_specific_endpoint_required'] = 'Cuando se utiliza el tipo de IdP "Plataforma de identidad de Microsoft (v2.0)" y el método de autenticación "Certificado", se requiere un punto final específico del inquilino (es decir, no común/organizaciones/consumidores).';
$string['error_empty_iomadoidcresource'] = 'El recurso no puede estar vacío cuando se utiliza Microsoft Entra ID (v1.0) u otros tipos de IdP.';
$string['erroruserwithusernamealreadyexists'] = 'Se produjo un error al intentar cambiar el nombre de su cuenta de Moodle. Ya existe un usuario de Moodle con el nuevo nombre de usuario. Pídale al administrador de su sitio que resuelva esto primero.';
$string['error_no_response_available'] = 'No hay respuestas disponibles.';
$string['eventuserauthed'] = 'Usuario autorizado con OpenID Connect';
$string['eventusercreated'] = 'Usuario creado con OpenID Connect';
$string['eventuserconnected'] = 'Usuario conectado a OpenID Connect';
$string['eventuserloggedin'] = 'El usuario inició sesión con OpenID Connect';
$string['eventuserdisconnected'] = 'Usuario desconectado de OpenID Connect';
$string['eventuserrenameattempt'] = 'El complemento auth_iomadoidc intentó cambiar el nombre de un usuario';
$string['iomadoidc:manageconnection'] = 'Permitir la conexión y desconexión de OpenID';
$string['iomadoidc:manageconnectionconnect'] = 'Permitir conexión OpenID';
$string['iomadoidc:manageconnectiondisconnect'] = 'Permitir la desconexión de OpenID';
$string['privacy:metadata:auth_iomadoidc'] = 'Autenticación de conexión OpenID';
$string['privacy:metadata:auth_iomadoidc_prevlogin'] = 'Métodos de inicio de sesión anteriores para deshacer conexiones de Microsoft 365';
$string['privacy:metadata:auth_iomadoidc_prevlogin:userid'] = 'El ID del usuario de Moodle';
$string['privacy:metadata:auth_iomadoidc_prevlogin:method'] = 'El método de inicio de sesión anterior';
$string['privacy:metadata:auth_iomadoidc_prevlogin:password'] = 'El campo de contraseña de usuario anterior (cifrado).';
$string['privacy:metadata:auth_iomadoidc_token'] = 'Fichas de conexión OpenID';
$string['privacy:metadata:auth_iomadoidc_token:iomadoidcuniqid'] = 'El identificador de usuario único de OIDC.';
$string['privacy:metadata:auth_iomadoidc_token:username'] = 'El nombre de usuario del usuario de Moodle';
$string['privacy:metadata:auth_iomadoidc_token:userid'] = 'El ID de usuario del usuario de Moodle.';
$string['privacy:metadata:auth_iomadoidc_token:iomadoidcusername'] = 'El nombre de usuario del usuario OIDC.';
$string['privacy:metadata:auth_iomadoidc_token:scope'] = 'El alcance del token';
$string['privacy:metadata:auth_iomadoidc_token:tokenresource'] = 'El recurso del token.';
$string['privacy:metadata:auth_iomadoidc_token:authcode'] = 'El código de autenticación del token.';
$string['privacy:metadata:auth_iomadoidc_token:token'] = 'la ficha';
$string['privacy:metadata:auth_iomadoidc_token:expiry'] = 'El vencimiento del token';
$string['privacy:metadata:auth_iomadoidc_token:refreshtoken'] = 'El token de actualización';
$string['privacy:metadata:auth_iomadoidc_token:idtoken'] = 'La ficha de identificación';
$string['ucp_general_intro'] = 'Aquí puedes gestionar tu conexión a {$a}. Si está habilitado, podrá usar su cuenta {$a} para iniciar sesión en Moodle en lugar de un nombre de usuario y contraseña separados. Una vez conectado, ya no tendrá que recordar un nombre de usuario y contraseña para Moodle; todos los inicios de sesión serán manejados por {$a}.';
$string['ucp_login_start'] = 'Comience a usar {$a} para iniciar sesión en Moodle';
$string['ucp_login_start_desc'] = 'Esto cambiará su cuenta para usar {$a} para iniciar sesión en Moodle. Una vez habilitado, iniciará sesión con sus credenciales {$a}; su nombre de usuario y contraseña actuales de Moodle no funcionarán. Puede desconectar su cuenta en cualquier momento y volver a iniciar sesión normalmente.';
$string['ucp_login_stop'] = 'Deje de usar {$a} para iniciar sesión en Moodle';
$string['ucp_login_stop_desc'] = 'Actualmente estás utilizando {$a} para iniciar sesión en Moodle. Al hacer clic en "Dejar de usar el inicio de sesión de {$a}" se desconectará su cuenta de Moodle de {$a}. Ya no podrá iniciar sesión en Moodle con su cuenta {$a}. Se le pedirá que cree un nombre de usuario y contraseña y, a partir de ese momento, podrá iniciar sesión en Moodle directamente.';
$string['ucp_login_status'] = '{$a} el inicio de sesión es:';
$string['ucp_status_enabled'] = 'Activado';
$string['ucp_status_disabled'] = 'Desactivado';
$string['ucp_disconnect_title'] = '{$a} Desconexión';
$string['ucp_disconnect_details'] = 'Esto desconectará su cuenta de Moodle de {$a}. Necesitará crear un nombre de usuario y contraseña para iniciar sesión en Moodle.';
$string['ucp_title'] = 'Gestión {$a}';
$string['ucp_o365accountconnected'] = 'Esta cuenta de Microsoft 365 ya está conectada con otra cuenta de Moodle.';
$string['cleanup_iomadoidc_tokens'] = 'Limpieza de tokens OpenID Connect';
$string['unmatched'] = 'Sin par';
$string['delete_token'] = 'Eliminar token';
$string['mismatched'] = 'No coinciden';
$string['na'] = 'n / A';
$string['mismatched_details'] = 'El registro de token contiene el nombre de usuario "{$a->tokenusername}"; El usuario de Moodle coincidente tiene el nombre de usuario "{$a->moodleusername}".';
$string['delete_token_and_reference'] = 'Eliminar token y referencia';
$string['table_token_id'] = 'ID de registro de token';
$string['table_iomadoidc_username'] = 'nombre de usuario OIDC';
$string['table_token_unique_id'] = 'ID único de OIDC';
$string['table_matching_status'] = 'Estado coincidente';
$string['table_matching_details'] = 'Detalles';
$string['table_action'] = 'Acción';
$string['token_deleted'] = 'El token se eliminó correctamente';
$string['no_token_to_cleanup'] = 'No hay ningún token OIDC para limpiar.';
$string['errorusermatched'] = 'La cuenta de Microsoft 365 "{$a->entraidupn}" ya coincide con el usuario de Moodle "{$a->username}". Para completar la conexión, primero inicie sesión como ese usuario de Moodle y siga las instrucciones en el bloque de Microsoft.';
$string['update_oncreate_and_onlogin'] = 'Al crear y cada inicio de sesión';
$string['update_oncreate_and_onlogin_and_usersync'] = 'En el momento de la creación, se ejecuta cada inicio de sesión y cada tarea de sincronización de usuario.';
$string['update_onlogin_and_usersync'] = 'En cada inicio de sesión y en cada tarea de sincronización de usuario ejecutada';
$string['settings_fieldmap_feild_not_mapped'] = '(no mapeado)';
$string['settings_fieldmap_field_city'] = 'Ciudad';
$string['settings_fieldmap_field_companyName'] = 'nombre de empresa';
$string['settings_fieldmap_field_objectId'] = 'ID de objeto';
$string['settings_fieldmap_field_country'] = 'País';
$string['settings_fieldmap_field_department'] = 'Departamento';
$string['settings_fieldmap_field_displayName'] = 'Nombre para mostrar';
$string['settings_fieldmap_field_surname'] = 'Apellido';
$string['settings_fieldmap_field_faxNumber'] = 'Número de fax';
$string['settings_fieldmap_field_telephoneNumber'] = 'Número telefónico';
$string['settings_fieldmap_field_givenName'] = 'Nombre de pila';
$string['settings_fieldmap_field_jobTitle'] = 'Título profesional';
$string['settings_fieldmap_field_mail'] = 'Correo electrónico';
$string['settings_fieldmap_field_mobile'] = 'Móvil';
$string['settings_fieldmap_field_postalCode'] = 'Código Postal';
$string['settings_fieldmap_field_preferredLanguage'] = 'Idioma';
$string['settings_fieldmap_field_state'] = 'Estado';
$string['settings_fieldmap_field_streetAddress'] = 'Dirección';
$string['settings_fieldmap_field_userPrincipalName'] = 'Nombre de usuario (UPN)';
$string['settings_fieldmap_field_employeeId'] = 'ID de empleado';
$string['settings_fieldmap_field_businessPhones'] = 'Teléfono de oficina';
$string['settings_fieldmap_field_mobilePhone'] = 'teléfono móvil';
$string['settings_fieldmap_field_officeLocation'] = 'Oficina';
$string['settings_fieldmap_field_preferredName'] = 'Nombre preferido';
$string['settings_fieldmap_field_manager'] = 'Nombre del administrador';
$string['settings_fieldmap_field_manager_email'] = 'Correo electrónico del administrador';
$string['settings_fieldmap_field_teams'] = 'equipos';
$string['settings_fieldmap_field_groups'] = 'Grupos';
$string['settings_fieldmap_field_roles'] = 'Roles';
$string['settings_fieldmap_field_onPremisesSamAccountName'] = 'Nombre de cuenta SAM local';
$string['settings_fieldmap_field_extensionattribute'] = 'Atributo de extensión {$a}';
$string['settings_fieldmap_field_sds_school_id'] = 'Identificación escolar SDS ({$a})';
$string['settings_fieldmap_field_sds_school_name'] = 'Nombre de la escuela SDS ({$a})';
$string['settings_fieldmap_field_sds_school_role'] = 'Rol de la escuela SDS ("Estudiante" o "Profesor")';
$string['settings_fieldmap_field_sds_student_externalId'] = 'Identificación externa de estudiante SDS';
$string['settings_fieldmap_field_sds_student_birthDate'] = 'Fecha de nacimiento del estudiante SDS';
$string['settings_fieldmap_field_sds_student_grade'] = 'Calificación del estudiante SDS';
$string['settings_fieldmap_field_sds_student_graduationYear'] = 'Año de graduación de estudiantes de SDS';
$string['settings_fieldmap_field_sds_student_studentNumber'] = 'Número de estudiante SDS';
$string['settings_fieldmap_field_sds_teacher_externalId'] = 'ID externo del profesor SDS';
$string['settings_fieldmap_field_sds_teacher_teacherNumber'] = 'Número de maestro SDS';
