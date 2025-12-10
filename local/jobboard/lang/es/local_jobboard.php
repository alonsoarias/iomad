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
$string['addvacancy'] = 'Agregar Vacante';
$string['viewvacancies'] = 'Ver Vacantes';
$string['managevacancies'] = 'Gestionar Vacantes';
$string['reviewapplications'] = 'Revisar Postulaciones';
$string['reports'] = 'Reportes';
$string['settings'] = 'Configuración';
$string['dashboard'] = 'Panel de Control';
$string['exemptions'] = 'Excepciones ISER';

// Capabilities.
$string['jobboard:view'] = 'Ver bolsa de empleo';
$string['jobboard:viewinternal'] = 'Ver vacantes internas';
$string['jobboard:manage'] = 'Gestionar bolsa de empleo';
$string['jobboard:createvacancy'] = 'Crear vacantes';
$string['jobboard:editvacancy'] = 'Editar vacantes';
$string['jobboard:deletevacancy'] = 'Eliminar vacantes';
$string['jobboard:publishvacancy'] = 'Publicar vacantes';
$string['jobboard:viewallvacancies'] = 'Ver todas las vacantes';
$string['jobboard:manageconvocatorias'] = 'Gestionar convocatorias';
$string['jobboard:apply'] = 'Postularse a vacantes';
$string['jobboard:viewownapplications'] = 'Ver postulaciones propias';
$string['jobboard:viewallapplications'] = 'Ver todas las postulaciones';
$string['jobboard:changeapplicationstatus'] = 'Cambiar estado de postulacion';
$string['jobboard:review'] = 'Revisar postulaciones';
$string['jobboard:validatedocuments'] = 'Validar documentos';
$string['jobboard:reviewdocuments'] = 'Revisar documentos';
$string['jobboard:assignreviewers'] = 'Asignar revisores';
$string['jobboard:downloadanydocument'] = 'Descargar cualquier documento';
$string['jobboard:evaluate'] = 'Evaluar candidatos';
$string['jobboard:viewevaluations'] = 'Ver evaluaciones';
$string['jobboard:manageworkflow'] = 'Gestionar flujo de trabajo';
$string['jobboard:viewreports'] = 'Ver reportes';
$string['jobboard:exportreports'] = 'Exportar reportes';
$string['jobboard:exportdata'] = 'Exportar datos';
$string['jobboard:manageexemptions'] = 'Gestionar excepciones ISER';
$string['jobboard:useapi'] = 'Usar API REST';
$string['jobboard:accessapi'] = 'Acceder a la API REST';
$string['jobboard:manageapitokens'] = 'Gestionar tokens API';
$string['jobboard:configure'] = 'Configurar bolsa de empleo';
$string['jobboard:managedoctypes'] = 'Gestionar tipos de documentos';
$string['jobboard:manageemailtemplates'] = 'Gestionar plantillas de correo';
$string['jobboard:viewpublicvacancies'] = 'Ver vacantes publicas';
$string['jobboard:viewinternalvacancies'] = 'Ver vacantes internas';
$string['jobboard:unlimitedapplications'] = 'Postulaciones ilimitadas';

// Roles personalizados.
$string['role_reviewer'] = 'Revisor de Documentos de Bolsa de Empleo';
$string['role_reviewer_desc'] = 'Puede revisar y validar documentos enviados por los postulantes en el sistema de bolsa de empleo.';
$string['role_coordinator'] = 'Coordinador de Bolsa de Empleo';
$string['role_coordinator_desc'] = 'Puede gestionar vacantes, convocatorias y coordinar el proceso de seleccion.';
$string['role_committee'] = 'Comite de Seleccion de Bolsa de Empleo';
$string['role_committee_desc'] = 'Puede evaluar candidatos y participar en las decisiones finales de seleccion.';

// Vacancy fields.
$string['vacancycode'] = 'Código de vacante';
$string['vacancycode_help'] = 'Código único interno para identificar la vacante dentro del sistema.

**Formato recomendado:** Use un código alfanumérico corto y descriptivo, por ejemplo:
- DOC-2025-001 (para docentes)
- ADM-2025-003 (para administrativos)
- TEMP-2025-010 (para temporales)

**Importante:**
- El código debe ser único y no puede repetirse
- Una vez creada la vacante, el código no puede modificarse
- Use guiones (-) para separar secciones del código
- Evite caracteres especiales y espacios';
$string['vacancytitle'] = 'Título de la vacante';
$string['vacancytitle_help'] = 'Nombre oficial del cargo o posición que se oferta.

**Recomendaciones:**
- Sea claro y específico (ej: "Docente de Matemáticas - Jornada Completa")
- Incluya información relevante como jornada o nivel si aplica
- Evite abreviaturas que no sean ampliamente conocidas
- El título será visible públicamente para los postulantes

**Ejemplos correctos:**
- Docente Catedrático - Programa de Ingeniería de Sistemas
- Profesional de Apoyo Académico - Facultad de Ciencias
- Docente Tiempo Completo - Área de Humanidades';
$string['vacancydescription'] = 'Descripción';
$string['vacancydescription_help'] = 'Descripción detallada del cargo, funciones y responsabilidades.

**Incluya información sobre:**
- Funciones principales del cargo
- Responsabilidades específicas
- A quién reporta (si aplica)
- Equipo de trabajo
- Proyectos o actividades especiales

**Consejos de redacción:**
- Use viñetas para listar funciones
- Sea específico sobre las expectativas
- Mencione si hay viajes o actividades fuera de horario
- Indique si es trabajo presencial, remoto o híbrido

**Longitud recomendada:** 200-500 palabras';
$string['contracttype'] = 'Tipo de contrato';
$string['contracttype_help'] = 'Modalidad de vinculación laboral ofrecida para esta posición.

**Tipos disponibles:**

**Planta:** Vinculación permanente con estabilidad laboral y todos los beneficios institucionales.

**Término Fijo:** Contrato por período determinado (usualmente 6-12 meses) con posibilidad de renovación según evaluación de desempeño.

**Ocasional:** Vinculación temporal para actividades específicas o proyectos puntuales, generalmente inferior a 6 meses.

**Cátedra:** Vinculación por horas para impartir asignaturas específicas. La remuneración se calcula por hora de clase.

**Prestación de Servicios:** Contrato civil para servicios profesionales específicos, sin relación laboral directa.

**Nota:** Cada tipo de contrato tiene implicaciones diferentes en términos de beneficios, seguridad social y estabilidad laboral.';
$string['duration'] = 'Duración';
$string['duration_help'] = 'Duración estimada del contrato o período de vinculación.

**Ejemplos de formato:**
- "6 meses con posibilidad de renovación"
- "Semestre académico 2025-1"
- "1 año - Término fijo"
- "Mientras dure el proyecto X"
- "Indefinido"

**Consideraciones:**
- Para contratos a término fijo, especifique claramente el período
- Mencione si existe posibilidad de renovación o prórroga
- Para docentes, puede indicar el período académico
- Si depende de un proyecto, indique la duración esperada del mismo';
// Campo salary eliminado en Fase 10 - remuneración manejada externamente.
$string['location'] = 'Ubicación';
$string['location_help'] = 'Lugar físico donde se desempeñarán las funciones del cargo.

**Información a incluir:**
- Ciudad o municipio
- Sede específica (si hay varias)
- Dirección o campus
- Si es trabajo remoto, híbrido o presencial

**Ejemplos:**
- "Sede Principal - Bogotá, Carrera 7 No. 45-20"
- "Campus Norte - Medellín (trabajo presencial)"
- "Trabajo remoto con reuniones presenciales mensuales en Cali"
- "Híbrido: 3 días presencial en Sede Centro, 2 días remoto"

**Importante para postulantes:** Esta información les permite evaluar si pueden asumir el desplazamiento necesario.';
$string['modality'] = 'Modalidad';
$string['modality_help'] = 'Modalidad educativa para esta posición: Presencial (en sitio), A Distancia (educación a distancia), Virtual (en línea), o Híbrida (combinación).';
$string['department'] = 'Departamento/Unidad';
$string['department_help'] = 'Área, facultad, departamento o unidad organizacional donde se desempeñará el cargo.

**Ejemplos:**
- Facultad de Ingeniería
- Departamento de Matemáticas y Estadística
- Vicerrectoría Académica
- Oficina de Bienestar Universitario
- Escuela de Ciencias Básicas

**Utilidad:**
- Ayuda a los postulantes a identificar el área de trabajo
- Facilita la organización interna de vacantes
- Permite filtrar vacantes por departamento
- Define el contexto organizacional del cargo';
$string['company'] = 'Empresa/Sede';
$string['company_help'] = 'Seleccione la empresa o sede donde se publicará esta vacante.

**En entornos multi-tenant (IOMAD):**
- Cada empresa tiene su propia gestión de vacantes
- Los postulantes solo verán las vacantes de la empresa correspondiente
- Las métricas y reportes se segmentan por empresa

**Nota para administradores:**
- Si gestiona múltiples sedes, asegúrese de seleccionar la correcta
- Las vacantes internas solo serán visibles para usuarios de la empresa seleccionada
- Las vacantes públicas pueden ser visibles para todos según la configuración';
$string['opendate'] = 'Fecha de apertura';
$string['opendate_help'] = 'Fecha y hora desde la cual se aceptarán postulaciones para esta vacante.

**Consideraciones importantes:**
- Los postulantes NO podrán enviar solicitudes antes de esta fecha
- La vacante será visible pero mostrará "Próximamente" si está publicada antes de la fecha de apertura
- La hora se considera en la zona horaria del servidor

**Recomendaciones:**
- Publique la vacante unos días antes de la apertura para dar visibilidad
- Coordine la fecha con el área de comunicaciones si es una convocatoria importante
- Considere días hábiles para iniciar (evite fines de semana)

**Si pertenece a una convocatoria:** La fecha se heredará de la convocatoria, a menos que marque esta vacante como "extemporánea".';
$string['closedate'] = 'Fecha de cierre';
$string['closedate_help'] = 'Fecha y hora límite para recibir postulaciones.

**Importante:**
- Después de esta fecha, el sistema NO permitirá nuevas postulaciones
- Los postulantes recibirán avisos cuando la fecha de cierre esté próxima
- Incluye la hora exacta (ej: 23:59 del día seleccionado)

**Recomendaciones:**
- Deje suficiente tiempo para que los postulantes preparen sus documentos (mínimo 7-15 días)
- Para convocatorias grandes, considere 30 días o más
- Evite cerrar en días festivos o fines de semana

**Nota:** Si necesita extender el plazo, puede editar la fecha mientras la vacante esté abierta. Los postulantes serán notificados del cambio.';
$string['positions'] = 'Número de vacantes';
$string['positions_help'] = 'Cantidad de personas que se contratarán para este cargo.

**Uso:**
- Si necesita contratar varias personas para el mismo cargo, indique el número
- Para una sola posición, deje el valor en 1
- Esto ayuda a los postulantes a conocer la magnitud de la convocatoria

**Ejemplo:**
Si necesita 3 docentes de matemáticas para diferentes jornadas, puede:
1. Crear una vacante con positions=3
2. O crear 3 vacantes separadas con información específica de cada jornada

**Recomendación:** Si las condiciones son diferentes (horarios, sedes, etc.), es mejor crear vacantes separadas para mayor claridad.';
$string['requirements'] = 'Requisitos mínimos';
$string['requirements_help'] = 'Liste los requisitos INDISPENSABLES que debe cumplir el candidato para ser considerado.

**Estructura recomendada:**

**Formación académica:**
- Título profesional requerido
- Especialización o maestría si es necesaria
- Tarjeta profesional vigente (si aplica)

**Experiencia:**
- Años de experiencia mínima
- Tipo de experiencia (docente, profesional, investigativa)
- Sectores o áreas específicas

**Conocimientos técnicos:**
- Software o herramientas específicas
- Idiomas (indique nivel requerido)
- Certificaciones obligatorias

**Otros:**
- Disponibilidad horaria
- Documentación legal vigente
- Requisitos especiales del cargo

**Importante:** Los postulantes que no cumplan estos requisitos NO pasarán la fase de verificación documental.';
$string['desirable'] = 'Requisitos deseables';
$string['desirable_help'] = 'Liste los requisitos DESEABLES que suman puntos pero NO son eliminatorios.

**Diferencia con requisitos mínimos:**
- Los mínimos son OBLIGATORIOS - sin ellos, el postulante queda excluido
- Los deseables SON OPCIONALES - dan ventaja pero no excluyen

**Ejemplos de requisitos deseables:**

**Formación adicional:**
- Doctorado o estudios doctorales en curso
- Cursos o diplomados en áreas relacionadas
- Certificaciones adicionales

**Experiencia valorada:**
- Experiencia en investigación
- Publicaciones académicas
- Participación en proyectos especiales
- Experiencia en el sector público/privado

**Habilidades:**
- Dominio de segundo idioma
- Conocimiento de metodologías específicas
- Experiencia con poblaciones especiales

**Consejo:** Estos criterios pueden usarse para diferenciar candidatos que cumplan todos los requisitos mínimos.';

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
$string['selectcategory'] = 'Seleccione una categoría';
$string['selectcompany'] = 'Seleccione una empresa/sede';
$string['selectdepartment'] = 'Seleccione un departamento';
$string['selectstatus'] = 'Seleccione estado';
$string['uploadfile'] = 'Subir archivo';
$string['choosefiles'] = 'Seleccionar archivos';
$string['nodocuments'] = 'No hay documentos subidos';

// Contract types.
$string['contract:catedra'] = 'Profesor Cátedra';
$string['contract:temporal'] = 'Temporal';
$string['contract:termino_fijo'] = 'Término Fijo';
$string['contract:prestacion_servicios'] = 'Prestación de Servicios';
$string['contract:planta'] = 'Planta';

// Educational modalities.
$string['modality'] = 'Modalidad';
$string['modality_help'] = 'Modalidad educativa del programa académico.';
$string['modality:presencial'] = 'Presencial';
$string['modality:distancia'] = 'A Distancia';
$string['modality:virtual'] = 'Virtual';
$string['modality:hibrida'] = 'Híbrida';
$string['selectmodality'] = 'Seleccione modalidad...';

// Actions.
$string['create'] = 'Crear';
$string['edit'] = 'Editar';
$string['delete'] = 'Eliminar';
$string['view'] = 'Ver';
$string['publish'] = 'Publicar';
$string['unpublish'] = 'Despublicar';
$string['close'] = 'Cerrar';
$string['reopen'] = 'Reabrir';
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
$string['import'] = 'Importar';
$string['download'] = 'Descargar';
$string['upload'] = 'Cargar';
$string['preview'] = 'Vista previa';
$string['validate'] = 'Validar';
$string['reject'] = 'Rechazar';
$string['approve'] = 'Aprobar';
$string['resubmit'] = 'Reenviar';
$string['manage'] = 'Gestionar';

// Herramientas de migración.
$string['migrationtools'] = 'Herramientas de Migración';
$string['importvacancies'] = 'Importar Vacantes';
$string['importvacancies_desc'] = 'Importar vacantes desde archivo CSV';
$string['importexemptions'] = 'Importar Exenciones';
$string['importexemptions_desc'] = 'Importar exenciones ISER desde CSV';
$string['exportdocuments'] = 'Exportar Documentos';
$string['exportdocuments_desc'] = 'Exportar documentos de postulaciones como ZIP';
$string['manageexemptions'] = 'Gestionar Exenciones';
$string['manageexemptions_desc'] = 'Gestionar exenciones de documentos por usuario';

// Strings de migración.
$string['migrateplugin'] = 'Migrar Plugin';
$string['migrateplugin_desc'] = 'Exportar o importar configuración y datos del plugin entre instancias de Moodle';
$string['exportdata'] = 'Exportar Datos';
$string['exportdata_desc'] = 'Exportar configuración del plugin a un archivo JSON que puede importarse en otra instancia.';
$string['importdata'] = 'Importar Datos';
$string['importdata_desc'] = 'Importar configuración del plugin desde un archivo JSON previamente exportado.';
$string['migrationfile'] = 'Archivo de migración (JSON)';
$string['overwriteexisting'] = 'Sobrescribir registros existentes';
$string['dryrunmode'] = 'Modo prueba (previsualizar sin guardar)';
$string['exportdownload'] = 'Descargar Exportación';
$string['importupload'] = 'Subir e Importar';
$string['importwarning'] = 'Advertencia: La importación modificará su base de datos. Use el modo prueba primero para previsualizar los cambios.';
$string['invalidmigrationfile'] = 'Archivo de migración inválido. Por favor suba un archivo de exportación JobBoard válido.';
$string['dryrunresults'] = 'Resultados del Modo Prueba (sin cambios realizados):';
$string['importerror'] = 'Error de importación';
$string['pluginsettings'] = 'Configuración del plugin';
$string['exemptions'] = 'Exenciones de usuario';
$string['importeddoctypes'] = 'Tipos de documento: {$a->inserted} insertados, {$a->updated} actualizados, {$a->skipped} omitidos';
$string['importedemails'] = 'Plantillas de email: {$a->inserted} insertadas, {$a->updated} actualizadas, {$a->skipped} omitidas';
$string['importedconvocatorias'] = 'Convocatorias: {$a->inserted} insertadas, {$a->updated} actualizadas, {$a->skipped} omitidas';
$string['importedvacancies'] = 'Vacantes: {$a->inserted} insertadas, {$a->updated} actualizadas, {$a->skipped} omitidas';
$string['importedsettings'] = 'Configuraciones: {$a} actualizadas';
$string['importedexemptions'] = 'Exenciones: {$a->inserted} insertadas, {$a->updated} actualizadas, {$a->skipped} omitidas';
$string['importedapplications'] = 'Postulaciones: {$a->inserted} insertadas, {$a->skipped} omitidas';
$string['importeddocuments'] = 'Documentos: {$a->inserted} insertados, {$a->skipped} omitidos';
$string['importedfiles'] = 'Archivos: {$a->inserted} insertados, {$a->skipped} omitidos';
$string['importingfrom'] = 'Importando desde {$a->site} (v{$a->version}) exportado el {$a->date}';
$string['fullexport'] = 'Exportación Completa';
$string['fullexport_info'] = 'Esto exportará TODOS los datos del plugin incluyendo postulaciones, documentos, archivos y configuraciones. El archivo ZIP puede importarse en otra instancia de Moodle IOMAD con JobBoard instalado.';
$string['datatorexport'] = 'Datos a exportar';
$string['exportwarning_files'] = 'La exportación incluye archivos y puede tomar tiempo en generarse. Por favor espere...';
$string['documents'] = 'Documentos';
$string['files'] = 'Archivos';
$string['migrationinfo_title'] = 'Acerca de la Migración';
$string['migrationinfo_desc'] = 'Esta herramienta permite transferir TODOS los datos de JobBoard entre instancias de Moodle. La exportación crea un archivo ZIP con todos los registros de base de datos y archivos. La importación lee el ZIP y restaura los datos con mapeo de IDs para registros relacionados. Ningún dato es opcional - todo se exporta.';
$string['exporterror'] = 'Error de exportación';
$string['applications'] = 'Postulaciones';
$string['auditlogs'] = 'Registros de auditoría';

// Secciones del dashboard.
$string['datatools'] = 'Importar/Exportar Datos';
$string['systemmigration'] = 'Migración del Sistema';
$string['reports_desc'] = 'Ver estadísticas y reportes';
$string['pluginsettings_desc'] = 'Configurar opciones del plugin';
$string['doctypes_desc'] = 'Gestionar tipos de documentos requeridos';
$string['migrateplugin_full_desc'] = 'Transferir TODOS los datos del plugin a otra instancia de Moodle IOMAD. Crea un respaldo completo que puede restaurarse en una nueva instalación.';
$string['migrate_includes_doctypes'] = 'Tipos de documento y configuraciones';
$string['migrate_includes_convocatorias'] = 'Convocatorias con excepciones';
$string['migrate_includes_vacancies'] = 'Todas las vacantes y configuraciones';
$string['migrate_includes_applications'] = 'Postulaciones con documentos';
$string['migrate_includes_files'] = 'Todos los archivos cargados';
$string['openmigrationtool'] = 'Abrir Herramienta de Migración';

// Cadenas de vista pública.
$string['totalpositions'] = 'Posiciones Totales';
$string['closingsoon'] = 'Próximas a Cerrar';
$string['closesindays'] = 'Cierra en {$a} días';
$string['noconvocatorias'] = 'No hay convocatorias activas en este momento';
$string['startdate'] = 'Fecha de Inicio';
$string['enddate'] = 'Fecha de Cierre';
$string['vacancy'] = 'Vacante';
$string['public'] = 'Pública';
$string['internal'] = 'Interna';
$string['type'] = 'Tipo';
$string['convocatoria_footer_info'] = 'Esta convocatoria tiene {$a->vacancies} vacantes con {$a->positions} posiciones disponibles en total.';

// Messages.
$string['vacancycreated'] = 'Vacante creada exitosamente';
$string['vacancyupdated'] = 'Vacante actualizada exitosamente';
$string['vacancydeleted'] = 'Vacante eliminada exitosamente';
$string['vacancypublished'] = 'Vacante publicada exitosamente';
$string['vacancyclosed'] = 'Vacante cerrada exitosamente';
$string['vacancyreopened'] = 'Vacante reabierta exitosamente';
$string['vacancyunpublished'] = 'Vacante despublicada exitosamente';
$string['applicationsubmitted'] = 'Postulación enviada exitosamente';
$string['applicationwithdrawn'] = 'Postulación retirada exitosamente';
$string['documentuploaded'] = 'Documento cargado exitosamente';
$string['documentvalidated'] = 'Documento validado exitosamente';
$string['documentrejected'] = 'Documento rechazado';
$string['changesaved'] = 'Cambios guardados exitosamente';

// Errors.
$string['error:vacancynotfound'] = 'Vacante no encontrada';
$string['error:vacancynotpublic'] = 'Esta vacante no está disponible públicamente';
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
$string['error:cannotdelete_hasapplications'] = 'No se puede eliminar la vacante: hay {$a} postulación(es) asociada(s)';
$string['error:cannotunpublish'] = 'No se puede despublicar la vacante: hay postulaciones asociadas';
$string['error:cannotclose'] = 'No se puede cerrar la vacante: debe estar en estado publicado';
$string['error:cannotreopen'] = 'No se puede reabrir la vacante: debe estar en estado cerrado';

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
$string['exemptionvalid'] = 'Excepción vigente';
$string['exemptionexpired'] = 'Excepción vencida';
$string['manageexemptions'] = 'Gestionar excepciones ISER';
$string['addexemption'] = 'Agregar excepción';
$string['editexemption'] = 'Editar excepción';
$string['deleteexemption'] = 'Eliminar excepción';
$string['exemptionimported'] = 'Excepciones importadas exitosamente';

// Convocatorias.
$string['convocatoria'] = 'Convocatoria';
$string['convocatorias'] = 'Convocatorias';
$string['manageconvocatorias'] = 'Gestionar Convocatorias';
$string['addconvocatoria'] = 'Agregar convocatoria';
$string['editconvocatoria'] = 'Editar convocatoria';
$string['deleteconvocatoria'] = 'Eliminar convocatoria';
$string['convocatoriacode'] = 'Código de convocatoria';
$string['convocatorianame'] = 'Nombre de convocatoria';
$string['convocatoriadescription'] = 'Descripción';
$string['convocatoriastartdate'] = 'Fecha de inicio';
$string['convocatoriaenddate'] = 'Fecha de cierre';
$string['convocatoriastatus'] = 'Estado';
$string['convocatoriaterms'] = 'Términos y condiciones';
$string['convocatoriavacancies'] = 'Vacantes de esta convocatoria';
$string['convocatoria_status_draft'] = 'Borrador';
$string['convocatoria_status_open'] = 'Abierta';
$string['convocatoria_status_closed'] = 'Cerrada';
$string['convocatoria_status_archived'] = 'Archivada';
$string['convocatoriacreated'] = 'Convocatoria creada exitosamente';
$string['convocatoriaupdated'] = 'Convocatoria actualizada exitosamente';
$string['convocatoriadeleted'] = 'Convocatoria eliminada exitosamente';
$string['noconvocatorias'] = 'No se encontraron convocatorias';
$string['convocatorianotfound'] = 'Convocatoria no encontrada';
$string['convocatoriaactive'] = 'Convocatorias activas';
$string['convocatoriaclosed'] = 'Convocatorias cerradas';
$string['viewconvocatoria'] = 'Ver convocatoria';
$string['convocatoriadetails'] = 'Detalles de la convocatoria';
$string['selectconvocatoria'] = 'Seleccione una convocatoria';
$string['convocatoriahelp'] = 'Una convocatoria agrupa vacantes relacionadas y define el período durante el cual se aceptan postulaciones.';
$string['convocatoriavacancycount'] = '{$a} vacantes';
$string['createvacancyinconvocatoria'] = 'Agregar vacante a esta convocatoria';
$string['confirmdeletevconvocatoria'] = '¿Está seguro de eliminar esta convocatoria? Las vacantes asociadas no serán eliminadas.';
$string['convocatoriawithvacancies'] = 'Esta convocatoria tiene {$a} vacantes. Serán desvinculadas pero no eliminadas.';
$string['openconvocatoria'] = 'Abrir convocatoria';
$string['closeconvocatoria'] = 'Cerrar convocatoria';
$string['archiveconvocatoria'] = 'Archivar convocatoria';
$string['reopenconvocatoria'] = 'Reabrir convocatoria';
$string['confirmopenconvocatoria'] = '¿Está seguro de abrir esta convocatoria? Todas las vacantes en borrador serán publicadas.';
$string['confirmcloseconvocatoria'] = '¿Está seguro de cerrar esta convocatoria? Todas las vacantes serán cerradas.';
$string['confirmreopenconvocatoria'] = '¿Está seguro de reabrir esta convocatoria? Las vacantes cerradas volverán a estar publicadas.';
$string['confirmarchiveconvocatoria'] = '¿Está seguro de archivar esta convocatoria? Esta acción es para convocatorias finalizadas.';
$string['convocatoriaopened'] = 'Convocatoria abierta exitosamente';
$string['convocatoriaclosedmsg'] = 'Convocatoria cerrada exitosamente';
$string['convocatoriaarchived'] = 'Convocatoria archivada exitosamente';
$string['convocatoriareopened'] = 'Convocatoria reabierta exitosamente';
$string['error:convocatoriahasnovacancies'] = 'No se puede abrir una convocatoria sin vacantes';
$string['error:cannotreopenconvocatoria'] = 'No se puede reabrir la convocatoria: debe estar en estado cerrado';
$string['error:convocatoriadatesinvalid'] = 'La fecha de cierre debe ser posterior a la fecha de inicio';
$string['error:convocatoriacodeexists'] = 'Ya existe una convocatoria con este código';
$string['error:cannotdeleteconvocatoria'] = 'No se puede eliminar esta convocatoria. Solo se pueden eliminar convocatorias en borrador o archivadas';
$string['dates'] = 'Fechas';
$string['vacanciesforconvocatoria'] = 'Vacantes de la convocatoria';
$string['backtoconvocatorias'] = 'Volver a convocatorias';
$string['backtoconvocatoria'] = 'Volver a la convocatoria';
$string['period'] = 'Período';
$string['totalvacancies'] = 'Total de Vacantes';
$string['totalconvocatorias'] = 'Total de Convocatorias';
$string['browseconvocatorias'] = 'Explorar Convocatorias';
$string['viewconvocatorias'] = 'Ver convocatorias';
$string['activeconvocatorias_alert'] = 'Hay {$a} convocatorias activas disponibles';
$string['novacancies'] = 'No hay vacantes disponibles';
$string['daysleft'] = '{$a} días restantes';

// Vacantes extemporáneas eliminadas en Fase 10 - fechas ahora a nivel de convocatoria.
$string['convocatoriadates'] = 'Fechas de la convocatoria';
$string['usingconvocatoriadates'] = 'Usando fechas de la convocatoria';

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
$string['emailtemplates'] = 'Plantillas de correo';
$string['edittemplate'] = 'Editar plantilla';

// Reports.
$string['report:applications'] = 'Reporte de postulaciones';
$string['report:documents'] = 'Reporte de documentos';
$string['report:metrics'] = 'Métricas de tiempo';
$string['report:vacancies'] = 'Reporte de vacantes';
$string['report:audit'] = 'Auditoría';

// Dashboard.
$string['activevacancies'] = 'Vacantes activas';
$string['applicationstoday'] = 'Postulaciones hoy';
$string['totalapplicants'] = 'Postulantes totales';
$string['selectedthismonth'] = 'Seleccionados este mes';
$string['averagereviewtime'] = 'Tiempo promedio de revisión';
$string['alerts'] = 'Alertas';
$string['quickactions'] = 'Acciones rápidas';

// Configuration.
$string['generalsettings'] = 'Configuración general';
$string['enableselfregistration'] = 'Habilitar auto-registro del plugin';
$string['enableselfregistration_desc'] = 'Permitir a los usuarios registrarse a través del Job Board incluso cuando el auto-registro global de Moodle está deshabilitado. Los usuarios se registrarán usando confirmación por correo electrónico.';
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
$string['confirmstatuschange'] = '¿Confirma el cambio de estado?';
$string['confirmpublish'] = '¿Está seguro que desea publicar esta vacante?';
$string['confirmunpublish'] = '¿Está seguro que desea despublicar esta vacante? Volverá al estado de borrador.';
$string['confirmclose'] = '¿Está seguro que desea cerrar esta vacante? No se aceptarán más postulaciones.';
$string['confirmreopen'] = '¿Está seguro que desea reabrir esta vacante?';

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
$string['event:vacancyclosed'] = 'Vacante cerrada';
$string['event:vacancyreopened'] = 'Vacante reabierta';
$string['event:applicationcreated'] = 'Postulación creada';
$string['event:applicationupdated'] = 'Postulación actualizada';
$string['event:documentuploaded'] = 'Documento cargado';
$string['event:documentvalidated'] = 'Documento validado';
$string['event:statuschanged'] = 'Estado cambiado';

// Task names.
$string['task:cleanupdata'] = 'Limpieza de datos antiguos';
$string['task:updatemetrics'] = 'Actualizar métricas del dashboard';

// Privacy.
$string['privacy:metadata:application'] = 'Información sobre las postulaciones del usuario';
$string['privacy:metadata:document'] = 'Documentos subidos por el usuario';
$string['privacy:metadata:audit'] = 'Registro de acciones del usuario';

// Miscellaneous.
$string['noresults'] = 'No se encontraron resultados';
$string['processing'] = 'Procesando...';
$string['allcompanies'] = 'Todas las empresas';
$string['alldepartments'] = 'Todos los departamentos';
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
$string['digitalsignature_help'] = 'Escriba su nombre legal completo tal como aparece en sus documentos de identidad. Esto sirve como su firma digital y confirma legalmente que: toda la información proporcionada es veraz, autoriza la verificación de sus documentos, acepta los términos y condiciones del proceso de selección. El uso de un nombre falso puede resultar en descalificación.';
$string['signaturetoooshort'] = 'La firma debe tener al menos 5 caracteres';
$string['documentshelp'] = 'Suba los documentos requeridos en formato PDF, JPG o PNG. El tamaño máximo por archivo es de 10MB.';

// Help strings para carga de documentos.
$string['documenttype_help'] = 'Seleccione el tipo de documento que está cargando. Cada vacante requiere documentos específicos. Los tipos comunes incluyen: títulos y diplomas académicos, certificaciones profesionales, documentos de identidad, cartas de referencia. Asegúrese de seleccionar el tipo correcto para evitar demoras en el proceso de revisión.';
$string['documentfile_help'] = 'Cargue su documento en formato PDF. Requisitos: tamaño máximo 10MB, formatos soportados solo PDF, el documento debe ser legible y estar completo. Para documentos de varias páginas, combine todas las páginas en un solo PDF. Los documentos escaneados deben tener una resolución mínima de 150 DPI para legibilidad.';
$string['documentissuedate_help'] = 'Ingrese la fecha en que se expidió o certificó este documento. Para títulos académicos, use la fecha de grado. Para certificaciones, use la fecha de certificación. Algunos documentos tienen períodos de validez y pueden ser rechazados si están vencidos.';

// Help strings para revisión de documentos.
$string['validationstatus_help'] = 'Estado actual de la validación del documento: Pendiente (aún no revisado), Aprobado (el documento cumple todos los requisitos), Rechazado (el documento tiene problemas - ver razón de rechazo), Requiere Aclaración (se necesita información adicional del postulante).';
$string['rejectionreason_help'] = 'Si rechaza el documento, seleccione la razón de la lista o proporcione una explicación personalizada. Razones comunes incluyen: el documento es ilegible, el documento está incompleto, el documento está vencido, se cargó un tipo de documento incorrecto, el nombre no coincide con la postulación. El postulante será notificado y podrá cargar una versión corregida.';
$string['reviewcomments_help'] = 'Agregue cualquier comentario interno sobre esta revisión de documento. Estos comentarios son visibles solo para revisores y administradores, no para el postulante. Use este campo para anotar preocupaciones, verificaciones realizadas o recomendaciones.';
$string['documentrequired'] = 'El documento "{$a}" es obligatorio';
$string['documentissuedate'] = 'Fecha de expedición';
$string['documentexpired'] = 'El documento ha expirado (máximo {$a})';
$string['additionalinfo'] = 'Información Adicional';
$string['declaration'] = 'Declaración';
$string['declarationtext'] = 'Declaro bajo la gravedad del juramento que la información proporcionada es veraz y los documentos anexos son auténticos. Entiendo que cualquier falsedad puede resultar en la exclusión del proceso y acciones legales.';
$string['declarationaccept'] = 'Acepto la declaración anterior';
$string['declarationrequired'] = 'Debe aceptar la declaración';
$string['exemptionnotice'] = 'Aviso de Excepción';
$string['exemptionapplied'] = 'Excepción ISER Aplicada';
$string['exemptionreduceddocs'] = 'Sus requisitos de documentación han sido reducidos debido a su historial laboral en la institución.';
$string['documentref'] = 'Referencia del documento';
$string['exemptiontype_historico_iser'] = 'Personal Histórico ISER';
$string['exemptiontype_documentos_recientes'] = 'Documentos Recientes Aprobados';
$string['exemptiontype_traslado_interno'] = 'Traslado Interno';
$string['exemptiontype_recontratacion'] = 'Recontratación';
$string['vacancynotopen'] = 'La vacante no está abierta para postulaciones';
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
$string['pendingvalidation'] = 'Pendiente de Validación';
$string['documenttypes'] = 'Tipos de Documento';

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

// Categorías de documentos.
$string['category'] = 'Categoría';
$string['doccategory_identification'] = 'Documentos de Identificación';
$string['doccategory_academic'] = 'Documentos Académicos';
$string['doccategory_employment'] = 'Documentos Laborales';
$string['doccategory_financial'] = 'Documentos Financieros';
$string['doccategory_health'] = 'Salud y Seguridad Social';
$string['doccategory_legal'] = 'Antecedentes Legales';
$string['doccategory_other'] = 'Otros Documentos';

// Mensajes condicionales de documentos.
$string['conditions'] = 'Condiciones';
$string['doc_condition_men_only'] = 'Requerido solo para hombres';
$string['doc_condition_women_only'] = 'Requerido solo para mujeres';
$string['doc_condition_profession_exempt'] = 'No requerido para: {$a}';
$string['doc_condition_iser_exempt'] = 'No requerido para empleados previos del ISER';

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
$string['availablereviewers'] = 'Revisores Disponibles';
$string['unassignedapplications'] = 'Postulaciones Sin Asignar';
$string['totalassigned'] = 'Total Asignadas';
$string['avgworkload'] = 'Carga Promedio';
$string['pendingassignment'] = 'pendiente de asignación';
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
$string['selectnone'] = 'Deseleccionar todo';
$string['approveselected'] = 'Aprobar seleccionados';
$string['rejectselected'] = 'Rechazar seleccionados';
$string['documentssummary'] = 'Resumen de documentos';
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
$string['exportpdf'] = 'Exportar PDF';
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
$string['noexpiry'] = 'Sin vencimiento';
$string['validityperiod'] = 'Período de validez';
$string['totalexemptions'] = 'Total de Excepciones';
$string['activeexemptions'] = 'Excepciones Activas';
$string['expiredexemptions'] = 'Excepciones Vencidas';
$string['revokedexemptions'] = 'Excepciones Revocadas';
$string['exemptionlist'] = 'Lista de Excepciones';
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

// Estadísticas de tokens.
$string['totaltokens'] = 'Total de Tokens';
$string['activetokens'] = 'Tokens Activos';
$string['revokedtokens'] = 'Tokens Revocados';
$string['usedtoday'] = 'Usados Hoy';
$string['tokenslist'] = 'Lista de Tokens API';

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

$string['privacy:metadata:interviewer'] = 'Información sobre usuarios asignados como miembros del panel de entrevistas';
$string['privacy:metadata:interviewer:userid'] = 'El ID del usuario asignado como entrevistador';
$string['privacy:metadata:interviewer:interviewid'] = 'La entrevista a la que está asignado';
$string['privacy:metadata:interviewer:timecreated'] = 'Cuándo se realizó la asignación';

$string['privacy:metadata:committeemember'] = 'Información sobre usuarios asignados a comités de selección';
$string['privacy:metadata:committeemember:userid'] = 'El ID del usuario asignado al comité';
$string['privacy:metadata:committeemember:committeeid'] = 'El comité al que está asignado';
$string['privacy:metadata:committeemember:role'] = 'El rol del usuario en el comité';
$string['privacy:metadata:committeemember:addedby'] = 'El usuario que realizó la asignación';
$string['privacy:metadata:committeemember:timecreated'] = 'Cuándo se realizó la asignación';

$string['privacy:metadata:evaluation'] = 'Puntuaciones y votos de evaluación enviados por miembros del comité';
$string['privacy:metadata:evaluation:userid'] = 'El ID del usuario que envió la evaluación';
$string['privacy:metadata:evaluation:applicationid'] = 'La postulación siendo evaluada';
$string['privacy:metadata:evaluation:score'] = 'La puntuación numérica otorgada';
$string['privacy:metadata:evaluation:vote'] = 'La decisión del voto (aprobar/rechazar)';
$string['privacy:metadata:evaluation:comments'] = 'Comentarios proporcionados con la evaluación';
$string['privacy:metadata:evaluation:timecreated'] = 'Cuándo se envió la evaluación';

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

// Strings multi-tenant IOMAD.
$string['iomadsettings'] = 'Empresa y Departamento';
$string['iomad_department'] = 'Departamento IOMAD';
$string['iomad_department_help'] = 'Seleccione el departamento dentro de la empresa para esta vacante. Los departamentos se gestionan en IOMAD.';

// Página pública.
$string['publicvacancies'] = 'Oportunidades Laborales';
$string['publicpagetitle'] = 'Oportunidades Laborales';
$string['publicpagetitle_default'] = 'Oportunidades Laborales';
$string['vacanciesfound'] = '{$a} vacantes encontradas';
$string['novacanciesfound'] = 'No se encontraron vacantes que coincidan con sus criterios.';
$string['searchplaceholder'] = 'Buscar por título, código o descripción...';
$string['viewdetails'] = 'Ver Detalles';
$string['loginandapply'] = 'Iniciar sesión para postularse';
$string['opensnewwindow'] = '(abre en nueva ventana)';
$string['closesin'] = 'Cierra en {$a} días';
$string['closeson'] = 'Cierra el';
$string['wanttoapply'] = '¿Desea postularse?';
$string['createaccounttoapply'] = 'Cree una cuenta o inicie sesión para postularse a las vacantes.';
$string['loginprompt_public'] = 'Inicie sesión o cree una cuenta para postularse a las vacantes.';
$string['loginrequired_apply'] = 'Debe iniciar sesión para postularse a esta vacante.';
$string['backtovacancies'] = 'Volver a Vacantes';
$string['requireddocuments'] = 'Documentos Requeridos';
$string['importantdates'] = 'Fechas Importantes';
$string['sharethisvacancy'] = 'Compartir esta Vacante';
$string['copylink'] = 'Copiar enlace';
$string['applynow'] = 'Postularse Ahora';
$string['alreadyapplied'] = 'Ya se ha postulado a esta vacante.';
$string['applicationstatus'] = 'Estado de la postulación';
$string['noapplypermission'] = 'No tiene permiso para postularse a vacantes.';
$string['youhaveapplied'] = 'Ya te has postulado';
$string['viewmyapplication'] = 'Ver mi postulación';
$string['viewallapplications'] = 'Ver todas mis postulaciones';
$string['applyforposition'] = 'Postularme a esta vacante';
$string['applynow_desc'] = '¿Listo para postularte? Envía tu postulación ahora y da el siguiente paso en tu carrera.';
$string['noapplycapability'] = 'No tienes permiso para postularte a vacantes.';
$string['quicklinks'] = 'Enlaces rápidos';
$string['othervacancies'] = 'Otras vacantes de esta convocatoria';
$string['allconvocatorias'] = 'Todas las convocatorias';
$string['share'] = 'Compartir';
$string['searchvacancies'] = 'Buscar vacantes...';
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
$string['applicationlimits_perconvocatoria_desc'] = 'Los límites de postulación (permitir múltiples, máximo por usuario) ahora se configuran por convocatoria. Edite cada convocatoria para establecer estas restricciones.';
$string['allowmultipleapplications'] = 'Permitir múltiples postulaciones';
$string['allowmultipleapplications_desc'] = 'Permitir a los usuarios postularse a múltiples vacantes simultáneamente.';
$string['maxactiveapplications'] = 'Máximo de postulaciones activas';
$string['maxactiveapplications_desc'] = 'Número máximo de postulaciones activas por usuario (0 = ilimitado). Solo aplica cuando se permiten múltiples postulaciones.';

// Errores de límite de postulación.
$string['error:multipleapplicationsnotallowed'] = 'Solo puede tener una postulación activa a la vez. Por favor retire su postulación actual antes de postularse a una nueva vacante.';
$string['error:applicationlimitreached'] = 'Ha alcanzado el número máximo de postulaciones activas ({$a}). Por favor espere a que se procesen sus postulaciones actuales o retire una antes de postularse a una nueva vacante.';
$string['error:publicpagedisabled'] = 'La página de vacantes públicas está deshabilitada.';
$string['error:loginrequiredforinternal'] = 'Debe iniciar sesión para ver vacantes internas.';

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
$string['previewdocument'] = 'Vista previa';
$string['togglepreview'] = 'Mostrar/Ocultar';
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

// =============================================================================
// USER TOURS - Tours Guiados Completos para todas las vistas del plugin
// =============================================================================

// Cadenas comunes de tours.
$string['tour_endlabel'] = 'Entendido!';

// Tour: Panel Principal (Dashboard).
$string['tour_dashboard_name'] = 'Tour del Panel Principal - Guia Completa';
$string['tour_dashboard_description'] = 'Aprenda a navegar por el panel principal de la Bolsa de Empleo. Este tour le mostrara todas las funciones disponibles segun su rol.';
$string['tour_dashboard_step1_title'] = 'Bienvenido a la Bolsa de Empleo';
$string['tour_dashboard_step1_content'] = 'Bienvenido al sistema de Bolsa de Empleo del ISER!

**Que es la Bolsa de Empleo?**
Es un sistema completo para gestionar procesos de seleccion de personal, incluyendo:

- Publicacion y gestion de vacantes
- Recepcion y seguimiento de postulaciones
- Validacion de documentos de los candidatos
- Evaluacion y seleccion de personal
- Generacion de reportes estadisticos

Este tour le guiara paso a paso por las funciones principales.';
$string['tour_dashboard_step2_title'] = 'Vista General del Panel';
$string['tour_dashboard_step2_content'] = 'Este es su panel principal de navegacion.

**Que encontrara aqui:**
- Accesos directos a todas las funciones del sistema
- Resumen de estadisticas relevantes
- Alertas y notificaciones pendientes
- Acciones rapidas segun su rol

**Su rol determina que opciones ve:**
- Postulantes: Vacantes disponibles y sus postulaciones
- Revisores: Documentos pendientes de validacion
- Administradores: Todas las funciones de gestion

Explore cada seccion para familiarizarse con el sistema.';
$string['tour_dashboard_step3_title'] = 'Tarjetas de Acciones Rapidas';
$string['tour_dashboard_step3_content'] = 'Las tarjetas proporcionan acceso directo a las funciones principales.

**Como usar las tarjetas:**
1. Cada tarjeta tiene un icono descriptivo y un titulo
2. Haga clic en la tarjeta para ir a esa seccion
3. Algunas tarjetas muestran contadores (ej: "5 pendientes")

**Tarjetas comunes:**
- **Vacantes**: Ver oportunidades laborales disponibles
- **Mis Postulaciones**: Seguimiento de sus aplicaciones
- **Gestionar Vacantes**: Para administradores
- **Revisar Documentos**: Para revisores asignados
- **Reportes**: Estadisticas y analisis

Las tarjetas disponibles dependen de sus permisos en el sistema.';
$string['tour_dashboard_step4_title'] = 'Funciones segun su Rol';
$string['tour_dashboard_step4_content'] = '**Si es POSTULANTE:**
- Explore las vacantes publicadas
- Envie postulaciones con sus documentos
- De seguimiento al estado de sus aplicaciones
- Reciba notificaciones de avances

**Si es REVISOR:**
- Vea las postulaciones asignadas
- Valide los documentos de los candidatos
- Marque documentos como aprobados o rechazados
- Agregue notas y comentarios

**Si es ADMINISTRADOR:**
- Cree y gestione convocatorias
- Publique y cierre vacantes
- Asigne revisores a postulaciones
- Genere reportes de seguimiento
- Configure el sistema

Cada rol tiene permisos especificos para mantener la seguridad del proceso.';
$string['tour_dashboard_step5_title'] = 'Listo para Comenzar!';
$string['tour_dashboard_step5_content'] = 'Ya conoce el panel principal!

**Proximos pasos recomendados:**

1. **Si busca empleo**: Vaya a "Vacantes" para ver las oportunidades disponibles

2. **Si es revisor**: Consulte "Mis Revisiones" para ver sus asignaciones pendientes

3. **Si es administrador**: Explore "Gestionar Vacantes" y "Convocatorias"

**Recursos de ayuda:**
- Cada pagina tiene su propio tour guiado
- Los campos con icono (?) tienen tooltips explicativos
- Puede reiniciar este tour desde el menu de ayuda

Mucho exito usando la Bolsa de Empleo!';

// Tour: Vacantes Publicas.
$string['tour_public_name'] = 'Tour de Vacantes Publicas';
$string['tour_public_description'] = 'Aprenda a buscar y filtrar vacantes disponibles';
$string['tour_public_step1_title'] = 'Vacantes Publicas';
$string['tour_public_step1_content'] = 'Bienvenido a la pagina de vacantes publicas! Aqui puede encontrar todas las oportunidades laborales disponibles. Este tour le mostrara como buscar y filtrar vacantes efectivamente.';
$string['tour_public_step2_title'] = 'Encabezado de Pagina';
$string['tour_public_step2_content'] = 'Esta seccion muestra el titulo de la pagina y cualquier mensaje de bienvenida configurado por el administrador. Lealo para conocer mas sobre el proceso de seleccion de la organizacion.';
$string['tour_public_step3_title'] = 'Busqueda y Filtros';
$string['tour_public_step3_content'] = 'Use estos controles para filtrar vacantes. Puede buscar por palabra clave, filtrar por tipo de contrato, ubicacion y mas para encontrar oportunidades que coincidan con su perfil.';
$string['tour_public_step4_title'] = 'Caja de Busqueda';
$string['tour_public_step4_content'] = 'Escriba palabras clave aqui para buscar en titulos, codigos y descripciones de vacantes. La busqueda no distingue mayusculas y encuentra coincidencias parciales.';
$string['tour_public_step5_title'] = 'Menus Desplegables';
$string['tour_public_step5_content'] = 'Use estos menus desplegables para filtrar por tipo de contrato (tiempo completo, medio tiempo, etc.) y ubicacion. Combine multiples filtros para afinar su busqueda.';
$string['tour_public_step6_title'] = 'Tarjetas de Vacantes';
$string['tour_public_step6_content'] = 'Cada vacante se muestra como una tarjeta con informacion clave: titulo, ubicacion, tipo de contrato y fecha de cierre. La tarjeta tambien muestra cuantos dias quedan hasta la fecha limite.';
$string['tour_public_step7_title'] = 'Insignias de Tipo';
$string['tour_public_step7_content'] = 'Las vacantes se marcan como "Publica" (abierta a todos) o "Interna" (solo para usuarios autenticados de la organizacion). Asegurese de cumplir con los criterios de elegibilidad antes de postularse.';
$string['tour_public_step8_title'] = 'Ver Detalles y Postularse';
$string['tour_public_step8_content'] = 'Haga clic en "Ver Detalles" para ver la informacion completa de la vacante, o haga clic en "Postularse" para iniciar su postulacion. Puede que necesite iniciar sesion primero.';

// Tour: Formulario de Postulacion.
$string['tour_apply_name'] = 'Tour del Formulario de Postulacion - Guia Paso a Paso';
$string['tour_apply_description'] = 'Aprenda a completar correctamente su postulacion y cargar todos los documentos requeridos. Este tour le guiara por cada seccion del formulario.';
$string['tour_apply_step1_title'] = 'Postularse a una Vacante';
$string['tour_apply_step1_content'] = 'Bienvenido al formulario de postulacion!

**Antes de comenzar, asegurese de tener:**
- Todos sus documentos digitalizados (PDF preferiblemente)
- Documentos legibles y completos
- Archivos de maximo 10MB cada uno
- Formato SIGEP si es requerido

**Tiempo estimado:** 15-20 minutos si tiene todos los documentos listos.

**IMPORTANTE:** La postulacion NO se guarda automaticamente. Complete todo en una sola sesion.

Este tour le explicara cada seccion del formulario.';
$string['tour_apply_step2_title'] = 'Instrucciones de la Vacante';
$string['tour_apply_step2_content'] = 'LEA ESTA SECCION CUIDADOSAMENTE antes de continuar.

**Aqui encontrara:**
- Documentos especificos requeridos para esta vacante
- Formatos aceptados para cada documento
- Condiciones especiales (si aplican)
- Fechas limites importantes

**Tipos de documentos comunes:**
- Formato SIGEP II actualizado
- Cedula de ciudadania (ambas caras)
- Titulos academicos
- Tarjeta profesional (si aplica)
- Certificados de antecedentes (vigentes)
- Certificaciones laborales

Revise los requisitos especificos de ESTA vacante.';
$string['tour_apply_step3_title'] = 'Consentimiento para Tratamiento de Datos';
$string['tour_apply_step3_content'] = 'Es OBLIGATORIO aceptar el consentimiento informado.

**Que esta aceptando:**
- El tratamiento de sus datos personales segun Ley 1581 de 2012
- El uso de su informacion exclusivamente para el proceso de seleccion
- El almacenamiento seguro de sus documentos
- La comunicacion sobre el estado de su postulacion

**Sus derechos:**
- Conocer, actualizar y rectificar sus datos
- Solicitar prueba del consentimiento
- Revocar el consentimiento en cualquier momento
- Presentar quejas ante la SIC

Marque la casilla solo si esta de acuerdo con estos terminos.';
$string['tour_apply_step4_title'] = 'Firma Digital';
$string['tour_apply_step4_content'] = 'Su firma digital valida legalmente la postulacion.

**Como firmar:**
1. Escriba su NOMBRE COMPLETO
2. Exactamente como aparece en su cedula
3. Incluya todos sus nombres y apellidos
4. Use mayusculas y minusculas correctamente

**Ejemplo:** Juan Carlos Perez Rodriguez

**Esta firma certifica que:**
- La informacion proporcionada es veridica
- Los documentos son autenticos
- Acepta los terminos del proceso

IMPORTANTE: Una firma incompleta o incorrecta puede retrasar su postulacion.';
$string['tour_apply_step5_title'] = 'Carga de Documentos - Seccion Critica';
$string['tour_apply_step5_content'] = 'Esta es la seccion mas importante del formulario.

**Para cada documento:**
1. Haga clic en el boton de carga
2. Seleccione el archivo desde su computador
3. Espere a que se cargue completamente
4. Verifique que el archivo correcto aparezca

**Requisitos tecnicos:**
- Formatos: PDF (preferido), JPG, PNG
- Tamano maximo: 10MB por archivo
- Resolucion minima: 150 DPI
- Documentos de multiples paginas: use PDF

**Consejos:**
- Escanee documentos, no tome fotos borrosas
- Verifique que el texto sea legible
- Incluya TODAS las paginas requeridas
- Los documentos con fecha deben estar vigentes

Un documento rechazado retrasa todo el proceso.';
$string['tour_apply_step6_title'] = 'Carta de Presentacion (Opcional pero Recomendada)';
$string['tour_apply_step6_content'] = 'La carta de presentacion puede diferenciarle de otros candidatos.

**Que incluir:**
- Por que le interesa esta vacante especificamente
- Como su experiencia se alinea con los requisitos
- Que puede aportar a la institucion
- Su disponibilidad horaria

**Estructura sugerida:**
1. Saludo formal
2. Mencion de la vacante a la que aplica
3. Resumen de su perfil relevante
4. Motivacion e interes
5. Despedida cordial

**Longitud recomendada:** 150-300 palabras

Aunque es opcional, una buena carta demuestra interes y profesionalismo.';
$string['tour_apply_step7_title'] = 'Enviar Postulacion';
$string['tour_apply_step7_content'] = 'Ultimo paso! Antes de enviar:

**Lista de verificacion final:**
✓ Lei y acepte el consentimiento de datos
✓ Firme digitalmente con mi nombre completo
✓ Cargue TODOS los documentos requeridos
✓ Verifique que los archivos se cargaron correctamente
✓ Revise que los documentos sean legibles
✓ Complete la carta de presentacion (si desea)

**Al hacer clic en Enviar:**
- Su postulacion se registra en el sistema
- Recibira un correo de confirmacion
- El proceso de revision comienza

**ADVERTENCIA:** Despues de enviar NO podra:
- Agregar mas documentos (salvo rechazo)
- Modificar la informacion
- Cambiar de vacante

Asegurese de que TODO este correcto antes de enviar.';
$string['tour_apply_step8_title'] = 'Postulacion Enviada - Que Sigue?';
$string['tour_apply_step8_content'] = 'Felicitaciones por completar su postulacion!

**Pasos siguientes del proceso:**

1. **Confirmacion** (inmediato)
   - Recibira un correo de confirmacion
   - Guarde este correo como comprobante

2. **Revision de documentos** (3-10 dias habiles)
   - Un revisor verificara sus documentos
   - Le notificaran si hay observaciones

3. **Evaluacion** (segun convocatoria)
   - Si sus documentos son aprobados
   - Pasara a la siguiente fase

**Como dar seguimiento:**
- Entre a "Mis Postulaciones" regularmente
- Configure notificaciones por correo
- Responda rapidamente si hay observaciones

**Si un documento es rechazado:**
- Recibira notificacion con el motivo
- Podra cargar una version corregida
- Haga la correccion lo antes posible

Le deseamos mucho exito en su proceso!';

// Tour: Mis Postulaciones.
$string['tour_myapplications_name'] = 'Tour de Mis Postulaciones';
$string['tour_myapplications_description'] = 'Aprenda a dar seguimiento y gestionar sus postulaciones';
$string['tour_myapplications_step1_title'] = 'Mis Postulaciones';
$string['tour_myapplications_step1_content'] = 'Bienvenido a su panel de postulaciones! Aqui puede dar seguimiento al estado de todas sus postulaciones y ver actualizaciones o acciones requeridas.';
$string['tour_myapplications_step2_title'] = 'Estado de Exencion';
$string['tour_myapplications_step2_content'] = 'Si tiene una exencion activa (por ejemplo, como empleado actual o anterior), se mostrara aqui. Esto afecta que documentos necesita presentar con sus postulaciones.';
$string['tour_myapplications_step3_title'] = 'Filtro de Estado';
$string['tour_myapplications_step3_content'] = 'Use este filtro para mostrar solo postulaciones con un estado especifico. Esto le ayuda a enfocarse en postulaciones que necesitan atencion o revision.';
$string['tour_myapplications_step4_title'] = 'Tabla de Postulaciones';
$string['tour_myapplications_step4_content'] = 'Esta tabla muestra todas sus postulaciones con informacion clave: nombre de vacante, fecha de postulacion, estado actual y cantidad de documentos. Haga clic en cualquier fila para mas detalles.';
$string['tour_myapplications_step5_title'] = 'Estado de Postulacion';
$string['tour_myapplications_step5_content'] = 'La insignia de estado muestra donde esta su postulacion en el proceso: Enviada, En Revision, Documentos Validados, Entrevista, Seleccionado o Rechazado. Este atento a los cambios!';
$string['tour_myapplications_step6_title'] = 'Acciones Disponibles';
$string['tour_myapplications_step6_content'] = 'Haga clic en "Ver" para ver los detalles completos de su postulacion y documentos cargados. Si su postulacion aun esta en revision, tambien puede tener la opcion de retirarla.';

// Tour: Revision de Documentos.
$string['tour_review_name'] = 'Tour de Revision de Documentos';
$string['tour_review_description'] = 'Aprenda a revisar y validar documentos de postulantes';
$string['tour_review_step1_title'] = 'Centro de Revision de Documentos';
$string['tour_review_step1_content'] = 'Bienvenido al centro de revision de documentos! Como revisor, es responsable de validar los documentos de los postulantes para asegurar que cumplan con los requisitos.';
$string['tour_review_step2_title'] = 'Progreso de Revision';
$string['tour_review_step2_content'] = 'Estos pasos muestran su progreso: Examinar (descargar y verificar documentos), Validar (aprobar o rechazar cada uno), y Completar (finalizar la revision). Los pasos completados se vuelven verdes.';
$string['tour_review_step3_title'] = 'Resumen de Estadisticas';
$string['tour_review_step3_content'] = 'Las estadisticas rapidas muestran el total de documentos, cuantos estan aprobados, rechazados y pendientes. Uselas para monitorear su progreso de un vistazo.';
$string['tour_review_step4_title'] = 'Guia de Revision';
$string['tour_review_step4_content'] = 'Lea estas pautas antes de revisar. Le recuerdan descargar documentos, verificar legibilidad, proporcionar razones claras de rechazo, y completar todas las revisiones antes de finalizar.';
$string['tour_review_step5_title'] = 'Lista de Documentos';
$string['tour_review_step5_content'] = 'Todos los documentos cargados se listan aqui. Los pendientes tienen borde amarillo, los aprobados verde, y los rechazados rojo. Cada uno muestra tipo, nombre y estado actual.';
$string['tour_review_step6_title'] = 'Estado del Documento';
$string['tour_review_step6_content'] = 'El indicador de color muestra el estado de cada documento. Los documentos pendientes requieren su atencion. Despues de validar, vera quien lo reviso y cuando.';
$string['tour_review_step7_title'] = 'Acciones de Validacion';
$string['tour_review_step7_content'] = 'Para cada documento: Descargue para examinar, haga clic en el check verde para Aprobar, o la X roja para Rechazar. Al rechazar, proporcione una razon clara para que el postulante pueda volver a enviar.';
$string['tour_review_step8_title'] = 'Consejos de Revision';
$string['tour_review_step8_content'] = 'Esta barra lateral proporciona recordatorios utiles: descargar documentos, verificar legibilidad, comprobar completitud y autenticar cuando sea posible. Siga estos consejos para revisiones exhaustivas.';
$string['tour_review_step9_title'] = 'Feliz Revision!';
$string['tour_review_step9_content'] = 'Cuando todos los documentos esten revisados, aparecera un boton de completar. Recuerde: la validacion cuidadosa es crucial para la integridad del proceso de seleccion. Tomese su tiempo!';

// Tour: Gestion de Vacantes.
$string['tour_manage_name'] = 'Tour de Gestion de Vacantes';
$string['tour_manage_description'] = 'Aprenda a crear y gestionar vacantes laborales';
$string['tour_manage_step1_title'] = 'Gestion de Vacantes';
$string['tour_manage_step1_content'] = 'Bienvenido al centro de gestion de vacantes! Aqui puede crear, editar, publicar y gestionar todas las vacantes. Las acciones masivas y los controles de paginacion facilitan la gestion de grandes cantidades de vacantes.';
$string['tour_manage_step2_title'] = 'Panel de Gestion';
$string['tour_manage_step2_content'] = 'Este es su panel de gestion de vacantes. Desde aqui puede ver estadisticas, filtrar vacantes, realizar acciones masivas y navegar eficientemente por sus vacantes.';
$string['tour_manage_step3_title'] = 'Tarjetas de Estadisticas';
$string['tour_manage_step3_content'] = 'Estas tarjetas muestran estadisticas rapidas: total de vacantes, vacantes publicadas, postulaciones recibidas y posiciones disponibles. Uselas para monitorear su actividad de reclutamiento.';
$string['tour_manage_step4_title'] = 'Opciones de Filtro';
$string['tour_manage_step4_content'] = 'Filtre vacantes por estado (Borrador, Publicada, Cerrada) y empresa (en configuraciones multi-tenant). Use la caja de busqueda para encontrar vacantes especificas por codigo o titulo.';
$string['tour_manage_step5_title'] = 'Seleccion Masiva';
$string['tour_manage_step5_content'] = 'Use estas casillas para seleccionar multiples vacantes a la vez. Cuando hay elementos seleccionados, aparece una barra de acciones masivas que le permite publicar, despublicar, cerrar o eliminar varias vacantes simultaneamente.';
$string['tour_manage_step6_title'] = 'Tabla de Vacantes';
$string['tour_manage_step6_content'] = 'Esta tabla muestra todas las vacantes con su codigo, titulo, estado, fechas y conteo de postulaciones. Haga clic en el titulo de una vacante para ver sus detalles completos.';
$string['tour_manage_step7_title'] = 'Insignias de Estado';
$string['tour_manage_step7_content'] = 'La insignia de estado muestra el estado actual: Borrador (no visible), Publicada (aceptando postulaciones), Cerrada (ya no acepta) o Asignada (posiciones cubiertas).';
$string['tour_manage_step8_title'] = 'Botones de Accion';
$string['tour_manage_step8_content'] = 'Use estos botones para acciones individuales: Editar la vacante, Publicar un borrador, Cerrar una vacante activa, Ver postulaciones, o Eliminar (solo si no hay postulaciones).';
$string['tour_manage_step9_title'] = 'Controles de Paginacion';
$string['tour_manage_step9_content'] = 'Use la barra de paginacion para navegar entre paginas de vacantes. Tambien puede seleccionar cuantos registros mostrar por pagina (10, 25, 50 o 100).';
$string['tour_manage_step10_title'] = 'Todo Listo!';
$string['tour_manage_step10_content'] = 'Ya sabe como gestionar vacantes! Use las acciones masivas para gestionar eficientemente multiples vacantes, y recuerde publicar las vacantes para hacerlas visibles a los postulantes.';

// Tour: Reportes.
$string['tour_reports_name'] = 'Tour de Reportes y Estadisticas';
$string['tour_reports_description'] = 'Aprenda a generar reportes y analizar datos de reclutamiento';
$string['tour_reports_step1_title'] = 'Panel de Reportes';
$string['tour_reports_step1_content'] = 'Bienvenido a la seccion de reportes! Aqui puede analizar datos de reclutamiento, dar seguimiento a metricas de rendimiento y exportar reportes para analisis adicional.';
$string['tour_reports_step2_title'] = 'Tipos de Reportes';
$string['tour_reports_step2_content'] = 'Use estas pestanas para cambiar entre diferentes tipos de reportes: Resumen (estadisticas generales), Postulaciones, Documentos, Revisores (rendimiento) y Linea de Tiempo (tendencias).';
$string['tour_reports_step3_title'] = 'Filtro de Vacante';
$string['tour_reports_step3_content'] = 'Filtre reportes por una vacante especifica o vea datos de todas las vacantes combinadas. Esto le ayuda a analizar el rendimiento de posiciones individuales.';
$string['tour_reports_step4_title'] = 'Rango de Fechas';
$string['tour_reports_step4_content'] = 'Establezca el rango de fechas para su reporte. Por defecto, los reportes muestran los ultimos 30 dias, pero puede ajustar esto para analizar cualquier periodo.';
$string['tour_reports_step5_title'] = 'Opciones de Exportacion';
$string['tour_reports_step5_content'] = 'Exporte los datos de su reporte en formato CSV o Excel para analisis adicional, compartir con interesados o archivar. La exportacion a PDF tambien esta disponible.';
$string['tour_reports_step6_title'] = 'Contenido del Reporte';
$string['tour_reports_step6_content'] = 'El area principal del reporte muestra estadisticas, tablas y graficos segun el tipo de reporte seleccionado. Los datos se actualizan automaticamente al cambiar filtros.';
$string['tour_reports_step7_title'] = 'Indicadores Visuales';
$string['tour_reports_step7_content'] = 'Las barras de progreso y graficos le ayudan a entender rapidamente los datos. El verde indica metricas positivas, el rojo indica areas que pueden necesitar atencion.';
$string['tour_reports_step8_title'] = 'Tome Decisiones Basadas en Datos!';
$string['tour_reports_step8_content'] = 'Use estos reportes para identificar tendencias, optimizar su proceso de reclutamiento y tomar decisiones informadas. El analisis regular lleva a la mejora continua!';

// Tour: Pagina de Validacion de Documentos.
$string['tour_validate_name'] = 'Tour de Validacion de Documentos';
$string['tour_validate_description'] = 'Aprenda a validar correctamente los documentos de postulantes';
$string['tour_validate_step1_title'] = 'Validacion de Documentos';
$string['tour_validate_step1_content'] = 'Bienvenido a la pagina de validacion de documentos! Aqui revisara un documento especifico y decidira si aprobarlo o rechazarlo.';
$string['tour_validate_step2_title'] = 'Informacion del Documento';
$string['tour_validate_step2_content'] = 'Esta seccion muestra detalles sobre el documento: tipo, nombre de archivo, fecha de carga y fecha de expedicion (si aplica). Revise esta informacion cuidadosamente.';
$string['tour_validate_step3_title'] = 'Ver Documento';
$string['tour_validate_step3_content'] = 'Haga clic en este boton para abrir el documento en una nueva pestana o use la vista previa integrada. Examine el documento cuidadosamente antes de tomar una decision.';
$string['tour_validate_step4_title'] = 'Lista de Verificacion';
$string['tour_validate_step4_content'] = 'Use esta lista de verificacion para comprobar que el documento cumple todos los requisitos. Cada tipo de documento tiene criterios especificos que debe verificar.';
$string['tour_validate_step5_title'] = 'Verifique Cada Punto';
$string['tour_validate_step5_content'] = 'Revise cada punto: Es legible el documento? Esta completo? El nombre coincide con el postulante? Las fechas estan vigentes (para documentos con vigencia)?';
$string['tour_validate_step6_title'] = 'Aprobar Documento';
$string['tour_validate_step6_content'] = 'Si el documento cumple todos los requisitos, haga clic en "Aprobar". Opcionalmente puede agregar notas para sus registros.';
$string['tour_validate_step7_title'] = 'Rechazar Documento';
$string['tour_validate_step7_content'] = 'Si el documento tiene problemas, seleccione una razon de rechazo del menu desplegable y haga clic en "Rechazar". El postulante sera notificado y podra cargar un documento corregido.';
$string['tour_validate_step8_title'] = 'Validacion Completada!';
$string['tour_validate_step8_content'] = 'Despues de tomar su decision, regresara a la vista de la postulacion. Continue revisando otros documentos hasta que todos esten validados.';

// Tour: Lista de Vacantes.
$string['tour_vacancies_name'] = 'Tour Lista de Vacantes';
$string['tour_vacancies_description'] = 'Aprenda a navegar y filtrar las vacantes disponibles';
$string['tour_vacancies_step1_title'] = 'Vista General de Vacantes';
$string['tour_vacancies_step1_content'] = 'Bienvenido a la lista de vacantes! Esta pagina muestra todas las vacantes a las que tiene acceso. Use los filtros y la busqueda para encontrar oportunidades especificas.';
$string['tour_vacancies_step2_title'] = 'Panel de Busqueda y Filtros';
$string['tour_vacancies_step2_content'] = 'Use este panel para buscar y filtrar vacantes. Puede combinar multiples filtros para reducir los resultados.';
$string['tour_vacancies_step3_title'] = 'Cuadro de Busqueda';
$string['tour_vacancies_step3_content'] = 'Escriba palabras clave para buscar en titulos, codigos y descripciones de vacantes. Presione Enter o haga clic en Buscar para aplicar.';
$string['tour_vacancies_step4_title'] = 'Filtro de Estado';
$string['tour_vacancies_step4_content'] = 'Filtre vacantes por su estado: Borrador (no publicada), Publicada (aceptando postulaciones), Cerrada (ya no acepta), o Asignada (posiciones cubiertas).';
$string['tour_vacancies_step5_title'] = 'Tabla de Vacantes';
$string['tour_vacancies_step5_content'] = 'La tabla muestra todas las vacantes que coinciden con informacion clave: codigo, titulo, estado, fechas y posiciones disponibles.';
$string['tour_vacancies_step6_title'] = 'Insignias de Estado';
$string['tour_vacancies_step6_content'] = 'Las insignias de estado ayudan a identificar rapidamente los estados: verde para publicada, amarillo para borrador, gris para cerrada.';
$string['tour_vacancies_step7_title'] = 'Postularse a Vacantes';
$string['tour_vacancies_step7_content'] = 'Haga clic en el boton Postularse para enviar su postulacion a cualquier vacante abierta que le interese. Buena suerte!';

// Tour: Detalle de Vacante Individual.
$string['tour_vacancy_name'] = 'Tour Detalle de Vacante';
$string['tour_vacancy_description'] = 'Conozca toda la informacion disponible en la pagina de detalle de vacante';
$string['tour_vacancy_step1_title'] = 'Detalles de la Vacante';
$string['tour_vacancy_step1_content'] = 'Esta pagina muestra informacion completa sobre una vacante especifica. Revise todos los detalles antes de postularse.';
$string['tour_vacancy_step2_title'] = 'Encabezado de Vacante';
$string['tour_vacancy_step2_content'] = 'El encabezado muestra el codigo de vacante y la insignia de tipo de publicacion. Las vacantes publicas estan abiertas a todos; las internas solo para miembros de la organizacion.';
$string['tour_vacancy_step3_title'] = 'Titulo de Vacante';
$string['tour_vacancy_step3_content'] = 'El titulo de la vacante y los detalles principales se muestran aqui, incluyendo nombre de empresa (si aplica), ubicacion y tipo de contrato.';
$string['tour_vacancy_step4_title'] = 'Alerta de Fecha de Cierre';
$string['tour_vacancy_step4_content'] = 'Preste atencion a la fecha de cierre! Si aparece como advertencia, la fecha limite se acerca. Asegurese de enviar su postulacion a tiempo.';
$string['tour_vacancy_step5_title'] = 'Boton Postularse';
$string['tour_vacancy_step5_content'] = 'Haga clic en este boton para iniciar su postulacion. Es posible que necesite iniciar sesion primero si aun no lo ha hecho.';
$string['tour_vacancy_step6_title'] = 'Detalles Adicionales';
$string['tour_vacancy_step6_content'] = 'Revise detalles adicionales como duración, tipo de contrato, departamento y fechas importantes antes de postularse.';
$string['tour_vacancy_step7_title'] = 'Navegación';
$string['tour_vacancy_step7_content'] = 'Use este botón para volver a la página anterior - ya sea la lista de convocatorias o la lista de vacantes, dependiendo de cómo llegó aquí.';
$string['tour_vacancy_step8_title'] = '¡Listo para Postularse!';
$string['tour_vacancy_step8_content'] = 'Ahora tiene toda la información que necesita. Si esta vacante coincide con su perfil, ¡adelante y postúlese!';

// Tour: Detalle de Postulacion.
$string['tour_application_name'] = 'Tour Detalle de Postulacion';
$string['tour_application_description'] = 'Aprenda a seguir el estado de su postulacion y gestionar documentos';
$string['tour_application_step1_title'] = 'Su Postulacion';
$string['tour_application_step1_content'] = 'Esta pagina muestra los detalles completos de su postulacion. Siga su progreso y gestione sus documentos aqui.';
$string['tour_application_step2_title'] = 'Estado de Postulacion';
$string['tour_application_step2_content'] = 'La insignia de estado muestra donde esta su postulacion en el proceso: Enviada, En Revision, Documentos Validados, Entrevista, Seleccionado o Rechazado.';
$string['tour_application_step3_title'] = 'Indicador de Progreso';
$string['tour_application_step3_content'] = 'Esta barra de progreso muestra que tan avanzado esta en el proceso de postulacion. Observe como avanza a medida que sus documentos son revisados.';
$string['tour_application_step4_title'] = 'Lista de Documentos';
$string['tour_application_step4_content'] = 'Todos sus documentos cargados se listan aqui con su estado de validacion. Verde significa aprobado, rojo significa rechazado, y amarillo significa pendiente de revision.';
$string['tour_application_step5_title'] = 'Acciones de Documentos';
$string['tour_application_step5_content'] = 'Para cada documento, puede verlo o descargarlo. Si un documento fue rechazado, vera una opcion para cargar una version corregida.';
$string['tour_application_step6_title'] = 'Historial de Postulacion';
$string['tour_application_step6_content'] = 'La seccion de historial muestra todos los cambios de estado y acciones tomadas en su postulacion. Esto le ayuda a seguir el proceso de revision.';
$string['tour_application_step7_title'] = 'Mantengase Informado!';
$string['tour_application_step7_content'] = 'Revise regularmente para actualizaciones, o active las notificaciones por correo para ser informado de cambios de estado. Buena suerte con su postulacion!';

// Tour: Mis Revisiones.
$string['tour_myreviews_name'] = 'Tour Mis Revisiones';
$string['tour_myreviews_description'] = 'Aprenda a gestionar sus revisiones de documentos asignadas';
$string['tour_myreviews_step1_title'] = 'Su Cola de Revisiones';
$string['tour_myreviews_step1_content'] = 'Bienvenido a su cola de revisiones! Esta pagina muestra todas las postulaciones y documentos asignados para su revision.';
$string['tour_myreviews_step2_title'] = 'Vista General de Cola';
$string['tour_myreviews_step2_content'] = 'Las tarjetas de vista general muestran sus revisiones pendientes, revisiones completadas y cualquier elemento urgente que requiera atencion inmediata.';
$string['tour_myreviews_step3_title'] = 'Elementos Pendientes';
$string['tour_myreviews_step3_content'] = 'Los elementos marcados con una insignia de advertencia requieren su atencion. Priorice estos para mantener el proceso de revision en movimiento.';
$string['tour_myreviews_step4_title'] = 'Tabla de Revisiones';
$string['tour_myreviews_step4_content'] = 'La tabla lista todas sus revisiones asignadas con informacion del postulante, detalles de vacante y estado actual.';
$string['tour_myreviews_step5_title'] = 'Acciones de Revision';
$string['tour_myreviews_step5_content'] = 'Haga clic en el boton Revisar para comenzar a revisar documentos de cualquier postulacion. Sera llevado a la pagina de revision detallada.';
$string['tour_myreviews_step6_title'] = 'Comience a Revisar!';
$string['tour_myreviews_step6_content'] = 'Sus revisiones ayudan a los postulantes a avanzar en el proceso. Trate de completar las revisiones prontamente para mantener un flujo de trabajo eficiente.';

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
$string['totaldoctypes'] = 'Total de Tipos de Documento';
$string['enableddoctypes'] = 'Tipos Habilitados';
$string['requireddoctypes'] = 'Tipos Requeridos';
$string['conditionaldoctypes'] = 'Tipos Condicionales';
$string['aboutdoctypes'] = 'Acerca de los Tipos de Documento';
$string['doctypelist'] = 'Lista de Tipos de Documento';
$string['items'] = 'elementos';
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
$string['type'] = 'Tipo';
$string['share'] = 'Compartir';
$string['loading'] = 'Cargando...';
$string['selectioncommittee'] = 'Comite de seleccion';

// Funcionalidad de exportacion.
$string['exportdocumentszip'] = 'Exportar Documentos (ZIP)';
$string['exportalldocuments'] = 'Exportar Todos los Documentos';
$string['exportapplicationdocs'] = 'Exportar Documentos de Postulacion';
$string['zipexportfailed'] = 'Error al crear el archivo ZIP';
$string['nodocumentstoexport'] = 'No hay documentos para exportar';
$string['invalidparameters'] = 'Parametros invalidos';
$string['exportingpdf'] = 'Exportando PDF...';
$string['pdfreportgenerated'] = 'Reporte PDF generado exitosamente';
$string['pdfexportfailed'] = 'Error al generar el reporte PDF';
$string['generatedon'] = 'Generado el';

// Conversión de documentos.
$string['conversionready'] = 'Vista previa lista';
$string['conversioninprogress'] = 'Convirtiendo documento...';
$string['conversionpending'] = 'Conversión pendiente';
$string['conversionfailed'] = 'Conversión fallida';
$string['conversionwait'] = 'El documento se está convirtiendo para vista previa. Esto puede tomar unos momentos.';
$string['previewunavailable'] = 'Vista previa no disponible para este tipo de archivo';
$string['downloadtoview'] = 'Descargar para ver';
$string['convertersavailable'] = 'Convertidores de documentos disponibles';
$string['noconvertersavailable'] = 'No hay convertidores de documentos configurados';
$string['supportedformats'] = 'Formatos soportados para conversión';
$string['documentconverted'] = 'Documento convertido exitosamente';
$string['refreshpreview'] = 'Actualizar vista previa';

// ==========================================================================
// Cadenas del Formulario de Registro Alternativo.
// ==========================================================================

// Títulos e introducción de la página de registro.
$string['signup_title'] = 'Crear Tu Cuenta';
$string['signup_intro'] = 'Regístrate para postularte a vacantes y dar seguimiento a tus aplicaciones. Completa el formulario con tu información.';
$string['signup_success_title'] = '¡Registro Exitoso!';
$string['signup_success_message'] = 'Se ha enviado un correo de confirmación a {$a}. Por favor revisa tu bandeja de entrada y haz clic en el enlace de confirmación para activar tu cuenta.';
$string['signup_success_instructions'] = 'Una vez confirmes tu correo electrónico, podrás iniciar sesión y postularte a vacantes.';

// Secciones del formulario de registro.
$string['signup_personalinfo'] = 'Información Personal';
$string['signup_contactinfo'] = 'Información de Contacto';
$string['signup_companyinfo'] = 'Selección de Compañía';
$string['signup_termsheader'] = 'Términos y Condiciones';

// Campos del formulario de registro.
$string['signup_username'] = 'Nombre de usuario';
$string['signup_username_help'] = 'Elige un nombre de usuario único que usarás para iniciar sesión. Debe contener solo letras minúsculas, números, guiones bajos y guiones.';
$string['signup_password'] = 'Contraseña';
$string['signup_password_help'] = 'Crea una contraseña segura con al menos 8 caracteres, incluyendo letras mayúsculas y minúsculas, números y símbolos.';
$string['signup_idnumber'] = 'Número de Identificación';
$string['signup_idnumber_help'] = 'Ingresa tu número de identificación nacional (ej. cédula de ciudadanía, pasaporte). Se usará para verificar tu identidad.';
$string['signup_company_help'] = 'Selecciona la compañía u organización a la que deseas postularte. Esto ayuda a dirigir tu aplicación al departamento apropiado.';

// Acciones del formulario de registro.
$string['signup_createaccount'] = 'Crear Cuenta';
$string['signup_already_account'] = '¿Ya tienes una cuenta?';
$string['signup_applying_for'] = 'Te estás registrando para postularte a:';

// Validaciones y errores del formulario de registro.
$string['signup_terms_accept'] = 'He leído y acepto los términos de servicio y la política de privacidad';
$string['signup_terms_required'] = 'Debes aceptar los términos y condiciones para crear una cuenta';
$string['signup_datatreatment_accept'] = 'Consiento el tratamiento de mis datos personales como se describe arriba';
$string['signup_datatreatment_required'] = 'Debes aceptar la política de tratamiento de datos para crear una cuenta';
$string['signup_privacy_text'] = 'Al crear una cuenta, aceptas nuestra <a href="{$a}" target="_blank">Política de Privacidad</a>. Tus datos personales serán procesados de acuerdo con las regulaciones de protección de datos aplicables.';
$string['signup_email_error'] = 'Error al enviar el correo de confirmación. Por favor intenta de nuevo o contacta a soporte.';
$string['emailnotmatch'] = 'Las direcciones de correo electrónico no coinciden';

// Indicadores de fortaleza de contraseña.
$string['password_strength_weak'] = 'Contraseña débil';
$string['password_strength_medium'] = 'Fortaleza media';
$string['password_strength_strong'] = 'Contraseña fuerte';

// Registro deshabilitado.
$string['registrationdisabled'] = 'El auto-registro está actualmente deshabilitado. Por favor contacta al administrador para asistencia.';

// ==========================================================================
// Cadenas del Formulario de Registro Mejorado (campos de perfil extendido).
// ==========================================================================

// Secciones del formulario.
$string['signup_account_header'] = 'Credenciales de la Cuenta';
$string['signup_academic_header'] = 'Perfil Académico y Profesional';
$string['signup_required_fields'] = 'Los campos marcados con asterisco son obligatorios';

// Tipos de documento.
$string['signup_doctype'] = 'Tipo de Documento';
$string['signup_doctype_cc'] = 'Cédula de Ciudadanía';
$string['signup_doctype_ce'] = 'Cédula de Extranjería';
$string['signup_doctype_passport'] = 'Pasaporte';
$string['signup_doctype_ti'] = 'Tarjeta de Identidad';
$string['signup_doctype_pep'] = 'Permiso Especial de Permanencia (PEP)';
$string['signup_doctype_ppt'] = 'Permiso de Protección Temporal (PPT)';

// Campos personales.
$string['signup_birthdate'] = 'Fecha de Nacimiento';
$string['signup_gender'] = 'Género';
$string['signup_gender_male'] = 'Masculino';
$string['signup_gender_female'] = 'Femenino';
$string['signup_gender_other'] = 'Otro';
$string['signup_gender_prefer_not'] = 'Prefiero no decir';

// Campos de contacto.
$string['signup_phone_mobile'] = 'Teléfono Celular';
$string['signup_phone_home'] = 'Teléfono Fijo/Alternativo';
$string['signup_department_region'] = 'Departamento/Estado/Provincia';

// Campos académicos.
$string['signup_education_level'] = 'Nivel Educativo Más Alto';
$string['signup_edu_highschool'] = 'Bachillerato';
$string['signup_edu_technical'] = 'Técnico';
$string['signup_edu_technological'] = 'Tecnológico';
$string['signup_edu_undergraduate'] = 'Pregrado (Profesional)';
$string['signup_edu_specialization'] = 'Especialización';
$string['signup_edu_masters'] = 'Maestría';
$string['signup_edu_doctorate'] = 'Doctorado';
$string['signup_edu_postdoctorate'] = 'Postdoctorado';

$string['signup_degree_title'] = 'Título Obtenido';
$string['signup_degree_title_help'] = 'Ingrese el nombre exacto de su título de mayor nivel. Por ejemplo: "Ingeniero de Sistemas" o "Licenciado en Matemáticas"';
$string['signup_institution'] = 'Institución';
$string['signup_institution_help'] = 'Ingrese el nombre de la institución educativa donde obtuvo su título de mayor nivel';
$string['signup_expertise_area'] = 'Área de Especialización/Expertise';
$string['signup_expertise_area_help'] = 'Ingrese su principal área de expertise profesional o especialización académica';

// Experiencia.
$string['signup_experience_years'] = 'Años de Experiencia Profesional';
$string['signup_exp_none'] = 'Sin experiencia';
$string['signup_exp_less_1'] = 'Menos de 1 año';
$string['signup_exp_1_3'] = '1 a 3 años';
$string['signup_exp_3_5'] = '3 a 5 años';
$string['signup_exp_5_10'] = '5 a 10 años';
$string['signup_exp_more_10'] = 'Más de 10 años';

// Perfil profesional.
$string['signup_professional_profile'] = 'Perfil Profesional';
$string['signup_professional_profile_help'] = 'Escriba una breve descripción de su perfil profesional, incluyendo sus habilidades clave, experiencia y objetivos profesionales (máximo 1000 caracteres)';

// Mensajes de validación.
$string['signup_username_tooshort'] = 'El nombre de usuario debe tener al menos 4 caracteres';
$string['signup_idnumber_exists'] = 'Este número de identificación ya está registrado en el sistema';
$string['signup_birthdate_minage'] = 'Debe tener al menos 18 años para registrarse';
$string['signup_dataaccuracy_accept'] = 'Declaro que toda la información proporcionada es verídica y exacta';
$string['signup_dataaccuracy_required'] = 'Debe confirmar que la información proporcionada es exacta';
$string['signup_error_creating'] = 'Ocurrió un error al crear su cuenta';

// Instrucciones de confirmación por email.
$string['signup_email_instructions_title'] = 'Próximos Pasos';
$string['signup_email_instruction_1'] = 'Revise su bandeja de entrada de correo electrónico para el mensaje de confirmación';
$string['signup_email_instruction_2'] = 'Haga clic en el enlace de confirmación del correo para activar su cuenta';
$string['signup_email_instruction_3'] = 'Una vez confirmada, podrá iniciar sesión y postularse a vacantes';
$string['signup_check_spam'] = 'Si no ve el correo, por favor revise su carpeta de spam o correo no deseado';

// Cadenas Username = Número de Identificación.
$string['signup_username_is_idnumber'] = 'Su número de identificación será su nombre de usuario para acceder a la plataforma.';
$string['signup_idnumber_username'] = 'Número de Identificación (Usuario)';
$string['signup_idnumber_username_help'] = 'Ingrese su número de identificación nacional. Este será su nombre de usuario para ingresar a la plataforma. Por ejemplo: 1234567890';
$string['signup_idnumber_tooshort'] = 'El número de identificación debe tener al menos 4 caracteres';
$string['signup_idnumber_exists_as_user'] = 'Ya existe un usuario con este número de identificación. Por favor inicie sesión.';

// Cadenas del modal de aplicación.
$string['apply_modal_title'] = 'Postularse a esta Vacante';
$string['apply_modal_question'] = '¿Ya tiene una cuenta en nuestra plataforma?';
$string['apply_modal_registered'] = 'Sí, tengo cuenta';
$string['apply_modal_not_registered'] = 'No, necesito registrarme';
$string['apply_modal_registered_desc'] = 'Inicie sesión con sus credenciales y actualice su perfil para aplicar.';
$string['apply_modal_not_registered_desc'] = 'Cree una cuenta nueva usando su número de cédula como usuario.';

// Cadenas de actualización de perfil.
$string['updateprofile_title'] = 'Actualizar su Perfil';
$string['updateprofile_intro'] = 'Por favor complete o actualice la información de su perfil antes de postularse a vacantes.';
$string['updateprofile_required'] = 'Debe completar su perfil antes de postularse a vacantes.';
$string['updateprofile_success'] = 'Su perfil ha sido actualizado exitosamente.';
$string['updateprofile_company_required'] = 'Por favor seleccione una empresa/departamento para continuar con su postulación.';
$string['updateprofile_continue_apply'] = 'Continuar a la Postulación';
$string['updateprofile_submit'] = 'Actualizar Perfil y Continuar';
$string['completeprofile_required'] = 'Debe completar la información de su perfil antes de postularse a esta vacante. Por favor complete los campos requeridos a continuación.';

// Tour: Convocatorias.
$string['tour_convocatorias_name'] = 'Tour de Gestion de Convocatorias';
$string['tour_convocatorias_description'] = 'Aprenda a crear y gestionar convocatorias que agrupan vacantes relacionadas';
$string['tour_convocatorias_step1_title'] = 'Bienvenido a Gestion de Convocatorias';
$string['tour_convocatorias_step1_content'] = 'Este es el centro de gestion de convocatorias. Una convocatoria agrupa vacantes relacionadas y define el periodo durante el cual se aceptan postulaciones. Puede crear convocatorias con fechas de inicio y fin especificas, y luego agregar multiples vacantes a cada una.';
$string['tour_convocatorias_step2_title'] = 'Crear Nueva Convocatoria';
$string['tour_convocatorias_step2_content'] = 'Haga clic en este boton para crear una nueva convocatoria. Debera proporcionar un codigo unico, nombre, descripcion, fecha de inicio, fecha de cierre y terminos y condiciones. La convocatoria puede ser publica (visible para todos) o interna (solo para usuarios autenticados).';
$string['tour_convocatorias_step3_title'] = 'Tabla de Convocatorias';
$string['tour_convocatorias_step3_content'] = 'Esta tabla muestra todas las convocatorias existentes con su codigo, nombre, fechas, estado y numero de vacantes asociadas. Cada convocatoria tiene un ciclo de vida: Borrador (en preparacion), Abierta (aceptando postulaciones), Cerrada (postulaciones finalizadas), y Archivada (registro historico).';
$string['tour_convocatorias_step4_title'] = 'Estado de la Convocatoria';
$string['tour_convocatorias_step4_content'] = 'La insignia de estado indica el estado actual de la convocatoria. Las convocatorias en borrador no son visibles para los postulantes. Cuando este listo, abra la convocatoria para comenzar a recibir postulaciones. La convocatoria puede cerrarse manualmente o se cerrara automaticamente en la fecha de cierre.';
$string['tour_convocatorias_step5_title'] = 'Estado de la Convocatoria';
$string['tour_convocatorias_step5_content'] = 'Las insignias muestran el estado actual de cada convocatoria: Borrador (en preparacion), Abierta (aceptando postulaciones), Cerrada (postulaciones finalizadas), o Archivada. El estado determina si los postulantes pueden ver y aplicar a las vacantes.';
$string['tour_convocatorias_step6_title'] = 'Importar Vacantes desde CSV';
$string['tour_convocatorias_step6_content'] = 'Use este boton para importar multiples vacantes de forma masiva desde un archivo CSV. Puede descargar una plantilla con el formato correcto y cargar los perfiles de profesores definidos en los documentos FCAS y FII.';
$string['tour_convocatorias_step7_title'] = 'Crear Nueva Convocatoria';
$string['tour_convocatorias_step7_content'] = 'Use este boton para crear una nueva convocatoria. Defina el nombre, codigo, fechas de inicio y cierre, y los terminos y condiciones. Una vez creada, podra agregar vacantes de forma individual o mediante importacion CSV.';
$string['tour_convocatorias_step8_title'] = 'Listo para Comenzar!';
$string['tour_convocatorias_step8_content'] = 'Ahora entiende como gestionar convocatorias en la Bolsa de Empleo. Recuerde: primero cree la convocatoria con sus fechas, luego agregue las vacantes (manualmente o via CSV segun los perfiles profesionales). Una vez todo configurado, abra la convocatoria para comenzar a recibir postulaciones.';

// Tour: Gestion de Tipos de Documentos.
$string['tour_documents_name'] = 'Tour de Configuracion de Tipos de Documentos';
$string['tour_documents_description'] = 'Aprenda a configurar y gestionar los tipos de documentos que los postulantes deben cargar';
$string['tour_documents_step1_title'] = 'Gestion de Tipos de Documentos';
$string['tour_documents_step1_content'] = 'Bienvenido a la pagina de configuracion de tipos de documentos! Aqui define que documentos deben cargar los postulantes al aplicar a vacantes. Cada tipo de documento especifica requisitos, reglas de validacion y si es obligatorio.';
$string['tour_documents_step2_title'] = 'Tabla de Tipos de Documentos';
$string['tour_documents_step2_content'] = 'Esta tabla muestra todos los tipos de documentos configurados. Cada tipo tiene un codigo unico, nombre, categoria y estado. Puede ver cuales documentos son requeridos versus opcionales, y cualquier condicion especial que aplique.';
$string['tour_documents_step3_title'] = 'Categorias de Documentos';
$string['tour_documents_step3_content'] = 'Los documentos se organizan en categorias: identificacion (cedulas, libreta militar), academicos (titulos, certificaciones), empleo (certificados laborales, hoja de vida), salud (EPS, pension), financieros (cuenta bancaria, RUT), y legales (antecedentes).';
$string['tour_documents_step4_title'] = 'Condiciones Especiales';
$string['tour_documents_step4_content'] = 'Algunos documentos tienen condiciones especiales: requisitos por genero (ej. libreta militar solo para hombres), exenciones por profesion (ej. sin tarjeta profesional para licenciados), o limites de antiguedad maxima del documento.';
$string['tour_documents_step5_title'] = 'Lista de Verificacion';
$string['tour_documents_step5_content'] = 'Cada tipo de documento tiene una lista de verificacion que los revisores usan para validar documentos. Esto asegura criterios de revision consistentes en todas las postulaciones y ayuda a mantener estandares de calidad.';
$string['tour_documents_step6_title'] = 'Habilitar/Deshabilitar Documentos';
$string['tour_documents_step6_content'] = 'Puede habilitar o deshabilitar tipos de documentos segun sea necesario. Los tipos de documentos deshabilitados no seran requeridos a los postulantes. Use esto para adaptar requisitos a diferentes campanas de reclutamiento.';

// Tour: Gestion de Convocatoria Individual.
$string['tour_convocatoria_manage_name'] = 'Tour de Detalle de Gestion de Convocatoria';
$string['tour_convocatoria_manage_description'] = 'Aprenda a crear y editar una convocatoria con todos sus detalles y vacantes';
$string['tour_convocatoria_manage_step1_title'] = 'Pagina de Detalle de Convocatoria';
$string['tour_convocatoria_manage_step1_content'] = 'Esta es la pagina de gestion de convocatoria. Desde aqui puede configurar todos los aspectos de una convocatoria: informacion basica, fechas, terminos, y las vacantes que pertenecen a esta convocatoria.';
$string['tour_convocatoria_manage_step2_title'] = 'Informacion Basica';
$string['tour_convocatoria_manage_step2_content'] = 'Ingrese el codigo de convocatoria (identificador unico), nombre y descripcion. El codigo debe seguir la convencion de nombres de su organizacion (ej. CONV-2024-001). La descripcion ayuda a los postulantes a entender el proposito de esta campana de reclutamiento.';
$string['tour_convocatoria_manage_step3_title'] = 'Fechas de la Convocatoria';
$string['tour_convocatoria_manage_step3_content'] = 'Establezca las fechas de inicio y fin de la convocatoria. Las postulaciones solo se aceptaran durante este periodo. Asegurese de dejar suficiente tiempo para que los postulantes recopilen los documentos requeridos y envien sus aplicaciones.';
$string['tour_convocatoria_manage_step4_title'] = 'Tipo de Publicacion';
$string['tour_convocatoria_manage_step4_content'] = 'Elija si la convocatoria es publica (visible para todos sin iniciar sesion) o interna (solo visible para usuarios autenticados). Las convocatorias internas son utiles para oportunidades de reclutamiento exclusivas para empleados.';
$string['tour_convocatoria_manage_step5_title'] = 'Terminos y Condiciones';
$string['tour_convocatoria_manage_step5_content'] = 'Defina los terminos y condiciones que los postulantes deben aceptar. Tipicamente incluye consentimiento de privacidad de datos, declaracion de veracidad de informacion, y aceptacion de las reglas del proceso de seleccion.';
$string['tour_convocatoria_manage_step6_title'] = 'Vacantes Asociadas';
$string['tour_convocatoria_manage_step6_content'] = 'Despues de crear la convocatoria, puede agregarle vacantes. Las vacantes heredan las restricciones de fecha de la convocatoria. Cada vacante representa un cargo especifico con sus propios requisitos, basados en la documentacion de PERFILES PROFESORES.';
$string['tour_convocatoria_manage_step7_title'] = 'Guardar y Publicar';
$string['tour_convocatoria_manage_step7_content'] = 'Guarde sus cambios y cuando este listo, cambie el estado de la convocatoria de Borrador a Abierta para comenzar a recibir postulaciones. Recuerde: puede agregar o modificar vacantes incluso despues de que la convocatoria este abierta.';

// Cadenas de ayuda para convocatorias.
$string['convocatoria_help'] = 'Una convocatoria agrupa vacantes laborales relacionadas bajo una campana unica con fechas de inicio y fin definidas. Al crear una vacante, puede asociarla con una convocatoria para organizar su proceso de reclutamiento. Las fechas de la convocatoria determinan cuando se pueden enviar postulaciones.';
$string['convocatoria_profile_help'] = 'Las vacantes dentro de una convocatoria deben crearse de acuerdo con los perfiles profesionales definidos en los documentos de PERFILES PROFESORES. Cada perfil especifica las calificaciones requeridas, experiencia y documentos necesarios para ese puesto.';
$string['convocatoriaid_help'] = 'Seleccione la convocatoria a la que pertenecerá esta vacante. La vacante heredará las fechas y condiciones de la convocatoria seleccionada.';
$string['convocatoriacode_help'] = 'Código único para identificar esta convocatoria. Use un formato consistente como CONV-2024-001.';
$string['convocatoriaterms_help'] = 'Ingrese los términos y condiciones que los postulantes deben aceptar al aplicar a vacantes de esta convocatoria.';
$string['departmentid_help'] = 'Seleccione el departamento de IOMAD al que pertenecerá esta vacante para filtrar por estructura organizacional.';
$string['companyid_help'] = 'Seleccione la empresa o sede donde se publicará esta vacante. En entornos multi-tenant, esto determina la visibilidad.';
$string['terms_help'] = 'Debe aceptar los términos y condiciones para continuar con el proceso.';
$string['recaptcha_help'] = 'Complete la verificación de seguridad para demostrar que no es un robot.';

// ============================================================================
// Dashboard redesign - Navegación jerárquica (Convocatorias → Vacantes)
// ============================================================================

// Welcome messages.
$string['dashboard_admin_welcome'] = 'Bienvenido al panel de administración de la Bolsa de Empleo. Desde aquí puede gestionar convocatorias, vacantes, postulaciones y revisar documentos.';
$string['dashboard_applicant_welcome'] = 'Bienvenido a la Bolsa de Empleo. Explore las vacantes disponibles y gestione sus postulaciones desde este panel.';

// Section titles.
$string['administracion'] = 'Administración';
$string['reviewertasks'] = 'Tareas de Revisión';

// Statistics cards.
$string['activeconvocatorias'] = 'Convocatorias activas';
$string['publishedvacancies'] = 'Vacantes publicadas';
$string['pendingreviews'] = 'Revisiones pendientes';

// Convocatorias management card.
$string['convocatorias_dashboard_desc'] = 'Gestione las convocatorias de la institución. Las vacantes se organizan dentro de convocatorias para facilitar el proceso de selección.';
$string['vacancies_dashboard_desc'] = 'Cree, edite y gestione las vacantes. Controle el estado de publicación y asignación de los cargos.';
$string['workflow_flow'] = 'Flujo de trabajo';
$string['selection'] = 'Selección';
$string['gotoconvocatorias'] = 'Ir a Convocatorias';

// Review card.
$string['review_dashboard_desc'] = 'Revise y valide los documentos de los postulantes. Verifique que la documentación cumpla con los requisitos establecidos.';
$string['pending_reviews_alert'] = 'Tiene {$a} documentos pendientes de revisión que requieren su atención.';
$string['gotoreview'] = 'Ir a Revisiones';

// Reports card.
$string['reports_dashboard_desc'] = 'Consulte estadísticas y reportes sobre convocatorias, vacantes, postulaciones y tiempos de procesamiento.';

// Configuration card.
$string['configuration'] = 'Configuración';
$string['config_dashboard_desc'] = 'Configure tipos de documentos, plantillas de correo, flujos de trabajo y otras opciones del sistema.';
$string['configure'] = 'Configurar';

// Exemptions card.
$string['exemptions_dashboard_desc'] = 'Gestione las excepciones documentales para personal histórico ISER y otras categorías especiales.';

// Applicant section statistics.
$string['myapplicationcount'] = 'Mis postulaciones';
$string['availablevacancies'] = 'Vacantes disponibles';
$string['pendingdocs'] = 'Documentos pendientes';

// Browse vacancies card.
$string['browservacancies'] = 'Explorar Vacantes';
$string['browse_vacancies_desc'] = 'Consulte las vacantes publicadas por la institución. Revise los requisitos y postúlese a las que cumplan con su perfil profesional.';
$string['available_vacancies_alert'] = 'Hay {$a} vacantes disponibles que coinciden con su perfil. ¡No pierda la oportunidad de postularse!';
$string['explorevacancias'] = 'Ver vacantes disponibles';

// My applications card.
$string['myapplications_desc'] = 'Consulte el estado de sus postulaciones, suba documentos pendientes y haga seguimiento a sus procesos de selección.';
$string['pending_docs_alert'] = 'Tiene {$a} documentos pendientes de cargar o corregir en sus postulaciones.';
$string['viewmyapplications'] = 'Ver mis postulaciones';

// Reviewer section.
$string['myreviews_desc'] = 'Consulte las postulaciones asignadas para revisión y valide la documentación de los postulantes.';
$string['viewmyreviews'] = 'Ver mis revisiones';

// ============================================================================
// Cadenas de ayuda adicionales para tooltips (Help strings)
// ============================================================================

// Signup form help strings - títulos.
$string['signup_email'] = 'Correo Electrónico';
$string['signup_phone'] = 'Número de Teléfono';
$string['companyid'] = 'Selección de Empresa';
$string['departmentid'] = 'Selección de Departamento';

// Signup form help strings - contenido.
$string['signup_email_help'] = 'Ingrese una dirección de correo electrónico válida. Se utilizará para enviar la confirmación de su cuenta y notificaciones importantes sobre el proceso de selección.';
$string['signup_doctype_help'] = 'Seleccione el tipo de documento de identidad que utilizará para el registro. Este documento debe coincidir con el número de identificación que ingrese.';
$string['signup_birthdate_help'] = 'Seleccione su fecha de nacimiento. Debe ser mayor de 18 años para postularse a las vacantes.';
$string['signup_phone_help'] = 'Ingrese su número de teléfono móvil principal donde podamos contactarlo para notificaciones y entrevistas.';
$string['signup_education_level_help'] = 'Seleccione el nivel educativo más alto que ha completado. Esto ayuda a filtrar las vacantes que coinciden con su perfil académico.';

// Convocatoria form help strings.
$string['convocatorianame_help'] = 'Ingrese un nombre descriptivo para la convocatoria. Este nombre será visible para los postulantes y debe reflejar el propósito de la convocatoria.';
$string['convocatoriadescription_help'] = 'Proporcione una descripción detallada de la convocatoria, incluyendo información general sobre las vacantes, requisitos comunes y el proceso de selección.';
$string['convocatoriastartdate_help'] = 'Seleccione la fecha de inicio de la convocatoria. A partir de esta fecha, las vacantes asociadas estarán disponibles para recibir postulaciones.';
$string['convocatoriaenddate_help'] = 'Seleccione la fecha de cierre de la convocatoria. Después de esta fecha, no se recibirán más postulaciones para las vacantes asociadas.';
$string['convocatoria_companyid'] = 'Empresa';
$string['convocatoria_companyid_help'] = 'Seleccione la empresa o sede a la que pertenece esta convocatoria. Seleccione "Todas las empresas" si la convocatoria es institucional.';
$string['convocatoria_departmentid'] = 'Departamento';
$string['convocatoria_departmentid_help'] = 'Seleccione el departamento de IOMAD asociado a esta convocatoria. Esto ayuda a organizar las vacantes por estructura organizacional.';

// Application form help strings.
$string['consentaccepted_help'] = 'Al marcar esta casilla, acepta que sus datos personales serán tratados de acuerdo con la política de protección de datos de la institución para fines del proceso de selección.';
$string['declarationaccepted_help'] = 'Al marcar esta casilla, declara bajo juramento que toda la información proporcionada es veraz y que los documentos adjuntos son auténticos.';

// ============================================================================
// Cadenas para el rediseño moderno de UI
// ============================================================================

// Mensajes de error.
$string['error:convocatoriarequired'] = 'Debe seleccionar una convocatoria. Las vacantes deben pertenecer a una convocatoria.';

// Búsqueda y filtros de vacantes.
$string['searchvacancies'] = 'Buscar vacantes';
$string['allcontracttypes'] = 'Todos los tipos de contrato';

// Página de detalle de vacante.
$string['closingsoondays'] = '¡Cierra en {$a} días! Postúlate ahora.';
$string['vacancyopen'] = 'Esta vacante está abierta para postulaciones';
$string['deadlineprogress'] = 'Progreso hacia la fecha límite';
$string['readytoapply'] = '¿Listo para postularte?';
$string['applynowdesc'] = 'Revisa los requisitos y envía tu postulación antes de la fecha límite.';
$string['applicationstats'] = 'Estadísticas de Postulaciones';
$string['applied'] = 'Postulado';
$string['total'] = 'Total';

// Navegación y flujo.
$string['selectconvocatoriafirst'] = 'Por favor seleccione una convocatoria primero';
$string['createvacancyinconvocatoriadesc'] = 'Las vacantes deben pertenecer a una convocatoria. Seleccione o cree una convocatoria primero, luego agregue vacantes.';
$string['noconvocatoriasavailable'] = 'No hay convocatorias disponibles. Cree una convocatoria primero para agregar vacantes.';
$string['gotocreateconvocatoria'] = 'Crear una convocatoria';

// Tarjetas del dashboard.
$string['quickstats'] = 'Estadísticas Rápidas';
$string['recentactivity'] = 'Actividad Reciente';
$string['pendingactions'] = 'Acciones Pendientes';

// Gestión de postulaciones.
$string['applicationprogress'] = 'Progreso de la Postulación';
$string['documentsvalidated'] = 'Documentos Validados';
$string['documentspending'] = 'Documentos Pendientes';
$string['documentsrejected'] = 'Documentos Rechazados';

// Interfaz de revisión.
$string['reviewqueue'] = 'Cola de Revisión';
$string['assignedtome'] = 'Asignados a mí';
$string['pendingmyreview'] = 'Pendientes de mi revisión';
$string['completedreviews'] = 'Revisiones completadas';

// Flujo de estados.
$string['workflowstatus'] = 'Estado del Flujo';
$string['nextsteps'] = 'Próximos Pasos';
$string['statusupdated'] = 'Estado actualizado exitosamente';

// Accesibilidad.
$string['expandsection'] = 'Expandir sección';
$string['collapsesection'] = 'Contraer sección';
$string['loadingcontent'] = 'Cargando contenido...';
$string['actionrequired'] = 'Acción requerida';

// Tooltips para la interfaz rediseñada.
$string['tooltip_viewdetails'] = 'Clic para ver detalles completos';
$string['tooltip_quickapply'] = 'Iniciar tu postulación';
$string['tooltip_trackstatus'] = 'Seguir el estado de tu postulación';
$string['tooltip_downloadall'] = 'Descargar todos los documentos';
$string['tooltip_filterresults'] = 'Filtrar resultados por criterios';

// ============================================================================
// Cadenas de rediseño de la interfaz de revisión
// ============================================================================
$string['pendingreview'] = 'Pendiente de Revisión';
$string['reviewapplication'] = 'Revisar Postulación';
$string['reviewprogress'] = 'Progreso de Revisión';
$string['alldocsreviewed'] = 'Todos los documentos han sido revisados';
$string['rejectreason_placeholder'] = 'Describa el motivo del rechazo...';

// Cadenas de rediseño de la página pública.
$string['alllocations'] = 'Todas las ubicaciones';
$string['allconvocatorias'] = 'Todas las convocatorias';
$string['openvacancies'] = 'Vacantes Abiertas';
$string['logintoapply'] = 'Inicia sesión para postularte a esta posición';
$string['allmodalities'] = 'Todas las modalidades';
$string['filtervacancies'] = 'Filtrar vacantes';
$string['publicpagedesc'] = 'Explora nuestras posiciones disponibles y encuentra tu próxima oportunidad laboral.';
$string['closingsoon'] = 'cierra pronto';

// General.
$string['viewall'] = 'Ver todos';
$string['urgent'] = 'Urgente';
$string['pending'] = 'Pendiente';
$string['allstatuses'] = 'Todos los estados';

// ============================================================================
// Cadenas sincronizadas desde EN - 2025-12-06
// ============================================================================
$string['actions'] = 'Acciones';
$string['adminstatistics'] = 'Estadísticas de administración';
$string['applicantstatistics'] = 'Estadísticas de postulantes';
$string['applicationform'] = 'Formulario de Postulación';
$string['applicationhelptext'] = 'Si necesita ayuda con su postulación, por favor contáctenos.';
$string['applicationinfo'] = 'Información de la Postulación';
$string['applicationlist'] = 'Lista de postulaciones';
$string['applicationsqueue'] = 'Cola de postulaciones';
$string['applicationstatistics'] = 'Estadísticas de postulaciones';
$string['applyto'] = 'Postularse a';
$string['approvalrate'] = 'Tasa de Aprobación';
$string['approved'] = 'Aprobado';
$string['at'] = 'a las';
$string['cannotapply'] = 'No puede postularse a esta vacante.';
$string['changessaved'] = 'Cambios guardados exitosamente';
$string['clickfordetails'] = 'Clic para ver detalles';
$string['closealert'] = 'Cerrar alerta';
$string['complete'] = 'completo';
$string['contactus'] = 'Contáctenos';
$string['datatable'] = 'Tabla de datos';
$string['date'] = 'Fecha';
$string['daysremaining'] = 'Días restantes';
$string['documentactions'] = 'Acciones del documento';
$string['documentstoreview'] = 'Documentos por revisar';
$string['editvacancy'] = 'Editar vacante';
$string['features'] = 'Características';
$string['filterform'] = 'Formulario de filtros';
$string['inprogress'] = 'En Progreso';
$string['issuedate'] = 'Fecha de Emisión';
$string['issuedatehelp'] = 'Fecha en que se emitió el documento';
$string['lastupdated'] = 'Última Actualización';
$string['logintoviewmore'] = 'Inicie sesión para ver todas las vacantes disponibles';
$string['navigation'] = 'Navegación';
$string['needhelp'] = '¿Necesita Ayuda?';
$string['noapplications'] = 'Sin postulaciones';
$string['noapplicationsdesc'] = 'Aún no ha enviado ninguna postulación. Explore las vacantes disponibles para comenzar.';
$string['noconvocatoriasdesc'] = 'No hay convocatorias abiertas en este momento. Vuelva más tarde para ver nuevas oportunidades.';
$string['nodata'] = 'No hay datos disponibles';
$string['nohistory'] = 'No hay historial disponible';
$string['noteshelptext'] = 'Agregue notas sobre el cambio de estado (visible para el postulante)';
$string['novacanciesyet'] = 'Aún no hay vacantes disponibles';
$string['pagination'] = 'Paginación';
$string['pendingbytype'] = 'Pendientes por Tipo';
$string['pendingdocsalert'] = 'Tiene documentos pendientes que requieren atención.';
$string['percentage'] = 'Porcentaje';
$string['performance'] = 'Rendimiento';
$string['performedby'] = 'Realizado por';
$string['progressindicator'] = 'Indicador de progreso';
$string['requireddocument'] = 'Documento requerido';
$string['resetfilters'] = 'Restablecer filtros';
$string['reviewstatistics'] = 'Estadísticas de revisión';
$string['select'] = 'Seleccionar';
$string['shareonfacebook'] = 'Compartir en Facebook';
$string['shareonlinkedin'] = 'Compartir en LinkedIn';
$string['shareontwitter'] = 'Compartir en Twitter';
$string['showingresults'] = 'Mostrando resultados';
$string['trend'] = 'Tendencia';
$string['trending_down'] = 'Tendencia a la baja';
$string['trending_up'] = 'Tendencia al alza';
$string['trydifferentfilters'] = 'Intente ajustar sus filtros para ver más resultados.';
$string['uploadform'] = 'Formulario de carga';
$string['uploading'] = 'Subiendo...';
$string['vacancydetails'] = 'Detalles de la vacante';
$string['vacancysummary'] = 'Resumen de la Vacante';
$string['warning'] = 'Advertencia';

// ============================================================================
// Cadenas de tours adicionales - 2025-12-06
// ============================================================================

// Tour: Dashboard (pasos 6-8)
$string['tour_dashboard_step6_title'] = 'Sección de Revisor';
$string['tour_dashboard_step6_content'] = 'Si eres un revisor de documentos, esta sección muestra tus revisiones asignadas y documentos pendientes. Mantén un seguimiento de tus tareas de revisión aquí.';
$string['tour_dashboard_step7_title'] = 'Mis Postulaciones';
$string['tour_dashboard_step7_content'] = 'Como postulante, esta sección muestra el estado de tus postulaciones y acceso rápido para ver todas tus postulaciones. Sigue tu progreso aquí.';
$string['tour_dashboard_step8_title'] = '¡Listo para Empezar!';
$string['tour_dashboard_step8_content'] = '¡Ya estás listo para usar la Bolsa de Empleo! Explora las opciones disponibles y no dudes en usar los recursos de ayuda si necesitas asistencia.';

// Tour: Public (paso 10)
$string['tour_public_step9_title'] = 'Llamada a la Acción';
$string['tour_public_step9_content'] = 'Si no has iniciado sesión, verás un mensaje para crear una cuenta o iniciar sesión. Esto te permite postularte a vacantes y seguir tus postulaciones.';
$string['tour_public_step10_title'] = '¡Comienza a Explorar!';
$string['tour_public_step10_content'] = '¡Ya estás listo para explorar las vacantes disponibles! Usa la búsqueda y los filtros para encontrar oportunidades que coincidan con tus habilidades e intereses. ¡Buena suerte con tus postulaciones!';

// Tour: Apply (paso 9)
$string['tour_apply_step9_title'] = '¿Necesitas Ayuda?';
$string['tour_apply_step9_content'] = 'Si encuentras algún problema o tienes preguntas sobre el proceso de postulación, por favor contacta a nuestro equipo de soporte. ¡Estamos aquí para ayudarte a tener éxito!';

// Tour: My Applications (paso 8)
$string['tour_myapplications_step7_title'] = 'Ver Detalles de Postulación';
$string['tour_myapplications_step7_content'] = 'Haz clic en "Ver" para ver los detalles completos de tu postulación y los documentos subidos. También puedes retirar tu postulación si aún está en revisión.';
$string['tour_myapplications_step8_title'] = '¡Mantente Actualizado!';
$string['tour_myapplications_step8_content'] = 'Revisa esta página regularmente para ver actualizaciones de tus postulaciones. También recibirás notificaciones por correo electrónico para cambios importantes de estado. ¡Buena suerte con tus postulaciones!';

// Paginación y acciones masivas (v2.0.25).
$string['recordsperpage'] = 'Registros por página';
$string['entries'] = 'registros';
$string['showingxofy'] = 'Mostrando {$a->start} a {$a->end} de {$a->total} registros';
$string['selectall'] = 'Seleccionar todo';
$string['selected'] = 'seleccionados';
$string['bulkactions'] = 'Acciones masivas';
$string['bulkdelete'] = 'Eliminar seleccionados';
$string['bulkpublish'] = 'Publicar seleccionados';
$string['bulkunpublish'] = 'Despublicar seleccionados';
$string['bulkclose'] = 'Cerrar seleccionados';
$string['confirmdelete'] = '¿Está seguro de que desea eliminar los elementos seleccionados? Esta acción no se puede deshacer.';
$string['confirmpublish'] = '¿Está seguro de que desea publicar los elementos seleccionados?';
$string['confirmunpublish'] = '¿Está seguro de que desea despublicar los elementos seleccionados?';
$string['confirmclose'] = '¿Está seguro de que desea cerrar los elementos seleccionados?';
$string['itemsdeleted'] = '{$a} elemento(s) eliminado(s) exitosamente';
$string['itemspublished'] = '{$a} elemento(s) publicado(s) exitosamente';
$string['itemsunpublished'] = '{$a} elemento(s) despublicado(s) exitosamente';
$string['itemsclosed'] = '{$a} elemento(s) cerrado(s) exitosamente';
$string['noitemsselected'] = 'No hay elementos seleccionados';
$string['confirmaction'] = 'Confirmar acción';
$string['bulkactionerrors'] = '{$a} elemento(s) no pudieron ser procesados debido a errores';

// CSV Import for vacancies.
$string['importvacancies'] = 'Importar Vacantes desde CSV';
$string['importvacancies_help'] = 'Suba un archivo CSV con las vacantes a importar. Use la plantilla para asegurarse del formato correcto.';
$string['downloadcsvtemplate'] = 'Descargar plantilla CSV';
$string['selectconvocatoria'] = '-- Seleccione una convocatoria --';
$string['iomadoptions'] = 'Opciones IOMAD';
$string['createcompanies'] = 'Crear empresas automáticamente';
$string['createcompanies_help'] = 'Si está habilitado, se crearán automáticamente las empresas IOMAD basándose en las ubicaciones del CSV.';
$string['importoptions'] = 'Opciones de importación';
$string['defaultstatus'] = 'Estado inicial';
$string['vacancy_status_draft'] = 'Borrador';
$string['vacancy_status_published'] = 'Publicada';
$string['updateexisting'] = 'Actualizar vacantes existentes';
$string['updateexisting_help'] = 'Si está habilitado, las vacantes con el mismo código serán actualizadas. De lo contrario, se omitirán.';
$string['previewmode'] = 'Modo vista previa - No se han realizado cambios. Revise los datos y suba nuevamente sin la opción de vista previa para importar.';
$string['previewconfirm'] = 'Se encontraron {$a} vacantes para importar. Suba nuevamente el archivo sin la opción de "Vista previa" para confirmar la importación.';
$string['uploadnewfile'] = 'Subir nuevo archivo';
$string['vacancies_created'] = 'Vacantes creadas';
$string['vacancies_updated'] = 'Vacantes actualizadas';
$string['vacancies_skipped'] = 'Vacantes omitidas';
$string['importerror_vacancyexists'] = 'Fila {$a->row}: La vacante {$a->code} ya existe';
$string['backtoconvocatorias'] = 'Volver a Convocatorias';
$string['csvformat_desc'] = 'El archivo CSV debe contener una fila de encabezado con los nombres de las columnas. Los cursos deben separarse con el caracter | (pipe).';
$string['csvcolumn_code'] = 'Código único de la vacante';
$string['csvcolumn_contracttype'] = 'Tipo de vinculación (OCASIONAL TIEMPO COMPLETO o CATEDRA)';
$string['csvcolumn_program'] = 'Programa académico';
$string['csvcolumn_profile'] = 'Perfil profesional requerido';
$string['csvcolumn_courses'] = 'Cursos a orientar (separados por |)';
$string['csvcolumn_location'] = 'Ubicación (PAMPLONA, CUCUTA, TIBU, etc.)';
$string['csvcolumn_modality'] = 'Modalidad (PRESENCIAL o A DISTANCIA)';
$string['csvcolumn_faculty'] = 'Facultad (FCAS o FII)';
$string['csvexample'] = 'Ejemplo de CSV';
$string['csvexample_desc'] = 'Puede copiar y pegar este ejemplo como base para crear su archivo CSV:';
$string['csvexample_tip'] = 'Los cursos se separan con el caracter | (pipe). Las ubicaciones válidas son: PAMPLONA, CUCUTA, TIBU, SANVICENTE, ELTARRA, OCANA, PUEBLOBELLO, SANPABLO, SANTAROSA, TAME, FUNDACION, CIMITARRA, SALAZAR, TOLEDO.';
$string['column'] = 'Columna';
$string['required'] = 'Requerido';
$string['example'] = 'Ejemplo';
$string['row'] = 'Fila';
$string['andmore'] = 'Y {$a} más...';

// ============================================================================
// Cadenas para vista de postulación mejorada - 2025-12-09
// ============================================================================
$string['step_consent'] = 'Consentimiento';
$string['step_documents'] = 'Documentos';
$string['step_coverletter'] = 'Carta';
$string['step_submit'] = 'Enviar';
$string['applicationsteps_tooltip'] = 'Progreso de su postulación. Complete cada sección en orden.';
$string['deadlinewarning_title'] = '¡Fecha límite próxima!';
$string['applyhelp_text'] = 'Si tiene dudas sobre cómo completar su postulación, inicie el tour guiado o contacte a soporte.';
$string['restarttour'] = 'Iniciar Tour Guiado';
$string['documentchecklist'] = 'Lista de Documentos';

// ============================================================================
// Cadenas para vista de revisión mejorada - 2025-12-09
// ============================================================================
$string['reviewsteps_tooltip'] = 'Progreso de revisión: examine cada documento y apruebe o rechace.';
$string['reviewhelp_text'] = 'Para cada documento, descárguelo y examínelo cuidadosamente. Luego apruebe si cumple los requisitos o rechace con una explicación clara.';
$string['step_examine'] = 'Examinar';
$string['step_validate'] = 'Validar';
$string['step_complete'] = 'Completar';
$string['reviewguidelines'] = 'Guía de Revisión';
$string['guideline_review1'] = 'Descargue y abra cada documento para verificar su contenido y autenticidad.';
$string['guideline_review2'] = 'Verifique que los documentos sean legibles, completos y correspondan al tipo requerido.';
$string['guideline_review3'] = 'Al rechazar un documento, proporcione una razón clara para que el postulante sepa qué corregir.';
$string['guideline_review4'] = 'Complete todas las revisiones de documentos antes de marcar la postulación como revisada.';
$string['quickactions'] = 'Acciones Rápidas';
$string['approveall_confirm'] = '¿Está seguro de aprobar todos los documentos pendientes? Esta acción no se puede deshacer.';
$string['documentchecklist_reviewer'] = 'Documentos a Revisar';
$string['reviewtips'] = 'Consejos de Revisión';
$string['tip_download'] = 'Descargue cada documento para verificar su contenido';
$string['tip_legible'] = 'Asegúrese de que los documentos sean claros y legibles';
$string['tip_complete'] = 'Verifique que toda la información requerida esté presente';
$string['tip_authentic'] = 'Verifique la autenticidad del documento cuando sea posible';
$string['needsattention'] = 'Requiere Atención';
$string['allclear'] = 'Todo en Orden';
$string['documentsremaining'] = '{$a} documento(s) pendiente(s)';
$string['reviewcompletetooltip'] = 'Todos los documentos revisados. Haga clic para completar el proceso de revisión.';

// ============================================================================
// Cadenas de Mejora UX - 2025-12-09
// Navegación y Feedback
// ============================================================================
$string['invalidview'] = 'La vista solicitada no existe.';
$string['redirectedtodashboard'] = 'Ha sido redirigido al panel principal.';
$string['actioncompleted'] = 'Acción completada exitosamente.';
$string['actionfailed'] = 'No se pudo completar la acción. Por favor intente de nuevo.';
$string['loadinginprogress'] = 'Cargando...';
$string['processingrequest'] = 'Procesando su solicitud...';
$string['pleasewait'] = 'Por favor espere...';

// Pasos de Progreso de Registro
$string['signup_step_account'] = 'Cuenta';
$string['signup_step_personal'] = 'Datos Personales';
$string['signup_step_contact'] = 'Contacto';
$string['signup_step_academic'] = 'Formación';
$string['signup_step_company'] = 'Empresa';
$string['signup_step_confirm'] = 'Confirmar';
$string['signup_progress'] = 'Progreso del Registro';
$string['signup_fields_required'] = '{$a} campos requeridos';
$string['signup_section_complete'] = 'Sección completa';
$string['signup_section_incomplete'] = 'Sección incompleta';

// Bienvenida Dashboard
$string['welcomeback'] = '¡Bienvenido/a de nuevo, {$a}!';
$string['dashboardwelcome'] = '¿Qué le gustaría hacer hoy?';
$string['dashboardwelcome_candidate'] = 'Encuentre y postúlese a oportunidades laborales que coincidan con su perfil.';
$string['dashboardwelcome_employer'] = 'Gestione sus vacantes y revise las postulaciones de candidatos.';
$string['thisweek'] = 'esta semana';
$string['thismonth'] = 'este mes';
$string['noactivity'] = 'Sin actividad reciente';
$string['quickactions_title'] = 'Acciones Rápidas';
$string['recentactivity'] = 'Actividad Reciente';
$string['pendingitems'] = 'Elementos Pendientes';

// Proceso de Postulación
$string['applyingto'] = 'Usted está postulándose a:';
$string['applicationfor'] = 'Postulación para: {$a}';
$string['closingsoon'] = 'Cierra en {$a} días';
$string['closingtoday'] = '¡Cierra hoy!';
$string['closingtomorrow'] = 'Cierra mañana';
$string['alreadyclosed'] = 'Esta vacante está cerrada';
$string['documentsprogress'] = 'Documentos subidos';
$string['documentsprogress_detail'] = '{$a->uploaded} de {$a->total} documentos';
$string['alldocumentsuploaded'] = 'Todos los documentos requeridos han sido subidos';
$string['missingdocuments'] = '{$a} documento(s) requerido(s) faltante(s)';

// Modal de Confirmación de Postulación
$string['confirmapplication'] = 'Confirmar su postulación';
$string['confirmapplication_title'] = '¿Listo para enviar?';
$string['confirmapplication_text'] = 'Por favor verifique lo siguiente antes de enviar su postulación:';
$string['confirmapplication_docs'] = 'Todos los documentos requeridos han sido subidos';
$string['confirmapplication_data'] = 'Su información personal es precisa y está actualizada';
$string['confirmapplication_consent'] = 'Ha leído y aceptado los términos y condiciones';
$string['confirmapplication_final'] = 'Una vez enviada, no podrá modificar su postulación. ¿Está seguro de que desea continuar?';
$string['confirmsubmit'] = 'Sí, enviar mi postulación';
$string['cancelsubmit'] = 'Cancelar, déjeme revisar';
$string['applicationsubmitting'] = 'Enviando su postulación...';
$string['applicationsubmitted_success'] = '¡Su postulación ha sido enviada exitosamente!';
$string['applicationsubmitted_next'] = 'Recibirá un correo de confirmación en breve. Puede hacer seguimiento al estado de su postulación en "Mis Postulaciones".';

// Estado de Documentos
$string['docstatus_pending'] = 'Pendiente de subir';
$string['docstatus_uploading'] = 'Subiendo...';
$string['docstatus_uploaded'] = 'Subido';
$string['docstatus_validating'] = 'En revisión';
$string['docstatus_approved'] = 'Aprobado';
$string['docstatus_rejected'] = 'Rechazado';
$string['docstatus_expired'] = 'Expirado';
$string['docupload_success'] = 'Documento subido exitosamente';
$string['docupload_error'] = 'Error al subir el documento';
$string['docremove_confirm'] = '¿Está seguro de que desea eliminar este documento?';

// Panel de Revisión
$string['reviewprogress'] = 'Progreso de Revisión';
$string['reviewprogress_detail'] = '{$a->reviewed} de {$a->total} documentos revisados';
$string['documentsapproved'] = 'Aprobados';
$string['documentsrejected'] = 'Rechazados';
$string['documentspending'] = 'Pendientes';
$string['reviewcomplete'] = 'Revisión Completa';
$string['reviewincomplete'] = 'Revisión Incompleta';
$string['startreview'] = 'Iniciar Revisión';
$string['continuereview'] = 'Continuar Revisión';
$string['reviewsummary'] = 'Resumen de Revisión';

// Atajos de Teclado
$string['keyboardshortcuts'] = 'Atajos de Teclado';
$string['shortcut_approve'] = 'Aprobar documento';
$string['shortcut_reject'] = 'Rechazar documento';
$string['shortcut_next'] = 'Siguiente documento';
$string['shortcut_previous'] = 'Documento anterior';
$string['shortcut_save'] = 'Guardar cambios';
$string['shortcut_help'] = 'Mostrar ayuda de atajos';
$string['shortcutshelp_title'] = 'Atajos de Teclado Disponibles';

// Resumen de Términos
$string['termssummary'] = 'Puntos Clave';
$string['termssummary_intro'] = 'Al enviar esta postulación, usted acepta:';
$string['termssummary_1'] = 'Sus datos serán procesados de acuerdo con nuestra política de privacidad';
$string['termssummary_2'] = 'Puede solicitar la eliminación de sus datos en cualquier momento';
$string['termssummary_3'] = 'Su postulación puede ser compartida con personal autorizado de selección';
$string['termssummary_4'] = 'Toda la información proporcionada debe ser precisa y veraz';
$string['viewfullterms'] = 'Ver términos y condiciones completos';
$string['hidefullterms'] = 'Ocultar términos completos';

// Validación de Formularios
$string['validating'] = 'Validando...';
$string['fieldvalid'] = 'Este campo es válido';
$string['fieldinvalid'] = 'Por favor revise este campo';
$string['formhasserrors'] = 'Por favor corrija los errores antes de continuar';
$string['allfieldsvalid'] = 'Todos los campos son válidos';

// Accesibilidad
$string['skiptomaincontent'] = 'Saltar al contenido principal';
$string['skiptoform'] = 'Saltar al formulario';
$string['clickfordetails'] = 'Clic para ver detalles';
$string['expandsection'] = 'Expandir sección';
$string['collapsesection'] = 'Contraer sección';
$string['opensinnewwindow'] = 'Abre en nueva ventana';
$string['requiredfield'] = 'Campo requerido';
$string['optionalfield'] = 'Campo opcional';
$string['currentstep'] = 'Paso actual';
$string['completedstep'] = 'Paso completado';
$string['pendingstep'] = 'Paso pendiente';

// Estados de Carga
$string['loading_vacancies'] = 'Cargando vacantes...';
$string['loading_applications'] = 'Cargando postulaciones...';
$string['loading_documents'] = 'Cargando documentos...';
$string['loading_data'] = 'Cargando datos...';
$string['savingchanges'] = 'Guardando cambios...';
$string['uploadingfile'] = 'Subiendo archivo...';

// Estados Vacíos
$string['novacancies_candidate'] = 'No hay vacantes disponibles en este momento. Vuelva más tarde para ver nuevas oportunidades.';
$string['noapplications_candidate'] = 'Aún no se ha postulado a ninguna vacante. Explore las posiciones disponibles para comenzar.';
$string['noapplications_employer'] = 'Aún no se han recibido postulaciones para esta vacante.';
$string['nodocuments_review'] = 'No hay documentos pendientes de revisión.';

// Mensajes de Éxito
$string['changesaved'] = 'Sus cambios han sido guardados.';
$string['documentsaved'] = 'Documento guardado exitosamente.';
$string['applicationsaved'] = 'Postulación guardada como borrador.';
$string['reviewsaved'] = 'Revisión guardada exitosamente.';

// ============================================================================
// Mejoras de navegación UX (v2.0.64)
// ============================================================================
$string['backtovacancy'] = 'Volver a la vacante';
$string['backtodashboard'] = 'Volver al Panel';
$string['backtoreviewlist'] = 'Volver a la lista de revisiones';

// Advertencia de cambios sin guardar.
$string['unsavedchanges'] = 'Cambios sin guardar';
$string['unsavedchangeswarning'] = 'Tiene cambios sin guardar. ¿Está seguro de que desea salir de esta página? Sus cambios se perderán.';
$string['leave'] = 'Salir';
$string['stay'] = 'Quedarse';

// Mejoras del sidebar de ayuda en postulaciones.
$string['quicktips'] = 'Consejos Rápidos';
$string['tip_saveoften'] = 'Complete todos los campos requeridos antes de enviar';
$string['tip_checkdocs'] = 'Asegúrese de que los documentos sean claros y legibles';
$string['tip_deadline'] = 'Envíe antes de que expire el plazo';
$string['viewfaq'] = 'Preguntas Frecuentes';
$string['contactsupport'] = 'Contactar Soporte';
$string['viewvacancydetails'] = 'Ver detalles de la vacante';

// Categorías de documentos.
$string['doccat_employment'] = 'Laboral';
$string['doccat_employment_desc'] = 'Historial laboral y documentos de empleo';
$string['doccat_identification'] = 'Identificación';
$string['doccat_identification_desc'] = 'Documentos de identificación personal';
$string['doccat_academic'] = 'Académico';
$string['doccat_academic_desc'] = 'Títulos, certificaciones y registros académicos';
$string['doccat_financial'] = 'Financiero';
$string['doccat_financial_desc'] = 'Documentos tributarios y bancarios';
$string['doccat_health'] = 'Salud y Seguridad Social';
$string['doccat_health_desc'] = 'Afiliación a salud y pensión';
$string['doccat_legal'] = 'Antecedentes';
$string['doccat_legal_desc'] = 'Certificados de antecedentes y verificaciones legales';

// Etiquetas de campos de documentos.
$string['docrequirements'] = 'Ver requisitos';
$string['optional'] = 'Opcional';

// Aviso de múltiples documentos.
$string['multipledocs_notice'] = 'Múltiples certificados en un solo archivo';
$string['multipledocs_titulo_academico'] = 'Si tiene varios títulos (pregrado, posgrado, especialización), combínelos todos en un solo archivo PDF.';
$string['multipledocs_formacion_complementaria'] = 'Si tiene múltiples certificados de formación complementaria, combínelos todos en un solo archivo PDF.';
$string['multipledocs_certificacion_laboral'] = 'Si tiene múltiples certificaciones laborales, combínelas todas en un solo archivo PDF ordenadas por fecha (más reciente primero).';

// ============================================================================
// Configuración de reCAPTCHA y Seguridad (v2.0.71)
// ============================================================================
$string['recaptchasettings'] = 'Configuración de reCAPTCHA';
$string['recaptchasettings_desc'] = 'Configure reCAPTCHA para prevenir spam y abuso en los formularios de registro y actualización de perfil.';
$string['recaptcha_enabled'] = 'Habilitar reCAPTCHA';
$string['recaptcha_enabled_desc'] = 'Habilitar verificación reCAPTCHA en los formularios de registro y actualización de perfil.';
$string['recaptcha_version'] = 'Versión de reCAPTCHA';
$string['recaptcha_version_desc'] = 'Seleccione qué versión de reCAPTCHA usar.';
$string['recaptcha_v2'] = 'reCAPTCHA v2 (Casilla de verificación)';
$string['recaptcha_v3'] = 'reCAPTCHA v3 (Invisible)';
$string['recaptcha_sitekey'] = 'Clave del sitio';
$string['recaptcha_sitekey_desc'] = 'Ingrese la clave del sitio reCAPTCHA desde la consola de Google reCAPTCHA.';
$string['recaptcha_secretkey'] = 'Clave secreta';
$string['recaptcha_secretkey_desc'] = 'Ingrese la clave secreta reCAPTCHA desde la consola de Google reCAPTCHA.';
$string['recaptcha_v3_threshold'] = 'Umbral de puntuación v3';
$string['recaptcha_v3_threshold_desc'] = 'Puntuación mínima (0.0-1.0) requerida para aprobar la verificación. Por defecto: 0.5';
$string['recaptcha_required'] = 'Por favor complete la verificación de seguridad.';
$string['recaptcha_failed'] = 'La verificación de seguridad falló. Por favor intente de nuevo.';
$string['verification'] = 'Verificación de Seguridad';

// ============================================================================
// Gestión de Credenciales de Cuenta (v2.0.71)
// ============================================================================
$string['username_differs_idnumber'] = 'Su nombre de usuario es diferente a su número de identificación. Puede actualizarlo para que coincida con su número de documento para un inicio de sesión más fácil.';
$string['update_username'] = 'Actualizar nombre de usuario';
$string['update_username_desc'] = 'Cambiar mi nombre de usuario para que coincida con mi número de identificación';
$string['password_change_optional'] = 'Deje los campos de contraseña vacíos si no desea cambiar su contraseña. Complete todos los campos solo si desea establecer una nueva contraseña.';
$string['currentpassword'] = 'Contraseña actual';
$string['currentpassword_help'] = 'Ingrese su contraseña actual para verificar su identidad. Esto es requerido si desea cambiar su correo electrónico o contraseña.';
$string['confirmpassword'] = 'Confirmar nueva contraseña';
$string['currentpassword_required'] = 'La contraseña actual es requerida para cambiar el correo electrónico o la contraseña.';
$string['currentpassword_invalid'] = 'La contraseña actual que ingresó es incorrecta.';
$string['passwordsdiffer'] = 'Las contraseñas no coinciden.';
$string['email_updated'] = 'Su correo electrónico ha sido actualizado.';
$string['password_updated'] = 'Su contraseña ha sido actualizada.';
$string['username_updated'] = 'Su nombre de usuario ha sido actualizado a: {$a}';
$string['completeprofile_required'] = 'Por favor complete la información de su perfil antes de postularse a esta vacante.';

// ============================================================================
// Fase 10 - Refactorización Mayor v2.1.0
// ============================================================================

// Herencia de fechas de vacante desde convocatoria.
$string['vacancy_inherits_dates'] = 'Las fechas de apertura y cierre de la vacante se heredan de la convocatoria asociada.';
$string['legacyconvocatoria'] = 'Convocatoria Heredada';
$string['legacyconvocatoria_desc'] = 'Convocatoria creada automáticamente durante la migración para vacantes sin convocatoria asignada.';
$string['convocatoriadates'] = 'Fechas de la convocatoria';

// Límites de aplicaciones por convocatoria.
$string['applicationlimits'] = 'Límites de aplicaciones';
$string['allowmultipleapplications_convocatoria'] = 'Permitir múltiples aplicaciones';
$string['allowmultipleapplications_convocatoria_desc'] = 'Si se habilita, los usuarios pueden aplicar a múltiples vacantes dentro de esta convocatoria.';
$string['maxapplicationsperuser'] = 'Máximo de aplicaciones por usuario';
$string['maxapplicationsperuser_desc'] = 'Número máximo de aplicaciones que un usuario puede enviar a esta convocatoria (0 = sin límite).';
$string['error:singleapplicationonly'] = 'Solo puede aplicar a una vacante por convocatoria. Ya tiene una aplicación activa en esta convocatoria.';
$string['error:applicationlimitreached'] = 'Ha alcanzado el límite máximo de {$a} aplicaciones para esta convocatoria.';

// Requisito de experiencia para contratos ocasionales.
$string['error:occasionalrequiresexperience'] = 'Los contratos ocasionales requieren un mínimo de 2 años de experiencia laboral relacionada.';
$string['occasionalcontract_experience_notice'] = 'Nota: Los contratos de tipo ocasional requieren un mínimo de 2 años de experiencia laboral relacionada.';

// Tarjeta profesional opcional.
$string['tarjeta_profesional_note'] = 'Este documento es obligatorio para aspirantes a cargos docentes que requieran título profesional (Art. 6 Ley 1188/2008).';
$string['conditional_document_note'] = 'Nota: {$a}';

// Exención de documentos por edad.
$string['age_exempt_notice'] = 'Este documento no es requerido para personas mayores de {$a} años.';
$string['document_age_exemption'] = 'Exento por edad';

// Acciones de auditoría.
$string['audit_action_create'] = 'Crear';
$string['audit_action_update'] = 'Actualizar';
$string['audit_action_delete'] = 'Eliminar';
$string['audit_action_view'] = 'Ver';
$string['audit_action_download'] = 'Descargar';
$string['audit_action_validate'] = 'Validar';
$string['audit_action_reject'] = 'Rechazar';
$string['audit_action_submit'] = 'Enviar';
$string['audit_action_transition'] = 'Cambio de estado';
$string['audit_action_email_sent'] = 'Correo enviado';
$string['audit_action_login'] = 'Inicio de sesión';
$string['audit_action_export'] = 'Exportar';
$string['audit_action_upload'] = 'Subir archivo';

// Entidades de auditoría.
$string['audit_entity_vacancy'] = 'Vacante';
$string['audit_entity_application'] = 'Aplicación';
$string['audit_entity_document'] = 'Documento';
$string['audit_entity_exemption'] = 'Exención';
$string['audit_entity_convocatoria'] = 'Convocatoria';
$string['audit_entity_config'] = 'Configuración';
$string['audit_entity_user'] = 'Usuario';
$string['audit_entity_email_template'] = 'Plantilla de correo';

// Campos de auditoría.
$string['previousvalue'] = 'Valor anterior';
$string['newvalue'] = 'Nuevo valor';
$string['audittrail'] = 'Historial de auditoría';
$string['viewaudittrail'] = 'Ver historial de auditoría';

// Plantillas de correo electrónico.
$string['emailtemplates'] = 'Plantillas de correo electrónico';
$string['emailtemplate'] = 'Plantilla de correo';
$string['templatekey'] = 'Clave de plantilla';
$string['templatesubject'] = 'Asunto';
$string['templatebody'] = 'Cuerpo del mensaje';
$string['availableplaceholders'] = 'Marcadores disponibles';
$string['emailtemplate_saved'] = 'Plantilla de correo guardada correctamente.';
$string['emailtemplate_deleted'] = 'Plantilla de correo eliminada.';
$string['restoreddefault'] = 'Restaurar valores por defecto';
$string['emailtemplate_restored'] = 'Plantilla restaurada a los valores por defecto.';

// Claves de plantillas de correo.
$string['template_application_received'] = 'Aplicación recibida';
$string['template_application_approved'] = 'Aplicación aprobada';
$string['template_application_rejected'] = 'Aplicación rechazada';
$string['template_document_rejected'] = 'Documento rechazado';
$string['template_review_complete'] = 'Revisión completada';

// Notificación de revisión completada (documentos).
$string['notification_review_complete_subject'] = 'Revisión de documentos completada - {$a->vacancytitle}';
$string['notification_review_complete_body'] = 'Estimado/a {$a->fullname},

La revisión de sus documentos para la vacante "{$a->vacancytitle}" ha sido completada.

{$a->summary}

{$a->action_required}

Para ver el estado detallado de su aplicación, visite:
{$a->applicationurl}

Atentamente,
{$a->sitename}';

// Revisión de documentos estilo mod_assign.
$string['documentreview'] = 'Revisión de documentos';
$string['reviewdocuments'] = 'Revisar documentos';
$string['documentstatus'] = 'Estado del documento';
$string['documentobservations'] = 'Observaciones';
$string['requiresreupload'] = 'Requiere nueva carga';
$string['markasinvalid'] = 'Marcar como inválido';
$string['markasvalid'] = 'Marcar como válido';
$string['savereview'] = 'Guardar revisión';
$string['sendnotification'] = 'Enviar notificación al aplicante';
$string['reviewsaved'] = 'Revisión guardada correctamente.';
$string['documentsummary'] = 'Resumen de documentos';
$string['approveddocuments'] = 'Documentos aprobados';
$string['rejecteddocuments'] = 'Documentos rechazados';
$string['pendingdocuments'] = 'Documentos pendientes';

// Exportación ZIP.
$string['exportzip'] = 'Exportar ZIP';
$string['exportzipdesc'] = 'Exportar aplicaciones y documentos en archivo ZIP estructurado por compañía.';
$string['exportinprogress'] = 'Exportación en progreso...';
$string['exportcomplete'] = 'Exportación completada';
$string['zipstructure'] = 'Estructura del archivo';
$string['includeapplicantfolders'] = 'Incluir carpetas por aplicante';
$string['includeapplicationdata'] = 'Incluir datos de aplicación (JSON)';

// Vista pública de convocatoria.
$string['viewconvocatoria'] = 'Ver convocatoria';
$string['convocatoriapublic'] = 'Información de la convocatoria';
$string['convocatoriavacancies'] = 'Vacantes de esta convocatoria';
$string['convocatoriaclosed'] = 'Esta convocatoria está cerrada.';
$string['convocatorianotstarted'] = 'Esta convocatoria aún no ha iniciado.';

// Página apply rediseñada con tabs.
$string['tab_vacancyinfo'] = 'Información de la vacante';
$string['tab_personaldata'] = 'Datos personales';
$string['tab_documents'] = 'Documentos';
$string['tab_review'] = 'Revisar y enviar';
$string['applicationsteps'] = 'Pasos de la aplicación';
$string['step_x_of_y'] = 'Paso {$a->current} de {$a->total}';
$string['nextstep'] = 'Siguiente';
$string['previousstep'] = 'Anterior';
$string['saveandcontinue'] = 'Guardar y continuar';
$string['reviewapplication'] = 'Revisar aplicación';

// Strings para vista pública.
$string['vacanciesavailable'] = 'Hay {$a} vacantes disponibles en esta convocatoria';

// Dashboard strings.
$string['role_administrator'] = 'Administrador';
$string['role_manager'] = 'Gestor';
$string['role_applicant'] = 'Aplicante';
$string['dashboard_manager_welcome'] = 'Gestiona convocatorias, vacantes y revisa aplicaciones.';
$string['dashboard_reviewer_welcome'] = 'Revisa documentos y evalúa candidatos asignados.';
$string['viewpublicpage'] = 'Ver página pública';
$string['overview'] = 'Resumen';
$string['contentmanagement'] = 'Gestión de contenido';
$string['active'] = 'Activas';
$string['draft'] = 'Borrador';
$string['viewall'] = 'Ver todo';
$string['addnew'] = 'Agregar';
$string['pending'] = 'Pendiente';
$string['total'] = 'Total';
$string['reviewall'] = 'Revisar todo';
$string['reportsanddata'] = 'Reportes y datos';
$string['exportdata'] = 'Exportar datos';
$string['exportdata_desc'] = 'Exportar datos de aplicaciones y documentos';
$string['systemconfiguration'] = 'Configuración del sistema';
$string['emailtemplates'] = 'Plantillas de correo';
$string['emailtemplates_desc'] = 'Configurar plantillas de notificación por correo';
$string['exemptions'] = 'Exenciones';
$string['adminonly'] = 'Solo administradores';
$string['reviewoverview'] = 'Resumen de revisiones';
$string['mypendingreviews'] = 'Mis revisiones pendientes';
$string['completedreviews'] = 'Revisiones completadas';
$string['pendingreviews_alert'] = 'Tienes {$a} revisiones pendientes';
$string['allapplications'] = 'Todas las aplicaciones';
$string['allapplications_desc'] = 'Ver y buscar todas las aplicaciones del sistema';
$string['explore'] = 'Explorar';
$string['welcometojobboard'] = 'Bienvenido a la Bolsa de Empleo';
$string['vieweronly_desc'] = 'Actualmente solo tienes acceso para ver las vacantes públicas disponibles.';
$string['viewpublicvacancies'] = 'Ver vacantes públicas';

// Dashboard - Gestión de flujo de trabajo.
$string['workflowmanagement'] = 'Gestión de flujo de trabajo';
$string['assignreviewers_desc'] = 'Asignar revisores a aplicaciones y vacantes';
$string['bulkvalidation_desc'] = 'Validar múltiples documentos a la vez';
$string['committees'] = 'Comités de selección';
$string['committees_desc'] = 'Gestionar miembros del comité de selección';
$string['committees_access_hint'] = 'Acceso desde gestión de vacantes';
$string['apitokens_desc'] = 'Gestionar tokens de acceso API para integraciones externas';

// Vista pública - Accesos rápidos por rol.
$string['sharepage'] = 'Compartir esta página';

// ============================================================================
// Gestión de Roles (v2.2.0)
// ============================================================================
$string['manageroles'] = 'Gestión de Roles';
$string['manageroles_desc'] = 'Asignar usuarios a roles del plugin (Revisor, Coordinador, Comité)';
$string['managecommittees'] = 'Gestionar Comités';
$string['usersassigned'] = '{$a} usuario(s) asignado(s) exitosamente';
$string['userunassigned'] = 'Usuario desasignado exitosamente';
$string['totalassignedusers'] = 'Total de Usuarios Asignados';
$string['selectroletoassign'] = 'Seleccione un rol para gestionar';
$string['usersassignedcount'] = 'usuarios asignados';
$string['capabilities'] = 'Capacidades';
$string['manageusers'] = 'Gestionar Usuarios';
$string['rolenotcreated'] = 'Rol no creado. Por favor actualice el plugin.';
$string['invalidrole'] = 'Rol seleccionado inválido';
$string['backtorolelist'] = 'Volver a la lista de roles';
$string['assignedusers'] = 'Usuarios Asignados';
$string['nousersassigned'] = 'No hay usuarios asignados a este rol aún';
$string['assigned'] = 'Asignado';
$string['unassign'] = 'Desasignar';
$string['confirmunassign'] = '¿Está seguro de que desea desasignar a este usuario de este rol?';
$string['assignnewusers'] = 'Asignar Nuevos Usuarios';
$string['searchusers'] = 'Buscar Usuarios';
$string['searchusersplaceholder'] = 'Escriba para filtrar usuarios...';
$string['selectusers'] = 'Seleccionar Usuarios';
$string['selectmultiplehelp'] = 'Mantenga presionado Ctrl (o Cmd en Mac) para seleccionar múltiples usuarios';
$string['assignselected'] = 'Asignar Seleccionados';
$string['pluginsettings'] = 'Configuración del Plugin';

// Descripciones de capacidades para vista previa de rol.
$string['cap_review'] = 'Revisar';
$string['cap_validate'] = 'Validar Docs';
$string['cap_download'] = 'Descargar';
$string['cap_manage'] = 'Gestionar';
$string['cap_createvacancy'] = 'Crear Vacantes';
$string['cap_assignreviewers'] = 'Asignar Revisores';
$string['cap_viewreports'] = 'Ver Reportes';
$string['cap_evaluate'] = 'Evaluar';
$string['cap_viewevaluations'] = 'Ver Evaluaciones';

// ============================================================================
// Gestión de Comités (v2.2.0)
// ============================================================================
$string['totalcommittees'] = 'Total de Comités';
$string['activecommittees'] = 'Comités Activos';
$string['totalcommmembers'] = 'Total de Miembros';
$string['selectvacancy'] = 'Seleccione una vacante...';
$string['allcommittees'] = 'Todos los Comités';
$string['vacancieswithoutcommittee'] = 'Vacantes Sin Comité';
$string['backtolist'] = 'Volver a la lista';
$string['changerole'] = 'Cambiar Rol';
$string['committeedefaultname'] = 'Comité de Selección para {$a}';
$string['chairhelp'] = 'El presidente lidera el comité de selección y tiene la autoridad de decisión final.';
$string['nosecretaryoptional'] = 'Sin secretario (opcional)';
$string['evaluatorshelp'] = 'Seleccione evaluadores adicionales. Mantenga Ctrl/Cmd para seleccionar varios.';
$string['committeeautoroleassign'] = 'Al crear este comité, todos los miembros recibirán automáticamente el rol "Miembro del Comité de Selección" en el plugin, otorgándoles los permisos necesarios para evaluar candidatos.';
$string['committeecreateerror'] = 'Error al crear el comité. Verifique si ya existe un comité para esta vacante.';
$string['memberadderror'] = 'Error al agregar miembro. El usuario podría ya ser miembro de este comité.';
$string['memberremoveerror'] = 'Error al eliminar miembro. No se puede eliminar el único miembro evaluador.';
$string['rolechangeerror'] = 'Error al cambiar el rol. Por favor intente nuevamente.';
$string['applicantranking'] = 'Clasificación de Aspirantes';
$string['rank'] = 'Posición';
$string['avgscore'] = 'Puntuación Promedio';
$string['votes'] = 'Votos';
$string['selectuser'] = 'Seleccione un usuario...';
