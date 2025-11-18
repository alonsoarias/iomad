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
 * Define default English language strings for report
 *
 * @author Forrest Gaston
 * @author Juan Pablo Torres Herrera
 * @author Shane Elliott, Pukunui Technology
 * @author Jordi Pujol-Ahulló, SREd, Universitat Rovira i Virgili
 * @author John Hoopes <hoopes@wisc.edu>, University of Wisconsin - Madison
 * @package tool_iomadmerge
 * @link http://moodle.org/mod/forum/discuss.php?d=103425
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['choose_users'] = 'Elegir usuarios para fusionar';
$string['clear_selection'] = 'Limpiar la selección actual de usuario';
$string['cligathering:description'] = "Introduce pares de ID de usuario para fusionar el primero en el\nsegundo. El primer ID de usuario (fromid) 'perderá' todos sus datos para ser 'migrados'\nal segundo (toid). El usuario 'toid' incluirá datos de ambos usuarios.";
$string['cligathering:fromid'] = 'ID de usuario de origen (fromid):';
$string['cligathering:stopping'] = 'Para detener la fusión, presione Ctrl+C o escriba -1 en los campos fromid o toid.';
$string['cligathering:toid'] = 'ID de usuario de destino   (toid):';
$string['dbko_no_transactions'] = '<strong>¡Fusión fallida!</strong> <br/>Su motor de base de datos no soporta transacciones. Por lo tanto, su base de datos <strong>ha sido actualizada</strong>. El estado de su base de datos puede ser inconsistente. <br/>Sin embargo, revise el registro de fusión e informe sobre el error a los desarrolladores del plugin. Obtendrá una solución en poco tiempo. Después de actualizar el plugin a su última versión, que incluirá la solución a ese problema, repita la acción de fusión para completarla con éxito.';
$string['dbko_transactions'] = '<strong>¡Fusión fallida!</strong> <br/>Su motor de base de datos soporta transacciones. Por lo tanto, toda la transacción actual ha sido revertida y <strong>no se ha realizado ninguna modificación en su base de datos</strong>.';
$string['dbok'] = 'Fusión exitosa';
$string['deleted'] = 'El usuario con ID {$a} fue eliminado';
$string['error_return'] = 'Volver al formulario de búsqueda';
$string['errorsameuser'] = 'Intentando fusionar el mismo usuario';
$string['errortransactionsonly'] = 'Error: se requieren transacciones, pero su tipo de base de datos {$a} no las soporta. Si es necesario, puede permitir fusionar usuarios sin transacciones. Por favor, revise la configuración del plugin para configurarlas adecuadamente.';
$string['eventusermergedfailure'] = 'Fusión fallida';
$string['eventusermergedsuccess'] = 'Fusión exitosa';
$string['excluded_exceptions'] = 'Excluir excepciones';
$string['excluded_exceptions_desc'] = 'La experiencia en este tema sugiere que todas estas tablas de base de datos deben ser excluidas de la fusión. Vea el README para más detalles. <br> Por lo tanto, para aplicar el comportamiento predeterminado del plugin, necesita elegir \'{$a}\' para excluir todas esas tablas del proceso de fusión (recomendado).<br> Si lo prefiere, puede excluir cualquiera de esas tablas e incluirlas en el proceso de fusión (no recomendado).';
$string['exportlogs'] = 'Exportar registros como CSV';
$string['finishtime'] = 'Fusión finalizada a las {$a}';
$string['form_description'] = '<p>Puede buscar usuarios aquí si no conoce el nombre de usuario/número de ID del usuario. De lo contrario, puede expandir el formulario para ingresar esa información directamente. Por favor, consulte la ayuda en los campos para más información</p>';
$string['form_header'] = 'Buscar usuarios para fusionar';
$string['header'] = 'Fusionar dos usuarios en una sola cuenta';
$string['header_help'] ='<p>Dado un usuario a eliminar y un usuario a mantener, esto fusionará los datos del usuario asociados con el primer usuario en el último usuario. Tenga en cuenta que ambos usuarios deben existir previamente y ninguna cuenta será eliminada realmente. Ese proceso queda en manos del administrador para hacerlo manualmente.</p><p><strong>¡Solo haga esto si sabe lo que está haciendo, ya que no es reversible!</strong></p>';
$string['into'] = 'en';
$string['invalid_option'] = 'Opción de formulario no válida';
$string['invaliduser'] = 'Usuario no válido';
$string['logid'] = 'Para referencia futura, estos resultados están registrados en el ID de registro {$a}.';
$string['logko'] = 'Ocurrió algún error:';
$string['loglist'] = 'Todos estos registros son acciones de fusión realizadas, mostrando si fueron exitosas:';
$string['logok'] = 'Aquí están las consultas que se han enviado a la base de datos:';
$string['mergedbyuseridonlog'] = 'Fusionado por';
$string['iomadmerge'] = 'Fusionar cuentas de usuario';
$string['iomadmergeadvanced'] = '<strong>Entrada directa de usuario</strong>';
$string['iomadmergeadvanced_help'] = 'Aquí puede ingresar los campos a continuación si sabe exactamente qué usuarios desea fusionar.<br /><br /> Haga clic en el botón "buscar" para verificar/confirmar que la entrada ingresada son usuarios de hecho.';
$string['iomadmerge_confirm'] = 'Después de confirmar, el proceso de fusión comenzará. <br /><strong>¡Esto no será reversible!</strong> ¿Está seguro de que desea continuar?';
$string['iomadmerge:iomadmerge'] = 'Fusionar cuentas de usuario';
$string['iomadmerge:view'] = 'Fusionar cuentas de usuario';
$string['merging'] = 'Fusionado';
$string['newuser'] = 'Usuario a mantener';
$string['newuserid'] = 'ID de usuario a mantener';
$string['newuseridonlog'] = 'Usuario mantenido';
$string['nologs'] = '¡Aún no hay registros de fusión. Bueno para usted!';
$string['nomergedby'] = 'No registrado';
$string['no_saveselection'] = 'No seleccionó un usuario antiguo o nuevo.';
$string['olduser'] = 'Usuario a eliminar';
$string['olduserid'] = 'ID de usuario a eliminar';
$string['olduseridonlog'] = 'Usuario eliminado';
$string['pluginname'] = 'IOMAD Fusionar cuentas de usuario';
$string['privacy:metadata'] = 'El plugin IOMAD Fusionar Cuentas de Usuario no almacena ningún dato personal.';
$string['qa_action_delete_fromid'] = 'Mantener intentos del usuario nuevo';
$string['qa_action_delete_toid'] = 'Mantener intentos del usuario antiguo';
$string['qa_action_remain'] = 'No hacer nada: no fusionar ni eliminar';
$string['qa_action_remain_log'] = 'Los datos de usuario de la tabla <strong>{$a}</strong> no se actualizan.';
$string['qa_action_renumber'] = 'Fusionar intentos de ambos usuarios y renumerar';
$string['qa_chosen_action'] = 'Opción activa para intentos de cuestionario: {$a}.';
$string['qa_grades'] = 'Calificaciones recalculadas para cuestionarios: {$a}.';
$string['quizattemptsaction'] = 'Cómo resolver intentos de cuestionario';
$string['quizattemptsaction_desc'] = 'Al fusionar intentos de cuestionario pueden existir tres casos: <ol><li>Solo el usuario antiguo tiene intentos de cuestionario. Todos los intentos aparecerán como si fueran realizados por el usuario nuevo.</li><li>Solo el usuario nuevo tiene intentos de cuestionario. Todo está correcto y no se hace nada.</li><li>Ambos usuarios tienen intentos para el mismo cuestionario. <strong>Debe elegir qué hacer en este caso de conflicto.</strong>. Se le requiere elegir una de las siguientes acciones: <ul> <li><strong>{$a->renumber}</strong>. Los intentos del usuario antiguo se fusionan con los del usuario nuevo y se renumeran por el momento en que se iniciaron.</li><li><strong>{$a->delete_fromid}</strong>. Los intentos del usuario antiguo se eliminan. Los intentos del usuario nuevo se mantienen, ya que esta opción los considera como los más importantes.</li><li><strong>{$a->delete_toid}</strong>. Los intentos del usuario nuevo se eliminan. Los intentos del usuario antiguo se mantienen, ya que esta opción los considera como los más importantes.</li><li><strong>{$a->remain}</strong> (por defecto). Los intentos no se fusionan ni se eliminan, permaneciendo relacionados con el usuario que los realizó. Esta es la acción más segura, pero fusionar usuarios de A a B o B a A puede producir calificaciones de cuestionario diferentes.</li></ul> </li></ol>';
$string['results'] = 'Resultados y registro de fusión';
$string['review_users'] = 'Confirmar usuarios para fusionar';
$string['saveselection_submit'] = 'Guardar selección';
$string['searchuser'] = 'Buscar usuario';
$string['searchuser_help'] = 'Ingrese un nombre de usuario, nombre/apellido, dirección de correo electrónico o ID de usuario para buscar usuarios potenciales. También puede especificar si solo desea buscar a través de un campo particular.';
$string['starttime'] = 'Fusión iniciada a las {$a}';
$string['suspenduser_setting'] = 'Suspender usuario antiguo';
$string['suspenduser_setting_desc'] = 'Si está habilitado, suspende al usuario antiguo automáticamente tras un proceso de fusión exitoso, evitando que el usuario inicie sesión en Moodle (recomendado). Si está deshabilitado, el usuario antiguo permanece activo. En ambos casos, el usuario antiguo no tendrá sus datos relacionados.';
$string['tableko'] = 'Tabla {$a} : ¡actualización NO exitosa!';
$string['tableok'] = 'Tabla {$a} : actualización exitosa';
$string['tableskipped'] = 'Por razones de registro o seguridad, estamos omitiendo <strong>{$a}</strong>. <br />Para eliminar estas entradas, elimine el usuario antiguo una vez que este script se haya ejecutado con éxito.';
$string['timetaken'] = 'La fusión tomó {$a} segundos';
$string['transactions_not_supported'] = 'Para su información, su base de datos <strong>no soporta transacciones</strong>.';
$string['transactions_setting'] = 'Solo transacciones permitidas';
$string['transactions_setting_desc'] = 'Si está habilitado, fusionar usuarios no funcionará en absoluto en bases de datos que NO soporten transacciones (recomendado). Habilitarlo es necesario para asegurar que su base de datos permanezca consistente en caso de errores de fusión. <br />Si está deshabilitado, siempre ejecutará acciones de fusión. En caso de errores, el registro de fusión le mostrará cuál fue el problema. Reportarlo a los responsables del plugin le dará una solución en breve.<br />Sobre todo, las tablas principales de Moodle y algunos plugins de terceros ya están considerados por este plugin. Si no tiene ningún plugin de terceros en su instalación de Moodle, puede estar tranquilo al ejecutar este plugin habilitando o deshabilitando esta opción.';
$string['transactions_supported'] = 'Para su información, su base de datos <strong>soporta transacciones</strong>.';
$string['uniquekeynewidtomaintain'] = 'Mantener datos del usuario nuevo';
$string['uniquekeynewidtomaintain_desc'] = 'En caso de conflicto, como cuando la columna relacionada con user.id es una clave única, este plugin mantendrá los datos del usuario nuevo (por defecto). Esto también significa que los datos del usuario antiguo se eliminan para mantener la consistencia. De lo contrario, si desmarca esta opción, se mantendrán los datos del usuario antiguo.';
$string['usermergingheader'] = '&laquo;{$a->username}&raquo; (ID de usuario = {$a->id})';
$string['userreviewtable_legend'] = '<b>Revisar usuarios para fusionar</b>';
$string['userselecttable_legend'] = '<b>Seleccionar usuarios para fusionar</b>';
$string['viewlog'] = 'Ver registros de fusión';
$string['wronglogid'] = 'El registro que está solicitando no existe.';
