# AGENTS.md - local_jobboard

Plugin de Moodle para gestión de vacantes académicas y postulaciones docentes.
Sistema de Bolsa de Empleo para reclutamiento de profesores de cátedra.

---

## Información del Proyecto

| Campo | Valor |
|-------|-------|
| **Componente** | `local_jobboard` |
| **Versión actual** | 3.2.1 (2025121241) |
| **Moodle requerido** | 4.1+ (2022112800) |
| **Moodle soportado** | 4.1 - 4.5 |
| **Madurez** | MATURITY_STABLE |
| **Licencia** | GNU GPL v3 or later |
| **Institución** | ISER (Instituto Superior de Educación Rural) |
| **Autor** | Alonso Arias <soporteplataformas@iser.edu.co> |
| **Supervisión** | Vicerrectoría Académica ISER |

---

## Arquitectura IOMAD ISER

El plugin opera en un entorno IOMAD multi-tenant con estructura de 4 niveles:

### PARTE A: Estructura Organizacional (Multi-tenant)

| Nivel | Componente IOMAD | Descripción |
|-------|------------------|-------------|
| 1 | Instancia IOMAD | virtual.iser.edu.co |
| 2 | Companies | 16 Centros Tutoriales (Pamplona, Cúcuta, Tibú, Ocaña, Toledo, El Tarra, Sardinata, San Vicente del Chucurí, Pueblo Bello, San Pablo, Santa Rosa del Sur, Fundación, Cimitarra, Salazar, Tame, Saravena) |
| 3 | Departments | Modalidades por Centro (Presencial, A Distancia, Virtual, Híbrida) |
| 4 | Sub-departments | Facultades por Modalidad (FCAS, FII) |

### PARTE B: Estructura Académica

**Facultad de Ciencias Administrativas y Sociales (FCAS):**
- Tecnología en Gestión Empresarial
- Tecnología en Gestión Comunitaria
- Tecnología en Gestión de Mercadeo
- Técnica Prof. en Seguridad y Salud en el Trabajo

**Facultad de Ingenierías e Informática (FII):**
- Tecnología Agropecuaria
- Tecnología en Procesos Agroindustriales
- Tecnología en Gestión Industrial
- Tecnología en Gestión de Redes y Sistemas Teleinformáticos
- Tecnología en Gestión y Construcción de Obras Civiles
- Técnica Prof. en Producción de Frutas y Hortalizas

---

## Estado Actual del Plugin (Diciembre 2025)

### Métricas del Plugin

| Métrica | Valor | Estado |
|---------|-------|--------|
| Archivos PHP totales | ~80 | Completos |
| Clases principales | 17 | 9,641 líneas totales |
| Renderer principal | 1 | 5,796 líneas |
| Renderers especializados | 10 | Implementados |
| Vistas (views) | 17 | Completas |
| Formularios (forms) | 8 | Completos |
| Templates Mustache | 115 | Completos |
| Módulos AMD | 12 | Implementados |
| Tablas de BD | 28 | Implementadas |
| Capabilities | 28 | Implementadas |
| Language strings EN | 2,711 líneas | Completo |
| Language strings ES | 2,711 líneas | Completo |
| Tareas programadas | 3 | Implementadas |
| Eventos | 8 | Implementados |
| Proveedores de mensajes | 5 | Implementados |
| Definiciones de caché | 5 | Implementadas |

### Estructura de Archivos Actual

#### Archivos Raíz

| Archivo | Descripción | Líneas |
|---------|-------------|--------|
| index.php | Router centralizado con switch por vista | ~200 |
| lib.php | Funciones principales, navegación, hooks | ~450 |
| settings.php | Configuración admin con secciones | ~200 |
| version.php | Información de versión 3.2.0 | ~30 |
| styles.css | Sistema CSS completo con prefijo jb-* | ~1,800 |
| bulk_validate.php | Página de validación masiva | ~150 |
| assign_reviewer.php | Asignación de revisores | ~120 |
| migrate.php | Exportación de datos | ~100 |
| signup.php | Registro personalizado IOMAD | ~180 |
| updateprofile.php | Actualización de perfil | ~150 |

#### Directorio views/ (17 archivos)

Todas las vistas siguen el patrón: verificar capabilities, cargar renderer, llamar método render_*_page().

| Vista | Propósito | Capabilities requeridas |
|-------|-----------|------------------------|
| dashboard.php | Dashboard adaptativo por rol | view |
| browse_convocatorias.php | Listado público de convocatorias | viewpublicvacancies |
| convocatorias.php | Gestión de convocatorias (admin) | manageconvocatorias |
| convocatoria.php | Crear/editar convocatoria | manageconvocatorias |
| view_convocatoria.php | Detalle de convocatoria | view |
| vacancies.php | Listado de vacantes | viewallvacancies |
| vacancy.php | Detalle de vacante | view |
| apply.php | Formulario de postulación | apply |
| applications.php | Listado de postulaciones | viewallapplications |
| application.php | Detalle de postulación | viewownapplications o viewallapplications |
| manage.php | Panel de gestión | manage |
| review.php | Revisión de documentos | review |
| myreviews.php | Mis revisiones pendientes | review |
| reports.php | Reportes y estadísticas | viewreports |
| public.php | Landing page pública | ninguna |
| public_convocatoria.php | Convocatoria pública | ninguna |
| public_vacancy.php | Vacante pública | ninguna |

#### Directorio classes/ (17 clases principales)

| Clase | Propósito | Líneas |
|-------|-----------|--------|
| application.php | CRUD de postulaciones, transiciones de estado | 780 |
| audit.php | Sistema de auditoría completo | 420 |
| bulk_validator.php | Validación masiva de documentos | 368 |
| committee.php | Gestión de comités de selección | 735 |
| convocatoria_exemption.php | Excepciones a nivel de convocatoria | 443 |
| data_export.php | Exportación GDPR de datos | 323 |
| document.php | Gestión de documentos y validaciones | 832 |
| document_services.php | Servicios de conversión PDF | 374 |
| email_template.php | Sistema de plantillas de email | 1,171 |
| encryption.php | Encriptación AES-256-GCM de archivos | 339 |
| exemption.php | Excepciones de documentos ISER | 587 |
| interview.php | Programación de entrevistas | 675 |
| notification.php | Cola de notificaciones | 317 |
| program_reviewer.php | Revisores por programa | 516 |
| review_notifier.php | Notificaciones de revisión | 284 |
| reviewer.php | Asignación de revisores | 388 |
| vacancy.php | CRUD de vacantes | 1,089 |

#### Directorio classes/output/ (11 archivos)

| Renderer | Propósito | Líneas |
|----------|-----------|--------|
| renderer.php | Renderer principal delegador | 5,796 |
| renderer_base.php | Clase base con utilidades compartidas | 366 |
| admin_renderer.php | Dashboard admin, doctypes, excepciones | 698 |
| application_renderer.php | Listas y detalles de postulaciones | 333 |
| convocatoria_renderer.php | Cards y listas de convocatorias | 323 |
| dashboard_renderer.php | Dashboards por rol | 500 |
| public_renderer.php | Landing page, navegación pública | 533 |
| reports_renderer.php | Generación de reportes | 619 |
| review_renderer.php | Interfaz de revisión de documentos | 314 |
| vacancy_renderer.php | Cards y detalles de vacantes | 271 |
| ui_helper.php | Utilidades estáticas para componentes HTML | 663 |

#### Directorio classes/forms/ (8 formularios)

| Formulario | Propósito | Líneas |
|------------|-----------|--------|
| application_form.php | Postulación multi-paso con documentos | 616 |
| convocatoria_form.php | Crear/editar convocatorias | ~300 |
| doctype_form.php | Configuración de tipos de documento | ~200 |
| email_template_form.php | Editor de plantillas de email | ~250 |
| exemption_form.php | Gestión de excepciones ISER | ~200 |
| signup_form.php | Registro personalizado IOMAD | 572 |
| updateprofile_form.php | Actualización de perfil | 543 |
| vacancy_form.php | Crear/editar vacantes | ~350 |

#### Directorio amd/src/ (12 módulos)

| Módulo | Propósito |
|--------|-----------|
| application_confirm.js | Confirmación de envío de postulación |
| apply_progress.js | Navegación multi-paso del formulario |
| bulk_actions.js | Selección checkbox, operaciones masivas |
| card_actions.js | Interacciones con cards de vacantes/convocatorias |
| convocatoria_form.js | Carga AJAX de departamentos |
| document_preview.js | Modal de preview de documentos |
| exemption_form.js | Selección de tipos de documento |
| loading_states.js | Gestión de estados de carga |
| progress_steps.js | Indicador visual de progreso |
| public_filters.js | Filtrado con AJAX en página pública |
| signup_form.js | Selección cascada company/department IOMAD |
| vacancy_form.js | Selects cascada para formulario de vacante |

#### Directorio templates/ (115 plantillas)

**Raíz (67 templates):** Plantillas generales del plugin incluyendo dashboard, cards, listas, formularios, modales.

**components/ (17 templates):** Componentes reutilizables (alert, badge, button, card, empty_state, filter_form, loading, page_header, pagination, progress_bar, stat_card, status_badge, table, tabs, timeline, tooltip, user_card).

**pages/ (26 templates):** Layouts completos de página (admin_dashboard, applicant_dashboard, application_detail, application_list, apply, assign_reviewer, bulk_validate, committee, committee_dashboard, convocatoria_detail, convocatoria_form, convocatoria_list, coordinator_dashboard, dashboard, doctypes, email_templates, exemptions, manage, public, public_convocatoria, public_vacancy, reports, review, review_document, reviewer_dashboard, vacancy_detail).

**reports/ (5 templates):** report_applications, report_convocatorias, report_documents, report_reviewers, report_vacancies.

#### Directorio db/

| Archivo | Descripción |
|---------|-------------|
| install.xml | Esquema de 28 tablas |
| install.php | Instalación con doctypes predeterminados |
| upgrade.php | Migraciones de versiones |
| access.php | 28 capabilities definidas |
| caches.php | 5 definiciones de caché |
| messages.php | 5 proveedores de mensajes |
| events.php | 8 eventos del plugin |
| tasks.php | 3 tareas programadas |
| services.php | Web services (PENDIENTE REMOCIÓN) |

#### Directorio lang/

| Archivo | Descripción | Líneas |
|---------|-------------|--------|
| en/local_jobboard.php | Strings en inglés | 2,711 |
| es/local_jobboard.php | Strings en español | 2,711 |

#### Directorio cli/

| Archivo | Descripción |
|---------|-------------|
| cli.php | Importador de perfiles v2.2 |
| parse_profiles.php | Parser de perfiles v1 |
| parse_profiles_v2.php | Parser de perfiles v2 |
| import_vacancies.php | Importación de vacantes |

#### Directorio admin/

| Archivo | Descripción |
|---------|-------------|
| doctypes.php | Gestión de tipos de documento |
| email_templates.php | Gestión de plantillas de email |
| exemptions.php | Gestión de excepciones |

---

## Tablas de Base de Datos (28 tablas)

### Tablas Core

| Tabla | Propósito | Campos clave |
|-------|-----------|--------------|
| local_jobboard_vacancy | Vacantes | id, convocatoriaid, companyid, departmentid, facultyid, programid, title, status |
| local_jobboard_application | Postulaciones | id, vacancyid, userid, status, timecreated |
| local_jobboard_document | Documentos subidos | id, applicationid, doctypeid, filepath, status |
| local_jobboard_convocatoria | Convocatorias | id, companyid, code, name, status, startdate, enddate |

### Tablas de Validación

| Tabla | Propósito |
|-------|-----------|
| local_jobboard_doc_validation | Validaciones de documentos con checklist |
| local_jobboard_doc_requirement | Requisitos de documentos por vacante |
| local_jobboard_doctype | Tipos de documento configurables |

### Tablas de Workflow

| Tabla | Propósito |
|-------|-----------|
| local_jobboard_workflow_log | Log de transiciones de estado |
| local_jobboard_audit | Auditoría completa de acciones |
| local_jobboard_notification | Cola de notificaciones |

### Tablas Organizacionales

| Tabla | Propósito |
|-------|-----------|
| local_jobboard_faculty | Facultades |
| local_jobboard_program | Programas académicos |
| local_jobboard_committee | Comités de selección |
| local_jobboard_committee_member | Miembros del comité |

### Tablas de Usuarios

| Tabla | Propósito |
|-------|-----------|
| local_jobboard_applicant_profile | Perfiles de postulantes |
| local_jobboard_consent | Consentimientos Habeas Data |
| local_jobboard_exemption | Excepciones de documentos |
| local_jobboard_program_reviewer | Revisores por programa |

### Tablas de Email

| Tabla | Propósito |
|-------|-----------|
| local_jobboard_email_template | Plantillas de email |
| local_jobboard_email_strings | Strings de email por idioma |

### Tablas de Entrevistas

| Tabla | Propósito |
|-------|-----------|
| local_jobboard_interview | Entrevistas programadas |
| local_jobboard_interviewer | Entrevistadores asignados |

### Tablas de Evaluación

| Tabla | Propósito |
|-------|-----------|
| local_jobboard_evaluation | Evaluaciones del comité |
| local_jobboard_criteria | Criterios de evaluación |
| local_jobboard_decision | Decisiones finales |

### Tablas de Configuración

| Tabla | Propósito |
|-------|-----------|
| local_jobboard_config | Configuración del plugin |
| local_jobboard_conv_docexempt | Excepciones de documentos por convocatoria |
| local_jobboard_vacancy_field | Campos personalizados de vacante |

---

## Capabilities (28 definidas)

### Vista General
- `local/jobboard:view` - Ver el job board
- `local/jobboard:viewinternal` - Ver contenido interno
- `local/jobboard:viewpublicvacancies` - Ver vacantes públicas

### Gestión de Vacantes
- `local/jobboard:manage` - Gestionar job board
- `local/jobboard:createvacancy` - Crear vacantes
- `local/jobboard:editvacancy` - Editar vacantes
- `local/jobboard:deletevacancy` - Eliminar vacantes
- `local/jobboard:publishvacancy` - Publicar vacantes
- `local/jobboard:viewallvacancies` - Ver todas las vacantes

### Convocatorias
- `local/jobboard:manageconvocatorias` - Gestionar convocatorias

### Postulaciones
- `local/jobboard:apply` - Aplicar a vacantes
- `local/jobboard:viewownapplications` - Ver propias postulaciones
- `local/jobboard:viewallapplications` - Ver todas las postulaciones
- `local/jobboard:changeapplicationstatus` - Cambiar estado de postulación

### Revisión
- `local/jobboard:review` - Revisar documentos
- `local/jobboard:validatedocuments` - Validar documentos
- `local/jobboard:reviewdocuments` - Revisar documentos
- `local/jobboard:assignreviewers` - Asignar revisores
- `local/jobboard:downloadanydocument` - Descargar cualquier documento

### Evaluación
- `local/jobboard:evaluate` - Evaluar candidatos
- `local/jobboard:viewevaluations` - Ver evaluaciones

### Workflow
- `local/jobboard:manageworkflow` - Gestionar workflow

### Reportes
- `local/jobboard:viewreports` - Ver reportes
- `local/jobboard:exportreports` - Exportar reportes
- `local/jobboard:exportdata` - Exportar datos

### Administración
- `local/jobboard:configure` - Configurar plugin
- `local/jobboard:managedoctypes` - Gestionar tipos de documento
- `local/jobboard:manageemailtemplates` - Gestionar plantillas de email
- `local/jobboard:manageexemptions` - Gestionar excepciones

---

## Roles del Sistema (3 roles)

| Shortname | Nombre | Propósito |
|-----------|--------|-----------|
| jobboard_reviewer | Revisor de Documentos | Valida documentos de postulantes |
| jobboard_coordinator | Coordinador de Selección | Gestiona convocatorias, vacantes, asigna revisores |
| jobboard_committee | Miembro del Comité | Evalúa candidatos finales |

---

## Sistema de Estados

### Estados de Vacante

| Estado | CSS Class | Descripción |
|--------|-----------|-------------|
| draft | secondary | Borrador |
| published | success | Publicada |
| closed | danger | Cerrada |
| archived | dark | Archivada |
| pending | warning | Pendiente |
| assigned | primary | Asignada |

### Estados de Postulación

| Estado | CSS Class | Descripción |
|--------|-----------|-------------|
| draft | secondary | Borrador |
| submitted | info | Enviada |
| reviewing | primary | En revisión |
| under_review | warning | Bajo revisión |
| approved | success | Aprobada |
| docs_validated | success | Documentos validados |
| rejected | danger | Rechazada |
| docs_rejected | danger | Documentos rechazados |
| withdrawn | dark | Retirada |
| interview | warning | En entrevista |
| hired | success | Contratado |
| selected | success | Seleccionado |

### Estados de Documento

| Estado | CSS Class | Descripción |
|--------|-----------|-------------|
| pending | warning | Pendiente |
| approved | success | Aprobado |
| rejected | danger | Rechazado |
| expired | dark | Vencido |
| reviewing | info | En revisión |

### Estados de Convocatoria

| Estado | CSS Class | Descripción |
|--------|-----------|-------------|
| draft | secondary | Borrador |
| open | success | Abierta |
| closed | danger | Cerrada |
| archived | dark | Archivada |

---

## Sistema CSS (prefijo jb-*)

El plugin utiliza un sistema CSS propio con prefijo `jb-*` para evitar conflictos con el theme de Moodle. Todas las clases CSS del plugin siguen esta convención.

### Categorías de Clases CSS

| Categoría | Ejemplos | Propósito |
|-----------|----------|-----------|
| Layout | jb-row, jb-col-*, jb-container | Sistema de grid |
| Espaciado | jb-m-*, jb-p-*, jb-mb-*, jb-mt-* | Márgenes y padding |
| Cards | jb-card, jb-card-header, jb-card-body | Componentes de tarjeta |
| Botones | jb-btn, jb-btn-primary, jb-btn-sm | Botones |
| Tablas | jb-table, jb-table-striped, jb-table-hover | Tablas |
| Badges | jb-badge, jb-badge-success, jb-badge-danger | Etiquetas de estado |
| Alertas | jb-alert, jb-alert-info, jb-alert-warning | Mensajes de alerta |
| Texto | jb-text-muted, jb-text-primary, jb-h4 | Estilos de texto |
| Flex | jb-d-flex, jb-justify-content-*, jb-align-items-* | Flexbox |
| Formularios | jb-form-control, jb-form-group, jb-input-group | Elementos de formulario |

### Regla Crítica

NUNCA usar clases Bootstrap directamente en templates o PHP. SIEMPRE usar clases con prefijo `jb-*` definidas en styles.css.

---

## Templates Mustache

### Convenciones de Templates

1. **Internacionalización:** Usar `{{#str}}stringkey, local_jobboard{{/str}}` para todas las cadenas de texto
2. **Variables de contexto:** Documentar en el header del template con `@template` y lista de variables
3. **Clases CSS:** Solo usar clases con prefijo `jb-*`
4. **Componentes reutilizables:** Usar `{{> local_jobboard/components/nombre}}` para partials
5. **Condiciones:** Usar `{{#variable}}...{{/variable}}` y `{{^variable}}...{{/variable}}`
6. **URLs:** Pasar como variables desde PHP, nunca construir en template

### Estructura de Context Data

Cada método `prepare_*_data()` en los renderers devuelve un array asociativo que se pasa al template. Los nombres de variables deben ser descriptivos y en snake_case.

---

## Módulos AMD (JavaScript)

### Convenciones de Módulos AMD

1. **Dependencias:** Usar módulos core de Moodle (core/ajax, core/notification, core/templates, core/str)
2. **No jQuery directo:** Usar API nativa del DOM o wrappers de Moodle
3. **No Bootstrap JS:** Usar componentes propios o de Moodle
4. **Inicialización:** Exportar función `init()` que se llama desde PHP
5. **Eventos:** Usar event delegation para eficiencia
6. **AJAX:** Usar core/ajax para llamadas al servidor

### Compilación

Los módulos deben compilarse con: `grunt amd --root=local/jobboard`

---

## Tareas Programadas (3 tareas)

| Tarea | Propósito | Frecuencia |
|-------|-----------|------------|
| process_notifications | Procesa cola de notificaciones | Cada 5 minutos |
| cleanup_expired | Limpia documentos vencidos | Diaria |
| send_reminders | Envía recordatorios | Diaria |

---

## Proveedores de Mensajes (5 proveedores)

| Proveedor | Propósito |
|-----------|-----------|
| application_submitted | Notifica postulación enviada |
| application_status_changed | Notifica cambio de estado |
| document_validated | Notifica documento validado |
| document_rejected | Notifica documento rechazado |
| review_assigned | Notifica asignación de revisión |

---

## Eventos (8 eventos)

| Evento | Trigger |
|--------|---------|
| application_created | Se crea postulación |
| application_submitted | Se envía postulación |
| application_status_changed | Cambia estado de postulación |
| document_uploaded | Se sube documento |
| document_validated | Se valida documento |
| document_rejected | Se rechaza documento |
| vacancy_created | Se crea vacante |
| vacancy_published | Se publica vacante |

---

## Definiciones de Caché (5 cachés)

| Caché | Propósito | TTL |
|-------|-----------|-----|
| convocatorias | Lista de convocatorias activas | 1 hora |
| vacancies | Lista de vacantes publicadas | 30 min |
| doctypes | Tipos de documento | 24 horas |
| user_permissions | Permisos de usuario | 10 min |
| email_templates | Plantillas de email | 1 hora |

---

## Cumplimiento Normativo

### Protección de Datos

- **Ley 1581/2012** - Habeas Data (Colombia): Implementado con consentimiento explícito
- **GDPR** - Privacy API de Moodle: Implementada en `classes/privacy/provider.php`
- **Exportación de datos:** Método `export_user_data()` completo
- **Eliminación de datos:** Método `delete_data_for_user()` con anonimización
- **Tabla de consentimientos:** `local_jobboard_consent` registra aceptación

### Contratación Docente

- Cumple normativa colombiana de contratación docente
- Excepciones de edad según legislación vigente (≥50 años exentos de libreta militar)
- Requisitos de libreta militar solo para hombres menores de 50 años

---

## Reglas de Negocio Críticas

### Organización por Facultad

1. **Vacantes separadas por facultad:** Las vacantes se organizan y filtran por facultad
2. **Comité de selección por FACULTAD:** NO por vacante. Cada facultad tiene su propio comité
3. **Revisores asignados por PROGRAMA:** Los revisores de documentos se asignan a nivel de programa académico

### Convocatorias

- **PDF adjunto obligatorio:** Al crear la convocatoria se debe cargar un PDF con el detalle completo
- **Descripción breve:** Campo de texto para resumen de la convocatoria
- **Términos y condiciones:** HTML con condiciones legales

### Postulaciones

- **Límite:** Un postulante solo puede aplicar a UNA vacante por convocatoria
- **Experiencia ocasional:** Docentes ocasionales requieren 2 años de experiencia laboral equivalente

### Excepciones de Documentos

- **Tipos de excepción:** historico_iser, documentos_recientes, traslado_interno, recontratacion
- **Documentos eximibles:** Los marcados con `iserexempted = 1` en doctype
- **Excepciones por edad:** Personas ≥50 años exentas de libreta militar automáticamente
- **Excepciones por género:** Libreta militar solo para hombres

### Validación de Documentos

- La verificación es **100% MANUAL** - NO existe verificación automática
- Cada tipo de documento tiene su checklist de verificación específico
- Documentos rechazados pueden recargarse con observaciones enviadas por email
- Razones de rechazo estándar: illegible, expired, incomplete, wrongtype, mismatch

---

## Flujo de Trabajo: Postulación Completa

### Diagrama de Estados

**Postulante:**
1. Aplica a vacante → Estado: `submitted`
2. Espera revisión de documentos

**Sistema:**
- Asigna revisor automáticamente por programa
- Estado cambia a: `under_review`

**Revisor:**
- Evalúa cada documento con checklist
- Aprueba → `docs_validated` o Rechaza → `docs_rejected`

**Postulante (si rechazo):**
- Recibe notificación con observaciones
- Corrige y recarga documentos
- Vuelve a `under_review`

**Si documentos validados:**
- Puede pasar a `interview` si aplica
- Comité evalúa candidatos

**Comité:**
- Evalúa candidatos finales
- Decisión: `selected`, `waitlist`, o `rejected`

---

## PENDIENTE DE REMOCIÓN: Web Services

Los archivos y funcionalidades relacionados con web services y API externa serán removidos del plugin:

### Archivos a Remover

| Archivo | Razón de remoción |
|---------|-------------------|
| db/services.php | Definición de servicios web externos |
| classes/external/api.php | Implementación de API externa |
| classes/external/*.php | Clases de servicios externos |

### Instrucciones de Remoción

1. Eliminar archivo `db/services.php`
2. Eliminar directorio `classes/external/` completo
3. Remover referencias a servicios web en `lib.php`
4. Actualizar `version.php` con nueva versión
5. Ejecutar `php admin/cli/upgrade.php`

---

## Validación Pre-Implementación (CRÍTICO)

Antes de implementar CUALQUIER cambio, se debe validar que no generará errores de base de datos o runtime. Esta sección es OBLIGATORIA para cualquier agente que trabaje en este plugin.

### Errores Comunes a Evitar

| Error | Causa | Prevención |
|-------|-------|------------|
| Unknown column 'X' in 'field list' | Columna referenciada en SQL no existe en BD | Verificar install.xml y upgrade.php antes de usar campos en queries |
| Table 'X' doesn't exist | Tabla referenciada no creada | Verificar que tabla exista en install.xml |
| Duplicate entry for key | Registro duplicado en índice único | Verificar condiciones antes de INSERT |
| Call to undefined method | Método no existe en clase | Verificar que método exista antes de llamar |

### Protocolo de Validación Obligatorio

**PASO 1: Verificar Esquema de BD**

Antes de escribir queries SQL con campos específicos:

1. Verificar que el campo existe en `db/install.xml`
2. Si el campo es nuevo, verificar que existe en `db/upgrade.php`
3. Verificar que la versión en `version.php` coincide con la migración

**PASO 2: Verificar Dependencias**

Antes de usar métodos o clases:

1. Verificar que la clase/método existe
2. Verificar parámetros esperados
3. Verificar valores de retorno

**PASO 3: Validar en Plataforma**

Después de cada cambio:

1. Ejecutar `php admin/cli/upgrade.php` si hay cambios de BD
2. Purgar caché: `php admin/cli/purge_caches.php`
3. Navegar a las vistas afectadas en el navegador
4. Verificar que no aparezcan errores en pantalla

### Patrón de Queries Seguras

Para queries que usan campos que podrían no existir (compatibilidad hacia atrás):

- Usar `COALESCE(campo_nuevo, campo_alternativo)` para valores con fallback
- Usar `LEFT JOIN` en lugar de `JOIN` si la relación puede no existir
- Verificar existencia de campo antes de usarlo en PHP

### Comandos de Verificación

| Tarea | Comando |
|-------|---------|
| Ejecutar migraciones | `php admin/cli/upgrade.php` |
| Purgar caché | `php admin/cli/purge_caches.php` |
| Verificar sintaxis PHP | `php -l archivo.php` |
| Validar estándares | `php admin/tool/phpcs/cli/run.php --standard=moodle local/jobboard` |

---

## Desarrollo Pendiente

### Prioridad Alta - Errores Conocidos

Ningún error conocido pendiente en la versión actual (3.2.1).

### Prioridad Media - Mejoras Funcionales

| Tarea | Descripción | Archivos Afectados |
|-------|-------------|-------------------|
| Interfaz de revisión estilo mod_assign | Panel lateral, preview PDF, checklist interactivo | Nuevo: views/review_document.php, templates/pages/review_document.mustache |
| Excepciones globales por convocatoria | Excepciones no por usuario sino globales activadas por convocatoria | classes/convocatoria_exemption.php, forms/exemption_form.php |
| Preview de email en tiempo real | Editor con variables y preview instantáneo | amd/src/email_template_editor.js |
| User Tours | 15 tours guiados para nuevos usuarios | db/tours/*.json |

### Prioridad Baja - Optimizaciones

| Tarea | Descripción |
|-------|-------------|
| Tests PHPUnit | Cobertura de tests para clases principales |
| Integración calendario | Eventos de calendario para fechas límite |
| CLI para PDFs | Procesar PDFs grandes dividiéndolos |

---

## Instrucciones para Desarrollo

### Antes de Cualquier Cambio

1. LEER completamente el archivo o clase a modificar
2. VERIFICAR esquema de BD en install.xml para cualquier query
3. VERIFICAR que no exista funcionalidad similar ya implementada
4. RESPETAR las convenciones de nomenclatura existentes
5. USAR get_string() para TODA cadena de texto
6. MANTENER paridad entre archivos de idioma EN y ES
7. PROBAR cambios en plataforma antes de commit

### Al Crear Nuevos Archivos

1. Incluir header PHPDoc completo con copyright ISER
2. Usar namespace apropiado según ubicación
3. Seguir estándares de código de Moodle (moodle-cs)
4. Documentar parámetros y retornos de métodos públicos

### Al Modificar Templates

1. Solo usar clases CSS con prefijo `jb-*`
2. Usar `{{#str}}` para internacionalización
3. Documentar variables de contexto en header
4. Probar en múltiples themes (Boost, Classic)

### Al Modificar JavaScript

1. Usar módulos AMD de Moodle
2. Evitar jQuery directo
3. Compilar con grunt después de cambios

### Control de Versiones

**CADA cambio, por mínimo que sea, DEBE:**
1. Incrementar `$plugin->version` en version.php (formato YYYYMMDDXX)
2. Actualizar `$plugin->release`
3. Documentar en CHANGELOG.md
4. Validar en plataforma ANTES de commit

---

## Convenciones de Nomenclatura

### PHP

| Elemento | Convención | Ejemplo |
|----------|------------|---------|
| Clases | PascalCase | `ApplicationRenderer` |
| Métodos | snake_case | `get_user_applications()` |
| Variables | snake_case | `$user_data` |
| Constantes | SCREAMING_SNAKE | `STATUS_PUBLISHED` |

### JavaScript

| Elemento | Convención | Ejemplo |
|----------|------------|---------|
| Módulos | snake_case | `apply_progress.js` |
| Funciones | camelCase | `initFormValidation()` |
| Variables | camelCase | `applicationData` |

### CSS

| Elemento | Convención | Ejemplo |
|----------|------------|---------|
| Clases | jb-kebab-case | `jb-card-header` |
| IDs | jb-kebab-case | `jb-main-container` |

### Base de Datos

| Elemento | Convención | Ejemplo |
|----------|------------|---------|
| Tablas | local_jobboard_snake | `local_jobboard_vacancy` |
| Campos | snake_case | `time_created` |

---

## Comandos Útiles

### Moodle CLI

| Comando | Propósito |
|---------|-----------|
| `php admin/cli/upgrade.php` | Ejecutar migraciones de BD |
| `php admin/cli/purge_caches.php` | Limpiar caché de Moodle |

### Desarrollo

| Comando | Propósito |
|---------|-----------|
| `grunt amd --root=local/jobboard` | Compilar JavaScript AMD |
| `php admin/tool/phpcs/cli/run.php --standard=moodle local/jobboard` | Validar código |

### CLI del Plugin

| Comando | Propósito |
|---------|-----------|
| `php local/jobboard/cli/cli.php --help` | Ver ayuda del importador |
| `php local/jobboard/cli/cli.php --create-structure --publish --public` | Importación completa |

---

## Notas Críticas para Agentes

### Reglas Absolutas

1. **ANALIZAR** el repositorio completo antes de implementar
2. **SOLO CLASES jb-*** - No usar clases Bootstrap directamente
3. **VALIDAR SIEMPRE** en plataforma antes de commit
4. **NO improvisar** cambios directamente en producción
5. **Respetar** la arquitectura IOMAD de 4 niveles
6. **Paridad EN/ES** - Toda string debe existir en AMBOS idiomas
7. **NO hardcodear** strings en PHP ni templates - usar get_string() SIEMPRE
8. **Documentar** TODO en CHANGELOG.md
9. **Comité de selección** es por FACULTAD, no por vacante
10. **Revisores** se asignan por PROGRAMA
11. **Formulario de postulación** es PERSONALIZABLE desde admin
12. **Carta de intención** es campo de TEXTO, no archivo
13. **Convocatoria** debe tener PDF adjunto con detalle completo
14. **Auditoría ROBUSTA** - registrar TODAS las acciones
15. Un postulante = UNA vacante por convocatoria
16. La validación de documentos es 100% MANUAL

### Archivos Críticos No Modificar Sin Revisión

- `db/install.xml` - Esquema de BD (requiere upgrade.php)
- `db/access.php` - Capabilities (afecta permisos)
- `version.php` - Versión (afecta upgrades)
- `lib.php` - Funciones core (afecta navegación)

---

## Contacto

- **Autor:** Alonso Arias
- **Email:** soporteplataformas@iser.edu.co
- **Supervisión:** Vicerrectoría Académica ISER
- **Institución:** ISER (Instituto Superior de Educación Rural)
- **Sede Principal:** Pamplona, Norte de Santander, Colombia

---

*Última actualización: Diciembre 2025*
*Plugin local_jobboard v3.2.1 para Moodle 4.1-4.5 con IOMAD*
