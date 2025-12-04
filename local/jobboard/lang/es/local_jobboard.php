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
$string['error:cannotedit'] = 'No se puede editar esta vacante';
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
$string['recommendation_approve'] = 'Recomendado';
$string['marginal'] = 'Marginal';
$string['recommendation_reject'] = 'No recomendado';

// Decision.
$string['makedecision'] = 'Tomar decisión';
$string['finaldecision'] = 'Decisión final';
$string['decisionreason'] = 'Justificación de la decisión';
$string['confirmdecision'] = '¿Está seguro de esta decisión? Esta acción notificará al postulante.';
$string['decisionrecorded'] = 'Decisión registrada exitosamente';
$string['applicantselected'] = 'Postulante seleccionado';
$string['applicantrejected'] = 'Postulante no seleccionado';

// Phase 5: Gestión de Tokens API.
$string['managetokens'] = 'Gestionar Tokens API';
$string['api:manageapitokens'] = 'Gestionar Tokens API';
$string['api:token:create'] = 'Crear Token';
$string['api:token:created'] = 'Token creado exitosamente. Asegúrese de copiar el token ahora ya que no se mostrará de nuevo.';
$string['api:token:description'] = 'Descripción del token';
$string['api:token:description_help'] = 'Un nombre descriptivo para identificar este token y su propósito';
$string['api:token:validity'] = 'Período de Validez';
$string['api:token:validfrom'] = 'Válido desde';
$string['api:token:validfrom_help'] = 'La fecha desde la cual el token será válido. Dejar vacío para validez inmediata.';
$string['api:token:validuntil'] = 'Válido hasta';
$string['api:token:validuntil_help'] = 'La fecha hasta la cual el token permanece válido. Dejar vacío para sin expiración.';
$string['api:token:ipwhitelist'] = 'Lista Blanca de IP';
$string['api:token:ipwhitelist_help'] = 'Ingrese una dirección IP o rango CIDR por línea. Dejar vacío para permitir todas las IPs. Ejemplo: 192.168.1.0/24';
$string['api:token:revoke'] = 'Revocar Token';
$string['api:token:delete'] = 'Eliminar Token';
$string['api:token:enable'] = 'Habilitar Token';
$string['api:token:disable'] = 'Deshabilitar Token';
$string['api:token:confirmrevoke'] = '¿Está seguro de que desea revocar este token API? Esta acción deshabilitará el token inmediatamente.';
$string['api:token:confirmdelete'] = '¿Está seguro de que desea eliminar permanentemente este token API? Esta acción no se puede deshacer.';
$string['api:token:revoked'] = 'El token ha sido revocado';
$string['api:token:deleted'] = 'El token ha sido eliminado';
$string['api:token:notfound'] = 'Token no encontrado';
$string['api:token:lastused'] = 'Último uso';
$string['api:token:never'] = 'Nunca';
$string['api:token:copytoclipboard'] = 'Copiar al portapapeles';
$string['api:token:copied'] = 'Token copiado al portapapeles';
$string['api:token:yourtoken'] = 'Su nuevo token API';
$string['api:token:warning'] = 'Advertencia: Esta es la única vez que se mostrará este token. Asegúrese de copiarlo ahora.';
$string['api:token:notoken'] = 'Aún no se han creado tokens API.';

// Estados de Token API.
$string['api:token:status:active'] = 'Activo';
$string['api:token:status:disabled'] = 'Deshabilitado';
$string['api:token:status:expired'] = 'Expirado';
$string['api:token:status:not_yet_valid'] = 'Aún no válido';

// Permisos API.
$string['permissions'] = 'Permisos';
$string['api:permission:view_vacancies'] = 'Ver listado de vacantes';
$string['api:permission:view_vacancy_details'] = 'Ver detalles de vacante';
$string['api:permission:create_application'] = 'Crear postulaciones';
$string['api:permission:view_applications'] = 'Ver postulaciones';
$string['api:permission:view_application_details'] = 'Ver detalles de postulación';
$string['api:permission:upload_documents'] = 'Subir documentos';
$string['api:permission:view_documents'] = 'Ver documentos';

// Errores API.
$string['api:error:unauthorized'] = 'No autorizado: Token API inválido o faltante';
$string['api:error:forbidden'] = 'Prohibido: No tiene permiso para acceder a este recurso';
$string['api:error:notfound'] = 'No encontrado: El recurso solicitado no existe';
$string['api:error:ratelimit'] = 'Límite de tasa excedido. Por favor intente más tarde.';
$string['api:error:invalidrequest'] = 'Solicitud inválida';
$string['api:error:ipnotallowed'] = 'Acceso denegado: Su dirección IP no está en la lista blanca';
$string['api:error:tokendisabled'] = 'El token está deshabilitado';
$string['api:error:tokenexpired'] = 'El token ha expirado';
$string['api:error:tokennotyetvalid'] = 'El token aún no es válido';

// Encabezados de respuesta API.
$string['api:ratelimit:limit'] = 'Límite de tasa';
$string['api:ratelimit:remaining'] = 'Solicitudes restantes';
$string['api:ratelimit:reset'] = 'Tiempo de reinicio';

// Encriptación.
$string['encryption:enabled'] = 'La encriptación de archivos está habilitada';
$string['encryption:disabled'] = 'La encriptación de archivos está deshabilitada';
$string['encryption:keygenerated'] = 'Clave de encriptación generada exitosamente';
$string['encryption:keyimported'] = 'Clave de encriptación importada exitosamente';
$string['encryption:invalidkey'] = 'Formato de clave de encriptación inválido';
$string['encryption:error'] = 'Ocurrió un error de encriptación/desencriptación';
$string['encryption:nokey'] = 'Clave de encriptación no configurada';
$string['encryption:settings'] = 'Configuración de Encriptación';
$string['encryption:generatekey'] = 'Generar Nueva Clave';
$string['encryption:importkey'] = 'Importar Clave';
$string['encryption:exportkey'] = 'Exportar Clave';
$string['encryption:warning'] = 'Advertencia: Cambiar la clave de encriptación hará ilegibles los archivos previamente encriptados.';

// Seguridad.
$string['security'] = 'Seguridad';
$string['security:settings'] = 'Configuración de Seguridad';
$string['security:apiconfig'] = 'Configuración de API';
$string['security:ratelimiting'] = 'Límite de Tasa';

// Exportación de Datos (GDPR/Habeas Data).
$string['dataexport'] = 'Exportar Mis Datos';
$string['dataexport:personal'] = 'Exportación de Datos Personales';
$string['dataexport:title'] = 'Informe de Exportación de Datos Personales';
$string['dataexport:userinfo'] = 'Información del Usuario';
$string['dataexport:exportdate'] = 'Fecha de exportación';
$string['dataexport:consent'] = 'Registros de Consentimiento';
$string['dataexport:json'] = 'Exportar como JSON';
$string['dataexport:pdf'] = 'Exportar como PDF';
$string['dataexport:requested'] = 'Su exportación de datos ha sido solicitada';
$string['dataexport:ready'] = 'Su exportación de datos está lista para descargar';
$string['dataexport:description'] = 'Descargue una copia de sus datos personales almacenados en el sistema de Bolsa de Empleo';

// Eliminación de Datos.
$string['datadeletion'] = 'Eliminar Mis Datos';
$string['datadeletion:request'] = 'Solicitar Eliminación de Datos';
$string['datadeletion:confirm'] = '¿Está seguro de que desea solicitar la eliminación de sus datos personales? Esta acción no se puede deshacer.';
$string['datadeletion:requested'] = 'Su solicitud de eliminación de datos ha sido enviada';
$string['datadeletion:completed'] = 'Sus datos personales han sido eliminados';
$string['datadeletion:pending'] = 'Eliminación de datos pendiente';

// Proveedor de privacidad extendido.
$string['privacy:metadata:local_jobboard_application'] = 'Información sobre postulaciones de empleo enviadas por el usuario';
$string['privacy:metadata:local_jobboard_application:userid'] = 'El ID del usuario que envió la postulación';
$string['privacy:metadata:local_jobboard_application:vacancyid'] = 'El ID de la vacante a la que se postuló';
$string['privacy:metadata:local_jobboard_application:status'] = 'El estado actual de la postulación';
$string['privacy:metadata:local_jobboard_application:coverletter'] = 'Texto de carta de presentación enviado con la postulación';
$string['privacy:metadata:local_jobboard_application:digitalsignature'] = 'Firma digital proporcionada por el postulante';
$string['privacy:metadata:local_jobboard_application:consentgiven'] = 'Si se otorgó consentimiento para el procesamiento de datos';
$string['privacy:metadata:local_jobboard_application:consenttimestamp'] = 'Cuándo se otorgó el consentimiento';
$string['privacy:metadata:local_jobboard_application:consentip'] = 'Dirección IP desde la cual se otorgó el consentimiento';
$string['privacy:metadata:local_jobboard_application:timecreated'] = 'Cuándo se creó la postulación';

$string['privacy:metadata:local_jobboard_document'] = 'Documentos subidos por usuarios como parte de sus postulaciones';
$string['privacy:metadata:local_jobboard_document:userid'] = 'El ID del usuario que subió el documento';
$string['privacy:metadata:local_jobboard_document:documenttype'] = 'El tipo de documento subido';
$string['privacy:metadata:local_jobboard_document:filename'] = 'El nombre del archivo subido';
$string['privacy:metadata:local_jobboard_document:timecreated'] = 'Cuándo se subió el documento';

$string['privacy:metadata:local_jobboard_exemption'] = 'Registros de exención ISER para usuarios';
$string['privacy:metadata:local_jobboard_exemption:userid'] = 'El ID del usuario con la exención';
$string['privacy:metadata:local_jobboard_exemption:exemptiontype'] = 'El tipo de exención otorgada';
$string['privacy:metadata:local_jobboard_exemption:validfrom'] = 'Cuándo la exención se vuelve válida';
$string['privacy:metadata:local_jobboard_exemption:validuntil'] = 'Cuándo expira la exención';

$string['privacy:metadata:local_jobboard_audit'] = 'Registro de auditoría de acciones del usuario';
$string['privacy:metadata:local_jobboard_audit:userid'] = 'El ID del usuario que realizó la acción';
$string['privacy:metadata:local_jobboard_audit:action'] = 'La acción realizada';
$string['privacy:metadata:local_jobboard_audit:ipaddress'] = 'La dirección IP desde la cual se realizó la acción';
$string['privacy:metadata:local_jobboard_audit:timecreated'] = 'Cuándo se realizó la acción';

$string['privacy:metadata:local_jobboard_api_token'] = 'Tokens API creados por usuarios';
$string['privacy:metadata:local_jobboard_api_token:userid'] = 'El ID del usuario propietario del token';
$string['privacy:metadata:local_jobboard_api_token:description'] = 'Descripción del propósito del token';
$string['privacy:metadata:local_jobboard_api_token:timecreated'] = 'Cuándo se creó el token';

$string['privacy:metadata:local_jobboard_notification'] = 'Registros de notificaciones para usuarios';
$string['privacy:metadata:local_jobboard_notification:userid'] = 'El ID del usuario que recibió la notificación';
$string['privacy:metadata:local_jobboard_notification:templatecode'] = 'La plantilla de notificación utilizada';
$string['privacy:metadata:local_jobboard_notification:timecreated'] = 'Cuándo se creó la notificación';

// Errores de validación adicionales.
$string['error:usernotfound'] = 'Usuario no encontrado';
$string['error:nopermission'] = 'Debe seleccionar al menos un permiso';
$string['error:invalidip'] = 'Dirección IP o notación CIDR inválida: {$a}';
$string['error:invalidtoken'] = 'Token inválido o expirado';
$string['error:tokenlimit'] = 'Número máximo de tokens alcanzado';

// Tarea de limpieza.
$string['task:cleanupolddata'] = 'Limpiar datos antiguos de bolsa de empleo';
$string['cleanup:applicationsdeleted'] = '{$a} postulaciones antiguas eliminadas';
$string['cleanup:tokensdeleted'] = '{$a} tokens expirados eliminados';
$string['cleanup:auditlogsdeleted'] = '{$a} registros de auditoría antiguos eliminados';
$string['cleanup:notificationsdeleted'] = '{$a} notificaciones antiguas eliminadas';

// Retención de datos.
$string['dataretention'] = 'Retención de Datos';
$string['dataretention:days'] = 'Período de retención (días)';
$string['dataretention:days_help'] = 'Número de días para retener postulaciones rechazadas o retiradas antes de la eliminación automática. Establecer en 0 para deshabilitar la eliminación automática.';
$string['dataretention:policy'] = 'Política de retención de datos';
$string['dataretention:auditdays'] = 'Retención de registro de auditoría (días)';
$string['dataretention:notificationdays'] = 'Retención de notificaciones (días)';

// Fechas de validez.
$string['validfrom'] = 'Válido desde';
$string['validuntil'] = 'Válido hasta';
$string['validfrom_help'] = 'La fecha desde la cual este elemento es válido';
$string['validuntil_help'] = 'La fecha hasta la cual este elemento permanece válido';

// Documentos de usuario/postulación.
$string['nodocumentsrequired'] = 'No se requieren documentos para esta vacante';
$string['alldocumentssubmitted'] = 'Todos los documentos requeridos han sido enviados';
$string['documentsmissing'] = 'Faltan algunos documentos requeridos';

// Formulario de postulación.
$string['applyfor'] = 'Postularse a: {$a}';
$string['coverletter'] = 'Carta de Presentación';
$string['coverletter_help'] = 'Carta de presentación opcional para acompañar su postulación';
$string['submitapplication'] = 'Enviar Postulación';
$string['applicationpreview'] = 'Vista Previa de Postulación';

// Encabezados de tabla de tokens.
$string['th:token'] = 'Token';
$string['th:description'] = 'Descripción';
$string['th:user'] = 'Usuario';
$string['th:permissions'] = 'Permisos';
$string['th:status'] = 'Estado';
$string['th:lastused'] = 'Último Uso';
$string['th:created'] = 'Creado';
$string['th:actions'] = 'Acciones';

// ==========================================================================
// Fase 7: Página Pública y Límites de Postulación.
// ==========================================================================

// Tipos de publicación.
$string['publicationtype'] = 'Tipo de publicación';
$string['publicationtype_help'] = 'Las vacantes públicas son visibles para todos, incluidos los usuarios no autenticados. Las vacantes internas solo son visibles para los usuarios autenticados de la organización.';
$string['publicationtype:public'] = 'Pública';
$string['publicationtype:internal'] = 'Interna';

// Página pública.
$string['publicvacancies'] = 'Oportunidades Laborales';
$string['publicpagetitle'] = 'Oportunidades Laborales';
$string['publicpagetitle_default'] = 'Oportunidades Laborales';
$string['vacanciesfound'] = '{$a} vacantes encontradas';
$string['novacanciesfound'] = 'No se encontraron vacantes que coincidan con sus criterios.';
$string['searchplaceholder'] = 'Buscar por título, código o descripción...';
$string['viewdetails'] = 'Ver Detalles';
$string['loginandapply'] = 'Iniciar sesión para postularse';
$string['closesin'] = 'Cierra en {$a} días';
$string['closeson'] = 'Cierra el';
$string['wanttoapply'] = '¿Desea postularse?';
$string['createaccounttoapply'] = 'Cree una cuenta o inicie sesión para postularse a las vacantes.';
$string['backtovacancies'] = 'Volver a Vacantes';
$string['requireddocuments'] = 'Documentos Requeridos';
$string['importantdates'] = 'Fechas Importantes';
$string['sharethisvacancy'] = 'Compartir esta Vacante';
$string['copylink'] = 'Copiar enlace';
$string['applynow'] = 'Postularse Ahora';
$string['alreadyapplied'] = 'Ya se ha postulado a esta vacante.';
$string['applicationstatus'] = 'Estado de la postulación';
$string['viewmyapplications'] = 'Ver Mis Postulaciones';
$string['loginrequiredtoapply'] = 'Debe iniciar sesión para postularse a esta vacante.';
$string['noapplypermission'] = 'No tiene permiso para postularse a vacantes.';
$string['all'] = 'Todos';

// Configuración de página pública.
$string['publicpagesettings'] = 'Configuración de Página Pública';
$string['publicpagesettings_desc'] = 'Configure la página de vacantes públicas accesible sin autenticación.';
$string['enablepublicpage'] = 'Habilitar página pública';
$string['enablepublicpage_desc'] = 'Permitir acceso público para ver vacantes públicas sin requerir autenticación.';
$string['publicpagedescription'] = 'Descripción de la página';
$string['publicpagedescription_desc'] = 'Texto introductorio mostrado en la parte superior de la página de vacantes públicas.';
$string['publicpagetitle_desc'] = 'Título personalizado para la página de vacantes públicas. Dejar vacío para usar el predeterminado.';
$string['showpublicnavlink'] = 'Mostrar en navegación';
$string['showpublicnavlink_desc'] = 'Mostrar un enlace a la página de vacantes públicas en la navegación principal para usuarios no autenticados.';

// Configuración de límites de postulación.
$string['applicationlimits'] = 'Límites de Postulación';
$string['applicationlimits_desc'] = 'Configure cuántas postulaciones pueden enviar los usuarios.';
$string['allowmultipleapplications'] = 'Permitir múltiples postulaciones';
$string['allowmultipleapplications_desc'] = 'Permitir a los usuarios postularse a múltiples vacantes simultáneamente.';
$string['maxactiveapplications'] = 'Máximo de postulaciones activas';
$string['maxactiveapplications_desc'] = 'Número máximo de postulaciones activas por usuario (0 = ilimitado). Solo aplica cuando se permiten múltiples postulaciones.';

// Errores de límite de postulación.
$string['error:multipleapplicationsnotallowed'] = 'Solo puede tener una postulación activa a la vez. Por favor retire su postulación actual antes de postularse a una nueva vacante.';
$string['error:applicationlimitreached'] = 'Ha alcanzado el número máximo de postulaciones activas ({$a}). Por favor espere a que se procesen sus postulaciones actuales o retire una antes de postularse a una nueva vacante.';
$string['error:publicpagedisabled'] = 'La página de vacantes públicas está deshabilitada.';
$string['error:loginrequiredforinternal'] = 'Debe iniciar sesión para ver vacantes internas.';

// Nuevas capabilities.
$string['jobboard:viewpublicvacancies'] = 'Ver vacantes públicas';
$string['jobboard:viewinternalvacancies'] = 'Ver vacantes internas';
$string['jobboard:unlimitedapplications'] = 'Omitir límites de postulación';

// ==========================================================================
// Cadenas adicionales para revisión de documentos.
// ==========================================================================

$string['addnote'] = 'Agregar nota';
$string['alldocumentsreviewed'] = 'Todos los documentos han sido revisados';
$string['applicationof'] = 'Postulación {$a->current} de {$a->total}';
$string['count'] = 'Cantidad';
$string['datefrom'] = 'Desde';
$string['dateto'] = 'Hasta';
$string['documentlist'] = 'Lista de documentos';
$string['documentpreview'] = 'Vista previa del documento';
$string['documentref_help'] = 'Número de referencia o identificador del documento de respaldo';
$string['documentstatus'] = 'Estado del documento';
$string['error:invalidapplication'] = 'Postulación inválida';
$string['error:invaliddocument'] = 'Documento inválido';
$string['error:reviewfailed'] = 'Error al enviar la revisión';
$string['name'] = 'Nombre';
$string['nextapplication'] = 'Siguiente postulación';
$string['nodocumentstoreview'] = 'No hay documentos para revisar';
$string['pendingdocuments'] = 'Documentos pendientes';
$string['previousapplication'] = 'Postulación anterior';
$string['rejectall'] = 'Rechazar todos';
$string['reviewedby'] = 'Revisado por';
$string['reviewedon'] = 'Revisado el';
$string['reviewhistory'] = 'Historial de revisión';
$string['reviewnotes'] = 'Notas de revisión';
$string['reviewsubmitted'] = 'Revisión enviada exitosamente';
$string['selected'] = 'Seleccionado';
$string['selectionrate'] = 'Tasa de selección';
$string['submitreview'] = 'Enviar revisión';
$string['validateall'] = 'Validar todos';

// ==========================================================================
// Strings de páginas de administración.
// ==========================================================================

// Gestión de tipos de documentos.
$string['doctypecode'] = 'Código del tipo de documento';
$string['doctypename'] = 'Nombre del tipo de documento';
$string['nodoctypes'] = 'No hay tipos de documentos configurados';
$string['doctypecreated'] = 'Tipo de documento creado exitosamente';
$string['doctypeupdated'] = 'Tipo de documento actualizado exitosamente';
$string['doctypedeleted'] = 'Tipo de documento eliminado exitosamente';
$string['enabledoctype'] = 'Habilitar tipo de documento';
$string['disabledoctype'] = 'Deshabilitar tipo de documento';

// Gestión de plantillas de correo.
$string['subject'] = 'Asunto';
$string['body'] = 'Cuerpo';
$string['emailtemplateshelp'] = 'Las plantillas de correo utilizan marcadores como {USER_NAME}, {VACANCY_TITLE}, {APPLICATION_URL} que se reemplazan con valores reales cuando se envía el correo.';
$string['notemplates'] = 'No hay plantillas de correo configuradas. Las plantillas se crean automáticamente cuando se instala el plugin.';

// Recarga de documentos.
$string['newdocument'] = 'Nuevo documento';
$string['uploaddocument'] = 'Subir documento';
$string['reuploadhelp'] = 'Suba una nueva versión del documento que fue rechazado. Asegúrese de que el nuevo documento atienda el motivo de rechazo.';
$string['documentreuploaded'] = 'Documento subido exitosamente. Su postulación está nuevamente en revisión.';
$string['uploadfailed'] = 'Error al subir el documento. Por favor intente de nuevo.';
$string['cannotreupload'] = 'No puede volver a cargar documentos para esta postulación en su estado actual.';

// Configuración de navegación.
$string['navigationsettings'] = 'Configuración de navegación';
$string['navigationsettings_desc'] = 'Configure cómo aparece Bolsa de Empleo en la navegación del sitio.';
$string['showinmainmenu'] = 'Mostrar en menú de navegación principal';
$string['showinmainmenu_desc'] = 'Si está habilitado, Bolsa de Empleo aparecerá en el menú de navegación principal (barra superior) con submenús desplegables.';
$string['mainmenutitle'] = 'Título del menú';
$string['mainmenutitle_desc'] = 'Título personalizado para el elemento de menú de Bolsa de Empleo. Déjelo vacío para usar el nombre predeterminado del plugin.';
$string['loginrequiredtoapply'] = 'Debe iniciar sesión para postularse a las vacantes.';

// ==========================================================================
// Cadenas adicionales para completar (Fase 8.3).
// ==========================================================================

// Detalles de entrevista adicionales (EN keys).
$string['interviewdetails'] = 'Detalles de la entrevista';
$string['interviewtime'] = 'Hora de la entrevista';
$string['interviewlocation'] = 'Ubicación de la entrevista';
$string['interviewtype_presencial'] = 'Presencial';
$string['interviewtype_virtual'] = 'Virtual';
$string['interviewtype_telefonica'] = 'Telefónica';
$string['interviewlink'] = 'Enlace de la reunión';
$string['interviewupdated'] = 'Entrevista actualizada exitosamente';
$string['confirmcancelinterview'] = '¿Está seguro de que desea cancelar esta entrevista?';
$string['rescheduleinterview'] = 'Reprogramar entrevista';
$string['rescheduledby'] = 'Reprogramada por';
$string['cancelnote'] = 'Nota de cancelación';
$string['pastinterviews'] = 'Entrevistas pasadas';
$string['upcominginterviews'] = 'Próximas entrevistas';
$string['nointerviewsscheduled'] = 'No hay entrevistas programadas';
$string['recordresults'] = 'Registrar resultados';
$string['interviewresults'] = 'Resultados de la entrevista';
$string['attended'] = 'Asistió';
$string['interviewresult'] = 'Resultado de la entrevista';
$string['result_favorable'] = 'Favorable';
$string['result_no_favorable'] = 'No favorable';
$string['result_pendiente'] = 'Pendiente de evaluación';
$string['interviewscore'] = 'Puntaje de la entrevista';
$string['interviewobservations'] = 'Observaciones de la entrevista';
$string['resultrecorded'] = 'Resultado registrado exitosamente';

// Comité de selección adicional (EN keys).
$string['editcommittee'] = 'Editar comité';
$string['committeedetails'] = 'Detalles del comité';
$string['committeeupdated'] = 'Comité actualizado exitosamente';
$string['committeedeleted'] = 'Comité eliminado';
$string['nocommittees'] = 'No hay comités creados';
$string['quorum'] = 'Quórum';
$string['quorummet'] = 'Quórum alcanzado';
$string['quorumnotmet'] = 'Quórum no alcanzado';

// Evaluación adicional (EN keys).
$string['evaluateapplicant'] = 'Evaluar postulante';
$string['score'] = 'Puntaje';
$string['totalscore'] = 'Puntaje total';
$string['evaluationnotes'] = 'Notas de evaluación';
$string['submitevaluation'] = 'Enviar evaluación';
$string['allevaluations'] = 'Todas las evaluaciones';
$string['pendingevaluations'] = 'Evaluaciones pendientes';
$string['completedevaluations'] = 'Evaluaciones completadas';
$string['myevaluations'] = 'Mis evaluaciones';
$string['viewevaluations'] = 'Ver evaluaciones';

// Importación adicional (EN keys).
$string['importfromcsv'] = 'Importar desde CSV';
$string['csvformat'] = 'Formato CSV';
$string['rowsprocessed'] = 'Filas procesadas';
$string['rowsimported'] = 'Filas importadas';
$string['rowsfailed'] = 'Filas fallidas';

// Gestión de tipos de documentos adicional (EN keys).
$string['managedoctypes'] = 'Gestionar tipos de documentos';

// User Tours - Tour del Administrador.
$string['tour_admin_name'] = 'Tour de Administrador - Bolsa de Empleo';
$string['tour_admin_description'] = 'Aprenda a gestionar vacantes y postulaciones en la Bolsa de Empleo';
$string['tour_admin_step1_title'] = 'Bienvenido a la Bolsa de Empleo';
$string['tour_admin_step1_content'] = 'Bienvenido al plugin de Bolsa de Empleo! Este tour guiado le mostrara como gestionar vacantes y postulaciones de manera efectiva. El plugin le permite crear vacantes, recibir postulaciones, validar documentos y gestionar el proceso de seleccion completo.';
$string['tour_admin_step2_title'] = 'Vista General del Panel';
$string['tour_admin_step2_content'] = 'Este es su panel principal. Aqui puede ver un resumen de todas las vacantes activas, postulaciones pendientes y documentos por revisar. Use el panel para acceder rapidamente a la informacion mas importante.';
$string['tour_admin_step3_title'] = 'Estadisticas Rapidas';
$string['tour_admin_step3_content'] = 'Estas estadisticas le muestran metricas clave de un vistazo: total de vacantes, postulaciones activas, documentos pendientes de revision y mas. Haga clic en cualquier estadistica para ver informacion detallada.';
$string['tour_admin_step4_title'] = 'Crear Nueva Vacante';
$string['tour_admin_step4_content'] = 'Haga clic aqui para crear una nueva vacante. Debera proporcionar detalles como titulo, descripcion, requisitos, documentos requeridos y fechas de postulacion. Una vez publicada, los candidatos podran empezar a postularse.';
$string['tour_admin_step5_title'] = 'Lista de Vacantes';
$string['tour_admin_step5_content'] = 'Esta es la lista de todas las vacantes. Puede filtrar por estado (borrador, publicada, cerrada) y realizar acciones como editar, publicar, cerrar o ver postulaciones de cada vacante.';
$string['tour_admin_step6_title'] = 'Listo para Comenzar!';
$string['tour_admin_step6_content'] = 'Ya esta listo para empezar a usar la Bolsa de Empleo! Recuerde: puede gestionar tipos de documentos, configurar plantillas de correo, crear comites de seleccion y generar reportes. Explore el menu de administracion para mas opciones.';

// User Tours - Tour del Postulante.
$string['tour_applicant_name'] = 'Tour de Postulante - Bolsa de Empleo';
$string['tour_applicant_description'] = 'Aprenda a buscar vacantes y postularse';
$string['tour_applicant_step1_title'] = 'Bienvenido a la Bolsa de Empleo';
$string['tour_applicant_step1_content'] = 'Bienvenido! Este tour guiado le mostrara como encontrar vacantes disponibles y enviar su postulacion. El proceso es sencillo: encuentre una vacante que le interese, prepare sus documentos y postulese.';
$string['tour_applicant_step2_title'] = 'Vacantes Disponibles';
$string['tour_applicant_step2_content'] = 'Aqui puede ver todas las vacantes disponibles. Cada vacante muestra el titulo, ubicacion, tipo de contrato y fecha de cierre. Use los filtros para encontrar vacantes que se ajusten a su perfil.';
$string['tour_applicant_step3_title'] = 'Detalles de la Vacante';
$string['tour_applicant_step3_content'] = 'Haga clic en cualquier vacante para ver los detalles completos: descripcion completa, requisitos, beneficios y documentos requeridos. Asegurese de cumplir con los requisitos antes de postularse.';
$string['tour_applicant_step4_title'] = 'Postularse a una Vacante';
$string['tour_applicant_step4_content'] = 'Cuando encuentre una vacante que le interese, haga clic en "Postularse" para iniciar su postulacion. Debera cargar los documentos requeridos. Asegurese de que sus documentos sean legibles y esten actualizados.';
$string['tour_applicant_step5_title'] = 'Seguimiento de sus Postulaciones';
$string['tour_applicant_step5_content'] = 'Puede dar seguimiento al estado de sus postulaciones en cualquier momento desde "Mis Postulaciones". Recibira notificaciones cuando haya actualizaciones en el estado de su postulacion. Mucha suerte!';

// User Tours - Comun.
$string['tour_endlabel'] = 'Entendido!';

// Cadenas faltantes - Fase 8.8: Cobertura completa de cadenas de idiomas.
// Relacionadas con asignacion y revisores.
$string['activeassignments'] = 'Asignaciones activas';
$string['assigned'] = 'Asignado';
$string['assignselected'] = 'Asignar seleccionados';
$string['assignto'] = 'Asignar a';
$string['autoassignall'] = 'Auto-asignar todos';
$string['autoassigncomplete'] = 'Auto-asignacion completada. {$a->assigned} postulaciones asignadas a {$a->reviewers} revisores.';
$string['autoassignhelp'] = 'Distribuir automaticamente las postulaciones pendientes entre los revisores disponibles segun su carga de trabajo actual.';
$string['manualassign'] = 'Asignacion manual';
$string['maxperreviewer'] = 'Maximo por revisor';
$string['nodocumentspending'] = 'No hay documentos pendientes de revision';
$string['norejections'] = 'Sin rechazos';
$string['noreviewers'] = 'No hay revisores disponibles';
$string['nounassignedapplications'] = 'No hay postulaciones sin asignar';
$string['reviewed'] = 'Revisado';
$string['reviewerunassigned'] = 'Revisor desasignado exitosamente';

// Validacion y documentos.
$string['alreadyvalidated'] = 'Este documento ya ha sido validado';
$string['autovalidated'] = 'Auto-validado';
$string['bulkactions'] = 'Acciones masivas';
$string['bulkvalidationcomplete'] = 'Validacion masiva completada. {$a->approved} aprobados, {$a->rejected} rechazados.';
$string['documentnotfound'] = 'Documento no encontrado';
$string['doctypeshelp'] = 'Configure los tipos de documentos que los postulantes pueden cargar. Cada tipo puede marcarse como requerido u opcional.';
$string['optionalnotes'] = 'Notas opcionales';
$string['rejectionreason'] = 'Razon de rechazo';
$string['rejectionreasons'] = 'Razones de rechazo';
$string['validationsummary'] = 'Resumen de validacion';

// Relacionadas con API.
$string['api:authheader'] = 'Encabezado de autorizacion';
$string['api:baseurl'] = 'URL base';
$string['api:info'] = 'Informacion de API';
$string['api:ratelimit'] = 'Limite de solicitudes';
$string['api:requestsperhour'] = 'solicitudes por hora';
$string['api:token:copywarning'] = 'Este token solo se mostrara una vez. Copielo ahora y guardelo de forma segura.';
$string['api:token:deleteconfirm'] = 'Esta seguro de que desea eliminar permanentemente este token de API? Esta accion no se puede deshacer.';
$string['api:token:none'] = 'No se encontraron tokens de API';
$string['api:token:revokeconfirm'] = 'Esta seguro de que desea revocar este token de API? Ya no funcionara para autenticacion.';
$string['api:token:usage'] = 'Uso del token';

// Reportes y estadisticas.
$string['applicationsbyvacancy'] = 'Postulaciones por vacante';
$string['avgtime'] = 'Tiempo promedio';

// Importacion CSV.
$string['csvimporterror'] = 'Error al importar archivo CSV';
$string['csvinvalidtype'] = 'Tipo de excepcion invalido: {$a}';
$string['csvlineerror'] = 'Error en linea {$a->line}: {$a->error}';
$string['csvusernotfound'] = 'Usuario no encontrado: {$a}';

// Encriptacion.
$string['encryption:backupinstructions'] = 'Descargue y guarde de forma segura esta clave de encriptacion. La necesitara para descifrar documentos si restaura desde un respaldo.';
$string['encryption:nokeytobackup'] = 'No hay clave de encriptacion para respaldar. Habilite la encriptacion primero.';

// Mensajes de error.
$string['error:invalidpublicationtype'] = 'Tipo de publicacion invalido';
$string['error:invalidstatus'] = 'Estado invalido';
$string['vacancynotfound'] = 'Vacante no encontrada';

// Miscelaneos.
$string['lastused'] = 'Ultimo uso';
$string['selecttype'] = 'Seleccionar tipo';
$string['share'] = 'Compartir';
$string['loading'] = 'Cargando...';
$string['selectioncommittee'] = 'Comite de seleccion';
