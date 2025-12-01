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
 * Spanish language strings for the usage monitor plugin.
 *
 * @package     report_usage_monitor
 * @category    string
 * @author      Alonso Arias <soporte@ingeweb.co>
 * @copyright   2025 Alonso Arias <soporte@ingeweb.co>
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
$string['processusersdailytask'] = 'Procesar estadísticas de usuarios diarios';
$string['processcombinednotificationtask'] = 'Procesar notificaciones del monitor de uso';

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