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
 * Spanish language strings for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// General strings.
$string['pluginname'] = 'Bolsa de Empleo';
$string['jobboard'] = 'Bolsa de Empleo';
$string['jobboard:desc'] = 'Sistema de gestión de vacantes y postulaciones para profesores cátedra';

// Navigation.
$string['vacancies'] = 'Vacantes';
$string['myvacancies'] = 'Mis Vacantes';
$string['myapplications'] = 'Mis Postulaciones';
$string['newvacancy'] = 'Nueva Vacante';
$string['managevacancies'] = 'Gestionar Vacantes';
$string['reviewapplications'] = 'Revisar Postulaciones';
$string['reports'] = 'Reportes';
$string['settings'] = 'Configuración';
$string['dashboard'] = 'Panel de Control';
$string['exemptions'] = 'Excepciones ISER';

// Capabilities.
$string['jobboard:createvacancy'] = 'Crear vacantes';
$string['jobboard:editvacancy'] = 'Editar vacantes';
$string['jobboard:deletevacancy'] = 'Eliminar vacantes';
$string['jobboard:publishvacancy'] = 'Publicar vacantes';
$string['jobboard:viewallvacancies'] = 'Ver todas las vacantes';
$string['jobboard:apply'] = 'Postularse a vacantes';
$string['jobboard:viewownapplications'] = 'Ver postulaciones propias';
$string['jobboard:viewallapplications'] = 'Ver todas las postulaciones';
$string['jobboard:reviewdocuments'] = 'Revisar documentos';
$string['jobboard:downloadanydocument'] = 'Descargar cualquier documento';
$string['jobboard:manageworkflow'] = 'Gestionar flujo de trabajo';
$string['jobboard:viewreports'] = 'Ver reportes';
$string['jobboard:exportdata'] = 'Exportar datos';
$string['jobboard:manageexemptions'] = 'Gestionar excepciones ISER';
$string['jobboard:accessapi'] = 'Acceder a la API REST';
$string['jobboard:manageapitokens'] = 'Gestionar tokens API';
$string['jobboard:configure'] = 'Configurar el sistema';
$string['jobboard:managedoctypes'] = 'Gestionar tipos de documentos';
$string['jobboard:manageemailtemplates'] = 'Gestionar plantillas de correo';

// Vacancy fields.
$string['vacancycode'] = 'Código de vacante';
$string['vacancycode_help'] = 'Código único interno para identificar la vacante';
$string['vacancytitle'] = 'Título de la vacante';
$string['vacancytitle_help'] = 'Nombre del cargo o posición';
$string['vacancydescription'] = 'Descripción';
$string['vacancydescription_help'] = 'Descripción detallada del cargo y sus funciones';
$string['contracttype'] = 'Tipo de contrato';
$string['contracttype_help'] = 'Modalidad de contratación';
$string['duration'] = 'Duración';
$string['duration_help'] = 'Duración estimada del contrato';
$string['salary'] = 'Salario';
$string['salary_help'] = 'Información sobre remuneración (opcional)';
$string['location'] = 'Ubicación';
$string['location_help'] = 'Lugar donde se desempeñará el cargo';
$string['department'] = 'Departamento/Unidad';
$string['department_help'] = 'Área o dependencia de la institución';
$string['course'] = 'Curso asociado';
$string['course_help'] = 'Curso de Moodle relacionado con esta vacante';
$string['category'] = 'Categoría';
$string['category_help'] = 'Categoría de cursos relacionada';
$string['company'] = 'Empresa/Sede';
$string['company_help'] = 'Empresa o sede (en entornos multi-tenant)';
$string['opendate'] = 'Fecha de apertura';
$string['opendate_help'] = 'Fecha desde la cual se pueden recibir postulaciones';
$string['closedate'] = 'Fecha de cierre';
$string['closedate_help'] = 'Fecha límite para recibir postulaciones';
$string['positions'] = 'Número de vacantes';
$string['positions_help'] = 'Cantidad de posiciones disponibles';
$string['requirements'] = 'Requisitos mínimos';
$string['requirements_help'] = 'Requisitos indispensables para el cargo';
$string['desirable'] = 'Requisitos deseables';
$string['desirable_help'] = 'Requisitos que suman puntos pero no son obligatorios';
$string['status'] = 'Estado';
$string['createdby'] = 'Creado por';
$string['modifiedby'] = 'Modificado por';
$string['timecreated'] = 'Fecha de creación';
$string['timemodified'] = 'Fecha de modificación';

// Vacancy statuses.
$string['status:draft'] = 'Borrador';
$string['status:published'] = 'Publicada';
$string['status:closed'] = 'Cerrada';
$string['status:assigned'] = 'Asignada';

// Application statuses.
$string['appstatus:submitted'] = 'Enviada';
$string['appstatus:under_review'] = 'En revisión';
$string['appstatus:docs_validated'] = 'Documentos validados';
$string['appstatus:docs_rejected'] = 'Documentos rechazados';
$string['appstatus:interview'] = 'Citado a entrevista';
$string['appstatus:selected'] = 'Seleccionado';
$string['appstatus:rejected'] = 'No seleccionado';
$string['appstatus:withdrawn'] = 'Retirada';

// Document types.
$string['doctype:sigep'] = 'Formato Único Hoja de Vida SIGEP II';
$string['doctype:bienes_rentas'] = 'Declaración de Bienes y Rentas';
$string['doctype:cedula'] = 'Cédula de Ciudadanía';
$string['doctype:titulo_academico'] = 'Títulos Académicos';
$string['doctype:tarjeta_profesional'] = 'Tarjeta Profesional';
$string['doctype:libreta_militar'] = 'Libreta Militar';
$string['doctype:formacion_complementaria'] = 'Certificados de Formación Complementaria';
$string['doctype:certificacion_laboral'] = 'Certificaciones Laborales';
$string['doctype:rut'] = 'RUT (Registro Único Tributario)';
$string['doctype:eps'] = 'Certificado EPS';
$string['doctype:pension'] = 'Certificado Pensión';
$string['doctype:cuenta_bancaria'] = 'Certificado Cuenta Bancaria';
$string['doctype:antecedentes_disciplinarios'] = 'Antecedentes Disciplinarios (Procuraduría)';
$string['doctype:antecedentes_fiscales'] = 'Antecedentes Fiscales (Contraloría)';
$string['doctype:antecedentes_judiciales'] = 'Antecedentes Judiciales (Policía)';
$string['doctype:medidas_correctivas'] = 'Registro Nacional de Medidas Correctivas';
$string['doctype:inhabilidades'] = 'Consulta de Inhabilidades (Delitos Sexuales)';
$string['doctype:redam'] = 'REDAM (Registro de Deudores Alimentarios Morosos)';
$string['doctype:otro'] = 'Otro documento';

// Document validation statuses.
$string['docstatus:pending'] = 'Pendiente de revisión';
$string['docstatus:approved'] = 'Aprobado';
$string['docstatus:rejected'] = 'Rechazado';

// Form labels and placeholders.
$string['entercode'] = 'Ingrese el código de vacante';
$string['entertitle'] = 'Ingrese el título de la vacante';
$string['enterdescription'] = 'Describa el cargo y sus funciones';
$string['selectcontracttype'] = 'Seleccione tipo de contrato';
$string['selectcourse'] = 'Seleccione un curso';
$string['selectcategory'] = 'Seleccione una categoría';
$string['selectcompany'] = 'Seleccione una empresa/sede';
$string['selectstatus'] = 'Seleccione estado';
$string['uploadfile'] = 'Subir archivo';
$string['choosefiles'] = 'Seleccionar archivos';
$string['nodocuments'] = 'No hay documentos cargados';

// Contract types.
$string['contract:catedra'] = 'Profesor Cátedra';
$string['contract:temporal'] = 'Temporal';
$string['contract:termino_fijo'] = 'Término Fijo';
$string['contract:prestacion_servicios'] = 'Prestación de Servicios';
$string['contract:planta'] = 'Planta';

// Actions.
$string['create'] = 'Crear';
$string['edit'] = 'Editar';
$string['delete'] = 'Eliminar';
$string['view'] = 'Ver';
$string['publish'] = 'Publicar';
$string['unpublish'] = 'Despublicar';
$string['close'] = 'Cerrar';
$string['assign'] = 'Asignar';
$string['apply'] = 'Postularse';
$string['withdraw'] = 'Retirar postulación';
$string['save'] = 'Guardar';
$string['saveandcontinue'] = 'Guardar y continuar';
$string['cancel'] = 'Cancelar';
$string['confirm'] = 'Confirmar';
$string['back'] = 'Volver';
$string['next'] = 'Siguiente';
$string['previous'] = 'Anterior';
$string['search'] = 'Buscar';
$string['filter'] = 'Filtrar';
$string['clearfilters'] = 'Limpiar filtros';
$string['export'] = 'Exportar';
$string['import'] = 'Importar';
$string['download'] = 'Descargar';
$string['upload'] = 'Cargar';
$string['preview'] = 'Vista previa';
$string['validate'] = 'Validar';
$string['reject'] = 'Rechazar';
$string['approve'] = 'Aprobar';
$string['resubmit'] = 'Reenviar';

// Messages.
$string['vacancycreated'] = 'Vacante creada exitosamente';
$string['vacancyupdated'] = 'Vacante actualizada exitosamente';
$string['vacancydeleted'] = 'Vacante eliminada exitosamente';
$string['vacancypublished'] = 'Vacante publicada exitosamente';
$string['vacancyclosed'] = 'Vacante cerrada exitosamente';
$string['applicationsubmitted'] = 'Postulación enviada exitosamente';
$string['applicationwithdrawn'] = 'Postulación retirada exitosamente';
$string['documentuploaded'] = 'Documento cargado exitosamente';
$string['documentvalidated'] = 'Documento validado exitosamente';
$string['documentrejected'] = 'Documento rechazado';
$string['changesaved'] = 'Cambios guardados exitosamente';

// Errors.
$string['error:vacancynotfound'] = 'Vacante no encontrada';
$string['error:applicationnotfound'] = 'Postulación no encontrada';
$string['error:documentnotfound'] = 'Documento no encontrado';
$string['error:codeexists'] = 'Ya existe una vacante con este código';
$string['error:invaliddates'] = 'La fecha de cierre debe ser posterior a la fecha de apertura';
$string['error:closedateexpired'] = 'La fecha de cierre ya pasó';
$string['error:cannotdelete'] = 'No se puede eliminar la vacante porque tiene postulaciones';
$string['error:cannotpublish'] = 'No se puede publicar: complete todos los campos obligatorios';
$string['error:alreadyapplied'] = 'Ya tiene una postulación activa para esta vacante';
$string['error:vacancyclosed'] = 'La vacante está cerrada y no acepta postulaciones';
$string['error:invalidfile'] = 'El archivo no es válido';
$string['error:filetoobig'] = 'El archivo excede el tamaño máximo permitido';
$string['error:invalidformat'] = 'El formato del archivo no está permitido';
$string['error:invalidmimetype'] = 'El tipo de archivo no es válido';
$string['error:documentexpired'] = 'El documento tiene una fecha de expedición muy antigua';
$string['error:consentrequired'] = 'Debe aceptar los términos y condiciones';
$string['error:permissiondenied'] = 'No tiene permisos para realizar esta acción';
$string['error:invalidtransition'] = 'Transición de estado no permitida';
$string['error:noaccess'] = 'No tiene acceso a este recurso';
$string['error:requiredfield'] = 'Este campo es obligatorio';

// Consent and privacy.
$string['consent'] = 'Consentimiento informado';
$string['consenttext'] = 'Texto de política de tratamiento de datos';
$string['consentagree'] = 'He leído y acepto la política de tratamiento de datos personales';
$string['consentverify'] = 'Autorizo la verificación de la información suministrada';
$string['consenttruth'] = 'Declaro que la información proporcionada es veraz y completa';
$string['digitalsignature'] = 'Firma digital (nombre completo)';
$string['privacypolicy'] = 'Política de Privacidad';
$string['habeasconsent'] = 'Consentimiento Habeas Data';

// ISER Exemptions.
$string['iserexemption'] = 'Excepción ISER';
$string['iserhistoric'] = 'Personal histórico ISER';
$string['isernewpersonnel'] = 'Personal nuevo';
$string['iserexemptionmessage'] = 'Usted es personal histórico ISER. Los siguientes documentos no se solicitan porque ya reposan en su Historia Laboral:';
$string['exempteddocs'] = 'Documentos exceptuados';
$string['exemptionvalid'] = 'Excepción vigente';
$string['exemptionexpired'] = 'Excepción vencida';
$string['manageexemptions'] = 'Gestionar excepciones ISER';
$string['addexemption'] = 'Agregar excepción';
$string['editexemption'] = 'Editar excepción';
$string['deleteexemption'] = 'Eliminar excepción';
$string['importexemptions'] = 'Importar excepciones desde CSV';
$string['exemptionimported'] = 'Excepciones importadas exitosamente';

// External links.
$string['externallinkinfo'] = 'Debe descargar este certificado desde el sitio oficial y subirlo aquí';
$string['linkantecedentesjudiciales'] = 'Antecedentes Judiciales (Policía Nacional)';
$string['linkmedidascorrectivas'] = 'Medidas Correctivas (Policía Nacional)';
$string['linkinhabilidades'] = 'Inhabilidades por Delitos Sexuales';
$string['linkredam'] = 'REDAM - Carpeta Ciudadana';
$string['linksigep'] = 'SIGEP II';

// Document validation checklist.
$string['checklist'] = 'Lista de verificación';
$string['checklistitem'] = 'Ítem de verificación';
$string['checklistcomplete'] = 'Todos los ítems verificados';
$string['checklistincomplete'] = 'Faltan ítems por verificar';
$string['reviewernotes'] = 'Notas del revisor';
$string['rejectreason'] = 'Motivo de rechazo';
$string['enterrejectreason'] = 'Indique el motivo del rechazo';

// Workflow.
$string['workflow'] = 'Flujo de trabajo';
$string['workflowlog'] = 'Historial de cambios';
$string['statuschange'] = 'Cambio de estado';
$string['previousstatus'] = 'Estado anterior';
$string['newstatus'] = 'Nuevo estado';
$string['changedby'] = 'Modificado por';
$string['changedate'] = 'Fecha de cambio';
$string['comments'] = 'Comentarios';

// Notifications.
$string['notification'] = 'Notificación';
$string['notifications'] = 'Notificaciones';
$string['emailsent'] = 'Correo enviado';
$string['emailfailed'] = 'Error al enviar correo';
$string['notificationsettings'] = 'Configuración de notificaciones';
$string['emailtemplates'] = 'Plantillas de correo';
$string['edittemplate'] = 'Editar plantilla';

// Reports.
$string['report:applications'] = 'Reporte de postulaciones';
$string['report:documents'] = 'Reporte de documentos';
$string['report:metrics'] = 'Métricas de tiempo';
$string['report:vacancies'] = 'Reporte de vacantes';
$string['report:audit'] = 'Auditoría';
$string['exportcsv'] = 'Exportar CSV';
$string['exportexcel'] = 'Exportar Excel';
$string['exportpdf'] = 'Exportar PDF';
$string['daterange'] = 'Rango de fechas';
$string['datefrom'] = 'Desde';
$string['dateto'] = 'Hasta';

// Dashboard.
$string['activevacancies'] = 'Vacantes activas';
$string['applicationstoday'] = 'Postulaciones hoy';
$string['pendingdocuments'] = 'Documentos pendientes';
$string['totalapplicants'] = 'Postulantes totales';
$string['selectedthismonth'] = 'Seleccionados este mes';
$string['averagereviewtime'] = 'Tiempo promedio de revisión';
$string['recentactivity'] = 'Actividad reciente';
$string['alerts'] = 'Alertas';
$string['quickactions'] = 'Acciones rápidas';

// Configuration.
$string['generalsettings'] = 'Configuración general';
$string['documentsettings'] = 'Configuración de documentos';
$string['notificationsettings'] = 'Configuración de notificaciones';
$string['workflowsettings'] = 'Configuración de flujo de trabajo';
$string['securitysettings'] = 'Configuración de seguridad';
$string['multitenentsettings'] = 'Configuración multi-tenant';
$string['institutionname'] = 'Nombre de la institución';
$string['institutionlogo'] = 'Logo institucional';
$string['contactemail'] = 'Correo de contacto';
$string['maxfilesize'] = 'Tamaño máximo de archivo (MB)';
$string['allowedformats'] = 'Formatos permitidos';
$string['epsmaxdays'] = 'Días máximo antigüedad EPS';
$string['pensionmaxdays'] = 'Días máximo antigüedad Pensión';
$string['antecedentesmaxdays'] = 'Días máximo antigüedad antecedentes';
$string['enableencryption'] = 'Habilitar encriptación de archivos';
$string['dataretentiondays'] = 'Días de retención de datos';
$string['enableapi'] = 'Habilitar API REST';

// API.
$string['apitokens'] = 'Tokens API';
$string['createtoken'] = 'Crear token';
$string['tokenname'] = 'Nombre del token';
$string['tokenpermissions'] = 'Permisos';
$string['tokenipwhitelist'] = 'Lista blanca de IPs';
$string['tokenexpiry'] = 'Fecha de expiración';
$string['tokencreated'] = 'Token creado exitosamente';
$string['tokenrevoked'] = 'Token revocado';
$string['ratelimit'] = 'Límite de solicitudes';
$string['ratelimitexceeded'] = 'Límite de solicitudes excedido';

// Audit.
$string['auditlog'] = 'Registro de auditoría';
$string['action'] = 'Acción';
$string['entity'] = 'Entidad';
$string['ipaddress'] = 'Dirección IP';
$string['useragent'] = 'Agente de usuario';
$string['timestamp'] = 'Fecha y hora';
$string['details'] = 'Detalles';

// Table headers.
$string['thcode'] = 'Código';
$string['thtitle'] = 'Título';
$string['thstatus'] = 'Estado';
$string['thapplicant'] = 'Postulante';
$string['thdate'] = 'Fecha';
$string['thactions'] = 'Acciones';
$string['thdocument'] = 'Documento';
$string['thvalidation'] = 'Validación';
$string['threviewer'] = 'Revisor';

// Pagination.
$string['showing'] = 'Mostrando {$a->from} a {$a->to} de {$a->total} registros';
$string['perpage'] = 'Por página';
$string['page'] = 'Página';
$string['first'] = 'Primera';
$string['last'] = 'Última';

// Confirmations.
$string['confirmdeletevacancy'] = '¿Está seguro que desea eliminar esta vacante?';
$string['confirmwithdraw'] = '¿Está seguro que desea retirar su postulación?';
$string['confirmstatuschange'] = '¿Confirma el cambio de estado?';
$string['confirmpublish'] = '¿Está seguro que desea publicar esta vacante?';

// Help strings.
$string['help:vacancy'] = 'Complete todos los campos obligatorios para crear una vacante';
$string['help:documents'] = 'Suba los documentos requeridos en formato PDF, JPG o PNG';
$string['help:review'] = 'Revise cada documento contra la lista de verificación';
$string['help:iser'] = 'Si usted es personal histórico ISER, algunos documentos no serán requeridos';

// Events.
$string['event:vacancycreated'] = 'Vacante creada';
$string['event:vacancyupdated'] = 'Vacante actualizada';
$string['event:vacancydeleted'] = 'Vacante eliminada';
$string['event:vacancypublished'] = 'Vacante publicada';
$string['event:applicationcreated'] = 'Postulación creada';
$string['event:applicationupdated'] = 'Postulación actualizada';
$string['event:documentuploaded'] = 'Documento cargado';
$string['event:documentvalidated'] = 'Documento validado';
$string['event:statuschanged'] = 'Estado cambiado';

// Task names.
$string['task:sendnotifications'] = 'Enviar notificaciones pendientes';
$string['task:cleanupdata'] = 'Limpieza de datos antiguos';
$string['task:updatemetrics'] = 'Actualizar métricas del dashboard';

// Privacy.
$string['privacy:metadata:application'] = 'Información sobre las postulaciones del usuario';
$string['privacy:metadata:document'] = 'Documentos subidos por el usuario';
$string['privacy:metadata:audit'] = 'Registro de acciones del usuario';

// Miscellaneous.
$string['noresults'] = 'No se encontraron resultados';
$string['loading'] = 'Cargando...';
$string['processing'] = 'Procesando...';
$string['allstatuses'] = 'Todos los estados';
$string['allcompanies'] = 'Todas las empresas';
$string['alldates'] = 'Todas las fechas';
$string['today'] = 'Hoy';
$string['thisweek'] = 'Esta semana';
$string['thismonth'] = 'Este mes';
$string['days'] = 'días';
$string['hours'] = 'horas';
$string['minutes'] = 'minutos';
$string['yes'] = 'Sí';
$string['no'] = 'No';
$string['required'] = 'Obligatorio';
$string['optional'] = 'Opcional';
$string['active'] = 'Activo';
$string['inactive'] = 'Inactivo';
$string['enabled'] = 'Habilitado';
$string['disabled'] = 'Deshabilitado';
