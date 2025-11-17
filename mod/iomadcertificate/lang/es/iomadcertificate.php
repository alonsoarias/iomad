<?php

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
$string['bordercolor'] = 'Líneas del borde';
$string['bordercolor_help'] = 'Dado que las imágenes pueden aumentar considerablemente el tamaño del archivo PDF, puede optar por imprimir un borde de líneas en lugar de utilizar una imagen (asegúrese de que la opción «Imagen del borde» esté configurada como No). La opción «Líneas del borde» imprimirá un borde de tres líneas de distintos grosores en el color elegido.';
$string['bordergreen'] = 'Verde';
$string['borderlines'] = 'Líneas';
$string['borderstyle'] = 'Imagen del borde';
$string['borderstyle_help'] = 'La opción «Imagen del borde» le permite elegir una imagen de borde de la carpeta certificate/pix/borders. Seleccione la imagen de borde que desee alrededor de los bordes del certificado o seleccione «sin borde».';
$string['iomadcertificate'] = 'Verificación para el código del certificado:';
$string['iomadcertificate:addinstance'] = 'Añadir una instancia de certificado';
$string['iomadcertificate:manage'] = 'Gestionar una instancia de certificado';
$string['iomadcertificate:printteacher'] = 'Aparecer como profesor en el certificado si la opción de imprimir profesor está activada';
$string['iomadcertificate:student'] = 'Recuperar un certificado';
$string['iomadcertificate:view'] = 'Ver un certificado';
$string['iomadcertificate:viewother'] = 'Ver el certificado de otro usuario';
$string['iomadcertificatename'] = 'Nombre del certificado';
$string['iomadcertificatereport'] = 'Informe de certificados';
$string['iomadcertificatesfor'] = 'Certificados para';
$string['iomadcertificatetype'] = 'Tipo de certificado';
$string['iomadcertificatetype_help'] = 'Aquí es donde se determina el diseño del certificado. La carpeta del tipo de certificado incluye cuatro certificados predeterminados:
A4 Embedded imprime en papel tamaño A4 con fuente incrustada.
A4 Non-Embedded imprime en papel tamaño A4 sin fuentes incrustadas.
Letter Embedded imprime en papel tamaño carta con fuente incrustada.
Letter Non-Embedded imprime en papel tamaño carta sin fuentes incrustadas.

Los tipos no incrustados utilizan las fuentes Helvetica y Times. Si cree que sus usuarios no tendrán estas fuentes en su equipo, o si su idioma utiliza caracteres o símbolos que no están contemplados por las fuentes Helvetica y Times, entonces elija un tipo incrustado. Los tipos incrustados utilizan las fuentes Dejavusans y Dejavuserif. Esto hará que los archivos PDF sean bastante grandes; no se recomienda utilizar un tipo incrustado a menos que sea la única opción.

Se pueden añadir nuevas carpetas de tipos a la carpeta certificate/type. El nombre de la carpeta y cualquier cadena de idioma nueva para el nuevo tipo deben añadirse al archivo de idioma del certificado.';
$string['certify'] = 'Por la presente se certifica que';
$string['code'] = 'Código';
$string['completiondate'] = 'Finalización del curso';
$string['course'] = 'Para';
$string['coursegrade'] = 'Calificación del curso';
$string['coursename'] = 'Curso';
$string['coursetimereq'] = 'Minutos requeridos en el curso';
$string['coursetimereq_help'] = 'Introduzca aquí la cantidad mínima de tiempo, en minutos, que un estudiante debe estar conectado al curso antes de poder recibir el certificado.';
$string['credithours'] = 'Horas de crédito';
$string['customtext'] = 'Texto personalizado';
$string['customtext_help'] = 'Si desea que el certificado imprima nombres diferentes para el profesorado asignado con el rol de profesor, no seleccione Imprimir profesor ni ninguna imagen de firma excepto la imagen de la línea. Introduzca los nombres del profesorado en este cuadro de texto tal y como desea que aparezcan. De forma predeterminada, este texto se sitúa en la parte inferior izquierda del certificado. Las siguientes etiquetas HTML están disponibles: &lt;br&gt;, &lt;p&gt;, &lt;b&gt;, &lt;i&gt;, &lt;u&gt;, &lt;img&gt; (src y width (o height) son obligatorios), &lt;a&gt; (href es obligatorio), &lt;font&gt; (los atributos posibles son: colour (código de color hexadecimal), face (arial, times, courier, helvetica, symbol)).';
$string['datefmt'] = 'Formato de fecha';
$string['datefmt_help'] = 'Elija un formato de fecha para imprimir en el certificado. O seleccione la última opción para imprimir la fecha en el formato del idioma elegido por el usuario.';
$string['datehelp'] = 'Fecha';
$string['deletissuediomadcertificates'] = 'Eliminar certificados emitidos';
$string['delivery'] = 'Entrega';
$string['delivery_help'] = 'Elija aquí cómo desea que el alumnado obtenga su certificado.
Abrir en el navegador: abre el certificado en una ventana nueva del navegador.
Forzar descarga: abre la ventana de descarga del navegador.
Enviar certificado por correo: al elegir esta opción se envía el certificado al estudiante como archivo adjunto.
Después de que un usuario reciba su certificado, si vuelve a hacer clic en el enlace del certificado desde la página del curso, verá la fecha en que lo recibió y podrá revisar el certificado recibido.';
$string['designoptions'] = 'Opciones de diseño';
$string['download'] = 'Forzar descarga';
$string['emailiomadcertificate'] = 'Correo electrónico';
$string['emailothers'] = 'Enviar correo a otras personas';
$string['emailothers_help'] = 'Introduzca aquí, separadas por comas, las direcciones de correo de quienes deban recibir un aviso cuando el alumnado reciba un certificado.';
$string['emailstudenttext'] = 'Se adjunta su certificado para {$a->course}.';
$string['emailteachers'] = 'Enviar correo al profesorado';
$string['emailteachers_help'] = 'Si se habilita, el profesorado recibirá un correo electrónico cuando el alumnado obtenga un certificado.';
$string['emailteachermail'] = "{$a->student} ha recibido su certificado: '{$a->certificate}'\npara {$a->course}.\n\nPuede revisar el certificado aquí:\n\n    {$a->url}";
$string['emailteachermailhtml'] = "{$a->student} ha recibido su certificado: '<i>{$a->certificate}</i>'\npara {$a->course}.\n\nPuede revisar el certificado aquí:\n\n    <a href=\"{$a->url}\">Informe de certificados</a>";
$string['entercode'] = 'Introduzca el código del certificado para verificar:';
$string['fontsans'] = 'Familia tipográfica sans-serif';
$string['fontsans_desc'] = 'Familia tipográfica sans-serif para certificados con fuentes incrustadas';
$string['fontserif'] = 'Familia tipográfica serif';
$string['fontserif_desc'] = 'Familia tipográfica serif para certificados con fuentes incrustadas';
$string['getiomadcertificate'] = 'Obtener su certificado';
$string['grade'] = 'Calificación';
$string['gradedate'] = 'Fecha de calificación';
$string['gradefmt'] = 'Formato de la calificación';
$string['gradefmt_help'] = 'Hay tres formatos disponibles si elige imprimir una calificación en el certificado:

Calificación en porcentaje: imprime la calificación como porcentaje.
Calificación en puntos: imprime el valor en puntos de la calificación.
Calificación en letras: imprime la calificación porcentual como una letra.';
$string['gradeletter'] = 'Calificación en letras';
$string['gradepercent'] = 'Calificación en porcentaje';
$string['gradepoints'] = 'Calificación en puntos';
$string['imagetype'] = 'Tipo de imagen';
$string['incompletemessage'] = 'Para descargar su certificado primero debe completar todas las actividades requeridas. Regrese al curso para completar su trabajo.';
$string['intro'] = 'Introducción';
$string['issueoptions'] = 'Opciones de emisión';
$string['issued'] = 'Emitido';
$string['issueddate'] = 'Fecha de emisión';
$string['landscape'] = 'Horizontal';
$string['lastviewed'] = 'Recibió este certificado por última vez el:';
$string['letter'] = 'Carta';
$string['lockingoptions'] = 'Opciones de bloqueo';
$string['modulename'] = 'Certificado de IOMAD';
$string['modulename_help'] = 'Este módulo permite la generación dinámica de certificados basados en las condiciones definidas por el profesorado.';
$string['modulename_link'] = 'Certificate_module';
$string['modulenameplural'] = 'Certificados de IOMAD';
$string['myiomadcertificates'] = 'Mis certificados';
$string['noiomadcertificates'] = 'No hay certificados';
$string['noiomadcertificatesissued'] = 'No se han emitido certificados';
$string['noiomadcertificatesreceived'] = 'no ha recibido ningún certificado del curso.';
$string['nofileselected'] = '¡Debe elegir un archivo para subir!';
$string['nogrades'] = 'No hay calificaciones disponibles';
$string['notapplicable'] = 'N/D';
$string['notfound'] = 'No se pudo validar el número de certificado.';
$string['notissued'] = 'No emitido';
$string['notissuedyet'] = 'Aún no emitido';
$string['notreceived'] = 'No ha recibido este certificado';
$string['openbrowser'] = 'Abrir en una ventana nueva';
$string['opendownload'] = 'Haga clic en el botón siguiente para guardar su certificado en su equipo.';
$string['openemail'] = 'Haga clic en el botón siguiente y su certificado se le enviará como archivo adjunto.';
$string['openwindow'] = 'Haga clic en el botón siguiente para abrir su certificado en una nueva ventana del navegador.';
$string['or'] = 'O';
$string['orientation'] = 'Orientación';
$string['orientation_help'] = 'Elija si desea que la orientación de su certificado sea vertical u horizontal.';
$string['pluginadministration'] = 'Administración de certificados de IOMAD';
$string['pluginname'] = 'Certificado de IOMAD';
$string['portrait'] = 'Vertical';
$string['printdate'] = 'Imprimir fecha';
$string['printdate_help'] = 'Esta es la fecha que se imprimirá en el certificado si se selecciona imprimir fecha. Si se elige la fecha de finalización del curso pero el estudiante no ha completado el curso, se imprimirá la fecha en que se recibió el certificado. También puede elegir imprimir la fecha basada en cuándo se calificó una actividad. Si un certificado se emite antes de que esa actividad se califique, se imprimirá la fecha de recepción.';
$string['printerfriendly'] = 'Página para imprimir';
$string['printhours'] = 'Imprimir horas de crédito';
$string['printhours_help'] = 'Introduzca aquí el número de horas de crédito que se imprimirán en el certificado.';
$string['printgrade'] = 'Imprimir calificación';
$string['printgrade_help'] = 'Puede elegir cualquier elemento de calificación disponible del libro de calificaciones para imprimir la calificación obtenida por el usuario en el certificado. Los elementos de calificación se muestran en el orden en que aparecen en el libro de calificaciones. Elija el formato de la calificación a continuación.';
$string['printnumber'] = 'Imprimir código';
$string['printnumber_help'] = 'Se puede imprimir en el certificado un código único de 10 dígitos compuesto por letras y números aleatorios. Este número puede verificarse comparándolo con el código mostrado en el informe de certificados.';
$string['printoutcome'] = 'Imprimir resultado';
$string['printoutcome_help'] = 'Puede elegir cualquier resultado del curso para imprimir el nombre del resultado y el resultado obtenido por el usuario en el certificado. Un ejemplo podría ser «Resultado de la tarea: Competente».';
$string['printseal'] = 'Imagen de sello o logotipo';
$string['printseal_help'] = 'Esta opción le permite seleccionar un sello o logotipo para imprimir en el certificado desde la carpeta certificate/pix/seals. De forma predeterminada, esta imagen se sitúa en la esquina inferior derecha del certificado.';
$string['printsignature'] = 'Imagen de firma';
$string['printsignature_help'] = 'Esta opción le permite imprimir una imagen de firma de la carpeta certificate/pix/signatures. Puede imprimir una representación gráfica de una firma o una línea para una firma manuscrita. De forma predeterminada, esta imagen se sitúa en la parte inferior izquierda del certificado.';
$string['printteacher'] = 'Imprimir nombre(s) del profesorado';
$string['printteacher_help'] = 'Para imprimir el nombre del profesorado en el certificado, asigne el rol de profesor a nivel del módulo. Hágalo si, por ejemplo, hay más de un profesor en el curso o más de un certificado y desea imprimir nombres diferentes en cada certificado. Haga clic para editar el certificado y luego en la pestaña «Roles asignados localmente». A continuación asigne el rol de profesor (profesor con permiso de edición) al certificado (no es necesario que sean profesores en el curso: puede asignar ese rol a cualquier persona). Esos nombres se imprimirán en el certificado como profesorado.';
$string['printwmark'] = 'Imagen de marca de agua';
$string['printwmark_help'] = 'Puede colocar una imagen de marca de agua en el fondo del certificado. Puede ser un logotipo, sello, emblema, texto o cualquier elemento gráfico que desee usar como fondo.';
$string['receivedcerts'] = 'Certificados recibidos';
$string['receiveddate'] = 'Fecha de recepción';
$string['removecert'] = 'Certificados emitidos eliminados';
$string['report'] = 'Informe';
$string['reportcert'] = 'Informar certificados';
$string['reportcert_help'] = 'Si elige Sí aquí, se mostrarán la fecha de recepción, el número de código y el nombre del curso de este certificado en los informes de certificados del usuario. Si elige imprimir una calificación en este certificado, también se mostrará en el informe.';
$string['requiredtimenotmet'] = 'Debe pasar al menos {$a->requiredtime} minutos en el curso antes de poder acceder a este certificado';
$string['requiredtimenotvalid'] = 'El tiempo requerido debe ser un número válido mayor que 0';
$string['reviewiomadcertificate'] = 'Revisar su certificado';
$string['savecert'] = 'Guardar certificados';
$string['savecert_help'] = 'Si elige esta opción, se guardará una copia del archivo PDF del certificado de cada usuario en el directorio moodledata. En el informe del certificado se mostrará un enlace al certificado guardado de cada usuario.';
$string['seal'] = 'Sello';
$string['sigline'] = 'línea';
$string['signature'] = 'Firma';
$string['statement'] = 'ha completado el curso';
$string['summaryofattempts'] = 'Resumen de certificados recibidos previamente';
$string['textoptions'] = 'Opciones de texto';
$string['title'] = 'CERTIFICADO DE LOGRO';
$string['to'] = 'Concedido a';
$string['typeA4_embedded'] = 'A4 con fuentes incrustadas';
$string['typeA4_non_embedded'] = 'A4 sin fuentes incrustadas';
$string['typeletter_embedded'] = 'Carta con fuentes incrustadas';
$string['typeletter_non_embedded'] = 'Carta sin fuentes incrustadas';
$string['unsupportedfiletype'] = 'El archivo debe ser de tipo jpeg o png';
$string['uploadimage'] = 'Subir imagen';
$string['uploadimagedesc'] = 'Este botón le llevará a una nueva pantalla donde podrá subir imágenes';
$string['userdateformat'] = 'Formato de fecha del idioma del usuario';
$string['validate'] = 'Verificar';
$string['verifyiomadcertificate'] = 'Verificar certificado';
$string['viewiomadcertificateviews'] = 'Ver {$a} certificados emitidos';
$string['viewed'] = 'Recibió este certificado el:';
$string['viewtranscript'] = 'Ver certificados';
$string['watermark'] = 'Marca de agua';
$string['companycertify'] = 'Este Certificado de finalización reconoce que';
$string['companydetails'] = 'ha completado con éxito el programa de formación en línea titulado';
$string['companyscore'] = 'con una puntuación global de {$a}';
$string['companydate'] = 'el {$a}';
$string['companydatecap'] = 'El {$a}';
