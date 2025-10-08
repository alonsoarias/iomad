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
 * Strings for component 'tool_iomadmerge', language 'es'.
 *
 * @package   tool_iomadmerge
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 o posterior
 */

defined('MOODLE_INTERNAL') || die();

$string['choose_users'] = 'Elija usuarios para fusionar';
$string['clear_selection'] = 'Borrar la selección de usuario actual';
$string['cligathering:description'] = 'Introduzca pares de ID de usuario para fusionar el primero con el segundo 
. La primera identificación de usuario (fromid) "perderá" todos sus datos para ser "migrados" 
 al segundo (toid). El usuario \'toid\' incluirá datos de ambos usuarios.';
$string['cligathering:fromid'] = 'ID de usuario de origen (fromid):';
$string['cligathering:stopping'] = 'Para detener la fusión, Ctrl+C o escriba -1 en los campos fromid o toid.';
$string['cligathering:toid'] = 'ID de usuario de destino (toid):';
$string['dbko_no_transactions'] = '<strong>¡Falló la fusión!</strong> <br/>Su motor de base de datos no admite transacciones. Por lo tanto, su base de datos <strong>ha sido actualizada</strong>. El estado de su base de datos puede ser inconsistente. <br/>Pero eche un vistazo al registro de fusión y, por favor, informe sobre el error a los desarrolladores de complementos. Obtendrá una solución en poco tiempo. Después de actualizar el complemento a su última versión, que incluirá la solución a ese problema, repita la acción de fusión para completarla con éxito.';
$string['dbko_transactions'] = '<strong>¡Falló la fusión!</strong> <br/>Su motor de base de datos admite transacciones. Por lo tanto, toda la transacción actual se ha revertido y <strong>no se ha realizado ninguna modificación en su base de datos</strong>.';
$string['dbok'] = 'Fusión exitosa';
$string['deleted'] = 'Usuario con ID {$a} fue eliminado';
$string['error_return'] = 'Volver al formulario de búsqueda';
$string['errorsameuser'] = 'Intentando fusionar el mismo usuario';
$string['errortransactionsonly'] = 'Error: se requieren transacciones, pero su tipo de base de datos {$a} no las admite. Si es necesario, puede permitir la fusión de usuarios sin transacciones. Por favor, revise la configuración del complemento para configurarlos en consecuencia.';
$string['eventusermergedfailure'] = 'Fusión fallida';
$string['eventusermergedsuccess'] = 'Fusión exitosa';
$string['excluded_exceptions'] = 'Excluir excepciones';
$string['excluded_exceptions_desc'] = 'La experiencia sobre este tema sugiere que todas estas tablas de bases de datos deberían excluirse de la fusión. Consulte LÉAME para obtener más detalles. <br> Por lo tanto, para aplicar el comportamiento predeterminado del complemento, debe elegir \'{$a}\' para excluir todas esas tablas del proceso de fusión (recomendado). <br> Si lo prefiere, puede excluir cualquiera de esas tablas e incluirlas en el proceso de fusión (no recomendado).';
$string['exportlogs'] = 'Exportar registros como CSV';
$string['finishtime'] = 'Finalizada la fusión en {$a}';
$string['form_description'] = '<p>Puede buscar usuarios aquí si no conoce el nombre de usuario/número de identificación del usuario. De lo contrario, puede ampliar el formulario para ingresar esa información directamente.  Consulte la ayuda sobre los campos para obtener más información</p>';
$string['form_header'] = 'Buscar usuarios para fusionar';
$string['header'] = 'Fusionar dos usuarios en una sola cuenta';
$string['header_help'] = '<p>Dado un usuario que se va a eliminar y un usuario que se va a conservar, esto fusionará los datos de usuario asociados con el usuario anterior con los del último usuario. Tenga en cuenta que ambos usuarios ya deben existir y no se eliminará ninguna cuenta. Ese proceso se deja en manos del administrador para que lo haga manualmente.</p><p><strong>¡Haga esto solo si sabe lo que está haciendo, ya que no es reversible!</strong></p>';
$string['into'] = 'en';
$string['invalid_option'] = 'Opción de formulario no válida';
$string['invaliduser'] = 'Usuario no válido';
$string['logid'] = 'Para mayor referencia, estos resultados se registran en el ID de registro {$a}.';
$string['logko'] = 'Se produjo algún error:';
$string['loglist'] = 'Todos estos registros están fusionando acciones realizadas, mostrando si salieron bien:';
$string['logok'] = 'Estas son las consultas que se han enviado a la base de datos:';
$string['mergedbyuseridonlog'] = 'Fusionado por';
$string['iomadmerge'] = 'Fusionar cuentas de usuario';
$string['iomadmergeadvanced'] = '<strong>Entrada directa del usuario</strong>';
$string['iomadmergeadvanced_help'] = 'Aquí puede ingresar los campos a continuación si sabe exactamente qué usuarios desea fusionar.<br /><br /> Haga clic en el botón "buscar" para verificar/confirmar que los datos ingresados ​​son en realidad usuarios.';
$string['iomadmerge_confirm'] = 'Después de confirmar, se iniciará el proceso de fusión. <br /><strong>¡Esto no será reversible!</strong> ¿Estás seguro de que quieres continuar?';
$string['iomadmerge:iomadmerge'] = 'Fusionar cuentas de usuario';
$string['iomadmerge:view'] = 'Fusionar cuentas de usuario';
$string['merging'] = 'Fusionado';
$string['newuser'] = 'Usuario a conservar';
$string['newuserid'] = 'ID de usuario que se mantendrá';
$string['newuseridonlog'] = 'Usuario mantenido';
$string['nologs'] = 'Aún no hay registros de fusión. ¡Bien por usted!';
$string['nomergedby'] = 'No grabado';
$string['no_saveselection'] = 'No seleccionó ningún usuario antiguo ni nuevo.';
$string['olduser'] = 'Usuario para eliminar';
$string['olduserid'] = 'ID de usuario que se eliminará';
$string['olduseridonlog'] = 'Usuario eliminado';
$string['pluginname'] = 'IOMAD Fusionar cuentas de usuario';
$string['privacy:metadata'] = 'El complemento IOMAD Merge User Accounts no almacena ningún dato personal.';
$string['qa_action_delete_fromid'] = 'Mantener los intentos del nuevo usuario.';
$string['qa_action_delete_toid'] = 'Mantener los intentos del usuario anterior.';
$string['qa_action_remain'] = 'No hacer nada: no fusionar ni eliminar';
$string['qa_action_remain_log'] = 'Los datos de usuario de la tabla <strong>{$a}</strong> no se actualizan.';
$string['qa_action_renumber'] = 'Fusionar intentos de ambos usuarios y renumerar';
$string['qa_chosen_action'] = 'Opción activa para intentos de prueba: {$a}.';
$string['qa_grades'] = 'Calificaciones recalculadas para las pruebas: {$a}.';
$string['quizattemptsaction'] = 'Cómo resolver intentos de prueba';
$string['quizattemptsaction_desc'] = 'Al fusionar intentos de prueba, pueden existir tres casos: <ol><li>Solo el usuario anterior tiene intentos de prueba. Todos los intentos aparecerán como si los hubiera realizado el nuevo usuario.</li><li>Solo el nuevo usuario tiene intentos de prueba. Todo es correcto y no se hace nada.</li><li>Ambos usuarios tienen intentos para el mismo cuestionario. <strong>Tienes que elegir qué hacer en este caso de conflicto.</strong>. Debe elegir una de las siguientes acciones: <ul> <li><strong>{$a->renumber}</strong>. Los intentos del usuario anterior se fusionan con los del nuevo usuario y se vuelven a numerar en el momento en que se iniciaron.</li><li><strong>{$a->delete_fromid}</strong>. Se eliminan los intentos del usuario anterior. Los intentos del nuevo usuario se conservan, ya que esta opción los considera los más importantes.</li><li><strong>{$a->delete_toid}</strong>. Se eliminan los intentos del nuevo usuario. Los intentos del usuario anterior se conservan, ya que esta opción los considera como los más importantes.</li><li><strong>{$a->remain}</strong> (por defecto). Los intentos no se fusionan ni se eliminan, permaneciendo relacionados con el usuario que los realizó. Esta es la acción más segura, pero fusionar usuarios del usuario A con el usuario B o de B con A puede producir diferentes calificaciones en las pruebas.</li></ul> </li></ol>';
$string['results'] = 'Fusionar resultados y registro';
$string['review_users'] = 'Confirmar usuarios para fusionar';
$string['saveselection_submit'] = 'Guardar selección';
$string['searchuser'] = 'Buscar usuario';
$string['searchuser_help'] = 'Ingrese un nombre de usuario, nombre/apellido, dirección de correo electrónico o identificación de usuario para buscar usuarios potenciales. También puede especificar si solo desea buscar en un campo en particular.';
$string['starttime'] = 'Comenzó a fusionarse en {$a}';
$string['suspenduser_setting'] = 'Suspender usuario antiguo';
$string['suspenduser_setting_desc'] = 'Si está habilitado, suspende automáticamente al usuario anterior tras un proceso de fusión exitoso, evitando que el usuario inicie sesión en Moodle (recomendado). Si está deshabilitado, el usuario anterior permanece activo. En ambos casos, el usuario antiguo no dispondrá de sus datos relacionados.';
$string['tableko'] = 'Tabla {$a}: ¡la actualización NO ESTÁ BIEN!';
$string['tableok'] = 'Tabla {$a}: actualización correcta';
$string['tableskipped'] = 'Por razones de seguridad o de registro, omitiremos <strong>{$a}</strong>. <br />Para eliminar estas entradas, elimine el usuario anterior una vez que este script se haya ejecutado correctamente.';
$string['timetaken'] = 'La fusión tomó {$a} segundos';
$string['transactions_not_supported'] = 'Para su información, su base de datos <strong>no admite transacciones</strong>.';
$string['transactions_setting'] = 'Sólo se permiten transacciones';
$string['transactions_setting_desc'] = 'Si está habilitado, fusionar usuarios no funcionará en absoluto en bases de datos que NO admitan transacciones (recomendado). Habilitarlo es necesario para garantizar que su base de datos permanezca consistente en caso de errores de fusión. <br />Si está deshabilitado, siempre ejecutará acciones de fusión. En caso de errores, el registro de fusión le mostrará cuál fue el problema. Informarlo a quienes apoyan el complemento le dará una solución breve.<br />Sobre todo, este complemento ya considera las tablas principales de Moodle y algunos complementos de terceros. Si no tiene ningún complemento de terceros en su instalación de Moodle, puede ejecutar este complemento habilitando o deshabilitando esta opción.';
$string['transactions_supported'] = 'Para su información, su base de datos <strong>admite transacciones</strong>.';
$string['uniquekeynewidtomaintain'] = 'Mantener los datos del nuevo usuario';
$string['uniquekeynewidtomaintain_desc'] = 'En caso de conflicto, como cuando la columna relacionada user.id es una clave única, este complemento conservará los datos del nuevo usuario (de forma predeterminada). Esto también significa que los datos del usuario anterior se eliminan para mantener la coherencia. De lo contrario, si desmarca esta opción, se conservarán los datos del usuario anterior.';
$string['usermergingheader'] = '&laquo;{$a->username}&raquo; (ID de usuario = {$a->id})';
$string['userreviewtable_legend'] = '<b>Revisar usuarios para fusionar</b>';
$string['userselecttable_legend'] = '<b>Seleccione usuarios para fusionar</b>';
$string['viewlog'] = 'Ver registros de fusión';
$string['wronglogid'] = 'El registro que solicita no existe.';
