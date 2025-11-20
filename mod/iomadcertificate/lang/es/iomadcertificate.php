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

/**
 * Strings for component 'iomadcertificate', language 'es'
 *
 * @translator Alonso Arias <soporte@orioncloud.com.co>
 */

$string['addlinklabel'] = 'Agregar otra opción de actividad vinculada';
$string['addlinktitle'] = 'Haga clic para agregar otra opción de actividad vinculada';
$string['areaintro'] = 'Introducción del certificado';
$string['awarded'] = 'Otorgado';
$string['awardedto'] = 'Otorgado a';
$string['back'] = 'Atrás';
$string['border'] = 'Borde';
$string['borderblack'] = 'Negro';
$string['borderblue'] = 'Azul';
$string['borderbrown'] = 'Café';
$string['bordercolor'] = 'Líneas de borde';
$string['bordercolor_help'] = 'Dado que las imágenes pueden aumentar sustancialmente el tamaño del archivo PDF, puede elegir imprimir un borde de líneas en lugar de usar una imagen de borde (asegúrese de que la opción \'Imagen de borde\' esté configurada en No). La opción \'Líneas de borde\' imprimirá un borde de tres líneas de diferentes anchos en el color elegido.';
$string['bordergreen'] = 'Verde';
$string['borderlines'] = 'Líneas';
$string['borderstyle'] = 'Imagen de borde';
$string['borderstyle_help'] = 'La opción \'Imagen de borde\' le permite elegir una imagen de borde de la carpeta certificate/pix/borders. Seleccione la imagen de borde que desea alrededor de los bordes del certificado o seleccione \'sin borde\'.';
$string['iomadcertificate'] = 'Verificación del código del certificado:';
$string['iomadcertificate:addinstance'] = 'Agregar una instancia de certificado';
$string['iomadcertificate:manage'] = 'Gestionar una instancia de certificado';
$string['iomadcertificate:printteacher'] = 'Aparecer como profesor en el certificado si la configuración de imprimir profesor está activada';
$string['iomadcertificate:student'] = 'Obtener un certificado';
$string['iomadcertificate:view'] = 'Ver un certificado';
$string['iomadcertificate:viewother'] = 'Ver el certificado de otro usuario';
$string['iomadcertificatename'] = 'Nombre del certificado';
$string['iomadcertificatereport'] = 'Reporte de certificados';
$string['iomadcertificatesfor'] = 'Certificados para';
$string['iomadcertificatetype'] = 'Tipo de certificado';
$string['iomadcertificatetype_help'] = 'Aquí es donde determina el diseño del certificado. La carpeta de tipo de certificado incluye cuatro certificados predeterminados:
A4 Embedded imprime en papel tamaño A4 con fuente embebida.
A4 Non-Embedded imprime en papel tamaño A4 sin fuentes embebidas.
Letter Embedded imprime en papel tamaño carta con fuente embebida.
Letter Non-Embedded imprime en papel tamaño carta sin fuentes embebidas.

Los tipos sin embeber usan las fuentes Helvetica y Times. Si cree que sus usuarios no tendrán estas fuentes en su computadora, o si su idioma usa caracteres o símbolos que no son compatibles con las fuentes Helvetica y Times, entonces elija un tipo embebido. Los tipos embebidos usan las fuentes Dejavusans y Dejavuserif. Esto hará que los archivos PDF sean bastante grandes; no se recomienda usar un tipo embebido a menos que sea su única opción.

Se pueden agregar nuevas carpetas de tipo a la carpeta certificate/type. El nombre de la carpeta y cualquier nueva cadena de idioma para el nuevo tipo deben agregarse al archivo de idioma del certificado.';
$string['certify'] = 'Esto certifica que';
$string['code'] = 'Código';
$string['completiondate'] = 'Finalización del curso';
$string['course'] = 'Para';
$string['coursegrade'] = 'Calificación del curso';
$string['coursename'] = 'Curso';
$string['coursetimereq'] = 'Minutos requeridos en el curso';
$string['coursetimereq_help'] = 'Ingrese aquí la cantidad mínima de tiempo, en minutos, que un estudiante debe estar conectado al curso antes de poder recibir el certificado.';
$string['credithours'] = 'Horas de crédito';
$string['customtext'] = 'Texto personalizado';
$string['customtext_help'] = 'Si desea que el certificado imprima diferentes nombres para el profesor para aquellos que tienen asignado el rol de profesor, no seleccione Imprimir profesor ni ninguna imagen de firma excepto la imagen de línea. Ingrese los nombres de los profesores en este cuadro de texto como desea que aparezcan. Por defecto, este texto se coloca en la parte inferior izquierda del certificado. Las siguientes etiquetas HTML están disponibles: &lt;br&gt;, &lt;p&gt;, &lt;b&gt;, &lt;i&gt;, &lt;u&gt;, &lt;img&gt; (src y width (o height) son obligatorios), &lt;a&gt; (href es obligatorio), &lt;font&gt; (atributos posibles: colour, (código de color hex), face, (arial, times, courier, helvetica, symbol)).';
$string['datefmt'] = 'Formato de fecha';
$string['datefmt_help'] = 'Elija un formato de fecha para imprimir la fecha en el certificado. O elija la última opción para que la fecha se imprima en el formato del idioma elegido por el usuario.';
$string['datehelp'] = 'Fecha';
$string['deletissuediomadcertificates'] = 'Eliminar certificados emitidos';
$string['delivery'] = 'Entrega';
$string['delivery_help'] = 'Elija aquí cómo desea que sus estudiantes obtengan su certificado.
Abrir en navegador: Abre el certificado en una nueva ventana del navegador.
Forzar descarga: Abre la ventana de descarga de archivos del navegador.
Enviar certificado por correo electrónico: Al elegir esta opción, se envía el certificado al estudiante como un archivo adjunto de correo electrónico.
Después de que un usuario reciba su certificado, si hace clic en el enlace del certificado desde la página principal del curso, verá la fecha en que recibió su certificado y podrá revisar su certificado recibido.';
$string['designoptions'] = 'Opciones de diseño';
$string['download'] = 'Forzar descarga';
$string['emailiomadcertificate'] = 'Correo electrónico';
$string['emailothers'] = 'Enviar correo electrónico a otros';
$string['emailothers_help'] = 'Ingrese aquí las direcciones de correo electrónico, separadas por comas, de aquellos que deben ser alertados con un correo electrónico cada vez que los estudiantes reciban un certificado.';
$string['emailstudenttext'] = 'Adjunto está su certificado para {$a->course}.';
$string['emailteachers'] = 'Enviar correo electrónico a profesores';
$string['emailteachers_help'] = 'Si está habilitado, los profesores son alertados con un correo electrónico cada vez que los estudiantes reciben un certificado.';
$string['emailteachermail'] = '
{$a->student} ha recibido su certificado: \'{$a->certificate}\'
para {$a->course}.

Puede revisar el certificado aquí:

    {$a->url}';
$string['emailteachermailhtml'] = '
{$a->student} ha recibido su certificado: \'<i>{$a->certificate}</i>\'
para {$a->course}.

Puede revisar el certificado aquí:

    <a href="{$a->url}">Reporte de certificados</a>';
$string['entercode'] = 'Ingrese el código del certificado para verificar:';
$string['fontsans'] = 'Familia de fuente sans-serif';
$string['fontsans_desc'] = 'Familia de fuente sans-serif para certificados con fuentes embebidas';
$string['fontserif'] = 'Familia de fuente serif';
$string['fontserif_desc'] = 'Familia de fuente serif para certificados con fuentes embebidas';
$string['getiomadcertificate'] = 'Obtenga su certificado';
$string['grade'] = 'Calificación';
$string['gradedate'] = 'Fecha de calificación';
$string['gradefmt'] = 'Formato de calificación';
$string['gradefmt_help'] = 'Hay tres formatos disponibles si elige imprimir una calificación en el certificado:

Calificación porcentual: Imprime la calificación como un porcentaje.
Calificación en puntos: Imprime el valor en puntos de la calificación.
Calificación con letra: Imprime la calificación porcentual como una letra.';
$string['gradeletter'] = 'Calificación con letra';
$string['gradepercent'] = 'Calificación porcentual';
$string['gradepoints'] = 'Calificación en puntos';
$string['imagetype'] = 'Tipo de imagen';
$string['incompletemessage'] = 'Para descargar su certificado, primero debe completar todas las actividades requeridas. Por favor regrese al curso para completar su trabajo.';
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
$string['noiomadcertificatesreceived'] = 'no ha recibido ningún certificado del curso.';
$string['nofileselected'] = '¡Debe elegir un archivo para subir!';
$string['nogrades'] = 'No hay calificaciones disponibles';
$string['notapplicable'] = 'N/A';
$string['notfound'] = 'El número de certificado no pudo ser validado.';
$string['notissued'] = 'No emitido';
$string['notissuedyet'] = 'Aún no emitido';
$string['notreceived'] = 'No ha recibido este certificado';
$string['openbrowser'] = 'Abrir en nueva ventana';
$string['opendownload'] = 'Haga clic en el botón de abajo para guardar su certificado en su computadora.';
$string['openemail'] = 'Haga clic en el botón de abajo y su certificado se le enviará como un archivo adjunto de correo electrónico.';
$string['openwindow'] = 'Haga clic en el botón de abajo para abrir su certificado en una nueva ventana del navegador.';
$string['or'] = 'O';
$string['orientation'] = 'Orientación';
$string['orientation_help'] = 'Elija si desea que la orientación de su certificado sea vertical u horizontal.';
$string['pluginadministration'] = 'Administración de certificado IOMAD';
$string['pluginname'] = 'Certificado IOMAD';
$string['portrait'] = 'Vertical';
$string['printdate'] = 'Imprimir fecha';
$string['printdate_help'] = 'Esta es la fecha que se imprimirá en el certificado si se selecciona una fecha de impresión. Si se selecciona la fecha de finalización del curso pero el estudiante no ha completado el curso, se imprimirá la fecha en que se recibió el certificado. También puede elegir imprimir la fecha basándose en cuándo se calificó una actividad. Si se emite un certificado antes de que se califique esa actividad, se imprimirá la fecha de recepción.';
$string['printerfriendly'] = 'Página para impresora';
$string['printhours'] = 'Imprimir horas de crédito';
$string['printhours_help'] = 'Ingrese aquí el número de horas de crédito que se imprimirán en el certificado.';
$string['printgrade'] = 'Imprimir calificación';
$string['printgrade_help'] = 'Puede elegir cualquier elemento de calificación del curso disponible en el libro de calificaciones para imprimir la calificación recibida por el usuario para ese elemento en el certificado. Los elementos de calificación se enumeran en el orden en que aparecen en el libro de calificaciones. Elija el formato de la calificación a continuación.';
$string['printnumber'] = 'Imprimir código';
$string['printnumber_help'] = 'Se puede imprimir en el certificado un código único de 10 dígitos de letras y números aleatorios. Este número puede verificarse comparándolo con el número de código que se muestra en el reporte de certificados.';
$string['printoutcome'] = 'Imprimir resultado';
$string['printoutcome_help'] = 'Puede elegir cualquier resultado del curso para imprimir el nombre del resultado y el resultado recibido por el usuario en el certificado. Un ejemplo podría ser \'Resultado de la tarea: Competente\'.';
$string['printseal'] = 'Imagen de sello o logo';
$string['printseal_help'] = 'Esta opción le permite seleccionar un sello o logo para imprimir en el certificado desde la carpeta certificate/pix/seals. Por defecto, esta imagen se coloca en la esquina inferior derecha del certificado.';
$string['printsignature'] = 'Imagen de firma';
$string['printsignature_help'] = 'Esta opción le permite imprimir una imagen de firma desde la carpeta certificate/pix/signatures. Puede imprimir una representación gráfica de una firma o imprimir una línea para una firma escrita. Por defecto, esta imagen se coloca en la parte inferior izquierda del certificado.';
$string['printteacher'] = 'Imprimir nombre(s) del profesor';
$string['printteacher_help'] = 'Para imprimir el nombre del profesor en el certificado, establezca el rol de profesor a nivel de módulo. Haga esto si, por ejemplo, tiene más de un profesor para el curso o más de un certificado en el curso y desea imprimir diferentes nombres de profesores en cada certificado. Haga clic para editar el certificado, luego haga clic en la pestaña \'Roles asignados localmente\'. Luego asigne el rol de profesor (profesor editor) al certificado (NO TIENEN que ser profesores del curso - puede asignar ese rol a cualquiera). Esos nombres se imprimirán en el certificado para el profesor.';
$string['printwmark'] = 'Imagen de marca de agua';
$string['printwmark_help'] =  'Se puede colocar una imagen de marca de agua en el fondo del certificado. Podría ser un logo, sello, escudo, texto o lo que desee usar como fondo gráfico.';
$string['receivedcerts'] = 'Certificados recibidos';
$string['receiveddate'] = 'Fecha de recepción';
$string['removecert'] = 'Certificados emitidos eliminados';
$string['report'] = 'Reporte';
$string['reportcert'] = 'Reportar certificados';
$string['reportcert_help'] = 'Si elige sí aquí, entonces la fecha de recepción de este certificado, el número de código y el nombre del curso se mostrarán en los reportes de certificados del usuario. Si elige imprimir una calificación en este certificado, entonces esa calificación también se mostrará en el reporte de certificados.';
$string['requiredtimenotmet'] = 'Debe pasar al menos un mínimo de {$a->requiredtime} minutos en el curso antes de poder acceder a este certificado';
$string['requiredtimenotvalid'] = 'El tiempo requerido debe ser un número válido mayor que 0';
$string['reviewiomadcertificate'] = 'Revisar su certificado';
$string['savecert'] = 'Guardar certificados';
$string['savecert_help'] = 'Si elige esta opción, se guardará una copia del archivo PDF del certificado de cada usuario en el directorio moodledata. Se mostrará un enlace al certificado guardado de cada usuario en el reporte de certificados.';
$string['seal'] = 'Sello';
$string['sigline'] = 'línea';
$string['signature'] = 'Firma';
$string['statement'] = 'ha completado el curso';
$string['summaryofattempts'] = 'Resumen de certificados recibidos anteriormente';
$string['textoptions'] = 'Opciones de texto';
$string['title'] = 'CERTIFICADO de LOGRO';
$string['to'] = 'Otorgado a';
$string['typeA4_embedded'] = 'A4 Embebido';
$string['typeA4_non_embedded'] = 'A4 No embebido';
$string['typeletter_embedded'] = 'Carta embebido';
$string['typeletter_non_embedded'] = 'Carta no embebido';
$string['unsupportedfiletype'] = 'El archivo debe ser un archivo jpeg o png';
$string['uploadimage'] = 'Subir imagen';
$string['uploadimagedesc'] = 'Este botón lo llevará a una nueva pantalla donde podrá subir imágenes';
$string['userdateformat'] = 'Formato de fecha del idioma del usuario';
$string['validate'] = 'Verificar';
$string['verifyiomadcertificate'] = 'Verificar certificado';
$string['viewiomadcertificateviews'] = 'Ver {$a} certificados emitidos';
$string['viewed'] = 'Recibió este certificado el:';
$string['viewtranscript'] = 'Ver certificados';
$string['watermark'] = 'Marca de agua';
$string['companycertify'] = 'Este Certificado de Finalización reconoce que';
$string['companydetails'] = 'ha completado exitosamente el programa de capacitación basado en web titulado';
$string['companyscore'] = 'con una puntuación general de {$a}';
$string['companydate'] = 'el {$a}';
$string['companydatecap'] = 'El {$a}';
