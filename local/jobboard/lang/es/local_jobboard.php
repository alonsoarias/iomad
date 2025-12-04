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

// Phase 2: Application System strings.
$string['applytovacancy'] = 'Postularse a vacante';
$string['vacancyinfo'] = 'Información de la vacante';
$string['code'] = 'Código';
$string['title'] = 'Título';
$string['consentheader'] = 'Consentimiento Informado';
$string['datatreatmentpolicytitle'] = 'Política de Tratamiento de Datos Personales';
$string['defaultdatatreatmentpolicy'] = 'De acuerdo con la Ley 1581 de 2012 y el Decreto 1377 de 2013, autorizo el tratamiento de mis datos personales para fines del proceso de selección. Los datos serán utilizados exclusivamente para evaluar mi postulación y serán almacenados de forma segura.';
$string['consentaccepttext'] = 'He leído y acepto la política de tratamiento de datos personales';
$string['consentrequired'] = 'Debe aceptar la política de tratamiento de datos';
$string['digitalsignature_help'] = 'Ingrese su nombre completo como firma digital para validar su consentimiento';
$string['signaturetoooshort'] = 'La firma debe tener al menos 5 caracteres';
$string['requireddocuments'] = 'Documentos Requeridos';
$string['documentshelp'] = 'Suba los documentos requeridos en formato PDF, JPG o PNG. El tamaño máximo por archivo es de 10MB.';
$string['documentrequired'] = 'El documento "{$a}" es obligatorio';
$string['documentissuedate'] = 'Fecha de expedición';
$string['documentexpired'] = 'El documento ha expirado (máximo {$a})';
$string['additionalinfo'] = 'Información Adicional';
$string['coverletter'] = 'Carta de presentación';
$string['coverletter_help'] = 'Escriba una breve carta de presentación describiendo su motivación e interés en el cargo';
$string['declaration'] = 'Declaración';
$string['declarationtext'] = 'Declaro bajo la gravedad del juramento que la información proporcionada es veraz y los documentos anexos son auténticos. Entiendo que cualquier falsedad puede resultar en la exclusión del proceso y acciones legales.';
$string['declarationaccept'] = 'Acepto la declaración anterior';
$string['declarationrequired'] = 'Debe aceptar la declaración';
$string['submitapplication'] = 'Enviar Postulación';
$string['exemptionnotice'] = 'Aviso de Excepción';
$string['exemptionapplied'] = 'Excepción ISER Aplicada';
$string['exemptionreduceddocs'] = 'Sus requisitos de documentación han sido reducidos debido a su historial laboral en la institución.';
$string['documentref'] = 'Referencia del documento';
$string['exemptiontype_historico_iser'] = 'Personal Histórico ISER';
$string['exemptiontype_documentos_recientes'] = 'Documentos Recientes Aprobados';
$string['exemptiontype_traslado_interno'] = 'Traslado Interno';
$string['exemptiontype_recontratacion'] = 'Recontratación';
$string['vacancynotopen'] = 'La vacante no está abierta para postulaciones';
$string['alreadyapplied'] = 'Ya tiene una postulación para esta vacante';
$string['applicationcreatefailed'] = 'Error al crear la postulación';
$string['applicationerror'] = 'Error en la postulación';
$string['deadlinewarning'] = 'Atención: La vacante cierra en {$a} día(s)';
$string['applicationguidelines'] = 'Instrucciones para postularse';
$string['guideline1'] = 'Complete todos los campos obligatorios marcados con asterisco (*)';
$string['guideline2'] = 'Suba documentos legibles y completos';
$string['guideline3'] = 'Verifique que la información proporcionada sea correcta antes de enviar';
$string['guideline4'] = 'Una vez enviada la postulación, recibirá un correo de confirmación';

// Application list strings.
$string['dateapplied'] = 'Fecha de postulación';
$string['filterbystatus'] = 'Filtrar por estado';
$string['allstatuses'] = 'Todos los estados';
$string['noapplicationsfound'] = 'No se encontraron postulaciones';
$string['browsevacancies'] = 'Explorar vacantes disponibles';
$string['documentsuploaded'] = 'documentos cargados';
$string['exemptionactive'] = 'Excepción ISER activa';

// Application status strings.
$string['status_submitted'] = 'Enviada';
$string['status_under_review'] = 'En revisión';
$string['status_docs_validated'] = 'Documentos validados';
$string['status_docs_rejected'] = 'Documentos rechazados';
$string['status_interview'] = 'Citado a entrevista';
$string['status_selected'] = 'Seleccionado';
$string['status_rejected'] = 'No seleccionado';
$string['status_withdrawn'] = 'Retirada';

// Application view strings.
$string['viewapplication'] = 'Ver postulación';
$string['application'] = 'Postulación';
$string['currentstatus'] = 'Estado actual';
$string['applicantinfo'] = 'Información del postulante';
$string['applicationdetails'] = 'Detalles de la postulación';
$string['consentgiven'] = 'Consentimiento otorgado';
$string['uploadeddocuments'] = 'Documentos cargados';
$string['statushistory'] = 'Historial de estados';
$string['workflowactions'] = 'Acciones de flujo de trabajo';
$string['changestatus'] = 'Cambiar estado';
$string['updatestatus'] = 'Actualizar estado';
$string['notes'] = 'Notas';
$string['backtoapplications'] = 'Volver a postulaciones';
$string['withdrawapplication'] = 'Retirar postulación';
$string['confirmwithdraw'] = '¿Está seguro que desea retirar su postulación? Esta acción no se puede deshacer.';
$string['cannotwithdraw'] = 'No puede retirar esta postulación en su estado actual';
$string['statuschanged'] = 'Estado actualizado exitosamente';
$string['invalidtransition'] = 'Transición de estado no válida';
$string['viewvacancy'] = 'Ver vacante';
$string['unknownvacancy'] = 'Vacante desconocida';
$string['noaccess'] = 'No tiene acceso a este recurso';

// Document validation strings.
$string['validatedocument'] = 'Validar documento';
$string['documentinfo'] = 'Información del documento';
$string['documenttype'] = 'Tipo de documento';
$string['filename'] = 'Nombre del archivo';
$string['uploaded'] = 'Cargado';
$string['viewdocument'] = 'Ver documento';
$string['validationchecklist'] = 'Lista de verificación';
$string['validationdecision'] = 'Decisión de validación';
$string['approvedocument'] = 'Aprobar documento';
$string['rejectdocument'] = 'Rechazar documento';
$string['selectreason'] = 'Seleccione un motivo';
$string['additionalnotes'] = 'Notas adicionales';
$string['rejectreason_illegible'] = 'Documento ilegible';
$string['rejectreason_expired'] = 'Documento vencido';
$string['rejectreason_incomplete'] = 'Documento incompleto';
$string['rejectreason_wrongtype'] = 'Tipo de documento incorrecto';
$string['rejectreason_mismatch'] = 'Información no coincide';
$string['validated'] = 'Validado';
$string['rejected'] = 'Rechazado';
$string['pendingvalidation'] = 'Pendiente de validación';
$string['pending'] = 'Pendiente';

// Validation checklist items.
$string['checklist_legible'] = 'El documento es legible';
$string['checklist_complete'] = 'El documento está completo';
$string['checklist_namematch'] = 'El nombre coincide con el postulante';
$string['checklist_cedula_number'] = 'El número de cédula es visible y legible';
$string['checklist_cedula_photo'] = 'La fotografía es clara';
$string['checklist_background_date'] = 'La fecha de expedición es reciente (máx. 3 meses)';
$string['checklist_background_status'] = 'No registra antecedentes';
$string['checklist_title_institution'] = 'La institución educativa es reconocida';
$string['checklist_title_date'] = 'La fecha de grado es visible';
$string['checklist_title_program'] = 'El programa académico es claro';
$string['checklist_acta_number'] = 'El número de acta es visible';
$string['checklist_acta_date'] = 'La fecha del acta es visible';
$string['checklist_tarjeta_number'] = 'El número de matrícula es visible';
$string['checklist_tarjeta_profession'] = 'La profesión está especificada';
$string['checklist_rut_nit'] = 'El NIT está visible';
$string['checklist_rut_updated'] = 'El RUT está actualizado';
$string['checklist_eps_active'] = 'La afiliación está activa';
$string['checklist_eps_entity'] = 'La entidad de EPS es clara';
$string['checklist_pension_fund'] = 'El fondo de pensiones es claro';
$string['checklist_pension_active'] = 'La afiliación está activa';
$string['checklist_medical_date'] = 'La fecha del certificado es reciente';
$string['checklist_medical_aptitude'] = 'El concepto de aptitud es favorable';
$string['checklist_military_class'] = 'La clase de libreta es visible';
$string['checklist_military_number'] = 'El número de libreta es visible';

// Document types (doctype_ prefix).
$string['doctype_sigep'] = 'Formato SIGEP II';
$string['doctype_cedula'] = 'Cédula de Ciudadanía';
$string['doctype_titulo_pregrado'] = 'Título de Pregrado';
$string['doctype_titulo_postgrado'] = 'Título de Postgrado';
$string['doctype_titulo_especializacion'] = 'Título de Especialización';
$string['doctype_titulo_maestria'] = 'Título de Maestría';
$string['doctype_titulo_doctorado'] = 'Título de Doctorado';
$string['doctype_acta_grado'] = 'Acta de Grado';
$string['doctype_tarjeta_profesional'] = 'Tarjeta Profesional';
$string['doctype_libreta_militar'] = 'Libreta Militar';
$string['doctype_rut'] = 'RUT';
$string['doctype_eps'] = 'Certificado EPS';
$string['doctype_pension'] = 'Certificado Pensión';
$string['doctype_cuenta_bancaria'] = 'Certificación Bancaria';
$string['doctype_antecedentes_procuraduria'] = 'Antecedentes Procuraduría';
$string['doctype_antecedentes_contraloria'] = 'Antecedentes Contraloría';
$string['doctype_antecedentes_policia'] = 'Antecedentes Policía';
$string['doctype_rnmc'] = 'Registro Medidas Correctivas';
$string['doctype_sijin'] = 'Certificado SIJIN';
$string['doctype_certificado_medico'] = 'Certificado Médico';

// Manage applications strings.
$string['manageapplications'] = 'Gestionar postulaciones';
$string['backtomanage'] = 'Volver a gestión';
$string['searchapplicant'] = 'Buscar postulante...';
$string['applicant'] = 'Postulante';
$string['applications'] = 'Postulaciones';
$string['exemption'] = 'Excepción';
$string['exportcsv'] = 'Exportar CSV';
$string['exportexcel'] = 'Exportar Excel';

// Notification strings.
$string['notification_application_received_subject'] = 'Confirmación de postulación - {VACANCY_TITLE}';
$string['notification_application_received_body'] = '<p>Estimado/a {USER_NAME},</p><p>Hemos recibido su postulación para la vacante <strong>{VACANCY_TITLE}</strong> (Código: {VACANCY_CODE}).</p><p>Puede consultar el estado de su postulación en cualquier momento a través del siguiente enlace: <a href="{APPLICATION_URL}">{APPLICATION_URL}</a></p><p>Atentamente,<br>{SITE_NAME}</p>';
$string['notification_under_review_subject'] = 'Su postulación está siendo revisada - {VACANCY_TITLE}';
$string['notification_under_review_body'] = '<p>Estimado/a {USER_NAME},</p><p>Su postulación para la vacante <strong>{VACANCY_TITLE}</strong> ha pasado a estado de revisión.</p><p>Le informaremos cuando haya novedades.</p><p>Atentamente,<br>{SITE_NAME}</p>';
$string['notification_docs_validated_subject'] = 'Documentos validados - {VACANCY_TITLE}';
$string['notification_docs_validated_body'] = '<p>Estimado/a {USER_NAME},</p><p>Sus documentos para la vacante <strong>{VACANCY_TITLE}</strong> han sido validados exitosamente.</p><p>Atentamente,<br>{SITE_NAME}</p>';
$string['notification_docs_rejected_subject'] = 'Documentos requieren corrección - {VACANCY_TITLE}';
$string['notification_docs_rejected_body'] = '<p>Estimado/a {USER_NAME},</p><p>Algunos documentos de su postulación para la vacante <strong>{VACANCY_TITLE}</strong> requieren corrección.</p><p>Por favor ingrese al sistema para ver los detalles: <a href="{APPLICATION_URL}">{APPLICATION_URL}</a></p><p>Atentamente,<br>{SITE_NAME}</p>';
$string['notification_interview_subject'] = 'Citación a entrevista - {VACANCY_TITLE}';
$string['notification_interview_body'] = '<p>Estimado/a {USER_NAME},</p><p>Ha sido citado/a a entrevista para la vacante <strong>{VACANCY_TITLE}</strong>.</p><p>{NOTES}</p><p>Atentamente,<br>{SITE_NAME}</p>';
$string['notification_selected_subject'] = '¡Felicitaciones! Ha sido seleccionado/a - {VACANCY_TITLE}';
$string['notification_selected_body'] = '<p>Estimado/a {USER_NAME},</p><p>Nos complace informarle que ha sido seleccionado/a para la vacante <strong>{VACANCY_TITLE}</strong>.</p><p>Próximamente recibirá información sobre los siguientes pasos del proceso de contratación.</p><p>Atentamente,<br>{SITE_NAME}</p>';
$string['notification_rejected_subject'] = 'Resultado del proceso de selección - {VACANCY_TITLE}';
$string['notification_rejected_body'] = '<p>Estimado/a {USER_NAME},</p><p>Agradecemos su participación en el proceso de selección para la vacante <strong>{VACANCY_TITLE}</strong>.</p><p>En esta oportunidad, hemos seleccionado a otro candidato cuyo perfil se ajusta mejor a los requerimientos específicos del cargo.</p><p>Le animamos a seguir participando en futuras convocatorias.</p><p>Atentamente,<br>{SITE_NAME}</p>';
$string['notification_closing_soon_subject'] = 'Vacante próxima a cerrar - {VACANCY_TITLE}';
$string['notification_closing_soon_body'] = '<p>Estimado/a {USER_NAME},</p><p>La vacante <strong>{VACANCY_TITLE}</strong> cierra en {DAYS_LEFT} día(s).</p><p>Si está interesado/a, puede postularse antes del {CLOSE_DATE} en: <a href="{VACANCY_URL}">{VACANCY_URL}</a></p><p>Atentamente,<br>{SITE_NAME}</p>';

// Task strings.
$string['task:sendnotifications'] = 'Enviar notificaciones pendientes';
$string['task:checkclosingvacancies'] = 'Verificar vacantes próximas a cerrar';
$string['task:cleanupolddata'] = 'Limpiar datos antiguos';

// Phase 3: Workflow and Document Validation strings.

// Reviewer management.
$string['myreviews'] = 'Mis revisiones';
$string['assignreviewers'] = 'Asignar revisores';
$string['assignreviewer'] = 'Asignar revisor';
$string['reviewer'] = 'Revisor';
$string['reviewers'] = 'Revisores';
$string['selectreviewer'] = 'Seleccionar revisor';
$string['workload'] = 'Carga de trabajo';
$string['currentworkload'] = 'Carga actual';
$string['maxworkload'] = 'Carga máxima';
$string['availablereviewers'] = 'Revisores disponibles';
$string['noassignments'] = 'No tiene asignaciones pendientes';
$string['pendingassignments'] = 'Asignaciones pendientes';
$string['assignedto'] = 'Asignado a';
$string['assignedby'] = 'Asignado por';
$string['assignmentdate'] = 'Fecha de asignación';
$string['unassigned'] = 'Sin asignar';
$string['reassign'] = 'Reasignar';
$string['autoassign'] = 'Asignación automática';
$string['autoassigndesc'] = 'Asignar automáticamente postulaciones a revisores disponibles con balanceo de carga';
$string['assignmentscompleted'] = '{$a} asignaciones completadas';
$string['assignmenterror'] = 'Error al asignar revisor';
$string['reviewerassigned'] = 'Revisor asignado exitosamente';
$string['reviewersassigned'] = '{$a} revisores asignados exitosamente';
$string['selectapplications'] = 'Seleccione las postulaciones a asignar';
$string['noapplicationsselected'] = 'No se seleccionaron postulaciones';
$string['allapplicationsassigned'] = 'Todas las postulaciones ya tienen revisor asignado';
$string['myassignments'] = 'Mis asignaciones';

// Bulk validation.
$string['bulkvalidation'] = 'Validación masiva';
$string['bulkvalidate'] = 'Validar en lote';
$string['selectdocuments'] = 'Seleccionar documentos';
$string['selectall'] = 'Seleccionar todo';
$string['selectnone'] = 'Deseleccionar todo';
$string['approveselected'] = 'Aprobar seleccionados';
$string['rejectselected'] = 'Rechazar seleccionados';
$string['documentssummary'] = 'Resumen de documentos';
$string['documentsvalidated'] = 'Documentos validados';
$string['documentsrejected'] = 'Documentos rechazados';
$string['documentspending'] = 'Documentos pendientes';
$string['validationcomplete'] = 'Validación completada';
$string['validationresults'] = 'Resultados de validación';
$string['validationsuccess'] = '{$a} documentos procesados exitosamente';
$string['validationfailed'] = '{$a} documentos fallaron';
$string['bydocumenttype'] = 'Por tipo de documento';
$string['byapplication'] = 'Por postulación';
$string['autovalidate'] = 'Auto-validar';
$string['autovalidatedesc'] = 'Validar automáticamente documentos que cumplen las reglas predefinidas';
$string['autovalidationrules'] = 'Reglas de auto-validación';
$string['documentsautovalidated'] = '{$a} documentos auto-validados';
$string['noautovalidationdocs'] = 'No hay documentos que cumplan las reglas de auto-validación';

// Document re-upload.
$string['reupload'] = 'Volver a cargar';
$string['reuploaddocument'] = 'Volver a cargar documento';
$string['reuploaddesc'] = 'Su documento fue rechazado. Por favor cargue una nueva versión corregida.';
$string['previousversion'] = 'Versión anterior';
$string['newversion'] = 'Nueva versión';
$string['superseded'] = 'Reemplazado';
$string['documenthistory'] = 'Historial del documento';
$string['reuploadsuccess'] = 'Documento recargado exitosamente';
$string['waitingforreupload'] = 'Esperando nueva versión';

// Dashboard.
$string['applicationpipeline'] = 'Pipeline de postulaciones';
$string['totalapplications'] = 'Total postulaciones';
$string['validationstats'] = 'Estadísticas de validación';
$string['avgvalidationtime'] = 'Tiempo promedio de validación';
$string['recentapplications'] = 'Postulaciones recientes';
$string['norecentapplications'] = 'No hay postulaciones recientes';
$string['viewreports'] = 'Ver reportes';
$string['createvacancy'] = 'Crear vacante';

// Reports.
$string['reportoverview'] = 'Resumen general';
$string['reportapplications'] = 'Reporte de postulaciones';
$string['reportdocuments'] = 'Reporte de documentos';
$string['reportreviewers'] = 'Desempeño de revisores';
$string['reporttimeline'] = 'Línea de tiempo';
$string['generatereport'] = 'Generar reporte';
$string['daterange'] = 'Rango de fechas';
$string['fromdate'] = 'Desde';
$string['todate'] = 'Hasta';
$string['vacancy'] = 'Vacante';
$string['allvacancies'] = 'Todas las vacantes';
$string['allreviewers'] = 'Todos los revisores';
$string['totaldocuments'] = 'Total documentos';
$string['totalreviewed'] = 'Total revisados';
$string['validationrate'] = 'Tasa de validación';
$string['rejectionrate'] = 'Tasa de rechazo';
$string['applicationsbystatus'] = 'Postulaciones por estado';
$string['applicationtrends'] = 'Tendencias de postulaciones';
$string['dailyapplications'] = 'Postulaciones diarias';
$string['documentsbytype'] = 'Documentos por tipo';
$string['validationbytype'] = 'Validación por tipo';
$string['commonrejections'] = 'Rechazos comunes';
$string['toprejectionreasons'] = 'Principales motivos de rechazo';
$string['reviewerperformance'] = 'Desempeño de revisores';
$string['reviewerworkload'] = 'Carga de trabajo de revisores';
$string['avgprocessingtime'] = 'Tiempo promedio de procesamiento';
$string['timetovalidation'] = 'Tiempo hasta validación';
$string['timetoselection'] = 'Tiempo hasta selección';
$string['noreportdata'] = 'No hay datos para el reporte seleccionado';

// My reviews page.
$string['sortby'] = 'Ordenar por';
$string['datesubmitted'] = 'Fecha de envío';
$string['closingdate'] = 'Fecha de cierre';
$string['progress'] = 'Progreso';
$string['closingsoon'] = 'Cierra en {$a} día(s)';
$string['reviewdocuments'] = 'Revisar documentos';
$string['documents'] = 'Documentos';

// Rejection reasons.
$string['rejectreason_ilegible'] = 'Documento ilegible';
$string['rejectreason_vencido'] = 'Documento vencido';
$string['rejectreason_incompleto'] = 'Documento incompleto';
$string['rejectreason_formato_incorrecto'] = 'Formato incorrecto';
$string['rejectreason_datos_erroneos'] = 'Datos erróneos';
$string['rejectreason_no_coincide'] = 'No coincide con información del postulante';
$string['rejectreason_sin_firma'] = 'Sin firma o sello';
$string['rejectreason_otro'] = 'Otro motivo';

// Export.
$string['export'] = 'Exportar';
$string['exportformat'] = 'Formato de exportación';
$string['exportcsv'] = 'CSV';
$string['exportexcel'] = 'Excel';
$string['exportpdf'] = 'PDF';
$string['downloading'] = 'Descargando...';

// Validation workflow.
$string['validationworkflow'] = 'Flujo de validación';
$string['nextdocument'] = 'Siguiente documento';
$string['previousdocument'] = 'Documento anterior';
$string['validateandnext'] = 'Validar y siguiente';
$string['rejectandnext'] = 'Rechazar y siguiente';
$string['skipfornow'] = 'Omitir por ahora';
$string['allvalidated'] = 'Todos los documentos validados';
$string['somerejected'] = 'Algunos documentos rechazados';
$string['completereview'] = 'Completar revisión';
$string['reviewcomplete'] = 'Revisión completada';
$string['markasreviewed'] = 'Marcar como revisado';

// Filters.
$string['filterbytype'] = 'Filtrar por tipo';
$string['filterbyvacancy'] = 'Filtrar por vacante';
$string['filterbyreviewer'] = 'Filtrar por revisor';
$string['filterbydate'] = 'Filtrar por fecha';
$string['clearfilters'] = 'Limpiar filtros';
$string['applyfilters'] = 'Aplicar filtros';

// Statistics.
$string['statistics'] = 'Estadísticas';
$string['overallstats'] = 'Estadísticas generales';
$string['mystats'] = 'Mis estadísticas';
$string['todaystats'] = 'Estadísticas del día';
$string['weekstats'] = 'Estadísticas de la semana';
$string['monthstats'] = 'Estadísticas del mes';
$string['validationstoday'] = 'Validaciones hoy';
$string['averagetime'] = 'Tiempo promedio';
$string['fastesttime'] = 'Tiempo más rápido';
$string['slowesttime'] = 'Tiempo más lento';

// Alerts and warnings.
$string['closingvacanciesalert'] = 'Hay {$a} vacante(s) que cierran en los próximos 3 días';
$string['pendingvalidationsalert'] = 'Tiene {$a} documentos pendientes de validación';
$string['overdueassignmentsalert'] = 'Tiene {$a} asignaciones con retraso';
$string['urgentattentionneeded'] = 'Atención urgente requerida';
$string['nourgentitems'] = 'No hay items urgentes';

// Phase 4: ISER Exemptions and Advanced Management strings.

// Exemption management.
$string['manageexemptions'] = 'Gestionar excepciones ISER';
$string['addexemption'] = 'Agregar excepción';
$string['editexemption'] = 'Editar excepción';
$string['viewexemption'] = 'Ver excepción';
$string['revokeexemption'] = 'Revocar excepción';
$string['exemptiondetails'] = 'Detalles de la excepción';
$string['exemptiontype'] = 'Tipo de excepción';
$string['exemptiontype_help'] = 'Seleccione el tipo de excepción que aplica para este usuario';
$string['exemptiontype_desc'] = 'historico_iser, documentos_recientes, traslado_interno, o recontratacion';
$string['exempteddocs'] = 'Documentos exceptuados';
$string['exempteddocs_help'] = 'Seleccione los tipos de documentos que no serán requeridos';
$string['exempteddocs_desc'] = 'Lista de códigos separados por | (ej: cedula|rut|eps)';
$string['validfrom'] = 'Válido desde';
$string['validuntil'] = 'Válido hasta';
$string['validuntil_help'] = 'Deje en blanco para excepción sin fecha de vencimiento';
$string['noexpiry'] = 'Sin vencimiento';
$string['validityperiod'] = 'Período de validez';
$string['activeexemptions'] = 'Excepciones activas';
$string['expiredexemptions'] = 'Excepciones vencidas';
$string['revokedexemptions'] = 'Excepciones revocadas';
$string['revoked'] = 'Revocada';
$string['expired'] = 'Vencida';
$string['revoke'] = 'Revocar';
$string['revokedby'] = 'Revocado por';
$string['revokereason'] = 'Motivo de revocación';
$string['confirmrevokeexemption'] = '¿Está seguro que desea revocar la excepción de {$a}?';
$string['exemptionupdated'] = 'Excepción actualizada exitosamente';
$string['exemptioncreated'] = 'Excepción creada exitosamente';
$string['exemptionrevoked'] = 'Excepción revocada exitosamente';
$string['exemptionerror'] = 'Error al procesar la excepción';
$string['exemptionrevokeerror'] = 'Error al revocar la excepción';
$string['noexemptions'] = 'No hay excepciones registradas';
$string['exemptionusagehistory'] = 'Historial de uso de la excepción';
$string['noexemptionusage'] = 'Esta excepción no ha sido utilizada en ninguna postulación';
$string['doctypes'] = 'tipos de documento';
$string['searchuser'] = 'Buscar usuario...';
$string['usernotfound'] = 'Usuario no encontrado';
$string['selectatleastone'] = 'Seleccione al menos uno';
$string['selectall'] = 'Seleccionar todos';
$string['selectidentitydocs'] = 'Docs. de identidad';
$string['selectbackgrounddocs'] = 'Antecedentes';

// Import exemptions.
$string['importexemptions'] = 'Importar excepciones';
$string['csvfile'] = 'Archivo CSV';
$string['csvdelimiter'] = 'Delimitador CSV';
$string['encoding'] = 'Codificación';
$string['defaultexemptiontype'] = 'Tipo de excepción predeterminado';
$string['defaultexemptiontype_help'] = 'Se usará este tipo cuando la columna exemptiontype esté vacía';
$string['defaultvalidfrom'] = 'Válido desde (predeterminado)';
$string['defaultvaliduntil'] = 'Válido hasta (predeterminado)';
$string['previewonly'] = 'Solo vista previa (no importar)';
$string['importinstructions'] = 'Instrucciones de importación';
$string['importinstructionstext'] = 'Prepare un archivo CSV con los datos de los usuarios que recibirán excepciones ISER.';
$string['requiredcolumns'] = 'Columnas requeridas';
$string['optionalcolumns'] = 'Columnas opcionales';
$string['useridentifier'] = 'Identificador del usuario';
$string['samplecsv'] = 'Ejemplo de CSV';
$string['importresults'] = 'Resultados de importación';
$string['previewmodenotice'] = 'Modo vista previa: no se realizaron cambios. Revise los resultados y ejecute nuevamente sin la opción "Solo vista previa".';
$string['previewtotal'] = '{$a} excepciones se crearán';
$string['previewconfirm'] = 'Para importar estas excepciones, regrese y desmarque la opción "Solo vista previa".';
$string['importcomplete'] = 'Importación completada';
$string['importedsuccess'] = '{$a} excepciones importadas exitosamente';
$string['importedskipped'] = '{$a} usuarios omitidos (ya tienen excepción activa)';
$string['importerrors'] = 'Errores encontrados';
$string['andmore'] = 'y {$a} más...';
$string['importednote'] = 'Importado vía CSV el {$a}';
$string['importerror_usernotfound'] = 'Fila {$a}: Usuario no encontrado';
$string['importerror_alreadyexempt'] = 'Fila {$a->row}: {$a->user} ya tiene una excepción activa';
$string['importerror_createfailed'] = 'Fila {$a->row}: Error al crear excepción para {$a->user}';
$string['row'] = 'Fila';
$string['numdocs'] = 'Núm. documentos';
$string['documentref_desc'] = 'Referencia del documento de soporte (opcional)';
$string['notes_desc'] = 'Notas adicionales (opcional)';

// Interview scheduling.
$string['scheduleinterview'] = 'Programar entrevista';
$string['schedulenewinterview'] = 'Programar nueva entrevista';
$string['scheduledinterviews'] = 'Entrevistas programadas';
$string['dateandtime'] = 'Fecha y hora';
$string['interviewtype'] = 'Tipo de entrevista';
$string['interviewtype_inperson'] = 'Presencial';
$string['interviewtype_video'] = 'Videollamada';
$string['interviewtype_phone'] = 'Telefónica';
$string['locationorurl'] = 'Ubicación o URL';
$string['locationorurl_help'] = 'Para entrevistas presenciales, ingrese la dirección. Para videollamadas, ingrese el enlace de la reunión.';
$string['interviewers'] = 'Entrevistadores';
$string['selectinterviewers'] = 'Seleccionar entrevistadores';
$string['interviewinstructions'] = 'Instrucciones para la entrevista';
$string['interviewscheduled'] = 'Entrevista programada exitosamente';
$string['interviewscheduleerror'] = 'Error al programar la entrevista';
$string['interviewcancelled'] = 'Entrevista cancelada';
$string['interviewcompleted'] = 'Entrevista completada';
$string['interviewdate'] = 'Fecha de entrevista';
$string['interviewstatus_scheduled'] = 'Programada';
$string['interviewstatus_confirmed'] = 'Confirmada';
$string['interviewstatus_completed'] = 'Completada';
$string['interviewstatus_cancelled'] = 'Cancelada';
$string['interviewstatus_noshow'] = 'No se presentó';
$string['interviewstatus_rescheduled'] = 'Reprogramada';
$string['noshow'] = 'No se presentó';
$string['confirmnoshow'] = '¿Está seguro de marcar como No se presentó?';
$string['confirmcancel'] = '¿Está seguro de cancelar esta entrevista?';
$string['markedasnoshow'] = 'Entrevista marcada como No se presentó';
$string['completeinterview'] = 'Completar entrevista';
$string['overallrating'] = 'Calificación general';
$string['rating_poor'] = 'Deficiente';
$string['rating_fair'] = 'Regular';
$string['rating_good'] = 'Bueno';
$string['rating_verygood'] = 'Muy bueno';
$string['rating_excellent'] = 'Excelente';
$string['recommendation'] = 'Recomendación';
$string['recommend_hire'] = 'Contratar';
$string['recommend_furtherreview'] = 'Requiere revisión adicional';
$string['recommend_reject'] = 'No contratar';
$string['interviewfeedback'] = 'Retroalimentación de la entrevista';
$string['saveresults'] = 'Guardar resultados';
$string['result'] = 'Resultado';
$string['rescheduledby'] = 'Reprogramada por {$a->user} el {$a->time}. Motivo: {$a->reason}';
$string['reschedulednote'] = 'Reprogramación de entrevista #{$a}';
$string['cancelledby'] = 'Cancelada por {$a->user} el {$a->time}. Motivo: {$a->reason}';
$string['markednoshow'] = 'Marcada como No se presentó por {$a->user} el {$a->time}. Notas: {$a->notes}';
$string['error:pastdate'] = 'La fecha debe ser en el futuro';
$string['error:schedulingconflict'] = 'Uno o más entrevistadores tienen conflicto de horario';

// Selection committee.
$string['managecommittee'] = 'Gestionar comité de selección';
$string['createcommittee'] = 'Crear comité de selección';
$string['committeename'] = 'Nombre del comité';
$string['defaultcommitteename'] = 'Comité de Selección - {$a}';
$string['committeechair'] = 'Presidente del comité';
$string['initialmembers'] = 'Miembros iniciales';
$string['initialmembers_help'] = 'Seleccione los miembros que conformarán el comité inicialmente (además del presidente)';
$string['committeecreated'] = 'Comité creado exitosamente';
$string['committeemembers'] = 'Miembros del comité';
$string['addmember'] = 'Agregar miembro';
$string['removemember'] = 'Quitar miembro';
$string['memberadded'] = 'Miembro agregado exitosamente';
$string['memberremoved'] = 'Miembro eliminado exitosamente';
$string['confirmremovemember'] = '¿Está seguro de eliminar este miembro del comité?';
$string['rolechanged'] = 'Rol actualizado exitosamente';
$string['role'] = 'Rol';
$string['role_chair'] = 'Presidente';
$string['role_evaluator'] = 'Evaluador';
$string['role_secretary'] = 'Secretario';
$string['role_observer'] = 'Observador';
$string['nomembers'] = 'No hay miembros en el comité';
$string['evaluationcriteria'] = 'Criterios de evaluación';
$string['criterion'] = 'Criterio';
$string['weight'] = 'Peso';
$string['maxscore'] = 'Puntaje máximo';
$string['nocriteria'] = 'No se han definido criterios de evaluación';
$string['editcriteria'] = 'Editar criterios';
$string['applicantranking'] = 'Ranking de postulantes';
$string['rank'] = 'Posición';
$string['avgscore'] = 'Puntaje promedio';
$string['votes'] = 'Votos';
$string['evaluate'] = 'Evaluar';
$string['decide'] = 'Decidir';

// Evaluation.
$string['evaluateapplication'] = 'Evaluar postulación';
$string['evaluationscore'] = 'Puntaje de evaluación';
$string['evaluationvote'] = 'Voto';
$string['vote_approve'] = 'Aprobar';
$string['vote_reject'] = 'Rechazar';
$string['vote_abstain'] = 'Abstención';
$string['evaluationcomments'] = 'Comentarios de evaluación';
$string['submitvote'] = 'Enviar evaluación';
$string['evaluationsubmitted'] = 'Evaluación registrada exitosamente';
$string['aggregateresults'] = 'Resultados agregados';
$string['totalevaluators'] = 'Total evaluadores';
$string['approvalvotes'] = 'Votos a favor';
$string['rejectionvotes'] = 'Votos en contra';
$string['abstentions'] = 'Abstenciones';
$string['committeeRecommendation'] = 'Recomendación del comité';
$string['strong_approve'] = 'Fuertemente recomendado';
$string['approve'] = 'Recomendado';
$string['marginal'] = 'Marginal';
$string['reject'] = 'No recomendado';
$string['pending'] = 'Pendiente';

// Decision.
$string['makedecision'] = 'Tomar decisión';
$string['finaldecision'] = 'Decisión final';
$string['decisionreason'] = 'Justificación de la decisión';
$string['confirmdecision'] = '¿Está seguro de esta decisión? Esta acción notificará al postulante.';
$string['decisionrecorded'] = 'Decisión registrada exitosamente';
$string['applicantselected'] = 'Postulante seleccionado';
$string['applicantrejected'] = 'Postulante no seleccionado';
