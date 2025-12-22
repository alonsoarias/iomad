# FASE 1: Estructura de Archivos y Base de Datos

## Plugin: local_jobboard
**Version:** 3.7.66 (2025122029)
**Moodle requerido:** 4.1+ (hasta 4.5)

---

## 1. ESTRUCTURA DE ARCHIVOS

### 1.1 Archivos Raiz

| Archivo | Proposito |
|---------|-----------|
| `version.php` | Definicion de version del plugin |
| `lib.php` | Funciones principales: navegacion, pluginfile, helpers |
| `settings.php` | Configuracion de admin settings |
| `index.php` | Punto de entrada principal, router de vistas |
| `public.php` | Pagina publica de convocatorias (sin auth) |
| `signup.php` | Registro de postulantes |
| `confirm.php` | Confirmacion de registro |
| `support.php` | Soporte/ayuda |
| `updateprofile.php` | Actualizacion de perfil de postulante |
| `download_documents.php` | Descarga de documentos |
| `download_text_document.php` | Descarga de documentos de texto |
| `reupload_document.php` | Re-subida de documentos rechazados |
| `ajax_conversion.php` | Conversion de archivos |
| `ajax_reorder_doctypes.php` | Reordenamiento de tipos de documentos |

### 1.2 Directorio `/admin/`

| Archivo | Proposito | **A ELIMINAR** |
|---------|-----------|----------------|
| `assign_reviewer.php` | Asignar revisor a postulacion | NO |
| `bulk_validate.php` | Validacion masiva de documentos | NO |
| `doctypes.php` | Gestion de tipos de documentos | NO |
| `edit.php` | Edicion de vacantes/convocatorias | NO |
| `exemptions.php` | Gestion de exenciones ISER | NO |
| `export_documents.php` | Exportacion de documentos | NO |
| `import_exemptions.php` | Importacion de exenciones | NO |
| `import_vacancies.php` | Importacion de vacantes | NO |
| `manage_applications.php` | Gestion de postulaciones | NO |
| `manage_committee.php` | Gestion de comites de seleccion | **SI - ELIMINAR** |
| `manage_exemptions.php` | Gestion de exenciones | NO |
| `manage_program_reviewers.php` | Gestion revisores por programa | NO |
| `migrate.php` | Migracion de datos | NO |
| `roles.php` | Gestion de roles | NO |
| `schedule_interview.php` | Programacion de entrevistas | NO |
| `templates.php` | Gestion de plantillas de email | NO |
| `validate_document.php` | Validacion individual de documento | NO |

### 1.3 Directorio `/ajax/`

| Archivo | Proposito |
|---------|-----------|
| `filter_public_vacancies.php` | Filtrado de vacantes publicas |
| `get_companies.php` | Obtener companias (IOMAD) |
| `get_convocatorias.php` | Obtener convocatorias |
| `get_departments.php` | Obtener departamentos |

### 1.4 Directorio `/classes/`

| Archivo | Proposito | **A ELIMINAR** |
|---------|-----------|----------------|
| `application.php` | Clase de postulacion | NO (MODIFICAR) |
| `audit.php` | Sistema de auditoria | NO (MEJORAR) |
| `bulk_validator.php` | Validador masivo | NO |
| `committee.php` | Gestion de comite de seleccion | **SI - ELIMINAR** |
| `convocatoria_exemption.php` | Exenciones por convocatoria | NO |
| `data_export.php` | Exportacion de datos | NO |
| `document.php` | Clase de documento | NO |
| `document_services.php` | Servicios de documentos | NO |
| `email_template.php` | Plantillas de email | NO |
| `encryption.php` | Encriptacion | NO |
| `exemption.php` | Exenciones ISER | NO |
| `interview.php` | Entrevistas | NO |
| `notification.php` | Notificaciones | NO |
| `program_reviewer.php` | Revisores por programa | NO |
| `review_notifier.php` | Notificador de revisiones | NO |
| `reviewer.php` | Gestion de revisores | NO (MODIFICAR) |
| `vacancy.php` | Clase de vacante | NO |

### 1.5 Directorio `/templates/pages/review/`

| Template | Proposito | **A ELIMINAR** |
|----------|-----------|----------------|
| `assign_reviewer.mustache` | Vista asignar revisor | NO |
| `committee.mustache` | Vista gestion de comite | **SI - ELIMINAR** |
| `index.mustache` | Vista principal de revision | NO (MODIFICAR) |
| `interview_complete.mustache` | Vista completar entrevista | NO |
| `my_reviews.mustache` | Mis revisiones | NO |
| `program_reviewers.mustache` | Revisores por programa | NO |
| `results.mustache` | Resultados | NO (MODIFICAR) |
| `schedule_interview.mustache` | Programar entrevista | NO |

### 1.6 Directorio `/db/`

| Archivo | Proposito |
|---------|-----------|
| `install.xml` | Definicion de tablas de BD |
| `install.php` | Script de instalacion |
| `upgrade.php` | Scripts de upgrade |
| `access.php` | Capabilities del plugin |
| `messages.php` | Definicion de mensajes |
| `services.php` | Servicios web |
| `tasks.php` | Tareas programadas |
| `caches.php` | Definiciones de cache |
| `uninstall.php` | Script de desinstalacion |

### 1.7 Directorio `/lang/`

| Archivo | Idioma |
|---------|--------|
| `lang/en/local_jobboard.php` | Ingles |
| `lang/es/local_jobboard.php` | Espanol |

---

## 2. ESTRUCTURA DE BASE DE DATOS

### 2.1 Tablas Principales

#### `local_jobboard_convocatoria`
**Proposito:** Convocatorias/campanas de seleccion

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| id | int(10) | PK |
| code | char(50) | Codigo unico |
| name | char(255) | Nombre |
| description | text | Descripcion completa |
| brief_description | text | Descripcion breve |
| pdf_contenthash | char(40) | Hash del PDF |
| pdf_filename | char(255) | Nombre del PDF |
| startdate | int(10) | Fecha inicio |
| enddate | int(10) | Fecha fin |
| status | char(20) | Estado: draft, open, closed, archived |
| companyid | int(10) | FK a company |
| departmentid | int(10) | FK a department |
| publicationtype | char(20) | public/internal |
| terms | text | Terminos y condiciones |
| allow_multiple_applications | int(1) | Permitir multiples postulaciones |
| max_applications_per_user | int(3) | Max postulaciones por usuario |
| createdby | int(10) | FK a user |
| timecreated | int(10) | Timestamp creacion |
| timemodified | int(10) | Timestamp modificacion |

**CAMPOS FALTANTES PARA NUEVO FLUJO:**
- `dean_review_startdate` - Fecha inicio revision Decano
- `dean_review_enddate` - Fecha fin revision Decano
- `hr_review_startdate` - Fecha inicio revision Talento Humano
- `hr_review_enddate` - Fecha fin revision Talento Humano

#### `local_jobboard_vacancy`
**Proposito:** Vacantes de trabajo

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| id | int(10) | PK |
| code | char(50) | Codigo |
| title | char(255) | Titulo |
| description | text | Descripcion |
| contracttype | char(50) | Tipo de contrato |
| duration | char(100) | Duracion |
| location | char(255) | Ubicacion |
| modality | char(100) | Modalidad |
| department | char(255) | Departamento |
| companyid | int(10) | FK a company |
| departmentid | int(10) | FK a department |
| convocatoriaid | int(10) | FK a convocatoria |
| positions | int(5) | Posiciones disponibles |
| requirements | text | Requisitos |
| desirable | text | Deseables |
| status | char(20) | Estado |
| publicationtype | char(20) | Tipo publicacion |
| opendate | int(10) | Fecha apertura |
| closedate | int(10) | Fecha cierre |
| createdby | int(10) | FK a user |
| timecreated | int(10) | Timestamp |

#### `local_jobboard_application`
**Proposito:** Postulaciones

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| id | int(10) | PK |
| vacancyid | int(10) | FK a vacancy |
| userid | int(10) | FK a user (postulante) |
| status | char(30) | Estado de la postulacion |
| statusnotes | text | Notas del estado |
| isexemption | int(1) | Marcador exencion ISER |
| exemptionreason | text | Razon exencion |
| consentgiven | int(1) | Consentimiento |
| consenttimestamp | int(10) | Timestamp consentimiento |
| consentip | char(45) | IP consentimiento |
| consentuseragent | char(512) | User agent |
| digitalsignature | char(255) | Firma digital |
| coverletter | text | Carta de presentacion |
| applicationdata | text | Datos adicionales (JSON) |
| reviewerid | int(10) | FK a user (revisor) |
| timecreated | int(10) | Timestamp |
| timemodified | int(10) | Timestamp modificacion |

**Estados Actuales:**
- `draft` - Borrador
- `submitted` - Enviada
- `under_review` - En revision
- `docs_validated` - Documentos validados
- `docs_rejected` - Documentos rechazados
- `interview` - Entrevista
- `selected` - Seleccionado
- `rejected` - Rechazado
- `withdrawn` - Retirada

**NUEVOS ESTADOS PROPUESTOS:**
- `pending_dean_review` - Pendiente revision Decano
- `dean_approved` - Perfil aprobado por Decano
- `dean_rejected` - Perfil rechazado por Decano
- `pending_hr_validation` - Pendiente validacion TH
- `hr_validated` - Validado por Talento Humano
- `hr_rejected` - Rechazado por Talento Humano

#### `local_jobboard_document`
**Proposito:** Documentos subidos

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| id | int(10) | PK |
| applicationid | int(10) | FK a application |
| documenttype | char(100) | Tipo de documento |
| filename | char(255) | Nombre archivo |
| contenthash | char(40) | Hash del contenido |
| filesize | int(10) | Tamano en bytes |
| mimetype | char(100) | Tipo MIME |
| issuedate | int(10) | Fecha emision |
| expirydate | int(10) | Fecha vencimiento |
| isencrypted | int(1) | Encriptado |
| issuperseded | int(1) | Reemplazado |
| uploadedby | int(10) | FK a user |
| timecreated | int(10) | Timestamp |

#### `local_jobboard_doc_validation`
**Proposito:** Validaciones de documentos

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| id | int(10) | PK |
| documentid | int(10) | FK a document |
| status | char(20) | pending/approved/rejected |
| isvalid | int(1) | Valido (legacy) |
| validatedby | int(10) | FK a user |
| rejectreason | char(100) | Razon rechazo |
| notes | text | Notas |
| checklistitems | text | Items checklist (JSON) |
| timecreated | int(10) | Timestamp |
| timemodified | int(10) | Timestamp |

### 2.2 Tablas de Comite (A ELIMINAR)

#### `local_jobboard_committee` - **ELIMINAR**
**Proposito:** Comites de seleccion

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| id | int(10) | PK |
| facultyid | int(10) | FK a faculty |
| companyid | int(10) | FK a company |
| vacancyid | int(10) | FK a vacancy |
| name | char(255) | Nombre |
| description | text | Descripcion |
| status | char(20) | Estado |
| createdby | int(10) | FK a user |
| timecreated | int(10) | Timestamp |

#### `local_jobboard_committee_member` - **ELIMINAR**
**Proposito:** Miembros de comite

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| id | int(10) | PK |
| committeeid | int(10) | FK a committee |
| userid | int(10) | FK a user |
| role | char(20) | Rol: chair, evaluator, observer, secretary |
| addedby | int(10) | FK a user |
| timecreated | int(10) | Timestamp |

#### `local_jobboard_evaluation` - **ELIMINAR**
**Proposito:** Evaluaciones del comite

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| id | int(10) | PK |
| committeeid | int(10) | FK a committee |
| applicationid | int(10) | FK a application |
| userid | int(10) | FK a user (evaluador) |
| score | int(3) | Puntuacion |
| vote | char(20) | Voto: approve, reject, abstain |
| comments | text | Comentarios |
| criteriaratings | text | Ratings por criterio (JSON) |
| timecreated | int(10) | Timestamp |

#### `local_jobboard_criteria` - **ELIMINAR**
**Proposito:** Criterios de evaluacion

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| id | int(10) | PK |
| vacancyid | int(10) | FK a vacancy |
| name | char(255) | Nombre |
| description | text | Descripcion |
| weight | int(2) | Peso |
| maxscore | int(3) | Puntuacion maxima |
| sortorder | int(4) | Orden |
| timecreated | int(10) | Timestamp |

#### `local_jobboard_decision` - **ELIMINAR**
**Proposito:** Decisiones finales del comite

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| id | int(10) | PK |
| committeeid | int(10) | FK a committee |
| applicationid | int(10) | FK a application |
| decision | char(20) | Seleccionado/Rechazado |
| reason | text | Razon |
| decidedby | int(10) | FK a user |
| timecreated | int(10) | Timestamp |

### 2.3 Tablas de Auditoria

#### `local_jobboard_audit`
**Proposito:** Log de auditoria general

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| id | int(10) | PK |
| userid | int(10) | FK a user |
| action | char(100) | Accion realizada |
| entitytype | char(50) | Tipo de entidad |
| entityid | int(10) | ID de entidad |
| ipaddress | char(45) | Direccion IP |
| useragent | char(512) | User agent |
| extradata | text | Datos adicionales (JSON) |
| previousvalue | text | Valor anterior (JSON) |
| newvalue | text | Valor nuevo (JSON) |
| timecreated | int(10) | Timestamp |

**CAMPOS FALTANTES PARA REQUERIMIENTO:**
- `role` - Rol del usuario al momento de la accion
- `targettype` - Tipo de objeto afectado (redundante con entitytype)
- `details` - JSON con informacion adicional (similar a extradata)

#### `local_jobboard_workflow_log`
**Proposito:** Log de cambios de estado de workflow

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| id | int(10) | PK |
| applicationid | int(10) | FK a application |
| previousstatus | char(30) | Estado anterior |
| newstatus | char(30) | Nuevo estado |
| changedby | int(10) | FK a user |
| comments | text | Comentarios |
| notificationsent | int(1) | Notificacion enviada |
| timecreated | int(10) | Timestamp |

### 2.4 Otras Tablas Relevantes

#### `local_jobboard_doctype`
**Proposito:** Tipos de documentos predefinidos

#### `local_jobboard_exemption`
**Proposito:** Exenciones ISER historicas

#### `local_jobboard_notification`
**Proposito:** Cola de notificaciones

#### `local_jobboard_email_template`
**Proposito:** Plantillas de email

#### `local_jobboard_interview`
**Proposito:** Entrevistas programadas

#### `local_jobboard_program_reviewer`
**Proposito:** Revisores asignados por programa academico

---

## 3. RELACIONES ENTRE TABLAS

```
convocatoria (1) ──────── (N) vacancy
     │
     └── conv_docexempt (N)

vacancy (1) ──────── (N) application
     │                      │
     │                      ├── (N) document ── (1) doc_validation
     │                      │
     │                      └── (N) workflow_log
     │
     └── (N) doc_requirement

committee (1) ──────── (N) committee_member  [A ELIMINAR]
     │
     ├── (N) evaluation                       [A ELIMINAR]
     │
     └── (N) decision                         [A ELIMINAR]
```

---

## 4. RESUMEN DE ELEMENTOS A ELIMINAR

### 4.1 Tablas a Eliminar
1. `local_jobboard_committee`
2. `local_jobboard_committee_member`
3. `local_jobboard_evaluation`
4. `local_jobboard_criteria`
5. `local_jobboard_decision`

### 4.2 Archivos a Eliminar
1. `admin/manage_committee.php`
2. `classes/committee.php`
3. `templates/pages/review/committee.mustache`

### 4.3 Campos a Agregar (convocatoria)
1. `dean_review_startdate` - int(10)
2. `dean_review_enddate` - int(10)
3. `hr_review_startdate` - int(10)
4. `hr_review_enddate` - int(10)

### 4.4 Campos a Agregar (audit)
1. `role` - char(50) - Rol del usuario al momento de la accion

---

## 5. PROXIMOS PASOS

La **Fase 2** analizara:
- Capabilities actuales y su mapeo a roles
- Roles existentes en el sistema
- Propuesta de nuevos capabilities para Decano y Talento Humano

---

*Documento generado: 2025-12-22*
*Fase 1 de 5 completada*
