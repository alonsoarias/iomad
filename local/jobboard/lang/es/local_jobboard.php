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
 * Cadenas de idioma para local_jobboard (Español).
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Plugin general.
$string['pluginname'] = 'Bolsa de Trabajo';
$string['jobboard'] = 'Bolsa de Trabajo';
$string['jobboard:view'] = 'Ver bolsa de trabajo';
$string['jobboard:apply'] = 'Aplicar a vacantes';
$string['jobboard:configure'] = 'Configurar bolsa de trabajo';
$string['jobboard:createvacancy'] = 'Crear vacantes';
$string['jobboard:editvacancy'] = 'Editar vacantes';
$string['jobboard:publishvacancy'] = 'Publicar vacantes';
$string['jobboard:viewallvacancies'] = 'Ver todas las vacantes';
$string['jobboard:manageconvocatorias'] = 'Gestionar convocatorias';
$string['jobboard:reviewdocuments'] = 'Revisar documentos';
$string['jobboard:validatedocuments'] = 'Validar documentos';
$string['jobboard:assignreviewers'] = 'Asignar revisores';
$string['jobboard:evaluate'] = 'Evaluar postulaciones';
$string['jobboard:viewevaluations'] = 'Ver evaluaciones';
$string['jobboard:viewallapplications'] = 'Ver todas las postulaciones';
$string['jobboard:changeapplicationstatus'] = 'Cambiar estado de postulación';
$string['jobboard:viewreports'] = 'Ver reportes';
$string['jobboard:exportreports'] = 'Exportar reportes';
$string['jobboard:exportdata'] = 'Exportar datos';
$string['jobboard:viewownapplications'] = 'Ver propias postulaciones';
$string['jobboard:viewinternalvacancies'] = 'Ver vacantes internas';
$string['jobboard:managedoctypes'] = 'Gestionar tipos de documentos';
$string['jobboard:manageemailtemplates'] = 'Gestionar plantillas de correo';
$string['jobboard:manageexemptions'] = 'Gestionar exenciones';
$string['jobboard:manageworkflow'] = 'Gestionar flujo de trabajo';

// Dashboard.
$string['dashboard'] = 'Panel de Control';
$string['administracion'] = 'Administración';
$string['adminstatistics'] = 'Estadísticas de administración';
$string['applicantstatistics'] = 'Tus estadísticas';
$string['notifications'] = 'Notificaciones';
$string['features'] = 'Características';

// Etiquetas de rol.
$string['role_administrator'] = 'Administrador';
$string['role_manager'] = 'Gestor';
$string['role_reviewer'] = 'Revisor';
$string['role_applicant'] = 'Postulante';

// Mensajes de bienvenida del dashboard.
$string['dashboard_admin_welcome'] = 'Acceso completo para gestionar convocatorias, vacantes y configuración del sistema.';
$string['dashboard_manager_welcome'] = 'Gestiona convocatorias, vacantes y revisa postulaciones.';
$string['dashboard_reviewer_welcome'] = 'Revisa y valida documentos de postulantes asignados a ti.';
$string['dashboard_applicant_welcome'] = 'Explora vacantes disponibles y gestiona tus postulaciones.';

// Secciones del dashboard.
$string['workflowmanagement'] = 'Gestión del Flujo de Trabajo';
$string['reportsanddata'] = 'Reportes y Datos';
$string['systemconfiguration'] = 'Configuración del Sistema';
$string['reviewertasks'] = 'Tareas de Revisión';

// Etiquetas de estadísticas.
$string['activeconvocatorias'] = 'Convocatorias Activas';
$string['publishedvacancies'] = 'Vacantes Publicadas';
$string['totalapplications'] = 'Total Postulaciones';
$string['pendingreviews'] = 'Revisiones Pendientes';
$string['availablevacancies'] = 'Vacantes Disponibles';
$string['myapplicationcount'] = 'Mis Postulaciones';
$string['pendingdocs'] = 'Documentos Pendientes';

// Convocatorias.
$string['convocatorias'] = 'Convocatorias';
$string['convocatoria'] = 'Convocatoria';
$string['convocatorias_dashboard_desc'] = 'Crea y gestiona convocatorias con múltiples vacantes.';
$string['newconvocatoria'] = 'Nueva Convocatoria';
$string['editconvocatoria'] = 'Editar Convocatoria';
$string['deleteconvocatoria'] = 'Eliminar Convocatoria';
$string['convocatoria_status_draft'] = 'Borrador';
$string['convocatoria_status_open'] = 'Abierta';
$string['convocatoria_status_closed'] = 'Cerrada';
$string['convocatoria_status_archived'] = 'Archivada';
$string['browseconvocatorias'] = 'Explorar Convocatorias';
$string['browseconvocatorias_desc'] = 'Explora convocatorias disponibles y encuentra vacantes que coincidan con tu perfil.';

// Vacantes.
$string['vacancies'] = 'Vacantes';
$string['vacancy'] = 'Vacante';
$string['vacancies_dashboard_desc'] = 'Crea, edita y publica vacantes dentro de convocatorias.';
$string['newvacancy'] = 'Nueva Vacante';
$string['editvacancy'] = 'Editar Vacante';
$string['deletevacancy'] = 'Eliminar Vacante';
$string['status:draft'] = 'Borrador';
$string['status:published'] = 'Publicada';
$string['status:closed'] = 'Cerrada';
$string['status:assigned'] = 'Asignada';

// Postulaciones.
$string['applications'] = 'Postulaciones';
$string['application'] = 'Postulación';
$string['myapplications'] = 'Mis Postulaciones';
$string['myapplications_desc'] = 'Ve y gestiona tus postulaciones enviadas y documentos requeridos.';
$string['viewmyapplications'] = 'Ver Mis Postulaciones';
$string['review_dashboard_desc'] = 'Revisa postulaciones, valida documentos y gestiona el proceso de selección.';
$string['reviewall'] = 'Revisar Todas';

// Estados de postulación.
$string['appstatus:submitted'] = 'Enviada';
$string['appstatus:under_review'] = 'En Revisión';
$string['appstatus:docs_validated'] = 'Documentos Validados';
$string['appstatus:docs_rejected'] = 'Documentos Rechazados';
$string['appstatus:interview'] = 'Entrevista';
$string['appstatus:selected'] = 'Seleccionado';
$string['appstatus:rejected'] = 'Rechazado';
$string['appstatus:withdrawn'] = 'Retirado';

// Tipos de contrato.
$string['contract:fulltime'] = 'Tiempo Completo';
$string['contract:parttime'] = 'Tiempo Parcial';
$string['contract:temporary'] = 'Temporal';
$string['contract:permanent'] = 'Permanente';
$string['contract:adjunct'] = 'Catedrático';
$string['contract:hourly'] = 'Por Horas';

// Tipos de publicación.
$string['publicationtype:internal'] = 'Interna';
$string['publicationtype:public'] = 'Pública';

// Revisiones.
$string['myreviews'] = 'Mis Revisiones';
$string['myreviews_desc'] = 'Ve y completa las revisiones de documentos asignadas a ti.';
$string['viewmyreviews'] = 'Ver Mis Revisiones';
$string['completedreviews'] = 'Revisiones Completadas';
$string['pending'] = 'Pendiente';
$string['pending_reviews_alert'] = 'Tienes {$a} revisiones pendientes por completar.';
$string['pending_docs_alert'] = 'Tienes {$a} documentos pendientes por cargar.';
$string['reviewerstatistics'] = 'Estadísticas del Revisor';
$string['pendingassignments'] = 'Asignaciones Pendientes';
$string['documentsvalidated'] = 'Documentos Validados';
$string['documentsrejected'] = 'Documentos Rechazados';
$string['avgvalidationtime'] = 'Tiempo Prom. Validación';
$string['noassignments'] = 'Sin asignaciones';
$string['noassignments_desc'] = 'No tienes postulaciones asignadas para revisar en este momento.';

// Flujo de trabajo.
$string['assignreviewers'] = 'Asignar Revisores';
$string['assignreviewers_desc'] = 'Asigna revisores a postulaciones para validación de documentos.';
$string['bulkvalidation'] = 'Validación Masiva';
$string['bulkvalidation_desc'] = 'Valida múltiples documentos a la vez para procesamiento más rápido.';
$string['committees'] = 'Comités de Selección';
$string['committees_desc'] = 'Gestiona comités de selección para cada facultad.';
$string['managecommittees'] = 'Gestionar Comités';
$string['program_reviewers'] = 'Revisores por Programa';
$string['program_reviewers_desc'] = 'Asigna revisores predeterminados para cada programa académico.';

// Reportes.
$string['reports'] = 'Reportes';
$string['reports_desc'] = 'Ve estadísticas, gráficos y reportes detallados del proceso de selección.';
$string['viewreports'] = 'Ver Reportes';
$string['importvacancies'] = 'Importar Vacantes';
$string['importvacancies_desc'] = 'Importa vacantes desde archivos CSV o Excel.';
$string['import'] = 'Importar';
$string['exportdata'] = 'Exportar Datos';
$string['exportdata_desc'] = 'Exporta postulaciones, evaluaciones y datos de selección.';
$string['export'] = 'Exportar';

// Configuración.
$string['pluginsettings'] = 'Configuración del Plugin';
$string['pluginsettings_desc'] = 'Configura opciones generales de la Bolsa de Trabajo.';
$string['configure'] = 'Configurar';
$string['doctypes'] = 'Tipos de Documentos';
$string['doctypes_desc'] = 'Define documentos requeridos y reglas de validación.';
$string['manage'] = 'Gestionar';
$string['emailtemplates'] = 'Plantillas de Correo';
$string['emailtemplates_desc'] = 'Personaliza correos de notificación enviados a postulantes.';
$string['exemptions'] = 'Exenciones de Usuario';
$string['manageexemptions_desc'] = 'Gestiona exenciones de documentos para usuarios específicos (miembros ISER, edad).';
$string['manageroles'] = 'Gestión de Roles';
$string['manageroles_desc'] = 'Configura roles y permisos para la Bolsa de Trabajo.';

// Página pública.
$string['viewpublicpage'] = 'Ver Página Pública';
$string['viewpublicvacancies'] = 'Ver Vacantes Públicas';
$string['welcometojobboard'] = 'Bienvenido a la Bolsa de Trabajo';
$string['vieweronly_desc'] = 'Actualmente tienes acceso de solo lectura. Explora las vacantes públicas disponibles para conocer más sobre las oportunidades.';

// Descripciones de características.
$string['feature_create_convocatorias'] = 'Crear y configurar convocatorias';
$string['feature_manage_vacancies'] = 'Gestionar vacantes dentro de convocatorias';
$string['feature_track_applications'] = 'Seguimiento del progreso de postulaciones';
$string['feature_create_vacancies'] = 'Crear nuevas ofertas de trabajo';
$string['feature_publish_vacancies'] = 'Publicar y cerrar vacantes';
$string['feature_import_export'] = 'Importar/exportar datos';
$string['feature_review_documents'] = 'Revisar documentos enviados';
$string['feature_validate_applications'] = 'Validar postulaciones';
$string['feature_assign_reviewers'] = 'Asignar revisores a postulaciones';

// Acciones.
$string['apply'] = 'Postular';
$string['view'] = 'Ver';
$string['edit'] = 'Editar';
$string['delete'] = 'Eliminar';
$string['save'] = 'Guardar';
$string['cancel'] = 'Cancelar';
$string['submit'] = 'Enviar';
$string['approve'] = 'Aprobar';
$string['reject'] = 'Rechazar';
$string['explore'] = 'Explorar';
$string['search'] = 'Buscar';
$string['filter'] = 'Filtrar';
$string['reset'] = 'Reiniciar';
$string['back'] = 'Volver';
$string['next'] = 'Siguiente';
$string['previous'] = 'Anterior';
$string['close'] = 'Cerrar';
$string['create'] = 'Crear';
$string['update'] = 'Actualizar';

// Mensajes.
$string['noconvocatorias'] = 'No hay convocatorias disponibles.';
$string['novacancies'] = 'No hay vacantes disponibles.';
$string['noapplications'] = 'No se encontraron postulaciones.';
$string['noreviews'] = 'No hay revisiones asignadas.';
$string['confirmdeletion'] = '¿Estás seguro de que deseas eliminar este elemento?';
$string['deletionsuccess'] = 'Elemento eliminado exitosamente.';
$string['savesuccess'] = 'Cambios guardados exitosamente.';
$string['error'] = 'Ha ocurrido un error.';

// Validación de documentos.
$string['documentvalidation'] = 'Validación de Documentos';
$string['validationstatus'] = 'Estado de Validación';
$string['validationpending'] = 'Pendiente de Validación';
$string['validationapproved'] = 'Aprobado';
$string['validationrejected'] = 'Rechazado';
$string['validationcomments'] = 'Comentarios de Validación';
$string['requireddocuments'] = 'Documentos Requeridos';
$string['uploaddocument'] = 'Cargar Documento';
$string['reupload'] = 'Volver a Cargar';

// Privacidad.
$string['privacy:metadata:local_jobboard_application'] = 'Información sobre postulaciones de usuarios a vacantes.';
$string['privacy:metadata:local_jobboard_document'] = 'Documentos cargados por usuarios para sus postulaciones.';
$string['privacy:metadata:local_jobboard_application:userid'] = 'El ID del usuario que envió la postulación.';
$string['privacy:metadata:local_jobboard_application:timecreated'] = 'La hora en que se envió la postulación.';
$string['privacy:metadata:local_jobboard_document:userid'] = 'El ID del usuario que cargó el documento.';
$string['privacy:metadata:local_jobboard_document:filename'] = 'El nombre del archivo cargado.';

// Tablas y datos.
$string['code'] = 'Código';
$string['name'] = 'Nombre';
$string['description'] = 'Descripción';
$string['status'] = 'Estado';
$string['startdate'] = 'Fecha de Inicio';
$string['enddate'] = 'Fecha de Fin';
$string['opendate'] = 'Fecha de Apertura';
$string['closedate'] = 'Fecha de Cierre';
$string['createdby'] = 'Creado Por';
$string['timecreated'] = 'Fecha de Creación';
$string['timemodified'] = 'Última Modificación';
$string['actions'] = 'Acciones';
$string['positions'] = 'Plazas';
$string['location'] = 'Ubicación';
$string['department'] = 'Departamento';
$string['contracttype'] = 'Tipo de Contrato';
$string['duration'] = 'Duración';
$string['requirements'] = 'Requisitos';
$string['desirable'] = 'Deseable';
$string['applicant'] = 'Postulante';
$string['vacancy_title'] = 'Vacante';
$string['documents'] = 'Documentos';
$string['progress'] = 'Progreso';

// IOMAD/Multi-tenant.
$string['company'] = 'Centro';
$string['faculty'] = 'Facultad';
$string['program'] = 'Programa';
$string['selectcompany'] = 'Seleccionar Centro';
$string['selectfaculty'] = 'Seleccionar Facultad';
$string['selectprogram'] = 'Seleccionar Programa';
$string['allcompanies'] = 'Todos los Centros';
$string['allfaculties'] = 'Todas las Facultades';
$string['allprograms'] = 'Todos los Programas';

// Configuración.
$string['settings'] = 'Configuración';
$string['generalsettings'] = 'Configuración General';
$string['enable_public_page'] = 'Habilitar Página Pública';
$string['enable_public_page_desc'] = 'Permitir a usuarios anónimos ver vacantes públicas.';
$string['require_consent'] = 'Requerir Consentimiento';
$string['require_consent_desc'] = 'Requerir que los postulantes acepten términos antes de postular.';
$string['consent_text'] = 'Texto de Consentimiento';
$string['consent_text_desc'] = 'Texto mostrado a postulantes para aceptación de consentimiento.';
$string['max_file_size'] = 'Tamaño Máximo de Archivo';
$string['max_file_size_desc'] = 'Tamaño máximo para documentos cargados (en MB).';
$string['allowed_file_types'] = 'Tipos de Archivo Permitidos';
$string['allowed_file_types_desc'] = 'Lista separada por comas de extensiones de archivo permitidas.';

// Notificaciones.
$string['notification_application_submitted'] = 'Postulación Enviada';
$string['notification_documents_approved'] = 'Documentos Aprobados';
$string['notification_documents_rejected'] = 'Documentos Rechazados';
$string['notification_interview_scheduled'] = 'Entrevista Programada';
$string['notification_application_selected'] = 'Postulación Seleccionada';
$string['notification_application_rejected'] = 'Postulación No Seleccionada';

// Errores.
$string['error:noaccess'] = 'No tienes permiso para acceder a esta página.';
$string['error:invalidid'] = 'ID proporcionado inválido.';
$string['error:notfound'] = 'El elemento solicitado no fue encontrado.';
$string['error:vacancyclosed'] = 'Esta vacante ya no acepta postulaciones.';
$string['error:alreadyapplied'] = 'Ya has postulado a esta vacante.';
$string['error:uploadfailed'] = 'Error al cargar el archivo. Por favor intenta de nuevo.';
$string['error:invalidfiletype'] = 'Tipo de archivo inválido. Tipos permitidos: {$a}';
$string['error:filetoobig'] = 'El archivo es demasiado grande. Tamaño máximo: {$a} MB';

// Programar entrevista.
$string['scheduleinterview'] = 'Programar Entrevista';
$string['interviewdate'] = 'Fecha de Entrevista';
$string['interviewtime'] = 'Hora de Entrevista';
$string['interviewlocation'] = 'Lugar de Entrevista';
$string['interviewtype'] = 'Tipo de Entrevista';
$string['interviewtype:inperson'] = 'Presencial';
$string['interviewtype:online'] = 'En Línea';
$string['interviewtype:phone'] = 'Telefónica';
$string['interviewlink'] = 'Enlace de Entrevista';
$string['interviewers'] = 'Entrevistadores';

// Perfil de usuario.
$string['applicantprofile'] = 'Perfil del Postulante';
$string['updateprofile'] = 'Actualizar Perfil';
$string['personalinfo'] = 'Información Personal';
$string['contactinfo'] = 'Información de Contacto';
$string['academicinfo'] = 'Información Académica';
$string['workexperience'] = 'Experiencia Laboral';

// Auditoría.
$string['auditlog'] = 'Registro de Auditoría';
$string['auditaction'] = 'Acción';
$string['audituser'] = 'Usuario';
$string['audittime'] = 'Hora';
$string['auditdetails'] = 'Detalles';

// Página de postulaciones - strings adicionales.
$string['browsevacancies'] = 'Explorar Vacantes';
$string['allstatuses'] = 'Todos los estados';
$string['inprogress'] = 'En Progreso';
$string['showingxtoy'] = 'Mostrando {$a->from} a {$a->to} de {$a->total}';
$string['noapplicationsdesc'] = 'Aún no has postulado a ninguna vacante. Comienza explorando las oportunidades disponibles.';
$string['unknownvacancy'] = 'Vacante Desconocida';
$string['exemptionactive'] = 'Exención Activa';
$string['exemptiontype_iser'] = 'Miembro ISER';
$string['exemptiontype_age'] = 'Exención por Edad';
$string['exemptiontype_disability'] = 'Exención por Discapacidad';
$string['documentstatus'] = 'Estado de Documentos';
$string['approved'] = 'Aprobado';
$string['rejected'] = 'Rechazado';
$string['uploaddocsreminder'] = 'Por favor sube los documentos requeridos para continuar.';
$string['viewdetails'] = 'Ver Detalles';
$string['withdraw'] = 'Retirar';
$string['confirmwithdraw'] = '¿Estás seguro de que deseas retirar esta postulación? Esta acción no se puede deshacer.';
$string['breadcrumb'] = 'Ruta de navegación';
$string['pagination'] = 'Navegación de páginas';

// Página de convocatorias - strings adicionales.
$string['manageconvocatorias'] = 'Gestionar Convocatorias';
$string['addconvocatoria'] = 'Agregar Convocatoria';
$string['addvacancy'] = 'Agregar Vacante';
$string['viewvacancies'] = 'Ver Vacantes';
$string['totalconvocatorias'] = 'Total Convocatorias';
$string['convocatoriahelp'] = 'Las convocatorias agrupan múltiples vacantes bajo una sola llamada. Crea una convocatoria, agrega vacantes, y luego ábrela para comenzar a recibir postulaciones.';
$string['noconvocatoriasdesc'] = 'Aún no se han creado convocatorias. Crea tu primera convocatoria para comenzar a gestionar vacantes.';
$string['openconvocatoria'] = 'Abrir Convocatoria';
$string['closeconvocatoria'] = 'Cerrar Convocatoria';
$string['reopenconvocatoria'] = 'Reabrir Convocatoria';
$string['archiveconvocatoria'] = 'Archivar Convocatoria';
$string['confirmopenconvocatoria'] = '¿Estás seguro de que deseas abrir esta convocatoria? Todas las vacantes en borrador serán publicadas.';
$string['confirmcloseconvocatoria'] = '¿Estás seguro de que deseas cerrar esta convocatoria? Todas las vacantes serán cerradas.';
$string['confirmreopenconvocatoria'] = '¿Estás seguro de que deseas reabrir esta convocatoria? Todas las vacantes cerradas serán reabiertas.';
$string['confirmarchiveconvocatoria'] = '¿Estás seguro de que deseas archivar esta convocatoria?';
$string['confirmdeletevconvocatoria'] = '¿Estás seguro de que deseas eliminar esta convocatoria? Esta acción no se puede deshacer.';
$string['convocatoriadeleted'] = 'Convocatoria eliminada exitosamente.';
$string['convocatoriaopened'] = 'Convocatoria abierta exitosamente. Todas las vacantes han sido publicadas.';
$string['convocatoriaclosedmsg'] = 'Convocatoria cerrada exitosamente. Todas las vacantes han sido cerradas.';
$string['convocatoriaarchived'] = 'Convocatoria archivada exitosamente.';
$string['convocatoriareopened'] = 'Convocatoria reabierta exitosamente. Todas las vacantes han sido publicadas.';
$string['error:cannotdeleteconvocatoria'] = 'No se puede eliminar esta convocatoria. Solo las convocatorias en borrador o archivadas pueden ser eliminadas.';
$string['error:convocatoriahasnovacancies'] = 'No se puede abrir esta convocatoria. Por favor agrega al menos una vacante primero.';
$string['error:cannotreopenconvocatoria'] = 'No se puede reabrir esta convocatoria. Solo las convocatorias cerradas pueden ser reabiertas.';

// Página de vacantes - strings adicionales.
$string['explorevacancias'] = 'Explorar Vacantes';
$string['browse_vacancies_desc'] = 'Encuentra tu próxima oportunidad entre nuestras posiciones disponibles.';
$string['closingsoon'] = 'Por Cerrar';
$string['searchvacancies'] = 'Buscar vacantes';
$string['allcontracttypes'] = 'Todos los tipos de contrato';
$string['alldepartments'] = 'Todos los departamentos';
$string['allvacancies'] = 'Todas las vacantes';
$string['daysleft'] = '{$a} días restantes';
$string['vacancystatistics'] = 'Estadísticas de Vacantes';
$string['convocatoriaactive'] = 'Convocatorias Activas';
$string['convocatoriaclosed'] = 'Convocatorias Cerradas';
$string['convocatoriastatistics'] = 'Estadísticas de Convocatorias';
$string['noconvocatorias'] = 'Sin convocatorias';
$string['noconvocatorias_desc'] = 'No hay convocatorias disponibles en este momento.';
$string['datesubmitted'] = 'Fecha de Envío';
$string['closingdate'] = 'Fecha de Cierre';
$string['pendingdocuments'] = 'Documentos Pendientes';
$string['sortby'] = 'Ordenar por';
$string['statustabs'] = 'Pestañas de estado';
$string['convocatoriavacancycount'] = '{$a} vacantes';
$string['noresults'] = 'No se encontraron resultados';
$string['applied'] = 'Postulado';
$string['backtodashboard'] = 'Volver al Panel';

// Página de detalle de vacante (v3.1.8).
$string['vacancyopen'] = 'Esta vacante está abierta y acepta postulaciones.';
$string['backtoconvocatoria'] = 'Volver a la convocatoria';
$string['backtovacancies'] = 'Volver a vacantes';
$string['deadlineprogress'] = 'Progreso del plazo';
$string['daysremaining'] = 'días restantes';
$string['modifiedby'] = 'Modificado por';
$string['readytoapply'] = '¿Listo para postular?';
$string['applynowdesc'] = 'Envía tu postulación y documentos de respaldo para ser considerado para esta posición.';
$string['cannotapply'] = 'No puedes postular a esta vacante.';
$string['logintoapply'] = 'Por favor inicia sesión para postular a esta vacante.';
$string['vacancydescription'] = 'Descripción';
$string['closingsoondays'] = 'Por cerrar pronto';

// Página de detalle de postulación (v3.1.8).
$string['currentstatus'] = 'Estado actual';
$string['vacancyinfo'] = 'Información de la vacante';
$string['uploadeddocuments'] = 'Documentos cargados';
$string['applicationdetails'] = 'Detalles de la postulación';
$string['reviewapplications'] = 'Revisar postulaciones';
$string['backtoapplications'] = 'Volver a mis postulaciones';
$string['backtoreviewlist'] = 'Volver a lista de revisión';
$string['reviewdocuments_desc'] = 'Revisa y valida los documentos presentados por el postulante.';
$string['exemptionapplied'] = 'Exención aplicada';
$string['coverletter'] = 'Carta de presentación';
$string['digitalsignature'] = 'Firma digital';
$string['consentgiven'] = 'Consentimiento otorgado';
$string['workflowactions'] = 'Acciones de flujo de trabajo';
$string['changestatus'] = 'Cambiar estado';
$string['optionalnotes'] = 'Notas opcionales';
$string['updatestatus'] = 'Actualizar estado';
$string['withdrawapplication'] = 'Retirar postulación';
$string['confirmwithdraw'] = '¿Estás seguro de que deseas retirar esta postulación? Esta acción no se puede deshacer.';
$string['applicationwithdrawn'] = 'Postulación retirada exitosamente.';
$string['cannotwithdraw'] = 'Esta postulación no puede ser retirada en esta etapa.';
$string['statuschanged'] = 'Estado de la postulación actualizado exitosamente.';
$string['invalidtransition'] = 'Esta transición de estado no está permitida.';
