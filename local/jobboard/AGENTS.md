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
| **PHP requerido** | 7.4+ (recomendado 8.1+) |
| **Madurez** | MATURITY_STABLE |
| **Licencia** | GNU GPL v3 or later |
| **Institución** | ISER (Instituto Superior de Educación Rural) |
| **Autor** | Alonso Arias <soporteplataformas@iser.edu.co> |
| **Supervisión** | Vicerrectoría Académica ISER |

---

## Arquitectura IOMAD ISER

El plugin opera en un entorno IOMAD multi-tenant con estructura de 4 niveles:

### Estructura Organizacional (4 Niveles)

| Nivel | Componente IOMAD | Descripción |
|-------|------------------|-------------|
| 1 | Instancia IOMAD | virtual.iser.edu.co |
| 2 | Companies | 16 Centros Tutoriales |
| 3 | Departments | Modalidades por Centro |
| 4 | Sub-departments | Facultades por Modalidad |

### Centros Tutoriales (Companies - Nivel 2)

1. Pamplona (Sede Principal)
2. Cúcuta
3. Tibú
4. Ocaña
5. Toledo
6. El Tarra
7. Sardinata
8. San Vicente del Chucurí
9. Pueblo Bello
10. San Pablo
11. Santa Rosa del Sur
12. Fundación
13. Cimitarra
14. Salazar de las Palmas
15. Tame
16. Saravena

### Modalidades (Departments - Nivel 3)

| Código | Modalidad |
|--------|-----------|
| PRE | Presencial |
| DIS | A Distancia |
| VIR | Virtual |
| HIB | Híbrida |

### Facultades (Sub-departments - Nivel 4)

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

## Reglas de Negocio Fundamentales

### Organización por Facultad y Programa

| Elemento | Nivel de Organización | Nota |
|----------|----------------------|------|
| Vacantes | Por FACULTAD | Separadas y filtradas por facultad |
| Comité de Selección | Por FACULTAD | NO por vacante - cada facultad tiene su comité |
| Revisores de Documentos | Por PROGRAMA | Asignados a nivel de programa académico |
| Convocatorias | Globales | Con activación de excepciones por convocatoria |

### Convocatorias (CRÍTICO)

| Regla | Descripción |
|-------|-------------|
| PDF adjunto OBLIGATORIO | Toda convocatoria debe tener PDF con detalle completo |
| Fechas centralizadas | Las fechas de apertura/cierre se gestionan SOLO desde la convocatoria |
| Descripción breve | Campo de texto para resumen |
| Términos y condiciones | HTML con condiciones legales |

### Vacantes (CRÍTICO)

| Regla | Descripción |
|-------|-------------|
| SIN fechas propias | Las vacantes NO tienen fecha de apertura/cierre - heredan de convocatoria |
| Sin vacante extemporánea | Esta opción debe estar eliminada |
| Organización | Por facultad académica |

### Postulaciones (CRÍTICO)

| Regla | Descripción |
|-------|-------------|
| Límite estricto | Un postulante solo puede aplicar a UNA vacante por convocatoria |
| Experiencia ocasional | Docentes ocasionales requieren 2 años de experiencia laboral equivalente |
| Carta de intención | Es campo de TEXTO redactado en formulario, NO un archivo |

### Validación de Documentos (CRÍTICO)

| Regla | Descripción |
|-------|-------------|
| 100% MANUAL | NO existe verificación automática en background |
| Checklist por tipo | Cada tipo de documento tiene su checklist de verificación |
| Recarga permitida | Documentos rechazados pueden recargarse con observaciones |
| Razones de rechazo | illegible, expired, incomplete, wrongtype, mismatch |

### Excepciones de Documentos (CRÍTICO)

| Regla | Descripción |
|-------|-------------|
| GLOBALES | Se definen en administración, NO por usuario individual |
| Activación por convocatoria | Cada convocatoria puede activar/desactivar excepciones |
| Por edad | Personas ≥50 años exentas de libreta militar |
| Por género | Libreta militar solo para hombres |

**Tipos de Excepciones:**
- `historico_iser` - Histórico en la institución
- `documentos_recientes` - Documentos recientes válidos
- `traslado_interno` - Traslado interno
- `recontratacion` - Recontratación

---

## Estado Actual del Plugin

### Métricas v3.2.1

| Métrica | Valor | Estado |
|---------|-------|--------|
| Clases principales | 17 | 9,641 líneas |
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

### Estructura de Archivos Actual

#### Archivos Raíz

| Archivo | Descripción |
|---------|-------------|
| index.php | Router centralizado |
| lib.php | Funciones principales, navegación |
| settings.php | Configuración admin |
| version.php | Versión 3.2.1 |
| styles.css | Sistema CSS con prefijo jb-* |
| bulk_validate.php | Validación masiva |
| assign_reviewer.php | Asignación de revisores |
| signup.php | Registro personalizado IOMAD |
| updateprofile.php | Actualización de perfil |

#### Vistas (views/ - 17 archivos)

| Vista | Propósito | Capability |
|-------|-----------|------------|
| dashboard.php | Dashboard adaptativo por rol | view |
| browse_convocatorias.php | Listado público convocatorias | viewpublicvacancies |
| convocatorias.php | Gestión de convocatorias | manageconvocatorias |
| convocatoria.php | Crear/editar convocatoria | manageconvocatorias |
| view_convocatoria.php | Detalle de convocatoria | view |
| vacancies.php | Listado de vacantes | viewallvacancies |
| vacancy.php | Detalle de vacante | view |
| apply.php | Formulario de postulación | apply |
| applications.php | Listado de postulaciones | viewallapplications |
| application.php | Detalle de postulación | viewownapplications |
| manage.php | Panel de gestión | manage |
| review.php | Revisión de documentos | review |
| myreviews.php | Mis revisiones pendientes | review |
| reports.php | Reportes y estadísticas | viewreports |
| public.php | Landing page pública | ninguna |
| public_convocatoria.php | Convocatoria pública | ninguna |
| public_vacancy.php | Vacante pública | ninguna |

#### Clases Principales (classes/ - 17 clases)

| Clase | Propósito | Líneas |
|-------|-----------|--------|
| application.php | CRUD postulaciones, transiciones | 780 |
| audit.php | Sistema de auditoría | 420 |
| bulk_validator.php | Validación masiva | 368 |
| committee.php | Comités de selección | 735 |
| convocatoria_exemption.php | Excepciones por convocatoria | 443 |
| data_export.php | Exportación GDPR | 323 |
| document.php | Gestión de documentos | 832 |
| document_services.php | Servicios PDF | 374 |
| email_template.php | Plantillas email | 1,171 |
| encryption.php | Encriptación AES-256-GCM | 339 |
| exemption.php | Excepciones ISER | 587 |
| interview.php | Programación entrevistas | 675 |
| notification.php | Cola notificaciones | 317 |
| program_reviewer.php | Revisores por programa | 516 |
| review_notifier.php | Notificaciones revisión | 284 |
| reviewer.php | Asignación revisores | 388 |
| vacancy.php | CRUD vacantes | 1,089 |

#### Renderers (classes/output/ - 11 archivos)

| Renderer | Propósito | Líneas |
|----------|-----------|--------|
| renderer.php | Principal delegador | 5,796 |
| renderer_base.php | Clase base compartida | 366 |
| admin_renderer.php | Dashboard admin | 698 |
| application_renderer.php | Postulaciones | 333 |
| convocatoria_renderer.php | Convocatorias | 323 |
| dashboard_renderer.php | Dashboards por rol | 500 |
| public_renderer.php | Vista pública | 533 |
| reports_renderer.php | Reportes | 619 |
| review_renderer.php | Revisión documentos | 314 |
| vacancy_renderer.php | Vacantes | 271 |
| ui_helper.php | Utilidades HTML | 663 |

#### Formularios (classes/forms/ - 8 archivos)

| Formulario | Propósito |
|------------|-----------|
| application_form.php | Postulación multi-paso |
| convocatoria_form.php | Crear/editar convocatorias |
| doctype_form.php | Configuración tipos documento |
| email_template_form.php | Editor plantillas email |
| exemption_form.php | Gestión excepciones |
| signup_form.php | Registro IOMAD |
| updateprofile_form.php | Actualización perfil |
| vacancy_form.php | Crear/editar vacantes |

#### Módulos AMD (amd/src/ - 12 módulos)

| Módulo | Propósito |
|--------|-----------|
| application_confirm.js | Confirmación envío |
| apply_progress.js | Navegación multi-paso |
| bulk_actions.js | Operaciones masivas |
| card_actions.js | Interacciones cards |
| convocatoria_form.js | Carga AJAX departamentos |
| document_preview.js | Preview documentos |
| exemption_form.js | Selección tipos documento |
| loading_states.js | Estados de carga |
| progress_steps.js | Indicador progreso |
| public_filters.js | Filtrado AJAX público |
| signup_form.js | Cascada company/department |
| vacancy_form.js | Cascada vacante |

#### Templates (templates/ - 115 archivos)

| Directorio | Cantidad | Descripción |
|------------|----------|-------------|
| Raíz | 67 | Plantillas generales |
| components/ | 17 | Componentes reutilizables |
| pages/ | 26 | Layouts de página |
| reports/ | 5 | Plantillas de reportes |

---

## Base de Datos (28 tablas)

### Tablas Core

| Tabla | Descripción |
|-------|-------------|
| local_jobboard_convocatoria | Convocatorias con PDF adjunto |
| local_jobboard_vacancy | Vacantes por facultad |
| local_jobboard_application | Postulaciones |
| local_jobboard_document | Documentos subidos |

### Tablas de Configuración

| Tabla | Descripción |
|-------|-------------|
| local_jobboard_doctype | Tipos documento CONFIGURABLES |
| local_jobboard_vacancy_field | Campos personalizados vacante |
| local_jobboard_config | Configuración plugin |

### Tablas de Validación

| Tabla | Descripción |
|-------|-------------|
| local_jobboard_doc_validation | Validaciones con checklist |
| local_jobboard_doc_requirement | Requisitos por vacante |

### Tablas Organizacionales

| Tabla | Descripción |
|-------|-------------|
| local_jobboard_faculty | Facultades académicas |
| local_jobboard_program | Programas por facultad |
| local_jobboard_committee | Comités por facultad |
| local_jobboard_committee_member | Miembros comité |

### Tablas de Usuarios

| Tabla | Descripción |
|-------|-------------|
| local_jobboard_applicant_profile | Perfiles postulantes |
| local_jobboard_consent | Consentimientos Habeas Data |
| local_jobboard_exemption | Excepciones globales |
| local_jobboard_conv_docexempt | Excepciones por convocatoria |
| local_jobboard_program_reviewer | Revisores por programa |

### Tablas de Workflow

| Tabla | Descripción |
|-------|-------------|
| local_jobboard_workflow_log | Log transiciones estado |
| local_jobboard_audit | Auditoría completa |
| local_jobboard_notification | Cola notificaciones |

### Tablas de Email

| Tabla | Descripción |
|-------|-------------|
| local_jobboard_email_template | Plantillas email |
| local_jobboard_email_strings | Strings por idioma |

### Tablas de Entrevistas y Evaluación

| Tabla | Descripción |
|-------|-------------|
| local_jobboard_interview | Entrevistas programadas |
| local_jobboard_interviewer | Entrevistadores |
| local_jobboard_evaluation | Evaluaciones comité |
| local_jobboard_criteria | Criterios evaluación |
| local_jobboard_decision | Decisiones finales |

---

## Roles y Capabilities

### Roles del Plugin

| Rol | Shortname | Archetype | Alcance |
|-----|-----------|-----------|---------|
| Revisor Documentos | jobboard_reviewer | teacher | Por PROGRAMA |
| Coordinador Selección | jobboard_coordinator | editingteacher | Sistema |
| Miembro Comité | jobboard_committee | teacher | Por FACULTAD |

### Capabilities (28 definidas)

#### Acceso Básico
- `local/jobboard:view` - Ver job board
- `local/jobboard:viewinternal` - Ver contenido interno
- `local/jobboard:viewpublicvacancies` - Ver vacantes públicas

#### Postulante
- `local/jobboard:apply` - Aplicar a vacantes
- `local/jobboard:viewownapplications` - Ver propias postulaciones

#### Gestión Vacantes
- `local/jobboard:manage` - Gestionar job board
- `local/jobboard:createvacancy` - Crear vacantes
- `local/jobboard:editvacancy` - Editar vacantes
- `local/jobboard:deletevacancy` - Eliminar vacantes
- `local/jobboard:publishvacancy` - Publicar vacantes
- `local/jobboard:viewallvacancies` - Ver todas las vacantes

#### Convocatorias
- `local/jobboard:manageconvocatorias` - Gestionar convocatorias

#### Revisión
- `local/jobboard:review` - Revisar documentos
- `local/jobboard:validatedocuments` - Validar documentos
- `local/jobboard:reviewdocuments` - Revisar documentos
- `local/jobboard:assignreviewers` - Asignar revisores
- `local/jobboard:downloadanydocument` - Descargar documentos

#### Evaluación
- `local/jobboard:evaluate` - Evaluar candidatos
- `local/jobboard:viewevaluations` - Ver evaluaciones

#### Postulaciones
- `local/jobboard:viewallapplications` - Ver todas postulaciones
- `local/jobboard:changeapplicationstatus` - Cambiar estado

#### Workflow y Reportes
- `local/jobboard:manageworkflow` - Gestionar workflow
- `local/jobboard:viewreports` - Ver reportes
- `local/jobboard:exportreports` - Exportar reportes
- `local/jobboard:exportdata` - Exportar datos

#### Administración
- `local/jobboard:configure` - Configurar plugin
- `local/jobboard:managedoctypes` - Gestionar tipos documento
- `local/jobboard:manageemailtemplates` - Gestionar plantillas
- `local/jobboard:manageexemptions` - Gestionar excepciones

### Matriz de Permisos

| Capability | Postulante | Revisor | Coordinador | Comité | Admin |
|------------|:----------:|:-------:|:-----------:|:------:|:-----:|
| view | ✓ | ✓ | ✓ | ✓ | ✓ |
| apply | ✓ | - | - | - | ✓ |
| viewownapplications | ✓ | - | - | - | ✓ |
| reviewdocuments | - | ✓ | ✓ | - | ✓ |
| validatedocuments | - | ✓ | ✓ | - | ✓ |
| createvacancy | - | - | ✓ | - | ✓ |
| editvacancy | - | - | ✓ | - | ✓ |
| deletevacancy | - | - | - | - | ✓ |
| publishvacancy | - | - | ✓ | - | ✓ |
| manageconvocatorias | - | - | - | - | ✓ |
| evaluate | - | - | - | ✓ | ✓ |
| viewevaluations | - | - | ✓ | ✓ | ✓ |
| viewreports | - | - | ✓ | - | ✓ |
| assignreviewers | - | - | ✓ | - | ✓ |
| configure | - | - | - | - | ✓ |
| viewallapplications | - | - | ✓ | - | ✓ |

---

## Formulario de Postulación

### Estructura con Pestañas

| Pestaña | Contenido |
|---------|-----------|
| 1. Información Personal | Datos básicos del postulante |
| 2. Formación Académica | Títulos, certificaciones |
| 3. Experiencia Laboral | Historial laboral |
| 4. Documentos | Carga de archivos |
| 5. Carta de Intención | Campo de TEXTO (NO archivo) |
| 6. Revisión y Envío | Resumen y consentimientos |

### Configuración Personalizable

| Atributo | Descripción |
|----------|-------------|
| Tipo | `file` (documento) o `text` (campo) |
| input_type | file, text, textarea, select |
| required | Obligatoriedad |
| enabled | Activo/inactivo |
| sortorder | Posición en formulario |
| instructions | Texto de ayuda |

### Documentos Requeridos

1. Hoja de Vida (Formato ISER)
2. Declaración de Bienes y Rentas
3. Cédula de Ciudadanía
4. Títulos Académicos
5. Tarjeta Profesional (OPCIONAL con justificación)
6. Libreta Militar (solo hombres, exento si ≥50 años)
7. Certificados Formación Complementaria
8. Certificaciones Laborales
9. RUT
10. Certificado EPS (máx. 30 días)
11. Certificado Pensión (máx. 30 días)
12. Certificado Cuenta Bancaria
13. Antecedentes Disciplinarios (Procuraduría)
14. Antecedentes Fiscales (Contraloría)
15. Antecedentes Judiciales (Policía)
16. Registro Nacional Medidas Correctivas
17. Consulta Inhabilidades (Ley 1918/2018)
18. REDAM

### Documentos ELIMINADOS

- Tarjeta de identidad - Remover
- Campos de salario/remuneración - Eliminar

---

## Sistema CSS (prefijo jb-*)

### Política de Estilos

**REGLA FUNDAMENTAL:** El plugin NO debe usar clases Bootstrap directamente. Debe tener sistema propio con prefijo `jb-*` para independencia gráfica.

### Clases Bootstrap a ELIMINAR

| Categoría | Clases a Eliminar |
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

### Reemplazo con Clases jb-*

| Bootstrap | Reemplazo |
|-----------|-----------|
| .card | .jb-card |
| .btn-primary | .jb-btn-primary |
| .table | .jb-table |
| .badge | .jb-badge |
| .alert | .jb-alert |
| .form-control | .jb-form-control |

---

## Plantillas de Email

### Plantillas Requeridas

| Template Key | Descripción |
|--------------|-------------|
| application_received | Confirmación postulación |
| application_status_changed | Cambio de estado |
| review_complete | Revisión completada (consolidado) |
| document_approved | Documento aprobado |
| document_rejected | Documento rechazado |
| interview_scheduled | Citación entrevista |
| selected | Notificación selección |
| rejected | Notificación no selección |
| vacancy_closing_soon | Vacante próxima a cerrar |

### Variables/Placeholders

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

---

## Sistema de Auditoría

### Acciones a Registrar

| Componente | Acciones |
|------------|----------|
| Convocatoria | create, update, delete, publish, close, archive |
| Vacante | create, update, delete, publish, close |
| Postulación | create, submit, transition, withdraw |
| Documento | upload, download, approve, reject, request_correction |
| Comité | create, update, add_member, remove_member |
| Revisor | assign, revoke |
| Configuración | update_doctype, update_exemption, update_template |
| Email | sent |

### Datos a Registrar

| Campo | Descripción |
|-------|-------------|
| userid | Usuario que realizó acción |
| action | Acción realizada |
| entitytype | Tipo de entidad |
| entityid | ID de la entidad |
| previousvalue | Valor anterior (JSON) |
| newvalue | Valor nuevo (JSON) |
| extradata | Datos adicionales (JSON) |
| ipaddress | Dirección IP |
| useragent | User agent |
| timecreated | Timestamp |

---

## Interfaz de Revisión de Documentos

### Diseño Estilo mod_assign

| Característica | Descripción |
|----------------|-------------|
| Layout dividido | Panel izquierdo (documentos), derecho (información) |
| Visor inline | PDF e imágenes sin descargar |
| Navegación AJAX | Cambiar documentos/postulantes sin recarga |
| Checklist | Configurable por tipo de documento |
| Atajos teclado | A=Aprobar, R=Rechazar, N=Siguiente, P=Anterior |

### Flujo de Revisión

1. Revisor abre panel
2. Sistema muestra postulantes de SUS programas
3. Selecciona postulante → ve documentos
4. Revisa cada documento con checklist
5. Aprueba o rechaza (observación obligatoria si rechaza)
6. Al finalizar TODOS → email consolidado

---

## Reportes

### Filtro Obligatorio por Convocatoria

**TODOS** los reportes DEBEN estar filtrados por convocatoria. El usuario debe seleccionar convocatoria antes de ver cualquier reporte.

### Reportes Requeridos

| Reporte | Descripción |
|---------|-------------|
| Postulaciones | Lista con estado |
| Documentos | Estado por postulante |
| Revisores | Carga de trabajo |
| Evaluaciones | Puntuaciones comité |
| Auditoría | Log de acciones |
| Estadísticas | Métricas generales |

---

## Validación Pre-Implementación (CRÍTICO)

Antes de implementar CUALQUIER cambio, validar que no generará errores.

### Errores Comunes a Evitar

| Error | Causa | Prevención |
|-------|-------|------------|
| Unknown column 'X' | Columna no existe en BD | Verificar install.xml antes de usar |
| Table 'X' doesn't exist | Tabla no creada | Verificar install.xml |
| Duplicate entry | Registro duplicado | Verificar condiciones INSERT |
| Call to undefined method | Método no existe | Verificar clase antes de llamar |

### Protocolo Obligatorio

**PASO 1: Verificar Esquema BD**
1. Verificar campo existe en db/install.xml
2. Si es nuevo, verificar db/upgrade.php
3. Verificar versión en version.php

**PASO 2: Verificar Dependencias**
1. Verificar clase/método existe
2. Verificar parámetros esperados
3. Verificar valores retorno

**PASO 3: Validar en Plataforma**
1. Ejecutar `php admin/cli/upgrade.php`
2. Purgar caché: `php admin/cli/purge_caches.php`
3. Navegar vistas afectadas
4. Verificar sin errores

### Patrón Queries Seguras

- Usar `COALESCE(campo_nuevo, campo_alternativo)` para fallback
- Usar `LEFT JOIN` si relación puede no existir
- Verificar existencia campo antes de usar en PHP

---

## Desarrollo Pendiente

### Prioridad Alta - Errores Conocidos

Ningún error conocido en v3.2.1.

### Prioridad Media - Mejoras Funcionales

| Tarea | Descripción |
|-------|-------------|
| Interfaz revisión mod_assign | Panel lateral, preview PDF, checklist interactivo |
| Excepciones globales | Activadas por convocatoria, no por usuario |
| Preview email tiempo real | Editor con variables y preview instantáneo |
| User Tours | 15 tours con selectores jb-* |

### Prioridad Baja - Optimizaciones

| Tarea | Descripción |
|-------|-------------|
| Tests PHPUnit | Cobertura clases principales |
| Integración calendario | Eventos fechas límite |
| CLI para PDFs | Procesar PDFs grandes |

---

## PENDIENTE DE REMOCIÓN: Web Services

Los archivos de web services y API externa serán removidos:

| Archivo | Acción |
|---------|--------|
| db/services.php | Eliminar |
| classes/external/*.php | Eliminar directorio |

### Instrucciones Remoción

1. Eliminar db/services.php
2. Eliminar classes/external/ completo
3. Remover referencias en lib.php
4. Actualizar version.php
5. Ejecutar upgrade.php

---

## Fases de Implementación Pendiente

### Fase 1: User Tours
- Eliminar tours existentes
- Crear 15 tours con selectores jb-*
- Probar cada tour

### Fase 2: Interfaz Revisión
- Diseño estilo mod_assign
- Navegación sin recarga
- Atajos de teclado

### Fase 3: Excepciones Globales
- Sistema de excepciones globales
- Activación por convocatoria

### Fase 4: Preview Email
- Editor con variables
- Preview tiempo real

### Fase 5: Tests PHPUnit
- Cobertura clases principales
- CI/CD integration

---

## Instrucciones para Desarrollo

### Antes de Cualquier Cambio

1. LEER completamente archivo a modificar
2. VERIFICAR esquema BD en install.xml
3. VERIFICAR no existe funcionalidad similar
4. RESPETAR convenciones nomenclatura
5. USAR get_string() para TODA cadena
6. MANTENER paridad EN/ES
7. PROBAR en plataforma antes de commit

### Al Crear Nuevos Archivos

1. Header PHPDoc con copyright ISER
2. Namespace apropiado según ubicación
3. Seguir estándares Moodle (moodle-cs)
4. Documentar métodos públicos

### Al Modificar Templates

1. Solo clases CSS con prefijo jb-*
2. Usar {{#str}} para internacionalización
3. Documentar variables contexto
4. Probar en múltiples themes

### Al Modificar JavaScript

1. Usar módulos AMD de Moodle
2. Evitar jQuery directo
3. Compilar con grunt después de cambios

---

## Convenciones de Nomenclatura

### PHP

| Elemento | Convención | Ejemplo |
|----------|------------|---------|
| Clases | PascalCase | ApplicationRenderer |
| Métodos | snake_case | get_user_applications() |
| Variables | snake_case | $user_data |
| Constantes | SCREAMING_SNAKE | STATUS_PUBLISHED |

### JavaScript

| Elemento | Convención | Ejemplo |
|----------|------------|---------|
| Módulos | snake_case | apply_progress.js |
| Funciones | camelCase | initFormValidation() |
| Variables | camelCase | applicationData |

### CSS

| Elemento | Convención | Ejemplo |
|----------|------------|---------|
| Clases | jb-kebab-case | jb-card-header |
| IDs | jb-kebab-case | jb-main-container |

### Base de Datos

| Elemento | Convención | Ejemplo |
|----------|------------|---------|
| Tablas | local_jobboard_snake | local_jobboard_vacancy |
| Campos | snake_case | time_created |

---

## Control de Versiones

### Formato

```php
$plugin->version = YYYYMMDDXX;  // Ej: 2025121241
$plugin->release = 'X.Y.Z';     // Ej: '3.2.1'
```

### Incrementos

| Tipo Cambio | version | release |
|-------------|---------|---------|
| Corrección typo | +1 | No cambia |
| Nueva string | +1 | No cambia |
| Bug fix | +1 | +0.0.1 |
| Funcionalidad menor | +1 | +0.1.0 |
| Funcionalidad mayor | +1 | +1.0.0 |
| Cambio BD | +1 | +0.1.0 |

---

## Comandos Útiles

### Moodle CLI

| Comando | Propósito |
|---------|-----------|
| `php admin/cli/upgrade.php` | Ejecutar migraciones BD |
| `php admin/cli/purge_caches.php` | Limpiar caché |

### Desarrollo

| Comando | Propósito |
|---------|-----------|
| `grunt amd --root=local/jobboard` | Compilar JavaScript AMD |
| `php -l archivo.php` | Verificar sintaxis PHP |
| `php admin/tool/phpcs/cli/run.php --standard=moodle local/jobboard` | Validar estándares |

### CLI del Plugin

| Comando | Propósito |
|---------|-----------|
| `php local/jobboard/cli/cli.php --help` | Ver ayuda importador |
| `php local/jobboard/cli/cli.php --action=list-centers` | Listar centros |
| `php local/jobboard/cli/cli.php --action=create-vacancies --convocatoria-id=5` | Crear vacantes |

---

## Reglas Absolutas para Agentes

1. **ANALIZAR** repositorio completo antes de implementar
2. **SOLO CLASES jb-*** - No usar Bootstrap directamente
3. **VALIDAR SIEMPRE** en plataforma antes de commit
4. **NO improvisar** cambios en producción
5. **Respetar** arquitectura IOMAD 4 niveles
6. **Paridad EN/ES** - Toda string en AMBOS idiomas
7. **NO hardcodear** strings - usar get_string() SIEMPRE
8. **Documentar** TODO en CHANGELOG.md
9. **Comité** es por FACULTAD, no por vacante
10. **Revisores** se asignan por PROGRAMA
11. **Formulario** es PERSONALIZABLE desde admin
12. **Carta intención** es campo TEXTO, no archivo
13. **Convocatoria** debe tener PDF adjunto
14. **Auditoría ROBUSTA** - registrar TODAS acciones
15. **Un postulante** = UNA vacante por convocatoria
16. **Validación documentos** es 100% MANUAL
17. **Búsqueda usuarios** por username en comités
18. **Cada cambio** = incremento versión + CHANGELOG
19. **Compilar AMD** después de modificaciones
20. **Reportes** filtrados por convocatoria obligatoriamente
21. **Vacantes** NO tienen fechas propias - heredan de convocatoria
22. **Excepciones** son GLOBALES, activadas por convocatoria
23. **VERIFICAR** esquema BD antes de escribir queries
24. **USAR** COALESCE para compatibilidad hacia atrás

### Archivos Críticos - No Modificar Sin Revisión

- `db/install.xml` - Esquema BD (requiere upgrade.php)
- `db/access.php` - Capabilities (afecta permisos)
- `version.php` - Versión (afecta upgrades)
- `lib.php` - Funciones core (afecta navegación)

---

## Cumplimiento Normativo

### GDPR / Habeas Data (Colombia)

- Consentimiento explícito tratamiento datos
- Derecho acceso, rectificación, eliminación
- Privacy API Moodle implementada
- Tabla `local_jobboard_consent`

### Normativa Colombiana

- Excepciones libreta militar ≥50 años
- Documentos antecedentes oficiales
- Requisitos MEN para docentes

---

## Compatibilidad

### Moodle
- Mínimo: 4.1 (2022112800)
- Máximo probado: 4.5

### PHP
- Mínimo: 7.4
- Recomendado: 8.1+

### Themes Compatibles
- Boost (default)
- Classic
- Remui
- Flavor

### IOMAD
- Integración completa multi-tenant
- Filtrado company/department

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
*Documento consolidado de requerimientos del proyecto*
