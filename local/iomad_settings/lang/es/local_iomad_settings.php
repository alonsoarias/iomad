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
 * Strings for component 'local_iomad_settings', language 'es'
 *
 * @translator Alonso Arias <soporte@orioncloud.com.co>
 */

$string['pluginname'] = 'Configuración de IOMAD';
$string['privacy:metadata'] = 'El plugin de configuración local de IOMAD solo muestra datos almacenados en otras ubicaciones.';
$string['customtext2'] = 'Texto personalizado 2';
$string['customtext3'] = 'Texto personalizado 3';
$string['dateformat'] = 'Formato de fecha';
$string['emaildelay'] = 'Retraso de correo electrónico';
$string['emaildelay_help'] = 'Cualquier correo electrónico de IOMAD tendrá este valor (en segundos) agregado al tiempo de envío de forma predeterminada. Esto permite un retraso predeterminado en el envío, similar al de las publicaciones en foros, de cualquier correo electrónico de IOMAD. Los tiempos aún se verán afectados por la tarea cron de local_mail, pero este retraso será un valor mínimo.';
$string['iomad_autoenrol_managers'] = 'Inscribir administradores como no estudiantes';
$string['iomad_autoenrol_managers_help'] = 'Si esto no está marcado, las cuentas de administrador no se inscribirán como roles de profesor de la empresa en cursos de inscripción manual.';
$string['iomad_autoreallocate_licenses'] = 'Reasignar licencia automáticamente';
$string['iomad_autoreallocate_licenses_help'] = 'Si esto está marcado, cuando se elimine la entrada de curso con licencia de un usuario dentro del informe de usuario, el sistema intentará automáticamente reasignar otra del grupo de licencias de la empresa.';
$string['iomadcertificate_logo'] = 'Logotipo predeterminado para el certificado de empresa de IOMAD';
$string['iomadcertificate_signature'] = 'Firma predeterminada para el certificado de empresa de IOMAD';
$string['iomadcertificate_border'] = 'Borde predeterminado para el certificado de empresa de IOMAD';
$string['iomadcertificate_watermark'] = 'Marca de agua predeterminada para el certificado de empresa de IOMAD';
$string['iomadcertificate_logodesc'] = 'Esta es la imagen de logotipo predeterminada utilizada para el tipo de certificado de empresa de IOMAD. Puede anularla en las páginas de edición de la empresa. La imagen cargada debe tener 80 píxeles de alto y un fondo transparente.';
$string['iomadcertificate_signaturedesc'] = 'Esta es la imagen de firma predeterminada utilizada para el tipo de certificado de empresa de IOMAD. Puede anularla en las páginas de edición de la empresa. La imagen cargada debe ser de 31 píxeles x 150 píxeles y tener un fondo transparente.';
$string['iomadcertificate_borderdesc'] = 'Esta es la imagen de borde predeterminada utilizada para el tipo de certificado de empresa de IOMAD. Puede anularla en las páginas de edición de la empresa. La imagen cargada debe ser de 800 píxeles x 604 píxeles.';
$string['iomadcertificate_watermarkdesc'] = 'Esta es la imagen de marca de agua predeterminada utilizada para el tipo de certificado de empresa de IOMAD. Puede anularla en las páginas de edición de la empresa. La imagen cargada no debe superar los 800 píxeles x 604 píxeles.';
$string['iomad_allow_username'] = 'Puede especificar nombre de usuario';
$string['iomad_allow_username_help'] = 'Seleccionar esto permitirá que se presente el campo de nombre de usuario al crear cuentas. Esto anulará el uso de la dirección de correo electrónico como configuración de nombre de usuario.';
$string['iomad_downloaddetails'] = 'Descargar detalles de actividad en el informe de finalización del curso.';
$string['iomad_downloaddetails_help'] = 'Seleccionar esto descargará todos los detalles de los criterios de finalización del curso para el usuario, así como su estado. Sin esta selección, solo se incluirá su estado.';
$string['iomad_hidevalidcourses'] = 'Mostrar solo los resultados actuales del curso en los informes de forma predeterminada';
$string['iomad_hidevalidcourses_help'] = 'Esto cambia la visualización de los informes de finalización para que solo muestre los resultados actuales del curso (los que aún no han expirado o no tienen fecha de expiración) de forma predeterminada.';
$string['iomad_max_list_classrooms'] = 'Máximo de aulas listadas';
$string['iomad_max_list_classrooms_help'] = 'Esto define el número máximo de aulas mostradas en una página';
$string['iomad_max_list_companies'] = 'Máximo de empresas listadas';
$string['iomad_max_list_companies_help'] = 'Esto define el número máximo de empresas mostradas en una página';
$string['iomad_max_list_competencies'] = 'Máximo de competencias listadas';
$string['iomad_max_list_competencies_help'] = 'Esto define el número máximo de competencias mostradas en una página';
$string['iomad_max_list_courses'] = 'Máximo de cursos listados';
$string['iomad_max_list_courses_help'] = 'Esto define el número máximo de cursos mostrados en una página';
$string['iomad_max_list_email_templates'] = 'Máximo de plantillas de correo electrónico listadas';
$string['iomad_max_list_email_templates_help'] = 'Esto define el número máximo de plantillas de correo electrónico mostradas en una página';
$string['iomad_max_list_frameworks'] = 'Máximo de marcos listados';
$string['iomad_max_list_frameworks_help'] = 'Esto define el número máximo de marcos mostrados en una página';
$string['iomad_max_list_licenses'] = 'Máximo de licencias listadas';
$string['iomad_max_list_licenses_help'] = 'Esto define el número máximo de licencias mostradas en una página';
$string['iomad_max_list_templates'] = 'Máximo de plantillas de planes de aprendizaje listadas';
$string['iomad_max_list_templates_help'] = 'Esto define el número máximo de plantillas de planes de aprendizaje mostradas en una página';
$string['iomad_max_list_users'] = 'Máximo de usuarios listados';
$string['iomad_max_list_users_help'] = 'Esto define el número máximo de usuarios mostrados en una página';
$string['iomad_max_select_courses'] = 'Máximo de cursos listados en el selector';
$string['iomad_max_select_courses_help'] = 'Esto define el número máximo de cursos mostrados en un selector de búsqueda de formulario antes de que se muestre \'demasiados cursos\'';
$string['iomad_max_select_frameworks'] = 'Máximo de marcos listados en el selector';
$string['iomad_max_select_frameworks_help'] = 'Esto define el número máximo de marcos mostrados en un selector de búsqueda de formulario antes de que se muestre \'demasiados marcos\'';
$string['iomad_max_select_templates'] = 'Máximo de plantillas de planes de aprendizaje listadas en el selector';
$string['iomad_max_select_templates_help'] = 'Esto define el número máximo de plantillas de planes de aprendizaje mostradas en un selector de búsqueda de formulario antes de que se muestre \'demasiadas plantillas\'';
$string['iomad_max_select_users'] = 'Máximo de usuarios listados en el selector';
$string['iomad_max_select_users_help'] = 'Esto define el número máximo de usuarios mostrados en un selector de búsqueda de formulario antes de que se muestre \'demasiados usuarios\'';
$string['iomad_report_fields'] = 'Campos de perfil de informe adicionales';
$string['iomad_report_fields_help'] = 'Esta es una lista de campos de perfil separados por comas. Si desea utilizar un campo de perfil opcional, debe usar profile_field_<shortname> donde <shortname> es el nombre corto definido para el campo de perfil. El orden dado es el orden en que se muestran.';
$string['iomad_report_grade_places'] = 'Número de decimales para las calificaciones en los informes';
$string['iomad_report_grade_places_help'] = 'Esto define el número de decimales que se mostrarán en los informes de IOMAD cada vez que se enumere la calificación de un usuario';
$string['iomad_settings:addinstance'] = 'Agregar un nuevo bloque de configuración de IOMAD';
$string['iomad_showcharts'] = 'Mostrar gráficos de finalización del curso de forma predeterminada';
$string['iomad_showcharts_help'] = 'Si se marca, los gráficos se mostrarán primero con una opción para mostrar como texto en su lugar';
$string['iomad_show_company_structure'] = 'Mostrar jerarquía de empresas en el selector';
$string['iomad_show_company_structure_help'] = 'Si se marca, las empresas secundarias aparecerán con sangría bajo la empresa principal en el selector de empresas. Esto puede causar problemas de rendimiento en sitios más grandes.';
$string['iomad_sync_department'] = 'Sincronizar departamento de la empresa con el perfil';
$string['iomad_sync_department_help'] = 'Seleccionar esto mantendrá el campo de perfil del usuario para el departamento sincronizado con el nombre del departamento de la empresa al que está asignado el usuario (Establecer desde el departamento de la empresa), o asignará al usuario a un departamento de la empresa que coincida (Establecer en el departamento de la empresa). Si el usuario está en varios departamentos, esto mostrará \'Múltiple\' en su lugar.';
$string['iomad_sync_institution'] = 'Sincronizar nombre de empresa con el perfil';
$string['iomad_sync_institution_help'] = 'Seleccionar esto mantendrá el campo de perfil de institución del usuario sincronizado con el nombre corto o el nombre de la empresa a la que está asignado el usuario. Si el usuario está en varias empresas, esto mostrará \'Múltiple\' en su lugar.';
$string['iomad_use_email_as_username'] = 'Usar dirección de correo electrónico como nombre de usuario';
$string['iomad_use_email_as_username_help'] = 'Seleccionar esto cambiará la forma en que se crea automáticamente el nombre de usuario de un usuario para una nueva cuenta de usuario en IOMAD, de modo que simplemente use su dirección de correo electrónico';
$string['iomad_useicons'] = 'Usar íconos en el panel de IOMAD';
$string['iomad_useicons_help'] = 'Seleccionar esto cambia los íconos del panel para usar imágenes en lugar de caracteres de Font Awesome.';
$string['iomad_showcompanydropdown'] = 'Mostrar selector de empresa en la barra de navegación';
$string['iomad_showcompanydropdown_help'] = 'Seleccionar esto muestra el selector desplegable de empresa en la barra de navegación cuando el usuario puede acceder a varias empresas. Los usuarios necesitarán que se les proporcione otra forma de acceder al selector de empresa si esto está deshabilitado y no tienen acceso al panel de IOMAD en su empresa actual.';
$string['reset_annually'] = 'Anualmente';
$string['reset_daily'] = 'Diariamente';
$string['reset_never'] = 'Nunca';
$string['reset_sequence'] = 'Restablecer número de secuencia';
$string['serialnumberformat'] = 'Formato de número de serie';
$string['serialnumberformat_help'] = '<p>Los campos de texto personalizado y el formato de número de serie pueden tener las siguientes variables:</p><ul>
                                        <li>{EC} = Código de establecimiento</li>
                                        <li>{CC} = Número de identificación del curso</li>
                                        <li>{CD:DDMMYY} = Fecha (con formato)</li>
                                        <li>{SEQNO:n} = Número de secuencia (con relleno n)</li>
                                        <li>{SN} = Número de serie del certificado (en blanco si se usa en el campo de formato de número de serie))</li>
                                        </ul>';

// SAMPLE Certificate.
$string['sampletitle'] = 'Certificado de capacitación';
$string['samplecertify'] = 'Se certifica que';
$string['samplestatement'] = 'ha completado un curso de aprendizaje en línea sobre';
$string['sampledate'] = 'el';
$string['samplecoursegrade'] = 'con el resultado de';
$string['typesample'] = 'Muestra';
$string['samplecode'] = 'Número de certificado:';
$string['samplesigned'] = 'Firmado:';
$string['sampleonbehalfof'] = 'En nombre de la empresa';
