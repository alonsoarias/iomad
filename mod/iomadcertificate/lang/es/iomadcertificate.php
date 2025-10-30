<?php

// This file is part of the IOMAD Certificate module for Moodle - http://moodle.org/
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
 * @package   mod_iomadcertificate
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @basedon   mod_certificate by Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['addlinklabel'] = 'Añadir otra opción de actividad vinculada';
$string['addlinktitle'] = 'Haga clic para añadir otra opción de actividad vinculada';
$string['areaintro'] = 'Introducción del certificado';
$string['awarded'] = 'Concedido';
$string['awardedto'] = 'Concedido a';
$string['back'] = 'Volver';
$string['border'] = 'Borde';
$string['borderblack'] = 'Negro';
$string['borderblue'] = 'Azul';
$string['borderbrown'] = 'Marrón';
$string['bordercolor'] = 'Líneas de borde';
$string['bordercolor_help'] = 'Dado que las imágenes pueden aumentar sustancialmente el tamaño del archivo PDF, puede optar por imprimir un borde de líneas en lugar de usar una imagen de borde (asegúrese de que la opción \'Imagen de borde\' esté configurada como No). La opción \'Líneas de borde\' imprimirá un borde de tres líneas de distintos anchos en el color seleccionado.';
$string['bordergreen'] = 'Verde';
$string['borderlines'] = 'Líneas';
$string['borderstyle'] = 'Imagen de borde';
$string['borderstyle_help'] = 'La opción \'Imagen de borde\' le permite elegir una imagen de borde de la carpeta certificate/pix/borders. Seleccione la imagen de borde que desee alrededor de los bordes del certificado o seleccione \'sin borde\'.';
$string['iomadcertificate'] = 'Verificación del código del certificado:';
$string['iomadcertificate:addinstance'] = 'Añadir una instancia de certificado';
$string['iomadcertificate:manage'] = 'Gestionar una instancia de certificado';
$string['iomadcertificate:printteacher'] = 'Aparecer como profesor en el certificado si la configuración de imprimir profesor está activada';
$string['iomadcertificate:student'] = 'Obtener un certificado';
$string['iomadcertificate:view'] = 'Ver un certificado';
$string['iomadcertificate:viewother'] = 'Ver el certificado de otro usuario';
$string['iomadcertificatename'] = 'Nombre del certificado';
$string['iomadcertificatereport'] = 'Informe de certificados';
$string['iomadcertificatesfor'] = 'Certificados para';
$string['iomadcertificatetype'] = 'Tipo de certificado';
$string['iomadcertificatetype_help'] = 'Aquí es donde determina el diseño del certificado. La carpeta de tipo de certificado incluye cuatro certificados predeterminados:
A4 Embedded imprime en papel tamaño A4 con fuente incrustada.
A4 Non-Embedded imprime en papel tamaño A4 sin fuentes incrustadas.
Letter Embedded imprime en papel tamaño carta con fuente incrustada.
Letter Non-Embedded imprime en papel tamaño carta sin fuentes incrustadas.

Los tipos no incrustados utilizan las fuentes Helvetica y Times. Si considera que sus usuarios no tendrán estas fuentes en su ordenador, o si su idioma utiliza caracteres o símbolos que no están contemplados por las fuentes Helvetica y Times, entonces elija un tipo incrustado. Los tipos incrustados utilizan las fuentes Dejavusans y Dejavuserif. Esto hará que los archivos PDF sean bastante grandes; no se recomienda usar un tipo incrustado a menos que sea su única opción.

Se pueden añadir nuevas carpetas de tipo a la carpeta certificate/type. El nombre de la carpeta y cualquier cadena de idioma nueva para el nuevo tipo deben añadirse al archivo de idioma del certificado.';
$string['certify'] = 'Se certifica que';
$string['code'] = 'Código';
$string['completiondate'] = 'Finalización del curso';
$string['course'] = 'Para';
$string['coursegrade'] = 'Calificación del curso';
$string['coursename'] = 'Curso';
$string['coursetimereq'] = 'Minutos requeridos en el curso';
$string['coursetimereq_help'] = 'Introduzca aquí la cantidad mínima de tiempo, en minutos, que un estudiante debe estar conectado al curso antes de poder recibir el certificado.';
$string['credithours'] = 'Horas de crédito';
$string['customtext'] = 'Texto personalizado';
$string['customtext_help'] = 'Si desea que el certificado imprima nombres diferentes para el profesor en aquellos que tienen asignado el rol de profesor, no seleccione Imprimir profesor ni ninguna imagen de firma excepto la imagen de línea. Introduzca los nombres de los profesores en este cuadro de texto tal como desea que aparezcan. Por defecto, este texto se coloca en la parte inferior izquierda del certificado. Las siguientes etiquetas HTML están disponibles: &lt;br&gt;, &lt;p&gt;, &lt;b&gt;, &lt;i&gt;, &lt;u&gt;, &lt;img&gt; (src y width (o height) son obligatorios), &lt;a&gt; (href es obligatorio), &lt;font&gt; (los atributos posibles son: colour (código de color hexadecimal), face (arial, times, courier, helvetica, symbol)).';
$string['datefmt'] = 'Formato de fecha';
$string['datefmt_help'] = 'Elija un formato de fecha para imprimir la fecha en el certificado. O elija la última opción para que la fecha se imprima en el formato del idioma elegido por el usuario.';
$string['datehelp'] = 'Fecha';
$string['deletissuediomadcertificates'] = 'Eliminar certificados emitidos';
$string['delivery'] = 'Entrega';
$string['delivery_help'] = 'Elija aquí cómo desea que sus estudiantes obtengan su certificado.
Abrir en el navegador: abre el certificado en una nueva ventana del navegador.
Forzar descarga: abre la ventana de descarga de archivos del navegador.
Enviar certificado por correo: al elegir esta opción se envía el certificado al estudiante como archivo adjunto de correo.
Después de que un usuario reciba su certificado, si hace clic en el enlace del certificado desde la página principal del curso, verá la fecha en que recibió su certificado y podrá revisar el certificado recibido.';
$string['designoptions'] = 'Opciones de diseño';
$string['download'] = 'Forzar descarga';
$string['emailiomadcertificate'] = 'Correo';
$string['emailothers'] = 'Enviar correo a otros';
$string['emailothers_help'] = 'Introduzca aquí las direcciones de correo, separadas por comas, de aquellos que deban recibir una alerta por correo cuando los estudiantes reciban un certificado.';
$string['emailstudenttext'] = 'Adjunto encontrará su certificado para {$a->course}.';
$string['emailteachers'] = 'Enviar correo a profesores';
$string['emailteachers_help'] = 'Si se habilita, los profesores recibirán una alerta por correo cuando los estudiantes reciban un certificado.';
$string['emailteachermail'] = '
{$a->student} ha recibido su certificado: \'{$a->certificate}\'
para {$a->course}.

Puede revisar el certificado aquí:

    {$a->url}';
$string['emailteachermailhtml'] = '
{$a->student} ha recibido su certificado: \'<i>{$a->certificate}</i>\'
para {$a->course}.

Puede revisar el certificado aquí:

    <a href="{$a->url}">Informe de certificados</a>';
$string['entercode'] = 'Introduzca el código del certificado para verificar:';
$string['fontsans'] = 'Familia de fuentes sans-serif';
$string['fontsans_desc'] = 'Familia de fuentes sans-serif para certificados con fuentes incrustadas';
$string['fontserif'] = 'Familia de fuentes serif';
$string['fontserif_desc'] = 'Familia de fuentes serif para certificados con fuentes incrustadas';
$string['getiomadcertificate'] = 'Obtenga su certificado';
$string['grade'] = 'Calificación';
$string['gradedate'] = 'Fecha de calificación';
$string['gradefmt'] = 'Formato de calificación';
$string['gradefmt_help'] = 'Hay tres formatos disponibles si elige imprimir una calificación en el certificado:

Calificación porcentual: imprime la calificación como porcentaje.
Calificación por puntos: imprime el valor en puntos de la calificación.
Calificación por letras: imprime la calificación porcentual como una letra.';
$string['gradeletter'] = 'Calificación por letras';
$string['gradepercent'] = 'Calificación porcentual';
$string['gradepoints'] = 'Calificación por puntos';
$string['imagetype'] = 'Tipo de imagen';
$string['incompletemessage'] = 'Para descargar su certificado, primero debe completar todas las actividades requeridas. Por favor, regrese al curso para completar su trabajo.';
$string['intro'] = 'Introducción';
$string['issueoptions'] = 'Opciones de emisión';
$string['issued'] = 'Emitido';
$string['issueddate'] = 'Fecha de emisión';
$string['landscape'] = 'Horizontal';
$string['lastviewed'] = 'Recibió este certificado por última vez el:';
$string['letter'] = 'Carta';
$string['lockingoptions'] = 'Opciones de bloqueo';
$string['modulename'] = 'Certificado IOMAD';
$string['modulename_help'] = 'Este módulo permite la generación dinámica de certificados basados en condiciones predefinidas establecidas por el profesor.';
$string['modulename_link'] = 'Certificate_module';
$string['modulenameplural'] = 'Certificados IOMAD';
$string['myiomadcertificates'] = 'Mis certificados';
$string['noiomadcertificates'] = 'No hay certificados';
$string['noiomadcertificatesissued'] = 'No hay certificados que hayan sido emitidos';
$string['noiomadcertificatesreceived'] = 'no ha recibido ningún certificado de curso.';
$string['nofileselected'] = 'Debe elegir un archivo para cargar';
$string['nogrades'] = 'No hay calificaciones disponibles';
$string['notapplicable'] = 'N/A';
$string['notfound'] = 'No se pudo validar el número de certificado.';
$string['notissued'] = 'No emitido';
$string['notissuedyet'] = 'Aún no emitido';
$string['notreceived'] = 'No ha recibido este certificado';
$string['openbrowser'] = 'Abrir en ventana nueva';
$string['opendownload'] = 'Haga clic en el botón siguiente para guardar su certificado en su ordenador.';
$string['openemail'] = 'Haga clic en el botón siguiente y su certificado se le enviará como archivo adjunto de correo.';
$string['openwindow'] = 'Haga clic en el botón siguiente para abrir su certificado en una nueva ventana del navegador.';
$string['or'] = 'O';
$string['orientation'] = 'Orientación';
$string['orientation_help'] = 'Elija si desea que la orientación de su certificado sea vertical u horizontal.';
$string['pluginadministration'] = 'Administración del certificado IOMAD';
$string['pluginname'] = 'Certificado IOMAD';
$string['portrait'] = 'Vertical';
$string['printdate'] = 'Imprimir fecha';
$string['printdate_help'] = 'Esta es la fecha que se imprimirá en el certificado si se selecciona imprimir fecha. Si se selecciona la fecha de finalización del curso pero el estudiante no ha completado el curso, se imprimirá la fecha en que se recibió el certificado. También puede elegir imprimir la fecha basándose en cuándo se calificó una actividad. Si se emite un certificado antes de que esa actividad sea calificada, se imprimirá la fecha de recepción.';
$string['printerfriendly'] = 'Página apta para impresora';
$string['printhours'] = 'Imprimir horas de crédito';
$string['printhours_help'] = 'Introduzca aquí el número de horas de crédito que se imprimirán en el certificado.';
$string['printgrade'] = 'Imprimir calificación';
$string['printgrade_help'] = 'Puede elegir cualquier elemento de calificación disponible del libro de calificaciones para imprimir la calificación recibida por el usuario para ese elemento en el certificado. Los elementos de calificación se listan en el orden en que aparecen en el libro de calificaciones. Elija el formato de la calificación a continuación.';
$string['printnumber'] = 'Imprimir código';
$string['printnumber_help'] = 'Se puede imprimir en el certificado un código único de 10 dígitos de letras y números aleatorios. Este número puede verificarse comparándolo con el número de código mostrado en el informe de certificados.';
$string['printoutcome'] = 'Imprimir resultado';
$string['printoutcome_help'] = 'Puede elegir cualquier resultado del curso para imprimir el nombre del resultado y el resultado recibido por el usuario en el certificado. Un ejemplo podría ser \'Resultado de tarea: Competente\'.';
$string['printseal'] = 'Imagen de sello o logo';
$string['printseal_help'] = 'Esta opción le permite seleccionar un sello o logo para imprimir en el certificado desde la carpeta certificate/pix/seals. Por defecto, esta imagen se coloca en la esquina inferior derecha del certificado.';
$string['printsignature'] = 'Imagen de firma';
$string['printsignature_help'] = 'Esta opción le permite imprimir una imagen de firma de la carpeta certificate/pix/signatures. Puede imprimir una representación gráfica de una firma o imprimir una línea para una firma escrita. Por defecto, esta imagen se coloca en la parte inferior izquierda del certificado.';
$string['printteacher'] = 'Imprimir nombre(s) del profesor';
$string['printteacher_help'] = 'Para imprimir el nombre del profesor en el certificado, establezca el rol de profesor a nivel de módulo. Haga esto si, por ejemplo, tiene más de un profesor para el curso o más de un certificado en el curso y desea imprimir nombres de profesores diferentes en cada certificado. Haga clic para editar el certificado y luego haga clic en la pestaña \'Roles asignados localmente\'. Después asigne el rol de profesor (profesor con permiso de edición) al certificado (NO tienen que ser profesores del curso - puede asignar ese rol a cualquier persona). Esos nombres se imprimirán en el certificado como profesor.';
$string['printwmark'] = 'Imagen de marca de agua';
$string['printwmark_help'] =  'Se puede colocar un archivo de imagen de marca de agua en el fondo del certificado. Puede ser un logo, sello, escudo, texto o cualquier elemento que desee usar como fondo gráfico.';
$string['receivedcerts'] = 'Certificados recibidos';
$string['receiveddate'] = 'Fecha de recepción';
$string['removecert'] = 'Certificados emitidos eliminados';
$string['report'] = 'Informe';
$string['reportcert'] = 'Reportar certificados';
$string['reportcert_help'] = 'Si elige sí aquí, entonces la fecha de recepción, el número de código y el nombre del curso de este certificado se mostrarán en los informes de certificados de usuario. Si elige imprimir una calificación en este certificado, entonces esa calificación también se mostrará en el informe del certificado.';
$string['requiredtimenotmet'] = 'Debe pasar al menos un mínimo de {$a->requiredtime} minutos en el curso antes de poder acceder a este certificado';
$string['requiredtimenotvalid'] = 'El tiempo requerido debe ser un número válido mayor que 0';
$string['reviewiomadcertificate'] = 'Revisar su certificado';
$string['savecert'] = 'Guardar certificados';
$string['savecert_help'] = 'Si elige esta opción, entonces se guardará una copia del archivo PDF del certificado de cada usuario en el directorio moodledata. Se mostrará un enlace al certificado guardado de cada usuario en el informe de certificados.';
$string['seal'] = 'Sello';
$string['sigline'] = 'línea';
$string['signature'] = 'Firma';
$string['statement'] = 'ha completado el curso';
$string['summaryofattempts'] = 'Resumen de certificados recibidos anteriormente';
$string['textoptions'] = 'Opciones de texto';
$string['title'] = 'CERTIFICADO DE LOGRO';
$string['to'] = 'Otorgado a';
$string['typeA4_embedded'] = 'A4 incrustado';
$string['typeA4_non_embedded'] = 'A4 no incrustado';
$string['typeletter_embedded'] = 'Carta incrustado';
$string['typeletter_non_embedded'] = 'Carta no incrustado';
$string['unsupportedfiletype'] = 'El archivo debe ser un archivo jpeg o png';
$string['uploadimage'] = 'Cargar imagen';
$string['uploadimagedesc'] = 'Este botón le llevará a una nueva pantalla donde podrá cargar imágenes';
$string['userdateformat'] = 'Formato de fecha del idioma del usuario';
$string['validate'] = 'Verificar';
$string['verifyiomadcertificate'] = 'Verificar certificado';
$string['viewiomadcertificateviews'] = 'Ver {$a} certificados emitidos';
$string['viewed'] = 'Recibió este certificado el:';
$string['viewtranscript'] = 'Ver certificados';
$string['watermark'] = 'Marca de agua';
$string['companycertify'] = 'Este certificado de finalización reconoce que';
$string['companydetails'] = 'ha completado con éxito el programa de capacitación basado en web titulado';
$string['companyscore'] = 'con una puntuación general de {$a}';
$string['companydate'] = 'el {$a}';
$string['companydatecap'] = 'El {$a}';
