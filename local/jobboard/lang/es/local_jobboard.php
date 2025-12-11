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
