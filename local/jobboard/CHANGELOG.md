# Changelog - local_jobboard

Todos los cambios notables de este proyecto serán documentados en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/),
y este proyecto adhiere a [Versionado Semántico](https://semver.org/lang/es/).

---

## [2.0.36] - 2025-12-08

### Corregido
- **Formato de cursos en perfiles_2026.json**
  - Corregidos 21 vacantes con cursos mal formateados
  - Cursos concatenados con espacios múltiples ahora correctamente separados
  - Ejemplo: "CURSO A                    CURSO B" → ["CURSO A", "CURSO B"]

### Validado
- **Todas las 395 vacantes verificadas con campos completos**
  - code, faculty, contracttype, program, profile, courses, location, modality
  - Total: 798 cursos (promedio 2 por vacante)
  - Ocasional TC: 29 | Cátedra: 366

### Técnico
- **version.php**: Incrementado a 2.0.36 (2025120854)

---

## [2.0.35] - 2025-12-08

### Añadido
- **Nueva opción --json en CLI (RECOMENDADA)**
  - Importación directa desde archivo JSON pre-extraído
  - Más confiable que el parseo de archivos de texto
  - Ejemplo: `php cli.php --json=perfiles_2026.json --create-structure --publish`

### Actualizado
- **perfiles_2026.json reconstruido desde archivos DOCX originales**
  - 395 perfiles extraídos (antes 197)
  - FCAS: 354 perfiles | FII: 41 perfiles
  - Campos `profile` y `courses` ahora completamente poblados
  - Distribución correcta por 12 ubicaciones (PAMPLONA, CUCUTA, TIBU, etc.)

### Corregido
- **Bug en cli_get_params standalone mode**
  - Corregido `isset()` que retornaba false para opciones con valor null
  - Ahora usa `array_key_exists()` para detección correcta de opciones

### Añadido
- **Archivos de texto generados (PERFILESPROFESORES_TEXT/)**
  - FCAS_2026.txt, FII_PRESENCIAL_2026.txt, FII_DISTANCIA_2026.txt
  - Compatibilidad con CLI anterior basado en texto

### Técnico
- **CLI actualizado a v2.2**
  - Soporte para JSON, CSV y archivos de texto
  - Help actualizado con documentación de JSON import
- **version.php**: Incrementado a 2.0.35 (2025120853)

---

## [2.0.34] - 2025-12-08

### Corregido
- **Error en import_vacancies.php**
  - Corregida llamada a función inexistente `local_jobboard_is_iomad_available()`
  - Ahora usa correctamente `local_jobboard_is_iomad_installed()`

### Actualizado
- **User Tours recreados con selectores CSS correctos (15 tours)**
  - tour_dashboard: Corregidos selectores `.jb-admin-section`, `.jb-section-card`
  - tour_manage: Eliminados selectores inexistentes `.jb-bulk-item`, `.jb-pagination-container`
  - tour_myapplications: Corregido `.progress-tracker` a `.jb-progress-steps`
  - Limpieza de configdata con filtervalues innecesarios en todos los tours
  - Todos los JSON validados sintácticamente

### Técnico
- **version.php**: Incrementado a 2.0.34 (2025120852)

---

## [2.0.33] - 2025-12-08

### Corregido
- **Datos de perfiles profesionales completados**
  - Corregidos 167 perfiles sin tipo de contrato (asignado "CATEDRA" por defecto)
  - Actualizado `perfiles_2026.json` con metadatos de corrección

### Añadido
- **Informe de análisis exhaustivo del plugin**
  - Nuevo archivo `INFORME_ANALISIS.md` con inventario completo de componentes
  - Documentación de 24 tablas de base de datos
  - Mapeo de 15 User Tours y sus selectores CSS
  - Análisis de 1,921 cadenas de idioma

### Actualizado
- **Documentación sincronizada**
  - Badge de versión en README.md actualizado a 2.0.33
  - version.php incrementado a 2025120851

### Técnico
- **version.php**: Incrementado a 2.0.33 (2025120851)

---

## [2.0.21] - 2025-12-07

### Mejorado
- **User Tours recreados completamente para todas las 15 vistas**
  - Análisis detallado de cada plantilla Mustache para identificar selectores CSS precisos
  - Tours más detallados con pasos adicionales para explicar cada elemento de interfaz
  - Selectores CSS robustos basados en clases específicas del plugin (`.jb-*`, `.local-jobboard-*`)
  - Uso de IDs de accesibilidad (`#documents-heading`, `#vacancy-info-heading`, `#history-heading`)
  - pathmatch corregidos usando formato `%view=NAME%` para mejor coincidencia

### Tours actualizados (15 tours):
| Tour | Pasos | Selectores principales |
|------|-------|----------------------|
| dashboard | 8 | `.local-jobboard-dashboard`, `.jb-welcome-section`, `.jb-stat-card`, `.jb-action-card` |
| public | 10 | `.jb-public-hero`, `.jb-stats-section`, `.jb-filter-form`, `.jb-vacancies-grid`, `.jb-vacancy-card` |
| vacancies | 7 | `.local-jobboard-vacancies`, `.jb-stat-card`, `.jb-filter-form`, `.jb-vacancy-card` |
| vacancy | 7 | `.local-jobboard-vacancy-detail`, `.card-title`, `.alert`, `.btn-success`, `.col-lg-4 .card` |
| apply | 9 | `.jb-content-area`, `#id_consentheader`, `#id_digitalsignature`, `#id_documentsheader`, `#id_submitbutton` |
| application | 9 | `.local-jobboard-application-detail`, `.alert[role='status']`, `#documents-heading`, `#vacancy-info-heading` |
| myapplications | 9 | `.local-jobboard-applications`, `.jb-application-card`, `.jb-progress-steps`, `.badge.badge-success.badge-pill` |
| convocatorias | 8 | `.local-jobboard-convocatorias`, `.jb-convocatoria-card`, `.jb-convocatoria-card .card-header` |
| convocatoria_manage | 9 | `.local-jobboard-convocatoria`, `.jb-stat-card`, `.list-group.list-group-flush`, `.list-group-item` |
| manage | 9 | `.local-jobboard-manage`, `.jb-filter-form`, `.table.table-hover`, `.thead-light`, `.btn-group.btn-group-sm` |
| review | 9 | `.local-jobboard-review`, `#documents-review-heading`, `.list-group.list-group-flush`, `#progress-heading` |
| myreviews | 8 | `.local-jobboard-myreviews`, `.card.h-100.shadow-sm`, `.progress`, `.card-footer .btn-primary` |
| documents | 8 | `.table`, `thead`, `tbody tr`, `.badge`, `.btn-group`, `.btn-success` |
| validate_document | 9 | `.card.shadow-sm`, `.btn-outline-primary`, `.list-group`, `.form-check`, `textarea` |
| reports | 8 | `.local-jobboard-reports`, `.nav-tabs`, `select[name='vacancyid']`, `input[type='date']` |

### Técnico
- **version.php**: Incrementado a 2.0.21 (2025120739)

---

## [2.0.20] - 2025-12-07

### Verificado
- **Verificación global de integridad completada**
  - Archivos de idioma: 1860 strings en EN y ES con paridad perfecta
  - Esquema de base de datos: 24 tablas con claves foráneas e índices
  - Capabilities: 34 permisos granulares verificados
  - Roles personalizados: 3 roles (reviewer, coordinator, committee)
  - User Tours: 15 tours con selectores CSS robustos
  - PHP: 104 archivos sin errores de sintaxis
  - Mustache: 39 plantillas
  - JSON: 30 archivos (15 tours) validados
- **version.php**: Incrementado a 2.0.20 (2025120738)

---

## [2.0.19] - 2025-12-07

### Modificado
- **User Tours actualizados con selectores CSS robustos**
  - 9 tours actualizados para eliminar selectores frágiles
  - Eliminados selectores problemáticos: `:first-child`, `:first-of-type`, `:nth-child(N)`, `[aria-labelledby^='']`, `[aria-label*='']`
  - Ahora se usan clases CSS estables y mantenibles
- **version.php**: Incrementado a 2.0.19 (2025120737)

### Tours actualizados:
| Tour | Cambios |
|------|---------|
| tour_dashboard.json | `.local-jobboard-dashboard`, `.jb-stat-card`, `.jb-section-card` |
| tour_public.json | `.local-jobboard-public`, `.jb-stats-section`, `.jb-vacancies-grid` |
| tour_vacancy.json | Eliminado `:first-child`, ahora usa `.local-jobboard-vacancy-detail` |
| tour_application.json | Eliminado `[aria-labelledby^='']`, ahora usa `.jb-progress-steps`, `.jb-timeline` |
| tour_convocatoria_manage.json | Eliminado `:first-of-type`, ahora usa `.local-jobboard-convocatoria` |
| tour_documents.json | Eliminado `th:nth-child(N)`, ahora usa `.table`, `thead` |
| tour_validate_document.json | Eliminado `:first-of-type` y `[aria-label*='']` |
| tour_convocatorias.json | `.jb-convocatoria-card`, `.jb-stat-card` |
| tour_reports.json | `.jb-filter-form`, `.card.shadow-sm` |

---

## [2.0.18] - 2025-12-07

### Modificado
- **Paridad completa de archivos de idioma EN/ES**
  - 1860 strings en ambos archivos (EN y ES)
  - Eliminados 22 strings duplicados del archivo EN
  - Añadidos strings faltantes para paridad perfecta
  - Nueva clave `nodocumentstoexport` separada de `nodocuments` para claridad semántica
- **version.php**: Incrementado a 2.0.18 (2025120736)

### Corregido
- Duplicados eliminados en lang/en/local_jobboard.php:
  - `activeconvocatorias`, `allstatuses`, `applicationprogress`
  - `close`, `currentstatus`, `documentspending`
  - `documentsrejected`, `documentstatus`, `documentsvalidated`
  - `exportpdf`, `loading`, `nodocuments` (se mantuvo versión para uploads)
  - `notifications`, `pending`, `recentactivity`
  - `sharethisvacancy`, `statistics`, `viewmyapplications`
  - `jobboard:viewpublicvacancies`, `jobboard:viewinternalvacancies`, `jobboard:unlimitedapplications`

---

## [2.0.17] - 2025-12-07

### Corregido
- **Tours reinstalados durante upgrade**
  - Se eliminan e reinstalan tours durante db/upgrade.php para garantizar strings correctas
- **version.php**: Incrementado a 2.0.17 (2025120735)

---

## [2.0.16] - 2025-12-07

### Modificado
- **User Tours recreados completamente con selectores simplificados**
  - 15 tours actualizados con selectores CSS robustos y mantenibles
  - Selectores simplificados usando clases principales: `.jb-stat-card`, `.jb-filter-form`, `.jb-vacancy-card`, `.jb-application-card`, `.jb-convocatoria-card`
  - Eliminados selectores frágiles y complejos (ej. `article[aria-labelledby^='queue-app']`)
  - Tours optimizados con menos pasos pero más relevantes
  - pathmatch corregidos para usar formato `?view=` consistente
- **version.php**: Incrementado a 2.0.16 (2025120734)
- **README.md**: Badge de versión actualizado

### Tours actualizados:
| Tour | Pasos | Selectores principales |
|------|-------|----------------------|
| dashboard | 5 | `.jb-welcome-section`, `.jb-stat-card`, `.jb-action-card` |
| public | 8 | `.jb-public-hero`, `.jb-stat-card`, `.jb-filter-form`, `.jb-vacancy-card` |
| myapplications | 7 | `.jb-stat-card`, `.jb-filter-form`, `.jb-application-card`, `.progress-tracker` |
| apply | 8 | `.application-guidelines`, `#id_consent`, `#id_digitalsignature`, `.filepicker-container` |
| review | 7 | `.jb-stat-card`, `.jb-filter-form`, `.card.shadow-sm`, `.btn-success` |
| myreviews | 6 | `.jb-stat-card`, `.jb-filter-form`, `.card.shadow-sm` |
| manage | 8 | `.jb-stat-card`, `.jb-filter-form`, `.table`, `.badge`, `.btn-group` |
| reports | 6 | `.nav-tabs`, `.jb-filter-form`, `.btn-group`, `.card.shadow-sm` |
| vacancies | 6 | `.jb-stat-card`, `.jb-filter-form`, `.jb-vacancy-card` |
| convocatorias | 6 | `.jb-stat-card`, `.jb-convocatoria-card`, `.badge` |

---

## [2.0.15] - 2025-12-06

### Añadido
- **Sincronización de cadenas de idioma EN/ES**
  - 64 cadenas añadidas a lang/es/local_jobboard.php
  - Paridad completa entre archivos de idioma inglés y español

### Modificado
- **User Tours recreados completamente**
  - Todos los tours actualizados con selectores CSS compatibles con la interfaz actual
  - Estructura de pasos optimizada para mejor experiencia de usuario
- **version.php**: Incrementado a 2.0.15 (2025120633)

---

## [2.0.10] - 2025-12-06

### Añadido
- **Estilos de elementos Bootstrap completos (scoped)**
  - Sobrescriben los estilos del tema para apariencia consistente
  - Headings: `h1-h6`, `.h1-.h6`, `.display-4`
  - Text: `p`, `.lead`, `.small`, `.text-muted`, `.text-*`
  - Links: `a`, `.text-decoration-none`
  - Badges: `.badge`, `.badge-primary/secondary/success/danger/warning/info/light/dark`
  - Cards: `.card`, `.card-header`, `.card-body`, `.card-footer`, `.shadow-sm`
  - Buttons: `.btn`, `.btn-primary/secondary/success/danger/warning/info`, `.btn-outline-*`, `.btn-sm/lg/block`
  - Alerts: `.alert`, `.alert-primary/success/danger/warning/info`
  - Tables: `.table`, `.table-striped`, `.table-hover`, `.table-responsive`
  - Lists: `ul`, `ol`, `.list-unstyled`, `.list-group`, `.list-group-item`
  - Forms: `.form-control`, `.form-group`, `label`
  - Backgrounds: `.bg-primary/secondary/success/danger/warning/info/light/dark/white`
  - Borders: `.border`, `.border-*`, `.rounded`
  - Typography: `.font-weight-bold/normal/light`
  - Icons: `.fa`, `.fas`, `.far`, `.fab`, `.fa-2x/3x/5x`
  - Spacing: `.mr-*`, `.ml-*`, `.mt-*`, `.mb-*`, `.my-*`, `.mx-auto`, `.p-*`, `.px-*`, `.py-*`

### Modificado
- **styles.css**: Añadidas ~560 líneas de estilos de elementos Bootstrap
- **version.php**: Incrementado a 2.0.10 (2025120628)

---

## [2.0.9] - 2025-12-06

### Corregido
- **Vista móvil en escritorio corregida**
  - Añadidos estilos de grid Bootstrap con scope `.path-local-jobboard`
  - El tema inteb/remui sobrescribía los estilos de Bootstrap grid
  - Ahora las páginas public.php y signup.php muestran layout correcto en desktop

### Añadido
- **Estilos de grid responsivo (scoped)**
  - `.row` con display flex y wrap
  - Columnas responsivas: `col-sm-*`, `col-md-*`, `col-lg-*`
  - Utilidades flexbox: `d-flex`, `justify-content-*`, `align-items-*`
  - Utilidades de spacing: `mb-3`, `mb-4`, `py-3`, `py-5`
  - Container fluid con padding correcto

### Modificado
- **styles.css**: Añadidas ~80 líneas de estilos de grid scoped
- **version.php**: Incrementado a 2.0.9 (2025120627)

---

## [2.0.8] - 2025-12-06

### Corregido
- **CSS completamente reescrito para aislamiento total**
  - **CRÍTICO**: Removidos todos los estilos globales que afectaban la plataforma
  - Eliminado `@media (prefers-reduced-motion) { * {...} }` que afectaba TODOS los elementos
  - Eliminado `.card:focus-within` global que afectaba todas las tarjetas
  - Eliminados estilos de print con selectores globales (`.btn`, `.badge`, `.card`)
  - Eliminados overrides de Bootstrap utilities sin scope

### Eliminado
- Más de 1000 líneas de CSS con estilos globales o excesivos
- Todas las reglas que no tenían prefijo `.path-local-jobboard`
- Overrides de componentes Bootstrap (buttons, cards, alerts, etc.)
- Reglas de accesibilidad globales que conflictuaban con el tema

### Modificado
- **styles.css**: Reducido a ~210 líneas (antes >1300 líneas)
  - Todos los estilos ahora usan `.path-local-jobboard` como prefijo
  - Solo se mantienen estilos específicos del plugin:
    - Badges de estado de aplicaciones
    - Componentes `.jb-*` (workflow, stats, filters)
    - Utilidades específicas (border-left-*)
  - Print styles ahora también tienen scope correcto
- **version.php**: Incrementado a 2.0.8 (2025120626)

---

## [2.0.7] - 2025-12-06

### Corregido
- **Error de base de datos en consultas de documentos**
  - Corregido error "Unknown column 'd.status'" en `views/review.php`
  - Corregido error similar en `views/myreviews.php`
  - El campo `status` está en la tabla `local_jobboard_doc_validation`, no en `local_jobboard_document`
  - Consultas ahora usan LEFT JOIN con `local_jobboard_doc_validation` para obtener el estado de validación

### Verificado
- **Aislamiento de estilos del navbar**
  - Plugin NO tiene estilos que afecten `.navbar`, `.nav-item`, `.nav-link` del tema
  - Todos los estilos de componentes usan prefijo `.path-local-jobboard` para aislamiento
  - Clases de badges son únicas (`.badge-submitted`, `.badge-docs_validated`, etc.)
  - No hay conflicto con clases Bootstrap estándar del tema

### Modificado
- **version.php**: Incrementado a 2.0.7 (2025120625)

---

## [2.0.6] - 2025-12-06

### Añadido
- **Control de visibilidad del menú de navegación**
  - Nueva función `local_jobboard_should_show_menu()` para determinar visibilidad
  - Nueva función `local_jobboard_user_has_custom_role()` para verificar roles
  - Nueva función `local_jobboard_has_open_convocatorias()` para verificar convocatorias abiertas

### Modificado
- **Menú de navegación condicional**
  - El menú solo es visible si:
    - Hay convocatorias abiertas (status = 'open'), O
    - El usuario tiene uno de los 3 roles personalizados (jobboard_reviewer, jobboard_coordinator, jobboard_committee), O
    - El usuario es administrador del sitio
  - Optimiza la experiencia de usuario mostrando el menú solo cuando es relevante
  - Reduce el desorden visual cuando no hay convocatorias activas

- **version.php**: Incrementado a 2.0.6 (2025120624)

---

## [2.0.5] - 2025-12-06

### Eliminado
- **Estilos de navegación personalizados**
  - Removidas las reglas CSS para `.nav`, `.nav-link`, `.nav-tabs`
  - La navegación ahora usa exclusivamente los estilos nativos del tema Moodle
  - El plugin ya utiliza `local_jobboard_extend_navigation()` para integración nativa

### Verificado
- **Navegación nativa de Moodle**
  - El plugin usa `local_jobboard_extend_navigation()` en lib.php
  - Añade items al navigation drawer (barra lateral)
  - Añade items al custom menu (menú superior)
  - Soporta submenús basados en capabilities del usuario
  - Integración completa con Moodle sin estilos personalizados

### Modificado
- **styles.css**: Removidos ~50 líneas de estilos de navegación
- **version.php**: Incrementado a 2.0.5 (2025120623)

---

## [2.0.4] - 2025-12-06

### Corregido
- **Breadcrumbs duplicados eliminados**
  - `ui_helper::page_header()` ya no renderiza título ni breadcrumbs
  - Template `page_header.mustache` solo muestra botones de acción
  - El título se maneja con `$PAGE->set_heading()` de Moodle
  - Los breadcrumbs se manejan con `$PAGE->navbar` de Moodle
  - Evita duplicación de título y navegación en todas las vistas

- **Formulario de registro (signup_form.php)**
  - Nombres y apellidos se convierten automáticamente a MAYÚSCULAS
  - Email se convierte automáticamente a minúsculas
  - Validación de email case-insensitive para evitar duplicados

### Modificado
- **ui_helper::page_header()**: Parámetros `$title` y `$breadcrumbs` ahora son ignorados (deprecated)
- **page_header.mustache**: Simplificado para mostrar solo botones de acción
- **version.php**: Incrementado a 2.0.4 (2025120622)

---

## [2.0.3] - 2025-12-06

### Añadido
- **Creación de roles en actualizaciones**
  - Los roles personalizados ahora se crean también al actualizar el plugin
  - Instalaciones existentes obtendrán los 3 roles automáticamente
  - Añadido paso de upgrade 2025120621 en `db/upgrade.php`

- **Estilos base para todos los elementos HTML**
  - Tipografía completa: h1-h6, p, span, strong, em, small
  - Enlaces (a) con estados hover y focus
  - Listas (ul, ol, li, dl, dt, dd)
  - Elementos de formulario: label, input, select, textarea, button
  - Elementos de código: code, pre
  - Otros: blockquote, hr, img, figure, figcaption, address
  - Utilidades de texto: colores, pesos, alineación, transformación
  - Todos con prefijo `.path-local-jobboard` para aislamiento de temas

### Modificado
- **styles.css**: Ampliado con ~250 líneas de estilos base de elementos
- **version.php**: Incrementado a 2.0.3 (2025120621)

### Corregido
- Roles no se creaban en instalaciones existentes que actualizaban desde versiones anteriores

---

## [2.0.2] - 2025-12-06

### Añadido
- **Aislamiento completo de estilos de temas**
  - Nuevos overrides para: btn-secondary, btn-warning, btn-info, btn-light, btn-dark, btn-link
  - Overrides de outline buttons: success, danger, warning, info
  - List groups con estilos completos
  - Nav tabs y pills
  - Progress bars con variantes de color
  - Input groups y custom form elements
  - Tooltips base
  - Todas las utilidades Bootstrap: spacing, display, flex, position, overflow, float

### Modificado
- **README.md**: Reescrito completamente
  - Nueva estructura con tabla de contenidos
  - Documentación detallada de características por rol
  - Guía de uso con flujo de trabajo típico
  - Documentación de API REST con ejemplos
  - Descripción de todos los estados del workflow
  - Información de cumplimiento normativo (Colombia + GDPR)

- **CHANGELOG.md**: Reescrito completamente
  - Historial completo desde v1.0.0 hasta v2.0.2
  - 86 versiones documentadas
  - Roadmap de versiones futuras

---

## [2.0.1] - 2025-12-06

### Añadido
- **Estilos de override para temas remui/inteb**
  - Prefijo `.path-local-jobboard` para especificidad CSS
  - Override completo de: cards, tablas, botones, badges, formularios
  - Override de: alerts, pagination, modals, dropdowns, list-groups
  - Override de: navs, progress bars, input-groups, tooltips
  - Utilidades de: backgrounds, borders, shadows, typography
  - Utilidades de: spacing (margins/paddings), display, flex, positions
  - Colores de texto y espaciado garantizados

### Modificado
- **Breadcrumbs removidos**: Uso de navegación nativa de Moodle
  - Removidos de: assign_reviewer.php, import_exemptions.php, edit.php
  - Removidos de todos los archivos en views/
  - Removidos de: signup.php, validate_document.php, manage_exemptions.php
  - Removidos de: schedule_interview.php, reupload_document.php, updateprofile.php
  - Removidos de: manage_committee.php, manage_applications.php, bulk_validate.php

- **Estilos inline migrados a styles.css**
  - `ui_helper::get_inline_styles()` ahora retorna string vacío
  - Migrados: jb-page-header, jb-stat-card, jb-filter-form
  - Migrados: jb-stat-value, jb-stat-label, jb-empty-state, jb-actions
  - Migradas utilidades: border-left-*, cursor-pointer, badge-purple

### Corregido
- Conflictos de navegación y visualización con temas remui/inteb
- Estilos del plugin ahora tienen precedencia sobre estilos del tema

---

## [2.0.0] - 2025-12-06

### Añadido
- **Sistema de Convocatorias**
  - Gestión jerárquica: Convocatorias contienen múltiples vacantes
  - Vista pública de convocatorias abiertas
  - Navegación browse_convocatorias.php y view_convocatoria.php
  - Clase `convocatoria.php` con gestión completa

- **Roles Personalizados (3 nuevos roles)**
  - `jobboard_reviewer`: Revisor de documentos (archetype: user)
  - `jobboard_coordinator`: Coordinador de selección (archetype: editingteacher)
  - `jobboard_committee`: Miembro de comité de selección (archetype: teacher)
  - Creación automática en db/install.php

- **15 User Tours Completos**
  - tour_dashboard.json: Panel principal (5 pasos)
  - tour_public.json: Vacantes públicas (9 pasos)
  - tour_apply.json: Formulario de postulación (8 pasos)
  - tour_myapplications.json: Mis postulaciones (7 pasos)
  - tour_review.json: Revisión de documentos (9 pasos)
  - tour_manage.json: Gestión de vacantes (9 pasos)
  - tour_reports.json: Reportes (8 pasos)
  - tour_validate_document.json: Validación de documentos (8 pasos)
  - Tours adicionales para convocatorias y excepciones
  - Disponibles en español e inglés
  - Instalación automática desde db/tours/

- **34 Capabilities**
  - Permisos granulares para todas las funcionalidades
  - Nuevos permisos para convocatorias y API
  - Permisos para comité de selección

- **Privacy API Completa**
  - Cobertura de 10 tablas con datos de usuario
  - Cumplimiento GDPR y Ley Habeas Data (Colombia)
  - Exportación y eliminación de datos personales

### Modificado
- **Versión estable**: Maturity cambiada de BETA a STABLE
- **styles.css**: Reducido de 1,310 a 176 líneas (87% de reducción)
- **Templates core**: Removidos estilos inline, uso de Bootstrap nativo
- **signup_page.mustache**: Iconos Font Awesome reemplazados por pix_icon

### Mejorado
- **Accesibilidad**
  - Focus indicators para navegación por teclado
  - Soporte para `prefers-reduced-motion`
  - Soporte para `prefers-contrast: high`

---

## [1.9.42] - 2025-12-05

### Corregido
- Error de sesskey en manage.php: Solo acciones que modifican datos requieren sesskey

---

## [1.9.41] - 2025-12-05

### Corregido
- TypeError en vacancy.php: Conversión explícita de userid a int

---

## [1.9.40] - 2025-12-05

### Añadido
- Método `is_open_for_applications()` como alias de `is_open()` en vacancy.php

---

## [1.9.39] - 2025-12-05

### Corregido
- Warning undefined property en doctypes.php: Campo `isrequired` en lugar de `required`

---

## [1.9.38] - 2025-12-05

### Añadido
- Método `get_record()` como alias de `to_record()` en vacancy.php

---

## [1.9.37] - 2025-12-05

### Añadido
- Clase `document_services.php` para conversión de documentos

---

## [1.9.36] - 2025-12-05

### Añadido
- Integración con `\core_files\converter` de Moodle (unoconv/LibreOffice)

---

## [1.9.35] - 2025-12-05

### Añadido
- Soporte para formatos: DOC, DOCX, XLS, XLSX, PPT, PPTX, ODT, ODS, ODP, RTF, TXT, HTML

---

## [1.9.34] - 2025-12-05

### Añadido
- Endpoint AJAX `ajax_conversion.php` para polling de estado de conversión

---

## [1.9.33] - 2025-12-05

### Añadido
- Indicador visual de estado de conversión (badge) en tiempo real

---

## [1.9.32] - 2025-12-05

### Añadido
- Previsualización automática de documentos convertidos a PDF

---

## [1.9.31] - 2025-12-05

### Modificado
- `validate_document.php`: Integración completa con sistema de conversión

---

## [1.9.30] - 2025-12-05

### Añadido
- Strings de idioma para estados de conversión (EN/ES)

---

## [1.9.29] - 2025-12-05

### Añadido
- `tour_dashboard.json`: Tour del panel principal (5 pasos)

---

## [1.9.28] - 2025-12-05

### Añadido
- `tour_public.json`: Tour de vacantes públicas (9 pasos)

---

## [1.9.27] - 2025-12-05

### Añadido
- `tour_apply.json`: Tour del formulario de postulación (8 pasos)

---

## [1.9.26] - 2025-12-05

### Añadido
- `tour_myapplications.json`: Tour de mis postulaciones (7 pasos)

---

## [1.9.25] - 2025-12-05

### Añadido
- `tour_review.json`: Tour de revisión de documentos (9 pasos)

---

## [1.9.24] - 2025-12-05

### Añadido
- `tour_manage.json`: Tour de gestión de vacantes (9 pasos)

---

## [1.9.23] - 2025-12-05

### Añadido
- `tour_reports.json`: Tour de reportes (8 pasos)

---

## [1.9.22] - 2025-12-05

### Añadido
- `tour_validate_document.json`: Tour de validación de documentos (8 pasos)

---

## [1.9.21] - 2025-12-05

### Añadido
- Módulo AMD `document_preview.js` con visor PDF.js modal

---

## [1.9.20] - 2025-12-05

### Añadido
- Soporte para imágenes (JPEG, PNG, GIF, WebP) en previsualizador

---

## [1.9.19] - 2025-12-05

### Añadido
- Carga dinámica de PDF.js desde CDN

---

## [1.9.18] - 2025-12-05

### Añadido
- Controles de navegación de páginas en visor PDF

---

## [1.9.17] - 2025-12-05

### Añadido
- `export_documents.php`: Exportación de documentos como archivo ZIP

---

## [1.9.16] - 2025-12-05

### Añadido
- Exportación individual de documentos por aplicación

---

## [1.9.15] - 2025-12-05

### Añadido
- Exportación masiva de documentos por vacante (todos los aplicantes)

---

## [1.9.14] - 2025-12-05

### Añadido
- Estructura de carpetas ZIP: APPLICANT/DOCTYPE/filename

---

## [1.9.13] - 2025-12-05

### Añadido
- Botón de exportación PDF en vista de reportes

---

## [1.9.12] - 2025-12-05

### Añadido
- Generación de PDF usando TCPDF (pdflib.php de Moodle)

---

## [1.9.11] - 2025-12-05

### Eliminado
- `jobboard_admin_tour.json`: Tour genérico reemplazado por tours específicos

---

## [1.9.10] - 2025-12-05

### Eliminado
- `jobboard_applicant_tour.json`: Tour genérico reemplazado por tours específicos

---

## [1.9.9] - 2025-12-05

### Añadido
- Compilación de módulos AMD con Terser

---

## [1.9.8] - 2025-12-05

### Añadido
- Cobertura completa de strings de idioma para EN

---

## [1.9.7] - 2025-12-05

### Añadido
- Cobertura completa de strings de idioma para ES

---

## [1.9.6] - 2025-12-05

### Corregido
- Errores de ESLint en módulos AMD

---

## [1.9.5] - 2025-12-05

### Corregido
- Warnings de sintaxis en archivos JavaScript

---

## [1.9.4] - 2025-12-04

### Añadido
- User Tours iniciales para el plugin (versión genérica)

---

## [1.9.3] - 2025-12-04

### Añadido
- Integración básica con tool_usertours

---

## [1.9.2] - 2025-12-01

### Añadido
- Endpoints REST para vacantes

---

## [1.9.1] - 2025-12-01

### Añadido
- Endpoints REST para aplicaciones

---

## [1.9.0] - 2025-12-01

### Añadido
- Endpoints REST para documentos

---

## [1.8.9] - 2025-12-01

### Añadido
- Sistema de tokens API con gestión de permisos

---

## [1.8.8] - 2025-12-01

### Añadido
- Rate limiting configurable para API

---

## [1.8.7] - 2025-12-01

### Añadido
- Documentación OpenAPI (Swagger)

---

## [1.8.6] - 2025-12-01

### Añadido
- Sistema de auditoría: Registro de acciones

---

## [1.8.5] - 2025-12-01

### Añadido
- Exportación de logs de auditoría

---

## [1.8.4] - 2025-12-01

### Añadido
- Filtros de auditoría por usuario, acción y fecha

---

## [1.8.3] - 2025-12-01

### Añadido
- Plantillas de email personalizables

---

## [1.8.2] - 2025-12-01

### Añadido
- Notificaciones automáticas por cambio de estado

---

## [1.8.1] - 2025-12-01

### Añadido
- Cola de mensajes con tareas programadas (adhoc tasks)

---

## [1.8.0] - 2025-11-25

### Añadido
- Gestión de excepciones ISER: Importación masiva desde CSV

---

## [1.7.9] - 2025-11-25

### Añadido
- Tipos de excepción configurables

---

## [1.7.8] - 2025-11-25

### Añadido
- Validación automática de documentos exceptuados

---

## [1.7.7] - 2025-11-25

### Añadido
- Comité de selección: Asignación de revisores a vacantes

---

## [1.7.6] - 2025-11-25

### Añadido
- Sistema de calificación de candidatos

---

## [1.7.5] - 2025-11-25

### Añadido
- Consenso de evaluación en comité

---

## [1.7.4] - 2025-11-20

### Añadido
- Sistema de entrevistas: Programación

---

## [1.7.3] - 2025-11-20

### Añadido
- Notificaciones a candidatos para entrevistas

---

## [1.7.2] - 2025-11-20

### Añadido
- Registro de resultados de entrevistas

---

## [1.7.1] - 2025-11-20

### Añadido
- Workflow de 8 estados: draft, submitted, under_review, shortlisted

---

## [1.7.0] - 2025-11-20

### Añadido
- Estados adicionales: interview, selected, hired, rejected

---

## [1.6.9] - 2025-11-15

### Añadido
- Validación de documentos: Checklist específico por tipo

---

## [1.6.8] - 2025-11-15

### Añadido
- Estados de documentos: pending, valid, rejected

---

## [1.6.7] - 2025-11-15

### Añadido
- Razones de rechazo predefinidas para documentos

---

## [1.6.6] - 2025-11-15

### Añadido
- Notas de validación en documentos

---

## [1.6.5] - 2025-11-15

### Añadido
- Privacy API (GDPR): Proveedor de privacidad

---

## [1.6.4] - 2025-11-15

### Añadido
- Exportación de datos personales (Privacy API)

---

## [1.6.3] - 2025-11-15

### Añadido
- Eliminación de datos bajo solicitud (Privacy API)

---

## [1.6.2] - 2025-11-10

### Añadido
- Dashboard con estadísticas de vacantes

---

## [1.6.1] - 2025-11-10

### Añadido
- Reportes por vacante

---

## [1.6.0] - 2025-11-10

### Añadido
- Reportes por período

---

## [1.5.9] - 2025-11-10

### Añadido
- Exportación de reportes a CSV

---

## [1.5.8] - 2025-11-10

### Añadido
- Exportación de reportes a Excel

---

## [1.5.7] - 2025-11-10

### Añadido
- Encriptación AES-256 para documentos sensibles

---

## [1.5.6] - 2025-11-10

### Añadido
- Backup de claves de encriptación

---

## [1.5.5] - 2025-11-10

### Añadido
- Configuración de encriptación por tipo de documento

---

## [1.5.4] - 2025-11-05

### Añadido
- Carga múltiple de documentos

---

## [1.5.3] - 2025-11-05

### Añadido
- Validación de tipos MIME en documentos

---

## [1.5.2] - 2025-11-05

### Añadido
- Límites de tamaño configurables para documentos

---

## [1.5.1] - 2025-11-05

### Añadido
- Preview de imágenes en documentos

---

## [1.5.0] - 2025-11-05

### Añadido
- 18 tipos de documentos predefinidos (contexto colombiano)
  - sigep, bienes_rentas, cedula
  - titulo_pregrado, titulo_postgrado, titulo_especializacion
  - titulo_maestria, titulo_doctorado, acta_grado
  - tarjeta_profesional, libreta_militar, rut
  - eps, pension, certificado_medico
  - antecedentes_procuraduria, antecedentes_contraloria, antecedentes_policia

---

## [1.4.9] - 2025-11-05

### Añadido
- Configuración de documentos requeridos/opcionales por vacante

---

## [1.4.8] - 2025-11-05

### Añadido
- Fechas de vencimiento para documentos

---

## [1.4.7] - 2025-11-01

### Añadido
- Formulario de postulación

---

## [1.4.6] - 2025-11-01

### Añadido
- Vista "Mis postulaciones"

---

## [1.4.5] - 2025-11-01

### Añadido
- Estados de postulación básicos

---

## [1.4.4] - 2025-11-01

### Añadido
- Historial de cambios en postulaciones

---

## [1.4.3] - 2025-11-01

### Añadido
- Multi-tenant (IOMAD): Filtrado por companyid

---

## [1.4.2] - 2025-11-01

### Añadido
- Permisos por empresa (IOMAD)

---

## [1.4.1] - 2025-11-01

### Añadido
- Vacantes por sede (IOMAD)

---

## [1.4.0] - 2025-10-30

### Añadido
- Gestión de vacantes: Crear vacantes

---

## [1.3.0] - 2025-10-30

### Añadido
- Gestión de vacantes: Editar vacantes

---

## [1.2.0] - 2025-10-30

### Añadido
- Gestión de vacantes: Eliminar vacantes

---

## [1.1.0] - 2025-10-28

### Añadido
- Estados de vacantes: draft, open, closed, cancelled

---

## [1.0.9] - 2025-10-28

### Añadido
- Fechas de apertura y cierre en vacantes

---

## [1.0.8] - 2025-10-28

### Añadido
- Requisitos y descripción en vacantes

---

## [1.0.7] - 2025-10-27

### Añadido
- Estructura de base de datos: 21 tablas
  - local_jobboard_vacancy
  - local_jobboard_application
  - local_jobboard_document
  - local_jobboard_doctype
  - local_jobboard_vacancy_doctype
  - local_jobboard_log
  - local_jobboard_reviewer
  - local_jobboard_exemption
  - local_jobboard_interview
  - local_jobboard_convocatoria
  - (y 11 tablas adicionales)

---

## [1.0.6] - 2025-10-27

### Añadido
- Índices optimizados en base de datos

---

## [1.0.5] - 2025-10-27

### Añadido
- Claves foráneas en base de datos

---

## [1.0.4] - 2025-10-26

### Añadido
- 15 capabilities iniciales

---

## [1.0.3] - 2025-10-26

### Añadido
- Roles predefinidos sugeridos

---

## [1.0.2] - 2025-10-25

### Añadido
- Idioma español (es) - idioma principal

---

## [1.0.1] - 2025-10-25

### Añadido
- Idioma inglés (en) - traducción completa

---

## [1.0.0] - 2025-10-25

### Añadido
- **Versión inicial del plugin local_jobboard**
- Estructura básica del plugin compatible con Moodle 4.1+
- Archivo version.php con metadatos del plugin
- Integración base con IOMAD para multi-tenancy
- lib.php con callbacks básicos de Moodle
- index.php como punto de entrada
- settings.php para configuración administrativa

---

## Leyenda

| Tipo | Descripción |
|------|-------------|
| **Añadido** | Nuevas funcionalidades |
| **Modificado** | Cambios en funcionalidades existentes |
| **Obsoleto** | Funcionalidades que serán eliminadas próximamente |
| **Eliminado** | Funcionalidades eliminadas |
| **Corregido** | Corrección de errores |
| **Seguridad** | Vulnerabilidades corregidas |

---

## Versiones Futuras (Roadmap)

### [2.2.0] - Planificado

- [ ] Migración completa de iconos Font Awesome a pix_icon
- [ ] Integración con sistemas de nómina
- [ ] Firmas digitales de documentos

### [2.3.0] - Planificado

- [ ] App móvil para postulantes
- [ ] Dashboard analítico avanzado
- [ ] Inteligencia artificial para matching de candidatos
