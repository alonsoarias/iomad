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
 * Cadenas de idioma para local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// =============================================================================
// METADATOS DEL PLUGIN
// =============================================================================
$string['pluginname'] = 'Bolsa de Empleo';
$string['plugindescription'] = 'Gestión de vacantes para profesores cátedra';

// =============================================================================
// CAPACIDADES
// =============================================================================
$string['jobboard:view'] = 'Ver bolsa de empleo';
$string['jobboard:apply'] = 'Postularse a vacantes';
$string['jobboard:manage'] = 'Gestionar vacantes';
$string['jobboard:review'] = 'Revisar postulaciones';
$string['jobboard:managecommittee'] = 'Gestionar comités de selección';
$string['jobboard:managetemplates'] = 'Gestionar plantillas de correo';
$string['jobboard:viewreports'] = 'Ver reportes';
$string['jobboard:managesettings'] = 'Gestionar configuración';

// =============================================================================
// NAVEGACIÓN
// =============================================================================
$string['nav_dashboard'] = 'Panel de Control';
$string['nav_convocatorias'] = 'Convocatorias';
$string['nav_vacancies'] = 'Vacantes';
$string['nav_applications'] = 'Postulaciones';
$string['nav_myapplications'] = 'Mis Postulaciones';
$string['nav_documents'] = 'Documentos';
$string['nav_reviewers'] = 'Revisores';
$string['nav_committees'] = 'Comités';
$string['nav_templates'] = 'Plantillas de Correo';
$string['nav_doctypes'] = 'Tipos de Documento';
$string['nav_settings'] = 'Configuración';
$string['nav_reports'] = 'Reportes';

// =============================================================================
// INTERFAZ GENERAL
// =============================================================================
$string['actions'] = 'Acciones';
$string['add'] = 'Agregar';
$string['edit'] = 'Editar';
$string['delete'] = 'Eliminar';
$string['save'] = 'Guardar';
$string['cancel'] = 'Cancelar';
$string['confirm'] = 'Confirmar';
$string['back'] = 'Volver';
$string['search'] = 'Buscar';
$string['filter'] = 'Filtrar';
$string['reset'] = 'Restablecer';
$string['view'] = 'Ver';
$string['download'] = 'Descargar';
$string['upload'] = 'Subir';
$string['status'] = 'Estado';
$string['date'] = 'Fecha';
$string['name'] = 'Nombre';
$string['description'] = 'Descripción';
$string['details'] = 'Detalles';
$string['active'] = 'Activo';
$string['inactive'] = 'Inactivo';
$string['yes'] = 'Sí';
$string['no'] = 'No';
$string['all'] = 'Todos';
$string['none'] = 'Ninguno';
$string['required'] = 'Requerido';
$string['optional'] = 'Opcional';
$string['loading'] = 'Cargando...';
$string['noresults'] = 'No se encontraron resultados';
$string['error'] = 'Error';
$string['success'] = 'Éxito';
$string['warning'] = 'Advertencia';
$string['info'] = 'Información';

// =============================================================================
// PANEL DE CONTROL
// =============================================================================
$string['dashboard'] = 'Panel de Control';
$string['dashboard_welcome'] = 'Bienvenido a la Bolsa de Empleo';
$string['dashboard_stats'] = 'Estadísticas';
$string['dashboard_recent'] = 'Actividad Reciente';
$string['stat_vacancies'] = 'Vacantes';
$string['stat_applications'] = 'Postulaciones';
$string['stat_pending'] = 'Pendientes';
$string['stat_approved'] = 'Aprobadas';
$string['stat_rejected'] = 'Rechazadas';

// =============================================================================
// CONVOCATORIAS
// =============================================================================
$string['convocatoria'] = 'Convocatoria';
$string['convocatorias'] = 'Convocatorias';
$string['convocatoria_add'] = 'Agregar Convocatoria';
$string['convocatoria_edit'] = 'Editar Convocatoria';
$string['convocatoria_delete'] = 'Eliminar Convocatoria';
$string['convocatoria_view'] = 'Ver Convocatoria';
$string['convocatoria_title'] = 'Título';
$string['convocatoria_code'] = 'Código';
$string['convocatoria_description'] = 'Descripción';
$string['convocatoria_startdate'] = 'Fecha de Inicio';
$string['convocatoria_enddate'] = 'Fecha de Cierre';
$string['convocatoria_status'] = 'Estado';
$string['convocatoria_vacancies'] = 'Vacantes';
$string['convocatoria_applications'] = 'Postulaciones';
$string['convocatoria_document'] = 'Documento de Convocatoria (PDF)';
$string['convocatoria_document_help'] = 'Suba el documento oficial de la convocatoria en formato PDF';
$string['convocatoria_faculty'] = 'Facultad';
$string['convocatoria_company'] = 'Centro Tutorial';

// Estados de convocatoria
$string['convocatoria_status_draft'] = 'Borrador';
$string['convocatoria_status_open'] = 'Abierta';
$string['convocatoria_status_closed'] = 'Cerrada';
$string['convocatoria_status_cancelled'] = 'Cancelada';
$string['convocatoria_status_completed'] = 'Completada';

// Mensajes de convocatoria
$string['convocatoria_created'] = 'Convocatoria creada exitosamente';
$string['convocatoria_updated'] = 'Convocatoria actualizada exitosamente';
$string['convocatoria_deleted'] = 'Convocatoria eliminada exitosamente';
$string['convocatoria_delete_confirm'] = '¿Está seguro que desea eliminar esta convocatoria?';
$string['convocatoria_not_found'] = 'Convocatoria no encontrada';
$string['convocatoria_has_applications'] = 'No se puede eliminar una convocatoria con postulaciones existentes';

// =============================================================================
// VACANTES
// =============================================================================
$string['vacancy'] = 'Vacante';
$string['vacancies'] = 'Vacantes';
$string['vacancy_add'] = 'Agregar Vacante';
$string['vacancy_edit'] = 'Editar Vacante';
$string['vacancy_delete'] = 'Eliminar Vacante';
$string['vacancy_view'] = 'Ver Vacante';
$string['vacancy_title'] = 'Título';
$string['vacancy_description'] = 'Descripción';
$string['vacancy_requirements'] = 'Requisitos';
$string['vacancy_positions'] = 'Posiciones';
$string['vacancy_program'] = 'Programa Académico';
$string['vacancy_course'] = 'Curso';
$string['vacancy_modality'] = 'Modalidad';
$string['vacancy_hours'] = 'Horas Semanales';
$string['vacancy_salary'] = 'Salario';
$string['vacancy_deadline'] = 'Fecha Límite de Postulación';

// Estados de vacante
$string['vacancy_status_draft'] = 'Borrador';
$string['vacancy_status_open'] = 'Abierta';
$string['vacancy_status_closed'] = 'Cerrada';
$string['vacancy_status_filled'] = 'Cubierta';
$string['vacancy_status_cancelled'] = 'Cancelada';

// Mensajes de vacante
$string['vacancy_created'] = 'Vacante creada exitosamente';
$string['vacancy_updated'] = 'Vacante actualizada exitosamente';
$string['vacancy_deleted'] = 'Vacante eliminada exitosamente';
$string['vacancy_delete_confirm'] = '¿Está seguro que desea eliminar esta vacante?';
$string['vacancy_not_found'] = 'Vacante no encontrada';
$string['vacancy_has_applications'] = 'No se puede eliminar una vacante con postulaciones existentes';
$string['vacancy_no_vacancies'] = 'No hay vacantes disponibles';

// =============================================================================
// POSTULACIONES
// =============================================================================
$string['application'] = 'Postulación';
$string['applications'] = 'Postulaciones';
$string['application_submit'] = 'Enviar Postulación';
$string['application_view'] = 'Ver Postulación';
$string['application_review'] = 'Revisar Postulación';
$string['application_withdraw'] = 'Retirar Postulación';
$string['myapplications'] = 'Mis Postulaciones';
$string['application_date'] = 'Fecha de Postulación';
$string['application_vacancy'] = 'Vacante';
$string['application_applicant'] = 'Postulante';
$string['application_documents'] = 'Documentos';
$string['application_notes'] = 'Notas';
$string['application_reviewer'] = 'Revisor Asignado';
$string['application_assign_reviewer'] = 'Asignar Revisor';

// Estados de postulación
$string['application_status_draft'] = 'Borrador';
$string['application_status_submitted'] = 'Enviada';
$string['application_status_under_review'] = 'En Revisión';
$string['application_status_docs_pending'] = 'Documentos Pendientes';
$string['application_status_docs_validated'] = 'Documentos Validados';
$string['application_status_docs_rejected'] = 'Documentos Rechazados';
$string['application_status_shortlisted'] = 'Preseleccionada';
$string['application_status_interview'] = 'Entrevista Programada';
$string['application_status_selected'] = 'Seleccionada';
$string['application_status_rejected'] = 'Rechazada';
$string['application_status_withdrawn'] = 'Retirada';

// Mensajes de postulación
$string['application_submitted'] = 'Postulación enviada exitosamente';
$string['application_updated'] = 'Postulación actualizada exitosamente';
$string['application_withdrawn'] = 'Postulación retirada exitosamente';
$string['application_withdraw_confirm'] = '¿Está seguro que desea retirar su postulación?';
$string['application_not_found'] = 'Postulación no encontrada';
$string['application_already_exists'] = 'Ya se ha postulado a esta vacante';
$string['application_deadline_passed'] = 'La fecha límite de postulación ha pasado';
$string['application_no_applications'] = 'No se encontraron postulaciones';

// =============================================================================
// DOCUMENTOS
// =============================================================================
$string['document'] = 'Documento';
$string['documents'] = 'Documentos';
$string['document_upload'] = 'Subir Documento';
$string['document_download'] = 'Descargar Documento';
$string['document_view'] = 'Ver Documento';
$string['document_delete'] = 'Eliminar Documento';
$string['document_type'] = 'Tipo de Documento';
$string['document_file'] = 'Archivo';
$string['document_filename'] = 'Nombre del Archivo';
$string['document_filesize'] = 'Tamaño del Archivo';
$string['document_uploaded'] = 'Fecha de Carga';
$string['document_validation'] = 'Validación';
$string['document_validate'] = 'Validar Documento';
$string['document_reject'] = 'Rechazar Documento';
$string['document_comments'] = 'Comentarios';

// Estados de documento
$string['document_status_pending'] = 'Pendiente de Revisión';
$string['document_status_approved'] = 'Aprobado';
$string['document_status_rejected'] = 'Rechazado';
$string['document_status_expired'] = 'Vencido';

// Mensajes de documento
$string['document_uploaded_success'] = 'Documento subido exitosamente';
$string['document_deleted'] = 'Documento eliminado exitosamente';
$string['document_validated'] = 'Documento validado exitosamente';
$string['document_rejected'] = 'Documento rechazado';
$string['document_not_found'] = 'Documento no encontrado';
$string['document_invalid_type'] = 'Tipo de documento inválido';
$string['document_too_large'] = 'El tamaño del archivo excede el máximo permitido';
$string['document_required_missing'] = 'Faltan documentos requeridos';
$string['document_only_pdf'] = 'Solo se permiten archivos PDF';

// =============================================================================
// TIPOS DE DOCUMENTO
// =============================================================================
$string['doctype'] = 'Tipo de Documento';
$string['doctypes'] = 'Tipos de Documento';
$string['doctype_add'] = 'Agregar Tipo de Documento';
$string['doctype_edit'] = 'Editar Tipo de Documento';
$string['doctype_delete'] = 'Eliminar Tipo de Documento';
$string['doctype_name'] = 'Nombre';
$string['doctype_code'] = 'Código';
$string['doctype_description'] = 'Descripción';
$string['doctype_required'] = 'Requerido';
$string['doctype_validitydays'] = 'Días de Vigencia';
$string['doctype_validitydays_help'] = 'Número de días que el documento es válido (0 = sin vencimiento)';
$string['doctype_maxsize'] = 'Tamaño Máximo de Archivo (MB)';
$string['doctype_input_type'] = 'Tipo de Entrada';
$string['doctype_input_file'] = 'Carga de Archivo';
$string['doctype_input_text'] = 'Entrada de Texto';
$string['doctype_input_date'] = 'Entrada de Fecha';
$string['doctype_input_number'] = 'Entrada Numérica';
$string['doctype_active'] = 'Activo';
$string['doctype_order'] = 'Orden de Visualización';

// Mensajes de tipos de documento
$string['doctype_created'] = 'Tipo de documento creado exitosamente';
$string['doctype_updated'] = 'Tipo de documento actualizado exitosamente';
$string['doctype_deleted'] = 'Tipo de documento eliminado exitosamente';
$string['doctype_delete_confirm'] = '¿Está seguro que desea eliminar este tipo de documento?';
$string['doctype_has_documents'] = 'No se puede eliminar un tipo de documento con documentos existentes';

// =============================================================================
// REVISORES POR PROGRAMA
// =============================================================================
$string['program_reviewers'] = 'Revisores por Programa';
$string['program_reviewer'] = 'Revisor de Programa';
$string['manage_program_reviewers'] = 'Gestionar Revisores por Programa';
$string['reviewer'] = 'Revisor';
$string['reviewers'] = 'Revisores';
$string['reviewer_add'] = 'Agregar Revisor';
$string['reviewer_remove'] = 'Eliminar Revisor';
$string['reviewer_role'] = 'Rol';
$string['reviewer_status'] = 'Estado';
$string['reviewer_program'] = 'Programa';
$string['reviewer_user'] = 'Usuario';
$string['reviewer_assigned'] = 'Fecha de Asignación';
$string['reviewer_workload'] = 'Carga de Trabajo';

// Roles de revisor
$string['role_lead_reviewer'] = 'Revisor Líder';
$string['role_reviewer'] = 'Revisor';

// Estados de revisor
$string['reviewer_status_active'] = 'Activo';
$string['reviewer_status_inactive'] = 'Inactivo';

// Estadísticas de revisores
$string['reviewer_stats_total'] = 'Asignaciones Totales';
$string['reviewer_stats_active'] = 'Revisores Activos';
$string['reviewer_stats_leads'] = 'Revisores Líderes';
$string['reviewer_stats_programs'] = 'Programas con Revisores';
$string['reviewer_stats_users'] = 'Revisores Únicos';

// Mensajes de revisor
$string['reviewer_added'] = 'Revisor agregado exitosamente';
$string['reviewer_removed'] = 'Revisor eliminado exitosamente';
$string['reviewer_role_updated'] = 'Rol del revisor actualizado exitosamente';
$string['reviewer_status_updated'] = 'Estado del revisor actualizado exitosamente';
$string['reviewer_already_assigned'] = 'El usuario ya es revisor de este programa';
$string['reviewer_not_found'] = 'Revisor no encontrado';
$string['reviewer_cannot_remove_last_lead'] = 'No se puede eliminar el último revisor líder activo';
$string['reviewer_no_capability'] = 'El usuario no tiene la capacidad de revisor';
$string['reviewer_select_program'] = 'Seleccionar Programa';
$string['reviewer_select_user'] = 'Seleccionar Usuario';
$string['reviewer_no_reviewers'] = 'No hay revisores asignados';
$string['reviewer_programs_without'] = 'Programas sin revisores';

// =============================================================================
// COMITÉS DE SELECCIÓN
// =============================================================================
$string['committee'] = 'Comité de Selección';
$string['committees'] = 'Comités de Selección';
$string['committee_manage'] = 'Gestionar Comités';
$string['committee_add'] = 'Agregar Comité';
$string['committee_edit'] = 'Editar Comité';
$string['committee_delete'] = 'Eliminar Comité';
$string['committee_view'] = 'Ver Comité';
$string['committee_faculty'] = 'Facultad';
$string['committee_members'] = 'Miembros';
$string['committee_add_member'] = 'Agregar Miembro';
$string['committee_remove_member'] = 'Eliminar Miembro';

// Roles de comité
$string['committee_role_president'] = 'Presidente';
$string['committee_role_secretary'] = 'Secretario';
$string['committee_role_member'] = 'Miembro';

// Mensajes de comité
$string['committee_created'] = 'Comité creado exitosamente';
$string['committee_updated'] = 'Comité actualizado exitosamente';
$string['committee_deleted'] = 'Comité eliminado exitosamente';
$string['committee_member_added'] = 'Miembro agregado al comité';
$string['committee_member_removed'] = 'Miembro eliminado del comité';
$string['committee_not_found'] = 'Comité no encontrado';
$string['committee_no_committees'] = 'No se encontraron comités';

// =============================================================================
// PLANTILLAS DE CORREO
// =============================================================================
$string['email_templates'] = 'Plantillas de Correo';
$string['email_template'] = 'Plantilla de Correo';
$string['template_manage'] = 'Gestionar Plantillas';
$string['template_add'] = 'Agregar Plantilla';
$string['template_edit'] = 'Editar Plantilla';
$string['template_delete'] = 'Eliminar Plantilla';
$string['template_duplicate'] = 'Duplicar Plantilla';
$string['template_preview'] = 'Vista Previa';
$string['template_send_test'] = 'Enviar Correo de Prueba';
$string['template_name'] = 'Nombre de la Plantilla';
$string['template_code'] = 'Código de Plantilla';
$string['template_subject'] = 'Asunto';
$string['template_body'] = 'Cuerpo';
$string['template_signature'] = 'Firma';
$string['template_language'] = 'Idioma';
$string['template_active'] = 'Activa';
$string['template_event'] = 'Evento Disparador';

// Eventos de plantilla
$string['template_event_application_submitted'] = 'Postulación Enviada';
$string['template_event_application_received'] = 'Postulación Recibida (Admin)';
$string['template_event_documents_validated'] = 'Documentos Validados';
$string['template_event_documents_rejected'] = 'Documentos Rechazados';
$string['template_event_application_shortlisted'] = 'Postulación Preseleccionada';
$string['template_event_interview_scheduled'] = 'Entrevista Programada';
$string['template_event_application_selected'] = 'Postulación Seleccionada';
$string['template_event_application_rejected'] = 'Postulación Rechazada';
$string['template_event_reviewer_assigned'] = 'Revisor Asignado';
$string['template_event_deadline_reminder'] = 'Recordatorio de Fecha Límite';

// Marcadores de posición
$string['template_placeholders'] = 'Marcadores Disponibles';
$string['placeholder_user_firstname'] = 'Nombre del usuario';
$string['placeholder_user_lastname'] = 'Apellido del usuario';
$string['placeholder_user_fullname'] = 'Nombre completo del usuario';
$string['placeholder_user_email'] = 'Correo del usuario';
$string['placeholder_vacancy_title'] = 'Título de la vacante';
$string['placeholder_vacancy_program'] = 'Programa académico';
$string['placeholder_convocatoria_title'] = 'Título de la convocatoria';
$string['placeholder_convocatoria_code'] = 'Código de la convocatoria';
$string['placeholder_application_date'] = 'Fecha de postulación';
$string['placeholder_application_status'] = 'Estado de la postulación';
$string['placeholder_company_name'] = 'Nombre del centro tutorial';
$string['placeholder_site_name'] = 'Nombre del sitio';
$string['placeholder_site_url'] = 'URL del sitio';
$string['placeholder_deadline'] = 'Fecha límite';
$string['placeholder_reviewer_name'] = 'Nombre del revisor';
$string['placeholder_documents_list'] = 'Lista de documentos';
$string['placeholder_rejection_reason'] = 'Motivo del rechazo';
$string['placeholder_interview_date'] = 'Fecha de entrevista';
$string['placeholder_interview_location'] = 'Lugar de entrevista';

// Mensajes de plantilla
$string['template_created'] = 'Plantilla creada exitosamente';
$string['template_updated'] = 'Plantilla actualizada exitosamente';
$string['template_deleted'] = 'Plantilla eliminada exitosamente';
$string['template_duplicated'] = 'Plantilla duplicada exitosamente';
$string['template_test_sent'] = 'Correo de prueba enviado exitosamente';
$string['template_not_found'] = 'Plantilla no encontrada';
$string['template_code_exists'] = 'El código de plantilla ya existe';
$string['template_preview_title'] = 'Vista Previa del Correo';
$string['template_preview_note'] = 'Esta es una vista previa con datos de ejemplo';

// =============================================================================
// REPORTES
// =============================================================================
$string['reports'] = 'Reportes';
$string['report_applications'] = 'Reporte de Postulaciones';
$string['report_vacancies'] = 'Reporte de Vacantes';
$string['report_reviewers'] = 'Reporte de Revisores';
$string['report_documents'] = 'Reporte de Documentos';
$string['report_export'] = 'Exportar';
$string['report_export_csv'] = 'Exportar a CSV';
$string['report_export_excel'] = 'Exportar a Excel';
$string['report_export_pdf'] = 'Exportar a PDF';
$string['report_date_from'] = 'Desde Fecha';
$string['report_date_to'] = 'Hasta Fecha';
$string['report_generate'] = 'Generar Reporte';

// =============================================================================
// CONFIGURACIÓN
// =============================================================================
$string['settings'] = 'Configuración';
$string['settings_general'] = 'Configuración General';
$string['settings_notifications'] = 'Configuración de Notificaciones';
$string['settings_documents'] = 'Configuración de Documentos';
$string['settings_emails'] = 'Configuración de Correos';

// Configuración general
$string['setting_enabled'] = 'Habilitar Bolsa de Empleo';
$string['setting_enabled_desc'] = 'Habilitar o deshabilitar la funcionalidad de la bolsa de empleo';
$string['setting_allow_applications'] = 'Permitir Postulaciones';
$string['setting_allow_applications_desc'] = 'Permitir que los usuarios envíen postulaciones';
$string['setting_require_login'] = 'Requerir Inicio de Sesión';
$string['setting_require_login_desc'] = 'Requerir que los usuarios inicien sesión para ver las vacantes';

// Configuración de documentos
$string['setting_max_filesize'] = 'Tamaño Máximo de Archivo';
$string['setting_max_filesize_desc'] = 'Tamaño máximo de archivo para carga de documentos (en MB)';
$string['setting_allowed_types'] = 'Tipos de Archivo Permitidos';
$string['setting_allowed_types_desc'] = 'Lista separada por comas de extensiones de archivo permitidas';
$string['setting_pdf_only'] = 'Solo PDF';
$string['setting_pdf_only_desc'] = 'Solo permitir carga de archivos PDF';

// Configuración de correo
$string['setting_email_from'] = 'Dirección de Correo Remitente';
$string['setting_email_from_desc'] = 'Dirección de correo usada como remitente';
$string['setting_email_replyto'] = 'Dirección de Respuesta';
$string['setting_email_replyto_desc'] = 'Dirección de correo para respuestas';
$string['setting_email_copy_admin'] = 'Copiar Correos al Admin';
$string['setting_email_copy_admin_desc'] = 'Enviar una copia de todos los correos a los administradores';

// =============================================================================
// ERRORES Y VALIDACIÓN
// =============================================================================
$string['error_required_field'] = 'Este campo es requerido';
$string['error_invalid_date'] = 'Formato de fecha inválido';
$string['error_invalid_email'] = 'Dirección de correo inválida';
$string['error_date_past'] = 'La fecha no puede estar en el pasado';
$string['error_date_order'] = 'La fecha de fin debe ser posterior a la fecha de inicio';
$string['error_file_upload'] = 'Error al subir el archivo';
$string['error_file_type'] = 'Tipo de archivo inválido';
$string['error_file_size'] = 'El tamaño del archivo excede el máximo permitido';
$string['error_permission_denied'] = 'Permiso denegado';
$string['error_not_found'] = 'Registro no encontrado';
$string['error_already_exists'] = 'El registro ya existe';
$string['error_cannot_delete'] = 'No se puede eliminar este registro';
$string['error_invalid_action'] = 'Acción inválida';
$string['error_session_expired'] = 'Sesión expirada. Por favor inicie sesión nuevamente.';
$string['error_unknown'] = 'Ocurrió un error desconocido';

// =============================================================================
// CONFIRMACIONES
// =============================================================================
$string['confirm_delete'] = '¿Está seguro que desea eliminar este elemento?';
$string['confirm_action'] = '¿Está seguro que desea realizar esta acción?';
$string['confirm_withdraw'] = '¿Está seguro que desea retirar su postulación?';
$string['confirm_submit'] = '¿Está seguro que desea enviar su postulación?';

// =============================================================================
// ACCESIBILIDAD
// =============================================================================
$string['aria_close'] = 'Cerrar';
$string['aria_expand'] = 'Expandir';
$string['aria_collapse'] = 'Contraer';
$string['aria_menu'] = 'Menú';
$string['aria_loading'] = 'Cargando contenido';
$string['aria_required'] = 'Campo requerido';

// =============================================================================
// MODALIDADES
// =============================================================================
$string['modality'] = 'Modalidad';
$string['modality_presencial'] = 'Presencial';
$string['modality_distancia'] = 'Distancia';
$string['modality_virtual'] = 'Virtual';
$string['modality_hibrida'] = 'Híbrida';

// =============================================================================
// FACULTADES Y PROGRAMAS
// =============================================================================
$string['faculty'] = 'Facultad';
$string['faculties'] = 'Facultades';
$string['program'] = 'Programa Académico';
$string['programs'] = 'Programas Académicos';
$string['select_faculty'] = 'Seleccionar Facultad';
$string['select_program'] = 'Seleccionar Programa';

// =============================================================================
// REGISTRO DE AUDITORÍA
// =============================================================================
$string['audit_log'] = 'Registro de Auditoría';
$string['audit_action'] = 'Acción';
$string['audit_user'] = 'Usuario';
$string['audit_date'] = 'Fecha';
$string['audit_details'] = 'Detalles';
$string['audit_ip'] = 'Dirección IP';

// Acciones de auditoría
$string['audit_application_submitted'] = 'Postulación enviada';
$string['audit_application_updated'] = 'Postulación actualizada';
$string['audit_application_withdrawn'] = 'Postulación retirada';
$string['audit_document_uploaded'] = 'Documento subido';
$string['audit_document_validated'] = 'Documento validado';
$string['audit_document_rejected'] = 'Documento rechazado';
$string['audit_status_changed'] = 'Estado cambiado';
$string['audit_reviewer_assigned'] = 'Revisor asignado';
$string['audit_email_sent'] = 'Correo enviado';
$string['audit_program_reviewer_added'] = 'Revisor de programa agregado';
$string['audit_program_reviewer_removed'] = 'Revisor de programa eliminado';
$string['audit_program_reviewer_role_changed'] = 'Rol de revisor de programa cambiado';

// =============================================================================
// CRON Y TAREAS
// =============================================================================
$string['task_send_notifications'] = 'Enviar notificaciones de correo pendientes';
$string['task_cleanup_drafts'] = 'Limpiar borradores de postulaciones abandonadas';
$string['task_send_reminders'] = 'Enviar recordatorios de fecha límite';
$string['task_expire_documents'] = 'Marcar documentos vencidos';

// =============================================================================
// PRIVACIDAD
// =============================================================================
$string['privacy:metadata:applications'] = 'Información sobre postulaciones de empleo';
$string['privacy:metadata:applications:userid'] = 'El ID del usuario que envió la postulación';
$string['privacy:metadata:applications:status'] = 'El estado de la postulación';
$string['privacy:metadata:documents'] = 'Documentos subidos con las postulaciones';
$string['privacy:metadata:documents:userid'] = 'El ID del usuario que subió el documento';

// =============================================================================
// CADENAS DE AYUDA
// =============================================================================
$string['help_convocatoria'] = 'Una convocatoria es un anuncio formal para contratar profesores cátedra';
$string['help_vacancy'] = 'Una vacante representa una posición docente específica dentro de una convocatoria';
$string['help_application'] = 'Una postulación es enviada por un candidato para una vacante específica';
$string['help_reviewer'] = 'Un revisor evalúa y valida los documentos de las postulaciones';
$string['help_committee'] = 'Un comité de selección toma las decisiones finales de contratación';
$string['help_program_reviewer'] = 'Los revisores por programa son asignados a programas académicos específicos para revisar postulaciones';

// =============================================================================
// PÁGINA DE GESTIÓN DE REVISORES POR PROGRAMA
// =============================================================================
$string['programreviewers'] = 'Revisores por Programa';
$string['program_reviewers_desc'] = 'Gestionar revisores asignados a programas académicos';
$string['programreviewerhelp'] = 'Asigne revisores a programas académicos específicos para revisar postulaciones';
$string['totalreviewers'] = 'Total de Revisores';
$string['activereviewers'] = 'Revisores Activos';
$string['leadreviewers'] = 'Revisores Líderes';
$string['programswithreviewers'] = 'Programas con Revisores';
$string['noprogramswithreviewers'] = 'Ningún programa tiene revisores asignados aún';
$string['addreviewer'] = 'Agregar Revisor';
$string['addreviewerstoprogram'] = 'Agregar revisores a este programa';
$string['assignedreviewers'] = 'Revisores Asignados';
$string['noreviewersforprogram'] = 'No hay revisores asignados a este programa';
$string['nousersavailable'] = 'No hay usuarios disponibles con capacidad de revisor';
$string['selectuser'] = 'Seleccionar usuario...';
$string['revieweradded'] = 'Revisor agregado exitosamente';
$string['revieweradderror'] = 'Error al agregar revisor';
$string['reviewerremoved'] = 'Revisor eliminado exitosamente';
$string['reviewerremoveerror'] = 'Error al eliminar revisor';
$string['rolechanged'] = 'Rol cambiado exitosamente';
$string['rolechangeerror'] = 'Error al cambiar rol';
$string['statuschanged'] = 'Estado cambiado exitosamente';
$string['statuschangeerror'] = 'Error al cambiar estado';
$string['confirmremovereviewer'] = '¿Está seguro que desea eliminar este revisor?';
$string['changerole'] = 'Cambiar Rol';
$string['backtolist'] = 'Volver a la lista';
$string['activate'] = 'Activar';
$string['deactivate'] = 'Desactivar';
$string['manage'] = 'Gestionar';
$string['remove'] = 'Eliminar';
$string['role'] = 'Rol';
$string['user'] = 'Usuario';
$string['email'] = 'Correo';
$string['help'] = 'Ayuda';

// Validación masiva y revisión de documentos.
$string['alreadyvalidated'] = 'Documento ya validado';
$string['bulkrejected'] = 'Rechazado durante validación masiva';
$string['interviewscheduled'] = 'Entrevista ha sido programada';

// =============================================================================
// CADENAS FALTANTES - Lista auto-generada
// =============================================================================

// Dashboard y navegación.
$string['aboutdoctypes'] = 'Acerca de tipos de documento';
$string['activeassignments'] = 'Asignaciones activas';
$string['activecommittees'] = 'Comités activos';
$string['activeconvocatorias'] = 'Convocatorias activas';
$string['activeconvocatorias_alert'] = 'Hay convocatorias abiertas para postulaciones';
$string['activeexemptions'] = 'Exenciones activas';
$string['addconvocatoria'] = 'Agregar convocatoria';
$string['adddoctype'] = 'Agregar tipo de documento';
$string['addexemption'] = 'Agregar exención';
$string['additionalinfo'] = 'Información adicional';
$string['additionalnotes'] = 'Notas adicionales';
$string['addmember'] = 'Agregar miembro';
$string['addnew'] = 'Agregar nuevo';
$string['addvacancy'] = 'Agregar vacante';
$string['adminonly'] = 'Solo administrador';
$string['age_exempt_notice'] = 'Aplica exención por edad';
$string['ageexemptionthreshold'] = 'Umbral de exención por edad';

// Postulante y postulaciones.
$string['allapplicants'] = 'Todos los postulantes';
$string['allapplications'] = 'Todas las postulaciones';
$string['allapplications_desc'] = 'Ver y gestionar todas las postulaciones';
$string['allcommittees'] = 'Todos los comités';
$string['allcompanies'] = 'Todas las empresas';
$string['allcontracttypes'] = 'Todos los tipos de contrato';
$string['alldepartments'] = 'Todos los departamentos';
$string['alldocsreviewed'] = 'Todos los documentos revisados';
$string['allowedformats'] = 'Formatos permitidos';
$string['allowedformats_desc'] = 'Formatos de archivo permitidos';
$string['allowmultipleapplications_convocatoria'] = 'Permitir múltiples postulaciones';
$string['allowmultipleapplications_convocatoria_desc'] = 'Permitir a usuarios postularse a múltiples vacantes';
$string['allstatuses'] = 'Todos los estados';
$string['allvacancies'] = 'Todas las vacantes';
$string['allvalidated'] = 'Todos validados';
$string['alreadyapplied'] = 'Ya te has postulado a esta vacante';
$string['andmore'] = 'y {$a} más';
$string['antecedentesmaxdays'] = 'Antigüedad máxima de antecedentes (días)';
$string['applicant'] = 'Postulante';
$string['applicantinfo'] = 'Información del postulante';
$string['applicantwillbenotified'] = 'El postulante será notificado';
$string['applicationdetails'] = 'Detalles de la postulación';
$string['applicationerror'] = 'Error procesando postulación';
$string['applicationguidelines'] = 'Instrucciones de postulación';
$string['applicationlimits'] = 'Límites de postulación';
$string['applicationlimits_perconvocatoria_desc'] = 'Postulaciones máximas por convocatoria';
$string['applicationof'] = 'Postulación {$a->current} de {$a->total}';
$string['applicationsbystatus'] = 'Postulaciones por estado';
$string['applicationsbyvacancy'] = 'Postulaciones por vacante';
$string['applicationstats'] = 'Estadísticas de postulaciones';
$string['applicationsubmitted'] = 'Postulación enviada exitosamente';
$string['applicationwithdrawn'] = 'Postulación retirada';
$string['applied'] = 'Postulado';
$string['apply'] = 'Postularse';
$string['applyhelp_text'] = 'Complete todos los campos y cargue los documentos';
$string['applynow_desc'] = 'Comience su postulación ahora';
$string['applynowdesc'] = 'Postularse ahora';
$string['applytovacancy'] = 'Postularse a vacante';
$string['approve'] = 'Aprobar';
$string['approveall_confirm'] = '¿Aprobar todos los documentos?';
$string['approvedocument'] = 'Aprobar documento';
$string['approveselected'] = 'Aprobar seleccionados';

// Estados de postulación.
$string['appstatus:docs_rejected'] = 'Documentos rechazados';
$string['appstatus:docs_validated'] = 'Documentos validados';
$string['appstatus:interview'] = 'Entrevista programada';
$string['appstatus:rejected'] = 'Rechazado';
$string['appstatus:selected'] = 'Seleccionado';
$string['appstatus:submitted'] = 'Enviado';
$string['appstatus:under_review'] = 'En revisión';
$string['appstatus:withdrawn'] = 'Retirado';

// Archivo y asignar.
$string['archiveconvocatoria'] = 'Archivar convocatoria';
$string['assigned'] = 'Asignado';
$string['assignedusers'] = 'Usuarios asignados';
$string['assignnewusers'] = 'Asignar nuevos usuarios';
$string['assignreviewer'] = 'Asignar revisor';
$string['assignreviewers'] = 'Asignar revisores';
$string['assignreviewers_desc'] = 'Gestionar asignaciones de revisores';
$string['assignselected'] = 'Asignar seleccionados';
$string['assignto'] = 'Asignar a';
$string['auditlog'] = 'Registro de auditoría';
$string['autoassign'] = 'Auto-asignar';
$string['autoassignall'] = 'Auto-asignar todos';
$string['autoassigncomplete'] = 'Auto-asignación completada';
$string['autoassignhelp'] = 'Distribuir postulaciones automáticamente';
$string['autovalidated'] = 'Auto-validado';
$string['available_placeholders'] = 'Marcadores disponibles';
$string['available_vacancies_alert'] = 'Vacantes disponibles para postulación';
$string['availablereviewers'] = 'Revisores disponibles';
$string['availablevacancies'] = 'Vacantes disponibles';
$string['avgtime'] = 'Tiempo promedio';
$string['avgvalidationtime'] = 'Tiempo promedio de validación';
$string['avgworkload'] = 'Carga de trabajo promedio';

// Navegación hacia atrás.
$string['back_to_templates'] = 'Volver a plantillas';
$string['backtoapplications'] = 'Volver a postulaciones';
$string['backtoconvocatoria'] = 'Volver a convocatoria';
$string['backtoconvocatorias'] = 'Volver a convocatorias';
$string['backtodashboard'] = 'Volver al panel';
$string['backtomanage'] = 'Volver a gestión';
$string['backtoreviewlist'] = 'Volver a lista de revisiones';
$string['backtorolelist'] = 'Volver a roles';
$string['backtovacancies'] = 'Volver a vacantes';
$string['backtovacancy'] = 'Volver a vacante';
$string['basicinfo'] = 'Información básica';
$string['briefdescription'] = 'Descripción breve';
$string['browse_vacancies_desc'] = 'Explorar vacantes disponibles';
$string['browseconvocatorias'] = 'Explorar convocatorias';
$string['browservacancies'] = 'Explorar vacantes';
$string['browsevacancies'] = 'Explorar vacantes';

// Acciones masivas.
$string['bulkactionerrors'] = 'Errores en acción masiva';
$string['bulkactions'] = 'Acciones masivas';
$string['bulkclose'] = 'Cerrar seleccionados';
$string['bulkdelete'] = 'Eliminar seleccionados';
$string['bulkpublish'] = 'Publicar seleccionados';
$string['bulkunpublish'] = 'Despublicar seleccionados';
$string['bulkvalidation'] = 'Validación masiva';
$string['bulkvalidation_desc'] = 'Validar múltiples documentos a la vez';
$string['bulkvalidationcomplete'] = 'Validación masiva completada';
$string['bydocumenttype'] = 'Por tipo de documento';

// Cancelado y capacidades.
$string['cancelledby'] = 'Cancelado por';
$string['cap_assignreviewers'] = 'Asignar revisores';
$string['cap_createvacancy'] = 'Crear vacantes';
$string['cap_download'] = 'Descargar documentos';
$string['cap_evaluate'] = 'Evaluar candidatos';
$string['cap_manage'] = 'Gestionar tablero de empleo';
$string['cap_review'] = 'Revisar documentos';
$string['cap_validate'] = 'Validar documentos';
$string['cap_viewevaluations'] = 'Ver evaluaciones';
$string['cap_viewreports'] = 'Ver reportes';
$string['capabilities'] = 'Capacidades';
$string['category'] = 'Categoría';
$string['chairhelp'] = 'Información del presidente del comité';
$string['changestatus'] = 'Cambiar estado';

// Items de lista de verificación.
$string['checklist_acta_date'] = 'Fecha de graduación visible';
$string['checklist_acta_number'] = 'Número de acta visible';
$string['checklist_background_date'] = 'Fecha de expedición reciente';
$string['checklist_background_status'] = 'Estado es claro';
$string['checklist_cedula_number'] = 'Número de cédula visible';
$string['checklist_cedula_photo'] = 'Foto visible';
$string['checklist_complete'] = 'Documento completo';
$string['checklist_eps_active'] = 'Afiliación activa';
$string['checklist_eps_entity'] = 'Nombre de EPS visible';
$string['checklist_legible'] = 'Documento legible';
$string['checklist_medical_aptitude'] = 'Estado de aptitud claro';
$string['checklist_medical_date'] = 'Fecha de examen válida';
$string['checklist_military_class'] = 'Clase militar especificada';
$string['checklist_military_number'] = 'Número de libreta visible';
$string['checklist_namematch'] = 'Nombre coincide con postulación';
$string['checklist_pension_active'] = 'Afiliación activa';
$string['checklist_pension_fund'] = 'Nombre del fondo visible';
$string['checklist_rut_nit'] = 'Número NIT/RUT visible';
$string['checklist_rut_updated'] = 'Fecha de actualización reciente';
$string['checklist_tarjeta_number'] = 'Número de tarjeta visible';
$string['checklist_tarjeta_profession'] = 'Profesión coincide con requisitos';
$string['checklist_title_date'] = 'Fecha de graduación visible';
$string['checklist_title_institution'] = 'Nombre de institución visible';
$string['checklist_title_program'] = 'Nombre del programa visible';
$string['checklistitems'] = 'Items de lista de verificación';
$string['clearfilters'] = 'Limpiar filtros';
$string['close'] = 'Cerrar';
$string['closeconvocatoria'] = 'Cerrar convocatoria';
$string['closedate'] = 'Fecha de cierre';
$string['closesindays'] = 'Cierra en {$a} días';
$string['closingdate'] = 'Fecha de cierre';
$string['closingsoon'] = 'Próximo a cerrar';
$string['closingsoondays'] = 'Días antes de alerta de cierre';
$string['code'] = 'Código';
$string['column'] = 'Columna';

// Comité.
$string['committeeautoroleassign'] = 'Auto-asignar roles de comité';
$string['committeecreated'] = 'Comité creado exitosamente';
$string['committeecreateerror'] = 'Error creando comité';
$string['committeename'] = 'Nombre del comité';
$string['committees_desc'] = 'Gestionar comités de selección';
$string['company'] = 'Empresa';
$string['completedreviews'] = 'Revisiones completadas';
$string['completeinterview'] = 'Completar entrevista';
$string['completeprofile_required'] = 'Complete su perfil antes de postularse';
$string['completerequiredfields'] = 'Complete todos los campos requeridos';
$string['conditional_document_note'] = 'Este documento tiene condiciones';
$string['conditionaldoctypes'] = 'Tipos de documento condicionales';
$string['conditionalnote'] = 'Nota condicional';
$string['conditions'] = 'Condiciones';
$string['configuration'] = 'Configuración';
$string['configure'] = 'Configurar';
$string['confirm_reset'] = 'Confirmar reinicio';

// Confirmar acciones.
$string['confirmaction'] = 'Confirmar acción';
$string['confirmarchiveconvocatoria'] = 'Confirmar archivar convocatoria';
$string['confirmcancel'] = 'Confirmar cancelación';
$string['confirmclose'] = 'Confirmar cierre';
$string['confirmcloseconvocatoria'] = 'Confirmar cerrar convocatoria';
$string['confirmdelete'] = 'Confirmar eliminación';
$string['confirmdeletedoctype'] = 'Confirmar eliminar tipo de documento';
$string['confirmdeletedoctype_msg'] = '¿Está seguro de eliminar este tipo de documento?';
$string['confirmdeletevacancy'] = 'Confirmar eliminar vacante';
$string['confirmdeletevconvocatoria'] = 'Confirmar eliminar convocatoria';
$string['confirmnoshow'] = 'Confirmar inasistencia';
$string['confirmopenconvocatoria'] = 'Confirmar abrir convocatoria';
$string['confirmpassword'] = 'Confirmar contraseña';
$string['confirmpublish'] = 'Confirmar publicación';
$string['confirmremovemember'] = 'Confirmar eliminar miembro';
$string['confirmreopen'] = 'Confirmar reapertura';
$string['confirmreopenconvocatoria'] = 'Confirmar reabrir convocatoria';
$string['confirmrevokeexemption'] = 'Confirmar revocar exención';
$string['confirmunassign'] = 'Confirmar desasignar';
$string['confirmunpublish'] = 'Confirmar despublicar';
$string['confirmwithdraw'] = 'Confirmar retirar postulación';

// Consentimiento.
$string['consentaccepttext'] = 'Acepto los términos y condiciones';
$string['consentgiven'] = 'Consentimiento otorgado';
$string['consentheader'] = 'Términos y consentimiento';
$string['consentrequired'] = 'El consentimiento es requerido';
$string['contactemail'] = 'Correo de contacto';
$string['contentmanagement'] = 'Gestión de contenido';

// Tipos de contrato.
$string['contract:catedra'] = 'Cátedra';
$string['contract:planta'] = 'Planta';
$string['contract:prestacion_servicios'] = 'Prestación de servicios';
$string['contract:temporal'] = 'Temporal';
$string['contract:termino_fijo'] = 'Término fijo';
$string['contracttype'] = 'Tipo de contrato';

// Conversión.
$string['conversionfailed'] = 'Conversión fallida';
$string['conversioninprogress'] = 'Conversión en progreso';
$string['conversionpending'] = 'Conversión pendiente';
$string['conversionready'] = 'Conversión lista';
$string['conversionwait'] = 'Por favor espere mientras se convierte el documento';

// Cadenas de convocatoria.
$string['convocatoria_status_archived'] = 'Archivada';
$string['convocatoriaactive'] = 'Convocatoria activa';
$string['convocatoriaarchived'] = 'Convocatoria archivada';
$string['convocatoriaclosed'] = 'Convocatoria cerrada';
$string['convocatoriaclosedmsg'] = 'Esta convocatoria ha sido cerrada';
$string['convocatoriacode'] = 'Código de convocatoria';
$string['convocatoriacreated'] = 'Convocatoria creada exitosamente';
$string['convocatoriadates'] = 'Fechas de convocatoria';
$string['convocatoriadeleted'] = 'Convocatoria eliminada';
$string['convocatoriadescription'] = 'Descripción de convocatoria';
$string['convocatoriadetails'] = 'Detalles de convocatoria';
$string['convocatoriadocexemptions'] = 'Exenciones de documentos';
$string['convocatoriaenddate'] = 'Fecha de fin';
$string['convocatoriahelp'] = 'Ayuda de convocatorias';
$string['convocatorianame'] = 'Nombre de convocatoria';
$string['convocatoriaopened'] = 'Convocatoria abierta';
$string['convocatoriapdf'] = 'PDF de convocatoria';
$string['convocatoriareopened'] = 'Convocatoria reabierta';
$string['convocatorias_dashboard_desc'] = 'Gestionar todas las convocatorias';
$string['convocatoriastartdate'] = 'Fecha de inicio';
$string['convocatoriastatus'] = 'Estado de convocatoria';
$string['convocatoriaterms'] = 'Términos de convocatoria';
$string['convocatoriaupdated'] = 'Convocatoria actualizada';
$string['convocatoriavacancies'] = 'Vacantes de convocatoria';
$string['convocatoriavacancycount'] = 'Número de vacantes';
$string['copy_placeholder'] = 'Copiar marcador';
$string['count'] = 'Cantidad';
$string['courses'] = 'Cursos';
$string['coverletter'] = 'Carta de presentación';
$string['create'] = 'Crear';
$string['createaccounttoapply'] = 'Crear cuenta para postularse';
$string['createcommittee'] = 'Crear comité';
$string['createcompanies'] = 'Crear empresas';
$string['createdby'] = 'Creado por';
$string['createvacancyinconvocatoriadesc'] = 'Crear vacante en esta convocatoria';

// Importación CSV.
$string['csvdelimiter'] = 'Delimitador CSV';
$string['csvexample'] = 'Ejemplo CSV';
$string['csvexample_desc'] = 'Formato de ejemplo CSV';
$string['csvexample_tip'] = 'Use este formato para importaciones';
$string['csvfile'] = 'Archivo CSV';
$string['csvformat'] = 'Formato CSV';
$string['csvformat_desc'] = 'Formato CSV esperado';
$string['csvimporterror'] = 'Error de importación CSV';
$string['csvinvalidtype'] = 'Tipo inválido en CSV';
$string['csvlineerror'] = 'Error en línea {$a}';
$string['csvusernotfound'] = 'Usuario no encontrado';
$string['currentpassword'] = 'Contraseña actual';
$string['currentpassword_invalid'] = 'Contraseña actual inválida';
$string['currentpassword_required'] = 'Se requiere contraseña actual';
$string['currentstatus'] = 'Estado actual';
$string['currentworkload'] = 'Carga de trabajo actual';
$string['dailyapplications'] = 'Postulaciones diarias';

// Cadenas del dashboard.
$string['dashboard_admin_welcome'] = 'Bienvenido, Administrador';
$string['dashboard_applicant_welcome'] = 'Bienvenido al Tablero de Empleo';
$string['dashboard_manager_welcome'] = 'Bienvenido, Gestor';
$string['dashboard_reviewer_welcome'] = 'Bienvenido, Revisor';

// Exportación de datos.
$string['dataexport'] = 'Exportación de datos';
$string['dataexport:consent'] = 'Información de consentimiento';
$string['dataexport:exportdate'] = 'Fecha de exportación';
$string['dataexport:personal'] = 'Datos personales';
$string['dataexport:title'] = 'Reporte de exportación de datos';
$string['dataexport:userinfo'] = 'Información del usuario';
$string['dataretentiondays'] = 'Días de retención de datos';
$string['datatorexport'] = 'Datos a exportar';
$string['datatreatmentpolicytitle'] = 'Política de tratamiento de datos';

// Fecha y hora.
$string['dateandtime'] = 'Fecha y hora';
$string['dateapplied'] = 'Fecha de postulación';
$string['datefrom'] = 'Desde fecha';
$string['dates'] = 'Fechas';
$string['datesubmitted'] = 'Fecha de envío';
$string['dateto'] = 'Hasta fecha';
$string['days'] = 'días';
$string['daysleft'] = 'Días restantes';
$string['daysremaining'] = '{$a} días restantes';
$string['deadlineprogress'] = 'Progreso de plazo';
$string['deadlinewarning'] = 'Solo quedan {$a} días para postularse';
$string['deadlinewarning_title'] = '¡Fecha límite próxima!';

// Declaración.
$string['declaration'] = 'Declaración';
$string['declarationaccept'] = 'Acepto la declaración';
$string['declarationrequired'] = 'La declaración es requerida';
$string['declarationtext'] = 'Texto de declaración';
$string['defaultdatatreatmentpolicy'] = 'Política de tratamiento de datos por defecto';
$string['defaultexemptiontype'] = 'Tipo de exención por defecto';
$string['defaultmaxagedays'] = 'Antigüedad máxima de documento por defecto (días)';
$string['defaultstatus'] = 'Estado por defecto';
$string['defaultvalidfrom'] = 'Válido desde por defecto';
$string['defaultvaliduntil'] = 'Válido hasta por defecto';
$string['department'] = 'Departamento';
$string['desirable'] = 'Deseable';
$string['digitalsignature'] = 'Firma digital';
$string['disabled'] = 'Deshabilitado';

// Condiciones de documento.
$string['doc_condition_iser_exempt'] = 'Exento ISER';
$string['doc_condition_men_only'] = 'Solo hombres';
$string['doc_condition_profession_exempt'] = 'Profesión exenta';
$string['doc_condition_women_only'] = 'Solo mujeres';

// Categorías de documento.
$string['doccategory_academic'] = 'Académico';
$string['doccategory_background'] = 'Antecedentes';
$string['doccategory_financial'] = 'Financiero';
$string['doccategory_health'] = 'Salud';
$string['doccategory_identity'] = 'Identidad';
$string['doccategory_professional'] = 'Profesional';
$string['docrequirements'] = 'Requisitos del documento';

// Estado de documento.
$string['docstatus:approved'] = 'Aprobado';
$string['docstatus:pending'] = 'Pendiente';
$string['docstatus:rejected'] = 'Rechazado';

// Nombres de tipos de documento.
$string['doctype_antecedentes_contraloria'] = 'Antecedentes Contraloría';
$string['doctype_antecedentes_policia'] = 'Antecedentes Policía';
$string['doctype_antecedentes_procuraduria'] = 'Antecedentes Procuraduría';
$string['doctype_cedula'] = 'Cédula de ciudadanía';
$string['doctype_certificado_medico'] = 'Certificado médico';
$string['doctype_cuenta_bancaria'] = 'Certificado cuenta bancaria';
$string['doctype_eps'] = 'Certificado EPS';
$string['doctype_isrequired_help'] = 'Este documento es obligatorio';
$string['doctype_libreta_militar'] = 'Libreta militar';
$string['doctype_pension'] = 'Certificado pensión';
$string['doctype_rnmc'] = 'Certificado RNMC';
$string['doctype_rut'] = 'RUT';
$string['doctype_sigep'] = 'Formato SIGEP';
$string['doctype_tarjeta_profesional'] = 'Tarjeta profesional';
$string['doctype_titulo_postgrado'] = 'Título de posgrado';
$string['doctype_titulo_pregrado'] = 'Título de pregrado';
$string['doctypecreated'] = 'Tipo de documento creado';
$string['doctypedeleted'] = 'Tipo de documento eliminado';
$string['doctypelist'] = 'Tipos de documento';
$string['doctypes_desc'] = 'Gestionar tipos de documento';
$string['doctypeshelp'] = 'Configurar documentos requeridos';
$string['doctypeupdated'] = 'Tipo de documento actualizado';

// Acciones de documento.
$string['documentchecklist'] = 'Lista de verificación de documentos';
$string['documentexpired'] = 'Documento ha expirado';
$string['documentinfo'] = 'Información del documento';
$string['documentissuedate'] = 'Fecha de expedición';
$string['documentlist'] = 'Lista de documentos';
$string['documentnotfound'] = 'Documento no encontrado';
$string['documentnumber'] = 'Número de documento';
$string['documentpreview'] = 'Vista previa del documento';
$string['documentref'] = 'Referencia de documento';
$string['documentref_desc'] = 'Referencia externa del documento';
$string['documentrejected'] = 'Documento rechazado';
$string['documentrequired'] = 'Este documento es requerido';
$string['documentreuploaded'] = 'Documento recargado';
$string['documentsapproved'] = 'Documentos aprobados';
$string['documentsettings'] = 'Configuración de documentos';
$string['documentshelp'] = 'Ayuda de documentos';
$string['documentsrejected'] = 'Documentos rechazados';
$string['documentsremaining'] = '{$a} documentos restantes';
$string['documentsreviewed'] = 'Documentos revisados';
$string['documentsvalidated'] = 'Documentos validados';
$string['documenttype'] = 'Tipo de documento';
$string['documenttypes'] = 'Tipos de documento';
$string['documentvalidated'] = 'Documento validado';
$string['downloadcsvtemplate'] = 'Descargar plantilla CSV';
$string['downloadtoview'] = 'Descargar para ver';
$string['draft'] = 'Borrador';
$string['dryrunmode'] = 'Modo de prueba';
$string['dryrunresults'] = 'Resultados de prueba';
$string['duration'] = 'Duración';
$string['edit_template'] = 'Editar plantilla';
$string['editconvocatoria'] = 'Editar convocatoria';
$string['editprofile'] = 'Editar perfil';
$string['education'] = 'Educación';
$string['educationlevel'] = 'Nivel educativo';
$string['email_action_reupload'] = 'Por favor recargue el documento';
$string['email_updated'] = 'Correo actualizado';
$string['emailnotmatch'] = 'Los correos no coinciden';
$string['emailtemplates'] = 'Plantillas de correo';
$string['emailtemplates_desc'] = 'Configurar notificaciones por correo';
$string['enableapi'] = 'Habilitar API';
$string['enabled'] = 'Habilitado';
$string['enableddoctypes'] = 'Tipos de documento habilitados';
$string['enableencryption'] = 'Habilitar encriptación';
$string['enablepublicpage'] = 'Habilitar página pública';
$string['enablepublicpage_desc'] = 'Mostrar página de vacantes públicas';
$string['enableselfregistration'] = 'Habilitar auto-registro';
$string['enableselfregistration_desc'] = 'Permitir a usuarios crear cuentas';
$string['encoding'] = 'Codificación';
$string['encryption:backupinstructions'] = 'Respaldar clave de encriptación';
$string['encryption:nokeytobackup'] = 'No hay clave de encriptación para respaldar';
$string['enddate'] = 'Fecha de fin';
$string['entries'] = 'entradas';
$string['epsmaxdays'] = 'Antigüedad máxima certificado EPS (días)';

// Mensajes de error.
$string['error:alreadyapplied'] = 'Ya se ha postulado';
$string['error:applicationlimitreached'] = 'Límite de postulaciones alcanzado';
$string['error:cannotdelete_hasapplications'] = 'No se puede eliminar: tiene postulaciones';
$string['error:cannotdeleteconvocatoria'] = 'No se puede eliminar convocatoria';
$string['error:cannotreopenconvocatoria'] = 'No se puede reabrir convocatoria';
$string['error:codealreadyexists'] = 'El código ya existe';
$string['error:codeexists'] = 'Este código ya está en uso';
$string['error:consentrequired'] = 'El consentimiento es requerido';
$string['error:convocatoriacodeexists'] = 'El código de convocatoria ya existe';
$string['error:convocatoriadatesinvalid'] = 'Fechas de convocatoria inválidas';
$string['error:convocatoriahasnovacancies'] = 'La convocatoria no tiene vacantes';
$string['error:convocatoriarequired'] = 'La convocatoria es requerida';
$string['error:doctypeinuse'] = 'El tipo de documento está en uso';
$string['error:invalidage'] = 'Edad inválida';
$string['error:invalidcode'] = 'Código inválido';
$string['error:invaliddates'] = 'Fechas inválidas';
$string['error:invalidpublicationtype'] = 'Tipo de publicación inválido';
$string['error:invalidstatus'] = 'Estado inválido';
$string['error:invalidurl'] = 'URL inválida';
$string['error:occasionalrequiresexperience'] = 'Posición ocasional requiere experiencia';
$string['error:pastdate'] = 'La fecha no puede estar en el pasado';
$string['error:requiredfield'] = 'Este campo es requerido';
$string['error:schedulingconflict'] = 'Conflicto de programación';
$string['error:singleapplicationonly'] = 'Solo se permite una postulación';
$string['error:vacancyclosed'] = 'La vacante está cerrada';
$string['error:vacancynotfound'] = 'Vacante no encontrada';
$string['evaluations'] = 'Evaluaciones';
$string['evaluatorshelp'] = 'Ayuda de evaluadores';

// Cadenas de eventos.
$string['event:applicationcreated'] = 'Postulación creada';
$string['event:documentuploaded'] = 'Documento cargado';
$string['event:statuschanged'] = 'Estado cambiado';
$string['event:vacancyclosed'] = 'Vacante cerrada';
$string['event:vacancycreated'] = 'Vacante creada';
$string['event:vacancydeleted'] = 'Vacante eliminada';
$string['event:vacancypublished'] = 'Vacante publicada';
$string['event:vacancyupdated'] = 'Vacante actualizada';
$string['example'] = 'Ejemplo';

// Cadenas de exención.
$string['exempteddocs'] = 'Documentos exentos';
$string['exempteddocs_desc'] = 'Documentos exentos de requisitos';
$string['exempteddoctypes'] = 'Tipos de documento exentos';
$string['exemption'] = 'Exención';
$string['exemptionactive'] = 'Exención activa';
$string['exemptionapplied'] = 'Exención aplicada';
$string['exemptioncreated'] = 'Exención creada';
$string['exemptiondetails'] = 'Detalles de exención';
$string['exemptionerror'] = 'Error de exención';
$string['exemptionlist'] = 'Lista de exenciones';
$string['exemptionnotice'] = 'Aviso de exención';
$string['exemptionreason'] = 'Razón de exención';
$string['exemptionreduceddocs'] = 'Requisitos de documentos reducidos';
$string['exemptionrevoked'] = 'Exención revocada';
$string['exemptionrevokeerror'] = 'Error revocando exención';
$string['exemptions'] = 'Exenciones';
$string['exemptiontype'] = 'Tipo de exención';
$string['exemptiontype_desc'] = 'Tipo de exención';
$string['exemptiontype_documentos_recientes'] = 'Documentos recientes';
$string['exemptiontype_historico_iser'] = 'Histórico ISER';
$string['exemptiontype_recontratacion'] = 'Recontratación';
$string['exemptiontype_traslado_interno'] = 'Traslado interno';
$string['exemptionupdated'] = 'Exención actualizada';
$string['exemptionusagehistory'] = 'Historial de uso de exención';
$string['existingvacancycommittee'] = 'Comité de vacante existente';
$string['expired'] = 'Expirado';
$string['expiredexemptions'] = 'Exenciones expiradas';
$string['explore'] = 'Explorar';
$string['explorevacancias'] = 'Explorar vacantes';
$string['export'] = 'Exportar';
$string['exportcsv'] = 'Exportar a CSV';
$string['exportdata'] = 'Exportar datos';
$string['exportdata_desc'] = 'Exportar datos de postulaciones';
$string['exportdownload'] = 'Descargar exportación';
$string['exporterror'] = 'Error de exportación';
$string['exportexcel'] = 'Exportar a Excel';
$string['exportwarning_files'] = 'Advertencia: archivos adjuntos no incluidos';
$string['externalurl'] = 'URL externa';

// Facultad y archivos.
$string['facultieswithoutcommittee'] = 'Facultades sin comité';
$string['facultycommitteedefaultname'] = 'Comité de facultad';
$string['facultyvacancies'] = 'Vacantes de facultad';
$string['filename'] = 'Nombre de archivo';
$string['files'] = 'Archivos';
$string['fullexport'] = 'Exportación completa';
$string['fullexport_info'] = 'Exportar todos los datos';
$string['fullname'] = 'Nombre completo';
$string['gendercondition'] = 'Condición de género';
$string['generalsettings'] = 'Configuración general';
$string['generatedon'] = 'Generado el';
$string['gotocreateconvocatoria'] = 'Crear nueva convocatoria';

// Directrices.
$string['guideline1'] = 'Revise cuidadosamente los requisitos de documentos';
$string['guideline2'] = 'Cargue documentos legibles en formato PDF';
$string['guideline3'] = 'Asegúrese de que la información personal sea correcta';
$string['guideline4'] = 'Envíe antes de la fecha límite';
$string['guideline_review1'] = 'Verificar autenticidad del documento';
$string['guideline_review2'] = 'Revisar todos los items de la lista';
$string['guideline_review3'] = 'Proporcionar razones claras para rechazo';
$string['guideline_review4'] = 'Notificar al postulante de cualquier problema';
$string['hasnote'] = 'Tiene notas';
$string['html_support'] = 'HTML soportado';

// Importar.
$string['import'] = 'Importar';
$string['importcomplete'] = 'Importación completa';
$string['importdata'] = 'Importar datos';
$string['importdata_desc'] = 'Importar datos desde archivo';
$string['importedapplications'] = 'Postulaciones importadas';
$string['importedconvocatorias'] = 'Convocatorias importadas';
$string['importeddoctypes'] = 'Tipos de documento importados';
$string['importeddocuments'] = 'Documentos importados';
$string['importedemails'] = 'Correos importados';
$string['importedexemptions'] = 'Exenciones importadas';
$string['importedfiles'] = 'Archivos importados';
$string['importednote'] = 'Nota de importación';
$string['importedsettings'] = 'Configuración importada';
$string['importedskipped'] = 'Omitidos';
$string['importedsuccess'] = 'Importado exitosamente';
$string['importedvacancies'] = 'Vacantes importadas';
$string['importerror'] = 'Error de importación';
$string['importerror_alreadyexempt'] = 'El usuario ya tiene exención';
$string['importerror_createfailed'] = 'Error al crear registro';
$string['importerror_usernotfound'] = 'Usuario no encontrado';
$string['importerror_vacancyexists'] = 'La vacante ya existe';
$string['importerrors'] = 'Errores de importación';
$string['importexemptions'] = 'Importar exenciones';
$string['importingfrom'] = 'Importando desde';
$string['importinstructions'] = 'Instrucciones de importación';
$string['importinstructionstext'] = 'Siga el formato CSV indicado';
$string['importoptions'] = 'Opciones de importación';
$string['importresults'] = 'Resultados de importación';
$string['importupload'] = 'Cargar archivo';
$string['importvacancies'] = 'Importar vacantes';
$string['importvacancies_desc'] = 'Importar vacantes desde CSV';
$string['importvacancies_help'] = 'Ayuda para importar vacantes';
$string['importwarning'] = 'Advertencia de importación';
$string['inprogress'] = 'En progreso';
$string['inputtype'] = 'Tipo de entrada';
$string['inputtype_file'] = 'Carga de archivo';
$string['inputtype_number'] = 'Número';
$string['inputtype_text'] = 'Texto';
$string['inputtype_url'] = 'URL';
$string['install_defaults'] = 'Instalar valores por defecto';
$string['institutionname'] = 'Nombre de institución';
$string['internal'] = 'Interno';

// Entrevista.
$string['interviewcancelled'] = 'Entrevista cancelada';
$string['interviewcompleted'] = 'Entrevista completada';
$string['interviewdate'] = 'Fecha de entrevista';
$string['interviewers'] = 'Entrevistadores';
$string['interviewfeedback'] = 'Retroalimentación de entrevista';
$string['interviewinstructions'] = 'Instrucciones de entrevista';
$string['interviews'] = 'Entrevistas';
$string['interviewscheduleerror'] = 'Error programando entrevista';
$string['interviewtype'] = 'Tipo de entrevista';
$string['interviewtype_inperson'] = 'Presencial';
$string['interviewtype_phone'] = 'Telefónica';
$string['interviewtype_video'] = 'Videollamada';
$string['invalidmigrationfile'] = 'Archivo de migración inválido';
$string['invalidrole'] = 'Rol inválido';
$string['iomad_department'] = 'Departamento IOMAD';
$string['iomadoptions'] = 'Opciones IOMAD';
$string['iomadsettings'] = 'Configuración IOMAD';
$string['iserexempted'] = 'Exento ISER';
$string['iserexempted_help'] = 'Este documento está exento para usuarios ISER';
$string['items'] = 'elementos';
$string['jobboard'] = 'Bolsa de Empleo';
$string['legacyvacancycommittee'] = 'Comité de vacante heredado';
$string['location'] = 'Ubicación';
$string['locationorurl'] = 'Ubicación o URL';
$string['loginrequiredtoapply'] = 'Inicie sesión para postularse';
$string['mainmenutitle'] = 'Bolsa de Empleo';
$string['mainmenutitle_desc'] = 'Título en el menú principal';

// Cadenas de gestión.
$string['manageapplications'] = 'Gestionar postulaciones';
$string['managecommittees'] = 'Gestionar comités';
$string['manageconvocatorias'] = 'Gestionar convocatorias';
$string['managedoctypes'] = 'Gestionar tipos de documento';
$string['manageexemptions'] = 'Gestionar exenciones';
$string['manageexemptions_desc'] = 'Agregar y gestionar exenciones';
$string['manageroles'] = 'Gestionar roles';
$string['manageroles_desc'] = 'Configurar roles de comité';
$string['manageusers'] = 'Gestionar usuarios';
$string['managevacancies'] = 'Gestionar vacantes';
$string['manualassign'] = 'Asignación manual';
$string['markedasnoshow'] = 'Marcado como inasistente';
$string['markednoshow'] = 'Inasistente';
$string['maxapplicationsperuser'] = 'Postulaciones máximas por usuario';
$string['maxfilesize'] = 'Tamaño máximo de archivo';
$string['maxperreviewer'] = 'Máximo por revisor';
$string['memberadded'] = 'Miembro agregado';
$string['memberadderror'] = 'Error agregando miembro';
$string['membercount'] = 'Cantidad de miembros';
$string['memberremoved'] = 'Miembro eliminado';
$string['memberremoveerror'] = 'Error eliminando miembro';
$string['members'] = 'Miembros';
$string['menonly'] = 'Solo hombres';

// Migración.
$string['migrate_includes_applications'] = 'Incluye postulaciones';
$string['migrate_includes_convocatorias'] = 'Incluye convocatorias';
$string['migrate_includes_doctypes'] = 'Incluye tipos de documento';
$string['migrate_includes_vacancies'] = 'Incluye vacantes';
$string['migrateplugin'] = 'Migrar plugin';
$string['migrateplugin_desc'] = 'Migrar desde versión anterior';
$string['migrateplugin_full_desc'] = 'Migración completa del plugin';
$string['migrationfile'] = 'Archivo de migración';
$string['migrationinfo_desc'] = 'Información de migración';
$string['migrationinfo_title'] = 'Migración';

// Modalidad.
$string['modality:distancia'] = 'Distancia';
$string['modality:hibrida'] = 'Híbrida';
$string['modality:presencial'] = 'Presencial';
$string['modality:virtual'] = 'Virtual';
$string['modifiedby'] = 'Modificado por';
$string['multi_tenant'] = 'Multi-inquilino';
$string['multipledocs_notice'] = 'Se permiten múltiples documentos';
$string['myapplicationcount'] = 'Mis postulaciones';
$string['myapplications_desc'] = 'Ver sus postulaciones';
$string['mypendingreviews'] = 'Mis revisiones pendientes';
$string['myreviews'] = 'Mis revisiones';
$string['myreviews_desc'] = 'Ver sus asignaciones de revisión';

// Navegación.
$string['navigationsettings'] = 'Configuración de navegación';
$string['navigationsettings_desc'] = 'Configurar opciones de navegación';
$string['needhelp'] = '¿Necesita ayuda?';
$string['needsattention'] = 'Requiere atención';
$string['newdocument'] = 'Nuevo documento';
$string['newvacancy'] = 'Nueva vacante';
$string['nextapplication'] = 'Siguiente postulación';
$string['no_templates'] = 'No se encontraron plantillas';
$string['noapplicationsfound'] = 'No se encontraron postulaciones';
$string['noassignments'] = 'Sin asignaciones';
$string['nocommitteeforthisvacancy'] = 'Sin comité para esta vacante';
$string['nocommittees'] = 'Sin comités';
$string['noconvocatorias'] = 'Sin convocatorias';
$string['noconvocatoriasavailable'] = 'No hay convocatorias disponibles';
$string['nodata'] = 'Sin datos';
$string['nodoctypes'] = 'Sin tipos de documento';
$string['nodocuments'] = 'Sin documentos';
$string['nodocumentspending'] = 'Sin documentos pendientes';
$string['nodocumentstoreview'] = 'Sin documentos para revisar';
$string['noexemptions'] = 'Sin exenciones';
$string['noexemptionusage'] = 'Sin uso de exención';
$string['noexpiry'] = 'Sin vencimiento';
$string['noobservations'] = 'Sin observaciones';
$string['noreason'] = 'Sin razón proporcionada';
$string['norejections'] = 'Sin rechazos';
$string['noreviewers'] = 'Sin revisores';
$string['nosecretaryoptional'] = 'Secretario opcional';
$string['noshow'] = 'Inasistencia';
$string['notes'] = 'Notas';
$string['notes_desc'] = 'Notas adicionales';
$string['notifications'] = 'Notificaciones';
$string['nounassignedapplications'] = 'Sin postulaciones no asignadas';
$string['nousersassigned'] = 'Sin usuarios asignados';
$string['novacancies'] = 'Sin vacantes';
$string['novacanciesfound'] = 'No se encontraron vacantes';
$string['numdocs'] = 'Número de documentos';
$string['of'] = 'de';

// Abrir y resumen.
$string['openconvocatoria'] = 'Abrir convocatoria';
$string['opendate'] = 'Fecha de apertura';
$string['openmigrationtool'] = 'Abrir herramienta de migración';
$string['openvacancies'] = 'Vacantes abiertas';
$string['optionalcolumns'] = 'Columnas opcionales';
$string['optionalnotes'] = 'Notas opcionales';
$string['overallrating'] = 'Calificación general';
$string['overview'] = 'Resumen';
$string['overwriteexisting'] = 'Sobrescribir existentes';
$string['password_change_optional'] = 'Deje en blanco para mantener contraseña actual';
$string['password_updated'] = 'Contraseña actualizada';
$string['passwordsdiffer'] = 'Las contraseñas no coinciden';

// Pendiente.
$string['pending'] = 'Pendiente';
$string['pending_docs_alert'] = 'Documentos pendientes de revisión';
$string['pendingassignment'] = 'Asignación pendiente';
$string['pendingassignments'] = 'Asignaciones pendientes';
$string['pendingbytype'] = 'Pendientes por tipo';
$string['pendingdocs'] = 'Documentos pendientes';
$string['pendingdocuments'] = 'Documentos pendientes';
$string['pendingreview'] = 'Revisión pendiente';
$string['pendingreviews'] = 'Revisiones pendientes';
$string['pendingreviews_alert'] = 'Tiene revisiones pendientes';
$string['pendingvalidation'] = 'Validación pendiente';
$string['pensionmaxdays'] = 'Antigüedad máxima certificado pensión (días)';
$string['percentage'] = 'Porcentaje';
$string['period'] = 'Período';
$string['personalinfo'] = 'Información personal';
$string['placeholders'] = 'Marcadores';
$string['placeholders_help'] = 'Marcadores disponibles para plantillas';
$string['pluginsettings'] = 'Configuración del plugin';
$string['pluginsettings_desc'] = 'Configurar ajustes del plugin';
$string['positions'] = 'Posiciones';
$string['previewconfirm'] = 'Confirmar vista previa';
$string['previewdocument'] = 'Vista previa de documento';
$string['previewmode'] = 'Modo vista previa';
$string['previewmodenotice'] = 'Modo vista previa - cambios no guardados';
$string['previewonly'] = 'Solo vista previa';
$string['previewtotal'] = 'Total a previsualizar';
$string['previewunavailable'] = 'Vista previa no disponible';
$string['previousapplication'] = 'Postulación anterior';
$string['professionexempt'] = 'Profesión exenta';
$string['profilereview'] = 'Revisión de perfil';
$string['profilereview_info'] = 'Revise y actualice su perfil';
$string['public'] = 'Público';
$string['publicationtype'] = 'Tipo de publicación';
$string['publicationtype:internal'] = 'Interno';
$string['publicationtype:public'] = 'Público';
$string['publicpagedesc'] = 'Descripción de página pública';
$string['publicpagedescription'] = 'Descripción de página pública';
$string['publicpagedescription_desc'] = 'Descripción para página pública';
$string['publicpagesettings'] = 'Configuración de página pública';
$string['publicpagesettings_desc'] = 'Configurar página pública';
$string['publicpagetitle'] = 'Título de página pública';
$string['publicpagetitle_desc'] = 'Título para página pública';
$string['publicvacancies'] = 'Vacantes públicas';
$string['publish'] = 'Publicar';
$string['published'] = 'Publicado';
$string['publishedvacancies'] = 'Vacantes publicadas';

// Acciones rápidas.
$string['quickactions'] = 'Acciones rápidas';
$string['quicktips'] = 'Consejos rápidos';

// Calificaciones.
$string['rating_excellent'] = 'Excelente';
$string['rating_fair'] = 'Regular';
$string['rating_good'] = 'Bueno';
$string['rating_poor'] = 'Deficiente';
$string['rating_verygood'] = 'Muy bueno';
$string['readytoapply'] = 'Listo para postularse';

// reCAPTCHA.
$string['recaptcha_enabled'] = 'Habilitar reCAPTCHA';
$string['recaptcha_enabled_desc'] = 'Requerir reCAPTCHA para registro';
$string['recaptcha_failed'] = 'Verificación reCAPTCHA falló';
$string['recaptcha_required'] = 'Por favor complete el reCAPTCHA';
$string['recaptcha_secretkey'] = 'Clave secreta';
$string['recaptcha_secretkey_desc'] = 'Clave secreta reCAPTCHA';
$string['recaptcha_sitekey'] = 'Clave del sitio';
$string['recaptcha_sitekey_desc'] = 'Clave del sitio reCAPTCHA';
$string['recaptcha_v2'] = 'reCAPTCHA v2';
$string['recaptcha_v3'] = 'reCAPTCHA v3';
$string['recaptcha_v3_threshold'] = 'Umbral de puntuación';
$string['recaptcha_v3_threshold_desc'] = 'Puntuación mínima para v3';
$string['recaptcha_version'] = 'Versión reCAPTCHA';
$string['recaptcha_version_desc'] = 'Seleccionar versión reCAPTCHA';
$string['recaptchasettings'] = 'Configuración reCAPTCHA';
$string['recaptchasettings_desc'] = 'Configurar reCAPTCHA';

// Recomendaciones.
$string['recommend_furtherreview'] = 'Recomendar revisión adicional';
$string['recommend_hire'] = 'Recomendar contratación';
$string['recommend_reject'] = 'Recomendar rechazo';
$string['recommendation'] = 'Recomendación';
$string['recordsperpage'] = 'Registros por página';
$string['refresh'] = 'Actualizar';

// Rechazar.
$string['reject'] = 'Rechazar';
$string['rejectdocument'] = 'Rechazar documento';
$string['rejected'] = 'Rechazado';
$string['rejecteddocuments'] = 'Documentos rechazados';
$string['rejectionreason'] = 'Razón de rechazo';
$string['rejectionreasons'] = 'Razones de rechazo';
$string['rejectreason'] = 'Razón del rechazo';
$string['rejectreason_expired'] = 'El documento ha expirado';
$string['rejectreason_illegible'] = 'El documento es ilegible';
$string['rejectreason_incomplete'] = 'El documento está incompleto';
$string['rejectreason_mismatch'] = 'La información no coincide';
$string['rejectreason_placeholder'] = 'Ingrese razón de rechazo';
$string['rejectreason_wrongtype'] = 'Tipo de documento incorrecto';
$string['rejectselected'] = 'Rechazar seleccionados';
$string['removemember'] = 'Eliminar miembro';
$string['reopen'] = 'Reabrir';
$string['reopenconvocatoria'] = 'Reabrir convocatoria';

// Reportes.
$string['reportapplications'] = 'Reporte de postulaciones';
$string['reportdocuments'] = 'Reporte de documentos';
$string['reportoverview'] = 'Reporte general';
$string['reportreviewers'] = 'Reporte de revisores';
$string['reports_desc'] = 'Ver y exportar reportes';
$string['reportsanddata'] = 'Reportes y datos';
$string['reporttimeline'] = 'Reporte de línea de tiempo';
$string['requiredcolumns'] = 'Columnas requeridas';
$string['requireddoctypes'] = 'Tipos de documento requeridos';
$string['requirements'] = 'Requisitos';
$string['rescheduledby'] = 'Reprogramado por';
$string['reschedulednote'] = 'Nota de reprogramación';
$string['reset_to_default'] = 'Restablecer a valores por defecto';
$string['restarttour'] = 'Reiniciar tour';
$string['result'] = 'Resultado';
$string['reuploaddocument'] = 'Recargar documento';
$string['reuploadhelp'] = 'Cargar una versión corregida';

// Revisar.
$string['review_dashboard_desc'] = 'Panel de revisión';
$string['reviewall'] = 'Revisar todos';
$string['reviewallapproved'] = 'Todas las revisiones aprobadas';
$string['reviewapplication'] = 'Revisar postulación';
$string['reviewapplications'] = 'Revisar postulaciones';
$string['reviewdocuments'] = 'Revisar documentos';
$string['reviewed'] = 'Revisado';
$string['reviewedby'] = 'Revisado por';
$string['reviewerassigned'] = 'Revisor asignado';
$string['reviewerperformance'] = 'Desempeño del revisor';
$string['reviewertasks'] = 'Tareas del revisor';
$string['reviewerunassigned'] = 'Revisor desasignado';
$string['reviewerworkload'] = 'Carga de trabajo del revisor';
$string['reviewguidelines'] = 'Directrices de revisión';
$string['reviewhasrejected'] = 'La revisión tiene rechazos';
$string['reviewhelp_text'] = 'Texto de ayuda de revisión';
$string['reviewobservations'] = 'Observaciones de revisión';
$string['reviewobservations_placeholder'] = 'Ingrese observaciones';
$string['reviewoverview'] = 'Resumen de revisión';
$string['reviewprogress'] = 'Progreso de revisión';
$string['reviewsteps_tooltip'] = 'Pasos de revisión';
$string['reviewsubmitted'] = 'Revisión enviada';
$string['reviewsubmitted_with_notification'] = 'Revisión enviada y postulante notificado';
$string['reviewtips'] = 'Consejos de revisión';

// Revocar.
$string['revoke'] = 'Revocar';
$string['revoked'] = 'Revocado';
$string['revokedby'] = 'Revocado por';
$string['revokedexemptions'] = 'Exenciones revocadas';
$string['revokeexemption'] = 'Revocar exención';
$string['revokereason'] = 'Razón de revocación';

// Roles.
$string['role_administrator'] = 'Administrador';
$string['role_applicant'] = 'Postulante';
$string['role_chair'] = 'Presidente del comité';
$string['role_committee'] = 'Miembro del comité';
$string['role_committee_desc'] = 'Puede evaluar candidatos';
$string['role_coordinator'] = 'Coordinador';
$string['role_coordinator_desc'] = 'Coordina el proceso de selección';
$string['role_evaluator'] = 'Evaluador';
$string['role_manager'] = 'Gestor';
$string['role_observer'] = 'Observador';
$string['role_reviewer_desc'] = 'Puede revisar y validar documentos';
$string['role_secretary'] = 'Secretario del comité';
$string['rolenotcreated'] = 'Rol no creado';
$string['row'] = 'Fila';
$string['samplecsv'] = 'CSV de ejemplo';
$string['saveresults'] = 'Guardar resultados';
$string['scheduledinterviews'] = 'Entrevistas programadas';
$string['scheduleinterview'] = 'Programar entrevista';
$string['schedulenewinterview'] = 'Programar nueva entrevista';

// Buscar.
$string['searchapplicant'] = 'Buscar postulante';
$string['searchbyusername'] = 'Buscar por nombre de usuario';
$string['searchuser'] = 'Buscar usuario';
$string['searchusers'] = 'Buscar usuarios';
$string['searchusersplaceholder'] = 'Escriba para buscar usuarios';
$string['searchvacancies'] = 'Buscar vacantes';
$string['securitysettings'] = 'Configuración de seguridad';
$string['selectall'] = 'Seleccionar todo';
$string['selectatleastone'] = 'Seleccione al menos uno';
$string['selectbackgrounddocs'] = 'Seleccionar documentos de antecedentes';
$string['selectcompany'] = 'Seleccionar empresa';
$string['selectcontracttype'] = 'Seleccionar tipo de contrato';
$string['selectconvocatoria'] = 'Seleccionar convocatoria';
$string['selectconvocatoriafirst'] = 'Seleccione una convocatoria primero';
$string['selectdepartment'] = 'Seleccionar departamento';
$string['selected'] = 'Seleccionado';
$string['selectfaculty'] = 'Seleccionar facultad';
$string['selectidentitydocs'] = 'Seleccionar documentos de identidad';
$string['selectinterviewers'] = 'Seleccionar entrevistadores';
$string['selectionrate'] = 'Tasa de selección';
$string['selectmodality'] = 'Seleccionar modalidad';
$string['selectmultiplehelp'] = 'Mantenga Ctrl para seleccionar múltiples';
$string['selectreason'] = 'Seleccionar razón';
$string['selectreviewer'] = 'Seleccionar revisor';
$string['selectroletoassign'] = 'Seleccionar rol a asignar';
$string['selecttype'] = 'Seleccionar tipo';
$string['selectusers'] = 'Seleccionar usuarios';

// Compartir.
$string['share'] = 'Compartir';
$string['shareonfacebook'] = 'Compartir en Facebook';
$string['shareonlinkedin'] = 'Compartir en LinkedIn';
$string['shareontwitter'] = 'Compartir en Twitter';
$string['sharepage'] = 'Compartir página';
$string['sharethisvacancy'] = 'Compartir esta vacante';
$string['showing'] = 'Mostrando';
$string['showingxofy'] = 'Mostrando {$a->from} a {$a->to} de {$a->total}';
$string['showinmainmenu'] = 'Mostrar en menú principal';
$string['showinmainmenu_desc'] = 'Mostrar bolsa de empleo en menú principal';
$string['showpublicnavlink'] = 'Mostrar enlace público de navegación';
$string['showpublicnavlink_desc'] = 'Mostrar enlace a vacantes públicas';
$string['signaturetoooshort'] = 'Firma muy corta';

// Cadenas de registro.
$string['signup_academic_header'] = 'Información académica';
$string['signup_account_header'] = 'Información de cuenta';
$string['signup_already_account'] = '¿Ya tiene una cuenta?';
$string['signup_applying_for'] = 'Postulándose a';
$string['signup_birthdate'] = 'Fecha de nacimiento';
$string['signup_birthdate_minage'] = 'Edad mínima requerida';
$string['signup_check_spam'] = 'Revise su carpeta de spam';
$string['signup_company_help'] = 'Seleccione su ubicación';
$string['signup_companyinfo'] = 'Información de ubicación';
$string['signup_contactinfo'] = 'Información de contacto';
$string['signup_createaccount'] = 'Crear cuenta';
$string['signup_dataaccuracy_accept'] = 'Confirmo que mi información es correcta';
$string['signup_dataaccuracy_required'] = 'Confirmación de exactitud de datos requerida';
$string['signup_datatreatment_accept'] = 'Acepto la política de tratamiento de datos';
$string['signup_datatreatment_required'] = 'Aceptación de tratamiento de datos requerida';
$string['signup_degree_title'] = 'Título de grado';
$string['signup_department_region'] = 'Departamento/Región';
$string['signup_doctype'] = 'Tipo de documento';
$string['signup_doctype_cc'] = 'Cédula de ciudadanía';
$string['signup_doctype_ce'] = 'Cédula de extranjería';
$string['signup_doctype_passport'] = 'Pasaporte';
$string['signup_doctype_pep'] = 'Permiso especial';
$string['signup_doctype_ppt'] = 'Permiso temporal';
$string['signup_edu_doctor'] = 'Doctorado';
$string['signup_edu_doctorate'] = 'Doctorado';
$string['signup_edu_especialista'] = 'Especialista';
$string['signup_edu_highschool'] = 'Bachiller';
$string['signup_edu_magister'] = 'Magíster';
$string['signup_edu_masters'] = 'Maestría';
$string['signup_edu_postdoctorate'] = 'Postdoctorado';
$string['signup_edu_profesional'] = 'Profesional';
$string['signup_edu_specialization'] = 'Especialización';
$string['signup_edu_technical'] = 'Técnico';
$string['signup_edu_technological'] = 'Tecnológico';
$string['signup_edu_tecnico'] = 'Técnico';
$string['signup_edu_tecnologo'] = 'Tecnólogo';
$string['signup_edu_undergraduate'] = 'Pregrado';
$string['signup_education_level'] = 'Nivel educativo';
$string['signup_email_instruction_1'] = 'Revise su bandeja de entrada';
$string['signup_email_instruction_2'] = 'Haga clic en el enlace de confirmación';
$string['signup_email_instruction_3'] = 'Complete su perfil';
$string['signup_email_instructions_title'] = 'Siguientes pasos';
$string['signup_error_creating'] = 'Error creando cuenta';
$string['signup_exp_1_3'] = '1-3 años';
$string['signup_exp_3_5'] = '3-5 años';
$string['signup_exp_5_10'] = '5-10 años';
$string['signup_exp_less_1'] = 'Menos de 1 año';
$string['signup_exp_more_10'] = 'Más de 10 años';
$string['signup_exp_none'] = 'Sin experiencia';
$string['signup_experience_years'] = 'Años de experiencia';
$string['signup_expertise_area'] = 'Área de experiencia';
$string['signup_gender'] = 'Género';
$string['signup_gender_female'] = 'Femenino';
$string['signup_gender_male'] = 'Masculino';
$string['signup_gender_other'] = 'Otro';
$string['signup_gender_prefer_not'] = 'Prefiero no decir';
$string['signup_idnumber'] = 'Número de documento';
$string['signup_idnumber_exists'] = 'Este número de documento ya está registrado';
$string['signup_idnumber_exists_as_user'] = 'Ya existe usuario con este documento';
$string['signup_idnumber_tooshort'] = 'El número de documento es muy corto';
$string['signup_intro'] = 'Cree su cuenta para postularse';
$string['signup_personalinfo'] = 'Información personal';
$string['signup_phone_home'] = 'Teléfono fijo';
$string['signup_phone_mobile'] = 'Teléfono móvil';
$string['signup_privacy_text'] = 'Texto de política de privacidad';
$string['signup_professional_profile'] = 'Perfil profesional';
$string['signup_required_fields'] = 'Campos requeridos';
$string['signup_step_academic'] = 'Académico';
$string['signup_step_account'] = 'Cuenta';
$string['signup_step_confirm'] = 'Confirmar';
$string['signup_step_contact'] = 'Contacto';
$string['signup_step_personal'] = 'Personal';
$string['signup_success_message'] = 'Cuenta creada. Revise {$a} para confirmación.';
$string['signup_success_title'] = 'Registro exitoso';
$string['signup_terms_accept'] = 'Acepto los términos y condiciones';
$string['signup_terms_required'] = 'Aceptación de términos requerida';
$string['signup_termsheader'] = 'Términos y condiciones';
$string['signup_title'] = 'Crear cuenta';
$string['signup_username_is_idnumber'] = 'Su usuario será su número de documento';

// Ordenar e iniciar.
$string['sortby'] = 'Ordenar por';
$string['sortorder'] = 'Orden de clasificación';
$string['startdate'] = 'Fecha de inicio';

// Cadenas de estado.
$string['status:assigned'] = 'Asignado';
$string['status:closed'] = 'Cerrado';
$string['status:draft'] = 'Borrador';
$string['status:published'] = 'Publicado';
$string['statushistory'] = 'Historial de estados';

// Pasos.
$string['step'] = 'Paso';
$string['step_complete'] = 'Completar';
$string['step_consent'] = 'Consentimiento';
$string['step_coverletter'] = 'Carta de presentación';
$string['step_documents'] = 'Documentos';
$string['step_examine'] = 'Examinar';
$string['step_profile'] = 'Perfil';
$string['step_submit'] = 'Enviar';
$string['step_validate'] = 'Validar';

// Enviar.
$string['submit'] = 'Enviar';
$string['submitapplication'] = 'Enviar postulación';
$string['submitreview'] = 'Enviar revisión';
$string['systemconfiguration'] = 'Configuración del sistema';
$string['systemmigration'] = 'Migración del sistema';

// Tareas.
$string['task:checkclosingvacancies'] = 'Verificar vacantes por cerrar';
$string['task:cleanupolddata'] = 'Limpiar datos antiguos';
$string['task:sendnotifications'] = 'Enviar notificaciones';

// Cadenas de plantilla.
$string['template_categories'] = 'Categorías de plantillas';
$string['template_category'] = 'Categoría de plantilla';
$string['template_content'] = 'Contenido de plantilla';
$string['template_delete_failed'] = 'Error al eliminar plantilla';
$string['template_deleted_success'] = 'Plantilla eliminada';
$string['template_description'] = 'Descripción de plantilla';
$string['template_disabled_success'] = 'Plantilla deshabilitada';
$string['template_enabled'] = 'Plantilla habilitada';
$string['template_enabled_desc'] = 'Habilitar esta plantilla';
$string['template_enabled_success'] = 'Plantilla habilitada';
$string['template_help_html'] = 'HTML soportado';
$string['template_help_placeholders'] = 'Use marcadores para contenido dinámico';
$string['template_help_tenant'] = 'Las plantillas pueden ser específicas por empresa';
$string['template_help_title'] = 'Ayuda de plantillas';
$string['template_info'] = 'Información de plantilla';
$string['template_preview_hint'] = 'La vista previa aparecerá aquí';
$string['template_priority'] = 'Prioridad de plantilla';
$string['template_reset_success'] = 'Plantilla restablecida';
$string['template_saved_success'] = 'Plantilla guardada';
$string['template_settings'] = 'Configuración de plantilla';
$string['templates_disabled'] = 'Plantillas deshabilitadas';
$string['templates_enabled'] = 'Plantillas habilitadas';
$string['templates_installed'] = 'Plantillas instaladas';

// Encabezados de tabla.
$string['thactions'] = 'Acciones';
$string['thcode'] = 'Código';
$string['thstatus'] = 'Estado';
$string['thtitle'] = 'Título';

// Consejos.
$string['tip_authentic'] = 'Asegure la autenticidad de documentos';
$string['tip_checkdocs'] = 'Revise todos los documentos cuidadosamente';
$string['tip_complete'] = 'Complete todos los campos';
$string['tip_deadline'] = 'Envíe antes de la fecha límite';
$string['tip_download'] = 'Descargue para revisión sin conexión';
$string['tip_legible'] = 'Los documentos deben ser legibles';
$string['tip_saveoften'] = 'Guarde su trabajo frecuentemente';
$string['title'] = 'Título';
$string['toggle_status'] = 'Cambiar estado';
$string['togglepreview'] = 'Alternar vista previa';

// Totales.
$string['total'] = 'Total';
$string['total_templates'] = 'Total de plantillas';
$string['totalapplications'] = 'Total de postulaciones';
$string['totalassigned'] = 'Total asignados';
$string['totalassignedusers'] = 'Total de usuarios asignados';
$string['totalcommittees'] = 'Total de comités';
$string['totalcommmembers'] = 'Total de miembros de comité';
$string['totalconvocatorias'] = 'Total de convocatorias';
$string['totaldoctypes'] = 'Total de tipos de documento';
$string['totaldocuments'] = 'Total de documentos';
$string['totalexemptions'] = 'Total de exenciones';
$string['totalpositions'] = 'Total de posiciones';
$string['totalvacancies'] = 'Total de vacantes';
$string['type'] = 'Tipo';

// Desasignar.
$string['unassign'] = 'Desasignar';
$string['unassignedapplications'] = 'Postulaciones no asignadas';
$string['unknownvacancy'] = 'Vacante desconocida';
$string['unpublish'] = 'Despublicar';

// Actualizar.
$string['update_username'] = 'Actualizar nombre de usuario';
$string['update_username_desc'] = 'Permitir actualización de nombre de usuario';
$string['updateexisting'] = 'Actualizar existentes';
$string['updateprofile_intro'] = 'Actualice su información de perfil';
$string['updateprofile_submit'] = 'Actualizar perfil';
$string['updateprofile_success'] = 'Perfil actualizado exitosamente';
$string['updateprofile_title'] = 'Actualizar perfil';
$string['updatestatus'] = 'Actualizar estado';

// Cargar.
$string['uploaddocument'] = 'Cargar documento';
$string['uploaded'] = 'Cargado';
$string['uploadeddocuments'] = 'Documentos cargados';
$string['uploadfailed'] = 'Carga fallida';
$string['uploadnewfile'] = 'Cargar nuevo archivo';
$string['urgent'] = 'Urgente';
$string['useridentifier'] = 'Identificador de usuario';
$string['username_differs_idnumber'] = 'Nombre de usuario difiere del número de documento';
$string['username_updated'] = 'Nombre de usuario actualizado';
$string['usernotfound'] = 'Usuario no encontrado';
$string['usersassigned'] = 'Usuarios asignados';
$string['usersassignedcount'] = '{$a} usuarios asignados';
$string['userunassigned'] = 'Usuario desasignado';

// Vacantes.
$string['vacancies_created'] = 'Vacantes creadas';
$string['vacancies_dashboard_desc'] = 'Gestionar todas las vacantes';
$string['vacancies_skipped'] = 'Vacantes omitidas';
$string['vacancies_updated'] = 'Vacantes actualizadas';
$string['vacanciesavailable'] = 'Vacantes disponibles';
$string['vacanciesforconvocatoria'] = 'Vacantes para esta convocatoria';
$string['vacanciesfound'] = '{$a} vacantes encontradas';
$string['vacancy_inherits_dates'] = 'La vacante hereda fechas de convocatoria';
$string['vacancy_status_published'] = 'Publicada';
$string['vacancyclosed'] = 'Vacante cerrada';
$string['vacancycode'] = 'Código de vacante';
$string['vacancycreated'] = 'Vacante creada';
$string['vacancydeleted'] = 'Vacante eliminada';
$string['vacancydescription'] = 'Descripción de vacante';
$string['vacancyinfo'] = 'Información de vacante';
$string['vacancynotfound'] = 'Vacante no encontrada';
$string['vacancyopen'] = 'Vacante abierta';
$string['vacancypublished'] = 'Vacante publicada';
$string['vacancyreopened'] = 'Vacante reabierta';
$string['vacancysummary'] = 'Resumen de vacante';
$string['vacancytitle'] = 'Título de vacante';
$string['vacancyunpublished'] = 'Vacante despublicada';
$string['vacancyupdated'] = 'Vacante actualizada';

// Validar.
$string['validate'] = 'Validar';
$string['validateall'] = 'Validar todos';
$string['validated'] = 'Validado';
$string['validatedocument'] = 'Validar documento';
$string['validationchecklist'] = 'Lista de verificación de validación';
$string['validationdecision'] = 'Decisión de validación';
$string['validationrequirements'] = 'Requisitos de validación';
$string['validfrom'] = 'Válido desde';
$string['validityperiod'] = 'Período de validez';
$string['validuntil'] = 'Válido hasta';
$string['verification'] = 'Verificación';

// Ver.
$string['viewall'] = 'Ver todos';
$string['viewapplication'] = 'Ver postulación';
$string['viewconvocatoria'] = 'Ver convocatoria';
$string['viewdetails'] = 'Ver detalles';
$string['vieweronly_desc'] = 'Acceso solo lectura';
$string['viewmyapplication'] = 'Ver mi postulación';
$string['viewmyapplications'] = 'Ver mis postulaciones';
$string['viewmyreviews'] = 'Ver mis revisiones';
$string['viewpublicpage'] = 'Ver página pública';
$string['viewpublicvacancies'] = 'Ver vacantes públicas';
$string['viewreports'] = 'Ver reportes';
$string['viewvacancies'] = 'Ver vacantes';
$string['viewvacancy'] = 'Ver vacante';
$string['viewvacancydetails'] = 'Ver detalles de vacante';
$string['wanttoapply'] = '¿Desea postularse?';
$string['welcometojobboard'] = 'Bienvenido a la Bolsa de Empleo';
$string['withdraw'] = 'Retirar';
$string['withdrawapplication'] = 'Retirar postulación';
$string['womenonly'] = 'Solo mujeres';
$string['workflowactions'] = 'Acciones de flujo de trabajo';
$string['workflowmanagement'] = 'Gestión de flujo de trabajo';

// Nombres de columnas CSV.
$string['csvcolumn_code'] = 'Código';
$string['csvcolumn_contracttype'] = 'Tipo de contrato';
$string['csvcolumn_courses'] = 'Cursos';
$string['csvcolumn_faculty'] = 'Facultad';
$string['csvcolumn_location'] = 'Ubicación';
$string['csvcolumn_modality'] = 'Modalidad';
$string['csvcolumn_profile'] = 'Perfil';
$string['csvcolumn_program'] = 'Programa';

// Acciones de edición.
$string['editdoctype'] = 'Editar tipo de documento';
$string['editexemption'] = 'Editar exención';

// Descripciones de placeholders de correo.
$string['ph_user_fullname'] = 'Nombre completo del usuario';
$string['ph_user_firstname'] = 'Nombre del usuario';
$string['ph_user_lastname'] = 'Apellido del usuario';
$string['ph_user_email'] = 'Email del usuario';
$string['ph_site_name'] = 'Nombre del sitio';
$string['ph_site_url'] = 'URL del sitio';
$string['ph_current_date'] = 'Fecha actual';
$string['ph_company_name'] = 'Nombre del centro tutorial';
$string['ph_vacancy_code'] = 'Código de la convocatoria';
$string['ph_vacancy_title'] = 'Título de la convocatoria';
$string['ph_application_id'] = 'ID de la aplicación';
$string['ph_application_url'] = 'URL de la aplicación';
$string['ph_submit_date'] = 'Fecha de envío';
$string['ph_reviewer_name'] = 'Nombre del revisor';
$string['ph_documents_count'] = 'Cantidad de documentos';
$string['ph_rejected_docs'] = 'Lista de documentos rechazados';
$string['ph_observations'] = 'Observaciones del revisor';
$string['ph_resubmit_deadline'] = 'Fecha límite para reenvío';
$string['ph_review_summary'] = 'Resumen de la revisión';
$string['ph_approved_count'] = 'Documentos aprobados';
$string['ph_rejected_count'] = 'Documentos rechazados';
$string['ph_action_required'] = 'Acciones requeridas';
$string['ph_interview_date'] = 'Fecha de la entrevista';
$string['ph_interview_time'] = 'Hora de la entrevista';
$string['ph_interview_location'] = 'Ubicación de la entrevista';
$string['ph_interview_type'] = 'Tipo de entrevista';
$string['ph_interview_duration'] = 'Duración de la entrevista';
$string['ph_interview_notes'] = 'Notas adicionales';
$string['ph_interviewer_name'] = 'Nombre del entrevistador';
$string['ph_hours_until'] = 'Horas restantes';
$string['ph_interview_feedback'] = 'Retroalimentación';
$string['ph_next_steps'] = 'Próximos pasos';
$string['ph_selection_notes'] = 'Notas de selección';
$string['ph_contact_info'] = 'Información de contacto';
$string['ph_rejection_reason'] = 'Motivo del rechazo';
$string['ph_feedback'] = 'Retroalimentación';
$string['ph_waitlist_position'] = 'Posición en lista de espera';
$string['ph_notification_note'] = 'Nota informativa';
$string['ph_days_remaining'] = 'Días restantes';
$string['ph_close_date'] = 'Fecha de cierre';
$string['ph_vacancy_url'] = 'URL de la convocatoria';
$string['ph_vacancy_description'] = 'Descripción de la convocatoria';
$string['ph_open_date'] = 'Fecha de apertura';
$string['ph_faculty_name'] = 'Nombre de la facultad';
$string['ph_applicant_name'] = 'Nombre del aspirante';
$string['ph_deadline'] = 'Fecha límite de revisión';

// Cadenas de JavaScript.
$string['js_select'] = 'Seleccionar...';
$string['js_selectconvocatoria'] = 'Seleccionar convocatoria...';
$string['js_selectmodality'] = 'Seleccionar modalidad...';
$string['js_loading'] = 'Cargando...';
$string['js_internalerror'] = 'Ocurrió un error interno';
