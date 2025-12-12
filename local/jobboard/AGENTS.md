# AGENTS.md - local_jobboard

**Documento consolidado de requerimientos del proyecto**

Plugin de Moodle para gestión de vacantes académicas y postulaciones docentes.
Sistema de Bolsa de Empleo para reclutamiento de profesores de cátedra.

**Fecha de actualización:** 2025-12-12
**Institución:** ISER (Instituto Superior de Educación Rural)
**Autor:** Alonso Arias <soporteplataformas@iser.edu.co>
**Supervisión:** Vicerrectoría Académica ISER

---

## 1. INFORMACION GENERAL DEL PROYECTO

| Campo | Valor |
|-------|-------|
| **Componente** | `local_jobboard` |
| **Version actual** | **3.3.0** (2025121250) |
| **Tipo** | Plugin local de Moodle |
| **Moodle requerido** | 4.1+ (2022112800) |
| **Moodle soportado** | 4.1 - 4.5 |
| **PHP requerido** | 7.4+ (recomendado 8.1+) |
| **Madurez** | MATURITY_STABLE |
| **Licencia** | GNU GPL v3 or later |

### 1.1 Estado de Implementacion v3.3.0

| Componente | Estado | Observaciones |
|------------|:------:|---------------|
| Base de datos (28 tablas) | ✅ | Completo con upgrade.php |
| Capabilities (31) | ✅ | Definidas en access.php |
| Roles (3 personalizados) | ✅ | jobboard_reviewer, jobboard_coordinator, jobboard_committee |
| Sistema CSS `jb-*` | ✅ 100% | Migrados en v3.2.3, Grading panel CSS v3.3.0 |
| Templates Mustache (116) | ✅ | Usando clases `jb-*`, +grading_panel.mustache v3.3.0 |
| Renderers especializados (11) | ✅ | renderer_base, dashboard_renderer, admin_renderer, etc. |
| AMD Modules (13) | ✅ | +grading_panel.js v3.3.0 con AJAX nav y atajos teclado |
| User Tours | ⚠️ | Definidos, pendiente actualizar selectores |
| Strings de idioma | ✅ | 2,754 lineas EN/ES cada uno (+43 grading panel) |
| Email templates | ✅ | 6 plantillas predefinidas |
| Web Services | ✅ Eliminados | Removidos en v3.2.2 |
| Privacy API | ⚠️ | Parcialmente implementada |
| Interfaz revision mod_assign | ✅ | Implementada v3.3.0: split-pane, PDF preview, AJAX, shortcuts |
| Reportes filtrados por convocatoria | ⚠️ | Parcialmente implementado |

---

## 2. ARQUITECTURA IOMAD

### 2.1 Estructura Organizacional Multi-tenant (4 Niveles)

```
NIVEL 1: INSTANCIA IOMAD
         virtual.iser.edu.co
              |
NIVEL 2: COMPANIES (16 Centros Tutoriales)
         +-- Pamplona (Sede Principal)
         +-- Cucuta
         +-- Tibu
         +-- Ocana
         +-- Toledo
         +-- El Tarra
         +-- Sardinata
         +-- San Vicente del Chucuri
         +-- Pueblo Bello
         +-- San Pablo
         +-- Santa Rosa del Sur
         +-- Fundacion
         +-- Cimitarra
         +-- Salazar de las Palmas
         +-- Tame
         +-- Saravena
              |
NIVEL 3: DEPARTMENTS (Modalidades por Centro)
         +-- Presencial (PRE)
         +-- A Distancia (DIS)
         +-- Virtual (VIR)
         +-- Hibrida (HIB)
              |
NIVEL 4: SUB-DEPARTMENTS (Facultades por Modalidad)
         +-- Facultad de Ciencias Administrativas y Sociales
         +-- Facultad de Ingenierias e Informatica
```

### 2.2 Estructura Academica

**Facultad de Ciencias Administrativas y Sociales (FCAS):**
- Tecnologia en Gestion Empresarial
- Tecnologia en Gestion Comunitaria
- Tecnologia en Gestion de Mercadeo
- Tecnica Profesional en Seguridad y Salud en el Trabajo

**Facultad de Ingenierias e Informatica (FII):**
- Tecnologia Agropecuaria
- Tecnologia en Procesos Agroindustriales
- Tecnologia en Gestion Industrial
- Tecnologia en Gestion de Redes y Sistemas Teleinformaticos
- Tecnologia en Gestion y Construccion de Obras Civiles
- Tecnica Profesional en Produccion de Frutas y Hortalizas

---

## 3. REGLAS DE NEGOCIO FUNDAMENTALES

### 3.1 Organizacion por Facultad y Programa

| Elemento | Nivel de Organizacion | Nota |
|----------|----------------------|------|
| Vacantes | Por FACULTAD | Separadas y filtradas por facultad |
| Comite de Seleccion | Por FACULTAD | NO por vacante - cada facultad tiene su comite |
| Revisores de Documentos | Por PROGRAMA | Asignados a nivel de programa academico |
| Convocatorias | Globales | Con activacion de excepciones por convocatoria |

### 3.2 Convocatorias (CRITICO)

| Regla | Descripcion |
|-------|-------------|
| PDF adjunto OBLIGATORIO | Toda convocatoria debe tener PDF con detalle completo |
| Fechas centralizadas | Las fechas de apertura/cierre se gestionan SOLO desde la convocatoria |
| Descripcion breve | Campo de texto para resumen |
| Terminos y condiciones | HTML con condiciones legales |
| Boton acceso PDF | Visible en la vista de la convocatoria |

### 3.3 Vacantes (CRITICO)

| Regla | Descripcion |
|-------|-------------|
| SIN fechas propias | Las vacantes NO tienen fecha de apertura/cierre - heredan de convocatoria |
| Sin vacante extemporanea | Esta opcion debe estar eliminada |
| Organizacion | Por facultad academica |

### 3.4 Postulaciones (CRITICO)

| Regla | Descripcion |
|-------|-------------|
| Limite estricto | Un postulante solo puede aplicar a UNA vacante por convocatoria |
| Experiencia ocasional | Docentes ocasionales requieren 2 anos de experiencia laboral equivalente |
| Carta de intencion | Es campo de TEXTO redactado en formulario, NO un archivo |

### 3.5 Validacion de Documentos (CRITICO)

| Regla | Descripcion |
|-------|-------------|
| 100% MANUAL | NO existe verificacion automatica en background |
| Checklist por tipo | Cada tipo de documento tiene su checklist de verificacion |
| Recarga permitida | Documentos rechazados pueden recargarse con observaciones |
| Razones de rechazo | illegible, expired, incomplete, wrongtype, mismatch |

### 3.6 Excepciones de Documentos (CRITICO)

| Regla | Descripcion |
|-------|-------------|
| GLOBALES | Se definen en administracion, NO por usuario individual |
| Activacion por convocatoria | Cada convocatoria puede activar/desactivar excepciones |
| Por edad | Personas >=50 anos exentas de libreta militar |
| Por genero | Libreta militar solo para hombres |

**Tipos de Excepciones:**
- `historico_iser` - Profesores previamente vinculados con ISER (eximidos de: titulos, cedula, tarjeta profesional, libreta militar, certificaciones laborales que ya reposen en Historia Laboral)
- `documentos_recientes` - Documentos recientes validos
- `traslado_interno` - Traslado interno
- `recontratacion` - Recontratacion

### 3.7 Reglas Especiales de Libreta Militar

| Condicion | Requisito |
|-----------|-----------|
| Hombres 18-28 anos | Libreta militar obligatoria |
| Hombres declarados NO aptos | Certificado provisional de tramite |
| Hombres exentos | Certificado provisional de tramite |
| Hombres >28 anos (superaron edad de incorporacion) | Certificado provisional de tramite |
| Mujeres | No aplica |

---

## 4. FORMULARIO DE POSTULACION PERSONALIZABLE

### 4.1 Arquitectura de Dos Niveles

El sistema de documentos funciona en **DOS NIVELES**:

| Nivel | Descripcion | Quien configura |
|-------|-------------|-----------------|
| **CATALOGO GLOBAL** | Tipos de documento disponibles en el sistema | Administrador del sistema |
| **POR CONVOCATORIA** | Documentos requeridos para esa convocatoria especifica | Administrador/Coordinador al crear convocatoria |

**Principio:** El catalogo global define QUE documentos PUEDEN existir. Cada convocatoria define QUE documentos SE REQUIEREN.

### 4.2 Nivel 1: Catalogo Global de Tipos de Documento

El administrador gestiona un **catalogo maestro** de tipos de documento desde `admin/doctypes.php`:

| Atributo | Descripcion | Ejemplo |
|----------|-------------|---------|
| **code** | Codigo unico identificador | `hoja_vida_sigep` |
| **name** | Nombre del documento | `Formato Unico Hoja de Vida SIGEP II` |
| **type** | Tipo de campo | `file` (archivo) o `text` (texto) |
| **input_type** | Subtipo de entrada | file, text, textarea, select |
| **description** | Descripcion general | Texto explicativo |
| **externalurl** | URL donde obtener el documento | `https://www.sigep.gov.co` |
| **acceptedformats** | Formatos aceptados | `pdf,jpg,png` |
| **maxsize** | Tamano maximo (bytes) | 5242880 (5MB) |
| **checklistitems** | Items de verificacion por defecto (JSON) | `["Legible","Firmado"]` |
| **gender_restricted** | Restriccion por genero | `M`, `F`, o `null` |
| **iserexempted** | Puede eximirse por historial ISER | `1` = si, `0` = no |
| **enabled** | Disponible para usar en convocatorias | `1` = si, `0` = no |

### 4.3 Nivel 2: Configuracion por Convocatoria

**Tabla:** `local_jobboard_convocatoria_doctype`

| Campo | Descripcion |
|-------|-------------|
| `id` | ID unico |
| `convocatoriaid` | FK a la convocatoria |
| `doctypeid` | FK al tipo de documento del catalogo |
| `required` | `1` = obligatorio, `0` = opcional para ESTA convocatoria |
| `sortorder` | Orden de aparicion en el formulario de ESTA convocatoria |
| `instructions` | Instrucciones especificas para ESTA convocatoria |
| `requirements` | Requisitos especificos para ESTA convocatoria |
| `maxagedays` | Antiguedad maxima en dias (ej: 30 para certificados) |
| `customchecklistitems` | Checklist personalizado para ESTA convocatoria (JSON) |
| `enabled` | Activo/inactivo en ESTA convocatoria |

### 4.4 Estructura del Formulario con Pestanas

| Pestana | Contenido |
|---------|-----------|
| 1. Informacion Personal | Datos basicos del postulante |
| 2. Formacion Academica | Titulos, certificaciones |
| 3. Experiencia Laboral | Historial laboral |
| 4. Documentos | Carga de archivos |
| 5. Carta de Intencion | Campo de TEXTO (NO archivo) |
| 6. Revision y Envio | Resumen y consentimientos |

### 4.5 Documentos Requeridos (18 documentos - Lista Oficial ISER)

| # | Codigo | Documento | Requisitos | Exempto ISER |
|---|--------|-----------|------------|--------------|
| 1 | sigep | Formato Unico Hoja de Vida SIGEP II | Todos campos diligenciados, experiencia conforme certificaciones, firmado | No |
| 2 | bienes_rentas | Declaracion de Bienes y Rentas | Informacion vigencia anterior, firmado | No |
| 3 | cedula | Cedula de Ciudadania | Copia en una sola pagina, legible | **Si** |
| 4 | titulo_academico | Titulos Academicos | Legibles, con folio/registro/fecha. Extranjeros: diploma + acta + convalidacion MEN | **Si** |
| 5 | tarjeta_profesional | Tarjeta Profesional | Legible, vigente. No aplica licenciados | **Si** |
| 6 | libreta_militar | Libreta Militar | Solo hombres. No aptos/exentos: certificado provisional | **Si** |
| 7 | formacion_complementaria | Certificados Formacion Complementaria | Legibles, completos | No |
| 8 | certificacion_laboral | Certificaciones Laborales | SOLO certificados (NO contratos, actas, nombramientos) | **Si** |
| 9 | rut | RUT | Verificar fecha actualizacion (inferior derecha) | No |
| 10 | eps | Certificado EPS | Expedicion <=30 dias, estado activo | No |
| 11 | pension | Certificado Pension | Expedicion <=30 dias. Pensionados: resolucion. Magisterio: RUAF | No |
| 12 | cuenta_bancaria | Certificado Cuenta Bancaria | Numero, tipo, entidad, a nombre del postulante | No |
| 13 | antecedentes_disciplinarios | Antecedentes Disciplinarios | Procuraduria General. Expedicion reciente | No |
| 14 | antecedentes_fiscales | Antecedentes Fiscales | Contraloria General. Expedicion reciente | No |
| 15 | antecedentes_judiciales | Antecedentes Judiciales | Policia Nacional. URL: antecedentes.policia.gov.co | No |
| 16 | medidas_correctivas | Registro Nacional Medidas Correctivas | URL: srvcnpc.policia.gov.co | No |
| 17 | inhabilidades | Consulta Inhabilidades (Ley 1918/2018) | Delitos sexuales menores. URL: inhabilidades.policia.gov.co | No |
| 18 | redam | REDAM | Deudores Alimentarios. URL: carpetaciudadana.and.gov.co | No |

### 4.6 URLs de Descarga Pre-configuradas

| Documento | URL |
|-----------|-----|
| SIGEP II | https://www.sigep.gov.co |
| Antecedentes Disciplinarios | https://www.procuraduria.gov.co |
| Antecedentes Fiscales | https://www.contraloria.gov.co |
| Antecedentes Judiciales | https://antecedentes.policia.gov.co:7005/WebJudicial |
| Medidas Correctivas | https://srvcnpc.policia.gov.co/PSC/frm_cnp_consulta.aspx |
| Inhabilidades Ley 1918 | https://inhabilidades.policia.gov.co:8080/ |
| REDAM | https://carpetaciudadana.and.gov.co/inicio-de-sesion |

### 4.7 Excepciones para Personal ISER Historico

Profesores previamente vinculados con ISER **NO deben presentar** documentos que ya reposan en su Historia Laboral:

| Documento | Codigo | Razon |
|-----------|--------|-------|
| Cedula de Ciudadania | cedula | Ya registrada en ISER |
| Titulos Academicos | titulo_academico | Ya registrados en ISER |
| Tarjeta Profesional | tarjeta_profesional | Ya registrada en ISER |
| Libreta Militar | libreta_militar | Ya registrada en ISER |
| Certificaciones Laborales ISER | certificacion_laboral | Ya en Historia Laboral |

**Campo en doctype:** `iserexempted = 1`

### 4.8 Condiciones Especiales por Documento

| Documento | Condicion | Campo |
|-----------|-----------|-------|
| Libreta Militar | Solo hombres | `gender_condition = 'M'` |
| Tarjeta Profesional | No aplica a licenciados | `profession_exempt = ['licenciatura']` |
| EPS, Pension, Antecedentes | Maximo 30 dias antiguedad | `defaultmaxagedays = 30` |

### 4.9 Estado de Configuracion: IMPLEMENTADO

Los 18 documentos estan correctamente configurados en `db/install.php` con:
- Checklist de verificacion para cada tipo
- URLs externas para descarga
- Condiciones de genero y profesion
- Excepciones para personal ISER

---

## 5. ROLES Y CAPABILITIES

### 5.1 Roles Personalizados del Plugin

| Rol | Shortname | Archetype | Alcance |
|-----|-----------|-----------|---------|
| Revisor de Documentos | `jobboard_reviewer` | teacher | Por PROGRAMA |
| Coordinador de Seleccion | `jobboard_coordinator` | editingteacher | Sistema |
| Miembro de Comite | `jobboard_committee` | teacher | Por FACULTAD |

### 5.2 Capabilities Implementadas (31 total)

#### Acceso Basico
```
local/jobboard:view              - Ver el plugin
local/jobboard:viewinternal      - Ver vacantes internas
local/jobboard:viewpublicvacancies - Ver vacantes publicas
```

#### Postulante
```
local/jobboard:apply             - Postularse a vacantes
local/jobboard:viewownapplications - Ver postulaciones propias
```

#### Gestion Vacantes
```
local/jobboard:manage            - Gestionar job board
local/jobboard:createvacancy     - Crear vacantes
local/jobboard:editvacancy       - Editar vacantes
local/jobboard:deletevacancy     - Eliminar vacantes
local/jobboard:publishvacancy    - Publicar vacantes
local/jobboard:viewallvacancies  - Ver todas las vacantes
```

#### Convocatorias
```
local/jobboard:manageconvocatorias - Gestionar convocatorias
```

#### Revision
```
local/jobboard:review            - Revisar documentos
local/jobboard:validatedocuments - Validar documentos
local/jobboard:reviewdocuments   - Revisar documentos
local/jobboard:assignreviewers   - Asignar revisores
local/jobboard:downloadanydocument - Descargar documentos
```

#### Evaluacion
```
local/jobboard:evaluate          - Evaluar candidatos
local/jobboard:viewevaluations   - Ver evaluaciones
```

#### Postulaciones
```
local/jobboard:viewallapplications - Ver todas postulaciones
local/jobboard:changeapplicationstatus - Cambiar estado
```

#### Workflow y Reportes
```
local/jobboard:manageworkflow    - Gestionar workflow
local/jobboard:viewreports       - Ver reportes
local/jobboard:exportreports     - Exportar reportes
local/jobboard:exportdata        - Exportar datos
```

#### Administracion
```
local/jobboard:configure         - Configurar plugin
local/jobboard:managedoctypes    - Gestionar tipos documento
local/jobboard:manageemailtemplates - Gestionar plantillas
local/jobboard:manageexemptions  - Gestionar excepciones
```

### 5.3 Matriz de Permisos

| Capability | Postulante | Revisor | Coordinador | Comite | Admin |
|------------|:----------:|:-------:|:-----------:|:------:|:-----:|
| view | X | X | X | X | X |
| apply | X | - | - | - | X |
| viewownapplications | X | - | - | - | X |
| reviewdocuments | - | X | X | - | X |
| validatedocuments | - | X | X | - | X |
| createvacancy | - | - | X | - | X |
| editvacancy | - | - | X | - | X |
| deletevacancy | - | - | - | - | X |
| publishvacancy | - | - | X | - | X |
| manageconvocatorias | - | - | - | - | X |
| evaluate | - | - | - | X | X |
| viewevaluations | - | - | X | X | X |
| viewreports | - | - | X | - | X |
| assignreviewers | - | - | X | - | X |
| configure | - | - | - | - | X |
| viewallapplications | - | - | X | - | X |

---

## 6. BASE DE DATOS (28 tablas)

### 6.1 Tablas Core

| Tabla | Descripcion |
|-------|-------------|
| local_jobboard_convocatoria | Convocatorias con PDF adjunto |
| local_jobboard_vacancy | Vacantes por facultad |
| local_jobboard_application | Postulaciones |
| local_jobboard_document | Documentos subidos |

### 6.2 Tablas de Configuracion

| Tabla | Descripcion |
|-------|-------------|
| local_jobboard_doctype | Tipos documento CONFIGURABLES |
| local_jobboard_vacancy_field | Campos personalizados vacante |
| local_jobboard_config | Configuracion plugin |

### 6.3 Tablas de Validacion

| Tabla | Descripcion |
|-------|-------------|
| local_jobboard_doc_validation | Validaciones con checklist |
| local_jobboard_doc_requirement | Requisitos por vacante |

### 6.4 Tablas Organizacionales

| Tabla | Descripcion |
|-------|-------------|
| local_jobboard_faculty | Facultades academicas |
| local_jobboard_program | Programas por facultad |
| local_jobboard_committee | Comites por facultad |
| local_jobboard_committee_member | Miembros comite |

### 6.5 Tablas de Usuarios

| Tabla | Descripcion |
|-------|-------------|
| local_jobboard_applicant_profile | Perfiles postulantes |
| local_jobboard_consent | Consentimientos Habeas Data |
| local_jobboard_exemption | Excepciones globales |
| local_jobboard_conv_docexempt | Excepciones por convocatoria |
| local_jobboard_program_reviewer | Revisores por programa |

### 6.6 Tablas de Workflow

| Tabla | Descripcion |
|-------|-------------|
| local_jobboard_workflow_log | Log transiciones estado |
| local_jobboard_audit | Auditoria completa |
| local_jobboard_notification | Cola notificaciones |

### 6.7 Tablas de Email

| Tabla | Descripcion |
|-------|-------------|
| local_jobboard_email_template | Plantillas email |
| local_jobboard_email_strings | Strings por idioma |

### 6.8 Tablas de Entrevistas y Evaluacion

| Tabla | Descripcion |
|-------|-------------|
| local_jobboard_interview | Entrevistas programadas |
| local_jobboard_interviewer | Entrevistadores |
| local_jobboard_evaluation | Evaluaciones comite |
| local_jobboard_criteria | Criterios evaluacion |
| local_jobboard_decision | Decisiones finales |

---

## 7. ESTRUCTURA DE ARCHIVOS ACTUAL

### 7.1 Archivos Raiz

| Archivo | Descripcion |
|---------|-------------|
| index.php | Router centralizado |
| lib.php | Funciones principales, navegacion |
| settings.php | Configuracion admin |
| version.php | Version 3.2.2 |
| styles.css | Sistema CSS con prefijo jb-* |
| bulk_validate.php | Validacion masiva |
| assign_reviewer.php | Asignacion de revisores |
| signup.php | Registro personalizado IOMAD |
| updateprofile.php | Actualizacion de perfil |

### 7.2 Vistas (views/ - 17 archivos)

| Vista | Proposito | Capability |
|-------|-----------|------------|
| dashboard.php | Dashboard adaptativo por rol | view |
| browse_convocatorias.php | Listado publico convocatorias | viewpublicvacancies |
| convocatorias.php | Gestion de convocatorias | manageconvocatorias |
| convocatoria.php | Crear/editar convocatoria | manageconvocatorias |
| view_convocatoria.php | Detalle de convocatoria | view |
| vacancies.php | Listado de vacantes | viewallvacancies |
| vacancy.php | Detalle de vacante | view |
| apply.php | Formulario de postulacion | apply |
| applications.php | Listado de postulaciones | viewallapplications |
| application.php | Detalle de postulacion | viewownapplications |
| manage.php | Panel de gestion | manage |
| review.php | Revision de documentos | review |
| myreviews.php | Mis revisiones pendientes | review |
| reports.php | Reportes y estadisticas | viewreports |
| public.php | Landing page publica | ninguna |
| public_convocatoria.php | Convocatoria publica | ninguna |
| public_vacancy.php | Vacante publica | ninguna |

### 7.3 Clases Principales (classes/ - 17 clases)

| Clase | Proposito | Lineas |
|-------|-----------|--------|
| application.php | CRUD postulaciones, transiciones | 780 |
| audit.php | Sistema de auditoria | 420 |
| bulk_validator.php | Validacion masiva | 368 |
| committee.php | Comites de seleccion | 735 |
| convocatoria_exemption.php | Excepciones por convocatoria | 443 |
| data_export.php | Exportacion GDPR | 323 |
| document.php | Gestion de documentos | 832 |
| document_services.php | Servicios PDF | 374 |
| email_template.php | Plantillas email | 1,171 |
| encryption.php | Encriptacion AES-256-GCM | 339 |
| exemption.php | Excepciones ISER | 587 |
| interview.php | Programacion entrevistas | 675 |
| notification.php | Cola notificaciones | 317 |
| program_reviewer.php | Revisores por programa | 516 |
| review_notifier.php | Notificaciones revision | 284 |
| reviewer.php | Asignacion revisores | 388 |
| vacancy.php | CRUD vacantes | 1,089 |

### 7.4 Renderers (classes/output/ - 11 archivos)

| Renderer | Proposito | Lineas |
|----------|-----------|--------|
| renderer.php | Principal delegador | 5,796 |
| renderer_base.php | Clase base compartida | 366 |
| admin_renderer.php | Dashboard admin | 698 |
| application_renderer.php | Postulaciones | 333 |
| convocatoria_renderer.php | Convocatorias | 323 |
| dashboard_renderer.php | Dashboards por rol | 500 |
| public_renderer.php | Vista publica | 533 |
| reports_renderer.php | Reportes | 619 |
| review_renderer.php | Revision documentos | 314 |
| vacancy_renderer.php | Vacantes | 271 |
| ui_helper.php | Utilidades HTML | 663 |

### 7.5 Modulos AMD (amd/src/ - 12 modulos)

| Modulo | Proposito |
|--------|-----------|
| application_confirm.js | Confirmacion envio |
| apply_progress.js | Navegacion multi-paso |
| bulk_actions.js | Operaciones masivas |
| card_actions.js | Interacciones cards |
| convocatoria_form.js | Carga AJAX departamentos |
| document_preview.js | Preview documentos |
| exemption_form.js | Seleccion tipos documento |
| loading_states.js | Estados de carga |
| progress_steps.js | Indicador progreso |
| public_filters.js | Filtrado AJAX publico |
| signup_form.js | Cascada company/department |
| vacancy_form.js | Cascada vacante |

### 7.6 Templates (templates/ - 115 archivos)

| Directorio | Cantidad | Descripcion |
|------------|----------|-------------|
| Raiz | 67 | Plantillas generales |
| components/ | 17 | Componentes reutilizables |
| pages/ | 26 | Layouts de pagina |
| reports/ | 5 | Plantillas de reportes |

---

## 8. SISTEMA CSS (prefijo jb-*)

### 8.1 Estado Actual: 95.8% Compliant

**Implementado:**
- Variables CSS (`:root`) para colores, espaciado, tipografia
- Sistema de grid (`jb-row`, `jb-col-*`)
- Cards (`jb-card`, `jb-card-header`, `jb-card-body`, `jb-card-footer`)
- Botones (`jb-btn`, `jb-btn-primary`, `jb-btn-secondary`, etc.)
- Badges (`jb-badge`, `jb-badge-*`)
- Tablas (`jb-table`, `jb-table-hover`, `jb-thead-light`)
- Utilidades de espaciado (`jb-m-*`, `jb-p-*`)
- Utilidades de flexbox (`jb-d-flex`, `jb-justify-content-*`)
- Tipografia (`jb-fs-*`, `jb-fw-*`, `jb-text-*`)

### 8.2 Variables CSS Definidas

```css
:root {
    --jb-primary: #0d6efd;
    --jb-secondary: #6c757d;
    --jb-success: #198754;
    --jb-danger: #dc3545;
    --jb-warning: #ffc107;
    --jb-info: #0dcaf0;
    --jb-spacer-1 a --jb-spacer-5;
    --jb-border-radius: 0.375rem;
    --jb-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
```

### 8.3 Politica de Estilos

**REGLA FUNDAMENTAL:** El plugin NO debe usar clases Bootstrap directamente. Debe tener sistema propio con prefijo `jb-*` para independencia grafica.

### 8.4 Clases Bootstrap a ELIMINAR

| Categoria | Clases a Eliminar |
|-----------|-------------------|
| Layout | row, col-*, mb-*, mt-*, p-* |
| Cards | card, card-header, card-body, card-footer |
| Botones | btn, btn-primary, btn-secondary, btn-* |
| Tablas | table, table-hover, table-responsive |
| Badges | badge, badge-* |
| Alertas | alert, alert-* |
| Formularios | form-control, form-group, input-group |
| Texto | text-muted, text-primary, font-weight-* |
| Utilidades | d-flex, d-none, justify-content-*, align-items-* |

### 8.5 Reemplazo con Clases jb-*

| Bootstrap | Reemplazo |
|-----------|-----------|
| .card | .jb-card |
| .btn-primary | .jb-btn-primary |
| .table | .jb-table |
| .badge | .jb-badge |
| .alert | .jb-alert |
| .form-control | .jb-form-control |
| .d-flex | .jb-d-flex |
| .row | .jb-row |
| .col-* | .jb-col-* |

---

## 9. PLANTILLAS DE EMAIL

### 9.1 Plantillas Requeridas

| Template Key | Descripcion |
|--------------|-------------|
| application_received | Confirmacion postulacion |
| application_status_changed | Cambio de estado |
| review_complete | Revision completada (email consolidado) |
| document_approved | Documento aprobado |
| document_rejected | Documento rechazado |
| interview_scheduled | Citacion entrevista |
| selected | Notificacion seleccion |
| rejected | Notificacion no seleccion |
| vacancy_closing_soon | Vacante proxima a cerrar |

### 9.2 Variables/Placeholders

```
{USER_FIRSTNAME}
{USER_LASTNAME}
{USER_EMAIL}
{VACANCY_TITLE}
{VACANCY_CODE}
{CONVOCATORIA_NAME}
{APPLICATION_DATE}
{APPLICATION_STATUS}
{DOCUMENT_NAME}
{REJECTION_REASON}
{INTERVIEW_DATE}
{INTERVIEW_LOCATION}
{SITE_NAME}
{SITE_URL}
```

### 9.3 Funcionalidades

- **Editor con variables:** Autocompletado de placeholders
- **Preview en tiempo real:** Ver como quedara el email
- **Historial de cambios:** Versiones anteriores de plantillas

---

## 10. SISTEMA DE AUDITORIA

### 10.1 Acciones a Registrar

| Componente | Acciones |
|------------|----------|
| Convocatoria | create, update, delete, publish, close, archive |
| Vacante | create, update, delete, publish, close |
| Postulacion | create, submit, transition, withdraw |
| Documento | upload, download, approve, reject, request_correction |
| Comite | create, update, add_member, remove_member |
| Revisor | assign, revoke |
| Configuracion | update_doctype, update_exemption, update_template |
| Email | sent |

### 10.2 Datos a Registrar

| Campo | Descripcion |
|-------|-------------|
| userid | Usuario que realizo accion |
| action | Accion realizada |
| entitytype | Tipo de entidad |
| entityid | ID de la entidad |
| previousvalue | Valor anterior (JSON) |
| newvalue | Valor nuevo (JSON) |
| extradata | Datos adicionales (JSON) |
| ipaddress | Direccion IP |
| useragent | User agent |
| timecreated | Timestamp |

---

## 11. INTERFAZ DE REVISION DE DOCUMENTOS

### 11.1 Diseno Estilo mod_assign (PENDIENTE)

| Caracteristica | Descripcion |
|----------------|-------------|
| Layout dividido | Panel izquierdo (documentos), derecho (informacion) |
| Visor inline | PDF e imagenes sin descargar |
| Navegacion AJAX | Cambiar documentos/postulantes sin recarga |
| Checklist | Configurable por tipo de documento |
| Atajos teclado | A=Aprobar, R=Rechazar, N=Siguiente, P=Anterior |

### 11.2 Flujo de Revision

1. Revisor abre panel
2. Sistema muestra postulantes de SUS programas
3. Selecciona postulante -> ve documentos
4. Revisa cada documento con checklist
5. Aprueba o rechaza (observacion obligatoria si rechaza)
6. Al finalizar TODOS -> email consolidado

---

## 12. REPORTES

### 12.1 Filtro Obligatorio por Convocatoria

**TODOS** los reportes DEBEN estar filtrados por convocatoria. El usuario debe seleccionar convocatoria antes de ver cualquier reporte.

### 12.2 Reportes Requeridos

| Reporte | Descripcion |
|---------|-------------|
| Postulaciones | Lista con estado |
| Documentos | Estado por postulante |
| Revisores | Carga de trabajo |
| Evaluaciones | Puntuaciones comite |
| Auditoria | Log de acciones |
| Estadisticas | Metricas generales |

---

## 13. CLI Y AUTOMATIZACION

### 13.1 Script CLI (cli.php)

```bash
# Mostrar ayuda
php local/jobboard/cli/cli.php --help

# Listar centros tutoriales
php local/jobboard/cli/cli.php --action=list-centers

# Listar modalidades
php local/jobboard/cli/cli.php --action=list-modalities

# Crear vacantes desde perfiles profesionales
php local/jobboard/cli/cli.php --action=create-vacancies --convocatoria-id=5

# Simulacion (dry-run)
php local/jobboard/cli/cli.php --action=create-vacancies --convocatoria-id=5 --dry-run
```

---

## 14. VALIDACION PRE-IMPLEMENTACION (CRITICO)

Antes de implementar CUALQUIER cambio, validar que no generara errores.

### 14.1 Errores Comunes a Evitar

| Error | Causa | Prevencion |
|-------|-------|------------|
| Unknown column 'X' | Columna no existe en BD | Verificar install.xml antes de usar |
| Table 'X' doesn't exist | Tabla no creada | Verificar install.xml |
| Duplicate entry | Registro duplicado | Verificar condiciones INSERT |
| Call to undefined method | Metodo no existe | Verificar clase antes de llamar |

### 14.2 Protocolo Obligatorio

**PASO 1: Verificar Esquema BD**
1. Verificar campo existe en db/install.xml
2. Si es nuevo, verificar db/upgrade.php
3. Verificar version en version.php

**PASO 2: Verificar Dependencias**
1. Verificar clase/metodo existe
2. Verificar parametros esperados
3. Verificar valores retorno

**PASO 3: Validar en Plataforma**
1. Ejecutar `php admin/cli/upgrade.php`
2. Purgar cache: `php admin/cli/purge_caches.php`
3. Navegar vistas afectadas
4. Verificar sin errores

### 14.3 Patron Queries Seguras

- Usar `COALESCE(campo_nuevo, campo_alternativo)` para fallback
- Usar `LEFT JOIN` si relacion puede no existir
- Verificar existencia campo antes de usar en PHP

---

## 15. AUDITORIA DEL PLUGIN (Diciembre 2025)

### 15.1 Resumen de Analisis

| Area | Estado | Detalle |
|------|--------|---------|
| Base de Datos | ⚠️ 2 issues | Vacancy tiene fechas temporales (COALESCE implementado) |
| CSS/Templates | 95.8% OK | 14 templates con Bootstrap raw pendientes |
| Capabilities | ✅ Completo | 31 capabilities, todas requeridas presentes |
| Web Services | ✅ Eliminados | Removidos en v3.2.2 |
| Language Strings | ✅ Completo | 2,711 lineas EN/ES |

---

## 16. REFACTORIZACION REQUERIDA

### 16.1 CRITICO - Esquema de Base de Datos

#### Issue 1: Vacancy tiene fechas temporales

**Problema:** La tabla `local_jobboard_vacancy` tiene campos `opendate` y `closedate` pero segun requerimientos las vacantes NO deben tener fechas propias - heredan de convocatoria.

**Estado:** COALESCE implementado en myreviews.php como solucion temporal.

**Accion requerida:**
1. Evaluar remocion de campos en futura version
2. Actualizar codigo que usa `v.closedate` para usar `c.enddate`
3. Usar siempre COALESCE para compatibilidad

#### Issue 2: doctype input_type valores

**Problema:** Campo `input_type` tiene valores `file, text, url, number` pero requerimientos piden `file, text, textarea, select`.

**Accion requerida:**
1. Actualizar DEFAULT y COMMENT del campo
2. Crear migracion para convertir valores existentes
3. Actualizar formularios que usan estos valores

---

### 16.2 ~~ALTA - Remocion de Web Services~~ COMPLETADO v3.2.2

**Archivos eliminados en v3.2.2:**

| Archivo | Lineas | Estado |
|---------|--------|--------|
| db/services.php | 133 | ELIMINADO |
| classes/external/api.php | 1,079 | ELIMINADO |

**Pasos completados:**
1. HECHO - Eliminar `db/services.php`
2. HECHO - Eliminar directorio `classes/external/` completo
3. HECHO - NO se requirieron cambios en lib.php
4. HECHO - Version incrementada a 3.2.2

**Se mantienen (NO son web services):**
- `ajax/get_departments.php` - Endpoint AJAX estandar
- `ajax/get_companies.php` - Endpoint AJAX estandar
- `ajax/get_convocatorias.php` - Endpoint AJAX estandar

---

### 16.3 MEDIA - Migracion CSS Bootstrap a jb-*

**Estado actual:** 95.8% compliant (3,922 clases jb-*)

**Pendiente:** 169 ocurrencias de Bootstrap raw en 14 templates

**Templates a migrar:**

| Prioridad | Template | Clases Bootstrap |
|-----------|----------|------------------|
| 1 | reports/applications.mustache | card, table, badge, progress |
| 1 | reports/documents.mustache | table, row, col- |
| 1 | reports/overview.mustache | table, row, col- |
| 1 | reports/reviewers.mustache | d-flex |
| 1 | reports/timeline.mustache | d-flex |
| 2 | application_row.mustache | btn, badge, progress |
| 2 | document_upload.mustache | btn, d-flex |
| 2 | signup_page.mustache | btn, d-flex |
| 3 | pages/public_detail.mustache | btn, row, col-, d-flex |
| 3 | components/list_group.mustache | btn-group, d-flex |
| 3 | components/timeline.mustache | d-flex |
| 3 | components/filter_form.mustache | row |
| 3 | document_row.mustache | row |

**Esfuerzo estimado:** 3.5-7 horas

---

### 16.4 BAJA - Mejoras Funcionales

| Tarea | Descripcion | Estado |
|-------|-------------|--------|
| Interfaz revision mod_assign | Panel lateral, preview PDF, checklist | ✅ v3.3.0 |
| Preview email tiempo real | Editor con variables y preview | Pendiente |
| User Tours | 15 tours con selectores jb-* | Pendiente |
| Tests PHPUnit | Cobertura clases principales | Pendiente |
| Integracion calendario | Eventos fechas limite | Pendiente |

---

## 17. FASES DE REFACTORIZACION

### Fase 1: Remocion Web Services - COMPLETADO (v3.2.2)
- [x] Eliminar db/services.php
- [x] Eliminar classes/external/
- [x] Incrementar version a 3.2.2
- [ ] Probar que no hay errores (pendiente validacion en plataforma)

### Fase 2: Migracion CSS - COMPLETADO (v3.2.3)
- [x] Migrar templates de reports/ (5 archivos)
- [x] Migrar templates de componentes (list_group, timeline, filter_form)
- [x] Migrar templates de paginas (public_detail, application_row, document_upload, signup_page)
- [ ] Verificar en themes: Boost, Remui, Flavor (pendiente validacion)

### Fase 3: Limpieza BD (2-3 horas)
- [ ] Evaluar remocion de opendate/closedate de vacancy
- [ ] Actualizar input_type valores en doctype
- [ ] Crear migraciones correspondientes
- [ ] Actualizar codigo dependiente

### Fase 4: Interfaz Revision (8-12 horas) ✅ Completada v3.3.0
- [x] Disenar layout mod_assign style (grading_panel.mustache)
- [x] Implementar visor PDF inline (iframe + image preview)
- [x] Agregar navegacion AJAX (grading_panel.js)
- [x] Implementar atajos de teclado (N/P/A/R/D/F/S/↑↓/Shift+A/?/Esc)

### Fase 5: User Tours (4-6 horas)
- [ ] Crear 15 tours con selectores jb-*
- [ ] Probar en cada rol
- [ ] Documentar tours

---

## 18. REGLAS ABSOLUTAS PARA AGENTES

1. **ANALIZAR** repositorio completo antes de implementar
2. **SOLO CLASES jb-*** - No usar Bootstrap directamente
3. **VALIDAR SIEMPRE** en plataforma antes de commit
4. **NO improvisar** cambios en produccion
5. **Respetar** arquitectura IOMAD 4 niveles
6. **Paridad EN/ES** - Toda string en AMBOS idiomas
7. **NO hardcodear** strings - usar get_string() SIEMPRE
8. **Documentar** TODO en CHANGELOG.md
9. **Comite** es por FACULTAD, no por vacante
10. **Revisores** se asignan por PROGRAMA
11. **Formulario** es PERSONALIZABLE por convocatoria (no global)
12. **Carta intencion** es campo TEXTO, no archivo
13. **Convocatoria** debe tener PDF adjunto
14. **Auditoria ROBUSTA** - registrar TODAS acciones
15. **Un postulante** = UNA vacante por convocatoria
16. **Validacion documentos** es 100% MANUAL
17. **Busqueda usuarios** por username en comites
18. **Cada cambio** = incremento version + CHANGELOG
19. **Compilar AMD** despues de modificaciones: `grunt amd --root=local/jobboard`
20. **Reportes** filtrados por convocatoria obligatoriamente
21. **Vacantes** NO tienen fechas propias - heredan de convocatoria
22. **Excepciones** son GLOBALES, activadas por convocatoria
23. **VERIFICAR** esquema BD antes de escribir queries
24. **USAR** COALESCE para compatibilidad hacia atras
25. **Documentos:** Catalogo global + configuracion especifica por convocatoria

### 18.1 Archivos Criticos - No Modificar Sin Revision

- `db/install.xml` - Esquema BD (requiere upgrade.php)
- `db/access.php` - Capabilities (afecta permisos)
- `version.php` - Version (afecta upgrades)
- `lib.php` - Funciones core (afecta navegacion)

---

## 19. CONTROL DE VERSIONES

### 19.1 Formato

```php
$plugin->version = YYYYMMDDXX;  // Ej: 2025121242
$plugin->release = 'X.Y.Z';     // Ej: '3.2.2'
```

### 19.2 Incrementos

| Tipo Cambio | version | release |
|-------------|---------|---------|
| Correccion typo | +1 | No cambia |
| Nueva string | +1 | No cambia |
| Bug fix | +1 | +0.0.1 |
| Funcionalidad menor | +1 | +0.1.0 |
| Funcionalidad mayor | +1 | +1.0.0 |
| Cambio BD | +1 | +0.1.0 |

---

## 20. COMANDOS UTILES

### 20.1 Moodle CLI

| Comando | Proposito |
|---------|-----------|
| `php admin/cli/upgrade.php` | Ejecutar migraciones BD |
| `php admin/cli/purge_caches.php` | Limpiar cache |

### 20.2 Desarrollo

| Comando | Proposito |
|---------|-----------|
| `grunt amd --root=local/jobboard` | Compilar JavaScript AMD |
| `php -l archivo.php` | Verificar sintaxis PHP |
| `php admin/tool/phpcs/cli/run.php --standard=moodle local/jobboard` | Validar estandares |

### 20.3 CLI del Plugin

| Comando | Proposito |
|---------|-----------|
| `php local/jobboard/cli/cli.php --help` | Ver ayuda importador |
| `php local/jobboard/cli/cli.php --action=list-centers` | Listar centros |
| `php local/jobboard/cli/cli.php --action=create-vacancies --convocatoria-id=5` | Crear vacantes |

---

## 21. CUMPLIMIENTO NORMATIVO

### 21.1 GDPR / Habeas Data (Colombia)

- Consentimiento explicito tratamiento datos
- Derecho acceso, rectificacion, eliminacion
- Privacy API Moodle implementada
- Tabla `local_jobboard_consent`

### 21.2 Normativa Colombiana

- Excepciones libreta militar >=50 anos
- Documentos antecedentes oficiales
- Requisitos MEN para docentes

---

## 22. COMPATIBILIDAD

### 22.1 Moodle
- Minimo: 4.1 (2022112800)
- Maximo probado: 4.5

### 22.2 PHP
- Minimo: 7.4
- Recomendado: 8.1+

### 22.3 Themes Compatibles
- Boost (default)
- Classic
- Remui
- Flavor

### 22.4 IOMAD
- Integracion completa multi-tenant
- Filtrado company/department

---

## 23. CONTACTO

- **Autor:** Alonso Arias
- **Email:** soporteplataformas@iser.edu.co
- **Supervision:** Vicerrectoria Academica ISER
- **Institucion:** ISER (Instituto Superior de Educacion Rural)
- **Sede Principal:** Pamplona, Norte de Santander, Colombia

---

*Ultima actualizacion: Diciembre 2025*
*Plugin local_jobboard v3.2.3 para Moodle 4.1-4.5 con IOMAD*
*Documento consolidado de requerimientos del proyecto*
*Web Services eliminados en v3.2.2*
*CSS Bootstrap migrado a jb-* en v3.2.3*
