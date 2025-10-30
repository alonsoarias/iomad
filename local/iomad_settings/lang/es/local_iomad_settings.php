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
 * @package   local_iomad_settings
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Configuración de IOMAD';
$string['privacy:metadata'] = 'El plugin de configuración local de IOMAD solo muestra datos almacenados en otros lugares.';
$string['customtext2'] = 'Texto personalizado 2';
$string['customtext3'] = 'Texto personalizado 3';
$string['dateformat'] = 'Formato de fecha';
$string['emaildelay'] = 'Retraso de correo electrónico';
$string['emaildelay_help'] = 'Cualquier correo de IOMAD tendrá este valor (en segundos) añadido al tiempo de envío de forma predeterminada. Esto permite un retraso predeterminado en el envío, al igual que en los mensajes de los foros, de cualquier correo de IOMAD. Los tiempos seguirán dependiendo de la tarea cron local_mail, pero este retraso será un valor mínimo.';
$string['iomad_autoenrol_managers'] = 'Matricular a los responsables como no estudiantes';
$string['iomad_autoenrol_managers_help'] = 'Si se desmarca, las cuentas de responsables no se matricularán como docentes de la empresa en los cursos de matrícula manual.';
$string['iomad_autoreallocate_licenses'] = 'Reasignar licencias automáticamente';
$string['iomad_autoreallocate_licenses_help'] = 'Si se marca, cuando se elimine la entrada de un curso con licencia de un usuario dentro del informe de usuario, el sistema intentará reasignar otra automáticamente del grupo de licencias de la empresa.';
$string['iomadcertificate_logo'] = 'Logotipo predeterminado para el certificado de empresa de IOMAD';
$string['iomadcertificate_signature'] = 'Firma predeterminada para el certificado de empresa de IOMAD';
$string['iomadcertificate_border'] = 'Marco predeterminado para el certificado de empresa de IOMAD';
$string['iomadcertificate_watermark'] = 'Marca de agua predeterminada para el certificado de empresa de IOMAD';
$string['iomadcertificate_logodesc'] = 'Esta es la imagen de logotipo predeterminada utilizada para el tipo de certificado de empresa de IOMAD. Puede sobrescribirla en las páginas de edición de la empresa. La imagen cargada debe tener 80 píxeles de alto y fondo transparente.';
$string['iomadcertificate_signaturedesc'] = 'Esta es la imagen de firma predeterminada utilizada para el tipo de certificado de empresa de IOMAD. Puede sobrescribirla en las páginas de edición de la empresa. La imagen cargada debe tener 31 píxeles x 150 píxeles y fondo transparente.';
$string['iomadcertificate_borderdesc'] = 'Esta es la imagen de marco predeterminada utilizada para el tipo de certificado de empresa de IOMAD. Puede sobrescribirla en las páginas de edición de la empresa. La imagen cargada debe ser de 800 píxeles x 604 píxeles.';
$string['iomadcertificate_watermarkdesc'] = 'Esta es la imagen de marca de agua predeterminada utilizada para el tipo de certificado de empresa de IOMAD. Puede sobrescribirla en las páginas de edición de la empresa. La imagen cargada no debe superar los 800 píxeles x 604 píxeles.';
$string['iomad_allow_username'] = 'Permitir especificar nombre de usuario';
$string['iomad_allow_username_help'] = 'Al seleccionarlo se permitirá mostrar el campo de nombre de usuario al crear cuentas. Esto anulará el ajuste de usar la dirección de correo como nombre de usuario.';
$string['iomad_downloaddetails'] = 'Descargar detalles de actividad en el informe de finalización del curso.';
$string['iomad_downloaddetails_help'] = 'Al seleccionarlo se descargarán todos los detalles de los criterios de finalización del curso del usuario, así como su estado. Sin esta opción solo se incluirá su estado.';
$string['iomad_hidevalidcourses'] = 'Mostrar solo los resultados de cursos vigentes en los informes de forma predeterminada';
$string['iomad_hidevalidcourses_help'] = 'Esto cambia la visualización de los informes de finalización para que solo muestren de forma predeterminada los resultados de cursos vigentes (los que no han caducado o no tienen caducidad).';
$string['iomad_max_list_classrooms'] = 'Máximo de aulas listadas';
$string['iomad_max_list_classrooms_help'] = 'Define el número máximo de aulas que se muestran en una página';
$string['iomad_max_list_companies'] = 'Máximo de empresas listadas';
$string['iomad_max_list_companies_help'] = 'Define el número máximo de empresas que se muestran en una página';
$string['iomad_max_list_competencies'] = 'Máximo de competencias listadas';
$string['iomad_max_list_competencies_help'] = 'Define el número máximo de competencias que se muestran en una página';
$string['iomad_max_list_courses'] = 'Máximo de cursos listados';
$string['iomad_max_list_courses_help'] = 'Define el número máximo de cursos que se muestran en una página';
$string['iomad_max_list_email_templates'] = 'Máximo de plantillas de correo listadas';
$string['iomad_max_list_email_templates_help'] = 'Define el número máximo de plantillas de correo que se muestran en una página';
$string['iomad_max_list_frameworks'] = 'Máximo de marcos listados';
$string['iomad_max_list_frameworks_help'] = 'Define el número máximo de marcos que se muestran en una página';
$string['iomad_max_list_licenses'] = 'Máximo de licencias listadas';
$string['iomad_max_list_licenses_help'] = 'Define el número máximo de licencias que se muestran en una página';
$string['iomad_max_list_templates'] = 'Máximo de plantillas de planes de aprendizaje listadas';
$string['iomad_max_list_templates_help'] = 'Define el número máximo de plantillas de planes de aprendizaje que se muestran en una página';
$string['iomad_max_list_users'] = 'Máximo de usuarios listados';
$string['iomad_max_list_users_help'] = 'Define el número máximo de usuarios que se muestran en una página';
$string['iomad_max_select_courses'] = 'Máximo de cursos listados en el selector';
$string['iomad_max_select_courses_help'] = 'Define el número máximo de cursos que se muestran en un selector de búsqueda de formularios antes de que se indique «demasiados cursos»';
$string['iomad_max_select_frameworks'] = 'Máximo de marcos listados en el selector';
$string['iomad_max_select_frameworks_help'] = 'Define el número máximo de marcos que se muestran en un selector de búsqueda de formularios antes de que se indique «demasiados marcos»';
$string['iomad_max_select_templates'] = 'Máximo de plantillas de planes de aprendizaje en el selector';
$string['iomad_max_select_templates_help'] = 'Define el número máximo de plantillas de planes de aprendizaje que se muestran en un selector de búsqueda de formularios antes de que se indique «demasiadas plantillas»';
$string['iomad_max_select_users'] = 'Máximo de usuarios listados en el selector';
$string['iomad_max_select_users_help'] = 'Define el número máximo de usuarios que se muestran en un selector de búsqueda de formularios antes de que se indique «demasiados usuarios»';
$string['iomad_report_fields'] = 'Campos de perfil adicionales para informes';
$string['iomad_report_fields_help'] = 'Esta es una lista de campos de perfil separados por comas. Si desea utilizar un campo de perfil opcional, debe usar profile_field_<shortname> donde <shortname> es el nombre corto definido para el campo de perfil. El orden indicado es el orden en que se muestran.';
$string['iomad_report_grade_places'] = 'Número de decimales para las calificaciones en los informes';
$string['iomad_report_grade_places_help'] = 'Define el número de decimales que se mostrarán en los informes de IOMAD siempre que se liste la calificación de un usuario';
$string['iomad_settings:addinstance'] = 'Añadir un nuevo bloque de configuración de IOMAD';
$string['iomad_showcharts'] = 'Mostrar gráficos de finalización de cursos por defecto';
$string['iomad_showcharts_help'] = 'Si se marca, primero se mostrarán los gráficos con la opción de verlos como texto en su lugar';
$string['iomad_show_company_structure'] = 'Mostrar jerarquía de la empresa en el selector';
$string['iomad_show_company_structure_help'] = 'Si se marca, las empresas secundarias aparecerán sangradas debajo de la empresa principal en el selector de empresas. Esto puede causar problemas de rendimiento en sitios grandes.';
$string['iomad_sync_department'] = 'Sincronizar el departamento de la empresa con el perfil';
$string['iomad_sync_department_help'] = 'Al seleccionarlo, se mantendrá sincronizado el campo de perfil de departamento del usuario con el nombre del departamento de la empresa al que se le ha asignado (Establecer desde el departamento de la empresa), o se asignará al usuario a un departamento de empresa que coincida (Establecer en el departamento de la empresa). Si el usuario está en varios departamentos, se mostrará «Múltiple».';
$string['iomad_sync_institution'] = 'Sincronizar el nombre de la empresa con el perfil';
$string['iomad_sync_institution_help'] = 'Al seleccionarlo, el campo de perfil de institución del usuario se mantendrá sincronizado con el nombre corto o el nombre de la empresa a la que se le ha asignado. Si el usuario pertenece a varias empresas, se mostrará «Múltiple».';
$string['iomad_use_email_as_username'] = 'Usar la dirección de correo como nombre de usuario';
$string['iomad_use_email_as_username_help'] = 'Al seleccionarlo se cambiará la forma en que se crea automáticamente el nombre de usuario para una nueva cuenta en IOMAD para que utilice únicamente la dirección de correo.';
$string['iomad_useicons'] = 'Usar iconos en el panel de IOMAD';
$string['iomad_useicons_help'] = 'Al seleccionarlo se cambian los iconos del panel para utilizar imágenes en lugar de caracteres de Font Awesome.';
$string['iomad_showcompanydropdown'] = 'Mostrar el selector de empresa en la barra de navegación';
$string['iomad_showcompanydropdown_help'] = 'Al seleccionarlo se mostrará el selector desplegable de empresa en la barra de navegación cuando el usuario pueda acceder a varias empresas. Si se desactiva y el usuario no tiene acceso al panel de IOMAD en su empresa actual, se le deberá proporcionar otra forma de acceder al selector de empresa.';
$string['reset_annually'] = 'Anualmente';
$string['reset_daily'] = 'Diariamente';
$string['reset_never'] = 'Nunca';
$string['reset_sequence'] = 'Restablecer número de secuencia';
$string['serialnumberformat'] = 'Formato del número de serie';
$string['serialnumberformat_help'] = '<p>Los campos de texto personalizado y el formato del número de serie pueden utilizar las siguientes variables:</p><ul>
                                        <li>{EC} = Código del establecimiento</li>
                                        <li>{CC} = ID del curso</li>
                                        <li>{CD:DDMMYY} = Fecha (con formato)</li>
                                        <li>{SEQNO:n} = Número de secuencia (con relleno n)</li>
                                        <li>{SN} = Número de serie del certificado (en blanco si se usa en el campo de formato de número de serie)</li>
                                        </ul>';
$string['sampletitle'] = 'Certificado de formación';
$string['samplecertify'] = 'Se certifica que';
$string['samplestatement'] = 'ha completado un curso en línea sobre';
$string['sampledate'] = 'el';
$string['samplecoursegrade'] = 'con el resultado de';
$string['typesample'] = 'Ejemplo';
$string['samplecode'] = 'Número de certificado:';
$string['samplesigned'] = 'Firmado: ';
$string['sampleonbehalfof'] = 'En nombre de la empresa';
