<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Las cadenas de complementos se definen aquí.
 *
 * @package     report_usage_monitor
 * @category    string
 * @copyright   2025 Soporte IngeWeb <soporte@ingeweb.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Plugin general strings
$string['pluginname'] = 'Usage Report';
$string['reportinfotext'] = 'Este plugin ha sido creado para otro caso de éxito de <strong>IngeWeb</strong>. Visítenos en <a target="_blank" href="http://ingeweb.co/">IngeWeb - Soluciones para triunfar en Internet</a>.';
$string['exclusivedisclaimer'] = 'Este plugin hace parte y es de uso exclusivo del servicio de hosting para Moodle proporcionado por <a target="_blank" href="http://ingeweb.co/">IngeWeb</a>.';

// Dashboard strings
$string['dashboard'] = 'Panel de Control';
$string['dashboard_title'] = 'Panel de Control de Uso';
$string['diskusage'] = 'Uso del disco';
$string['users_today_card'] = 'Usuarios Diarios Hoy';
$string['max_userdaily_for_90_days'] = 'Máximo de usuarios diarios en los últimos 90 días';
$string['notcalculatedyet'] = 'Aún no calculado';
$string['lastexecution'] = 'Última ejecución de cálculo de usuarios diarios: {$a}';
$string['lastexecutioncalculate'] = 'Último cálculo de espacio en disco: {$a}';
$string['users_today'] = 'Cantidad de usuarios diarios el día de hoy: {$a}';
$string['date'] = 'Fecha';
$string['last_calculation'] = 'Último cálculo';
$string['usersquantity'] = 'Cantidad de usuarios diarios';
$string['disk_usage_distribution'] = 'Distribución de Uso de Disco';
$string['disk_usage_history'] = 'Historial de Uso de Disco (Últimos 30 Días)';
$string['percentage_used'] = 'Porcentaje Utilizado';

// Dashboard sections
$string['disk_usage_by_directory'] = 'Uso de Disco por Directorio';
$string['largest_courses'] = 'Cursos más Grandes';
$string['database'] = 'Base de datos';
$string['files_dir'] = 'Archivos (filedir)';
$string['cache'] = 'Caché';
$string['others'] = 'Otros';
$string['directory'] = 'Directorio';
$string['size'] = 'Tamaño';
$string['percentage'] = 'Porcentaje';
$string['course'] = 'Curso';
$string['backup_count'] = 'Número de Copias';
$string['topuser'] = 'Top 10 usuarios diarios';
$string['lastusers'] = 'Usuarios diarios de los últimos 10 días';
$string['usertable'] = 'Tabla de top usuarios';
$string['userchart'] = 'Graficar top usuarios';
$string['system_info'] = 'Información del Sistema';
$string['moodle_version'] = 'Versión de Moodle';
$string['total_courses'] = 'Total de Cursos';
$string['backup_per_course'] = 'Copias de Seguridad por Curso';
$string['registered_users'] = 'Usuarios Registrados';
$string['active_users'] = 'usuarios activos';
$string['suspended_users'] = 'usuarios suspendidos';
$string['recommendations'] = 'Recomendaciones';

// Warning levels and indicator labels
$string['warning70'] = 'Advertencia (70%)';
$string['critical90'] = 'Crítico (90%)';
$string['limit100'] = 'Límite (100%)';
$string['percent_of_threshold'] = '% del umbral';

// Notification status section
$string['notification_status'] = 'Estado de Notificaciones';
$string['last_disk_notification'] = 'Última notificación de disco';
$string['last_user_notification'] = 'Última notificación de usuarios';
$string['next_possible_notification'] = 'Próxima posible en';
$string['no_notification_sent'] = 'Sin notificaciones enviadas';
$string['notifications_disabled'] = 'Notificaciones deshabilitadas';
$string['email_not_configured'] = 'Email no configurado';

// Task status section
$string['task_status'] = 'Estado de Tareas Programadas';
$string['task_name'] = 'Tarea';
$string['last_run'] = 'Última ejecución';
$string['next_run'] = 'Próxima ejecución';
$string['task_never_run'] = 'Nunca ejecutada';
$string['task_disabled'] = 'Deshabilitada';

// Active alerts section
$string['active_alerts'] = 'Alertas Activas';
$string['alert_disk_critical'] = 'Espacio en disco crítico ({$a}%)';
$string['alert_disk_warning'] = 'Advertencia de espacio en disco ({$a}%)';
$string['alert_users_critical'] = 'Usuarios diarios en nivel crítico ({$a}%)';
$string['alert_users_warning'] = 'Advertencia de usuarios diarios ({$a}%)';
$string['no_active_alerts'] = 'Sin alertas activas - Todos los sistemas normales';

// Quick actions
$string['quick_actions'] = 'Acciones Rápidas';
$string['go_to_settings'] = 'Configuración del Plugin';
$string['purge_cache'] = 'Purgar Caché';
$string['run_tasks'] = 'Tareas Programadas';
$string['file_cleanup'] = 'Limpieza de Archivos';

// Show more courses
$string['show_more_courses'] = 'Mostrar más cursos';
$string['install_coursesize'] = 'Instale el plugin report_coursesize para un análisis detallado del tamaño de cursos';
$string['coursesize_plugin_url'] = 'https://moodle.org/plugins/report_coursesize';

// Course access and completion sections
$string['course_access_trends'] = 'Tendencias de Acceso a Cursos';
$string['most_accessed_courses'] = 'Cursos más Accedidos';
$string['course_completion_trends'] = 'Tendencias de Finalización de Cursos';
$string['total_accesses'] = 'Total de Accesos';
$string['unique_users'] = 'Usuarios Únicos';
$string['unique_courses'] = 'Cursos Accedidos';
$string['completions'] = 'Finalizaciones';
$string['avg_per_day'] = 'Prom. por Día';
$string['last_30_days'] = 'Últimos 30 Días';
$string['no_data_available'] = 'Sin datos disponibles';
$string['access_summary'] = 'Resumen de Accesos';
$string['completion_summary'] = 'Resumen de Finalizaciones';
$string['users_completed'] = 'Usuarios con Finalizaciones';
$string['courses_with_completions'] = 'Cursos con Finalizaciones';
$string['total_completions'] = 'Total de Finalizaciones';
$string['access_chart'] = 'Gráfico de Accesos';
$string['completion_chart'] = 'Gráfico de Finalizaciones';
$string['access_table'] = 'Tabla de Accesos';
$string['completion_table'] = 'Tabla de Finalizaciones';

// Tooltips for metrics explanation
$string['tooltip_total_accesses'] = 'Número total de veces que se accedió a los cursos en los últimos 30 días. Incluye múltiples visitas del mismo usuario.';
$string['tooltip_unique_users'] = 'Número de usuarios diferentes que accedieron al menos a un curso en los últimos 30 días.';
$string['tooltip_unique_courses'] = 'Número de cursos diferentes que fueron accedidos al menos una vez en los últimos 30 días. Este NO es el total de cursos en la plataforma.';
$string['tooltip_total_completions'] = 'Número total de finalizaciones de cursos registradas en los últimos 30 días.';
$string['tooltip_users_completed'] = 'Número de usuarios diferentes que completaron al menos un curso en los últimos 30 días.';
$string['tooltip_courses_with_completions'] = 'Número de cursos diferentes que tuvieron al menos una finalización en los últimos 30 días.';
$string['tooltip_avg_per_day'] = 'Promedio de accesos o finalizaciones por día durante los últimos 30 días.';
$string['tooltip_disk_usage'] = 'Espacio en disco actualmente utilizado comparado con su cuota asignada.';
$string['tooltip_users_today'] = 'Número de usuarios únicos que iniciaron sesión hoy comparado con su límite diario de usuarios.';
$string['tooltip_max_90_days'] = 'El número más alto de inicios de sesión únicos diarios registrados en los últimos 90 días.';

// Recommendation tips
$string['space_saving_tips'] = 'Consejos para ahorrar espacio en disco:';
$string['tip_backups'] = 'Reducir el número de copias de seguridad automáticas por curso (actualmente: {$a})';
$string['tip_files'] = 'Limpiar archivos antiguos sin uso mediante la herramienta de limpieza de archivos';
$string['tip_courses'] = 'Archivar o eliminar cursos antiguos que ya no se utilizan';
$string['tip_cache'] = 'Purgar la caché del sistema para liberar espacio temporal';
$string['disk_usage_ok'] = 'El uso del disco está en un nivel aceptable. No se requiere acción inmediata.';
$string['user_count_ok'] = 'El recuento de usuarios está en un nivel aceptable. No se requiere acción inmediata.';
$string['user_limit_tips'] = 'Consejos para gestionar el límite de usuarios:';
$string['tip_user_inactive'] = 'Considere limpiar las cuentas de usuario inactivas que no han iniciado sesión durante mucho tiempo.';
$string['tip_user_limit'] = 'Si el número de usuarios se acerca constantemente al límite, considere aumentar su cuota.';

// Task strings
$string['calculatediskusagetask'] = 'Tarea para calcular el uso del disco';
$string['getlastusers'] = 'Tarea para calcular el top de accesos unicos';
$string['getlastusers90days'] = 'Tarea para obtener el top de usuarios en los últimos 90 días';
$string['getlastusersconnected'] = 'Tarea para calcular la cantidad de usuarios diarios de hoy';
$string['processdisknotificationtask'] = 'Tarea de notificación del uso del disco';
$string['processuserlimitnotificationtask'] = 'Tarea de notificación del límite de usuarios diarios';

// Settings strings
$string['mainsettings'] = 'Configuraciones principales';
$string['email'] = 'Email para notificaciones';
$string['configemail'] = 'Dirección de correo donde desea enviar las notificaciones.';
$string['max_daily_users_threshold'] = 'Límite de usuarios';
$string['configmax_daily_users_threshold'] = 'Establezca el límite de usuarios.';
$string['disk_quota'] = 'Cuota de disco';
$string['configdisk_quota'] = 'Cuota de disco en gigabytes';
$string['notificationsettings'] = 'Configuración de notificaciones';
$string['notificationsettingsinfo'] = 'Configure cuándo y cómo se envían las notificaciones.';
$string['disk_warning_level'] = 'Nivel de advertencia de disco';
$string['configdisk_warning_level'] = 'Porcentaje de uso de disco que activa las advertencias.';
$string['users_warning_level'] = 'Nivel de advertencia de usuarios';
$string['configusers_warning_level'] = 'Porcentaje del límite de usuarios que activa las advertencias.';
$string['pathtodu'] = 'Ruta al comando du';
$string['configpathtodu'] = 'Configura la ruta al comando du (uso de disco). Esto es necesario para calcular el uso de disco. <strong>Este ajuste se refleja en las rutas del sistema de Moodle</strong>)';
$string['pathtodurecommendation'] = 'Recomendamos que revise y configure la ruta a \'du\' en las Rutas del sistema de Moodle. Puede encontrar esta configuración en Administración del sitio > Servidor > Rutas del sistema. <a target="_blank" href="settings.php?section=systempaths#id_s__pathtodu">Haga clic aquí para ir a Rutas del sistema</a>.';
$string['pathtodunote'] = 'Nota: El path a \'du\' se detectará automáticamente solo si este plugin se encuentra en un sistema Linux y si se logra detectar la ubicación de \'du\'.';
$string['activateshellexec'] = 'La función shell_exec no está activa en este servidor. Para utilizar la detección automática del camino a du, debes habilitar shell_exec en la configuración de tu servidor.';
$string['enable_api'] = 'Habilitar API';
$string['configenable_api'] = 'Habilitar acceso API para que sistemas externos obtengan información de uso.';

// Email notification strings
$string['subjectemail1'] = 'Límite de usuarios diarios superado plataforma:';
$string['subjectemail2'] = 'Alerta de espacio en disco plataforma:';

// API strings
$string['get_usage_data'] = 'Obtener datos de uso';
$string['get_usage_data_desc'] = 'Recupera datos precalculados de uso de disco y usuarios con mínima sobrecarga.';
$string['set_usage_thresholds'] = 'Configurar umbrales de uso';
$string['set_usage_thresholds_desc'] = 'Actualiza los umbrales configurados para usuarios y espacio en disco.';
$string['user_threshold_updated'] = 'Umbral de usuarios actualizado correctamente.';
$string['disk_threshold_updated'] = 'Umbral de disco actualizado correctamente.';
$string['error_user_threshold_negative'] = 'El umbral de usuarios debe ser mayor que 0.';
$string['error_disk_threshold_negative'] = 'El umbral de disco debe ser mayor que 0.';
$string['error_no_thresholds_provided'] = 'No se proporcionaron umbrales para actualizar.';

// API response field descriptions
$string['site_name'] = 'Nombre del sitio';
$string['site_shortname'] = 'Nombre corto del sitio';
$string['moodle_release'] = 'Versión de Moodle legible';
$string['course_count'] = 'Número de cursos';
$string['user_count'] = 'Número de usuarios';
$string['backup_auto_max_kept'] = 'Número de copias automáticas conservadas';
$string['total_bytes'] = 'Uso total de disco en bytes';
$string['total_readable'] = 'Uso de disco legible';
$string['quota_bytes'] = 'Cuota de disco en bytes';
$string['quota_readable'] = 'Cuota de disco legible';
$string['disk_percentage'] = 'Porcentaje de uso de disco';
$string['database_bytes'] = 'Tamaño de la base de datos en bytes';
$string['database_readable'] = 'Tamaño legible de la base de datos';
$string['database_percentage'] = 'Porcentaje de tamaño de la base de datos';
$string['filedir_bytes'] = 'Tamaño del directorio de archivos en bytes';
$string['filedir_readable'] = 'Tamaño legible del directorio de archivos';
$string['filedir_percentage'] = 'Porcentaje de tamaño del directorio de archivos';
$string['cache_bytes'] = 'Tamaño de caché en bytes';
$string['cache_readable'] = 'Tamaño legible de caché';
$string['cache_percentage'] = 'Porcentaje de caché';
$string['backup_bytes'] = 'Tamaño de copia de seguridad en bytes';
$string['backup_readable'] = 'Tamaño legible de copia de seguridad';
$string['backup_percentage'] = 'Porcentaje de copia de seguridad';
$string['others_bytes'] = 'Tamaño de otros directorios en bytes';
$string['others_readable'] = 'Tamaño legible de otros directorios';
$string['others_percentage'] = 'Porcentaje de otros directorios';
$string['user_threshold'] = 'Umbral de usuarios';
$string['user_percentage'] = 'Porcentaje de uso de usuarios';
$string['course_id'] = 'ID del curso';
$string['course_fullname'] = 'Nombre completo del curso';
$string['course_shortname'] = 'Nombre corto del curso';
$string['course_size_bytes'] = 'Tamaño del curso en bytes';
$string['course_size_readable'] = 'Tamaño legible del curso';
$string['course_backup_size_bytes'] = 'Tamaño de la copia de seguridad del curso en bytes';
$string['course_backup_size_readable'] = 'Tamaño legible de la copia de seguridad del curso';
$string['course_percentage'] = 'Porcentaje de tamaño del curso';
$string['course_backup_count'] = 'Número de copias de seguridad del curso';
$string['disk_calculation_timestamp'] = 'Timestamp del cálculo de disco';
$string['users_calculation_timestamp'] = 'Timestamp del cálculo de usuarios';

// Notification history API strings
$string['notification_type'] = 'Tipo de notificación (disk, users, o all)';
$string['notification_limit'] = 'Número máximo de registros a devolver';
$string['notification_offset'] = 'Desplazamiento para paginación';
$string['notification_total'] = 'Número total de registros disponibles';
$string['notification_limit_value'] = 'Número máximo de registros solicitados';
$string['notification_offset_value'] = 'Desplazamiento solicitado';
$string['notification_id'] = 'ID de la notificación';
$string['notification_type_value'] = 'Tipo de notificación (disk o users)';
$string['notification_percentage'] = 'Porcentaje de uso';
$string['notification_value'] = 'Valor legible';
$string['notification_value_raw'] = 'Valor en bytes o número de usuarios';
$string['notification_threshold'] = 'Umbral legible';
$string['notification_threshold_raw'] = 'Umbral en bytes o número de usuarios';
$string['notification_timecreated'] = 'Timestamp de creación';
$string['notification_timereadable'] = 'Fecha y hora legibles';

// Projections and growth rates
$string['api_projections_title'] = 'Proyecciones de crecimiento';
$string['api_projections_desc'] = 'Datos de proyección de crecimiento y días estimados para alcanzar umbrales';
$string['api_monthly_growth_rate'] = 'Tasa de crecimiento mensual';
$string['api_projection_days'] = 'Días para alcanzar umbral';
$string['growth_rate_disk'] = 'Tasa de crecimiento de disco';
$string['growth_rate_disk_desc'] = 'Tasa de crecimiento mensual del uso de disco en porcentaje';
$string['growth_rate_users'] = 'Tasa de crecimiento de usuarios';
$string['growth_rate_users_desc'] = 'Tasa de crecimiento mensual del número de usuarios en porcentaje';
$string['days_to_threshold_disk'] = 'Días hasta umbral de disco';
$string['days_to_threshold_disk_desc'] = 'Días proyectados hasta alcanzar el umbral de advertencia de disco';
$string['days_to_threshold_users'] = 'Días hasta umbral de usuarios';
$string['days_to_threshold_users_desc'] = 'Días proyectados hasta alcanzar el umbral de advertencia de usuarios';

// Unified email template strings.
$string['email_notification_title'] = 'Alerta de Usage Monitor';
$string['email_notification_subject'] = 'Alerta de uso en plataforma';
$string['email_users_alert'] = 'Usuarios';
$string['email_disk_alert'] = 'Disco';
$string['email_thresholds_exceeded'] = 'Se han superado uno o más umbrales de uso en su plataforma. Por favor revise los detalles a continuación.';
$string['email_userlimit_section'] = 'Límite de usuarios diarios superado';
$string['email_diskusage_section'] = 'Alerta de uso de espacio en disco';
$string['email_rec_users'] = 'Revisar usuarios';
$string['email_rec_users_desc'] = 'Considere limpiar las cuentas de usuario inactivas que no han iniciado sesión durante mucho tiempo';
$string['email_rec_quota'] = 'Aumentar cuota';
$string['email_rec_quota_desc'] = 'Si el número de usuarios se acerca constantemente al límite, considere aumentar su cuota';
$string['email_general_disclaimer'] = 'Esta es una notificación automática. Para asistencia técnica, por favor contacte a su administrador de hosting.';

// Email template strings for Mustache templates.
// User limit email strings.
$string['email_userlimit_title'] = 'Alerta de límite de usuarios diarios';
$string['email_of_limit'] = 'del límite alcanzado';
$string['email_attention_required'] = 'Atención requerida';
$string['email_userlimit_urgency'] = 'La plataforma ha superado el umbral de usuarios diarios. Se recomienda revisión inmediata para';
$string['email_alert_summary'] = 'Resumen de la alerta';
$string['email_report_date'] = 'Fecha del reporte';
$string['email_active_users_today'] = 'Usuarios activos hoy';
$string['email_configured_limit'] = 'Límite configurado';
$string['email_users_over_limit'] = 'Usuarios en exceso';
$string['email_users'] = 'usuarios';
$string['email_growth_projection'] = 'Proyección de crecimiento';
$string['email_projection_text'] = 'De mantenerse la tendencia actual, se estima que en';
$string['email_days'] = 'días';
$string['email_projection_will_reach'] = 'la plataforma alcanzará el';
$string['email_platform_info'] = 'Información de la plataforma';
$string['email_recent_history'] = 'Historial reciente de usuarios (últimos 7 días)';
$string['email_active_users'] = 'Usuarios activos';
$string['email_percent_of_limit'] = '% del límite';
$string['email_view_dashboard'] = 'Ver panel de control';
$string['email_auto_generated'] = 'Este mensaje fue generado automáticamente por';
$string['email_from'] = 'de';
$string['email_users_disclaimer'] = 'Se contabilizan usuarios distintos que se autenticaron en la fecha indicada. Usuarios que se autentican más de una vez solo cuentan una vez.';

// Disk usage email strings.
$string['email_diskusage_title'] = 'Alerta de espacio en disco';
$string['email_of_quota_used'] = 'de la cuota utilizada';
$string['email_disk_warning'] = 'Advertencia de espacio en disco';
$string['email_diskusage_urgency'] = 'ha superado el {$a}% del espacio en disco asignado. Revise las recomendaciones a continuación para la plataforma';
$string['email_disk_summary'] = 'Resumen de uso de disco';
$string['email_used_space'] = 'Espacio utilizado';
$string['email_assigned_quota'] = 'Cuota asignada';
$string['email_available_space'] = 'Espacio disponible';
$string['email_space_distribution'] = 'Distribución del espacio por categoría';
$string['email_percent_of_total'] = '% del total';
$string['email_recommendations_title'] = 'Recomendaciones para liberar espacio';
$string['email_rec_backups'] = 'Reducir copias';
$string['email_rec_backups_desc'] = 'Disminuir el número de copias automáticas por curso (actualmente: {$a})';
$string['email_rec_files'] = 'Limpiar archivos';
$string['email_rec_files_desc'] = 'Eliminar archivos antiguos sin uso mediante la herramienta de limpieza';
$string['email_rec_courses'] = 'Revisar cursos';
$string['email_rec_courses_desc'] = 'Archivar o limpiar los cursos más grandes listados arriba';
$string['email_rec_cache'] = 'Purgar caché';
$string['email_rec_cache_desc'] = 'Limpiar la caché del sistema para liberar espacio temporal';
$string['email_disk_disclaimer'] = 'Si necesita asistencia técnica, por favor no responda a este correo y contacte a su administrador de hosting.';

// Legacy email templates (kept for backwards compatibility).
$string['messagehtml_userlimit'] = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerta de Límite de Usuarios - {$a->sitename}</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f4f4f4;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 20px 10px;">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%); padding: 30px 20px; text-align: center;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="text-align: center;">
                                        <span style="font-size: 40px;">&#9888;</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: #ffffff; font-size: 24px; font-weight: bold; padding-top: 10px;">
                                        Alerta de Límite de Usuarios
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-top: 15px;">
                                        <span style="display: inline-block; background-color: #ffffff; color: #c0392b; padding: 8px 20px; border-radius: 25px; font-weight: bold; font-size: 18px;">
                                            {$a->percentaje}% del límite alcanzado
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Urgency Banner -->
                    <tr>
                        <td style="background-color: #fff3cd; padding: 15px 20px; border-left: 4px solid #ffc107;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="font-size: 14px; color: #856404;">
                                        <strong>&#9889; ATENCIÓN REQUERIDA:</strong> La plataforma <strong>{$a->sitename}</strong> ha superado el umbral de usuarios diarios. Se recomienda revisar inmediatamente.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 25px 30px;">
                            <!-- Alert Summary -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="font-size: 18px; font-weight: bold; color: #2c3e50; padding-bottom: 15px; border-bottom: 2px solid #3498db;">
                                        &#128202; Resumen de la Alerta
                                    </td>
                                </tr>
                            </table>

                            <!-- Progress Bar -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 20px;">
                                <tr>
                                    <td style="background-color: #ecf0f1; border-radius: 10px; padding: 3px;">
                                        <table role="presentation" width="{$a->percentaje}%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="background: linear-gradient(90deg, #e74c3c, #c0392b); color: #ffffff; text-align: center; padding: 10px 0; border-radius: 8px; font-weight: bold; font-size: 14px;">
                                                    {$a->percentaje}%
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Key Metrics Table -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
                                <tr style="background-color: #f8f9fa;">
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555; width: 50%; border-bottom: 1px solid #e0e0e0;">Fecha del Reporte:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #2c3e50; border-bottom: 1px solid #e0e0e0;">{$a->lastday}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555; border-bottom: 1px solid #e0e0e0;">Usuarios Activos Hoy:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #e74c3c; font-size: 16px; border-bottom: 1px solid #e0e0e0;">{$a->numberofusers} usuarios</td>
                                </tr>
                                <tr style="background-color: #f8f9fa;">
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555; border-bottom: 1px solid #e0e0e0;">Límite Configurado:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #2c3e50; border-bottom: 1px solid #e0e0e0;">{$a->threshold} usuarios</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555;">Usuarios en Exceso:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #c0392b;">{$a->excess_users} usuarios</td>
                                </tr>
                            </table>

                            <!-- Projection Box -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px;">
                                <tr>
                                    <td style="padding: 20px; color: #ffffff;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="font-size: 16px; font-weight: bold; padding-bottom: 10px;">
                                                    &#128200; Proyección de Crecimiento
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 14px; line-height: 1.5;">
                                                    De mantenerse la tendencia actual, se estima que en <strong>{$a->days_to_critical} días</strong> la plataforma alcanzará el <strong>{$a->critical_threshold}%</strong> del límite configurado. Considere aumentar su cuota de usuarios.
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Platform Information -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="font-size: 18px; font-weight: bold; color: #2c3e50; padding-bottom: 15px; border-bottom: 2px solid #3498db;">
                                        &#128421; Información de la Plataforma
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
                                <tr style="background-color: #f8f9fa;">
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555; width: 50%; border-bottom: 1px solid #e0e0e0;">Versión de Moodle:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #2c3e50; border-bottom: 1px solid #e0e0e0;">{$a->moodle_release}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555; border-bottom: 1px solid #e0e0e0;">Total de Cursos:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #2c3e50; border-bottom: 1px solid #e0e0e0;">{$a->courses_count}</td>
                                </tr>
                                <tr style="background-color: #f8f9fa;">
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555; border-bottom: 1px solid #e0e0e0;">Copias por Curso:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #2c3e50; border-bottom: 1px solid #e0e0e0;">{$a->backup_auto_max_kept}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555;">Uso de Disco:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #2c3e50;">{$a->diskusage} / {$a->quotadisk} ({$a->disk_percent}%)</td>
                                </tr>
                            </table>

                            <!-- Historical Data -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="font-size: 18px; font-weight: bold; color: #2c3e50; padding-bottom: 15px; border-bottom: 2px solid #3498db;">
                                        &#128197; Historial Reciente (Últimos 7 Días)
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
                                <tr style="background-color: #34495e;">
                                    <th style="padding: 12px 15px; font-weight: 600; color: #ffffff; text-align: left;">Fecha</th>
                                    <th style="padding: 12px 15px; font-weight: 600; color: #ffffff; text-align: left;">Usuarios Activos</th>
                                    <th style="padding: 12px 15px; font-weight: 600; color: #ffffff; text-align: left;">% del Límite</th>
                                </tr>
                                {$a->historical_data_rows}
                            </table>

                            <!-- CTA Button -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top: 30px;">
                                <tr>
                                    <td align="center">
                                        <a href="{$a->referer}" style="display: inline-block; background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: #ffffff; padding: 14px 35px; border-radius: 25px; text-decoration: none; font-weight: bold; font-size: 16px;">
                                            &#128202; Ver Panel de Control
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #2c3e50; padding: 20px; text-align: center;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="color: #bdc3c7; font-size: 12px; padding-bottom: 10px;">
                                        Este mensaje fue generado automáticamente por <strong style="color: #3498db;">Usage Report</strong> de <a href="https://ingeweb.co/" style="color: #3498db; text-decoration: none;">ingeweb.co</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: #95a5a6; font-size: 11px; font-style: italic;">
                                        * Se contabilizan usuarios distintos que se autenticaron en la fecha indicada. Usuarios que se autentican más de una vez solo cuentan una vez.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';

$string['messagehtml_diskusage'] = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerta de Espacio en Disco - {$a->sitename}</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f4f4f4;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 20px 10px;">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #d35400 0%, #e67e22 100%); padding: 30px 20px; text-align: center;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="text-align: center;">
                                        <span style="font-size: 40px;">&#128190;</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: #ffffff; font-size: 24px; font-weight: bold; padding-top: 10px;">
                                        Alerta de Espacio en Disco
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-top: 15px;">
                                        <span style="display: inline-block; background-color: #ffffff; color: #d35400; padding: 8px 20px; border-radius: 25px; font-weight: bold; font-size: 18px;">
                                            {$a->percentage}% de la cuota utilizada
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Urgency Banner -->
                    <tr>
                        <td style="background-color: #ffeaa7; padding: 15px 20px; border-left: 4px solid #fdcb6e;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="font-size: 14px; color: #6c5c00;">
                                        <strong>&#128680; ADVERTENCIA DE DISCO:</strong> La plataforma <strong>{$a->sitename}</strong> ha superado el <strong>{$a->percentage}%</strong> del espacio en disco asignado. Revise las recomendaciones a continuación.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 25px 30px;">
                            <!-- Disk Usage Summary -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="font-size: 18px; font-weight: bold; color: #2c3e50; padding-bottom: 15px; border-bottom: 2px solid #e67e22;">
                                        &#128202; Resumen de Uso de Disco
                                    </td>
                                </tr>
                            </table>

                            <!-- Progress Bar -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 20px;">
                                <tr>
                                    <td style="background-color: #ecf0f1; border-radius: 10px; padding: 3px;">
                                        <table role="presentation" width="{$a->percentage}%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="background: linear-gradient(90deg, #e67e22, #d35400); color: #ffffff; text-align: center; padding: 10px 0; border-radius: 8px; font-weight: bold; font-size: 14px;">
                                                    {$a->percentage}%
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Key Metrics Table -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
                                <tr style="background-color: #f8f9fa;">
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555; width: 50%; border-bottom: 1px solid #e0e0e0;">Fecha del Reporte:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #2c3e50; border-bottom: 1px solid #e0e0e0;">{$a->lastday}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555; border-bottom: 1px solid #e0e0e0;">Espacio Utilizado:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #e67e22; font-size: 16px; border-bottom: 1px solid #e0e0e0;">{$a->diskusage}</td>
                                </tr>
                                <tr style="background-color: #f8f9fa;">
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555; border-bottom: 1px solid #e0e0e0;">Cuota Asignada:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #2c3e50; border-bottom: 1px solid #e0e0e0;">{$a->quotadisk}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555;">Espacio Disponible:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #27ae60;">{$a->available_space} ({$a->available_percent}%)</td>
                                </tr>
                            </table>

                            <!-- Space Distribution -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="font-size: 18px; font-weight: bold; color: #2c3e50; padding-bottom: 15px; border-bottom: 2px solid #e67e22;">
                                        &#128193; Distribución del Espacio por Categoría
                                    </td>
                                </tr>
                            </table>

                            <!-- Distribution Bars -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px;">
                                <!-- Database -->
                                <tr>
                                    <td style="padding: 5px 0;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td width="120" style="padding: 10px; background-color: #9b59b6; color: #ffffff; font-weight: 600; font-size: 13px; border-radius: 5px 0 0 5px;">Base de Datos</td>
                                                <td style="padding: 10px 15px; background-color: #e8e8e8; border-radius: 0 5px 5px 0;">
                                                    <strong>{$a->databasesize}</strong> <span style="color: #777;">({$a->db_percent}%)</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <!-- Files -->
                                <tr>
                                    <td style="padding: 5px 0;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td width="120" style="padding: 10px; background-color: #27ae60; color: #ffffff; font-weight: 600; font-size: 13px; border-radius: 5px 0 0 5px;">Archivos (filedir)</td>
                                                <td style="padding: 10px 15px; background-color: #e8e8e8; border-radius: 0 5px 5px 0;">
                                                    <strong>{$a->filedir_size}</strong> <span style="color: #777;">({$a->filedir_percent}%)</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <!-- Cache -->
                                <tr>
                                    <td style="padding: 5px 0;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td width="120" style="padding: 10px; background-color: #e67e22; color: #ffffff; font-weight: 600; font-size: 13px; border-radius: 5px 0 0 5px;">Caché</td>
                                                <td style="padding: 10px 15px; background-color: #e8e8e8; border-radius: 0 5px 5px 0;">
                                                    <strong>{$a->cache_size}</strong> <span style="color: #777;">({$a->cache_percent}%)</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <!-- Others -->
                                <tr>
                                    <td style="padding: 5px 0;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td width="120" style="padding: 10px; background-color: #7f8c8d; color: #ffffff; font-weight: 600; font-size: 13px; border-radius: 5px 0 0 5px;">Otros</td>
                                                <td style="padding: 10px 15px; background-color: #e8e8e8; border-radius: 0 5px 5px 0;">
                                                    <strong>{$a->other_size}</strong> <span style="color: #777;">({$a->other_percent}%)</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Platform Information -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="font-size: 18px; font-weight: bold; color: #2c3e50; padding-bottom: 15px; border-bottom: 2px solid #e67e22;">
                                        &#128421; Información de la Plataforma
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
                                <tr style="background-color: #f8f9fa;">
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555; width: 50%; border-bottom: 1px solid #e0e0e0;">Versión de Moodle:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #2c3e50; border-bottom: 1px solid #e0e0e0;">{$a->moodle_release}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555; border-bottom: 1px solid #e0e0e0;">Total de Cursos:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #2c3e50; border-bottom: 1px solid #e0e0e0;">{$a->coursescount}</td>
                                </tr>
                                <tr style="background-color: #f8f9fa;">
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555; border-bottom: 1px solid #e0e0e0;">Copias por Curso:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #2c3e50; border-bottom: 1px solid #e0e0e0;">{$a->backupcount}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 15px; font-weight: 500; color: #555555;">Usuarios Activos:</td>
                                    <td style="padding: 12px 15px; font-weight: 600; color: #2c3e50;">{$a->numberofusers} / {$a->threshold} ({$a->user_percent}%)</td>
                                </tr>
                            </table>

                            <!-- Largest Courses -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="font-size: 18px; font-weight: bold; color: #2c3e50; padding-bottom: 15px; border-bottom: 2px solid #e67e22;">
                                        &#128218; Cursos que Más Espacio Ocupan
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
                                <tr style="background-color: #34495e;">
                                    <th style="padding: 12px 15px; font-weight: 600; color: #ffffff; text-align: left;">Curso</th>
                                    <th style="padding: 12px 15px; font-weight: 600; color: #ffffff; text-align: left;">Tamaño</th>
                                    <th style="padding: 12px 15px; font-weight: 600; color: #ffffff; text-align: left;">% del Total</th>
                                </tr>
                                {$a->top_courses_rows}
                            </table>

                            <!-- Recommendations Box -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 25px; background-color: #e8f6f3; border-radius: 8px; border-left: 4px solid #1abc9c;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="font-size: 16px; font-weight: bold; color: #16a085; padding-bottom: 12px;">
                                                    &#128161; Recomendaciones para Liberar Espacio
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 14px; color: #2c3e50; line-height: 1.8;">
                                                    &#8226; <strong>Reducir copias:</strong> Disminuir el número de copias automáticas por curso (actualmente: {$a->backupcount})<br>
                                                    &#8226; <strong>Limpiar archivos:</strong> Eliminar archivos antiguos sin uso mediante la herramienta de limpieza<br>
                                                    &#8226; <strong>Revisar cursos:</strong> Archivar o limpiar los cursos más grandes listados arriba<br>
                                                    &#8226; <strong>Purgar caché:</strong> Limpiar la caché del sistema para liberar espacio temporal
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA Button -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top: 30px;">
                                <tr>
                                    <td align="center">
                                        <a href="{$a->referer}" style="display: inline-block; background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: #ffffff; padding: 14px 35px; border-radius: 25px; text-decoration: none; font-weight: bold; font-size: 16px;">
                                            &#128202; Ver Panel de Control
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #2c3e50; padding: 20px; text-align: center;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="color: #bdc3c7; font-size: 12px; padding-bottom: 10px;">
                                        Este mensaje fue generado automáticamente por <strong style="color: #3498db;">Usage Report</strong> de <a href="https://ingeweb.co/" style="color: #3498db; text-decoration: none;">ingeweb.co</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: #95a5a6; font-size: 11px; font-style: italic;">
                                        Si necesita asistencia técnica, por favor no responda a este correo y contacte a su administrador de hosting.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';